<?php # 자주쓰는 옵션 팝업 폼
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
			</div>
			<div class="right_box">
				<a href="#none" onclick="$('.js_thisform').toggle(); return false;" class="c_btn icon icon_excel only_pc_view">엑셀 일괄등록</a>
			</div>
		</div>

		<div class="data_search js_thisform" style="display:none;">
			<form action="_product.common_option.excel_pro.php" name="wFrm" method="post" enctype="multipart/form-data">
				<input type="hidden" name="tran_type" value="ins_excel">
				<input type="hidden" name="pass_common_mode" value="<?=$pass_common_mode?>"> <?php // 자주쓰는 옵션 차수 ?>
				<input type="hidden" name="pass_common_uid" value="<?=$pass_common_uid?>"> <?php // -- 자주쓰는옵션 고유번호?>
				<table class="table_form">
					<colgroup>
						<col width="140"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>일괄업로드</th>
							<td>
								<div class="input_file" style="width:300px">
									<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
									<div class="fileDiv">
										<input type="button" class="buttonImg" value="파일찾기">
										<input type="file" name="w_excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
									</div>
								</div>
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row">
									<a href="/upfiles/normal/option<?php echo $pass_common_mode.'depth'; ?>_sample.xls" class="c_btn green line">샘플파일 다운로드</a>
									<a href="#none" onclick="_common_option_submit();return false;" class="c_btn h27 green">엑셀파일 업로드</a>
								</div>
							</td>
						</tr>
						<tr>
							<th>참고사항</th>
							<td>
								<div class="tip_box">
									<?php echo _DescStr('엑셀 일괄등록의 경우, 업로드를 함과 동시에 자동으로 정보가 저장 됩니다.');?>
									<?php echo _DescStr('차수에 맞게 옵션을 등록할 수 있습니다. (샘플파일을 다운받아서 업로드 하시기 바랍니다.)');?>
									<?php echo _DescStr('파일은 최대 '. $MaxUploadSize .'까지 업로드 가능 하며, 용량에 따라 다소시간이 걸릴 수 있습니다.');?>
									<?php echo _DescStr('1차, 2차, 3차 옵션의 합산명칭이 같은 경우에는 기존 항목이 수정되고, 다를 경우 새롭게 추가 됩니다.');?>
									<!-- KAY : 상품일괄업로드 개선 : 2021-07-02 -- 일괄업로드 문구 수정 -->
									<?php //echo _DescStr('엑셀97~2003 버전 파일만 업로드가 가능하며, 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록해주십시오.');?>
									<?php echo _DescStr('옵션 유형이 컬러인 경우, 색상과 이미지는 엑셀을 통해 일괄 등록할 수 없습니다.', 'black');?>
									<?php echo _DescStr('옵션명 입력 시 특수문자(> , § , | )가 포함되어있을 때 특수문자는 삭제됩니다.');?>
									<!-- KAY : 상품일괄업로드 개선 : 2021-07-02 -- 일괄업로드 문구 수정 -->
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>



		<!-- 옵션데이터 {-->
		<div id="span_option_3depth" class="option_table_wrap"></div>
		<!--} 옵션데이터 -->


		<div class="tip_box page_tip">
			<?php echo _DescStr('옵션 정보 입력 후 반드시 [옵션저장]버튼을 클릭하여 변경 및 추가 내용을 저장하시기 바랍니다.');?>
			<?php echo _DescStr('공급가, 판매가 항목은 입력 금액을 적용하는 방식으로 적용됩니다. 예) 상품 판매가 : <em>10,000원</em> ,옵션 판매가 : <em>15,000원</em> 일 경우 옵션 적용 시 <em>15,000원</em>이 적용됩니다.');?>
			<?php echo _DescStr('컬러옵션에서 이미지는 자동으로 조정되며 되도록 50px~100px정도로 업로드해주세요.', '');?>
			<?php echo _DescStr('컬러옵션에서 이미지일 경우 한번에 등록할 수 있는 이미지의 개수는 최대 10개를 초과할 수 없습니다.', 'black');?>
		</div>

	</div>

	<!-- 가운데정렬버튼 -->
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
		if(rm_str($pass_common_mode) == "1") echo "var app_url = '_product.common_option1.ajax.php';";
		if(rm_str($pass_common_mode) == "2") echo "var app_url = '_product.common_option2.ajax.php';";
		if(rm_str($pass_common_mode) == "3") echo "var app_url = '_product.common_option3.ajax.php';";

		// 차수에 따른 옵션 유형 - 부모창에서 추출
		if(IN_ARRAY($pass_common_mode , array("1" , "2" , "3"))) {
			echo "app_option1_type = $(\"input[name='cos_option1_type']:checked\", opener.document ).val();";
		}
		if(IN_ARRAY($pass_common_mode , array("2" , "3"))) {
			echo "app_option2_type = $(\"input[name='cos_option2_type']:checked\", opener.document ).val();";
		}
		if(IN_ARRAY($pass_common_mode , array("3"))) {
			echo "app_option3_type = $(\"input[name='cos_option3_type']:checked\", opener.document ).val();";
		}
	?>

	console.log('&app_option1_type=' + app_option1_type + '&app_option2_type=' + app_option2_type + '&app_option3_type=' + app_option3_type)

	setTimeout(function() {
		$.ajax({
			url: app_url,
			cache: false,
			type: 'POST',
			data: 'app_mode=popup&pass_common_uid=<?=$pass_common_uid?>&pass_mode=' + mode + '&pass_uid=' + uid  + '&app_option1_type=' + app_option1_type + '&app_option2_type=' + app_option2_type + '&app_option3_type=' + app_option3_type,
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
function _common_option_submit() {

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