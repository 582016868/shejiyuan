<?php
/**题库管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 题库控制器
 * Class Index
 * @package app\admin\controller
 */
class Quest extends Allow
{
    //题目分类列表
    public function getIndex()
    {
        //创建请求对象
        $request=request();
        $s=$request->get("keyword");
        //获取所有数据
        $list=db('question')->where('id|cname|topicw','like','%'.$s.'%')->paginate(10);
        return $this->fetch("Quest/index",["list"=>$list,'s'=>$s,'request'=>$request->param()]);
    }
    //题目分类添加页
    public function getAdd()
    {
        $list=db('cates')->select();
        return $this->fetch("Quest/add",['list'=>$list]);
    }
    //执行删除
    public function postdel(){
        $image = Db::table('question')->where('id',$_POST['id'])->find();
        $res = Db::table('question')->where('id',$_POST['id'])->delete();
        if($res){
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$image['image'])){
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$image['image']);
            }
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$image['answer'])){
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$image['answer']);
            }
            return 1;
        }else{
            return 2;
        }
    }
    //获取access_token
    public function getAccess()
    {
        $wx = Db::table('wx')->field('apikey,secretkey')->find();
        $url = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id='.$wx['apikey'].'&client_secret='.$wx['secretkey'];
        $info = file_get_contents($url);//发送HTTPs请求并获取返回的数据，推荐使用curl
        $json = json_decode($info);//对json数据解码
        $arr = get_object_vars($json);
        return $arr['access_token'];
    }
    //更具图片获取机器码
    public function getJieguo($image)
    {
        $img = file_get_contents($image);
        $img = base64_encode($img);
        $data = array(
            "image" => $img
        );
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token='.$this->getAccess();
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
        $str = '';
        foreach($text_arr['words_result'] as $key=>$value){
            $str .= $value['words'];
        }
        return $str;
    }
    //执行添加
    public function postDoadd()
    {
        $request = request();

        $file = request()->file('image');
        $file1 = request()->file('answer');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'topic');
        $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static' . DS .'answer');
        if($info && $info1){
            // 成功上传后 获取上传信息
            $_POST['image'] = $info->getSaveName();
            $_POST['answer'] = $info1->getSaveName();
            $image = 'http://'.$_SERVER['HTTP_HOST'].'/static/topic/'.$_POST['image'];

            $str = $this->getJieguo($image);

            $_POST['topicw'] = $str;
          	$result = db('question')->where('topicw',$str)->find();
            if($result){
                return 1;//题目已存在
            }else{
                $res = db('question')->insert($_POST);
                if($res){
                    return 2;//添加成功
                }else{
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$_POST['image']);
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$_POST['answer']);
                    return 3;//添加失败
                }
            }
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
    //修改
    public function getEdit(){
        $list=db('cates')->select();
        $info = db('question')->where('id',$_GET['id'])->find();
        return $this->fetch('Quest/edit',['info'=>$info,'list'=>$list]);
    }
    //执行修改
    public function postDoedit(){
        $file = request()->file('image');
        $file1 = request()->file('answer');
        if(!empty($file) && empty($file1)){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'topic');
            if($info) {
                // 成功上传后 获取上传信息
                $data = array(
                    'cname' => $_POST['cname'],
                    'image' => $info->getSaveName(),
                );
                $image = 'http://'.$_SERVER['HTTP_HOST'].'/static/topic/'.$data['image'];
                $str = $this->getJieguo($image);

                $data['topicw'] = $str;
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
            $res = db('question')->where('id', $_POST['id'])->update($data);
            if($res){
                if(!empty($_POST['image'])){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$_POST['image1']);
                }
                return 2;//修改成功
            }else{
                if(!empty($file)){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$data['image']);
                }
                return 3;//修改失败
            }
        }elseif(!empty($file1) && empty($file)){
            $info = $file1->move(ROOT_PATH . 'public' . DS . 'static' . DS .'answer');
            if($info) {
                // 成功上传后 获取上传信息
                $data = array(
                    'cname' => $_POST['cname'],
                    'answer' => $info->getSaveName(),
                );

            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
            $res = db('question')->where('id', $_POST['id'])->update($data);
            if($res){
                if(!empty($_POST['answer'])){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$_POST['answer1']);
                }
                return 2;//修改成功
            }else{
                if(!empty($file)){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$data['answer']);
                }
                return 3;//修改失败
            }
        }elseif(empty($file) && empty($file1)){
            $data = array('cname'=>$_POST['cname']);
            $res = db('question')->where('id', $_POST['id'])->update($data);
            if($res){
                return 2;//修改成功
            }else{
                return 3;//修改失败
            }
        }elseif(!empty($file) && !empty($file1)){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'topic');
            $info1 = $file1->move(ROOT_PATH . 'public' . DS . 'static' . DS .'answer');
            if($info && $info1){
                // 成功上传后 获取上传信息
                $data = array(
                    'cname' => $_POST['cname'],
                    'image'=> $info->getSaveName(),
                    'answer' => $info1->getSaveName(),
                );
                $image = 'http://'.$_SERVER['HTTP_HOST'].'/static/topic/'.$data['image'];

                $str = $this->getJieguo($image);

                $data['topicw'] = $str;
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
            $res = db('question')->where('id', $_POST['id'])->update($data);
            if($res){
                if(!empty($_POST['answer'])){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$_POST['image1']);
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$_POST['answer1']);
                }
                return 2;//修改成功
            }else{
                if(!empty($file)){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/topic/'.$data['image']);
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/answer/'.$data['answer']);
                }
                return 3;//修改失败
            }
        }
    }
    //查看题目
    public function getTopicw(){
        $info = Db::table('question')->field('id,topicw')->where('id',$_GET['id'])->find();
        return $this->fetch('Quest/topicw',['info'=>$info]);
    }
  	public function postDotopicw(){
      $map['topicw'] = $_POST['topic'];
     $res = Db::table('question')->where('id',$_POST['id'])->update($map);
      if($res){
      	return 1;
      }else{
      	return 2;
      }
    }
    //查看题目图片
    public function getToppic(){
        $info = Db::table('question')->field('image')->where('id',$_GET['id'])->find();
        return $this->fetch('Quest/toppic',['info'=>$info]);
    }
    //查看题目图片
    public function getAnswer(){
        $info = Db::table('question')->field('answer')->where('id',$_GET['id'])->find();
        return $this->fetch('Quest/answer',['info'=>$info]);
    }
}