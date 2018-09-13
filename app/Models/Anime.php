<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $fillable = ['id', 'name', 'release_date', 'season', 'anilist_id', 'mal_id', 'kitsu_id', 'anidb_id'];
}
