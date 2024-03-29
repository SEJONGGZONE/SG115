<?php
// -- 쿠폰발급
$app_current_link = '_coupon_set.list.php';
include_once('wrap.header.php');
$row = _MQ("select *, (select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = ocs_uid ) as issued_cnt from  smart_individual_coupon_set where ocs_uid = '".$_uid."' ");
if( count($row) < 1){ error_loc_msg("_coupon_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");  }
$rowChk = _MQ("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$_uid."'  ");
?>


<div class="group_title type_first">
	<strong class="">쿠폰 발급내역</strong>
	<div class="btn_box">
		<a href="_coupon_set.list.php<?php echo ($_PVSC ? '?' . enc('d' , $_PVSC) : null); ?>" class="c_btn h46 black line">목록으로</a>
		<?php if( $row['ocs_issued_type'] == 'manual') {  ?>
			<?php
			if($row['ocs_view'] == 'N' || $row['ocs_edate'] < date('Y-m-d')){ // 발급중지 쿠폰은 발급안되도록 처리
				echo '
					<a href="#none" onclick="alert(\'발급중지된 쿠폰 및 사용기간이 지난 쿠폰은 발급이 불가능합니다.\'); return false;"  class="c_btn gray line h22">발급불가</a>
						';
			}else{
				echo '
					<a href="_coupon.form.php?_uid='.$row['ocs_uid'].'"  class="c_btn blue h22">'.$arrCouponSet['ocs_issued_type'][$row['ocs_issued_type']].' 하러가기</a>
						';
			}
			?>
		<?php } ?>
	</div>
</div>


<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
<div class="data_search type_info">
	<table class="table_form">
		<colgroup>
			<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th>쿠폰명</th>
				<td colspan="3" class="t_blue t_bold">
					<?php echo stripslashes($row['ocs_name']); ?>
				</td>
			</tr>
			<tr>
				<th>상태/유형</th>
				<td>
					<div class="lineup-row">
						<?php

							// 발급여부
							if( $row['ocs_view'] == 'Y'){
								echo '<div class="lineup-center"><span class="c_tag h18 blue line">발급중</span></div>';
							}else{
								echo '<div class="lineup-center"><span class="c_tag h18 light">발급중지</span></div>';
							}

						?>
						<?php echo $arrCouponSet['ocs_type_view'][$row['ocs_type']]; ?>
					</div>
				</td>
                <th>발급방법</th>
				<td>
					<?php echo $arrCouponSet['ocs_issued_type'][$row['ocs_issued_type']].($row['ocs_issued_type'] == 'auto' ? ' ('.$arrCouponSet['ocs_issued_type_auto'][$row['ocs_issued_type_auto']].')':null); ?>
				</td>
			</tr>
			<tr>
				<th>사용기간</th>
                <td>
                    <?php
                    // 사용기간
                    if($row['ocs_use_date_type'] == 'date'){ // 사용기간
                        echo $row['ocs_sdate'].' ~ '.$row['ocs_edate'];
                    }else{
                        echo  '발급일로부터 '.$row['ocs_expire'].'일까지 사용가능';
                    }
                    ?>
                </td>
				<th>쿠폰혜택</th>
				<td>
					<?php
						// 할인혜택
						echo printCouponSetBoon($row);
					?>
				</td>
			</tr>
			<?php if( trim($row['ocs_desc']) != ''){ ?>
			<tr>
				<th>쿠폰설명</th>
                <td colspan="3">
                    <?php echo stripslashes($row['ocs_desc']); ?>
                </td>
			</tr>
            <?php } ?>
		</tbody>
	</table>

</div>



<?php

	// 회원그룹 정보를 가져온다. (전체)
	$getGroupInfo = getGroupInfo();


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

	// 추가파라메터
	if(!$arr_param) $arr_param = array();
	$arr_param = array('_uid'=>$_uid);

	// 회원타입을 선택하지 않으면 전체선택
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_individual_coupon as coup left join smart_individual as indr on(indr.in_id = coup.coup_inid) where 1 and coup_ocs_uid = '".$_uid."'  ";

	//	==> s_query 리턴됨.
	include_once("_individual.inc_search.php");

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

<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->



<!-- ● 데이터 리스트 -->
<div class="data_list">
<form name="frm" id="frm" method="post" action="_coupon.pro.php" target="common_frame">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="issuedCnt" value="<?php echo $rowChk['cnt'] < 1 ? 0 : $rowChk['cnt']; ?>">
	<input type="hidden" name="_mode" value="delete">
	<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
	<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
	<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
	<input type="hidden" name="ctrlMode" value="">
	<input type=hidden name="_PVSC" value="<?=$_PVSC?>">


	<div class="list_ctrl">
		<div class="left_box">
            <a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
            <a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
			<a href="#none" onclick="ctrlIssued('select'); return false;" class="c_btn h27 black line">선택회원 쿠폰삭제</a>
			<a href="#none" onclick="ctrlIssued('search'); return false;" class="c_btn h27 black line">검색회원 쿠폰삭제(<?=number_format($TotalCount)?>)</a>
		</div>
		<div class="right_box">
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'coup_rdate' && $so == 'asc'?' selected':null); ?>>발급일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'coup_rdate' && $so == 'desc'?' selected':null); ?>>발급일 ▼</option>

				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_expdate ', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'coup_expdate ' && $so == 'asc'?' selected':null); ?>>만료일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_expdate ', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'coup_expdate ' && $so == 'desc'?' selected':null); ?>>만료일 ▼</option>

				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_usedate ', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'coup_usedate ' && $so == 'asc'?' selected':null); ?>>사용일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'coup_usedate ', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'coup_usedate ' && $so == 'desc'?' selected':null); ?>>사용일 ▼</option>
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
			<col width="40"/><col width="80"/><col width="100"/>
			<col width="*"/>
			<col width="*"/>
			<col width="110"/>
			<col width="110"/>
			<col width="110"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
				<th scope="col">No</th>
				<th scope="col">쿠폰상태</th>
				<th scope="col">회원등급</th>
				<th scope="col">회원정보</th>
				<th scope="col">발급일</th>
				<th scope="col">만료일</th>
				<th scope="col">사용일</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($res as $k=>$v) {
				$_num = $TotalCount - $count - $k ;
				$_num = number_format($_num);

				// -- 승인여부
				if($v['coup_use']  == 'W' ){
					$printUse = '<span class="c_tag gray line h18 t4">사용대기</span>';
				}else if($v['coup_use']  == 'Y' ){
					$printUse = '<span class="c_tag blue line h18 t4">사용완료</span>';
				}else if($v['coup_use']  == 'N' ){
					$printUse = '<span class="c_tag gray line h18 t4">미사용</span>';
				}else{
					$printUse = '<span class="c_tag light h18 t4">만료</span>';
				}


				$printRdate = rm_str($v['coup_rdate']) > 0 ?  printDateInfo($v['coup_rdate']) : '<span class="t_none">발급전</span>'; // 발급일
				$printExpdate = rm_str($v['coup_expdate']) > 0 ?  printDateInfo($v['coup_expdate']) : '<span class="t_none">만료</span>'; // 만료일
				$printUsedate = rm_str($v['coup_usedate']) > 0 ?  printDateInfo($v['coup_usedate']) : '<span class="t_none">사용전</span>'; // 사용일

				// -- 출력
				echo '<tr>';
				echo '	<td class="this_check"><label class="design"><input type="checkbox" class="js_ck coup-uid'.($v['coup_use'] == 'Y' ? ' disabled':'').'" name="arr_coup_uid[]" value="'.$v['coup_uid'].'" '.($v['coup_use'] == 'Y' ? 'disabled':'').'></label></td>';
				echo '	<td class="this_num">'.$_num.'</td>';
				echo '	<td class="this_ctrl">'.$printUse.'</td>';
				echo '	<td class="t_blue">'.$getGroupInfo[$v['in_mgsuid']]['name'].'</td>';
				echo '	<td class="t_left">'.$v['in_name'].' ('.$v['in_id'].')</td>';
				echo '	<td class="this_date">'.$printRdate.'</td>';
				echo '	<td><span class="hidden_tx">만료일</span>'.$printExpdate.'</td>';
				echo '	<td class="t_red"><span class="hidden_tx">사용일</span>'.$printUsedate.'</td>';
				echo '</tr>';
			}
		?>
		</tbody>

	</table>

	<?php if(count($res) <  1) {  ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>

</div>

</form>

<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
</div>





<script>
 function ctrlIssued(ctrlMode)
 {
 	if( ctrlMode == 'select'){
 		if( confirm("선택하신 회원의 쿠폰을 삭제 하시겠습니까?\n사용된 쿠폰은 삭제되지 않습니다.") == false){ return false; }
 		var chkLen = $('.coup-uid:checked').length *1;
 		if( chkLen < 1){ alert("한개이상 선택해 주세요."); return false; }
 	}else if( ctrlMode == 'search'){
 		if( confirm("검색하신 회원의 쿠폰을 삭제 하시겠습니까?\n사용된 쿠폰은 삭제되지 않습니다.") == false){ return false; }
 		var chkCnt = $('form#frm input[name="searchCnt"]').val()*1;
 		if( chkCnt < 1){ alert("삭제할 회원의 쿠폰이 없습니다.");  }
 	}else{ alert("잘못된 접근입니다."); return false; }

	$('form#frm input[name="ctrlMode"]').val(ctrlMode);
	$('form#frm').submit();
	$('form#frm input[name="ctrlMode"]').val('');

 }
</script>


<?php
	include_once('wrap.footer.php');
?>