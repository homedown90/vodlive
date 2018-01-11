<?php
namespace Extend\util;
class UtilTool {
    /**
     * @param array $data
     */
    static public function html_entity_encode_array($data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = is_array($v) ? self::html_entity_encode_array($v) : htmlspecialchars(htmlspecialchars_decode($v,ENT_QUOTES),ENT_QUOTES,'UTF-8');
        }
        return $result;
    }
    /**
     * @param string $json_data 接受到的json
     * @param number $type      0代表正常解析  1代表解析以后再转换(针对form表单序列化处理)
     * @return Ambigous <mixed, multitype:mixed >
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
}

?>