<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperVideo
 */
class Video extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "vimeo_uri", "name", "description", "width", "height", "pictures", "tags", "synchronized_at"
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'synchronized_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pictures' => 'array',
        'tags' => 'array',
    ];

    public function getPicturesBrowseAttribute() {
        return !empty($this->pictures) && !empty($this->pictures['sizes'])
            ? $this->pictures['sizes'][0]['link']
            : null;
    }

    public function getPicturesReadAttribute() {
        return !empty($this->pictures) && !empty($this->pictures['sizes'])
            ? $this->pictures['sizes'][count($this->pictures['sizes'])-1]['link']
            : null;
    }

}
