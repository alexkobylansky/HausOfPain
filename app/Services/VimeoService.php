<?php

namespace App\Services;

use App\Models\Video;
use Vimeo\Laravel\VimeoManager;

class VimeoService
{

    const DEFAULT_NEW_VIDEO_NAME = 'New video';

    /** @var VimeoManager */
    protected $vimeo;

    protected $defaultPrivacy;

    public function __construct(VimeoManager $vimeo)
    {
        $this->vimeo = $vimeo;
        $this->defaultPrivacy = config('vimeo.defaultPrivacy');
    }

    public function uploadNew(string $localFilePath, array $videoData)
    {
        $response = $this->vimeo->upload($localFilePath, [
            'name' => self::DEFAULT_NEW_VIDEO_NAME,
            'privacy' => $this->defaultPrivacy,
        ]);

        $videoData = array_merge($videoData, ['vimeo_uri' => $response]);

        if(empty($videoData['name'])) {
            $videoData['name'] = self::DEFAULT_NEW_VIDEO_NAME;
        }

        return Video::create($videoData);
    }

    public function replaceExisting(string $vimeoUri, string $localFilePath)
    {
        return $this->vimeo->replace($vimeoUri, $localFilePath);
    }

}
