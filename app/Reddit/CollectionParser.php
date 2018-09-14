<?php

namespace App\Reddit;

use Illuminate\Support\Facades\Log;

/**
 * Regexes
 * 
 * Year Index Discover: "/###\[.*?\]\(https:\/\/www\.reddit\.com\/r\/AnimeThemes\/wiki\/(.*?)\)/m"
 * Season Discover: "/##(\d+) (.*?) Season/m"
 * Anime Discover: "/###\[(.*)\]\((.*)\)/m"
 * Theme Discover: "(([A-Z][A-Z])?(\d+)? (V)?(\d+)?.*?(\".*?\"))?\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m"
 */
class CollectionParser {

    public static function startWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function getCollections() {
        LOG::info('sync-collection Download Year Index');
        $rd_yearindex = 'https://www.reddit.com/r/animethemes/wiki/year_index.json';
        $year_index_rd = preg_split('/$\R?^/m', json_decode(file_get_contents($rd_yearindex))->data->content_md); // Get
        $collections = array();
        for ($i = 0; $i < count($year_index_rd); $i++) {
            $line = $year_index_rd[$i];
            if ($c=preg_match_all ('/###\[.*?\]\(https:\/\/www\.reddit\.com\/r\/AnimeThemes\/wiki\/(.*?)\)/m', $line, $matches)) {
                $collections[] = $matches[1][0];
                LOG::info("Year {$matches[1][0]}");
            }
        }
        $collections[] = "misc";
        return $collections;
    }

    public static function getCollection($collection) {
        $animes = array();
        $currentAnime = null;
        $currentSeason = 4; // Default to All

        // Download Markdown File
        LOG::info("sync-collection Download $collection");
        $cotUrl = "https://www.reddit.com/r/animethemes/wiki/$collection.json";
        $collectionLines = preg_split('/$\R?^/m', json_decode(file_get_contents($cotUrl))->data->content_md);

        // Loop Markdown Lines
        for ($i = 0; $i < count($collectionLines); $i++) {
            $line = $collectionLines[$i];
            if ($currentAnime !== null) {
                $animes[] = $currentAnime;
            }
            $currentAnime = array();
            // Check for season id
            if ($c=preg_match_all ('/##(\d+) (.*?) Season/m', $line, $matches)) {
                $season = AnimeParser::getSeasonfromString($matches[1][2]);
                $currentSeason = "$collection{$season}";
            }

            // Get name and List
            if ($c=preg_match_all ('/###\[(.*)\]\((.*)\)/m', $line, $matches)) {
                if ($currentAnime !== null) {
                    $animes[] = $currentAnime;
                }
                $currentAnime = array();
                $currentAnime["Name"] = $matches[1][1];
                $currentAnime["Season"] = $currentSeason;
                if ($c=preg_match_all ('/https:\/\/myanimelist\.net\/anime\/(\d+)\//m', $matches[1][2], $matches)) {
                    $currentAnime["mal_id"] = $matches[1][1];
                } else if ($c=preg_match_all ('/https:\/\/anidb\.net\/perl-bin\/animedb\.pl\?show=anime&aid=(\d+)/m', $matches[1][2], $matches)) {
                    $currentAnime["anidb_id"] = $matches[1][1];
                }
                $currentAnime["Themes"] = array();
            }

            // Get Themes from list
            $currentTheme = null;
            // Process rows with theme name
            if ($c=preg_match_all ('/([A-Z][A-Z])?(\d+)? V?(\d+)?.*?\"(.*?)\"\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                if ($currentTheme !== null) {
                    $currentAnime["Themes"][] = $currentTheme;
                }
                $currentTheme = array();

                $currentTheme["id"] = AnimeParser::slugify("{$currentAnime["Name"]}{$currentAnime["Season"]}{$matches[1][1]}{$matches[1][2]}{$matches[1][3]}");

                // Set if NSFW
                if ($c=preg_match_all ('/(NSFW)/m', $matches[1][8], $link)) {
                    $currentTheme["isNSFW"] = true;
                } else {
                    $currentTheme["isNSFW"] = true;
                }

                $currentTheme["SongName"] = $matches[1][4];
                $currentTheme["Theme"] = $matches[1][1];
                $currentTheme["Major"] = $matches[1][2];
                $currentTheme["Minor"] = $matches[1][3];
                $currentTheme["Episodes"] = $matches[1][7];
                $currentTheme["Notes"] = $matches[1][8];
                $currentTheme["Videos"] = array();

                // Process main video
                $videoTitle = $matches[1][5];
                $videoLink = $matches[1][6];

                // Set file name
                if ($c=preg_match_all ('/https:\/\/animethemes\.moe\/video\/(.*.webm)/m', $videoLink, $link)) {
                    $currentVideo["basename"] = $currentVideo["filename"] = $currentVideo["path"] = $link[1][1];
                }
                
                // Set quality
                if ($c=preg_match_all ('/(\d+)/m', $videoTitle, $link)) {
                    $currentVideo["quality"] = $link[1][1];
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
                    $currentVideo["source"] = $link[1][1];
                } else {
                    $currentVideo["source"] = 'TV/UNK';
                }
            }

            // Process without theme name
            if ($c=preg_match_all ('/\|\|\[(.*?)\]\((.*?)\)\|(.*?)?\|(.*)?/m', $line, $matches)) {
                
            }
        }
    }
}