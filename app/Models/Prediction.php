<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $table = 'predictions';

    protected $fillable = [
        'title',
        'match_id',
        'start_time',
        'prediction_type',
        'prediction_status'
    ];

}
