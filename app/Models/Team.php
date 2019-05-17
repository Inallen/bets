<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'category_id',
        'team_name',
        'team_short_name',
        'team_logo_url',
        'steam_team_id',
    ];
}
