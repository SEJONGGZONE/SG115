<?php

	// 관리자 - 메인 > 쇼핑몰 주요 현황 > 주문 부분

	define('_OD_DIRECT_', true); // 개별 실행방지


?>
<div class="inner_box table_box">
	<div class="log_table">
		<dl class="thead">
			<dt>날짜</dt>
			<dd>구매건수</dd>
			<dd>구매수량</dd>
			<dd>구매금액</dd>
		</dl>
		<?php
			// ------- 주문 - 날짜별 목록 -------
			$arr_data = $arr_cumul = array();

			$que = "
					SELECT
						DATE(o.o_rdate) as rdate,
						COUNT(*) as sum_sale_cnt,
						SUM( op.op_cnt ) as sum_buy_cnt,
						SUM( op.op_price * op.op_cnt ) as sum_buy_price
					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					WHERE
						o.o_paystatus = 'Y' AND
						o.o_canceled!='Y' AND
						op.op_cancel = 'N'  AND
						DATE_ADD(DATE(o.o_rdate) , INTERVAL + 7 DAY) > CURDATE()
				GROUP BY rdate
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_data[$v['rdate']][$sk] = $sv;
				}
				$arr_cumul['sum_sale_cnt'] += $v['sum_sale_cnt'];//구매건수
				$arr_cumul['sum_buy_cnt'] += $v['sum_buy_cnt'];//구매수량
				$arr_cumul['sum_buy_price'] += $v['sum_buy_price'];//구매금액
			}

			// 그래프 적용 데이터
			$arr_tot_date_num = array(); // 전체
			$arr_tot_date_date = array(); // 날짜
			$arr_tot_date_color = array(); // 그래프 색
			$arr_tot_date_border = array(); // 그래프 border 색

			// 7일 지정
			for($i=0 ; $i<7 ; $i++){
				$rdate = DATE("Y-m-d" , strtotime(" - ". (6 - $i) ." DAY")); // 날짜 지정
				$v = $arr_data[$rdate]; // 날짜별 배열 지정

				$printRdate = DATE("m월 d일" , strtotime($rdate)).'('.$arr_day_week_short[date('w',strtotime($rdate))].')';

				echo '
					<dl class="'. ( $rdate == DATE("Y-m-d") ? 'today' : '') .'">
						<dt>'. $printRdate .'</dt>
						<dd>'. number_format($v['sum_sale_cnt']) .'</dd>
						<dd>'. number_format($v['sum_buy_cnt']) .'</dd>
						<dd>'. number_format($v['sum_buy_price']) .'</dd>
					</dl>
				';

				// ------------------------ 그래프 적용 데이터 ------------------------
				$arr_tot_date_num[$i] = $v['sum_sale_cnt']*1;// 구매건수
				$arr_tot_date_date[$i] = DATE("m/d" , strtotime($rdate));// 주문일자
				$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
				$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
				// ------------------------ 그래프 적용 데이터 ------------------------

			}

			echo '
				<dl class="total week">
					<dt>일주일</dt>
					<dd>' . number_format($arr_cumul['sum_sale_cnt']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_buy_cnt']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_buy_price']) . '</dd>
				</dl>
			';
			// 1개월 합계
			$que = "
					SELECT
						COUNT(*) as sum_sale_cnt,
						SUM( op.op_cnt ) as sum_buy_cnt,
						SUM( op.op_price * op.op_cnt ) as sum_buy_price
					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					WHERE
						o.o_paystatus = 'Y' AND
						o.o_canceled!='Y' AND
						op.op_cancel = 'N'  AND
						DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			";
			$row_month = _MQ($que);
			echo '
				<dl class="total month">
					<dt>한달</dt>
					<dd>' . number_format($row_month['sum_sale_cnt']) . '</dd>
					<dd>' . number_format($row_month['sum_buy_cnt']) . '</dd>
					<dd>' . number_format($row_month['sum_buy_price']) . '</dd>
				</dl>
			';

		?>
	</div>
	<div class="go_btn"><a href="_static_order.all.php" class="more_btn">전체 주문현황 보기</a></div>
</div>