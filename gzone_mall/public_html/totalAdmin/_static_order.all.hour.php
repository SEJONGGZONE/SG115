<?php

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


		$s_query = "
			where
				o.o_canceled!='Y'
				AND o.o_paystatus = 'Y'
				AND op.op_cancel = 'N'
				AND DATE(o.o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
		";


		$arr_sum = array();

		// ---- 구매건수, 구매수량, 구매금액 요약 ----
		$que = "
			SELECT

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
				COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
				SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
				SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			FROM
			(
				SELECT

					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

					IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
					IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,

					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

					SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
					SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

					SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
					SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){
			foreach( $v as $sk=>$sv ){
				$arr_sum[$sk] = $sv;
			}
		}
		// ---- 구매건수, 구매수량, 구매금액 요약 ----




		// ------- 목록 -------
		$arr_res = array();
		$que = "
			select

				rdate,

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
				COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
				SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
				SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			from
			(
				SELECT

					HOUR(o_rdate) as rdate ,
					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

					IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
					IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,


					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

					SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
					SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

					SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
					SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view

			GROUP BY rdate
			ORDER BY
				rdate ASC
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){

			// 구매건수
			$arr_res[$v['rdate']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
			$arr_res[$v['rdate']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
			$arr_res[$v['rdate']]['order_cnt']['memtypeN'] = $v['sum_memtypeN_order_cnt'];
			$arr_res[$v['rdate']]['order_cnt']['memtypeY'] = $v['sum_memtypeY_order_cnt'];
			$arr_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

			// 구매수량
			$arr_res[$v['rdate']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
			$arr_res[$v['rdate']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
			$arr_res[$v['rdate']]['buy_cnt']['memtypeN'] = $v['sum_memtypeN_buy_cnt'];
			$arr_res[$v['rdate']]['buy_cnt']['memtypeY'] = $v['sum_memtypeY_buy_cnt'];
			$arr_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

			// 구매금액
			$arr_res[$v['rdate']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
			$arr_res[$v['rdate']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
			$arr_res[$v['rdate']]['buy_price']['memtypeN'] = $v['sum_memtypeN_buy_price'];
			$arr_res[$v['rdate']]['buy_price']['memtypeY'] = $v['sum_memtypeY_buy_price'];
			$arr_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

		}
		// ------- 목록 -------
	?>

<div class="group_title type_search">
	<strong><?=DATE("Y년 m월 d일" , strtotime($pass_date) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?></strong>

	<!-- 기간검색 -->
	<form name="searchfrm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="simple_search">
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



<?php
	$app_sum_order_cnt = $arr_sum['sum_order_cnt'] > 0 ? $arr_sum['sum_order_cnt'] : 1;
	$app_sum_buy_cnt = $arr_sum['sum_buy_cnt'] > 0 ? $arr_sum['sum_buy_cnt'] : 1;
	$app_sum_buy_price = $arr_sum['sum_buy_price'] > 0 ? $arr_sum['sum_buy_price'] : 1;
?>

<div class="data_graph_wrap">

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
					<th>구매건수</th>
					<td class='t_right t_blue'>
						<?php echo number_format($arr_sum['sum_mobileN_order_cnt'] * 1); ?>건
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileN_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</div>
					</td>
					<td class='t_right t_blue'>
						<?php echo number_format($arr_sum['sum_mobileY_order_cnt'] * 1); ?>건
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileY_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</div>
					</td>
				</tr>
				<tr>
					<th>구매수량</th>
					<td class='t_right t_green'>
						<?php echo number_format($arr_sum['sum_mobileN_buy_cnt'] * 1); ?>개
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileN_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</div>
					</td>
					<td class='t_right t_green'>
						<?php echo number_format($arr_sum['sum_mobileY_buy_cnt'] * 1); ?>개
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileY_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</div>
					</td>
				</tr>
				<tr>
					<th>구매금액</th>
					<td class='t_right t_red'>
						<?php echo number_format($arr_sum['sum_mobileN_buy_price'] * 1); ?>원
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileN_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</div>
					</td>
					<td class='t_right t_red'>
						<?php echo number_format($arr_sum['sum_mobileY_buy_price'] * 1); ?>원
						<div class="t_none">(<?=number_format($arr_sum['sum_mobileY_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


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
					<th>구매건수</th>
					<td class='t_right t_blue'>
						<?php echo number_format($arr_sum['sum_memtypeY_order_cnt'] * 1); ?>건
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeY_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</div>
					</td>
					<td class='t_right t_blue'>
						<?php echo number_format($arr_sum['sum_memtypeN_order_cnt'] * 1); ?>건
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeN_order_cnt'] * 100 / $app_sum_order_cnt , 2)?>%)</div>
					</td>
				</tr>
				<tr>
					<th>구매수량</th>
					<td class='t_right t_green'>
						<?php echo number_format($arr_sum['sum_memtypeY_buy_cnt'] * 1); ?>개
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeY_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</div>
					</td>
					<td class='t_right t_green'>
						<?php echo number_format($arr_sum['sum_memtypeN_buy_cnt'] * 1); ?>개
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeN_buy_cnt'] * 100 / $app_sum_buy_cnt , 2)?>%)</div>
					</td>
				</tr>
				<tr>
					<th>구매금액</th>
					<td class='t_right t_red'>
						<?php echo number_format($arr_sum['sum_memtypeY_buy_price'] * 1); ?>원
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeY_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</div>
					</td>
					<td class='t_right t_red'>
						<?php echo number_format($arr_sum['sum_memtypeN_buy_price'] * 1); ?>원
						<div class="t_none">(<?=number_format($arr_sum['sum_memtypeN_buy_price'] * 100 / $app_sum_buy_price , 2)?>%)</div>

					</td>
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
					<th>합계</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>구매건수</th>
					<td class='t_right t_blue t_bold'><?php echo number_format($arr_sum['sum_order_cnt'] * 1); ?>건</td>
				</tr>
				<tr>
					<th>구매수량</th>
					<td class='t_right t_green t_bold'><?php echo number_format(( $arr_sum['sum_buy_cnt'] )* 1); ?>개</td>
				</tr>
				<tr>
					<th>구매금액</th>
					<td class='t_right t_red t_bold'><?php echo number_format(( $arr_sum['sum_buy_price'])* 1); ?>원</td>
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




<form name="frmSearch" method="post" action="_static_order.pro.php" >
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
		$('form[name="frmSearch"]').attr('action', '_static_order.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>










<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용
	// 각 항목에 따른 비율 클래스 적용
	$arr_class_data['all_order_cnt_memtypeY_ratio'] =  array('grid_sky');
	$arr_class_data['all_order_cnt_memtypeN_ratio'] =  array('grid_sky');
	$arr_class_data['all_order_cnt_mobileN_ratio'] =  array('grid_sky');
	$arr_class_data['all_order_cnt_mobileY_ratio'] =  array('grid_sky');

	$arr_class_data['all_buy_cnt_memtypeY_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_cnt_memtypeN_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_cnt_mobileN_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_cnt_mobileY_ratio'] =  array('grid_sky');

	$arr_class_data['all_buy_price_memtypeY_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_price_memtypeN_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_price_mobileN_ratio'] =  array('grid_sky');
	$arr_class_data['all_buy_price_mobileY_ratio'] =  array('grid_sky');

	$arr_table_data = array();

	for($i=0 ; $i<=23 ; $i++){

		$app_date = $i . "시";
		$app_date_key = $i;

		$app_sum_order_cnt = $arr_res[$app_date_key]['order_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['order_cnt']['sum'] : 1;
		$app_sum_buy_cnt = $arr_res[$app_date_key]['buy_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['buy_cnt']['sum'] : 1;
		$app_sum_buy_price = $arr_res[$app_date_key]['buy_price']['sum'] > 0 ? $arr_res[$app_date_key]['buy_price']['sum'] : 1;

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'className' =>array(
						'column' => $arr_class_data
					)
				),

				'rdate' => $app_date,

				// 구매건수
				'all_order_cnt_sum' => number_format($arr_res[$app_date_key]['order_cnt']['sum']),
					//회원
				'all_order_cnt_memtypeY_num' => number_format($arr_res[$app_date_key]['order_cnt']['memtypeY']),
				'all_order_cnt_memtypeY_ratio' => number_format($arr_res[$app_date_key]['order_cnt']['memtypeY'] * 100 / $app_sum_order_cnt , 2) . '%',
					// 비회원
				'all_order_cnt_memtypeN_num' => number_format($arr_res[$app_date_key]['order_cnt']['memtypeN']),
				'all_order_cnt_memtypeN_ratio' => number_format($arr_res[$app_date_key]['order_cnt']['memtypeN'] * 100 / $app_sum_order_cnt , 2) . '%',
					// PC
				'all_order_cnt_mobileN_num' => number_format($arr_res[$app_date_key]['order_cnt']['mobileN']),
				'all_order_cnt_mobileN_ratio' => number_format($arr_res[$app_date_key]['order_cnt']['mobileN'] * 100 / $app_sum_order_cnt , 2) . '%',
					// MOBILE
				'all_order_cnt_mobileY_num' => number_format($arr_res[$app_date_key]['order_cnt']['mobileY']),
				'all_order_cnt_mobileY_ratio' => number_format($arr_res[$app_date_key]['order_cnt']['mobileY'] * 100 / $app_sum_order_cnt , 2) . '%',

				// 구매수량
				'all_buy_cnt_sum' => number_format($arr_res[$app_date_key]['buy_cnt']['sum']),
					//회원
				'all_buy_cnt_memtypeY_num' => number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY']),
				'all_buy_cnt_memtypeY_ratio' => number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY'] * 100 / $app_sum_buy_cnt , 2) . '%',
					// 비회원
				'all_buy_cnt_memtypeN_num' => number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN']),
				'all_buy_cnt_memtypeN_ratio' => number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN'] * 100 / $app_sum_buy_cnt , 2) . '%',
					// PC
				'all_buy_cnt_mobileN_num' => number_format($arr_res[$app_date_key]['buy_cnt']['mobileN']),
				'all_buy_cnt_mobileN_ratio' => number_format($arr_res[$app_date_key]['buy_cnt']['mobileN'] * 100 / $app_sum_buy_cnt , 2) . '%',
					// MOBILE
				'all_buy_cnt_mobileY_num' => number_format($arr_res[$app_date_key]['buy_cnt']['mobileY']),
				'all_buy_cnt_mobileY_ratio' => number_format($arr_res[$app_date_key]['buy_cnt']['mobileY'] * 100 / $app_sum_buy_cnt , 2) . '%',

				// 구매금액
				'all_buy_price_sum' => number_format($arr_res[$app_date_key]['buy_price']['sum']),
					//회원
				'all_buy_price_memtypeY_num' => number_format($arr_res[$app_date_key]['buy_price']['memtypeY']),
				'all_buy_price_memtypeY_ratio' => number_format($arr_res[$app_date_key]['buy_price']['memtypeY'] * 100 / $app_sum_buy_price , 2) . '%',
					// 비회원
				'all_buy_price_memtypeN_num' => number_format($arr_res[$app_date_key]['buy_price']['memtypeN']),
				'all_buy_price_memtypeN_ratio' => number_format($arr_res[$app_date_key]['buy_price']['memtypeN'] * 100 / $app_sum_buy_price , 2) . '%',
					// PC
				'all_buy_price_mobileN_num' => number_format($arr_res[$app_date_key]['buy_price']['mobileN']),
				'all_buy_price_mobileN_ratio' => number_format($arr_res[$app_date_key]['buy_price']['mobileN'] * 100 / $app_sum_buy_price , 2) . '%',
					// MOBILE
				'all_buy_price_mobileY_num' => number_format($arr_res[$app_date_key]['buy_price']['mobileY']),
				'all_buy_price_mobileY_ratio' => number_format($arr_res[$app_date_key]['buy_price']['mobileY'] * 100 / $app_sum_buy_price , 2) . '%'

		);

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

        columnFixCount: 1,
        headerHeight: 99,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [

			// 구매건수
			{"title" : "회원", "columnName" : "all_order_cnt_memtypeY", "columnNameList" : ["all_order_cnt_memtypeY_num", "all_order_cnt_memtypeY_ratio"] },
			{"title" : "비회원", "columnName" : "all_order_cnt_memtypeN", "columnNameList" : ["all_order_cnt_memtypeN_num", "all_order_cnt_memtypeN_ratio"] },
			{"title" : "PC", "columnName" : "all_order_cnt_mobileN", "columnNameList" : ["all_order_cnt_mobileN_num", "all_order_cnt_mobileN_ratio"] },
			{"title" : "MOBILE", "columnName" : "all_order_cnt_mobileY", "columnNameList" : ["all_order_cnt_mobileY_num", "all_order_cnt_mobileY_ratio"] },

			// 구매수량
			{"title" : "회원", "columnName" : "all_buy_cnt_memtypeY", "columnNameList" : ["all_buy_cnt_memtypeY_num", "all_buy_cnt_memtypeY_ratio"] },
			{"title" : "비회원", "columnName" : "all_buy_cnt_memtypeN", "columnNameList" : ["all_buy_cnt_memtypeN_num", "all_buy_cnt_memtypeN_ratio"] },
			{"title" : "PC", "columnName" : "all_buy_cnt_mobileN", "columnNameList" : ["all_buy_cnt_mobileN_num", "all_buy_cnt_mobileN_ratio"] },
			{"title" : "MOBILE", "columnName" : "all_buy_cnt_mobileY", "columnNameList" : ["all_buy_cnt_mobileY_num", "all_buy_cnt_mobileY_ratio"] },

			// 구매금액
			{"title" : "회원", "columnName" : "all_buy_price_memtypeY", "columnNameList" : ["all_buy_price_memtypeY_num", "all_buy_price_memtypeY_ratio"] },
			{"title" : "비회원", "columnName" : "all_buy_price_memtypeN", "columnNameList" : ["all_buy_price_memtypeN_num", "all_buy_price_memtypeN_ratio"] },
			{"title" : "PC", "columnName" : "all_buy_price_mobileN", "columnNameList" : ["all_buy_price_mobileN_num", "all_buy_price_mobileN_ratio"] },
			{"title" : "MOBILE", "columnName" : "all_buy_price_mobileY", "columnNameList" : ["all_buy_price_mobileY_num", "all_buy_price_mobileY_ratio"] },

			// header
			{"title" : "<b>구매건수</b>", "columnName" : "all_order_cnt_cols", "columnNameList" : ["all_order_cnt_sum", "all_order_cnt_memtypeY", "all_order_cnt_memtypeN", "all_order_cnt_mobileN", "all_order_cnt_mobileY"] },
			{"title" : "<b>구매수량</b>", "columnName" : "all_buy_cnt_cols", "columnNameList" : ["all_buy_cnt_sum", "all_buy_cnt_memtypeY", "all_buy_cnt_memtypeN", "all_buy_cnt_mobileN", "all_buy_cnt_mobileY"] },
			{"title" : "<b>구매금액</b>", "columnName" : "all_buy_price_cols", "columnNameList" : ["all_buy_price_sum", "all_buy_price_memtypeY", "all_buy_price_memtypeN", "all_buy_price_mobileN", "all_buy_price_mobileY"] }

        ],

        columnModelList: [
            {"title" : "<b>시간</b>", "columnName" : "rdate", "align" : "center", "width" : 60 },

			// 구매건수
			{"title" : "총건수", "columnName" : "all_order_cnt_sum", "align" : "right", "width" : 70},
				// 회원
				{"title" : "건수", "columnName" : "all_order_cnt_memtypeY_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_order_cnt_memtypeY_ratio", "align" : "right", "width" : 70 },
				// 비회원
				{"title" : "건수", "columnName" : "all_order_cnt_memtypeN_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_order_cnt_memtypeN_ratio", "align" : "right", "width" : 70 },
				// PC
				{"title" : "건수", "columnName" : "all_order_cnt_mobileN_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_order_cnt_mobileN_ratio", "align" : "right", "width" : 70 },
				// 모바일
				{"title" : "건수", "columnName" : "all_order_cnt_mobileY_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_order_cnt_mobileY_ratio", "align" : "right", "width" : 70 },

			// 구매수량
			{"title" : "총수량", "columnName" : "all_buy_cnt_sum", "align" : "right", "width" : 70 },
				// 회원
				{"title" : "수량", "columnName" : "all_buy_cnt_memtypeY_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_buy_cnt_memtypeY_ratio", "align" : "right", "width" : 70 },
				// 비회원
				{"title" : "수량", "columnName" : "all_buy_cnt_memtypeN_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_buy_cnt_memtypeN_ratio", "align" : "right", "width" : 70 },
				// PC
				{"title" : "수량", "columnName" : "all_buy_cnt_mobileN_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_buy_cnt_mobileN_ratio", "align" : "right", "width" : 70 },
				// 모바일
				{"title" : "수량", "columnName" : "all_buy_cnt_mobileY_num", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "all_buy_cnt_mobileY_ratio", "align" : "right", "width" : 70 },

			// 구매금액
			{"title" : "총금액", "columnName" : "all_buy_price_sum", "align" : "right", "width" : 120 },
				// 회원
				{"title" : "금액", "columnName" : "all_buy_price_memtypeY_num", "align" : "right", "width" : 120 },
				{"title" : "비율", "columnName" : "all_buy_price_memtypeY_ratio", "align" : "right", "width" : 70 },
				// 비회원
				{"title" : "금액", "columnName" : "all_buy_price_memtypeN_num", "align" : "right", "width" : 120 },
				{"title" : "비율", "columnName" : "all_buy_price_memtypeN_ratio", "align" : "right", "width" : 70 },
				// PC
				{"title" : "금액", "columnName" : "all_buy_price_mobileN_num", "align" : "right", "width" : 120 },
				{"title" : "비율", "columnName" : "all_buy_price_mobileN_ratio", "align" : "right", "width" : 70 },
				// 모바일
				{"title" : "금액", "columnName" : "all_buy_price_mobileY_num", "align" : "right", "width" : 120 },
				{"title" : "비율", "columnName" : "all_buy_price_mobileY_ratio", "align" : "right", "width" : 70 }

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>