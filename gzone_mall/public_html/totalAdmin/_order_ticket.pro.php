<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');

if(in_array($_mode, array('get_excel', 'get_search_excel'))) { // Excel 다운로드 Start
	$toDay = date('YmdHis', time());
	$fileName = '_order_ticket_list';
	if(!$test) {

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	}

	# 모드별 쿼리 조건
	if($_mode == 'get_excel') $s_query = " and o.o_canceled != 'Y' and o.o_paystatus = 'Y' and npay_order = 'N' and op.op_cancel = 'N' and op.op_uid in ('".implode("', '", $_uid)."') ";
	else $s_query = enc('d', $_search_que);
	if(!$st) $st = 'o_rdate';
	if(!$so) $so = 'desc';

	// LCY : 2021-03-22 : 상품 티켓유형 추가
	$s_query .= " and op_ptype = 'ticket' ";

	$res = _MQ_assoc("
		select
			op.*, o.*, p.p_name, p.p_img_list
		from
			smart_order_product as op inner join
			smart_order as o on (o.o_ordernum=op.op_oordernum) left join
			smart_product as p on (p.p_code=op.op_pcode)
		where (1)
			{$s_query}
		order by {$st} {$so}
	");

	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';
?>
	<table>
		<thead>
			<th<?php echo $THStyle; ?>>고유번호</th>
			<th<?php echo $THStyle; ?>>주문번호</th>
			<th<?php echo $THStyle; ?>>주문일시</th>
			<th<?php echo $THStyle; ?>>주문자 이름</th>
			<th<?php echo $THStyle; ?>>주문자 휴대폰</th>
			<th<?php echo $THStyle; ?>>사용자 이름</th>
			<th<?php echo $THStyle; ?>>사용자 휴대폰</th>
			<th<?php echo $THStyle; ?>>상품코드</th>
			<th<?php echo $THStyle; ?>>대표상품명</th>
			<th<?php echo $THStyle; ?>>선택날짜</th>
			<th<?php echo $THStyle; ?>>옵션1</th>
			<th<?php echo $THStyle; ?>>옵션2</th>
			<th<?php echo $THStyle; ?>>옵션3</th>
			<th<?php echo $THStyle; ?>>판매단가</th>
			<th<?php echo $THStyle; ?>>수량</th>
			<th<?php echo $THStyle; ?>>금액</th>
			<th<?php echo $THStyle; ?>>티켓정보</th>
			<th<?php echo $THStyle; ?>>관리자메모</th>
		</thead>
		<tbody>
			<?php 

				foreach($res as $k=>$v) {
					$ticketInfo = _MQ_assoc("select *from smart_order_product_ticket where opt_opuid ='".$v['op_uid']."'  ");
					$ticketInfoView = array();
					if( count($ticketInfo) > 0){
						foreach($ticketInfo as $sk=>$sv){
							$ticketInfoView[] = $sv['opt_ticketnum'].'('.$arr_ticket_status[$sv['opt_status']].')';
						}
					}
			?>
				<tr>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_uid']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_oordernum']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo date('Y-m-d (H:i:s)', strtotime($v['o_rdate'])); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_ohp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_uname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_uhp']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_pcode']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_pname']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_dateoption_use'] == 'Y' ? $v['op_dateoption_date'] : ''; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option1']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option2']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_option3']; ?></td>
					<td<?php echo $TDStyle2; ?>><?php echo $v['op_price']; ?></td>
					<td<?php echo $TDStyle2; ?>><?php echo $v['op_cnt']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['op_price']*$v['op_cnt']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo implode("<br>",$ticketInfoView); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_admcontent']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
}

else if($_mode == 'ticket_use')
{

	if( $type == "use" ) {

		// 티켓정보 체크 
		$ticketInfo = _MQ("select *from smart_order_product_ticket where opt_uid ='".$uid."'  ");
		if( count($ticketInfo) < 1){ error_alt("사용할 수 없는 티켓입니다.");}
		if( $ticketInfo['opt_status'] != '대기'){ error_alt("이미 사용된 티켓입니다.");  }


		update_order_ticket_status($ticketInfo['opt_uid'],'사용');


		// 문자 발송유형
		$ss_uid = "ticket_use";
		$result = true;
	}
	else if( $type == "unuse" ) {

		// 티켓정보 체크 
		$ticketInfo = _MQ("select *from smart_order_product_ticket where opt_uid ='".$uid."'   ");
		if( count($ticketInfo) < 1){ error_alt("사용할 수 없는 티켓입니다.");}
		if( $ticketInfo['opt_status'] != '사용'){ error_alt("아직 미사용된 티켓입니다.");  }


		update_order_ticket_status($ticketInfo['opt_uid'],'대기');	

		// 문자 발송유형
		$ss_uid = "ticket_unuse";
		$result = true;
	}

	// 티켓사용시 문자알림
	if($result) {

		$r = _MQ("select o_ohp, o_uhp from smart_order where o_ordernum = '".$ticketInfo['opt_oordernum']."' ");
		$sms_to = $r['o_uhp'] ? $r['o_uhp'] : $r['o_ohp'];
		shop_send_sms($sms_to,'ticket_'.$type,$ticketInfo['opt_uid']);

	}
	error_frame_reload("티켓이 ".($type == 'use' ? '사용':'미사용')." 처리 되었습니다.") ; // 부모창 reload
}

else if($_mode == 'settlementstatus_ready') { // 정산대기 처리
	if(count($_uid) <= 0) error_alt('처리할 주문을 1건 이상 선택 바랍니다.');

	// 발급완료가 아닌 상품을 걸러냄
	$chk = _MQ_assoc(" select op_uid from smart_order_product where op_uid in ('".implode("' , '", array_values($_uid))."') and op_sendstatus != '발급완료' ");
	$error_msg = '';
	if(count($chk) > 0) {
		$_uid_array_tmp = array_flip($_uid);
		$error_msg = "발급완료상태가 아닌 주문상품을 제외하고,\\n";
		foreach($chk as $k=>$v) {
			if($_uid_array_tmp[$v['op_uid']]) unset($_uid_array_tmp[$v['op_uid']]);
		}
		$_uid = array_flip($_uid_array_tmp);
	}

	// 정산대기 전환
	_MQ_noreturn(" update smart_order_product set op_settlementstatus='ready', op_settlement_reday = now() where op_uid in ('".implode("' , '", array_values($_uid))."') and op_settlementstatus='none' ");
	order_settlement_status_opuid(array_values($_uid));
	error_frame_reload($error_msg."선택한 주문상품이 정산대기로 변경되었습니다.");
}
else if($_mode == 'settlementstatus_complete') { // 정산완료 처리

	// --------------------------------------------- 2017-06-26 ::: 부가세율설정 ::: JJC ---------------------------------------------
	// 입점업체정보 배열 추출
	$partner = array();
	$cp_row = _MQ_assoc("
		select
			op.op_partnerCode,
			cp.cp_vat_delivery
		from
			smart_company as cp left join
			smart_order_product as op on (op.op_partnerCode = cp.cp_id)
		where
			op.op_uid in ('".implode("' , '", array_values($OpUid))."')
	");
	foreach($cp_row as $sk=>$sv) {
		$partner[$sv['op_partnerCode']] = $sv['cp_vat_delivery'];
	}
	// 입점업체정보 배열 추출


	// 주문정보 호출
	$pr = _MQ_assoc("
		select
			op.*, p.* , o.* ,
			IF(
				op.op_comSaleType = '공급가',
				(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price),
				(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission / 100 + op.op_delivery_price + op.op_add_delivery_price)
			) as comPrice
		from
			smart_order_product as op left join
			smart_product as p on (p.p_code=op.op_pcode) left join
			smart_order as o on (op.op_oordernum = o.o_ordernum )
		where (1) and
			op.op_uid in ('".implode("', '", $OpUid)."')
	");
	// 2017-06-22 ::: 부가세율설정 ::: JJC
	$data2 = array();$data_uid = array();
	foreach($pr as $sk=>$sv) {

		// 과세
		if($sv['op_vat'] == 'Y') {
			$data2[$sv['op_partnerCode']]['count'] += $sv['op_cnt'];
			$data2[$sv['op_partnerCode']]['price'] += $sv['op_price'] * $sv['op_cnt'];
			$data2[$sv['op_partnerCode']]['com_price'] += $sv['comPrice'];
			$data2[$sv['op_partnerCode']]['usepoint'] += $sv['op_usepoint'];
			$data2[$sv['op_partnerCode']]['discount'] += $sv['op_price'] * $sv['op_cnt'] - $sv['comPrice'] - $sv['op_usepoint'];
		}
		// 면세
		else if($sv['op_vat'] == 'N') {
			$data2[$sv['op_partnerCode']]['count_vatN'] += $sv['op_cnt'];
			$data2[$sv['op_partnerCode']]['price_vatN'] += $sv['op_price'] * $sv['op_cnt'];
			$data2[$sv['op_partnerCode']]['com_price_vatN'] += $sv['comPrice'];
			$data2[$sv['op_partnerCode']]['usepoint_vatN'] += $sv['op_usepoint'];
			$data2[$sv['op_partnerCode']]['discount_vatN'] += $sv['op_price'] * $sv['op_cnt'] - $sv['comPrice'] - $sv['op_usepoint'];
		}

		// 배송비 과세
		if($partner[$sv['op_partnerCode']] == 'Y') {
			$data2[$sv['op_partnerCode']]['delivery_price'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
			$data2[$sv['op_partnerCode']]['discount'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}
		// 배송비 면세
		else if($partner[$sv['op_partnerCode']] == 'N') {
			$data2[$sv['op_partnerCode']]['delivery_price_vatN'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
			$data2[$sv['op_partnerCode']]['discount_vatN'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}

		$data_uid[$sv['op_partnerCode']][] = $sv['op_uid'];
	}
	// 2017-06-22 ::: 부가세율설정 ::: JJC

	// --------------------------------------------- 2017-06-26 ::: 부가세율설정 ::: JJC ---------------------------------------------


	// smart_order_settle_complete(정산완료테이블) 기록 및 odtTableText에 주문상품 고유값저장
	foreach($data2 as $k=>$v) {
		$que = "
			insert into smart_order_settle_complete set
				s_partnerCode = '{$k}',
				s_price = '{$v['price']}',
				s_delivery_price = '{$v['delivery_price']}',
				s_com_price = '{$v['com_price']}',
				s_usepoint = '{$v['usepoint']}',
				s_discount = '{$v['discount']}',
				s_count = '{$v['count']}',
				s_price_vat_n = '{$v['price_vatN']}',
				s_delivery_price_vat_n = '{$v['delivery_price_vatN']}',
				s_com_price_vat_n = '{$v['com_price_vatN']}',
				s_usepoint_vat_n = '{$v['usepoint_vatN']}',
				s_discount_vat_n = '{$v['discount_vatN']}',
				s_count_vat_n = '{$v['count_vatN']}',
				s_date = now()
		";
		_MQ_noreturn($que);
		$serialnum = _MQ_insert_id();
		_text_info_insert('smart_order_settle_complete', $serialnum, 's_opuid', implode(',', array_values($data_uid[$k])), 'ignore');
	}

	if(count($OpUid) > 0) {
		_MQ_noreturn(" update smart_order_product set op_settlementstatus='complete', op_settlement_complete = now() where op_uid in ('".implode("', '", array_values($OpUid))."') and op_settlementstatus='ready' ");
		order_settlement_status_opuid(array_values($OpUid)); // 2015-08-19 추가 - 정준철
	}
	error_frame_reload_nomsg() ; // 부모창 reload
}