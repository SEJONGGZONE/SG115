<?php 
	include_once('inc.php');

	/*
		# DB :: smart_setup
		- s_main_best_title
		- s_main_best_limit
		- s_main_best_order
		- s_main_best_view
		- s_main_best_mobile_display
	*/

	if($_mode=='pcMainSelect'){
		$tempSkinInfoMo = SkinInfoMo('category');

		// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
		// 리스트형일땐 리스트형만
		if(preg_match("/박스/",$_main_view_pc)==true){
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'box';
		}else{
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'list';
		}
			
		die(json_encode($arrProductDisplay['arrListMoView']));
	}else{

		if($s_main_best_view=='Y'){
			$s_main_best_display = nullchk($s_main_best_display , '상품 진열 설정을 선택해주시기 바랍니다.');
			$s_main_best_mobile_display = nullchk($s_main_best_mobile_display , '상품 진열 설정을 선택해주시기 바랍니다.');
		}

		$s_query = "
			s_main_best_title = '".$s_main_best_title."'
			,s_main_best_limit = '".$s_main_best_limit."'
			,s_main_best_order = '".$s_main_best_order."'
			,s_main_best_view = '".$s_main_best_view."'
			,s_main_best_display = '".rm_str($s_main_best_display)."'
			,s_main_best_mobile_display = '".rm_str($s_main_best_mobile_display)."'
			,s_main_best_display_type = '".$main_view_type."'
		";


		_MQ_noreturn(" update smart_setup set  ".$s_query." where s_uid = '1' ");
		error_loc("_config.display.category.php");

	}
?>