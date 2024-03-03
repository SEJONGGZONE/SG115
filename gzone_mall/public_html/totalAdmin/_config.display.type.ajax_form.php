<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$row = _MQ("select *from smart_display_type_set where dts_uid = '".$_uid."' ");

		$printParent = '';
		if($row['dts_depth'] > 1){
			$arrParent = explode(",",$row['dts_parent']);
			$rowDepth1 = _MQ("select dts_name from smart_display_type_set where dts_depth = '1' and dts_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dts_name']."</span> ";
		}

		if($row['dts_depth'] > 2){
			$rowDepth2 = _MQ("select dts_name from smart_display_type_set where dts_depth = '2' and dts_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dts_name']."</span> ";
		}
		$_depth = $row['dts_depth'];
	}else{
		$printParent = '';
		if($_depth > 1){
			$rowDepth1 = _MQ("select dts_name from smart_display_type_set where dts_depth = '1' and dts_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dts_name']."</span> ";
		}

		if($_depth > 2){
			$rowDepth2 = _MQ("select dts_name from smart_display_type_set where dts_depth = '2' and dts_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dts_name']."</span> ";
		}
	}

	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');

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

	$arrProDisplay['arrListName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);	

	$list_display_type = $row['s_list_timesale_display_type']!=''?$row['s_list_timesale_display_type']:'box';

	// 기본 값
	$list_default = $row['dts_list_product_display']>0?$row['dts_list_product_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
	$list_mo_default = $row['dts_list_product_mobile_display']>0?$row['dts_list_product_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
	$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
	$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';


	$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];




	// -- 타입 url
	$typeInUrl = '/?pn=product.list&_event=type&typeuid='.$row['dts_uid'];
	$typeFullUrl = $system['url'].$typeInUrl;

?>

	<form name="formDisplayType">
		<input type="hidden" name="_uid" value="<?=$_uid?>">
		<input type="hidden" name="_mode" value="<?=$_mode?>">
		<input type="hidden" name="_depth" value="<?=$_depth?>">
		<input type="hidden" name="locUid1" value="<?=$locUid1?>">
		<input type="hidden" name="locUid2" value="<?=$locUid2?>">
		<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">

		<div class="group_title"><strong>선택 분류 <?=$_mode == 'add' ? '추가':'수정' ?></strong><?=openMenualLink('선택타입설정')?></div>

		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<?php if($_depth > 1) {?>
					<?php }?>
					<tr>
						<th>분류명</th>
						<td>
							<?php if($_depth > 1) {?>
								<!-- 상위카테고리가 있을 경우 -->
								<div class="category_upper"><span class="fr_bullet">상위 분류</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="분류명" value="<?=$row['dts_name']?>" style="width:240px" />
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$row['dts_view'] || $row['dts_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$row['dts_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>

					<?php if($_depth > 1) {?>
						<?php if($row['dts_uid'] != ''){ ?>
							<tr>
								<th>페이지 주소</th>
								<td colspan="3">
									<div class="lineup-row">
										<a href="<?php echo $typeFullUrl?>" class="c_btn sky line h22" target="_blank" >미리보기</a>
										<a href="#none" data-clipboard-text="<?php echo $typeFullUrl; ?>" class="c_btn gray line line js-clipboard" onclick="return false;">절대경로 주소복사</a>
										<a href="#none" data-clipboard-text="<?php echo $typeInUrl; ?>" class="c_btn gray line  js-clipboard" onclick="return false;">상대경로 주소복사</a>

									</div>
									<div class="c_tip">절대경로 : <?php echo $typeFullUrl?></div>
									<div class="c_tip">상대경로 : <?php echo $typeInUrl?></div>
								</td>
							</tr>
						<?php }?>
						<?php if( $tempSkinInfo['pc_image_width'] !='off'){ ?>
							<tr>
								<th>상단 배너</th>
								<td colspan="3">
									<?php echo _InputRadio( "_img_top_banner_use" , array('Y' , 'N'), (!$row['dts_img_top_banner_use'] ? "N" : $row['dts_img_top_banner_use']) , "" , array('사용','미사용') , ""); ?>

										<div class="lineup-row js_img_top_banner_useY"<?php echo $row['dts_img_top_banner_use'] != 'Y' ? ' style="display:none;"':'' ?>>
											<?php echo _PhotoForm('../upfiles/category', '_img_top_banner', $row['dts_img_top_banner'], 'style="width:280px"'); ?>
											<?php echo _DescStr('권장사이즈 : '.($tempSkinInfo['pc_image_width']).' × Free (pixel)'); ?>
										</div>
										<div class="dash_line js_img_top_banner_useY"<?php echo $row['dts_img_top_banner_use'] != 'Y' ? ' style="display:none;"':'' ?>><!-- 점선라인 --></div>

										<div class="js_img_top_banner_useY"<?php echo $row['dts_img_top_banner_use'] != 'Y' ? ' style="display:none;"':'' ?>>
										<?php echo _InputRadio( "_img_top_banner_target" , array('_none', '_self' , '_blank'), (!$row['dts_img_top_banner_target'] ? "_self" : $row['dts_img_top_banner_target']) , " class='' " , array('링크없음','같은창','새창') , ""); ?>
										</div>
										<input type="text" name="_img_top_banner_link" class="design" placeholder="링크 주소" value="<?=$row['dts_img_top_banner_link']?>" style="width:425px;<?php echo $row['dts_img_top_banner_target'] == '_none' || $row['dts_img_top_banner_use'] != 'Y' ? 'display:none;':'' ?>" autocomplete="off" />

								</td>
							</tr>
						<?php } ?>
					<?php } ?>

				</tbody>
			</table>
		</div>


		<?php if($_depth > 1) {  ?>
			<div class="group_title"><strong>타입별 상품설정</strong><!-- 메뉴얼로 링크 --><?php echo openMenualLink('타입별상품진열설정')?></div>
			<div class="data_form">
				<table class="table_form">
					<colgroup>
						<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
					</colgroup>
					<tbody>
						<tr>
							<th>상품 노출</th>
							<td>
								<?php echo _InputRadio( "_list_product_mobile_view" , array('Y' , 'N'), (!$row['dts_list_product_mobile_view'] ? "N" : $row['dts_list_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>
							</td>

							<th>상품 진열 설정</th>
							<td>
								<div class="lineup-row type_responsive">
									<?php
										echo _InputSelect( '_list_product_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_list_type_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
										echo _InputSelect( '_list_product_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , ' class="js_list_type_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
									?>
									<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
								</div>
								<? include_once("help.item_setting.php"); //단수도움말 ?>
							</td>

						</tr>
						<tr>
							<th>목록 페이지당 상품 개수</th>
							<td colspan="3">
								<?php echo _InputRadio('_list_limit', array(20,30,40,50,60,80,100), ($row['dts_list_limit'] ?$row['dts_list_limit']:20) , "" ,array('20개','30개','40개','50개','60개','80개','100개'), "");  ?>
								<?php echo _DescStr('선택한 상품진열 단수에 따라 알맞게 설정해주세요.',''); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>


			<!-- ● 단락타이틀 -->
			<div class="group_title" data-name="view-type"><strong>선택된 상품</strong><!-- 메뉴얼로 링크 --></div>
			<!-- ● 데이터 리스트 -->
			<div class="data_list select-type-product-list">

			</div>

		<?php } ?>

		</form>

		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="configDisplayType.saveItem(); return false;" class="c_btn h46 red">저장</a></li>
				<?php if($_depth > 1){?>
				<li><a href="#none" onclick="configDisplayType.deleteItem('<?php echo $row['dts_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php }?>
			</ul>
		</div>

		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping">
				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox type_full">
				<ul>
					<li><a href="#none" onclick="configDisplayType.saveItem(); return false;" class="c_btn h34 red">저장</a></li>
					<?php if($_depth > 1){?>
					<li><a href="#none" onclick="configDisplayType.deleteItem('<?php echo $row['dts_uid']?>');" class="c_btn h34 black line">삭제</a></li>
					<?php } ?>
				</ul>
				</div>
			</div>
		</div>


	<script>

		var categoryForm = {};

		// -- 초기화
		categoryForm.init = function()
		{
			// -- 배너링크에 따른 처리
			var chk = $('[name="_img_top_banner_target"]:checked').val();
			var top_banner_type = $('[name="_img_top_banner_use"]:checked').val();

			$('.js_img_top_banner_useY').hide();
			$('input[name="_img_top_banner_target"]').closest('label').hide();
			$('[name="_img_top_banner_link"]').hide();
			if(top_banner_type == 'Y'){
				$('.js_img_top_banner_useY').show();
				$('input[name="_img_top_banner_target"]').closest('label').show();
				if( chk != '_none'){$('[name="_img_top_banner_link"]').show(); }
			}
		}

		$(document).ready(function(){
			categoryForm.init();
			TypeListSelect('reload');
		});

		// -- 배너링크에 따른 처리
		$(document).on('click' , '[name="_img_top_banner_use"]', categoryForm.init );
		$(document).on('click','[name="_img_top_banner_target"]',categoryForm.init);


		$(document).on('change', '.js_list_type_pc', TypeListSelect);
		function TypeListSelect(chk_mode) {
			var _list_type_pc = $('.js_list_type_pc').find('option:selected').val();
			var _list_type_pcval = '<?php echo $list_default;?>';
			var _list_type_moval = '<?php echo $list_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_list_type_pc!=''){
				if(chk_mode=='change'){
					_list_type_moval='';
				}
				$('.js_list_type_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcSelect',
					_list_type_pc: _list_type_pc
				},
				type: 'POST',
				cache: false,
				url: '_config.display.type.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_list_type_moval!='' && chk_mode=='reload'){
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
							_option += '<option value="'+option_title+'" '+( (_list_type_moval!='' && _list_type_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_list_type_mo').show();
					}
					$('.js_list_type_mo').html(_option);
					$('input[name="list_view_type"]').val(result.type);
					return result;
				}
			});
		}

		// 베스트 상품 상품 진열 설정 클릭시
		$(document).on('change', '.js_list_type_mo', TypeListSelectMo);
		function TypeListSelectMo() {
			var _list_type_pc = $('.js_list_type_pc').find('option:selected').val();
			var _list_type_mo = $('.js_list_type_mo').find('option:selected').val();

			if(_list_type_mo!='' && _list_type_pc!=''){
				$('.js_list_type_mo').find('option[value=""]').prop('disabled',true);
			}
		}

		<?php if($_depth == 2){ // AJAX데이터 서포트 { ?>
			$(function(){
				selectTypeProductList(); // 선택된 카테고리 베스트 상품 가져오기
			});
		<?php } // AJAX데이터 서포트 } ?>

	</script>

