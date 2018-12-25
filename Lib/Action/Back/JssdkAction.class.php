<?php
/**
 *微信js-sdk
 */
class JssdkAction extends Action {

  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url = $_GET['url'];
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage;
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("jsapi_ticket.php"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $this->set_php_file("jsapi_ticket.php", json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("access_token.php"));
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";

      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    /*$curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);*/

	$res = file_get_contents($url);
    return $res;
  }

  private function get_php_file($filename) {
    //return trim(substr(file_get_contents($filename), 15));
    return trim(substr(F($filename), 15));
  }
  private function set_php_file($filename, $content) {
    /*$fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);*/
	F($filename, $content);
  }

/*
  //用户信息获取--以下方法跟以上都不关联
  public function getAccessTokenByCode($code) {
	// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("code_access_token.php"));
    if ($data->expire_time < time()) {
      //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->appSecret&code=$code&grant_type=authorization_code";
	  $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      print_r($res);
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("code_access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }

	$rs = $this->getUserInfo($res->access_token, $res->openid);
    return $rs;
  }
  //通过access_token获取用户信息
  private function getUserInfo($access_token, $openid) {
	  $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
	  $res = json_decode($this->httpGet($url));
	  return $res;
  }*/

    //发送客服消息
    public function sendCustomerMsg($touser,$content,$type){

            $ACC_TOKEN = $this->getAccessToken();
            if($type == 'text'){
                $data = '{
                            "touser":"'.$touser.'",
                            "msgtype":"'.$type.'",
                            "text":
                            {
                                 "content":"'.$content.'"
                            }
                        }';
            }else{
                $data = '{
                            "touser":"'.$touser.'",
                            "msgtype":"'.$type.'",
                            "image":
                            {
                              "media_id":"'.$content.'"
                            }
                        }';

            }

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACC_TOKEN;
        $result = $this->curl_post($url,$data);
        return $result;

    }

    public function curl_post($url, $data = null){
             //创建一个新cURL资源
             $curl = curl_init();
             //设置URL和相应的选项
             curl_setopt($curl, CURLOPT_URL, $url);
             if (!empty($data)){
              curl_setopt($curl, CURLOPT_POST, 1);
              curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
             }
             curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

             curl_setopt_array(
                    $curl,
                    array(
                            CURLOPT_URL => $url,
                            CURLOPT_REFERER => $url,
                            CURLOPT_AUTOREFERER => true,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_CONNECTTIMEOUT => 1,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36'
                    )
            );
             //执行curl，抓取URL并把它传递给浏览器
             $output = curl_exec($curl);
             /* if (false === $output) {
                  error_log(curl_error($curl),3,'/test.log');
             } */
             //error_log($output.'\r\n',3,'test.log');

             //关闭cURL资源，并且释放系统资源
             curl_close($curl);
             return $output;
    }

        //发送客服模板消息
public function sendReplyMsg($touser, $cont, $form_id){

        $ACC_TOKEN = $this->getAccessToken();
        //oGrPb0vHnfNqPv8fq4vWX9Mulj2c "'.$touser.'",
        $url = 'http://www.baidu.com';
        $time = date('Y-m-d H:i:s', time());
        $data = '{
            "touser":"'.$touser.'",
            "template_id":"7vryoGur4kRE_ATuuU02VK0fkpS6V6kW8J0jB7mnmW4",
            "form_id":"'.$form_id.'",
            "data":{
                    "keyword1": {
                        "value":"'.$time.'",
                        "color":"#173177"
                    },
                    "keyword2": {
                        "value":"'.$cont.'",
                        "color":"#173177"
                    },
                    "keyword3":{
                        "value":"您好，有问题请联系xxxx",
                        "color":"#173177"
                    }
                }
            }';

        //$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ACC_TOKEN;
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$ACC_TOKEN; //小程序模板消息接口

        $result = $this->curl_post($url,$data);

        return $result;

    }

//日程模板消息
public function sendAffairMsg($touser, $form_id, $cont){

    $ACC_TOKEN = $this->getAccessToken();
    $url = 'http://www.baidu.com';
    $time = date('Y-m-d H:i:s', time());
    $data = '{
        "touser":"'.$touser.'",
        "template_id":"NAk47nmB6pEtqMrbeN7UV_xvSqa3E6BaxDjpEgUIrr0",
        "form_id":"'.$form_id.'",
        "page":"page/home/detail?mid='.$cont['id'].'",
        "data":{
                "keyword1": {
                    "value":"'.$cont['title'].'",
                    "color":"#173177"
                },
                "keyword2": {
                    "value":"'.$cont['time'].'",
                    "color":"#173177"
                },
                "keyword3":{
                    "value":"'.$cont['addr'].'",
                    "color":"#173177"
                }
            }
        }';

    //$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ACC_TOKEN;
    $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$ACC_TOKEN; //小程序模板消息接口

    $result = $this->curl_post($url,$data);

    return $result;

}



}
