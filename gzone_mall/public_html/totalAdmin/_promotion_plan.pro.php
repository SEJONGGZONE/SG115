<?PHP

	include "./inc.php";


	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$_title		= nullchk($_title , "기획전명을 입력해주시기 바랍니다.");
		$_sdate		= nullchk($_sdate , "진행기간을 입력해주시기 바랍니다.");
		$_edate		= nullchk($_edate , "진행기간을 입력해주시기 바랍니다.");
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro('../upfiles/banner', 'pp_img') ; // 기획전 목록 이미지

		// --query 사전 준비 ---
		$sque = "
			pp_title = '" . addslashes($_title) . "'
			, pp_view = '" . $_view . "'
			, pp_sdate = '" . $_sdate . "'
			, pp_edate = '" . $_edate . "'
			, pp_img = '{$_imgname}'
		";
		// --query 사전 준비 ---
		

		// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
		$_title = addslashes($_title);
		$_content = wrap_tag_iframe($_content);
		// 2019-12-04 SSJ :: 이미지 alt 속성 자동추가
		$_content = set_img_alter($_content, $_title);

		// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
		$_content_m = wrap_tag_iframe($_content_m);
		// 2019-12-04 SSJ :: 이미지 alt 속성 자동추가
		$_content_m = set_img_alter($_content_m, $_title);


	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		// 설정저장
		case "config":

			// --사전 체크 ---
			$_promotion_plan_view		= nullchk($_promotion_plan_view , "기획전 노출여부를 선택해주시기 바랍니다.");
			$_promotion_plan_title		= nullchk($_promotion_plan_title , "기획전 메뉴명을 입력해주시기 바랍니다.");
			$_promotion_display		= nullchk($_promotion_display , "상품진열 첫화면 설정을 선택해주시기 바랍니다.");
			$_promotion_mobile_display		= nullchk($_promotion_mobile_display , "상품진열 반응형 설정을 선택해주시기 바랍니다.");

			$que = "
				update smart_setup set
					s_promotion_plan_view = '". $_promotion_plan_view ."'
					,s_promotion_plan_title = '". $_promotion_plan_title ."'
					,s_promotion_display = '". rm_str($_promotion_display) ."'
					,s_promotion_mobile_display = '". rm_str($_promotion_mobile_display) ."'
					,s_promotion_display_type = '". $list_view_type ."'
			";
			_MQ_noreturn($que);

			error_frame_loc_msg('_promotion_plan.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null) , '설정이 저장되었습니다.');
			break;


		case "add":

			$que = " insert smart_promotion_plan set $sque , pp_rdate = now() ";
			_MQ_noreturn($que);
			$uid = _MQ_insert_id();

			// _text_info_insert 입력 시 함수 내에 addslashes 작동함.
			_text_info_insert( "smart_promotion_plan" , $uid , "pp_content" , $_content , "ignore");//기획전내용 (PC)
			_text_info_insert( "smart_promotion_plan" , $uid , "pp_content_m" , $_content_m , "ignore");//기획전내용 (MOBLIE)

			// KAY :: 에디터 이미지 관리 :: 에디터에 이미지 등록함수 :: 2021-06-02 -------------
			editor_img_ex($_content.$_content_m , 'promotion' , $uid);

			// 기획전 상품 있을 경우 수정 ---> uid가 0인 것을 추가한 $uid로 변경함.
			$que = " UPDATE smart_promotion_plan_product_setup SET ppps_ppuid = '". $uid ."' WHERE ppps_ppuid = '0' ";
			_MQ_noreturn($que);

			error_loc("_promotion_plan.form.php?_mode=modify&uid=${uid}&_PVSC=${_PVSC}");
			break;



		case "modify":
			$que = " update smart_promotion_plan set $sque where pp_uid='{$uid}' ";
			_MQ_noreturn($que);

			// _text_info_insert 입력 시 함수 내에 addslashes 작동함.
			_text_info_insert( "smart_promotion_plan" , $uid , "pp_content" , $_content , "ignore");//기획전내용 (PC)
			_text_info_insert( "smart_promotion_plan" , $uid , "pp_content_m" , $_content_m , "ignore");//기획전내용 (MOBLIE)

			// KAY :: 에디터 이미지 관리 :: 에디터에 이미지 등록함수 :: 2021-06-02 -------------
			editor_img_ex($_content.$_content_m , 'promotion' , $uid);

			error_loc("_promotion_plan.form.php?_mode=${_mode}&uid=${uid}&_PVSC=${_PVSC}");
			break;



		case "delete":

			// -- 이미지 삭제 ---
			$r = _MQ(" select pp_img from smart_promotion_plan where pp_uid = '{$_uid}' ");
			if($r['pp_img']) _PhotoDel('../upfiles/banner', $r['pp_img']);
			// -- 이미지 삭제 ---

			// 상품정보 삭제
			_MQ_noreturn("delete from smart_promotion_plan where pp_uid='{$uid}' ");

			_text_info_delete( "smart_promotion_plan" , $uid , "pp_content");//기획전내용 (PC)
			_text_info_delete( "smart_promotion_plan" , $uid , "pp_content_m");//기획전내용 (MOBLIE)

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($_uid,'promotion');

			// 기획전 상품 삭제
			_MQ_noreturn(" delete from smart_promotion_plan_product_setup where ppps_ppuid = '". $uid ."' ");

			error_loc("_promotion_plan.list.php?".enc('d' , $_PVSC));
			break;




		// 일괄삭제
		case "mass_delete":

			$s_query = " where pp_uid in ('".implode("','" , array_keys($chk_pcode))."') ";

			// -- text 내용 삭제 ---
			$res = _MQ_assoc("select pp_uid, pp_img from smart_promotion_plan {$s_query} ");
			foreach($res as $k=>$v){

				_text_info_delete( "smart_promotion_plan" , $v['pp_uid'] , "pp_content");//기획전내용 (PC)
				_text_info_delete( "smart_promotion_plan" , $v['pp_uid'] , "pp_content_m");//기획전내용 (MOBLIE)

				// -- 이미지 삭제 ---
				if($v['pp_img']) _PhotoDel('../upfiles/banner', $v['pp_img']);
				// -- 이미지 삭제 ---

			}
			// -- text 내용 삭제 ---

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del(array_keys($chk_pcode),'promotion');

			// 기획전 삭제
			_MQ_noreturn("delete from smart_promotion_plan {$s_query} ");

			// 기획전 상품 삭제
			_MQ_noreturn(" delete from smart_promotion_plan_product_setup where ppps_ppuid in ('".implode("','" , array_keys($chk_pcode))."') ");

			error_loc("_promotion_plan.list.php?".enc('d' , $_PVSC));
			break;


		// 진열설정 PC 선택
		case "pcListSelect":

			$tempSkinInfoMo = SkinInfoMo('category');

			// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
			// 리스트형일땐 리스트형만
			if(preg_match("/박스/",$_promotion_view_pc)==true){
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'box';
			}else{
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'list';
			}
				
			die(json_encode($arrProductDisplay['arrListMoView']));

		break;

	}
	// - 모드별 처리 ---
