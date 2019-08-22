<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class AnswerImageModel extends Model
{
    protected $table = 'answer_image';
    protected $fillable = ['answer_id', 'img_id'];

    public function imgUrl()
    {
        return $this->belongsTo(ImageModel::class, 'img_id', 'id');
    }
}
