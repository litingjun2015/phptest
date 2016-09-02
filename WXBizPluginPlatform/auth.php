<?php
include __DIR__ . '/../vendor/autoload.php'; // 引入 composer 入口文件
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;

include_once "wx/wxBizMsgCrypt.php";
include_once "util/ArrayUtil.php";
include_once "util/WXUtil.php";

define(OPEN_MSG_VERIFY_TOKEN, "lwU5ANAtbeNfVbu");
define(OPEN_ENCRYPT_KEY, "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma");
define(OPEN_APPID, "wx27cbbee18fa9fb94");
define(OPEN_APPSECRET, "b4f3d7045e77823c382fdaa6748a0013");
define(OPEN_COMPONENT_VERIFY_TICKET_PATH, "/tmp/ticket.txt");


/*
 * 获取第三方平台component_access_token
* https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
*/

function get_component_access_token($component_verify_ticket) {
  $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
//  $post_data =[
//   'component_appid'=>"wx27cbbee18fa9fb94",
//   'component_appsecret'=> OPEN_APPSECRET,
//   'component_verify_ticket'=>$component_verify_ticket,
//   ];
  
    $post_data = '{"component_appid":"'.OPEN_APPID. '", '
         .'"component_appsecret":"'.OPEN_APPSECRET. '", '
         .'"component_verify_ticket":"'.$component_verify_ticket.'"}';
 
  $ch = curl_init();  

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  // 我们在POST数据哦！
  curl_setopt($ch, CURLOPT_POST, 1);
  // 把post的变量加上
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
  $output = curl_exec($ch);
  //调试使用
  if ($output === FALSE) {
      echo "cURL Error: " . curl_error($ch);
      //Log::debug(curl_error($ch));
  }
  Log::debug($output);
  curl_close($ch);
  return  json_decode($output,true)['component_access_token'];
}
     

$options = [
    'debug'  => true,
    'app_id' => OPEN_APPID, // 生命更新: wxbf2d6507cfc723ac  杜昂微信公众号第三方平台授权: wx27cbbee18fa9fb94
    'secret' => OPEN_APPSECRET, // 生命更新: b4f3d7045e77823c382fdaa6748a0013 杜昂微信公众号第三方平台授权: 5b0089a5e58ae2fd9aaee460e3197c13
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

$str = file_get_contents('php://input');
$xml_tree = new DOMDocument();
$xml_tree->loadXML($str); 
$array_e = $xml_tree->getElementsByTagName('Encrypt');
$encrypt = $array_e->item(0)->nodeValue;
//Log::debug($encrypt);
$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>"; 

$from_xml = sprintf ( $format, $encrypt );         

// 第三方收到公众号平台发送的消息  
$msg = '';  
$errCode = $pc->decryptMsg ( $msg_sign, $timeStamp, $nonce, $from_xml, $msg ); // 解密  
//file_put_contents('php://stderr', print_r("\nerrCode: ", TRUE));
//file_put_contents('php://stderr', print_r($errCode, TRUE));

//file_put_contents('php://stderr', print_r("\nmsg: ", TRUE));
//file_put_contents('php://stderr', print_r($msg, TRUE));

if ($errCode == 0) {
    
    $param = ArrayUtil::xml2array2 ( $msg );
    
    $InfoType = $param["xml"]["InfoType"];
    //file_put_contents('php://stderr', print_r("\nInfoType: ", TRUE));
    //file_put_contents('php://stderr', print_r($InfoType, TRUE));

    switch ($InfoType) {  
        case 'component_verify_ticket' : // 授权凭证  
            $component_verify_ticket = $param["xml"]["ComponentVerifyTicket"];
            
            $ret ['component_verify_ticket'] = $component_verify_ticket;  
            file_put_contents ( OPEN_COMPONENT_VERIFY_TICKET_PATH, $component_verify_ticket ); // 缓存  
            
            Log::debug($component_verify_ticket);
            
            $token = WXUtil::get_component_access_token("ticket@@@J95RoqlKV_LWL7GMNqsr2amkGs9Uw7MqZCosV0QO6_4r1iaLP8PIwCUcZbFYUgl78VCDUiCFC4I5HYBVJV7-Xg");
            Log::debug($token);

            $pre_auth_code = WXUtil::get_pre_auth_code($token);
            Log::debug($pre_auth_code);
            
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

