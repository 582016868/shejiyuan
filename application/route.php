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
Route::controller('aorder','admin/Aorder');    // 订单

// 接口模块路由
Route::controller('auth','index/Login');        // 小程序授权登录
Route::controller('photo','index/Photo');       // 小程序拍照录题
Route::controller('order','index/Order');       // 小程序下单获取参数
Route::controller('ocr','index/Ocr');            // 百度OCR获取Access Token
Route::controller('search','index/Search');     // 搜索获取学校
Route::controller('pay','index/Pay');            // 支付

