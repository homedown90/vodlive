<?php
namespace Extend\Util;
class UtilTool {
    static private $KeyCode = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_$';

    /**
     *
     * xss转义
     *
     * @param $data
     * @return array
     */
    static public function html_entity_encode_array($data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = is_array($v) ? self::html_entity_encode_array($v) : htmlspecialchars(htmlspecialchars_decode($v,ENT_QUOTES),ENT_QUOTES,'UTF-8');
        }
        return $result;
    }

    /**
     *
     * json转数组
     *
     * @param $json_data
     * @param int $type
     * @return array|mixed
     */
    static public function json2array($json_data, $type = 0) {
        $json_data = html_entity_decode($json_data);
        $arr_data  = array();
        if ($type == 0) {
            $arr_data = json_decode($json_data, true);
        } else if ($type == 1) //表单json解析后需要做数组转换
        {
            $arr_tmp  = json_decode($json_data, true);
            $arr_data = self::array2dict($arr_tmp, 'name', 'value');
        }
        $arr_data = self::html_entity_encode_array($arr_data);
        return $arr_data;
    }

    /**
     * 将64进制的数字字符串转为10进制的数字字符串
     * @param $m string 64进制的数字字符串
     * @param $len integer 返回字符串长度，如果长度不够用0填充，0为不填充
     * @return string
     * @author ct
     */
    static public function hex64to10($m, $len = 0) {
        $m = (string)$m;
        $hex2 = '';
        $Code = self::$KeyCode;
        for($i = 0, $l = strlen($Code); $i < $l; $i++) {
            $KeyCode[] = $Code[$i];
        }
        $KeyCode = array_flip($KeyCode);

        for($i = 0, $l = strlen($m); $i < $l; $i++) {
            $one = $m[$i];
            $hex2 .= str_pad(decbin($KeyCode[$one]), 6, '0', STR_PAD_LEFT);
        }
        $return = bindec($hex2);

        if($len) {
            $clen = strlen($return);
            if($clen >= $len) {
                return $return;
            }
            else {
                return str_pad($return, $len, '0', STR_PAD_LEFT);
            }
        }
        return $return;
    }

    /**
     * 将10进制的数字字符串转为64进制的数字字符串
     * @param $m string 10进制的数字字符串
     * @param $len integer 返回字符串长度，如果长度不够用0填充，0为不填充
     * @return string
     * @author ct
     */
    static public function hex10to64($m, $len = 0) {
        $KeyCode = self::$KeyCode;
        $hex2 = decbin($m);
        $hex2 = self::str_rsplit($hex2, 6);
        $hex64 = array();
        foreach($hex2 as $one) {
            $t = bindec($one);
            $hex64[] = $KeyCode[$t];
        }
        $return = preg_replace('/^0*/', '', implode('', $hex64));
        if($len) {
            $clen = strlen($return);
            if($clen >= $len) {
                return $return;
            }
            else {
                return str_pad($return, $len, '0', STR_PAD_LEFT);
            }
        }
        return $return;
    }

    /**
     * 将16进制的数字字符串转为64进制的数字字符串
     * @param $m string 16进制的数字字符串
     * @param $len integer 返回字符串长度，如果长度不够用0填充，0为不填充
     * @return string
     * @author ct
     */
 static public function hex16to64($m, $len = 0) {
        $KeyCode = self::$KeyCode;
        $hex2 = array();
        for($i = 0, $j = strlen($m); $i < $j; ++$i) {
            $hex2[] = str_pad(base_convert($m[$i], 16, 2), 4, '0', STR_PAD_LEFT);
        }
        $hex2 = implode('', $hex2);
        $hex2 = self::str_rsplit($hex2, 6);
        foreach($hex2 as $one) {
            $hex64[] = $KeyCode[bindec($one)];
        }
        $return = preg_replace('/^0*/', '', implode('', $hex64));
        if($len) {
            $clen = strlen($return);
            if($clen >= $len) {
                return $return;
            }
            else {
                return str_pad($return, $len, '0', STR_PAD_LEFT);
            }
        }
        return $return;
    }

    /**
     * 功能和PHP原生函数str_split接近，只是从尾部开始计数切割
     * @param $str string 需要切割的字符串
     * @param $len integer 每段字符串的长度
     * @return array
     * @author ct
     */
    static public function str_rsplit($str, $len = 1) {
        if($str == null || $str == false || $str == '') return false;
        $strlen = strlen($str);
        if($strlen <= $len) return array($str);
        $headlen = $strlen % $len;
        if($headlen == 0) {
            return str_split($str, $len);
        }
        $return = array(substr($str, 0, $headlen));
        return array_merge($return, str_split(substr($str, $headlen), $len));
    }
    static public function random($length) {
        $hash = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max = strlen($chars) - 1;
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }
}

?>