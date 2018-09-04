<?php
/**
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/11
 * Time: 13:04
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Login extends Controller
{
  	public function getShan()
    {
        $dir = substr($_SERVER['DOCUMENT_ROOT'],0,-6).'runtime/temp/';
        if(!$handle=@opendir($dir)){     //检测要打开目录是否存在
            die("没有该目录");
        }
        while(false !==($file=readdir($handle))){
            if($file!=="."&&$file!==".."){       //排除当前目录与父级目录
                $file=$dir.$file;
                @unlink($file);
            }
        }
//       $dir = $_SERVER['DOCUMENT_ROOT'].'/static/doc/';
//        if(!$handle=@opendir($dir)){     //检测要打开目录是否存在
//            die("没有该目录");
//        }
//        while(false !==($file=readdir($handle))){
//            if($file!=="."&&$file!==".."){       //排除当前目录与父级目录
//                $file=$dir.$file;
//                @unlink($file);
//            }
//        }
      return 1;
    }
    //加载登录页
    public function getIndex()
    {
        return $this->fetch("Login/login");
    }
    //执行用户登录
    public function postDologin(){
        //创建请求对象
        $request=request();
        //获取用户名和密码
        $name=$request->param('name');
        $pass=md5($request->param('password'));
        //检测用户名和密码
        $res=db('admin')->where("name='{$name}' and password='{$pass}'")->select();
        if($res){
            //设置用户登录信息写入到session
            session('uname',$name);
            return 1;
        }else{
            return 2;
        }
    }
   //退出登录
    public function getOutlogin(){
        session('uname',null);
        $this->redirect('/login/index');
    }
}