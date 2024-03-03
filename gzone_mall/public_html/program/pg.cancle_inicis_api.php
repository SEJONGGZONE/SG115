<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$tid = $r[oc_tid]; // PG사 거래 번호

// @ 2017-02-22 LCY :: 에스크로 주문건이라면
$mid = $siteInfo[s_pg_code];
$key         = $siteInfo['s_pg_apikey']; // "ItEQKi3rY7uvDS8l";
if( preg_match("/(".$siteInfo[s_pg_code_escrow].")/",$tid ) === true && $siteInfo[s_pg_code_escrow] != ''){
	$mid = $siteInfo[s_pg_code_escrow];
	$key         = $siteInfo['s_pg_escrow_apikey']; // "ItEQKi3rY7uvDS8l";
}



// LCY : 2022-12-28 : 이니시스TX모듈 교체 -- 결제수단 추가
if($osr[o_paymethod] == "card") $paymethod       = "Card";
if($osr[o_paymethod] == "iche") $paymethod       = "Acct";
if($osr[o_paymethod] == "virtual") $paymethod       = "Vacct"; // 가상계좌 지원암함
if($osr[o_paymethod] == "hpp") $paymethod       = "HPP";

$type        = "Refund";
$timestamp   = date("YmdHis");
$clientIp    = $_SERVER['SERVER_ADDR'];// "192.0.0.1";				
$msg         = "normal";

// INIAPIKey + type + paymethod + timestamp + clientIp + mid + tid
$hashData = hash("sha512",(string)$key.(string)$type.(string)$paymethod.(string)$timestamp.(string)$clientIp.(string)$mid.(string)$tid); // hash 암호화


//step2. key=value 로 post 요청
$data = array(
	'type' => $type,
	'paymethod' => $paymethod,
	'timestamp' => $timestamp,
	'clientIp' => $clientIp,
	'mid' => $mid,
	'tid' => $tid,
	'msg' => $msg,
	'hashData'=> $hashData
);
	

$url = "https://iniapi.inicis.com/api/v1/refund";  
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


// 취소 성공 여부
$is_pg_status = $result->resultCode == "00" ? true : false;

// 발행된 현금영수증이 있으면 취소기록
if($is_pg_status){
	_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
}

// 취소결과 로그 기록
card_cancle_log_write($tid,$result->resultMsg);	// 카드거래번호 , 결과 메세지

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행