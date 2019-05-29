<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Illusion extends Model
{
    const TYPE_CATEGORY = 'App\Models\Category';
    const TYPE_TOURNAMENT = 'APP\Models\Tournament';
    const TYPE_MATCH = 'App\Models\Match';
    const TYPE_TEAM = 'App\Models\Team';
    const TYPE_PREDICTION = 'App\Models\Prediction';

    protected $table = 'illusions';

    protected $fillable = [
        'platform',
        'uri',
        'illusion_type',
        'illusion_id',
        'status',
    ];

    public function illusion()
    {
        return $this->morphTo();
    }
}
