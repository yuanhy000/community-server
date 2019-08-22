<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class VideoModel extends Model
{
    protected $table = 'videos';
    protected $hidden = ['id', 'deleted_at', 'updated_at'];
    protected $fillable = ['video_url', 'video_cover', 'from'];
}
