<?php

use Illuminate\Database\Seeder;

use App\Reddit\CollectionParser;
use App\Models\Anime;
use App\Models\Theme;
use App\Models\Video;

class AnimeTableSeeder extends Seeder
{
    /**
     * This will fill tables with year list
     *
     * @return void
     */
    public function run()
    {
        $collections = CollectionParser::getAllCollections();
        
        foreach ($collections["anime"] as $anime) {
            Anime::create($anime);
        }

        foreach ($collections["theme"] as $theme) {
            Theme::create($theme);
        }
    }
}
