<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26
 * Time: 22:44
 */

namespace App\Exceptions;

class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或Token无效';
    public $errorCode = 10001;
}