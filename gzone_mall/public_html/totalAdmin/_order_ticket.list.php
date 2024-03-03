<?php
include_once('wrap.header.php');

// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	else $_PVS .= "&$key=$val";
}
$_PVSC = enc('e' , $_PVS);

// 검색 조건
$s_query = " and o.o_canceled = 'N' and o.o_paystatus = 'Y' and npay_order = 'N' and op.op_cancel = 'N' and op.op_is_addoption = 'N'  "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외

// LCY : 2021-03-22 : 상품 티켓유형 추가 -- 배송 or (티켓+배송)
$s_query .=" and o.o_order_type in('ticket','both') and op_ptype = 'ticket'  ";

// 만약 별도 티켓조회가 있을 경우 처리
if($pass_ticketnum != '' ){ $pass_input_type = 'pass_ticketnum'; $pass_input_value = $pass_ticketnum;  }


// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();
	if( in_array($pass_input_type, array('all','pass_ticketnum')) > 0 ){ $arr_input_que[] = " (select count(*) as cnt from smart_order_product_ticket where opt_opuid= op_uid and opt_ticketnum like '%".$pass_input_value."%') > 0  "; }
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " op_oordernum like '%".$pass_input_value."%'  "; }
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

// 티켓사용상태
if($pass_ticketstatus){
	$s_query .= " and (select count(*) as cnt from smart_order_product_ticket where opt_opuid= op_uid and opt_status = '".$pass_ticketstatus."' ) > 0  ";
}

if($pass_memtype) $s_query .= " and o.o_memtype = '{$pass_memtype}' "; // 회원구분


// ----- JJC : 입점관리 : 2020-09-17 -----
if($pass_com) {
	$s_query .= " and  op_partnerCode = '".addslashes($pass_com)."' ";
}

// 받는분
if( $pass_uname !="" ) { $s_query .= " and o_rname like '%".$pass_uname."%' "; }
if( rm_str($pass_uhp) !="" ) { $s_query .= " and replace(o_rhp,'-','') like '%".rm_str($pass_uhp)."%' "; }
if( $pass_uemail !="" ) { $s_query .= " and o_uemail like '%".$pass_uemail."%' "; }

// 날짜검색
if( $pass_sdate !="" ) { $s_query .= " and date(o_rdate) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(o_rdate) <='". $pass_edate ."' "; }


// 날짜검색 - 사용일
if( $pass_sudate !="" || $pass_eudate !="" ){
	$s_query .= " and (select count(*) as cnt from smart_order_product_ticket where opt_opuid= op_uid and opt_status = '사용'
	".($pass_sudate != '' ? " and date(opt_udatetime) >= '".$pass_sudate."' ":null)." ".($pass_eudate != '' ? " and date(opt_udatetime) <= '".$pass_eudate."' ":null)."  ) > 0  ";
}

if($pass_vat) $s_query .= " and op.op_vat = '{$pass_vat}' "; // 과세여부


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
<form  method="post" enctype="multipart/form-data">

</form>


<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
    <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
    <input type="hidden" name="mode" value="search">


	<?php // 통합검색 ?>
	<div class="comp_search">
		<div class="form_wrap">
			<select name="pass_input_type">
				<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
				<option value="pass_ordernum" <?php echo $pass_input_type == 'pass_ordernum' ? 'selected' : ''?>>주문번호</option>
				<option value="pass_ticketnum" <?php echo $pass_input_type == 'pass_ticketnum' ? 'selected' : ''?>>티켓번호</option>
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
					<th>사용일</th>
					<td colspan="5">


						<div class="lineup-row type_date">
							<input type="text" name="pass_sudate" value="<?php echo $pass_sudate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_eudate" value="<?php echo $pass_eudate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
							<?php // 버튼클릭하면 해당날짜가 날짜 인풋에 입력(바로검색아님) ?>
							<div class="lineup-row type_days">
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['today']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['today']['edate'] ?>">오늘</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['1week']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1week']['edate'] ?>">1주일</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['1month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1month']['edate'] ?>">1개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['3month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['3month']['edate'] ?>">3개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['6month']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['6month']['edate'] ?>">6개월</a>
								<a href="#none" onclick="return false;" class="c_btn gray line js_date_auto_set" data-sname="pass_sudate" data-ename="pass_eudate" data-sdate="<?php echo $arrTodayToDate['1year']['sdate'] ?>" data-edate="<?php echo $arrTodayToDate['1year']['edate'] ?>">1년</a>
							</div>
						</div>

                    </td>
				</tr>
				<tr>
					<th>사용상태</th>
                    <td colspan="5">
                        <?php echo _InputRadio('pass_ticketstatus', array_merge(array(''),array_keys($arr_ticket_status)), $pass_ticketstatus, '', array_merge(array('전체'),array_values($arr_ticket_status)), ''); ?>
                    </td>
				</tr>
				<tr>
					<th>정산상태</th>
					<td colspan="3">
						<?php echo _InputRadio('pass_settlement', array('', 'none', 'ready', 'complete'), $pass_settlement, '', array('전체', '정산미정', '정산대기', '정산완료'), ''); ?>
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
					<th>사용자 이름</th>
					<td>
						<input type="text" name="pass_uname" value="<?php echo $pass_uname; ?>" class="design"  style="width:100px;" placeholder="사용자 이름">
					</td>
					<th>사용자 휴대폰</th>
					<td>
						<input type="text" name="pass_uhp" value="<?php echo $pass_uhp; ?>" class="design"  style="" placeholder="사용자 휴대폰">
					</td>
					<th>사용자 이메일</th>
					<td>
						<input type="text" name="pass_uemail" class="design" style="width:150px" value="<?=$pass_uemail?>" placeholder="사용자 이메일" />
					</td>
				</tr>
				 <?php if($siteInfo['s_vat_product'] == 'C') { ?>
                    <tr>
                        <th>과세여부</th>
                        <td colspan="5">
                            <?php echo _InputRadio('pass_vat', array('', 'Y', 'N'), $pass_vat, '', array('전체', '과세', '면세'), ''); ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>참고사항</th>
                    <td colspan="5">
                        <div class="tip_box">
                        <?php echo _DescStr('티켓상품은 플랫폼 정책상 네이버페이(주문형)으로 주문이 불가합니다.'); ?>
                        <?php echo _DescStr('티켓상품은 결제가 완료되거나 입금이 확인되면 즉시 발급되므로 사용자의 주문취소가	불가합니다.'); ?>
                        </div>
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
	<form action="_order_ticket.pro.php" method="post" class="form_list"<?php echo ($c?null:' target="common_frame"'); ?>>
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>


		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<?php if($AdminPath == 'totalAdmin'){?>
				<a href="#none" onclick="settlement_status('ready'); return false;" class="c_btn h27 red">선택 정산대기</a>
				<?php } ?>
			</div>
			<div class="right_box">
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
				<col width="40"><col width="70"><col width="150"><col width="150">
				<col width="*">
				<col width="100"><col width="90">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col" >No</th>
					<th scope="col" >정산/발급상태</th>
					<th scope="col" >주문번호/주문자</th>
					<th scope="col" >티켓 발급정보</th>
					<th scope="col" >주문일</th>
					<th scope="col" >관리</th>
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
							case 'none': $print_settlement_status = '<span class="c_tag black line h22 t4">정산미정</span>'; break;
							case 'ready': $print_settlement_status = '<span class="c_tag blue line  h22 t4">정산대기</span>'; break;
							case 'complete': $print_settlement_status = '<span class="c_tag red line h22 t4">정산완료</span>'; break;
						}



						$tmp_content = "";
						$_row = 0;


						$ticket_assoc = _MQ_assoc("select * from smart_order_product_ticket where opt_opuid = '".$v['op_uid']."'".$ticket_squery);

						$ticket_date_string = '';
						foreach($ticket_assoc as $ticket_key => $ticket_row) {

							// 티켓의 유효기간을 표시한다.
							if( $ticket_date_string == '' ){
								if( $ticket_row['opt_expire_type'] != ''){
									$ticket_date_string = $ticket_row['opt_expire_date'];
								}
								else{
									$ticket_date_string = '제한없음';
								}
							}


							// ---- 쿠폰발급완료 일때만 버튼이 출력 ---
							$usestring = "";
							if ($v['op_sendstatus']=="발급완료") {
								// 미사용, 사용
								if($ticket_row['opt_status'] == "대기") {
									$usestring ="<a href='#none' class='c_btn blue t6' onClick=\"f_check2('" . $ticket_row['opt_uid'] . "','use');\" alt='클릭시 해당쿠폰이 사용으로 변경됩니다.'>사용 완료처리</a>";
								}
								else if($ticket_row['opt_status'] == "사용") {
									$usestring ="<a href='#none' class='c_btn black t6' onClick=\"f_check2('" . $ticket_row['opt_uid'] . "','unuse');\" alt='클릭시 해당쿠폰이 미사용으로 변경됩니다.'>미사용 전환</a>";
								}else if($ticket_row['opt_status'] == "취소"){
									$usestring ="<a href='#none' class='c_btn black line t6' onClick=\"return false;\" alt='취소된 쿠폰입니다.'>주문취소</a>";
								}else if($ticket_row['opt_status'] == "만료"){
									$usestring ="<a href='#none' class='c_btn light t6' onClick=\"return false;\"  alt='만료된 쿠폰입니다.'>기간만료</a>";
								}
							}

							// <div class='ticket_okday'>사용일 : 2023-03-15 (15:36:00)</div>
							$ticket_usedate = '';
							if( $ticket_row['opt_status'] == '사용'){
								$ticket_usedate = "<div class='ticket_okday'>사용일 : ".printDateInfo($ticket_row['opt_udatetime'],1)."</div>";
							}
							$tmp_content .= "
								<dd class='ticket_list".($ticket_row['opt_status'] != '대기' ? ' if_end':'')."'>
									<input type='hidden' name='OrderNumValue[]' value='".$v['o_ordernum']."'>
									<input type='hidden' name='op_uid[]' value='". $v['op_uid'] ."'>
									<input type='hidden' name='opt_uid[]' value='".$ticket_row['opt_uid']."'>
									<div class='ticket_number'><strong>" . $ticket_row['opt_ticketnum'] . "</strong>".$ticket_usedate."</div>
									<div class='ticket_ctrl'>" . $usestring . "</div>
								</dd>
							";
						}



						if($tmp_content != ''){  $tmp_content = "".$tmp_content.""; }

						// 모바일 아이콘 LDD002
						$device_icon = '<span class="c_tag h18 t3 pc">PC</span>';
						if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';



						// KAY :: 2022-12-07 :: 옵션 구분 수정
						$arr_option_name = array($v['op_option1'] ,$v['op_option2'],$v['op_option3'] );
						$arr_option_name = array_filter($arr_option_name);

						unset($print_dateoption_date);
						if($v['op_dateoption_use'] == 'Y' && rm_str($v['op_dateoption_date']) > 0  ){
							$print_dateoption_day = date('w',strtotime($v['op_dateoption_date']));
							$print_dateoption_date = "<span class='ticket_day'>".$v['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")</span>";
						}


						// --- 옵션값 추출  ---
						$arr_option = array();
						$arr_option[] = ($v['op_option1'] ? "<dd>".($v['op_is_addoption']=="Y" ? "" : "")."".$print_dateoption_date.' '.implode(" / ",$arr_option_name) : "</dd>");// 해당상품에 대한 옵션내역이 있으면
						//$arr_option[] = ($v['op_option1'] ? "<dd>".($v['op_is_addoption']=="Y" ? "<span class='add_option'>추가옵션</span>" : "<span class='option'>필수옵션</span>")." ".$print_dateoption_date.' '.implode(" / ",$arr_option_name) : "</dd>");// 해당상품에 대한 옵션내역이 있으면

						/*
						// 추가옵션을 노출하기 위한 처리 -- 티켓은 추가옵션을 사용할 수 없으나, 노출이 필요할 시
						if( $v['op_option1'] != ''){
							$add_option_res = _MQ_assoc("SELECT op_option1, op_option2, op_option3 FROM smart_order_product where op_is_addoption = 'Y' and op_addoption_parent = '".$v['op_pouid']."' ");
							if( count($add_option_res) > 0){
								foreach($add_option_res as $sk=>$sv){
									// KAY :: 2022-12-07 :: 옵션 구분 수정
									$arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );
									$arr_option_name = array_filter($arr_option_name);
									$arr_option[] = "추가 : ".implode(" / ",$arr_option_name);// 해당상품에 대한 옵션내역이 있으면
								}
							}
						}
						*/

						unset($option_name);
						foreach($arr_option as $sk=>$sv){
							$option_name .= "".$sv."";
						}


						// --- 추가옵션 추출 ---

						$userLink = '/?pn=product.view&code='.$v['op_pcode']; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$v['op_pcode'];
						if( $v['op_ptype'] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$v['op_pcode'];
						}
						$app_content = "
							<dt>
								<div class='item_name'><a href='".$adminLink."' target='_blank'>".$v['op_pname']."</a></div>
								<span class='mount'>".$v['op_cnt']."개</span>
							</dt>
							<dt class='t_red'>
								유효기간 : ".$ticket_date_string."
							</dt>
							".$option_name."
							".$tmp_content."
						";


						// 이미지 체크
						$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
						if($_p_img == '') $_p_img = 'images/thumb_no.jpg';


					?>
						<tr data-uid="<?php echo $v['op_uid']; ?>">
							<td class="this_check">
								<?php if( $v['op_reservation_use'] !='Y'){?>
								<label class="design"><input type="checkbox" name="_uid[]" class="js_ck" value="<?php echo $v['op_uid']; ?>"></label>
								<?php }else{ ?>
								-
								<?php }?>
							</td>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
								<?php echo $print_settlement_status; ?>
									<span class='c_tag yellow line h22 t4'>발급완료</span>
								</div>
							</td>
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
											<?php echo $app_content; ?>
										</dl>
										<ul class="user_apply">
											<li>사용자 : <?php echo $v['o_uname']; ?></li>
											<li>휴대폰 : <?php echo tel_format($v['o_uhp']) ?></li>
										</ul>
									</div><!-- end order_item -->
								</div><!-- end order_item_thumb -->
							</td>
							<td class="this_date">
								<?php echo printDateInfo($v['o_rdate']) ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$v['op_oordernum'], 'view'=>'order_ticket', '_PVSC'=>$_PVSC)); ?>" class="c_btn gray">상세보기</a>
									<a href="#none" onclick="window.open('<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php<?php echo URI_Rebuild('?', array('_mode'=>'print', 'ordernum'=>$v['op_oordernum'], '_PVSC'=>$_PVSC)); ?>' ,'print','width=860,height=820,scrollbars=yes'); return false;" class="c_btn gray line only_pc_view">주문인쇄</a>
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


	// 티켓 사용 , 미사용 여부
	function f_check2(uid , type) {
		if (type == "use") { if( confirm("선택하신 티켓을 사용처리 하시겠습니까? ") == false){ return false; } }
		else { if( confirm("선택하신 티켓을 미사용처리 하시겠습니까? ") == false){ return false; } }

		common_frame.location.href= ("_order_ticket.pro.php?_mode=ticket_use&uid=" + uid + "&type="+type+"&_PVSC=" + $("input[name=_PVSC]").val() );
	}

	// -  쿠폰번호 일괄발송 // 송장번호 일괄발급 ---
	function selectSendstatus(force) {

		if($('.js_ck:checked').length <= 0) { alert('1건 이상 선택 바랍니다.'); return false;}
		if(!confirm('선택하신 '+$('.js_ck:checked').length+'건의 티켓을 재발송 하시겠습니까?')) return false;
		$('.form_list').attr('action','_order_ticket.express.php');
		$('.form_list').find('input[name=_mode]').val('modify_sendstatus');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').attr('action','');
	}
	// - 쿠폰번호발송 ---

</script>
<?php include_once('wrap.footer.php'); ?>