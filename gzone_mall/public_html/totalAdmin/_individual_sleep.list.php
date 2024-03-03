<?php

	// -- LCY -- 회원리스트
	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// 회원타입을 선택하지 않으면 전체선택
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N','A'); }  // {LCY} : 하이앱 

	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_individual_sleep as indr where 1 and in_sleep_type = 'Y' AND in_out = 'N' ";
	include_once("_individual.inc_search.php");
	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'ins_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");


?>


<div class="data_list">
	<form name="frm" id="frm" method="post">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="searchQue" value="<?php echo enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?php echo count($res)?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?php echo $_PVSC?>">
		<input type="hidden" name="apply" value="true">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_id" value="">


		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick="ctrlReturn('select'); return false;" class="c_btn h27 blue ">선택 휴면해제</a>
				<a href="#none" onclick="ctrlReturn('search'); return false;" class="c_btn h27 blue ">검색 휴면해제(<?php echo number_format($TotalCount)?>)</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="ctrlOutMail('select'); return false;" class="c_btn h27 green line">선택 휴면메일발송</a>
				<a href="#none" onclick="ctrlOutMail('search'); return false;" class="c_btn h27 green line">검색 휴면메일발송(<?php echo number_format($TotalCount)?>)</a>
				<a href="#none" onclick="ctrlExcelDownload('select'); return false;"  class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="ctrlExcelDownload('search'); return false;"  class="c_btn icon icon_excel only_pc_view">검색 엑셀다운(<?=number_format($TotalCount)?>)</a>

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'ins_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'ins_rdate' && $so == 'asc'?' selected':null); ?>>전환일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'ins_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'ins_rdate' && $so == 'desc'?' selected':null); ?>>전환일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'asc'?' selected':null); ?>>아이디 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'desc'?' selected':null); ?>>아이디 ▼</option>
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
				<col width="*"/><col width="130"/>
				<col width="100"/><col width="100"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">No</th>
					<th scope="col">승인</th>
					<th scope="col">휴면메일 발송</th>
					<th scope="col">성명(아이디)/이메일</th>
					<th scope="col">휴대폰</th>
					<th scope="col">휴면 전환일</th>
					<th scope="col">최근 접속일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);
					$printEmail = $v['in_email'] != '' ? trim($v['in_email']):'';
					$printTel = rm_str($v['in_tel']) == '' ? '-' : tel_format($v['in_tel']);  // 전화
					$printTel2 = rm_str($v['in_tel2']) == '' ? '-' : tel_format($v['in_tel2']);  // 휴대폰

					$printSendEmail = $v['ins_mailing'] == 'Y' ? '<span class="t_green">휴면메일 발송함</span>':'<span class="t_light">휴면메일 미발송</span>'; // 휴면메일발송여부
					$printSleepdate = rm_str($v['ins_rdate']) > 0 ?  printDateInfo($v['ins_rdate']) : '-'; // 휴면전환일
					$printLdate = rm_str($v['in_ldate']) > 0 ?  printDateInfo($v['in_ldate']) : '-'; // 최근접속일

					// -- 승인여부
					if($v['in_auth']  != 'Y' ){
						$printAuth = '<span class="c_tag h18 light t3">미승인</span>';
					}else{
						$printAuth = '<span class="c_tag gray h18 blue line t3">승인</span>';
					}
			?>
					<tr>
						<td class="this_check"><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="<?php echo $v['in_id'];?>"></label></td>
						<td class="this_num"><?php echo $_num;?></td>
						<td class="this_state"><?php echo $printAuth;?></td>
						<td><?php echo $printSendEmail;?></td>
						<td class="t_left">
							<div class="lineup-row type_side"><strong class="t_black"><?php echo $v['in_name'];?></strong><strong class="t_sky"><?php echo $v['in_id'];?></strong></div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo $printEmail;?>
						</td>
						<td><?php echo $printTel2;?></td>
						<td class="this_date"><?php echo $printSleepdate;?></td>
						<td><span class="hidden_tx">최근 접속일</span><?php echo $printLdate;?></td>
						<td class="this_ctrl"><a href="#none" onclick="return flase;" class="c_btn blue line get-return" data-id="<?php echo $v['in_id'];?>">휴면해제</a></td>
					</tr>
			<?php	}?>
			</tbody>

		</table>

		<?php if(count($res) <  1) {  ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">휴면회원이 없습니다.</div></div>
		<?php } ?>

	</form>
</div>



<div class="paginate">
<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
</div>



<script>

	// -- 선택 엑셀 다운로드
	function ctrlExcelDownload(ctrlMode)
	{
		if( ctrlMode == 'select'){ // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}

		$('form#frm [name="_mode"]').val('getExcelDownload');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');

		frm.submit();
	}

	// -- 휴면해제 처리
	function ctrlReturn(ctrlMode)
	{
		if(ctrlMode == 'select'){
				if(confirm("선택된 회원을 휴면해제 하시겠습니까?") == false){ return false; }
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
				if(confirm("검색된 회원을 휴면해제 하시겠습니까?") == false){ return false; }
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}else{ return false; }

		$('form#frm [name="_mode"]').val('getSleepReturn');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');
		$('form#frm').submit();
	}

		// -- 휴면해제 처리
	function ctrlOutMail(ctrlMode)
	{
		if(ctrlMode == 'select'){
			if(confirm("선택된 회원에게 휴면메일을 발송하시겠습니까?") == false){ return false; }
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
			if(confirm("검색된 회원에게 휴면메일을 발송하시겠습니까?") == false){ return false; }
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}else{ return false; }

		$('form#frm [name="_mode"]').val('getSleepReturnMail');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');
		$('form#frm').submit();
	}


	$(document).on('click', '.get-return', function() {

		var _id = $(this).data('id');

		if(confirm("회원을 휴면해제 하시겠습니까?") == false){ return false; }
		$('form#frm [name="_mode"]').val('getSleepReturnId');
		$('form#frm [name="_id"]').val(_id);
		$('form#frm').attr('action','_individual_sleep.pro.php');
		$('form#frm').submit();
	});


</script>
<?php
	include_once('wrap.footer.php');

	# 수신거부 고객을 포함하여 재발송 확인
	include_once OD_ADDONS_ROOT."/080deny/_inc.reconfirm.php";
?>
