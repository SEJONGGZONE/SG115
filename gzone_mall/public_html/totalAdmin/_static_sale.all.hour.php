<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 매출분석 - 일별


	// 일자계산 - 시작일자 정의
	$pass_date = ($pass_date?$pass_date:date('Y-m-d', strtotime("-1 week")));
	$Select_Year = date('Y', strtotime($pass_date));
	$Select_Month = date('m', strtotime($pass_date));
	$Select_Day = date('d', strtotime($pass_date));

	// 일자계산 - 종료일자 정의
	$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));
	$Select_eYear = date('Y', strtotime($pass_edate));
	$Select_eMonth = date('m', strtotime($pass_edate));
	$Select_eDay = date('d', strtotime($pass_edate));


	// JJC : 주문 취소항목 추출 : 2018-01-04
	$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));


	// ---- 요약 ----
	$arr_sum = array();
	$que = "

			SELECT

				SUM( IF( mobile = 'Y' , o_price_real , 0 ) ) as sum_mobileY_total_price,
				SUM( IF( mobile != 'Y' , o_price_real , 0 ) ) as sum_mobileN_total_price,

				SUM( IF( o_memtype = 'N' , o_price_real , 0 ) ) as sum_memtypeN_total_price,
				SUM( IF( o_memtype != 'N' , o_price_real , 0  )) as sum_memtypeY_total_price,

				SUM( IF( mobile = 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_mobileY_cancel_price,
				SUM( IF( mobile != 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_mobileN_cancel_price,

				SUM( IF( o_memtype = 'N' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_memtypeN_cancel_price,
				SUM( IF( o_memtype != 'N' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0  )) as sum_memtypeY_cancel_price

			FROM smart_order

			WHERE
				o_paystatus = 'Y' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

	";
	$res = _MQ($que);
	foreach( $res as $k=>$v ){
		$arr_sum[$k] = $v;
	}
	// ---- 요약 ----



	// ------- 매출 - 시간대별 목록 -------
	$arr_data = $arr_res = $arr_max = $arr_tot_cumul = array();

	// JJC : 주문 할인항목 추출 : 2018-01-04
	$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

	// JJC : 주문 취소항목 추출 : 2018-01-04
	$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

	$que = "

			SELECT

				HOUR(o_rdate) as rdate ,

				'------------ PC 부분 ------------ ' as dummy_pc ,
				SUM( IF( mobile != 'Y' , IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) , 0 ) ) as mobileN_sum_price_total,
				SUM( IF( mobile != 'Y' , o_price_delivery , 0 ) ) as mobileN_sum_price_delivery,
				SUM( IF( mobile != 'Y' , ". $add_que_discount ." , 0 ) ) as mobileN_sum_discount,
				SUM( IF( mobile != 'Y' , o_price_real , 0 ) ) as mobileN_sum_price_real,
				SUM( IF( mobile != 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as mobileN_sum_price_refund,
				SUM( IF( mobile != 'Y' , o_price_supplypoint , 0 ) ) as mobileN_sum_price_supplypoint,

				'------------ MOBILE 부분 ------------ ' as dummy_mobile ,
				SUM( IF( mobile = 'Y' , IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) , 0 ) ) as mobileY_sum_price_total,
				SUM( IF( mobile = 'Y' , o_price_delivery , 0 ) ) as mobileY_sum_price_delivery,
				SUM( IF( mobile = 'Y' , ". $add_que_discount ." , 0 ) ) as mobileY_sum_discount,
				SUM( IF( mobile = 'Y' , o_price_real , 0 ) ) as mobileY_sum_price_real,
				SUM( IF( mobile = 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as mobileY_sum_price_refund,
				SUM( IF( mobile = 'Y' , o_price_supplypoint , 0 ) ) as mobileY_sum_price_supplypoint


			FROM smart_order

			WHERE
				o_paystatus = 'Y' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

		GROUP BY rdate
		ORDER BY rdate ASC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		foreach( $v as $sk=>$sv ){
			$arr_res[$v['rdate']][$sk] = $sv;
		}

		$arr_data['mobileY'][$v['rdate']] = $v['mobileY_sum_price_real'];
		$app_max['mobileY'] = ($app_max['mobileY'] < $v['mobileY_sum_price_real'] ? $v['mobileY_sum_price_real'] : $app_max['mobileY']);

		$arr_data['mobileN'][$v['rdate']] = $v['mobileN_sum_price_real'];
		$app_max['mobileN'] = ($app_max['mobileN'] < $v['mobileN_sum_price_real'] ? $v['mobileN_sum_price_real'] : $app_max['mobileN']);

		$arr_tot_cumul[$v['rdate']] = array(
			'idx' => $arr_tot_cumul[$v['rdate']]['idx'] + 1 , // 횟수
			'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['mobileY_sum_price_real'] + $v['mobileN_sum_price_real'] // 누적수
		);

	}
	// ------- 매출 - 시간대별 목록 -------

	// ------- 기간내 매출 -------
	$arr_tot_data = array();
	$app_tot_max = 0; // 기간내 매출 평균 최대치
	$arr_tot_maxmin = array(); // 기간내 매출 평균 최대 최소 방문정보 배열 기록
	foreach($arr_tot_cumul as $k=>$v){
		$v['idx'] = $v['idx'] > 0 ? $v['idx'] : 1; // 횟수
		$avg_cnt = round($v['cnt'] / $v['idx']);
		$arr_tot_data[$k] = $avg_cnt;
		$app_tot_max = ($app_tot_max < $avg_cnt ? $avg_cnt : $app_tot_max);
	}
	// ------- 기간내 가입 매출 -------


	# Chart 그래프 적용
	$arr_date_num = array(); // 시간대별 매출
	$arr_date_date = array(); // 시간대
	$arr_date_color = array(); // 그래프 색
	$arr_date_border = array(); // 그래프 border 색

	$arr_tot_date_num = array(); // 기간내 전체 매출
	$arr_tot_date_date = array(); // 기간내 전체 매출 날짜
	$arr_tot_date_color = array(); // 기간내 전체 매출 그래프 색
	$arr_tot_date_border = array(); // 기간내 전체 매출 그래프 border 색

	$arr_avg = $arr_max_avg = array();
	for($i=0 ; $i<=23 ; $i++){

		// ------------------------ 가입기기별 ------------------------
		$arr_date_date[$i] = $i . "시";// 시간

		$arr_date_num['mobileN'][$i] = $arr_data['mobileN'][$i] * 1;// 선택일자 PC 매출
		$arr_date_num['mobileY'][$i] = $arr_data['mobileY'][$i] * 1;// 선택일자 모바일 매출

		// PC - 최대값일 경우
		if( $app_max['mobileN'] == $arr_date_num['mobileN'][$i] && $app_max['mobileN'] > 0) {
			$arr_date_color['mobileN'][$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
			$arr_date_border['mobileN'][$i] = "rgba(255,99,132,1)"; // 그래프 border 색
		}
		// PC - 일반 데이터 일 경우
		else {
			$arr_date_color['mobileN'][$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
			$arr_date_border['mobileN'][$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
		}

		// MOBILE - 최대값일 경우
		if( $app_max['mobileY'] == $arr_date_num['mobileY'][$i] && $app_max['mobileY'] > 0) {
			$arr_date_color['mobileY'][$i] = "rgba(0, 128, 0, 0.2)"; // 그래프 색
			$arr_date_border['mobileY'][$i] = "rgba(0, 128, 0,1)"; // 그래프 border 색
		}
		// MOBILE - 일반 데이터 일 경우
		else {
			$arr_date_color['mobileY'][$i] = "rgba(128, 0, 255, 0.2)"; // 그래프 색
			$arr_date_border['mobileY'][$i] = "rgba(128, 0, 255, 1)"; // 그래프 border 색
		}


		// PC - 최대값 체크
		$arr_maxmin['mobileN']['max'] = ( $arr_maxmin['mobileN']['max']['cnt'] < $arr_date_num['mobileN'][$i] ? array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i ) : $arr_maxmin['mobileN']['max']);

		// MOBILE - 최대값 체크
		$arr_maxmin['mobileY']['max'] = ( $arr_maxmin['mobileY']['max']['cnt'] < $arr_date_num['mobileY'][$i] ? array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i ) : $arr_maxmin['mobileY']['max']);



		// PC - 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_maxmin['mobileN']['min']['cnt'])) {
			$arr_maxmin['mobileN']['min'] = array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i );
			$arr_avg['mobileN']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileN']['cnt'] += $arr_date_num['mobileN'][$i]; // 계산할 접속수
		}
		// PC - 최소 정보일 경우 현시간 제외
		else if(date("Ymd") > $i){
			$arr_maxmin['mobileN']['min'] = ( $arr_maxmin['mobileN']['min']['cnt'] > $arr_date_num['mobileN'][$i] ? array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i ) : $arr_maxmin['mobileN']['min']);
			$arr_avg['mobileN']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileN']['cnt'] += $arr_date_num['mobileN'][$i]; // 계산할 접속수
		}

		// MOBILE - 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_maxmin['mobileY']['min']['cnt'])) {
			$arr_maxmin['mobileY']['min'] = array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i );
			$arr_avg['mobileY']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileY']['cnt'] += $arr_date_num['mobileY'][$i]; // 계산할 접속수
		}
		// MOBILE - 최소 정보일 경우 현시간 제외
		else if(date("Ymd") > $i){
			$arr_maxmin['mobileY']['min'] = ( $arr_maxmin['mobileY']['min']['cnt'] > $arr_date_num['mobileY'][$i] ? array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i ) : $arr_maxmin['mobileY']['min']);
			$arr_avg['mobileY']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileY']['cnt'] += $arr_date_num['mobileY'][$i]; // 계산할 접속수
		}
		// ------------------------ 가입기기별 ------------------------


		// ------------------------ 기간내 매출 평균 ------------------------
		$arr_tot_date_num[$i] = $arr_tot_data[$i] * 1;// 선택일자 회원수
		$arr_tot_date_date[$i] = $i . "시";// 시간

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
		$arr_tot_maxmin['max'] = ( $arr_tot_maxmin['max']['cnt'] < $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i ) : $arr_tot_maxmin['max']);

		// 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_tot_maxmin['min']['cnt'])) {
			$arr_tot_maxmin['min'] = array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i );
			$arr_max_avg['idx'] ++; // 계산할 횟수
			$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
		}
		else {
			$arr_tot_maxmin['min'] = ( $arr_tot_maxmin['min']['cnt'] > $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i ) : $arr_tot_maxmin['min']);
			$arr_max_avg['idx'] ++; // 계산할 횟수
			$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
		}
		// ------------------------ 기간내 매출 평균 ------------------------


	}

?>

<div class="group_title type_search">
    <strong><?=DATE("Y년 m월 d일" , strtotime($pass_date) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?></strong>

    <!-- 기간검색 -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
        <input type="hidden" name="_type" value="<?php echo $_type; ?>">
        <input type="hidden" name="_mode" value="search">

        <div class="lineup-row type_date">
            <input type="text" name="pass_date" class="design js_pic_day_max_today" value="<?php echo $pass_date; ?>" style="width:90px" autocomplete="off" placeholder="날짜 선택" readonly>
            <span class="fr_tx">~</span>
            <input type="text" name="pass_edate" class="design js_pic_day_max_today" value="<?php echo $pass_edate; ?>" style="width:90px" autocomplete="off" placeholder="날짜 선택" readonly>

            <span class="c_btn h34 blue"><input type="submit" value="검색" accesskey="s"></span>
            <?php if($_mode == 'search'){ ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=<?=$_type?>" class="c_btn h34 black line normal" accesskey="l">초기화</a>
            <?php } ?>
        </div>
    </form><!-- end simple_search -->
</div>


<div class="data_graph">
	<ul>
		<li><canvas id="js_couter_chart1" height="330" ></canvas></li>
	</ul>
</div>



<div class="data_graph_wrap">
	<div class="data_graph">
		<table class="table_must">
			<colgroup><col width="100"/><col width="*"/><col width="*"/></colgroup>
			<thead>
				<tr>
					<th>회원별</th>
					<th>회원</th>
					<th>비회원</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 매출액</th>
					<td class='t_right t_blue'><?php echo number_format($arr_sum['sum_memtypeY_total_price']); ?>원</td>
					<td class='t_right t_blue'><?php echo number_format($arr_sum['sum_memtypeN_total_price']); ?>원</td>
				</tr>
				<tr>
					<th>취소/환불</th>
					<td class='t_right t_green'><?php echo number_format($arr_sum['sum_memtypeY_cancel_price']); ?>원</td>
					<td class='t_right t_green'><?php echo number_format($arr_sum['sum_memtypeN_cancel_price']); ?>원</td>
				</tr>
				<tr>
					<th>실 매출액</th>
					<td class='t_right t_red'><?php echo number_format($arr_sum['sum_memtypeY_total_price'] - $arr_sum['sum_memtypeY_cancel_price']); ?>원</td>
					<td class='t_right t_red'><?php echo number_format($arr_sum['sum_memtypeN_total_price'] - $arr_sum['sum_memtypeN_cancel_price']); ?>원</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="data_graph">
		<table class="table_must">
			<colgroup><col width="100"/><col width="*"/><col width="*"/></colgroup>
			<thead>
				<tr>
					<th>기기별</th>
					<th>PC</th>
					<th>모바일</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 매출액</th>
					<td class='t_right t_blue'><?php echo number_format($arr_sum['sum_mobileN_total_price']); ?>원</td>
					<td class='t_right t_blue'><?php echo number_format($arr_sum['sum_mobileY_total_price']); ?>원</td>
				</tr>
				<tr>
					<th>취소/환불</th>
					<td class='t_right t_green'><?php echo number_format($arr_sum['sum_mobileN_cancel_price']); ?>원</td>
					<td class='t_right t_green'><?php echo number_format($arr_sum['sum_mobileY_cancel_price']); ?>원</td>
				</tr>
				<tr>
					<th>실 매출액</th>
					<td class='t_right t_red'><?php echo number_format($arr_sum['sum_mobileN_total_price'] - $arr_sum['sum_mobileN_cancel_price']); ?>원</td>
					<td class='t_right t_red'><?php echo number_format($arr_sum['sum_mobileY_total_price'] - $arr_sum['sum_mobileY_cancel_price']); ?>원</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="data_graph">
		<table class="table_must">
			<colgroup><col width="100"/><col width="*"/></colgroup>
			<thead>
				<tr>
					<th>기간내</th>
					<th>총 매출</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 매출액</th>
					<td class='t_right t_blue t_bold'><?php echo number_format($arr_sum['sum_mobileY_total_price'] + $arr_sum['sum_mobileN_total_price']); ?>원</td>
				</tr>
				<tr>
					<th>취소/환불</th>
					<td class='t_right t_green t_bold'><?php echo number_format($arr_sum['sum_mobileY_cancel_price'] + $arr_sum['sum_mobileN_cancel_price']); ?>원</td>
				</tr>
				<tr>
					<th>실 매출액</th>
					<td class='t_right t_red t_bold'><?php echo number_format($arr_sum['sum_mobileY_total_price'] + $arr_sum['sum_mobileN_total_price'] - $arr_sum['sum_mobileY_cancel_price'] - $arr_sum['sum_mobileN_cancel_price']); ?>원</td>
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

	<div class="tip_box">
		<?=_DescStr("<strong>실 결제액 :</strong> 구매총액 + 배송비 - 할인액 ");?>
		<?=_DescStr("<strong>실 매출액 :</strong> 실결제액 - 취소/환불 ");?>
		<?=_DescStr("할인액은 적립금과 쿠폰, 프로모션 코드등 모든 할인 금액의 합계이고 취소/환불은 부분취소와 주문취소의 합계입니다.");?>
	</div>
</div>
<!-- / 도표 -->



<form name="frmSearch" method="post" action="_static_sale.pro.php" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="Select_Year" value="<?php echo $Select_Year; ?>">
	<input type="hidden" name="Select_Month" value="<?php echo $Select_Month; ?>">
	<input type="hidden" name="Select_Day" value="<?php echo $Select_Day; ?>">
	<input type="hidden" name="Select_eYear" value="<?php echo $Select_eYear; ?>">
	<input type="hidden" name="Select_eMonth" value="<?php echo $Select_eMonth; ?>">
	<input type="hidden" name="Select_eDay" value="<?php echo $Select_eDay; ?>">
</form>


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('all_hour_search');
		$('form[name="frmSearch"]').attr('action', '_static_sale.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>


<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// ---------- 기본 line-bar 그래프 ----------
	var background = 'rgba(255,99,132,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// PC 매출
		{
			type: 'bar',
			label: '시간별 PC 매출',
			data: [<?=implode(' , ' , array_values($arr_date_num['mobileN']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['mobileN']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['mobileN']))?>"],
			borderWidth: 1
		},
		// 모바일 매출
		{
			type: 'bar',
			label: '시간별 모바일 매출',
			data: [<?=implode(' , ' , array_values($arr_date_num['mobileY']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['mobileY']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['mobileY']))?>"],
			borderWidth: 1
		},
		// 전체매출
		{
			type: 'line',
			label: '시간별 매출',
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
		var myChart = new Chart(ctx, {type: 'bar',
			data: chartData,
			options: {scales: {yAxes: [{ticks: {beginAtZero:false}}]}}
		});
	};
</script>













<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용

	$arr_table_data = array();

	for($i=0 ; $i<=23 ; $i++){

		$app_date = $i . "시";
		$app_date_key = $i;

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'rowSpan' =>array('rdate' => 3),// 날짜 열별합 - 3개(rowspan=3)
					'className' =>array(
						'row' => array('grid')// 행에 디자인 클래스를 적용
					)
				),
				'rdate' => $app_date,
				'device' => '소계',

				'price_total' => number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- 소계 -----


		// ----- PC -----
		$arr_table_data[] = array(
				'device' => 'PC',

				'price_total' => number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- PC -----


		// ----- 모바일 -----
		$arr_table_data[] = array(
				'device' => '모바일',

				'price_total' => number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- 모바일 -----

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
        headerHeight: 40,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnModelList: [
            {"title" : "<b>시간</b>", "columnName" : "rdate", "align" : "center", "width" : 30 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 50 },
			{"title" : "구매총액", "columnName" : "price_total", "align" : "right", "width" : 120 },
			{"title" : "배송비", "columnName" : "price_delivery", "align" : "right", "width" : 120 },
			{"title" : "할인액", "columnName" : "discount", "align" : "right", "width" : 120 },
			{"title" : "실결제액", "columnName" : "price_real", "align" : "right", "width" : 120 },
			{"title" : "취소/환불", "columnName" : "price_refund", "align" : "right", "width" : 120 },
			{"title" : "실매출액", "columnName" : "sale_real", "align" : "right", "width" : 120 },
			{"title" : "적립금", "columnName" : "price_supplypoint", "align" : "right", "width" : 90 }
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>