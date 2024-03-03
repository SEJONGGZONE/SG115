<?PHP

	// LMH005

	include "inc.php";


	// - 입력수정 사전처리 ---
	if( in_array($_mode , array("add" , "modify"))) {
		// --사전 체크 ---
		$pr_amount = nullchk(rm_str($pr_amount) , "할인율 또는 할인금액을 입력하시기 바랍니다.");

		if($_mode == 'add'){
			$pr_code = nullchk($pr_code , "프로모션코드를 입력하시기 바랍니다.");

			// 프로모션 코드 중복 체크
			$chk_org = _MQ(" select pr_code from smart_promotion_code where pr_uid = '".$pr_uid."' ");
			if( $chk_org['pr_code']<>$pr_code ) {
				$chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$pr_code."' ");
				if( $chk>0 ) { error_msg("이미 등록된 프로모션 코드 입니다."); }
			}
		}

		// 최대 할인율 체크 
		if($pr_type == 'P'){
			if( $pr_price_max_use == 'Y' && $pr_price_max < 1){ error_msg("최대 할인 금액을 입력해 주시기 바랍니다."); }
		}

		$pr_expire_date = nullchk($pr_expire_date , "만료일을 선택하시기 바랍니다.");
		// --사전 체크 ---
	}
	// - 입력수정 사전처리 ---




	// - 모드별 처리 ---
	switch( $_mode ){


		// -- 추가 ---
		case "add":
			
			_MQ_noreturn("
				insert smart_promotion_code set
					pr_code			= '".$pr_code."',
					pr_name			= '".$pr_name."',
					pr_amount		= '".rm_str($pr_amount)."',
					pr_expire_date	= '".$pr_expire_date."',
					pr_expire		= 'N',
					pr_use			= '".$pr_use."',
					pr_type			= '".$pr_type."',
					pr_min_order_price = '".rm_str($pr_min_order_price)."',
					pr_price_max_use = '".($pr_price_max_use == 'Y' ? 'Y':'N')."',
					pr_price_max = '".rm_str($pr_price_max)."',
					pr_due_use = '".($pr_due_use == 'Y' ? 'Y':'N')."',
					pr_rdate		= now()

				");

			error_loc( "_promotion.list.php?" . enc('d' , $_PVSC) );
			break;
		// -- 추가 ---


		// -- 수정 ---
		case "modify":
			$sque = "
				update smart_promotion_code set
					pr_code			= '".$pr_code."',
					pr_name			= '".$pr_name."',
					pr_amount		= '".rm_str($pr_amount)."',
					pr_expire_date	= '".$pr_expire_date."',
					pr_use			= '".$pr_use."',
					pr_type			= '".$pr_type."',
					pr_min_order_price = '".rm_str($pr_min_order_price)."',
					pr_price_max_use = '".($pr_price_max_use == 'Y' ? 'Y':'N')."',
					pr_price_max = '".rm_str($pr_price_max)."',
					pr_due_use = '".($pr_due_use == 'Y' ? 'Y':'N')."',
					pr_edate		= now()
				where 
					pr_uid='${pr_uid}'
			";
			_MQ_noreturn( $sque );
			error_loc("_promotion.form.php" . URI_Rebuild('?', array('_mode'=>'modify', 'pr_uid'=>$pr_uid, '_PVSC'=>$_PVSC)));
			break;
		// -- 수정 ---


		// -- 삭제 ---
		case "delete":
			_MQ_noreturn("delete from smart_promotion_code where pr_uid='$pr_uid' ");
			error_loc( "_promotion.list.php" . URI_Rebuild('?'.enc('d' , $_PVSC)) );
			break;
		// -- 삭제 ---


		// 선택삭제
		case "select_delete":
			if(sizeof($chk_id) == 0 ) {
				error_msg("선택된 코드가 없습니다.");
			}
			_MQ_noreturn("delete from smart_promotion_code where pr_uid in ('".implode("','" , array_keys($chk_id))."') ");

			error_loc_msg("_promotion.list.php".URI_Rebuild('?'.enc('d' , $_PVSC)) , "정상적으로 삭제하였습니다.");
			break;

		// 프로모션 코드 중복체크
		case 'pr_code_check':

			// 아이디 입력여부 검증
			$_pr_code = trim($_pr_code);
			if(empty($_pr_code) || $_pr_code == '') die(json_encode(array('result'=>'error', 'msg'=>'프로모션 코드를 입력해주세요.'))); // 프로모션 코드 입력 체크

			// 중복 처리 및 가능처리
			$r = _MQ(" select count(*) as cnt from smart_promotion_code where pr_code='{$_pr_code}' ");
			if($r['cnt'] > 0) die(json_encode(array('result'=>'error', 'msg'=>'이미 등록된 프로모션 코드 입니다.'))); // 중복
			else die(json_encode(array('result'=>'success', 'msg'=>'등록이 가능한 프로모션 코드입니다.'))); // 사용가능
		break;
	}
	// - 모드별 처리 ---

?>
