<?PHP
	include "./inc.php";

	// - 모드별 처리 ---
	switch( $_mode ){

		case "modify":

			$_brand_display = nullchk($_brand_display, '브랜드 진열 설정을 선택해주세요.');
			$_brand_mobile_display = nullchk($_brand_mobile_display, '브랜드 진열 설정을 선택해주세요.');
			
			_MQ_noreturn("
				update smart_setup set
					s_brand_view = '". $_brand_view ."' 
					,s_brand_display = '". rm_str($_brand_display) ."' 
					,s_brand_mobile_display = '". rm_str($_brand_mobile_display) ."' 
					,s_brand_limit = '". $_brand_limit ."' 
					,s_brand_display_type = '". $list_view_type ."' 
				where
					s_uid = '1'
			");

			error_frame_loc_msg("_brand.list.php" , "브랜드 설정이 저장되었습니다.");
			break;

	}
	// - 모드별 처리 ---

	exit;
?>