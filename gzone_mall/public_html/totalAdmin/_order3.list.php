<?php
/**** SSJ : 정산대기관리 메뉴 개선 패치 : 2021-10-01 ****/

include_once('wrap.header.php');

// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	else $_PVS .= "&$key=$val";
}
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기



// 검색 조건 => LCY : 2023-10-05 : 부분취소되지 않은건 추가 (op.op_cancel = 'N')
$s_query = " and op.op_settlementstatus = 'ready' and o.o_paystatus = 'Y' and o.o_canceled = 'N' and o.npay_order = 'N' and op.op_cancel = 'N' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외

if($pass_com){ $pass_company = $pass_com;  }

$date_type = 'op_rdate';
if($pass_date_type == 'odate') $date_type = 'op_rdate'; // 주문일
else if($pass_date_type == 'pdate') $date_type = 'op_paydate'; // 결제일
else if($pass_date_type == 'ddate') $date_type = 'op_senddate'; // 배송완료일
else if($pass_date_type == 'rdate') $date_type = 'op_settlement_reday'; // 정산대기 전환일
if($pass_sdate && $pass_edate)  $s_query .= " AND ({$date_type} between '{$pass_sdate}' and '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."') ";// - 검색기간
else if($pass_sdate) $s_query .= " AND {$date_type} >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND {$date_type} < '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' ";
$DateTypeArr = array(
	'op_rdate'=>'주문일',
	'op_paydate'=>'결제일',
	'op_senddate'=>'배송완료일',
	'op_settlement_reday'=>'정산대기 전환일',
);

if($pass_company) $s_query .= " AND op.op_partnerCode = '{$pass_company}' "; // 공급업체
if($pass_pname) $s_query .= " AND op.op_pname like '%{$pass_pname}%' "; //상품명
if($pass_paymethod) $s_query .= " and o.o_paymethod = '{$pass_paymethod}' ";  // 결제수단

// 페이징 작업
if(!$listmaxcount) $listmaxcount = 10;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


$res = _MQ("
	select count(*) as cnt
	from (
		select
			op_partnerCode
		FROM smart_order_product AS op
		LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
		LEFT JOIN smart_company as cp ON (cp.cp_id = op.op_partnerCode)
		WHERE (1)
			{$s_query}
		GROUP BY op.op_partnerCode
	) as t
");
$TotalCount= $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);

// 데이터 조회
$res = _MQ_assoc("
	select
		cp.cp_charge , cp.cp_tel , cp.cp_tel2 , cp.cp_ceoname ,
		op_partnerCode ,
		sum( op.op_price * op.op_cnt ) as sum_price,
		sum(if(
			op.op_comSaleType='공급가' ,
			op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price  ,
			op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price
		)) as comPrice,
		sum(op.op_delivery_price + op.op_add_delivery_price) as delivery_price,
		sum(op.op_usepoint+op.op_use_discount_price+op.op_use_product_coupon) as use_point,
		sum(op.op_cnt) as total_cnt
	FROM smart_order_product AS op
	LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
	LEFT JOIN smart_company as cp ON (cp.cp_id = op.op_partnerCode)
	WHERE (1)
		{$s_query}
	GROUP BY op.op_partnerCode
	ORDER BY op.op_uid desc
	limit $count , $listmaxcount
");






// -- 전체 데이터 조회 ----
$total_res = _MQ("
	select
		sum( op.op_price * op.op_cnt ) as sum_price,
		sum(if(
			op.op_comSaleType='공급가' ,
			op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price  ,
			op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price
		)) as comPrice,
		sum(op.op_delivery_price + op.op_add_delivery_price) as delivery_price,
		sum(op.op_usepoint+op.op_use_discount_price+op.op_use_product_coupon) as use_point,
		sum(op.op_cnt) as total_cnt
	FROM smart_order_product AS op
	LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
	LEFT JOIN smart_company as cp ON (cp.cp_id = op.op_partnerCode)
	WHERE (1)
		{$s_query}
	ORDER BY NULL
");
// -- 전체 데이터 조회 ----






// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();


?>
<form name="searchfrm" action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
	<input type="hidden" name="mode" value="search">

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php
            if($mode == 'search') {
                ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
            <?php } ?>
			<?php if( $siteInfo['s_product_auto_on'] == 'Y' || $siteInfo['s_ticket_auto_on']=='Y' ) {?>
				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">자동정산 안내</a>
			<?php } ?>
        </div>
    </div>

	<div class="js_thisview"  style="display:none">
		<?php if( $siteInfo['s_product_auto_on'] == 'Y' ) {?>
			<div class="group_title"><strong>배송상품 자동 정산대기</strong></div>
			<div class="tip_table">
				<ul class="thead">
					<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<li class="th"><?php echo $v; ?></li>
					<?php } ?>
				</ul>
				<ul>
					<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<li><?php echo number_format($siteInfo['s_product_auto_'.$k]); ?>일</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		<?php if( $siteInfo['s_ticket_auto_on'] == 'Y' ) {?>
			<div class="group_title"><strong>티켓상품 자동 정산대기</strong></div>
			<div class="tip_table">
				<ul class="thead">
					<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<li class="th"><?php echo $v; ?></li>
					<?php } ?>
				</ul>
				<ul>
					<?php foreach($arr_paymethod_name as $k=>$v) { ?>
						<li><?php echo number_format($siteInfo['s_ticket_auto_'.$k]); ?>일</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		<?php echo _DescStr('정산금액(공급가) : (공급가*수량) + 배송비 + 추가배송비', 'blue'); ?>
		<?php echo _DescStr('정산금액(수수료) : ((판매가*수량) - (판매가*수량 *수수료/100)) + 배송비 + 추가배송비', 'blue'); ?>
		<?php if( $AdminPath == 'totalAdmin'){?>
			<?php echo _DescStr("자동 정산대기 기능 사용 시 배송완료/티켓발급완료 후 설정된 기간이 지나면 자동으로 정산대기로 넘어갑니다."); ?>
			<?php echo _DescStr("상품별 배송처리/티켓발급 목록에서 수동으로 정산대기 처리를 할 수도 있습니다.", ''); ?>
			<div class="dash_line"></div>
			<a href="/totalAdmin/_config.product.form.php" class="c_btn sky line">자동 정산대기 설정 바로가기</a>
		<?php } ?>
	</div>

    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>상품명</th>
					<td><input type="text" name="pass_pname" class="design" value="<?php echo $pass_pname; ?>" placeholder="상품명" /></td>
					<th>결제수단</th>
					<td>
						<?php echo _InputSelect("pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , '결제수단 전체'); ?>
					</td>
				</tr>
				<tr>
					<th>검색기간</th>
					<td>
                        <div class="lineup-row">
                            <select name="pass_date_type">
                                <option value="odate"<?php echo ($pass_date_type == 'odate'?' selected':null); ?>>주문일</option>
                                <option value="pdate"<?php echo ($pass_date_type == 'pdate'?' selected':null); ?>>결제일</option>
                                <option value="ddate"<?php echo ($pass_date_type == 'ddate'?' selected':null); ?>>배송완료일</option>
                                <option value="rdate"<?php echo ($pass_date_type == 'rdate'?' selected':null); ?>>정산대기 전환일</option>
                            </select>
                            <div class="lineup-row type_date">
                                <input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly autocomplete="off" placeholder="날짜 선택" />
                                <span class="fr_tx">-</span>
                                <input type="text" name="pass_edate" class="design js_pic_day_max_today right" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly autocomplete="off" placeholder="날짜 선택" />
                            </div>
                        </div>
					</td>
					<th>입점업체</th>
					<td>
						<?php
						// ----- JJC : 입점관리 : 2020-09-17 -----
						if($SubAdminMode === true ) { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer2 = arr_company2();
						?>

							<?php if( $AdminPath == 'totalAdmin'){?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
							<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '입점업체 선택'); ?>
							<?php }else{ ?>
								<?php echo $arr_customer2[$pass_com]; ?>
							<?php } ?>
						<?php }else{?>
							<?php echo _DescStr('입점업체 미사용 <a href="https://www.onedaynet.co.kr/p/solution_plus.html#page_entershop" target="_blank"><em>신청하기</em></a>',''); ?>
						<?php }?>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"/></span></li>
				<?php
				if($mode == 'search') {
				?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form><!-- end data_search -->


<?php
	// 총합계
	$total_sale_cnt = $total_res['total_cnt']*1;
	$total_sum_price = $total_res['sum_price']*1;
	$total_delivery_price = $total_res['delivery_price']*1;
	$total_com_price = $total_res['comPrice']*1;
	$total_use_point = $total_res['use_point']*1;
	$total_fee_price = $total_sum_price + $total_delivery_price - $total_com_price - $total_use_point;;
?>

<div class="data_search">
	<div class="group_title"><strong>전체 정산대기 통계</strong></div>
	<div class="table_accounts">
		<dl>
			<dt>판매 수</dt>
			<dd><?php echo number_format($total_sale_cnt); ?></span>개</dd>
		</dl>
		<dl>
			<dt>판매 금액</dt>
			<dd><?php echo number_format($total_sum_price); ?>원</dd>
		</dl>
		<dl>
			<dt>배송비</dt>
			<dd class="t_green"><?php echo number_format($total_delivery_price); ?>원</dd>
		</dl>
		<!--
		<dl>
			<dt>총 합계</dt>
			<dd class="t_black"><?php echo number_format($total_sum_price + $total_delivery_price); ?>원</dd>
		</dl>
		-->
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
	</div>
</div>


<!-- ● 데이터 리스트 -->
<form class="form_list" action="_order_product.pro.php" method="post" target="common_frame">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_search_cnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_s_query" value="<?php echo enc('e', $s_query); ?>">
	<div class="data_list">

		<div class="list_ctrl">
			<div class="left_box">
				<?php if( $AdminPath == 'totalAdmin'){?>
				<a href="#none" onclick="AllChecked('active');" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="AllChecked('inactive');" class="c_btn gray line">선택해제</a>
				<a href="#none" onclick="settlement_status(); return false;" class="c_btn h27 blue">선택 정산완료처리</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<?php if( $AdminPath == 'totalAdmin'){ ?>
				<a href="#none" onclick="saveExcel('_order3.excel.php'); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<?php } ?>
				<a href="#none" onclick="searchExcel('_order3.excel.php'); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운 (<?php echo number_format($TotalCount); ?>)</a>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<?php if(count($res) > 0) { ?>
			<table class="table_list">
				<colgroup>
					<?php if( $AdminPath == 'totalAdmin' ){ ?>
					<col width="40"/>
					<col width="70"/>
					<?php } ?>
					<col width="200"/>
					<col width="*"/>
					<col width="100"/>
					<col width="70"/>
				</colgroup>
				<thead>
					<tr>
						<?php if( $AdminPath == 'totalAdmin'){?>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<?php } ?>
						<th scope="col">입점업체</th>
						<th scope="col">정산내역</th>
						<th scope="col">판매수</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($res as $k=>$v) {
							$_num = $TotalCount - $count - $k ;

							$sale_cnt = $v['total_cnt'];
							$sum_price = $v['sum_price'];
							$delivery_price = $v['delivery_price'];
							$com_price = $v['comPrice'];
							$use_point = $v['use_point'];
							$fee_price = $sum_price + $delivery_price - $com_price - $use_point;
					?>
							<tr>
								<?php if( $AdminPath == 'totalAdmin'){?>
								<td class="this_check">
									<label class="design"><input type="checkbox" name="chk_id[<?php echo $v['op_partnerCode']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td>
									<div class="lineup-column type_left">
										<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
											<div class="entershop"><?php echo showCompanyInfo($v['op_partnerCode']); ?></div>
										<?php } ?>
										<div class="t_black">대표자 : <?php echo $v['cp_ceoname']; ?></div>
										<div class="t_black">대표전화 : <?php echo $v['cp_tel']; ?></div>
										<?php
											//$arr_tel = array_filter(array($v['cp_charge'] , $v['cp_tel'] , $v['cp_tel2']));
											//echo (sizeof($arr_tel) > 0 ? "" . implode(" / " , $arr_tel) . "" : "");
										?>
									</div>
								</td>
								<?php }else{ ?>
								<td><?php echo $arr_customer2[$v['op_partnerCode']]; ?> <span class="t_light">(<?php echo $v['op_partnerCode']; ?>)</span></td>
								<?php } ?>
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
								<td class="this_state t_black">
									<span class="hidden_tx">판매수</span><?php echo number_format($sale_cnt); ?>개
								</td>
								<td class="this_ctrl">
									<div class="lineup-row type_center">
										<a href="_order3.view.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_id'=>$v['op_partnerCode'], '_s_query'=>enc('e', $s_query), '_PVSC'=>$_PVSC)); ?>" class="c_btn gray" target="">상세보기</a>
										<a href="_order3.view.excel.php<?php echo URI_Rebuild('?', array('_mode'=>'listexcel', '_id'=>$v['op_partnerCode'], '_s_query'=>enc('e', $s_query), '_PVSC'=>$_PVSC)); ?>" class="c_btn green line only_pc_view" target="">엑셀다운</a>
									</div>
								</td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }else{ ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>
	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>


</form>




<script type="text/javascript">

	// 선택정산완료
	function settlement_status() {
		if($('input:checkbox[name^=chk_id]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', '_order_product.pro.php');
		$('.form_list').find('input[name=_mode]').val('settlementstatus_complete');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 엑셀저장
	function saveExcel(fileTemp) {
		if($('input:checkbox[name^=chk_id]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').find('input[name=_mode]').val('select_excel');
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 엑셀저장
	function searchExcel(fileTemp) {
		var search_cnt = $('input[name=_search_cnt]').val()*1;
		if(search_cnt <= 0) {
			alert('검색된 내역이 없습니다.');
			return false;
		}
		$('.form_list').find('input[name=_mode]').val('search_excel');
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 전체선택/해제
	function AllChecked(_mode) {
		$('.js_ck').prop('checked', false);
		$('.js_AllCK').prop('checked', false);
		if(_mode == 'active'){
			$('.js_AllCK').prop('checked', true);
			$('.js_ck').prop('checked', true);
		}
	}

</script>
<?php include_once('wrap.footer.php'); ?>