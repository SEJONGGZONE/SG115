<?php
	include_once('wrap.header.php');

	/*
		# DB :: smart_setup
		- s_search_option 	검색조건 var.php 참고
		- s_search_display
		- s_search_mobile_display
		- s_search_diff_orderby
		- s_search_diff_maxcnt
		- s_search_diff_option
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
	$main_display_type = $siteInfo['s_search_display_type']!=''?$siteInfo['s_search_display_type']:'box';

	// 기본 값
	$main_default = $siteInfo['s_search_display']>0?$siteInfo['s_search_display']:$tempSkinInfo['pc_pro_depth_default'];						// 메인 상품진열 pc
	$main_mo_default = $siteInfo['s_search_mobile_display']>0?$siteInfo['s_search_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];	// 메인 상품진열 모바일
	$main_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_default.'단형';
	$main_mo_default = ($main_display_type=='box'?'박스 ':'리스트 ').$main_mo_default.'단형';

	$arrProDisplay['arrMainNameMo'] = $arrProDisplay['arrDepthNameMo'][$main_display_type];



	$searchDiffOrderbyValue = $siteInfo['s_search_diff_orderby'] == '' ? 'salecnt':$siteInfo['s_search_diff_orderby']; // 정렬방식
	$searchDiffMaxcntValue = $siteInfo['s_search_diff_maxcnt'] == 0 ? 20:$siteInfo['s_search_diff_maxcnt']; // 최대출력개수
	$searchDiffOptionValue = $siteInfo['s_search_diff_option'] == '' ? 'rand':$siteInfo['s_search_diff_option']; // 뽑는옵션

	// 상품에서 사용된 해시 리스트를 추출 한다.
	$HashData = _MQ(" select group_concat(sp.p_hashtag) as hash from `smart_product` as sp where (1) and p_hashtag != '' ");
	$HashData = explode(',', $HashData['hash']);
	$HashList = '';
	if(count($HashData) <= 0) $HashData = array();
	if(count($HashData) > 0) {
		$HashData = array_flip($HashData);
		@ksort($HashData);
		$HashList = "'".implode("', '", array_keys($HashData))."'";
	}

	$searchOption = $siteInfo['s_search_option'] == '' ? array() : explode(",",$siteInfo['s_search_option']);
	

?>

<form name="formDisplaySearch" action="_config.display.search.pro.php" method="POST">
	<input type="hidden" name="main_view_type" value="<?php echo $main_display_type;?>">
	<div class="group_title"><strong>검색 기본 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>검색조건</th>
					<td >
						<?php echo _InputCheckbox('s_search_option', array_keys($arrSearchOption), $searchOption, ' class="s_search_option"', array_values($arrSearchOption), ''); ?>
						<?php echo _DescStr('검색결과가 있을 때 사용자가 선택할 수 있는 상세조건 노출을 설정합니다.'); ?>
					</td>
					<th>검색결과 상품진열</th>
					<td>
						<div class="lineup-row type_responsive">
							<?php
								echo _InputSelect( 's_search_display' , array_values($arrProDisplay['arrMainName']) , $main_default , ' class="js_search_view_pc" ' , array_values($arrProDisplay['arrMainName']) , 'PC 상품진열');
								echo _InputSelect( 's_search_mobile_display' , array_values($arrProDisplay['arrMainNameMo']) , $main_mo_default , ' class="js_search_view_mo" ' , array_values($arrProDisplay['arrMainNameMo']) , '반응형 상품진열');
							?>
							<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
						</div>
						<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
						<? include_once("help.item_setting.php"); //단수도움말 ?>
					</td>
				</tr>
				<tr>
					<th>검색폼 문구설정</th>
					<td colspan="3">
						<input type="text" name="s_saerch_text" class="design" placeholder="검색폼 문구설정" value="<?php echo $siteInfo['s_saerch_text'];?>">
						<?php echo _DescStr('검색 입력 폼에 노출될 문구를 설정합니다. 클릭 시 검색되는 단어와는 별개의 문구입니다. 미 입력시 "Search"가 표시됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>검색결과 없을 경우</th>
					<td colspan="3">
						<div class="lineup-row">
							<div class="fr_tx">노출 순서</div>
							<div class="divi"></div>
							<?php echo _InputRadio('s_search_diff_orderby', array('salecnt', 'review'), $searchDiffOrderbyValue, '', array('상품판매 높은순', '리뷰등록 많은순')); ?>
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
							<div class="fr_tx">최대 노출</div>
							<div class="divi"></div>
							<input type="text" name="s_search_diff_maxcnt" class="design bold t_black number_style" placeholder="" value="<?php echo $searchDiffMaxcntValue; ?>" style="width:70px;">
							<div class="fr_tx">개</div>
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
							<div class="fr_tx">노출 방식</div>
							<div class="divi"></div>
							<?php echo _InputRadio('s_search_diff_option', array('rand', 'normal'), $searchDiffOptionValue, '', array('랜덤', '기본순서')); ?>
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('검색 결과가 한개도 없을 시 노출되는 상품 노출에 대해 설정하며, 페이지 로딩을 고려하여 설정해주세요. (최대 100개)'); ?>
						<?php echo _DescStr('사용자 페이지에서는 "다른 고객이 많이 찾은 상품"으로 노출됩니다.'); ?>
					</td>
				</tr>


			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>검색 키워드 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>검색 키워드</th>
					<td>
						<input type="text" name="s_recommend_keyword" class="design js_tag" value="<?php echo $siteInfo['s_recommend_keyword']; ?>" style="width:100%;">
					</td>
				</tr>
				<tr>
					<th>검색 해시태그</th>
					<td>
						<input type="text" name="s_recommend_hashtag" class="design js_hashtag" value="<?php echo $siteInfo['s_recommend_hashtag']; ?>" style="width:100%">
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td>
						<?php echo _DescStr('검색어나 해시태그를 입력후 Enter 혹은 Tab을 클릭하면 자동으로 추가되고 꼭 확인버튼을 눌러주셔야 최종 저장됩니다.'); ?>
						<?php echo _DescStr('해시태그는 상품등록 시 추가된 해시태그만 입력 가능 하며, 등록되지 않은 해시태그는 저장되지 않습니다.', 'blue'); ?>
						<?php echo _DescStr('해시태그는 단어를 하나만 입력해도 만약 등록된 해시태그가 있으면 검색목록이 나타나며 해당 태그를 클릭해서 추가할 수 있습니다.'); ?>
						<?php echo _DescStr('단어를 다시 클릭하면 수정가능하고, 단어 옆에 x 버튼을 클릭하면 삭제됩니다.'); ?>
						<?php echo _DescStr('사이트 검색폼 하단에 검색어와 해시태그가 랜덤으로 노출되지만, 스킨에 따라 노출여부나 위치가 상이할 수 있습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<?php echo _submitBTNsub(); ?>
</form>





<script type="text/javascript">
	var form = $(form);

	$(document).on('submit',form,function(){
		var chk_search_diff_orderby = $(this).find('[name="s_search_diff_orderby"]:checked').val();
		var chk_search_diff_maxcnt = $(this).find('[name="s_search_diff_maxcnt"]').val();
		var chk_search_diff_option = $(this).find('[name="s_search_diff_option"]:checked').val();

		if( typeof chk_search_diff_orderby == 'undefined' || chk_search_diff_orderby == ''){
			alert('검색결과 없을 경우 노출순서를 선택해 주세요.');
			$(this).find('[name="s_search_diff_orderby"]:checked').focus();
			return false;
		}

		if( typeof chk_search_diff_maxcnt == 'undefined' || chk_search_diff_maxcnt == '' || chk_search_diff_maxcnt*1 < 1 || chk_search_diff_maxcnt > 100 ){
			alert("검색결과 없을 경우 최대 노출개수는 1개 이상 100 이하로 입력해 주세요.");
			$(this).find('[name="s_search_diff_maxcnt"]:checked').focus();
			return false;
		}

		if( typeof chk_search_diff_option == 'undefined' || chk_search_diff_option == ''){
			alert('검색결과 없을 경우 노출방식을 선택해 주세요');
			$(this).find('[name="s_search_diff_option"]:checked').focus();
			return false;
		}

		var s_search_display = $('[name="s_search_display"]').val();
		var s_search_mobile_display = $('[name="s_search_mobile_display"]').val();
		if( s_search_display == ''){ alert("검색결과 상품진열 설정을 선택해 주세요. "); return false; }
		if( s_search_mobile_display == ''){ alert("검색결과 반응형 상품진열을 선택해 주세요. "); return false; }

		return true;
	});

	// 태그
	$('.js_hashtag').tagEditor({
		autocomplete: {
			delay: 0,
			position: { collision: 'flip' },
			source: [<?php echo $HashList; ?>],
			slect: function(e, u) {
				var tag = u.item.value;
				u.item.value = '';
				$(this).tagEditor('addTag', tag);
			}
		},
		forceLowercase: false,
		beforeTagSave: function(field, editor, tags, tag, val) {
			var list = $.parseJSON('<?php echo json_encode($HashData); ?>');
			if(list[val] === undefined) return false;
		}
	});


	$(document).ready(function(){
		SearchListSelect('reload');
	});

	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_search_view_pc', SearchListSelect);
	function SearchListSelect(chk_mode) {
		var _search_view_pc = $('.js_search_view_pc').find('option:selected').val();
		var _search_view_pcval = '<?php echo $main_default;?>';
		var _search_view_moval = '<?php echo $main_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';

			if(_search_view_pc!=''){
				if(chk_mode=='change'){
					_search_view_moval='';
				}
				$('.js_search_view_pc').find('option[value=""]').prop('disabled',true);
			}

		$.ajax({
			data: {
				_mode: 'pcSelect',
				_search_view_pc: _search_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_config.display.search.pro.php',
			success: function(data) {
				if(data == '') data = null;
				var result = $.parseJSON(data);
				var _option;
					if(_search_view_moval!='' && chk_mode=='reload'){
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
						_option += '<option value="'+option_title+'" '+( (_search_view_moval!='' && _search_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
					});
					$('.js_search_view_mo').show();
				}
				$('.js_search_view_mo').html(_option);
				$('input[name="main_view_type"]').val(result.type);
				return result;
			}
		});
	}

	// 베스트 상품 상품 진열 설정 클릭시
	$(document).on('change', '.js_search_view_mo', SearchListSelectMo);
	function SearchListSelectMo() {
		var _search_view_pc = $('.js_search_view_pc').find('option:selected').val();
		var _search_view_mo = $('.js_search_view_mo').find('option:selected').val();

		if(_search_view_mo!='' && _search_view_pc!=''){
			$('.js_search_view_mo').find('option[value=""]').prop('disabled',true);
		}
	}

</script>
<?php include_once('wrap.footer.php');?>