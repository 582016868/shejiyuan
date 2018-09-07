<?php
/**
 * 首页管理控制器
 * Created by PhpStorm.
 * @package app\admin\controller
 * @version 18/7/7 下午5:30
 * @author  cgqfree
 */
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    //首页
    public function index(){
        $cate=$this->cates(0);
//        var_dump($cate);die;
        return $this->fetch('Index/index',['cates'=>$cate]);
    }
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
    //判断跳转页面
    public function getIntro()
    {
        //获取导航栏
        $cate=$this->cates(0);
        //企业概况页
        if($_GET['id'] == 10){
            if(isset($_GET['pid'])){
                $intro = Db::table('cates')->where('id',$_GET['pid'])->select();
                $intro1 = Db::table('intro1')->field('intro')->where('pid',$_GET['pid'])->select();
                $intro[0]['intro'] = $intro1[0]['intro'];
                return $this->fetch('About/aboutus',['cate'=>$cate,'intro'=>$intro]);
            }
            $intro = Db::table('cates')->where('pid',$_GET['id'])->select();
            foreach($intro as $k=>$v){
                $intro1 = Db::table('intro1')->field('intro')->where('pid',$intro[$k]['id'])->find();
                $intro[$k]['intro'] = $intro1['intro'];
            }
            return $this->fetch('About/aboutus',['cate'=>$cate,'intro'=>$intro]);
        }elseif ($_GET['id'] == 11){
            $honor = Db::table('cates')->where('id',$_GET['pid'])->select();
            return $this->fetch('Honor/honor',['cate'=>$cate]);
        }
    }
}