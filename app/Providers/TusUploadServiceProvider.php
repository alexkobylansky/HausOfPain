<?php

namespace App\Providers;

use App\Services\VimeoService;
use TusPhp\Tus\Server as TusServer;
use Illuminate\Support\ServiceProvider;

class TusUploadServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        \TusPhp\Config::set(config_path('tus-server.php'));

        $this->app->singleton('exercise-video-server', function ($app) {
            $server = new TusServer(config('tus-server.default'));

            $server
                ->setApiPath('/admin/upload/exercise-video') // tus server endpoint.
                ->setUploadDir(storage_path('app/uploads/exercise-videos')); // uploads dir.

            return $server;
        });

        $this->app->singleton('group-video-server', function ($app) {
            $server = new TusServer(config('tus-server.default'));

            $server
                ->setApiPath('/app/upload/group-video') // tus server endpoint.
                ->setUploadDir(storage_path('app/uploads/group-videos')); // uploads dir.

            return $server;
        });

        $this->app->singleton('1to1-video-server', function ($app) {
            $server = new TusServer(config('tus-server.default'));

            $server
                ->setApiPath('/app/upload/1to1-video') // tus server endpoint.
                ->setUploadDir(storage_path('app/uploads/1to1-videos')); // uploads dir.

            return $server;
        });

    }
}

