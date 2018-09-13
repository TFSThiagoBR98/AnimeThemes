<?php

namespace App\Reddit;

use Illuminate\Support\Facades\Log;


class CollectionParser {
    public static function startWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function getCollections() {
        LOG::info('sync-collection Download Year Index');
        $rd_yearindex = 'https://www.reddit.com/r/animethemes/wiki/year_index.json';
        $year_index_rd = html_entity_decode(json_decode(file_get_contents($rd_yearindex))->data->content_html); // Get 
        
        $dom = new \DomDocument();
        $dom->loadHTML($year_index_rd);
        $xpath = new \DomXPath($dom);
        $nodes = $xpath->query("//div/ul/li");
        $collections = array();
        for ($i = 0; $i < $nodes->length; $i++) {
            $name = $nodes->item($i)->firstChild->nodeValue;
            if (CollectionParser::startWith($name, "2")) {
                $str = mb_substr($name, 0, 4);
                $collections[] = $str;
                LOG::info("Year {$str}");
            } else {
                $str = mb_substr($name, 2, 2);
                $collections[] = "{$str}s";
                LOG::info("Year {$str}s");
            }
        }
        $collections[] = "misc";
        return $collections;
    }

    public static function getAnimesfromCollection($collectionNodes) {
        $animes = array();
        $currentAnime = new Anime();
        $currentSeason = 4; // Default to All

        $nodeCollection = array();
        for ($i = 0; $i < $collectionNodes->length; $i++) {
            if ($node->$nodeName == "h3") {
                $animes[] = $currentAnime;
                $currentAnime = new Anime();
            }
        }
        $animes[] = $currentAnime;
        $currentAnime = new Anime();
    }
}