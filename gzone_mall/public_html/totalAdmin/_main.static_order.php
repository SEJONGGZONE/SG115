<?
	// 관리자 메인 페이지 적용용
	// ------------- 주문/배송 현황 -------------
?>

	<?php

		// 배열 설정
		$admin_main_order_type = array('today' => '오늘' , 'week' => '일주일' , 'month' => '한달');

		$admin_main_order_status_import = array('결제완료','배송완료','발급완료','주문취소');		// 상단에 노출되는 4개
		$admin_main_order_status = array('결제대기' , '배송준비' , '배송중', '부분취소요청' , '부분취소완료' , '반품/교환요청','반품/교환완료' , '환불요청','환불완료');	// 4개 아래에 노출되는 상태값

		// ----- 주문 상태값에 따른 건수 정보 추출 - 오늘/1주일/1개월  -----
		$order_status = array();
		
		// 데이터 순서
		// 결제완료 , 배송완료, 발급완료, 주문취소 ,  결제대기, 배송준비, 배송중
		$r = _MQ_assoc("
			select
				DATE(o_rdate) as rdate,
				sum(IF( (o_status = '결제완료' or o_status = '배송대기') and o_paystatus ='Y' and o_canceled='N'  , 1 , 0 )) as cnt_step01 ,
				sum(IF( o_status = '배송완료' and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step02 ,
				sum(IF( o_status = '발급완료' and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step03 ,
				sum(IF( o_canceled='Y' , 1 , 0 )) as cnt_step04,
				sum(IF( o_status = '결제대기' and o_paymethod IN ('online' , 'virtual') and o_paystatus ='N' and o_canceled='N' , 1 , 0 )) as cnt_step05 ,
				sum(IF( (o_status = '배송준비') and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step06 ,
				sum(IF( (o_status = '배송중') and o_paystatus ='Y' and o_canceled='N' , 1 , 0 )) as cnt_step07
			from smart_order
			WHERE 
				DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
				and npay_order='N'
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['결제완료'] = $v['cnt_step01'];
				$order_status['today']['배송완료'] = $v['cnt_step02'];
				$order_status['today']['발급완료'] = $v['cnt_step03'];
				$order_status['today']['주문취소'] = $v['cnt_step04'];
				$order_status['today']['결제대기'] = $v['cnt_step05'];
				$order_status['today']['배송준비'] = $v['cnt_step06']; 
				$order_status['today']['배송중'] = $v['cnt_step07']; 
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['결제완료'] += $v['cnt_step01'];
				$order_status['week']['배송완료'] += $v['cnt_step02'];
				$order_status['week']['발급완료'] += $v['cnt_step03'];
				$order_status['week']['주문취소'] += $v['cnt_step04'];
				$order_status['week']['결제대기'] += $v['cnt_step05'];
				$order_status['week']['배송준비'] += $v['cnt_step06'];
				$order_status['week']['배송중'] += $v['cnt_step07'];
			}
			// 1개월 정보 추출
			$order_status['month']['결제완료'] += $v['cnt_step01'];
			$order_status['month']['배송완료'] += $v['cnt_step02'];
			$order_status['month']['발급완료'] += $v['cnt_step03'];
			$order_status['month']['주문취소'] += $v['cnt_step04'];
			$order_status['month']['결제대기'] += $v['cnt_step05'];
			$order_status['month']['배송준비'] += $v['cnt_step06']; 
			$order_status['month']['배송중'] += $v['cnt_step07']; 
		}
		// ----- 주문 상태값에 따른 건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 부분취소 완료건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(op_cancel_rdate) as rdate,
				COUNT(*)  as cnt
			from smart_order_product
			WHERE
				op_cancel = 'Y' and
				DATE_ADD(DATE(op_cancel_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['부분취소완료'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['부분취소완료'] += $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['부분취소완료'] += $v['cnt'];
		}
		// ----- 부분취소 완료건수 정보 추출 - 오늘/1주일/1개월  -----


		// ----- 부분취소 요청건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(op_cancel_rdate) as rdate,
				COUNT(*)  as cnt
			from smart_order_product
			WHERE
				op_cancel = 'R' and
				op_is_addoption='N' and
				DATE_ADD(DATE(op_cancel_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['부분취소요청'] = $v['cnt'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['부분취소요청'] += $v['cnt'];
			}
			// 1개월 정보 추출
			$order_status['month']['부분취소요청'] += $v['cnt'];
		}
		// ----- 부분취소 요청건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 반품/교환요청 건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(op_complain_date) as rdate,
				COUNT(*)  as cnt,
				sum(IF( (op_complain = '교환/반품신청') , 1 , 0 )) as cnt_complain_wait ,
				sum(IF( (op_complain = '교환/반품신청 완료') , 1 , 0 )) as cnt_complain_end
			from smart_order_product as op
			inner join smart_order as o on (o.o_ordernum=op.op_oordernum)
			WHERE
				o.o_canceled!='Y' and
				op.op_complain!='' and
				DATE_ADD(DATE(op_complain_date) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {

			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['반품/교환요청'] = $v['cnt_complain_wait'];
				$order_status['today']['반품/교환완료'] = $v['cnt_complain_end'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['반품/교환요청'] += $v['cnt_complain_wait'];
				$order_status['week']['반품/교환완료'] += $v['cnt_complain_end'];
			}
			// 1개월 정보 추출
			$order_status['month']['반품/교환요청'] += $v['cnt_complain_wait'];
			$order_status['month']['반품/교환완료'] += $v['cnt_complain_end'];
		}
		// ----- 반품/교환요청 건수 정보 추출 - 오늘/1주일/1개월  -----



		// ----- 환불요청 건수 정보 추출 - 오늘/1주일/1개월  -----
		$r = _MQ_assoc("
			select
				DATE(o_rdate) as rdate,
				sum(IF( (o_moneyback_status = 'request') , 1 , 0 )) as cnt_monyback_request ,
				sum(IF( (o_moneyback_status = 'complete') , 1 , 0 )) as cnt_complain_complete
			from smart_order
			where
				DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
			GROUP BY rdate
			ORDER BY rdate DESC
		");
		foreach($r as $k => $v) {
			// 오늘 정보 추출
			if( $v['rdate'] == DATE("Y-m-d") ) {
				$order_status['today']['환불요청'] = $v['cnt_monyback_request'];
				$order_status['today']['환불완료'] = $v['cnt_complain_complete'];
			}
			// 1주일 정보 추출
			if( $v['rdate'] >= DATE("Y-m-d" , strtotime("-1 week")) ) {
				$order_status['week']['환불요청'] += $v['cnt_monyback_request'];
				$order_status['week']['환불완료'] += $v['cnt_complain_complete'];
			}
			// 1개월 정보 추출
			$order_status['month']['환불요청'] += $v['cnt_monyback_request'];
			$order_status['month']['환불완료'] += $v['cnt_complain_complete'];
		}
		// ----- 환불요청 건수 정보 추출 - 오늘/1주일/1개월  -----




		// 주문/배송 현황 링크 함수
		//			status - 결제대기, 결제완료, 배송중 등 o_status 값
		//			type - today, week, month
		if (!function_exists('order_link')) {
			function order_link($status , $type=null){
				$view = ''; $pass_paystatus = $pass_complain=$pass_moneyback_status='';
				switch( $status ){
					case "결제대기":
						$pass_paystatus = 'A'; $view = 'online'; $file_name = '_order.list.php';
						$pass_status = 'pass_status[]';
					break;
					case "결제완료": case "배송중": case "배송준비": case "배송완료":
						$pass_paystatus = 'Y'; $file_name = '_order.list.php';
						$pass_status = 'pass_status[]';
					break;
					case "주문취소":
						$pass_paystatus = 'A';  $file_name = '_order.cancel_list.php';
					break;
					case "부분취소요청":
						 $pass_cancel ='R'; $file_name = '_cancel.list.php';
					break;
					case "부분취소완료":
						 $pass_cancel ='Y'; $file_name = '_cancel.list.php';
					break;
					case "반품/교환요청":
						$file_name = '_order_complain.list.php';
						$pass_complain = '교환/반품신청';
					break;
					case "반품/교환완료":
						$file_name = '_order_complain.list.php';
						$pass_complain = '교환/반품완료';
					break;
					case "환불요청":
						$file_name = '_cancel_order.list.php';
						$pass_moneyback_status = 'request';
						$pass_status = 'pass_status[]';
					break;
					case "환불완료":
						$file_name = '_cancel_order.list.php';
						$pass_moneyback_status = 'complete';
					break;
					case "발급완료":
						$pass_paystatus = 'Y'; $file_name = '_order.list.php';
						$pass_status = 'pass_status[]';
					break;
				}

				$pass_status = $pass_status!=''?$pass_status:'pass_status';

				switch( $type ){
					case "today": $pass_sdate = DATE("Y-m-d"); $pass_edate = DATE("Y-m-d"); break;
					case "week": $pass_sdate = DATE("Y-m-d" , strtotime("-1 week")); $pass_edate = DATE("Y-m-d"); break;
					case "month": $pass_sdate = DATE("Y-m-d" , strtotime("-1 month")); $pass_edate = DATE("Y-m-d"); break;
					default : $pass_sdate = ''; $pass_edate = '';  break; // 없는 경우에 대한 처리
				}
				
				$arr_search = array(
											'mode'=>'search', $pass_status=>urlencode($status),'pass_main'=>'Y',
											'pass_paystatus'=>$pass_paystatus,'pass_sdate'=>urlencode($pass_sdate),
											'pass_edate'=>urlencode($pass_edate),'view'=>$view,
											'pass_cancel'=>$pass_cancel,'pass_complain'=>urlencode($pass_complain),
											'pass_moneyback_status'=>$pass_moneyback_status
										);

				$_link = $file_name.URI_Rebuild('?',$arr_search);

				return $_link;
			}
		}

	?>

	<?php // 주요 현황만 한번 더 노출/ 숫자는 천단위 콤마 필수 ?>
	<div class="sc_major_order">
		<ul>
			<?php
				foreach($admin_main_order_status_import as $k=>$v){
					// 상태값 별로 클래스 지정
					$inport_status_class = $v=='결제완료'?'class="this_payok"':($v=='주문취소'?'class="type_cancel"':'');
			?>
				<li <?php echo $inport_status_class?>>
					<div class="static_box">
						<dl>
							<dt>
								<a href="<?php echo order_link($v , 'today') ;?>" class="upper_link"></a>
								<div class="due"><strong><?php echo $v;?></strong>오늘</div>
								<div class="value"><?php echo number_format($order_status['today'][$v]);?></div>
							</dt>
							<dd>
								<a href="<?php echo order_link($v , 'week') ;?>" class="upper_link"></a>
								<div class="due">일주일</div>
								<div class="value"><?php echo number_format($order_status['week'][$v]);?></div>
							</dd>
							<dd>
								<a href="<?php echo order_link($v , 'month') ;?>" class="upper_link"></a>
								<div class="due">한달</div>
								<div class="value"><?php echo number_format($order_status['month'][$v]);?></div>
							</dd>
						</dl>
					</div>
				</li>
			<?php }?>
		</ul>
	</div>

	<div class="sc_count">
		<dl class="this_tit">
			<dt><div class="link">기간</div></dt>
			<dd><div class="link">오늘</div></dd>
			<dd><div class="link">일주일</div></dd>
			<dd><div class="link">한달</div></dd>
		</dl>
		<?php foreach($admin_main_order_status as $k=>$v){?>
			<dl>
				<dt><a href="<?php echo order_link($v);?>" class="link"><?php echo $v;?></a></dt>
				<dd><a href="<?php echo order_link($v , 'today') ;?>" class="link"><?php echo number_format($order_status['today'][$v]);?></a></dd>
				<dd><a href="<?php echo order_link($v , 'week') ;?>" class="link"><?php echo number_format($order_status['week'][$v]);?></a></dd>
				<dd><a href="<?php echo order_link($v , 'month') ;?>" class="link"><?php echo number_format($order_status['month'][$v]);?></a></dd>
			</dl>
		<?php	}?>
	</div>


<?// ------------- 주문/배송 현황 ------------- ?>