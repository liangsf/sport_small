<?php

// 特殊字符过滤
function htmldecode($str){
    if (empty($str)) return;
    if ($str=="") return $str;
    $str = str_replace("&"," ",$str);
    $str = str_replace(">"," ",$str);
    $str = str_replace("<"," ",$str);
    $str = str_replace("chr(32)"," ",$str);
    $str = str_replace("chr(9)"," ",$str);
    $str = str_replace("chr(34)"," ",$str);
    $str = str_replace("\""," ",$str);
    $str = str_replace("chr(39)"," ",$str);
    $str = str_replace(""," ",$str);
    $str = str_replace("'"," ",$str);
    $str = str_replace("select"," ",$str);
    $str = str_replace("join"," ",$str);
    $str = str_replace("union"," ",$str);
    $str = str_replace("where"," ",$str);
    $str = str_replace("insert"," ",$str);
    $str = str_replace("delete"," ",$str);
    $str = str_replace("update"," ",$str);
    $str = str_replace("like"," ",$str);
    $str = str_replace("drop"," ",$str);
    $str = str_replace("create"," ",$str);
    $str = str_replace("modify"," ",$str);
    $str = str_replace("rename"," ",$str);
    $str = str_replace("alter"," ",$str);
    $str = str_replace("cas"," ",$str);
    $str = str_replace("replace"," ",$str);
    $str = str_replace("%"," ",$str);
    $str = str_replace("or"," ",$str);
    $str = str_replace("and"," ",$str);
    $str = str_replace("!"," ",$str);
    $str = str_replace("xor"," ",$str);
    $str = str_replace("not"," ",$str);
    $str = str_replace("user"," ",$str);
    $str = str_replace("||"," ",$str);
    $str = str_replace("<"," ",$str);
    $str = str_replace(">"," ",$str);
    return $str;
}

// 中文字符串截取
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix='...') {
    if (strlen($str)<=3*$length) {
        return $str;
    }
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset) . $suffix;
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset) . $suffix;
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice . $suffix;
}

/*
 * getValueByField
 * 获取数组字段值
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 数组(各字段值)
 *
 */
function getValueByField($array = array(), $field = 'id') {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $result[] = $value[$field];
        }
    }
    return $result;
}

/*
 * getDataByArray
 * 通过关联数组获取数据
 * @param string $table 表名
 * @param array $array 数组
 * @param string $arrayField 数组的字段
 * @param string $getField 要获取的字段
 *
 * @return array $result 获取的数据
 *      使用参考：通过活动获取对应的课时列表,传递M(课时), 活动数组及课时ID字段
 */
function getDataByArray($table, $array, $arrayField, $getField = '*') {
    $result = array();
    $result = M($table)->where(array($arrayField => array('IN', implode(',', getValueByField($array, $arrayField)))))->field($getField)->select();
    return setArrayByField($result, $arrayField);
}

/*
 * setArrayByField
 * 根据字段重组数组
 * @param array $array 数组 默认为 array()
 * @param string $field 字段名 默认为id
 *
 * @return array $result 重组好的数组
 *
 */
function setArrayByField($array = array(), $field = 'id', $status = 0) {
    $result = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
        	if (!$status) {
        		$result[$value[$field]] = $value;
        	} else {
        		$result[$value[$field]][] = $value;
        	}
        }
    }
    return $result;
}

// 根据字段排序
function sortByField($arr, $field) {

    $count = count($arr);
    for ($i = 0; $i < $count; $i ++) {

        for ($j = $count-1; $j > $i; $j --) {

            if ($arr[$j][$field] < $arr[$i][$field] ) {

                $tmp = $arr[$j];
                $arr[$j] = $arr[$i];
                $arr[$i] = $tmp;
            }
        }
    }
    return $arr;
}

// 获取文件后缀名
function getFileExt($filename) {

    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

function myStripSlashes($str) {
    return stripslashes($str);
}

function myAddSlashes($str) {
    return get_magic_quotes_gpc() ? $str : addslashes($str);
}

function mk_dir($dir, $mode = 0755) {
    if (is_dir($dir) || @mkdir($dir,$mode)) return true;
    if (!mk_dir(dirname($dir),$mode)) return false;
    return @mkdir($dir,$mode);
}


// 获取中英文混搭字符串的长度
function strAllLength($str,$charset='utf-8'){
    if($charset=='utf-8') {
        $str = iconv('utf-8','gb2312',$str);
    }
    $num = strlen($str);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
}







//上传文件方法
function imgUpload($path='',$ext='',$thumb=false,$width="100",$height="100",$maxSize='10000000'){
    header("Content-type: text/html; charset=utf-8");
    import("ORG.Net.UploadFile");
    $upload = new UploadFile();// 实例化上传类
    //$upload->maxSize = $maxSize ;// 设置附件上传大小
    if(empty($ext)){
        $upload->allowExts = array('jpg', 'gif', 'png','docx','doc','xlsx','xls','txt','rar','pdf','mp4','flv');// 设置附件上传类型
    }else{
        $upload->allowExts = explode(',',$ext);// 设置附件上传类型
    }
    //$M_C = S('M_C'.$uid);
    $upload->autoSub = true;
    $upload->subType=date;
    $upload->dateFormat='Y-m-d';
    $upload->hashLevel=1;
    //$filedate = date('Ymd',time()).'_'.$uid;
    $savePath = $path.$filedate.'/';
    $upload->savePath = 'Uploads/'.$savePath;// 设置附件上传目录

    //生成缩略图
    if($thumb){
        $upload->thumb=true;
        $upload->thumbMaxWidth=$width;
        $upload->thumbMaxHeight=$height;
    }
    //$allowExts_arry= array('.jpg', '.gif', '.png', '.bmp','.docx','.doc','.xlsx','.xls','.txt','.rar');
    //$upload->saveRule = str_replace($allowExts_arry,'',$_FILES['Filedata']['name']);
    $upload->saveRule = 'uniqid';//time();
    if(!$upload->upload()) {

        // 上传错误时，提示的错误信息
        $err = $upload->getErrorMsg();
        return $err;
    }else{
        // 上传成功 获取上传文件信息
        $info = $upload->getUploadFileInfo();
        return $info;
    }
}

/**
 * 返回缩略图的图片路径
 */
function thumb($img_url){
	if(empty($img_url)) return '';
	$thumb_img = 'thumb_'.trim(strrchr($img_url,'/'),'/');
	$url_ar = explode('/',$img_url);
	return $url_ar[0].'/'.$url_ar[1].'/'.$url_ar[2].'/'.$thumb_img;
}



function fileUpload() {

    $img['status'] = 0;
    $img['info']   = '';
    $img['data']   = '';
    if ($_FILES['picture']['name']) {
        if ($_POST['name']) {
            unlink('.' . $_POST['name']);
        }
        $up_info = imgUpload('Manage', 'jpg,png,jpeg', false);
        if (!is_array($up_info)) {
            $img['info']   = $up_info;
            echo json_encode($img);
            exit;
        }
        $img['status'] = 1;
        $img['info']   = '上传成功';
        $img['data']   = $up_info[0]['savepath'] . $up_info[0]['savename'];
        echo json_encode($img);
    } else {

        $img['info']   = '请上传图片';
        echo json_encode($img);
    }
}
function WriteFiletext_n($filepath,$string){
	//global $public_r;
	$fp=@fopen($filepath,"w");
	@fputs($fp,$string);
	@fclose($fp);
	/*if(empty($public_r[filechmod]))
	{
		@chmod($filepath,0777);
	}*/
}


/**
 * @param $lat1纬度
 * @param $lng1经度
 * @param $lat2纬度
 * @param $lng2经度
 * @return float|int
 * 计算距离(KM)
 */
function GetDistance($lat1, $lng1, $lat2, $lng2)
{
	$EARTH_RADIUS = 6378.137;
	$radLat1 = rad($lat1);
	$radLat2 = rad($lat2);
	$a = $radLat1 - $radLat2;
	$b = rad($lng1) - rad($lng2);
	$s = 2 * asin(sqrt(pow(sin($a / 2), 2) +
			cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
	$s = $s * $EARTH_RADIUS;
	$s = round($s * 10000) / 10000;
	return $s;
}

/**
 * @param $d
 * @return float
 * 转换弧度
 */
function rad($d)
{
	return $d * pi() / 180.0;
}


function getHttpHeader($headerKey) {
    $headerKey = strtoupper($headerKey);
    $headerKey = str_replace('-', '_', $headerKey);
    $headerKey = 'HTTP_' . $headerKey;
    return isset($_SERVER[$headerKey]) ? $_SERVER[$headerKey] : '';
}

//获取 request Payload   json数据
function getPostPayload()
{
    $json = file_get_contents('php://input', 'r');
    return (array)json_decode($json);
}

//验证是否符合参加活动的条件
function checkJoin($affairInfo)
{
    $rs = array();
    $rs['status'] = true;
    $rs['info'] = '通过校验';

    $close_time = strtotime($affairInfo['close_time']);
    $active_time = strtotime($affairInfo['active_time']);
    $current_time = time();

    if($affairInfo['status'] !=0) {
        $rs['status'] = false;
        $rs['info'] = '活动已结束';
        return $rs;
    }

    if( $affairInfo['open_status'] == "0" ) {
        $rs['status'] = false;
        $rs['info'] = '活动已结束报名';
        return $rs;
    }

    if($close_time == '' || $close_time<=0) {
        if($current_time>$active_time) {
            $rs['status'] = false;
            $rs['info'] = '活动已经开始';
            return $rs;
        }

    } else {
        if($current_time>$close_time) {
            $rs['status'] = false;
            $rs['info'] = '活动已关闭报名';
            return $rs;
        }
    }

    if($affairInfo['quota']>0) {
        $where['affair_id'] = $affairInfo['id'];
        $where['status'] = array('gt', 0);
        $count = D('UF')->where($where)->count();
        if($affairInfo['quota']<=$count) {
            $rs['status'] = false;
            $rs['info'] = '满员啦！';
            return $rs;
        }
    }

    return $rs;
}

/**
 * 求两个已知经纬度之间的距离,单位为米
 *
 * @param lng1 $ ,lng2 经度
 * @param lat1 $ ,lat2 纬度
 * @return float 距离，单位米
 * @author www.Alixixi.com
 */
function getdistance_mi($lng1, $lat1, $lng2, $lat2) {
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}



/**
 * 微信对账单数据处理
 * @param $response 对账单数据
 * @return array 返回结果
 */
function deal_WeChat_response($response){

    $result   = array();
    $response = str_replace(","," ",$response);
    $response = explode(PHP_EOL, $response);

    foreach ($response as $key=>$val){
        if(strpos($val, '`') !== false){
            $data = explode('`', $val);
            array_shift($data); // 删除第一个元素并下标从0开始

            if(count($data) == 27){ // 处理账单数据
                $result['bill'][] = array(
                    'pay_time'             => $data[0], // 交易时间
                    'APP_ID'               => $data[1], // app_id
                    'MCH_ID'               => $data[2], // 商户id
                    'IMEI'                 => $data[4], // 设备号
                    'order_sn_wx'          => $data[5], // 微信订单号
                    'order_sn_sh'          => $data[6], // 商户订单号
                    'user_tag'             => $data[7], // 用户标识
                    'pay_type'             => $data[8], // 交易类型
                    'pay_status'           => $data[9], // 交易状态
                    'bank'                 => $data[10], // 付款银行
                    'money_type'           => $data[11], // 货币种类
                    'total_amount'         => $data[12], // 总金额
                    'coupon_amount'        => $data[13], // 代金券或立减优惠金额
                    'refund_number_wx'     => $data[14], // 微信退款单号
                    'refund_number_sh'     => $data[15], // 商户退款单号
                    'refund_amount'        => $data[16], // 退款金额
                    'coupon_refund_amount' => $data[17], // 代金券或立减优惠退款金额
                    'refund_type'          => $data[18], // 退款类型
                    'refund_status'        => $data[19], // 退款状态
                    'goods_name'           => $data[20], // 商品名称
                    'service_charge'       => $data[22], // 手续费
                    'rate'                 => $data[23], // 费率
                );
            }
            if(count($data) == 7){ // 统计数据
                $result['summary'] = array(
                    'order_num'       => $data[0],    // 总交易单数
                    'turnover'        => $data[1],    // 总交易额
                    'refund_turnover' => $data[2],    // 总退款金额
                    'coupon_turnover' => $data[3],    // 总充值券退款总金额
                    'rate_turnover'   => $data[4],    // 手续费总金额
                    'order_all_money'   => $data[5],    // 订单总金额
                    'apply_refund_turnover'   => $data[6],    // 申请退款总金额
                );
            }
        }
    }

    return $result;
}


?>
