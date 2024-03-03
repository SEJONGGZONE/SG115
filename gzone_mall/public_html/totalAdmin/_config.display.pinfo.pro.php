<?php
include_once('inc.php');


if($_mode=='pcListSelect'){

		$tempSkinInfoMo = SkinInfoMo('category');

		// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
		// 리스트형일땐 리스트형만
		if(preg_match("/박스/",$_relation_view_pc)==true){
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'box';
		}else{
			$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
			$arrProductDisplay['arrListMoView']['type'] = 'list';
		}
			
		die(json_encode($arrProductDisplay['arrListMoView']));

}else{
	$s_relation_display = nullchk($s_relation_display , '상품 상세 진열설정을 입력해주시기 바랍니다.');
	$s_relation_mobile_display	= nullchk($s_relation_mobile_display , '상품 상세 진열설정을 입력해주시기 바랍니다.');

	// 설정값 업데이트
	$que = "
		update smart_setup set
			s_display_pinfo_add = '". (is_array($s_display_pinfo_add) ? implode('|', array_filter($s_display_pinfo_add)) : $s_display_pinfo_add) ."'
			,s_display_pinfo_add_info = '". (is_array($s_display_pinfo_add_info) ? implode('|', array_filter($s_display_pinfo_add_info)) : $s_display_pinfo_add_info) ."'
			,s_display_relation_mo_use = '". $s_display_relation_mo_use ."'
			,s_display_content_open = '". $s_display_content_open ."'
			,s_relation_display = '". rm_str($s_relation_display) ."'
			,s_relation_mobile_display = '". rm_str($s_relation_mobile_display) ."'
			,s_relation_display_type = '". $list_view_type ."'
		where s_uid = '1'
	";
	_MQ_noreturn($que);

	// 설정페이지 이동
	error_loc_msg('_config.display.pinfo.php', '정상적으로 저장되었습니다.');

}
