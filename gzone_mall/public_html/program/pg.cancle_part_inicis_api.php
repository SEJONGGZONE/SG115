<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
$tid = $ocl[oc_tid]; // PG사 거래 번호

// @ 2017-02-22 LCY :: 에스크로 주문건이라면
$mid = $siteInfo[s_pg_code];
$key         = $siteInfo['s_pg_apikey']; // "ItEQKi3rY7uvDS8l";
if( preg_match("/(".$siteInfo[s_pg_code_escrow].")/",$tid ) === true && $siteInfo[s_pg_code_escrow] != ''){
	$mid = $siteInfo[s_pg_code_escrow];
	$key         = $siteInfo['s_pg_escrow_apikey']; // "ItEQKi3rY7uvDS8l";
}


$_cancel_price = trim($_total_amount); 

// 승인금액
$tmp = _MQ("
	SELECT 
		IFNULL(SUM( op_price * op_cnt + op_delivery_price + op_add_delivery_price - op_usepoint - op_use_discount_price), 0) AS op_sum,
		IFNULL(SUM( cl_price ),0) AS cl_sum,
		IFNULL( (SUM( op_price * op_cnt + op_delivery_price + op_add_delivery_price - op_usepoint - op_use_discount_price) - IFNULL(SUM( cl_price ), 0) ) ,0) AS sum
	FROM smart_order_product 
	LEFT JOIN (SELECT cl_price ,cl_oordernum , cl_pcode FROM smart_order_coupon_log WHERE cl_type = 'product' ) AS tbl ON (cl_oordernum = op_oordernum AND cl_pcode = op_pcode)
	WHERE 
		IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 ) and 
		op_oordernum = '".$_ordernum."'
");
$_confirm_price = ($tmp['sum']- $_cancel_price) > 0 ? ($tmp['sum']- $_cancel_price) : 0;
// --------- JJC : 부분취소 개선 : 2021-02-10 ---------


$type            = "PartialRefund";

// LCY : 2022-12-28 : 이니시스TX모듈 교체 -- 결제수단 추가
if($ordr[o_paymethod] == "card") $paymethod       = "Card";
if($ordr[o_paymethod] == "iche") $paymethod       = "Acct";
if($ordr[o_paymethod] == "virtual") $paymethod       = "Vacct"; // 가상계좌 지원암함
if($ordr[o_paymethod] == "hpp") $paymethod       = "HPP";

$timestamp       = date("YmdHis");
$clientIp    = $_SERVER['SERVER_ADDR'];// "192.0.0.1";			
$price           = $_cancel_price; // 부분취소 금액
$confirmPrice    = $_confirm_price;
$msg             = "normal";

// INIAPIKey + type + paymethod + timestamp + clientIp + mid + tid + price + confirmPrice
$hashData = hash("sha512",(string)$key.(string)$type.(string)$paymethod.(string)$timestamp.(string)$clientIp.(string)$mid.(string)$tid.(string)$price.(string)$confirmPrice); // hash 암호화


//step2. key=value 로 post 요청
$data = array(
	'type' => $type,
	'paymethod' => $paymethod,
	'timestamp' => $timestamp,
	'clientIp' => $clientIp,
	'mid' => $mid,
	'tid' => $tid,
	'price' => $price,
	'confirmPrice' => $confirmPrice,
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

// 취소결과 로그 기록
card_cancle_log_write($tid,$result->resultMsg);	// 카드거래번호 , 결과 메세지

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행