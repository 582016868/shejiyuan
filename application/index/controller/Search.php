<?php
/**
 * 学校搜索接口控制器
 * Created by PhpStorm.
 * @package app\admin\controller
 * @version 18/7/711下午3:08
 * @author  cgqfree
 */
namespace app\index\controller;

use think\Controller;
use think\Db;
use Org\Util\QQMailer;

class Search extends Controller
{
  public function getCc()
  {
  	echo '<pre>';
    $len = strlen('/www/wwwroot/');
    echo substr($_SERVER['DOCUMENT_ROOT'],$len);
    print_r($_SERVER);
  }
  public function getShan()
    {
        $dir = $_SERVER['DOCUMENT_ROOT'].'/static/doc/';
        if(!$handle=@opendir($dir)){     //检测要打开目录是否存在
            die("没有该目录");
        }
        while(false !==($file=readdir($handle))){
            if($file!=="."&&$file!==".."){       //排除当前目录与父级目录
                $file=$dir.$file;
                if(@unlink($file)){
                    echo "文件<b>$file</b>删除成功。<br>";
                }else{
                    echo  "文件<b>$file</b>删除失败!<br>";
                }
            }
        }
    }
    // 搜索学校
    public function getIndex()
    {
        $request = request();
        $name = $request->param('school');
        //获取总条数
        $tot = db('school')->where('name','like','%'.$name.'%')->Count();
        //规定每页显示条数
        $rev = 15;
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

        $list = DB::table('school')->distinct(true)->field('name')->where('name','like','%'.$name.'%')->limit($offset,$rev)->select();

        if($list){
            $data = array('status'=>0,'msg'=>'success','total'=>$tot,'data'=>$list);
            return json_encode($data);
        }else{
            return 'false';
        }
    }
    // 获取用户的错题
    public function getCuo(){
        $request = request();
        $openid = $request -> param('openid');
        //获取总条数
        $tot = db('user_tiku')->where('openid',$openid)->Count();
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
        $sql = "select q.answer,q.image,u.id,u.degree,u.addtime from user_tiku as u,question as q where u.openid = '$openid' and u.tid = q.id order by addtime desc limit {$offset},{$rev}";
        //查询
        $list=Db::query($sql);
   
        if($list){
            $data = array('status'=>0,'msg'=>'success','total'=>$tot,'data'=>$list);
            return json_encode($data);
        }else{
            return 'false';
        }
    }
    // 搜索用户的错题
    public function getSearch(){
        $request = request();
        $con = $request->param('con');
        $openid = $request -> param('openid');
        //获取总条数
        $sql = "select count(q.topicw) from user_tiku as u,question as q where q.topicw like '%{$con}%' and u.openid = '{$openid}' and u.tid = q.id";
        //查询
        $res = Db::query($sql);
        $tot = $res[0]['count(q.topicw)'];
        //规定每页显示条数
        $rev = 2;
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
        $sql = "select q.answer,q.image,u.id,u.degree,u.addtime from user_tiku as u,question as q where q.topicw like '%{$con}%' and u.openid = '{$openid}' and u.tid = q.id limit {$offset},{$rev}";
        //查询
        $list=Db::query($sql);
        if($list){
            $data = array('status'=>0,'msg'=>'success','total'=>$tot,'data'=>$list);
            return json_encode($data);
        }else{
            return 'false';
        }
    }
  public function post($url,$param){

        $ch = curl_init();
        //如果$param是数组的话直接用
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);

        curl_close($ch);
        return $data;
    }
    // 打印用户错题
    public function getDown(){
        $con = request()->param('con');

        $downnum = request()->param('num');
        $openid = request() -> param('openid');
        $num = Db::table('user')->field('down,email')->where('openid',$openid)->find();
        if($num['down'] == 0){
            $list = array('status'=>1,'msg'=>'fail','message'=>'下载次数已用完');
            return json_encode($list);
        }else{
            $jie = $num['down'] - $downnum;
            if($jie<0){
                $list = array('status'=>1,'msg'=>'fail','message'=>'下载次数不足','num'=>$num['down']);
                return json_encode($list);
            }elseif(empty($num['email'])){
                $list = array('status'=>2,'msg'=>'fail','message'=>'请先设置邮箱');
                return json_encode($list);
            }elseif($jie>=0 && !empty($num['email'])){
              	$result = Db::table('user')->where('openid',$openid)->update(['down'=>$jie]);
              	if($result){
                   $wx = Db::table('wx')->field('etitle,econtent')->find();
                  	$len = strlen('/www/wwwroot/');
                	$arr = explode(',',$con);
                  	$arr['where'] = substr($_SERVER['DOCUMENT_ROOT'],$len);
                   $name =  $this -> post('aa.hbg168.com',$arr);
                    // 实例化 QQMailer
                    $mailer = new QQMailer(true);
                    // 添加附件
                    $mailer->addFile($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name);
                    // 邮件标题
                    $title = $wx['etitle'];
                    // 邮件内容
                    $content = $wx['econtent'];
                    // 发送QQ邮件
                    $mailer->send($num['email'], $title, $content);
                  	unlink($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name);
                    $list = array('msg'=>'success','message'=>'邮件已发送','status'=>0);
                    return json_encode($list);
                }else{
                	$list = array('status'=>1,'msg'=>'fail','message'=>'下载失败');
                  	return json_encode($list);
                }
                
            }
        }
    }
    // 用户下载次数用完
    public function getPrice()
    {
        $pri = Db::table('wx')->field('price')->find();
        $con = request()->param('con');
        
        $downnum = request()->param('num');
        $openid = request() -> param('openid');
        $price = Db::table('user')->field('price,email')->where('openid',$openid)->find();
        $jie = $price['price'] - $downnum * $pri['price'];
        if($jie<0){
            $list = array('status'=>1,'msg'=>'fail','message'=>'余额不足','price'=>$price['price']);
            return json_encode($list);
        }elseif(empty($price['email'])){
             $list = array('status'=>2,'msg'=>'fail','message'=>'请先设置邮箱');
            return json_encode($list);
        }else{
            $len = strlen('/www/wwwroot/');
                	$arr = explode(',',$con);
                  	$arr['where'] = substr($_SERVER['DOCUMENT_ROOT'],$len);
            $name =  $this -> post('aa.hbg168.com',$arr);
          $wx = Db::table('wx')->field('etitle,econtent')->find();
            // 实例化 QQMailer
            $mailer = new QQMailer(true);
            // 添加附件
            $mailer->addFile($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name);
            // 邮件标题
            $title = $wx['etitle'];
			// 邮件内容
			$content = $wx['econtent'];
            // 发送QQ邮件
            $mailer->send($price['email'], $title, $content);
          if(file_exists($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name)){
          	unlink($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name);
          }
            
          	//添加交易记录
          	$data = array(
                'order_num'=>$this->order_num(),
                'openid'=>$openid,
                'price'=>'-'.$downnum * $pri['price'],
                'addtime'=>time()
            );
          	$res = Db::table('order1')->insert($data);
            Db::table('user')->where('openid',$openid)->update(['price'=> $jie]);
            $list = array('msg'=>'success','message'=>'邮件已发送','status'=>0,'content'=>$content,'title'=>$title);
            return json_encode($list);
        }
    }
  // 订单号
    function order_num( $length = 32 )
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";    
    	 $str = str_shuffle($str);    
	 	return substr($str,0,$length);
    }
    //删除用户错题
    public function getUserdel()
    {
        $id = request()->param('id');
        $res = Db::table('user_tiku')->where('id',$id)->delete();
        if($res){
            $list = array('status'=>0,'msg'=>'success');
            return json_encode($list);
        }else{
            $list = array('status'=>1,'msg'=>'fail');
            return json_encode($list);
        }
    }
    //删除word生成的文档
    public function getDel()
    {
        $name = request()->param('name');
        if(unlink($_SERVER['DOCUMENT_ROOT'].'/static/doc/'.$name)){
            $list = array('status'=>0,'msg'=>'success');
        }else{
            $list = array('status'=>1,'msg'=>'fail');
        }
        return json_encode($list);
    }
    // 随机获取字符串
    function make_password( $length = 10 )
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";    
    	 $str = str_shuffle($str);    
	 	return substr($str,0,$length);
    }
    function start()
    {
        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:w="urn:schemas-microsoft-com:office:word"
            xmlns="http://www.w3.org/TR/REC-html40"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    	<xml><w:WordDocument><w:View></w:View></xml>
	</head><body align="center">';
    }
    function save($path)
    {

        echo "</body></html>";
        $data = ob_get_contents();
        ob_end_clean();
        $this->wirtefile ($path,$data);
    }

    function wirtefile ($fn,$data)
    {
        $fp=fopen($fn,"wb");
        fwrite($fp,$data);
        fclose($fp);
    }
}
