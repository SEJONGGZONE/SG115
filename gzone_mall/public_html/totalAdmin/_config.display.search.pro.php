<?php
	include_once('inc.php');

	/*
		# DB :: smart_setup
		- s_search_option
		- s_search_display
		- s_search_mobile_display
		- s_search_diff_orderby
		- s_search_diff_maxcnt
		- s_search_diff_option
	*/

	if($_mode=='pcSelect'){
		$tempSkinInfoMo = SkinInfoMo('category');

		// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
		// 리스트형일땐 리스트형만
		if(preg_match("/박스/",$_search_view_pc)==true){
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'box';
		}else{
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'list';
		}
			
		die(json_encode($arrProductDisplay['arrListMoView']));
	}else{

		// -- 콤마제거
		$s_search_diff_maxcnt = delComma($s_search_diff_maxcnt); // 다른고객이 많이 찾은상품 최대개수

		// -- 분류설정 확인
		$s_search_option = count($s_search_option) > 0 ? implode(",",$s_search_option) : '';

		$s_query = "
				s_search_option = '".$s_search_option."'
			,	s_search_display = '".rm_str($s_search_display)."'
			,	s_search_mobile_display = '".rm_str($s_search_mobile_display)."'
			,	s_search_diff_orderby = '".$s_search_diff_orderby."'
			,	s_search_diff_maxcnt = '".$s_search_diff_maxcnt."'
			,	s_search_diff_option = '".$s_search_diff_option."'
			,   s_recommend_keyword = '".$s_recommend_keyword."'
			,   s_recommend_hashtag = '".$s_recommend_hashtag."'
			,   s_saerch_text = '".$s_saerch_text."'
			,	s_search_display_type ='".$main_view_type."'
		";

		_MQ_noreturn(" update smart_setup set  ".$s_query." where s_uid = '1' ");
		error_loc("_config.display.search.php");
	}
?>