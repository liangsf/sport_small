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

    public function refund()
    {
        try{
    		// $transaction_id = '4200000215201812043375560422';//$_REQUEST["transaction_id"];
            $out_trade_no = 'sport201812261042283066';
    		$total_fee = 100;//$_REQUEST["total_fee"];
    		$refund_fee =100;// $_REQUEST["refund_fee"];
    		$input = new WxPayRefund();
    		// $input->SetTransaction_id($transaction_id);
    		$input->SetOut_trade_no($out_trade_no);
    		$input->SetTotal_fee($total_fee);
    		$input->SetRefund_fee($refund_fee);

    		$config = new WxPayConfig();
    	    $input->SetOut_refund_no("sdkphp".date("YmdHis"));
    	    $input->SetOp_user_id($config->GetMerchantId());
    		print_R(WxPayApi::refund($config, $input));
    	} catch(Exception $e) {
    		//Log::ERROR(json_encode($e));
            echo $e->getMessage();
    	}
    	exit();
    }

}
