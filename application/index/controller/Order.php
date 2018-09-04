<?php
/**
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 10:57
 */
namespace app\index\controller;
use think\Controller;
use think\Db;
//小程序下单获取参数
class Order extends Controller
{
    public function getOrder()
    {
        $request = request();
        $openid = $request->param('openid');
        $result = Db::table('order')->field('addtime,price,status')->where('openid',$openid)->select();
        if($result){
            $list = array('status'=>0,'msg'=>'success','data'=>$result);
        }else{
            $list = array('status'=>1,'msg'=>'fail','data'=>'暂无记录');
        }
        return json_encode($list);
    }
}