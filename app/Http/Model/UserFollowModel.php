<?php

namespace App\Http\model;

use Illuminate\Database\Eloquent\Model;

class UserFollowModel extends Model
{
    protected $table = 'user_follower';
    protected $fillable = ['user_id', 'follower_id'];


}
