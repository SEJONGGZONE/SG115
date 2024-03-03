<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$rowCategory = _MQ("select *from smart_category where c_uid = '".$_uid."' ");

		$printParent = '';
		if($rowCategory['c_depth'] > 1){
			$arrParent = explode(",",$rowCategory['c_parent']);
			$rowCategoryDepth1 = _MQ("select c_name from smart_category where c_depth = '1' and c_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowCategoryDepth1['c_name']."</span> ";
		}

		if($rowCategory['c_depth'] > 2){
			$rowCategoryDepth2 = _MQ("select c_name from smart_category where c_depth = '2' and c_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowCategoryDepth2['c_name']."</span> ";
		}

		$_depth = $rowCategory['c_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowCategoryDepth1 = _MQ("select c_name from smart_category where c_depth = '1' and c_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowCategoryDepth1['c_name']."</span> ";
		}

		if($_depth > 2){
			$rowCategoryDepth2 = _MQ("select c_name from smart_category where c_depth = '2' and c_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowCategoryDepth2['c_name']."</span> ";
		}
	}


	// -- 카테고리 전체 url
	$categoryInUrl = '/?pn=product.list&cuid='.$rowCategory['c_uid'];
	$categoryFullUrl = $system['url'].$categoryInUrl;


	// -- 리스트 :: 스킨에 따른 단수처리
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
	$arrProDisplay['arrBestName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);


	$list_display_type = $rowCategory['c_list_product_display_type']!=''?$rowCategory['c_list_product_display_type']:'box';
	$best_display_type = $rowCategory['c_best_product_display_type']!=''?$rowCategory['c_best_product_display_type']:'box';

	// 기본 값
	$best_default = $rowCategory['c_best_product_display']>0?$rowCategory['c_best_product_display']:$tempSkinInfo['pc_pro_depth_default'];						// 메인 상품진열 pc
	$best_mo_default = $rowCategory['c_best_product_mobile_display']>0?$rowCategory['c_best_product_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];	// 메인 상품진열 모바일
	$best_default = ($best_display_type=='box'?'박스 ':'리스트 ').$best_default.'단형';
	$best_mo_default = ($best_display_type=='box'?'박스 ':'리스트 ').$best_mo_default.'단형';


	$list_default = $rowCategory['c_list_product_display']>0?$rowCategory['c_list_product_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
	$list_mo_default = $rowCategory['c_list_product_mobile_display']>0?$rowCategory['c_list_product_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
	$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
	$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';


	$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];
	$arrProDisplay['arrBestNameMo'] = $arrProDisplay['arrDepthNameMo'][$best_display_type];






?>

<form name="formCategory">
	<input type="hidden" name="_uid" value="<?php echo $_uid?>">
	<input type="hidden" name="_mode" value="<?php echo $_mode?>">
	<input type="hidden" name="_depth" value="<?php echo $_depth?>">
	<input type="hidden" name="locUid1" value="<?php echo $locUid1?>">
	<input type="hidden" name="locUid2" value="<?php echo $locUid2?>">
	<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">
	<input type="hidden" name="best_view_type" value="<?php echo $best_display_type;?>">


	<div class="group_title"><strong>선택 카테고리 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">카테고리명</th>
					<td>
						<?php if($_depth > 1) {?>
							<div class="category_upper"><span class="fr_bullet">상위 카테고리</span><?php echo $printParent?></div>
						<?php } ?>
						<input type="text" name="_name" class="design bold t_black" placeholder="카테고리명" value="<?php echo $rowCategory['c_name']?>" style="width:100%" />
					</td>
					<th>카테고리 노출</th>
					<td>
						<label class="design"><input type="radio" value="Y" name="_view" <?php echo !$rowCategory['c_view'] || $rowCategory['c_view'] == 'Y' ? 'checked':''?>>노출</label>
						<label class="design"><input type="radio" value="N" name="_view" <?php echo $rowCategory['c_view'] == 'N' ? 'checked':''?>>숨김</label>
						<?php echo _DescStr('상품노출과는 별도로 카테고리 메뉴 노출여부를 설정합니다. 숨김 시 접근이 불가합니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th>상단 배너</th>
					<td colspan="3">
						<?php echo _InputRadio( "_img_top_mobile_banner_use" , array('Y' , 'N'), (!$rowCategory['c_img_top_mobile_banner_use'] ? "N" : $rowCategory['c_img_top_mobile_banner_use']) , "" , array('사용','미사용') , ""); ?>
						<div class="lineup-row cls_topbanner_wrap">
							<?php echo _PhotoForm('../upfiles/category', '_img_top_mobile_banner', $rowCategory['c_img_top_mobile_banner'], 'style="width:280px"'); ?>
							<?php echo _DescStr('이미지 권장사이즈 : '.($tempSkinInfo['pc_image_width']).' × Free'); ?>
						</div>
						<div class="dash_line cls_topbanner_wrap"><!-- 점선라인 --></div>
						<?php echo _InputRadio( "_img_top_mobile_banner_target" , array('_none', '_self' , '_blank'), (!$rowCategory['c_img_top_mobile_banner_target'] ? "_self" : $rowCategory['c_img_top_mobile_banner_target']) , "" , array('링크없음','같은창','새창') , ""); ?>
						<input type="text" name="_img_top_mobile_banner_link" class="design" placeholder="링크 주소" value="<?php echo $rowCategory['c_img_top_mobile_banner_link']?>" style="width:425px;<?php echo $rowCategory['c_img_top_mobile_banner_target']=='_none'?'display:none;':'';?>" />
					</td>
				</tr>
				<?php if($rowCategory['c_uid'] != ''){ ?>
					<tr>
						<th>페이지 주소</th>
						<td colspan="3">
							<div class="lineup-row">
								<a href="<?php echo $categoryFullUrl?>" class="c_btn sky line h22" target="_blank" >미리보기</a>
								<a href="#none" data-clipboard-text="<?php echo $categoryFullUrl; ?>" class="c_btn gray line line js-clipboard" onclick="return false;">절대경로 주소복사</a>
								<a href="#none" data-clipboard-text="<?php echo $categoryInUrl; ?>" class="c_btn gray line  js-clipboard" onclick="return false;">상대경로 주소복사</a>

							</div>
							<div class="c_tip">절대경로 : <?php echo $categoryFullUrl?></div>
							<div class="c_tip">상대경로 : <?php echo $categoryInUrl?></div>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>상품 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>목록 상품노출</th>
					<td>
						<?php echo _InputRadio( "_list_product_mobile_view" , array('Y' , 'N'), (!$rowCategory['c_list_product_mobile_view'] ? "Y" : $rowCategory['c_list_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>
						<?php if($_depth < 3){ ?>
							<label class="design"><input type="checkbox" name="_list_product_mobile_all" class="chk-alert" value="Y">하위 카테고리 동일 적용</label>
						<?php } ?>
						<?php echo _DescStr('카테고리 클릭 시 상품목록의 노출여부를 설정합니다.',''); ?>
					</td>
					<th>목록 상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( '_list_product_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_list_view_pc"   ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
								echo _InputSelect( '_list_product_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , ' class="js_list_view_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
							?>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
					</td>
				</tr>
				<?php if($_depth==1){?>
					<tr>
						<th>목록 페이지당 상품 개수</th>
						<td colspan="3">
							<?php echo _InputRadio('_list_limit', array(20,30,40,50,60,80,100), ($rowCategory['c_list_limit'] ?$rowCategory['c_list_limit']:20) , "" ,array('20개','30개','40개','50개','60개','80개','100개'), "");  ?>
							<?php echo _DescStr('선택한 상품진열 단수에 따라 알맞게 설정해주세요.',''); ?>
						</td>
					</tr>
				<?php }?>
				<tr>
					<th>베스트 상품노출</th>
					<td>
						<?php echo _InputRadio( "_best_product_mobile_view" , array('Y' , 'N'), (!$rowCategory['c_best_product_mobile_view'] ? "Y" : $rowCategory['c_best_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>
						<?php if($_depth < 3){ ?>
							<label class="design left20"><input type="checkbox" name="_best_product_mobile_all" class="chk-alert" value="Y">하위 카테고리 동일 적용</label>
						<?php } ?>
						<?php echo _DescStr('카테고리 목록 상단에 베스트 상품 노출여부를 설정합니다.',''); ?>
					</td>
					<th>베스트 상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( '_best_product_display' , array_values($arrProDisplay['arrBestName']) , $best_default , ' class="js_best_view_pc" ' , array_values($arrProDisplay['arrBestName']) , 'PC 상품진열');
								echo _InputSelect( '_best_product_mobile_display' , array_values($arrProDisplay['arrBestNameMo']) , $best_mo_default , ' class="js_best_view_mo" ' , array_values($arrProDisplay['arrBestNameMo']) , '반응형 상품진열');
							?>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<?php echo _DescStr('1줄에 설정한 개수가 초과할 경우 슬라이드(롤링)기능이 적용됩니다.','blue'); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">상품 진열설정 도움말</a>
						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php if($_mode=='modify'){?>
		<div class="group_title" data-name="view-best">
			<strong>선택된 베스트 상품</strong>
			<div class="btn_box">

			</div>
		</div>
		<div class="data_list select-best-product-list"></div>
	<?php }?>

</form>

		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="saveCategory(); return false;" class="c_btn h46 red">저장</a></li>
				<?php if($_mode != 'add') { ?>
					<li><a href="#none" onclick="deleteCategory('<?php echo $rowCategory['c_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php } ?>
			</ul>
		</div>

		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping">
				<div class="c_btnbox type_full">
					<ul>
						<li><a href="#none" onclick="saveCategory(); return false;" accesskey="s" class="c_btn h34 red">저장</a></li>
						<?php if($_mode != 'add') { ?>
							<li><a href="#none" onclick="deleteCategory('<?php echo $rowCategory['c_uid']?>');" class="c_btn h34 black line">삭제</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>


	<script>

		var categoryForm = {};

		// -- 튤팁 따로 선언
		$('img.js_thumb_img').tooltip({
			show: null, hide: null,
			items: 'img.js_thumb_img[data-img]',
			content: function(e) {
				if(!$(this).data('img')) return;
				return '<img src="'+$(this).data('img')+'" alt="" />';
			}
		});


		// -- 초기화
		categoryForm.init = function()
		{
			// -- 배너링크에 따른 처리
			var chk = $('[name="_img_top_mobile_banner_target"]:checked').val();
			var top_banner_type = $('[name="_img_top_mobile_banner_use"]:checked').val();

			$('.cls_topbanner_wrap').hide();
			$('input[name="_img_top_mobile_banner_target"]').closest('label').hide();
			$('[name="_img_top_mobile_banner_link"]').hide();
			if(top_banner_type == 'Y'){
				$('.cls_topbanner_wrap').show();
				$('input[name="_img_top_mobile_banner_target"]').closest('label').show();
				if( chk != '_none'){$('[name="_img_top_mobile_banner_link"]').show(); }
			}
		}

	
		$(document).ready(function(){
			CateListSelect('reload');
			CateBestSelect('reload');
			categoryForm.init();
		});

		// -- 배너링크에 따른 처리
		$(document).on('click' , '[name="_img_top_mobile_banner_use"]', categoryForm.init );
		$(document).on('click','[name="_img_top_mobile_banner_target"]',categoryForm.init);




		// 상품 진열 설정 클릭시
		$(document).on('change', '.js_list_view_pc', CateListSelect);
		function CateListSelect(chk_mode) {
			var _list_view_pc = $('.js_list_view_pc').find('option:selected').val();
			var _list_view_pcval = '<?php echo $list_default_val;?>';
			var _list_view_moval = '<?php echo $list_mo_default_val;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_list_view_pc!=''){
				if(chk_mode=='change'){
					_list_view_moval='';
				}
				$('.js_list_view_pc').find('option[value=""]').prop('disabled',true);
			}


			$.ajax({
				data: {
					_mode: 'pcSelect',
					_list_view_pc: _list_view_pc
				},
				type: 'POST',
				cache: false,
				url: '_category.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_list_view_moval!='' && chk_mode=='reload'){
						_option = '<option value="" disabled>반응형 상품진열</option>';
					}else{
						_option = '<option value="">반응형 상품진열</option>';
					}
					if(result && result!=null) {
						$.each(result, function(k, v) {
							if(v>0){
								var option_title = '박스 '+v+'단형';
							}else{
								var option_title = '리스트 1단형';
							}
							_option += '<option value="'+v+'" '+( (_list_view_moval!='' && _list_view_moval===v) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_list_view_mo').show();
					}
					$('.js_list_view_mo').html(_option);
					return result;
				}
			});
		}


		// 상품 진열 설정 클릭시
		$(document).on('change', '.js_list_view_pc', CateListSelect);
		function CateListSelect(chk_mode) {
			var _list_view_pc = $('.js_list_view_pc').find('option:selected').val();
			var _list_view_pcval = '<?php echo $list_default;?>';
			var _list_view_moval = '<?php echo $list_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_list_view_pc!=''){
				if(chk_mode=='change'){
					_list_view_moval='';
				}
				$('.js_list_view_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcListSelect',
					_list_view_pc: _list_view_pc
				},
				type: 'POST',
				cache: false,
				url: '_category.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_list_view_moval!='' && chk_mode=='reload'){
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
							_option += '<option value="'+option_title+'" '+((_list_view_moval!='' && _list_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_list_view_mo').show();
					}
					$('.js_list_view_mo').html(_option);
					$('input[name="list_view_type"]').val(result.type);
					return result;
				}
			});
		}


		// 메인 상품진열 설정 모바일 클릭시
		$(document).on('change', '.js_list_view_mo', CateListSelectMo);
		function CateListSelectMo() {
			var _list_view_pc = $('.js_list_view_pc').find('option:selected').val();
			var _list_view_mo = $('.js_list_view_mo').find('option:selected').val();

			if(_list_view_mo!='' && _list_view_pc!=''){
				$('.js_list_view_mo').find('option[value=""]').prop('disabled',true);
			}
		}




		// 상품 진열 설정 클릭시
		$(document).on('change', '.js_best_view_pc', CateBestSelect);
		function CateBestSelect(chk_mode) {
			var _best_view_pc = $('.js_best_view_pc').find('option:selected').val();
			var _best_view_pcval = '<?php echo $best_default;?>';
			var _best_view_moval = '<?php echo $best_mo_default;?>';

			chk_mode = chk_mode=='reload'?'reload':'change';

			if(_best_view_pc!=''){
				if(chk_mode=='change'){
					_best_view_moval='';
				}
				$('.js_best_view_pc').find('option[value=""]').prop('disabled',true);
			}

			$.ajax({
				data: {
					_mode: 'pcBestSelect',
					_best_view_pc: _best_view_pc
				},
				type: 'POST',
				cache: false,
				url: '_category.ajax_pro.php',
				success: function(data) {
					if(data == '') data = null;
					var result = $.parseJSON(data);
					var _option;
					if(_best_view_moval!='' && chk_mode=='reload'){
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
							_option += '<option value="'+option_title+'" '+((_best_view_moval!='' && _best_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
						});
						$('.js_best_view_mo').show();
					}
					$('.js_best_view_mo').html(_option);
					$('input[name="best_view_type"]').val(result.type);
					return result;
				}
			});
		}


		// 메인 상품진열 설정 모바일 클릭시
		$(document).on('change', '.js_best_view_mo', CateListSelectMo);
		function CateBestSelectMo() {
			var _best_view_pc = $('.js_best_view_pc').find('option:selected').val();
			var _best_view_mo = $('.js_best_view_mo').find('option:selected').val();

			if(_best_view_mo!='' && _best_view_pc!=''){
				$('.js_best_view_mo').find('option[value=""]').prop('disabled',true);
			}
		}




		<?php if($_mode != 'add'){?>
		// AJAX데이터 서포트
		$(function(){
			selectBestProductList(); // 선택된 카테고리 베스트 상품 가져오기
		});
		<?php } ?>
	</script>
