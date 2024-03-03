<?php
# LCY : 애플로그인
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(dirname(__FILE__).'/../sns_login.hook.php');


// 필요항목 정리
$sns_login_addon_type = str_replace(array(str_replace('//', '/', OD_ADDONS_ROOT), '/sns_login/'), '', dirname(__FILE__));
if($siteInfo[$SNSField[$sns_login_addon_type]['config_use']] == 'N' || !$siteInfo[$SNSField[$sns_login_addon_type]['config_key']]) error_msgPopup($SNSField[$sns_login_addon_type]['name'].' 로그인 기능이 OFF 상태입니다.'); // 사용여부
$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php';
$key = $siteInfo[$SNSField[$sns_login_addon_type]['config_key']]; // 설정 키
if($_mode) {
	samesiteCookie("SNSOauthMode", $_mode, 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	$SNSOauthMode = $_COOKIE['SNSOauthMode'] = $_mode;
}



// state 가 없다면 로그인 실행
if($_POST['state'] == ''){
	$apple_sign_JS .= '<script>';
	if( preg_match("/(mypage\.modify)/",$_SERVER['HTTP_REFERER']) == true){
		$apple_sign_JS .= " location.href='/?pn=mypage.modify.form&_rurl=".urlencode($_rurl)."&stateApple=true'; ";
	} 
	else  if( preg_match("/(mypage\.leave)/",$_SERVER['HTTP_REFERER']) == true){
		$apple_sign_JS .= " location.href='/?pn=mypage.leave.form&_rurl=".urlencode($_rurl)."&stateApple=true'; ";
	}
	else  if( preg_match("/(member\.join.)/",$_SERVER['HTTP_REFERER']) == true){
		$apple_sign_JS .= " location.href='/?pn=member.join.agree&_rurl=".urlencode($_rurl)."&stateApple=true'; ";
	}
	else{
		$apple_sign_JS .= " location.href='/?pn=member.login.form&_rurl=".urlencode($_rurl)."&stateApple=true'; ";
	}
	
	$apple_sign_JS .= '</script>';
	echo $apple_sign_JS;
	exit;
}


// POST로 RESPONSE
/*
	state :: custom code: 세션에 정보 저장
	code :: apple 서버에서 주는 정보
	id_token :: apple 서버에서 주는 정보 -> base64_decode 필요
*/


if($_POST['id_token'] != '' && $_SESSION['state'] == $_POST['state'] ){

	$token_explode = @explode(".",$_POST['id_token']);
	$SNSLoginData = @json_decode(base64_decode($token_explode[1]),true);	
	
	$sns_id = $SNSLoginData['sub'];

	// 처음 애플로그인 시에는 이름 정보를 준다.
	if( $_POST['user'] != ''){
		$SNSUserData = @json_decode(stripslashes($_POST['user']),true);
		$SNSLoginData['name'] = $SNSUserData['name']['lastName'].$SNSUserData['name']['firstName'];
		if( $SNSLoginData['name'] != '') { 
			$_SESSION['appleSnsName'] = $SNSLoginData['name']; // 애플은 초기 승인 시 1번밖에 이름을 얻지 못하기 때문에 오류 발생 시를 위해 저장
		}
	}else{
		$SNSLoginData['name'] = $_SESSION['appleSnsName']; // 반대로 이름을 얻지 못할경우
	}
	$SNSLoginData['name'] = $SNSLoginData['name'] == '' ? '&확인불가능' : $SNSLoginData['name'];

	$sns_info = array(
		'type'=>'apple',
		'id'=>$sns_id,
		'name'=>$SNSLoginData['name'],
		'email'=>$SNSLoginData['email']
	);
}


// 보안 2중체크 
if($SNSLoginData['aud'] != $key){ $sns_info = array(); } // 클라이언트 키 값이 변조 되었다면 정보를 초기화 한다.


// 모드가 있다면 모드 전달
if($_COOKIE['SNSOauthMode']) {
	$SNSOauthMode = $_COOKIE['SNSOauthMode'];
	samesiteCookie('SNSOauthMode', '', time() -3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
}


// 로그인 처리
include_once(dirname(__FILE__).'/login.pro.php');
