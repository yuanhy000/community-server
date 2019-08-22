<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26
 * Time: 18:44
 */

namespace App\Service;


use Illuminate\Http\Request;

class Common
{
    public static function curl_get($url, &$httpCode = '0')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $file_contents;
    }

    public static function getRequestInfo(Request $request, $name)
    {
        $data = $request->getContent();
        $data = json_decode($data);
        $value = $data->$name;
        return $value;
    }

    public static function getRequestAddress(Request $request)
    {
//    $result = Input::all();
        $data = $request->getContent();
        $data = json_decode($data, true);
        return $data;
    }

    public static function getRandChar($length)
    {
        $str = null;
        $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }
}