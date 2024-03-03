<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// LCY : 2023-01-04 : 성공/실패에 따른 URL 정의 
$response_location_url = array(
	'success'=>'/?pn=shop.order.complete',
	'fail'=>'/?pn=shop.order.form&errcd=fail',
);
	
$ool_bank_name_array = array(
	'39'=>'경남',
	'34'=>'광주',
	'04'=>'국민',
	'03'=>'기업',
	'11'=>'농협',
	'31'=>'대구',
	'32'=>'부산',
	'02'=>'산업',
	'45'=>'새마을금고',
	'07'=>'수협',
	'88'=>'신한',
	'26'=>'신한',
	'48'=>'신협',
	'05'=>'외환',
	'20'=>'우리',
	'71'=>'우체국',
	'37'=>'전북',
	'35'=>'제주',
	'81'=>'하나',
	'27'=>'한국씨티',
	'53'=>'씨티',
	'23'=>'SC은행',
	'09'=>'동양증권',
	'78'=>'신한금융투자증권',
	'40'=>'삼성증권',
	'30'=>'미래에셋증권',
	'43'=>'한국투자증권',
	'69'=>'한화증권'
);


/*
	$P_STATUS : 결과코드 ("00":성공, 이외 실패)
	$P_RMESG1 : 결과메시지
	$P_TID : 인증거래번호(성공시에만 전달	)
	$P_AMT : 거래금액
	$P_REQ_URL : 승인요청 URL (해당 URL로 HTTPS API Request 승인요청 - POST)
	$P_NOTI : 가맹점 임의 데이터
*/
foreach($_REQUEST as $k=>$v){
	$_REQUEST[$k] = iconv('euckr','utf8',$_REQUEST[$k]);
}
$P_STATUS    = $_REQUEST["P_STATUS"];
$P_RMESG1    = $_REQUEST["P_RMESG1"];
$P_TID       = $_REQUEST["P_TID"];
$P_REQ_URL   = $_REQUEST["P_REQ_URL"];
$P_NOTI      = $_REQUEST["P_NOTI"];
$P_AMT       = $_REQUEST["P_AMT"];


if ($_REQUEST["P_STATUS"] === "00") {             // 인증이 P_STATUS===00 일 경우만 승인 요청

	$id_merchant = substr($P_TID,'10','10');     // P_TID 내 MID 구분
	$data = array(
	
	 'P_MID' => $id_merchant,         // P_MID
	 'P_TID' => $P_TID                // P_TID
	);

	// curl 통신 시작 ---> 실제 승인요청
	$ch = curl_init();                                                //curl 초기화
	curl_setopt($ch, CURLOPT_URL, $_REQUEST["P_REQ_URL"]);            //URL 지정하기
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                   //요청 결과를 문자열로 반환 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);                     //connection timeout 10초 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                      //원격 서버의 인증서가 유효한지 검사 안함
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));    //POST 로 $data 를 보냄
	curl_setopt($ch, CURLOPT_POST, 1);                                //true시 post 전송 


	$response = curl_exec($ch);
	curl_close($ch);

	$response = iconv('euckr','utf8',$response);
	
	// 결과URL파싱 
	parse_str($response, $result);



	// 주문번호 보완처리
	$ordernum = $result['P_OID'] != '' ? $result['P_OID'] : $ordernum;

	if($result['P_STATUS'] != '00'){
		error_loc_msg($response_location_url['fail'] , $result['P_RMESG1']." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.(주문서를 다시 작성해 주세요)");  
	}
	
	// 주문조회
	$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

	// 이미결제가 된건인지 체크 
	if( $r['o_paystatus'] == 'Y'){  error_loc($response_location_url['success']); }

	// 결제금액이 정상인지 체크
	if($result['P_AMT'] != $r[o_price_real]) {
		error_loc_msg($response_location_url['fail'] , "결제금액이 다릅니다. 정상결제금액 : ".$r[o_price_real].", 요청된결제금액 : ".$result[P_AMT].'(주문서를 다시 작성해 주세요)');
	}


	// 로그기록 ->  결제수단 산관없이 저장한다.
	$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
	foreach($result as $key=>$value) {
		$app_oc_content .= $key . "||" . $value. "§§" ; // 데이터 저장
	}
	$que = "
		insert smart_order_cardlog set
			 oc_oordernum = '". $ordernum ."'
			,oc_tid = '".$result['P_TID']."'
			,oc_content = '{$app_oc_content}§§subTy||".$subTy."'
			,oc_rdate = now();
	";
	_MQ_noreturn($que);

	switch($result['P_TYPE']){

		// 카드
		case "CARD":

			// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
			include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

			// 결제완료페이지 이동
			error_loc($response_location_url['success']);

		break;

		// 실시간계좌이체
		case "BANK":

			// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
			include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

			// 결제완료페이지 이동
			error_loc($response_location_url['success']);			

		break;

		// 휴대폰
		case "MOBILE":
			// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
			include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

			// 결제완료페이지 이동
			error_loc($response_location_url['success']);
		break;		


		// 가상계좌(무통장입금)	
		case "VBANK":


			$ool_type = 'R'; // 발급
			$tno = $result['P_TID'];
			$app_time = $result['P_AUTH_DT'];
			$amount = $result['P_AMT'];
			$account = $result['P_VACT_NUM'];

			// LCY : 2022-11-25 : 가상계좌 구매자명적용
			$depositor = $result['P_UNAME'] != '' ? $result['P_UNAME'] : $r['o_oname'];
			$bankcode = $result['P_FN_CD1'] != '' ? $result['P_FN_CD1']: $result['P_VACT_BANK_CODE'];
			$bankname = $result['P_FN_NM'];
			$bank_owner = $result['P_VACT_NAME'];

			$buyr_tel2 = $result['P_HPP_NUM'] != '' ? $result['P_HPP_NUM'] : $r['o_ohp'];

			_MQ_noreturn("
				insert into smart_order_onlinelog (
				ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
				) values (
				'$ordernum', '$indr[in_id]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '".$depositor."', '".$bankname."', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
				)
			");

			// 가상계좌 결제 이메일 및 SMS 발송
			include OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";

			// 결제완료페이지 이동
			error_loc($response_location_url['success']);

		break;


	}

}

// 인증실패
else{

	// 2017-01-09 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
	$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid != '' and (oc_content like '%resultCode||0000§§%' or oc_content like '%m_resultCode||00§§%') ");
	if($oc_res_cnt['cnt'] == 1 ) {
		// 결제완료페이지 이동
		error_loc($response_location_url['success']);
	}

	// 결제실패 처리
	else {

		//해당 정보가 없으면 주문 취소
		$que = "
			insert smart_order_cardlog set
				 oc_oordernum = '". $ordernum ."'
				,oc_tid = '".$P_TID."'
				,oc_content = '".$P_RMESG1."'
				,oc_rdate = now();
		";
		_MQ_noreturn($que);

		_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
		error_loc_msg($response_location_url['fail'] , $P_RMESG1." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.(주문서를 다시 작성해 주세요)");
	}
	// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
	
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 
?>