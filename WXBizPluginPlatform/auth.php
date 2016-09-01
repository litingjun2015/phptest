<?php
include __DIR__ . '/../vendor/autoload.php'; // 引入 composer 入口文件
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;

include_once "wx/wxBizMsgCrypt.php";

$options = [
    'debug'  => true,
    'app_id' => 'wx27cbbee18fa9fb94', // 生命更新: wxbf2d6507cfc723ac  wx27cbbee18fa9fb94
    'secret' => 'b4f3d7045e77823c382fdaa6748a0013',
    'token'  => 'lwU5ANAtbeNfVbu',  // easywechat
    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/WXBizPluginPlatform.log', // XXX: 绝对路径！！！！
    ],
    //...
];
$app = new Application($options);
//$response = $app->server->serve();
// 将响应输出
//$response->send(); // Laravel 里请使用：return $response;

Log::debug("server->setMessageHandler");
$server = $app->server;
$server->setMessageHandler(function ($message) {
    return "您好！欢迎关注我!";
});


$str = file_get_contents('php://input');
Log::debug($str);
$xml_tree = new DOMDocument();
Log::debug("1");
$xml_tree->loadXML($str); 
Log::debug("2");
$array_e = $xml_tree->getElementsByTagName('Encrypt');
Log::debug("3");
$encrypt = $array_e->item(0)->nodeValue;
//file_put_contents('php://stderr', print_r($encrypt, TRUE));
Log::debug("4");
// 第三方收到公众号平台发送的消息
$msg = '';
// Wxbiz_Prpcrypt类参照 消息加密接入文档 ,官网已经提供
//$pc = new Wxbiz_Prpcrypt(COMPONET_KEY);// COMPONET_KEY第三方平台 公众号消息加解密Key

$encodingAesKey = "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma";
$token = "lwU5ANAtbeNfVbu";
$appId = "wx27cbbee18fa9fb94";

Log::debug("5");
$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
// $pc = new WXBizMsgCrypt ( OPEN_MSG_VERIFY_TOKEN, OPEN_ENCRYPT_KEY, OPEN_APPID );  
Log::debug("6");
// $result = $pc->decrypt($encrypt, $appId);//COMPONET_APPID第三方平台AppID

$timeStamp = empty ( $_GET ['timestamp'] ) ? '' : trim ( $_GET ['timestamp'] ); 
Log::debug($timeStamp);
$nonce = empty ( $_GET ['nonce'] ) ? '' : trim ( $_GET ['nonce'] );  
Log::debug($nonce);
$msg_sign = empty ( $_GET ['msg_signature'] ) ? "" : trim ( $_GET ['msg_signature'] ); 
Log::debug($msg_sign);
$encryptMsg = file_get_contents ( 'php://input' ); 
Log::debug($encryptMsg);

$result = $pc->decryptMsg ( $msg_sign, $timeStamp, $nonce, $encrypt, $msg ); // 解密  
//Log::debug("result",$result);
file_put_contents('php://stderr', print_r("\nencrypt: ", TRUE));
file_put_contents('php://stderr', print_r($encrypt, TRUE));
file_put_contents('php://stderr', print_r("\nencryptMsg: ", TRUE));
file_put_contents('php://stderr', print_r($encryptMsg, TRUE));
file_put_contents('php://stderr', print_r("\nresult: ", TRUE));
file_put_contents('php://stderr', print_r($result, TRUE));
file_put_contents('php://stderr', print_r("\nmsg: ", TRUE));
file_put_contents('php://stderr', print_r($msg, TRUE));


if ($result == 0) {  
    $param = ArrayUtil::xml2array ( $msg );  
    switch ($param ['InfoType']) {  
        case 'component_verify_ticket' : // 授权凭证  
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



Log::debug("7");
$xml_tree->loadXML($result[1]);
Log::debug("8");
$array_e = $xml_tree->getElementsByTagName('ComponentVerifyTicket');
Log::debug("9");
$ComponentVerifyTicket = $array_e->item(0)->nodeValue;
Log::debug("10");
if(!empty($ComponentVerifyTicket)){
    Log::debug("11");
    Log::debug("ComponentVerifyTicket",$ComponentVerifyTicket);
    //$db = new ZmtSetModel();//自己建立系统配置表 由于componentVerifyTicket是每10分钟自动获取一次
    //$db->where(array('id'=>1))->update(array('componentVerifyTicket'=>$ComponentVerifyTicket));
}







$encodingAesKey = "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma";
$token = "lwU5ANAtbeNfVbu";
$appId = "wx27cbbee18fa9fb94";



define(OPEN_MSG_VERIFY_TOKEN, "lwU5ANAtbeNfVbu");
define(OPEN_ENCRYPT_KEY, "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma");
define(OPEN_APPID, "wx27cbbee18fa9fb94");

Log::debug(OPEN_MSG_VERIFY_TOKEN);
Log::debug(OPEN_ENCRYPT_KEY);
Log::debug(OPEN_APPID);

$timeStamp = empty ( $_GET ['timestamp'] ) ? '' : trim ( $_GET ['timestamp'] );  
$nonce = empty ( $_GET ['nonce'] ) ? '' : trim ( $_GET ['nonce'] );  
$msg_sign = empty ( $_GET ['msg_signature'] ) ? "" : trim ( $_GET ['msg_signature'] );  
$encryptMsg = file_get_contents ( 'php://input' );  
$pc = new WXBizMsgCrypt ( OPEN_MSG_VERIFY_TOKEN, OPEN_ENCRYPT_KEY, OPEN_APPID );  

//todo add
//$postArr = ArrayUtil::xml2array ( $encryptMsg ); // xml对象解析  
$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";  

//todo 
$from_xml = sprintf ( $format, $encrypt );          
//$from_xml = sprintf ( $format, $postArr ['Encrypt'] );  
//
// 第三方收到公众号平台发送的消息  
$msg = '';  
$errCode = $pc->decryptMsg ( $msg_sign, $timeStamp, $nonce, $from_xml, $msg ); // 解密  
file_put_contents('php://stderr', print_r("\nerrCode: ", TRUE));
file_put_contents('php://stderr', print_r($errCode, TRUE));

if ($errCode == 0) {  
    file_put_contents('php://stderr', print_r("\nmsg: ", TRUE));
    file_put_contents('php://stderr', print_r($msg, TRUE));
    
    
    $str = file_get_contents('php://input');
    Log::debug($str);
    $xml_tree = new DOMDocument();
    Log::debug("1");
    $xml_tree->loadXML($msg); 
    Log::debug("2");
    $array_e = $xml_tree->getElementsByTagName('InfoType');
    Log::debug("3");
    $InfoType = $array_e->item(0)->nodeValue;
    file_put_contents('php://stderr', print_r($InfoType, TRUE));
    Log::debug("4");

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

