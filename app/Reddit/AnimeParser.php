<?php

namespace App\Reddit;

class AnimeParser {
    /**
     * Define season for anime based on yearseason like 20180 for Winter 2018
     * 
     * 0 - Winter
     * 1 - Spring
     * 2 - Summer
     * 3 - Fall
     * 4 - All (Default)
     */
    public static function getSeasonfromString($text) {
        if ($text == "Winter") {
            return 0;
        } else if ($text == "Spring") {
            return 1;
        } else if ($text == "Summer") {
            return 2;
        } else if ($text == "Fall" || $text == "Autumn") {
            return 3;
        } else {
            return 4;
        }
    }

    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}