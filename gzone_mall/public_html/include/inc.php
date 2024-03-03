<?php
error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT ) );
ini_set('display_errors', 0);

// ![LCY] 2020-08-25 :: 크롬 쿠키공유(IFRAME 간 세션 공유) 문제 서포트
if(!function_exists('samesiteCookie')) {
	function samesiteCookie($name = '', $value =''  , $expires = '' , $path = '', $domain = '', $secure = false, $httponly = false)
	{
		if($name != ''){
			// 기본 쿠키 방식
			setcookie($name, $value , $expires , $path, $domain, $secure , $httponly);

			// 추가 쿠키 방식 PHP VER >= 7.2
			/*
				$options = array(
					'expires' => $expires,
					'path' => $path,
					'domain' => $domain,
					'secure' => $secure,     // or false
					'httponly' => $httponly,    // or false
					'samesite' => 'None' // None || Lax  || Strict
				);
				setcookie($name, $value, $options);
			*/
		}

		$res = @session_start();
		// IE 브라우저 또는 엣지브라우저 일때는 secure; SameSite=None 을 설정하지 않습니다.
		if( preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~',$_SERVER['HTTP_USER_AGENT']) ){
			return $res;
		}

		$httpsChk = false;
		if(isset($_SERVER['HTTPS'])) {
			if(strtolower($_SERVER['HTTPS']) == 'on') $httpsChk = true;
			else if($_SERVER['HTTPS'] == '1') $httpsChk = true;
		}

		if($httpsChk !== true){ return $res; }
		$headers = headers_list();
		krsort($headers);

		foreach ($headers as $header) {
			//if (!preg_match('~^Set-Cookie: PHPSESSID=~', $header)) continue;
			if (preg_match('~^Set-Cookie: PHPSESSID=~', $header)) {  $_SESSION['SESS_SAME_SITE_COOKIE'] = true;  }
			if (preg_match('/(SameSite=None)/', $header)) continue;
			if (!preg_match('~^Set-Cookie:~', $header)) continue;
			if (preg_match('/=deleted;/i', $header)) continue;
			$header = preg_replace('~; secure(; HttpOnly)?$~', '', $header) . '; secure; SameSite=None';
			header($header, false);
			//break;
		}

		// 세션쿠키에 same site 적용이 안됬을 경우 다시한번 생성해 준다.
		if( $_SESSION['SESS_SAME_SITE_COOKIE'] !== true){
			$res = session_regenerate_id();
			samesiteCookie();
		}

		return $res;
	}
}
samesiteCookie(); // PHPSESSID 을 위한 처리
header("Content-Type: text/html; charset=UTF-8");
$_SESSION['filedownAuth'] = true;
// $_path_str 지정
if(!$_path_str) {
	if(@file_exists("../include/config_database.php")) $_path_str = "..";
	else $_path_str = ".";
}
$_path_str = dirname(__FILE__);
//$_path_str = $_SERVER[DOCUMENT_ROOT];

// 2020-03-09 SSJ :: 장비교체에 따른 소스수정
if( !(IS_ARRAY($HTTP_POST_VARS) && sizeof($HTTP_POST_VARS)) ){$HTTP_POST_VARS = array();}
if( !(IS_ARRAY($HTTP_GET_VARS) && sizeof($HTTP_GET_VARS)) ){$HTTP_GET_VARS = array();}
if( !(IS_ARRAY($HTTP_ENV_VARS) && sizeof($HTTP_ENV_VARS)) ){$HTTP_ENV_VARS = array();}



// --- JJC : 2022-07-11 : 웹 접근 제어 ---
// 배열의 최종값에 addslashes 2015-08-13
// SQL Inject 문구 -> 제거
$arr_inj_chk = array("\/*" , "*\/" , "&&" , "||" , "sleep" , "benchmark", "union" , "from" , "where" , "declare" , "column_name" , "table_name" , "openrowset" , "substr" , "substring" , "xp_" , "sysobjects" , "syscolumns"); // "if" , "or" ,  "insert"  , "and" , "update" , "join" , "select" 
// XSS 문구 -> 변경
$arr_xss_chk = array("<" => "&lt;" , ">" => "&gt;" , "(" => "&#40;" , ")" => "&#41;" , "\"" => "&quot;"  ); // "&" => "&#38;"  , "#" => "&#35;" , "/" => "&#x2F;"

if(!function_exists('escape_string')) {
	function escape_string($value) {
		if(is_array($value)) return array_map('escape_string', $value);
		else {
			global $arr_inj_chk , $arr_xss_chk , $_COOKIE , $_SESSION;
			if( !($_COOKIE["AuthAdmin"] || $_SESSION['AuthAdminDiff'] || $_COOKIE["AuthCompany"] || $_SESSION['AuthSubAdminDiff'])  ) { // 사용자에서만 SQL Inject , XSS 적용
				$value = str_ireplace($arr_inj_chk , "" , $value);
				$value = str_ireplace(array_keys($arr_xss_chk) , array_values($arr_xss_chk) , $value);
			}
			return (isset($value) ? addslashes(stripslashes($value)):null);
		}
	}
}
// post / get 방식처리
$_GET = array_map('escape_string', $_GET);
$_POST = array_map('escape_string', $_POST);
$_COOKIE = array_map('escape_string', $_COOKIE);
foreach(array_merge($_GET , $_POST) as $k_tmp=>$v_tmp) { $$k_tmp = $v_tmp; }
// 아래와 같은 방법도 있음
//@extract($_GET);
//@extract($_POST);


include_once("${_path_str}/config_database.php");
include_once("${_path_str}/config_connect.php");
include_once("${_path_str}/lib.func.php");
include_once("${_path_str}/lib.qry.php");
include_once("${_path_str}/var.php");
include_once("${_path_str}/img_support.php"); // 이미지 서포트 함수 파일 호출
// --- JJC : 2022-07-11 : 웹 접근 제어 ---



// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 {
if( is_login() === true) { 
	$chk_AuthShopCOOKIEID = site_secure_decode($_COOKIE['AuthShopCOOKIEID']);
	$chk_AuthIndividualMember = site_secure_decode($_COOKIE['AuthIndividualMember']);
	if( !empty($chk_AuthShopCOOKIEID)){ $_COOKIE['AuthShopCOOKIEID'] = $chk_AuthShopCOOKIEID; }
	if( !empty($chk_AuthIndividualMember)){ $_COOKIE['AuthIndividualMember'] = $chk_AuthIndividualMember; }
}
// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 }


## ***  오류가 날 경우 아래 주석을 풀어 조절하시면 됩니다.
## *** error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) );
//error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED ) );

// LCY : 2022-03-11 : 임대몰용 -- 에러조정
error_reporting( E_ALL & ~( E_NOTICE | E_USER_NOTICE | E_WARNING | E_COMPILE_WARNING | E_CORE_WARNING | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT ) );


// LCY : 2022-08-02 : 스킨 강제모드 
$ONLY_SKIN = $_COOKIE['ONLY_SKIN'];

// 사이트 정보 호출
$siteInfo = get_site_info();
$siteInfo['instagram_main_use'] = 'N';

// 운영자정보 호출 :: 있을경우에만 호출 lib.qry.php
$siteAdmin = get_site_admin();

// 회원정보 호출
if(is_login()) $mem_info = _individual_info($_COOKIE["AuthIndividualMember"]);

// {{{회원등급혜택}}} -- 그룹할인 정보 호출
if( is_login() ) $groupSetInfo = getGroupSetInfo();
// {{{회원등급혜택}}}

// 카트에 담긴 상품 갯수
$cart_cnt = get_cart_cnt();

// 게시판 정보
$_ARR_BBS = get_board_list_array();

// 현재 파일명 확인
$EX_FILENAME = explode("/" , $_SERVER['SCRIPT_FILENAME']);
$CURR_FILENAME = $EX_FILENAME[(sizeof($EX_FILENAME)-1)]; // 현재파일명
$END_FILENAME = end(explode('/', dirname($_SERVER['SCRIPT_NAME']))); // 현재 최하위 DIR명

# 서버에 지정된 업로드 최대용량 확인
$MaxUploadSize = (ini_get('upload_max_filesize')?ini_get('upload_max_filesize'):'서버설정용량');

# 스킨 체크
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.path.php'); // 경로 설정 파일 호출
if(file_exists(OD_SITE_SKIN_ROOT.'/var.php')) include_once(OD_SITE_SKIN_ROOT.'/var.php'); // PC 사이트 스킨 설정 호출
if(file_exists(OD_SITE_MSKIN_ROOT.'/var.php')) include_once(OD_SITE_MSKIN_ROOT.'/var.php'); // Mobile 사이트 스킨 설정 호출
$SiteInfoEnc = serialize(array('license'=>$siteInfo['s_license'], 'site_name'=>$siteInfo['s_adshop'], 'company_name'=>$siteInfo['s_company_name'], 'company_num'=>$siteInfo['s_company_num'], 'cso_name'=>$siteInfo['s_ceo_name'], 'tel'=>$siteInfo['s_glbtel'], 'htel'=>$siteInfo['s_glbmanagerhp'], 'system'=>$system));
for($i=0; $i<3; $i++) { $SiteInfoEnc = enc('e', $SiteInfoEnc); }


// 이메일 제공자(회원, 주문등에 사용)
$email_suffix = explode(',', $siteInfo['join_email_list']);
$email_suffix = array_merge($email_suffix, array('direct'));
$email_suffix = array_values(array_filter($email_suffix));



# 후킹엔진 호출 2016-10-25 LDD
include_once(OD_ADDONS_ROOT.'/hook/hook.add.php');


# 관리자에서 강제로 아이디 삭제 했을때 사용자 로그인 풀기
if(is_login() === true && (!$mem_info['in_id'] || $mem_info['in_out'] == 'Y') && !preg_match('/totalAdmin/i', $_SERVER['REQUEST_URI']) && !is_master()) {
	// 로그인 쿠키 적용 - 로그아웃
	samesiteCookie("AuthIndividualMember", "" , time() - 3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	header('location: /');
}
if(is_login() === true && !preg_match('/totalAdmin/i', $_SERVER['REQUEST_URI']) && !preg_match('/subAdmin/i', $_SERVER['REQUEST_URI'])) UserLoginCheck(); // 로그인 세션 체크

// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====
$none_member_buy = ($siteInfo['s_none_member_buy'] == "N" && !is_login());
// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====
