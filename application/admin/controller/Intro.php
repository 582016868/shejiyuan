<?php
/**订单管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 订单管理控制器
 * Class Index
 * @package app\admin\controller
 */
class Intro extends Allow
{
    //版式一
    public function getIndex1()
    {
        $id = $_GET['id'];
        $nav1 = Db::table('cates')->where('id',$id)->find();
        $nav2 = Db::table('cates')->where('id',$nav1['pid'])->find();
        $intro = Db::table('intro1')->where('pid',$id)->find();
        return $this->fetch("Intro/index1",['pid'=>$id,'intro'=>$intro,'name1'=>$nav2['name'],'name2'=>$nav1['name']]);
    }
    //版式二
    public function getIndex2()
    {
        //创建请求对象
        $request=request();
        $id = $_GET['id'];
        $nav1 = Db::table('cates')->where('id',$id)->find();
        $nav2 = Db::table('cates')->where('id',$nav1['pid'])->find();
        $list = Db::table('intro2')->where('pid',$id)->paginate(10);
        return $this->fetch('Intro/index2',['id'=>$id,'list'=>$list,'name1'=>$nav2['name'],'name2'=>$nav1['name'],'request'=>$request->param()]);
    }
    //版式三
    public function getIndex3()
    {
        $id = $_GET['id'];
        $nav1 = Db::table('cates')->where('id',$id)->find();
        $nav2 = Db::table('cates')->where('id',$nav1['pid'])->find();
        $intro = Db::table('intro3')->where('pid',$id)->find();
        return $this->fetch("Intro/index3",['pid'=>$id,'intro'=>$intro,'name1'=>$nav2['name'],'name2'=>$nav1['name']]);
    }
    public function getAdd3()
    {
        return $this->fetch('Intro/add3',['pid'=>$_GET['pid']]);
    }
    public function getAdd2()
    {
        return $this->fetch('Intro/add2',['pid'=>$_GET['pid']]);
    }
    public function postDoadd3()
    {
        $file = request()->file('image');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'intro');
            if($info){
                // 成功上传后 获取上传信息
                $data['image'] = $info->getSaveName();
                $data['intro'] = $_POST['intro'];
                $data['pid'] = $_POST['pid'];
                if(empty($_POST['id'])){
                    $result = db('intro3')->insert($data);
                }else{
                    $result = db('intro3')->where('id',$_POST['id'])->update($data);
                }
                if($result){
                    if(!empty($_POST['id'])){
                        unlink($_SERVER['DOCUMENT_ROOT'].'/static/intro/'.$_POST['image']);
                    }
                    return 1;//修改成功
                }else{
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/intro/'.$data['image']);
                    return -1;//添加失败
                }
            }else{
                // 上传失败获取错误信息
                return -1;//添加失败
            }
        }else{
            $data['intro'] = $_POST['intro'];
            $result = db('intro3')->where('id',$_POST['id'])->update($data);
            if($result){
                return 1;//修改成功
            }else{
                return -1;//修改失败
            }
        }
    }
    public function postDoadd2()
    {
        $file = request()->file('image');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'intro');
            if($info){
                // 成功上传后 获取上传信息
                $_POST['image'] = $info->getSaveName();
                $result = db('intro2')->insert($_POST);
                if($result){
                    return 1;//添加成功
                }else{
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/intro/'.$_POST['image']);
                    return -1;//添加失败
                }
            }
        }else{
            unset($_POST['image']);
            $result = db('intro2')->insert($_POST);
            if($result){
                return 1;//添加成功
            }else{
                return -1;//添加失败
            }
        }
    }
    //版式一的添加和修改
    public function postDoadd(){
        $data = array(
            'intro'=>$_POST['intro'],
            'pid'=>$_POST['pid']
        );
        if(!empty($_POST['id'])){
            $result = Db::table('intro1')->where('id',$_POST['id'])->update($data);
        }else{
            $result = Db::table('intro1')->insert($data);
        }
        if($result){
            return 1;
        }else{
            return -1;
        }
    }
    //执行删除
    public function postDel2(){
        $res = Db::table('intro2')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return -1;
        }
    }
    //修改
    public function getEdit3(){
        $info = db('intro3')->where('id',$_GET['id'])->find();
        return $this->fetch('Intro/edit3',['info'=>$info]);
    }
    //修改
    public function getEdit2(){
        $info = db('intro2')->where('id',$_GET['id'])->find();
        return $this->fetch('Intro/edit2',['info'=>$info]);
    }
    //执行修改
    public function postDoedit2(){
        $file = request()->file('image');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'intro');
            if($info){
                // 成功上传后 获取上传信息
                $data['image'] = $info->getSaveName();
                $data['title'] = $_POST['title'];
                $data['intro'] = $_POST['intro'];
                $result = db('intro2')->where('id',$_POST['id'])->update($data);
                if($result){
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/intro/'.$_POST['image']);
                    return 1;//修改成功
                }else{
                    unlink($_SERVER['DOCUMENT_ROOT'].'/static/intro/'.$data['image']);
                    return -1;//添加失败
                }
            }else{
                // 上传失败获取错误信息
                return -1;//添加失败
            }
        }else{
            $data['title'] = $_POST['title'];
            $data['intro'] = $_POST['intro'];
            $result = db('intro2')->where('id',$_POST['id'])->update($data);
            if($result){
                return 1;//修改成功
            }else{
                return -1;//修改失败
            }
        }
    }
    //查看轮播图片
    public function getCheck(){
        $info = Db::table('intro2')->field('image')->where('id',$_GET['id'])->find();
        return $this->fetch('Intro/check',['info'=>$info]);
    }
    //查看题目
    public function getIntro(){
        $info = Db::table('intro2')->field('id,intro')->where('id',$_GET['id'])->find();
        return $this->fetch('Intro/intro',['info'=>$info]);
    }
}