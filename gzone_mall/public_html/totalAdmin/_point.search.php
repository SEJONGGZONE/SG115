<?php
	$app_mode = 'popup';
	include_once('inc.header.php');

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


?>

<div class="popup none_layer">

	<div class="pop_title">
		<strong>회원검색</strong><!-- 메뉴얼로 링크 --><?php echo openMenualLink('회원검색'); ?>
		<a href="#none" class="btn_close" title="닫기" onclick="window.close(); return false;" ></a>
	</div>

	<div class="form_wrap">
		<?php

			// 회원타입을 선택하지 않으면 전체선택
			if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

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

		<div class="data_list pop_conts">
			<form name="frm" id="frm" method="post">
				<input type="hidden" name="_mode" value="">
				<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
				<input type="hidden" name="searchQue" value="<?php echo enc('e',$s_query); ?>">
				<input type="hidden" name="searchCnt" value="<?php echo $TotalCount; ?>">
				<input type="hidden" name="ctrlMode" value="">
				<input type=hidden name="_PVSC" value="<?php echo $_PVSC; ?>">

				<div class="list_ctrl">
					<div class="left_box">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					</div>
					<div class="right_box">
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
					</div>
				</div>

				<table class="table_list">
					<colgroup>
						<col width="40"/><col width="80"/><col width="70"/><col width="*"/><col width="*"/><col width="*"/><col width="*"/><col width="100"/><col width="110"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
							<th scope="col">번호</th>
							<th scope="col">승인</th>
							<th scope="col">아이디</th>
							<th scope="col">성명</th>
							<th scope="col">이메일</th>
							<!-- <th scope="col">전화</th> -->
							<th scope="col">휴대폰</th>
							<th scope="col">적립금</th>
							<th scope="col">가입일</th>
							<!-- <th scope="col">최근접속일</th> -->
						</tr>
					</thead>
					<tbody>
					<?php
						foreach($res as $k=>$v) {
							$_num = $TotalCount - $count - $k ;
							$printEmail = $v['in_email'] != '' ? trim($v['in_email']):'';
							$printTel = rm_str($v['in_tel']) == '' ? '-' : tel_format($v['in_tel']);  // 전화
							$printTel2 = rm_str($v['in_tel2']) == '' ? '-' : tel_format($v['in_tel2']);  // 휴대폰
							$printRdate = rm_str($v['in_rdate']) > 0 ?  printDateInfo($v['in_rdate']) : '-'; // 가입일
							$printLdate = rm_str($v['in_ldate']) > 0 ?  printDateInfo($v['in_ldate']) : '-'; // 최근접속일

							// -- 승인여부
							if($v['in_auth']  != 'Y' ){
								$printAuth = '<span class="c_tag h18 light t3">미승인</span>';
							}else{
								$printAuth = '<span class="c_tag gray h18 blue line t3">승인</span>';
							}

							// -- 출력
							echo '<tr>';
							echo '	<td class="this_check"><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="'.$v['in_id'].'"></label></td>';
							echo '	<td class="this_num">'.number_format($_num).'</td>';
							echo '	<td class="this_state">'.$printAuth.'</td>';
							echo '	<td>'.$v['in_id'].'</td>';
							echo '	<td>'.$v['in_name'].'</td>';
							echo '	<td>'.$printEmail.'</td>';
							//echo '	<td>'.$printTel.'</td>';
							echo '	<td>'.$printTel2.'</td>';
							echo '	<td class="t_blue t_bold t_right"><span class="hidden_tx">적립금</span>'.number_format($v['in_point']).'</td>';
							echo '	<td class="this_ctrl">'.$printRdate.'</td>';
							//echo '	<td>'.$printLdate.'</td>';
							echo '</tr>';
						}
					?>
					</tbody>

				</table>

				<?php if(count($res) <  1) {  ?>
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
				<?php } ?>

			</form>

			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
			</div>
		</div>
	</div><!-- end form_wrap -->

	<div class="c_btnbox type_full">
		<ul>
			<li><a href="#none" class="c_btn h34 blue js_selecte_product">선택완료</a></li>
			<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
		</ul>
	</div>

	<div class="fixed_save js_fixed_save" style="display:none;">
		<div class="wrapping" style="margin:0;">
			<div class="c_btnbox type_full" style="margin:0 !important;">
				<ul>
					<li><a href="#none" class="c_btn h34 blue js_selecte_product">선택완료</a></li>
					<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).on('click', '.js_selecte_product', function(e) {
		e.preventDefault();
		var ele = $('.js_ck:checked');
		if(ele.length <= 0) return alert('회원을 선택해주세요.');
		var _str = "";
		ele.each(function(){
			_str += $(this).val() + ",";
		});
		opener.document.frm._inid.value += _str;
		window.close();
	});
</script>

<?php include_once('inc.footer.php'); ?>
