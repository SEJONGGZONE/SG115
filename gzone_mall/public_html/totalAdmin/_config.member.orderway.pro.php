<?php
include_once('inc.php');


// -- 2020-03-25 SSJ :: 비회원 바로구매 시 로그인 페이지 경유 설정 추가 ----
if(IsField('smart_setup', 's_none_member_login_skip') === false){
	$add_column = array('Field'=>'s_none_member_login_skip','Type'=>'enum(\'Y\',\'N\') CHARACTER SET utf8 COLLATE utf8_general_ci','Default'=>'N','Null'=>'NO','Extra'=>'COMMENT  \'비회원 바로구매 시 로그인 페이지 경유 여부(Y-바로구매, N-로그인페이지 경유)\'');
	AddFeidlUpdate('smart_setup',$add_column);
}

// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
create_apply_auth_count_db(); // DB 생성체크 필요
if( $member_login_cnt > 0 && rm_str($member_login_time) < 1 ){ $member_login_time = 60; }
// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }

// 설정값 업데이트
$que = "
	update smart_setup set 
		s_none_member_buy = '".$_none_member_buy."'
		, s_leave_guidance = '".$_leave_guidance."'
		, s_none_member_login_skip = '".$_none_member_login_skip."'
		, member_login_time = '".$member_login_time."'
		, member_login_cnt = '".$member_login_cnt."'
	where 
		s_uid = 1
";


_MQ_noreturn($que);

// 설정페이지 이동
error_loc('_config.member.orderway.form.php');