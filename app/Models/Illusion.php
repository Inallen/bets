<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Illusion extends Model
{
    const TYPE_CATEGORY = 1;
    const TYPE_TOURNAMENT = 2;
    const TYPE_MATCH = 3;
    const TYPE_TEAM = 4;
    const TYPE_PREDICTION = 5;

    protected $table = 'illusions';

    protected $fillable = [
        'platform',
        'uri',
        'illusion_id',
        'illusion_type',
        'illusion_status',
    ];
}
