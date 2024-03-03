<?php
include_once(dirname(__FILE__).'/inc.php');

// 사이트 도메인에 대한 전체 URL
$siteDomain = $system['url'];


// PG사 결제 인증요청 페이지
switch($siteInfo[s_pg_type]) {
	case "inicis" :

		$_pg_mid = $r[o_paymethod]=='virtual'?trim($siteInfo[s_pg_code_escrow]):trim($siteInfo[s_pg_code]);

		if( is_mobile()){
			// 모바일은 별도로 없음
		}else{
			$pgScriptUrl = $_pg_mid == "INIpayTest" ? "https://stgstdpay.inicis.com/stdjs/INIStdPay.js":"https://stdpay.inicis.com/stdjs/INIStdPay.js";
			echo '<script type="text/javascript" src="'.$pgScriptUrl.'"></script>';

		}
		break;
    case "lgpay" :
		if( is_mobile()){
			echo '<script src="https://js.tosspayments.com/v1"></script>';
		}else{
			echo '<script src="https://js.tosspayments.com/v1"></script>';
		} 
        break;
    case "kcp" :
    	include_once(PG_DIR.'/kcp/cfg/site_conf_inc.php');
		if( is_mobile()){
        	echo '<script type="text/javascript" src="'.$siteDomain.OD_PROGRAM_DIR.'/shop.order.result_kcp_approval.js"></script>';
		}else{
			echo '<script type="text/javascript" src="'.$g_conf_js_url.'"></script>';
		} 

        break;
    case "daupay" :

		if( is_mobile()){
		}else{
		} 

        break;
}

// 공통설정 
if($siteInfo['s_payple_use'] == 'Y'){
	if($siteInfo['s_payple_mode']== "service") {
		echo '<script src="https://cpay.payple.kr/js/cpay.payple.1.0.1.js"></script> ';
	}
	else{
		echo '<script src="https://democpay.payple.kr/js/cpay.payple.1.0.1.js"></script>';
	}
}
