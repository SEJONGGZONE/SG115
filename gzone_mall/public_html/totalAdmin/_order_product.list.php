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

// 검색 조건
$s_query = " and o.o_canceled = 'N' and o.o_paystatus = 'Y' and npay_order = 'N' and op.op_cancel = 'N' and op_ptype = 'delivery' /*티켓제외*/ "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외

// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " op_oordernum like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_sendnum')) > 0 ){ $arr_input_que[] = " op_sendnum like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_oname')) > 0 ){ $arr_input_que[] = " o_oname like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_mid')) > 0 ){ $arr_input_que[] = " o_mid like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_ohp')) > 0 && rm_str($pass_input_value) != '' ){ $arr_input_que[] = " replace(o_ohp,'-','') like '%".rm_str($pass_input_value)."%'  "; }

	if( in_array($pass_input_type, array('all','pass_pname')) > 0 ){
		$arr_input_que[] = " op_pname like '%".$pass_input_value."%'  ";
	}
	if( in_array($pass_input_type, array('all','pass_pcode')) > 0 ){
		$arr_input_que[] = " op_pcode like '%".$pass_input_value."%'  ";
	}
	if( in_array($pass_input_type, array('all','pass_optionname')) > 0 ){
		$arr_input_que[] = " concat(ifnull(op_option1,''),ifnull(op_option2,''),ifnull(op_option3,'')) like '%".$pass_input_value."%' ";
	}

	if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
}
// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. }

if($pass_settlement) $s_query .= " and op.op_settlementstatus='{$pass_settlement}' "; // 정산상태
if($pass_sendstatus) $s_query .= " and op.op_sendstatus='{$pass_sendstatus}' "; // 배송상태
if($pass_sendcompany) $s_query .= " and op.op_sendcompany='{$pass_sendcompany}' "; // 택배사
if($pass_memtype) $s_query .= " and o.o_memtype = '{$pass_memtype}' "; // 회원구분

// 주문기기
if( $pass_mobile == 'Y'){ $s_query .= " and mobile in('Y','A')  "; }
else if($pass_mobile == 'N') { $s_query .= " and mobile = 'N'  "; }


// ----- JJC : 입점관리 : 2020-09-17 -----
if($pass_com) {

	// LCY : 2022-02-21 : 입점업체 검색기능 강화 -- 배송주문상품관리에서는 해당 업체의 주문상품 건만 조회되도록 --
	$s_query .= " and  op_partnerCode = '".addslashes($pass_com)."' ";
}

// 받는분
if( $pass_rname !="" ) { $s_query .= " and o_rname like '%".$pass_rname."%' "; }
if( rm_str($pass_rhp) !="" ) { $s_query .= " and replace(o_rhp,'-','') like '%".rm_str($pass_rhp)."%' "; }
if( $pass_address !="" ) { $s_query .= " and concat(ifnull(o_raddr_doro,''),ifnull(o_raddr2,'')) like '%".$pass_address."%'  "; }

// 날짜검색
if( $pass_sdate !="" ) { $s_query .= " and date(o_rdate) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(o_rdate) <='". $pass_edate ."' "; }

if($pass_vat) $s_query .= " and op.op_vat = '{$pass_vat}' "; // 과세여부


	// ----- JJC : 입점관리 : 2020-09-17 -----


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'o_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ("
	select
		count(*) as cnt
	from
		smart_order_product as op inner join
		smart_order as o on (o.o_ordernum=op.op_oordernum) left join
		smart_product as p on (p.p_code=op.op_pcode)
	where (1)
		{$s_query}
");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		op.*, o.*, p.p_name, p.p_img_list,p.p_img_list_square
	from
		smart_order_product as op inner join
		smart_order as o on (o.o_ordernum=op.op_oordernum) left join
		smart_product as p on (p.p_code=op.op_pcode)
	where (1)
		{$s_query}
	order by {$st} {$so} limit {$count}, {$listmaxcount}
");
?>



<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="mode" value="search">

	<?php // 통합검색 ?>
	<div class="comp_search">
		<div class="form_wrap">
			<select name="pass_input_type">
				<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
				<option value="pass_ordernum" <?php echo $pass_input_type == 'pass_ordernum' ? 'selected' : ''?>>주문번호</option>
				<option value="pass_sendnum" <?php echo $pass_input_type == 'pass_sendnum' ? 'selected' : ''?>>송장번호</option>
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
		<div class="ctrl_wrap only_pc_view">
			<a href="#none" class="c_btn h46 red line js_open_excel_box">일괄 업로드</a>
		</div>
	</div><!-- end comp_search -->
	<div class="mobile_tip">엑셀 일괄업로드는 PC에서 가능합니다.</div>

	<div class="search_form comp_search_open">
		<div class="group_title">
			<strong>Search</strong>
			<div class="btn_box">
				<?php // 체크 : if_open 붙여진 상태로 캐시저장, 캐시저장 후  checked 상태유지, 체크된 상태에서는 상세검색 닫아도 새로고침하면 다시 열려진 상태임 ?>
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey; ?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문일</th>
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
					<th>정산상태</th>
					<td colspan="5">
						<?php echo _InputRadio('pass_settlement', array('', 'none', 'ready', 'complete'), $pass_settlement, '', array('전체', '정산미정', '정산대기', '정산완료'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>배송상태</th>
					<td colspan="3">
						<?php echo _InputRadio('pass_sendstatus', array('', '배송대기', '배송준비', '배송중', '배송완료'), $pass_sendstatus, '', array('전체', '배송대기', '배송준비', '배송중', '배송완료'), ''); ?>
					</td>
					<th>택배사</th>
					<td>
						<?php echo _InputSelect('pass_sendcompany', array_keys($arr_delivery_company), $pass_sendcompany, '', '', ''); ?>
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
					<th>받는분 이름</th>
					<td>
						<input type="text" name="pass_rname" class="design" style="width:100px;" value="<?php echo $pass_rname; ?>" placeholder="받는분 이름">
					</td>
					<th>받는분 휴대폰</th>
					<td>
						<input type="text" name="pass_rhp" class="design" style="width:100px;" value="<?php echo $pass_rhp; ?>" placeholder="받는분 휴대폰">
					</td>
					<th>받는분 주소</th>
					<td>
						<input type="text" name="pass_address" class="design" style="width:150px" value="<?=$pass_address?>" placeholder="받는분 주소" />
					</td>
				</tr>
				<?php if($siteInfo['s_vat_product'] == 'C') { ?>
					<tr>
						<th>과세상품</th>
						<td colspan="5">
							<?php echo _InputRadio('pass_vat', array('', 'Y', 'N'), $pass_vat, '', array('전체', '과세상품', '면세상품'), ''); ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>참고사항</th>
					<td colspan="5">
						<div class="tip_box">
						<?php
							if($AdminPath == 'totalAdmin'){
								 echo _DescStr('네이버페이 주문정보는 별도 관리되며 정산처리가 불가능합니다. 네이버주문은 네이버페이 센터를 이용바랍니다.','red');
							}
						?>
						<?php echo _DescStr('배송상태를 <em>배송준비</em>상태로 변경하면 사용자의 직접 전체 주문취소가 불가합니다. (부분취소는 요청가능)'); ?>
						</div>
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


</form>



<form action="_order_product.excel_form.php" method="post" enctype="multipart/form-data" class="data_search js_excel_box" style="display: none;">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>일괄업로드</th>
				<td>
					<div class="lineup-row">
						<div class="input_file" style="width:300px">
							<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
							<div class="fileDiv">
								<input type="button" class="buttonImg" value="파일찾기">
								<input type="file" name="excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value; return false;">
							</div>
						</div>
						<span class="c_btn h27 blue"><input type="submit" value="엑셀 업로드" /></span>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="tip_box">
						<?php echo _DescStr('현재 페이지에서 <u>선택 엑셀다운로드</u> 또는 <u>검색 엑셀다운로드</u>를 통하여 받은 데이터를 수정 후 업로드 바랍니다. (다른 페이지 엑셀업로드 적용불가)', 'red'); ?>
						<?php echo _DescStr('데이터를 수정 시 <u>배송상태</u>, <u>택배사</u>, <u>송장번호</u>만 수정 반영 됩니다.'); ?>
						<?php echo _DescStr('파일은 최대 '.$MaxUploadSize.'까지 업로드 가능 하며, 용량에 따라 다소시간이 걸릴 수 있습니다.'); ?>
						<?php echo _DescStr('일괄업로드는 "파일업로드 → 업로드 수정/확인 → 등록처리" 단계를 거쳐 처리됩니다.'); ?>
						<?php echo _DescStr('엑셀97~2003 버전 파일만 업로드가 가능하므로, 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하시기 바랍니다.');?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>


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

</form>





<!-- ● 데이터 리스트 -->
<div class="data_list">
	<form action="_order_product.pro.php" method="post" class="form_list"<?php echo ($c?null:' target="common_frame"'); ?>>
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<?php if( $AdminPath == 'totalAdmin'){ ?>
				<a href="#none" onclick="settlement_status('ready'); return false;" class="c_btn h27 red">선택 정산대기</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<?php echo _InputSelect('select_sendstatus', $arr_order_product_sendstatus, ' class="js_select_sendstatus"', '', '', '배송상태'); ?>
				<a href="#none" onclick="selectSendstatus(); return false;" class="c_btn h27 blue">선택 일괄변경</a>
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'desc'?' selected':null); ?>>주문일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'asc'?' selected':null); ?>>주문일 ▲</option>
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
				<col width="40"/><col width="70"/><col width="90"/><col width="150"/>
				<col width="*"/>
				<col width="150"/><col width="150"/><col width="100"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">정산상태</th>
					<th scope="col">주문번호/주문자</th>
					<th scope="col">상품정보</th>
					<th scope="col">택배사/송장번호</th>
					<th scope="col">배송상태</th>
					<th scope="col">주문일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count -$k;

						// 정산상태 출력
						$print_settlement_status = '';
						switch($v['op_settlementstatus']) {
							case 'none': $print_settlement_status = '<span class="c_tag light">정산미정</span>'; break;
							case 'ready': $print_settlement_status = '<span class="c_tag blue line t4">정산대기</span>'; break;
							case 'complete': $print_settlement_status = '<span class="c_tag red line t4">정산완료</span>'; break;
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
						<tr data-uid="<?php echo $v['op_uid']; ?>">
							<td class="this_check"><label class="design"><input type="checkbox" name="_uid[]" class="js_ck" value="<?php echo $v['op_uid']; ?>"></label></td>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_date2"><?php echo $print_settlement_status; ?></td>
							<td>
								<div class="lineup-column type_left">
									<div class="t_blue"><?php echo $v['op_oordernum']; ?></div>
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
												<div class="item_name">
													<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo $v['op_pname']; ?></a>
												</div>
												<?php if($v['op_option1'] || $v['op_option2'] || $v['op_option3']) { ?>
													<?php
														// KAY :: 2022-12-07 :: 옵션 구분 수정
														$arr_option_name = array($v['op_option1'] ,$v['op_option2'],$v['op_option3'] );
														$arr_option_name = array_filter($arr_option_name);
													?>
													<dd>
														<div class="option_name">
															<?php echo ($v['op_is_addoption']=="Y" ? "<span class='add_option'>추가</span>" : "<span class='option'>필수</span>" )."".implode(" / ",$arr_option_name); ?>
														</div>
														<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
													</dd>
												<?php } else { ?>
														<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
												<?php } ?>
											</dt>
										</dl>
										<ul class="user_apply">
											<li>받는분 : <?php echo $v['o_rname']; ?><!-- 받는분 --></li>
										</ul>
									</div><!-- end order_item -->
								</div><!-- end order_item_thumb -->
							</td>
							<td>
								<div class="lineup-column type_auto col_to_row">
									<?php echo _InputSelect('_sendcompany['.$v['op_uid'].']', array_keys($arr_delivery_company), $v['op_sendcompany'], '', '', '택배사 선택'); ?>
									<input type="text" name="_sendnum[<?php echo $v['op_uid']; ?>]" class="design" placeholder="송장번호" value="<?php echo $v['op_sendnum']; ?>">
								</div>
							</td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php
									$DClass = ''; // 배송상태 셀렉트박스 클래스
									if($v['op_sendstatus'] == '배송대기') $DClass = 'pay_ready';
									else if($v['op_sendstatus'] == '배송준비') $DClass = 'diliver_ready';
									else if($v['op_sendstatus'] == '배송중') $DClass = 'diliver_ing';
									else if($v['op_sendstatus'] == '배송완료') $DClass = 'diliver_ok';
									echo _InputSelect('_sendstatus['.$v['op_uid'].']', $arr_order_product_sendstatus, $v['op_sendstatus'], ($DClass?' class="'.$DClass.'"':null), '', '배송상태');
									?>
									<a href="#none" class="c_btn h28 blue line js_submit">적용</a>
								</div>
							</td>
							<td class="this_date">
								<?php echo printDateInfo($v['o_rdate']) ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$v['op_oordernum'], 'view'=>'order_product', '_PVSC'=>$_PVSC)); ?>" class="c_btn gray">상세보기</a>
									<a href="#none" onclick="window.open('<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php<?php echo URI_Rebuild('?', array('_mode'=>'print', 'ordernum'=>$v['op_oordernum'], '_PVSC'=>$_PVSC)); ?>' ,'print','width=860,height=820,scrollbars=yes'); return false;" class="c_btn gray line only_pc_view">주문인쇄</a>
									<?php
										if($v['op_sendstatus'] != '배송대기' && $v['op_sendcompany']) {
											$DLink = '';
											$DLinkJS = null;
											if($v['op_sendcompany'] == '[자체배송]') {
												$DLink = '#none';
												$DLinkJS = "alert('자체배송은 배송조회가 불가능합니다.'); return false;";
											}
											else if($arr_delivery_company[$v['op_sendcompany']] && $v['op_sendnum']) {
												$DLink = $arr_delivery_company[$v['op_sendcompany']].rm_str($v['op_sendnum']);
												$DLinkJS = null;
											}
											else {
												$DLink = '#none';
												$DLinkJS = "alert('배송사 정보를 확인 할 수 없습니다..'); return false;";
											}
										?>

											<a href="<?php echo $DLink; ?>"<?php echo ($DLinkJS != null?' onclick="'.$DLinkJS.'"':null); ?><?php echo ($DLink != '#none'?' target="_blank"':null); ?> class="c_btn h22 green line">배송조회</a>
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
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</form>
</div>


<script type="text/javascript">
	// 주문개별처리
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		var su = $(this).closest('tr');
		var _ordernum = su.data('uid');
		var _uid = su.data('uid');
		var _sendcompany = su.find('select[name^=_sendcompany] option:selected').val();
		var _sendnum = su.find('input[name^=_sendnum]').val();
		var _sendstatus = su.find('select[name^=_sendstatus] option:selected').val();
		var _url = '_order_product.pro.php';
		// JJC : 2021-01-15 : 배송준비 수정 가능하게 함
		if(_sendstatus == '배송중' || _sendstatus == '배송완료') {
			if(!_sendcompany) {
				alert('배송사를 선택하세요.');
				su.find('select[name^=_sendcompany]').focus();
				return false;
			}
			if(!_sendnum) {
				alert('송장번호를 입력하세요.');
				su.find('input[name^=_sendnum]').focus();
				return false;
			}
		}
		// JJC : 2021-01-15 : 배송준비 수정 가능하게 함
		if(!_sendstatus) {
			alert('배송상태를 선택하세요.');
			su.find('select[name^=_sendstatus]').focus();
			return false;
		}
		_url = _url+'?_mode=modify_sendstatus&_uid[]='+_ordernum+'&_sendcompany['+_ordernum+']='+_sendcompany+'&_sendnum['+_ordernum+']='+_sendnum+'&select_sendstatus='+_sendstatus;

		common_frame.location.href = _url;
	});

	// 일괄배송상태변경
	function selectSendstatus() {
		var sendstatus = $('select[name=select_sendstatus]').val();
		var trigger = true;

		if(!sendstatus) {
			alert('배송상태를 선택하세요.');
			$("select[name=select_sendstatus]").focus();
			return false;
		}

		if($('.js_ck:checked').length <= 0) {
			alert('처리할 주문을 1건 이상 선택 바랍니다.');
			return false;
		}

		$.each($('.js_ck:checked'), function(k, v) {
			if(!$(this).closest('tr').find('select[name^=_sendcompany] option:selected').val()) {
				trigger = false;
				return false;
			}
			if(!$(this).closest('tr').find('input[name^=_sendnum]').val()) {
				trigger = false;
				return false;
			}
		});
		if(trigger === false) {
			if(!confirm('입력되지 않은 택배사 또는 송장번호가 있습니다.\n\n제외 또는 무시하고 계속 하시겠습니까?')) return false;
		}

		if(!confirm('선택하신 '+$('.js_ck:checked').length+'건의 배송상태를 일괄 수정하시겠습니까?')) return false;
		$('.form_list').find('input[name=_mode]').val('modify_sendstatus');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}


	// 선택 엑셀 다운로드
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 선택정산대기
	function settlement_status() {
		if($('.js_ck:checked').length <= 0) {
			alert('처리할 주문을 1건 이상 선택 바랍니다.');
			return false;
		}
		if(!confirm("선택하신 항목을 정산대기로 처리하시겠습니까?")) {
			return false;
		}
		$('.form_list').find('input[name=_mode]').val('settlementstatus_ready');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 상품 일괄업로드 폼 열기/닫기
	$(document).delegate('.js_open_excel_box', 'click', function(){
		$('.js_excel_box').toggle(); return false;
	});
</script>
<?php include_once('wrap.footer.php'); ?>