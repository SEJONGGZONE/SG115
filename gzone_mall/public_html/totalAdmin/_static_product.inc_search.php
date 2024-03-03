<?php


	// --------------------- 상품 일괄 관리 --- 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " where 1 ";


	// 추가파라메터
	if(!$arr_param) $arr_param = array();


	// 2017-06-16 ::: 부가세율설정 ::: JJC
	if( $pass_vat !="" ) { $s_query .= " AND p.p_vat = '".$pass_vat."' "; }

	// JJC ::: 브랜드관리 ::: 2017-11-03
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

	if( $pass_code !="" ) { $s_query .= " and p_code like '%${pass_code}%' "; }
	if( $pass_name !="" ) { $s_query .= " and p_name like '%${pass_name}%' "; }
	if( $pass_view !="" ) { $s_query .= " and p_view='${pass_view}' "; }

	// 입점업체 검색기능 2016-05-26 LDD
	if($pass_com) { $s_query .= " and `p_cpid` = '{$pass_com}' "; }

	if( $_cpid !="" ) { $s_query .= " and p_cpid='${_cpid}' "; }
	if( $_cuid !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$_cuid."') > 0 "; }
	else if( $pass_parent03_real !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$pass_parent03_real."') > 0 "; }
	else if( $pass_parent02_real !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , -1) = '" . $pass_parent02_real . "' or
						pct.pct_cuid = '" . $pass_parent02_real . "'
					)
			) > 0
		";
	}
	else if( $pass_parent01 !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , 1) = '" . $pass_parent01 . "' or
						pct.pct_cuid = '" . $pass_parent01 . "'
					)
			) > 0
		";
	}


	// - 검색기간 -- 장바구니일 경우
	if( $search_type == 'cart' ){
		if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(c.c_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
		else if( $pass_sdate ) { $s_query .= " AND DATE(c.c_rdate) >= '". $pass_sdate ."' "; }
		else if( $pass_edate ) { $s_query .= " AND DATE(c.c_rdate) <= '". $pass_edate ."' "; }
	}
	// - 검색기간 -- 찜하기일 경우
	else if( $search_type == 'wish' ){
		if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(pw.pw_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
		else if( $pass_sdate ) { $s_query .= " AND DATE(pw.pw_rdate) >= '". $pass_sdate ."' "; }
		else if( $pass_edate ) { $s_query .= " AND DATE(pw.pw_rdate) <= '". $pass_edate ."' "; }
	}
	// - 검색기간 -- 일반 - 주문 경우
	else {
		if( $pass_sdate && $pass_edate ) { $s_query .= " AND DATE(o.o_rdate) between '". $pass_sdate ."' and '". $pass_edate ."' "; }
		else if( $pass_sdate ) { $s_query .= " AND DATE(o.o_rdate) >= '". $pass_sdate ."' "; }
		else if( $pass_edate ) { $s_query .= " AND DATE(o.o_rdate) <= '". $pass_edate ."' "; }
	}

	if( $pass_npay_use !="" ) { $s_query .= " and npay_use ='".$pass_npay_use."' "; }
	if( $pass_option_type_chk !="" ) { $s_query .= " and p_option_type_chk ='".$pass_option_type_chk."' "; }
	if( $pass_expire_type !="" ) { $s_query .= " and p_expire_type ='".($pass_expire_type)."' "; }
	if( $pass_dateoption_use !="" ) { $s_query .= " and p_dateoption_use ='".$pass_dateoption_use."' "; }
	if( $pass_sale_type !="" ) {
		if( $pass_sale_type == 'A'){ $s_query .= " and p_sale_type ='A' ";  }
		else if($pass_sale_type == 'T1'){$s_query .= " and p_sale_type ='T' AND p_sale_sdate > CURDATE() ";  }
		else if($pass_sale_type == 'T2'){$s_query .= " and p_sale_type ='T' AND CURDATE() between p_sale_sdate and p_sale_edate ";  }
		else if($pass_sale_type == 'T3'){$s_query .= " and p_sale_type ='T' AND p_sale_sdate < CURDATE() ";  }
	}

?>




<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search">
	<input type="hidden" name="_mode" value=""><?//엑셀 다운로드용 - mode?>
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>


	<div class="group_title">
		<strong>Search</strong>
		<div class="btn_box">
			<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
			<?php
			if($mode == 'search'){
				$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount),$arr_param));
				?>
				<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
			<?php } ?>
		</div>
	</div>

	<div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?>
					</td>
					<th>상품코드</th>
					<td><input type="text" name="pass_code" class="design" style="" value="<?php echo $pass_code; ?>" placeholder="상품코드"></td>
					<th>상품명</th>
					<td><input type="text" name="pass_name" class="design" style="" value="<?php echo $pass_name; ?>" placeholder="상품명"></td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td <?=($SubAdminMode === true ? "" : "")?>>
						<div class="lineup-row type_date">
							<input type="text" name="pass_sdate" class="design js_pic_day_max_today" value="<?php echo $pass_sdate; ?>" style="width:90px" autocomplete="off" placeholder="날짜 선택" readonly >
							<span class="fr_tx">~</span>
							<input type="text" name="pass_edate" class="design js_pic_day_max_today" value="<?php echo $pass_edate; ?>" style="width:90px" autocomplete="off" placeholder="날짜 선택" readonly >
						</div>
					</td>
					<th>브랜드</th>
					<td>
						<?php
							// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
							//		basic : 기본정보
							//		all : 브랜드 전체 정보
							$arr_brand = brand_info('basic');
							echo _InputSelect( "pass_brand" , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "-브랜드-");
						?>
					</td>
					<th>입점업체</th>
					<td >
					<?php
					// ----- JJC : 입점관리 : 2020-09-17 -----
					if($SubAdminMode === true ) { // 입점업체 검색기능 2016-05-26 LDD
						$arr_customer = arr_company();
						$arr_customer2 = arr_company2();
					?>

						<?php if( $AdminPath == 'totalAdmin'){?>
						<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
						<script src="/include/js/select2/js/select2.min.js"></script>
						<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '입점업체 선택'); ?>
						<?php }else{ ?>
							<?php echo $arr_customer2[$pass_com]; ?>
						<?php } ?>
					<?php }else{?>
						<?php echo _DescStr('입점업체 미사용 <a href="https://www.onedaynet.co.kr/p/solution_plus.html#page_entershop" target="_blank"><em>신청하기</em></a>',''); ?>
					<?php }?>
					</td>
				</tr>
				<tr>
					<th>카테고리</th>
					<td colspan="5">
						<div class="lineup-row type_process">
							<?PHP
								// 상품 카테고리 분류 (list , form 을 공통으로 쓰기 위한 조치)
								// 1차 - pass_parent01 -> app_depth1
								// 2차 - pass_parent02_real -> app_depth2
								// 3차 - pass_parent03_real -> $row[p_cuid]
								if( $pass_parent01 ) {
									$app_depth1 =  $pass_parent01 ;
								}
								if( $pass_parent02_real ) {
									$app_depth2 =  $pass_parent02_real ;
								}
								if( $pass_parent03_real ) {
									$app_depth3 =  $pass_parent03_real ;
								}

								$pass_parent03_no_required = "Y";

								include_once(OD_PROGRAM_ROOT."/category.inc.php");
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
				<?php
					if($mode == 'search'){
						$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount),$arr_param));
				?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

</form><!-- end data_search -->


