<?PHP
	// LMH005
	include_once("wrap.header.php");


	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_name !="" ) { $s_query .= " and pr_name like '%${pass_name}%' "; }
	if( $pass_code !="" ) { $s_query .= " and pr_code like '%${pass_code}%' "; }
	if( $pass_expire !="" ) { $s_query .= " and pr_expire_date='${pass_expire}' "; }
	if( $pass_use !="" ) { $s_query .= " and pr_use='${pass_use}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_promotion_code $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);
	$res = _MQ_assoc(" select * from smart_promotion_code {$s_query} ORDER BY pr_uid desc limit $count , $listmaxcount ");
?>


	<form name="searchfrm" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="data_search">
	<input type="hidden" name="_mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

        <!-- ● 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($_mode == 'search') { ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>
                <a href="_promotion.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">코드등록</a>
            </div>
        </div>

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
        <div class="search_form">
			<table class="table_form" summary="검색항목">
				<colgroup>
					<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>코드명</td>
						<td><input type="text" name="pass_name" class="design" value="<?php echo $pass_name; ?>" placeholder="프로모션코드명"></td>
						<th>코드</td>
						<td><input type="text" name="pass_code" class="design" value="<?php echo $pass_code; ?>" placeholder="프로모션코드"></td>
					</tr>
					<tr>
						<th>사용여부</td>
						<td><?php echo _InputRadio('pass_use', array('','Y','N'), $pass_use, '', array('전체','사용','미사용')); ?></td>
						<th>만료일</td>
						<td>
							<div class="lineup-row type_date">
							<input type="text" name="pass_expire" class="design js_pic_day" value="<?php echo $pass_expire; ?>" style="width:85px;" autocomplete="off" placeholder="날짜 선택" readonly>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="c_btnbox">
				<ul>
					<li>
						<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					</li>
					<?php if($_mode == 'search') { ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form><!-- end data_search -->



	<form name="frm" method="post" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e',$s_query); ?>">

		<!-- 리스트영역 -->
		<div class="data_list">

			<!-- 리스트 제어버튼영역 //-->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="select_send('delete'); return false;" class="c_btn h27 black line">선택삭제</a>
				</div>
			</div>
			<!-- // 리스트 제어버튼영역 -->

			<table class="table_list" summary="리스트기본">
				<colgroup>
					<col width="40"><col width="80"><col width="100"><col width="*"><col width="*"><col width="130">
					<col width="110"><col width="110"><col width="110">
				</colgroup>
				<thead>
					<tr>
						<th><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th>No</th>
						<th>사용여부</th>
						<th>코드명</th>
						<th>코드</th>
						<th>할인혜택</th>
						<th>만료일</th>
						<th>등록일</th>
						<th>관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if(sizeof($res)>0){
						foreach($res as $k=>$v) {

							$_num = $TotalCount - $count - $k ;
				?>
					<tr>
						<td class="this_check"><label class="design"><input type="checkbox" name="chk_id[<?php echo $v['pr_uid']; ?>]" class="js_ck" value="Y"></label></td>
						<td class="this_num"><?php echo $_num; ?></td>
						<td class="this_state"><?php echo $arr_adm_button[($v['pr_use'] == 'Y' ? '사용' : '미사용')]; ?></td>
						<td class="t_left"><?php echo ($v['pr_name'] ? $v['pr_name'] : "<span class='t_none'>미입력</span>"); ?></td>
						<td class="t_blue t_bold"><?php echo $v['pr_code']; ?></td>
						<td class="t_orange t_right"><?php echo ($v['pr_type']=='P'?$v['pr_amount']."%":number_format($v['pr_amount'])."원"); ?></td>
						<td><span class="hidden_tx">만료일</span><?php echo printDateInfo($v['pr_expire_date']); ?></td>
						<td class="this_date"><?php echo printDateInfo($v['pr_rdate']); ?></td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_promotion.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pr_uid'=>$v['pr_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 gray">수정</a>
								<a href="#none" onclick="del('_promotion.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pr_uid'=>$v['pr_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 dark line">삭제</a>
							</div>
						</td>
					</tr>
				<?
						}
					}
				?>
				</tbody>
			</table>

			<?php if(count($res) <= 0) { ?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

			<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
			</div>

		</div>
	</form>


<script>

	// - 타입별 액션 적용 ---
	function type_action(_type , _mode){
		switch(_type){
			// 삭제
			case "delete":
				$("input[name=_mode]").val(_mode + "_delete");
				$("form[name=frm]").attr("action" , "_promotion.pro.php");
				break;
		}
	}
	// - 타입별 액션 적용 ---

	// - 선택적용 ---
	 function select_send(_type) {
		 if($('.js_ck').is(":checked")){
			type_action(_type , "select");
			 document.frm.submit();
		 }
		 else {
			 alert('1명 이상 선택하시기 바랍니다.');
		 }
	 }
	// - 선택적용 ---

</script>


<?PHP
	include_once("wrap.footer.php");
?>


