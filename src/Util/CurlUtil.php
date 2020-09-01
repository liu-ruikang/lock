<?php

/**
 * @name CurlUtil
 * @package Lock\Util
 * @desc Curl工具类
 * @author  Ruikang <tianxingjianlrk@gmail.com>
 * @date 2020年8月13日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月13日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Util;

use Lock\Exception\LockException;

class CurlUtil
{
    public static function execCurl(string $url, string $method, array $header = [], string $data_json = "")
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, !empty($header) ? $header : array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        if(strlen($data_json) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        }
        $resp  = curl_exec($ch);
        if(!$resp) {
            $resp = (json_encode(array(array("error" => curl_error($ch), "code" => curl_errno($ch)))));
        }   
        curl_close($ch);
        return $resp;
    }
}

