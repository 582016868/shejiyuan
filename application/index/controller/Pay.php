<?php
/**支付管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
header("Content-type:text/html;charset=utf-8");
/**
 * 支付控制器
 * Class Index
 * @package app\index\controller
 */
class Pay extends Controller
{
  	public function postCz()
    {
    	$request = request();
      	$openid = $request->param('openid');
        //获取总条数
        $tot = db('order1')->Count();
        //规定每页显示条数
        $rev = 10;
        //获取最大页
        $max = ceil($tot/$rev);
        //处理最大页
        $pp = array();
        for($i=1;$i<=$max;$i++){
            $pp[$i]=$i;
        }
        //获取参数
        $page = $request->get('page');
        if(empty($page)){
            $page = 1;
        }
        //获取偏移量
        $offset = ($page-1)*$rev;
        $sql = "select price,addtime from order1 where openid = '$openid' and price > 0 order by addtime desc limit {$offset},{$rev}";
        //查询
        $list=Db::query($sql);
        if($list){
            $data = array('status'=>0,'msg'=>'success','total'=>$tot,'data'=>$list);
            return json_encode($data);
        }else{
            return 'false';
        }
    }
  public function postJy()
    {
    	$request = request();
   		$openid = $request->param('openid');
        //获取总条数
        $tot = db('order1')->Count();
        //规定每页显示条数
        $rev = 10;
        //获取最大页
        $max = ceil($tot/$rev);
        //处理最大页
        $pp = array();
        for($i=1;$i<=$max;$i++){
            $pp[$i]=$i;
        }
        //获取参数
        $page = $request->get('page');
        if(empty($page)){
            $page = 1;
        }
        //获取偏移量
        $offset = ($page-1)*$rev;
        $sql = "select price,addtime from order1 where openid = '$openid' order by addtime desc limit {$offset},{$rev}";
        //查询
        $list=Db::query($sql);
        if($list){
            $data = array('status'=>0,'msg'=>'success','total'=>$tot,'data'=>$list);
            return json_encode($data);
        }else{
            return 'false';
        }
    }
    //调用支付成功时调用
    public function postOrder()
    {
       $openid = request()->param('openid');
       $data = array(
            'order_num'=>$this->make_password(),
            'openid'=>$openid,
            'price'=>request()->param('price'),
            'addtime'=>time()
        );
        
        $res = Db::table('order1')->insert($data);
        if($res){
            $user = Db::table('user')->field('price')->where('openid',$openid)->find();
            $total = $data['price'] + $user['price'];
            $result = Db::table('user')->where('openid',$openid)->update(['price'=>$total]);
            if($result){
                $list = array('status'=>0,'msg'=>'success');
            }else {
                $list = array('status' => 1, 'msg' => 'fail');
            }
        }else{
            $list = array('status'=>1,'msg'=>'fail');
        }
        return json_encode($list);
    }
    //返回支付所需参数
    public function postPay()
    {
        $wx = Db::table('wx')->field('xappid,shop_descr,mch_secret,mch_id')->find();
        $appid = $wx['xappid']; //小程序appid
        $openid=request()->param('openid'); //用户openid
        $mch_id=$wx['mch_id'];  //商户号
        $key=$wx['mch_secret'];//商户密钥
        $out_trade_no = $this->make_password();//订单号
        $total_fee = request()->param('price');//订单金额
       	$this->price = request()->param('price'); 
        $body = $wx['shop_descr'];//商品描述
        $total_fee = floatval($total_fee*100);
 
        $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
        $return = $weixinpay->pay();
        if($return){
            $list = array('status'=>0,'msg'=>'success','data'=>$return);
        }else{
            $list = array('status'=>1,'msg'=>'fail');
        }
        return json_encode($list);
    }
    // 随机获取字符串
    function make_password( $length = 32 )
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";    
    	 $str = str_shuffle($str);    
	 	return substr($str,0,$length);
    }
}