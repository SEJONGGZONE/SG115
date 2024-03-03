<?php
$app_current_link = '_npay_order.list.php'; // 목록페이지 지정
include_once('wrap.header.php');

# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData);

# 정보조회
$r = _MQ("
	select
		`op`.*,
		`o`.*,
		`op`.`npay_status` as `npay_status`,
		`p`.`p_img_list`
	from
		`smart_order_product` as `op` left join
		`smart_product` as `p` on (`p`.`p_code` = `op`.`op_pcode`) left join
		`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`)
	where
		`op`.`op_uid` = '{$_uid}'
		".($AdminPath == 'subAdmin'?" and `op`.`op_partnerCode` = '{$_COOKIE["AuthCompany"]}' ":null)."
");
$sv = $r;
if($r['npay_order'] != 'Y') error_msg('네이버페이 주문만 조회가능합니다.');

# 주문상품정보 변수 할당
// -- 이미지 ---
$img_src = get_img_src($sv['p_img_list']);
$img_src = (@file_exists($_SERVER['DOCUMENT_ROOT'].'/upfiles/product/'.$img_src) && $img_src?$img_src:$sv['p_img_list']);


// -- 배송상품정보 ::: 택배, 송장, 발송일 표기 ---
$delivery_html = '';
if($sv['op_sendcompany']) {
	$delivery_html = "
		<div class='option_box'>
			<div class='pro_option'>
				<span  style='display:block'><span class='coupon_num'>택배사 : ". $sv['op_sendcompany'] ."</span></span>
				<span  style='display:block'><span class='coupon_num'>송장번호 : ". $sv['op_sendnum'] ."</span></span>
				<span  style='display:block'><span class='coupon_num'>발송일 : ". substr($sv['op_senddate'],0, 10) ."</span></span>
			</div>
		</div>
	";
}
// -- 배송상품정보 ---

// 상태아이콘
$StatusIcon = '';
if($r['npay_status'] == 'PAYED') $StatusIcon = '<span class="c_tag blue h22 t4">결제완료</span>';
if($r['npay_status'] == 'PLACE') $StatusIcon = '<span class="c_tag purple h22 t4">발주처리</span>';
if($r['npay_status'] == 'DISPATCHED') $StatusIcon = '<span class="c_tag green h22 t4">배송처리</span>';
if($r['npay_status'] == 'CANCELED') $StatusIcon = '<span class="c_tag light h22 t4">주문취소</span>';


// LDD: 2019-01-18 네이버페이 패치
	$StatusArray = array(
		  'PAYED' => '결제 완료'
		, 'DISPATCHED' => '발송 처리'
		, 'CANCEL_REQUESTED' => '취소 요청'
		, 'RETURN_REQUESTED' => '반품 요청'
		, 'EXCHANGE_REQUESTED' => '교환 요청'
		, 'EXCHANGE_REDELIVERY_READY' => '교환 재배송 준비'
		, 'HOLDBACK_REQUESTED' => '구매 확정 보류 요청'
		, 'CANCELED' => '취소'
		, 'RETURNED' => '반품'
		, 'EXCHANGED' => '교환'
		, 'PURCHASE_DECIDED' => '구매 확정'
	);
	if(in_array($r['npay_status'], array('PAYED', 'PLACE', 'DISPATCHED', 'CANCELED')) == false) {
		$StatusIcon = '<span class="c_tag gray h22 t4" style="width:auto; padding:0 5px!important">'.$StatusArray[$r['npay_status']].'</span>';
	}
	$SyncIcon = array(
		'Y'=>'<span class="c_tag darkgreen line t4">연동완료</span>',
		'R'=>'<span class="c_tag black t4">연동대기</span>',
		'A'=>'<span class="c_tag green line t4">후연동</span>'
	);
// LDD: 2019-01-18 네이버페이 패치

// -- 배송비 ---
// $delivery_print = ($sv['op_delivery_price'] > 0 && $delivery_print != "무료배송") ? number_format($sv['op_delivery_price'])."원" : "-"; // 배송정보.
// $add_delivery_print = ($sv['op_add_delivery_price'] ? "<br>추가배송비 : +".number_format($sv['op_add_delivery_price'])."원" : "") ;// 추가배송비 여부
// // -- 배송비 ---


$OrderDate = printDateInfo($r['o_rdate'],1);

// 전체 주문통계 부분
$totalOrderProductCnt = 0;


// 함께 주문한 전체 상품
$OtherOrder = _MQ_assoc(" select
		`op`.*,
		`o`.*,
		`op`.`npay_status` as `npay_status`,
		`p`.`p_img_list`, p.p_img_list_square
		from `smart_order_product` as op left join smart_order as o on(o.o_ordernum = op.op_oordernum) left join smart_product as p on(p.p_code = op.op_pcode) where (1) and `op_oordernum` = '{$sv['op_oordernum']}' ".($AdminPath == 'subAdmin'?" and `op_partnerCode` = '{$_COOKIE['AuthCompany']}' ":null)." order by `op_uid` asc ");
?>
<?php if($AdminPath == 'totalAdmin') { // 통합관리자는 수정 가능 ?>
	<form action="_npay_order.pro.php" method="post">
		<input type="hidden" name="_mode" value="modify">
		<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="ordernum" value="<?php echo $r['op_oordernum']; ?>">
<?php } echo PHP_EOL; ?>



	<?php if(count($OtherOrder) > 0) { ?>
		<div class="data_list">

			<table class="table_list type_nocheck">
				<colgroup>
					<col width="70"/><col width="150"><col width="150"/>
					<col width="*"/>
					<col width="120"/><col width="80"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">연동/진행상태</th>
						<th scope="col">주문번호/주문자</th>
						<th scope="col">상품/주문정보</th>
						<th scope="col">결제금액/결제수단</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$tmpOrder = ''; // 주문 번호별 구분 색상 - 주문코드
					$tmpNum = 0; // 주문 번호별 구분 색상 - 주문번호별 넘버
					$totalOrderProduct = array();
					foreach($OtherOrder as $k=>$v) {

						$totalOrderProduct['cnt'] += $v['op_cnt'];
						$totalOrderProduct['price'] += ($v['op_price'] * $v['op_cnt']);
						$totalOrderProduct['delivery'] += $v['op_delivery_price'] + $v['op_add_delivery_price'];

						// // 주문 번호별 구분 색상 - 주문별 배경색 채우기
						if($tmpOrder != $v['op_oordernum']) $tmpNum++;
						if($tmpNum%2 === 0) $TDcolor = ' style="background-color:#'.$DivisionColor.'"';
						else $TDcolor = null;
						$tmpOrder = $v['op_oordernum'];

						// 순서
						$_num = $k+1;

						// 상태아이콘
						$StatusIcon = '';
						if($v['npay_status'] == 'PAYED') $StatusIcon = '<span class="c_tag blue h22 t4">결제완료</span>';
						if($v['npay_status'] == 'PLACE') $StatusIcon = '<span class="c_tag darkgreen h22 t4">발주처리</span>';
						if($v['npay_status'] == 'DISPATCHED') $StatusIcon = '<span class="c_tag green h22 t4">발송처리</span>';
						if($v['npay_status'] == 'CANCELED') $StatusIcon = '<span class="c_tag light h22 t4">주문취소</span>';

						// LDD: 2019-01-18 네이버페이 패치
						if(in_array($v['npay_status'], array('PAYED', 'PLACE', 'DISPATCHED', 'CANCELED')) === false) {
							$StatusIcon = '<span class="c_tag red h22 t4">'.$StatusArray[$v['npay_status']].'</span>';
						}

						$userLink = '/?pn=product.view&code='.$v['op_pcode']; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$v['op_pcode'];
						if( $v['op_ptype'] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$v['op_pcode'];
						}

						// 이미지 체크
						$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
						if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
					?>
						<tr>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php echo (!$v['npay_order_group']?$SyncIcon['이전주문']:$SyncIcon[$v['npay_sync']]); ?>
									<?php echo $StatusIcon; ?>
								</div>
							</td>
							<td>
								<div class="lineup-column type_left">
									<span class="t_blue"><?php echo $v['op_oordernum']; ?></span>
									<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>

									<?php if($v['mobile'] == 'Y') { ?>
									<span class="c_tag h18 mo">MO</span>
									<?php } else { ?>
										<span class="c_tag h18 pc">PC</span>
									<?php } ?>
								</div>
							</td>
							<td>
								<div class="order_item_thumb">
									<div class="thumb">
										<a href="<?php echo $adminLink; ?>" target="_blank">
											<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>" >
										</a>
									</div>
									<div class="order_item">
										<dl>
											<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
												<div class='entershop'><?php echo showCompanyInfo($v['op_partnerCode']); ?></div>
											<?php } ?>
											<dt>
												<div class="item_name">
													<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo htmlspecialchars_decode($v['op_pname']); ?></a>
												</div>
												<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
											</dt>
											<?php echo ($v['op_option1']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option1']).'</dd>':null); ?>
											<?php echo ($v['op_option2']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option2']).'</dd>':null); ?>
											<?php echo ($v['op_option3']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option3']).'</dd>':null); ?>

											<dd class="this_npay">
												<?php if($v['npay_order_group']) { ?>
												<span class="npay_tag">
													N 주문번호 : <?php echo ($v['npay_order_group']?$v['npay_order_group']:'이전주문'); ?>
												</span>
												<?php } ?>
												<span class="npay_tag">
													N 상품주문번호 : <?php echo ($v['npay_order_code']?$v['npay_order_code']:'연동대기'); ?>
												</span>
											</dd>
										</dl>

									</div><!-- end order_item -->
								</div>
							</td>
							<td class="this_date t_red t_right this_price t_bold">
								<div class="lineup-row type_end">
									<?php echo number_format( ($v['op_price'] * $v['op_cnt'])+$v['op_delivery_price'] +$v['op_add_delivery_price']  ); ?>원
									<?php echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]]; ?>
								</div>
							</td>



							<?php if($v['op_uid'] == $_uid) { ?>
								<td class="t_red"<?php echo $TDcolor; ?>>현재상품</td>
							<?php }else{ ?>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_npay_order.form.php?_mode=modify&_uid=<?php echo $v['op_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn gray">상세보기</a>
								</div>
							</td>
							<?php } ?>

						</tr>
					<?php } ?>
				</tbody>
			</table>

		</div>
	<?php } ?>


	<div class="total_price">
		<div class="inner_box">
			<dl>
				<dt>
					<div class="">전체 주문서 <strong class="t_blue">(<?php echo $r['op_oordernum']; ?>)</strong></div>
				</dt>
				<dd>
					<ul>
						<li>
							<em>총 상품개수</em>
							<strong  class="t_black"><?php echo number_format($totalOrderProduct['cnt']); ?>개</strong>
						</li>
						<li>
							<em>총 상품금액</em>
							<strong  class="t_blue"><?php echo number_format($totalOrderProduct['price']); ?>원</strong>
						</li>
						<li>
							<em>총 배송비</em>
							<strong class="t_green"><?php echo $totalOrderProduct['delivery']; ?>원</strong>
						</li>
						<li>
							<em>총 결제금액</em>
							<strong class="t_red"><?php echo number_format($totalOrderProduct['price']+$totalOrderProduct['delivery']) ?>원</strong>
						</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="">현재상품 주문서</div>
					<!-- 아래처럼 아이콘으로 표시하고 소스 삭제 <?php echo $arr_payment_type[$sv['o_paymethod']]; ?> -->
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
							<strong  class="t_black"><?php echo number_format($sv['op_cnt']); ?>개</strong>
						</li>
						<li>
							<em>상품금액</em>
							<strong  class="t_blue"><?php echo number_format($sv['op_price'] * $sv['op_cnt']); ?>원</strong>
						</li>
						<li>
							<em>배송비</em>
							<strong class="t_green"><?php echo number_format($sv['op_delivery_price'] +$sv['op_add_delivery_price']); ?>원</strong>
						</li>
						<li>
							<em>결제금액</em>
							<strong class="t_red"><?php echo number_format( ($sv['op_price'] * $sv['op_cnt'])+$sv['op_delivery_price'] +$sv['op_add_delivery_price']  ); ?>원</strong>
						</li>
					</ul>
				</dd>
			</dl>
		</div>
		<div class="calculate_date">주문일 : <?php echo $OrderDate; ?></div>
		<div class="calculate_date">결제일 : <?php echo printDateInfo($sv['o_rdate'],1) ?></div>
	</div>


	<div class="group_title"><strong>네이버페이 처리</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>네이버페이 관리코드</th>
					<td colspan="3" class="t_green">
						<!--
						<?php if($r['npay_order_group']) { ?>
							<span class="npay_tag">
								<strong style="line-height:16px">N 주문번호</strong><em><input type="text" value="<?php echo ($r['npay_order_group']?$r['npay_order_group']:'이전주문'); ?>" readonly style="width:110px; color: inherit; line-height:16px; text-align: center;" onclick="$(this).select();"></em>
							</span>
						<?php } ?>
						<span class="npay_tag" style="margin-left:5px;">
							<strong style="line-height:16px">N 상품주문번호</strong><em><input type="text" value="<?php echo ($r['npay_order_code']?$r['npay_order_code']:'연동대기'); ?>" readonly style="width:110px; color: inherit; line-height:16px; text-align: center;" onclick="$(this).select();"></em>
						</span>
						-->
						<div class="clear_both"><span class="bold">MallManageCode :</span> <?php echo $r['npay_uniq']; ?></div>
					</td>
				</tr>
				<?php if(in_array($r['npay_status'], array('PAYED', 'PLACE')) || $r['npay_status'] != 'CANCELED') { ?>
					<tr>
						<?php if(in_array($r['npay_status'], array('PAYED', 'PLACE'))) { // 발주처리 or 발주처리 완료 표시 ?>
						<th>발주처리</th>
						<td>
							<?php if($r['npay_status'] == 'PAYED') { ?>
								<a href="/addons/npay/npay.order.pro.php?path=<?php echo $AdminPath; ?>&_mode=PlaceProductOrder&_uid=<?php echo $_uid; ?>&code=<?php echo $r['npay_order_code']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h27 purple">발주처리 하기</a>
							<?php } else { ?>
								<span class="c_tag h27 gray line">발주처리가 완료되었습니다</span>
							<?php } ?>
							<div class="tip_box">
								<div class="c_tip">발주처리 후 복구할 수 없습니다.</div>
								<div class="c_tip">고객이 네이버측에서 취소 및 주소지 변경이 불가하게 됩니다.</div>
							</div>
						</td>
						<?php } // 발주처리 or 발주처리 완료 표시 End ?>
						<?php
						$_expressname = $r['op_sendcompany'];
						$_expressnum = $r['op_sendnum'];
						if($r['npay_status'] != 'CANCELED') { // 취소시 배송처리 항목 보이지 않게 처리
						?>
						<th>배송처리</th>
						<td<?php echo (!in_array($r['npay_status'], array('PAYED', 'PLACE'))?' colspan="3"':null); ?>>
							<div class="lineup-row">
								<?php if(!$_expressname) { ?>
									<?php
										$NpayDeliveryCode = array(
											'CJ대한통운'=>'CJGLS',
											'로젠택배'=>'KGB',
											'KG로지스'=>'DONGBU',
											'우체국택배'=>'EPOST',
											'우편등기'=>'REGISTPOST',
											'한진택배'=>'HANJIN',
											'롯데택배'=>'HYUNDAI',
											'GTX로지스'=>'INNOGIS',
											'대신택배'=>'DAESIN',
											'일양로지스'=>'ILYANG',
											'경동택배'=>'KDEXP',
											'천일택배'=>'CHUNIL',
											'기타 택배'=>'CH1',
											'합동택배'=>'HDEXP',
											'편의점택배'=>'CVSNET',
											'DHL'=>'DHL',
											'FEDEX'=>'FEDEX',
											'GSMNTON'=>'GSMNTON',
											'WarpEx'=>'WARPEX',
											'WIZWA'=>'WIZWA',
											'EMS'=>'EMS',
											'DHL(독일)'=>'DHLDE',
											'ACI'=>'ACIEXPRESS',
											'EZUSA'=>'EZUSA',
											'범한판토스'=>'PANTOS',
											'UPS'=>'UPS',
											'CJ대한통운(국제택배)'=>'KOREXG',
											'TNT'=>'TNT',
											'성원글로벌'=>'SWGEXP',
											'대운글로벌'=>'DAEWOON',
											'USPS'=>'USPS',
											'i-parcel'=>'IPARCEL',
											'건영택배'=>'KUNYOUNG',
											'한의사랑택배'=>'HPL',
											'다드림'=>'DADREAM',
											'SLX택배'=>'SLX',
											'호남택배'=>'HONAM',
											'GSI익스프레스'=>'GSIEXPRESS',
											'직접배송'=>'DIRECT_DELIVERY',
											'방문수령'=>'VISIT_RECEIPT'
										);
										echo _InputSelect( "expressname" , array_keys($NpayDeliveryCode) , $_expressname , "" , "" , "택배사 선택");
									?>
									<input type="text" name="expressnum" class="design" value="<?php echo $_expressnum; ?>" placeholder="송장번호">
									<a href="#none" onclick="NPaySendExpress(); return false;" class="c_btn h27 green">발송처리 요청</a>
									<script type="text/javascript">
										function NPaySendExpress() {
											var expressname = $('select[name^=expressname] option:selected').val();
											var expressnum = $('input[name^=expressnum]').val();
											if(expressname == '') {
												alert('택배사를 선택해주세요.');
												return false;
											}
											if(!confirm('네이버페이주문연동 상품입니다.\n발송처리 후 수정할 수 없습니다. \n\n계속하시겠습니까?')) {
												alert('발송이 취소되었습니다..');
												return false;
											}
											location.href = '/addons/npay/npay.order.pro.php?path=<?php echo $AdminPath; ?>&op_uid=<?php echo $r['op_uid']; ?>&expressname='+expressname+'&expressnum='+expressnum+'&_PVSC=<?php echo $_PVSC; ?>';
										}
									</script>
								<?php } else { ?>
									<a href="<?php echo ($NPayCourier[$r[op_sendcompany]]?$NPayCourier[$r[op_sendcompany]]:$arr_delivery_company[$r[op_sendcompany]]).rm_str($r['op_sendnum']); ?>" class="c_btn h22 green line" target="_blank">배송조회 : <?php echo $_expressname; ?>(<?php echo $_expressnum; ?>)</a>
								<?php } ?>
								</div>
							<?php if(!$_expressname) { ?>
							<div class="tip_box">
								<?php echo _DescStr('네이버페이 정책상 발송처리 후 수정이 불가능 하오니 신중히 발송처리 바랍니다.'); ?>
								<?php echo _DescStr('송장번호 오류시 네이버페이로 문의 하시여 변경 요청 바랍니다.'); ?>
							</div>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
				<?php } ?>
				<?php if($AdminPath == 'totalAdmin' && (in_array($r['npay_status'], array('PAYED', 'PLACE'))) || $r['npay_status'] != 'CANCELED') { // 통합관리자 && 결제완료 혹은 발추처리 ?>
					<tr>
						<?php if($AdminPath == 'totalAdmin' && in_array($r['npay_status'], array('PAYED', 'PLACE'))) { // 통합관리자 && 결제완료 혹은 발추처리 ?>
							<th>네이버페이 주문취소</th>
							<td>
								<a href="#none" onclick="NPaySendCancel(); return false;" class="c_btn h27 red line">주문취소 요청</a>
								<?php echo _DescStr('주문취소는 네이버페이 정책상 <em>결제완료 상태</em>에서만 가능 합니다.'); ?>
								<script type="text/javascript">
									function NPaySendCancel() {
										if(!confirm('취소요청을 하실경우 복구 할 수 없습니다.\n\n계속하시겠습니까?')) return false;
										location.href = '/addons/npay/npay.order.pro.php?path=<?php echo $AdminPath; ?>&_uid=<?php echo $r['op_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>';
									}
								</script>
							</td>
						<?php } ?>
						<?php if($AdminPath == 'totalAdmin' && $r['npay_status'] != 'CANCELED') { // 강제취소 ?>
							<th>강제취소</th>
							<td<?php echo (!in_array($r['npay_status'], array('PAYED', 'PLACE'))?' colspan="3"':null); ?>>
								<a href="#none" onclick="if(confirm('네이버페이 관리자를 통하여 반품 및 환불을 하였을 경우 사용 바랍니다.\n\n계속하시겠습니까?')) document.location.href = '_npay_order.pro.php?_mode=force_cancel&npay_code=<?php echo $r['npay_order_code']; ?>&_PVSC=<?php echo $_PVSC; ?>'; return false;" class="c_btn h27 red">강제취소 처리하기</a>
								<?php echo _DescStr('네이버페이 관리자를 통하여 반품 및 환불을 하였을 경우 사용 바랍니다.'); ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
				<tr>
					<th>포인트 사용</th>
					<td><?php echo number_format($r['npay_point']); ?>원</td>
					<th>적립금 사용</th>
					<td><?php echo number_format($r['npay_point2']); ?>원</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>주문자 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문자명</th>
					<td colspan="3">
						<?php echo showUserInfo($r['o_mid'], $r['o_oname']); ?>
					</td>
				</tr>
				<tr>
					<th>휴대폰번호</th>
					<td><input type="text" name="o_ohp" class="design" value="<?php echo $r['o_ohp']; ?>" placeholder="휴대폰번호" style="width: 110px;"></td>
					<th>이메일 주소</th>
					<td><input type="text" name="o_oemail" class="design" placeholder="이메일 주소" style="width:185px;" value="<?php echo $r['o_oemail']; ?>"></td>
				</tr>
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>받는 분 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>받는 분 이름</th>
					<td><input type="text" name="o_rname" value="<?php echo $r['o_rname']; ?>" class="design" placeholder="받는 분 이름" style="width:100px" <?php echo $AdminPath != 'totalAdmin' ? ' readonly':'' ?>></td>
					<th>휴대폰번호</th>
					<td><input type="text" name="o_rhp" class="design" value="<?php echo $r['o_rhp']; ?>" placeholder="휴대폰번호" style="width: 110px;" <?php echo $AdminPath != 'totalAdmin' ? ' readonly':'' ?>></td>
				</tr>
				<tr>
					<th>배송지 주소</th>
					<td colspan="3">
                        <?php $Zip = explode('-', $r['o_rpost']); ?>
                        <div class="lineup-row">
                            <input type="text" name="_rzonecode" id="_zonecode" value="<?php echo $r['o_rzonecode']; ?>" class="design" style="width:90px" readonly="readonly" onclick="new_post_view_action(); return false;" placeholder="우편번호" <?php echo $AdminPath != 'totalAdmin' ? ' readonly':'' ?>>

                            <?php if($AdminPath == 'totalAdmin'){?>
                            <a href="#none" onclick="new_post_view_action(); return false;" class="c_btn h27 black">주소찾기</a>
	                        <?php }?>
                        </div>

                        <div class="lineup-column type_auto">
                            <input type="text" name="_raddress_doro" id="_addr_doro" value="<?php echo $r['o_raddr_doro']; ?>" class="design" readonly="readonly" placeholder="주소검색" onclick="new_post_view_action(); return false;">
                            <input type="text" name="_raddress1" id="_addr2" class="design" value="<?php echo $r['o_raddr2']; ?>" placeholder="나머지 주소" <?php echo $AdminPath != 'totalAdmin' ? ' readonly':'' ?>>

                            <div class="dash_line"><!-- 점선라인 --></div>
                            <div class="lineup-row type_multi">
                                <span class="fr_tx">지번주소</span>
                                <input type="hidden" name="_rzip1" id="_post1" value="<?php echo $Zip[0]; ?>" class="design t_center" style="width:50px" readonly="readonly">
                                <input type="hidden" name="_rzip2" id="_post2" value="<?php echo $Zip[1]; ?>" class="design t_center" style="width:50px" readonly="readonly">
                                <input type="text" name="_raddress" id="_addr1" class="design" value="<?php echo $r['o_raddr1']; ?>" readonly="readonly" placeholder="지번주소(자동입력)" onclick="new_post_view_action(); return false;">
                            </div>
                        </div>
					</td>
				</tr>
				<tr>
					<th>배송시 유의사항</th>
					<td colspan="3">
						<textarea name="comment" rows="4" cols="" placeholder="배송시 유의사항" class="design"><?php echo htmlspecialchars(stripslashes($r['o_content'])); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>관리용 메모</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>메모 내용</th>
					<td>
						<textarea name="o_admcontent" rows="4" cols="" placeholder="관리자 메모" class="design"><?php echo stripslashes($r['o_admcontent']); ?></textarea>
						<?php echo _DescStr('해당 주문에 대한 관리용 메모가 있는 경우 기재하시고, 사용자에게는 노출되지 않습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


<!-- 상세페이지 버튼 -->
<?php if($AdminPath == 'totalAdmin') { ?>
		<?php echo _submitBTN($app_current_link); ?>
	</form>
<?PHP
} else { // 입점업체의 경우 확인 버튼 제거
	if(strpos($str,'?')===false) $prefix = '?';
	else $prefix = '&';
	$app_pvsc = URI_Rebuild(enc('d' , $_PVSC));
	$app_current_link = $app_current_link.($app_pvsc?$prefix:null).$app_pvsc;
?>
	<div class="c_btnbox">
		<ul>
			<li><a href="<?php echo $app_current_link; ?>" class="c_btn h46 black line" accesskey="l">목록</a></li>
		</ul>
	</div>
	<div class="fixed_save js_fixed_save" style="display: none;">
		<div class="wrapping">
			<div class="c_btnbox">
				<ul>
					<li><a href="<?php echo $app_current_link; ?>" class="c_btn h34 black line" accesskey="l">목록</a></li>
				</ul>
			</div>
		</div>
	</div>
<?php } ?>


<script>
function new_post_view_action(){
	<?php if($AdminPath == 'totalAdmin'){?>
	new_post_view();
	<?php } ?>
}
</script>

<?php
include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_m.php');
include_once('wrap.footer.php');
?>