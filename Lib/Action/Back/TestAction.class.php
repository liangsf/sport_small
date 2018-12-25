<?php

/**
 * author lsf880101@foxmail.com
 */
//import("ORG.WxPay.lib.WxPay#Api", "", ".php");    先不用这个   对应目录ThinkPHP\Extend\Library\ORG\WxPay

include "./WxPay/lib/WxPay.Api.php";
include './WxPay/lib/WxPay.Notify.php';
class TestAction extends Action
{

    public function FunctionName($value='')
    {
        // code...
    }

}
