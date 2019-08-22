<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26
 * Time: 22:45
 */

namespace App\Service;

use App\Exceptions\TokenException;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class Token
{
    public static function generateToken()
    {
        $randChars = Common::getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME'];
        $salt = config('setting.token_salt');
        return md5($randChars . $timestamp . $salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的Token变量不存在');
            }
        }
    }

    public static function getCurrentUid()
    {
        $uid = (new self())->getCurrentTokenVar('uid');
        return $uid;
    }

    //检测用户操作是否合法
    public static function isValidOperate($checkedUID)
    {
        if (!$checkedUID) {
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if ($currentOperateUID == $checkedUID) {
            return true;
        }
        return false;
    }

    public static function checkToken($token)
    {
        $result = Cache::get($token);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}