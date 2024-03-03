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


	// 추가파라메터
	if(!$arr_param) $arr_param = array();


	// 검색 체크
	$s_query = " from smart_order as o left join smart_individual as indr on (indr.in_id=o.o_mid) where o_canceled='Y' and `npay_order` = 'N' ";


	// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
	if($pass_input_value != ''){
		$arr_input_que = array();
		if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " o_ordernum like '%".$pass_input_value."%'  "; } // 주문번호
		if( in_array($pass_input_type, array('all','pass_oname')) > 0 ){ $arr_input_que[] = " o_oname like '%".$pass_input_value."%'  "; } // 주문자명
		if( in_array($pass_input_type, array('all','pass_mid')) > 0 ){ $arr_input_que[] = " o_mid like '%".$pass_input_value."%'  "; } // 주문자 ID
		if( in_array($pass_input_type, array('all','pass_ohp')) > 0 && rm_str($pass_input_value) != '' ){ $arr_input_que[] = " replace(o_ohp,'-','') like '%".rm_str($pass_input_value)."%'  "; } // 주문자 휴대폰
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


	if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; } // 회원구분
	if( count($pass_paymethod) > 0){ $s_query .= " and (o_paymethod in('".implode("','",$pass_paymethod)."') or o_easypay_paymethod_type in('".implode("','",$pass_paymethod)."')  )  "; } // 결제수단

	// 할인쿠폰
	if( count($pass_promotion) > 0){
		$arr_promotion_que = array();
		if( in_array('product_coupon',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_coupon_product > 0 "; }
		if( in_array('individual_coupon',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_coupon_individual > 0 or o_save_price_coupon_individual > 0 "; }
		if( in_array('point',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_usepoint > 0 ";  }
		if( in_array('promotion',$pass_promotion) > 0){ $arr_promotion_que[] = " o_promotion_price > 0 ";  }
		if( count($arr_promotion_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_promotion_que).") "; }
	}

	// 주문기기
	if( $pass_mobile == 'Y'){ $s_query .= " and mobile in('Y','A')  "; }
	else if($pass_mobile == 'N') { $s_query .= " and mobile = 'N'  "; }

	// 주문타입(상품타입)
	if( $pass_order_type == 'delivery'){  $s_query .= " and o_order_type in('delivery','both')  "; }
	else if( $pass_order_type == 'ticket'){ $s_query .= " and o_order_type in('ticket','both')  "; }

	// 날짜검색
	if( $pass_sdate !="" ) { $s_query .= " and date(o_rdate) >='".$pass_sdate."' "; }
	if( $pass_edate !="" ) { $s_query .= " and date(o_rdate) <='". $pass_edate ."' "; }

	// 날짜검색 - 취소일
	if( $pass_cancel_sdate !="" ) { $s_query .= " and date(o_canceldate) >='".$pass_cancel_sdate."' "; }
	if( $pass_cancel_edate !="" ) { $s_query .= " and date(o_canceldate) <='". $pass_cancel_edate ."' "; }

	// ----- JJC : 입점관리 : 2020-09-17 -----
	if($pass_com) {
		$s_query .= "
			and (
				SELECT
					count(*)
				FROM smart_order_product as op
				WHERE
					op.op_oordernum = o.o_ordernum AND
					op.op_partnerCode = '". addslashes($pass_com) ."'
			) > 0
		";
	}
	// ----- JJC : 입점관리 : 2020-09-17 -----


	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'o_canceldate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt {$s_query} ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = "
		select
			o.* , indr.in_id, indr.in_name
		{$s_query}
		order by {$st} {$so} limit $count , $listmaxcount
	";

	$res = _MQ_assoc($que);

?>

<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
    <?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
        <input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
    <?php }} ?>
    <input type="hidden" name="mode" value="search">
    <input type="hidden" name="st" value="<?php echo $st; ?>">
    <input type="hidden" name="so" value="<?php echo $so; ?>">
    <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
    <input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">


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
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey; ?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

        <table class="table_form">
            <colgroup>
                <col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
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
					<th>취소일</th>
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
					<th>할인적용</th>
					<td colspan="3">
						<?php echo _InputCheckbox( "pass_promotion" , array('product_coupon','individual_coupon','point','promotion'), $pass_promotion , "" , array('상품쿠폰','회원쿠폰','적립금','프로모션코드')); ?>
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
					<th>회원구분</th>
					<td>
						 <?php echo _InputRadio( "pass_memtype" , array('','Y','N'), $pass_memtype , "" , array('전체','회원','비회원')); ?>
					</td>
					<th>주문기기</th>
					<td>
						<?php echo _InputRadio( "pass_mobile" , array('','N','Y'), $pass_mobile , "" , array('전체','PC','모바일') , ''); ?>
					</td>
					<th>상품타입</th>
					<td>
						<?php echo _InputRadio( "pass_order_type" , array('','delivery','ticket'), $pass_order_type , "" , array('전체','배송상품','티켓상품') , ''); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="5">
						<?php echo _DescStr('주문취소는 주문한 모든 상품을 함께 취소한 경우입니다.',''); ?>
						<?php echo _DescStr('주문취소 가능한 단계 : 결제대기 / 결제완료','red'); ?>
					</td>
				</tr>
            </tbody>
        </table>

        <div class="c_btnbox">
            <ul>
                <li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span></li>
                <?php
                    if($mode == 'search'){
                        $arr_param = array_filter(array_merge($arr_param,array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)));
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





<div class="data_list">

	<form name="frm" method="post" action="" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">


		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<?php if($AdminPath == 'totalAdmin'){?>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_canceldate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_canceldate' && $so == 'desc'?' selected':null); ?>>취소일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_canceldate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_canceldate' && $so == 'asc'?' selected':null); ?>>취소일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'desc'?' selected':null); ?>>주문일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'asc'?' selected':null); ?>>주문일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_price_real', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_price_real' && $so == 'desc'?' selected':null); ?>>결제금액 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_price_real', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_price_real' && $so == 'asc'?' selected':null); ?>>결제금액 ▲</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->


		<table class="table_list">
			<colgroup>
				<col width="40"><col width="70"><col width="150">
				<col width="*">
				<col width="120"><col width="100"><col width="100"><col width="90">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">주문번호/주문자</th>
					<th scope="col">상품정보</th>
					<th scope="col">결제금액/결제수단</th>
					<th scope="col">주문일</th>
					<th scope="col">취소일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php
				 if(sizeof($res) > 0){
					foreach($res as $k=>$v){

						$_num = $TotalCount - $count - $k ;


						# 모바일 구매
						if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';
						else $device_icon = '<span class="c_tag h18 t3 pc">PC</span>';


						# 주문상품 추출
						$arr_pinfo = array(); // 주문상품, 옵션 정보
						$arr_status = array(); // 주문상품 진행상태 체크
						$sque = "
							select
								op.op_pouid, op.op_pcode, op.op_pname, op.op_option1, op.op_option2, op.op_option3, op.op_cnt, op.op_is_addoption, op.op_cancel, op_sendstatus , op.op_partnerCode,op.op_dateoption_use,op.op_dateoption_date,op.op_ptype,  /* JJC : 입점관리 : 2020-09-17 */
								p.p_img_list_square, IF(op_is_addoption = 'Y' , op_addoption_parent , op_pouid) AS order_uid
							from smart_order_product as op
							left join smart_product as p on (p.p_code=op.op_pcode)
							where op.op_oordernum = '". $v['o_ordernum'] ."' order by order_uid ASC , op.op_uid ASC
						";
						$sres = _MQ_assoc($sque);
						foreach($sres as $sk=>$sv){
							// 상품코드
							$arr_pinfo[$sv['op_pcode']]['code'] = $sv['op_pcode'];
							// 상품명
							$arr_pinfo[$sv['op_pcode']]['name'] = stripslashes($sv['op_pname']);

							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
							if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
							$arr_pinfo[$sv['op_pcode']]['img'] = $_p_img;

							// 배송/티켓 아이콘 표기
							$arr_pinfo[$sv['op_pcode']]['ptype_icon'] = $arr_adm_button[$sv['op_ptype']];
							$arr_pinfo[$sv['op_pcode']]['p_type'] = $sv['op_ptype'];

							// JJC : 입점관리 : 2020-09-17
							$arr_pinfo[$sv['op_pcode']]['cpid'] = $sv['op_partnerCode'];

							if($sv['op_pouid']){ // 옵션있음
								// KAY :: 2022-12-07 :: 옵션 구분 수정
								$arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );
								$arr_option_name = array_filter($arr_option_name);
								$arr_pinfo[$sv['op_pcode']]['has_option'] = 'Y';
								$arr_pinfo[$sv['op_pcode']]['option'][] = array(
																							'name'=>implode(" / ",$arr_option_name)
																							,'cnt'=>$sv['op_cnt']
																							,'is_addoption'=>$sv['op_is_addoption']
																							,'row'=>$sv // 별도처리 없이 변수만 이용하기 위한용도
																						);
							}else{ // 옵션없음
								$arr_pinfo[$sv['op_pcode']]['has_option'] = 'N';
								$arr_pinfo[$sv['op_pcode']]['row'] = $sv; // 별도처리 없이 변수만 이용하기 위한용도
							}
							$arr_pinfo[$sv['op_pcode']]['cnt'] += $sv['op_cnt'];
							$arr_pinfo[$sv['op_pcode']]['point'] += $sv['op_point'];
							$arr_pinfo[$sv['op_pcode']]['delivery_type'] = $sv['op_delivery_type'];
							$arr_pinfo[$sv['op_pcode']]['delivery_price'] += $sv['op_delivery_price'];
							$arr_pinfo[$sv['op_pcode']]['add_delivery_price'] += $sv['op_add_delivery_price'];

							// 주문상품의 진행상태
							$arr_status[$sv['op_pcode']]['total']++;
							if($v['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
								$arr_status[$sv['op_pcode']]['cancel']++;
							}else if($v['o_status'] == '결제실패'){ // 결제실패일경우
								$arr_status[$sv['op_pcode']]['fail']++;
							}else{
								if($v['o_paystatus'] =='Y'){ // 주문결제를 했다면,
									if($sv['op_sendstatus'] == '배송대기') {
										$arr_status[$sv['op_pcode']]['pay']++;
									}else if($sv['op_sendstatus'] == '배송중'){
										$arr_status[$sv['op_pcode']]['delivery']++;
									}else if($sv['op_sendstatus'] == '배송완료'){
										$arr_status[$sv['op_pcode']]['complete']++;
									}else{
										$arr_status[$sv['op_pcode']]['cancel']++;
									}
								}else{ // 주문결제를 하지 않았다면
									$arr_status[$sv['op_pcode']]['ready']++;
								}
							}
						}

						// 주문상품 진행상태 체크
						foreach($arr_status as $sk=>$sv){
							# 진행상태
							$op_status_icon = '';
							if($v['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패]
								$arr_pinfo[$sk]['status'] = '주문취소';
							}
							else if($sv['fail']>0){ // 결제실패가 하나라도 있으면 결제실패상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
								$arr_pinfo[$sk]['status'] = '결제실패';
							}
							else if($sv['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
								$arr_pinfo[$sk]['status'] = '결제대기';
							}
							else if($sv['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
								$arr_pinfo[$sk]['status'] = '배송중';
							}
							else if($sv['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
								$arr_pinfo[$sk]['status'] = '결제완료';
							}
							else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
								$arr_pinfo[$sk]['status'] = '배송완료';
							}else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
								$arr_pinfo[$sk]['status'] = '주문취소';
							}
						}

						// 주문상품 수 체크 - 최소:1
						$app_rowspan	= max(1, count($arr_pinfo));

						// 첫번째 주문상품 별도처리
						$pinfo = array_shift($arr_pinfo);

						$userLink = '/?pn=product.view&code='.$pinfo['code']; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$pinfo['code'];
						if( $pinfo['p_type'] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$pinfo['code'];
						}

				?>
						<tr>
							<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck" value="<?php echo $v['o_ordernum'];?>"></label></td>
							<td class="this_num">
								<?php echo number_format($_num); ?>
							</td>
							<td>
								<div class="lineup-column type_left">
									<div class="t_blue"><?php echo $v['o_ordernum']; ?></div>
									<?php echo showUserInfo($v['o_mid'],$v['o_oname']); ?>
								</div>
							</td>
							<td>
								<div class="order_item_thumb">
									<div class="thumb">
										<a href="<?php echo $adminLink; ?>" title="<?php echo addslashes($pinfo['name']); ?>" target="_blank"><img src="<?php echo $pinfo['img']; ?>" alt="<?php echo addslashes($pinfo['name']); ?>"></a>
									</div>
									<div class="order_item">
										<dl>
											<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
												<div class="entershop"><?php echo showCompanyInfo($pinfo['cpid']); ?></div>
											<?php } ?>
											<dt>
												<div class="item_name">
													<a href="<?php echo $adminLink; ?>" target="_blank" data-p="1"><?php echo stripslashes($pinfo['name']); ?></a>
												</div>
												<?php echo ($pinfo['has_option']=='N' ? '<span class="mount">'. number_format($pinfo['cnt']) .'개</span>' : null); ?>
											</dt>

											<?php
												// 옵션 반복
												if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
													foreach($pinfo['option'] as $sk=>$sv){
														// 달력옵션일 경우 예약일 표시
													$print_dateoption_date = '';
													if($sv['row']['op_dateoption_use'] == 'Y' && rm_str($sv['row']['op_dateoption_date']) > 0  ){
														$print_dateoption_day = date('w',strtotime($sv['row']['op_dateoption_date']));
														$print_dateoption_date = "<span style='color:#369' class='if_cal_date'>[".$sv['row']['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")]</span> ";
													}
											?>
														<dd>
															<div class="option_name"><?php echo ($sv['is_addoption']=='N'?'<span class="option">필수</span>':'<span class="add_option">추가</span>'); ?><?php echo $print_dateoption_date;?><?php echo stripslashes($sv['name']); ?></div>
															<span class="mount"><?php echo number_format($sv['cnt']); ?>개</span>
														</dd>
											<?php
													}
												}
											?>
											</dl>


										<?php
											// 나머지 주문상품별 옵션 노출
											if(count($arr_pinfo)>0){
												foreach($arr_pinfo as $pinfo){
										?>
												<dl>
													<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
														<div class="entershop"><?php echo showCompanyInfo($pinfo['cpid']); ?></div>
													<?php } ?>
													<dt>
														<div class="item_name">
															<?php
																$userLink = '/?pn=product.view&code='.$pinfo['code']; // 사용자 링크 사용시
																$adminLink = '_product.form.php?_mode=modify&_code='.$pinfo['code'];
																if( $pinfo['p_type'] == 'ticket'){
																	$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$pinfo['code'];
																}
															?>
															<a href="<?php echo $adminLink; ?>" target="_blank" data-p="1"><?php echo stripslashes($pinfo['name']); ?></a>
														</div>
														<?php echo ($pinfo['has_option']=='N' ? '<span class="mount">'. number_format($pinfo['cnt']) .'개</span>' : null); ?>
													</dt>
												<?php
													// 옵션 반복
													if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
														foreach($pinfo['option'] as $sk=>$sv){
															// 달력옵션일 경우 예약일 표시
															$print_dateoption_date = '';
															if($sv['row']['op_dateoption_use'] == 'Y' && rm_str($sv['row']['op_dateoption_date']) > 0  ){
																$print_dateoption_day = date('w',strtotime($sv['row']['op_dateoption_date']));
																$print_dateoption_date = "<span style='color:#369' class='if_cal_date'>[".$sv['row']['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")]</span> ";
															}
												?>
															<dd>
																<div class="option_name"><?php echo ($sv['is_addoption']=='N'?'<span class="option">필수</span>':'<span class="add_option">추가</span>'); ?><?php echo $print_dateoption_date;?><?php echo stripslashes($sv['name']); ?></div>
																<span class="mount"><?php echo number_format($sv['cnt']); ?>개</span>
															</dd>
												<?php
														}
													}
												?>
												</dl>

										<?php
												}
											}
										?>
									</div><!-- end order_item -->
								</div><!-- end order_item_thumb -->
								<div class="order_item_tag">
									<?php echo $device_icon; ?>
								</div>
							</td>
							<td class="t_red t_right this_price t_bold">
								<div class="lineup-row type_end">
									<?php echo number_format($v['o_price_real']); ?>원
									<?php echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]]; ?>
								</div>
							</td>
							<td>
								<span class="hidden_tx">주문일</span>
								<?php echo printDateInfo($v['o_rdate']); ?>
							</td>
							<td>
								<span class="hidden_tx">취소일</span>
								<?php echo printDateInfo($v['o_canceldate']); ?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>'cancel', '_mode'=>'modify', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray">상세보기</a>
									<a href="#none" onclick="del('_order.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 dark line">삭제하기</a>
								</div>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>



		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>


		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>

		</form>

</div>
<!-- / 데이터 리스트 -->


<script>
	 // 선택삭제
	 function selectDelete() {
		 if($('.js_ck').is(':checked')){
			 if(confirm('삭제 후에는 복구가 불가합니다.\n그래도 삭제하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
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