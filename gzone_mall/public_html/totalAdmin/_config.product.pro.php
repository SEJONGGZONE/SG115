<?PHP
	include "./inc.php";

	// 자동정산 설정
	if(!$_product_auto_on) $_product_auto_on = 'N';
	$s_que = "";
	$s_que .= ", s_product_auto_C = '".$_product_auto_C."' ";
	$s_que .= ", s_product_auto_L = '".$_product_auto_L."' ";
	$s_que .= ", s_product_auto_B = '".$_product_auto_B."' ";
	$s_que .= ", s_product_auto_G = '".$_product_auto_G."' ";
	$s_que .= ", s_product_auto_V = '".$_product_auto_V."' ";
	$s_que .= ", s_product_auto_H = '".$_product_auto_H."' ";
	$s_que .= ", s_product_auto_P = '".$_product_auto_P."' ";
	$s_que .= ", s_product_auto_PP = '".$_product_auto_PP."' ";
	$s_que .= ", s_product_auto_on = '".$_product_auto_on."' ";


	// LCY : 2023-01-04 : 티켓상품 정산처리 추가
	if(!$_ticket_auto_on) $_ticket_auto_on = 'N';
	$s_que .= ", s_ticket_auto_C = '".$_ticket_auto_C."' ";
	$s_que .= ", s_ticket_auto_L = '".$_ticket_auto_L."' ";
	$s_que .= ", s_ticket_auto_B = '".$_ticket_auto_B."' ";
	$s_que .= ", s_ticket_auto_G = '".$_ticket_auto_G."' ";
	$s_que .= ", s_ticket_auto_V = '".$_ticket_auto_V."' ";
	$s_que .= ", s_ticket_auto_H = '".$_ticket_auto_H."' ";
	$s_que .= ", s_ticket_auto_P = '".$_ticket_auto_P."' ";
	$s_que .= ", s_ticket_auto_PP = '".$_ticket_auto_PP."' ";
	$s_que .= ", s_ticket_auto_on = '".$_ticket_auto_on."' ";

	$que = "
		update smart_setup set
			s_account_commission		= '".$_account_commission."'
			, s_delivery_auto				= '".$_delivery_auto."'
			, s_today_view_time			= '".$_today_view_time."'
			, s_today_view_max			= '".$_today_view_max."'
			, s_naver_switch				= '".$_naver_switch."'
			, s_daum_switch				= '".$_daum_switch."'
			{$s_que}
			where s_uid						= 1
	";

	_MQ_noreturn($que);

	error_loc("_config.product.form.php");
	exit;
?>