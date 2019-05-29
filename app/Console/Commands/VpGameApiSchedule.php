<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Illusion;
use App\Models\Match;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\Tournament;
use App\Utils\Traits\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;


class VpGameApiSchedule extends Command
{
    use Url;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:vpgame';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $baseUri = 'http://www.vpgame.com/prediction/api/prediction/matches';

    protected $platform = 'vpgame';

    private $client;

    private $leftTeam;

    private $rightTeam;

    private $searchCategories = [
        1 => 'dota',
        2 => 'csgo',
        3 => 'sports',
        4 => 'lol',
    ];

    private $allowCategories = [
        1 => 'dota',
        2 => 'csgo',
        3 => 'lol',
        4 => 'football',
        5 => 'basketball',
        6 => 'tennis',
    ];

    private $chineseNumber = [
        '一' => 1,
        '二' => 2,
        '三' => 3,
        '四' => 4,
        '五' => 5,
        '六' => 6,
        '七' => 7,
        '八' => 8,
        '九' => 9,
        '十' => 10,
    ];

    private $match;

    private $predictionInfo;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $promises = [];

        foreach ($this->searchCategories as $key => $category) {
            $query = [
                'category' => $category,
                'offset' => 0,
                'limit' => 20,
                't' => time() * 1000,
            ];
            if (App::isLocale('zh_cn')) {
                $query['lang'] = 'zh_cn';
            }
            $promises[$category] =  $this->client->getAsync($this->baseUri, [
                'query' => $query
                ]);
        }
        $results = Promise\unwrap($promises);
        foreach ($this->searchCategories as $allowCategory) {
            $this->convertData($results[$allowCategory]->getBody()->getContents());
        }
        $this->convertData($results['dota']->getBody()->getContents());

    }

    private function convertData($content)
    {
        $content = json_decode($content, true);
        if (!empty($content) && isset($content['data'])) {
            $matches = $content['data'];
            foreach ($matches as $matchInfo) {
                $this->match = $this->parseMatchInfo($matchInfo);
                if (empty($this->match)) {
                    continue;
                }
                if (isset($matchInfo['predictions'])) {
                    $predictions = $matchInfo['predictions'];
                    foreach ($predictions as $predictionInfo) {
                        $this->predictionInfo = $predictionInfo;
                        $this->parsePredictionInfo();
                    }
                }
            }

        }
    }

    private function parseMatchInfo($matchInfo)
    {
        $categoryInfo = $matchInfo['category'];
        $categoryIndex = array_search($categoryInfo, $this->allowCategories);
        if (false == $categoryIndex) {
            return null;
        }
        $tournament = Tournament::where('tournament_name', $matchInfo['tournament_name'])->first();
        if (empty($tournament)) {
            $path = $this->storeFileFromUrl($matchInfo['background'], '/tournaments/images/');
            $tournament = Tournament::create([
                'tournament_name' => $matchInfo['tournament_name'],
                'tournament_image_url' => $path
            ]);
        }

        $leftTeamInfo = $matchInfo['teams']['left'];
        $teamLeft = Team::where('team_short_name', $leftTeamInfo['short_name'])
            ->where('category_id', $categoryIndex)
            ->first();
        if (empty($teamLeft)) {
            $path = $this->storeFileFromUrl($leftTeamInfo['logo'], '/teams/images/');
            $teamLeft = Team::create([
                'category_id' => $categoryIndex,
                'team_name' => $leftTeamInfo['name'],
                'team_short_name' => $leftTeamInfo['short_name'],
                'team_logo_url' => $path,
                'steam_team_id' => $leftTeamInfo['steam_team_id']
            ]);
        }
        $this->leftTeam = $teamLeft;

        $rightTeamInfo = $matchInfo['teams']['right'];
        $teamRight = Team::where('team_short_name', $rightTeamInfo['short_name'])
            ->where('category_id', $categoryIndex)
            ->first();
        if (empty($teamRight)) {
            $path = $this->storeFileFromUrl($rightTeamInfo['logo'], '/teams/images/');
            $teamRight = Team::create([
                'category_id' => $categoryIndex,
                'team_name' => $rightTeamInfo['name'],
                'team_short_name' => $rightTeamInfo['short_name'],
                'team_logo_url' => $path,
                'steam_team_id' => $rightTeamInfo['steam_team_id']
            ]);
        }
        $this->rightTeam = $teamRight;

        $illusion = Illusion::where('platform', $this->platform)
            ->where('illusion_type', Illusion::TYPE_MATCH)
            ->where('uri', $matchInfo['id'])
            ->first();
        if (empty($illusion)) {
            $match = Match::create([
                'category_id' => $categoryIndex,
                'tournament_id' => $tournament->id,
                'left_team_id' => $teamLeft->id,
                'right_team_id' => $teamRight->id,
                'left_team_score' => $matchInfo['teams']['left']['score'],
                'right_team_score' => $matchInfo['teams']['right']['score'],
                'result' => $matchInfo['result'],
                'start_time' => $matchInfo['start_time'],
                'match_status' => $this->parseMatchStatus($matchInfo['status']),
            ]);
            Illusion::create([
                'platform' => $this->platform,
                'illusion_type' => Illusion::TYPE_MATCH,
                'uri' => $matchInfo['id'],
                'illusion_id' => $match->id,
            ]);
        } else {
            $match = $illusion->illusion;
            $matchUpdated = false;
            if ($match->left_team_id != $teamLeft->id) {
                $match->left_team_id = $teamLeft->id;
                $matchUpdated = true;
            }
            if ($match->right_team_id != $teamRight->id) {
                $match->right_team_id = $teamRight->id;
                $matchUpdated = true;
            }
            if ($match->left_team_score != $matchInfo['teams']['left']['score']) {
                $match->left_team_score = $matchInfo['teams']['left']['score'];
                $matchUpdated = true;
            }
            if ($match->right_team_score != $matchInfo['teams']['right']['score']) {
                $match->right_team_score = $matchInfo['teams']['right']['score'];
                $matchUpdated = true;
            }
            if ($match->result != $matchInfo['result']) {
                $match->result = $matchInfo['result'];
                $matchUpdated = true;
            }
            if ($match->start_time != $matchInfo['start_time']) {
                $match->start_time = $matchInfo['start_time'];
                $matchUpdated = true;
            }
            if ($match->match_status != $this->parseMatchStatus($matchInfo['status'])) {
                $match->match_status = $this->parseMatchStatus($matchInfo['status']);
                $matchUpdated = true;
            }
            if ($matchUpdated) {
                $match->save();
            }
        }
        return $match;
    }

    private function parsePredictionInfo()
    {
        $illusion = Illusion::where('platform', $this->platform)
            ->where('illusion_type', Illusion::TYPE_PREDICTION)
            ->where('uri', $this->predictionInfo['id'])
            ->first();
        if (empty($illusion)) {
            $prediction = Prediction::create([
                'match_id' => $this->match->id,
                'title' => $this->parseTitle(),
                'start_time' => $this->predictionInfo['start_time'],
                'handicap' => $this->parseHandicap(),
                'score' => $this->predictionInfo['score'],
                'scene' => $this->parseScene(),
                'prediction_type' => $this->predictionInfo['mode_type'],
                'prediction_status' => $this->parseStatus($this->predictionInfo['status']),
            ]);
            Illusion::create([
                'platform' => $this->platform,
                'illusion_type' => Illusion::TYPE_PREDICTION,
                'uri' => $this->predictionInfo['id'],
                'illusion_id' => $prediction->id,
            ]);
        } else {
            $prediction = $illusion->illusion;
            $predictionUpdated = false;
            if ($prediction->prediction_status != $this->parseStatus($this->predictionInfo['status'])) {
                $prediction->prediction_status = $this->parseStatus($this->predictionInfo['status']);
                $predictionUpdated = true;
            }
            if ($prediction->start_time != $this->predictionInfo['start_time']) {
                $prediction->start_time = $this->predictionInfo['start_time'];
                $predictionUpdated = true;
            }
            if ($predictionUpdated) {
                $prediction->save();
            }
        }
    }


    private function parseTitle()
    {
        $title = '';
        $handicap = $this->parseHandicap();
        $score = $this->predictionInfo['score'];
        $scene = $this->parseScene();
        if ($this->predictionInfo['mode_type'] == Prediction::TYPE_MATCH_WIN) {
            $title .=  'Match Winner';
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_HANDICAP) {
            if (!empty($handicap)) {
                if ($handicap > 0) {
                    $title .= $this->rightTeam->team_short_name . ' ' . -$handicap;
                } else {
                    $title .= $this->leftTeam->team_short_name . ' ' . $handicap;
                }
            }
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_REGULAR_KILLS) {
            if ($this->match->category_id == Category::CATEGORY_DOTA) {
                $title .= '10 Kills';
            } elseif ($this->match->category_id == Category::CATEGORY_LOL) {
                $title .= '5 Kills';
            } else {
                $title .= $this->predictionInfo['mode_name'];
            }
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_TOTAL_SCORE) {
            $title .= $score;
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_REGULAR_ROUNDS) {
            $title .=  'First 5 Rounds';
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_REGULAR_TIME) {
            $title .=  '36 Minutes';
        } elseif ($this->predictionInfo['mode_type'] == Prediction::TYPE_MAP_WIN) {
            $title .=  'Winner';
        }
        if (!empty($scene)) {
            $title .= "[Game $scene]";
        }
        return $title;
    }

    private function parseHandicap()
    {
        if (!empty($this->predictionInfo['handicap'])) {
            if ($this->predictionInfo['option']['left']['is_handicap']) {
                return -$this->predictionInfo['handicap'];
            }
            if ($this->predictionInfo['option']['right']['is_handicap']) {
                return $this->predictionInfo['handicap'];
            }
        }
        return 0;
    }

    private function parseScene()
    {
        if (App::isLocale('zh_cn')) {
            foreach (array_keys($this->chineseNumber) as $key) {
                if (strpos($this->predictionInfo['mode_name'], $key) !== false) {
                    return $this->chineseNumber[$key];
                }
            }
        } else {
            $terms = explode(' ', $this->predictionInfo['mode_name']);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (is_numeric($term)) {
                        return intval($term);
                    }
                }
            }
        }
        return 0;
    }

    private function parseStatus($status)
    {
        $statusPool = [
            'cancel' => 0,
            'normal' => 1,
            'start' => 2,
            'wait_clear' => 8,
            'clear' => 9,
        ];
        return $statusPool[$status];
    }

    private function parseMatchStatus($status)
    {
        $statusPool = [
            'cancel' => 0,
            'normal' => 1,
            'not_start' => 2,
            'live' => 3,
            'none' => 4,
            'close' => 9,
        ];
        return $statusPool[$status];
    }

}
