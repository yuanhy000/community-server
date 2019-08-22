<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/24
 * Time: 18:23
 */

namespace App\Http\Service;


use App\Exceptions\TokenException;
use App\Http\Model\ThirdAppModel;

class AppToken extends Token
{
    public function get($user_name, $user_pass)
    {
        $result = ThirdAppModel::check($user_name, $user_pass);
        if (!$result) {
//            throw new TokenException([
//                'msg' => '授权失败，难受哦',
//                'errorCode' => 10004
//            ]);
            return null;
        } else {
            $scope = $result->scope;
            $uid = $result->id;
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    private function saveToCache($values)
    {
        $token = self::generateToken();
        $value = json_encode($values);
        //设置token令牌有效时间
        $expiresAt = now()->addMinutes(120);
        $result = \Cache::add($token, $value, $expiresAt);
        if (!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }
}