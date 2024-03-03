<?php
// 구글 로그인 추가
//클라이언트 아이디: $siteInfo['s_google_client_id']

include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(dirname(__FILE__).'/../sns_login.hook.php');



// 필요항목 정리
$sns_login_addon_type = str_replace(array(str_replace('//', '/', OD_ADDONS_ROOT), '/sns_login/'), '', dirname(__FILE__));

if(!$siteInfo[$SNSField[$sns_login_addon_type]['config_key']] || !$siteInfo[$SNSField[$sns_login_addon_type]['config_secret']]) error_msgPopup($SNSField[$sns_login_addon_type]['name'].' 로그인 기능이 OFF 상태입니다.'); // 사용여부
$redirect = 'https://'.$_SERVER['HTTP_HOST'].'/addons/sns_login/'.$sns_login_addon_type.'/callback.php';
$key = $siteInfo[$SNSField[$sns_login_addon_type]['config_key']];
$secret = $siteInfo[$SNSField[$sns_login_addon_type]['config_secret']];
$base = 'https://accounts.google.com';
$CUrl = $base.'/o/oauth2/v2/auth'; // 콜백 URL
$TUrl = 'https://oauth2.googleapis.com/token'; // access token URL
$PUrl = 'https://www.googleapis.com/oauth2/v2/userinfo'; // 프로필 URL



$code = $_REQUEST['code'];
$token = $_REQUEST['token'];
if($_mode) {
	SetCookie("SNSOauthMode", $_mode, 0 , "/" , '.'.str_replace("www." , "" , $_SERVER['HTTP_HOST']));
	$SNSOauthMode = $_COOKIE['SNSOauthMode'] = $_mode;
}


// 로그인 요청
if(!$code) die(header("location: {$CUrl}?client_id={$key}&redirect_uri=".urlencode($redirect)."&response_type=code&scope=email profile"));

// 토큰요청
$Param = array(
	'grant_type'=>'authorization_code',
	'client_id'=>$key,
	'client_secret'=>$secret,
	'redirect_uri'=>$redirect,
	'code'=>$code
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $TUrl);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Param));
//curl_setopt($ch, CURLOPT_POSTFIELDSIZE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
));

$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$response = (array)json_decode($response);
$access_token = $response['access_token'];
if(!$access_token) error_loc('callback.php');







// 사용자 정보 조회
$Param = array(
	'access_token'=>$access_token
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $PUrl.'?'.http_build_query($Param));
curl_setopt($ch, CURLOPT_POST, false);
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Param));
//curl_setopt($ch, CURLOPT_POSTFIELDSIZE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
));
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$response = json_decode($response, true);
$SNSLoginData = $response;


$print_name = $SNSLoginData['name']!=''?$SNSLoginData['name']:$SNSLoginData['given_name'];

$sns_id = $SNSLoginData['id'];
$sns_info = array(
	'type'=>$sns_login_addon_type,
	'id'=>$sns_id,
	'name'=>$print_name,
	'email'=>$SNSLoginData['email']
);

// 이메일이 넘어오지 않은경우 권한 재요청
if(!$sns_info['email']) {
	error_loc_msg("https://accounts.google.com/o/oauth2/v2/auth?client_id={$key}&redirect_uri={$redirect}&response_type=code&auth_type=rerequest&scope=email", '사이트 이용을 위해서 이메일 권한이 필요합니다.\\n이메일 권한을 승인 부탁 드립니다.');
}

// 모드가 있다면 모드 전달
if($_COOKIE['SNSOauthMode']) {
	$SNSOauthMode = $_COOKIE['SNSOauthMode'];
	SetCookie('SNSOauthMode', '', time() -3600, '/', '.'.str_replace('www.', '', $system['host']));
}



// 로그인 처리
include_once(dirname(__FILE__).'/login.pro.php');