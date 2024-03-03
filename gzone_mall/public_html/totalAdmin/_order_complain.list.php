<?php
include_once('wrap.header.php');

// 상태값 재정의
$arr_apply_order_complain = $arr_order_complain;

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
$s_query = " and o.o_canceled != 'Y' and o.o_paystatus = 'Y' ";
$date_type = 'op_complain_date';


// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " o_ordernum like '%".$pass_input_value."%'  "; } // 주문번호
	if( in_array($pass_input_type, array('all','pass_oname')) > 0 ){ $arr_input_que[] = " o_oname like '%".$pass_input_value."%'  "; } // 주문자명
	if( in_array($pass_input_type, array('all','pass_mid')) > 0 ){ $arr_input_que[] = " o_mid like '%".$pass_input_value."%'  "; } // 주문자 ID
	if( in_array($pass_input_type, array('all','pass_ohp')) > 0 && rm_str($pass_input_value) != '' ){ $arr_input_que[] = " replace(o_ohp,'-','') like '%".rm_str($pass_input_value)."%'  "; } // 주문자 휴대폰
	if( in_array($pass_input_type, array('all','pass_deposit')) > 0 ){ $arr_input_que[] = " o_deposit like '%".$pass_input_value."%'  "; } // 입금자명
	if( in_array($pass_input_type, array('all','pass_pname')) > 0 ){
		$arr_input_que[] = " op_pname like '%".$pass_input_value."%'  ";
	} // 주문 상품명
	if( in_array($pass_input_type, array('all','pass_pcode')) > 0 ){
		$arr_input_que[] = " op_pcode like '%".$pass_input_value."%'  ";
	} // 주문상품 코드
	if( in_array($pass_input_type, array('all','pass_optionname')) > 0 ){
		$arr_input_que[] = " concat(ifnull(op_option1,''),ifnull(op_option2,''),ifnull(op_option3,'')) like '%".$pass_input_value."%' ";
	} // 주문상품 옵션명
	if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
}
// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. }

// 교환/반품상태
if($pass_complain) $s_query .= " and op.op_complain = '${pass_complain}' ";
else $s_query .= " and op.op_complain != '' ";

if($pass_sendstatus) $s_query .= " and op.op_sendstatus='{$pass_sendstatus}' "; // 배송상태
if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; } // 회원구분

// 주문기기
if( $pass_mobile == 'Y'){ $s_query .= " and mobile in('Y','A')  "; }
else if($pass_mobile == 'N') { $s_query .= " and mobile = 'N'  "; }


// 날짜검색
if( $pass_sdate !="" ) { $s_query .= " and date(op_complain_date) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(op_complain_date) <='". $pass_edate ."' "; }


// 입점업체
if($pass_com) {
	$s_query .= " and  op_partnerCode = '".addslashes($pass_com)."' ";
}

$DateTypeArr = array(
	'op_complain_date'=>'신청일',
	'op_rdate'=>'주문일',
	'op_paydate'=>'결제일',
	'op_senddate'=>'배송완료일'
);

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
//if(!$st) $st = $date_type;
if(!$st) $st = 'op_senddate'; // 기본정렬값
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
		op.*, o.*, p.p_name,p.p_img_list_square
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
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey; ?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>교환/반품 신청일</th>
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
					<th>교환/반품상태</th>
					<td colspan="3">
						<?php echo _InputRadio( "pass_complain" , array_merge(array(''),array_keys($arr_apply_order_complain)), $pass_complain , "" , array_merge(array('전체'),array_values($arr_apply_order_complain)) , ''); ?>
					</td>
					<th>배송상태</th>
					<td >
						<?php echo _InputRadio('pass_sendstatus', array('', '배송중', '배송완료'), $pass_sendstatus, '', array('전체','배송중', '배송완료'), ''); ?>
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
						<?php echo _DescStr('교환/반품은 배송상품 주문건만 해당되며, 배송이후 상품개별적으로 취소한 경우입니다.',''); ?>
						<?php echo _DescStr('교환/반품 가능한 단계 : 배송중 / 배송완료','red'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('교환/반품 신청 : 고객이 교환/반품을 신청만 한 상태입니다. (수정가능)',''); ?>
						<?php echo _DescStr('교환/반품 완료 : 별도의 부분취소 처리 없이 바로 완료 처리된 상태입니다. (수정가능)',''); ?>
						<?php echo _DescStr('PG연동 : PG 연동신청으로 부분취소 목록에 요청건으로 추가된 상태입니다. (수정불가)',''); ?>
						<?php echo _DescStr('적립금 환불 : 적립금 환불 신청으로 부분취소 목록에 요청건으로 추가된 상태입니다. (수정불가)',''); ?>
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
	<!-- ●리스트 컨트롤영역 -->
	<div class="list_ctrl">
		<div class="left_box">
			<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
			<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
			<?php if($AdminPath == 'totalAdmin'){?>
			<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black">선택 요청취소</a>
			<?php } ?>
		</div>
		<div class="right_box">
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_senddate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'op_senddate' && $so == 'desc'?' selected':null); ?>>요청일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_senddate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'op_senddate' && $so == 'asc'?' selected':null); ?>>요청일 ▲</option>
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
			<col width="40"><col width="70"><col width="180"><col width="150">
			<col width="*">
			<col width="100"><col width="90">
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
				<th scope="col">No</th>
				<th scope="col">상태설정</th>
				<th scope="col">주문번호/주문자</th>
				<th scope="col">주문/신청정보</th>
				<th scope="col"><?php echo str_replace(' ', '', $DateTypeArr[$date_type]); ?></th>
				<th scope="col">상세보기</th>
			</tr>
		</thead>
		<?php if(count($res) > 0) { ?>
			<tbody>
				<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount-$coun -$k;
					$arr_text_info = _text_info_extraction("smart_order_product", $v['op_uid']);
					$v = array_merge($v, $arr_text_info);

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
					<tr data-uid="<?php echo $v['op_uid']; ?>">
						<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck" value="<?php echo $v['pt_uid'];?>"></label></td>
						<td class="this_num"><?php echo number_format($_num); ?></td>
						<td class="this_state">
							<div class="lineup-row type_center">
								<?php if($v['op_cancel'] === 'Y') { ?>
									<span class="c_tag h22 blue line t4">취소완료</span>
									<?php echo ($v['op_cancel_type'] == 'pg'?'<span class="c_tag green line t5">PG연동</span>':'<span class="c_tag purple line t5">적립금환불</span>'); ?>
								<?php } else if($v['op_cancel'] === 'R') { ?>
									<span class="c_tag h22 black t4">취소요청</span>
									<?php echo ($v['op_cancel_type'] == 'pg'?'<span class="c_tag green line t5">PG연동</span>':'<span class="c_tag purple line t5">적립금환불</span>'); ?>
								<?php } else { ?>
									<?php echo _InputSelect( "op_complain" , array_keys($arr_apply_order_complain) , $v['op_complain'] , "" , array_values($arr_apply_order_complain) , ""); ?>
									<a href="#none" onclick="return false;" class="c_btn h28 blue js_submit">적용</a>
								<?php } ?>
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
											<?php echo (!$v['op_option1'] ? '<span class="mount">'. number_format($v['op_cnt']) .'개</span>' : null); ?>
										</dt>

										<?php if($v['op_option1'] || $v['op_option2'] || $v['op_option3']) { ?>
											<?php
												// KAY :: 2022-12-07 :: 옵션 구분 수정
												$arr_option_name = array($v['op_option1'] ,$v['op_option2'],$v['op_option3'] );
												$arr_option_name = array_filter($arr_option_name);
											?>
											<dd>
												<div class="option_name"><?php echo ($v['op_is_addoption']=="Y" ? "<span class='add_option'>추가옵션</span> : " : "<span class='option'>필수옵션</span>" )." : ".implode(" / ",$arr_option_name); ?></div>
												<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
											</dd>
										<?php } ?>
									</dl>
									<ul class="user_apply">
										<li><?php echo nl2br(htmlspecialchars($v['op_complain_comment'])); ?></li>
									</ul>
								</div><!-- end order_item -->
							</div><!-- end order_item_thumb -->

							<div class="order_item_tag">
								<?php echo $device_icon; ?>
								<?php echo $arr_adm_button[$v['op_sendstatus']]; ?>
							</div>

						</td>
						<td class="this_date">
							<?php
							if($date_type == 'op_senddate') $v[$date_type] = $v[$date_type].' 00:00:00'; // 배송완료일은 date포맷이 다르기 때문에 맞춰준다
							?>
							<?php echo ($v[$date_type] == '0000-00-00 00:00:00'?'-':printDateInfo($v[$date_type])); ?>

						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_order.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'view'=>'order_complain', '_ordernum'=>$v['op_oordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray">상세보기</a>
								<?php if($v['op_cancel'] === 'N') { ?>
									<a href="_order_complain.pro.php<?php echo URI_Rebuild('?', array('op_complain'=>'reset', 'uid'=>$v['op_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 black line" target="common_frame">요청취소</a>
								<?php } else { ?>
									<a href="#none" onclick="alert('부분취소로 넘어간 경우 취소 불가합니다.'); return false;" class="c_btn light">취소불가</a>
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

<script type="text/javascript">
	// 교환/반품상태 변경
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		var su = $(this).closest('tr');
		var _uid = su.data('uid');
		var op_complain = su.find('select[name^=op_complain] option:selected').val();
		var _url = '_order_complain.pro.php';
		if(!op_complain) {
			alert('교환/반품상태를 선택하세요.');
			su.find('select[name^=op_complain]').focus();
			return false;
		}


		if( op_complain == '완료/부분취소요청(PG연동)' || op_complain == '완료/부분취소요청(적립금 환불)'){
			<?php if( $AdminPath != 'totalAdmin'){?>
			alert("부분취소요청 상태적용은 통합관리자만 가능합니다.");
			return false;
			<?php } ?>

			if( confirm("이전 상태로 되돌릴 수 없습니다.\n그래도 처리하시겠습니까?") == false){
				return false;
			}
		}




		_url = _url+'?&uid='+_uid+'&op_complain='+op_complain;
		common_frame.location.href = _url;
	});
</script>
<?php include_once('wrap.footer.php'); ?>