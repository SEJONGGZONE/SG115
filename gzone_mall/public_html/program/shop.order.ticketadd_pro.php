<?php
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// *** 결제확인 시 --> 포인트 / 쿠폰 등 적용 ***
// - 주문정보 추출 ---
$osr = get_order_info($_ordernum);

// - 주문상품 정보 추출 ---
$op_assoc = get_order_product_info($_ordernum);

$ticket_send_check = 0;

// 주문 상품 유형이 티켓 또는 둘다 일경우만 실행
if( $osr['o_order_type'] == "ticket" || $osr['o_order_type'] == "both"){
	foreach($op_assoc as $op_key => $op_row) {

		if($op_row['op_is_addoption'] == 'Y') { continue; } // 추가옵션일 경우 패스
		if($op_row['op_ptype'] != "ticket"){ continue; }   // 티켓이 아닐경우 패스

		// 발급된 티켓이 있을 경우 해당 티켓 개수만큼 제외하고 처리
		$createTicketCount = $op_row['op_cnt'];
		$chkTicket = _MQ("select count(*) as cnt from smart_order_product_ticket where opt_oordernum = '".$_ordernum."' and opt_opuid = '".$op_row['op_uid']."' ");
		if( $chkTicket['cnt'] > 0){ $createTicketCount -= $chkTicket['cnt']; } 


		// 티켓 유효기간 추출
		$pinfo = _MQ("select p_expire_type, p_expire_day , p_expire_date from smart_product where p_code = '".$op_row['op_pcode']."'");
		$_expire_type = $pinfo['p_expire_type'];

		if($op_row['op_dateoption_use'] == 'Y'){ // 달력옵션 사용시에는 지정된 달력 날짜가 적용된다.
			$_expire_type = 'date'; 
			$_expire_date =  $op_row['op_dateoption_date'];
		}
		else{
			if( $_expire_type == 'day'){ // 만료일 지정
				$_expire_date = date('Y-m-d',strtotime("+ ".$pinfo['p_expire_day']."days"));
			}
			// 만료날짜 지정
			else if($_expire_type == 'date'){
				$_expire_date = $pinfo['p_expire_date'] == '0000-00-00' ? date('Y-m-d') : $pinfo['p_expire_date'];
			}
		}



		for($i=0;$i<$op_row['op_cnt'];$i++) {	// 갯수만큼 티켓 생성.

			$_ticketnum = shop_ticketnum_create();				

			$ts_query = "
				opt_oordernum = '".$op_row['op_oordernum']."'
				, opt_pcode = '".$op_row['op_pcode']."'
				, opt_opuid = '".$op_row['op_uid']."'
				, opt_ticketnum = '".$_ticketnum."'
				, opt_rdatetime = now()
				, opt_expire_type = '".$_expire_type."'
				, opt_status = '대기'
			";

			if($_expire_type != ''){
				$ts_query .= " , opt_expire_date = '".$_expire_date."' ";
			}

			_MQ_noreturn("insert smart_order_product_ticket set ".$ts_query);
		}
		_MQ_noreturn("update smart_order_product set op_sendstatus = '발급완료' , op_senddate = '".date('Y-m-d')."'  where op_uid = '".$op_row['op_uid']."'");
		$ticket_send_check++;
	}


	// 주문자체가 ticket 이라면 전체상택값을 발급완료로 상태값을 변경, 별도 상택값을 위해 o_ticketstatus 추가(참고용)
	if( $osr['o_order_type'] == 'ticket'){
		_MQ_noreturn("update smart_order set o_status = '발급완료', o_sendstatus = '발급완료' , o_senddate = '".date('Y-m-d')."' ,o_completedate = '".date('Y-m-d')."' where o_ordernum = '".$_ordernum."' ");
	}

	if( $ticket_send_check > 0){ 
		// 문자 발송 -> 티켓은 기본 사용자이며, 만약 없을 경우 주문자에게..
		$sms_to = $osr['o_uhp'] ? $osr['o_uhp'] : $osr['o_ohp'];
		shop_send_sms($sms_to,"ticket",$_ordernum);
	}

}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행