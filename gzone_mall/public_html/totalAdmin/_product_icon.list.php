<?php
	include_once('wrap.header.php');

	$_PVS = ''; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

	// 상품아이콘에 자동적용아이콘 추가
	$arr_product_icon_type2 = array_merge($arr_product_icon_type_auto, $arr_product_icon_type);


	// 검색 체크
	$s_query = " where 1 ";
	// if( $pass_type !="" ) { $s_query .= " and pi_type='${pass_type}' "; }
	if( $pass_type == 'auto'){ $s_query .= " and pi_type in('".implode("','",array_keys($arr_product_icon_type_auto))."')  ";  }
	else if($pass_type == 'select'){ $s_query .= " and pi_type in('".implode("','",array_keys($arr_product_icon_type))."')  ";  }
	if( $pass_title !="" ) { $s_query .= " and pi_title like '%${pass_title}%' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'pi_idx';
	if(!$so) $so = 'asc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_product_icon $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_product_icon {$s_query} order by if(pi_type='product_name_small_icon',1,2) asc,  {$st} {$so} , pi_uid desc limit $count , $listmaxcount ");
?>


    <form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" autocomplete="off" class="data_search">
        <input type="hidden" name="mode" value="search">
        <input type="hidden" name="st" value="<?php echo $st; ?>">
        <input type="hidden" name="so" value="<?php echo $so; ?>">
        <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($mode == 'search'){ ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>
				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">도움말</a>
                <a href="_product_icon.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">아이콘 등록</a>
            </div>
        </div>

		<div class="js_thisview" style="display:none">
			<table class="table_form">
				<colgroup>
					<col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>참고사항</th>
						<td>
							<?php echo _DescStr('자동적용 아이콘 : 상품 설정 및 상태에 따라 자동으로 적용되며, 삭제는 불가하고 수정만 가능합니다. 자동적용 아이콘의 종류는 아래 4가지 입니다.',''); ?>
							<?php echo _DescStr('티켓상품 : 상품타입 중 배송상품이 아닌 티켓상품인 경우',''); ?>
							<?php echo _DescStr('기획전 적용상품 : 기획전에 등록된 상품일 경우',''); ?>
							<?php echo _DescStr('상품 할인쿠폰 : 상품 개별 할인쿠폰이 등록된 경우',''); ?>
							<?php echo _DescStr('무료배송 : 배송비 설정이 무료배송으로 적용된 경우',''); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _DescStr('선택적용 아이콘 : 운영방침에 따라 자유롭게 추가, 수정삭제가 가능한 아이콘입니다.',''); ?>
							<?php echo _DescStr('선택적용 아이콘은 자동적용되지 않으므로 상품관리에서 원하는 상품에 아이콘 적용체크를 수동으로 해주셔야 합니다.',''); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

        <div class="search_form">
			<table class="table_form">
				<colgroup>
					<col width="130"><col width="*"><col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>구분</th>
						<td>
							<?php echo _InputRadio( 'pass_type' , array('','auto','select') , $pass_type , "" , array('전체','자동적용','선택적용') , ''); ?>
						</td>
						<th>아이콘명</th>
						<td><input type="text" name="pass_title" class="design" style="" value="<?php echo $pass_title; ?>" placeholder="아이콘명"></td>
					</tr>
				</tbody>
			</table>

			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
        </div>

    </form><!-- end data_search -->



	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<form name="frm" method="post" action="_product_icon.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn black line">선택삭제</a>
				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="80">
					<col width="130">
					<col width="*">
					<col width="100"><col width="120">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">순위</th>
						<th scope="col">아이콘 정보</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?PHP
					if(count($res) >  0) {
						foreach($res as $k=>$v) {

							$_mod = '<a href="#none" onclick="location.href=(\'_product_icon.form.php' . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['pi_uid'], '_PVSC'=>$_PVSC)) .'\');" class="c_btn gray ">수정</a>';
							$_del = '<a href="#none" onclick="del(\'_product_icon.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['pi_uid'], '_PVSC'=>$_PVSC)) .'\');" class="c_btn h22 dark line">삭제</a>';
							if(!in_array($v['pi_type'],array_keys($arr_product_icon_type))){
								$_del = '<a href="#none" onclick="alert(\'자동적용 쿠폰은 삭제가 불가합니다.\')"; return false;" class="c_btn h22 light line">삭제</a>';
							}

							$_num = $TotalCount - $count - $k ;

							// 이미지 검사
							if($v['pi_view_type']=='img'){
								// 이미지 검사
								if($v['pi_img_m'] && file_exists('..'.IMG_DIR_ICON . $v['pi_img_m'])){
									$app_icon = '<img src="'. get_img_src($v['pi_img_m'], IMG_DIR_ICON) .'" alt="'. addslashes(strip_tags($v['pi_title'])) .'"" class="icon">';
								}else{
									$app_icon = ''. _DescStr('미등록') .'';
								}
							}else{
								// 상품아이콘
								$app_icon = '<span class="item_icon" style="background-color:'.$v['pi_bg_color'].'; color:'.$v['pi_text_color'].'; border-color:'.$v['pi_line_color'].';">'.addslashes(strip_tags($v['pi_title'])).'</span>';
							}

					?>
							<tr>
								<td class="this_check">
									<label class="design"><input type="checkbox" name="chk_uid[]" <?php echo (!in_array($v['pi_type'],array_keys($arr_product_icon_type)) ? ' disabled ' : ' class="js_ck" ')?> value="<?php echo $v['pi_uid']; ?>"></label>
								</td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td class="this_state">
									<?php if(in_array($v['pi_type'],array_keys($arr_product_icon_type))){ ?>
										<div class="lineup-updown_ctrl">
											<div class="ctrl_form">
												<input type="text" name="" value="<?php echo $v['pi_idx']; ?>" class="design number_style js_sort_uid_<?php echo $v['pi_uid']; ?>" placeholder="">
												<a href="#none" onclick="sort_index('<?php echo $v['pi_uid']; ?>'); return false;" class="c_btn blue">수정</a>
											</div>
										</div>
									<?php }else{ echo '<span class="c_tag blue line">자동적용</span>'; } ?>
								</td>
								<td class="t_blue">
									<div class="list_data_box">
										<div class="text_info">
											<dl>
												<dt class="t_blue"><?php echo $arr_product_icon_type2[$v['pi_type']]; ?></dt>
												<dd><?php echo stripslashes($v['pi_title']); ?></dd>
											</dl>
										</div>
										<div class="side"><?php echo $app_icon; ?></div>
									</div>
								</td>
								<td class="this_date">
									<?php if(in_array($v['pi_type'],array_keys($arr_product_icon_type))){
											echo date('Y-m-d' , strtotime($v['pi_rdate']));
										}else{
											echo '<span class="t_none">삭제불가</span>';
										}
									?>
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


			<?php if(count($res) < 1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>
		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(":checked")){
				if(confirm('정말 삭제하시겠습니까?')){
					$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
					$('form[name=frm]').attr('action' , '_product_icon.pro.php');
					document.frm.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}

		// 순위수정
		function sort_index(_uid){
			var _idx = $('.js_sort_uid_' + _uid).val();
			if( _idx == ''){ alert('순위를 입력해 주시기 바랍니다.'); return false; }
			if( _idx*1 <= 0){ alert('순위는 1이상 입력해 주시기 바랍니다.');  return false; }
			if(_uid && _idx){
				document.location.href = '_product_icon.pro.php?_mode=sort&_uid=' + _uid +'&_idx=' + _idx;
			}
		}
	</script>


<?php include_once('wrap.footer.php'); ?>