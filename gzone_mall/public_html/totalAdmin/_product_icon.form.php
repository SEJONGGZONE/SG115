<?php
	$app_current_name = "상품 아이콘 등록";
	$app_current_link = '_product_icon.list.php';
	include_once('wrap.header.php');

	// 상품아이콘에 자동적용아이콘 추가
	$arr_product_icon_type2 = array_merge($arr_product_icon_type_auto, $arr_product_icon_type);

	if( $_mode == "modify" ) {
		$que = "  select * from smart_product_icon where pi_uid='". $_uid ."' ";
		$r = _MQ($que);
	}else{
		$_mode = 'add';
		$r['pi_type'] = array_shift(array_keys($arr_product_icon_type));

		$r['pi_idx'] = _MQ_result("select pi_idx from smart_product_icon where pi_type in('".implode("','",array_keys($arr_product_icon_type))."') order by pi_idx desc");
		$r['pi_idx'] = ($r['pi_idx']*1) +1;
	}
?>




	<form name="frm" method="post" action="_product_icon.pro.php" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
		<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_type" value="<?php echo $r['pi_type']; ?>">

		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">아이콘명</th>
						<td>
							<input type="text" name="_title" class="design" value="<?php echo $r['pi_title']; ?>" placeholder="아이콘명" style="width:500px"  maxlength="10">
							<?php echo _DescStr('아이콘 형태에 따라 이미지형은 alt값으로 사용되고, 텍스트형은 실제 아이콘 자체로 노출됩니다.'); ?>
							<?php echo _DescStr('아이콘 노출시 디자인 오류 방지를 위해 10자 이내로 입력가능합니다.','red'); ?>
						</td>
						<th class="ess">노출순위</th>
						<td>
							<div class="lineup-row">
							<?php if(in_array($r['pi_type'],array_keys($arr_product_icon_type))){ ?>
								<input type="text" name="_idx" class="design number_style" value="<?php echo ($r['pi_idx'] ?$r['pi_idx']:1); ?>" placeholder="0" style="width:50px"><span class="fr_tx">순위</span>
								<?php echo _DescStr('노출순위가 낮을수록 먼저 나오며, 순위가 같으면 최근 등록한 아이콘이 먼저 나옵니다.'); ?>
							<?php }else{ ?>
								<?php echo _DescStr('자동적용 아이콘은 순위 설정이 불가합니다.','red'); ?>
								<input type="hidden" name="_idx" value="1">
							<?php } ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>아이콘 형태</th>
						<td colspan="3">
							<?php echo _InputRadio( "_view_type" , array('img','text'), ($r['pi_view_type'] ? $r['pi_view_type'] : "img") , " class='js_view_type' " , array('이미지형','텍스트형') , '');?>
						</td>
					</tr>
					<tr class="js_view_type_img">
						<th class="ess">이미지</th>
						<td colspan="3">
							<div class="lineup-row">
								<?php echo _PhotoForm( '..'.IMG_DIR_ICON , '_img_m'  , $r['pi_img_m'] , 'style="width:250px"'); ?>
							</div>
						</td>
					</tr>

					<tr class="js_view_type_text" style="display:none;">
						<th class="ess">아이콘 색상설정</th>
						<td colspan="3">
							<div class="lineup-row">
								<span class="fr_tx">글씨 색상</span><input type="text" name="_text_color" class="design js_colorpic" value="<?php echo $r['pi_text_color']; ?>" style="width:70px">
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row">
								<span class="fr_tx">바탕 색상</span><input type="text" name="_bg_color" class="design js_colorpic" value="<?php echo $r['pi_bg_color']; ?>" style="width:70px">
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row">
								<span class="fr_tx">보더 색상</span><input type="text" name="_line_color" class="design js_colorpic" value="<?php echo $r['pi_line_color']; ?>" style="width:70px">
							</div>
						</td>
					</tr>
				</tbody>
			</table>

		</div>

		<?php echo _submitBTN('_product_icon.list.php' , ($_type ? '_type='.$_type : '')); ?>

	</form>


<script>

	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_type: { required: true }
						,_title: { required: true }
						,_idx: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						_type : { required: '아이콘유형를 선택해주시기 바랍니다.' }
						,_title : { required: '아이콘명을 입력해주시기 바랍니다.' }
						,_idx : { required: '순위를 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {

					try{

						var _idx = $('[name="_idx"]').val();
						if( _idx == ''){ throw "순위를 입력해 주시기 바랍니다."; }
						if( _idx*1 <= 0){ throw "순위는 1이상 입력해 주시기 바랍니다.";  }

						var _view_type =  $('.js_view_type:checked').val();
						if( _view_type == 'img'){
							var _img_m = $('[name="_img_m"]').val();
							if( _img_m == ''){
								var _img_m_OLD = $('[name="_img_m_OLD"]').val();
								if( typeof _img_m_OLD == 'undefined' || _img_m_OLD == ''){ throw "이미지를 선택해 주시기 바랍니다.";  }
								else{
									var _img_m_DEL_check = $('[name="_img_m_DEL"]').prop('checked');
									if( _img_m_DEL_check == true){  throw "이미지를 선택해 주시기 바랍니다."; }
								}
							}
						}
						else if( _view_type == 'text'){
							var _text_color = $('[name="_text_color"]').val();
							if( _text_color == ''){ throw "글씨 색상을 선택해 주시기 바랍니다."; }
							var _bg_color = $('[name="_bg_color"]').val();
							if( _bg_color == ''){ throw "바탕 색상을 선택해 주시기 바랍니다."; }
							var _line_color = $('[name="_line_color"]').val();
							if( _line_color == ''){ throw "보더 색상을 선택해 주시기 바랍니다."; }
						}
						else{
							throw "아이콘 형태를 선택해 주시기 바라니다.";
						}
					}catch(e){
						if( typeof e != 'string'){ alert("<?php echo $_mode == 'add' ? '등록':'수정' ?>이 불가능합니다."); }
						else{alert(e);  }
						return false;
					}
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});



	// 아이콘 형태 (이미지형, 텍스트형)
	function func_view_type() {
		if($('.js_view_type:checked').val() == 'img') {
			$('.js_view_type_img').show();
			$('.js_view_type_text').hide();
		} else {
			$('.js_view_type_img').hide();
			$('.js_view_type_text').show();
		}
	}
	$(document).ready(func_view_type);
	$(document).on('click', '.js_view_type', func_view_type);

</script>


<?php include_once('wrap.footer.php'); ?>