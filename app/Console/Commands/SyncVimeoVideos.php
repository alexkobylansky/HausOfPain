<?php

namespace App\Console\Commands;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Vimeo\Laravel\VimeoManager;

class SyncVimeoVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:vimeo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Vimeo Videos';

    /** @var VimeoManager */
    protected $vimeo;

    /** @var  int */
    protected $pageSize = 10;

    /**
     * Create a new command instance.
     *
     * @param VimeoManager $vimeo
     */
    public function __construct(VimeoManager $vimeo)
    {
        parent::__construct();
        $this->vimeo = $vimeo;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $response = $this->vimeo->request('/me/videos', ['per_page' => 1], 'GET');

        $totalPages = ceil($response["body"]["total"] / $this->pageSize);
        for ($i=1; $i<=$totalPages; $i++) {

            # Get paginated video information from Vimeo
            $response = $this->vimeo->request('/me/videos', ['per_page' => $this->pageSize, 'page' => $i], 'GET');

            # Process each video
            foreach ($response["body"]["data"] as $video) {
                $dbVideo = Video::where("vimeo_uri", $video["uri"])->first();

                if ($dbVideo && $dbVideo->synchronized_at < $dbVideo->updated_at) {
                    # If this video exists in our database and it was updated
                    # after previous synchronization, update it in Vimeo
                    $result = $this->vimeo->request(
                        $dbVideo->vimeo_uri,
                        [
                            'name' => $dbVideo->name,
                            'description' => $dbVideo->description,
                        ],
                        'PATCH');

                    if ($result["status"] == 200) {
                        # If request went well, we need to mark video as synchronized
                        # by adding several extra seconds to make it greater than updated_at
                        $dbVideo->synchronized_at = now()->addSeconds(5);
                        $dbVideo->save();
                    }

                } else if (
                    !$dbVideo
                    || $dbVideo->synchronized_at < new Carbon($video["modified_time"])) {
                    # If this video does not exist in our database or it was updated
                    # after previous synchronization, update it in the database
                    Video::updateOrCreate(
                        ["vimeo_uri" => $video["uri"]],
                        [
                            "name" => $video["name"],
                            "description" => $video["description"],
                            "width" => $video["width"],
                            "height" => $video["height"],
                            "pictures" => $video["pictures"],
                            "tags" => $video["tags"],
                            # We add several extra seconds to make it greater than updated_at
                            "synchronized_at" => now()->addSeconds(5),
                        ]
                    );
                }
            }
        }

        return 0;
    }
}
