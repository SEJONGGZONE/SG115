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

// 주문 번호별 구분 색상
$DivisionColor = 'F5F5F5';

# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData); // 'totalAdmin' or 'subAdmin'

# 쿼리 조건
$s_query = " where (1) and `o`.`npay_order` = 'Y' ".($AdminPath == 'subAdmin'?" and `op`.`op_partnerCode` = '{$_COOKIE["AuthCompany"]}' ":null);


// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
if($pass_input_value != ''){
	$arr_input_que = array();

	if( in_array($pass_input_type, array('all','pass_npay_order_group')) > 0 ){ $arr_input_que[] = " op.npay_order_group like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_npay_order_code')) > 0 ){ $arr_input_que[] = " npay_order_code like '%".$pass_input_value."%'  "; }
	if( in_array($pass_input_type, array('all','pass_ordernum')) > 0 ){ $arr_input_que[] = " o_ordernum like '%".$pass_input_value."%'  "; }
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


if($pass_sync) $s_query .= " and npay_sync = '{$pass_sync}' "; // 연동상태

// 주문기기
if( $pass_mobile == 'Y'){ $s_query .= " and o.mobile in('Y','A')  "; }
else if($pass_mobile == 'N') { $s_query .= " and o.mobile = 'N'  "; }


if($pass_com) $s_query .= " and `op`.`op_partnerCode` = '{$pass_com}' "; // 입점업체
if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; } // 회원구분

// 결제수단
if( count($pass_paymethod) > 0){
	$s_query .= " and (o_paymethod in('".implode("','",$pass_paymethod)."') or o_easypay_paymethod_type in('".implode("','",$pass_paymethod)."')  )  ";
}

// 진행상태
if(count($pass_status) > 0){
	$s_query .=" and (op.npay_status in('".implode("','",$pass_status)."') ) ";
}


// 받는분
if( $pass_rname !="" ) { $s_query .= " and o_rname like '%".$pass_rname."%' "; }
if( rm_str($pass_rhp) !="" ) { $s_query .= " and replace(o_rhp,'-','') like '%".rm_str($pass_rhp)."%' "; }
if( $pass_address !="" ) { $s_query .= " and concat(ifnull(o_raddr_doro,''),ifnull(o_raddr2,'')) like '%".$pass_address."%'  "; }

// 사용자
if( $pass_uname !="" ) { $s_query .= " and o_uname like '%".$pass_uname."%' "; }
if( rm_str($pass_uhp) !="" ) { $s_query .= " and replace(o_uhp,'-','') like '%".rm_str($pass_uhp)."%' "; }
if( $pass_uemail !="" ) { $s_query .= " and o_uemail like '%".$pass_uemail."%' "; }

// 날짜검색
if( $pass_sdate !="" ) { $s_query .= " and date(o_rdate) >='".$pass_sdate."' "; }
if( $pass_edate !="" ) { $s_query .= " and date(o_rdate) <='". $pass_edate ."' "; }




# 쿼리
$listmaxcount = $listmaxcount ? $listmaxcount : 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;
$que = " select count(*) as `cnt` from `smart_order_product` as `op` left join `smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) {$s_query} ";
$res = _MQ($que);
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);

if(!$st) $st = "o_rdate"; // 결제완료일 우선 정렬
$st = stripslashes($st);
if(!$so) $so = 'desc';

$que = "
	select
		`op`.*,
		`o`.*,
		`op`.`npay_status` as `npay_status`,
		`o_rtel` as `ordertel`,
		`o_rhp` as `orderhtel`,
		`p_name`,`p_img_list_square`
	from
		`smart_order_product` as `op` left join
		`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) left join
		`smart_product` as `p` on(`op`.`op_pcode` = `p`.`p_code`)
		{$s_query}
	order by {$st} {$so} limit $count , $listmaxcount
";

$res = _MQ_assoc($que);
if(count($res) <= 0) $res = array();


# 공금업체 리스트 추출
$arr_customer = arr_company();


// LDD: 2019-01-18 네이버페이 패치
$StatusArray = array(
	  'PAYED' => '결제 완료'
	, 'DISPATCHED' => '발송처리'
	, 'CANCEL_REQUESTED' => '취소 요청'
	, 'RETURN_REQUESTED' => '반품 요청'
	, 'EXCHANGE_REQUESTED' => '교환 요청'
	, 'EXCHANGE_REDELIVERY_READY' => '교환 재배송 준비'
	, 'HOLDBACK_REQUESTED' => '구매 확정 보류 요청'
	, 'CANCELED' => '취소'
	, 'RETURNED' => '반품'
	, 'EXCHANGED' => '교환'
	, 'PURCHASE_DECIDED' => '구매 확정'
);
$SyncIcon = array(
	'Y'=>'<span class="c_tag darkgreen line t4">연동완료</span>',
	'R'=>'<span class="c_tag black t4">연동대기</span>',
	'A'=>'<span class="c_tag green line t4">후연동</span>'
);
// LDD: 2019-01-18 네이버페이 패치


?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" class="data_search <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>



	<?php // 통합검색 ?>
	<div class="comp_search">
		<div class="form_wrap">
			<select name="pass_input_type">
				<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
				<option value="pass_npay_order_group" <?php echo $pass_input_type == 'pass_npay_order_group' ? 'selected' : ''?>>N 주문번호</option>
				<option value="pass_npay_order_code" <?php echo $pass_input_type == 'pass_npay_order_code' ? 'selected' : ''?>>N 상품주문번호</option>
				<option value="" disabled>----------</option>
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
					<th>진행상태</th>
					<td colspan="5">
						<?php
						echo _InputCheckbox('pass_status', array_keys($StatusArray), $pass_status, '', array_values($StatusArray), '-상태-');
						?>
					</td>
				</tr>
				<tr>
					<th>결제수단</th>
					<td colspan="5">
						<?php
						$arr_paymethod = array(
							"신용카드" => "card",
							"계좌이체" => "iche",
							"무통장입금" => "online",
							"포인트결제" => "point",
							"가상계좌" => "virtual",
							"휴대폰" => "phone"
						);
						echo _InputCheckbox('pass_paymethod', array_values($arr_paymethod), $pass_paymethod, '', array_keys($arr_paymethod), '전체');
						?>
					</td>
				</tr>
				<tr>
					<th>연동상태</th>
					<td colspan="5">
						<?php echo _InputRadio('pass_sync', array('', 'Y', 'R', 'A'), $pass_sync, '', array('전체', '연동완료', '연동대기', '후연동')); ?>
					</td>
				</tr>
				<tr>
					<th>회원구분</th>
					<td>
						<?php echo _InputRadio( "pass_memtype" , array('','Y','N'), $pass_memtype , "" , array('전체','회원','비회원')); ?>
					</td>
					<th>주문기기</th>
					<td>
						<label class="design"><input type="radio" name="pass_mobile" value=""<?php echo (empty($pass_mobile) || $pass_mobile == ''?' checked':null); ?>>전체</label>
						<label class="design"><input type="radio" name="pass_mobile" value="N"<?php echo ($pass_mobile == 'N'?' checked':null); ?>>PC</label>
						<label class="design"><input type="radio" name="pass_mobile" value="Y"<?php echo ($pass_mobile == 'Y'?' checked':null); ?>>모바일</label>
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
                    <th>참고사항</th>
					<td colspan="5">
						<div class="tip_box">
							<?php echo _DescStr('발주처리, 배송처리, 취소처리는 상세보기를 통해서만 가능 합니다. (네이버페이 제약사항)'); ?>
							<?php echo _DescStr('네이버페이 주문정보는 정산처리가 불가능 합니다. 네이버주문은 네이버페이 센터를 이용바랍니다.'); ?>
							<?php echo _DescStr('엑셀다운로드에 표기 되는 <em>N 포인트 사용</em>과 <em>N 적립금 사용</em>은 동일 주문번호의 전체 주문을 기준으로합니다.', 'black'); ?>
							<?php echo _DescStr('네이버페이(주문형)의 경우 티켓상품을 지원하지 않습니다.'); ?>
							<?php echo _DescStr('최종 자동동기화 : <strong>'.$siteInfo['npay_sync_date'].'</strong> (네이버 콜백시스템과 별도로 1시간 단위로 동기화 되는 프로세스가 작동된 최종 시간입니다.)'); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _DescStr($SyncIcon['Y'].' 주문수집 시 누락없이 일괄 수집 된 주문'); ?>
							<?php echo _DescStr($SyncIcon['R'].' 상품정보가 누락되어 주문처리가 불가능한 주문 '); ?>
							<?php echo _DescStr($SyncIcon['A'].' 상품정보가 추가 수집되어 처리가 가능한 주문'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>


		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"></span></li>
				<?php if($mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form><!-- end data_search -->



<form action="_npay_order.pro.php" method="POST" class="form_list"<?php echo ($c?null:' target="common_frame"'); ?>>
	<input type="hidden" name="_mode" value="get_search_excel">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>

	<div class="data_list">
		<div class="list_ctrl">

			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
			</div>

			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운 <?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>

				<select onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'desc'?' selected':null); ?>>주문일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'o_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'o_rdate' && $so == 'asc'?' selected':null); ?>>주문일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'op_price' && $so == 'desc'?' selected':null); ?>>결제금액 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'op_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'op_price' && $so == 'asc'?' selected':null); ?>>결제금액 ▲</option>
				</select>

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<table class="table_list type_nocheck">
			<colgroup>
				<col width="40"><col width="70"/><col width="150"><col width="150"/>
				<col width="*"/>
				<col width="120"/><col width="100"/><col width="80"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">연동/진행상태</th>
					<th scope="col">주문번호/주문자</th>
					<th scope="col">상품/주문정보</th>
					<th scope="col">결제금액/결제수단</th>
					<th scope="col">주문일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$tmpOrder = ''; // 주문 번호별 구분 색상 - 주문코드
				$tmpNum = 0; // 주문 번호별 구분 색상 - 주문번호별 넘버
				foreach($res as $k=>$v) {

					// // 주문 번호별 구분 색상 - 주문별 배경색 채우기
					if($tmpOrder != $v['op_oordernum']) $tmpNum++;
					if($tmpNum%2 === 0) $TDcolor = ' style="background-color:#'.$DivisionColor.'"';
					else $TDcolor = null;
					$tmpOrder = $v['op_oordernum'];

					// 순서
					$_num = $TotalCount - $count - $k;

					// 상태아이콘
					$StatusIcon = '';
					if($v['npay_status'] == 'PAYED') $StatusIcon = '<span class="c_tag blue h22 t4">결제완료</span>';
					if($v['npay_status'] == 'PLACE') $StatusIcon = '<span class="c_tag darkgreen h22 t4">발주처리</span>';
					if($v['npay_status'] == 'DISPATCHED') $StatusIcon = '<span class="c_tag green h22 t4">발송처리</span>';
					if($v['npay_status'] == 'CANCELED') $StatusIcon = '<span class="c_tag light h22 t4">주문취소</span>';

					// LDD: 2019-01-18 네이버페이 패치
					if(in_array($v['npay_status'], array('PAYED', 'PLACE', 'DISPATCHED', 'CANCELED')) === false) {
						$StatusIcon = '<span class="c_tag red h22 t4">'.$StatusArray[$v['npay_status']].'</span>';
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
					<tr>
						<td class="this_check">
							<label class="design"><input type="checkbox" name="op_uid[]" class="chk_box js_ck" value="<?php echo $v['op_uid']; ?>"></label>
						</td>
						<td class="this_num"><?php echo number_format($_num); ?></td>
						<td class="this_state">
							<div class="lineup-row type_center">
								<?php echo (!$v['npay_order_group']?$SyncIcon['이전주문']:$SyncIcon[$v['npay_sync']]); ?>
								<?php echo $StatusIcon; ?>
							</div>
						</td>
						<td>
							<div class="lineup-column type_left">
								<span class="t_blue"><?php echo $v['op_oordernum']; ?></span>
								<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
							</div>
						</td>
						<td>
							<div class="order_item_thumb">
								<div class="thumb">
									<a href="<?php echo $adminLink; ?>" target="_blank">
										<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>" >
									</a>
								</div>
								<div class="order_item">
									<dl>
										<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
											<div class='entershop'><?php echo showCompanyInfo($v['op_partnerCode']); ?></div>
										<?php } ?>
										<dt>
											<div class="item_name">
												<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo htmlspecialchars_decode($v['op_pname']); ?></a>
											</div>
											<span class="mount"><?php echo number_format($v['op_cnt']); ?>개</span>
										</dt>
										<?php echo ($v['op_option1']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option1']).'</dd>':null); ?>
										<?php echo ($v['op_option2']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option2']).'</dd>':null); ?>
										<?php echo ($v['op_option3']?'<dd>'.($v['op_is_addoption'] == 'N'?'<span class="option">필수</span> : ':'<span class="add_option">추가</span> ').htmlspecialchars_decode($v['op_option3']).'</dd>':null); ?>

										<dd class="this_npay">
											<?php if($v['npay_order_group']) { ?>
											<span class="npay_tag">
												N 주문번호 : <?php echo ($v['npay_order_group']?$v['npay_order_group']:'이전주문'); ?>
											</span>
											<?php } ?>
											<span class="npay_tag">
												N 상품주문번호 : <?php echo ($v['npay_order_code']?$v['npay_order_code']:'연동대기'); ?>
											</span>
										</dd>
									</dl>

									<ul class="user_apply">
										<li>주문자 휴대폰 : <?php echo ($v['orderhtel']?$v['orderhtel']:null); ?></li>
									</ul>
								</div><!-- end order_item -->
							</div>
							<div class="order_item_tag">
								<?php if($v['mobile'] == 'Y') { ?>
								<span class="c_tag h18 mo">MO</span>
								<?php } else { ?>
									<span class="c_tag h18 pc">PC</span>
								<?php } ?>
							</div>
						</td>
						<td class="this_date t_red t_right this_price t_bold">
							<div class="lineup-row type_end">
								<?php echo number_format($v['op_price'] * $v['op_cnt']); ?>원
								<?php echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]]; ?>
							</div>
						</td>
						<td >
							<span class="hidden_tx">주문일</span>
							<?php echo printDateInfo($v['o_rdate']) ?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_npay_order.form.php?_mode=modify&_uid=<?php echo $v['op_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn gray">상세보기</a>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<?php if(count($res) <= 0) { ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
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
		var cnt = $('.chk_box:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
	}

	$(function() {
		// 전체선택 or 해제
		$('body').delegate('.allchk', 'click', function() {
			var chk = $(this).is(':checked');
			if(chk === true) $('input:checkbox.chk_box').removeAttr('checked').attr('checked', true);
			else $('input:checkbox.chk_box').removeAttr('checked');
			$('.allchk').attr('checked', chk);
		});
	});

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


</script>
<?php include_once('wrap.footer.php'); ?>