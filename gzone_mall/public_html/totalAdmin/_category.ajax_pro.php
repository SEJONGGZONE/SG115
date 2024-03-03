<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

	// -- 공통쿼리문 실행
	if( in_array($_mode,array("add","modify")) == true){
		
		if( isset($_uid)){
			$rowCategory = _MQ("select *from smart_category where c_uid = '".$_uid."' ");
		}

		if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'카테고리명을 입력해 주세요.','key'=>'_name')); exit;}
		if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

		if( $_img_top_banner_use == 'Y' && $rowCategory['c_img_top_banner'] == '' && ($_FILES['_img_top_banner']['size']) < 1  ){  echo json_encode(array('rst'=>'blank','msg'=>'(PC) 상단 배너를 등록해 주세요.','key'=>'_img_top_banner')); exit; }
		if( $_img_top_mobile_banner_use == 'Y' && $rowCategory['c_img_top_banner'] == '' && ($_FILES['_img_top_mobile_banner']) < 1){  echo json_encode(array('rst'=>'blank','msg'=>'(모바일) 상단 배너를 등록해 주세요.','key'=>'_img_top_mobile_banner')); exit; }

		// --이미지 처리 ---
		$_img_top_banner_name = _PhotoPro('..'.IMG_DIR_CATEGORY, '_img_top_banner') ; // 배너이미지
		$_img_top_mobile_banner_name = _PhotoPro('..'.IMG_DIR_CATEGORY, '_img_top_mobile_banner') ; // 배너이미지

		// -- 상위 카테고리 일경우
		if($_depth < 3 && $_uid != ''){

			// 베스트 상품노출 하위 카테고리 동일 적용일경우 처리
			if($_best_product_mobile_all == 'Y'){
				_MQ_noreturn("update smart_category set c_best_product_mobile_view = '".$_best_product_mobile_view."', c_best_product_display = '".$_best_product_display."'  , c_best_product_mobile_display = '".$_best_product_mobile_display."' where find_in_set('".$_uid."',c_parent ) > 0 and c_depth > '".$_depth."' ");
			}

			// 목록 상품노출 하위 카테고리 동일 적용일경우 처리
			if($_list_product_mobile_all == 'Y'){
				_MQ_noreturn("update smart_category set c_list_product_mobile_view = '".$_list_product_mobile_view."', c_list_product_display = '".$_list_product_display."', c_list_product_mobile_display = '".$_list_product_mobile_display."' where find_in_set('".$_uid."',c_parent ) > 0 and c_depth > '".$_depth."' ");
			}

		}

		// -- 현재 저장된 스킨의 정보를 가져온다.
		$tempSkinInfo = SkinInfo('category');
		if( $tempSkinInfo['pc_image_width'] == 'off'){ $_img_top_banner_use = 'N'; } // 스킨에서 사용이 off 라면
		if( $tempSkinInfo['mo_image_width'] == 'off'){ $_img_top_mobile_banner_use = 'N'; } // 스킨에서 사용이 off 라면

		if($_list_product_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'목록 상품진열 설정을 선택해 주세요.','key'=>'_list_product_display')); exit;}
		if($_list_product_mobile_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'목록 반응형 상품진열 설정을 선택해 주세요.','key'=>'_list_product_mobile_display')); exit;}

		if($_best_product_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'베스트 상품진열 설정을 선택해 주세요.','key'=>'_best_product_display')); exit;}
		if($_best_product_mobile_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'베스트 반응형 상품진열 설정을 선택해 주세요','key'=>'_best_product_mobile_display')); exit;}



		$sque = "
			c_view													= '".$_view."'
			, c_name												='".addslashes($_name)."'
			,c_img_top_banner_use						= '".$_img_top_banner_use."'
			, c_best_product_view						= '".$_best_product_view."'
			, c_best_product_display				= '".rm_str($_best_product_display)."'
			, c_best_product_mobile_view		= '".$_best_product_mobile_view."'
			, c_best_product_mobile_display	= '".rm_str($_best_product_mobile_display)."'

			, c_list_product_view						= '".$_list_product_view."'
			, c_list_product_display				= '".rm_str($_list_product_display)."'
			, c_list_product_mobile_view		= '".$_list_product_mobile_view."'
			, c_list_product_mobile_display	= '".rm_str($_list_product_mobile_display)."'

			,c_img_top_banner								= '".$_img_top_banner_name."'
			,c_img_top_banner_target				= '".$_img_top_banner_target."'
			,c_img_top_banner_link					= '".$_img_top_banner_link."'
			,c_img_top_mobile_banner_use		= '".$_img_top_mobile_banner_use."'
			,c_img_top_mobile_banner				= '".$_img_top_mobile_banner_name."'
			,c_img_top_mobile_banner_target	= '".$_img_top_mobile_banner_target."'
			,c_img_top_mobile_banner_link		= '".$_img_top_mobile_banner_link."'
			,c_list_limit													= '".$_list_limit."'
			,c_list_product_display_type													= '".$list_view_type."'
			,c_best_product_display_type													= '".$best_view_type."'
		";
	}

	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 메뉴의 정보를 가져온다.
			if(count($rowCategory) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			_MQ_noreturn("update smart_category set
				".$sque."
				where c_uid = '".$_uid."'  ");

			echo json_encode(array('rst'=>'success','msg'=>'카테고리가 저장되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 추가
		case "add":

			$_parent = 0;
			if($_depth == 2){
				$_parent = $locUid1;
			}else if($_depth == 3){
				$_parent = $locUid1.",".$locUid2;
			}

			// -- 카테고리 순서 초기화
			$rowIdx = _MQ("select ifnull(max(c_idx),0) + 1 as max_idx from smart_category where c_depth = '".$_depth."' ".($_depth > 1 ? "and c_parent = '".$_parent."' ":""));
			_MQ_noreturn("insert smart_category set
				".$sque."
				, c_depth		= '".$_depth."'
				, c_parent	= '".$_parent."'
				, c_idx			= '".$rowIdx['max_idx']."'
			");

			echo json_encode(array('rst'=>'success','msg'=>'카테고리가 추가되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 삭제
		case "delete":
			// -- 해당 메뉴의 정보를 가져온다.
			$rowCategory = _MQ("select *from smart_category where c_uid = '".$_uid."' ");
			if(count($rowCategory) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			// 해당 카테고리를 부모로 가진 카테고리 있을 경우 삭제 불가
			$rowChk = _MQ("select count(*) as cnt from smart_category where find_in_set('".$_uid."' , c_parent) > 0 ");
			if( $rowChk['cnt'] > 0 ) {
				 echo json_encode(array('rst'=>'error','msg'=>'하위 카테고리가 있어 삭제할 수 없습니다.')); exit;
			}

			// -- 등록이미지 삭제 --
			_PhotoDel( "..".IMG_DIR_CATEGORY , $rowCategory['c_img_top_banner'] );
			_PhotoDel( "..".IMG_DIR_CATEGORY , $rowCategory['c_img_top_mobile_banner'] );

			_MQ_noreturn("delete from smart_category where c_uid = '".$_uid."'  ");
			echo json_encode(array('rst'=>'success','msg'=>'메뉴가 삭제되었습니다.','_depth'=>$_depth)); exit;
		break;

		// -- 순서변경
		case "idx":

			if( $_type == 'up'){
		    // 정보 불러오기
		    $que  = " SELECT c_idx , c_depth , c_parent FROM smart_category WHERE c_uid = '$_uid' ";
		    $r = _MQ($que);

		    $_idx = $r[c_idx];
		    $_parent = $r[c_parent];
		    $_depth = $r[c_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_category WHERE c_idx = '$_idx' and c_parent='$_parent' ";
		        $r = _MQ($que);
		        if($r[cnt] > 1) {
		      _MQ_noreturn(" UPDATE smart_category SET c_idx = c_idx+1 WHERE c_idx >= '$_idx' and c_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MIN(c_idx),0) as minc_idx FROM smart_category WHERE c_parent='$_parent' ";
		    $r = _MQ($que);
		    $minc_idx = $r[minc_idx];

		    if ($minc_idx == $_idx) {
		       echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계위 데이터와 c_idx 값 바꿈
		        $sque = "select c_idx , c_uid from smart_category WHERE c_parent='$_parent' and c_idx < '$_idx' order by c_idx desc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_category SET c_idx = $_idx WHERE c_uid='$sr[c_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_category SET c_idx = $sr[c_idx] WHERE c_uid = '$_uid' ");

		    }
		  }else if($_type == 'down'){

		    // 정보 불러오기
		    $que  = " SELECT c_idx , c_depth , c_parent FROM smart_category WHERE c_uid = '$_uid' ";
		    $r = _MQ($que);
		    $_idx = $r[c_idx];
		    $_parent = $r[c_parent];
		    $_depth = $r[c_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_category WHERE c_idx = '$_idx' and c_parent='$_parent' ";
		        $r = _MQ($que);

		        if($r[cnt] > 1) {
		      		_MQ_noreturn(" UPDATE smart_category SET c_idx = c_idx-1 WHERE c_idx <= '$_idx' and c_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MAX(c_idx),0) as maxc_idx FROM smart_category WHERE c_parent='$_parent' ";
		    $r = _MQ($que);
		    $maxc_idx = $r[maxc_idx];

		    if ($maxc_idx == $_idx) {
		    	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계 아래 데이터와 c_idx 값 바꿈
		        $sque = "select c_idx , c_uid from smart_category WHERE 1 and c_idx > '$_idx' ".($_depth != 1 ? " and c_parent='$_parent'  ":"" )." order by c_idx asc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_category SET c_idx = $_idx WHERE c_uid='$sr[c_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_category SET c_idx = $sr[c_idx] WHERE c_uid = '$_uid' ");
		        //	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }

		  }else{
		  	echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
		  }

			echo json_encode(array('rst'=>'success')); exit;


		break;

		// -- 선택된 베스트 상품 리스트 목록 가져오기
		case "selectBestProductList":

			ob_start();
			include_once dirname(__FILE__)."/_category.ajax_form.inc_best_product_list.php";
			$printList = ob_get_clean();

			echo json_encode(array('rst'=>'success','cnt'=>count($res),'printList'=>$printList));
			exit;

		break;


		// -- 선택된 베스트 아이템 삭제
		case "selectBestProductDelete":

			//KAY : 상품진열관리 상품순서설정 추가 : 2021-11-18
			cate_product_resort($cuid);

			if( rm_str($cuid) < 1 ){ echo json_encode(array('rst'=>'카테고리 추가 후 선택 가능합니다.')); exit; }
			// -- 변수로 해석 :: 선택된 베스트 아이팀을 변수로 해석
			if($selectVar != '' ) parse_str($selectVar);
			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			_MQ_noreturn("delete from smart_product_category_best where pctb_cuid = '".$cuid."' and find_in_set(pctb_pcode, '".implode(",",array_values($chk_pcode))."' ) > 0    ");
			echo json_encode(array('rst'=>'success','msg'=>'선택된 베스트 상품이 삭제되었습니다.')); exit;
		break;

		// -- 선택된 베스트 아이템 추가
		case "selectBestProductAdd":

			if( rm_str($cuid) < 1 ){ echo json_encode(array('rst'=>'선택적용이 불가능합니다.')); exit; }
			if($selectVar != '' ) parse_str($selectVar);
			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			$que = " select p_code from smart_product where p_code in ('". implode("','", $chk_pcode) ."') order by p_idx desc ";
			$pres = _MQ_assoc($que);
			foreach($pres as $k=>$v){
				$pcode = $v['p_code'];
				// -- 중복의 경우 업데이트 처리 :: duplicate
				_MQ_noreturn("
					insert into smart_product_category_best
						(pctb_pcode,pctb_cuid,pctb_idx,pctb_sort_idx,pctb_sort_group ) 
					values 
						('".$pcode."','".$cuid."','0.5','0.5','100')
					on duplicate key 
					update pctb_pcode = '".$pcode."', pctb_cuid = '".$cuid."' , pctb_idx = '".delComma($_idx)."', pctb_sort_idx = '".delComma($_sort_idx)."', pctb_sort_group = '".delComma($_sort_group)."' ");
			}
			// KAY : 상품진열관리 상품순서설정 추가 : 2021-11-18
			cate_product_resort($cuid);

			echo json_encode(array('rst'=>'success','msg'=>'선택하신 상품이 적용 되었습니다.')); exit;

		break;

		// 진열설정 PC 선택
		case "pcListSelect":
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
		break;

		// 진열설정 PC 선택
		case "pcBestSelect":
			$tempSkinInfoMo = SkinInfoMo('category');

			// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
			// 리스트형일땐 리스트형만
			if(preg_match("/박스/",$_best_view_pc)==true){
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'box';
			}else{
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'list';
			}
				
			die(json_encode($arrProductDisplay['arrListMoView']));
		break;
	}
?>