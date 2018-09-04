<?php
/**
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/11
 * Time: 14:26
 */
namespace app\admin\controller;
use think\Controller;
class Allow extends controller
{
    //初始化方法
    public function _initialize(){
        //检测是否具有用户的登录session信息
        if(!session('uname')){
            $this->redirect('/login/index');
            die;
        }
    }
}