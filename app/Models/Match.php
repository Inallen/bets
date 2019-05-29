<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'title',
        'category_id',
        'tournament_id',
        'left_team_id',
        'right_team_id',
        'left_team_score',
        'right_team_score',
        'result',
        'start_time',
        'match_status',
    ];

    public function leftTeam()
    {
        return $this->belongsTo('App\Models\Team', 'left_team_id');
    }

    public function rightTeam()
    {
        return $this->belongsTo('App\Models\Team', 'right_team_id');
    }

    public function illusion()
    {
        return $this->morphOne('App\Models\Illusion', 'illusion');
    }
}
