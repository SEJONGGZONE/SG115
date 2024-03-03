<?php

	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {if(is_array($val)) {foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }}else {$_PVS .= "&$key=$val";}}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기



	######## 검색 체크
	$pass_sort = $pass_sort ? $pass_sort : 'rdate_desc';
	$pass_limit = $pass_limit ? $pass_limit : 20;

	$s_query = " from smart_promotion_plan where 1 ";

	if( $pass_title !="" ) { $s_query .= " and pp_title like '%${pass_title}%' "; }
	if( $pass_view !="" ) { $s_query .= " and pp_view='${pass_view}' "; }

	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(pp_rdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 진행기간
	else if( $pass_sdate ) { $s_query .= " AND left(pp_rdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(pp_rdate,10) <= '". $pass_edate ."' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'p_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$s_orderby = "order by pp_uid desc";
	switch($pass_sort){
		case "title_asc": $s_orderby = "ORDER BY pp_title ASC, pp_uid desc"; break;//기획전명순↑
		case "title_desc": $s_orderby = "ORDER BY pp_title desc, pp_uid desc"; break;//기획전명순↓
		case "rdate_asc": $s_orderby = "ORDER BY pp_rdate asc"; break;//등록일순↑
		case "rdate_desc": $s_orderby = "ORDER BY pp_rdate desc"; break;//등록일순↓
	}

	$res = _MQ_assoc(" SELECT * $s_query $s_orderby  LIMIT $count , $listmaxcount ");

	$tempSkinInfo = SkinInfo('category');

	// -- 리스트 :: 스킨에 따른 단수처리
	$arrProDisplay['arrDepth']['box'] = explode(",",$tempSkinInfo['pc_pro_box_depth']);	// pc 박스형
	$arrProDisplay['arrDepth']['list'] = explode(",",$tempSkinInfo['pc_pro_list_depth']);	// pc 리스트형
	$arrProDisplay['arrDepthMo']['box'] = explode(",",$tempSkinInfo['mo_pro_box_depth']);	//모바일 박스형
	$arrProDisplay['arrDepthMo']['list'] = explode(",",$tempSkinInfo['mo_pro_list_depth']);	// 모바일 리스트형

	foreach($arrProDisplay['arrDepth'] as $apdk => $apdv){
		foreach($apdv as $apdkk=>$apdvv){
			$arrProDisplay['arrDepthName'][$apdk][$apdvv] = $apdk=='box' ? $tempSkinInfo['pro_depth_all_title']['pc_box_'.$apdvv]:$tempSkinInfo['pro_depth_all_title']['pc_list_'.$apdvv];
		}
	}

	foreach($arrProDisplay['arrDepthMo'] as $admk => $admv){
		foreach($admv as $admkk=>$admvv){
			$arrProDisplay['arrDepthNameMo'][$admk][$admvv] =  $admk=='box' ? $tempSkinInfo['pro_depth_all_title']['mo_box_'.$admvv]:$tempSkinInfo['pro_depth_all_title']['mo_list_'.$admvv];
		}
	}

	$arrProDisplay['arrDepth']['box'][]='list_none';
	$arrProDisplay['arrDepthName']['box'][] = '===========';

	$arrProDisplay['arrListName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);	

	$list_display_type = $siteInfo['s_promotion_display_type']!=''?$siteInfo['s_promotion_display_type']:'box';

	// 기본 값
	$list_default = $siteInfo['s_promotion_display']>0?$siteInfo['s_promotion_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
	$list_mo_default = $siteInfo['s_promotion_mobile_display']>0?$siteInfo['s_promotion_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
	$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
	$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';

	$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];
?>



	<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search tab_conts" data-idx="search">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

		<!-- ● 단락타이틀 -->
		<div class="group_title">
			<strong>Search</strong>
			<div class="btn_box">
				<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
				<?php if($mode == 'search'){ ?>
					<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
				<?php } ?>

				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">설정하기</a>
				<a href="_promotion_plan.form.php?_mode=add" class="c_btn h46 red" accesskey="a">기획전등록</a>
			</div>
		</div>

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<div class="search_form">
			<table class="table_form">
				<colgroup>
					<col width="130"/><col width="*"/><col width="130"/><col width="*"/><col width="130"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>기획전명</th>
						<td><input type="text" name="pass_title" class="design" style="" value="<?php echo $pass_title; ?>" placeholder="기획전명"></td>
						<th>진행기간</th>
						<td>
							<div class="lineup-row type_date">
								<input type="text" name="pass_sdate" value="<?php echo $pass_sdate?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택" readonly>
								<span class="fr_tx">-</span>
								<input type="text" name="pass_edate" value="<?php echo $pass_sdate?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택" readonly>
							</div>
						</td>
						<th>노출여부</th>
						<td><?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?></td>
					</tr>
				</tbody>
			</table>

			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form><!-- end data_search -->


    <!-- 환경 설정  -->
    <form name="promotionSet" action="_promotion_plan.pro.php" method="post" class="data_search js_thisview" style="display:none;" target="common_frame">
		<input type="hidden" name="_mode" value="config" />
		<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">

		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출 및 메뉴명</th>
					<td>
						<?php echo _InputRadio( "_promotion_plan_view" , array('Y','N'), $siteInfo['s_promotion_plan_view']?$siteInfo['s_promotion_plan_view']:'N' , '' , array('노출','숨김') ); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<input type="text" name="_promotion_plan_title" class="design" style="" value="<?php echo $siteInfo['s_promotion_plan_title']; ?>" placeholder="기획전 명칭">
					</td>
					<th>상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( '_promotion_display' , array_values($arrProDisplay['arrListName']) , $list_default, ' class="js_promotion_view_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
								echo _InputSelect( '_promotion_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , 'class="js_promotion_view_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
							?>
							<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 blue "><input type="submit" name="" value="설정저장"></span></li>
			</ul>
		</div>
    </form>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
			</div>
			<div class="right_box">

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_title', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pp_title' && $so == 'asc'?' selected':null); ?>>기획전명 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_title', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pp_title' && $so == 'desc'?' selected':null); ?>>기획전명 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pp_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pp_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>

			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->



		<form name="frm" method="post" action="_promotion_plan.mass_form.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="80"><col width="140">
					<col width="100"><col width="*"><col width="*"><col width="100"><col width="110"><col width="175">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">노출여부/진행상태</th>
						<th scope="col">목록이미지</th>
						<th scope="col">기획전명</th>
						<th scope="col">기획전기간</th>
						<th scope="col">등록상품</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if(sizeof($res) > 0){
						foreach($res as $k=>$v){

							$_mod = "<a href='_promotion_plan.form.php?_mode=modify&uid=" . $v['pp_uid'] . "&_PVSC=" . $_PVSC . "' class='c_btn h22 gray'>수정</a>";
							$_del = "<a href='#none' onclick='del(\"_promotion_plan.pro.php?_mode=delete&uid=" . $v['pp_uid'] . "&_PVSC=" . $_PVSC . "\");' class='c_btn h22 dark line'>삭제</a>";
							$_preview = "<a href='/?pn=product.promotion_view&uid=".$v['pp_uid']."' target='_blank' class='c_btn h22 sky line'>바로가기</a>";

							$_num = $TotalCount - $count - $k ;

							// 목록 이미지
							$_img = IMG_DIR_BANNER.$v['pp_img'];
							if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) $app_img = '<img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt="'.addslashes($_title).'">';
							else $app_img = '';

							// 진행상태
							$app_status = '';
							//종료후
							if($v['pp_edate']<DATE('Y-m-d')) {
								$app_status = '<span class="c_tag light h22 t4">진행종료</span>';// 종료문구
							}
							//시작전
							else if($v['pp_sdate']>DATE('Y-m-d')) {
								$app_status = '<span class="c_tag green line h22 t4">진행전</span>';
							}
							//진행중
							else {
								$app_status = '<span class="c_tag green h22 t4">진행중</span>';
							}

							// 상품갯수 추출
							$app_pcnt = _MQ_result("select count(*) as cnt from smart_promotion_plan_product_setup where ppps_ppuid = '". $v['pp_uid'] ."' ");

							// 에디터 이미지 사용개수 체크
							$edit_img_cnt = edit_img_cnt($v['pp_uid'],'promotion');

					?>
							<tr>
								<td class="this_check">
									<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['pp_uid']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td class="this_state"><div class="lineup-row type_center"><?php echo $arr_adm_button[($v['pp_view'] == 'Y' ? '노출' : '숨김')]; ?> <?php echo $app_status; ?></div></td>
								<td class="img80 img_full">
									<?php echo $app_img; ?>
								</td>
								<td class="t_left t_black">
									<?php echo stripslashes(strip_tags($v['pp_title'])); ?>
									<?php if($edit_img_cnt['cnt']>0){?>
										<div class="in_something">
											<a href="#none" onclick="edit_img_pop('<?php echo $v['pp_uid'] ?>')" class="c_btn h22 gray line">이미지 관리</a>
										</div>
									<?php }?>
								</td>
								<td class="t_sky"><?php echo date('Y-m-d' , strtotime($v['pp_sdate'])); ?> ~ <?php echo date('Y-m-d' , strtotime($v['pp_edate'])); ?></td>
								<td><?php echo $app_pcnt;?>개 상품</td>
								<td class="this_date"><?php echo printDateInfo($v['pp_rdate']); ?></td>
								<td class="this_ctrl">
                                    <div class="lineup-row type_center">
										<?php echo $_mod; ?>
										<?php echo $_del; ?>
										<?php echo $_preview; ?>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>

		</form>

		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>



	<script>

		// 검색 / 환경설정 텝메뉴 클릭 이벤트
		var trigger_cont_editor = true;
		$(document).on('click', '.tab_menu', function() {
			var idx = $(this).data('idx');
			// 탭변경
			$('.tab_menu').closest('li').removeClass('hit');
			$('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
			// 입력항목변경
			$('.tab_conts').hide();
			$('.tab_conts[data-idx='+ idx +']').show();
		});

		 // 선택삭제
		 function selectDelete() {
			 if($('.js_ck').is(":checked")){
				 if(confirm("정말 삭제하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
					$("form[name=frm]").attr("action" , "_promotion_plan.pro.php");
					document.frm.submit();
				 }
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
			$("form[name=frm]").attr("action" , "_promotion_plan.download.php");
			$("form[name=frm]").attr("target" , "_self");
			document.frm.submit();
			return true;
		}
		// 검색엑셀 다운로드

		// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
		function edit_img_pop(_uid, table='promotion'){
			window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=600,height=600,scrollbars=yes');
		}
		// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기

		$(document).ready(function(){
			PromotionListSelect('reload');
		});

		// 상품 진열 설정 클릭시
		$(document).on('change', '.js_promotion_view_pc', PromotionListSelect);
		function PromotionListSelect(chk_mode) {
			var _promotion_view_pc = $('.js_promotion_view_pc').find('option:selected').val();
			var _promotion_view_pcval = '<?php echo $list_default;?>';
			var _promotion_view_moval = '<?php echo $list_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_promotion_view_pc!=''){
				if(chk_mode=='change'){
					_promotion_view_moval='';
				}
				$('.js_promotion_view_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcListSelect',
					_promotion_view_pc: _promotion_view_pc
				},
				type: 'POST',
				cache: false,
				url: '_promotion_plan.pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_promotion_view_moval!='' && chk_mode=='reload'){
						_option = '<option value="" disabled>반응형 상품진열</option>';
					}else{
						_option = '<option value="">반응형 상품진열</option>';
					}
					if(result && result!=null) {
						$.each(result.name, function(k, v) {
							if(result.type=='box'){
								var option_title = '박스 '+v+'단형';
							}else{
								var option_title = '리스트 1단형';
							}
							_option += '<option value="'+option_title+'" '+((_promotion_view_moval!='' && _promotion_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_promotion_view_mo').show();
					}
					$('.js_promotion_view_mo').html(_option);
					$('input[name="list_view_type"]').val(result.type);
					return result;
				}
			});
		}

		// 상품 진열설정 모바일 클릭시
		$(document).on('change', '.js_promotion_view_mo', PromotionListSelectMo);
		function PromotionListSelectMo() {
			var _promotion_view_pc = $('.js_promotion_view_pc').find('option:selected').val();
			var _promotion_view_mo = $('.js_promotion_view_mo').find('option:selected').val();

			if(_promotion_view_mo!='' && _promotion_view_pc!=''){
				$('.js_promotion_view_mo').find('option[value=""]').prop('disabled',true);
			}
		}



		// 폼 유효성 검사
		$(document).ready(function(){
			$("form[name=promotionSet]").validate({
					ignore: ".ignore",
					rules: {
							_promotion_plan_view: { required: true }
							,_promotion_plan_title: { required: true }
							,_promotion_display: { required: true }
							,_promotion_mobile_display: { required: true }
					},
					invalidHandler: function(event, validator) {
						// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
					},
					messages: {
							_promotion_plan_view : { required: "기획전 노출여부를 선택해주시기 바랍니다." }
							, _promotion_plan_title: { required: "기획전 메뉴명을 입력해주시기 바랍니다."}
							, _promotion_display: { required: "상품진열 첫화면 설정을 선택해주시기 바랍니다."}
							,_promotion_mobile_display : { required: "상품진열 반응형 설정을 선택해주시기 바랍니다." }
					},
					submitHandler : function(form) {
						// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
						form.submit();
					}

			});
		});

	</script>


<?php
	include_once('wrap.footer.php');
?>
