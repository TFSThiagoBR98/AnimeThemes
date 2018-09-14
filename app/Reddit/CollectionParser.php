<?php

namespace App\Reddit;

use Illuminate\Support\Facades\Log;


class CollectionParser {

    public static function startWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function getCollectionList() {
        LOG::info('sync-collection Download Year Index');
        $rd_yearindex = 'https://www.reddit.com/r/animethemes/wiki/year_index.json';
        $year_index_rd = preg_split('/$\R?^/m', json_decode(file_get_contents($rd_yearindex))->data->content_md); // Get
        $collections = array();
        for ($i = 0; $i < count($year_index_rd); $i++) {
            $line = $year_index_rd[$i];
            if ($c=preg_match_all ('/###\[.*?\]\(https:\/\/www\.reddit\.com\/r\/AnimeThemes\/wiki\/(.*?)\)/m', $line, $matches)) {
                $collections[] = $matches[1][0];
                Log::debug("Collection {$matches[1][0]}");
            }
        }
        //$collections[] = "misc";
        //Log::debug("Collection misc");
        return $collections;
    }

    public static function getAllCollections() {
        $collections = CollectionParser::getCollectionList();
        $alldata = array();
        $alldata["anime"] = array();
        $alldata["themes"] = array();
        $alldata["videos"] = array();
        for ($i = 0; $i < count($collections); $i++) {
            $currentCollection = $collections[$i];
            Log::debug("Get Collection $currentCollection");
            $data = CollectionParser::getCollection($currentCollection);
            $alldata["anime"] = array_merge($alldata["anime"], $data["anime"]);
            $alldata["themes"] = array_merge($alldata["themes"], $data["themes"]);
            $alldata["videos"] = array_merge($alldata["videos"], $data["videos"]);
        }
        return $alldata;
    }

    public static function getCollection($collection) {
        $alldata = array();
        $alldata["anime"] = array();
        $alldata["themes"] = array();
        $alldata["videos"] = array();
        $currentAnime = null;
        $season = 4; // Default to All
        $currentSeason = "$collection{$season}";; // Default to All
        Log::debug("Set current season $currentSeason");
        $currentTheme = null;

        // Download Markdown File
        LOG::info("sync-collection Download $collection");
        $cotUrl = "https://www.reddit.com/r/animethemes/wiki/$collection.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($cotUrl))->data->content_md);
        
        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i];
            //Log::debug("Process: $line");
            // Check for season id
            if ($c=preg_match_all ('/##(\d+) (.*?) Season/m', $line, $matches)) {
                $season = AnimeParser::getSeasonfromString($matches[2][0]);
                $currentSeason = "$collection{$season}";
                Log::debug("Set current season $currentSeason");
            }

            // Get name and List
            if ($c=preg_match_all ('/###\[(.*)\]\((.*)\)/m', $line, $matches)) {
                if ($currentTheme !== null) {
                    if (count($alldata["videos"]) !== 0) { // Check if have videos
                        $currentTheme["id_anime"] = $currentAnime["id_anime"];
                        $alldata["themes"][] = $currentTheme;
                    }
                    $currentTheme = null;
                }
                if ($currentAnime !== null) {
                    $alldata["anime"][] = $currentAnime;
                }
                $currentAnime = array();
                $currentAnime["name"] = $matches[1][0];
                $currentAnime["season"] = $currentSeason;
                $currentAnime["id_anime"] = AnimeParser::slugify("{$currentAnime["name"]}-{$currentAnime["season"]}");
                
                if ($c=preg_match_all ('/https:\/\/myanimelist\.net\/anime\/(\d+)/m', $matches[2][0], $link1)) {
                    $currentAnime["mal_id"] = $link1[1][0];
                } else if ($c=preg_match_all ('/https:\/\/anidb\.net\/perl-bin\/animedb\.pl\?show=anime&aid=(\d+)/m', $matches[2][0], $link2)) {
                    $currentAnime["anidb_id"] = $link2[1][0];
                }

                Log::debug("anime-parser", $currentAnime);
            }

            // Process rows with theme name
            if ($c=preg_match_all ('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\".*?\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                if ($currentTheme !== null) {
                    if (count($alldata["videos"]) !== 0) {
                        $currentTheme["id_anime"] = $currentAnime["id_anime"];
                        $alldata["themes"][] = $currentTheme;
                    }
                }
                $currentTheme = array();

                $currentTheme["theme_id"] = AnimeParser::slugify("{$currentAnime["name"]}-{$currentAnime["season"]}--{$matches[1][0]}{$matches[2][0]}{$matches[3][0]}");

                // Set if NSFW
                if ($c=preg_match_all ('/(NSFW)/m', $matches[8][0], $link)) {
                    $currentTheme["isNSFW"] = true;
                } else {
                    $currentTheme["isNSFW"] = false;
                }
                $currentTheme["artist"] = "";
                $currentTheme["song_name"] = $matches[4][0];
                $currentTheme["theme"] = $matches[1][0];
                $currentTheme["ver_major"] = $matches[2][0];
                $currentTheme["ver_minor"] = $matches[3][0];
                $currentTheme["episodes"] = $matches[7][0];
                $currentTheme["notes"] = $matches[8][0];

                Log::debug("theme-parser", $currentTheme);

                // Process main video
                $videoTitle = $matches[5][0];
                $videoLink = $matches[6][0];
                $currentVideo = array();
                
                $currentVideo["theme_id"] = $currentTheme["theme_id"];

                // Set quality
                if ($c=preg_match_all ('/(\d+)/m', $videoTitle, $link)) {
                    $currentVideo["quality"] = $link[1][0];
                } else {
                    $currentVideo["quality"] = '720';
                }

                // Set if NC
                if ($c=preg_match_all ('/(NC)/m', $videoTitle, $link)) {
                    $currentVideo["isNC"] = true;
                } else {
                    $currentVideo["isNC"] = false;
                }

                // Set if Lyrics
                if ($c=preg_match_all ('/(Lyrics)/m', $videoTitle, $link)) {
                    $currentVideo["isLyrics"] = true;
                } else {
                    $currentVideo["isLyrics"] = false;
                }

                // Set source
                if ($c=preg_match_all ('/(BD|DVD|VHS)/m', $videoTitle, $link)) {
                    $currentVideo["source"] = $link[1][0];
                } else {
                    $currentVideo["source"] = "TV/UNK";
                }

                // Set file name
                if ($c=preg_match_all ('/https:\/\/animethemes\.moe\/video\/(.*.webm)/m', $videoLink, $link)) {
                    $currentVideo["basename"] = $currentVideo["filename"] = $currentVideo["path"] = $link[1][0];
                    $alldata["videos"][] = $currentVideo;
                    Log::notice('video-parser', $currentVideo);
                } else if ($videoLink !== ""){
                    $currentVideo["url"] = $videoLink;
                    $alldata["videos"][] = $currentVideo;
                    Log::notice('video-upload', $currentVideo);
                } else {
                    Log::notice('video-empty', $currentVideo);
                    $currentVideo = null;
                }
            }

            // Process without theme name
            if ($c=preg_match_all ('/\|\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                // Process derivatives
                $videoTitle = $matches[1][0];
                $videoLink = $matches[2][0];
                $currentVideo = array();

                // Set quality
                if ($c=preg_match_all ('/(\d+)/m', $videoTitle, $link)) {
                    $currentVideo["quality"] = $link[1][0];
                } else {
                    $currentVideo["quality"] = '720';
                }

                // Set if NC
                if ($c=preg_match_all ('/(NC)/m', $videoTitle, $link)) {
                    $currentVideo["isNC"] = true;
                } else {
                    $currentVideo["isNC"] = false;
                }

                // Set if Lyrics
                if ($c=preg_match_all ('/(Lyrics)/m', $videoTitle, $link)) {
                    $currentVideo["isLyrics"] = true;
                } else {
                    $currentVideo["isLyrics"] = false;
                }

                // Set source
                if ($c=preg_match_all ('/(BD|DVD|VHS)/m', $videoTitle, $link)) {
                    $currentVideo["source"] = $link[1][0];
                } else {
                    $currentVideo["source"] = "TV/UNK";
                }
                
                // Set file name
                if ($c=preg_match_all ('/https:\/\/animethemes\.moe\/video\/(.*.webm)/m', $videoLink, $link)) {
                    $currentVideo["basename"] = $currentVideo["filename"] = $currentVideo["path"] = $link[1][0];
                    $alldata["videos"][] = $currentVideo;
                } else if ($videoLink !== ""){
                    Log::notice("Need to Upload: $videoLink");
                    $currentVideo["url"] = $videoLink;
                    $alldata["videos"][] = $currentVideo;
                } else {
                    $currentVideo = null;
                }
            }
        }
        // Close latest theme and anime
        if ($currentTheme !== null) {
            if (count($alldata["videos"]) !== 0) {
                $currentTheme["id_anime"] = $currentAnime["id_anime"];
                $alldata["themes"][] = $currentTheme;
            }
        }
        if ($currentAnime !== null) {
            $alldata["anime"][] = $currentAnime;
        }

        return $alldata;
    }
}