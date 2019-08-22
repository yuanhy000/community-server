<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class QuestionFollowModel extends Model
{
    protected $table = 'question_follower';
    protected $fillable = ['question_id', 'follower_id'];
}
