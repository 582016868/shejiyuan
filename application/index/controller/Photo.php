<?php
/**
 * 拍照查题库接口控制器
 * Created by PhpStorm.
 * @package app\admin\controller
 * @version 18/7/711下午3:08
 * @author  cgqfree
 */
namespace app\index\controller;

use think\Controller;
use think\Db;

class Photo extends Controller
{
    //获取access_token
    public function getIndex()
    {
        $wx = Db::table('wx')->field('apikey,secretkey')->find();
        $url = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id='.$wx['apikey'].'&client_secret='.$wx['secretkey'];
        $info = file_get_contents($url);//发送HTTPs请求并获取返回的数据，推荐使用curl
        $json = json_decode($info);//对json数据解码
        $arr = get_object_vars($json);
        return $arr['access_token'];
    }
    //获取题目
    public function getJieguo($image)
    {
        $img = file_get_contents($image);
        $img = base64_encode($img);
        $data = array(
            'image' => $img,
          	'detect_direction'=>true,
          	'detect_language'=>true
        );
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token='.$this->getIndex();
        $header = array('Content-Type:application/x-www-form-urlencoded');
        $ch = curl_init();
        if(substr($url,0,5)=='https'){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        if($error=curl_error($ch)){
            die($error);
        }
        curl_close($ch);
        $text_arr = json_decode($response,1);
        $str = $text_arr;
        return $str['words_result'];
    }
    //获取上传的图片
    public function postImg()
    {
        // 获取表单上传文件
        $file = request()->file('img');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'uploads');
            if($info){
                $path = $info->getSaveName();
                unset($info);
                $image = 'http://'.$_SERVER['HTTP_HOST'].'/static/uploads/'.$path;
                $str = $this->getJieguo($image);
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/uploads/'.$path);
                $quest = '';
                foreach($str as $key=>$value){
                    $quest .= $value['words'];
                }
                if(strlen($quest) <= 20){
                    $arr = $this -> _str_split($quest,3);
                    for($i=0;$i<count($arr);$i++){
                        $res = Db::table('question')->field('id,image,answer,topicw')->where('topicw','like','%'.$arr[$i].'%')->find();
                        similar_text($quest,$res['topicw'],$percent);
                        if($percent > 60){
                            $res['image'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/topic/'.$res['image'];
                            $res['answer'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/answer/'.$res['answer'];
                            $data = array('status'=>1,'msg'=>'success','data'=>$res);
                            return json_encode($data);
                        }
                    }
                }else{
                    foreach($str as $key=>$value){
                        $res = Db::table('question')->field('id,image,answer,topicw')->where('topicw','like','%'.$value['words'].'%')->find();
                        similar_text($quest,$res['topicw'],$percent);
                        if($percent > 60){
                            $res['image'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/topic/'.$res['image'];
                            $res['answer'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/answer/'.$res['answer'];
                            $data = array('status'=>1,'msg'=>'success','data'=>$res);
                            return json_encode($data);
                        }
                    }
                }
             	$data = array('status'=>1,'msg'=>'error');
            }else{
                $data = array('status'=>1,'msg'=>'error');
            }
        }else{
            $data = array('status'=>1,'msg'=>'error');
        }
        return json_encode($data);
    }
    public function postQuest()
    {
        $quest = request()->param('quest');
        $quest = str_replace('{}','',$quest);
        $num = strpos($quest,'、');
        if($num <= 2){
            $quest = mb_substr($quest,strpos($quest,'、')+1);
        }
        $arr = $this -> _str_split($quest,5);
//        foreach($arr as $value){
        for($i=0;$i<count($arr);$i++){
            $res = Db::table('question')->field('id,image,answer,topicw')->where('topicw','like','%'.$arr[$i].'%')->select();
            foreach($res as $val){
                similar_text($quest,$val['topicw'],$percent);
                if($percent > 60){
                    $val['image'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/topic/'.$val['image'];
                    $val['answer'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/static/answer/'.$val['answer'];
                    $data = array('status'=>1,'msg'=>'success','data'=>$val);
                    return json_encode($data);
                }
            }
        }
        $data = array('status'=>1,'msg'=>'error','data'=>$quest);
        return json_encode($data);

    }
    //  添加科目和程度
    public function getMess(){
        $openid = request()->param('openid');
        $tid = request()->param('tid');
        $data = array(
            'openid'=>$openid,
            'tid'=>$tid,
            'remark' =>request()->param('remark'),
            'kemu' =>request()->param('kemu'),
            'degree' =>request()->param('degree'),
            'addtime'=>time()
        );
        $res = Db::table('user_tiku')->insert($data);
        if($res){
            return 'true';
        }else{
            return 'false';
        }
    }
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
