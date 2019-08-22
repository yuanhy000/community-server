<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class ArticleImageModel extends Model
{
    protected $table = 'article_image';
    protected $fillable = ['article_id', 'img_id'];


    public function imgUrl()
    {
        return $this->belongsTo(ImageModel::class, 'img_id', 'id');
    }

}
