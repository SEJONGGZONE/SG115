<?php
	// SSJ : 2017-12-20 상품 상세페이지 노출 설정
	include_once('wrap.header.php');

	// 추가 노출항목 설정 추출
	$s_display_pinfo_add = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add'])));
	$s_display_pinfo_add = (count($arrDisplayPinfo1) > 1 ? $s_display_pinfo_add : $siteInfo['s_display_pinfo_add']);

	// 추가 노출항목 설정 추출
	$s_display_pinfo_add_info = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add_info'])));
	$s_display_pinfo_add_info = (count($arrDisplayPinfo2) > 1 ? $s_display_pinfo_add_info : $siteInfo['s_display_pinfo_add_info']);

?>


<form name="frm" method="post" action="_config.display.pinfo.pro.php" enctype="multipart/form-data" >
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value='<?php echo $_uid; ?>'>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">

	<?php
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

		$list_display_type = $siteInfo['s_relation_display_type']!=''?$siteInfo['s_relation_display_type']:'box';

		// 기본 값
		$list_default = $siteInfo['s_relation_display']>0?$siteInfo['s_relation_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
		$list_mo_default = $siteInfo['s_relation_mobile_display']>0?$siteInfo['s_relation_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
		$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
		$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';

		$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];
	?>

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출 항목 설정</th>
					<td colspan="3">
						<?php echo _InputCheckBox('s_display_pinfo_add', array_keys($arrDisplayPinfo1), $s_display_pinfo_add, '', array_values($arrDisplayPinfo1)); ?>
						<div class="dash_line"></div>
						<?php echo _InputCheckBox('s_display_pinfo_add_info', array_keys($arrDisplayPinfo2), $s_display_pinfo_add_info, '', array_values($arrDisplayPinfo2)); ?>
						<div class="dash_line"></div>
						<?php echo _DescStr('상세페이지의 노출되는 항목중 일부를 숨김처리 할 수 있습니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th>상세내용 접어두기 기능</th>
					<td colspan="3">
						<?php echo _InputRadio('s_display_content_open', array('Y', 'N'), $siteInfo['s_display_content_open']!=''?$siteInfo['s_display_content_open']:'N', '', array('사용', '미사용')); ?>
						<?php echo _DescStr('상세페이지의 상세내용이 너무 길 경우 처음에 내용을 접어둘 수 있는 기능입니다.',''); ?>
						<?php echo _DescStr('사용자가 더보기 클릭 시 전체 내용이 펼쳐집니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th>관련상품 노출</th>
					<td>
						<?php echo _InputRadio('s_display_relation_mo_use', array('Y', 'N'), $siteInfo['s_display_relation_mo_use'], '', array('노출', '숨김')); ?>
						<?php echo _DescStr('상품별로 상세페이지 하단에 관련상품을 노출 설정할 수 있습니다.',''); ?>
					</td>
					<th>관련상품 진열설정</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( 's_relation_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_relation_view_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
								echo _InputSelect( 's_relation_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , ' class="js_relation_view_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
							?>
							<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<?php echo _DescStr('1줄에 설정한 개수가 초과할 경우 슬라이드(롤링)기능이 적용됩니다.','blue'); ?>
						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<?php echo _submitBTNsub(); ?>

</form>

<script>

	$(document).ready(function(){
		RelationSelect('reload');
	});

	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_relation_view_pc', RelationSelect);
	function RelationSelect(chk_mode) {
		var _relation_view_pc = $('.js_relation_view_pc').find('option:selected').val();
		var _relation_view_pcval = '<?php echo $list_default;?>';
		var _relation_view_moval = '<?php echo $list_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';

		if(_relation_view_pc!=''){
			if(chk_mode=='change'){
				_relation_view_moval='';
			}
			$('.js_relation_view_pc').find('option[value=""]').prop('disabled',true);
		}

		$.ajax({
			data: {
				_mode: 'pcListSelect',
				_relation_view_pc: _relation_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_config.display.pinfo.pro.php',
			success: function(data) {
				if(data == '') data = null;
				var result = $.parseJSON(data);
				var _option;
				if(_relation_view_moval!='' && chk_mode=='reload'){
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
						_option += '<option value="'+option_title+'" '+((_relation_view_moval!='' && _relation_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
					});
					$('.js_relation_view_mo').show();
				}
				$('.js_relation_view_mo').html(_option);
				$('input[name="list_view_type"]').val(result.type);
				return result;
			}
		});
	}


	// 메인 상품진열 설정 모바일 클릭시
	$(document).on('change', '.js_relation_view_mo', RelationSelectMo);
	function RelationSelectMo() {
		var _relation_view_pc = $('.js_relation_view_pc').find('option:selected').val();
		var _relation_view_mo = $('.js_relation_view_mo').find('option:selected').val();

		if(_relation_view_mo!='' && _relation_view_pc!=''){
			$('.js_relation_view_mo').find('option[value=""]').prop('disabled',true);
		}
	}
</script>

<?php include_once('wrap.footer.php'); ?>