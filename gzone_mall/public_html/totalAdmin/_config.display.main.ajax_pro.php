<?php
	include_once("inc.php");

	if($_mode == ''){ echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

	// -- 공통쿼리문 실행
	if( in_array($_mode,array("add","modify")) == true){

		if( isset($_uid)){
			$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");
		}

		if($_name == ''){echo json_encode(array('rst'=>'blank','msg'=>'분류명을 입력해 주세요.','key'=>'_name')); exit;}
		if($_view == ''){echo json_encode(array('rst'=>'blank','msg'=>'노출여부를 선택해 주세요.','key'=>'_view')); exit;}

		$sque = "
			dms_view													= '".$_view."'
			, dms_name												='".addslashes($_name)."'
		";

		// -- 2차만 가능한 아이템들
		if( $_depth != 1) {

			if($locUid1!=2){
				if($_main_product_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'메인 상품진열 설정을 선택해 주세요.','key'=>'_main_product_display')); exit;}
				if($_main_product_mobile_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'메인 반응형 상품진열 설정을 선택해 주세요','key'=>'_main_product_mobile_display')); exit;}
			}
            
			if($_list_product_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'목록 상품진열 설정을 선택해 주세요.','key'=>'_list_product_display')); exit;}
			if($_list_product_mobile_display == ''){echo json_encode(array('rst'=>'blank','msg'=>'목록 반응형 상품진열 설정을 선택해 주세요','key'=>'_list_product_mobile_display')); exit;}

			$sque .= "
				,	dms_list_product_mobile_view													= '".$_list_product_mobile_view."'
				,	dms_list_product_mobile_limit													= '".$_list_product_mobile_limit."'
				,	dms_list_product_display													= '".rm_str($_list_product_display)."'
				,	dms_list_product_mobile_display													= '".rm_str($_list_product_mobile_display)."'
				,	dms_main_product_display													= '".rm_str($_main_product_display)."'
				,	dms_main_product_mobile_display													= '".rm_str($_main_product_mobile_display)."'
				,	dms_main_product_display_type									 = '".$main_view_type."'
				,	dms_list_product_display_type										= '".$list_view_type."'
			";
		
		}
	}

	switch($_mode){

		// -- 수정
		case "modify":
			// -- 해당 메뉴의 정보를 가져온다.
			if(count($row) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			_MQ_noreturn("update smart_display_main_set set ".$sque." where dms_uid = '".$_uid."'  ");


			echo json_encode(array('rst'=>'success','msg'=>'저장되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 추가
		case "add":

			if($_depth == 1){
				echo json_encode(array('rst'=>'fail','msg'=>'1차분류는 추가가 불가능합니다.')); exit;
			}

			$_parent = 0;
			$_parent = $locUid1;
			// -- 부모의 정보를 가져온다.
			$rowParent = _MQ("select *from smart_display_main_set where dms_uid = '".$_parent."' ");

			// -- 부모 정보가 없다면 무조건 오류처리
			if(count($rowParent) < 1){ echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit; }


			// -- 분류 순서 초기화
			$rowIdx = _MQ("select ifnull(max(dms_idx),0) + 1 as max_idx from smart_display_main_set where dms_depth = '".$_depth."' ".($_depth > 1 ? "and dms_parent = '".$_parent."' ":""));
			_MQ_noreturn("insert smart_display_main_set set
				".$sque."
				, dms_depth		= '".$_depth."'
				, dms_parent	= '".$_parent."'
				, dms_idx			= '".$rowIdx['max_idx']."'
				, dms_type		= '".$rowParent['dms_type']."'
			");

			echo json_encode(array('rst'=>'success','msg'=>'분류가 추가되었습니다.','_depth'=>$_depth)); exit;

		break;

		// -- 삭제
		case "delete":

			if($_depth == 1){ echo json_encode(array('rst'=>'fail','msg'=>'1차 분류는 삭제할 수 없습니다.')); exit; }

			// -- 해당  정보를 가져온다.
			$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");
			if(count($row) < 1 ){  echo json_encode(array('rst'=>'error','msg'=>'잘못된 접근입니다.')); exit; }

			// 해당  부모로 가진 분류 있을 경우 삭제 불가
			$rowChk = _MQ("select count(*) as cnt from smart_display_main_set where find_in_set('".$_uid."' , dms_parent) > 0 ");
			if( $rowChk['cnt'] > 0 ) {
				error_msgPopup_s("하위 분류가 있어 삭제할 수 없습니다.");
			}

			_MQ_noreturn("delete from smart_display_main_set where dms_uid = '".$_uid."' or find_in_set('".$_uid."',dms_parent) > 0 ");
			echo json_encode(array('rst'=>'success','msg'=>'분류가 삭제되었습니다.','_depth'=>$_depth)); exit;
		break;

		// -- 순서변경
		case "idx":

			if( $_type == 'up'){
		    // 정보 불러오기
		    $que  = " SELECT dms_idx , dms_depth , dms_parent FROM smart_display_main_set WHERE dms_uid = '$_uid' ";
		    $r = _MQ($que);

		    $_idx = $r[dms_idx];
		    $_parent = $r[dms_parent];
		    $_depth = $r[dms_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_display_main_set WHERE dms_idx = '$_idx' and dms_parent='$_parent' ";
		        $r = _MQ($que);
		        if($r[cnt] > 1) {
		      _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = dms_idx+1 WHERE dms_idx >= '$_idx' and dms_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MIN(dms_idx),0) as mindms_idx FROM smart_display_main_set WHERE dms_parent='$_parent' ";
		    $r = _MQ($que);
		    $mindms_idx = $r[mindms_idx];

		    if ($mindms_idx == $_idx) {
		       echo json_encode(array('rst'=>'fail','msg'=>'더이상 상위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계위 데이터와 dms_idx 값 바꿈
		        $sque = "select dms_idx , dms_uid from smart_display_main_set WHERE dms_parent='$_parent' and dms_idx < '$_idx' order by dms_idx desc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $_idx WHERE dms_uid='$sr[dms_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $sr[dms_idx] WHERE dms_uid = '$_uid' ");

		    }
		  }else if($_type == 'down'){

		    // 정보 불러오기
		    $que  = " SELECT dms_idx , dms_depth , dms_parent FROM smart_display_main_set WHERE dms_uid = '$_uid' ";
		    $r = _MQ($que);
		    $_idx = $r[dms_idx];
		    $_parent = $r[dms_parent];
		    $_depth = $r[dms_depth];

		        // 같은 순위의 값이 있는지 체크///////////////////////////
		    $que  = " SELECT count(*) as cnt FROM smart_display_main_set WHERE dms_idx = '$_idx' and dms_parent='$_parent' ";
		        $r = _MQ($que);

		        if($r[cnt] > 1) {
		      		_MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = dms_idx-1 WHERE dms_idx <= '$_idx' and dms_parent='$_parent' ");
		        }

		    // 최소 순위  찾기 //////////////////////////////////////////
		    $que  = " SELECT ifnull(MAX(dms_idx),0) as maxdms_idx FROM smart_display_main_set WHERE dms_parent='$_parent' ";
		    $r = _MQ($que);
		    $maxdms_idx = $r[maxdms_idx];

		    if ($maxdms_idx == $_idx) {
		    	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }
		    else {

		        // 바로 한단계 아래 데이터와 dms_idx 값 바꿈
		        $sque = "select dms_idx , dms_uid from smart_display_main_set WHERE 1 and dms_idx > '$_idx' ".($_depth != 1 ? " and dms_parent='$_parent'  ":"" )." order by dms_idx asc limit 1";
		        $sr = _MQ($sque);

		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $_idx WHERE dms_uid='$sr[dms_uid]'");

		        // 순서값 제거 - 자신의 순서값
		        _MQ_noreturn(" UPDATE smart_display_main_set SET dms_idx = $sr[dms_idx] WHERE dms_uid = '$_uid' ");
		        //	echo json_encode(array('rst'=>'fail','msg'=>'더이상 하위로 이동할 수 없습니다.')); exit;
		    }

		  }else{
		  	echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit;
		  }

			echo json_encode(array('rst'=>'success')); exit;


		break;

		// -- 선택된 상품 리스트 목록 가져오기
		case "selectMainProductList":

			ob_start();
			include_once dirname(__FILE__)."/_config.display.main.ajax_form.inc_product_list.php";
			$printList = ob_get_clean();

			echo json_encode(array('rst'=>'success','cnt'=>count($res),'printList'=>$printList));
			exit;

		break;


		// -- 선택된 아이템 삭제
		case "selectMainProductDelete":

			main_product_resort($_uid);

			if( rm_str($_uid) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'분류 추가 후 선택 가능합니다.')); exit; }
			// -- 변수로 해석 :: 선택된 아이팀을 변수로 해석
			if($selectVar != '' ) parse_str($selectVar);

			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			_MQ_noreturn("delete from smart_display_main_product where dmp_dmsuid = '".$_uid."' and find_in_set(dmp_pcode, '".implode(",",array_values($chk_pcode))."' ) > 0    ");
			echo json_encode(array('rst'=>'success','msg'=>'선택된 상품이 삭제되었습니다.')); exit;
		break;

		// -- 선택된 아이템 추가
		case "selectMainProductAdd":

			if( rm_str($_uid) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택적용이 불가능합니다.')); exit; }
			if($selectVar != '' ) parse_str($selectVar);
			if( count($chk_pcode) < 1 ){ echo json_encode(array('rst'=>'fail','msg'=>'선택된 값이 없습니다.')); exit; }
			$que = " select p_code from smart_product where p_code in ('". implode("','", $chk_pcode) ."') order by p_idx desc ";
			$pres = _MQ_assoc($que);
			foreach($pres as $k=>$v){
				$pcode = $v['p_code'];
				// -- 중복의 경우 업데이트 처리 :: duplicate
				_MQ_noreturn("
					insert into smart_display_main_product 
						(dmp_pcode,dmp_dmsuid,dmp_idx,dmp_sort_idx,dmp_sort_group ) 
					values 
						('".$pcode."','".$_uid."','0.5','0.5','100')
					on duplicate key 
					update dmp_pcode = '".$pcode."', dmp_dmsuid = '".$_uid."', dmp_idx = '".delComma($_idx)."', dmp_sort_idx = '".delComma($_sort_idx)."', dmp_sort_group = '".delComma($_sort_group)."'  ");
			}
			main_product_resort($_uid);

			echo json_encode(array('rst'=>'success','msg'=>'선택하신 상품이 적용 되었습니다.')); exit;

		break;

		// 진열설정 목록 PC 선택
		case "pcListSelect":

			$tempSkinInfoMo = SkinInfoMo('category');

			// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
			// 리스트형일땐 리스트형만
			if(preg_match("/박스/",$_pro_list_pc)==true){
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_box_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'box';
			}else{
				$arrProductDisplay['arrListMoView']['name'] = explode(",",$tempSkinInfoMo['mo_pro_list_depth']);
				$arrProductDisplay['arrListMoView']['type'] = 'list';
			}

			die(json_encode($arrProductDisplay['arrListMoView']));
		break;

		// 진열설정 메인 PC 선택
		case "pcMainSelect":

			$tempSkinInfoMo = SkinInfoMo('category');

			// -- 리스트 :: 스킨에 따른 단수처리 :: 박스형일땐 박스형만
			// 리스트형일땐 리스트형만
			if(preg_match("/박스/",$_pro_main_pc)==true){
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