<?php
include __DIR__ . '/../vendor/autoload.php'; // 引入 composer 入口文件
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;

include_once "wx/wxBizMsgCrypt.php";
include_once "util/ArrayUtil.php";

$options = [
    'debug'  => true,
    'app_id' => 'wxbf2d6507cfc723ac', // 生命更新: wxbf2d6507cfc723ac wx27cbbee18fa9fb94
    'secret' => 'b4f3d7045e77823c382fdaa6748a0013',
    'token'  => 'lwU5ANAtbeNfVbu',  // easywechat
    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
    ],
    //...
];
$app = new Application($options);

$server = $app->server;
$server->setMessageHandler(function ($message) {
    return "您好！欢迎关注我!";
});

# test xml 160902

$msg = "<xml>\n    <AppId><![CDATA[wx27cbbee18fa9fb94]]></AppId>\n    <Encrypt><![CDATA[0QpNTBrP143Ns0vojQVOiUyZY1u913j658oGMDMxaMzw9gz9ucrcKV2MlZxsZG0cZ/F7nwHIAIz+ZihQewcBuMrO+G55fBWTcWbrD+wxmygQSBHVK4BdevadAMEj47pNTdJEx2aWgCQtxLK2FSTM6UJCJ8qSLTm1kSdKeWQcJUVqkqUmLbzGKiGnWGAmni9j1fDDyzvb5Wx214dn71qQTvjGXHEcW7eCKXg9OvidMFTMuqnoLiDvqZ84LvPxE1m+fVdejA3X5vEi6hk0o/ZUctoyYrCuZX/bhS0VCKmYnFJeE9BOikAHJIHcGO1GqcwDhCx7KCtzWMQnQ10L7iEGcWY33BP2LXMpTlV7+aW3E5d6RH0wDzgxIbwg605ppOqjn5GMx2Yc8R9VS1pckgZIOPyZ+SxavBV+Kg4cyXyYjXVNglXAwtt9psGMKcAQlWpvD4ITQNhyzMxyCnE791aspQ==]]></Encrypt>\n</xml>";
$xml_tree = new DOMDocument();
$xml_tree->loadXML($msg); 
$array_e = $xml_tree->getElementsByTagName('Encrypt');
file_put_contents('php://stderr', print_r("\narray_e: ", TRUE));
file_put_contents('php://stderr', print_r($array_e, TRUE));
$encrypt = $array_e->item(0)->nodeValue;

$msg = "<xml><AppId><![CDATA[wx27cbbee18fa9fb94]]></AppId><CreateTime>1472784183</CreateTime><InfoType><![CDATA[component_verify_ticket]]></InfoType><ComponentVerifyTicket><![CDATA[ticket@@@wQRsWM132I-03M76rmFXzdZCQhwjrmbc5gxwKdhaYga6z_vmXVCkUMn7X1xFuaWOmU2U82o1LhFpdZK9KcAtJQ]]></ComponentVerifyTicket>";
$param = ArrayUtil::xml2array2 ( $msg );

file_put_contents('php://stderr', print_r("param", TRUE));
file_put_contents('php://stderr', print_r($param["xml"]["InfoType"], TRUE));

$component_verify_ticket = $param["xml"]["ComponentVerifyTicket"];
file_put_contents('php://stderr', print_r($param["xml"]["ComponentVerifyTicket"], TRUE));










$response = $app->server->serve();
// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

