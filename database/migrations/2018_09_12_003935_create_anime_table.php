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
        Schema::create('animes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_anime')->unique(); // Slug for anime name-20180
            $table->string('name');
            $table->char('season', 5); // year season - 20180
            $table->integer('mal_id')->unique();
            $table->integer('anilist_id')->unique();
            $table->integer('kitsu_id')->unique(); // Unique id number to user list
            $table->string('kitsu_slug')->unique(); // Unique slug for url
            $table->timestamps();
            $table->primary('id_anime');
            $table->index(['season',  'mal_id', 'anilist_id', 'kitsu_id', 'kitsu_slug'], "multiindex");
        });

        Schema::create('anime_names', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_anime');
            $table->string('title');
            $table->char('language', 5);
            $table->timestamps();
            $table->foreign('id_anime')->references('id_anime')->on('animes');
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id_theme'); // Also a slug anime-oped-ver-name
            $table->string('id_anime');
            $table->string('name_song');
            $table->boolean('isNSFW');
            $table->char('theme', 2);
            $table->integer('ver_major'); // OP1
            $table->integer('ver_minor'); // OP1 V1
            $table->string('episodes');
            $table->string('artist');
            $table->string('notes');
            $table->timestamps();
            $table->primary('id_theme');
            $table->foreign('id_anime')->references('id_anime')->on('animes');
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
        Schema::dropIfExists('animes');
        Schema::dropIfExists('anime_names');
        Schema::dropIfExists('themes');
        Schema::dropIfExists('videos');
    }
}
