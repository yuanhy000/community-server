<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class ArticleVideoModel extends Model
{
    protected $table = 'article_video';
    protected $fillable = ['article_id', 'video_id'];

}
