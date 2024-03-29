<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 회원 가입형태 분석




// ------- 회원 가입형태 분석 Summary -------
$mem_sum = _MQ("
	SELECT

		count(*) as total_plus,

		SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
		SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
		SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
		SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

		SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
		SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

		SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
		SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
		SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
		SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

	FROM smart_individual
");
// ------- 회원 가입형태 분석 Summary -------



// ------- 회원 가입형태 분석 - 날짜별 목록 -------
$arr_data = $arr_max = array();
$arr_tot_cumul = array(); // 누적정보 저장
$mem_date = _MQ_assoc("
	SELECT

		LEFT(in_rdate,4) as rdate ,

		count(*) as total_plus,

		SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
		SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
		SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
		SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

		SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
		SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

		SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
		SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
		SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
		SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

	FROM smart_individual
	GROUP BY rdate
	ORDER BY rdate ASC
");
foreach($mem_date as $k=>$v){

	$arr_data['in_join_ua_PC_plus'][$v['rdate']] = $v['in_join_ua_PC_plus'];
	$app_max['in_join_ua_PC_plus'] = ($app_max['in_join_ua_PC_plus'] < $v['in_join_ua_PC_plus'] ? $v['in_join_ua_PC_plus'] : $app_max['in_join_ua_PC_plus']);

	$arr_data['in_join_ua_MOBILE_plus'][$v['rdate']] = $v['in_join_ua_MOBILE_plus'];
	$app_max['in_join_ua_MOBILE_plus'] = ($app_max['in_join_ua_MOBILE_plus'] < $v['in_join_ua_MOBILE_plus'] ? $v['in_join_ua_MOBILE_plus'] : $app_max['in_join_ua_MOBILE_plus']);

	if($v['rdate'] > 0 ) {
		$arr_tot_cumul[$v['rdate']] = array(
			'idx' => $arr_tot_cumul[$v['rdate']]['idx'] + 1 , // 횟수
			'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['total_plus'] // 누적수
		);
	}
}
// ------- 회원 가입형태 분석 - 날짜별 목록 -------



// ------- 기간내 가입 회원 수 -------
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

$year_min = IS_ARRAY($arr_tot_cumul) ? min(array_keys($arr_tot_cumul)) : DATE("Y");
$year_max = IS_ARRAY($arr_tot_cumul) ? max(array_keys($arr_tot_cumul)) : DATE("Y");

for($i=$year_min ; $i<=$year_max ; $i++){

	// ------------------------ 가입기기별 ------------------------
	$arr_date_date[$i] = $i . "년";// 접속자일


	$arr_date_num['in_join_ua_PC_plus'][$i] = $arr_data['in_join_ua_PC_plus'][sprintf("%04d" , $i)] * 1;// 선택년 PC 회원수
	$arr_date_num['in_join_ua_MOBILE_plus'][$i] = $arr_data['in_join_ua_MOBILE_plus'][sprintf("%04d" , $i)] * 1;// 선택년 MOBILE 회원수


	// PC - 최대값일 경우
	if( $app_max['in_join_ua_PC_plus'] == $arr_date_num['in_join_ua_PC_plus'][$i] && $app_max['in_join_ua_PC_plus'] > 0) {
		$arr_date_color['in_join_ua_PC_plus'][$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
		$arr_date_border['in_join_ua_PC_plus'][$i] = "rgba(255,99,132,1)"; // 그래프 border 색
	}
	// PC - 일반 데이터 일 경우
	else {
		$arr_date_color['in_join_ua_PC_plus'][$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
		$arr_date_border['in_join_ua_PC_plus'][$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
	}

	// MOBILE - 최대값일 경우
	if( $app_max['in_join_ua_MOBILE_plus'] == $arr_date_num['in_join_ua_MOBILE_plus'][$i] && $app_max['in_join_ua_MOBILE_plus'] > 0) {
		$arr_date_color['in_join_ua_MOBILE_plus'][$i] = "rgba(0, 128, 0, 0.2)"; // 그래프 색
		$arr_date_border['in_join_ua_MOBILE_plus'][$i] = "rgba(0, 128, 0,1)"; // 그래프 border 색
	}
	// MOBILE - 일반 데이터 일 경우
	else {
		$arr_date_color['in_join_ua_MOBILE_plus'][$i] = "rgba(128, 0, 255, 0.2)"; // 그래프 색
		$arr_date_border['in_join_ua_MOBILE_plus'][$i] = "rgba(128, 0, 255, 1)"; // 그래프 border 색
	}


	// PC - 최대값 체크
	$arr_maxmin['in_join_ua_PC_plus']['max'] = ( $arr_maxmin['in_join_ua_PC_plus']['max']['cnt'] < $arr_date_num['in_join_ua_PC_plus'][$i] ? array('cnt'=>$arr_date_num['in_join_ua_PC_plus'][$i] , 'date'=>sprintf("%04d" , $i)) : $arr_maxmin['in_join_ua_PC_plus']['max']);

	// MOBILE - 최대값 체크
	$arr_maxmin['in_join_ua_MOBILE_plus']['max'] = ( $arr_maxmin['in_join_ua_MOBILE_plus']['max']['cnt'] < $arr_date_num['in_join_ua_MOBILE_plus'][$i] ? array('cnt'=>$arr_date_num['in_join_ua_MOBILE_plus'][$i] , 'date'=>sprintf("%04d" , $i)) : $arr_maxmin['in_join_ua_MOBILE_plus']['max']);


	// PC - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['in_join_ua_PC_plus']['min']['cnt'])) {
		$arr_maxmin['in_join_ua_PC_plus']['min'] = array('cnt'=>$arr_date_num['in_join_ua_PC_plus'][$i] , 'date'=>sprintf("%04d" , $i));
		$arr_avg['in_join_ua_PC_plus']['idx'] ++; // 계산할 횟수
		$arr_avg['in_join_ua_PC_plus']['cnt'] += $arr_date_num['in_join_ua_PC_plus'][$i]; // 계산할 접속수
	}
	// PC - 최소 정보일 경우 현시간 제외
	else if(date("Y") > sprintf("%04d" , $i)){
		$arr_maxmin['in_join_ua_PC_plus']['min'] = ( $arr_maxmin['in_join_ua_PC_plus']['min']['cnt'] > $arr_date_num['in_join_ua_PC_plus'][$i] ? array('cnt'=>$arr_date_num['in_join_ua_PC_plus'][$i] , 'date'=>sprintf("%04d" , $i)) : $arr_maxmin['in_join_ua_PC_plus']['min']);
		$arr_avg['in_join_ua_PC_plus']['idx'] ++; // 계산할 횟수
		$arr_avg['in_join_ua_PC_plus']['cnt'] += $arr_date_num['in_join_ua_PC_plus'][$i]; // 계산할 접속수
	}

	// MOBILE - 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_maxmin['in_join_ua_MOBILE_plus']['min']['cnt'])) {
		$arr_maxmin['in_join_ua_MOBILE_plus']['min'] = array('cnt'=>$arr_date_num['in_join_ua_MOBILE_plus'][$i] , 'date'=>sprintf("%04d" , $i));
		$arr_avg['in_join_ua_MOBILE_plus']['idx'] ++; // 계산할 횟수
		$arr_avg['in_join_ua_MOBILE_plus']['cnt'] += $arr_date_num['in_join_ua_MOBILE_plus'][$i]; // 계산할 접속수
	}
	// MOBILE - 최소 정보일 경우 현시간 제외
	else if(date("Y") > sprintf("%04d" , $i)){
		$arr_maxmin['in_join_ua_MOBILE_plus']['min'] = ( $arr_maxmin['in_join_ua_MOBILE_plus']['min']['cnt'] > $arr_date_num['in_join_ua_MOBILE_plus'][$i] ? array('cnt'=>$arr_date_num['in_join_ua_MOBILE_plus'][$i] , 'date'=>sprintf("%04d" , $i)) : $arr_maxmin['in_join_ua_MOBILE_plus']['min']);
		$arr_avg['in_join_ua_MOBILE_plus']['idx'] ++; // 계산할 횟수
		$arr_avg['in_join_ua_MOBILE_plus']['cnt'] += $arr_date_num['in_join_ua_MOBILE_plus'][$i]; // 계산할 접속수
	}
	// ------------------------ 가입기기별 ------------------------



	// ------------------------ 기간내 가입 회원 수 평균 ------------------------
	$arr_tot_date_num[$i] = $arr_tot_data[sprintf("%04d" , $i)] * 1;// 선택월 회원수
	$arr_tot_date_date[$i] = $i . "년";// 접속자년

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
	$arr_tot_maxmin['max'] = ( $arr_tot_maxmin['max']['cnt'] < $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%04d" , $i)) : $arr_tot_maxmin['max']);

	// 등록되어 있지 않은 경우 무조건 등록
	if(!isset($arr_tot_maxmin['min']['cnt'])) {
		$arr_tot_maxmin['min'] = array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%04d" , $i));
		$arr_max_avg['idx'] ++; // 계산할 횟수
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
	}
	else {
		$arr_tot_maxmin['min'] = ( $arr_tot_maxmin['min']['cnt'] > $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=>sprintf("%04d" , $i)) : $arr_tot_maxmin['min']);
		$arr_max_avg['idx'] ++; // 계산할 횟수
		$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
	}
	// ------------------------ 기간내 가입 회원 수 평균 ------------------------


}

?>

<div class="group_title type_search">
	<strong>최근 10년간</strong>
</div>


<div class="group_title type_first"><strong>접속기기</strong></div>
<div class="data_graph">
	<ul>
		<li><canvas id="js_couter_chart1" height="330"></canvas></li>
	</ul>
</div>



<div class="data_graph_wrap">
	<div class="data_graph">
		<div class="group_title"><strong>가입방법</strong></div>
		<ul>
			<li>
				<?php if(count($mem_date)>0){?>
					<canvas id="js_couter_chart2" height="330"></canvas>
				<?php }else{?>
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
				<?php }?>
			</li>
		</ul>
	</div>


	<div class="data_graph">
		<div class="group_title"><strong>이메일/문자 수신여부</strong></div>
		<ul>
			<li>
				<?php if(count($mem_date)>0){?>
					<canvas id="js_couter_chart3" height="330"></canvas>
				<?php }else{?>
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">아직 데이터가 없습니다.</div></div>
				<?php }?>
			</li>
		</ul>
	</div>
</div>




<div class="data_graph_wrap">
	<div class="data_graph">
		<table class="table_must">
			<thead>
				<tr>
					<th>PC</th>
					<th>MOBILE</th>
					<th>합계</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="t_right"><?php echo number_format($mem_sum['in_join_ua_PC_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['in_join_ua_MOBILE_plus']); ?>명</td>
					<td class="t_blue t_right t_bold"><?php echo number_format($mem_sum['total_plus']); ?>명</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="data_graph">
		<table class="table_must">
			<thead>
				<tr>
					<th>일반</th>
					<th>페이스북</th>
					<th>카카오</th>
					<th>네이버</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="t_right"><?php echo number_format($mem_sum['sns_join_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['fb_join_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['ko_join_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['nv_join_plus']); ?>명</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="data_graph">
		<table class="table_must">
			<thead>
				<tr>
					<th>이메일 허용</th>
					<th>이메일 거부</th>
					<th>문자 허용</th>
					<th>문자 거부</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="t_right"><?php echo number_format($mem_sum['in_emailsendY_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['in_emailsendN_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['in_smssendY_plus']); ?>명</td>
					<td class="t_right"><?php echo number_format($mem_sum['in_smssendN_plus']); ?>명</td>
				</tr>
			</tbody>
		</table>
	</div>
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
</form>


<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('method_year_search');
		$('form[name="frmSearch"]').attr('action', '_static_mem.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---



	// ---------- 기본 line-bar 그래프 (가입자수, 가입기기) ----------
	var background = 'rgba(255,99,132,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// 가입기기 - PC 수
		{
			type: 'bar',
			label: '전체 가입기기 - PC 수',
			data: [<?=implode(' , ' , array_values($arr_date_num['in_join_ua_PC_plus']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['in_join_ua_PC_plus']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['in_join_ua_PC_plus']))?>"],
			borderWidth: 1
		},
		// 가입기기 - MOBILE 수
		{
			type: 'bar',
			label: '전체 가입기기 - MOBILE 수',
			data: [<?=implode(' , ' , array_values($arr_date_num['in_join_ua_MOBILE_plus']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['in_join_ua_MOBILE_plus']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['in_join_ua_MOBILE_plus']))?>"],
			borderWidth: 1
		},
		// 가입 회원수
		{
			type: 'line',
			label: '전체 가입 회원수',
			data: [<?=implode(' , ' , array_values($arr_tot_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 (가입자수, 가입기기) ----------




	// ---------- 파이 - 그래프 ( 회원구분 ) ----------
	var config2 = {
		type: 'pie',
		data: {
            datasets: [{
                data: [<?=$mem_sum['sns_join_plus']?>, <?=$mem_sum['fb_join_plus']?>, <?=$mem_sum['ko_join_plus']?>, <?=$mem_sum['nv_join_plus']?>],
                backgroundColor: ["#FFAAAA", "#74AAFC", "#FDD828", "#07DA46"],
            }],
            labels: ["일반","페이스북","카카오톡","네이버"]
		},
		options: {
			responsive: true,
			legend: {position: 'top',},
			title: {display: true,text: '전체 회원구분'},
			animation: {
				animateRotate: false,
				animateScale: true
			}
		}
	};
	// ---------- 파이 - 그래프 ( 회원구분 ) ----------




	// ---------- 파이 - 그래프 ( 수신여부 ) ----------

    var config3 = {
        type: 'pie',
        data: {
            datasets: [{
                data: [<?=$mem_sum['in_emailsendY_plus']?>, <?=$mem_sum['in_emailsendN_plus']?>,0,0],
                backgroundColor: ["#FFAAAA","#6A6AFF","#55EAFF","#D0AAFF"],
            }, {
                data: [0,0,<?=$mem_sum['in_smssendY_plus']?>, <?=$mem_sum['in_smssendN_plus']?>],
                backgroundColor: ["#FFAAAA","#6A6AFF","#55EAFF","#D0AAFF"],
            }],
            labels: ["이메일수신","이메일거부","문자수신","문자거부"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			title: {display: true,text: '전체 수신여부'},
			animation: {
				animateRotate: false,
				animateScale: true
			}
		}
    };
	// ---------- 파이 - 그래프 ( 수신여부 ) ----------

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


		// 파이 - 그래프 -  회원구분
		var ctx2 = document.getElementById("js_couter_chart2").getContext("2d");
		window.myPie = new Chart(ctx2, config2);

		// 파이 - 그래프 - 수신여부
		var ctx3 = document.getElementById("js_couter_chart3").getContext("2d");
		window.myPie2 = new Chart(ctx3, config3);

	};
</script>


<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용
	$arr_class_data['total_plus'] =  array('grid_sky');

	$arr_table_data = array();

	foreach( $mem_date as $mem_datek => $mem_datev ){

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'className' =>array(
						'column' => $arr_class_data
					)
				),
				'rdate' => $mem_datev['rdate']=='0000'?'휴면전환':$mem_datev['rdate'],
				'total_plus' => number_format($mem_datev['total_plus']),

				// 회원구분
				'sns_join_plus' => number_format($mem_datev['sns_join_plus']),
				'fb_join_plus' => number_format($mem_datev['fb_join_plus']),
				'ko_join_plus' => number_format($mem_datev['ko_join_plus']),
				'nv_join_plus' => number_format($mem_datev['nv_join_plus']),

				// 가입기기
				'in_join_ua_PC_plus' => number_format($mem_datev['in_join_ua_PC_plus']),
				'in_join_ua_MOBILE_plus' => number_format($mem_datev['in_join_ua_MOBILE_plus']),

				// 수신여부
				'in_emailsendY_plus' => number_format($mem_datev['in_emailsendY_plus']),
				'in_emailsendN_plus' => number_format($mem_datev['in_emailsendN_plus']),
				'in_smssendY_plus' => number_format($mem_datev['in_smssendY_plus']),
				'in_smssendN_plus' => number_format($mem_datev['in_smssendN_plus']),

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
        headerHeight: 99,
		rowHeight  : 35,
        displayRowCount: 8,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [
			{"title" : "<b>회원구분</b>", "columnName" : "mem", "columnNameList" : ["sns_join_plus", "fb_join_plus", "ko_join_plus", "nv_join_plus"] },
			{"title" : "<b>가입기기</b>", "columnName" : "device", "columnNameList" : ["in_join_ua_PC_plus", "in_join_ua_MOBILE_plus"] },
			{"title" : "<b>수신여부</b>", "columnName" : "send", "columnNameList" : ["in_emailsendY_plus", "in_emailsendN_plus", "in_smssendY_plus", "in_smssendN_plus"] }
        ],

        columnModelList: [
            {"title" : "<b>년월</b>", "columnName" : "rdate", "align" : "center", "width" : 60 },
			{"title" : "<b>가입회원수</b>", "columnName" : "total_plus", "align" : "right", "width" : 80 },

			// 회원구분
			{"title" : "일반", "columnName" : "sns_join_plus", "align" : "right", "width" : 70 },
			{"title" : "페이스북", "columnName" : "fb_join_plus", "align" : "right", "width" : 70 },
			{"title" : "카카오톡", "columnName" : "ko_join_plus", "align" : "right", "width" : 70 },
			{"title" : "네이버", "columnName" : "nv_join_plus", "align" : "right", "width" : 70 },

			// 가입기기
			{"title" : "PC", "columnName" : "in_join_ua_PC_plus", "align" : "right", "width" : 70 },
			{"title" : "MOBILE", "columnName" : "in_join_ua_MOBILE_plus", "align" : "right", "width" : 70 },

			// 수신여부
			{"title" : "이메일허용", "columnName" : "in_emailsendY_plus", "align" : "right", "width" : 70 },
			{"title" : "이메일거부", "columnName" : "in_emailsendN_plus", "align" : "right", "width" : 70 },
			{"title" : "문자허용", "columnName" : "in_smssendY_plus", "align" : "right", "width" : 70 },
			{"title" : "문자거부", "columnName" : "in_smssendN_plus", "align" : "right", "width" : 70 }

        ]
    });

	var table_data = <?php echo json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>