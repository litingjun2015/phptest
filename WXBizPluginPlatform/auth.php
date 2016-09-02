<?php
include __DIR__ . '/../vendor/autoload.php'; // 引入 composer 入口文件
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;

include_once "wx/wxBizMsgCrypt.php";
include_once "util/ArrayUtil.php";

define(OPEN_MSG_VERIFY_TOKEN, "lwU5ANAtbeNfVbu");
define(OPEN_ENCRYPT_KEY, "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma");
define(OPEN_APPID, "wx27cbbee18fa9fb94");

$options = [
    'debug'  => true,
    'app_id' => 'wx27cbbee18fa9fb94', // 生命更新: wxbf2d6507cfc723ac  杜昂微信公众号第三方平台授权: wx27cbbee18fa9fb94
    'secret' => 'b4f3d7045e77823c382fdaa6748a0013', // 生命更新: b4f3d7045e77823c382fdaa6748a0013 杜昂微信公众号第三方平台授权: 5b0089a5e58ae2fd9aaee460e3197c13
    'token'  => 'lwU5ANAtbeNfVbu',  // 
    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/WXBizPluginPlatform.log', // XXX: 绝对路径！！！！
    ],
    //...
];
$app = new Application($options);

$server = $app->server;
// 生命更新 公众号测试
//$server->setMessageHandler(function ($message) {
//    return "您好！欢迎关注我!";
//});

//Log::debug(OPEN_MSG_VERIFY_TOKEN);
//Log::debug(OPEN_ENCRYPT_KEY);
//Log::debug(OPEN_APPID);

$timeStamp = empty ( $_GET ['timestamp'] ) ? '' : trim ( $_GET ['timestamp'] );  
$nonce = empty ( $_GET ['nonce'] ) ? '' : trim ( $_GET ['nonce'] );  
$msg_sign = empty ( $_GET ['msg_signature'] ) ? "" : trim ( $_GET ['msg_signature'] );  
$encryptMsg = file_get_contents ( 'php://input' );  
$pc = new WXBizMsgCrypt ( OPEN_MSG_VERIFY_TOKEN, OPEN_ENCRYPT_KEY, OPEN_APPID );  

//todo add
//$postArr = ArrayUtil::xml2array ( $encryptMsg ); // xml对象解析 
$str = file_get_contents('php://input');
$xml_tree = new DOMDocument();
$xml_tree->loadXML($str); 
$array_e = $xml_tree->getElementsByTagName('Encrypt');
$encrypt = $array_e->item(0)->nodeValue;
$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";  

//todo 
$from_xml = sprintf ( $format, $encrypt );          
//$from_xml = sprintf ( $format, $postArr ['Encrypt'] );  
//
// 第三方收到公众号平台发送的消息  
$msg = '';  
$errCode = $pc->decryptMsg ( $msg_sign, $timeStamp, $nonce, $from_xml, $msg ); // 解密  
//file_put_contents('php://stderr', print_r("\nerrCode: ", TRUE));
//file_put_contents('php://stderr', print_r($errCode, TRUE));

if ($errCode == 0) {  
    //file_put_contents('php://stderr', print_r("\nmsg: ", TRUE));
    file_put_contents('php://stderr', print_r($msg, TRUE));
    
    $str = file_get_contents('php://input');
    $xml_tree = new DOMDocument();
    $xml_tree->loadXML($msg); 
    $array_e = $xml_tree->getElementsByTagName('InfoType');
    $InfoType = $array_e->item(0)->nodeValue;
    file_put_contents('php://stderr', print_r($InfoType, TRUE));

    $param = ArrayUtil::xml2array ( $msg );  
    //todo
    switch ($InfoType) {  
    //switch ($param ['InfoType']) {  
        case 'component_verify_ticket' : // 授权凭证  
            $array_e = $xml_tree->getElementsByTagName('ComponentVerifyTicket');
            $component_verify_ticket = $array_e->item(0)->nodeValue;
            Log::debug("component_verify_ticket: ", $component_verify_ticket);
            
            $component_verify_ticket = $param ['ComponentVerifyTicket'];  
            $ret ['component_verify_ticket'] = $component_verify_ticket;  
            file_put_contents ( OPEN_COMPONENT_VERIFY_TICKET_PATH, $component_verify_ticket ); // 缓存  
            break;  
        case 'unauthorized' : // 取消授权  
            $status = 2;  
            break;  
        case 'authorized' : // 授权  
            $status = 1;  
            break;  
        case 'updateauthorized' : // 更新授权  
            break;  
    }  
}  









$response = $app->server->serve();
// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

