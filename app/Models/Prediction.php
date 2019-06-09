<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    const TYPE_MATCH_WIN = 1;
    const TYPE_HANDICAP = 2;
    const TYPE_REGULAR_KILLS = 3;
    const TYPE_TOTAL_SCORE = 6;
    const TYPE_REGULAR_ROUNDS = 11;
    const TYPE_REGULAR_TIME = 12;
    const TYPE_MAP_WIN = 13;

    protected $table = 'predictions';

    protected $fillable = [
        'title',
        'match_id',
        'start_time',
        'handicap',
        'score',
        'scene',
        'prediction_type',
        'prediction_status'
    ];

    public function illusion()
    {
        return $this->morphOne('App\Models\Illusion', 'illusion');
    }

    public function match()
    {
        return $this->belongsTo('App\Models\Match', 'match_id');
    }

}
