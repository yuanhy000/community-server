<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class AnswerCommentModel extends Model
{
    protected $table = 'answer_comment';
    protected $fillable = ['answer_id', 'comment_id'];
}
