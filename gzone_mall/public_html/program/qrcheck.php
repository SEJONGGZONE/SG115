<?PHP
include_once(dirname(__FILE__).'/inc.php');

/*
	- 해당페이지는 mypage.order.pro.php 의 create_qrcode_url 을 통해 생성된다 
	- ticketCode 는 초기 opt_ticketnum 이였으나 qr코드를 위해 opt_uid 로 변경되었다.
*/

// QR코드 체크 페이지 
if( empty($ticketCode) ){
	error_loc("/");
}

// 입점업체 사용여부에 다른 처리
$token_query = http_build_query(array('ticketCode'=>$ticketCode,'tokenType'=>'qrcheck'));
$move_url = '/_ticket_confirm.php?'.$token_query;
if( $SubAdminMode === true){
	if( empty($_COOKIE['AuthCompany']) ){
		$_rurl = enc('e',$move_url);
		error_loc(OD_SUB_ADMIN_URL."?_rurl=".$_rurl."&tokenType=qrcheck");
	}
	else{
		error_loc(OD_SUB_ADMIN_URL.$move_url);
	}
}
else{
	if( empty($_COOKIE['AuthAdmin']) ){
		$_rurl = enc('e',$move_url);
		error_loc(OD_ADMIN_URL."/index.php?_rurl=".$_rurl);
	}
	else{
		error_loc(OD_ADMIN_URL.$move_url);
	}
}
