<?php
	$app_mode = 'popup';
	include_once('inc.header.php');
?>

<div class="popup none_layer">
	<div class="pop_title">
		<strong>옵션 설정</strong>
		<a href="#none" onclick="window.close();" class="btn_close"></a>
	</div>

	<div class="pop_conts">
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="category_apply_save('1depth_add','');return false;" class="c_btn blue">옵션추가</a>
				<a href="#none" onclick="submit_alert();return false;" class="c_btn h27 red">옵션저장</a>
				<span class="fr_tx t_red t_11 t_bold">옵션 수정 후 꼭 저장을 해주세요!</span>
			</div>
			<div class="right_box">
				<a href="#none" onclick="$('.js_thisform').toggle(); return false;" class="c_btn icon icon_excel only_pc_view2">엑셀 일괄등록</a>
			</div>
		</div>

		<div class="data_search js_thisform" style="display:none;">
			<form action="_product_option.excel_pro.php" name="wFrm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="tran_type" value="ins_excel">
				<input type="hidden" name="pass_mode" value="<?php echo $pass_mode?>">
				<input type="hidden" name="pass_code" value="<?php echo $pass_code?>">
				<table class="table_form">
					<colgroup>
						<col width="130"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>일괄업로드</th>
							<td>
								<div class="lineup-row type_multi">
									<div class="input_file" style="width:300px">
										<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
										<div class="fileDiv">
											<input type="button" class="buttonImg" value="파일찾기">
											<input type="file" name="w_excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
										</div>
									</div>
									<a href="#none" onclick="_product_option_submit();return false;" class="c_btn h27 green">업로드</a>
								</div>
							</td>
						</tr>
						<tr>
							<th>참고사항</th>
							<td>
								<a href="/upfiles/normal/option<?php echo $pass_mode; ?>_sample.xls" class="c_btn green line">샘플파일 다운로드</a>
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="tip_box">
									<?php echo _DescStr('엑셀 일괄등록의 경우, 업로드를 함과 동시에 자동으로 정보가 저장 됩니다.');?>
									<?php echo _DescStr('차수에 맞게 옵션을 등록할 수 있습니다. (샘플파일을 다운받아서 업로드 하시기 바랍니다.)');?>
									<?php echo _DescStr('파일은 최대 '. $MaxUploadSize .'까지 업로드 가능 하며, 용량에 따라 다소시간이 걸릴 수 있습니다.');?>
									<?php echo _DescStr('1차, 2차, 3차 옵션의 합산명칭이 같은 경우에는 기존 항목이 수정되고, 다를 경우 새롭게 추가 됩니다.');?>
									<?php //echo _DescStr('엑셀97~2003 버전 파일만 업로드가 가능하며, 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록해주십시오.');?>
									<?php echo _DescStr('옵션 유형이 컬러인 경우, 색상과 이미지는 엑셀을 통해 일괄 등록할 수 없습니다.', 'black');?>
									<?php echo _DescStr('옵션명 입력 시 특수문자(> , § , | )가 포함되어있을 때 특수문자는 삭제됩니다.');?>
									<?php echo _DescStr('일괄업로드 시 사이즈,컬러에 대한 옵션유형은 포함되지 않으며 색상코드,이미지는 따로 설정해주시기 바랍니다.',''); ?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>

		<!-- 옵션데이터 {-->
		<div id="span_option_3depth" class="option_table_wrap">
			<div class="p_loading"><div class="loading"><strong>페이지 로딩중입니다</strong></div></div>
		</div>
		<!--} 옵션데이터 -->

		<div class="tip_box page_tip">
			<?php echo _DescStr('공급가 : 입점업체에 정산 시 공급가입니다. (사용자에게는 노출되지 않습니다.)','blue');?>
			<?php echo _DescStr('판매가 : 상품 판매가와는 별도이며 구매시에는 이 옵션 판매가가 적용됩니다.','blue');?>
			<?php echo _DescStr('재고량 : 상품에 재고량이 남아있더라도 옵션에 재고량이 없으면 품절처리됩니다.','blue');?>
			<?php echo _DescStr('컬러옵션에서 이미지는 자동으로 조정되며 한번에 최대 10개를 초과할 수 없습니다.', '');?>
		</div>
	</div>

	<div class="c_btnbox type_full">
		<ul>
			<li><a href="#none" onclick="category_apply_save('1depth_add','');return false;" class="c_btn blue">옵션추가</a></li>
			<li><a href="#none" onclick="submit_alert();return false;" class="c_btn h34 red">옵션저장</a></li>
			<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>
</div>


<script>
// 저장하기
function submit_alert(){
	document.frm_option.submit();
	alert('저장하였습니다.');
	category_apply();
}

// 옵션 추가
function category_apply_save(mode , uid) {
	if(mode=='1depth_del' || mode=='2depth_del' || mode=='3depth_del'){
		if(confirm("해당 옵션을 삭제하시겠습니까?")==false){
			return false;
		}
	}

	document.frm_option.submit();
	category_apply(mode , uid);
}

// 옵션 추가
function category_apply(mode , uid) {

//	if(document.frm_option) {
//		document.frm_option.submit();
//	}

	var app_option1_type = app_option2_type = app_option3_type = '';

	<?php
		// 차수 확인 --> 큰 오류가 발생할 수 있으므로 -- 차수 옵션은 한번 적용시 수정할 수 없게 봉인하여야 함
		if($pass_mode == "1depth") echo "var app_url = '_product_option1.ajax.php';";
		if($pass_mode == "2depth") echo "var app_url = '_product_option2.ajax.php';";
		if($pass_mode == "3depth") echo "var app_url = '_product_option3.ajax.php';";

		// 차수에 따른 옵션 유형 - 부모창에서 추출
		if(IN_ARRAY($pass_mode , array("1depth" , "2depth" , "3depth"))) {
			echo "app_option1_type = $(\"input[name='p_option1_type']:checked\", opener.document ).val();";
		}
		if(IN_ARRAY($pass_mode , array("2depth" , "3depth"))) {
			echo "app_option2_type = $(\"input[name='p_option2_type']:checked\", opener.document ).val();";
		}
		if(IN_ARRAY($pass_mode , array("3depth"))) {
			echo "app_option3_type = $(\"input[name='p_option3_type']:checked\", opener.document ).val();";
		}
	?>

	setTimeout(function() {
		$.ajax({
			url: app_url,
			cache: false,
			type: 'POST',
			data: 'app_mode=popup&pass_code=<?=$pass_code?>&pass_mode=' + mode + '&pass_uid=' + uid + '&app_option1_type=' + app_option1_type + '&app_option2_type=' + app_option2_type + '&app_option3_type=' + app_option3_type,
			success: function(data){
				if(data == 'is_subcategory') {
					alert('하위 카테고리가 존재하여 삭제할 수 없습니다.');
				}
				else {
					$('#span_option_3depth').html(data);
					// color picker 재 실행
					$('.js_colorpic').colorpicker({
						strings: '테마별 색상,기본색상,사용자지정,테마별 색상,돌아가기,히스토리,저장된 히스토리가 없습니다.'
					});
				}
			}
		});
	}, 500);
}

// 엑셀저장 # LDD011
function _product_option_submit() {

	if(confirm("엑셀일괄등록을 실행하시겠습니까?")) {
		document.wFrm.submit();
	}
}

// 옵션 엑셀 업로드창 열기/닫기
function option_excel_toggle() {

	var Action = $('.open_excel').hasClass('if_open') ? 'close' : 'open';
	option_excel_status(Action);
}

// 옵션엑셀업로드 펼치기 제어[M] # LDD011
function option_excel_status(Action) {

	var Target = $('.open_excel');

	if(!Action) Action = getCookie('option_excel_open');
	if(Action == 'close') Target.removeClass('if_open').hide();
	else Target.removeClass('if_open').addClass('if_open').show();

	document.cookie = 'option_excel_open='+Action+';';
}

// 지정된 이름의 쿠키를 가져온다. # LDD011
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

// 옵션 SORTing
// _type - U : up , D :down
// _depth - 1, 2, 3
// _uid  -옵션 고유번호
function f_sort(_type , _depth , _uid) {
	if( _type && _depth && _uid ) {

		$("input[name='pass_type']").val(_type);
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}

// 옵션 삽입
// _depth - 1, 2, 3
// _uid  -옵션 고유번호
function f_insert(_depth , _uid) {
	if( _depth && _uid ) {
		$("input[name='pass_type']").val("insert");
		$("input[name='pass_depth']").val(_depth);
		$("input[name='pass_uid']").val(_uid);
		document.frm_option.submit();
		category_apply();
	}
	else {
		alert("잘못된 접근입니다. 관리자에게 문의하세요.");
	}
}


$(document).ready(function() {

	option_excel_status(); // # LDD011
	category_apply();
});
</script>

<?PHP
	include_once("inc.footer.php");
?>