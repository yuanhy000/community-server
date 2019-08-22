<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26
 * Time: 18:54
 */

namespace App\Exceptions;



class CommentException extends BaseException
{
    public $code = 400;
    public $msg = '发表评论错误，请稍后从试';
    public $errorCode = 70000;
}