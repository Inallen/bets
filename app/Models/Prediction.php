<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    const TYPE_MATCH_WIN = 1;
    const TYPE_HANDICAP = 2;
    const TYPE_TEN_KILLS = 3;
    const TYPE_TOTAL_SCORE = 6;
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

}
