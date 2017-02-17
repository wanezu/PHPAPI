<?php
/**
 * php返回数据类型调用
 */

namespace Init;
class Response
{
    const JSON = "json";

    /**
     *按综合方式输出通信数据
     * @param integer $code 状态码
     * @param string $message 提示信息
     * @param array $data 数据
     * @param string $type 数据类型
     * @param string $cache 生成缓存数据文件名称
     * @param integer $time 储存缓存的时间 0表示不限制
     * return string
     */
    public static function show($code, $message = '', $data = array(), $type = self::JSON, $cache = NULL, $time = 0)
    {
        if (!is_numeric($code)) {
            return '';
        }
        // $type = isset($_GET['format'])?$_GET['format']:self::JSON;
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );

        if ($type == 'json') {
            self::json($code, $message, $data, $cache, $time);
            exit;
        } elseif ($type == 'array') {
            var_dump($result);
        } elseif ($type == 'xml') {
            self::xmlEncode($code, $message, $data, $cache, $time);
            exit;
        } else {

        }
    }

    /**
     * 按json方式输出通信数据
     * @param integer $code 状态码 && !is_null($cache)
     * @param string $message 提示信息
     * @param array $data 数据
     * @param string $cache 生成缓存数据文件名称
     * @param integer $time 储存缓存的时间 0表示不限制
     * return string
     */
    public static function json($code, $message = '', $data = array(), $cache = NULL, $time = 0)
    {
        if (!is_numeric($code)) {
            return '';
        }

        $resultdata = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        $result = json_encode($resultdata);
        if (!is_null($cache)) {
            File::cacheDate($cache, 'txt', $result, $time);
        }
        echo $result;
        exit;
    }

    /**
     * 按xml方式输出通信数据
     * @param integer $code 状态码 && !is_null($cache)
     * @param string $message 提示信息
     * @param array $data 数据
     * @param string $cache 生成缓存数据文件名称
     * @param integer $time 储存缓存的时间 0表示不限制
     * return string
     */
    public static function xmlEncode($code, $message, $data = array(), $cache = NULL, $time = 0)
    {
        if (!is_numeric($code)) {
            return '';
        }

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<root>\n";
        $xml .= self::xmlToEncode($result);
        $xml .= "</root>\n";
        if (!is_null($cache)) {
            File::cacheDate($cache, 'xml', $xml, $time);
        }
        echo $xml;
        exit;
    }

    public static function xmlToEncode($data)
    {
        $xml = $attr = "";
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }


    //获取数据缓存
    public static function getcache($cache, $type = 'json')
    {
        if ($type == 'xml') {
            echo File::cacheDate($cache, 'xml');
        } else {
            echo File::cacheDate($cache, 'txt');
        }
    }
}
