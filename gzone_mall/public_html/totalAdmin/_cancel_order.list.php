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

// 전체검색 필터
if( !$pass_input_type) $pass_input_type = 'all';

// 검색 체크
$s_query = " and o_canceled != 'N' and o_moneyback_status != 'none' ";


// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " o_ordernum like '%".$pass_input_value."%'  "; } // 주문번호
	if( in_array($pass_input_type, array('all','pass_oname')) > 0 ){ $arr_input_que[] = " o_oname like '%".$pass_input_value."%'  "; } // 주문자명
	if( in_array($pass_input_type, array('all','pass_mid')) > 0 ){ $arr_input_que[] = " o_mid like '%".$pass_input_value."%'  "; } // 주문자 ID
	if( in_array($pass_input_type, array('all','pass_ohp')) > 0 && rm_str($pass_input_value) != '' ){ $arr_input_que[] = " replace(o_ohp,'-','') like '%".rm_str($pass_input_value)."%'  "; } // 주문자 휴대폰
	if( in_array($pass_input_type, array('all','pass_deposit')) > 0 ){ $arr_input_que[] = " o_deposit like '%".$pass_input_value."%'  "; } // 입금자명
	if( in_array($pass_input_type, array('all','pass_pname')) > 0 ){
		$arr_input_que[] = " (select count(*) as cnt from smart_order_product where op_oordernum = o.o_ordernum and op_pname like '%".$pass_input_value."%' ) > 0  ";
	} // 주문 상품명
	if( in_array($pass_input_type, array('all','pass_pcode')) > 0 ){
		$arr_input_que[] = " (select count(*) as cnt from smart_order_product where op_oordernum = o.o_ordernum and op_pcode like '%".$pass_input_value."%' ) > 0  ";
	} // 주문상품 코드
	if( in_array($pass_input_type, array('all','pass_optionname')) > 0 ){
		$arr_input_que[] = " (select count(*) as cnt from smart_order_product where op_oordernum = o.o_ordernum and concat(ifnull(op_option1,''),ifnull(op_option2,''),ifnull(op_option3,'')) like '%".$pass_input_value."%' ) > 0  ";
	} // 주문상품 옵션명
	if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
}
// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. }

if( count($pass_paymethod) > 0){ $s_query .= " and (o_paymethod in('".implode("','",$pass_paymethod)."') or o_easypay_paymethod_type in('".implode("','",$pass_paymethod)."')  )  "; } // 결제수단
if($pass_moneyback_status) $s_query .= " and o_moneyback_status = '{$pass_moneyback_status}' "; // 환불상태
if($pass_moneyback_comment) $s_query .= " AND o_moneyback_comment like '%{$pass_moneyback_comment}%' "; // 환불 계좌정보

// 날짜검색 - 환불요청일
if( $pass_sdate !="" ) { $s_query .= " and date(o_moneyback_date) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(o_moneyback_date) <='". $pass_edate ."' "; }

// 날짜검색 - 환불처리일
if( $pass_com_sdate !="" ) { $s_query .= " and date(o_moneyback_comdate) >='".$pass_com_sdate."' "; }
if( $pass_com_edate !="" ) { $s_query .= " and date(o_moneyback_comdate) <='". $pass_com_edate ."' "; }

// 입점업체
if($pass_com) {
	$s_query .= "
		and (
			SELECT
				count(*)
			FROM smart_order_product as op
			WHERE
				op_oordernum = o_ordernum AND
				op_partnerCode = '". addslashes($pass_com) ."'
		) > 0
	";
}

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = $date_type;
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order as o where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
if(!$st) $st = 'o_moneyback_date';
if(!$so) $so = 'desc';
$que = "
	select
		o.*,
		(select count(*) from smart_order_product as op where op.op_oordernum = o.o_ordernum) as op_cnt,
		(
			select
				concat(op.op_pname, '|' , op.op_partnerCode, '|', op.op_pcode,'|' ,op.op_ptype ,'|',p.p_img_list_square) /* JJC : 입점관리 : 2020-09-17 */
			from
				smart_order_product as op
				left join smart_product as p on (p.p_code=op.op_pcode)
			where
				op.op_oordernum = o.o_ordernum
			order by op.op_uid asc limit 1
		) as p_info
	from
		smart_order as o
	where (1)
		{$s_query}
	order by {$st} {$so} limit {$count}, {$listmaxcount}
";
$res = _MQ_assoc($que);
?>


<form name="searchfrm" action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="mode" value="search">


	<?php // 통합검색 ?>
	<div class="comp_search">
		<div class="form_wrap">
			<select name="pass_input_type">
				<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
				<option value="pass_ordernum" <?php echo $pass_input_type == 'pass_ordernum' ? 'selected' : ''?>>주문번호</option>
				<option value="" disabled>----------</option>
				<option value="pass_oname" <?php echo $pass_input_type == 'pass_oname' ? 'selected' : ''?>>주문자 이름</option>
				<option value="pass_mid" <?php echo $pass_input_type == 'pass_mid' ? 'selected' : ''?>>주문자 ID</option>
				<option value="pass_ohp" <?php echo $pass_input_type == 'pass_ohp' ? 'selected' : ''?>>주문자 휴대폰</option>
				<option value="" disabled>----------</option>
				<option value="pass_pname" <?php echo $pass_input_type == 'pass_pname' ? 'selected' : ''?>>상품명</option>
				<option value="pass_pcode" <?php echo $pass_input_type == 'pass_pcode' ? 'selected' : ''?>>상품코드</option>
				<option value="pass_optionname" <?php echo $pass_input_type == 'pass_optionname' ? 'selected' : ''?>>옵션명</option>
			</select>
			<div class="search_input">
				<input type="search" name="pass_input_value" class="design" value="<?=$pass_input_value?>" placeholder="검색어" />
				<input type="submit" name="" value="" accesskey="s" class="btn_search" title="검색" />
				<?php
					if($mode == 'search' && $pass_input_value != '' ){
						$arr_param = array_filter(array_merge($arr_param,array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)));
				?>
					<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="line btn_reset" title="검색초기화"></a>
				<?php } ?>
			</div>
			<a href="#none" class="c_btn sky line js_onoff_event type_open" data-target=".data_search" data-add="if_open_comp"><strong>상세검색</strong></a>
		</div><!-- end form_wrap -->
	</div><!-- end comp_search -->


    <div class="search_form comp_search_open">
		<div class="group_title">
			<strong>Search</strong>
			<div class="btn_box">
				<?php // 체크 : if_open 붙여진 상태로 캐시저장, 캐시저장 후  checked 상태유지, 체크된 상태에서는 상세검색 닫아도 새로고침하면 다시 열려진 상태임 ?>
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey ?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>환불 요청일</th>
					  <td colspan="5">
						<div class="lineup-row type_date">
							<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<?php // 버튼클릭하면 해당날짜가 날짜 인풋에 입력(바로검색아님) ?>
							<div class="lineup-row type_days">
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['today']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['today']['edate'] ?>">오늘</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['1week']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1week']['edate'] ?>">1주일</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['1month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1month']['edate'] ?>">1개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['3month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['3month']['edate'] ?>">3개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['6month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['6month']['edate'] ?>">6개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sdate" data-ename="pass_edate" data-sdate="<?php echo $arrTodayToDate['1year']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1year']['edate'] ?>">1년</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>환불 처리일</th>
					  <td colspan="5">
						<div class="lineup-row type_date">
							<input type="text" name="pass_com_sdate" value="<?php echo $pass_com_sdate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_com_edate" value="<?php echo $pass_com_edate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<?php // 버튼클릭하면 해당날짜가 날짜 인풋에 입력(바로검색아님) ?>
							<div class="lineup-row type_days">
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['today']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['today']['edate'] ?>">오늘</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['1week']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1week']['edate'] ?>">1주일</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['1month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1month']['edate'] ?>">1개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['3month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['3month']['edate'] ?>">3개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['6month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['6month']['edate'] ?>">6개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_com_sdate" data-ename="pass_com_edate" data-sdate="<?php echo $arrTodayToDate['1year']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1year']['edate'] ?>">1년</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>결제수단</th>
					<td colspan="5">
						<?php echo _InputCheckbox( "pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , ''); ?>
					</td>
				</tr>
				<tr>
					<th>환불상태</th>
					<td>
						<?php echo _InputRadio('pass_moneyback_status', array('', 'request', 'complete'), $pass_moneyback_status, '', array('전체', '환불요청', '환불완료')); ?>
					</td>
					<th>환불 계좌정보</th>
					<td>
						<input type="text" name="pass_moneyback_comment" class="design" value="<?php echo $pass_moneyback_comment; ?>" placeholder="은행명/계좌번호/예금주명" />
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
				<tr>
					<th>참고사항</th>
					<td colspan="5">
						<?php echo _DescStr('환불요청은 현금성 주문(무통장입금, 계좌이체, 가상계좌 등)을 한번에 취소 한 경우입니다.',''); ?>
						<?php echo _DescStr('환불요청 가능한 단계 : 결제완료','red'); ?>
						<?php echo _DescStr('환불완료 처리 후 주문취소까지 하면 수정이 불가합니다.',''); ?>
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

    <!-- 공통 통합검색 스크립트 -->
    <script>
    	// 공통:  검색상세열기 상태 저장
    	$(document).on('click','.js_common_search_detail',function(e){
    		var checked = $(this).prop('checked') == true ? 'Y':'N';
    		var name = $(this).attr('name');
    		$.ajax({url:'_pro.php',data:{_mode:'set_search_detail_cache_cookie',checked : checked , name : name},type:'get', dataType:'json'});
    	});

    	// 공통: 날짜선택 이벤트
    	$(document).on('click','.js_date_auto_set',function(){
    		var thisData= $(this).data();
    		// 아무 데이터도 없을 시 처리
    		if( typeof thisData.sname == 'undefined' || !thisData.sname){ thisData.sname = ''; }
    		if( typeof thisData.sdate == 'undefined' || !thisData.sdate){ thisData.sdate = ''; }
    		if( typeof thisData.ename == 'undefined' || !thisData.ename){ thisData.ename = ''; }
    		if( typeof thisData.edate == 'undefined' || !thisData.edate){ thisData.edate = ''; }
    		if( thisData.sname != '' && thisData.sdate != ''){ $('form [name="'+thisData.sname+'"]').val(thisData.sdate); }
    		if( thisData.ename != '' && thisData.edate != ''){ $('form [name="'+thisData.ename+'"]').val(thisData.edate); }
    		return false;
    	});

    	// 시작 공통 이벤트
    	$(document).ready(function(e){

    	});
    </script>


</form><!-- end data_search -->



<!-- ● 데이터 리스트 -->
<form name="frm" class="form_list" action="_cancel_order.pro.php" method="post">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<input type="hidden" name="_submode" value="_cancel_order">
	<div class="data_list">


		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<a href="#none" onclick="mass_complete(); return false;" class="c_btn h27 blue">선택 환불완료</a>
				<a href="#none" onclick="selectCancel(); return false;" class="c_btn h27 black">선택 주문취소</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_moneyback_date', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_moneyback_date' && $so == 'desc'?' selected':null); ?>>요청일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_moneyback_date', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_moneyback_date' && $so == 'asc'?' selected':null); ?>>요청일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_moneyback_comdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_moneyback_comdate' && $so == 'desc'?' selected':null); ?>>처리일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_moneyback_comdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_moneyback_comdate' && $so == 'asc'?' selected':null); ?>>처리일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_price_real', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_price_real' && $so == 'desc'?' selected':null); ?>>환불금액 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_price_real', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_price_real' && $so == 'asc'?' selected':null); ?>>환불금액 ▲</option>
				</select>
				<select onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>


		<table class="table_list">
			<colgroup>
				<col width="45"/><col width="70"/><col width="90"/><col width="150"/>
				<col width="*"/>
				<col width="120"/><col width="100"/><col width="100"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">환불상태</th>
					<th scope="col">주문번호/주문자명</th>
					<th scope="col">상품정보</th>
					<th scope="col">환불금액/결제수단</th>
					<th scope="col">요청일</th>
					<th scope="col">처리일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count-$k;

						# 모바일 구매
						if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';
						else $device_icon = '<span class="c_tag h18 t3 pc">PC</span>';

						$app_pinfo = explode("|",$v['pinfo']); // [2] => op_pcode , [3] => op_ptype
						$userLink = '/?pn=product.view&code='.$app_pinfo[0]; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$app_pinfo[0];
						if( $app_pinfo[1] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$app_pinfo[0];
						}

						// --- JJC : 입점관리 : 2020-09-17 ---
						$app_pname = explode('|', $v['p_info']);

						// 이미지 체크
						$_p_img = get_img_src('thumbs_s_'.$app_pname[4]);
						if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

					?>
						<tr>
							<td class="this_check">
								<label class="design"><input type="checkbox" name="chk_ordernum[<?php echo $v['o_ordernum']; ?>]" class="class_ordernum js_ck" value="Y"></label>
							</td>
							<td class="this_num">
								<?php echo number_format($_num); ?>
							</td>
							<td class="this_state">
								<div class="lineup-center">
									<?php if($v['o_moneyback_status'] == 'request') { ?>
										<span class="c_tag h22 black t5">환불요청</span>
									<?php } else { ?>
										<?php if($v['o_moneyback_status'] == 'complete') { ?>
											<span class="c_tag h22 blue line">환불완료</span>
										<?php } ?>
									<?php } ?>
								</div>
							</td>
							<td>
								<div class="lineup-column type_left">
									<span class="t_blue"><?php echo $v['o_ordernum']; ?></span>
									<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
								</div>
							</td>
							<td>
								<div class="order_item_thumb">
									<div class="thumb">
										<a href="<?php echo $adminLink; ?>" title="<?php echo addslashes($app_pname[0]); ?>" target="_blank"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes($app_pname[0]); ?>"></a>
									</div>
									<div class="order_item">
										<dl>
											<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
												<div class="entershop"><?php echo showCompanyInfo($app_pname[1]); ?></div>
											<?php } ?>
											<dt>
												<div class="item_name">
													<?php
														// --- JJC : 입점관리 : 2020-09-17 ---
														$app_pname = explode('|', $v['p_info']);
														echo '<a href="'.$adminLink.'" target="_blank">'.$app_pname[0].'</a>	';
														// --- JJC : 입점관리 : 2020-09-17 ---
													?>
												</div>
												<?php if($v['op_cnt'] > 1) { ?>
													<div class="mount"> 외 <?php echo number_format(($v['op_cnt']-1)); ?>개</div>
												<?php } ?>
											</dt>
										</dl>
										<ul class="user_apply">
											<li>
												주문자 휴대폰 : <?php if($v['o_otel'] || $v['o_ohp']) { ?><?php echo implode(" , " , array_filter(array(trim($v['o_otel']) , trim($v['o_ohp'])))); ?><?php } ?>
											</li>
											<li class="user_bank">
												환불계좌 : <?php echo str_replace('환불계좌: ', '', $v['o_moneyback_comment']); ?>
											</li>
										</ul>
									</div><!-- end order_item -->
								</div><!-- end order_item_thumb -->
								<div class="order_item_tag">
									<?php echo $device_icon; ?>
									<?php echo $arr_adm_button[$v['o_status']]; ?>
								</div>
							</td>
							<td class="t_right t_bold t_red this_price">
								<div class="lineup-row type_end">
									<?php if($v['o_price_real'] > 0) { ?>
										<?php echo number_format($v['o_price_real']); ?>원
									<?php } else { ?>
										<span class="c_tag h22 sky t5">전액적립금</span>
									<?php } ?>
									<?php
                                        // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                        if( $v['o_easypay_paymethod_type'] != ''){
                                            echo $arr_adm_button["E".$arr_available_easypay_pg_list[$v['o_easypay_paymethod_type']]];
                                        }else{
                                            echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]];
                                        }
                                    ?>
								</div>
							</td>
							<td>
								<span class="hidden_tx">요청일</span>
									<?php echo ($v['o_moneyback_date'] == '0000-00-00 00:00:00'?'-':printDateInfo($v['o_moneyback_date'])); ?>
							</td>
							<td>
								<span class="hidden_tx">처리일</span>
										<?php echo ($v['o_moneyback_comdate'] == '0000-00-00 00:00:00'?'<span class="t_none">처리전</span>':printDateInfo($v['o_moneyback_comdate'])); ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_order.form.php?_mode=modify&_ordernum=<?php echo $v['o_ordernum']; ?>&view=cancel_order&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 gray">상세보기</a>
									<?php if($v['o_moneyback_status'] == 'request') { ?>
										<a href="_cancel_order.pro.php?_mode=complete&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('정말 실행하시겠습니까?')) return false;" class="c_btn h22 blue ">완료처리</a>
										<a href="_cancel_order.pro.php?_mode=reset&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('정말 실행하시겠습니까?')) return false;" class="c_btn h22 black line">요청취소</a>
									<?php } else if($v['o_canceled']=='R'){ ?>
										<a href="_cancel_order.pro.php?_mode=request&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('환불완료를 다시 요청으로 변경하시겠습니까?')) return false;" class="c_btn h22">요청전환</a>
										<a href="#none" onclick="cancel('_order.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'cancel', '_ordernum'=>$v['o_ordernum'], '_submode'=>'_cancel_order', '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 black">주문취소</a>
									<?php }else{ ?>
										<?php echo $arr_adm_button['취소완료']; ?>
										<a href="#none" class="c_btn light line" onclick="alert('주문취소 처리되어 환불 상태 전환이 불가합니다.'); return false;">전환불가</a>
									<?php } ?>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>

		<?php if(count($res) <= 0) { ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">접수된 내용이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</div>
</form>


<script type="text/javascript">
	// 선택 엑셀 다운로드
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('select_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('search_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 선택 환불완료 처리
	function mass_complete() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('1건 이상 선택 바랍니다.');
		if(confirm('정말 선택하신 주문의 환불요청을 완료처리하겠습니까?')) {
			$('.form_list').find('input[name=_mode]').val('mass');
			$('.form_list').submit();
			$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		}
	}


	 function selectCancel() {
		 if($('.js_ck').is(':checked')){
			 if(confirm('정말 취소하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_cancel');
				$('form[name=frm]').attr('action' , '_order.pro.php');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

</script>
<?php include_once('wrap.footer.php'); ?>