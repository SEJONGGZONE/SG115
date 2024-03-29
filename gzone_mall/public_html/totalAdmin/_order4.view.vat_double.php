

<div class="data_list">
	<table class="table_list type_nocheck">
		<colgroup>
			<col width="70"><col width="450"><col width="*"><col width="100"><col width="90">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">No</th>
				<th scope="col">주문상품</th>
				<th scope="col">주문금액</th>
				<th scope="col">과세여부</th>
				<th scope="col">관리</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($pr as $k=>$v) {
				$_num = $TotalCount - $count - $k;
				$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
				if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

				// 2017-06-22 ::: 부가세율설정 ::: JJC
				$app_vat_str = ($v['op_vat'] == 'N'?'<span class="c_tag h18 yellow t5">면세 상품</span>':'<span class="c_tag h18 green t5">과세 상품</span>');
				$partner['cp_vat_delivery'] = ($siteInfo['s_vat_delivery'] == 'C' ? $partner['cp_vat_delivery'] : $siteInfo['s_vat_delivery']);
				$app_delivery_vat_str = ''.($partner['cp_vat_delivery'] == 'N'?'<span class="c_tag h18 t5 yellow line">배송비 면세</span>':'<span class="c_tag h18 t5 green line">배송비 과세</span>').'';
				// 2017-06-22 ::: 부가세율설정 ::: JJC

				$userLink = '/?pn=product.view&code='.$v['op_pcode']; // 사용자 링크 사용시
				$adminLink = '_product.form.php?_mode=modify&_code='.$v['op_pcode'];
				if( $v['op_ptype'] == 'ticket'){
					$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$v['op_pcode'];
				}

			?>
					<tr>
						<td class="this_num"><?php echo number_format($_num); ?></td>
						<td>
							<div class="order_item_thumb">
								<div class="thumb"><a href="<?php echo $adminLink; ?>" target="_blank"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(htmlspecialchars($v['op_pname'])); ?>"></a></div>
								<div class="order_item">
									<dl>
										<dt>
											<div class="item_name">
												<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo htmlspecialchars($v['op_pname']); ?></a>
											</div>
											<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
										</dt>
										<?php
											if($sv['op_option1']) {
												// KAY :: 2022-12-07 :: 옵션 구분 수정
												$arr_option_name = array($v['op_option1'] ,$v['op_option2'],$v['op_option3'] );
												$arr_option_name = array_filter($arr_option_name);
										?>
											<dd>
												<div class="option_name"><?php echo ($v['op_is_addoption'] == 'Y'?'<span class="add_option">추가옵션</span> : ':'<span class="option">필수옵션</span> : '); ?><?php echo implode(" / ",$arr_option_name); ?></div>

											</dd>
										<?php } ?>
									</dl>

									<ul class="user_apply">
										<li>주문자명 : <?php echo htmlspecialchars($v['o_oname']); ?></li>
									</ul>

								</div><!-- end order_item -->
							</div><!-- end order_item_thumb -->
							<div class="order_item_tag">
								<div class="left">
									<a href="<?php echo $arr_delivery_company[$v['op_sendcompany']].rm_str($v['op_sendnum']); ?>" class="c_btn h22 green line h22 " target="_blank">배송조회 : <?php echo $v['op_sendcompany']; ?> : <?php echo $v['op_sendnum']; ?></a>
								</div>
								<div class="right">
									<?php echo $arr_adm_button[$v['op_sendstatus']]; ?>
								</div>
							</div>
						</td>
						<td>
							<div class="in_price">
								<ul class="in_thead">
									<li>상품금액</li>
									<li>주문금액</li>
									<li>배송비</li>
									<li>정산금액</li>
								</ul>
								<ul>
									<li class="t_light"><?php echo number_format($v['op_price']); ?>원</li>
									<li class="t_black"><?php echo number_format($v['op_price'] * $v['op_cnt']); ?>원</li>
									<li class="t_green">
										<?php echo number_format($v['op_delivery_price']); ?>원
										<?php if($v['op_delivery_type'] != '입점') { ?>
											<div class="t_black">(<?php echo $v['op_delivery_type']; ?>배송)</div>
										<?php } ?>
										<?php if($v['op_add_delivery_price'] > 0) { ?>
											<div class="t_black">+<?php echo number_format($v['op_add_delivery_price']); ?>원<br>(추가배송비)</div>
										<?php } ?>
									</li>
									<li class="t_blue"><?php echo number_format($v['comPrice']); ?>원</li>
								</ul>
							</div>
						</td>
						<td class="this_state">
							<div class="lineup-row type_center">
								<?php echo $app_vat_str; ?><!-- 상품과세 -->
								<?php echo $app_delivery_vat_str; ?> <!-- 배송비과세 -->
							</div>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_order.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_ordernum'=>$v['op_oordernum'])); ?>" class="c_btn gray" target="_blank">주문상세</a>
							</div>
						</td>
					</tr>
				<?php } ?>
		</tbody>
	</table>


	<div class="total_price">
		<div class="inner_box">
			<dl>
				<dt>
					<strong class="t_green">과세</strong>상품 합계
				</dt>
				<dd>
					<ul>
						<li>
							<em>상품개수</em>
							<strong  class="t_black"><?php echo number_format($arr_sum['product_cnt']['Y']); ?>개</strong>
						</li>
						<li>
							<em>주문금액</em>
							<strong  class="t_black"><?php echo number_format($arr_sum['product_price']['Y']); ?>원</strong>
						</li>
						<li>
							<em>배송비</em>
							<strong class="t_green"><?php echo number_format($arr_sum['delivery_price']['Y']); ?>원</strong>
						</li>
						<li>
							<em>할인금액</em>
							<strong class="t_orange"><?php echo number_format($arr_sum['product_usepoint']['Y']); ?>원</strong>
						</li>
						<li>
							<em>정산금액</em>
							<strong  class="t_blue"><?php echo number_format($arr_sum['comPrice']['Y']); ?>원</strong>
						</li>
						<li>
							<em>수수료</em>
							<strong  class="t_sky"><?php echo number_format($arr_sum['product_price']['Y'] + $arr_sum['delivery_price']['Y'] - $arr_sum['comPrice']['Y'] - $arr_sum['product_usepoint']['Y']); ?>원</strong>
						</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>
					<strong class="t_orange">면세</strong>상품 합계
				</dt>
				<dd>
					<ul>
						<li>
							<em>상품개수</em>
							<strong  class="t_black"><?php echo number_format($arr_sum['product_cnt']['N']); ?>개</strong>
						</li>
						<li>
							<em>주문금액</em>
							<strong  class="t_black"><?php echo number_format($arr_sum['product_price']['N']); ?>원</strong>
						</li>
						<li >
							<em>배송비</em>
							<strong class="t_green"><?php echo number_format($arr_sum['delivery_price']['N']); ?>원</strong>
						</li>
						<li >
							<em>할인금액</em>
							<strong class="t_orange"><?php echo number_format($arr_sum['product_usepoint']['N']); ?>원</strong>
						</li>
						<li>
							<em>정산금액</em>
							<strong  class="t_blue"><?php echo number_format($arr_sum['comPrice']['N']); ?>원</strong>
						</li>
						<li>
							<em>수수료</em>
							<strong  class="t_sky"><?php echo number_format($arr_sum['product_price']['N'] + $arr_sum['delivery_price']['N'] - $arr_sum['comPrice']['N'] - $arr_sum['product_usepoint']['N']); ?>원</strong>
						</li>
					</ul>
				</dd>
			</dl>
		</div>
		<div class="calculate_date">
			정산일 : <?php echo date('Y-m-d', strtotime($r['s_date'])); ?>
			<span class="t_light">(<?php echo date('H:i:s', strtotime($r['s_date'])); ?>)</span>
		</div>
	</div><!-- end total_price -->

</div>


<!-- 입점업체 정보 -->
<div class="group_title"><strong>입점업체 정보</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>아이디</th>
				<td class="only_text bold">
					<?php echo ($partner['cp_id']?$partner['cp_id']:$r['s_partnerCode']); ?>
				</td>
				<th>업체명</th>
				<td class="only_text bold">
					<?php echo ($partner['cp_name']?$partner['cp_name']:'<span class="t_orange">삭제됨</span>'); ?>
				</td>
			</tr>
			<tr>
				<th>대표명</th>
				<td class="only_text">
					<?php echo ($partner['cp_ceoname']?$partner['cp_ceoname']:'-'); ?>
				</td>
				<th>담당자명</th>
				<td class="only_text">
					<?php echo ($partner['cp_charge']?$partner['cp_charge']:'-'); ?>
				</td>
			</tr>
			<tr>
				<th>이메일</th>
				<td class="only_text">
					<?php echo ($partner['cp_email']?$partner['cp_email']:'-'); ?>
				</td>
				<th>전화번호</th>
				<td class="only_text">
					<?php echo ($partner['cp_tel'] || $partner['cp_tel2']?($partner['cp_tel']?tel_format($partner['cp_tel']):tel_format($partner['cp_tel2'])):'-'); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>



<?php
// JJC001
// ---------------------------------------
// ----- 세금계산서 사용여부에 따른 노출 -----
// ---------------------------------------
if($siteInfo['TAX_CHK'] == 'Y') {

	// 세금계산서 관련 변수 파일 불러오기
	include_once(OD_ADDONS_ROOT.'/barobill/include/var.php');

	// 바로빌 정발행 내부상태값 테이블
	// 공백
	// 1000 => "임시저장"
	// 2010 => "발행대기 - 발행예정_승인대기",
	// 2011 => "발행대기 - 발행예정_승인완료",
	// 4012 => "거부 - 발행예정_거부",
	// 5013 => "취소 - 발행예정_공급자취소[승인전 취소]",
	// 5031 => "취소 - 발행예정-공급자취소[승인후 취소] 국세청 승인번호가 없음", // 발행완료 후 공급자에 의한 발행취소 국세청 승인번호가 있음
	// 3014 => "발행완료 - 발행완료[즉시발행/즉시 전송]",
	// 3011 => "발행완료 - 발행완료[발행예정후 발행]",
	/*
	function tax_btn_vatY($app_mode , $app_tax_btn_nm , $color='blue'){
		global $suid , $_PVSC ;
		return '<a href="'.OD_ADDONS_URL.'/barobill/_tax.pro.php?suid='.$suid.'&mode='.$app_mode.'&_PVSC='.$_PVSC.'" onclick="if(!confirm(\'정말 실행하시겠습니까?\')) return false;" class="c_btn h22 line '.$color.'">'.$app_tax_btn_nm.'</a>';
	}
	function tax_btn_vatN($app_mode , $app_tax_btn_nm , $color='blue'){
		global $suid , $_PVSC ;
		return '<a href="'.OD_ADDONS_URL.'/barobill/_tax.pro.php?suid='.$suid.'&mode='.$app_mode.'&submode=N&_PVSC='.$_PVSC.'" onclick="if(!confirm(\'정말 실행하시겠습니까?\')) return false;" class="c_btn h22 line '.$color.'">'.$app_tax_btn_nm.'</a>';
	}

	// 세금계산서 상태에 따른 버튼 노출 변경
	switch($r['s_tax_status']){
		case 1000 ://임시저장
			$app_tax_btn_vatY = tax_btn_vatY('issue', '세금계산서 발행', 'red') . tax_btn_vatY('delete', '세금계산서 삭제'); break;
		case 2010 : case 2011 : //발행대기
		case 4012 : //거부
			$app_tax_btn_vatY = ''; break;
		case 3014 : case 3011 : //발행완료
			$app_tax_btn_vatY = tax_btn_vatY('cancel', '세금계산서 발행취소'); break;
		case 5013 : case 5031 : //발행취소
			$app_tax_btn_vatY = tax_btn_vatY('delete', '세금계산서 삭제'); break;
		default : //미발행상태
			$app_tax_btn_vatY = tax_btn_vatY('regist', '세금계산서 임시저장') . tax_btn_vatY('quick', '세금계산서 바로발행', 't_green'); break;
	}

	// 계산서 상태에 따른 버튼 노출 변경
	switch($r['s_tax_status_vat_n']){
		case 1000 ://임시저장
			$app_tax_btn_vatN = tax_btn_vatN('issue', '계산서 발행', 'red') . tax_btn_vatN('delete', '계산서 삭제'); break;
		case 2010 : case 2011 : //발행대기
		case 4012 : //거부
			$app_tax_btn_vatN = ''; break;
		case 3014 : case 3011 : //발행완료
			$app_tax_btn_vatN = tax_btn_vatN('cancel', '계산서 발행취소'); break;
		case 5013 : case 5031 : //발행취소
			$app_tax_btn_vatN = tax_btn_vatN('delete', '계산서 삭제'); break;
		default : //미발행상태
			$app_tax_btn_vatN = tax_btn_vatN('regist', '계산서 임시저장') . tax_btn_vatN('quick', '계산서 바로발행', 't_green'); break;
	}
	*/

	// 세금계산서 연동 체크
	$app_tax_btuid_vatY = _MQ_result(" select bt_uid from smart_baro_tax where bt_suid = '{$suid}' and TaxInvoiceType = '1' and bt_is_delete = 'N' order by bt_uid desc ");
	$app_tax_btuid_vatN = _MQ_result(" select bt_uid from smart_baro_tax where bt_suid = '{$suid}' and TaxInvoiceType = '2' and bt_is_delete = 'N' order by bt_uid desc ");

	// 세금계산서 상태에 따른 버튼 노출 변경
	switch($r['s_tax_status']){
		case 1000 ://임시저장
			$app_tax_btn_vatY = '<a href="#none" onclick="window.open(\'_tax.form.php?_mode=modify&_uid='.$app_tax_btuid_vatY.'\');" class="c_btn blue line">세금계산서 수정</a>';
			break;
		case 2010 : case 2011 : //발행대기
		case 4012 : //거부
			$app_tax_btn_vatY = '';
		case 3014 : case 3011 : //발행완료
			$app_tax_btn_vatY = '<a href="#none" onclick="window.open(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'info', '_uid'=>$app_tax_btuid_vatY)) .'\', \'tax_print\', \'width=900, height=700\')" class="c_btn black">세금계산서 조회</a>'; break;
		case 5013 : case 5031 : //발행취소
			$app_tax_btn_vatY = '<a href="#none" onclick="if(!confirm(\'정말 실행하시겠습니까?\')){ return false; }else{ window.open(\'_tax.form.php?suid='.$suid.'&vat=Y\'); }" class="c_btn h22 blue">세금계산서 발행</a>';
			break;
		default : //미발행상태
			$app_tax_btn_vatY = '<a href="#none" onclick="if(!confirm(\'정말 실행하시겠습니까?\')){ return false; }else{ window.open(\'_tax.form.php?suid='.$suid.'&vat=Y\'); }" class="c_btn h22 blue">세금계산서 발행</a>';
			break;
	}

	// 계산서 상태에 따른 버튼 노출 변경
	switch($r['s_tax_status_vat_n']){
		case 1000 ://임시저장
			$app_tax_btn_vatN = '<a href="#none" onclick="window.open(\'_tax.form.php?_mode=modify&_uid='.$app_tax_btuid_vatN.'\');" class="c_btn h22 t_green">세금계산서 수정</a>';
			break;
		case 2010 : case 2011 : //발행대기
		case 4012 : //거부
			$app_tax_btn_vatN = '';
		case 3014 : case 3011 : //발행완료
			$app_tax_btn_vatN = '<a href="#none" onclick="window.open(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'info', '_uid'=>$app_tax_btuid_vatN)) .'\', \'tax_print\', \'width=900, height=700\')" class="c_btn h22">계산서 조회</a>'; break;
		case 5013 : case 5031 : //발행취소
			$app_tax_btn_vatN = '<a href="#none" onclick="if(!confirm(\'정말 실행하시겠습니까?\')){ return false; }else{ window.open(\'_tax.form.php?suid='.$suid.'&vat=N\'); }" class="c_btn h22 blue">계산서 발행</a>';
			break;
		default : //미발행상태
			$app_tax_btn_vatN = '<a href="#none" onclick="if(!confirm(\'정말 실행하시겠습니까?\')){ return false; }else{ window.open(\'_tax.form.php?suid='.$suid.'&vat=N\'); }" class="c_btn h22 blue">계산서 발행</a>';
			break;
	}

	// 상태값 추출 - // 세금계산서 상태값 업데이트
	if($r['s_tax_mgtnum'] && $r['s_tax_status'] == -9999) {
		$submode = 'Y';
		$uid = $app_tax_btuid_vatY;
		include(OD_ADDONS_ROOT.'/barobill/api_ti/_tax.GetTaxInvoiceState.php');
	}

	// 상태값 추출 - // 계산서 상태값 업데이트
	if($r['s_tax_mgtnum_vat_n'] && $r['s_tax_status_vat_n'] == -9999) {
		$submode = 'N';
		$uid = $app_tax_btuid_vatN;
		include(OD_ADDONS_ROOT.'/barobill/api_ti/_tax.GetTaxInvoiceState.php');
	}

	// ------ 2017-06-22 ::: 부가세율설정 ::: JJC --------------------
	$app_vat_Y_discount = $arr_sum['product_price']['Y'] + $arr_sum['delivery_price']['Y'] - $arr_sum['comPrice']['Y'] - $arr_sum['product_usepoint']['Y']; // 과세 수수료
	$app_vat_N_discount = $arr_sum['product_price']['N'] + $arr_sum['delivery_price']['N'] - $arr_sum['comPrice']['N'] - $arr_sum['product_usepoint']['N']; // 면세 수수료
	if($app_vat_Y_discount <> 0 && $app_vat_N_discount <> 0) {
		include('_order4.view.tax_y_info.php'); // 과세수수료 - app_vat_Y_discount
		include('_order4.view.tax_n_info.php'); // 면세수수료 - app_vat_N_discount
	}
	else {
		if($app_vat_Y_discount <> 0) {
			$_tax_type = 'Y'; // 과세
			include(OD_ADMIN_ROOT.'/_order4.view.tax_y_info.php'); // 과세수수료 - app_vat_Y_discount
		}
		else if($app_vat_N_discount <> 0 ) {
			$_tax_type = 'N'; // 면세
			include(OD_ADMIN_ROOT.'/_order4.view.tax_n_info.php'); // 면세수수료 - app_vat_N_discount
		}
	}
	// ------ 2017-06-22 ::: 부가세율설정 ::: JJC --------------------
?>
	<?php
		if($AdminPath == 'totalAdmin') {
			$sres = _MQ_assoc(" select * from smart_baro_tax_log as tl left join smart_baro_tax as bt on (tl.tl_btuid = bt.bt_uid) where bt.bt_suid = '{$r['s_uid']}' and bt.bt_is_delete = 'N' ");
	?>
		<!-- 계산서 및 세금계산서 연동기록 -->
		<div class="group_title"><strong>계산서 및 세금계산서 연동기록</strong></div>
		<div class="data_list">
			<?php if(count($sres) > 0) { ?>
				<table class="table_list type_nocheck">
					<colgroup>
						<col width="80">
						<col width="150">
						<col width="180">
						<col width="*">
						<col width="100">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">연동형태</th>
							<th scope="col">문서번호</th>
							<th scope="col">연동기록</th>
							<th scope="col">연동일</th>
						</tr>
					</thead>
						<tbody>
						<?php foreach($sres as $sk=>$sv) { ?>
							<tr>
								<td class="this_num"><?php echo number_format($sk+1); ?></td>
								<td><?php echo ($sv['TaxInvoiceType']<>'1' ? str_replace('세금', '', $arr_tax_mode_status[$sv['tl_mode']]) : $arr_tax_mode_status[$sv['tl_mode']]); ?></td>
								<td class="t_blue"><?php echo $sv['MgtKey']; ?></td>
								<td class="t_left">
									<?php echo (in_array($sv['tl_remark'], array_keys($arr_error_code))?"[{$sv['tl_remark']}] ".$arr_error_code[$sv['tl_remark']]:$sv['tl_remark']); ?>
								</td>
								<td class="this_date">
									<?php echo date('Y-m-d', strtotime($sv['tl_rdate'])); ?>
									<div class="t_light"><?php echo date('H:i:s', strtotime($sv['tl_rdate'])); ?></div>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
			<?php if(count($sres) <= 0) { ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">내역이 없습니다.</div></div>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>


<?php echo _submitBTN($app_current_link, null, '', true, true); ?>