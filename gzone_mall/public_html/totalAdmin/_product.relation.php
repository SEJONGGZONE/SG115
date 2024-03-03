<?php
	// 관련상품 개수 추출
	$relation_cnt = number_format(count(array_filter(explode('|', stripslashes($row['p_relation'])))));

	// 관련상품 노출용데이터 추출
	$ex_relation = array_filter(explode('|', stripslashes($row['p_relation'])));
	$arr_str_view = array();
	if(sizeof($ex_relation)>0){
		foreach($ex_relation as $k=>$v){
			$_tmp = _MQ(" select p_code, p_name from smart_product where p_code = '". $v ."' and p_code != '".$_code."' ");
			if( $_tmp['p_code'] == ''){ continue; }
			$arr_str_view[] = stripslashes($_tmp['p_name']) . '('. $v .')';
		}
	}
	$relation_cnt = count($arr_str_view);
	$app_str_view = implode(" | " , $arr_str_view);
?>

		<?php echo _InputRadio('_relation_type', array('none' , 'category' , 'manual'), ($row['p_relation_type'] ? $row['p_relation_type'] : 'none'), '', array('미사용' , '자동지정' , '수동지정') , ''); ?>

		<div class="lineup-row js_relation_hide" style="<?php echo ($row['p_relation_type'] <> 'manual' ? 'display:none;' : null); ?>">
			<a href="#none" onclick="relationWin('all');return false;" class="c_btn h27 blue">관련상품 설정하기</a>
			<a href="#none" onclick="relationWin('select');return false;" class="c_btn blue line  js_relation_select">선택상품 : 총 <span class="js_relation_cnt"><?php echo $relation_cnt; ?></span>개</a>
			<a href="#none" onclick="relationWin('reset');return false;" class="c_btn h27 black line  js_relation_reset">설정상품 초기화</a>
		</div>

		<div class="dash_line js_relation_wrap"><!-- 점선라인 --></div>
		<textarea name="" rows="5" cols="" placeholder="관련 상품 설정" class="js_relation_view js_relation_wrap design<?php echo ($row['p_relation_type'] <> 'manual' ? ' disabled' : null); ?>" readonly="" onclick="relationHelp(); return false;"><?php echo stripslashes($app_str_view); ?></textarea>
		<input type="hidden" name="_relation" value="<?php echo stripslashes($row['p_relation']); ?>">

		<?php echo _DescStr('자동지정 : 동일 카테고리 상품이 랜덤으로 노출됩니다. ',''); ?>
		<?php echo _DescStr('수동지정 : 직접 선택한 상품이 순서대로 노출됩니다.',''); ?>
		<?php echo _DescStr('관련상품은 상세페이지 로딩을 고려하여 최대 50개까지만 노출됩니다.',''); ?>

<script>
	//관련상품삭제
	function delField(objTemp) {
		objTemp.value='';
	}

	//관련상품수정/입력
	function relationWin(type){
		if(type=='all'){
			setCookie('relation_prop_code_<?php echo $row[p_code]?>', $('input[name=_relation]').val());
			window.open('_product.relation.pop.php?relation_code=<?php echo $row[p_code]?>&search_type=all','relation', 'width=1350, height=700, scrollbars=yes');
		}else if(type=='select'){
			
			setCookie('relation_prop_code_<?php echo $row[p_code]?>', $('input[name=_relation]').val());
			window.open('_product.relation.pop.php?relation_code=<?php echo $row[p_code]?>&search_type=select','relation', 'width=1350, height=700, scrollbars=yes');
		}else{
			
			// 초기화
			var value = $('input[name=_relation]').val();
			$('.js_relation_select').hide();
			$('input[name=_relation]').val('');
			$('.js_relation_view').text('');
			$('.js_relation_cnt').text('0');
			controller_realtion_display();
		}
	}

	//관련상품 안내문구
	function relationHelp() {
		if(!$('.js_relation_view').hasClass('disabled')){
			alert('위 "관련상품 설정하기"버튼을 이용하여 입력해 주시기 바랍니다.   ');
		}
	}

	// 관련상품 노출 설정시
	$(document).ready(controller_realtion_display);
	$(document).on('click', 'input[name=_relation_type]', controller_realtion_display);
	function controller_realtion_display(){
		var selected = $('input[name=_relation_type]:checked').val();

		if(selected == 'manual'){
			$('.js_relation_wrap').show();
			$('.js_relation_hide').show();
			$('.js_relation_view').removeClass('disabled');
		}
		else{
			$('.js_relation_wrap').hide();
			$('.js_relation_hide').hide();
			$('.js_relation_view').addClass('disabled');
		}

		var _relation = $('[name="_relation"]').val();
		if( _relation == ''){
			$('.js_relation_select').hide();
			$('.js_relation_reset').hide();
		}else{
			$('.js_relation_select').show();
			$('.js_relation_reset').show();
		}


	}

	// 관련상품 변경시 관련상품 개수 갱싱
	//$(document).ready(sync_relation_cnt);
	$(document).on('change', 'input[name=_relation]', sync_relation_cnt);
	function sync_relation_cnt(){
		var value = $('input[name=_relation]').val();
		var cnt = 0;
		if(value != '') cnt = value.split('|').length;
		$('.js_relation_cnt').text(cnt.toString().comma());
	}
</script>