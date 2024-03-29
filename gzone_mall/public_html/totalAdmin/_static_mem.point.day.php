<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 회원 적립금 분석


// 일자계산
$pass_date = ($pass_date?$pass_date:date('Y-m', time()));
$Select_Year = date('Y', strtotime($pass_date));
$Select_Month = date('m', strtotime($pass_date));




// ------- 회원 적립금 분석 Summary -------
	// 전체 , 지급예정 정보 취합
	$res1 = _MQ("
		SELECT
			SUM(IFNULL(pl_point,0)) as total_sum,
			SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
		FROM smart_point_log
		WHERE
			LEFT(pl_rdate,7) = '". $pass_date ."'
	");
	// 지급 , 사용 정보 취합
	$res2 = _MQ("
		SELECT
			SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
			SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
		FROM smart_point_log
		WHERE
			LEFT(pl_appdate,7) = '". $pass_date ."'
	");
	$mem_sum = array_merge($res1 , $res2);
// ------- 회원 적립금 분석 Summary -------


// ------- 회원 적립금 분석 - 날짜별 목록 -------
	$arr_data = $arr_max = array();
	$arr_tot_cumul = array(); // 누적정보 저장
	$mres1 = _MQ_assoc("
		SELECT
			DATE(pl_rdate) as rdate ,
			SUM(IFNULL(pl_point,0)) as total_sum,
			SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
		FROM smart_point_log
		WHERE
			LEFT(pl_rdate,7) = '". $pass_date ."'
		GROUP BY rdate
		ORDER BY rdate ASC
	");
	$mres2 = _MQ_assoc("
		SELECT
			pl_appdate as rdate ,
			SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
			SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
		FROM smart_point_log
		WHERE
			LEFT(pl_appdate,7) = '". $pass_date ."'
		GROUP BY rdate
		ORDER BY rdate ASC
	");
	$mem_date = array_merge($mres1 , $mres2);
	foreach($mem_date as $k=>$v){

		$arr_data['pay_ing'][DATE("Ymd" , strtotime($v['rdate']))] = $v['pay_ing'];
		$app_max['pay_ing'] = ($app_max['pay_ing'] < $v['pay_ing'] ? $v['pay_ing'] : $app_max['pay_ing']);

		$arr_data['pay_end'][DATE("Ymd" , strtotime($v['rdate']))] = $v['pay_end'];
		$app_max['pay_end'] = ($app_max['pay_end'] < $v['pay_end'] ? $v['pay_end'] : $app_max['pay_end']);

		$arr_data['use_end'][DATE("Ymd" , strtotime($v['rdate']))] = $v['use_end'];
		$app_max['use_end'] = (abs($app_max['use_end']) < abs($v['use_end']) ? $v['use_end'] : $app_max['use_end']);

		// 총액
		$arr_tot_cumul[DATE("d" , strtotime($v['rdate']))] = array(
			'idx' => $arr_tot_cumul[DATE("d" , strtotime($v['rdate']))]['idx'] + 1 , // 횟수
			'cnt' => $arr_tot_cumul[DATE("d" , strtotime($v['rdate']))]['cnt'] + $v['total_sum'] // 총액
		);

	}
// ------- 회원 적립금 분석 - 날짜별 목록 -------


// ------- 기간내 적립금 총액수 -------
	$arr_tot_data = array();
	$app_tot_max = 0; // 기간내 가입 회원 수 평균 최대치
	$arr_tot_maxmin = array(); // 기간내 가입 회원 수 평균 최대 최소 방문정보 배열 기록
	foreach($arr_tot_cumul as $k=>$v){
		$v['idx'] = $v['idx'] > 0 ? $v['idx'] : 1; // 횟수
		$avg_cnt = round($v['cnt'] / $v['idx']);
		$arr_tot_data[$k] = $avg_cnt;
		$app_tot_max = ($app_tot_max < $avg_cnt ? $avg_cnt : $app_tot_max);
	}
// ------- 기간내 적립금 총액수 -------



# Chart 그래프 적용
$arr_date_num = array(); // 회원수
$arr_date_date = array(); // 접속자일
$arr_date_color = array(); // 그래프 색
$arr_date_border = array(); // 그래프 border 색

$arr_tot_date_num = array(); // 기간내 가입 회원 수
$arr_tot_date_date = array(); // 기간내 가입 회원 날짜
$arr_tot_date_color = array(); // 기간내 가입 회원 그래프 색
$arr_tot_date_border = array(); // 기간내 가입 회원 그래프 border 색

$arr_avg = $arr_max_avg = array();
for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

	// ------------------------ 가입기기별 ------------------------
	$arr_date_date[$i] = $i . "일";// 접속자일


	$arr_date_num['pay_ing'][$i] = $arr_data['pay_ing'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1;// 선택일자 지급예정액
	$arr_date_num['pay_end'][$i] = $arr_data['pay_end'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1;// 선택일자 지급액
	$arr_date_num['use_end'][$i] = $arr_data['use_end'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1;// 선택일자 사용액


	// 지급예정액 - 최대값일 경우
	if( $app_max['pay_ing'] == $arr_date_num['pay_ing'][$i] && $app_max['pay_ing'] > 0) {
		$arr_date_color['pay_ing'][$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
		$arr_date_border['pay_ing'][$i] = "rgba(255,99,132,1)"; // 그래프 border 색
	}
	// 지급예정액 - 일반 데이터 일 경우
	else {
		$arr_date_color['pay_ing'][$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
		$arr_date_border['pay_ing'][$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
	}

	// 지급액 - 최대값일 경우
	if( $app_max['pay_end'] == $arr_date_num['pay_end'][$i] && $app_max['pay_end'] > 0) {
		$arr_date_color['pay_end'][$i] = "rgba(0, 128, 0, 0.2)"; // 그래프 색
		$arr_date_border['pay_end'][$i] = "rgba(0, 128, 0,1)"; // 그래프 border 색
	}
	// 지급액 - 일반 데이터 일 경우
	else {
		$arr_date_color['pay_end'][$i] = "rgba(128, 0, 255, 0.2)"; // 그래프 색
		$arr_date_border['pay_end'][$i] = "rgba(128, 0, 255, 1)"; // 그래프 border 색
	}

	// 사용액 - 최대값일 경우
	if( $app_max['use_end'] == $arr_date_num['use_end'][$i] && $app_max['use_end'] < 0) {
		$arr_date_color['use_end'][$i] = "rgba(255, 0, 128, 0.2)"; // 그래프 색
		$arr_date_border['use_end'][$i] = "rgba(255, 0, 128,1)"; // 그래프 border 색
	}
	// 사용액 - 일반 데이터 일 경우
	else {
		$arr_date_color['use_end'][$i] = "rgba(0, 128, 128, 0.2)"; // 그래프 색
		$arr_date_border['use_end'][$i] = "rgba(0, 128, 128, 1)"; // 그래프 border 색
	}


	// 지급예정액 - 최대값 체크
	$arr_maxmin['pay_ing']['max'] = ( $arr_maxmin['pay_ing']['max']['cnt'] < $arr_date_num['pay_ing'][$i] ? array('cnt'=>$arr_date_num['pay_ing'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['pay_ing']['max']);

	// 지급액 - 최대값 체크
	$arr_maxmin['pay_end']['max'] = ( $arr_maxmin['pay_end']['max']['cnt'] < $arr_date_num['pay_end'][$i] ? array('cnt'=>$arr_date_num['pay_end'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['pay_end']['max']);

	// 사용약 - 최대값 체크
	$arr_maxmin['use_end']['max'] = ( $arr_maxmin['use_end']['max']['cnt'] < $arr_date_num['use_end'][$i] ? array('cnt'=>$arr_date_num['use_end'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['use_end']['max']);


	// 지급예정액 - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['pay_ing']['min']['cnt'])) {
		$arr_maxmin['pay_ing']['min'] = array('cnt'=>$arr_date_num['pay_ing'][$i] , 'date'=>sprintf("%02d" , $i));
		$arr_avg['pay_ing']['idx'] ++; // 계산할 횟수
		$arr_avg['pay_ing']['cnt'] += $arr_date_num['pay_ing'][$i]; // 계산할 접속수
	}
	// 지급예정액 - 최소 정보일 경우 현시간 제외
	else if(date("Ymd") > $Select_Year . $Select_Month . sprintf("%02d" , $i)){
		$arr_maxmin['pay_ing']['min'] = ( $arr_maxmin['pay_ing']['min']['cnt'] > $arr_date_num['pay_ing'][$i] ? array('cnt'=>$arr_date_num['pay_ing'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['pay_ing']['min']);
		$arr_avg['pay_ing']['idx'] ++; // 계산할 횟수
		$arr_avg['pay_ing']['cnt'] += $arr_date_num['pay_ing'][$i]; // 계산할 접속수
	}

	// 지급액 - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['pay_end']['min']['cnt'])) {
		$arr_maxmin['pay_end']['min'] = array('cnt'=>$arr_date_num['pay_end'][$i] , 'date'=>sprintf("%02d" , $i));
		$arr_avg['pay_end']['idx'] ++; // 계산할 횟수
		$arr_avg['pay_end']['cnt'] += $arr_date_num['pay_end'][$i]; // 계산할 접속수
	}
	// 지급액 - 최소 정보일 경우 현시간 제외
	else if(date("Ymd") > $Select_Year . $Select_Month . sprintf("%02d" , $i)){
		$arr_maxmin['pay_end']['min'] = ( $arr_maxmin['pay_end']['min']['cnt'] > $arr_date_num['pay_end'][$i] ? array('cnt'=>$arr_date_num['pay_end'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['pay_end']['min']);
		$arr_avg['pay_end']['idx'] ++; // 계산할 횟수
		$arr_avg['pay_end']['cnt'] += $arr_date_num['pay_end'][$i]; // 계산할 접속수
	}

	// 사용액 - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['use_end']['min']['cnt'])) {
		$arr_maxmin['use_end']['min'] = array('cnt'=>$arr_date_num['use_end'][$i] , 'date'=>sprintf("%02d" , $i));
		$arr_avg['use_end']['idx'] ++; // 계산할 횟수
		$arr_avg['use_end']['cnt'] += $arr_date_num['use_end'][$i]; // 계산할 접속수
	}
	// 사용액 - 최소 정보일 경우 현시간 제외
	else if(date("Ymd") > $Select_Year . $Select_Month . sprintf("%02d" , $i)){
		$arr_maxmin['use_end']['min'] = ( $arr_maxmin['use_end']['min']['cnt'] > $arr_date_num['use_end'][$i] ? array('cnt'=>$arr_date_num['use_end'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['use_end']['min']);
		$arr_avg['use_end']['idx'] ++; // 계산할 횟수
		$arr_avg['use_end']['cnt'] += $arr_date_num['use_end'][$i]; // 계산할 접속수
	}
	// ------------------------ 가입기기별 ------------------------



	// ------------------------ 기간내 가입 회원 수 평균 ------------------------
	$arr_tot_date_num[$i] = $arr_tot_data[sprintf("%02d" , $i)] * 1;// 선택일자 회원수
	$arr_tot_date_date[$i] = $i . "일";// 접속자일

	//최대값일 경우
	if( $app_tot_max == $arr_tot_date_num[$i] && $app_tot_max > 0) {
		$arr_tot_date_color[$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
		$arr_tot_date_border[$i] = "rgba(255,99,132,1)"; // 그래프 border 색
	}
	// 일반 데이터 일 경우
	else {
		$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
		$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
	}

	// 최대값 체크
	$arr_tot_maxmin['max'] = ( $arr_tot_maxmin['max']['cnt'] < $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%02d" , $i)) : $arr_tot_maxmin['max']);

	// 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_tot_maxmin['min']['cnt'])) {
		$arr_tot_maxmin['min'] = array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%02d" , $i));
		$arr_max_avg['idx'] ++; // 계산할 횟수
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
	}
	else {
		$arr_tot_maxmin['min'] = ( $arr_tot_maxmin['min']['cnt'] > $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%02d" , $i)) : $arr_tot_maxmin['min']);
		$arr_max_avg['idx'] ++; // 계산할 횟수
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
	}
	// ------------------------ 기간내 가입 회원 수 평균 ------------------------


}

?>

<div class="group_title type_search">
	<strong><?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월</strong>

	<!-- 기간검색 -->
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
		<input type="hidden" name="_type" value="<?php echo $_type; ?>">
		<input type="hidden" name="_mode" value="search">

		<div class="lineup-row type_date">
			<input type="text" name="pass_date" class="design js_pic_month_max_today right" value="<?php echo $pass_date; ?>" style="width:70px" autocomplete="off" placeholder="날짜 선택" readonly>
			<span class="c_btn h34 blue"><input type="submit" value="검색" accesskey="s"></span>
			<?php if($_mode == 'search'){ ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=<?=$_type?>" class="c_btn h34 black line normal" accesskey="l">초기화</a>
			<?php } ?>
		</div>
	</form><!-- end simple_search -->

</div>


<div class="data_graph">
	<ul>
		<li><canvas id="js_couter_chart1" height="330"></canvas></li>
	</ul>

	<table class="table_must">
		<colgroup>
			<col width="*"/><col width="*"/><col width="*"/><col width="*"/>
		</colgroup>
		<thead>
			<tr>
				<th>예정</th>
				<th>지급</th>
				<th>사용</th>
				<th>실지급 합계</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="t_right t_green t_bold"><?php echo number_format($mem_sum['pay_ing'] * 1); ?>원</td>
				<td class="t_right t_blue t_bold"><?php echo number_format($mem_sum['pay_end'] * 1); ?>원</td>
				<td class="t_right t_red t_bold"><?php echo number_format($mem_sum['use_end'] * -1); ?>원</td>
				<td class="t_right t_black t_bold"><?php echo number_format($mem_sum['total_sum'] * 1); ?>원</td>
			</tr>
		</tfoot>
	</table>
</div>




<div class="data_list">
	<div class="list_ctrl">
		<div class="left_box"><strong>상세내역</strong></div>
		<div class="right_box">
			<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel only_pc_view">엑셀다운</a>
		</div>
	</div>
	<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>
	<div class="mobile_tip">상하,좌우로 스크롤을 이동하여 데이터를 확인할 수 있습니다.</div>


	<?// ----- 표 테이블  -----?>
	<div ID="grid_table"></div>


</div>
<!-- / 도표 -->



<form name="frmSearch" method="post" action="_static_mem.pro.php" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="Select_Year" value="<?php echo $Select_Year; ?>">
	<input type="hidden" name="Select_Month" value="<?php echo $Select_Month; ?>">
</form>


<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('point_day_search');
		$('form[name="frmSearch"]').attr('action', '_static_mem.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---



	// ---------- 기본 line-bar 그래프 ----------
	var background = 'rgba(255,99,132,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// 지급예정액
		{
			type: 'bar',
			label: '<?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월 지급예정액',
			data: [<?=implode(' , ' , array_values($arr_date_num['pay_ing']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['pay_ing']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['pay_ing']))?>"],
			borderWidth: 1
		},
		// 지급액
		{
			type: 'bar',
			label: '<?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월 지급액',
			data: [<?=implode(' , ' , array_values($arr_date_num['pay_end']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['pay_end']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['pay_end']))?>"],
			borderWidth: 1
		},
		// 사용액
		{
			type: 'bar',
			label: '<?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월 사용액',
			data: [<?=implode(' , ' , array_values($arr_date_num['use_end']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['use_end']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['use_end']))?>"],
			borderWidth: 1
		},
		// 기간 내 지급(예정)/사용액
		{
			type: 'line',
			label: '<?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월 지급(예정)/사용액',
			data: [<?=implode(' , ' , array_values($arr_tot_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 ----------


	window.onload = function() {

		var ctx = document.getElementById("js_couter_chart1").getContext("2d");
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: chartData,
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:false
						}
					}]
				}
			}
		});

	};
</script>



<?

//	// 색상 배열
//	$arr_color = array(
//		"rgba(0, 0, 255, 0.5)" , "rgba(255, 0, 0, 0.5)" , "rgba(0, 128, 0, 0.5)" , "rgba(128, 0, 255, 0.5)" , "rgba(255, 0, 128, 0.5)" , "rgba(0, 128, 128, 0.5)" , "rgba(128, 0, 0, 0.5)" , "rgba(255, 128, 0, 0.5)" , "rgba(0, 128, 255, 0.5)" , "rgba(255, 128, 255, 0.5)",
//		"rgba(0, 0, 255, 0.5)" , "rgba(255, 0, 0, 0.5)" , "rgba(0, 128, 0, 0.5)" , "rgba(128, 0, 255, 0.5)" , "rgba(255, 0, 128, 0.5)" , "rgba(0, 128, 128, 0.5)" , "rgba(128, 0, 0, 0.5)" , "rgba(255, 128, 0, 0.5)" , "rgba(0, 128, 255, 0.5)" , "rgba(255, 128, 255, 0.5)"
//	);
//	// 색상 border 배열
//	$arr_color_border = array(
//		"rgba(0, 0, 255, 0.8)" , "rgba(255, 0, 0, 0.8)" , "rgba(0, 128, 0, 0.8)" , "rgba(128, 0, 255, 0.8)" , "rgba(255, 0, 128, 0.8)" , "rgba(0, 128, 128, 0.8)" , "rgba(128, 0, 0, 0.8)" , "rgba(255, 128, 0, 0.8)" , "rgba(0, 128, 255, 0.8)" , "rgba(255, 128, 255, 0.8)",
//		"rgba(0, 0, 255, 0.8)" , "rgba(255, 0, 0, 0.8)" , "rgba(0, 128, 0, 0.8)" , "rgba(128, 0, 255, 0.8)" , "rgba(255, 0, 128, 0.8)" , "rgba(0, 128, 128, 0.8)" , "rgba(128, 0, 0, 0.8)" , "rgba(255, 128, 0, 0.8)" , "rgba(0, 128, 255, 0.8)" , "rgba(255, 128, 255, 0.8)"
//	);

?>











<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용
	$arr_class_data['cnt'] =  array('grid_sky');

	$arr_table_data = array();

	foreach( $arr_tot_cumul as $mem_datek => $mem_datev ){

		$app_date = $Select_Year ."-".  $Select_Month ."-". sprintf("%02d" , $mem_datek);
		$app_date_key = $Select_Year .  $Select_Month . sprintf("%02d" , $mem_datek);

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'className' =>array(
						'column' => $arr_class_data
					)
				),
				'rdate' => $app_date,
				'cnt' => number_format($mem_datev['cnt'] * 1),
				'pay_ing' => number_format($arr_data['pay_ing'][$app_date_key] * 1),
				'pay_end' => number_format($arr_data['pay_end'][$app_date_key] * 1),
				'use_end' => number_format($arr_data['use_end'][$app_date_key] * -1)

		);

		// ----- 소계 -----

	}
	// ------- 표 - 테이블 데이터 -------

?>

<link rel="stylesheet" type="text/css" href="./js/tui.grid/tui-grid.min.css">
<script type="text/javascript" src="/include/js/underscore.min.js"></script>
<script type="text/javascript" src="/include/js/backbone-min.js"></script>
<script type="text/javascript" src="./js/tui.grid/tui-code-snippet.min.js"></script>
<script src="./js/tui.grid/grid.min.js"></script>
<script type="text/javascript" class="code-js">

    var grid = new tui.Grid({
        el: $('#grid_table'),

        columnFixCount: 2,
        headerHeight: 50,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnModelList: [
            {"title" : "<b>날짜</b>", "columnName" : "rdate", "align" : "center", "width" : 80 },
			{"title" : "<b>기간 내 지급(예정)/사용 적립금 합계</b>", "columnName" : "cnt", "align" : "right", "width" : 80 },

			{"title" : "<b>적립금 지급예정액</b>", "columnName" : "pay_ing", "align" : "right", "width" : 80 },
			{"title" : "<b>적립금 지급액</b>", "columnName" : "pay_end", "align" : "right", "width" : 80 },
			{"title" : "<b>적립금 사용액</b>", "columnName" : "use_end", "align" : "right", "width" : 80 },

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>