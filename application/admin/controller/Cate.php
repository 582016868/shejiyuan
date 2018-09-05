<?php
/**题目分类管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 分类控制器
 * Class Index
 * @package app\admin\controller
 */
class Cate extends Allow
{
    //导航分类列表
    public function getIndex()
    {
        //创建请求对象
        $request=request();
        //获取所有数据
        $list=$this->getCates();
        return $this->fetch("Cate/index",["list"=>$list,'request'=>$request->param()]);
    }
    //调整导航类别顺序
    public function getCates(){
        $data = Db::query("select *,concat(path,',',id) as path from cates order by path");
        //遍历
        foreach($data as $key=>$value){
            //转为数组
            $arr=explode(',',$value['path']);
            //获取逗号个数
            $len=count($arr)-2;
            //字符串重复函数
            $data[$key]['class_name']=str_repeat('— —|',$len).$value['name'];
        }
        return $data;
    }
    //题目分类添加页
    public function getAdd()
    {
        $list=$this->getCates();
        return $this->fetch("Cate/add",["list"=>$list,'uname'=>session('uname')]);
    }
    //执行删除
    public function postdel(){
        $res = Db::table('cates')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
    //执行添加
    public function postDoadd()
    {
        $pid = $_POST['pid'];
        // 判断是一级分类还是二级以上分类
        if($pid == 0){
            //拼接path
            $data['path'] = 0;
        }else{
            //否则获取分类信息
            $info = Db::table('cates')->where('id',$pid)->find();
            //拼接path
            $data['path'] = $info['path'].','.$info['id'];
        }
        $data['name'] = $_POST['name'];
        $data['ename'] = $_POST['ename'];
        $data['lei'] = $_POST['lei'];
        $data['pid'] = $pid;
        // 以id的存在判断是修改还是添加
        if(isset($_POST['id'])){
            $result = Db::table('cates')->where('id',$_POST['id'])->update($data);
        }else{
            $result = Db::table('cates')->insert($data);
        }
        if($result){
            return 1;//操作成功
        }else{
            return -1;//操作失败
        }
    }
    //修改
    public function getedit(){
        $list=$this->getCates();
        $info = db('cates')->where('id',$_GET['id'])->find();
        return $this->fetch('Cate/edit',['list'=>$list,'info'=>$info]);
    }
    //执行修改
    public function postDoedit(){
        $name = Db::table('cates')->where('name',$_POST['name'])->find();
        if($name){
            return 1;//校名已存在
        }else{
            $res = Db::table('cates')->where('id', $_POST['id'])->update($_POST);
            if($res){
                return 3;//修改成功
            }else{
                return 4;//修改失败
            }
        }
    }
    //版式一查看内容

}