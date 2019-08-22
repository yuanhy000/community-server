<?php

namespace App\Http\Controllers;


use App\Exceptions\ParameterException;
use App\Http\Model\ValidateModel;
use App\Service\Token;
use App\Service\UserToken;
use App\Service\Common;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /*
     * 获得传入的code信息
     * @url /token/user   传入json形式code
     * @http POST
     * */
    public function getToken(Request $request)
    {
        ValidateModel::checkToken($request);
        $code = Common::getRequestInfo($request,'code');
        $ut = new UserToken($code);
        $token = $ut->get();
        return [
            'token' => $token
        ];
    }

    public function checkToken(Request $request)
    {
        if (!$request->token) {
            throw new ParameterException([
                'token不允许为空哦'
            ]);
        }
        $result = Token::checkToken($request->token);
        return [
            'result' => $result
        ];
    }


}
