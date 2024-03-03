<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_popup.list.php';
$app_current_name = "팝업 등록";
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_popup where p_uid = '{$_uid}' ");
}else{
	$_mode = 'add';

	// 등록상품이 가장위에 노출되도록
	$r['p_sort_group'] = 100;
	$r['p_sort_idx'] = 0.5;
	$r['p_idx'] = 1;
}

?>
<form name="frm" action="_popup.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_sort_group" value="<?php echo $r['p_sort_group']; ?>" >
	<input type="hidden" name="_sort_idx"  value="<?php echo $r['p_sort_idx']; ?>" >
	<input type="hidden" name="_idx" value="<?php echo $r['p_idx']; ?>" >
	<input type="hidden" name="p_type" value="A" >

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>타입</th>
					<td>
						<?php echo _InputRadio('p_mode', array('I', 'E'), ($r['p_mode']?$r['p_mode']:'I'), ' class="js_mode"', array('이미지형', '에디터형'), ''); ?>
					</td>
					<th>노출</th>
					<td>
						<?php echo _InputRadio('p_view', array('Y', 'N'), ($r['p_view']?$r['p_view']:'N'), '', array('노출', '숨김'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>노출기간</th>
					<td colspan="3">
						<?php echo _InputRadio('p_none_limit', array('Y', 'N'), ($r['p_none_limit']?$r['p_none_limit']:'N'), ' class="js_banner_trim_type"', array('무기한', '기간지정'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="js_change_trim">
                            <div class="lineup-row type_date">
                                <input type="text" name="p_sdate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['p_none_limit'] <> 'Y'?$r['p_sdate']:''); ?>" placeholder="날짜 선택" style="width:85px" <?php echo  ($r['p_none_limit'] == 'Y'?' disabled ':null); ?> readonly>
                                <span class="fr_tx">-</span>
                                <input type="text" name="p_edate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['p_none_limit'] <> 'Y'?$r['p_edate']:''); ?>" placeholder="날짜 선택" style="width:85px" <?php echo  ($r['p_none_limit'] == 'Y'?' disabled ':null); ?> readonly>
                            </div>
						</div>
						<script type="text/javascript">
							$(document).on('change', '.js_banner_trim_type', BannerTrim);
							$(document).ready(BannerTrim);
							function BannerTrim() {
								var _type = $('.js_banner_trim_type:checked').val();
								if(_type == 'N') $('.js_banner_trim_use').removeAttr('disabled');
								else $('.js_banner_trim_use').attr('disabled',true);
							}
						</script>
					</td>
				</tr>
				<tr>
					<th>타이틀</th>
					<td colspan="3">
						<input type="text" name="p_title" class="design" value="<?php echo $r['p_title']; ?>" placeholder="타이틀" style="width:400px">
					</td>
				</tr>
				<tr class="js_images_mode">
					<th>팝업이미지</th>
					<td colspan="3">
                        <div class="lineup-row">
						    <?php echo _PhotoForm('..'.IMG_DIR_POPUP, 'p_img', $r['p_img'], 'style="width:250px"'); ?>
                        </div>
					</td>
				</tr>
				<tr class="js_edit_mode">
					<th>팝업내용</th>
					<td colspan="3">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="p_content" class="design SEditor" style="width:100%;height:300px;" cols="30" rows="10"><?php echo $r['p_content']; ?></textarea>
					</td>
				</tr>
				<tr class="js_edit_mode">
					<th>팝업 배경색</th>
					<td>
						<input type="text" name="p_bgcolor" class="design js_colorpic" value="<?php echo strtoupper($r['p_bgcolor']?$r['p_bgcolor']:'#ffffff'); ?>" placeholder="색상 선택" style="width:70px">
					</td>
					<th>팝업크기(픽셀)</th>
					<td>
                        <div class="lineup-row">
                            <span class="fr_tx">가로</span>
							<input type="text" name="p_width" class="input_text design number_style" style="width:50px" placeholder="0" value="<?php echo ((int)$r['p_width'] >= 350?$r['p_width']:350); ?>">
                            <span class="fr_tx">세로</span>
							<input type="text" name="p_height" class="input_text design number_style" style="width:50px" placeholder="0" value="<?php echo ((int)$r['p_height'] >= 250?$r['p_height']:250); ?>">
                        </div>
					</td>
				</tr>
				<tr class="js_images_mode">
					<th>링크타켓</th>
					<td colspan="3">
						<?php echo _InputRadio('p_target', array('_self', '_blank'), ($r['p_target']?$r['p_target']:'_self'), '', array('같은창', '새창'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
                            <input type="text" name="p_link" class="design js_app_link" value="<?php echo $r['p_link']; ?>" placeholder="링크주소" style="width:400px;">
                            <a href="#none" onclick="productWin(); return false;" class="c_btn h27 line sky">상품연결</a>
                        </div>
					</td>
				</tr>
				<!--
				<tr>
					<th>노출순위</th>
					<td>
						<input type="text" name="p_idx" class="design t_center" value="<?php echo ($r['p_idx']?$r['p_idx']:0); ?>" placeholder="0" style="width:50px">
						<?php echo _DescStr('노출순위가 높을수록 우선 노출됩니다.'); ?>
					</td>
				</tr>
				-->
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN($app_current_link); ?>
</form>



<script type="text/javascript">
	function productWin() {
		var _pcode = $('input[name=p_link]').val();
		_pcode = _pcode.split('/?pn=product.view&pcode=');
		_pcode = (_pcode[1]?_pcode[1]:_pcode[0]);
		window.open('_banner.product_link.php?relation_prop_code='+_pcode, 'relation', 'width=1120, height=800, scrollbars=yes'); // 배너의 상품연결과 동일한 파일 사용
	}

	$(document).ready(function() {
		// -  validate ---
		$('form[name=frm]').validate({
			ignore: 'input[type=text]:hidden,input[type=button]',
			rules: {
				p_sdate: { required: function(){ if( $('input[name=p_none_limit]:checked').val()=='N' ){ return true; }else{ return false; } } }	//노출여부
				,p_edate: { required: function(){ if( $('input[name=p_none_limit]:checked').val()=='N' ){ return true; }else{ return false; } } }	//노출순위
			},
			messages: {
				p_sdate: { required: '시작일을 입력해주시기 바랍니다.'}	//노출여부
				,p_edate: { required: '종료일을 입력해주시기 바랍니다.'}	//노출순위
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
		// - validate ---
	});



	function PopupMode() {
		var _mode = $('.js_mode:checked').val();
		$('.js_images_mode').hide();
		$('.js_edit_mode').hide();
		if(_mode == 'I') {
			$('.js_images_mode').show();
		}
		else {
			$('.js_edit_mode').show();

			// 에디터 사이즈 조정
			if(oEditors.length > 0) {
				var id = $('.SEditor').attr('id');
				oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
			}
		}
	}
	$(document).ready(PopupMode);
	$(document).on('click', '.js_mode', PopupMode);
</script>
<?php include_once('wrap.footer.php'); ?>