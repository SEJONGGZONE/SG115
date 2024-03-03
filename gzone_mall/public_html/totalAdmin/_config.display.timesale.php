<?php
	include_once('wrap.header.php');

	/*
		# DB :: smart_setup
		- s_main_timesale_view
		- s_main_timesale_title
		- s_main_timesale_limit
		- s_main_timesale_order
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

	$arrProDisplay['arrListName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);	
	$arrProDisplay['arrMainName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);


	$list_display_type = $siteInfo['s_list_timesale_display_type']!=''?$siteInfo['s_list_timesale_display_type']:'box';
	$main_display_type = $siteInfo['s_main_timesale_display_type']!=''?$siteInfo['s_main_timesale_display_type']:'box';

	// 기본 값
	$main_default = $siteInfo['s_main_timesale_display']>0?$siteInfo['s_main_timesale_display']:$tempSkinInfo['pc_pro_depth_default'];						// 메인 상품진열 pc
	$main_mo_default = $siteInfo['s_main_timesale_mobile_display']>0?$siteInfo['s_main_timesale_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];	// 메인 상품진열 모바일
	$main_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_default.'단형';
	$main_mo_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_mo_default.'단형';


	$list_default = $siteInfo['s_list_timesale_display']>0?$siteInfo['s_list_timesale_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
	$list_mo_default = $siteInfo['s_list_timesale_mobile_display']>0?$siteInfo['s_list_timesale_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
	$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
	$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';


	$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];
	$arrProDisplay['arrMainNameMo'] = $arrProDisplay['arrDepthNameMo'][$main_display_type];

?>

<form name="formDisplayCategory" action="_config.display.timesale.pro.php" method="post">
	<input type="hidden" name="main_view_type" value="<?php echo $main_display_type;?>">
	<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>메인 노출</th>
					<td colspan="3">
						<?php echo _InputRadio('s_main_timesale_view', array('Y','N'),($siteInfo['s_main_timesale_view']!=''?$siteInfo['s_main_timesale_view']:'Y') , ' class="s_main_timesale_view"', array('노출','숨김'), ''); ?>
						<?php echo _DescStr('타임세일 상품이 있을 경우 메인에 타임세일상품만 모아볼 수 있는 영역에 대해 노출을 설정합니다.',''); ?>
						<?php echo _DescStr('상품별로 설정된 시간이 모두 지났거나, 설정된 상품이 하나도 없을 경우 설정에 관계없이 무조건 비노출됩니다',''); ?>
						<?php echo _DescStr('타임세일 상품을 모두 볼 수 있는 목록은 별도로 노출설정을 하지않습니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">타임세일 타이틀</th>
					<td>
						<input type="text" name="s_main_timesale_title" class="design" value="<?php echo $siteInfo['s_main_timesale_title']; ?>" style="width:50%;" placeholder="타임세일 타이틀">
						<?php echo _DescStr('메인 타임세일 영역 및 타임세일 목록에 노출되는 타이틀을 설정합니다.',''); ?>
					</td>
					<th class="ess">메인 최대노출</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="s_main_timesale_limit" class="design bold t_black number_style" value="<?php echo $siteInfo['s_main_timesale_limit'];?>" style="width:70px" placeholder="0">
							<div class="fr_tx">개</div>
						</div>
						<?php echo _DescStr('사이트 접속 시 로딩 속도를 고려하여 적당한 개수로 설정해주세요. (최대 100개)',''); ?>
					</td>
				</tr>
				<tr>
					<th>메인 상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( 's_main_timesale_display' , array_values($arrProDisplay['arrMainName']) , $main_default , ' class="js_main_timesale_pc"  ' , array_values($arrProDisplay['arrMainName']) , 'PC 상품진열');

								echo _InputSelect( 's_main_timesale_mobile_display' , array_values($arrProDisplay['arrMainNameMo']) , $main_mo_default, ' class="js_main_timesale_mo" ' , array_values($arrProDisplay['arrMainNameMo']) , '반응형 상품진열');
							?>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<?php echo _DescStr('1줄에 설정한 개수가 초과할 경우 슬라이드(롤링)기능이 적용됩니다.','blue'); ?>
						<div class="dash_line"></div>

						<?php echo _InputRadio('s_main_timesale_order', array('edate_asc','edate_desc','random'),($siteInfo['s_main_timesale_order']!=''?$siteInfo['s_main_timesale_order']:'edate_asc') , ' ', array('종료 빠른순','종료 느린순','랜덤순'), ''); ?>
					</td>
					<th>목록 상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( 's_list_timesale_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_list_timesale_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');

								echo _InputSelect( 's_list_timesale_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default, ' class="js_list_timesale_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
							?>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<?php echo _DescStr('타임세일 상품만 별도로 모아볼 수 있는 목록페이지의 진열을 설정합니다. ',''); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<div class="lineup-row">
							<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">상품 진열설정 도움말</a>
							<a href="/?pn=product.time_list" class="c_btn sky js_onoff_event" target="_blank">바로가기</a>
						</div>
						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">

	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=formDisplayCategory]").validate({
				ignore: ".ignore",
				rules: {
						s_main_timesale_title: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
				},
				messages: {
						s_main_timesale_title : { required: '타임세일 타이틀을 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					try{
						var s_main_timesale_limit = $('[name="s_main_timesale_limit"]').val();
						if( s_main_timesale_limit*1 < 1 || s_main_timesale_limit*1 > 100 ){
							throw "메인최대노출 개수는 1개이상 100개 이하로 입력해 주시기 바랍니다.";
						}
					}catch(e){
						if( typeof e != 'string'){ alert("수정이 불가능합니다."); }
						else{alert(e);  }
						return false;
					}
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

					var s_main_timesale_view = $('[name="s_main_timesale_view"]').val();
					var s_main_timesale_display = $('[name="s_main_timesale_display"]').val();
					var s_main_timesale_mobile_display = $('[name="s_main_timesale_mobile_display"]').val();
					var s_list_timesale_display = $('[name="s_list_timesale_display"]').val();
					var s_list_timesale_mobile_display = $('[name="s_list_timesale_mobile_display"]').val();

					if(s_main_timesale_view=='Y'){
						if( s_main_timesale_display == ''){ alert("메인 상품진열 설정을 선택해 주세요."); return false; }
						if( s_main_timesale_mobile_display == ''){ alert("메인 반응형 상품진열 설정을 선택해 주세요."); return false; }
						if( s_list_timesale_display == ''){ alert("목록 상품진열 설정을 선택해 주세요."); return false; }
						if( s_list_timesale_mobile_display == ''){ alert("목록 반응형 상품진열 설정을 선택해 주세요."); return false; }
					}

					form.submit();
				}
		});
	});

	$(document).ready(function(){
		TimesaleListSelect('reload');
		TimesaleMainSelect('reload');
	});

	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_list_timesale_pc', TimesaleListSelect);
	function TimesaleListSelect(chk_mode) {
		var _list_view_pc = $('.js_list_timesale_pc').find('option:selected').val();
		var _list_view_pcval = '<?php echo $list_default;?>';
		var _list_view_moval = '<?php echo $list_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';

		if(_list_view_pc!=''){
			if(chk_mode=='change'){
				_list_view_moval='';
			}
			$('.js_list_timesale_pc').find('option[value=""]').prop('disabled',true);
		}

		$.ajax({
			data: {
				_mode: 'pcListSelect',
				_list_view_pc: _list_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_config.display.timesale.pro.php',
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
					$('.js_list_timesale_mo').show();
				}
				$('.js_list_timesale_mo').html(_option);
				$('input[name="list_view_type"]').val(result.type);
				return result;
			}
		});
	}


	// 메인 상품진열 설정 모바일 클릭시
	$(document).on('change', '.js_list_timesale_mo', TimeMainBestSelectMo);
	function TimeMainBestSelectMo() {
		var _list_view_pc = $('.js_list_timesale_pc').find('option:selected').val();
		var _list_view_mo = $('.js_list_timesale_mo').find('option:selected').val();

		if(_list_view_mo!='' && _list_view_pc!=''){
			$('.js_list_timesale_mo').find('option[value=""]').prop('disabled',true);
		}
	}



	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_main_timesale_pc', TimesaleMainSelect);
	function TimesaleMainSelect(chk_mode) {
		var _main_view_pc = $('.js_main_timesale_pc').find('option:selected').val();
		var _main_view_pcval =  '<?php echo $main_default;?>';
		var _main_view_moval = '<?php echo $main_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';


		if(_main_view_pc!=''){
			if(chk_mode=='change'){
				_main_view_moval='';
			}
			$('.js_main_timesale_pc').find('option[value=""]').prop('disabled',true);
		}

		$.ajax({
			data: {
				_mode: 'pcMainSelect',
				_main_view_pc: _main_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_config.display.timesale.pro.php',
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
					$('.js_main_timesale_mo').show();
				}
				$('.js_main_timesale_mo').html(_option);
				$('input[name="main_view_type"]').val(result.type);
				return result;
			}
		});
	}


	// 메인 상품진열 설정 모바일 클릭시
	$(document).on('change', '.js_main_timesale_mo', TimeMainBestSelectMo);
	function TimeMainBestSelectMo() {
		var _main_view_pc = $('.js_main_timesale_pc').find('option:selected').val();
		var _main_view_mo = $('.js_main_timesale_mo').find('option:selected').val();

		if(_main_view_mo!='' && _main_view_pc!=''){
			$('.js_main_timesale_mo').find('option[value=""]').prop('disabled',true);
		}
	}
</script>
<?php include_once('wrap.footer.php');?>