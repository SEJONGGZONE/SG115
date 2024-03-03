<?php

	// 관리자 - 메인 > 쇼핑몰 주요 현황 > 방문자 부분

	define('_OD_DIRECT_', true); // 개별 실행방지


?>

<div class="inner_box table_box">
	<div class="log_table">
		<dl class="thead">
			<dt>날짜</dt>
			<dd>총 방문자</dd>
			<dd>PC</dd>
			<dd>모바일</dd>
		</dl>
		<?php
			// ------- 주문 - 날짜별 목록 -------
			$arr_data = $arr_cumul = array();

			$que = "
				SELECT
					DATE(sc_date) as rdate,
					COUNT(*) as sum_total_cnt,
					SUM(IF( sc_mobile  = 'Y' , 1 , 0 )) as sum_mobileY_cnt,
					SUM(IF( sc_mobile  = 'N' , 1 , 0 )) as sum_mobileN_cnt
				FROM smart_cntlog_list
				WHERE
					DATE_ADD(DATE(sc_date) , INTERVAL + 7 DAY) > CURDATE()
				GROUP BY rdate
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_data[$v['rdate']][$sk] = $sv;
				}
				$arr_cumul['sum_total_cnt'] += $v['sum_total_cnt'];//총 방문자수
				$arr_cumul['sum_mobileY_cnt'] += $v['sum_mobileY_cnt'];//PC
				$arr_cumul['sum_mobileN_cnt'] += $v['sum_mobileN_cnt'];//모바일
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
						<dd>'. number_format($v['sum_total_cnt']) .'</dd>
						<dd>'. number_format($v['sum_mobileN_cnt']) .'</dd>
						<dd>'. number_format($v['sum_mobileY_cnt']) .'</dd>
					</dl>
				'; // 2019-07-08 SSJ :: PC와 모바일의 순서가 잘못되있어서 순서변경, 별도 패치 없음

				// ------------------------ 그래프 적용 데이터 ------------------------
				$arr_tot_date_num[$i] = $v['sum_total_cnt']*1;// 방문자수
				$arr_tot_date_date[$i] = DATE("m/d" , strtotime($rdate));// 저장일자
				$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
				$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
				// ------------------------ 그래프 적용 데이터 ------------------------

			}

			echo '
				<dl class="total week">
					<dt>일주일</dt>
					<dd>' . number_format($arr_cumul['sum_total_cnt']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_mobileN_cnt']) . '</dd>
					<dd>' . number_format($arr_cumul['sum_mobileY_cnt']) . '</dd>
				</dl>
			'; // 2019-07-08 SSJ :: PC와 모바일의 순서가 잘못되있어서 순서변경, 별도 패치 없음


			// 1개월 합계
			$que = "
				SELECT
					DATE(sc_date) as rdate,
					COUNT(*) as sum_total_cnt,
					SUM(IF( sc_mobile  = 'Y' , 1 , 0 )) as sum_mobileY_cnt,
					SUM(IF( sc_mobile  = 'N' , 1 , 0 )) as sum_mobileN_cnt
				FROM smart_cntlog_list
				WHERE
					DATE_ADD(DATE(sc_date) , INTERVAL + 1 MONTH) >= CURDATE()
			";
			$row_month = _MQ($que);
			echo '
				<dl class="total month">
					<dt>한달</dt>
					<dd>' . number_format($row_month['sum_total_cnt']) . '</dd>
					<dd>' . number_format($row_month['sum_mobileN_cnt']) . '</dd>
					<dd>' . number_format($row_month['sum_mobileY_cnt']) . '</dd>
				</dl>
			'; // 2019-07-08 SSJ :: PC와 모바일의 순서가 잘못되있어서 순서변경, 별도 패치 없음

		?>
	</div>
	<div class="go_btn"><a href="_cntlog.php" class="more_btn">전체 방문자분석 보기</a></div>
</div>