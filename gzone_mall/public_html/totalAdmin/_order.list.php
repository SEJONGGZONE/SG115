<?php
	if(!isset($_REQUEST['view'])) $_REQUEST['view'] = '';
	// 입금대기 주문 목록 지정
	if($_REQUEST['view'] == 'online') {
		$app_current_link = '_order.list.php?view=online';
		// 추가 파라메터 설정
		$arr_param = array('view'=>'online');
	}

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

	// 전체검색 필터
	if( !$pass_input_type) $pass_input_type = 'all';

	// 결제상태 추가 -- 결제실패
	$arr_order_status[] = "환불요청";
	$arr_order_status[] = "결제실패";
	$arr_order_status = array_diff($arr_order_status , array('주문취소'));		/// 주문 전체내역에서 검색에서 주문취소 항목 삭제

	// 기본결제상태 지정
	if($_REQUEST['view'] == 'online') {
		if($pass_paystatus == '') $pass_paystatus = 'A';
		$pass_paymethod= array($pass_paymethod);
	}else{
		if($pass_paystatus == '') $pass_paystatus = 'Y';
	}



	// 검색 체크
	$s_query = " from smart_order as o left join smart_individual as indr on (indr.in_id=o.o_mid) where o_canceled!='Y' and `npay_order` = 'N'  ";

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

	if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; } // 회원구분
	if( $pass_paystatus !="A" ) { $s_query .= " and o_paystatus='${pass_paystatus}' "; } // 결제상태

	if( count(array_filter($pass_paymethod)) > 0){ $s_query .= " and (o_paymethod in('".implode("','",$pass_paymethod)."') or o_easypay_paymethod_type in('".implode("','",$pass_paymethod)."')  )  "; } // 결제수단

	// 주문상태
	if( count($pass_status) > 0){
		$tmp_pass_status = $pass_status;
		if( in_array('결제완료',$tmp_pass_status) > 0){ $tmp_pass_status[] = '배송대기';  }
		$s_query .= " and o_status in('".implode("','",$tmp_pass_status)."')  ";
	}

	// 할인쿠폰
	if( count($pass_promotion) > 0){
		$arr_promotion_que = array();
		if( in_array('product_coupon',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_coupon_product > 0 "; }
		if( in_array('individual_coupon',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_coupon_individual > 0 or o_save_price_coupon_individual > 0 "; }
		if( in_array('point',$pass_promotion) > 0){ $arr_promotion_que[] = " o_price_usepoint > 0 ";  }
		if( in_array('promotion',$pass_promotion) > 0){ $arr_promotion_que[] = " o_promotion_price > 0 ";  }
		if( count($arr_promotion_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_promotion_que).") "; }
	}

	// 받는분
	if( $pass_rname !="" ) { $s_query .= " and o_rname like '%".$pass_rname."%' "; }
	if( rm_str($pass_rhp) !="" ) { $s_query .= " and replace(o_rhp,'-','') like '%".rm_str($pass_rhp)."%' "; }
	if( $pass_address !="" ) { $s_query .= " and concat(ifnull(o_raddr_doro,''),ifnull(o_raddr2,'')) like '%".$pass_address."%'  "; }

	// 사용자
	if( $pass_uname !="" ) { $s_query .= " and o_uname like '%".$pass_uname."%' "; }
	if( rm_str($pass_uhp) !="" ) { $s_query .= " and replace(o_uhp,'-','') like '%".rm_str($pass_uhp)."%' "; }
	if( $pass_uemail !="" ) { $s_query .= " and o_uemail like '%".$pass_uemail."%' "; }

	// 주문기기
	if( $pass_mobile == 'Y'){ $s_query .= " and mobile in('Y','A')  "; }
	else if($pass_mobile == 'N') { $s_query .= " and mobile = 'N'  "; }

	// 주문타입(상품타입)
	if( $pass_order_type == 'delivery'){  $s_query .= " and o_order_type in('delivery','both')  "; }
	else if( $pass_order_type == 'ticket'){ $s_query .= " and o_order_type in('ticket','both')  "; }

	// 날짜검색
	if( $pass_sdate !="" ) { $s_query .= " and date(o_rdate) >='".$pass_sdate."' "; }
	if( $pass_edate !="" ) { $s_query .= " and date(o_rdate) <='". $pass_edate ."' "; }

	if( $pass_get_tax =="Y" ) {
		$s_query .= "
			and o_get_tax='Y'
			and (
				ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') = 'AUTH'
				or
				(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) > 0
			)
		";
	}
	else if( $pass_get_tax =="N" ) {
		$s_query .= "
			and o_get_tax='Y'
			and (
				ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') != 'AUTH'
				and
				(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) = 0
			)
		";
	}

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

	// 입금대기 주문 목록 검색 지정
	if($_REQUEST['view'] == 'online') {
		// 결제방식 - 무통장, 가상계좌
		$s_query .= " and o_paymethod in ('online', 'virtual') ";
		// 결제상태 - 결제대기
		$s_query .= " and o_paystatus='N' ";
		// 주문상태 - 결제대기
		$s_query .= " and o_status='결제대기' ";

		// 가상계좌 주문 체크 - 입금계좌 정보가 있는 주문만
		$s_query .= " and if(o_paymethod='virtual', (select count(*) as cnt from smart_order_onlinelog as ool where ool.ool_ordernum=o.o_ordernum), 1) > 0 ";
	}




	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = "o_rdate"; // 결제완료일 우선 정렬
	$st = stripslashes($st);
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt {$s_query} ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$que = "
		select
			o.* , indr.in_id, indr.in_name,
			(select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1) as ocs_cash,
			(select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) as bc_cnt
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
				<option value="pass_deposit" <?php echo $pass_input_type == 'pass_deposit' ? 'selected' : ''?>>입금자 이름</option>
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

		<table class="table_form" >
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
				<?php if($_REQUEST['view'] <> 'online') { // 전체주문일때 ?>
					<tr>
						<th>진행상태</th>
						<td colspan="5">
							<?php echo _InputCheckbox( "pass_status" , array_values($arr_order_status), $pass_status , "" , array_values($arr_order_status) , ''); ?>
						</td>
					</tr>
					<tr>
						<th>결제수단</th>
						<td colspan="5">
							<?php echo _InputCheckbox( "pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , ''); ?>
						</td>
					</tr>
				<?php }else{ // 입금대기일때 ?>
					<tr>
						<th>결제수단</th>
						<td colspan="5">
							<?php $pass_paymethod_val = $pass_paymethod[0]!=''?$pass_paymethod[0]:''; ?>
							<?php echo _InputRadio( "pass_paymethod" , array('','online','virtual'), $pass_paymethod_val, "" , array('전체','무통장','가상계좌') , ''); ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>할인적용</th>
					<td colspan="5">
						<?php echo _InputCheckbox( "pass_promotion" , array('product_coupon','individual_coupon','point','promotion'), $pass_promotion , "" , array('상품쿠폰','회원쿠폰','적립금','프로모션코드')); ?>
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
					<th>결제상태</th>
					<td>
						<?php echo _InputRadio( "pass_paystatus" , array('A','Y','N'), $pass_paystatus , "" , array('전체','결제완료','결제대기') , ''); ?>
					</td>
					<th>현금영수증</th>
                    <td>
                        <?php echo _InputRadio( "pass_get_tax" , array('','N','Y'), $pass_get_tax , "" , array('전체','발행대기','발행완료')); ?>
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
				<tr>
					<th>사용자 이름</th>
					<td>
						<input type="text" name="pass_uname" class="design" style="width:100px;" value="<?php echo $pass_uname; ?>" placeholder="사용자 이름">
					</td>
					<th>사용자 휴대폰</th>
					<td>
						<input type="text" name="pass_uhp" class="design" style="width:100px;" value="<?php echo $pass_uhp; ?>" placeholder="사용자 휴대폰">
					</td>
					<th>사용자 이메일</th>
					<td>
						<input type="text" name="pass_uemail" class="design" style="width:150px" value="<?=$pass_uemail?>" placeholder="사용자 이메일" />
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="5">
						<?php echo _DescStr('무통장 및 계좌이체 등 직접 입금확인이 필요한 주문은 <a href="/totalAdmin/_order.list.php?view=online&menuUid=99"><em>입금대기</em></a>에서 확인해주세요. ','red'); ?>
						<?php echo _DescStr('전체주문에서 "결제대기"는 PG창이 열리고 실제 결제까지 완료되지 않은 주문입니다.',''); ?>
						<?php echo _DescStr('<em>받는분</em>은 배송상품 주문, <em>사용자</em>는 티켓상품 주문 정보를 검색합니다.',''); ?>
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
    </div><!-- end search_form -->

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

	<form name="frm" method="post" action="" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">


		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
				<a href="#none" onclick="selectCancel(); return false;" class="c_btn h27 black">선택 주문취소</a>
				<?php if($view == 'online'){ ?>
					<a href="#none" onclick="select_paystatus_send(); return false;" class="c_btn h27 blue">선택 입금확인</a>
				<?php } ?>
			</div>
			<div class="right_box">
				<a href="#none" onclick="selectExcel(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<?php // LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 ?>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<a href="#none" onclick="selectPrint(); return false;" class="c_btn icon icon_print only_pc_view">선택 일괄인쇄</a>

				<select onchange="location.href=this.value;">
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
		<div class="mobile_tip">엑셀 다운로드 및 인쇄는 PC에서 가능합니다.</div>


		<table class="table_list">
			<colgroup>
				<col width="40"><col width="80"><col width="150">
				<col width="*">
				<col width="120"><col width="100"><col width="100">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">주문번호/주문자</th>
					<th scope="col">상품/주문정보</th>
					<th scope="col">결제금액/결제수단</th>
					<th scope="col">주문일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php
				 if(sizeof($res) > 0){
					foreach($res as $k=>$v){

						$_num = $TotalCount - $count - $k ;

						// 현금영수증 발행여부 확인
						if($v['o_get_tax']=='Y') {
							if($v['ocs_cash']=='AUTH') { $cash_status = '현금영수증 발행'; }
							else if($v['bc_cnt']>0) { $cash_status = '현금영수증 발행'; } // 바로빌 현금영수증 확인
							else { $cash_status = '현금영수증 요청'; }
						} else { $cash_status = ''; }

						# 모바일 구매
						if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO</span>';
						else $device_icon = '<span class="c_tag h18 t3 pc">PC</span>';

			


						# 주문상품 추출
						$arr_pinfo = array(); // 주문상품, 옵션 정보
						$arr_status = array(); // 주문상품 진행상태 체크

						// JJC : 2023-01-13 : 추가옵션 부분취소 - 위치조정 : 지정된 기본옵션 하위에 추가옵션 위치함
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
																			,'cancel_refund'=>$sv['op_cancel'] // KAY :: 2021-09-09 :: 옵션 부분취소
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
							}else if($v['o_canceled'] == 'R'){ // 환불요청
								$arr_status[$sv['op_pcode']]['refund']++;
							}else if($sv['op_cancel'] == 'R'){ // KAY :: 2021-09-06 :: 부분취소요청
								$arr_status[$sv['op_pcode']]['cancel_refund']++;
							}else if($v['o_status'] == '결제실패'){ // 결제실패일경우
								$arr_status[$sv['op_pcode']]['fail']++;
							}else{
								if($v['o_paystatus'] =='Y'){ // 주문결제를 했다면,
									if($sv['op_sendstatus'] == '배송대기') {
										$arr_status[$sv['op_pcode']]['pay']++;
									}else if($sv['op_sendstatus'] == '배송준비'){
										$arr_status[$sv['op_pcode']]['del_ready']++;
									}else if($sv['op_sendstatus'] == '배송중'){
										$arr_status[$sv['op_pcode']]['delivery']++;
									}else if($sv['op_sendstatus'] == '배송완료'){
										$arr_status[$sv['op_pcode']]['complete']++;
									}else if($sv['op_sendstatus'] == '발급완료'){
										$arr_status[$sv['op_pcode']]['issued']++;
									}
									else{
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
							else if($sv['refund']>0){ // 환불요청이 하나라도 있으면 환불요청상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
								$arr_pinfo[$sk]['status'] = '환불요청';
							}
							else if($sv['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
								$arr_pinfo[$sk]['status'] = '결제대기';
							}
							else if($sv['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
								$arr_pinfo[$sk]['status'] = '배송중';
							}
							else if($sv['del_ready']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
								$arr_pinfo[$sk]['status'] = '배송준비';
							}
							else if($sv['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
								$arr_pinfo[$sk]['status'] = '결제완료';
							}
							else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
								$arr_pinfo[$sk]['status'] = '배송완료';
							}
							else if($sv['issued']>0){ // 발급완료 하나라도 있으면 발급완료
								$arr_pinfo[$sk]['status'] = '발급완료';
							}
							else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
								$arr_pinfo[$sk]['status'] = '주문취소';
							}

							// KAY :: 2021-09-09  :: 부분취소
							if($sv['cancel_refund']>0){
								$arr_pinfo[$sk]['cancel_refund']='부분취소요청';
							}
						}

						// 주문상품 수 체크 - 최소:1
						$app_rowspan	= max(1, count($arr_pinfo));

						// 첫번째 주문상품 별도처리
						$pinfo = array_shift($arr_pinfo);

						// 상품관리 링크
						$userLink = '/?pn=product.view&code='.$pinfo['code']; // 사용자 링크 사용시
						$adminLink = '_product.form.php?_mode=modify&_code='.$pinfo['code'];
						if( $pinfo['p_type'] == 'ticket'){
							$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$pinfo['code'];
						}


				?>
					<tr>
						<td class="this_check">
							<label class="design"><input type="checkbox" name="chk_ordernum[<?php echo $v['o_ordernum']; ?>]" class="js_ck" value="Y"></label>
						</td>
						<td class="this_num">
							<?php echo number_format($_num); ?>
						</td>
						<td>
							<div class="lineup-column type_left">
								<span class="t_blue"><?php echo $v['o_ordernum']; ?></span>
								<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
							</div>
						</td>
						<td class="this_item">
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
											<div class="item_name"><a href="<?php echo $adminLink; ?>" target="_blank"><?php echo stripslashes($pinfo['name']); ?></a></div>

											<?php
											if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
											foreach($pinfo['option'] as $sk=>$sv){
												// 달력옵션일 경우 예약일 표시
												$print_dateoption_date = '';
												if($sv['row']['op_dateoption_use'] == 'Y' && rm_str($sv['row']['op_dateoption_date']) > 0  ){
													$print_dateoption_day = date('w',strtotime($sv['row']['op_dateoption_date']));
													$print_dateoption_date = "<span class='ticket_day'>".$sv['row']['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")</span> ";
												}
											?>
												<dd>
													<div class="option_name"><?php echo ($sv['is_addoption']=='N'?'<span class="option">필수</span>':'<span class="add_option">추가</span>'); ?><?php echo $print_dateoption_date;?><?php echo stripslashes($sv['name']); ?></div>
													<span class="mount"><?php echo number_format($sv['cnt']); ?>개</span>

													<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
													<?php if($sv['cancel_refund']=='R'){?>
														<div class="option_btn"><span class="c_tag black">취소요청</span></div>
													<?php }else if($sv['cancel_refund']=='Y'){?>
														<div class="option_btn"><span class="c_tag black line">취소완료</span></div>
													<?php }?>
													<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
												</dd>
											<?php
													}
												}else{
											?>
												<span class="mount"><?php echo number_format($pinfo['cnt']); ?>개</span>
												<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
												<?php if($pinfo['cancel_refund']=='부분취소요청'){?>
												  <span class="c_tag black">취소요청</span>
												<?php }else if($pinfo['status']=='주문취소'){?>
												  <span class="c_tag black line">취소완료</span>
												<?php }?>
												<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
											<?php } ?>

										</dt>
									</dl>
								</div><!-- end order_item -->
								<div class="other_tag">
									<?php echo $pinfo['ptype_icon']; // 상품타입에 따른 아이콘 ?>
									<?php echo ($pinfo['status']?$arr_adm_button[$pinfo['status']]:$arr_adm_button['결제실패']); ?>
								</div>
							</div><!-- end order_item_thumb -->

							<?php
								// 나머지 주문상품별 옵션 노출
								if(count($arr_pinfo)>0){
									foreach($arr_pinfo as $pinfo){

										$userLink = '/?pn=product.view&code='.$pinfo['code']; // 사용자 링크 사용시
										$adminLink = '_product.form.php?_mode=modify&_code='.$pinfo['code'];
										if( $pinfo['op_ptype'] == 'ticket'){
											$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$pinfo['code'];
										}

							?>
								<div class="order_item_thumb">
									<a href="<?php echo $adminLink?>" title="<?php echo addslashes($pinfo['name']); ?>" target="_blank" class="thumb"><img src="<?php echo $pinfo['img']; ?>" alt="<?php echo addslashes($pinfo['name']); ?>"></a>
									<div class="order_item">
										<dl>
											<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
												<div class="entershop"><?php echo showCompanyInfo($pinfo['cpid']); ?></div>
											<?php } ?>
											<dt>
												<div class="item_name"><a href="<?php echo $adminLink; ?>" target="_blank"><?php echo stripslashes($pinfo['name']); ?></a></div>

												<?php
													if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
														foreach($pinfo['option'] as $sk=>$sv){

														// 달력옵션일 경우 예약일 표시
														$print_dateoption_date = '';
														if($sv['row']['op_dateoption_use'] == 'Y' && rm_str($sv['row']['op_dateoption_date']) > 0  ){
															$print_dateoption_day = date('w',strtotime($sv['row']['op_dateoption_date']));
															$print_dateoption_date = "<span class='ticket_day'>".$sv['row']['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")</span> ";
														}

												?>
														<dd>
															<div class="option_name"><?php echo ($sv['is_addoption']=='N'?'<span class="option">필수</span>':'<span class="add_option">추가</span>'); ?><?php echo $print_dateoption_date;?><?php echo stripslashes($sv['name']); ?></div>
															<span class="mount"><?php echo number_format($sv['cnt']); ?>개</span>
															<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
															<?php if($sv['cancel_refund']=='R'){?>
															  <span class="c_tag black">취소요청</span>
															<?php }else if($sv['cancel_refund']=='Y'){?>
															  <span class="c_tag black line">취소완료</span>
															<?php }?>
															<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
														</dd>
												<?php
														}
													}else{
												?>
														<span class="mount"><?php echo number_format($pinfo['cnt']); ?>개</span>
														<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
														<?php if($pinfo['cancel_refund']=='부분취소요청'){?>
															<span class="c_tag black" style="margin:5px 5px;">취소요청</span>
														<?php }else if($pinfo['status']=='주문취소'){?>
															<span class="c_tag black line" style="margin:5px 5px;">취소완료</span>
														<?php }?>
														<!-- KAY :: 2021-09-10 :: 부분취소알림 패치 -->
												<?php } ?>
											</dt>
										</dl>
									</div><!-- end order_item -->
									<div class="other_tag">
										<?php echo $pinfo['ptype_icon']; // 상품타입에 따른 아이콘 ?>
										<?php echo $arr_adm_button[$pinfo['status']]; ?>
									</div>
								</div><!-- end order_item_thumb -->
							<?php
									}
								}
							?>


							<div class="order_item_tag">
								<?php echo $device_icon; ?>
								<?php if($cash_status!=''){?><?php echo $arr_adm_button[$cash_status]; ?><?php }?>
							</div>
						</td>
						<td class="t_red t_right this_price t_bold">
							<div class="lineup-row type_end">
								<?php echo number_format($v['o_price_real']); ?>원
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
							<span class="hidden_tx">주문일</span>
							<?php
								// 결제완료일이 있으면 노출 없으면 주문일 노출
								$app_rdate = ($v['o_paydate']<> '0000-00-00 00:00:00' ? $v['o_paydate'] : $v['o_rdate']);
							?>
							<?php echo printDateInfo($app_rdate); ?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray ">상세보기</a>
								<a href="#none" onclick="cancel('_order.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'cancel', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 black line">주문취소</a>
								<a href="#none" onclick="window.open('<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php<?php echo URI_Rebuild('?', array('_mode'=>'print', 'ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>' ,'print','width=860,height=820,scrollbars=yes'); return false;" class="c_btn gray line only_pc_view">주문인쇄</a>
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
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>


		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>

	</form>
</div>
<!-- / 데이터 리스트 -->



<script>

	<?php if($view == 'online'){ ?>
	// # -- 2016-11-28 LCY :: 무통장다수개처리
	function select_paystatus_send()
	{
		if(confirm('선택된 항목을 입금확인 처리 하시겠습니까?(주문연동이 자동적용됩니다.)') == false){
			return false;
		}

		// -- 체크항목
		 if($('.js_ck').is(':checked')){

			$('form[name=frm]').children('input[name=_mode]').val('select_paystatus');
			$('form[name=frm]').attr('action' , '_order.pro.php');
			document.frm.submit();
		 }
		 else { // 체크 안되었을 시
			 alert('1건 이상 선택시 입금확인이 가능합니다.');
		 }

	}
	// # -- 2016-11-28 LCY :: 무통장다수개처리
	<?php } ?>

	 function selectModify() {
		 if($('.js_ck').is(":checked")){
			 document.frm.submit();
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
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

	 function selectExcel() {
		 if($('.js_ck').is(':checked')){
			$('form[name=frm]').children('input[name=_mode]').val('get_excel');
			$('form[name=frm]').attr('action' , '_order.pro.php');
			document.frm.submit();
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
	 function selectPrint() {
	 	 if($('.js_ck').is(':checked')){
	 		$('form[name=frm]').children('input[name=_mode]').val('mass_print');
	 		$('form[name=frm]').attr('target' , 'mass_print');
	 		$('form[name=frm]').attr('action' , '<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php');
	 		document.frm.submit();
	 	 }
	 	 else {
	 		 alert('1개 이상 선택해 주시기 바랍니다.');
	 	 }
	 }

	<?php // LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 ?>
	function search_excel_send() {
		$('form[name=frm]').children('input[name=_mode]').val('get_search_excel');
		$('form[name=frm]').attr('action' , '_order.pro.php');
		document.frm.submit();
	}

</SCRIPT>





<?php include_once('wrap.footer.php'); ?>