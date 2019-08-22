<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class ImageModel extends Model
{
    protected $table = 'images';
    protected $hidden = ['id', 'deleted_at', 'updated_at'];

    public function getUrlAttribute($value)
    {
        if (starts_with($value, 'http')) {
            return $value;
        } else {
            return config('setting.img_prefix') . $value;
        }
    }
}
