<?php
include_once('wrap.header.php');


// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	else $_PVS .= "&$key=$val";
}
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기



// 기본 날짜 지정 (7일)
if(!$pass_sdate) $pass_sdate = date('Y-m-d', strtotime('-7 day'));
$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d'));


if($pass_com){ $pass_company = $pass_com; }

// 검색 조건
$s_query = '';
if($pass_sdate && $pass_edate) $s_query .= " and date(s_date) between '{$pass_sdate}' and '{$pass_edate}' "; // - 검색기간
else if($pass_sdate) $s_query .= " and date(s_date) >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " and date(s_date) <= '{$pass_edate}' ";
if($pass_company) $s_query .= " and s_partnerCode = '{$pass_company}' "; // 공급업체


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 's_date';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order_settle_complete where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		*, (s_count+s_count_vat_n) as orderby_count, (s_com_price+s_com_price_vat_n) as orderby_price
	from
		smart_order_settle_complete
	where (1)
		{$s_query}
	order by {$st} {$so}
	limit {$count}, {$listmaxcount}
");


// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();
?>
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
	<input type="hidden" name="mode" value="search">

    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php
            if($mode == 'search') {
                ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
            <?php } ?>
        </div>
    </div>

    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>검색기간</th>
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


<form class="form_list" method="post" target="">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>

	<div class="data_list">

		<div class="list_ctrl">
			<div class="left_box">
				<?php if( $AdminPath == 'totalAdmin'){ ?>
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<a href="#none" onclick="settlement_status_ready(); return false;" class="c_btn h27 blue">정산대기 전환</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'s_date', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 's_date' && $so == 'desc'?' selected':null); ?>>정산일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'s_date', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 's_date' && $so == 'asc'?' selected':null); ?>>정산일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'orderby_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'orderby_price' && $so == 'desc'?' selected':null); ?>>정산금액 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'orderby_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'orderby_price' && $so == 'asc'?' selected':null); ?>>정산금액 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'orderby_count', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'orderby_count' && $so == 'desc'?' selected':null); ?>>판매건수 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'orderby_count', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'orderby_count' && $so == 'asc'?' selected':null); ?>>판매건수 ▲</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<table class="table_list">
			<colgroup>
				<col width="40">
				<col width="60">
				<?php if( $AdminPath == 'totalAdmin' && $SubAdminMode === true){ ?>
				<col width="170">
				<?php } ?>
				<col width="*">
				<col width="80">
				<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
					<col width="90">
				<?php } ?>
				<col width="100">
				<col width="80">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<?php if( $AdminPath == 'totalAdmin' && $SubAdminMode === true){ ?>
					<th scope="col">입점업체</th>
					<?php } ?>
					<th scope="col">정산내역</th>
					<th scope="col">정산건수</th>
					<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
						<th scope="col">세금계산서</th>
					<?php } ?>
					<th scope="col">정산일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php foreach($res as $k=>$v) { ?>
						<?php $_num = $TotalCount - $count - $k ;?>
						<tr>
							<td class="this_check">
								<label class="design"><input type="checkbox" name="_uid[]" class="js_ck" value="<?php echo $v['s_uid']; ?>"></label>
							</td>
							<td class="this_num"><?php echo $_num; ?></td>
							<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
							<td>
								<div class="entershop"><?php echo showCompanyInfo($v['s_partnerCode']); ?></div>
							</td>
							<?php } ?>
							<td>
								<div class="table_accounts">
									<dl>
										<dt>판매금액</dt>
										<dd class="t_black"><?php echo number_format($v['s_price']+$v['s_price_vat_n']); ?>원</dd>
									</dl>
									<dl>
										<dt>배송비</dt>
										<dd class="t_green"><?php echo number_format($v['s_delivery_price']+$v['s_delivery_price_vat_n']); ?>원</dd>
									</dl>
									<dl>
										<dt>할인금액</dt>
										<dd class="t_orange"><?php echo number_format($v['s_usepoint']+$v['s_usepoint_vat_n']); ?>원</dd>
									</dl>
									<dl>
										<dt>판매 수수료</dt>
										<dd class="t_sky"><?php echo number_format($v['s_discount']+$v['s_discount_vat_n']); ?>원</dd>
									</dl>
									<dl>
										<dt>정산금액</dt>
										<dd class="t_blue"><?php echo number_format($v['s_com_price']+$v['s_com_price_vat_n']); ?>원</dd>
									</dl>
								</div>

							</td>
							<td class="t_bold t_black">
								<span class="hidden_tx">판매개수</span><?php echo number_format($v['s_count']+$v['s_count_vat_n']); ?>개
							</td>
							<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
								<td class="this_state">
									<div class="lineup-row type_center">
										<?php
											switch($v['s_tax_status']){
												case 1000 :echo '<span class="c_tag h22 blue line t4">임시저장</span>'; break;
												case 2010 : case 2011 :echo '<span class="c_tag h22 black line t4">발행중</span>'; break;
												case 4012 :echo '<span class="c_tag h22 red line t4">발행거부</span>'; break;
												case 3014 : case 3011 : echo '<span class="c_tag blue line t4">발행완료</span>'; break;
												case 5013 : case 5031 : echo '<span class="c_tag gray line t4">발행취소</span>'; break;
												default : echo '<span class="c_tag h22 light t4">미발행</span>'; break;
											}
										?>
									</div>
								</td>
							<?php } ?>
							<td class="this_date">
								<?php echo printDateInfo($v['s_date']); ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_order4.view.php<?php echo URI_Rebuild('?', array('suid'=>$v['s_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray">상세보기</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>


		<?php if(count($res) <= 0) { ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</div>
</form>

<script type="text/javascript">

	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 항목을 1건 이상 선택 바랍니다.');
		$('.form_list').prop('action', '_order4.excel.php');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val('');
		$('.form_list').prop('action', '');
	}

	function search_excel_send() {
		$('.form_list').prop('action', '_order4.excel.php');
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val('');
		$('.form_list').prop('action', '');
	}
	// 선택정산대기전환
	function settlement_status_ready() {
		if($('input:checkbox[name^=_uid]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		if(confirm('선택한 항목을 정산대기 상태로 전환하시겠습니까?')){
			$('.form_list').prop('action', '_order4.pro.php');
			$('.form_list').find('input[name=_mode]').val('settlementstatus_ready');
			$('.form_list').submit();
			$('.form_list').find('input[name=_mode]').val('');
			$('.form_list').prop('action', '');
		}
	}
</script>
<?php include_once('wrap.footer.php'); ?>