<?php
include_once('inc.php');

// -- 통합관리자 로그인 페이지 보안서버 추가 2019-07-03 LCY --
AutoHTTPSMove('admin');
// -- 통합관리자 로그인 페이지 보안서버 추가 2019-07-03 LCY --



# 로그인 후 다시 메인 접근시 로그인 skip 처리
if(($_COOKIE['AuthAdmin'] || $_COOKIE['AuthCompany']) && $_mode != 'autologin') {
	if( $AdminPath == 'totalAdmin' && $_COOKIE['AuthAdmin']){ $userType = 'master'; }
	else if($AdminPath == 'subAdmin' && $_COOKIE['AuthCompany']){ $userType = 'com'; }

	// 입점업체 사용안할 시 강제로 고정
	if( $SubAdminMode !== true){ $userType = 'master'; }
}




# 로그인 처리
if($userType == 'com') { // 입점로그인

	if( $SubAdminMode !== true){ error_msg("잘못된 접근입니다."); }
		


	// 입점업체 로그인 후 이동 액션처리 추가
	if( empty($_rurl) ){ $_rurl = '/_main.php'; }
	else{  $_rurl = enc('d',$_rurl);}

	$_rurl = OD_SUB_ADMIN_URL.$_rurl;


	if($_mode=='autologin' && $_COOKIE['AuthAdmin']) $row = _MQ(" select * from smart_company where cp_id = '{$_id}' ");
	else if($_id && $_pw ) $row = _MQ(" select * from smart_company where cp_id = '{$_id}' and cp_pw = '".db_password($_pw)."' ; ");

	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
	if($siteInfo['member_login_cnt'] > 0 && $_id && $_pw && empty($_COOKIE['AuthCompany']) ){
		$loginChk = get_apply_auth_count('subAdminLogin',$_id);
		if( count($loginChk) > 0 && $loginChk['aac_count'] >= $siteInfo['member_login_cnt']  ){
			if( $loginChk['aac_udate'] > date('Y-m-d H:i:s',time()-$siteInfo['member_login_time']) ){
				error_msg("현재 로그인이 불가능합니다.\\n\\n잠시후에 시도해 주세요.");
			}
			else{ // 이미 설정된 카운트가 되었고 시간초가 지났기 때문에 초기화
				init_apply_auth_count('subAdminLogin',$_id);
			}
		}
	}
	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }

	if(count($row) <= 0 && $_COOKIE['AuthCompany']) {
		error_loc($_rurl); // 이미 입점업체 로그인 중 이라면 바로 이동
	}
	else if(count($row) <= 0) {

		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
		insert_apply_auth_count('subAdminLogin',$_id);
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }

		error_msg('입력하신 아이디나 비밀번호가 일치하지 않습니다.\\n\\n다시 입력해 주세요.');
	}
	else {
		samesiteCookie('AuthCompany', $_id, 0, '/');
		SubAdminLogin($_id); // 입점관리자 세션 로그인 처리

		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
		init_apply_auth_count('subAdminLogin',$_id);
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }

		error_loc($_rurl);
	}
}
else if($userType == "master") { // 관리자로그인

	// 통합관리자 로그인 후 이동 액션처리 추가
	if( empty($_rurl) ){ $_rurl = '/_main.php'; }
	else{  $_rurl = enc('d',$_rurl);}

	$_rurl = OD_ADMIN_URL.$_rurl;



	if($_id && $_pw ) $row = _MQ(" select * from smart_admin where a_id = '{$_id}' and a_pw = '".db_password($_pw)."' "); // -- 운영자 검색

	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
	if($siteInfo['member_login_cnt'] > 0 && $_id && $_pw && empty($_COOKIE['AuthAdmin']) ){
		$loginChk = get_apply_auth_count('totalAdminLogin',$_id);
		if( count($loginChk) > 0 && $loginChk['aac_count'] >= $siteInfo['member_login_cnt']  ){
			if( $loginChk['aac_udate'] > date('Y-m-d H:i:s',time()-$siteInfo['member_login_time']) ){
				error_msg("현재 로그인이 불가능합니다.\\n\\n잠시후에 시도해 주세요.");
			}
			else{ // 이미 설정된 카운트가 되었고 시간초가 지났기 때문에 초기화
				init_apply_auth_count('totalAdminLogin',$_id);
			}
		}
	}
	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }


	if(!$row['a_id'] && $_COOKIE['AuthAdmin']) { // 통합관리자에 이미 로그인 중 이라면 바로 이동
		error_loc($_rurl);
	}
	else if($row['a_id'] && $_id == $row['a_id'])  {
		if($row['a_use'] == 'N') error_msg('승인되지 않은 운영자 계정입니다.'); // -- 미승인일경우
		samesiteCookie('AuthAdmin', $row['a_uid'], 0, '/');
		AdminLogin($row['a_uid']); // 통합관리자 세션 로그인 처리

		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
		init_apply_auth_count('totalAdminLogin',$_id);
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }
	}
	else {
	  // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
	  if($_id != '' || $_pw != '') {
			insert_apply_auth_count('totalAdminLogin',$_id); // 카운트 증가
			error_msg('입력하신 정보가 맞지않습니다.\\n\\n Caps Lock, 한/영 키의 상태를 확인하시고 다시 입력하여 주십시오.');
	  }
	  // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
	}
	if(($_id == $row['a_id'] || db_password($_pw) == $row['a_pw']) || trim($siteAdmin['a_id']) != '') error_loc($_rurl);


}

# 파비콘
$Favicon = $siteInfo['s_favicon'];

?>
<!DOCTYPE HTML>
<html lang="ko">
<head>
	<title><?=$siteInfo['s_adshop']?> :: <?php echo $AdminPath == 'subAdmin' ? '입점':'통합' ?> 관리자</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi" />

	<!-- 모바일브라우저 상단 색상 -->
	<meta content='#21212D' name='apple-mobile-web-app-status-bar-style'/><!-- iOS Safari -->
	<meta content='#21212D' name='msapplication-navbutton-color'/><!-- Windows Phone -->
	<meta content='#21212D' name='theme-color'/><!-- Chrome, Firefox, Opera -->

	<?php // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 외부 정보 노출 { ?>
	<meta name="robots" content="noindex">
	<?php // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 외부 정보 노출 } ?>

	<?php if($Favicon) { ?>
		<link rel="apple-touch-icon-precomposed" href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" />
		<link rel="shortcut icon" href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" type="image/x-icon"/>
	<?php echo PHP_EOL; } ?>
	<meta name="format-detection" content="telephone=no" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/setting.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/totalAdmin.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/login.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/responsive.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

	<script src="/include/js/jquery-1.7.1.min.js"></script>
</head>
<body>


<div class="t_login <?php echo $AdminPath == 'subAdmin' ? 'if_sub':'if_total' ?>">
	<div class="grid_layout">

		<div class="area_box area_left">
			<div class="login_guide">
				<div class="picto_box">
					<div class="picto picto1"></div>
					<div class="picto picto2"></div>
				</div>
				<div class="guide_box">
					<div class="site_name"><?=$siteInfo['s_adshop']?></div>
					<div class="conts">
						<div class="only_pc">인증된 관리자만 로그인이 가능하며,<br/></div>
						접속 후 시스템 정보에 대한 보안규정을 준수해주세요.
						<div class="only_pc">※ Internet Explorer 접속 시 반응형이 미적용됩니다.</div>
					</div>
					<?php if($siteInfo['s_login_page_phone']) { ?>
						<div class="tel">SERVICE : <?=$siteInfo['s_login_page_phone']?></div>
					<?php } ?>
					<?php if($siteInfo['s_login_page_email']) { ?>
						<div class="email">E-MAIL : <?php echo $siteInfo['s_login_page_email']; ?></div>
					<?php } ?>

				</div>
			</div>
		</div><!-- end  area_left -->

		<div class="area_box area_right">
			<form name="form_login" id="form_login" action="<?=$PHP_SELF?>" method="post" autocomplete="off" class="login_form">
				<?php  // LCY : 2023-01-10 : _rurl 체크 ?>
				<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">

				<div class="tit"><?php echo $AdminPath == 'subAdmin' ? 'Sub':'Total' ?> Admin</div>
				<div class="stit"><?=$siteInfo['s_adshop']?> <?php echo $AdminPath == 'subAdmin' ? '입점':'통합' ?> 관리자</div>
				<input type="hidden" name="userType" value="<?php echo $AdminPath == 'subAdmin' ? 'com':'master' ?>">

				<ul class="form">
					<li>
						<span class="upper_tx">아이디</span>
						<input type="text" name="_id" class="design js" placeholder="ID" value="" />
					</li>
					<li>
						<span class="upper_tx">비밀번호</span>
						<input type="password" name="_pw" class="design js" value="" autocomplete="new-password" placeholder="PASSWORD"/>
					</li>
				</ul>
				<input type="submit" name="" class="btn_login" value="LOGIN" onclick="form_login.submit();" />
				<div class="copyright">ⓒ <?=$siteInfo['s_adshop']?> ALL RIGHTS RESERVED.</div>
			</form><!-- end login_form -->
		</div><!-- end area_right -->

	</div><!-- end grid -->
</div><!-- end t_login -->


</body>
</html>

