<?php
include_once(dirname(__FILE__).'/inc.php');

if( !$ordernum){
	$ordernum = $_SESSION["session_ordernum"];//주문번호
}
if( $auth_order_directpay !== true){
	error_loc_msg('/?errcd='.__LINE__,'접근 권한이 없습니다.');
}

if($siteInfo[s_pg_type] == 'kcp' && $_POST[ "res_cd"]) {
    include_once(OD_PROGRAM_ROOT.'/shop.order.result_kcp_m.pro.php');
    exit;
}

// 주문정보 추출
$row = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");
if($row['o_ordernum'] == ''){ error_loc_msg('/','필수 정보가 누락되었습니다. 메인페이지로 이동합니다.'); }

// 상품명 추출
$op_cnt = _MQ_result(" select count(*) as cnt from smart_order_product where op_oordernum = '".$ordernum."' ");
$app_product_name = _MQ_result("select op_pname from smart_order_product where op_oordernum = '".$ordernum."' order by op_uid asc ");
if( $op_cnt > 1){ $app_product_name .= " 외 ".$op_cnt."건"; }

// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
//$ordernum = $ordernum; // --> 주문번호
$order_row = $row; // --> 주문배열정보

include(OD_PROGRAM_ROOT."/shop.order.result.vat_calc.php");
// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC


ob_start();
//{{{페이코}}}
if($row['o_paymethod'] == 'payco'){

	if( is_mobile()){
	    require_once(OD_PROGRAM_ROOT."/shop.order.result.payco.php");
	    $submit_onclick = 'payco_open();';
	}else{
		require_once(OD_PROGRAM_ROOT."/shop.order.result.payco.php");
		$submit_onclick = 'payco_open();';
	} 

}
// JJC : 간편결제 - 페이플 : 2021-06-05
else if($row['o_paymethod'] == 'payple'){
    require_once(OD_PROGRAM_ROOT."/shop.order.result.payple.php");
    $submit_id = 'payAction';
}
// JJC : 간편결제 - 페이플 : 2021-06-05

else{
    // PG사 결제 인증요청 페이지
    switch($siteInfo[s_pg_type]) {
		case "inicis" :
			if( is_mobile()){
				require_once(OD_PROGRAM_ROOT."/shop.order.result_inicis_api_m.php");
				$submit_onclick = "call_pay_form();";
			}else{
				require_once(OD_PROGRAM_ROOT."/shop.order.result_inicis_std.php");
				$submit_onclick = "ini_submit();";
			}
			break;
        case "lgpay" :
			if( is_mobile()){
				require_once(OD_PROGRAM_ROOT."/shop.order.result_toss.php");
				$submit_onclick = "requestPayment();";
			}else{
				require_once(OD_PROGRAM_ROOT."/shop.order.result_toss.php");
				$submit_onclick = "requestPayment();";
			} 
            break;
        case "kcp" :

			if( is_mobile()){
	            require_once(OD_PROGRAM_ROOT."/shop.order.result_kcp_m.php");
	            $submit_onclick = "kcp_AJAX();";
			}else{
				require_once(OD_PROGRAM_ROOT."/shop.order.result_kcp.php"); // KCP 는 결제창, 결제처리 만 따로쓴다.
				$submit_onclick = "onload_pay(document.order_info);";
			} 

            break;
        case "daupay" :

			if( is_mobile()){
	            require_once(OD_PROGRAM_ROOT."/shop.order.result_daupay.php"); // 웹뷰방식은 계좌이체 미지원으로 기존방식 그대로 사용 PC와 공통
	            $submit_onclick = "fnSubmit();";
			}else{
				require_once(OD_PROGRAM_ROOT."/shop.order.result_daupay.php");
				$submit_onclick = "fnSubmit();";
			} 

            break;
    }
}
$payView = ob_get_clean();

die(json_encode(array('rst'=>'success','payView'=>$payView)));

