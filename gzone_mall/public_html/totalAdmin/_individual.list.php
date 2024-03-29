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
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N','A','G'); }

	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_individual as indr where 1 and in_sleep_type = 'N' AND in_out = 'N' ";

	include_once("_individual.inc_search.php");
	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'in_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");

?>

<!-- ● 데이터 리스트 -->
<div class="data_list">
	<form name="frm" id="frm" method="post">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search_que" value="">
		<input type="hidden" name="searchQue" value="<?php echo enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?php echo $TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?php echo $_PVSC?>">

		<div class="list_ctrl">
			<div class="left_box type_autofill">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<span class="nowrap">
					<a href="#none" onclick="ctrlAuth('Y'); return false;" class="c_btn h27 blue ">선택 승인</a>
					<a href="#none" onclick="ctrlAuth('N'); return false;" class="c_btn h27 black ">선택 미승인</a>
				</span>
			</div>
			<div class="right_box">
				<span class="nowrap">
					<a href="#none" onclick="ctrlSmsSend('select'); return false;" class="c_btn h27 green line">선택 문자발송</a>
					<a href="#none" onclick="ctrlSmsSend('search'); return false;" class="c_btn h27 green line">검색 문자발송 (<?php echo number_format($TotalCount)?>)</a>
				</span>
				<a href="#none" onclick="ctrlExcelDownload('select'); return false;"  class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="ctrlExcelDownload('search'); return false;"  class="c_btn icon icon_excel only_pc_view">검색 엑셀다운 (<?php echo number_format($TotalCount)?>)</a>
				<span class="nowrap">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_rdate' && $so == 'asc'?' selected':null); ?>>가입일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_rdate' && $so == 'desc'?' selected':null); ?>>가입일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_ldate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_ldate' && $so == 'asc'?' selected':null); ?>>최근접속일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_ldate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_ldate' && $so == 'desc'?' selected':null); ?>>최근접속일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_name' && $so == 'asc'?' selected':null); ?>>이름순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_name' && $so == 'desc'?' selected':null); ?>>이름순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'asc'?' selected':null); ?>>아이디 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'desc'?' selected':null); ?>>아이디 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_point', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_point' && $so == 'asc'?' selected':null); ?>>적립금 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_point', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_point' && $so == 'desc'?' selected':null); ?>>적립금 ▼</option>
					</select>
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>
				</span>
			</div>
		</div>
		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="90"/>
				<col width="100"/><col width="*"/><col width="130"/>
				<col width="100"/><col width="100"/><col width="160"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">No</th>
					<th scope="col">승인</th>
					<th scope="col">회원등급</th>
					<th scope="col">성명(아이디)/이메일</th>
					<th scope="col">휴대폰</th>
					<th scope="col">가입일</th>
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
					$printTel = rm_str($v['in_tel']) == '' ? '<span class="t_none">미등록</span>' : tel_format($v['in_tel']);  // 전화
					$printTel2 = rm_str($v['in_tel2']) == '' ? '<span class="t_none">미등록</span>' : tel_format($v['in_tel2']);  // 휴대폰
					$printRdate = rm_str($v['in_rdate']) > 0 ?  printDateInfo($v['in_rdate']) : '-'; // 가입일
					$printLdate = rm_str($v['in_ldate']) > 0 ?  printDateInfo($v['in_ldate']) : '-'; // 최근접속일

					// -- 최고 관리자는 삭제가 불가능하다.
					$disabledOutClass = $v['in_userlevel'] >= 9 ? ' disabled-admin ':''; // 운영자 게정은 탈퇴불가
					$disabledOutClass .= $v['in_sleep_type'] == 'Y' ? ' disabled-dormancy ':''; // 휴면회원은 탈퇴불가
					$disabledOutClass .= $v['in_out'] == 'Y' ? ' disabled-out ':''; // 탈퇴회원은 탈퇴불가

					$printBtn = '
						<div class="lineup-row type_center">
							<a href="_individual.form.php?_mode=modify&_id='.$v['in_id'].'&_PVSC='.$_PVSC.'" class="c_btn h22 gray">수정</a>
							<a href="#none" onclick="return false;" class="c_btn h22 dark line on-get-secession '.$disabledOutClass.'" data-id="'.$v['in_id'].'" data-apply = "true">탈퇴</a>
							<a href="'.OD_PROGRAM_URL.'/member.login.pro.php?_mode=login&_mode2=master_login&login_id='.$v['in_id'].'" target="_blank" class="c_btn h22 sky line">로그인</a>
						</div>
					'; // 관리버튼


					// -- 승인여부
					if($v['in_auth']  != 'Y' ){
						$printAuth = '<span class="c_tag h18 light t3">미승인</span>';
					}else{
						$printAuth = '<span class="c_tag gray h18 blue line t3">승인</span>';
					}

					// 회원등급
					$printGroup = $arrGroupInfo[$v['in_mgsuid']];

					// -- 출력
					echo '<tr>';
					echo '	<td class="this_check"><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="'.$v['in_id'].'"></label></td>';
					echo '	<td class="this_num">'.$_num.'</td>';
					echo '	<td class="this_state">'.$printAuth.'</td>';
					echo '	<td class="t_blue">'.$printGroup.'</td>';
					echo '	<td class="t_left"><div class="lineup-row type_side"><strong class="t_bold t_black">'.$v['in_name'].'</strong><span class="t_sky">'.$v['in_id'].'</span><div class="dash_line"><!-- 점선라인 --></div></div>'.$printEmail.'</td>';
					echo '	<td>'.$printTel2.'</td>';
					echo '	<td class="this_date">'.$printRdate.'</td>';
					echo '	<td><span class="hidden_tx">최근 접속일</span>'.$printLdate.'</td>';
					echo '	<td class="this_ctrl">'.$printBtn.'</td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>

		<?php if(count($res) <  1) {  ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</form>
</div>

<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
</div>


<script>
	// -- 회원 탈퇴
	$(document).on('click','.on-get-secession',function(){
		var inID = $(this).attr('data-id'); // 회원아이디
		var adminChk = $(this).hasClass('disabled-admin');
		var dormancyChk = $(this).hasClass('disabled-dormancy');
		if( inID == '' || inID == ''){ return false;}
		if( adminChk == true){ alert('관리자 계정은 탈퇴 처리 할 수 없습니다.'); return false; } // 관리자 계정은 탈퇴처리 불가능
		if( dormancyChk == true){ alert('휴면회원은 탈퇴 처리 할 수 없습니다.'); return false; } // 데이터 처리상 휴면회원 탈퇴처리 불가
		if( confirm('선택하신 회원을 탈퇴 처리 하시겠습니까?') == false){ return false; }
		var apply = $(this).attr('data-apply');
		if( apply != 'true'){ alert("잠시만 기다려주세요.\n현재 선택회원에 대한 탈퇴처리를 진행중입니다."); return false;  }

		var url = '_individual.ajax.php';
		  $.ajax({
		      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'getOut' , inID : inID }, success: function(data){
		      	$(this).attr('data-apply','true');
		      	if( data == undefined){ return false; }
		      	if( data.rst == 'success'){
		      		alert(data.msg);
		      		window.location.reload();
		      		return false;
		      	}else{ alert(data.msg); return false; }
		      },error:function(request,status,error){ console.log(request.responseText);}
		  });
	})

	function ctrlAuth(ctrlMode)
	{
		if( ctrlMode == undefined || ctrlMode == ''){return false;}
		if( ctrlMode == 'Y'){
			if( confirm('선택하신 회원을 승인 처리 하시겠습니까?') == false){ return false; }
		}else if(ctrlMode == 'N'){
			if( confirm('선택하신 회원을 미승인 처리 하시겠습니까?') == false){ return false; }
		}else{ return false; }

		$('form#frm [name="_mode"]').val('selectAuth');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual.pro.php');
		frm.submit();
	}

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
		$('form#frm').attr('target','common_frame');
		$('form#frm').attr('action','_individual.pro.php');

		frm.submit();
	}

	// -- 선택 문자발송
	function ctrlSmsSend(ctrlMode) {
		if( ctrlMode == 'select'){ // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
			$('form#frm [name="_mode"]').val('sms_chk_again_select');
		}else if( ctrlMode == 'search'){
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
			$('form#frm [name="_mode"]').val('sms_chk_again_search');
		}
		$('form#frm').removeAttr('target');
		$('form#frm').attr('method','get');
		$('form#frm').attr('action','_sms.form.php');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		// --- 수신거부 고객을 포함하여 발송시 재확인 ---
		$.ajax({
		    url: "/addons/080deny/_inc.reconfirm.pro.php",
		    type: "POST",
		    dataType:'json',
		    data: $("form[name=frm]").serialize()+"&_action=check",
		    success: function(data){
				if(data.rst == 'success'){
		          sms_chk_again_view(data.deny_cnt); // 레이어 팝업 띄움 :: 수신거부 사용자 개수 추가
				}
		    },error:function(request,status,error){ console.log(request.responseText);}
		});
	}

</script>

<?php
	include_once('wrap.footer.php');

	# 수신거부 고객을 포함하여 재발송 확인
	include_once OD_ADDONS_ROOT."/080deny/_inc.reconfirm.php";
?>
