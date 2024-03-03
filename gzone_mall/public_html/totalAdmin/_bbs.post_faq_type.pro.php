<?php 
	include_once("inc.php");

	// -- 데이터를 배열화
	$arrFaqData = array('type'=>$faqType);
	$faqData = addslashes(serialize($arrFaqData));

	// 설정값 업데이트
	$que = "
		update smart_setup set
			  s_bbs_faq_type = '".$faqData."' 
			, s_faq_keyword = '".$s_faq_keyword."'
		where s_uid = '1'
	";

	_MQ_noreturn($que);
	error_loc_msg("_bbs.post_faq_type.form.php","저장되었습니다.");


?>