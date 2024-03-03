<?php 
	$count=0;
	echo '<ul class="view_calendar_item">';
	foreach($calendar['dayInfo'] as $kkk=>$vvv){ 

		$this_year = $vvv['year'];
		$this_month = $vvv['month'];

		if( $kkk == 'prev'){ // 지난달
			$current_j = ($vvv['totalDay'] - $calendar['dayInfo']['now']['firstDay'])+1; // 이전달의 경우  요일코드를 통해 계산
			$totalDay = $vvv['totalDay']; // 

		}else if( $kkk == 'now' ){ // 현재
			$current_j = 1; // 선택된 달의 경우 시작일이 1일
			$totalDay = $vvv['totalDay'];
		}else if( $kkk == 'next' ){ // 다음달
			// 다음달의 경우도 1일
			$current_j = 1;
			$totalDay = (7-$vvv['firstDay']); // 
		}

		// 날짜를 테이블에 표시
		for($j = $current_j ;$j<=$totalDay;$j++){
			// 달력 td별 날짜 지정
			$li_cell_date = $this_year . "-" . sprintf("%02d" , $this_month) . "-" . sprintf("%02d" , $j);

			// 예약 불가요일 체크
			$chk_yoil_code = op_day_yoil_code($li_cell_date);
			if( in_array($chk_yoil_code,$arr_dateoption_ex_week) == true ) { $state = 'no';  }
			else{  $state = 'ok'; }

			// 시작일보다 작을경우
			if($state == 'ok' &&  $li_cell_date < $calendar['startDate']) { $state = 'no'; } // 

			// 예약 불가 날짜 체크
			if($state == 'ok' &&  in_array($li_cell_date,$arr_dateoption_ex_date) == true  ){ $state = 'no'; }

			// 종료일 추가 
			if($state == 'ok' && $calendar['endDate'] != '' && $li_cell_date > $calendar['endDate'] ) { $state = 'no'; }



			//  당일날짜 시간체크
			if ( $state == 'ok' && $li_cell_date == date('Y-m-d')  )
			{
				if( $p_info['p_dateoption_stime'] > 0 && strtotime($p_info['p_dateoption_stime']) > time() ) { $state='no'; }
				if( $p_info['p_dateoption_etime'] > 0 && strtotime($p_info['p_dateoption_etime']) < time() ) { $state='no'; }	
			}

			// 오늘이라면 
			$arr_app_li_class = array();
			if( $li_cell_date == date('Y-m-d')){$arr_app_li_class[] = 'today'; }


			// 날짜별 판별
			if($state == 'ok' ){
				$arr_app_li_class[] = 'ok';
				$dayDateBox = array('data-date="'.$li_cell_date.'"', 'data-wcode="'.$chk_yoil_code.'"');
				$app_td_html = '<a href="#none" class="day" onclick="calendar.selectDate(\''.$li_cell_date.'\'); return false;" '.implode(" ",$dayDateBox).'><strong>'.$j.'</strong></a>';

				// 기존 선택날짜가 있다면
				if($selDate == $li_cell_date){ $arr_app_li_class[] ="hit"; }
			}else{
				$arr_app_li_class[] = 'no';
				$app_td_html = "<a class='day'><strong>".  $j  ."</strong></a>";
			}

			// 이전이라면
			if( $kkk == 'prev' || $kkk == 'next' ){
				$app_td_html = '';
			}

			// 요일별 클래스 추가 
			if( $chk_yoil_code == 1){ $arr_app_li_class[] = " sun";  } // 일요일 
			if( $chk_yoil_code == 7){ $arr_app_li_class[] = " sat";  } // 토요일


			// 줄바꿈
			echo ( $count%7 ==0 && $count <> 0 ? '</ul><ul class="view_calendar_item">' : "" );
			// 일자표시
			echo "<li class='". implode(" ",$arr_app_li_class) ."' data-date='".$li_cell_date."' data-update='".$this_year."-".$this_month."-01' data-state=''>". $app_td_html ."</li>";
			$count++;
		}
	}
	echo '</ul>';
?>	