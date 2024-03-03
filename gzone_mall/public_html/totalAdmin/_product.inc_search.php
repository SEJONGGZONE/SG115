<?php


	// --------------------- 상품 일괄 관리 --- 검색폼 부분 ---------------------
	//			해당 파일 include 전 s_query가 정의되어야 함.
	//			예) $s_query = " from smart_product as p where 1 ";
	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 전체검색 필터
	if( !$pass_input_type) $pass_input_type = 'all';

	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info("product_name_small_icon");

	// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. {
	if($pass_input_value != ''){
		$arr_input_que = array();
		if( in_array($pass_input_type, array('all','pname')) > 0 ){ $arr_input_que[] = " p_name like '%".$pass_input_value."%'  "; } // 상품명
		if( in_array($pass_input_type, array('all','pcode')) > 0 ){ $arr_input_que[] = " p_code like '%".$pass_input_value."%'  "; } // 상품코드

		if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
	}
	// 신규: 검색어가 있을 경우 타입이 다라 분류 해준다. }

	if( $pass_type !="" ) { $s_query .= " AND p.p_type = '".$pass_type."' "; }	// 상품타입
	if( $pass_vat !="" ) { $s_query .= " AND p.p_vat = '".$pass_vat."' "; }			// 부가세율
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }	// 브랜드

	if($relation_code != ''){ $s_query .= " AND p.p_code != '".$relation_code."' "; }

	// 재고량
	if($pass_stock != '') {
		if($pass_stock == 'x') $s_query .= " and p_stock <= 0 ";
		else if($pass_stock == 1) $s_query .= " and p_stock > 0 and p_stock <= 50 ";
		else if($pass_stock == 50) $s_query .= " and p_stock > 50 and p_stock <= 100 ";
		else if($pass_stock == 100) $s_query .= " and p_stock > 100 ";
	}
	// 입점업체
	if($pass_com!="") {	$s_query .= " and `p_cpid` = '{$pass_com}' "; }

	// 추가옵션 사용여부
	if($pass_addoption_type_chk!=""){
		if($pass_addoption_type_chk=='Y'){
			$s_query .= " and (select count(*) from smart_product_addoption as pao where pao.pao_pcode=p.p_code ) > 0 ";
		}else{
			$s_query .= " and (select count(*) from smart_product_addoption as pao where pao.pao_pcode=p.p_code ) <= 0 ";
		}
	}

	if( $pass_view !="" ) { $s_query .= " and p_view='".$pass_view."' "; }																// 노출여부
	if( $pass_option_valid_chk !="" ) { $s_query .= " and p_option_valid_chk='${pass_option_valid_chk}' "; }	// 옵션 적합성
	if( $_cpid !="" ) { $s_query .= " and p_cpid='${_cpid}' "; }																				// 입점업체아이디
	if( $pass_npay_use !="" ) { $s_query .= " and npay_use ='".$pass_npay_use."' "; }										// 네이버페이 사용여부
	if( $pass_option_type_chk !="" ) { $s_query .= " and p_option_type_chk ='".$pass_option_type_chk."' "; }	// 옵션설정
	if( $pass_expire_type !="" ) { $s_query .= " and p_expire_type ='".($pass_expire_type)."' "; }						// 티켓 유효기간
	if( $pass_dateoption_use !="" ) { $s_query .= " and p_dateoption_use ='".$pass_dateoption_use."' "; }		// 티켓 달력옵션 사용여부
	if($pass_time_sale!=""){	$s_query .= " and p_sale_type ='A' and p_time_sale ='".$pass_time_sale."'";}											// 타임세일 적용여부
	if($pass_commission_type!=""){	$s_query .= " and p_commission_type ='".$pass_commission_type."'";}		// 정산방식

	// 카테고리
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


	// 판매기간 검색
	if( $pass_sale_type !="" ) {
		if( $pass_sale_type == 'A'){ $s_query .= " and p_sale_type ='A' ";  }
		else if($pass_sale_type == 'T1'){$s_query .= " and p_sale_type ='T' AND p_sale_sdate > CURDATE() ";  }
		else if($pass_sale_type == 'T2'){$s_query .= " and p_sale_type ='T' AND CURDATE() between p_sale_sdate and p_sale_edate ";  }
		else if($pass_sale_type == 'T3'){$s_query .= " and p_sale_type ='T' AND p_sale_sdate < CURDATE() and p_sale_edate < CURDATE()";  }
	}

?>


<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search<?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' if_open_comp':null; ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
		<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

	<?php // 통합검색 ?>
	<div class="comp_search">
		<div class="form_wrap">
			<select name="pass_input_type">
				<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
				<option value="pname" <?php echo $pass_input_type == 'pname' ? 'selected' : ''?>>상품명</option>
				<option value="pcode" <?php echo $pass_input_type == 'pcode' ? 'selected' : ''?>>상품코드</option>
			</select>
			<div class="search_input">
				<input type="search" name="pass_input_value" class="design" value="<?php echo $pass_input_value?>" placeholder="검색어" />
				<input type="submit" name="" value="" accesskey="s" class="btn_search" title="검색" />
				<?php
					if($mode == 'search' && $pass_input_value != '' ){
						$arr_param = array_filter(array_merge($arr_param,array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)));
				?>
					<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="line btn_reset" title="검색초기화"></a>
				<?php } ?>
			</div>
			<a href="#none" class="c_btn sky line js_onoff_event type_open" data-target=".data_search" data-add="if_open_comp"><strong>상세검색</strong></a>
		</div><!-- end form_wrap -->

		<?php if(in_array($CURR_FILENAME, array('_product.list.php','_product_ticket.list.php')) > 0){?>
			<div class="ctrl_wrap">
				<?php if($pass_type == 'ticket'){ ?>
					<a href="_product_ticket.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn write red" accesskey="a"><strong>상품등록</strong></a><?php // 티켓상품등록 ?>
				<?php }else{ ?>
					<a href="_product.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn red write" accesskey="a"><strong>상품등록</strong></a><?php // 배송상품등록 ?>
				<?php }?>
				<a href="#none" class="c_btn h46 red line js_open_excel_box only_pc_view">일괄 업로드</a>
			</div>
		<?php }?>
	</div><!-- end comp_search -->
	<?php if(in_array($CURR_FILENAME, array('_product.list.php','_product_ticket.list.php')) > 0){?>
	<div class="mobile_tip">엑셀 다운 및 일괄업로드는 PC에서 가능합니다.</div>
	<?php }?>


	<div class="search_form comp_search_open">
		<div class="group_title">
			<strong>Search</strong>
			<div class="btn_box">
				<?php // 체크 : if_open 붙여진 상태로 캐시저장, 캐시저장 후  checked 상태유지, 체크된 상태에서는 상세검색 닫아도 새로고침하면 다시 열려진 상태임 ?>
				<label class="design"><input type="checkbox" class="js_common_search_detail" name="<?php echo $searchDetailCacheKey; ?>" value="Y" <?php echo $search_detail_cache[$searchDetailCacheKey] == 'Y' ? ' checked': null  ?> />상세검색 계속 열어두기</label>
			</div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<?php if( $_SERVER['PHP_SELF'] == '/totalAdmin/_category.best_product.pop.php' ) { ?>
				<?php }else { ?>
				<tr>
					<th>카테고리</th>
					<td colspan="3">
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
				<?php } ?>
				<?php if(in_array($CURR_FILENAME, array('_product.list.php','_product_ticket.list.php')) < 1){?>
					<tr>
						<th>상품타입</th>
						<td colspan="3">
							<?php
								echo _InputRadio( "pass_type" , array('', 'delivery','ticket'), $pass_type , "" , array('전체', '배송상품','티켓상품') );
							?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?>
					</td>
					<th>네이버페이</th>
					<td>
						<?php echo _InputRadio("pass_npay_use" , array('', 'Y','N') , $pass_npay_use, '' , array('전체', '사용','미사용'));  ?>
					</td>
				</tr>
				<tr>
					<th>판매기간</th>
					<td>
						<?php
							echo _InputRadio( "pass_sale_type" , array('', 'A','T1','T2','T3'), $pass_sale_type , "" , array('전체', '상시판매','판매전','판매중','판매종료') );
						?>
					</td>
					<th>재고검색</th>
					<td>
						<?php echo _InputRadio('pass_stock' , array('','x',1,50,100) , $pass_stock, '' , array('전체','품절', '1개 ~ 50개', '50개 ~ 100개', '100개 초과'), '-재고량-');  ?>
					</td>
				</tr>
				<tr>
					<th>옵션설정</th>
					<td>
						<?php echo _InputRadio('pass_option_type_chk' , array('', 'nooption','1depth','2depth','3depth') , $pass_option_type_chk, '' , array('전체', '미사용', '1차옵션','2차옵션','3차옵션'));  ?>
					</td>
					<th>옵션오류</th>
					<td>
						<?php echo _InputRadio('pass_option_valid_chk' , array('', 'Y','N') , $pass_option_valid_chk, '' , array('전체', '정상','오류'));  ?>
					</td>
				</tr>
				<tr>
					<th>추가옵션</th>
					<td>
						<?php
							if( $pass_type=='ticket'){
								echo _DescStr('티켓상품은 추가옵션 사용불가','red');
							}else{
								echo _InputRadio('pass_addoption_type_chk' , array('', 'Y','N') , $pass_addoption_type_chk, '' , array('전체', '사용', '미사용'));
							}
						?>
					</td>
					<th>타임세일</th>
					<td>
						<?php
							echo _InputRadio( "pass_time_sale" , array('', 'Y','N'), $pass_time_sale , "" , array('전체', '적용','미적용') );
						?>
					</td>
				</tr>
				<?php if($pass_type == 'ticket'){  // LCY : 2022-12-21 : 티켓기능 -- 검색 추가 { ?>
				<tr>
					<th>티켓 유효기간</th>
					<td>
						<?php echo _InputRadio('pass_expire_type' , array('', 'none', 'day','date',) , $pass_expire_type, '' , array('전체', '제한없음', '만료일 지정', '만료날짜 지정'));  ?>
					</td>
					<th>달력옵션</th>
					<td>
						<?php echo _InputRadio('pass_dateoption_use' , array('', 'Y','N',) , $pass_dateoption_use, '' , array('전체', '사용', '미사용'));  ?>
					</td>
				</tr>
				<?php } // LCY : 2022-12-21 : 티켓기능 -- 검색 추가 { ?>
				<tr>
					<th>브랜드</th>
					<td>
						<?php
							// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
							//		basic : 기본정보
							//		all : 브랜드 전체 정보
							$arr_brand = brand_info('basic');
							echo _InputSelect( "pass_brand" , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "브랜드 선택");
						?>
					</td>
					<th>입점업체</th>
					<td>
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
				<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
				<tr>
					<th>과세여부</th>
					<td colspan="3">
						<?php echo _InputRadio( 'pass_vat' , array('', 'Y','N'), $pass_vat , '' , array('전체', '과세','면세')); ?>
					</td>
				</tr>
				<?php } ?>


				<?php if( $search_detail_name == 'mass_price') {?>
				<tr>
					<th>정산방식</th>
					<td colspan="3">
						<?php
							echo _InputRadio( "pass_commission_type" , array('', '공급가','수수료'), $pass_commission_type , "" , array('전체', '공급가','수수료') );
						?>
					</td>
				</tr>
				<?php }?>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('옵션오류 : 상품의 옵션설정과 등록된 실제 옵션 차수가 맞지 않을 경우 오류로 표시됩니다.'); ?>
						<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
							<?php echo _DescStr('과세여부 : 환경설정에서 복합과세 선택 시 검색가능한 항목입니다.'); ?>
						<?php } ?>
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


    <!-- 공통 통합검색 스크립트 -->
    <script>
    	// 공통:  검색상세열기 상태 저장
    	$(document).on('click','.js_common_search_detail',function(e){
    		var checked = $(this).prop('checked') == true ? 'Y':'N';
    		var name = $(this).attr('name');
    		$.ajax({url:'_pro.php',data:{_mode:'set_search_detail_cache_cookie',checked : checked , name : name},type:'get', dataType:'json'});
    	});

    	// 공통: 날짜선택 이벤트
    	$(document).on('click','.js_date_auto_set',function(){
    		var thisData= $(this).data();
    		// 아무 데이터도 없을 시 처리
    		if( typeof thisData.sname == 'undefined' || !thisData.sname){ thisData.sname = ''; }
    		if( typeof thisData.sdate == 'undefined' || !thisData.sdate){ thisData.sdate = ''; }
    		if( typeof thisData.ename == 'undefined' || !thisData.ename){ thisData.ename = ''; }
    		if( typeof thisData.edate == 'undefined' || !thisData.edate){ thisData.edate = ''; }
    		if( thisData.sname != '' && thisData.sdate != ''){ $('form [name="'+thisData.sname+'"]').val(thisData.sdate); }
    		if( thisData.ename != '' && thisData.edate != ''){ $('form [name="'+thisData.ename+'"]').val(thisData.edate); }
    		return false;
    	});

    	// 시작 공통 이벤트
    	$(document).ready(function(e){

    	});
    </script>

</form><!-- end data_search -->

<script>
    	// 공통:  검색상세열기 상태 저장
    	$(document).on('click','.js_common_search_detail',function(e){
    		var checked = $(this).prop('checked') == true ? 'Y':'N';
    		var name = $(this).attr('name');
    		$.ajax({url:'_pro.php',data:{_mode:'set_cookie',checked : checked , name : name},type:'get', dataType:'json'});
    	});
</script>
