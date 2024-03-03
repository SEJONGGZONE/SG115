<?php
/**** SSJ : 정산대기관리 메뉴 개선 패치 : 2021-10-01 ****/

// --------- JJC : 정산기능분화 : 2021-01-19 ---------

$app_current_name = "정산대기 상세내역";

@ini_set('memory_limit', '-1');
$app_current_link = '_order3.list.php';
include_once('wrap.header.php');

if($AdminPath == 'subAdmin' && $pass_com != $_id){
	error_msg('잘못된 접근입니다.');
}

if($_s_query == '' || $_id == ''){
	error_msg('잘못된 접근입니다.');
}
$s_query = enc('d', $_s_query);
$s_query .= " and o.o_canceled = 'N' "; // 취소 주문 제외
// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;

$res = _MQ(" select count(*) as cnt from smart_order_product AS op left join smart_order AS o ON (o.o_ordernum=op.op_oordernum) where (1) {$s_query} and op.op_partnerCode = '". addslashes($_id) ."' ");
$TotalCount = $res['cnt'];

$res = _MQ_assoc("
	select
		(op.op_price * op.op_cnt) as sum_price,
		IF(
			op.op_comSaleType = '공급가',
			(op.op_supply_price * op.op_cnt) + op.op_delivery_price + op.op_add_delivery_price ,
			(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission / 100) + op.op_delivery_price + op.op_add_delivery_price
		) as comPrice,
		(op.op_delivery_price + op.op_add_delivery_price) as delivery_price,
		op.op_usepoint+op.op_use_discount_price+op.op_use_product_coupon as use_point,
		op.op_cnt as total_cnt,
		o_rdate , op_uid, op_pname , op_option1, op_option2, op_option3 , op_oordernum, o_mid, o_oname,
		p.p_type,p.p_img_list_square,p.p_code
	FROM smart_order_product AS op
	LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
	LEFT JOIN smart_product as p on (op.op_pcode=p.p_code)
	WHERE (1)
		{$s_query}
		and op.op_partnerCode = '". addslashes($_id) ."'
	order by op.op_uid desc
");

// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();

$cprow = _MQ("SELECT * FROM smart_company WHERE cp_id = '". addslashes($_id) ."'  ");

?>


<div class="data_search">
	<table class="table_form">
		<colgroup>
			<col width="130"/><col width="*"/><col width="130"/><col width="*"/><col width="130"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th>입점업체명</th>
				<td>
					<a href="_entershop.form.php?_mode=modify&_id=<?php echo $cprow['cp_id']; ?>" target="_blank"><?php echo $cprow['cp_name']; ?> <span class="t_light">(<?php echo $cprow['cp_id']; ?>)</span></a>
				</td>
				<th>대표자</th>
				<td>
					<?php echo trim($cprow['cp_ceoname']); ?>
				</td>
				<th>대표전화</th>
				<td>
					<?php echo tel_format($cprow['cp_tel']); ?>
				</td>
			</tr>
			<tr>
				<th>담당자명</th>
				<td>
					<?php echo trim($cprow['cp_charge']); ?>
				</td>
				<th>담당자 휴대폰</th>
				<td>
					<?php echo tel_format($cprow['cp_tel2']); ?>
				</td>
				<th>담당자 이메일</th>
				<td>
					<?php echo ($cprow['cp_email']); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>


<!-- ● 데이터 리스트 -->
<form class="form_list" action="_order_product.pro.php" method="post" target="common_frame">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_id" value="<?php echo $_id; ?>">
	<input type="hidden" name="_s_query" value="<?php echo $_s_query; ?>">
	<div class="data_list">

		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="AllChecked('active');" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="AllChecked('inactive');" class="c_btn gray line">선택해제</a>
				<?php if( $AdminPath == 'totalAdmin'){?>
				<a href="#none" onclick="settlement_status(); return false;" class="c_btn h27 blue">선택 정산완료처리</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<a href="#none" onclick="saveExcel('_order3.view.excel.php'); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운 </a>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<?php if(count($res) > 0) { ?>
			<table class="table_list">
				<colgroup>
					<col width="40"/>
					<col width="70"/>
					<col width="100"/>
					<col width="150"/>
					<col width="250"/>
					<col width="*"/>
					<col width="100"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">주문일</th>
						<th scope="col">주문코드</th>
						<th scope="col">상품정보</th>
						<th scope="col">정산내역</th>
						<th scope="col">판매수</th>
					</tr>
				</thead>
				<tbody>
					<?php
						// 총합계
						$total_sale_cnt = 0;
						$total_sum_price = 0;
						$total_delivery_price = 0;
						$total_com_price = 0;
						$total_use_point = 0;
						$total_fee_price = 0;
						foreach($res as $k=>$v) {
							$sale_cnt = $v['total_cnt'];
							$sum_price = $v['sum_price'];
							$delivery_price = $v['delivery_price'];
							$com_price = $v['comPrice'];
							$use_point = $v['use_point'];
							$fee_price = $sum_price  + $delivery_price - $com_price - $use_point ;

							// 총합계
							$total_sale_cnt += $sale_cnt;
							$total_sum_price += $sum_price;
							$total_delivery_price += $delivery_price;
							$total_com_price += $com_price;
							$total_use_point += $use_point;
							$total_fee_price += $fee_price;

							// KAY :: 2022-12-07 :: 옵션 구분 수정
							$arr_option_name = array($v['op_option1'] ,$v['op_option2'],$v['op_option3'] );
							$arr_option_name = array_filter($arr_option_name);

							$_num = $TotalCount - $count - $k ;


							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
							if($_p_img == '') $_p_img = 'images/thumb_no.jpg'; // SSJ : 썸네일 체크 변경 : 2021-02-17


							if($v['p_type']=='ticket'){
								$plink = '/totalAdmin/_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
							}else{
								$plink = '/totalAdmin/_product.form.php?_mode=modify&_code='.$v['p_code'].'';
							}
					?>
							<tr>
								<td class="this_check">
									<label class="design"><input type="checkbox" name="OpUid[]" class="js_ck" value="<?php echo $v['op_uid']; ?>"></label>
								</td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td class="this_date">
									<?php echo printDateInfo($v['o_rdate']); ?>
								</td>
								<td class="t_left">
									<div class="lineup-column type_left">
										<div class="t_blue"><?php echo $v['op_oordernum']; ?></div>
										<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
									</div>
								</td>
								<td>
									<div class="order_item_thumb type_simple">
										<div class="thumb">
											<a href="<?php echo $plink;?>" target="_blank" title="<?php echo $pname;?>"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['op_pname'])); ?>" ></a>
										</div>
										<div class="order_item">
											<dl>
												<dt>
													<div class="item_name"><a href="<?php echo $plink; ?>" target="_blank"><?php echo $v['op_pname']; ?></a></div>
												</dt>
												<?php if(count($arr_option_name)>0){?>
													<dd><?php echo implode(" / ",$arr_option_name); ?></dd>
												<?php }?>
											</dl>
										</div><!-- end order_item -->
									</div>
								</td>
								<td>

									<div class="table_accounts">
										<dl>
											<dt>판매 금액</dt>
											<dd><?php echo number_format($sum_price); ?>원</dd>
										</dl>
										<dl>
											<dt>배송비</dt>
											<dd class="t_green"><?php echo number_format($delivery_price); ?>원</dd>
										</dl>
										<dl>
											<dt>할인금액</dt>
											<dd class="t_orange"><?php echo number_format($use_point); ?>원</dd>
										</dl>
										<dl>
											<dt>판매 수수료</dt>
											<dd class="t_sky"><?php echo number_format($fee_price); ?>원</dd>
										</dl>
										<dl>
											<dt>정산금액</dt>
											<dd class="t_blue"><?php echo number_format($com_price); ?>원</dd>
										</dl>
										<!--
										<dl>
											<dt>총 합계</dt>
											<dd class="t_red"><?php echo number_format($sum_price + $delivery_price); ?>원</dd>
										</dl>
										-->
									</div>
								</td>
								<td class="this_ctrl t_black"><span class="hidden_tx">판매수</span><?php echo number_format($sale_cnt); ?>개</td>
							</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" class="title">합계</th>
						<th>
							<div class="table_accounts">
								<dl>
									<dt>판매 금액</dt>
									<dd><?php echo number_format($total_sum_price); ?>원</dd>
								</dl>
								<dl>
									<dt>배송비</dt>
									<dd class="t_green"><?php echo number_format($total_delivery_price); ?>원</dd>
								</dl>
								<dl>
									<dt>할인금액</dt>
									<dd class="t_orange"><?php echo number_format($total_use_point); ?>원</dd>
								</dl>
								<dl>
									<dt>판매 수수료</dt>
									<dd class="t_sky"><?php echo number_format($total_fee_price); ?>원</dd>
								</dl>
								<dl>
									<dt>정산금액</dt>
									<dd class="t_blue"><?php echo number_format($total_com_price); ?>원</dd>
								</dl>
								<!--
								<dl>
									<dt>총 합계</dt>
									<dd class="t_red"><?php echo number_format($total_sum_price + $total_delivery_price); ?>원</dd>
								</dl>
								-->
							</div>
						</th>
						<th class="this_ctrl t_black t_bold"><?php echo number_format($total_sale_cnt); ?>개</th>
					</tr>
				</tfoot>
			</table>
		<?php }else{ ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>
	</div>
</form>




<div class="c_btnbox type_full">
	<ul>
		<li><a href="_order3.list.php?<?php echo enc('d', $_PVSC); ?>" class="c_btn h46 black line" accesskey="l">목록</a></li>
	</ul>
</div>

<div class="fixed_save js_fixed_save" style="display: block;">
	<div class="wrapping">
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="_order3.list.php?<?php echo enc('d', $_PVSC); ?>" class="c_btn h34 black line" accesskey="l">목록</a></li>
			</ul>
		</div>
	</div>
</div>



<script type="text/javascript">

	// 전체선택/해제
	function AllChecked(_mode) {
		$('.js_ck').prop('checked', false);
		$('.js_AllCK').prop('checked', false);
		if(_mode == 'active'){
			$('.js_AllCK').prop('checked', true);
			$('.js_ck').prop('checked', true);
		}
	}

	// 선택정산완료
	function settlement_status() {
		if($('input:checkbox[name^=OpUid]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', '_order_product.pro.php');
		$('.form_list').find('input[name=_mode]').val('settlementstatus_complete');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 엑셀저장
	function saveExcel(fileTemp) {
		if($('input:checkbox[name^=OpUid]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

</script>
<?php include_once('wrap.footer.php'); ?>