<?php

	// 관리자 - 메인 > 쇼핑몰 주요 현황 > 매출 부분

	define('_OD_DIRECT_', true); // 개별 실행방지


?>
<div class="inner_box table_box">
	<div class="log_table">
		<dl class="thead">
			<dt>날짜</dt>
			<dd>구매총액</dd>
			<dd>실결제액</dd>
			<dd>취소/환불</dd>
		</dl>
		<?php
			// ------- 매출 - 날짜별 목록 -------
			$arr_data = $arr_cumul = array();

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$que = "
					SELECT
						DATE(o_rdate) as rdate,
						SUM( IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) ) as sum_price_total,
						SUM( o_price_real ) as sum_price_real,
						SUM( IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) ) as sum_price_refund
					FROM smart_order
					WHERE
						o_paystatus = 'Y' AND
						DATE_ADD(DATE(o_rdate) , INTERVAL + 7 DAY) > CURDATE()
				GROUP BY rdate
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_data[$v['rdate']][$sk] = $sv;
				}
				$arr_cumul['sum_price_total'] += $v['sum_price_total'];//구매총액
				$arr_cumul['sum_price_real'] += $v['sum_price_real'];//실결제액
				$arr_cumul['sum_price_refund'] += $v['sum_price_refund'];//취소/환불
			}


			// 그래프 적용 데이터
			$arr_tot_date_num = array(); // 전체 매출
			$arr_tot_date_date = array(); // 매출 날짜
			$arr_tot_date_color = array(); // 매출 그래프 색
			$arr_tot_date_border = array(); // 그래프 border 색

			// 7일 지정
			for($i=0 ; $i<7 ; $i++){
				$rdate = DATE("Y-m-d" , strtotime(" - ". (6 - $i) ." DAY")); // 날짜 지정
				$v = $arr_data[$rdate]; // 날짜별 배열 지정

				$printRdate = DATE("m월 d일" , strtotime($rdate)).'('.$arr_day_week_short[date('w',strtotime($rdate))].')';

				echo '
					<dl class="'. ( $rdate == DATE("Y-m-d") ? 'today' : '') .'">
						<dt>'. $printRdate .'</dt>
						<dd>'. number_format($v['sum_price_total']) .'</dd>
						<dd>'. number_format($v['sum_price_real']) .'</dd>
						<dd>'. number_format($v['sum_price_refund']) .'</dd>
					</dl>
				';

				// ------------------------ 그래프 적용 데이터 ------------------------
				$arr_tot_date_num[$i] = $v['sum_price_real']*1;// 실결제액
				$arr_tot_date_date[$i] = DATE("m/d" , strtotime($rdate));// 주문일자
				$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
				$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
				// ------------------------ 그래프 적용 데이터 ------------------------

			}

			echo '
				<dl class="total week">
					<dt>일주일</dt>
					<dd>' . number_format($arr_cumul['sum_price_total']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_price_real']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_price_refund']) . '</dd>
				</dl>
			';

			// 1개월 합계
			$que = "
					SELECT
						SUM( IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) ) as sum_price_total,
						SUM( o_price_real ) as sum_price_real,
						SUM( IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) ) as sum_price_refund
					FROM smart_order
					WHERE
						o_paystatus = 'Y' AND
						DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			";
			$row_month = _MQ($que);
			echo '
				<dl class="total month">
					<dt>한달</dt>
					<dd>' . number_format($row_month['sum_price_total']) . '</dd>
					<dd>' . number_format($row_month['sum_price_real']) . '</dd>
					<dd>' . number_format($row_month['sum_price_refund']) . '</dd>
				</dl>
			';

		?>
	</div>
	<div class="go_btn"><a href="_static_sale.all.php" class="more_btn">전체 매출현황 보기</a></div>
</div>