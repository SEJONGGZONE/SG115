<?php
# 기타 프로세스(팝업, 히트등등)
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

if($_mode == 'eval_hit') { // 상품평점 hit 카운트
	if($_smode == 'update') {
		$_uid = preg_replace("/[^0-9]*/s", "", $_uid);
		$_uid = (int)$_uid;
		_MQ_noreturn(" update `smart_product_talk` set `pt_hit` = pt_hit+1 where `pt_uid` = '{$_uid}' ");
	}
}
else if($_mode == 'popup_close') { // 팝업닫기
	if(!$uid) die('error');
	$uid = preg_replace("/[^0-9]*/s", "", $uid);
	$uid = (int)$uid;
	samesiteCookie("AuthPopupClose_".$uid, 'Y', (time()+3600*24), '/', '.'.str_replace("www." , "" , reset(explode(':', $system['host']))));
	die('success');
}
else if($_mode == 'intro_skip') { // 인트로 스킵
	samesiteCookie('intro_skip', 'Y', (time()+3600*24), '/', '.'.str_replace("www." , "" , reset(explode(':', $system['host']))));
	die('success');
}

else if($_mode == 'banner_close') { // 닫기형 배너를 하루동안 열지 않음
	if(!$uid) die('error');
	$uid = preg_replace("/[^0-9]*/s", "", $uid);
	$uid = (int)$uid;
	samesiteCookie("AuthBannerClose_".$uid, 'Y', (time()+3600*24), '/', '.'.str_replace("www." , "" , reset(explode(':', $system['host']))));
	die('success');
}


else if($_mode == 'request_add_files') { // 1:1 문의 파일

	if($idx < 1) die(json_encode(array('rst'=>'fail')));
	$nextIdx = ($idx+1);
	$html = '
		<div class="duplicate list-files" data-mode="add">
			<div class="input_file_box">
				<input type="text" id="fakeFileTxt'.$nextIdx.'" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="파일 등록">
				<div class="fileDiv">
					<input type="button" class="buttonImg" value="파일찾기">
					<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById(\'fakeFileTxt'.$nextIdx.'\').value = this.value">
				</div>
			</div>
			<span class="add_btn_box"><a href="#none" onclick="return false;" class="file_btn exec-delfile">- 삭제</a></span>
		</div>
	';

	die(json_encode(array('rst'=>'success','idx'=>$idx,'nextIdx'=>$nextIdx,'html'=>$html)));
}
else if($_mode == 'get_pstock') {
    // 2019-07-24 SSJ :: 현재 상품 재고를 추출한다
    // -- 옵션타입에 상관없이 p_stock을 반환한다
    // -- 옵션을 사용하지 않는 상품만 호출
    $r = _MQ(" select p_stock as cnt from smart_product where p_code = '". $pcode ."' ");
    $stock = (string) ($r['cnt']*1);
    die($stock);
}

// 주소를 통한 좌표 추출 -> return json
else if($_mode == 'get_addr_position'){

	$mapx = $mapy = '';
	if( $address != '' && $siteInfo['kakao_api'] != ''){
		$_e = kakao_addr_search($address); 
		if( !empty($_e['documents'][0]['x']) && !empty($_e['documents'][0]['y'])){
			$mapx = $_e['documents'][0]['x'];
			$mapy = $_e['documents'][0]['y'];
		}				
	}	
	die(json_encode(array('rst'=>'success','mapx'=>$mapx, 'mapy'=>$mapy)));
}

else if($_mode == 'time_sale_view_modify'){
	
	if(!$pcode) { die(json_encode(array("rst" =>"error" , "msg" => "상품정보가 없습니다."))); }

	$time_sale_end = _MQ_result("SELECT COUNT(*) FROM smart_product where 1 and concat(p_time_sale_sdate,' ', p_time_sale_sclock) <= now() AND concat(p_time_sale_edate,' ', p_time_sale_eclock) <= now() AND p_code = '". $pcode ."' ");

	/*if( $time_sale_end == 1 ){ 
		_MQ_noreturn("UPDATE smart_product SET p_view = 'N' WHERE 1 and concat(p_time_sale_sdate,' ', p_time_sale_sclock) <= now() AND concat(p_time_sale_edate,' ', p_time_sale_eclock) <= now() AND p_code = '". $pcode ."' ");
	}*/
	die(json_encode(array("rst" =>"success" , "msg" => "OK")));
}

// LCY : 2021-12-29 : 신고하기 기능 추가 -- 신고하기 타입에 따라 처리 (get_report : 신고하기 폼 , set_report : 신고하기 처리)
else if($_mode == 'get_report'){

	$response = array('rst'=>'fail','msg'=>'신고하기에 실패하였습니다.');

	if( is_login() !== true){
		$response['msg'] = '로그인 또는 회원가입 후 이용가능한 서비스 입니다.';
		die(json_encode($response));			
	}

	// 신고하기 타입이 리뷰일경우 -- 현재 리뷰만 사용
	if($_type  == 'review'){	

		// 이미 신고 접수된 글인지 확인 
		$rep_chk = _MQ("select count(*) as cnt from smart_product_talk_report where ptr_inid ='".get_userid()."' and ptr_ptuid = '".$_uid."' ");
		if( $rep_chk['cnt'] > 0){ 
			$response['msg'] = '이미 신고가 접수되었습니다.';
			die(json_encode($response));			
		}

		// $talk_type
		//리뷰 데이터 조회 
		$talk_type = 'eval';
		$row = _MQ("select * from smart_product_talk as pt where pt_depth=1 and pt_type='".$arr_p_talk_type[$talk_type]."' and pt_uid = '".$_uid."' ");


		if( empty($row['pt_uid']) || $row['pt_uid'] < 1){
			$response['msg'] = '상품리뷰 조회에 실패하였습니다.';
			die(json_encode($response));			
		}

		if($row['pt_inid'] == get_userid()){
			$response['msg'] = '본인이 작성한 상품리뷰는 신고가 불가능합니다.';
			die(json_encode($response));						
		}

		// 해당 폴더 호출
		ob_start();
		include_once($SkinData['skin_root'].'/inc.footer.report.review.php'); // 스킨폴더에서 해당 파일 호출
		$response['content'] = ob_get_clean();
		$response['rst'] = 'success';
		die(json_encode($response));

	}else{
		$response['msg'] = '지원하지 않은 신고타입입니다.';
		die(json_encode($response));
	}
}
else if($_mode == 'set_report'){


	$response = array('rst'=>'fail','msg'=>'신고하기에 실패하였습니다.');

	if( is_login() !== true){
		$response['msg'] = '로그인 또는 회원가입 후 이용가능한 서비스 입니다.';
		die(json_encode($response));			
	}

	// 신고하기 타입이 리뷰일경우 -- 현재 리뷰만 사용
	if($_type  == 'review'){	

		// 이미 신고 접수된 글인지 확인 
		$rep_chk = _MQ("select count(*) as cnt from smart_product_talk_report where ptr_inid ='".get_userid()."' and ptr_ptuid = '".$_uid."' ");
		if( $rep_chk['cnt'] > 0){ 
			$response['msg'] = '이미 신고가 접수되었습니다.';
			die(json_encode($response));			
		}

		// $talk_type
		//리뷰 데이터 조회 
		$talk_type = 'eval';
		$row = _MQ("select * from smart_product_talk as pt where pt_depth=1 and pt_type='".$arr_p_talk_type[$talk_type]."' and pt_uid = '".$_uid."' ");


		if( empty($row['pt_uid']) || $row['pt_uid'] < 1){
			$response['msg'] = '상품리뷰 조회에 실패하였습니다.';
			die(json_encode($response));			
		}

		if($row['pt_inid'] == get_userid()){
			$response['msg'] = '본인이 작성한 상품리뷰는 신고가 불가능합니다.';
			die(json_encode($response));						
		}

		if( in_array($_reason, array(1,2,3,4)) < 1){
			$response['msg'] = '신고이유를 선택해 주시기 바랍니다.';
			die(json_encode($response));									
		}


		if( $_reason == 4 && $_reason_etc == ''){
			$response['msg'] = '자세한 신고이유를 입력해 주시기 바랍니다.';
			die(json_encode($response));									
		}

		$que = " ptr_inid = '".get_userid()."', ptr_ptuid = '".$_uid."', ptr_reason = '".$_reason."',  ptr_status_update = now(), ptr_rdate = now() ";
		if( $_reason == 4){  $que .= "  , ptr_reason_etc = '".$_reason_etc."' "; }


		// 최종 신고하기 처리 
		_MQ_noreturn("insert smart_product_talk_report set ".$que);

		$response['rst'] = 'success';
		$response['msg'] = "해당리뷰에 대한 신고가 접수되었습니다.\n새로고침 후에는 해당 리뷰는 더이상 노출되지 않습니다.";
		die(json_encode($response));

	}else{
		$response['msg'] = '지원하지 않은 신고타입입니다.';
		die(json_encode($response));
	}	

}

// {LCY} : 하이앱 -- 회원 차단하기 기능
else if($_mode == 'block_user'){	
	$row = _MQ("select * from smart_product_talk as pt where pt_depth=1 and pt_uid = '".$_uid."' ");
	if( count($row) < 1){
		die(json_encode(array('rst'=>'fail','msg'=>"본 리뷰 조회에 실패하였습니다.")));
	}

	$block_mid = $row['pt_inid'];
	if( $block_mid == get_userid()){
		die(json_encode(array('rst'=>'fail','msg'=>"본인을 차단할 수는 없습니다.")));
	}

	// 이미 차단한 회원인지 체크 
	$rowchk = _MQ("select count(*) as cnt from smart_individual_block where ib_inid = '".get_userid()."' and ib_type = '상품후기' and ib_block_inid = '".$block_mid."'  ");
	if( $rowchk['cnt']  > 0){
		die(json_encode(array('rst'=>'fail','msg'=>"이미 차단된 회원입니다.")));
	}
	
	// 차단 진행 
	$que = "
	insert smart_individual_block set 
		ib_inid = '".get_userid()."'
		, ib_block_inid = '".$block_mid."'
		, ib_type = '상품후기'
		, ib_rdate = now()
	";
	_MQ_noreturn($que);
	die(json_encode(array('rst'=>'success')));
}