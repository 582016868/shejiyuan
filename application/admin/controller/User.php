<?php
/**会员管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 会员管理控制器
 * Class Index
 * @package app\admin\controller
 */
class User extends Allow
{
    //会员列表
    public function getIndex()
    {
        //创建请求对象
        $request=request();
        $s=$request->get("keyword");
        //获取所有数据
        $list=db('user')->where('name|class|school','like','%'.$s.'%')->paginate(15);
        return $this->fetch("User/index",["list"=>$list,'s'=>$s,'request'=>$request->param()]);
    }
    //执行删除
    public function postDel(){
        $res = Db::table('user')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
    //根据用户id获取错题
    public function getQuest(){
        //创建请求对象
        $request=request();
        $openid = $_GET['openid'];
        $list = Db::field('u.tid,u.remark,u.kemu,u.degree,u.id,u.openid,q.image')
            ->table('user_tiku u,question q')->where('u.openid',$openid)->where('u.tid = q.id')->paginate(15);
        return $this->fetch('User/quest',['list'=>$list,'openid'=>$openid,'request'=>$request->param()]);
    }
    //查看题目
    public function getCheck(){
        $info = Db::table('question')->field('image')->where('id',$_GET['id'])->find();
        return $this->fetch('User/check',['info'=>$info]);
    }
    //执行删除
    public function postTkdel(){
        $res = Db::table('user_tiku')->where('id',$_POST['id'])->delete();
        if($res){
            return 1;
        }else{
            return 2;
        }
    }
  public function getCz()
    {
    	$request = request();
      	$openid = $request->param('openid');
    	$list = Db::table('order1')->where('openid',$openid)->where('price','>',0)->order('addtime desc')->paginate(15);
 		
        return $this->fetch('User/cz',['list'=>$list,'request'=>$request->param()]);
    }
  public function getJy()
    {
    	$request = request();
   		$openid = $request->param('openid');
        $list = Db::table('order1')->where('openid',$openid)->order('addtime desc')->paginate(15);
    	 return $this->fetch('User/jy',['list'=>$list,'request'=>$request->param()]);
    }
    /**
     * 会员修改
     * @return mixed
     */
    public function getEdit()
    {
        $info = Db::table('user')->where('id',$_GET['id'])->find();
        return $this->fetch('User/edit',['info'=>$info]);
    }
    /**
     * 执行会员修改
     * @return mixed
     */
    public function postDoedit()
    {
        $info = Db::table('user')->where('id',$_POST['id'])->update($_POST);
        if($info){
            return 1;
        }else{
            return 0;
        }
    }
}