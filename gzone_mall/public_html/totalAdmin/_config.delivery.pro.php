<?PHP
	include "./inc.php";

	// 사전체크
	$_delprice		= delComma($_delprice);
	$_delprice_free	= delComma($_delprice_free);

	$s_que .= " s_del_addprice_use = '".$_del_addprice_use."' ";
	$s_que .= ", s_del_addprice_use_normal = '".$_del_addprice_use_normal."' ";
	$s_que .= ", s_del_addprice_use_unit = '".$_del_addprice_use_unit."' ";
	$s_que .= ", s_del_addprice_use_free = '".$_del_addprice_use_free."' ";
	$s_que .= ", s_del_addprice_use_product = '" . $_del_addprice_use_product . "' ";

	$que = "update smart_setup set
				s_delprice						= '".$_delprice."',
				s_delprice_free					= '".$_delprice_free."',
				s_del_company					= '".$_del_company."',
				s_del_date						= '".$_del_date."',
				s_del_return_addr				= '".$_del_return_addr."',
				{$s_que}
				where s_uid						= 1";

	_MQ_noreturn($que);

	error_loc("_config.delivery.form.php");
	exit;
?>