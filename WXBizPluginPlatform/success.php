<?php
include __DIR__ . '/../vendor/autoload.php'; // 引入 composer 入口文件
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;

include_once "wx/wxBizMsgCrypt.php";
include_once "util/ArrayUtil.php";

$auth_code = empty ( $_GET ['auth_code'] ) ? '' : trim ( $_GET ['auth_code'] );  

Log::debug($auth_code);
file_put_contents('php://stderr', print_r($auth_code, TRUE));

//todo: save auth_code

$newURL = "http://f3157484.ngrok.io/yii/test/WXBizPluginPlatform/success.html";
header('Location: '.$newURL);