<?php
/**
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/30
 * Time: 16:01
 */
namespace app\index\controller;

Class Splite{
    function mbStringToArray($str) {
        if(empty($str)){
            return false;
        }
        $len = mb_strlen($str);
        $array = array();
        for($i = 0; $i<$len; $i++) {
            $array[] = mb_substr($str, $i, 1);
        }
        return $array;
    }
    function _str_split($str,$length,$byte=false){
        if(mb_strwidth($str) == 1 || empty($str)){
            return $str;
        }
        if($encoding = mb_detect_encoding($str, null, true) === false ){
            return str_split($str, $length);
        }
        if($byte){
            $line = '';
            $split_arr = [];
            foreach (preg_split('//u', $utf8_str,-1,PREG_SPLIT_NO_EMPTY ) as $char) {
                $width = mb_strwidth($line.$char,'utf8');
                if($width <= $length){
                    $line .= $char;
                    continue;
                }
                $split_arr[] = str_pad($line, $width);
                $line = $char;
            }
            return $split_arr;
        }else{
            $str_arr = $this->mbStringToArray($str);
            if($str_arr){
                $chunk_index = 0;
                $k_index = 0;
                $line = '';
                $chunks = [];
                foreach ($str_arr as $key=>$val){
                    $line .= $val;
                    $chunks[$k_index] = $line;
                    if ($chunk_index++ == $length-1) {
                        $line = '';
                        $k_index++;
                        $chunk_index = 0;
                    }
                }
            }
            return $chunks;
        }
    }
}