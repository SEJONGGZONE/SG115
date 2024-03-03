<?php 
include_once(dirname(__FILE__).'/inc.php');

$cond_month_date = date('Y-m-d',strtotime("-".($siteInfo['member_sleep_period']-1)." month"));

$mrs = _MQ_assoc("select in_id, in_email, in_ldate , in_name from smart_individual where  in_userlevel != '9' AND in_name not in ('휴면전환' , '탈퇴한회원') and in_sleep_type != 'Y'  and 
	date(in_ldate) = '".$cond_month_date."' and in_ldate != '0000-00-00 00:00:00' and in_email != '' ");


foreach($mrs as $k=>$v){
	$unique = md5($v['in_id'].$cond_month_date);

	// LCY : 2022-04-05 -- 추가 중복 방지 추가 스택 유니크 테이블을 활용 {
	_MQ_noreturn("insert into smart_stack_never(sn_unique_type,sn_unique_value,sn_update_dt,sn_reg_dt) values('sleep_ready','".$unique."',now(),now()) on duplicate key update sn_cnt = sn_cnt +1, sn_update_dt = now() ");
	$stack_chk = _MQ_affected_rows();
	if( $stack_chk != 1){ echo '중복'; continue; }
	$stack_chk = 0;
	// LCY : 2022-04-05 -- 추가 중복 방지 추가 스택 유니크 테이블을 활용 }

	$join_email = $v['in_email'];
	$_name = $v['in_name'];
	$_id = $v['in_id'];
	$_content = '';

	// - 메일발송 ---
	if( mailCheck($join_email) ){
	    include(OD_MAIL_ROOT."/member.sleep_ready.mail.php"); // 메일 내용 불러오기 ($mailing_content)
	    $_title = "[".$siteInfo[s_adshop]."] 휴면전환 예정안내!";

	    $_content = get_mail_content($mailling_content);
	    mailer( $join_email , $_title , $_content );
	}
	// - 메일발송 ---	
}
