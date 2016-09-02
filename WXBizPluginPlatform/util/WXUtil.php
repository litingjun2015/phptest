<?php

define(OPEN_MSG_VERIFY_TOKEN, "lwU5ANAtbeNfVbu");
define(OPEN_ENCRYPT_KEY, "lvAnztwetUbepplienNf4ureppixiappwANVbliuwma");
define(OPEN_APPID, "wx27cbbee18fa9fb94");
define(OPEN_APPSECRET, "5b0089a5e58ae2fd9aaee460e3197c13");
define(OPEN_COMPONENT_VERIFY_TICKET_PATH, "/tmp/ticket.txt");

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class WXUtil
{
    /*
     * 获取第三方平台component_access_token
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
     * @param $component_verify_ticket
     */
    public static function get_component_access_token($component_verify_ticket) {
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
      curl_close($ch);
      return  json_decode($output,true)['component_access_token'];
    }
    
    /*
     * 获取第三方平台pre_auth_code
     * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
     * @param $component_verify_ticket
     */
    public static function get_pre_auth_code($component_access_token) {
      $url = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=".$component_access_token;
    //  $post_data =[
    //   'component_appid'=>"wx27cbbee18fa9fb94",
    //   'component_appsecret'=> OPEN_APPSECRET,
    //   'component_verify_ticket'=>$component_verify_ticket,
    //   ];

      $post_data = '{"component_appid":"'.OPEN_APPID.'"}';

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
      curl_close($ch);
      return  json_decode($output,true)['pre_auth_code'];
    }
}
