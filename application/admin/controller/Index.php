<?php
/**
 * 管理员管理控制器
 * Created by PhpStorm.
 * @package app\admin\controller
 * @version 18/7/7 下午5:30
 * @author  cgqfree
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 首页和管理员管理控制器
 * Class Index
 * @package app\admin\controller
 */
class Index extends Allow
{
    //获取无限分类递归数据
    public function cates($pid){
        $data=Db::table("cates")->where('pid',$pid)->select();
        //遍历
        $data1=array();
        foreach($data as $key=>$value){
            $value['shop']=$this->cates($value['id']);
            $data1[]=$value;
        }
        return $data1;
    }
    /**
     * 首页
     * @return mixed
     */
    public function getIndex()
    {
        //获取无限分类递归数据
        $cate=$this->cates(0);
        return $this->fetch('Admin/index',['cate'=>$cate,'uname'=>session('uname')]);
    }
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        //获取无限分类递归数据
        $cate=$this->cates(0);
        var_dump($_GET);die;
        return $this->fetch('Admin/index',['cate'=>$cate,'uname'=>session('uname')]);
    }
    /**
     * 首页欢迎页
     * @return mixed
     */
    public function getWelcome()
    {
        return $this->fetch('Admin/welcome');
    }
    /**
     * 管理员列表
     * @return mixed
     */
    public function getAdminlist()
    {
        //创建请求对象
        $request=request();
        //获取所有数据
        $list=Db::field('name,id,addtime')->table('admin')->paginate(10);
        return $this->fetch("Admin/adminlist",["list"=>$list,'request'=>$request->param()]);
    }
    /**
     * 管理员添加
     * @return mixed
     */
    public function getAdminadd()
    {
        return $this->fetch('Admin/adminadd');
    }
    /**
     * 执行管理员添加
     * @return mixed
     */
    public function postDoadminadd()
    {
        $name = Db::table('admin')->where('name',$_POST['name'])->find();
        if($name){
            return 1;//用户已存在
        }else{
            unset($_POST['password2']);
            $_POST['addtime'] = time();
            $_POST['password'] = md5($_POST['password']);
            $res = Db::table('admin')->insert($_POST);
            if($res){
                return 2;//添加成功
            }else{
                return 3;//添加失败
            }
        }
    }
    /**
     * 管理员修改
     * @return mixed
     */
    public function getAdminedit()
    {
        $admin = Db::table('admin')->where('id',$_GET['id'])->find();
        return $this->fetch('Admin/adminedit',['admin'=>$admin]);
    }
    /**
     * 执行管理员修改
     * @return mixed
     */
    public function postDoadminedit()
    {
        $name = Db::table('admin')->where('name',$_POST['name'])->find();
        $password = Db::table('admin')->where('id',$_POST['id'])->find();
        if($name && $name['name'] !== $password['name']){
            return 1;//用户已存在
        }elseif($password['password'] !== md5($_POST['password1'])){
            return 2;//原密码错误
        }else{
            unset($_POST['password1']);
            $_POST['password'] = md5($_POST['password']);
            $res = Db::table('admin')->where('id', $_POST['id'])->update($_POST);
            if($res){
                return 3;//修改成功
            }else{
                return 4;//修改失败
            }
        }
    }
    /**
     * 管理员删除
     * @return mixed
     */
    public function postAdmindel()
    {
        $res = Db::table('admin')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
    /**
     * 角色列表
     * @return mixed
     */
    public function getRole()
    {
        //创建请求对象
        $request=request();
        //获取所有数据
        $list=db('admin_role')->paginate(10);
        return $this->fetch("Admin/role",["list"=>$list,'request'=>$request->param()]);
    }
    /**
     * 角色添加
     * @return mixed
     */
    public function getRoleadd()
    {
        return $this->fetch('Admin/roleadd');
    }
    /**
     * 执行角色添加
     * @return mixed
     */
    public function postDoroleadd()
    {
        $res = Db::table('admin_role')->insert($_POST);
        if($res){
            return 2;//添加成功
        }else{
            return 3;//添加失败
        }
    }
    /**
     * 角色修改
     * @return mixed
     */
    public function getRoleedit()
    {
        $admin = Db::table('admin_role')->where('id',$_GET['id'])->find();
        return $this->fetch('Admin/roleedit',['admin'=>$admin]);
    }
    /**
     * 执行角色修改
     * @return mixed
     */
    public function postDoroleedit()
    {
        $res = Db::table('admin_role')->where('id', $_POST['id'])->update($_POST);
        if($res){
            return 3;//修改成功
        }else {
            return 4;//修改失败
        }
    }
    /**
     * 角色删除
     * @return mixed
     */
    public function postRoledel()
    {
        $res = Db::table('admin_role')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
}
