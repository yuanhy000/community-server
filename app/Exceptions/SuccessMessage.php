<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/27
 * Time: 15:57
 */

namespace App\Exceptions;


class SuccessMessage extends BaseException
{
    public $code = 201;
    public $msg = 'success!';
    public $errorCode = 0;
}