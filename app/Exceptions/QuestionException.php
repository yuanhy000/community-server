<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5
 * Time: 17:24
 */


namespace App\Exceptions;

class QuestionException extends BaseException
{
    public $code = 400;
    public $msg = '目标话题不存在，出问题了';
    public $errorCode = 30000;
}