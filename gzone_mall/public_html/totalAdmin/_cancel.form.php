<?php
	$app_current_name = "부분취소 상세";
	$app_current_link = '_cancel.list.php';// 메뉴지정
	include_once('wrap.header.php');


	$r = _MQ("
		select
			op.* , o.* , p.*
		from smart_order_product as op
		inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
		left join smart_product as p on (p.p_code = op.op_pcode)
		where op.op_oordernum='".$_ordernum."' and op.op_uid = '".$uid."' and op.op_is_addoption = 'N'
	");
	//ViewArr($r);
	if($r['op_uid']<1) error_msg('잘못된 접근입니다.');

	// --- JJC : 2023-01-13 : 추가옵션 부분취소 ---
	//		추가옵션을 제외한 일반 옵션으로 정상 주문 1개인지 확인
	$arr_add = array();
	$addoption_price = $addoption_point = $addoption_cnt = 0;
	$aores = _MQ_assoc("
		SELECT
			op_addoption_parent , op_uid , concat(op_option1,' ',op_option2) as option_name , op_price , op_cnt , op_point
		FROM smart_order_product
		WHERE (1)
			AND op_oordernum = '".$r['op_oordernum']."'
			AND op_pcode = '".$r['op_pcode']."'
			AND op_addoption_parent = '".$r['op_pouid']."'
			AND op_cancel != 'N'
			AND op_is_addoption = 'Y'
	");
	foreach( $aores as $k=>$v) {
		$arr_add[$v['op_addoption_parent']][$v['op_pouid']] = $v;
		$addoption_price += $v['op_price'] * $v['op_cnt'];
		$addoption_point += $v['op_point'];
		$addoption_cnt += $v['op_cnt'];
	}
	// --- JJC : 2023-01-13 : 추가옵션 부분취소 ---



	// 주문 상품정보 추출
	//$totalPrice = 0 ;//총상품가격
	$arr_product = array();

	// 이미지 체크
	$_p_img = get_img_src('thumbs_s_'.$r['p_img_list_square']);
	if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

	//$pro_view_link = get_pro_view_link($sv[p_code]);
	//$p_img_list = get_img_src($sv[p_img_list]);


	//{{{혜택표기}}} -- 혜택에 대한 처리
	$arrBoonInfo =  array();
	// 혜택에 대한 처리 :: list1 -  회원 할인/추가적립
	if( $r['op_groupset_use'] == 'Y' && $r['op_groupset_price_per'] > 0 ){ $arrBoonInfo[] = '회원할인 '.odt_number_format($r['op_groupset_price_per'],1).'%'; }
	if( $r['op_groupset_use'] == 'Y' && $r['op_groupset_point_per'] > 0 ){ $arrBoonInfo[] = '회원추가적립'.odt_number_format($r['op_groupset_price_per'],1).'%'; }

	// 혜택에 대한 처리 :: list2 -  쿠폰적용여부
	$rowClChk = _MQ("select count(*) as cnt from smart_order_coupon_log where cl_oordernum = '".$r['op_oordernum']."' and cl_pcode = '".$r['op_pcode']."'   ");
	if( $rowClChk['cnt'] > 0){ $arrBoonInfo[] = '상품쿠폰사용 '.number_format($rowClChk['cnt']).'개';  }

	$r['boonInfo'] = count($arrBoonInfo) > 0 ? implode("<br>",$arrBoonInfo) : '<span class="t_none">할인/혜택없음</span>';
	//{{{혜택표기}}}


	//상품수 , 포인트 , 상품금액
	$arr_product['cnt'] += $r['op_cnt'] + $addoption_cnt;//상품수
	//$arr_product['point'] += $r['op_point'];//포인트// JJC : 2022-12-21 : 부분취소개선
	$arr_product['sum'] += $r['op_price'] * $r['op_cnt'] + $addoption_price;//상품금액
	$arr_product['add_delivery'] += $r['op_delivery_price'] + $r['op_add_delivery_price'];//개별배송비 포장

	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
	$arr_product["discount"] += $r['op_cancel_discount_price'] + $r['op_usepoint'];// JJC : 2022-12-21 : 부분취소개선
	$arr_product["point"] += $r['op_usepoint'] + $addoption_point;// JJC : 2022-12-21 : 부분취소개선
	// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------

?>


<div class="data_list">
	<table class="table_list type_nocheck">
		<colgroup>
			<col width="150">
			<col width="*">
			<col width="120"><col width="120"><col width="100"><col width="150">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">진행상태/상품타입</th>
				<th scope="col">주문상품</th>
				<th scope="col">주문금액</th>
				<th scope="col">배송비</th>
				<th scope="col">적립금</th>
				<th scope="col">할인/추가혜택</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="this_ctrl">
					<div class="lineup-row type_center">
						<?php echo $arr_adm_button[$r['op_sendstatus']]; ?>
						<?php echo $arr_adm_button[$r['op_ptype']]; // 상품타입에 따른 아이콘 ?>
					</div>
				</td>
				<td class="this_item">
					<div class="order_item_thumb type_simple">
						<div class="thumb">
							<?php if($r['p_code'] == ''){?>
								<div class="error">삭제된상품</div>
							<?php } ?>
							<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes($r['op_pname']); ?>">
						</div>
						<div class="order_item">
							<dl>
								<dt><div class="item_name"><?php echo $r['op_pname']; ?></div></dt>


							<?php if($r['op_pouid']>0){ ?>
								<dd>
									<?php echo ($r['op_is_addoption']=='Y'?'<div class="add_option">추가</div>':'<div class="option">필수</div>'); ?><?php echo implode(' ', array_filter(array($r['op_option1'],$r['op_option2'],$r['op_option3']))); ?>
									<span class="mount"><?php echo number_format($r['op_price']); ?>원 x <?php echo number_format($r['op_cnt']); ?>개</span><?php // JJC : 2023-01-13 : 추가옵션 부분취소?>
								</dd>
								<?php // --- JJC : 2023-01-13 : 추가옵션 부분취소 --- ?>
								<?php if(sizeof($arr_add[$r['op_pouid']]) > 0 ) {?>
									<?php foreach( $arr_add[$r['op_pouid']] as $sk=>$sv) {?>
										<dd>
											<div class="add_option">추가</div><?php echo $sv['option_name']; ?>
											<span class="mount"><?php echo number_format($sv['op_price']); ?>원 x <?php echo number_format($sv['op_cnt']); ?>개</span>
											<?php echo $r['delivery_print'];?>
											<?php echo $r['ticket_print'];?>
										</dd>
									<?php } ?>
								<?php } // --- JJC : 2023-01-13 : 추가옵션 부분취소 --- ?>

							<?php } ?>
							</dl>
						</div>
					</div>
				</td>
				<!--<td class="t_black"><?php echo number_format($r['op_cnt']); ?></td>
				 <td class="t_black"><?php echo number_format($r['op_price']); ?>원</td> --><?php // JJC : 2023-01-13 : 추가옵션 부분취소?>

				<td class="t_right t_blue t_bold this_state">
					<span class="hidden_tx">주문금액</span><?php echo number_format($r['op_price']*$r['op_cnt'] + $addoption_price); // JJC : 2023-01-13 : 추가옵션 부분취소?>원
				</td>
				<td class="t_right t_green">
					<span class="hidden_tx">배송비</span><?php echo number_format($r['op_delivery_price']); ?>원
					<?php if($r['op_delivery_type']<>'입점'){ ?>
						<div><?php echo $r['op_delivery_type']; ?>배송</div>
					<?php } ?>
					<?php if($r['op_add_delivery_price']>0){ ?>
						<div>+<?php echo number_format($r['op_add_delivery_price']); ?>원</div><div>추가배송비</div>
					<?php } ?>
				</td>
				<td class="t_sky t_right">
					<span class="hidden_tx">적립금</span><?php echo number_format($r['op_point'] + $addoption_point); // JJC : 2023-01-13 : 추가옵션 부분취소?>원
				</td>
				<td class="t_orange bold">
					<?php echo $r['boonInfo']; ?>
				</td>
			</tr>
		</tbody>
	</table>


	<div class="total_price">
		<div class="inner_box">
			<dl>
				<dt>
					<div class="">환불내역</div>
				</dt>
				<dd>
					<ul>
						<li>
							<em>적립금 환불</em>
							<strong class="t_sky"><?php echo number_format($arr_product['point']); ?>원</strong>
						</li>
						<li>
							<em>총 환불금액</em>
							<strong class="t_red"><?php echo number_format($arr_product['sum']+$arr_product['add_delivery']-$arr_product['discount']); ?>원</strong>
						</li>
					</ul>
					<input type="hidden" name="cancel_total" value="<?php echo ($arr_product['sum']+$arr_product['add_delivery']-$arr_product['discount']); ?>"/>
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="">주문서 <strong class="t_blue">(<?php echo $r['o_ordernum']; ?>)</strong></div>
					<div class="lineup-row">
						<?php echo ($r['o_paystatus'] == 'Y' ? $arr_adm_button['결제완료'] : $arr_adm_button['결제대기']); ?>
						<?php
							// 신용카드 간편결제 추가
							if( $r['o_easypay_paymethod_type'] != ''){
								echo $arr_adm_button["E".$arr_available_easypay_pg_list[$r['o_easypay_paymethod_type']]];
							}else{
								echo $arr_adm_button[$arr_payment_type[$r['o_paymethod']]];
							}
						?>
					</div>
				</dt>
				<dd>
					<ul>
						<li>
							<em>총상품가격</em>
							<strong  class="t_black"><?php echo number_format($arr_product['sum']); ?>원</strong>
						</li>
						<li>
							<em>총 배송비</em>
							<strong class="t_green"><?php echo number_format($arr_product['add_delivery']); ?>원</strong>
						</li>
						<li>
							<em>할인금액</em>
							<strong class="t_orange"><?php echo number_format($arr_product['discount']); ?>원</strong>
						</li>
					</ul>
				</dd>
			</dl>
		</div>
		<div class="calculate_date">주문일시 : <?php echo date('Y-m-d', strtotime($r['o_rdate'])); ?> <span class="t_light"><?php echo date('H:i:s', strtotime($r['o_rdate'])); ?></span></div>
	</div>
</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>고객 요청내용</strong></div>

	<form name="frm" method="post" action="_cancel.pro.php" >
	<input type="hidden" name="_mode" value="modify">
	<input type="hidden" name="ordernum" value='<?php echo $_ordernum; ?>'>
	<input type="hidden" name="op_uid" value="<?php echo $uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="statusUpdate" value="yes">
	<input type="hidden" name="cancel_type" value="<?php echo $r["op_cancel_type"]; ?>">
	<input type="hidden" name="cancel_bank" class="js_change_val" value="<?php echo $r['op_cancel_bank']; ?>">
	<input type="hidden" name="cancel_bank_account" class= "js_cancel_bank_account" value="<?php echo $r['op_cancel_bank_account']; ?>" />
	<input type="hidden" name="cancel_bank_name" class="js_cancel_bank_name" value="<?php echo $r['op_cancel_bank_name']; ?>" />

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>고객 요청내용</th>
						<td>
							<textarea name="cancel_msg" rows="4" cols="" placeholder="고객 요청내용" class="design"<?php echo $AdminPath == 'totalAdmin' ? null:' readonly' ?>><?php echo $r['op_cancel_msg']; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- 상세페이지 버튼 -->
			<div class="c_btnbox type_full">
				<ul>
					<?php if(  $AdminPath == 'totalAdmin') {?>
					<li><span class="c_btn h46 red"><input type="submit" name="" value="정보수정" accesskey="s"></span></li>
					<?php } ?>
					<li><a href="_cancel.list.php<?php echo ($_PVSC ? '?'.enc('d' , $_PVSC) : null); ?>" class="c_btn h46 black line" accesskey="l">목록으로</a></li>
				</ul>
			</div>
		</div>

	</form>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문 및 결제 정보</strong></div>

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>취소요청 일시</th>
					<td>
						<?php echo date('Y-m-d (H:i:s)',strtotime($r['op_cancel_rdate'])); ?>
					</td>
					<th>취소완료 일시</th>
					<td class="t_red">
						<?php if($r['op_cancel'] == 'Y'){?>
						<?php echo date('Y-m-d (H:i:s)',strtotime($r['op_cancel_cdate'])); ?>
						<?php }else{ ?>
							<div class="t_light">아직 취소처리 전입니다</div>
						<?php } ?>
					</td>
				</tr>


				<?php
					// 환불요청이 있을 경우
					if(in_array($r['o_paymethod'],$arr_refund_payment_type)) { // SSJ : 주문/결제 통합 패치 : 2021-02-24
				?>
				<tr>
					<th>환불계좌 정보</th>
					<td>
						<div class="lineup-row type_multi">
							<?php if($r['op_cancel']=='Y') { ?>
								<strong><?php echo $ksnet_bank[$r['op_cancel_bank']]; ?></strong>
							<?php } else { ?>
								<select name="cancel_bank" class="js_bank_change" <?php echo $AdminPath != 'totalAdmin' ? ' disabled':'' ?>>
									<option value="">- 은행 선택 -</option>
									<?php foreach($ksnet_bank as $k=>$v) { ?>
									<option value="<?php echo $k; ?>" <?php echo ($k==$r['op_cancel_bank']?'selected':'');?>><?=$v?></option>
									<?php } ?>
								</select>
							<?php } ?>
							<?php if($r['op_cancel']=='Y' || $AdminPath == 'subAdmin') { ?>
							<strong><?php echo $r['op_cancel_bank_account'];?></strong>
							<?php } else { ?>
								<input type="text" name="cancel_bank_account" value="<?php echo $r['op_cancel_bank_account']; ?>" placeholder="계좌번호" class="design js_bank_keyup"/>
							<?php } ?>
						</div>
					</td>
					<th>환불계좌 예금주</th>
					<td>
						<?php if($r['op_cancel']=='Y' || $AdminPath == 'subAdmin') { ?>
							<strong><?php echo $r['op_cancel_bank_name']; ?></strong>
						<?php } else { ?>
							<input type="text" name="cancel_bank_name" value="<?php echo $r['op_cancel_bank_name']; ?>" placeholder="예금주명" class="design js_bank_keyup"/>
						<?php } ?>
					</td>
				</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문자 정보</strong></div>



	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>회원타입</th>
					<td>
						<?php echo ($r['o_memtype']=='Y' ? '회원' : '비회원'); ?>
					</td>
					<th>주문자 아이디</th>
					<td>
						<?php echo $r['o_mid']; ?>
					</td>
				</tr>
				<tr>
					<th>주문자명</th>
					<td>
						<?php echo $r['o_oname']; ?>
					</td>
					<th>휴대폰번호</th>
					<td>
						<?php echo tel_format($r['o_ohp']); ?>
					</td>
				</tr>
				<tr>
					<th>주문자 이메일</th>
					<td colspan="3">
						<?php echo $r['o_oemail']; ?>
					</td>
				</tr>

				<tr>
					<th>주문자 기기정보</th>
					<td colspan="3">
						<?php echo nl2br($r['device_info']); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>

	<?php if( in_array($r['op_ptype'], array('delivery','both')) > 0) {?>
		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>받는 분 정보</strong></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>받는분 이름</th>
						<td><?php echo $r['o_rname']; ?></td>
						<th>휴대폰번호</th>
						<td>
							<?php echo tel_format($r['o_rhp']); ?>
						</td>
					</tr>
					<?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>
					<tr>
						<th>배송지 주소</th>
						<td>
							<?php
								echo '('.$r['o_rzonecode'].') '.$r['o_raddr_doro'].' '.$r['o_raddr2'];
							?>
						</td>
						<th>지번 주소</th>
						<td>
							<?php
								// 배송지 우편번호
								echo $r['o_raddr1'];
							?>
						</td>
					</tr>
					<?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>
					<tr>
						<th>배송시 유의사항</th>
						<td colspan="3">
							<textarea name="_content" rs="4" cols="" class="design" placeholder="배송시 유의사항" readonly><?php echo $r['o_content']; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>


	<?php if( in_array($r['op_ptype'], array('ticket','both')) > 0) {?>
		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>사용자 정보</strong></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>사용자명</th>
						<td>
							<?=$r['o_uname']?>
						</td>
						<th>휴대폰번호</th>
						<td>
							<?php echo tel_format($r['o_uhp']); ?>
						</td>
					</tr>
					<tr>
						<th>사용자 이메일</th>
						<td colspan="3">
							<?php echo $r['o_uemail']; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>


<script>

	$(document).ready(function(){
		$(".js_bank_change").on("change", function(){
			$(".js_change_val").val($(this).val());
		});
		$(".js_bank_keyup").on("keyup", function(){
			var value = $(this).val();
			var name = $(this).attr("name");
			$(".js_"+name).val(value);
			console.log( $(".js_"+name).val() );
		});
	});
</script>

<?php include_once('wrap.footer.php'); ?>
