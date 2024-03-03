<?php
	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

?>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php
		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.  // // LCY : 2022-12-21 : 티켓기능 -- delivery 추가
		$s_query = " from smart_product as p where 1 ";

		if($AdminPath == 'subAdmin') { $s_query .= " and p.p_cpid = '{$com_id}' "; }

		$pass_type = 'delivery';
		$search_detail_name = 'delivery';
		include_once("_product.inc_search.php");
		//	==> s_query 리턴됨.

		if(!$listmaxcount) $listmaxcount = 20;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'p_rdate';
		if(!$so) $so = 'desc';
		$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$res = _MQ_assoc(" select p.* $s_query order by {$st} {$so} limit $count , $listmaxcount ");
	?>



	<div class="data_list">

		<form name="frm" method="post" action="" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
					<?php if($AdminPath == 'totalAdmin'){ ?>
					<a href="#none" onclick="selectSortModify(); return false;" class="c_btn h27 blue">선택 순위수정</a>
					<?php } ?>
				</div>
				<div class="right_box">
					<a href="#none" onclick="downloadExcel('select'); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
					<a href="#none" onclick="downloadExcel('search'); return false;" class="c_btn icon icon_excel only_pc_view">검색 엑셀다운 (<?php echo number_format($TotalCount); ?>)</a>
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'asc'?' selected':null); ?>>상품명 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'desc'?' selected':null); ?>>상품명 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'asc'?' selected':null); ?>>판매가 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'desc'?' selected':null); ?>>판매가 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'asc'?' selected':null); ?>>재고량 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'desc'?' selected':null); ?>>재고량 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_salecnt', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_salecnt' && $so == 'asc'?' selected':null); ?>>판매량 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_salecnt', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_salecnt' && $so == 'desc'?' selected':null); ?>>판매량 ▼</option>
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
					<col width="40"><col width="70">
					<?php if($AdminPath == 'totalAdmin'){ ?><col width="120"><?php } ?>
					<col width="140">
					<col width="*"><col width="100"><col width="100"><col width="100"><col width="110">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<?php if($AdminPath == 'totalAdmin'){ ?>
						<th scope="col">순위</th>
						<?php } ?>
						<th scope="col">노출/판매</th>
						<th scope="col">상품정보</th>
						<th scope="col">판매가</th>
						<th scope="col">재고량</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					 if(sizeof($res) > 0){
						foreach($res as $k=>$v){

							$_mod = '<a href="_product.form.php?_mode=modify&_code=' . $v['p_code'] . '&_PVSC=' . $_PVSC . '" class="c_btn gray">수정</a>';
							$_del = '<a href="#none" onclick="del(\'_product.pro.php?_mode=delete&_code=' . $v['p_code'] . '&_PVSC=' . $_PVSC . '\');" class="c_btn h22 dark line">삭제</a>';
							$preview = '<a href="#none" onclick="window.open(\'/?pn=product.view&pcode='.$v['p_code'].'\')" class="c_btn h22 sky line">미리보기</a>';

							$_num = $TotalCount - $count - $k ;

							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
							if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

							$time_sale_sdate = date('m월 d일',strtotime($v['p_time_sale_sdate']));
							$time_sale_sclock = '('.date('H:i',strtotime($v['p_time_sale_sclock'])).')';

							$time_sale_edate = date('m월 d일',strtotime($v['p_time_sale_edate']));
							$time_sale_eclock = '('.date('H:i',strtotime($v['p_time_sale_eclock'])).')';


							// 에디터 이미지 사용개수 체크
							$edit_img_cnt = edit_img_cnt($v['p_code'],'product');
					?>
							<tr>
								<td class="this_check">
									<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['p_code']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td class="this_num">
									<?php echo $_num; ?>
								</td>
								<?php if($AdminPath == 'totalAdmin'){ ?>
								<td class="this_updown">
									<div class="lineup-updown_ctrl">
										<div class="ctrl_form">
											<input type="text" name="sort_group[<?php echo $v['p_code']; ?>]" value="<?php echo $v['p_sort_group']?>" class="design number_style sort_group_<?php echo $v['p_code']; ?>" placeholder="0">
											<a href="#none" onclick="sort_group('<?php echo $v['p_code']; ?>')" class="c_btn gray ">수정</a>
										</div>
										<div class="ctrl_btn">
											<a href="#none" onclick="sort_up('<?php echo $v['p_code']; ?>','up','<?php echo enc('e' ,$s_query); ?>')" class="c_btn h22 icon_up" title="위로"></a>
											<a href="#none" onclick="sort_up('<?php echo $v['p_code']; ?>','down','<?php echo enc('e' ,$s_query); ?>')" class="c_btn h22 icon_down" title="아래로"></a>
											<a href="#none" onclick="sort_up('<?php echo $v['p_code']; ?>','top','<?php echo enc('e' ,$s_query); ?>')" class="c_btn h22 icon_top" title="맨위로"></a>
											<a href="#none" onclick="sort_up('<?php echo $v['p_code']; ?>','bottom','<?php echo enc('e' ,$s_query); ?>')" class="c_btn h22 icon_bottom" title="맨아래로"></a>
										</div>
									</div>
								</td>
								<?php } ?>
								<td class="this_state">
									<div class="lineup-row type_center">
										<?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')]; ?>
										<?php
											if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
											else{
												if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
												else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
												else{  echo '<span class="c_tag light t4">판매종료</span>'; }
											}
										?>
									</div>
								</td>
								<td class="this_item t_left">
									<div class="order_item_thumb type_simple">
										<div class="thumb">
											<?php if($v['p_option_valid_chk']<>'Y'){?>
												<div class="error">옵션 오류</div>
											<?php } ?>
											<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>" >
										</div>
										<div class="order_item">
											<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){?>
												<div class='entershop'><?php echo showCompanyInfo($v['p_cpid']); ?></div>
											<?php }?>
											<dl>
												<dt>
													<div class="item_name"><?php echo strip_tags($v['p_name']); ?></div>
												</dt>
												<dt class="t_light"><?php echo $v['p_code']; ?></dt>
												<dt>
													<?php
														// JJC ::: 브랜드관리 ::: 2017-11-03
														echo ($arr_brand[$v['p_brand']] ? "<span class='item_brand'>Brand : ".$arr_brand[$v['p_brand']] . "</span>" : "") ;
													?>
												</dt>
											</dl>
											<?php if($v['p_time_sale']=='Y'){?>
												<div class="item_timesale">
													<strong>타임세일</strong>
													<em><?php echo $time_sale_sdate.' '.$time_sale_sclock.' ~ '.$time_sale_edate.' '.$time_sale_eclock;?></em>
												</div>
											<?php }?>

											<?php if($v['p_sale_type']=='T'){?>
												<div class="item_timesale type_limited">
													<strong>기간판매</strong>
													<em><?php echo $v['p_sale_sdate']; ?> ~ <?php echo $v['p_sale_edate']; ?></em>
												</div>
											<?php }?>
										</div>
									</div>

									<div class="lineup-row">
										<div class="dash_line"><!-- 점선라인 --></div>
										<?php echo $preview; ?>
										<?php if($AdminPath == 'totalAdmin' && $edit_img_cnt['cnt']>0) {?>
											<a href="#none" onclick="edit_img_pop('<?php echo $v['p_code'] ?>')" class="c_btn h22 gray line t6">이미지 관리</a>
										<?php } ?>
									</div>
								</td>
								<td class="t_red t_right t_bold this_price">
									<?php echo number_format($v['p_price']); ?>원
								</td>
								<td>
									<div class="lineup-column type_reverse">
										<?php if( $v['p_stock'] > 0){?>
											<span class="hidden_tx">재고량</span><?php echo number_format($v['p_stock']); ?>개
											<?php if( $v['p_salecnt'] > 0){?>
												<span class="t_sky t_11">(<?php echo number_format($v['p_salecnt']);?>개 판매)</span>
											<?php }?>
										<?php }else{ ?>
											<span class="t_none">품절</span>

											<?php if( $v['p_salecnt'] > 0){?>
												<span class="t_sky t_11">(<?php echo number_format($v['p_salecnt']);?>개 판매)</span>
											<?php }?>

										<?php }?>
									</div>
								</td>
								<td>
									<span class="hidden_tx">등록일</span><?php echo printDateInfo($v['p_rdate']); ?>
								</td>
								<td class="this_ctrl">
									<div class="lineup-row type_center">
										<?php echo $_mod; ?>
										<?php echo $_del; ?>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>

			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>

		// --- KAY : 상품일괄업로드 개선 : 2021-07-02 ---
		// 상품 일괄업로드 폼 열기/닫기
		$(document).on("click" , ".js_open_excel_box", function(){
			window.open('_product.upload.pop.php?','reqinfo','width=1120,height=600,scrollbars=yes');
		});
		// --- KAY : 상품일괄업로드 개선 : 2021-07-02 ---

		// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
		function edit_img_pop(_uid, table='product'){
			window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
		}

		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(":checked")){
				if(confirm("정말 삭제하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
					$("form[name=frm]").attr("action" , "_product.pro.php");
					document.frm.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}

		// 순위조정 up-down-top-bottom
		function sort_up(pcode,mode,query) {
			<?php if($st  == 'p_idx' && $so == 'asc' ){ ?>
				common_frame.location.href='_product.sort.php?pcode='+pcode+'&_mode='+mode+'&query='+query;
			<?php }else{ ?>
				alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
			<?php } ?>
		}

		// 순위그룹 수정
		function sort_group(pcode){
			var group = $('.sort_group_'+ pcode).val()*1;
			if(group <= 0){
				alert('상품 순위를 입력해 주시기 바랍니다.');
				$('.sort_group_'+ pcode).focus();
				return false;
			}
			common_frame.location.href='_product.sort.php?pcode='+pcode+'&_mode=modify_group&_group='+group;
		}
		// 선택순위그룹 수정
		function selectSortModify() {
			if($('.js_ck').is(':checked')){
				$('form[name=frm]').attr({'action':'_product.sort.php' , 'target':'common_frame'});
				$('input[name=_mode]').val('mass_sort');
				document.frm.submit();
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
		// 선택엑셀 다운로드
		function downloadExcel(_mode){
			if(_mode == 'select' && $('.js_ck').is(":checked") === false){
				alert('1개 이상 선택해 주시기 바랍니다.');
				return false;
			}

			$("form[name=frm]").children("input[name=_mode]").val(_mode);
			$("form[name=frm]").attr("action" , "_product.download.php");
			$("form[name=frm]").attr("target" , "_self");
			document.frm.submit();
			return true;
		}
		// 검색엑셀 다운로드


	</script>


<?php include_once('wrap.footer.php'); ?>