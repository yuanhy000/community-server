<?php

namespace App\http\model;

use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    protected $table = 'admin';

    public static function checkAdmin($user_name, $user_pass)
    {
        $user = self::where('user_name', '=', $user_name)->first();
        if (!$user) {
            return false;
        }
        if ($user_pass != $user->password) {
            return false;
        }
        return $user;
    }
}
