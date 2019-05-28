<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const CATEGORY_DOTA = 1;
    const CATEGORY_CSGO = 2;
    const CATEGORY_LOL = 3;
    const CATEGORY_FOOTBALL = 4;
    const CATEGORY_BASKETBALL = 5;
    const CATEGORY_TENNIS = 6;

    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'category_image_url',
    ];
}
