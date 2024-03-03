<?php
include_once('wrap.header.php');



// 기본 날짜 지정 (7일)
if(!$pass_sdate) $pass_sdate = date('Y-m-d', strtotime('-7 day'));
$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d'));



// 검색 조건
//$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외
$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' and op.op_cancel='N' "; // SSJ : 정산 할인금액 패치 : 2021-05-14 -- 부분취소 제외

if($pass_com){ $pass_company = $pass_com;  }

if($pass_sdate && $pass_edate)  $s_query .= " and (o_rdate between '{$pass_sdate}' and '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."') ";// - 검색기간
else if($pass_sdate) $s_query .= " and o_rdate >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " and o_rdate < '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' ";
if($pass_company) $s_query .= " and op.op_partnerCode = '{$pass_company}' "; // SSJ : 정산 할인금액 패치 : 2021-05-14



// 합계 변수 초기화
$sum_app_cnt = $sum_tPrice = $sum_payPrice = $sum_dPrice = 0;
// SSJ : 정산 할인금액 패치 : 2021-05-14 -- smart_order_company 제거
$que = "
	select
		sum(op_price*op_cnt) as tPrice,
		sum(if(
			op.op_comSaleType='공급가' ,
			op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price  ,
			op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price
		)) as comPrice,
		sum(op.op_cnt) as tCnt,
		sum(op.op_delivery_price + op.op_add_delivery_price) as dPrice,
		date(o.o_rdate) as sub_orderdate,
		count(*) as cnt,
		op_partnerCode
	from smart_order_product as op
	left join smart_order as o on (o.o_ordernum = op.op_oordernum)
	where (1)
		{$s_query}
	group by sub_orderdate, op.op_partnerCode
	order by sub_orderdate, op.op_partnerCode
";
$res = _MQ_assoc($que);



// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();
?>
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
	<input type="hidden" name="mode" value="search">

    <!-- 단락타이틀 -->
    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php
            if($mode == 'search') {
                ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
            <?php } ?>
			<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">도움말</a>
        </div>
    </div>

	<div class="js_thisview" style="display:none">
		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>참고사항</th>
					<td>
						<?php echo _DescStr('매출금액뿐 아니라 발생된 업체 정산 금액 등을 업체별/기간별로 볼 수 있습니다.'); ?>
						<?php echo _DescStr('정산금액(공급가) : (공급가*수량) + 배송비 + 추가배송비', 'blue'); ?>
						<?php echo _DescStr('정산금액(수수료) : ((판매가*수량) - (판매가*수량 *수수료/100)) + 배송비 + 추가배송비', 'blue'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문일</th>
					<td>
                        <div class="lineup-row type_date">
                            <input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly autocomplete="off" placeholder="날짜 선택" />
                            <span class="fr_tx">-</span>
                            <input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly autocomplete="off" placeholder="날짜 선택" />
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


<div class="data_list">
	<div class="list_ctrl">
		<div class="left_box"></div>
		<div class="right_box">
			<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">엑셀다운 (<?php echo count($res); ?>)</a>
		</div>
	</div>
	<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

	<table class="table_must type_ordercalc">
		<thead>
			<tr>
				<th scope="col">판매량</th>
				<th scope="col">구매금액</th>
				<th scope="col">배송비</th>
				<th scope="col">정산금액</th>
			</tr>
		</thead>
		<?php if(count($res) > 0) { ?>
			<tbody>
				<?php
				foreach($res as $sk=>$sv) {

					$sum_app_cnt += $sv['tCnt'];
					$sum_tPrice += $sv['tPrice'];
					$sum_dPrice += $sv['dPrice'];
					$sum_payPrice += $sv['comPrice'];
				?>
					<tr>
						<th class="t_left this_entershop" colspan="4">
							<div class="lineup-row type_side">
								<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
									<div class="entershop"><?php echo showCompanyInfo($sv['op_partnerCode']); ?></div>
								<?php } ?>
								<div class="t_light">주문일 : <?php echo $sv['sub_orderdate']; ?></div>
							</div>
						</th>
					</tr>
					<tr>
						<td class="t_right t_purple t_bold">
							<?php echo number_format($sv['tCnt']); ?>개
						</td>
						<td class="t_right t_black t_bold">
							<?php echo number_format($sv['tPrice']); ?>원
						</td>
						<td class="t_right t_green t_bold">
							<?php echo number_format($sv['dPrice']); ?>원
						</td>
						<td class="t_right t_blue t_bold">
							<?php echo number_format($sv['comPrice']); ?>원
						</td>
					</tr>
				<?php } ?>
			</tbody>
		<?php } ?>
			<tfoot>
				<th class="t_right t_bold count1"><?php echo number_format($sum_app_cnt); ?>개</th>
				<th class="t_right t_bold count2"><?php echo number_format($sum_tPrice); ?>원</th>
				<th class="t_right t_bold count3"><?php echo number_format($sum_dPrice); ?>원</th>
				<th class="t_right t_bold count4"><?php echo number_format($sum_payPrice); ?>원</th>
			</tfoot>
	</table>


	<?php if(count($res) <= 0) { ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>
</div>



<script type="text/javascript">
	function search_excel_send() {
		common_frame.location.href= '_ordercalc.excel.php?pass_sdate=<?php echo $pass_sdate; ?>&pass_edate=<?php echo $pass_edate; ?>&pass_company=<?php echo $pass_company; ?>';
	}
</script>
<?php include_once('wrap.footer.php'); ?>