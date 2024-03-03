<?php 
	include_once('inc.php');

	/*
		# DB :: smart_setup
		- s_main_timesale_title
		- s_main_timesale_limit
		- s_main_timesale_order
		- s_main_timesale_view
		- s_main_timesale_mobile_display
	*/

	if($_mode=='pcListSelect'){

		$tempSkinInfoMo = SkinInfoMo('category');

		// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
		// 리스트형일땐 리스트형만
		if(preg_match("/박스/",$_list_view_pc)==true){
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'box';
		}else{
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'list';
		}
			
		die(json_encode($arrProductDisplay['arrListMoView']));

	}else	if($_mode=='pcMainSelect'){
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
		
		$s_query = "
			s_main_timesale_title = '".$s_main_timesale_title."'
			,s_main_timesale_limit = '".$s_main_timesale_limit."'
			,s_main_timesale_view = '".$s_main_timesale_view."'
			,s_main_timesale_display = '".rm_str($s_main_timesale_display)."'
			,s_main_timesale_mobile_display = '".rm_str($s_main_timesale_mobile_display)."'
			,s_list_timesale_display = '".rm_str($s_list_timesale_display)."'
			,s_list_timesale_mobile_display = '".rm_str($s_list_timesale_mobile_display)."'
			,s_main_timesale_display_type = '".$main_view_type."'
			,s_list_timesale_display_type = '".$list_view_type."'
			,s_main_timesale_order = '".$s_main_timesale_order."'
		";

		_MQ_noreturn(" update smart_setup set  ".$s_query." where s_uid = '1' ");
		
		error_loc("_config.display.timesale.php");

	}

?>