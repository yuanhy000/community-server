<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class TopicQuestionModel extends Model
{
    protected $table = 'topic_question';
    protected $fillable = ['topic_id','question_id'];

}
