<?php
include_once(dirname(__FILE__).'/inc.php');
/* INIinquiry.php
 *
 * 이미 승인된 지불을 확인한다.
 *
 * Date : 2012/
 * Author : mi@inicis.com
 * Project : INIpay V5.0 for PHP
 *
 * http://www.inicis.com
 * Copyright (C) 2007 Inicis, Co. All rights reserved.
 */

$card_fail_order = _MQ_assoc(" select o_ordernum, o_paymethod from smart_order where o_paymethod in('card', 'iche') and o_paystatus='N' and o_canceled = 'N' and npay_order = 'N' and o_rdate >= '". date("Y-m-d", strtotime("-1 days"))." 00:00:00' ");


foreach( $card_fail_order as $k => $v){
	$card_log = _MQ(" select oc_tid from smart_order_cardlog where oc_oordernum ='".$v['o_ordernum']."' ");
	$tid = $card_log['oc_tid'];
	if($tid == '' ){ continue; }

	// @ 2017-02-22 LCY :: 에스크로 주문건이라면
	$mid = $siteInfo[s_pg_code];
	$key         = $siteInfo['s_pg_apikey']; // "ItEQKi3rY7uvDS8l";
	if( preg_match("/(".$siteInfo[s_pg_code_escrow].")/",$tid ) === true && $siteInfo[s_pg_code_escrow] != ''){
		$mid = $siteInfo[s_pg_code_escrow];
		$key         = $siteInfo['s_pg_escrow_apikey']; // "ItEQKi3rY7uvDS8l";
	}

	$oid = $v['o_ordernum'];

    $type        		= "Extra";						
    $paymethod   		= "Inquiry";					
    $timestamp   		= date("YmdHis");
    $originalTid        = $tid;
    $clientIp    		= $_SERVER['SERVER_ADDR']; // "111.222.333.444";
    
	// hash 암호화 : INIAPIKey + type + paymethod + timestamp + clientIp + mid
    $hashData = hash("sha512",(string)$key.(string)$type.(string)$paymethod.(string)$timestamp.(string)$clientIp.(string)$mid);

    //step2. key=value 로 post 요청
    $data = array(
        'type' => $type,
        'paymethod' => $paymethod,
        'timestamp' => $timestamp,
        'clientIp' => $clientIp,
        'mid' => $mid,
        'originalTid' => $originalTid,
        'oid' => $oid,
        'hashData'=> $hashData
	);
		
 
    $url = "https://iniapi.inicis.com/api/v1/extra"; 
    
    $ch = curl_init();                                         
    curl_setopt($ch, CURLOPT_URL, $url);                          
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);                         
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));          
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                          
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8')); 
    curl_setopt($ch, CURLOPT_POST, 1);                        
     
	$response = curl_exec($ch);
    curl_close($ch);
	
	$result = json_decode($response);

	// LCY : 결제 승인 여부 체크 추가 : 2021-10-21
	if( $result->resultCode == '00' && $result->status === '0'  ){ // status : 거래상태 ["0":승인, "1":취소, "9":거래없음] , 가상계좌 거래 시 ["N":입금대기, "Y":입금완료, "C":입금 전 취소]

		$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
		foreach($result as $name => $value) {
			$app_oc_content .= $name . "||" . $value . "§§" ; // 데이터 저장
		}
		
		$ordernum = $oid;
		// - 주문결제기록 저장 ---
		$que = "
			insert smart_order_cardlog set
				 oc_oordernum = '".$ordernum."'
				,oc_tid = '". $tid."'
				,oc_content = '". addslashes($app_oc_content) ."'
				,oc_rdate = now();
		";

		if(!preg_match('/중복/i' , $app_oc_content))  _MQ_noreturn($que);

		include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
	}
}
?>
