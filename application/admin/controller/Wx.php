<?php
/**系统配置管理
 * Created by PhpStorm.
 * User: c5820
 * Date: 2018/7/12
 * Time: 16:31
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

/**
 * 系统配置控制器
 * Class Index
 * @package app\admin\controller
 */
class Wx extends Allow
{
    //题目分类列表
    public function getIndex()
    {
        $list = Db::table('wx')->find();
        return $this->fetch("Wx/index",['list'=>$list]);
    }
    //执行修改
    public function postedit(){
        $name = Db::table('wx')->where('id',$_POST['id'])->update($_POST);
        if($name){
            return 1;
        }else{
            return 2;
        }
    }
    public function getAa(){
        $sql = "update user set price= '10'";
        Db::query($sql);
    }
}