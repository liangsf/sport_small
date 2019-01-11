<?php

class WxPayAction extends MyAction {

    public function __construct() {
        parent::__construct();
    }

    //支付成功的回调
    public function payNotify()
    {
        /* $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        file_put_contents('./log.txt',json_encode($GLOBALS['HTTP_RAW_POST_DATA']) , FILE_APPEND);
        $this->ajaxReturn('支付成功', '支付成功', 200); */
    }

    //调用统一下单
    public function downOrder()
    {
        //$this->openid = 'ovABH47yJ3yBjub1hBz2WSxKG7VI';
        if(!isset($_POST['affair_id'])) {
            $this->ajaxReturn('', '活动id不能为空', 403);
        }
        $affairId = intval($_POST['affair_id']);
        $affairMod = D('Affair');
        $affWhere['id'] = $affairId;
        $affairInfo = $affairMod->where($affWhere)->find();

        $ufMod = D('UF');
        $ufWhere['affair_id'] = $affairId;
        $ufWhere['open_id'] = $this->openid;
        $ufInfo = $ufMod->where($ufWhere)->find();


        $payMod = D('Pay');
        $jsApiParameters = $payMod->downOrder($affairInfo['title'], $ufInfo['out_trade_no'], $affairInfo['promise_money'], $this->openid);
        $this->ajaxReturn($jsApiParameters, '获取成功', 200);
    }



}
