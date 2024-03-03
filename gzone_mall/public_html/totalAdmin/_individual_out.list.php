<?php
	// -- LCY -- 회원리스트
	include_once('wrap.header.php');

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

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



	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_individual as indr where 1 and in_out = 'Y' and in_sleep_type = 'N' ";


	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {
		if($pass_id != ''){
			 $s_query .= " and in_id like '%".$pass_id."%' ";  // 아이디
		}

		// -- 탈퇴처리 유형
		if($pass_out_type != ''){
			 $s_query .= " and in_out_type = '".$pass_out_type."'  ";  // 아이디
		}

		// -- 회원 탈퇴 기간검색
		if(rm_str($pass_odays) != ''){
			$s_query .= " and (TO_DAYS(now()) - TO_DAYS(in_odate)) >= '".$pass_odays."' ";
		}

		// -- 회원 탈퇴일 검색
		if( $pass_sdate != '' && $pass_edate != ''){
			$s_query .= " and  ( left(in_odate,10) BETWEEN '".$pass_sdate."' and '".$pass_edate."' ) ";
		}else{
			if($pass_sdate != ''){
				$s_query .= " and left(in_odate,10) >= '".$pass_sdate."' ";
			}

			if($pass_edate != ''){
				$s_query .= " and left(in_odate,10) <= '".$pass_edate."' ";
			}
		}

	}
	// -- 검색종료 -- }}}


	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'in_odate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select *, (TO_DAYS(now()) - TO_DAYS(in_odate)) as odays $s_query order by {$st} {$so} limit $count , $listmaxcount ");

?>


<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>' class="data_search">
	<input type=hidden name="searchMode" value="true">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>Search</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('탈퇴회원검색')?>
		<div class="btn_box">
			<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
			<?php
			if($searchMode == 'true'){
				$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
				?>
				<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
			<?php } ?>
		</div>
	</div>

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>아이디</th>
					<td>
						<input type="text" name="pass_id" class="design" style="width:150px" value="<?=$pass_id?>" placeholder="아이디" />
					</td>
					<th>탈퇴처리 유형</th>
					<td>
						<?php echo _InputRadio( 'pass_out_type' , array('', 'member', 'admin'), ($pass_out_type) , '' , array('전체', '회원', '관리자') , ''); ?>
					</td>
				</tr>
				<tr>
					<th>탈퇴일</th>
					<td>
						<div class="lineup-row type_date">
							<input type="text" name="pass_sdate" class="design js_pic_day" style="width:90px;" value="<?=rm_str($pass_sdate) < 1 ? '': $pass_sdate ?>" readonly autocomplete="off" placeholder="날짜 선택">
							<span class="fr_tx">~</span>
							<input type="text" name="pass_edate" class="design js_pic_day" style="width:90px;" value="<?=rm_str($pass_edate) < 1 ? '': $pass_edate ?>" readonly autocomplete="off" placeholder="날짜 선택">
						</div>
					</td>
					<th>탈퇴 경과일</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="pass_odays" class="design t_right" style="width:70px;" value="<?=$pass_odays?>" placeholder="0" />
							<span class="fr_tx">일 이상</span>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
				<?php
					if($searchMode == 'true'){
						$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
				?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form><!-- end data_search -->


<div class="data_list">
	<form name="frm" id="frm" method="post">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="chkVar" value=""> <?php // 회원관리에서 개별 삭제일 시 저장될 아이디 ?>


		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick="ctrlDeleteMember('select'); return false;" class="c_btn h27 black line">선택삭제</a>
				<a href="#none" onclick="ctrlDeleteMember('search'); return false;" class="c_btn h27 black line">검색삭제(<?=$TotalCount?>)</a>
			</div>

			<div class="right_box">
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_odate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_odate' && $so == 'asc'?' selected':null); ?>>탈퇴일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_odate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_odate' && $so == 'desc'?' selected':null); ?>>탈퇴일 ▼</option>
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

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="80"/><col width="150"/>
				<col width="*"/><col width="100"/><col width="120"/><col width="70"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">No</th>
					<th scope="col">탈퇴처리 유형</th>
					<th scope="col">아이디</th>
					<th scope="col">탈퇴일</th>
					<th scope="col">탈퇴 경과일</th>
					<!-- <th scope="col">가입일</th> -->
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);
					$printOutType = $v['in_out_type'] == 'member' ? '<span class="c_tag purple t5">by 회원</span>':'<span class="c_tag purple line t5">by 관리자</span>';
					$printOutDate = rm_str($v['in_odate']) > 0 ?  printDateInfo($v['in_odate']) : '-'; // 탈퇴일
					$printOutDays = rm_str($v['odays']) > 0 ?  $v['odays'].'일째' : '오늘'; // 탈퇴한 날로부터 얼마나 됬는지 출력
					$printRdate = rm_str($v['in_rdate']) > 0 ?  printDateInfo($v['in_rdate']) : '-'; // 가입일

					$printBtn = '
						<div class="lineup-row type_center">
							<a href="#none" onclick="return false;" class="c_btn h22 dark line get-delete"  data-id="'.$v['in_id'].'" data-apply = "true">삭제</a>
						</div>
					'; // 관리버튼

					// ctrlDeleteMember

					// -- 출력
					echo '<tr>';
					echo '	<td class="this_check"><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="'.$v['in_id'].'"></label></td>';
					echo '	<td class="this_num">'.$_num.'</td>';
					echo '	<td class="this_state">'.$printOutType.'</td>';
					echo '	<td>'.$v['in_id'].'</td>';
					echo '	<td class="this_date">'.$printOutDate.'</td>';
					echo '	<td class="t_sky">'.$printOutDays.'</td>';
					//echo '	<td class="this_date">'.$printRdate.'</td>';
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


<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
</div>



<script>

	$(document).on('click','.get-delete',function(){
		var chkVar  = $(this).attr('data-id');
		ctrlDeleteMember('single', chkVar);
	});


	// -- 선택회원 완전삭제 chkVar == 회원아이디
	function ctrlDeleteMember(ctrlMode, chkVar) {

		// -- 선택/검색 처리 시에는 chkVar 가 없기때문에 초기화 처리
		if(chkVar == undefined) { chkVar = ''; }

		if(ctrlMode == 'select') { // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}
		else if( ctrlMode == 'search'){
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1) { alert("검색된 회원이 없습니다."); return false; }
		}
		else if( ctrlMode == 'single') { // 관리에서 개별삭제 처리 시
			if(chkVar == '') { alert("삭제 처리가 불가능합니다."); return false; }
			$('form#frm input[name="chkVar"]').val(chkVar);
		}

		if(confirm("탈퇴회원을 삭제할 경우 해당 아이디로 재가입이 가능하여\n기존 아이디의 정보가 승계될 수 있으니 해당 정보를 \n꼭 먼저 확인해주시고, 삭제 후에는 복원이 불가합니다. \n그래도 삭제하시겠습니까?") == false){ return false; }

		$('form#frm [name="_mode"]').val('delete');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_out.pro.php');

		frm.submit();
	}

</script>
<?php
	include_once('wrap.footer.php');
?>
