<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['id', 'name_song', 'isNSFW', 'theme', 'major', 
                            'minor', 'artist', 'episodes', 'notes'];
}
