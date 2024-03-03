<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");
		$printParent = '';
		if($row['dms_depth'] > 1){
			$arrParent = explode(",",$row['dms_parent']);
			$rowDepth1 = _MQ("select dms_name, dms_uid from smart_display_main_set where dms_depth = '1' and dms_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dms_name']."</span> ";
		}
		if($row['dms_depth'] > 2){
			$rowDepth2 = _MQ("select dms_name , dms_uid from smart_display_main_set where dms_depth = '2' and dms_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dms_name']."</span> ";
		}
		$_depth = $row['dms_depth'];
	}else{
		$printParent = '';
		if($_depth > 1){
			$rowDepth1 = _MQ("select dms_name, dms_uid from smart_display_main_set where dms_depth = '1' and dms_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dms_name']."</span> ";
		}
		if($_depth > 2){
			$rowDepth2 = _MQ("select dms_name, dms_uid from smart_display_main_set where dms_depth = '2' and dms_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dms_name']."</span> ";
		}
	}


	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');  // 기존규칙은 상품의 정렬을 따른다.

	// -- 메인 :: 스킨에 따른 단수처리
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

	if($_depth > 1){

		// 메인 md's pick 인 경우
		if($locUid1=='1'){

			$arrProDisplay['arrMainName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);
			$main_display_type = $row['dms_main_product_display_type']!=''?$row['dms_main_product_display_type']:'box';

			// 기본 값
			$main_default = $row['dms_main_product_display']>0?$row['dms_main_product_display']:$tempSkinInfo['pc_pro_depth_default'];						// 메인 상품진열 pc
			$main_mo_default = $row['dms_main_product_mobile_display']>0?$row['dms_main_product_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];	// 메인 상품진열 모바일
			$main_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_default.'단형';
			$main_mo_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_mo_default.'단형';

			$arrProDisplay['arrMainNameMo'] = $arrProDisplay['arrDepthNameMo'][$main_display_type];
		}

		$arrProDisplay['arrListName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);
		$list_display_type = $row['dms_list_product_display_type']!=''?$row['dms_list_product_display_type']:'box';

		// 기본 값
		$list_default = $row['dms_list_product_display']>0?$row['dms_list_product_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
		$list_mo_default = $row['dms_list_product_mobile_display']>0?$row['dms_list_product_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
		$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
		$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';

		$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];
	}



?>

	<form name="formDisplayMain">
		<input type="hidden" name="_uid" value="<?php echo $_uid?>">
		<input type="hidden" name="_mode" value="<?php echo $_mode?>">
		<input type="hidden" name="_depth" value="<?php echo $_depth?>">
		<input type="hidden" name="locUid1" value="<?php echo $locUid1?>">
		<input type="hidden" name="locUid2" value="<?php echo $locUid2?>">
		<?php if($_mode=='modify' && $_depth < 2 && $_uid=='2'){?>
			<input type="hidden" name="_name" value="<?php echo $row['dms_name']?>">
		<?php }?>
		<input type="hidden" name="main_view_type" value="<?php echo $main_display_type;?>">
		<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">

		<div class="group_title"><strong>선택 분류 <?php echo $_mode == 'add' ? '추가':'수정' ?></strong></div>
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>분류명</th>
						<td>
							<?php if($_depth > 1) {?>
								<div class="category_upper"><span class="fr_bullet">상위 분류</span><?php echo $printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="분류명" value="<?php echo $row['dms_name']?>" style="width:240px" <?php echo $_uid=='2' && $_depth=='1'?'disabled':'';?> />
							<?php if($_depth < 2 && $_uid=='2') {?>
								<?php echo _DescStr('이 분류명은 사용자페이지에서 노출되지 않으며 수정불가합니다. (세부 분류를 설정해주세요.)',''); ?>
							<?php } ?>
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?php echo !$row['dms_view'] || $row['dms_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?php echo $row['dms_view'] == 'N' ? 'checked':''?>>숨김</label>
							<?php if($_depth < 2) {?>
								<?php echo _DescStr('세부 분류 설정에 상관없이 모두 동일하게 설정됩니다.',''); ?>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<?php if($_depth > 1){  ?>
			<!-- ● 단락타이틀 -->
			<div class="group_title"><strong>상품 설정</strong></div>

			<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
			<div class="data_form">
				<table class="table_form">
					<colgroup>
						<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<th>상품 노출</th>
							<td>
								<?php echo _InputRadio( "_list_product_mobile_view" , array('Y' , 'N'), (!$row['dms_list_product_mobile_view'] ? "N" : $row['dms_list_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>
								<?php echo _DescStr('아래 선택된 상품이 한개도 없을 경우 해당분류는 노출되지 않습니다.',''); ?>
							</td>
							<th>메인 노출 최대</th>
							<td>
								<div class="lineup-row">
									<input type="text" name="_list_product_mobile_limit" class="design bold t_black number_style" value="<?php echo $row['dms_list_product_mobile_limit'];?>" style="width:70px;">
									<div class="fr_tx">개</div>
								</div>
								<?php echo _DescStr('사이트 접속 시 로딩 속도를 고려하여 적당한 개수로 설정해주세요. (최대 100개)',''); ?>
							</td>
						</tr>
						<?php if($_depth > 1) {?>
							<tr>
								<?php if($rowDepth1['dms_uid'] == 1){?>
									<th>메인 상품진열</th>
									<td>
										<div class="lineup-row type_responsive">
											<?php
												echo _InputSelect( '_main_product_display' , array_values($arrProDisplay['arrMainName']) , $main_default , ' class="js_pro_main_pc" ' , array_values($arrProDisplay['arrMainName']) , 'PC 상품진열');
												echo _InputSelect( '_main_product_mobile_display' , array_values($arrProDisplay['arrMainNameMo']) , $main_mo_default , ' class="js_pro_main_mo" ' , array_values($arrProDisplay['arrMainMoName']) , '반응형 상품진열');
											?>
										</div>
										<?php echo _DescStr('1줄에 설정한 개수가 초과할 경우 슬라이드(롤링)기능이 적용됩니다.','blue'); ?>
									</td>
								<?php }?>
								<th>목록 상품진열</th>
								<td>
									<div class="lineup-row type_responsive">
										<?php
											echo _InputSelect( '_list_product_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_pro_list_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
											echo _InputSelect( '_list_product_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , ' class="js_pro_list_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
										?>
									</div>
								</td>
							</tr>
						<?php }?>
						<tr>
							<th>참고사항</th>
							<td colspan="3">
								<?php echo _DescStr('상품진열은 첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">상품 진열설정 도움말</a>
								<? include_once("help.item_setting.php"); //단수도움말 ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>



			<div class="group_title" data-name="view-main"><strong>선택된 상품</strong></div>
			<div class="data_list select-main-product-list">
			</div>
		<?php } ?>

	</form>

	<div class="c_btnbox type_full">
		<ul>
			<li><a href="#none" onclick="configDisplayMain.saveItem(); return false;" class="c_btn h46 red" accesskey="s">저장</a></li>
			<?php if($_mode != 'add' && $_depth != 1 && ( $row['dms_parent'] != '1' || $varMdAuth === true) ) { ?>
				<li><a href="#none" onclick="configDisplayMain.deleteItem('<?=$row['dms_uid']?>');" class="c_btn h46 black line">삭제</a></li>
			<?php } ?>
		</ul>
	</div>


	<div class="fixed_save js_fixed_save" style="display:none;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="configDisplayMain.saveItem(); return false;" class="c_btn h34 red" accesskey="s">저장</a></li>
				<?php if($_mode != 'add' && $_depth != 1 && ( $row['dms_parent'] != '1' || $varMdAuth === true) ){ ?>
					<li><a href="#none" onclick="configDisplayMain.deleteItem('<?=$row['dms_uid']?>');" class="c_btn h34 black line">삭제</a></li>
				<?php } ?>
			</ul>
			</div>
		</div>
	</div>



	<script>

		$(function(){
			MainListSelect('reload');
			MainSelect('reload');
		});

		// 상품 진열 설정 클릭시 [목록 - pc]
		$(document).on('change', '.js_pro_list_pc', MainListSelect);
		function MainListSelect(chk_mode) {
			var _pro_list_pc = $('.js_pro_list_pc').find('option:selected').val();
			var _pro_list_pcval = '<?php echo $list_default;?>';
			var _pro_list_moval = '<?php echo $list_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_pro_list_pc!=''){
				if(chk_mode=='change'){
					_pro_list_moval='';
				}
				$('.js_pro_list_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcListSelect',
					_pro_list_pc: _pro_list_pc
				},
				type: 'POST',
				cache: false,
				url: '_config.display.main.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_pro_list_moval!='' && chk_mode=='reload'){
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
							_option += '<option value="'+option_title+'" '+( (_pro_list_moval!='' && _pro_list_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_pro_list_mo').show();
					}
					$('.js_pro_list_mo').html(_option);
					$('input[name="list_view_type"]').val(result.type);
					return result;
				}
			});
		}

		// 상품 진열 설정 클릭시 [목록 - 모바일]
		$(document).on('change', '.js_pro_list_mo', MainListSelectMo);
		function MainListSelectMo() {
			var _list_view_pc = $('.js_pro_list_pc').find('option:selected').val();
			var _list_view_mo = $('.js_pro_list_mo').find('option:selected').val();

			if(_list_view_mo!='' && _list_view_pc!=''){
				$('.js_pro_list_mo').find('option[value=""]').prop('disabled',true);
			}
		}



		// 상품 진열 설정 클릭시 [메인 - pc]
		$(document).on('change', '.js_pro_main_pc', MainSelect);
		function MainSelect(chk_mode) {
			var _pro_main_pc = $('.js_pro_main_pc').find('option:selected').val();
			var _pro_main_pcval = '<?php echo $main_default;?>';
			var _pro_main_moval = '<?php echo $main_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_pro_main_pc!=''){
				if(chk_mode=='change'){
					_pro_main_moval='';
				}
				$('.js_pro_main_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcMainSelect',
					_pro_main_pc: _pro_main_pc
				},
				type: 'POST',
				cache: false,
				url: '_config.display.main.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_pro_main_moval!='' && chk_mode=='reload'){
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
							_option += '<option value="'+option_title+'" '+( (_pro_main_moval!='' && _pro_main_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_pro_main_mo').show();
					}
					$('.js_pro_main_mo').html(_option);
					$('input[name="main_view_type"]').val(result.type);
					return result;
				}
			});
		}

		// 상품 진열 설정 클릭시 [메인 - 모바일]
		$(document).on('change', '.js_pro_main_mo', MainSelectMo);
		function MainSelectMo() {
			var _main_view_pc = $('.js_pro_main_pc').find('option:selected').val();
			var _main_view_mo = $('.js_pro_main_mo').find('option:selected').val();

			if(_main_view_mo!='' && _main_view_pc!=''){
				$('.js_pro_main_mo').find('option[value=""]').prop('disabled',true);
			}
		}



		<?php if($_depth == 2){ // AJAX데이터 서포트 { ?>
			$(function(){
				selectMainProductList(); // 선택된 카테고리 베스트 상품 가져오기
			});
		<?php }  // AJAX데이터 서포트 } ?>

	</script>