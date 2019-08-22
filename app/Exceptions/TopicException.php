<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5
 * Time: 17:24
 */


namespace App\Exceptions;

class TopicException extends BaseException
{
    public $code = 400;
    public $msg = '选择主题错误，请稍后再试';
    public $errorCode = 30000;
}