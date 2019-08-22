<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/27
 * Time: 15:51
 */

namespace App\Exceptions;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = 'No 用户不存在';
    public $errorCode = 60000;
}