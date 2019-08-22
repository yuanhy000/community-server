<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5
 * Time: 17:24
 */


namespace App\Exceptions;

class ArticleException extends BaseException
{
    public $code = 400;
    public $msg = '很遗憾，发布文章失败';
    public $errorCode = 30000;
}