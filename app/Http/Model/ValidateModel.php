<?php

namespace App\Http\Model;

use App\Exceptions\ParameterException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Service\Common as CommonModel;

class ValidateModel extends Model
{
    public static function checkToken(Request $request)
    {
        $value = CommonModel::getRequestInfo($request, 'code');
        $result = (new self())->isNotNull($value);
        if ($result) {
            return true;
        } else {
            $error = '没有code还想获取Token，睡醒了吗';
            (new self())->throwParamsException($error);
        }
    }

    public static function checkComment(Request $request)
    {
        $value = CommonModel::getRequestInfo($request, 'comment');
        $result = (new self())->isNotNull($value);
        if ($result) {
            return true;
        } else {
            $error = '评论不能为空哦';
            (new self())->throwParamsException($error);
        }
    }

    public static function checkUserInfo(Request $request)
    {
        $nickName = CommonModel::getRequestInfo($request, 'nickName');
        $avatarUrl = CommonModel::getRequestInfo($request, 'avatarUrl');
        if ((new self())->isNotNull($nickName) || (new self())->isNotNull($avatarUrl)) {
            return true;
        } else {
            $error = '没有收到要更新的信息';
            (new self())->throwParamsException($error);
        }
    }

    public static function checkSearch(Request $request)
    {
        $name = CommonModel::getRequestInfo($request, 'name');
        self::valueMustBePositiveInt($request->page);
        self::valueMustBePositiveInt($request->size);
        if ((new self())->isNotNull($name)) {
            return true;
        } else {
            $error = '请输入搜索关键字哦';
            (new self())->throwParamsException($error);
        }
    }

    public static function checkArticle(Request $request)
    {
        $info = CommonModel::getRequestInfo($request, 'info');
        if ((new self())->isNotNull($info)) {
            return true;
        } else {
            $error = '发布内容不能为空哦';
            (new self())->throwParamsException($error);
        }
    }

    public static function valueMustBePositiveInt($id)
    {
        $result = (new self())->isPositiveInteger($id);
        if ($result) {
            return true;
        } else {
            $error = '数据必须是正整数哦';
            (new self())->throwParamsException($error);
        }
    }

    private function isPositiveInteger($value)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function isNotNull($value)
    {
        if (empty($value) && $value != 0) {
            return false;
        } else {
            return true;
        }
    }


    private function throwParamsException($error)
    {
        $exception = new ParameterException([
            'msg' => $error
        ]);
        throw $exception;
    }
}
