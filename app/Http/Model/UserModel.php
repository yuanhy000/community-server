<?php

namespace App\Http\Model;

use App\Service\Token;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['openid'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'openid', 'extend'];

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'user_follower', 'user_id', 'follower_id');
    }


    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)->get();
        return $user;
    }

    public static function getUserBySearch($name, $page, $size)
    {
        $result = self::where('nickname', 'like', "%{$name}%")
            ->orderBy('follower', 'desc')
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])
            ->toArray();
        return $result;
    }

    public static function getUserFollowUser($user_id, $page, $size)
    {
        $result = self::whereHas('users', function ($query) use ($user_id) {
            $query->where('follower_id', '=', $user_id);
        })
            ->orderBy('follower', 'desc')
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])
            ->toArray();
        return $result;
    }

    public static function getUserFans($user_id, $page, $size)
    {
        $result = self::whereHas('users', function ($query) use ($user_id) {
            $query->where('user_id', '=', $user_id);
        })
            ->orderBy('follower', 'desc')
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])
            ->toArray();
        return $result;
    }

    public static function isFollowUser($user)
    {
        if (is_array($user)) {
            for ($i = 0; $i < count($user); $i++) {
//                dd($article[$i]);
                $author_id = $user[$i]['id'];
                $uid = Token::getCurrentUid();
                $record = UserFollowModel::where([['user_id', $author_id], ['follower_id', $uid]])->get();
                if ($record->isEmpty()) {
                    $user[$i]['isFollow'] = false;
                } else {
                    $user[$i]['isFollow'] = true;
                }
            }
        } else {
            $target_id = $user->id;
            $uid = Token::getCurrentUid();
            $record = UserFollowModel::where([['user_id', $target_id], ['follower_id', $uid]])->get();
            if ($record->isEmpty()) {
                $user['isFollow'] = false;
            } else {
                $user['isFollow'] = true;
            }
        }
        return $user;
    }
}
