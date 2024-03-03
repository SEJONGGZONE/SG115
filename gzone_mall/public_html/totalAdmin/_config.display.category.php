<?php
	include_once('wrap.header.php');

	/*
		# DB :: smart_setup
		- s_main_best_view
		- s_main_best_title
		- s_main_best_limit
		- s_main_best_order
	*/
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

	$arrProDisplay['arrMainName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);

	$main_display_type = $siteInfo['s_main_best_display_type']!=''?$siteInfo['s_main_best_display_type']:'box';

	// 기본 값
	$main_default = $siteInfo['s_main_best_display']>0?$siteInfo['s_main_best_display']:$tempSkinInfo['pc_pro_depth_default'];						// 메인 상품진열 pc
	$main_mo_default = $siteInfo['s_main_best_mobile_display']>0?$siteInfo['s_main_best_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];	// 메인 상품진열 모바일
	$main_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_default.'단형';
	$main_mo_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_mo_default.'단형';

	$arrProDisplay['arrMainNameMo'] = $arrProDisplay['arrDepthNameMo'][$main_display_type];


?>

<form name="formDisplayCategory" action="_config.display.category.pro.php" method="POST">
	<input type="hidden" name="main_view_type" value="<?php echo $main_display_type;?>">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>노출여부</th>
					<td colspan="3">
						<?php echo _InputRadio('s_main_best_view', array('Y','N'),($siteInfo['s_main_best_view']!=''?$siteInfo['s_main_best_view']:'Y') , ' class="s_main_best_view"', array('노출','숨김'), ''); ?>
						<?php echo _DescStr('카테고리에서 베스트로 선택된 상품이 있을 경우 메인에서의 베스트 상품을 모아볼 수 있는 영역에 대해 노출을 설정합니다.',''); ?>
						<?php echo _DescStr('설정된 상품이 하나도 없을 경우 설정에 관계없이 무조건 비노출됩니다.',''); ?>
						<?php echo _DescStr('카테고리에서 노출되는 베스트 상품은 카테고리에서 설정할 수 있습니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">타이틀</th>
					<td>
						<input type="text" name="s_main_best_title" class="design" value="<?php echo $siteInfo['s_main_best_title']; ?>" style="width:50%;" placeholder="메인 타이틀">
						<?php echo _DescStr('메인 카테고리 베스트 영역에 노출되는 타이틀을 설정합니다.',''); ?>
					</td>
					<th  class="ess">최대노출</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="s_main_best_limit" class="design bold t_black number_style" value="<?php echo $siteInfo['s_main_best_limit'];?>" style="width:70px;">
							<div class="fr_tx">개</div>
						</div>
						<?php echo _DescStr('사이트 접속 시 로딩 속도를 고려하여 적당한 개수로 설정해주세요. (최대 100개)',''); ?>
					</td>
				</tr>
				<tr>
					<th>상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( 's_main_best_display' , array_values($arrProDisplay['arrMainName']) , $main_default , ' class="js_main_best_pc" ' , array_values($arrProDisplay['arrMainName']) , 'PC 상품진열');
								echo _InputSelect( 's_main_best_mobile_display' , array_values($arrProDisplay['arrMainNameMo']) , $main_mo_default , ' class="js_main_best_mo" ' , array_values($arrProDisplay['arrMainNameMo']) , '반응형 상품진열');
							?>
							<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<?php echo _DescStr('1줄에 설정한 개수가 초과할 경우 슬라이드(롤링)기능이 적용됩니다.','blue'); ?>

						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
					<th>상품 정렬순서</th>
					<td>
						<?php echo _InputRadio('s_main_best_order', array('date','recent','sales','random'),($siteInfo['s_main_best_order']!=''?$siteInfo['s_main_best_order']:'date') , ' class="s_main_best_order"', array('오래된 등록순','최신 등록순','판매순','랜덤'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('오래된 등록순 : 가장 오래전에 등록된 상품이 먼저 노출됩니다.',''); ?>
						<?php echo _DescStr('최신 등록순 : 가장 최근에 등록된 상품이 먼저 노출됩니다.',''); ?>
						<?php echo _DescStr('판매순 : 판매량이 많은 상품이 먼저 노출됩니다.',''); ?>
						<?php echo _DescStr('랜덤 : 사이트 접속 또는 새로고침 시마다 다른 상품이 노출됩니다.',''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">

	$(document).ready(function(){
		CateMainBestSelect('reload');
	});

	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_main_best_pc', CateMainBestSelect);
	function CateMainBestSelect(chk_mode) {
		var _main_view_pc = $('.js_main_best_pc').find('option:selected').val();
		var _main_view_pcval = '<?php echo $main_default;?>';
		var _main_view_moval = '<?php echo $main_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';

		if(_main_view_pc!=''){
			if(chk_mode=='change'){
				_main_view_moval='';
			}
			$('.js_main_best_pc').find('option[value=""]').prop('disabled',true);
		}

		$.ajax({
			data: {
				_mode: 'pcMainSelect',
				_main_view_pc: _main_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_config.display.category.pro.php',
			success: function(data) {
				if(data == '') data = null;
				var result = $.parseJSON(data);
				var _option;
				if(_main_view_moval!='' && chk_mode=='reload'){
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
						_option += '<option value="'+option_title+'" '+((_main_view_moval!='' && _main_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
					});
					$('.js_main_best_mo').show();
				}
				$('.js_main_best_mo').html(_option);
				$('input[name="main_view_type"]').val(result.type);
				return result;
			}
		});
	}


	// 메인 카테고리 베스트 모바일
	$(document).on('change', '.js_main_best_mo', CateMainBestSelectMo);
	function CateMainBestSelectMo() {
		var _main_view_pc = $('.js_main_best_pc').find('option:selected').val();
		var _main_view_mo = $('.js_main_best_mo').find('option:selected').val();

		if(_main_view_mo!='' && _main_view_pc!=''){
			$('.js_main_best_mo').find('option[value=""]').prop('disabled',true);
		}
	}


	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=formDisplayCategory]").validate({
			ignore: ".ignore",
			rules: {
					s_main_best_title: { required: true }
			},
			messages: {
					s_main_best_title : { required: '타이틀을 입력해주시기 바랍니다.' }
			},
			invalidHandler: function(event, validator) {
				
				console.log(event);
				return false;
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
			},
			submitHandler : function(form) {

				var chk_main_best_limit = $('input[name="s_main_best_limit"]').val();				

				if( chk_main_best_limit == undefined || chk_main_best_limit == '' || chk_main_best_limit<=0 ){
					alert("최대노출 개수는 1개 이상 입력해주시기 바랍니다.");
					$('input[name="s_main_best_limit"]').focus();
					return false;
				}

				if( chk_main_best_limit == undefined || chk_main_best_limit == '' || chk_main_best_limit > 100 ){
					alert("최대 노출개수는 100개 이하로 입력해주시기 바랍니다.");
					$('input[name="s_main_best_limit"]').focus();
					return false;
				}
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
	});

</script>
<?php include_once('wrap.footer.php');?>