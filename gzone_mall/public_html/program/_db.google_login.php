<?php
/*
* KAY : 2023-11-06 :: 구글 로그인 추가
* -- http://{도메인}/program/_db.google_login.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// smart_setup 필드추가 추가
if(!IsField('smart_setup', 's_google_login_use')){
	_MQ_noreturn("
		ALTER TABLE `smart_setup`
			ADD COLUMN `s_google_login_use` ENUM('Y','N') NOT NULL DEFAULT 'N' COMMENT '구글 로그인 사용여부',
			ADD COLUMN `s_google_client_id` VARCHAR(255) NOT NULL COMMENT '구글 API 클라이언트 ID',
			ADD COLUMN `s_google_client_pw` VARCHAR(255) NOT NULL COMMENT '구글 API 클라이언트 비밀번호' ;
	");

	echo '<hr>smart_setup에 항목이 추가되었습니다.</hr>';
}else{
	echo '<hr>smart_setup에 이미 추가된 항목입니다.</hr>';
}


// smart_order 필드 추가
if(!IsField('smart_individual', 'go_join')){

	_MQ_noreturn("
		ALTER TABLE `smart_individual`
			ADD COLUMN `go_join` ENUM('Y','N') NOT NULL DEFAULT 'N' COMMENT '구글 가입',
			ADD COLUMN `go_encid` VARCHAR(255) NOT NULL COMMENT '구글 고유아이디';
	");

	echo '<hr>smart_individual에 항목이 추가되었습니다.</hr>';
}else{
	echo '<hr>smart_individual에 이미 추가된 항목입니다.</hr>';
}