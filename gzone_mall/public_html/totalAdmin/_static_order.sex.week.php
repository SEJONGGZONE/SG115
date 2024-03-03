<?php


	// 일자계산
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
				AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
		";


		$arr_sum = array();

		// ---- 구매건수, 구매수량, 구매금액 요약 ----
		$que = "
			SELECT

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,

				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			FROM
			(
				SELECT

					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,


					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
				INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

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
		$arr_res = $arr_all_res = array();
		$que = "
			select

				rdate, sex,

				COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
				COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

				COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


				SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
				SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

				SUM(sub_sum_buy_cnt) as sum_buy_cnt,

				SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
				SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

				SUM(sub_sum_buy_price) as sum_buy_price

			from
			(
				SELECT

					DATE_FORMAT(o.o_rdate, '%w') as rdate ,
					ind.in_sex as sex,
					op.op_oordernum ,

					IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
					IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


					SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
					SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

					SUM( op.op_cnt ) as sub_sum_buy_cnt,

					SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
					SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


					SUM( op_price * op_cnt ) as sub_sum_buy_price

				FROM smart_order_product as op
				INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
				INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

				" . $s_query . "

				group by op.op_oordernum

			) as tbl_view

			GROUP BY rdate, sex
			ORDER BY
				rdate ASC
		";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v ){

			// 성별 정리 ::: M,F가 아닐 경우 미선택
			$v['sex'] = ( IN_ARRAY($v['sex'] , array('M' , 'F'))) ? $v['sex'] : 'etc';

			// 구매건수
			$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
			$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
			$arr_res[$v['rdate']][$v['sex']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

			$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
			$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


			// 구매수량
			$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
			$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
			$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

			$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
			$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


			// 구매금액
			$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
			$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
			$arr_res[$v['rdate']][$v['sex']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
			$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
			$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

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
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('sex_week_search');
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
	$arr_class_data['sex_all_order_cnt_num'] =  array('grid_sky');
	$arr_class_data['sex_all_buy_cnt_num'] =  array('grid_sky');
	$arr_class_data['sex_all_buy_price_num'] =  array('grid_sky');

	$arr_table_data = array();

	for($i=0 ; $i<=6 ; $i++){

		$app_date = week_name( $i , '요일');
		$app_date_key = $i;

		// ----- 소계 -----
		$arr_table_data_tmp1 = array(
				'_extraData' => array(
					'rowSpan' =>array('rdate' => 3),// 날짜 열별합 - 3개(rowspan=3)
					'className' =>array(
						'row' => array('grid'),// 행에 디자인 클래스를 적용
						'column' => $arr_class_data
					)
				),
				'rdate' => $app_date,
				'device' => '소계',

				'sex_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['sum']),
				'sex_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']),
				'sex_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['sum'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_sex as $sk=>$sv) {
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'sex_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']),
				'sex_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']),
				'sex_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['sum'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- 소계 -----



		// ----- PC -----
		$arr_table_data_tmp1 = array(
				'device' => 'PC',

				'sex_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']),
				'sex_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']),
				'sex_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileN'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_sex as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'sex_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']),
				'sex_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']),
				'sex_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- PC -----



		// ----- 모바일 -----
		$arr_table_data_tmp1 = array(
				'device' => '모바일',

				'sex_all_order_cnt_num' => number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']),
				'sex_all_buy_cnt_num' => number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']),
				'sex_all_buy_price_num' => number_format($arr_all_res[$app_date_key]['buy_price']['mobileY'])
		);

		$arr_table_data_tmp2 = array();
		foreach($arr_order_sex as $sk=>$sv) {

			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'sex_' . $sk . '_order_cnt_num' => number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']),
				'sex_' . $sk . '_buy_cnt_num' => number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']),
				'sex_' . $sk . '_buy_price_num' => number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY'])
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
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
        headerHeight: 99,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [
			{
				"title" : "<b>전체</b>", "columnName" : "sex_all",
				"columnNameList" : [
					"sex_all_order_cnt_num",
					"sex_all_buy_cnt_num",
					"sex_all_buy_price_num"
				]
			},
			<?$cnt=0; foreach($arr_order_sex as $sk=>$sv) {?>
				<?echo ( $cnt <> 0 ? ',' : '' ); $cnt++;?>
				{
					"title" : "<b><?=$sv?></b>", "columnName" : "sex_<?=$sk?>",
					"columnNameList" : [
						"sex_<?=$sk?>_order_cnt_num",
						"sex_<?=$sk?>_buy_cnt_num",
						"sex_<?=$sk?>_buy_price_num",
					]
				}
			<?}?>
        ],

        columnModelList: [
            {"title" : "<b>시간</b>", "columnName" : "rdate", "align" : "center", "width" : 70 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 70 },

			{"title" : "구매건수", "columnName" : "sex_all_order_cnt_num", "align" : "right", "width" : 70 },
			{"title" : "구매수량", "columnName" : "sex_all_buy_cnt_num", "align" : "right", "width" : 70 },
			{"title" : "구매금액", "columnName" : "sex_all_buy_price_num", "align" : "right", "width" : 120 },

			<?$cnt=0; foreach($arr_order_sex as $sk=>$sv) {?>
				<?echo ( $cnt <> 0 ? ',' : '' ); $cnt++;?>
				{"title" : "구매건수", "columnName" : "sex_<?=$sk?>_order_cnt_num", "align" : "right", "width" : 70 },
				{"title" : "구매수량", "columnName" : "sex_<?=$sk?>_buy_cnt_num", "align" : "right", "width" : 70 },
				{"title" : "구매금액", "columnName" : "sex_<?=$sk?>_buy_price_num", "align" : "right", "width" : 120 }
			<?}?>
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>