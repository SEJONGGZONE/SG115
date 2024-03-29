<?php
	// -- LCY -- 회원리스트
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

	// 저장한 정보 불러오기 --> $app_profile 로 저장됨
	include_once("..".IMG_DIR_NORMAL."/mailing.profile.php");
	$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));

?>

<div class="popup none_layer">

	<div class="pop_title">
		<strong>메일링 회원추가</strong>
		<a href="#none" class="btn_close" title="닫기" onclick="window.close(); return false;"></a>
	</div>


	<?php
		// 회원타입을 선택하지 않으면 전체선택
		if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

		if($searchMode <> 'true') $pass_emailsend = $pass_emailsend ? $pass_emailsend : 'Y';
		// 회원 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_individual as indr where 1 and in_sleep_type = 'N' AND in_out = 'N' ";

		// 추가된 회원은 검색 제외
		if( sizeof($ex_app_profile) > 0 ) {
			$s_query .= " and in_email not in ('".implode("','" , $ex_app_profile)."') ";
		}

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
	<form name="frm2" action="_mailing_profile.popup_pro.php"  method=post class="form_wrap">

		<div class="pop_conts">
			<div class="data_list">
				<div class="list_ctrl">
					<div class="left_box">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
						<span class="c_btn h27 blue"><input type="submit" class="" value="선택추가"></span>
					</div>
				</div>

				<table class="table_list">
					<colgroup>
						<col width="40"/><col width="80"/><col width="*"/><col width="*"/><col width="*"/><col width="110"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
							<th scope="col">No</th>
							<th scope="col">성명(아이디)</th>
							<th scope="col">이메일</th>
							<!-- <th scope="col">전화</th> -->
							<th scope="col">휴대폰</th>
							<th scope="col">가입일</th>
							<!-- <th scope="col">최근접속일</th> -->
						</tr>
					</thead>
					<tbody>
					<?php
						foreach($res as $k=>$v) {
							$_num = $TotalCount - $count - $k ;
							$_num = number_format($_num);
							$printEmail = $v['in_email'] != '' ? trim($v['in_email']):'';
							$printTel = rm_str($v['in_tel']) == '' ? '' : tel_format($v['in_tel']);  // 전화
							$printTel2 = rm_str($v['in_tel2']) == '' ? '<span class="t_none">휴대폰 미등록</span>' : tel_format($v['in_tel2']);  // 핸드폰
							$printRdate = rm_str($v['in_rdate']) > 0 ?  printDateInfo($v['in_rdate']) : '-'; // 가입일
							$printLdate = rm_str($v['in_ldate']) > 0 ?  printDateInfo($v['in_ldate']) : '-'; // 최근접속일

							// -- 출력
							echo '<tr>';
							echo '	<td class="this_check"><label class="design"><input type="checkbox" class="js_ck in-id" name="_chk[]" value="'.$printEmail.'"></label></td>';
							echo '	<td class="this_num">'.$_num.'</td>';
							echo '	<td><div class="lineup-row type_side">'.$v['in_name'].'<strong class="t_sky">'.$v['in_id'].'</strong></div></td>';
							echo '	<td>'.$printEmail.'</td>';
							//echo '	<td>'.$printTel.'</td>';
							echo '	<td>'.$printTel2.'</td>';
							echo '	<td class="this_date">'.$printRdate.'</td>';
							//echo '	<td>'.$printLdate.'</td>';
							echo '</tr>';
						}
					?>
					</tbody>

				</table>

				<?php if(count($res) <  1) {  ?>
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
				<?php } ?>

				<div class="paginate">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
				</div>
			</div>
		</div><!-- end pop_conts -->

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox type_full">
			<ul>
				<li><span class="c_btn h34 blue"><input type="submit" class="" value="선택추가"></span></li>
				<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
			</ul>
		</div>
		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping">
				<div class="c_btnbox type_full" style="margin:0 !important;">
					<ul>
						<li><span class="c_btn h34 blue"><input type="submit" class="" value="선택추가"></span></li>
						<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
					</ul>
				</div>
			</div>
		</div>

	</form>
</div>


<?php
	include_once('inc.footer.php');
?>
