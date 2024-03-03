<?php
	include_once('inc.php');

	// 메인 리뷰 최대 노출개수가 5개 이하인경우 경고창
	if($s_main_review_limit<1){ error_msg("최대 노출개수는 1개이상 입력해주세요."); }

	_MQ_noreturn(" 
		update smart_setup set 
			s_main_review = '".(isset($s_main_review)?$s_main_review:'Y')."', 
			s_main_review_porder = '".(isset($s_main_review_porder)?$s_main_review_porder:'R')."',
			s_main_review_score = '".(isset($s_main_review_score)?$s_main_review_score:1)."', 
			s_main_review_view = '".(isset($s_main_review_view)?$s_main_review_view:'A')."',
			s_main_review_limit = '".$s_main_review_limit."'
		where s_uid = '1' 
	");

	error_frame_loc('_config.display.review.php');