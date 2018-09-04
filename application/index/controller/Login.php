<?php
/**
 * 授权登录接口控制器
 * Created by PhpStorm.
 * @package app\admin\controller
 * @version 18/7/711下午3:08
 * @author  cgqfree
 */
namespace app\index\controller;

use think\Controller;
use think\Db;

class Login extends Controller
{
  public function getXia()
    {
        $name = request()->param('name');
        return $this->fetch('Ac/index',['name'=>$name]);
    }
    public function getAc()
    {
      $name = request()->param('name');
                //检查文件是否存在
        if (! file_exists ($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name)) {
            $data = array('status'=>0,'msg'=>'文件不存在');
       	  return json_encode($data);
        } else {
            //以只读和二进制模式打开文件
            $file = fopen ( $_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name,'rb');
            //告诉浏览器这是一个文件流格式的文件
            Header ( "Content-type: application/octet-stream" );
            //请求范围的度量单位
            Header ( "Accept-Ranges: bytes" );
            //Content-Length是指定包含于请求或响应中数据的字节长度
            Header ( "Accept-Length: " . filesize ( $_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name) );
            //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
            Header ( "Content-Disposition: attachment; filename=" .$name);

            //读取文件内容并直接输出到浏览器
            echo fread ( $file, filesize ( $_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name) );
            fclose ( $file );
            exit ();
        }
    }
    public function getDown()
    {
        $openid = request()->param('openid');
        $res = db('user')->field('down')->where('openid',$openid)->find();
        if($res){
            return json_encode($res);
        }else{
            return '{"data": "false"}';
        }
    }
    //每周修改所有用户的下载次数为10
    public function getAa(){
        $wx = Db::table('wx')->field('down')->find();
        $sql = "update user set down= '".$wx['down']."'";
        Db::query($sql);
    }
    //根据code获取openid
    public function postIndex()
    {
        $wx = Db::table('wx')->field('xappid,xsecret')->find();
        //创建请求
        $request = request();
        $code = $request->param('code');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$wx['xappid'].'&secret='.$wx['xsecret'].'&js_code=' . $code . '&grant_type=authorization_code';
        $info = file_get_contents($url);//发送HTTPs请求并获取返回的数据，推荐使用curl
        $json = json_decode($info);//对json数据解码
        $arr = get_object_vars($json);
        $openid = $arr['openid'];
        $content = array(
            'openid' => $openid,
            'name' => $request->param('name'),
            'addtime' => time()
        );
        $user = Db::table('user')->where('openid',$openid)->find();
        if(!$user){
            $res = Db::table('user')->insert($content);
        }
        return json_encode($arr);
    }
  public function getUser()
    {
        $pic = request()->param('pic');
        $nikename = request()->param('nikename');
    	$openid = request()->param('openid');
    	$data = array(
        	'pic'=>$pic,
          	'nikename'=>$nikename
        );
    	$res = db('user')->where('openid',$openid)->update($data);
    	
        if($res){
          	$arr = array('status'=>1,'msg'=>'success','data'=>$data);
			return json_encode($arr);
        }else{
        	$arr = array('status'=>0,'msg'=>'error');
          	return json_encode($arr);
        }
    }
    //获取用户信息
    public function postMessage()
    {
        //创建请求
        $request = request();
        $param = $request->post();
        $openid = $param['openid'];
      	if(isset($param['price'])){
        	$res = Db::table('user')->field('price')->where('openid', $openid)->find();
        }else{
        	$res = Db::table('user')->where('openid', $openid)->find();
        }
        if($res){
            return json_encode($res);
        }else{
            return '{"data": "false"}';
        }
    }
    //修改用户信息
    public function postUpdate()
    {
        //创建请求
        $request = request();
        $param = $request->post();
        $openid = $param['openid'];
        unset($param['openid']);
        $res = Db::table('user')->where('openid', $openid)->update($param);
        if($res){
            return '{"data": "true"}';
        }else{
            return '{"data": "false"}';
        }
    }
}
