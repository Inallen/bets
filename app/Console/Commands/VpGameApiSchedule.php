<?php

namespace App\Console\Commands;

use App\Models\Illusion;
use App\Models\Match;
use App\Models\Prediction;
use App\Models\Team;
use App\Models\Tournament;
use App\Utils\Traits\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;


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

    private $match;

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
            $promises[$category] =  $this->client->getAsync($this->baseUri, [
                'query' => [
                    'category' => $category,
                    'offset' => 0,
                    'limit' => 6,
                    't' => time() * 1000,
                    ]
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
                        $this->parsePredictionInfo($predictionInfo);
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

        $match = Match::where('category_id', $categoryIndex)
            ->where('tournament_id', $tournament->id)
            ->where('left_team_id', $teamLeft->id)
            ->orWhere('right_team_id', $teamRight->id)
            ->first();
        if (empty($match)) {
            $match = Match::create([
                'category_id' => $categoryIndex,
                'tournament_id' => $tournament->id,
                'left_team_id' => $teamLeft->id,
                'right_team_id' => $teamRight->id,
                'start_time' => $matchInfo['start_time']
            ]);
        }
        $match->left_team_id = $teamLeft->id;
        $match->right_team_id = $teamRight->id;
        $match->left_team_score = $matchInfo['teams']['left']['score'];
        $match->right_team_score = $matchInfo['teams']['right']['score'];
        $match->result = $matchInfo['result'];
        $match->start_time = $matchInfo['start_time'];
        $match->save();
        return $match;
    }

    private function parsePredictionInfo($predictionInfo)
    {
        $illusion = Illusion::where('platform', $this->platform)
            ->where('illusion_type', Illusion::TYPE_PREDICTION)
            ->where('uri', $predictionInfo['id'])
            ->first();
        if (empty($illusion)) {
            $prediction = Prediction::create([
                'match_id' => $this->match->id,
                'title' => $this->parseTitle($predictionInfo),
                'start_time' => $predictionInfo['start_time'],
                'prediction_type' => $predictionInfo['mode_type'],
                'prediction_status' => $predictionInfo['start_time'],
            ]);
        }
    }


    private function parseTitle($predictionInfo) {

    }

}
