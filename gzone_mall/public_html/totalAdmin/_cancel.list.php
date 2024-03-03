<?php
include_once('wrap.header.php');

// 검색 체크
$s_query = " and o.npay_order = 'N' AND op.op_is_addoption = 'N'"; // JJC : 2023-01-13 : 추가옵션 부분취소


// 전체검색 필터
if( !$pass_input_type) $pass_input_type = 'all';

// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " o_ordernum like '%".$pass_input_value."%'  "; } // 주문번호
	if( in_array($pass_input_type, array('all','pass_oname')) > 0 ){ $arr_input_que[] = " o_oname like '%".$pass_input_value."%'  "; } // 주문자명
	if( in_array($pass_input_type, array('all','pass_mid')) > 0 ){ $arr_input_que[] = " o_mid like '%".$pass_input_value."%'  "; } // 주문자 ID
	if( in_array($pass_input_type, array('all','pass_ohp')) > 0 && rm_str($pass_input_value) != '' ){ $arr_input_que[] = " replace(o_ohp,'-','') like '%".rm_str($pass_input_value)."%'  "; } // 주문자 휴대폰
	if( in_array($pass_input_type, array('all','pass_pname')) > 0 ){
		$arr_input_que[] = " op_pname like '%".$pass_input_value."%'  ";
	} // 주문 상품명
	if( in_array($pass_input_type, array('all','pass_pcode')) > 0 ){
		$arr_input_que[] = " op_pcode like '%".$pass_input_value."%'   ";
	} // 주문상품 코드
	if( in_array($pass_input_type, array('all','pass_optionname')) > 0 ){
		$arr_input_que[] = " concat(ifnull(op_option1,''),ifnull(op_option2,''),ifnull(op_option3,'')) like '%".$pass_input_value."%'  ";
	} // 주문상품 옵션명
	if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
}
// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. }

// 환불수단 처리
if($pass_cancel_type == 'pg'){ $s_query .= " and o_paymethod in('".implode("','",$arr_cancel_part_payment_type)."') and op_cancel_type = 'pg'  ";  } // PG연동
else if($pass_cancel_type == 'refund'){ $s_query .= " and o_paymethod in('".implode("','",$arr_refund_payment_type)."') and op_cancel_type = 'pg'  ";  } // 계좌입금
else if($pass_cancel_type == 'point'){ $s_query .= " and op_cancel_type = 'point'  "; }


// 취소요청 상태
if($pass_cancel) $s_query .= " AND op.op_cancel = '{$pass_cancel}' ";
else $s_query .= " AND op.op_cancel in ('Y','R') ";

if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; } // 회원구분
if( count($pass_paymethod) > 0){ $s_query .= " and (o_paymethod in('".implode("','",$pass_paymethod)."') or o_easypay_paymethod_type in('".implode("','",$pass_paymethod)."')  )  "; } // 결제수단

// 날짜검색 - 취소요청일
if( $pass_sdate !="" ) { $s_query .= " and date(op_cancel_rdate) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(op_cancel_rdate) <='". $pass_edate ."' "; }

// 날짜검색 - 취소완료일
if( $pass_cancel_sdate !="" ) { $s_query .= " and date(op_cancel_cdate) >='".$pass_cancel_sdate."' "; }
if( $pass_cancel_edate !="" ) { $s_query .= " and date(op_cancel_cdate) <='". $pass_cancel_edate ."' "; }

// 주문기기
if( $pass_mobile == 'Y'){ $s_query .= " and mobile in('Y','A')  "; }
else if($pass_mobile == 'N') { $s_query .= " and mobile = 'N'  "; }

if($pass_com) $s_query .= " and op.op_partnerCode = '{$pass_com}' "; // 입점업체

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'op.op_cancel_rdate';
if(!$so) $so = 'desc';
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order_product as op left join smart_order as o on (o.o_ordernum = op.op_oordernum) where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$que = "
	select
		* ,
		o.o_otel as ordertel,
		o.o_ohp as orderhtel
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum = op.op_oordernum) left join
		smart_product as p on (p.p_code = op.op_pcode)
	where (1)
		{$s_query}
	order by {$st} {$so}
	limit {$count}, {$listmaxcount}
";
$res = _MQ_assoc($que);


// --- JJC : 2023-01-13 : 추가옵션 부분취소 ---
//		추가옵션을 제외한 일반 옵션으로 정상 주문 1개인지 확인
if(sizeof($res) > 0 ) {
	$arr_opuid = $arr_ordernum = $arr_add = array();
	foreach( $res as $k=>$v) {$arr_opuid[$v['op_pouid']]++; $arr_ordernum[$v['op_oordernum']]++; }

	if(sizeof($arr_opuid) > 0 ) {
		$aores = _MQ_assoc("
			SELECT
				op_oordernum , op_pcode , op_addoption_parent , op_uid , concat(op_option1,' ',op_option2) as option_name , op_price , op_cnt
			FROM smart_order_product
			WHERE (1)
				AND op_oordernum IN ('". implode("' , '" , array_keys($arr_ordernum)) ."')
				AND op_addoption_parent IN ('". implode("' , '" , array_keys($arr_opuid)) ."')
				AND op_cancel != 'N'
				AND op_is_addoption = 'Y'
		");
		foreach( $aores as $k=>$v) {
			$arr_add[$v['op_oordernum']][$v['op_pcode']][$v['op_addoption_parent']][$v['op_uid']] = $v;
		}
	}
}
// --- JJC : 2023-01-13 : 추가옵션 부분취소 ---


?>


<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
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
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>취소 요청일</th>
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
					<th>취소 완료일</th>
					  <td colspan="5">
						<div class="lineup-row type_date">
							<input type="text" name="pass_cancel_sdate" value="<?php echo $pass_cancel_sdate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_cancel_edate" value="<?php echo $pass_cancel_edate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<?php // 버튼클릭하면 해당날짜가 날짜 인풋에 입력(바로검색아님) ?>
							<div class="lineup-row type_days">
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['today']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['today']['edate'] ?>">오늘</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['1week']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1week']['edate'] ?>">1주일</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['1month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1month']['edate'] ?>">1개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['3month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['3month']['edate'] ?>">3개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['6month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['6month']['edate'] ?>">6개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_cancel_sdate" data-ename="pass_cancel_edate" data-sdate="<?php echo $arrTodayToDate['1year']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1year']['edate'] ?>">1년</a>
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
					<th>취소상태</th>
					<td>
						<?php echo _InputRadio('pass_cancel', array('', 'R', 'Y'), $pass_cancel, '', array('전체', '취소요청', '취소완료'), ''); ?>
					</td>
					<th>환불수단</th>
					<td colspan="3">
						<?php echo _InputRadio( "pass_cancel_type" , array('','pg','refund','point'), $pass_cancel_type , "" , array('전체','PG연동','계좌입금','적립금환불')); ?>
					</td>

				</tr>
				<tr>
					<th>회원구분</th>
					<td>
						<?php echo _InputRadio( "pass_memtype" , array('','Y','N'), $pass_memtype , "" , array('전체','회원','비회원')); ?>
					</td>
					<th>주문기기</th>
					<td>
						<?php echo _InputRadio( "pass_mobile" , array('','N','Y'), $pass_mobile , "" , array('전체','PC','모바일') , ''); ?>
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
						<?php echo _DescStr('부분취소는 주문한 상품들 중에 개별적으로 취소한 경우입니다.',''); ?>
						<?php echo _DescStr('부분취소 가능한 단계 : 결제완료 / 배송준비 / 배송대기','red'); ?>
						<?php echo _DescStr('배송중 또는 배송완료 주문이 부분취소 목록에 있는 경우는 교환/반품 신청목록에서 PG연동 또는 적립금 환불로 처리한 경우입니다.',''); ?>
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
<div class="data_list">
	<form action="_cancel.pro.php" method="post" class="form_list">
		<input type="hidden" name="_mode" value="get_search_excel">
		<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">

		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<?php if($AdminPath == 'totalAdmin'){?>
				<a href="#none" onclick="mass_cancel_del(); return false;" class="c_btn h27 black line ">선택 요청삭제</a>
				<a href="#none" onclick="mass_cancel(); return false;" class="c_btn h27 blue ">선택 취소처리</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_rdate' && $so == 'desc'?' selected':null); ?>>요청일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_rdate' && $so == 'asc'?' selected':null); ?>>요청일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_cdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_cdate' && $so == 'desc'?' selected':null); ?>>취소일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_cdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_cdate' && $so == 'asc'?' selected':null); ?>>취소일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_price' && $so == 'desc'?' selected':null); ?>>환불금액 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_cancel_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'op_cancel_price' && $so == 'asc'?' selected':null); ?>>환불금액 ▲</option>
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
				<col width="40"><col width="70"><col width="160"><col width="150">
				<col width="*">
				<col width="120"><col width="90"><col width="90"><col width="80">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">취소상태/환불수단</th>
					<th scope="col">주문번호/주문자</th>
					<th scope="col">상품정보</th>
					<th scope="col">환불금액/결제수단</th>
					<th scope="col">요청일</th>
					<th scope="col">취소완료일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count -$k;
						$v['op_cancel_price'] = $v['op_price'] * $v['op_cnt'] + $v['op_delivery_price'] + $v['op_add_delivery_price'] - $v['op_cancel_discount_price'] - $v['op_usepoint'] ;// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC // - JJC : 2022-12-21 : 부분취소개선 -

						# 모바일 구매
						if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';
						else $device_icon = '<span class="c_tag h18 t3 pc">PC</span>';

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
							<td class="this_check">
								<label class="design">
									<input type="checkbox" name="OpUid[]" class="js_ck <?php echo $v['op_cancel']=='Y'?'if_cancel':'';?>" value="<?php echo $v['op_uid']; ?>">
									<input type="hidden" name="op_uid" value="<?php echo $v['op_uid'];?>">
								</label>
							</td>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php echo ($v['op_cancel'] == 'R' && $v['o_canceled'] != 'Y'?'<span class="c_tag h22 black t4">취소요청</span>':'<span class="c_tag line blue t4">취소완료</span>'); ?>

									<?php
										if( $v['op_cancel_type'] == 'pg'){
											if( in_array($v['o_paymethod'], $arr_cancel_part_payment_type) > 0){ echo '<span class="c_tag green line t5">PG연동</span>'; } // PG연동
											else if(in_array($v['o_paymethod'], $arr_refund_payment_type) > 0){ echo '<span class="c_tag yellow line t5">계좌입금</span>';} // 계좌입금
										}
										else if($v['op_cancel_type'] == 'point'){
											echo '<span class="c_tag purple line t5">적립금환불</span>';
										}
									?>


									<!--<span class="c_tag green line t5">PG연동</span>-->
								</div>
							</td>
							<td>
								<div class="lineup-column type_left">
									<span class="t_blue"><?php echo $v['op_oordernum']; ?></span>
									<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
								</div>
							</td>
							<td class="t_left">
								<div class="order_item_thumb">
									<div class="thumb">
										<a href="<?php echo $adminLink; ?>" title="<?php echo addslashes($v['op_pname']); ?>" target="_blank"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes($v['op_pname']); ?>"></a>
									</div>
									<div class="order_item">
										<dl>
											<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
												<div class="entershop"><?php echo showCompanyInfo($v['op_partnerCode']); ?></div>
											<?php } ?>
											<dt>
												<div class="item_name"><a href="<?php echo $adminLink; ?>" target="_blank"><?php echo $v['op_pname']; ?></a></div>

												<?php if($v['op_option1'] || $v['op_option2'] || $v['op_option3']) { ?>
													<dd>
														<div class="option_name">
															<?php echo ($v['op_is_addoption']=="Y" ? "<span class='add_option'>추가옵션</span> : " : "<span class='option'>필수옵션</span>" )."".trim($v['op_option1']." ".$v['op_option2']." ".$v['op_option3']); ?>
														</div>
														<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
													</dd>
												<?php } else { ?>
													<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
												<?php } ?>
											</dt>

											<?php if(sizeof($arr_add[$v['op_oordernum']][$v['op_pcode']][$v['op_pouid']]) > 0 ) { // --- JJC : 2023-01-13 : 추가옵션 부분취소 --- ?>
												<?php foreach( $arr_add[$v['op_oordernum']][$v['op_pcode']][$v['op_pouid']] as $sk=>$sv) {?>
													<dd>
														<div class="option_name">
															<?php echo "<span class='add_option'>추가옵션</span>".trim($sv['option_name']); ?>
														</div>
														<span class="mount"><?php echo number_format($sv['op_cnt']); ?>개</span>
													</dd>
													<?php $v['op_cancel_price'] += $sv['op_price'] * $sv['op_cnt']; ?>
												<?php } ?>
											<?php } // --- JJC : 2023-01-13 : 추가옵션 부분취소 --- ?>
											<input type="hidden" name="ordernum" value="<?php echo $v['op_oordernum']; ?>" />
										</dl>
										<ul class="user_apply">
											<li class="user_info">
												<strong>주문자 휴대폰 : </strong><?php echo ($v['orderhtel']?$v['orderhtel']:'<span class="t_none">미입력</span>'); ?>
											</li>
											<?php if($v['op_cancel_bank_account'] <> '' || $v['op_cancel_bank_name'] <> '' || $v['op_cancel_msg'] <> ''){ ?>
												<?php if($v['op_cancel_type'] == 'pg' && in_array($v['o_paymethod'], array('online', 'virtual'))){ ?>
												<li class="user_bank">
													<strong>환불계좌 : </strong>
													<?php echo $ksnet_bank[$v['op_cancel_bank']]; ?> <?php echo $v['op_cancel_bank_account']; ?> (<?php echo $v['op_cancel_bank_name']; ?>)
												</li>
												<?php } ?>
												<?php if($v['op_cancel_msg'] <> ''){ ?>
												<li class="user_comment">
													<strong>요청내용 : </strong>
													<?php echo nl2br(htmlspecialchars($v['op_cancel_msg'])); ?>
												</li>
												<?php } ?>
											<?php } ?>
										</ul>
									</div><!-- end order_item -->
								</div><!-- end order_item_thumb -->
								<div class="order_item_tag">
									<?php echo $device_icon; ?>
									<?php echo $arr_adm_button[$v['op_sendstatus']]; ?>
								</div>
							</td>
							<td class="t_right t_red t_bold this_price">
								<div class="lineup-row type_end">
									<?php if($v['op_cancel_price'] > 0) { ?>
										<div class="t_red"><?php echo number_format($v['op_cancel_price']); ?>원</div>
										<?php
											// - JJC : 2022-12-21 : 부분취소개선 -
											if($v['op_usepoint'] > 0 ) {
												echo '<div class="t_sky">적립금 : ' . number_format($v['op_usepoint']) . '원</div>';
											}
										?>
									<?php } else { ?>
										<div class="t_sky t_bold">전액적립금</div>
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
								<span class="hidden_tx">요청일</span><?php echo printDateInfo($v['op_cancel_rdate']); ?>
							</td>
							<td>
								<span class="hidden_tx">취소일</span><?php echo ($v['op_cancel_cdate'] != '0000-00-00 00:00:00'?''.printDateInfo($v['op_cancel_cdate']):($v['o_canceldate'] != '0000-00-00 00:00:00'?''.printDateInfo($v['o_canceldate']):'<span class="t_none">취소전</span>')); ?>
								<?php if($v['op_cancel'] != 'Y' && $v['o_canceled'] != 'Y' && $AdminPath == 'totalAdmin'){ ?>
									<div class="lineup-row type_center">
										<a href="#none" onclick="order_cancel(); return false;"  class="c_btn h22 blue js_order_cancel" data-ordernum="<?php echo $v['op_oordernum'];?>" data-opuid="<?php echo $v['op_uid'];?> ">취소처리	</a>
										<a href="#none" onclick="order_cancel_del(); return false;"  class="c_btn h22 black line js_order_cancel_del" data-ordernum="<?php echo $v['op_oordernum'];?>" data-opuid="<?php echo $v['op_uid'];?> ">요청삭제</a>
									</div>
								<?php } ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_cancel.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_ordernum'=>$v['op_oordernum'], 'uid'=>$v['op_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 gray">상세보기</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>
	</form>

	<?php if(count($res) <= 0) { ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">접수된 내용이 없습니다.</div></div>
	<?php } ?>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>
</div>


<script type="text/javascript">
	// 선택 엑셀 다운로드
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
	}

	// 선택 주문 취소
	function mass_cancel() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0){
			alert('1개 이상 선택해주세요.');
			return false;
		}else{
			if(confirm('선택한 항목을 취소처리를 하며 되돌릴 수 없습니다. \n그래도 취소처리 하시겠습니까?')){
				$('.form_list').find('input[name=_mode]').val('mass');
				$('.form_list').submit();
			}else{
				return false;
			}
		}
	}


	 function order_cancel() {
		var ordernum = $('.js_order_cancel').data('ordernum');
		var opuid = $('.js_order_cancel').data('opuid');

		 if(confirm('한번 취소하면 이전으로 되돌릴 수 없습니다. \n그래도 취소처리 하시겠습니까?')){
			 $('.form_list').find('input[name=ordernum]').val(ordernum);
			$('.form_list').find('input[name=op_uid]').val(opuid);
			$('.form_list').find('input[name=_mode]').val('cancel');
			$('.form_list').attr('action' , '_cancel.pro.php');
			$('.form_list').submit();
		 }
	 }

	// 개별 요청 삭제
	function order_cancel_del() {
		var ordernum = $('.js_order_cancel_del').data('ordernum');
		var opuid = $('.js_order_cancel_del').data('opuid');

		 if(confirm('삭제 후에는 복구가 불가하며, 다시 주문으로 남게 됩니다. \n그래도 삭제하시겠습니까?')){
			 $('.form_list').find('input[name=ordernum]').val(ordernum);
			$('.form_list').find('input[name=op_uid]').val(opuid);
			$('.form_list').find('input[name=_mode]').val('req_cancel');
			$('.form_list').attr('action' , '_cancel.pro.php');
			$('.form_list').submit();
		 }
	}

	// 선택 주문 삭제
	function mass_cancel_del() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0){
			alert('1개 이상 선택해주세요.');
			return false;
		}else{
			if(confirm('삭제 후에는 복구가 불가하며, 다시 주문으로 남게 됩니다. \n그래도 삭제하시겠습니까?')){
				$('.form_list').find('input[name=_mode]').val('mass_del');
				$('.form_list').submit();
			}else{
				return false;
			}
		}
	}


</script>
<?php include_once('wrap.footer.php'); ?>