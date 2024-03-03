<?PHP
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');

//shop_pointlog_insert( 'papersj' , '포인트삭제' , '-5000' , 'N' , '0');
//exit;


# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);


// 검색 체크
$s_query = " and pl_delete = 'N' ";
if( $pass_inid !="" ) $s_query .= " and pl_inid like '%${pass_inid}%' ";
if( $pass_title !="" ) $s_query .= " and pl_title like '%${pass_title}%' ";
if( $pass_status !="" ) $s_query .= " and pl_status = '${pass_status}' ";

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'pl_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_point_log as pl where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
    <input type="hidden" name="_mode" value="search">
    <input type="hidden" name="st" value="<?php echo $st; ?>">
    <input type="hidden" name="so" value="<?php echo $so; ?>">
    <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php if($_mode == 'search') { ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
            <?php } ?>
			<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">참고사항</a>
            <a href="_point.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">적립금 등록</a>
        </div>
    </div>

	<div class="js_thisview" style="display:none">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>참고사항</th>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('적립이 완료된 내역은 수정할 수 없습니다.'); ?>
							<?php echo _DescStr('적립이 완료된 내역을 취소할 경우 해당 적립금 만큼을 차감 또는 회수하는 내역이 추가됩니다. (적립 → 차감, 차감 → 적립) '); ?>
							<?php echo _DescStr('적립예정인 내역을 취소할 경우 <em>적립취소</em>상태가 되며 적립예정일이 되어도 적립금이 지급되지 않습니다. '); ?>
							<?php echo _DescStr('내역 삭제 시 관리자와 회원 마이페이지 적립금 내역에서도 모두 삭제되며 복구가 불가합니다.'); ?>
							<?php echo _DescStr('적립 완료된 내역을 삭제할 경우 금액자체 변동없이 내역만 삭제되어 실제 적립금이 달라질 수 있으니 주의바랍니다.', 'red'); ?>
							<?php echo _DescStr('최종 적립금에 "보정"으로 표시된 부분은 적립금 차감 시 0원 미만이 될 경우 적립금이 마이너스가 되지 않도록 0원으로 적립금을 조정한것입니다.','blue'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

    <div class="search_form">
        <table class="table_form">
            <colgroup>
                <col width="130"><col width="*"><col width="130"><col width="*">
            </colgroup>
            <tbody>
                <tr>
					<th>아이디</th>
                    <td>
                        <input type="text" name="pass_inid" class="design" value="<?php echo $pass_inid; ?>" style="" placeholder="아이디">
                    </td>
                    <th>처리상태/내용</th>
                    <td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="" placeholder="적립내용">
                    </td>
                </tr>
				<tr>
					<th>적립금 상태</th>
					<td colspan="3">
						<?php echo _InputRadio('pass_status', array('','Y', 'N', 'C'), $pass_status, '', array('전체','적립완료', '적립예정', '적립취소'), ''); ?>
					</td>
				</tr>
            </tbody>
        </table>

        <div class="c_btnbox">
            <ul>
                <li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
                <?php if($_mode == 'search') { ?>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</form><!-- end data_search -->


<!-- 리스트 -->
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
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="downloadExcel('select'); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="downloadExcel('search'); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운(<?php echo number_format($TotalCount); ?>)</a>

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_uid', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pl_uid' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_uid', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pl_uid' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_adate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pl_adate' && $so == 'asc'?' selected':null); ?>>지급일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_adate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pl_adate' && $so == 'desc'?' selected':null); ?>>지급일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_point_after', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pl_point_after' && $so == 'asc'?' selected':null); ?>>최종적립금 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_point_after', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pl_point_after' && $so == 'desc'?' selected':null); ?>>최종적립금 ▼</option>
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
				<col width="40">
				<col width="70">
				<col width="90">
				<col width="100">
				<col width="*">
				<col width="270">
				<col width="100">
				<col width="110">
			</colgroup>
			<thead>
				<tr>
					<th><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th>No</th>
					<th>상태</th>
					<th>회원명(ID)</th>
					<th>지급내용/지급(예정)일</th>
					<th>적립금 내역</th>
					<th>등록일</th>
					<th>관리</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($r) > 0) { ?>
					<?php
					foreach($r as $k=>$v) {
						$_num = $TotalCount-$count-$k; // NO 표시
						$_title = strip_tags($v['pl_title']);

						// 적립상태
						$status_icon = $arr_adm_button['적립예정'];
						if($v['pl_status']=='Y') $status_icon = $arr_adm_button['적립완료'];
						else if($v['pl_status']=='C') $status_icon = $arr_adm_button['적립취소'];

					?>
						<tr>
							<td class="this_check"><label class="design"><input type="checkbox" name="chk_uids[]" class="js_ck" value="<?php echo $v['pl_uid']; ?>"></label></td>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_state"><?php echo $status_icon; ?></td>
							<td>
								<?php
									if($v['in_out']=='Y'){
										echo $v['in_name'].' <span class="t_light">('.$v['pl_inid'].')</span>';
									}else{
										echo showUserInfo($v['pl_inid'],$v['in_name']);
									}
								?>
							</td>
							<td class="t_left t_black">
								<?php echo $_title; ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<strong class="t_sky"><?php echo printDateInfo($v['pl_appdate']); ?> 지급(예정)</strong>
							</td>
							<td class="">
								<div class="in_price">
									<ul class="in_thead">
										<li class="th">이전 적립금</li>
										<li class="th">지급/차감</li>
										<li class="th">최종 적립금</li>
									</ul>
									<ul>
										<li class="t_light"><?php echo ($v['pl_status']=='Y'?number_format($v['pl_point_before']):'0'); ?></li>
										<li class="t_blue t_bold"><?php echo number_format($v['pl_point']); ?></li>
										<li class="t_black">
											<?php echo ($v['pl_status']=='Y'?number_format($v['pl_point_after']):'<span class="t_none">미지급</span>'); ?>
											<?php echo ($v['pl_status']=='Y' && $v['pl_point'] <> $v['pl_point_apply'] ? '<div class="t_red t_col_to_row">(보정 '.number_format($v['pl_point_apply']-$v['pl_point']).')</div>' : ''); ?>
										</li>
									</ul>
								</div>
							</td>
							<td class="this_date"><?php echo printDateInfo($v['pl_rdate']); ?></td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_point.form.php?_mode=modify&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 gray">수정</a>
									<a href="#none" onclick="cancel('_point.pro.php?_mode=cancel&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 dark line">취소</a>
									<!-- <a href="#none" onclick="delete_log('_point.pro.php?_mode=delete&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 gray">삭제</a> -->
								</div>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>

		<?php if(count($r) <= 0) { ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>

	</form>
</div>
<!-- // 리스트 -->


<script>
	// 포인트 로그 삭제  -- db상에서는 삭제하지 않고 노출되지 않도록만 처리
	function delete_log($href){
		if(confirm('적립금 내역을 삭제하여도 회원 적립금은 차감(가감)되지 않습니다.\n\n적립금 내역 삭제 시 적립금 내역의 합계와 회원의 보유 적립금이 \n\n다를 수 있습니다. 정말 삭제하시겠습니까?')){
			document.location.href = $href;
		}
	}
	 // 선택삭제
	 function selectDelete() {
		 if($('.js_ck').is(':checked')){
			 if(confirm('적립금 내역을 삭제하여도 회원 적립금은 차감(가감)되지 않습니다.\n\n적립금 내역 삭제 시 적립금 내역의 합계와 회원의 보유 적립금이 \n\n다를 수 있습니다. 정말 삭제하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
				$('form[name=frm]').attr('action' , '_point.pro.php');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

	// 2020-01-14 SSJ :: 엑셀다운로드 추가
	function downloadExcel(_mode){
		if(_mode == 'select' && $('.js_ck').is(":checked") === false){
			alert('1개 이상 선택해 주시기 바랍니다.');
			return false;
		}

		$("form[name=frm]").children("input[name=_mode]").val(_mode);
		$("form[name=frm]").attr("action" , "_point.download.php");
		$("form[name=frm]").attr("target" , "_self");
		document.frm.submit();
		return true;
	}
	// 검색엑셀 다운로드
</script>

<?php include_once('wrap.footer.php'); ?>