<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\VimeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use TusPhp\Tus\Server as TusServer;

class ExerciseVideoController extends Controller
{
    /** @var TusServer */
    private $tusServer;

    /** @var VimeoService */
    private $vimeoService;

    public function __construct(VimeoService $vimeoService)
    {
        $this->tusServer = app()->make('exercise-video-server');
        $this->vimeoService = $vimeoService;
    }

    public function pushToVimeo(Request $request, string $fileUuid)
    {
        $fileMeta = $this->tusServer->getCache()->get($fileUuid);
        if($fileMeta['name'] == 'new') {
            $video = $this->vimeoService->uploadNew($fileMeta['file_path'], $request->all());
            return Response::json(['id' => $video->id]);
        } else {
            $video = Video::where('vimeo_uri', '/videos/'.$fileMeta['name'])->firstOrFail();
            $this->vimeoService->replaceExisting($video->vimeo_uri, $fileMeta['file_path']);
            return Response::json(null);
        }
    }
}


