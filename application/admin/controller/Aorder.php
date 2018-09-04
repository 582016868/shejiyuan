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
class Aorder extends Allow
{
    //订单列表
    public function getIndex()
    {
        //创建请求对象
        $request=request();
        $s=$request->get("keyword");
        //获取所有数据
        $map['u.name|o.order_num'] = ['like',"%".$s."%"];
        $list = Db::field('o.id,o.order_num,o.price,o.openid,o.addtime,u.name')
            ->table('order1 o,user u')->where('o.openid = u.openid')->where($map)->where('o.price > 0')->order('o.addtime','desc')
            ->paginate(15);
        return $this->fetch("Aorder/index",["list"=>$list,'s'=>$s,'request'=>$request->param()]);
    }
    //执行删除
    public function postdel(){
        $res = Db::table('order1')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
    //修改
    public function getedit(){
        $info = db('order1')->where('id',$_GET['id'])->find();
        return $this->fetch('Aorder/edit',['info'=>$info]);
    }
    //执行修改
    public function postDoedit(){
        $res = Db::table('order1')->where('id', $_POST['id'])->update($_POST);
        if($res){
            return 3;//修改成功
        }else{
            return 4;//修改失败
        }
    }
}