<?php
/**
 * 全局URL路由配置
 * Created by PhpStorm.
 * @author  cbwfree
 */

use \think\Route;
// admin 模块路由
Route::controller('admin','admin/Index');       // 后台首页
Route::controller('login','admin/Login');       //后台登录
Route::controller('lunbo','admin/Lunbo');     // 学校
Route::controller('user','admin/User');         // 会员
Route::controller('cate','admin/Cate');         // 导航分类
Route::controller('quest','admin/Quest');       // 题库
Route::controller('wx','admin/Wx');             // 配置参数
Route::controller('intro','admin/Intro');    // 订单

// index模块路由
Route::controller('index','index/Index');        // 小程序授权登录


