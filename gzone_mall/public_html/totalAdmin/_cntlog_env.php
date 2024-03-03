<?php
	include_once('wrap.header.php');

	// 일자계산
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

	// 색상 배열
	$arr_color = array(
		"rgba(0, 0, 255, 0.5)",
		"rgba(255, 0, 0, 0.5)",
		"rgba(0, 128, 0, 0.5)",
		"rgba(128, 0, 255, 0.5)",
		"rgba(255, 0, 128, 0.5)",
		"rgba(0, 128, 128, 0.5)",
		"rgba(128, 0, 0, 0.5)",
		"rgba(255, 128, 0, 0.5)",
		"rgba(0, 128, 255, 0.5)",
		"rgba(255, 128, 255, 0.5)",
		"rgba(0, 0, 255, 0.5)",
		"rgba(255, 0, 0, 0.5)",
		"rgba(0, 128, 0, 0.5)",
		"rgba(128, 0, 255, 0.5)",
		"rgba(255, 0, 128, 0.5)",
		"rgba(0, 128, 128, 0.5)",
		"rgba(128, 0, 0, 0.5)",
		"rgba(255, 128, 0, 0.5)",
		"rgba(0, 128, 255, 0.5)",
		"rgba(255, 128, 255, 0.5)"
	);


	$arr_sum = array();

	// ------------- device -------------
	$s_query = " where 1 ";
	$s_query .= " and scd_date between '". $pass_sdate ."' and '". $pass_edate ."' ";
	$que = "
		SELECT
			scd_device ,
			SUM(scd_cnt) as sum
		FROM smart_cntlog_device
			" . $s_query . "
		GROUP BY scd_device
		ORDER BY sum DESC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_sum['device'][$v['scd_device']] += $v['sum'];
		$arr_data_color['device'][$v['scd_device']] = $arr_color[$k];
	}
	// ------------- device -------------

	// ------------- os -------------
	$s_query = " where 1 ";
	$s_query .= " and sco_date between '". $pass_sdate ."' and '". $pass_edate ."' ";
	$que = "
		SELECT
			sco_os ,
			SUM(sco_cnt_pc + sco_cnt_mo) as sum
		FROM smart_cntlog_os
			" . $s_query . "
		GROUP BY sco_os
		ORDER BY sum DESC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_sum['os'][$v['sco_os']] += $v['sum'];
		$arr_data_color['os'][$v['sco_os']] = $arr_color[$k];
	}
	// ------------- os -------------

	// ------------- browser -------------
	$s_query = " where 1 ";
	$s_query .= " and scb_date between '". $pass_sdate ."' and '". $pass_edate ."' ";
	$que = "
		SELECT
			scb_browser ,
			SUM(scb_cnt_pc + scb_cnt_mo) as sum
		FROM smart_cntlog_browser
			" . $s_query . "
		GROUP BY scb_browser
		ORDER BY sum DESC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_sum['browser'][$v['scb_browser']] += $v['sum'];
		$arr_data_color['browser'][$v['scb_browser']] = $arr_color[$k];
	}
	// ------------- browser -------------

	// ------------- age -------------
	$s_query = " where 1 ";
	$s_query .= " and sca_date between '". $pass_sdate ."' and '". $pass_edate ."' and sca_age < 1000 ";
	$que = "
		SELECT
			truncate(sca_age/10,0) * 10 as age ,
			SUM(sca_cnt_pc + sca_cnt_mo) as sum
		FROM smart_cntlog_age
			" . $s_query . "
		GROUP BY age
		ORDER BY age ASC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$v['age'] = $v['age'] > 1000 ? 0 : $v['age']; // 1000이 넘을 경우 잘못된 등록으로 봄
		$age_text = $v['age']<=0?"10대 미만":$v['age']."대";
		$arr_sum['age'][$age_text] += $v['sum'];
		$arr_data_color['age'][$age_text] = $arr_color[$k];
	}
	// ------------- age -------------

	// ------------- sex -------------
	$s_query = " where 1 ";
	$s_query .= " and scs_date between '". $pass_sdate ."' and '". $pass_edate ."' ";
	$que = "
		SELECT
			scs_sex ,
			SUM(scs_cnt_pc + scs_cnt_mo) as sum
		FROM smart_cntlog_sex
			" . $s_query . "
		GROUP BY scs_sex
		ORDER BY sum DESC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$app_sex = ($v['scs_sex'] == 'M' ? '남성' : ( $v['scs_sex'] == 'F' ? '여성' : '미선택' )) ;
		$arr_sum['sex'][$app_sex] += $v['sum'];
		$arr_data_color['sex'][$app_sex] = $arr_color[$k];
	}
	// ------------- sex -------------


?>


<div class="group_title type_search">
	<strong><?php echo date("Y년 m월 d일" , strtotime($pass_sdate))?> ~ <?php echo date("Y년 m월 d일" , strtotime($pass_edate))?></strong>
	<!-- 기간검색 -->
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
	<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
	<input type="hidden" name="_mode" value="search">

		<div class="lineup-row type_date">
			<input type="text" name="pass_sdate" class='design js_pic_day_max_today' value="<?php echo $pass_sdate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
			<span class="fr_tx">~</span>
			<input type="text" name="pass_edate" class='design js_pic_day_max_today' value="<?php echo $pass_edate?>" autocomplete="off" placeholder="날짜 선택" readonly style="width:90px;">
			<span class="c_btn h34 blue"><input type="submit" value="검색" accesskey="s"></span>
			<?php if($_mode == 'search'){ ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal" accesskey="l">초기화</a>
			<?php } ?>
		</div>
	</form><!-- end simple_search -->
</div>




<?/*-------------------------------------- Device 현황 -------------------------------------- */?>
<div class="group_title type_first"><strong>접속기기</strong></div>
<div class="data_graph">
	<dl>
		<dt>
			<?php if(count($arr_sum['device']) > 0 ) {?>
				<canvas id="js_couter_chart1" height="330"></canvas>
			<?php }else{?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
			<?php }?>
		</dt>
		<dd>

			<table class="table_must">
				<colgroup><col width="100"><col width="*"><col width="*"></colgroup>
				<thead>
					<th>구분</th>
					<th>접속건수</th>
					<th>비율</th>
				</thead>
				<tbody>
					<?php
						if(count($arr_sum['device']) > 0 ) {
							foreach($arr_sum['device'] as $k=>$v){
								echo '
										<th>'. $k .'</th>
										<td class=" t_right">' . number_format($v) . '건</td>
										<td class=" t_right">' . number_format( (sizeof($arr_sum['device']) > 0 ? $v * 100 / array_sum($arr_sum['device']) : 0), 2) . '%</td>
									</tr>
								';
							}
						}
					?>
					<tr>
						<th>전체</th>
						<td class="t_blue t_bold t_right"><?php echo (sizeof($arr_sum['device']) > 0 ? number_format(array_sum($arr_sum['device'])) : 0)?>건</td>
						<td class="t_orange t_bold t_right">100%</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
</div>
<?/*-------------------------------------- Device 현황 -------------------------------------- */?>


<?/*-------------------------------------- OS 현황 -------------------------------------- */?>
<div class="group_title"><strong>접속 OS</strong></div>
<div class="data_graph">
	<dl>
		<dt>
			<?php if(count($arr_sum['os']) > 0 ) {?>
				<canvas id="js_couter_chart2" height="330"></canvas>
			<?php }else{?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
			<?php }?>
		</dt>
		<dd>
			<table class="table_must">
				<colgroup><col width="100"><col width="*"><col width="*"></colgroup>
				<thead>
					<th>구분</th>
					<th>접속건수</th>
					<th>비율</th>
				</thead>
				<tbody>

					<?php
						if(count($arr_sum['os']) > 0 ) {
							foreach($arr_sum['os'] as $k=>$v){
								echo '
									<tr>
										<th>'. $k .'</th>
										<td class=" t_right">' . number_format($v) . '건</td>
										<td class=" t_right">' . number_format( (sizeof($arr_sum['os']) > 0 ? $v * 100 / array_sum($arr_sum['os']) : 0), 2) . '%</td>
									</tr>
								';
							}
						}
					?>
					<tr>
						<th>전체</th>
						<td class="t_blue t_bold t_right"><?=(sizeof($arr_sum['os']) > 0 ? number_format(array_sum($arr_sum['os'])) : 0)?>건</td>
						<td class="t_orange t_bold t_right">100%</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
</div>
<?/*-------------------------------------- OS 현황 -------------------------------------- */?>


<?/*-------------------------------------- BROWSER 현황 -------------------------------------- */?>
<div class="group_title"><strong>접속 브라우저</strong></div>
<div class="data_graph">
	<dl>
		<dt>
			<?php if(count($arr_sum['browser']) > 0 ) {?>
				<canvas id="js_couter_chart3" height="330"></canvas>
			<?php }else{?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
			<?php }?>
		</dt>
		<dd>
			<table class="table_must">
				<colgroup><col width="170"><col width="*"><col width="*"></colgroup>
				<thead>
					<th>구분</th>
					<th>접속건수</th>
					<th>비율</th>
				</thead>
				<tbody>
					<?php
						if(count($arr_sum['browser']) > 0 ) {
							foreach($arr_sum['browser'] as $k=>$v){
								echo '
									<tr>
										<th>'. $k .'</th>
										<td class=" t_right">' . number_format($v) . '건</td>
										<td class=" t_right">' . number_format( (sizeof($arr_sum['browser']) > 0 ? $v * 100 / array_sum($arr_sum['browser']) : 0), 2) . '%</td>
									</tr>
								';
							}
						}
					?>
					<tr>
						<th>전체</th>
						<td class="t_blue t_bold t_right"><?=(sizeof($arr_sum['browser']) > 0 ? number_format(array_sum($arr_sum['browser'])) : 0)?>건</td>
						<td class="t_orange t_bold t_right">100%</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
</div>
<?/*-------------------------------------- BROWSER 현황 -------------------------------------- */?>

<?/*-------------------------------------- 연령별 현황 -------------------------------------- */?>
<div class="group_title"><strong>접속 연령대</strong></div>
<div class="data_graph">
	<dl>
		<dt>
			<?php if(count($arr_sum['age']) > 0 ) {?>
				<canvas id="js_couter_chart4" height="330"></canvas>
			<?php }else{?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
			<?php }?>
		</dt>
		<dd>
			<table class="table_must">
				<colgroup><col width="100"><col width="*"><col width="*"></colgroup>
				<thead>
					<th>구분</th>
					<th>접속건수</th>
					<th>비율</th>
				</thead>
				<tbody>
					<?php
						if(count($arr_sum['age']) > 0 ) {
							foreach($arr_sum['age'] as $k=>$v){
								echo '
									<tr>
										<th>'. $k .'</th>
										<td class=" t_right">' . number_format($v) . '건</span></td>
										<td class=" t_right">' . number_format( (sizeof($arr_sum['age']) > 0 ? $v * 100 / array_sum($arr_sum['age']) : 0), 2) . '%</td>
									</tr>
								';
							}
						}
					?>
					<tr>
						<th>전체</th>
						<td class="t_blue t_bold t_right"><?php echo (sizeof($arr_sum['age']) > 0 ? number_format(array_sum($arr_sum['age'])) : 0)?>건</td>
						<td class="t_orange t_bold t_right">100</span>%</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
</div>
<?/*-------------------------------------- 연령별 현황 -------------------------------------- */?>

<?/*-------------------------------------- 성별 현황 -------------------------------------- */?>
<div class="group_title"><strong>접속 성별</strong></div>
<div class="data_graph">
	<dl>
		<dt>
			<?php if(count($arr_sum['sex']) > 0 ) {?>
				<canvas id="js_couter_chart5" height="330"></canvas>
			<?php }else{?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
			<?php }?>
		</dt>
		<dd>
			<table class="table_must">
				<colgroup><col width="100"><col width="*"><col width="*"></colgroup>
				<thead>
					<th>구분</th>
					<th>접속건수</th>
					<th>비율</th>
				</thead>
				<tbody>
					<?php
						if(count($arr_sum['sex']) > 0 ) {
							foreach($arr_sum['sex'] as $k=>$v){
								echo '
									<tr>
										<th>'. $k .'</th>
										<td class=" t_right">' . number_format($v) . '건</td>
										<td class=" t_right">' . number_format( (sizeof($arr_sum['sex']) > 0 ? $v * 100 / array_sum($arr_sum['sex']) : 0), 2) . '%</td>
									</tr>
								';
							}
						}
					?>
					<tr>
						<th>전체</th>
						<td class="t_blue t_bold t_right"><?=(sizeof($arr_sum['sex']) > 0 ? number_format(array_sum($arr_sum['sex'])) : 0)?>건</td>
						<td class="t_orange t_bold t_right">100%</td>
					</tr>
				</tbody>
			</table>
		</dd>
	</dl>
</div>
<?/*-------------------------------------- 성별 현황 -------------------------------------- */?>







<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>

	// ---------- 파이 - 그래프 ( DEVICE ) ----------
	<?php if(sizeof($arr_sum['device']) > 0 ) { ?>
    var config1 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum['device']))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_data_color['device']))?>"],
            }],
            labels: ["<?=implode('" , "' , array_keys($arr_sum['device']))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx1 = document.getElementById("js_couter_chart1").getContext("2d");
	window.myPie = new Chart(ctx1, config1);
	<?php } ?>
	// ---------- 파이 - 그래프 ( DEVICE ) ----------



	// ---------- 파이 - 그래프 ( OS ) ----------
	<?php if(sizeof($arr_sum['os']) > 0 ) { ?>
    var config2 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum['os']))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_data_color['os']))?>"],
            }],
            labels: ["<?=implode('" , "' , array_keys($arr_sum['os']))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx2 = document.getElementById("js_couter_chart2").getContext("2d");
	window.myPie = new Chart(ctx2, config2);
	<?php } ?>
	// ---------- 파이 - 그래프 ( OS ) ----------



	// ---------- 파이 - 그래프 ( BROWSER ) ----------
	<?php if(sizeof($arr_sum['browser']) > 0 ) { ?>
    var config3 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum['browser']))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_data_color['browser']))?>"],
            }],
            labels: ["<?=implode('" , "' , array_keys($arr_sum['browser']))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx3 = document.getElementById("js_couter_chart3").getContext("2d");
	window.myPie = new Chart(ctx3, config3);
	<?php } ?>
	// ---------- 파이 - 그래프 ( BROWSER ) ----------



	// ---------- 파이 - 그래프 ( age ) ----------
	<?php if(sizeof($arr_sum['age']) > 0 ) { ?>
    var config4 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum['age']))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_data_color['age']))?>"],
            }],
            labels: ["<?=implode('" , "' , array_keys($arr_sum['age']))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx4 = document.getElementById("js_couter_chart4").getContext("2d");
	window.myPie = new Chart(ctx4, config4);
	<?php } ?>
	// ---------- 파이 - 그래프 ( age ) ----------



	// ---------- 파이 - 그래프 ( sex ) ----------
	<?php if(sizeof($arr_sum['sex']) > 0 ) { ?>
    var config5 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum['sex']))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_data_color['sex']))?>"],
            }],
            labels: ["<?=implode('" , "' , array_keys($arr_sum['sex']))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx5 = document.getElementById("js_couter_chart5").getContext("2d");
	window.myPie = new Chart(ctx5, config5);
	<?php } ?>
	// ---------- 파이 - 그래프 ( sex ) ----------

</script>




<?php

	include_once('wrap.footer.php');

?>