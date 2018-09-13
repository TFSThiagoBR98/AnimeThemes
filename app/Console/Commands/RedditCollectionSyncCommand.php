<?php

namespace App\Console\Commands;

use App\Models\Anime;
use App\Reddit\CollectionParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RedditYearSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync-collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize Anime, Year, Themes and Video from Wiki';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Get list of years from https://www.reddit.com/r/animethemes/wiki/year_index 
     * and https://www.reddit.com/r/AnimeThemes/wiki/misc
     * Check if still exits in list and delete if removed
     * Also upload videos that doesn't use animethemes.moe
     * @return mixed
     */

    public function handle()
    {
        LOG::info('sync-collection start');

        /**
         * Stage 1: Process list of pages from year_index + misc
         */

        $collections = CollectionParser::getCollections();
        /**
         * Stage 2: Create a Anime Dumper
         */
        LOG::info('sync-collection end');
    }
}
