<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 에스크로 정보
$escrow_icon = '';
$escrow_link = '';
switch($siteInfo['s_pg_type']) {
	// 토스페이먼츠
	case 'lgpay':
		//$escrow_icon = $SkinData['skin_url'].'/images/pg_img/pg_lg.jpg';
		$escrow_icon = $SkinData['skin_url'].'/images/c_img/pg_toss.jpg';
		$escrow_link = "window.open('https://pgweb.uplus.co.kr/pg/wmp/mertadmin/jsp/mertservice/s_escrowYn.jsp?mertid={$siteInfo['s_pg_code']}', 'escrow_popup', 'width=345, height=270, scrollbars=no, left=200, top=50'); return false;";
	break;
	// KG 이니시스
	case 'inicis':
		//$escrow_icon = $SkinData['skin_url'].'/images/pg_img/pg_kg.jpg';
		$escrow_icon = $SkinData['skin_url'].'/images/c_img/pg_kg.jpg';
		$escrow_link = "window.open('https://mark.inicis.com/mark/escrow_popup.php?mid={$siteInfo['s_pg_code_escrow']}', 'escrow_popup', 'width=565, height=683, scrollbars=no, left=200, top=50'); return false;";
	break;
	// NHN KCP
	case 'kcp':
		//$escrow_icon = $SkinData['skin_url'].'/images/pg_img/pg_kcp.jpg';
		$escrow_icon = $SkinData['skin_url'].'/images/c_img/pg_kcp.jpg';
		$escrow_link = "window.open('http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd={$siteInfo['s_pg_code']}', 'escrow_popup', 'width=500, height=450, scrollbars=no, left=200, top=50'); return false;";
	break;
	case 'billgate':
		$escrow_icon = $SkinData['skin_url'].'/images/pg_img/pg_gal.jpg';
		$escrow_link = "window.open('https://www.billgate.net/etc/galaxiaEscrowOK.jsp?MERCHANT_ID={$siteInfo['s_pg_code']}', 'escrow_popup', 'width=500, height=450, scrollbars=no, left=200, top=50'); return false;";
	break;
	// 페이조아
	case 'daupay':
		//$escrow_icon = $SkinData['skin_url'].'/images/pg_img/pg_ki.jpg';
		$escrow_icon = $SkinData['skin_url'].'/images/c_img/pg_payjoa.jpg';
		$escrow_link = "window.open('https://agent.daoupay.com/EscrowUsedCheck.jsp?CPID={$siteInfo['s_pg_code']}&CPBUSINESSNO=".str_replace('-', '', $siteInfo['s_company_num'])."', 'escrow_popup', 'width=470, height=484, scrollbars=no, left=200, top=50'); return false;";
	break;
}
if($siteInfo['s_view_escrow_join_info'] != 'Y') { // 에스크로 비노출이라면 변수 초기화
	$escrow_icon = '';
	$escrow_link = '';
}


// SSL인증서
$ssl_icon = '';
$ssl_link = '';
$ssl_etc = '';
switch($siteInfo['s_ssl_pc_img']) {
	case 'N': // 사용안함
		$ssl_icon = '';
		$ssl_link = '';
	break;
	case 'U': // UCERT ssl
		$ssl_icon = 'https://www.ucert.co.kr/images/maincenterContent/trustlogo/ucert_black.gif';
		$ssl_link = "window.open('https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={$siteInfo['s_ssl_pc_sealnum']}&sealid={$siteInfo['s_ssl_pc_sealid']}', 'ssl_popup', 'scrollbars=no,resizable=no,width=565,height=780'); return false;";
	break;
	case 'K': // KISA ssl
		$ssl_icon = 'https://www.ucert.co.kr/image/trustlogo/s_kisa.gif';
		$ssl_link = "window.open('https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={$siteInfo['s_ssl_pc_sealnum']}&sealid={$siteInfo['s_ssl_pc_sealid']}', 'ssl_popup', 'scrollbars=no,resizable=no,width=565,height=780'); return false;";
	break;
	case 'A': // Alpha ssl
		$ssl_icon = 'https://www.ucert.co.kr/image/trustlogo/alphassl_seal.gif';
		$ssl_link = "window.open('https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={$siteInfo['s_ssl_pc_sealnum']}&sealid={$siteInfo['s_ssl_pc_sealid']}', 'ssl_popup', 'scrollbars=no,resizable=no,width=565,height=780'); return false;";
	break;
	case 'C': // Comodo ssl
		// LCY : 2023-11-20 : SSL보안서버추가 -- 섹티고추가 --		
		//$ssl_icon = 'https://www.ucert.co.kr/images/maincenterContent/trustlogo/PositiveSSL_tl_trans.gif';
		//$ssl_link = "window.open('https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={$siteInfo['s_ssl_pc_sealnum']}&sealid={$siteInfo['s_ssl_pc_sealid']}', 'ssl_popup', 'scrollbars=no,resizable=no,width=565,height=780'); return false;";
		$ssl_icon = $SkinData['skin_url'].'/images/c_img/ssl_positive.png';
		$ssl_link = "window.open('https://secure.trust-provider.com/ttb_searcher/trustlogo?v_querytype=W&v_shortname=CL1&v_search=".$system['url']."/&x=6&y=5', 'ssl_popup', 'scrollbars=no,resizable=no,width=380,height=720'); return false;";
	break;
	case 'E': // 기타
		$ssl_icon = '';
		$ssl_link = '';
		$ssl_etc = $siteInfo['s_ssl_pc_img_etc'];
	break;
}
if($siteInfo['s_ssl_check'] != 'Y') $ssl_icon = $ssl_link = '';

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행