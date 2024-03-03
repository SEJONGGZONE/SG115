<?php
# LDD: 2017-10-12 카카오톡 로그인 - 로그인 처리
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// ![LCY] 2020-06-03 -- SNS고유아이디 없을 시 튕겨낸다. --
if(trim($sns_id) == ''){ error_msgPopup('인증에 실패하였습니다.'); }

# 회원 검색
$FindID = _MQ(" select * from `smart_individual` where `{$SNSField[$sns_login_addon_type]['join']}` = 'Y' and `{$SNSField[$sns_login_addon_type]['id']}` != '' and `{$SNSField[$sns_login_addon_type]['id']}` = '{$sns_id}' ");


$_mode = 'login'; // 동작모드 (login, update, join)
if(is_login() === true && $FindID['in_id'] && $FindID['in_id'] != $mem_info['in_id']) error_msgPopup('이미 다른 계정에 연결된 아이디 입니다.');
else if(is_login() === true && $FindID['in_id'] && $FindID['in_id'] != $mem_info['in_id']) error_msgPopup('이미 연동되어 있거나 잘못 된 소셜 아이디 입니다.');
else if(is_login() === true && !$FindID['in_id']) $_mode = 'update'; // 로그인 + 고유키 아이디가 없다면 update 모드
else if(is_login() === false && !$FindID['in_id']) $_mode = 'join'; // 비로그인 + 고유키 아이디가 없다면 join 모드
if($SNSOauthMode) $_mode = $SNSOauthMode; // 개별모드가 있다면 적용

actionHook('sns_login.start'); // 해당 파일 시작에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php


# 프로세스 처리
if($_mode == 'update') { // 소셜정보 update
	actionHook('sns_update.'.$SNSField[$sns_login_addon_type]['short'].'.start'); // 해당 파일 시작에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php

	_MQ_noreturn(" update `smart_individual` set `sns_join` = 'Y', `{$SNSField[$sns_login_addon_type]['join']}` = 'Y', `{$SNSField[$sns_login_addon_type]['id']}` = '{$sns_id}' where `in_id` = '{$row_member['id']}' ");

	actionHook('sns_update.'.$SNSField[$sns_login_addon_type]['short'].'.end'); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php
	error_msgPopup_s('연동처리가 완료되었습니다.');
}
else if($_mode == 'login') { // 로그인
	$login_id = $FindID['in_id'];
	actionHook('sns_login.'.$SNSField[$sns_login_addon_type]['short'].'.start'); // 해당 파일 시작에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php

	// 휴면계정체크
	if( $FindID['in_sleep_type'] == 'Y'){
		// LCY 2018-03-07 -- 휴면회원 이메일 인증없이 처리가능한경우
		if( $FindID['in_sleep_request'] == 'Y'){
			member_sleep_return( $FindID['in_id'] );
			// -- 백업이후 정보를 다시 불러온다.
			$FindID = _MQ(" select * from `smart_individual` where `{$SNSField[$sns_login_addon_type]['join']}` = 'Y' and `{$SNSField[$sns_login_addon_type]['id']}` = '{$sns_id}' ");
			_MQ_noreturn("update smart_individual set in_sleep_request = 'N' where in_id = '". $FindID['in_id']."'  ");

		}else{
			// KAY :: 2023-01-10 :: 한국진흥원 보안패치 적용 -- 파일명 변경
			error_loc_msgPopup('/?pn=member.slp_form&_id=' . $login_id , '회원님은 현재 장기 미사용 계정으로 휴면전환된 상태입니다.\\n\\n휴면 상태를 풀기 위해서는 이메일 인증절차를 거쳐야 합니다.\\n\\n인증을 진행하기 위한 페이지로 이동합니다.');
		}
	}

	// -- 승인 미승인 추가
	if( $FindID['in_auth'] != 'Y'){
		error_msgPopup_s("가입에 대한 승인처리가 되지 않았습니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");
	}

	// 회원정보 업데이트
	_MQ_noreturn(" update `smart_individual` set `in_ldate` = now() where `in_id` = '{$login_id}' ");
	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 {
	$enc_login_id = site_secure_encode($login_id);
	samesiteCookie("AuthIndividualMember", $enc_login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 }

	access_deny_cnt('del'); //LCY 로그인 틀린 횟수 기록 - 로그인성공 시 초기화
	$login_trigger = loginchk_insert($login_id , 'individual');
	_MQ_noreturn("update smart_cart set c_cookie='". $login_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");

	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 {
	samesiteCookie("AuthShopCOOKIEID", $enc_login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 }

	// LCY : 2023-11-14 : 장바구니 업데이트 체크 함수 (로그인 후 실행필요)
	update_cart_check($login_id);

	UserLogin($login_id); // 세션로그인
	actionHook('sns_login.'.$SNSField[$sns_login_addon_type]['short'].'.end'); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php
	actionHook('sns_login.end', $login_id); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : /addones/hook/autoload/sns_join.pro.php

	// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음
	$_rurl = $_COOKIE['_rurl'];
	if(!$_rurl) $_rurl = "/";
	if($_rurl == 'index.php') $_rurl = "/";

	// 2019-08-28 SSJ :: 인코드된 데이터인지 체크 추가
	$_rurl2 = enc('d', $_rurl);
	if(strpos($_rurl2, 'pn=') !== false){
		$_rurl = '/?'.$_rurl2;
	}

	// JJC : 2022-07-11 : 웹 접근 제어
	if( 
		(preg_match('/http/i', $_rurl ) && !preg_match('/'. reset(explode(':', $system['host'])) .'/i', $_rurl )) || //  - http 주소가 있고 외부 host일 경우 초기화
		preg_match('/\.php/i', $_rurl ) //  - 실행파일이 php일 경우 초기화
	){$_rurl = '/';}


	die("<script language='javascript'>if(opener&&!opener.closed){opener.location.href=('".$_rurl."');window.close();}else{document.location.href=('".$_rurl."');}</script>");
}
else if($_mode == 'join') { // 가입
	actionHook('sns_join.start', $sns_info); // 해당 파일 종료에 대한 후킹액션 실행 -> 실가입처리 후킹 --> 선언위치 : ../sns_login.hook.php
	actionHook('sns_join.'.$SNSField[$sns_login_addon_type]['short'].'.start', $sns_info); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php

	actionHook('sns_join.'.$SNSField[$sns_login_addon_type]['short'].'.end', $sns_info); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php
	actionHook('sns_join.end', $sns_id); // 해당 파일 종료에 대한 후킹액션 실행  --> 선언위치 : /addones/hook/autoload/sns_join.pro.php
}
else if($_mode == 'leave') { // 탈퇴

	actionHook('sns_leave.'.$SNSField[$sns_login_addon_type]['short'].'.end', $FindID); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php
	actionHook('sns_leave.end', $FindID); // 해당 파일 종료에 대한 후킹액션 실행 --> 선언위치 : ../sns_login.hook.php -> 실탈퇴처리 후킹
}