<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class QuestionImageModel extends Model
{
    protected $table = 'question_image';
    protected $fillable = ['question_id', 'img_id'];


    public function imgUrl()
    {
        return $this->belongsTo(ImageModel::class, 'img_id', 'id');
    }
}
