<?PHP
	$app_mode = "popup";
	include_once("inc.header.php");

	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_GET , $_POST)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

	$_code = ($_code?$_code:$relation_code);

	$relation_prop_code = $_COOKIE['relation_prop_code_'.$_code];
	if($relation_prop_code){
		$ex = explode("|" , $relation_prop_code );
		foreach($ex as $v) {
			$relation_prop_code_Division[] = $v;
		}
		$relation_prop_code_Division = array_filter($relation_prop_code_Division);
	}

?>


<div class="popup none_layer" >

	<div class="pop_title">
		<strong>베스트 상품추가</strong>
		<a href="#none" onclick="window.close(); return false;" class="btn_close" accesskey="x"></a>
	</div>

	<?php
		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where 1 ";

		// 검색조건 추가
		if($relation_code && sizeof($relation_prop_code_Division) > 0){ $s_query .= " and p_code in ('".implode("','",$relation_prop_code_Division)."') "; $mode = 'search'; }


		// 추가파라메터
		$arr_param = array('pass_cuid'=>$pass_cuid);

		// -- 카테고리 정보를 가져온다.
		$rowCate = _MQ("select *from smart_category where c_uid = '".$pass_cuid."' ");

		$arrParent = rm_str($rowCate['c_parent']) > 0 ? explode(',',$rowCate['c_parent']) : array();
		if( $rowCate['c_depth'] >= 1){
			$pass_parent01 = $pass_cuid;
		}

		if( $rowCate['c_depth'] >= 2){
			$pass_parent01 = $arrParent[0];
			$pass_parent02_real = $pass_cuid;
		}

		if( $rowCate['c_depth'] >= 3){
			$pass_parent01 = $arrParent[0];
			$pass_parent02_real = $arrParent[1];
			$pass_parent03_real = $pass_cuid;
		}

		$search_detail_name = 'cate_best';
		include_once("_product.inc_search.php");
		//	==> s_query 리턴됨.

		// -- 기존에 선택된건 나오지 않게한다.
		$s_query .= " and (select count(*) as cnt from smart_product_category_best where pctb_pcode = p.p_code and pctb_cuid = '".$pass_cuid."' ) < 1  ";

		if(!$listmaxcount) $listmaxcount = 50;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'p_rdate';
		if(!$so) $so = 'desc';
		$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$res = _MQ_assoc(" select p.* $s_query order by {$st} {$so} limit $count , $listmaxcount ");
	?>


	<form name="bestProductForm" class="form_wrap">
		<input type="hidden" name="cuid" value="<?php echo $pass_cuid; ?>">

		<div class="pop_conts">

			<div class="data_list">
				<div class="list_ctrl">
					<div class="left_box">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
						<span class="c_btn h27 blue"><input type="button" name="" value="선택 상품추가"  onclick="bestProductAdd();"/></span>
					</div>
					<div class="right_box">
						<select class="h27" onchange="location.href=this.value;">
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'asc'?' selected':null); ?>>상품명 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'desc'?' selected':null); ?>>상품명 ▼</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'asc'?' selected':null); ?>>판매가 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'desc'?' selected':null); ?>>판매가 ▼</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'asc'?' selected':null); ?>>재고량 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'desc'?' selected':null); ?>>재고량 ▼</option>
						</select>

						<select class="h27" onchange="location.href=this.value;">
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
						</select>
					</div>
				</div>

				<table class="table_list">
					<colgroup>
						<col width="40"><col width="70"><col width="90"><col width="*"><col width="120"><col width="100">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
							<th scope="col">No</th>
							<th scope="col">노출</th>
							<th scope="col">상품정보</th>
							<th scope="col">판매가</th>
							<th scope="col">재고량</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(count($res) > 0){
							foreach($res as $k=>$v){

								$_num = $TotalCount-$count-$k; // NO 표시
								$_pname = addslashes(strip_tags($v['p_name']));

								// 이미지 검사
								$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
								if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

								if($v['p_type']=='ticket'){
									$plink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
								}else{
									$plink = '_product.form.php?_mode=modify&_code='.$v['p_code'].'';
								}

						?>
							<tr>
								<td class="this_check">
									<label class="design">
										<input type="checkbox" name="chk_pcode[]" class="js_ck chk_box best-pcode" value="<?php echo $v['p_code']; ?>">
									</label>
								</td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td class="this_state">
									<?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')]; ?>
								</td>
								<td>
									<div class="order_item_thumb type_simple">
										<div class="thumb">
											<a href="<?php echo $plink;?>" title="<?php echo $_pname; ?>" target="_blank"><img src="<?php echo $_p_img; ?>" alt="<?php echo $_pname; ?>"></a>
										</div>
										<div class="order_item">
											<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){?>
												<div class='entershop'><?php echo showCompanyInfo($v['p_cpid']); ?></div>
											<?php }?>
											<dl>
												<dt><div class="item_name"><a href="<?php echo $plink;?>" title="<?php echo $_pname;?>" target="_blank"><?php echo $_pname; ?></a></div></dt>
												<dt class="t_light"><?php echo $v['p_code']; ?></dt>
											</dl>
										</div>
										<div class="other_tag">
											<?php echo $arr_adm_button[$v['p_type']]; ?>
											<?php
												if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
												else{
													if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
													else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
													else{  echo '<span class="c_tag light t4">판매종료</span>'; }
												}
											?>
										</div>
									</div>
								</td>
								<td class="t_red t_right t_bold this_price"><?php echo number_format($v['p_price']); ?>원</td>
								<td class="this_ctrl">
									<?php if($v['p_stock']>0){?>
										<span class="hidden_tx">재고량</span><?php echo number_format($v['p_stock']); ?>개
									<?php }else{?>
										<span class="t_none">품절</span>
									<?php }?>
								</td>
							</tr>
						<?php
							}
						}
						?>
					</tbody>
				</table>
				<?php if(count($res) <= 0) { ?>
					<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
				<?php } ?>

				<div class="paginate">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
				</div>
			</div>
		</div><!-- end pop_conts -->

		<div class="c_btnbox type_full">
			<ul>
				<li><span class="c_btn h34 blue"><input type="button" value="선택상품 추가" onclick="bestProductAdd();"  /></span></li>
				<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
			</ul>
		</div>
		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping">
				<div class="c_btnbox type_full">
					<ul>
						<li><span class="c_btn h34 blue"><input type="button" value="선택상품 추가" onclick="bestProductAdd();" /></span></li>
						<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal" accesskey="x">창닫기</a></li>
					</ul>
				</div>
			</div>
		</div>

	</form>

</div>


<script>
	// -- 베스트상품 선택적용 클릭할 시
	function bestProductAdd()
	{
		var cuid = $('[name="bestProductForm"] [name="cuid"]').val();
			var chkLen = $('.best-pcode:checked').length;
			if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }
			var selectVar = $('.best-pcode:checked').serialize();
			var url = '_category.ajax_pro.php';
	    $.ajax({
	        url: url, cache: false,dataType : 'json', type: "POST",
	        data: {_mode : 'selectBestProductAdd' , selectVar : selectVar , cuid : cuid },
	        success: function(data){
	        	if(data.rst == 'success'){
	        		opener.selectBestProductList(); // 다시 로드
	        		window.close();
	        	}else{
	        		alert(data.msg);
	        		return false;
	        	}
	        },error:function(request,status,error){ $('.console-log-view').html(request.responseText); }
	    });
	    return false;
	}
</script>

<?php include_once("inc.footer.php"); ?>