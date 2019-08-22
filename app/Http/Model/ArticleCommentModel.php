<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class ArticleCommentModel extends Model
{
    protected $table = 'article_comment';
    protected $fillable = ['article_id', 'comment_id'];
}
