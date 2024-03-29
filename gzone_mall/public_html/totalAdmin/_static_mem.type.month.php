<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 회원 상태 분석


// 일자계산
$pass_date = ($pass_date?$pass_date:date('Y', time()));
$Select_Year = $pass_date;


// ------- 전체 회원수 -------
$mem_tot_sum = _MQ("SELECT count(*) as total_plus FROM smart_individual ");
$mem_total_cnt = $mem_tot_sum['total_plus'];


$arr_data = $arr_max = array();




// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
$mem_date = _MQ_assoc(" SELECT  LEFT(in_odate,7) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_odate,4) = '". $pass_date ."' and in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
foreach($mem_date as $k=>$v){
	$arr_data['out'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
	$app_max['out'] = ($app_max['out'] < $v['total_plus'] ? $v['total_plus'] : $app_max['out']);
}

// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
$mem_date = _MQ_assoc("
	SELECT
		LEFT(ins.ins_rdate, 7) as rdate , count(*) as total_plus
	FROM smart_individual_sleep as ins
	INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
	WHERE
		LEFT(ins.ins_rdate,4) = '". $pass_date ."'
	GROUP BY rdate
	ORDER BY rdate ASC
");
foreach($mem_date as $k=>$v){
	$arr_data['sleep'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
	$app_max['sleep'] = ($app_max['sleep'] < $v['total_plus'] ? $v['total_plus'] : $app_max['out']);
}


// ------- 기간내 가입 회원 수 -------
$arr_tot_cumul = array(); // 누적정보 저장
$res = _MQ_assoc(" SELECT LEFT(in_rdate,7) as rdate ,count(*) as total_plus FROM smart_individual WHERE LEFT(in_rdate,4) = '". $pass_date ."' GROUP BY rdate ORDER BY rdate ASC ");
foreach($res as $k=>$v){
	$arr_tot_cumul[end(explode("-" , $v['rdate']))] = array(
		'idx' => $arr_tot_cumul[end(explode("-" , $v['rdate']))]['idx'] + 1 , // 횟수
		'cnt' => $arr_tot_cumul[end(explode("-" , $v['rdate']))]['cnt'] + $v['total_plus'] // 누적수
	);
}

$arr_tot_data = array();
$app_tot_max = 0; // 기간내 가입 회원 수 평균 최대치
$arr_tot_maxmin = array(); // 기간내 가입 회원 수 평균 최대 최소 방문정보 배열 기록
foreach($arr_tot_cumul as $k=>$v){
	$v['idx'] = $v['idx'] > 0 ? $v['idx'] : 1; // 횟수
	$avg_cnt = round($v['cnt'] / $v['idx']);
	$arr_tot_data[$k] = $avg_cnt;
	$app_tot_max = ($app_tot_max < $avg_cnt ? $avg_cnt : $app_tot_max);
}
// ------- 기간내 가입 회원 수 -------




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
for($i=1 ; $i<=12 ; $i++){

	$arr_date_date[$i] = $i . "월";// 접속자일

	// ------------------------ 기간 내 상태 분석 ------------------------
	$arr_date_num['sleep'][$i] = $arr_data['sleep'][$Select_Year . sprintf("%02d" , $i)] * 1;// 선택월 휴면 회원수
	$arr_date_num['out'][$i] = $arr_data['out'][$Select_Year . sprintf("%02d" , $i)] * 1;// 선택월 탈퇴 회원수



	// 휴면 - 최대값일 경우
	if( $app_max['sleep'] == $arr_date_num['sleep'][$i] && $app_max['sleep'] > 0) {
		$arr_date_color['sleep'][$i] = "rgba(0, 128, 0, 0.2)"; // 그래프 색
		$arr_date_border['sleep'][$i] = "rgba(0, 128, 0,1)"; // 그래프 border 색
	}
	// 휴면 - 일반 데이터 일 경우
	else {
		$arr_date_color['sleep'][$i] = "rgba(128, 0, 255, 0.2)"; // 그래프 색
		$arr_date_border['sleep'][$i] = "rgba(128, 0, 255, 1)"; // 그래프 border 색
	}

	// 탈퇴 - 최대값일 경우
	if( $app_max['out'] == $arr_date_num['out'][$i] && $app_max['out'] > 0) {
		$arr_date_color['out'][$i] = "rgba(255, 0, 128, 0.2)"; // 그래프 색
		$arr_date_border['out'][$i] = "rgba(255, 0, 128, 1)"; // 그래프 border 색
	}
	// 탈퇴 - 일반 데이터 일 경우
	else {
		$arr_date_color['out'][$i] = "rgba(0, 128, 128, 0.2)"; // 그래프 색
		$arr_date_border['out'][$i] = "rgba(0, 128, 128, 1)"; // 그래프 border 색
	}



	// 휴면 - 최대값 체크
	$arr_maxmin['sleep']['max'] = ( $arr_maxmin['sleep']['max']['cnt'] < $arr_date_num['sleep'][$i] ? array('cnt'=>$arr_date_num['sleep'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['sleep']['max']);




	// 휴면 - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['sleep']['min']['cnt'])) {
		$arr_maxmin['sleep']['min'] = array('cnt'=>$arr_date_num['sleep'][$i] , 'date'=>sprintf("%02d" , $i));
		$arr_avg['sleep']['idx'] ++; // 계산할 횟수
		$arr_avg['sleep']['cnt'] += $arr_date_num['sleep'][$i]; // 계산할 수
	}
	// 휴면 - 최소 정보일 경우 현시간 제외
	else if(date("Ym") > $Select_Year . sprintf("%02d" , $i)){
		$arr_maxmin['sleep']['min'] = ( $arr_maxmin['sleep']['min']['cnt'] > $arr_date_num['sleep'][$i] ? array('cnt'=>$arr_date_num['sleep'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['sleep']['min']);
		$arr_avg['sleep']['idx'] ++; // 계산할 횟수
		$arr_avg['sleep']['cnt'] += $arr_date_num['sleep'][$i]; // 계산할 수
	}

	// 탈퇴 - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['out']['min']['cnt'])) {
		$arr_maxmin['out']['min'] = array('cnt'=>$arr_date_num['out'][$i] , 'date'=>sprintf("%02d" , $i));
		$arr_avg['out']['idx'] ++; // 계산할 횟수
		$arr_avg['out']['cnt'] += $arr_date_num['out'][$i]; // 계산할 수
	}
	// 탈퇴 - 최소 정보일 경우 현시간 제외
	else if(date("Ym") > $Select_Year . sprintf("%02d" , $i)){
		$arr_maxmin['out']['min'] = ( $arr_maxmin['out']['min']['cnt'] > $arr_date_num['out'][$i] ? array('cnt'=>$arr_date_num['out'][$i] , 'date'=>sprintf("%02d" , $i)) : $arr_maxmin['out']['min']);
		$arr_avg['out']['idx'] ++; // 계산할 횟수
		$arr_avg['out']['cnt'] += $arr_date_num['out'][$i]; // 계산할 수
	}
	// ------------------------ 기간 내 상태 분석 ------------------------



	// ------------------------ 기간내 가입 회원 수 ------------------------
	$arr_tot_date_num[$i] = $arr_tot_data[sprintf("%02d" , $i)] * 1;// 선택일자 회원수
	$arr_tot_date_date[$i] = $i . "월";// 접속자월

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
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 수
	}
	else {
		$arr_tot_maxmin['min'] = ( $arr_tot_maxmin['min']['cnt'] > $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%02d" , $i)) : $arr_tot_maxmin['min']);
		$arr_max_avg['idx'] ++; // 계산할 횟수
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 수
	}
	// ------------------------ 기간내 가입 회원 수 평균 ------------------------


}

?>


<div class="group_title type_search">
	<strong><?php echo $Select_Year; ?>년</strong>

	<!-- 기간검색 -->
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
		<input type="hidden" name="_type" value="<?php echo $_type; ?>">
		<input type="hidden" name="_mode" value="search">

		<div class="lineup-row type_date">
			<input type="text" name="pass_date" class="design js_pic_year_max_today right" value="<?php echo $pass_date; ?>" style="width:70px" autocomplete="off" placeholder="날짜 선택" readonly>
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
		<thead>
			<tr>
				<th>가입</th>
				<th>휴면</th>
				<th>탈퇴</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="t_blue t_bold t_right"><?php echo number_format( IS_ARRAY($arr_tot_data) ? array_sum($arr_tot_data) : 0); ?>명</td>
				<td class="t_green t_bold t_right"><?php echo number_format( IS_ARRAY($arr_data['sleep']) ? array_sum($arr_data['sleep']) : 0) ?>명</td>
				<td class="t_red t_bold t_right"><?php echo number_format( IS_ARRAY($arr_data['out']) ? array_sum($arr_data['out']) : 0) ?>명</td>
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
</form>


<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>

	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('type_month_search');
		$('form[name="frmSearch"]').attr('action', '_static_mem.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---



	// ---------- 기본 line-bar 그래프 (회원 상태 분석) ----------
	var background = 'rgba(255,99,132,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [

		// 휴면 회원수
		{
			type: 'bar',
			label: '<?php echo $Select_Year; ?>년 휴면 회원수',
			data: [<?=implode(' , ' , array_values($arr_date_num['sleep']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['sleep']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['sleep']))?>"],
			borderWidth: 1
		},
		// 탈퇴 회원수
		{
			type: 'bar',
			label: '<?php echo $Select_Year; ?>년 탈퇴 회원수',
			data: [<?=implode(' , ' , array_values($arr_date_num['out']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['out']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['out']))?>"],
			borderWidth: 1
		},
		// 가입 회원수
		{
			type: 'line',
			label: '<?php echo $Select_Year; ?>년 가입 회원수',
			data: [<?=implode(' , ' , array_values($arr_tot_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 (회원 상태 분석) ----------


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
	$arr_class_data['total'] =  array('grid_sky');

	$arr_table_data = array();

	for($i=1 ; $i<=12 ; $i++){

		$app_date = $Select_Year ."년 ". sprintf("%02d" , $i) . "월";
		$app_date_key = $Select_Year . sprintf("%02d" , $i);

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'className' =>array(
						'column' => $arr_class_data
					)
				),
				'rdate' => $app_date,
				'total' => number_format($arr_tot_data[sprintf("%02d" , $i)] * 1),
				'sleep' => number_format($arr_data['sleep'][$app_date_key] * 1),
				'out' => number_format($arr_data['out'][$app_date_key] * 1)

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
            {"title" : "<b>년월</b>", "columnName" : "rdate", "align" : "center", "width" : 80 },
			{"title" : "<b>가입회원수</b>", "columnName" : "total", "align" : "right", "width" : 80 },

			{"title" : "<b>휴면회원수</b>", "columnName" : "sleep", "align" : "right", "width" : 80 },
			{"title" : "<b>탈퇴회원수</b>", "columnName" : "out", "align" : "right", "width" : 80 },

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>