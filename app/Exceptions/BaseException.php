<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17
 * Time: 23:49
 */

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    // HTTP状态码 404,200
    public $code = '400';
    // 具体错误信息
    public $msg = '参数错误';
    // 自定义的错误码
    public $errorCode = '1000';

    public function __construct($params = [])
    {
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}