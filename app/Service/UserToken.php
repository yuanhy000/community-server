<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26
 * Time: 22:42
 */

namespace App\Service;

use App\Exceptions\TokenException;
use App\Exceptions\WeChatException;
use App\Http\Enum\ScopeEnum;
use App\Http\model\UserModel;
use Exception;
use Illuminate\Support\Facades\Cache;


class UserToken extends Token
{
    //由用户在微信小程序中登录所产生的code码
    protected $code;
    //小程序开发者的appid
    protected $wxAppId;
    //小程序开发者的appSecret
    protected $wxAppSecret;
    //微信请求地址，将code码换成用户唯一标识openID
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppId = config('wx_conf.app_id');
        $this->wxAppSecret = config('wx_conf.app_secret');
        $this->wxLoginUrl = sprintf(config('wx_conf.login_url'), $this->wxAppId, $this->wxAppSecret, $this->code);
    }

    public function get()
    {
        //向微信服务其发起请求
        $result = Common::curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key和openID异常，微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            //判断微信服务器是否返回错误信息
            if ($loginFail) {
                //返回错误信息
                $this->processLoginError($wxResult);
            } else {
                //办法Token令牌
                return $this->grantToken($wxResult);
            }
        }
    }

    //颁发token令牌
    private function grantToken($wxResult)
    {
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        //查询数据表判断是否存在这个用户
        if (empty(json_decode($user, true))) {
            $uid = $this->newUser($openid);
        } else {
            $userArray = json_decode($user, true);
            $uid = $userArray[0]['id'];
            //得到用户的id
        }
        //准备需要缓存的数据
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    //将Token令牌作为键名，用户信息作为键值将数据存入缓存
    private function saveToCache($cachedValue)
    {
        $token = self::generateToken();
        $value = json_encode($cachedValue);
        //设置token令牌有效时间
        $expiresAt = now()->addMinutes(120);
        $result = Cache::add($token, $value, $expiresAt);
        if (!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }

    //准备所需缓存数据
    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    private function newUser($openid)
    {
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user;
    }

    private function processLoginError($wxResult)
    {
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}