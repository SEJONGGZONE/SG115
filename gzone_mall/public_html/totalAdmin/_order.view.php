<?php
	if(!isset($_REQUEST['view'])) $_REQUEST['view'] = '';
	if($_REQUEST['view'] == 'online') {
		$app_current_link = '_order.list.php?view=online';// 메뉴지정
		$app_current_link_list = '_order.list.php';// 목록페이지 지정
	}else if($_REQUEST['view'] == 'cancel') {
		$app_current_link = '_order.cancel_list.php';// 메뉴지정
		$app_current_link_list = '_order.cancel_list.php';// 목록페이지 지정
	}else if($_REQUEST['view'] == 'order_delivery') {
		$app_current_link = '_order_delivery.list.php';// 메뉴지정
		$app_current_link_list = '_order_delivery.list.php';// 목록페이지 지정
	}else if($_REQUEST['view'] == 'order_product') {
		$app_current_link = '_order_product.list.php';// 메뉴지정
		$app_current_link_list = '_order_product.list.php';// 목록페이지 지정
	}
	else if($_REQUEST['view'] == 'order_ticket') {
		$app_current_link = '_order_ticket.list.php';// 메뉴지정
		$app_current_link_list = '_order_ticket.list.php';// 목록페이지 지정
	}
	else if($_REQUEST['view'] == 'cancel_order') { // SSJ : 주문/결제 통합 패치 : 2021-02-24
		$app_current_link = '_cancel_order.list.php';// 메뉴지정
		$app_current_link_list = '_cancel_order.list.php';// 목록페이지 지정
	}
	else if($_REQUEST['view'] == 'order4_view') {
	$app_current_link = '_order4.list.php';// 메뉴지정
	$app_current_link_list = '_order4.list.php';// 목록페이지 지정
}
	else if($_REQUEST['view'] == 'order_complain') { // 교환/반품
		$app_current_link = '_order_complain.list.php';// 메뉴지정
		$app_current_link_list = '_order_complain.list.php';// 목록페이지 지정
	}else{
		$app_current_link = '_order.list.php';// 메뉴지정
		$app_current_link_list = '_order.list.php'; // 목록페이지 지정
	}


	include_once('wrap.header.php');


	if( $_mode == "modify" ) {
		// 주문정보 추출
		$que = " select * from smart_order where o_ordernum='{$_ordernum}' ";
		$row = _MQ($que);

		$_member = _MQ(" select * from smart_individual where in_id = '".$row['o_mid']."' ");

		# 모바일 구매
		if($row['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';
		else $device_icon = '<span class="c_tag h18 t3 pc">PC</span>';

		// 주문상품 정보 추출
		$arr_product = array();
		// JJC : 2023-01-13 : 추가옵션 부분취소 - 위치조정 : 지정된 기본옵션 하위에 추가옵션 위치함
		$sres = _MQ_assoc("
			select op.* , p.p_name, p.p_img_list_square, p.p_code, IF(op_is_addoption = 'Y' , op_addoption_parent , op_pouid) AS order_uid
			from smart_order_product as op
			left join smart_product as p on (p.p_code=op.op_pcode)
			where op_oordernum='{$_ordernum}'
			".($AdminPath == 'totalAdmin' ? null : " and op.op_partnerCode = '{$com_id}' ")."
			order by order_uid ASC , op.op_uid ASC
		");
		# 주문상품 추출
		$arr_pinfo = array(); // 주문상품, 옵션 정보
		$arr_status = array(); // 주문상품 진행상태 체크
		foreach($sres as $sk=>$sv){
			// 상품코드
			$arr_pinfo[$sv['op_pcode']]['code'] = $sv['op_pcode'];
			// 상품명
			$arr_pinfo[$sv['op_pcode']]['name'] = stripslashes($sv['op_pname']);
			// 이미지 체크
			$_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
			if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
			$arr_pinfo[$sv['op_pcode']]['img'] = $_p_img;

			// 배송/티켓 아이콘 표기
			$arr_pinfo[$sv['op_pcode']]['ptype_icon'] = $arr_adm_button[$sv['op_ptype']];
			$arr_pinfo[$sv['op_pcode']]['p_type'] = $sv['op_ptype'];


			// 부분취소 상태 체크 -- 결제전에는 상태없음
			$app_cancel_btn = '';

			$app_delivery_search = $app_ticket_search = '';
			if($sv['op_ptype'] == 'delivery'){
				# 배송조회
				if(in_array($sv['op_sendstatus'], array('배송중','배송완료')) && $sv['op_sendcompany'] && $sv['op_sendnum']){
					$app_delivery_search = '

							<a href="'. ($row['npay_order'] == 'Y' ? ($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]) : $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="c_btn h22 green line h22" target="_blank">배송조회</a>

					';

				}
			}

			//티켓조회
			else if($sv['op_ptype'] == 'ticket' && $row['o_canceled'] != 'Y'){
				$ticket_cnt = _MQ_result("select count(*) as cnt from smart_order_product_ticket where opt_opuid = '".$sv['op_uid']."'  ");
				if( $ticket_cnt > 0){
					$app_ticket_search = '

							<a href="_order_ticket.list.php?pass_input_type=pass_ordernum&pass_input_value='.$_ordernum.'" target="_blank" class="c_btn h22 blue line h22" >티켓조회</a>

					';
				}
			}





			// 2017-06-20 ::: 부가세율설정 ::: JJC
			$app_vat_str = ( ($siteInfo['s_vat_product'] == 'C' && $sv['op_vat'] == 'N') ? ' <span class="t_blue">(면세)</span>' : '');
			// 2017-06-20 ::: 부가세율설정 ::: JJC

			if($sv['op_pouid']){ // 옵션있음
				$arr_pinfo[$sv['op_pcode']]['has_option'] = 'Y';

				// KAY :: 2022-12-07 :: 옵션 구분 수정
				$arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );
				$arr_option_name = array_filter($arr_option_name);

				// 달력옵션일 경우 예약일 표시
				$print_dateoption_date = '';
				if($sv['op_dateoption_use'] == 'Y' && rm_str($sv['op_dateoption_date']) > 0  ){
					$print_dateoption_day = date('w',strtotime($v['op_dateoption_date']));
					$print_dateoption_date = "<span class='ticket_day'>".$sv['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")</span>";
				}

				$arr_pinfo[$sv['op_pcode']]['option'][$sk] = array(
																			'op_uid'=>$sv['op_uid']
																			,'name'=>$print_dateoption_date.' '.implode(" / ",$arr_option_name)
																			,'price'=>$sv['op_price']
																			,'cnt'=>$sv['op_cnt']
																			,'is_addoption'=>$sv['op_is_addoption']
																			,'app_cancel_btn'=>$app_cancel_btn
																			,'app_ticket_search'=>$app_ticket_search
																			,'app_delivery_search'=>$app_delivery_search
																			,'app_vat_str'=>$app_vat_str
																			,'row'=>$sv // 별도처리 없이 변수만 이용하기 위한용도
																		);
			}else{ // 옵션없음
				$arr_pinfo[$sv['op_pcode']]['op_uid'] = $sv['op_uid'];
				$arr_pinfo[$sv['op_pcode']]['has_option'] = 'N';
				$arr_pinfo[$sv['op_pcode']]['price'] = $sv['op_price'];
				$arr_pinfo[$sv['op_pcode']]['app_cancel_btn'] = $app_cancel_btn;
				$arr_pinfo[$sv['op_pcode']]['app_ticket_search'] = $app_ticket_search;
				$arr_pinfo[$sv['op_pcode']]['app_delivery_search'] = $app_delivery_search;
				$arr_pinfo[$sv['op_pcode']]['app_vat_str'] = $app_vat_str;
				$arr_pinfo[$sv['op_pcode']]['row'] = $sv; // 별도처리 없이 변수만 이용하기 위한용도
			}

			$arr_pinfo[$sv['op_pcode']]['cnt'] += $sv['op_cnt'];
			$arr_pinfo[$sv['op_pcode']]['tprice'] += ($sv['op_cnt'] * $sv['op_price']);
			$arr_pinfo[$sv['op_pcode']]['point'] += $sv['op_point'];
			$arr_pinfo[$sv['op_pcode']]['delivery_type'] = $sv['op_delivery_type'];
			$arr_pinfo[$sv['op_pcode']]['delivery_price'] += $sv['op_delivery_price'];
			$arr_pinfo[$sv['op_pcode']]['add_delivery_price'] += $sv['op_add_delivery_price'];


			// 주문상품의 진행상태
			$arr_status[$sv['op_pcode']]['total']++;
			if($row['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
				$arr_status[$sv['op_pcode']]['cancel']++;
			}else if($sv['o_canceled'] == 'R'){ // 환불요청
				$arr_status[$sv['op_pcode']]['refund']++;
			}else if($row['o_status'] == '결제실패'){ // 결제실패일경우
				$arr_status[$sv['op_pcode']]['fail']++;
			}else{
				if($row['o_paystatus'] =='Y'){ // 주문결제를 했다면,
					if($sv['op_sendstatus'] == '배송대기') {
						$arr_status[$sv['op_pcode']]['pay']++;
					}else if($sv['op_sendstatus'] == '배송준비'){
						$arr_status[$sv['op_pcode']]['del_ready']++;
					}else if($sv['op_sendstatus'] == '배송중'){
						$arr_status[$sv['op_pcode']]['delivery']++;
					}else if($sv['op_sendstatus'] == '배송완료'){
						$arr_status[$sv['op_pcode']]['complete']++;
					}else if($sv['op_sendstatus'] == '발급완료'){
						$arr_status[$sv['op_pcode']]['issued']++;
					}
					else{
						$arr_status[$sv['op_pcode']]['cancel']++;
					}
				}else{ // 주문결제를 하지 않았다면
					$arr_status[$sv['op_pcode']]['ready']++;
				}
			}


			//{{{혜택표기}}} -- 혜택에 대한 처리
		 	$arrBoonInfo =  array();
		 	// 혜택에 대한 처리 :: list1 -  회원 할인/추가적립
		 	if( $sv['op_groupset_use'] == 'Y' && $sv['op_groupset_price_per'] > 0 ){ $arrBoonInfo[] = '회원할인 '.odt_number_format($sv['op_groupset_price_per'],1).'%'; }
		 	if( $sv['op_groupset_use'] == 'Y' && $sv['op_groupset_point_per'] > 0 ){ $arrBoonInfo[] = '회원추가적립'.odt_number_format($sv['op_groupset_point_per'],1).'%'; }

		 	// 혜택에 대한 처리 :: list2 -  쿠폰적용여부
		 	$rowClChk = _MQ("select count(*) as cnt from smart_order_coupon_log where cl_oordernum = '".$sv['op_oordernum']."' and cl_pcode = '".$sv['op_pcode']."'   ");
		 	if( $rowClChk['cnt'] > 0){ $arrBoonInfo[] = '상품쿠폰사용 '.number_format($rowClChk['cnt']).'개';  }


		 	$arr_pinfo[$sv['op_pcode']]['boonInfo'] = count($arrBoonInfo) > 0 ? implode("<br>",$arrBoonInfo) : '<span class="t_none">할인/혜택없음</span>';
		 	//{{{혜택표기}}}

		} //

		// 주문상품 진행상태 체크
		foreach($arr_status as $sk=>$sv){
			# 진행상태
			$op_status_icon = '';
			if($row['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패]
				$arr_pinfo[$sk]['status'] = '주문취소';
			}
			else if($sv['fail']>0){ // 결제실패가 하나라도 있으면 결제실패상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
				$arr_pinfo[$sk]['status'] = '결제실패';
			}
			else if($sv['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
				$arr_pinfo[$sk]['status'] = '결제대기';
			}
			else if($sv['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
				$arr_pinfo[$sk]['status'] = '배송중';
			}
			else if($sv['del_ready']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
				$arr_pinfo[$sk]['status'] = '배송준비';
			}
			else if($sv['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
				//$arr_pinfo[$sk]['status'] = '결제완료';
				$arr_pinfo[$sk]['status'] = '배송대기'; //=> 상세페이지에서는 배송대기로 표현
			}
			else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
				$arr_pinfo[$sk]['status'] = '배송완료';
			}
			else if($sv['issued']>0){ // 발급완료가 하나라도 있으면 발급완료
				$arr_pinfo[$sk]['status'] = '발급완료';
			}
			else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
				$arr_pinfo[$sk]['status'] = '주문취소';
			}
		}

		# 상품쿠폰정보 추춘
		$pcoupon = _MQ_assoc(" select * from smart_order_coupon_log where cl_oordernum = '". $row['o_ordernum'] ."' and cl_type = 'product' ");
		if(count($pcoupon)>0){
			foreach($pcoupon as $k=>$v){
				$arr_pinfo[$v['cl_pcode']]['product_coupon'] = $v['cl_title'].'<br>'.number_format($v['cl_price']).'원';
			}
		}

	}else{ error_msg('잘못된 접근입니다.'); }

?>


<form name="frm" method="post" action="_order.pro.php" >
	<input type="hidden" name="_ordernum" value='<?php echo $_ordernum; ?>'>
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="view" value="<?php echo $view; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_paymethod" value="<?php echo $row['o_paymethod']; ?>">


	<div class="data_list">
		<table class="table_list type_nocheck">
			<colgroup>
				<col width="60">
				<col width="150">
				<col width="*">
				<col width="120"><col width="120">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">No</th>
					<th scope="col">진행상태/상품타입</th>
					<th scope="col">주문상품</th>
					<th scope="col">주문금액</th>
					<th scope="col">배송비</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$_num = 0;
				foreach( $arr_pinfo as $k=>$v ){

						// 상품관리 링크
						$userLink = '/?pn=product.view&code='.$v['code']; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$v['code'];
						if( $v['p_type'] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$v['code'];
						}

						// 넘버링
						$_num++;
				?>
					<tr>
						<td class="this_num">
							<?php echo number_format($_num); ?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<?php echo $arr_adm_button[$v['status']]; ?>
								<?php echo $v['ptype_icon']; // 상품타입에 따른 아이콘 ?>
							</div>
						</td>
						<td class="this_item">
							<div class="order_item_thumb type_simple">
								<div class="thumb"><a href="<?php echo $adminLink; ?>" target="_blank"><img src="<?php echo $v['img']; ?>" alt="<?php echo addslashes($v['name']); ?>"></a></div>
								<div class="order_item">
									<dl>
										<dt>
											<div class="item_name">
												<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo stripslashes($v['name']); ?></a>
											</div>
											<!-- <span class="mount"><?php echo number_format($v['cnt']); ?>개</span> -->
											<?php if($v['has_option']=='Y' && count($v['option'])>0){ ?>
											<?php foreach($v['option'] as $sk=>$sv){ ?>
												<dd>
													<span class="option_name"><?php echo ($sv['is_addoption']=='Y'?'<span class="add_option">추가</span>':'<span class="option">필수</span>'); ?><?php echo $sv['name']; ?></span>
													<span class="mount"><?php echo number_format($sv['price']); ?>원 x <?php echo number_format($sv['cnt']); ?>개</span>

													<?php if( trim($sv['app_cancel_btn'].$sv['app_delivery_search'].$sv['app_ticket_search']) != ''){?>
													<div class="option_btn">
														<?php echo $sv['app_cancel_btn']; ?>
														<?php echo $sv['app_delivery_search']; ?>
														<?php echo $sv['app_ticket_search']; ?>
													</div>
													<?php } ?>
												</dd>
											<?php } ?>
											<?php }else{ ?>
												<span class="mount"><?php echo number_format($v['price']); ?>원 x <?php echo number_format($v['cnt']); ?>개</span>
												<div class="option_btn">
													<?php echo $v['app_cancel_btn']; ?>
													<?php echo $v['app_delivery_search']; ?>
													<?php echo $v['app_ticket_search']; ?>
												</div>
											<?php } ?>
										</dt>
									</dl>
								</div><!-- end order_item -->
							</div><!-- end order_item_thumb -->
						</td>
						<td class="t_right t_blue t_bold this_state">
							<span class="hidden_tx">주문금액</span><?php echo number_format($v['tprice']); ?>원</div>
						</td>
						<td class="t_right t_green">
							<span class="hidden_tx">배송비</span><?php echo number_format($v['delivery_price']); ?>원
							<?php if($v['delivery_type']<>'입점'){ ?>
								<div><?php echo $v['delivery_type']; ?>배송</div>
							<?php } ?>
							<?php if($v['add_delivery_price']>0){ ?>
								<div>+<?php echo number_format($v['add_delivery_price']); ?>원</div><div>추가배송비</div>
							<?php } ?>
						</td>
					</tr>

				<?php
						//상품수 , 포인트 , 상품금액
						$arr_product['cnt'] += $v['cnt'];//상품수
						$arr_product['point'] += $v['point'];//포인트
						$arr_product['sum'] += $v['tprice'];//상품금액
						$arr_product['add_delivery'] += $v['delivery_price'] + $v['add_delivery_price'];//개별배송비 포함
					}
				?>
			</tbody>
		</table>

		<div class="total_price">
			<div class="inner_box">
				<dl>
					<dt>
						<div class="">주문서 <strong class="t_blue">(<?php echo $row['o_ordernum']; ?>)</strong></div>
						<?php
							// LCY : 2021-07-04 : 신용카드 간편결제 추가
							if( $row['o_easypay_paymethod_type'] != ''){
								echo $arr_adm_button["E".$arr_available_easypay_pg_list[$row['o_easypay_paymethod_type']]];
							}else{
								echo $arr_adm_button[$arr_payment_type[$row['o_paymethod']]];
							}
						?>
					</dt>
					<dd>
						<ul>
							<li>
								<em>상품개수</em>
								<strong  class="t_black"><?php echo number_format($arr_product['cnt']); ?>개</strong>
							</li>
							<li>
								<em>총 상품금액</em>
								<strong  class="t_blue"><?php echo number_format($arr_product['sum']); ?>원</strong>
							</li>
							<li>
								<em>총 배송비</em>
								<strong class="t_green">+ <?php echo number_format($arr_product['add_delivery']); ?>원</strong>
							</li>
							<li>
								<em>합계금액</em>
								<strong  class="t_black"><?=number_format($arr_product['sum'] + $arr_product['add_delivery']); ?>원</strong>
							</li>
						</ul>
					</dd>
				</dl>
			</div>
			<div class="calculate_date">주문일시 : <?php echo printDateInfo($row['o_rdate'],1) ?></div>
		</div><!-- end total_price -->

	</div>


	<div class="group_title"><strong>주문자 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>회원타입</th>
					<td colspan="3">
						<?php echo $row['o_memtype'] == 'Y' ? '회원':'비회원'; ?>
					</td>
				</tr>
				<tr>
					<th>아이디</th>
					<td>
						<?=$row['o_mid']?>
					</td>
					<th>이름</th>
					<td>
						<?=$row['o_oname']?>
					</td>
				</tr>
				<tr>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($row['o_ohp']); ?>
					</td>
					<th>이메일</th>
					<td>
						<?php echo $row['o_oemail']; ?>
					</td>
				</tr>
				<tr>
					<th>기기정보</th>
					<td colspan="3">
						<textarea cols="30" rows="4" class="design" readonly><?php echo $row['device_info']; ?></textarea>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


	<?php if( in_array($row['o_order_type'], array('delivery','both')) > 0) {?>
	<div class="group_title"><strong>받는 분 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>이름</th>
					<td><?php echo $row['o_rname']; ?></td>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($row['o_rhp']); ?>
					</td>
				</tr>
				<tr>
					<th>배송지 주소</th>
					<td>
						<?php 
							echo '('.$row['o_rzonecode'].') '.$row['o_raddr_doro'].' '.$row['o_raddr2']; 
						?>                  
					</td>
					<th>지번주소</th>
					<td>
						<?php
							// 배송지 우편번호
							echo $row['o_raddr1']; 
						?>                  
					</td>					
				</tr>
				<tr>
					<th>배송시 유의사항</th>
					<td colspan="3">
						<textarea name="_content" rows="4" cols="" class="design" readonly><?php echo $row['o_content']; ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php } ?>




	<?php if( in_array($row['o_order_type'], array('ticket','both')) > 0) {?>
	<div class="group_title"><strong>사용자 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용자명</th>
					<td>
						<?=$row['o_uname']?>
					</td>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($row['o_uhp']); ?>
					</td>
				</tr>
				<tr>
					<th>사용자 이메일 주소</th>
					<td colspan="3">
						<?php echo $row['o_uemail']; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php } ?>



	<div class="group_title"><strong>관리용 메모</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>메모 내용</th>
					<td>
						<textarea name="_admcontent" rows="4" cols="" class="design" readonly><?php echo $row['o_admcontent']; ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>




	<!-- 상세페이지 버튼 -->
	<?php echo _submitBTN($app_current_link_list,'','',true,true); ?>

</form>




<?php include_once('wrap.footer.php'); ?>