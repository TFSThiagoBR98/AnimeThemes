<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('videos');
        Schema::create('anime', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_anime')->unique(); // Slug for anime name-20180
            $table->string('name');
            $table->date('release_date');
            $table->char('season', 5); // year season - 20180
            $table->integer('mal_id')->unique();
            $table->integer('anilist_id')->unique();
            $table->integer('kitsu_id')->unique(); // Unique id number to user list
            $table->string('kitsu_slug')->unique(); // Unique slug for url
            $table->timestamps();
            $table->primary('id_anime');
            $table->index(['release_date', 'season',  'mal_id', 'anilist_id', 'kitsu_id', 'kitsu_slug']);
        });

        Schema::create('anime_names', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_anime');
            $table->string('title');
            $table->char('language', 5);
            $table->timestamps();
            $table->foreign('id_anime')->references('id_anime')->on('anime');
        });

        Schema::create('anime_names', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_anime');
            $table->string('title');
            $table->char('language', 5);
            $table->timestamps();
            $table->foreign('id_anime')->references('id_anime')->on('anime');
        });

        Schema::create('artists', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id_artist');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('artist_theme', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_artist');
            $table->string('id_theme');
            $table->timestamps();
            $table->foreign('id_theme')->references('id_theme')->on('themes');
            $table->foreign('id_artist')->references('id_artist')->on('artists');
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_theme'); // Also a slug anime-oped-ver-name
            $table->string('id_anime');
            $table->string('name_song');
            $table->boolean('isNSFW');
            $table->boolean('isOP');
            $table->integer('ver_major'); // OP1
            $table->integer('ver_minor'); // OP1 V1
            $table->string('episodes');
            $table->string('notes');
            $table->char('language', 5);
            $table->timestamps();
            $table->primary('id_theme');
            $table->foreign('id_anime')->references('id_anime')->on('anime');
            $table->index(['id_anime', 'name_song', 'artist']);
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
            $table->string('id_theme');
            $table->string('basename');
            $table->string('filename');
            $table->string('path');
            $table->integer('quality');
            $table->boolean('isNC');
            $table->boolean('isLyrics');
            $table->boolean('source');
            $table->index(['id_theme', 'basename', 'quality', 'isNC', 'isLyrics', 'source']);
            $table->foreign('id_theme')->references('id_theme')->on('themes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime');
    }
}
