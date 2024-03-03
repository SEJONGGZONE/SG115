<?PHP

	include_once "inc.php";

	// 상품타입을 기반으로 정보제공고시를 조정 
	$app_product_reqinfo = array();
	if( $pass_ptype == 'ticket'){
		$app_product_reqinfo['popup_url'] = '_product_ticket_reqinfo.popup.php';
	}
	else{
		$app_product_reqinfo['popup_url'] = '_product_reqinfo.popup.php';	
	}

	if( in_array($_mode , array("add" , "modify"))){

		// --사전 체크 ---
		if(sizeof($_key)>0){
			foreach($_key as $k=>$v) {
				$v = nullchk($v , "항목명은 필수 입력 사항입니다. \\n\\n항목명을 입력해 주세요. ");
			}
		}

	}

	switch($_mode){
		// - 수정 ---
		case "modify":

			// 기존정보 삭제
			_MQ_noreturn("delete from smart_product_req_info where pri_pcode='".$pass_code."' ");
			if(sizeof($_key)>0){
				foreach($_key as $k=>$v) {
					$sque = " 
						pri_pcode = '". $pass_code ."' ,
						pri_key = '". $_key[$k] ."' ,
						pri_value = '". $_value[$k] ."' 
					";
					_MQ_noreturn("insert into smart_product_req_info set $sque  ");
				}
			}
			error_loc($app_product_reqinfo['popup_url']."?pass_code=" . $pass_code);
			break;
	}

?>