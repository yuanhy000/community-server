<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class TopicArticleModel extends Model
{
    protected $table='topic_article';
    protected $fillable = ['topic_id','article_id'];

}
