<?php

	include_once('wrap.header.php');


	// - 넘길 변수 설정하기 ---
	$_PVS = ""; $ARR_PVS = array(); // 링크 넘김 변수
	foreach(array_filter($_GET) as $key => $val) { $ARR_PVS[$key] = $val; } // GET먼저 중복걸러내기
	foreach(array_filter($_POST) as $key => $val) { $ARR_PVS[$key] = $val; } // POST나중 중복걸러내기
	foreach( $ARR_PVS as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---



	$listmaxcount = 50 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	// 일자계산
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

	$s_query = "
		FROM smart_cntlog_list as sc
		INNER JOIN smart_cntlog_detail as scd ON (sc.sc_uid = scd.sc_uid)
		where 1
	";
	$s_query .= " and DATE(sc.sc_date) between '". $pass_sdate ."' and '". $pass_edate ."' ";

	if($pass_mobile)	$s_query .= " and sc.sc_mobile = '". $pass_mobile ."' ";
	if($pass_keyword)	$s_query .= " and scd.sc_keyword like '%". $pass_keyword ."%' ";
	if($pass_browser)	$s_query .= " and scd.sc_browser like '%". $pass_browser ."%' ";
	if($pass_ip)	$s_query .= " and scd.sc_ip like '%". $pass_ip ."%' ";
	if($pass_referer)	$s_query .= " and scd.sc_referer like '%". $pass_referer ."%' ";


	// ------- 순위별 목록 -------
	$que = "
		SELECT
			count(*) as cnt
			" . $s_query . "
	";
	$res = _MQ($que);

	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);


	//------------------ sort 기능 ------------------
	IF($order_field) {
		$order_sort = $order_sort ? $order_sort : '1';
		$que_order = $order_field .' '. ($order_sort == '2' ? ' DESC' : ' ASC') . ($order_field <> 'sc.sc_uid' ? ' , sc.sc_uid DESC ' : '');
	}
	else {
		$que_order = ' sc.sc_uid DESC ';
	}
	//------------------ sort 기능 ------------------


	// ------- 순위별 목록 -------
	$que = "
		SELECT
			sc.*, scd.*
			" . $s_query . "
		ORDER BY ". $que_order ."
		LIMIT " . $count . " , " . $listmaxcount . "
	";
	$res = _MQ_assoc($que);

?>


	<!-- 기간검색 -->
	<form name='searchfrm' method='post' action='<?=$PHP_SELF?>' class="data_search">
	<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
	<input type=hidden name='mode' value='search'>
	<input type=hidden name='_mode' value=''><!-- 엑셀다운로드용 -->

		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>Search</strong>
			<div class="btn_box">
				<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색열기</i><em>검색닫기</em></a>
				<?php if($mode == 'search'){ ?>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
				<?php } ?>
			</div>
		</div>

		<div class="search_form">
			<table class="table_form">
				<colgroup>
					<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>기간선택</th>
						<td>
							<div class="lineup-row type_date">
								<input type="text" name="pass_sdate" class='design js_pic_day_max_today' value="<?=$pass_sdate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
								<span class="fr_tx">~</span>
								<input type="text" name="pass_edate" class='design js_pic_day_max_today' value="<?=$pass_edate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
							</div>
						</td>
						<th>기기</th>
						<td class="conts">
							<?=_InputRadio( "pass_mobile" , array('','N','Y') , $pass_mobile , "" , array('전체','PC','모바일') , "")?>
						</td>
						<th>아이피</th>
						<td class="conts"><input type=text name='pass_ip' class='design' value="<?=$pass_ip?>" placeholder="아이피"></td>
					</tr>
					<tr>
						<th>키워드</th>
						<td class="conts"><input type=text name='pass_keyword' class='design' value="<?=$pass_keyword?>" placeholder="키워드"></td>
						<th>유입경로</th>
						<td class="conts"><input type=text name='pass_referer' class='design' value="<?=$pass_referer?>" style="width:300px;" placeholder="유입경로"></td>
						<th>브라우저</th>
						<td class="conts"><input type=text name='pass_browser' class='design' value="<?=$pass_browser?>" placeholder="브라우저"></td>
					</tr>
				</tbody>
			</table>

			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>

	</form><!-- end data_search -->



<div class="data_list">
	<div class="list_ctrl">
		<div class="left_box"></div>
		<div class="right_box">
			<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel only_pc_view">엑셀다운</a>
		</div>
	</div>
	<div class="mobile_tip">엑셀다운로드는 PC에서 가능합니다.</div>

	
	<table class="table_list if_counter_table type_nocheck">
		<colgroup>
			<col width="80"/><col width="130"/><col width="120"/><col width="120"/><col width="*"/>
			<col width="170"/><col width="200"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col" class="colorset" >No</th>
				<th scope="col" class="colorset" >기기</th>
				<th scope="col" class="colorset" >아이피</th>
				<th scope="col" class="colorset" >키워드</th>
				<th scope="col" class="colorset" >유입경로</th>
				<th scope="col" class="colorset" >유입일</th>
				<th scope="col" class="colorset" >브라우저</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(count($res)>0){
					foreach( $res as $datek => $datev ){
						$_num = $TotalCount - $count - $datek ;
						// 접속기기
						$_device = ($datev['sc_mobile'] == 'Y' ? '<span class="c_tag h18 mo">MO</span>' : '<span class="c_tag h18 t3 pc">PC</span>');
			?>
				<tr>
					<td class="this_num"><?php echo $_num;?></td>
					<td class="this_state"><?php echo $_device ;?></td>
					<td><?php echo $datev['sc_ip'];?></td>
					<td><?php echo $datev['sc_keyword'];?></td>
					<td class="t_left"><?php echo $datev['sc_referer'] ? '<a href="'.$datev['sc_referer'].'" target="_blank">'.$datev['sc_referer'].'</a>' : '<spanc class="t_none">직접입력</span>' ;?> </td>
					<td class="this_date"><?php echo printDateInfo($datev['sc_date']) ?></td>
					<td class="t_sky this_ctrl"><?php echo $datev['sc_browser'];?></td>
				</tr>
			<?php
					}// ------- 순위별 목록 -------
				}
			?>

		</tbody>
	</table>

	<?php if(count($res)<1){?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
	<?php }?>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

</div>



<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="searchfrm"]').children('input[name="_mode"]').val('cntlog_detail_search');
		$('form[name="searchfrm"]').attr('action', '_cntlog.pro.php');
		$('form[name="searchfrm"]')[0].submit();
		$('form[name="searchfrm"]').attr('action', '<?=$PHP_SELF?>');
	}
	// --- 검색 엑셀 ---
</script>



<?php

	include_once('wrap.footer.php');

?>