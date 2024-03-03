<?php

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; $ARR_PVS = array(); // 링크 넘김 변수
	foreach(array_filter($_GET) as $key => $val) { $ARR_PVS[$key] = $val; } // GET먼저 중복걸러내기
	foreach(array_filter($_POST) as $key => $val) { $ARR_PVS[$key] = $val; } // POST나중 중복걸러내기
	foreach( $ARR_PVS as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---



	// 일자계산
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');


	$arr_sum = array();

	$s_query = " where 1 ";
	$s_query .= " and sck_date between '". $pass_sdate ."' and '". $pass_edate ."' ";


	// ---- 총방문수, 접속기기 요약 ----
	$que = "
		SELECT
			'----------- 접속기기 -----------',
			SUM( sck_cnt_mo ) as sum_mobileY_cnt,
			SUM( sck_cnt_pc ) as sum_mobileN_cnt,
			'----------- 총방문수 -----------',
			SUM( sck_cnt_pc + sck_cnt_mo ) as sum_cnt
		FROM smart_cntlog_keyword
			" . $s_query . "
	";
	$res = _MQ($que);
	foreach( $res as $k=>$v ){
		$arr_sum[$k] = $v;
	}
	// ---- 총방문수, 접속기기 요약 ----


?>




<!-- 리스트영역 -->
<div class="group_title type_search">
	<strong><?=date("Y년 m월 d일" , strtotime($pass_sdate))?> ~ <?=date("Y년 m월 d일" , strtotime($pass_edate))?></strong>
	<!-- 기간검색 -->
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
		<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
		<input type="hidden" name="_mode" value="search">

		<div class="lineup-row type_date">
			<input type="text" name="pass_sdate" class='design js_pic_day_max_today' value="<?=$pass_sdate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
			<span class="fr_tx">~</span>
			<input type="text" name="pass_edate" class='design js_pic_day_max_today' value="<?=$pass_edate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
			<span class="c_btn h34 blue"><input type="submit" value="검색" accesskey="s"></span>
			<?php if($_mode == 'search'){ ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal" accesskey="l">초기화</a>
			<?php } ?>
		</div>
	</form><!-- end simple_search -->

	<?php
	/*


	<div class="data_list">
		<table class="table_must">
			<colgroup>
				<col width="*"/><col width="*"/><col width="*"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">PC <span class="t_orange">(<?=number_format($arr_sum['sum_mobileN_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span></th>
					<th scope="col">모바일 <span class="t_orange">(<?=number_format($arr_sum['sum_mobileY_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span></th>
					<th scope="col" >전체</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="t_blue t_right"><?=number_format($arr_sum['sum_mobileN_cnt'])?>건</td>
					<td class="t_blue t_right"><?=number_format($arr_sum['sum_mobileY_cnt'])?>건</td>
					<td class="t_black t_bold t_right"><?=number_format($arr_sum['sum_cnt'])?>건</td>
				</tr>
			</tbody>
		</table>
	</div>
	*/
	?>
</div>





<div class="data_list">
	<div class="list_ctrl">
		<div class="left_box"></div>
		<div class="right_box">
			<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel only_pc_view">엑셀다운</a>
		</div>
	</div>
	<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

	<table class="table_list type_nocheck">
		<colgroup>
			<col width="80"/><col width="*"/>
			<col width="350"/><col width="120"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col" class="colorset" >No</th>
				<th scope="col" class="colorset" >접속경로</th>
				<th scope="col" class="colorset" >접속기기</th>
				<th scope="col" class="colorset" >비율</th>
			</tr>
		</thead>
		<tbody>

			<?php

				$listmaxcount = 50 ;
				if( !$listpg ) {$listpg = 1 ;}
				$count = $listpg * $listmaxcount - $listmaxcount;

				// ------- 순위별 목록 -------
				$que = "
					SELECT
						COUNT(DISTINCT(sck_keyword)) as cnt
					FROM smart_cntlog_keyword
						" . $s_query . "
				";
				$res = _MQ($que);

				$TotalCount = $res['cnt'];
				$Page = ceil($TotalCount / $listmaxcount);


				//------------------ sort 기능 ------------------
				$order_field = $order_field ? $order_field : 'sum_cnt';
				$order_sort = $order_sort ? $order_sort : '1';
				switch($order_field){
					case "sum_cnt": case "sum_ratio": default: $que_order = 'sum_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') . ' , sck_keyword ASC'; break;
					case "pc_sum_cnt": $que_order = 'sum_mobileN_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') .', sum_cnt DESC' ; break;
					case "mo_sum_cnt": $que_order = 'sum_mobileY_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') .', sum_cnt DESC' ; break;
					case "keyword": $que_order = 'sck_keyword '. ($order_sort == '2' ? 'DESC' : 'ASC') .', sum_cnt DESC' ; break;
				}
				//------------------ sort 기능 ------------------


				// ------- 순위별 목록 -------
				$que = "
					SELECT
						sck_keyword ,
						'----------- 접속기기 -----------',
						SUM( sck_cnt_mo ) as sum_mobileY_cnt,
						SUM( sck_cnt_pc ) as sum_mobileN_cnt,
						'----------- 총방문수 -----------',
						SUM( sck_cnt_pc + sck_cnt_mo ) as sum_cnt

					FROM smart_cntlog_keyword
						" . $s_query . "
					GROUP BY sck_keyword
					ORDER BY ". $que_order ."
					limit " . $count . " , " . $listmaxcount . "
				";
				$res = _MQ_assoc($que);
				foreach( $res as $datek => $datev ){

					$_num = $count + $datek + 1;// 순위

					$sum_cnt = $arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1;
					$_ratio =  number_format( 100 * $datev['sum_cnt'] / $sum_cnt , 2); // 비율

					echo '
						<tr>
							<td class="this_num">'. $_num .'</td>
							<td class="t_left">' . ($datev['sck_keyword'] ? $datev['sck_keyword'] : '<span class="t_none">키워드 없음 (직접접속)</span>') . '</td>
							<td>
								<div class="in_price">
									<ul class="in_thead">
										<li>PC <span class="t_orange">(' . number_format($datev['sum_mobileN_cnt'] * 100 /($datev['sum_cnt'] > 0 ? $datev['sum_cnt'] : 1),2) . '%)</span></li>
										<li>모바일 <span class="t_orange">(' . number_format($datev['sum_mobileY_cnt'] * 100 /($datev['sum_cnt'] > 0 ? $datev['sum_cnt'] : 1),2) . '%)</span></li>
										<li>전체</li>
									</ul>
									<ul>
										<li>' . number_format($datev['sum_mobileN_cnt']) . '건</li>
										<li>' . number_format($datev['sum_mobileY_cnt']) . '건</li>
										<li class="t_blue">' . number_format($datev['sum_cnt']) . '건</li>
									</ul>
								</div>
							</td>
							<td class="t_orange t_right this_date">' . $_ratio . '%</td>
						</tr>
					';
				}
				// ------- 순위별 목록 -------
			?>

		</tbody>
	</table>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

</div>








<form name="frmSearch" method="post" action="_cntlog.pro.php" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="pass_sdate" value="<?php echo $pass_sdate; ?>">
	<input type="hidden" name="pass_edate" value="<?php echo $pass_edate; ?>">
</form>



<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('cntlog_keyword_search');
		$('form[name="frmSearch"]').attr('action', '_cntlog.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>




<?//------------------ sort 기능 ------------------?>
<?php
	// sort 함수
	function sort_click( $key , $value ){
		global $order_field , $order_sort ;
		echo '<span class="sort_click" data-key="'.$key.'" style="cursor:pointer;">'.$value . ($order_field == $key ? ' <strong>'. ($order_sort == 2 ? '▲' : '▼') .'</strong>' : '▽') .'</span>';
	}
?>
<script>
	// --- sort 실행 ---
	$(document).ready(function(){
		$(".sort_click").click(function(){
			$('input[name="order_sort"]').val( $('input[name="order_sort"]').val() == "1" && $('input[name="order_field"]').val() == $(this).data('key') ? "2" : "1" );
			$('input[name="order_field"]').val($(this).data('key'));
			$('form[name="searchfrm"]')[0].submit();
		});
	});
</script>
<?//------------------ sort 기능 ------------------?>
