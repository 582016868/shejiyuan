<?php
/**学校管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 轮播管理控制器
 * Class Index
 * @package app\admin\controller
 */
class Lunbo extends Allow
{
    //轮播列表
    public function getIndex()
    {
        //创建请求对象
        $request=request();
        //获取所有数据
        $list=db('lunbo')->paginate(15);
        return $this->fetch("Lunbo/index",["list"=>$list,'request'=>$request->param()]);
    }
    //添加页
    public function getAdd()
    {
        return $this->fetch("Lunbo/add",['uname'=>session('uname')]);
    }
    //执行删除
    public function postdel(){
        $img = db('lunbo')->where('id',$_POST['id'])->find();
        $res = Db::table('lunbo')->where('id',$_POST['id'])->delete();
        if($res){
            unlink($_SERVER['DOCUMENT_ROOT'].'/static/lunbo/'.$img['image']);
            return 1;
        }else{
            return -1;
        }
    }
    //执行添加
    public function postDoadd()
    {
        $file = request()->file('image');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'lunbo');
        if($info){
            // 成功上传后 获取上传信息
            $data['image'] = $info->getSaveName();

            $result = db('lunbo')->insert($data);
            if($result){
                return 1;//添加成功
            }else{
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/lunbo/'.$_POST['image']);
                return -1;//添加失败
            }
        }else{
            // 上传失败获取错误信息
            return -1;//添加失败
        }
    }
    //修改
    public function getEdit(){
        $info = db('lunbo')->where('id',$_GET['id'])->find();
        return $this->fetch('Lunbo/edit',['info'=>$info]);
    }
    //执行修改
    public function postDoedit(){
        $file = request()->file('image');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS .'lunbo');
        if($info){
            // 成功上传后 获取上传信息
            $data['image'] = $info->getSaveName();

            $result = db('lunbo')->where('id',$_POST['id'])->update($data);
            if($result){
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/lunbo/'.$_POST['image']);
                return 1;//修改成功
            }else{
                unlink($_SERVER['DOCUMENT_ROOT'].'/static/lunbo/'.$data['image']);
                return -1;//添加失败
            }
        }else{
            // 上传失败获取错误信息
            return -1;//添加失败
        }
    }
    //查看轮播图片
    public function getCheck(){
        $info = Db::table('lunbo')->field('image')->where('id',$_GET['id'])->find();
        return $this->fetch('Lunbo/check',['info'=>$info]);
    }
}