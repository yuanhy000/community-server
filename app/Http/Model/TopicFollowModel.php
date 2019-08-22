<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class TopicFollowModel extends Model
{

    protected $table = 'topic_follower';
    protected $fillable = ['topic_id', 'follower_id'];

}
