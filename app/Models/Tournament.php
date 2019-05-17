<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $table = 'tournaments';

    protected $fillable = [
        'tournament_name',
        'tournament_image_url',
        'tournament_type',
        'tournament_status',
    ];
}
