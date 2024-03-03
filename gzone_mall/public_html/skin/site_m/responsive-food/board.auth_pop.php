<?php
	if( in_array($_mode,array('view','modify','delete')) == true) {
		ob_start();
?>
<?php // 팝업 / 팝업 사이즈는 컨텐츠 마다 별도 ?>
<form name="bbsAuth" class="wrapping" action="<?php echo OD_PROGRAM_URL.'/board.auth.php'; ?>" target="common_frame" onsubmit="return false;">
	<input type="hidden" name="_mode" value="<?php echo $_mode ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid?>">
	<div class="tit_box">
		<div class="tit">
			<?php if( $_mode == 'view') { // 비밀글 보기일경우   ?>
			비밀글 열람하기
			<?php }else if($_mode == 'modify'){ // 비회원 글 수정일경우 ?>
			게시물 수정
			<?php }else if($_mode == 'delete') { // 게시물 삭제하기 ?>
			게시물 삭제
			<?php } ?>
		</div>
		<a href="#none" onclick="return false;" class="btn_close close js_onoff_event" data-target=".c_layer.type_password" data-add="if_open_layer" title="닫기"></a>
	</div>
	<div class="conts_box c_scroll_v">
		<input type="password" name="passwd" class="input_design" placeholder="게시글 비밀번호" autocomplete="new-password">
	</div>
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="return false;" class="c_btn h40 light line close js_onoff_event" data-target=".c_layer.type_password" data-add="if_open_layer">취소</a></li>
			<li><a href="#none" class="c_btn h40 black confirm secret_submit" onclick="return false;">확인</a></li>
		</ul>
	</div>
</form><!-- end wrapping -->
<div class="bg_close js_onoff_event" data-target=".c_layer.type_password" data-add="if_open_layer" ></div>
<?php
		$html = ob_get_contents();
		ob_end_clean();
		echo json_encode(array('rst'=>'success','html'=>$html)); exit;

	}else{ // 뷰일 시
?>

<?php // ajax 처리 {{{  ?>
<div class="c_layer type_password js_secret_pop"></div>
<div class="js_board_auth_rst" style="display:none;"></div><?php // LCY : 2023-01-20 : 권한수정 -- 게시글 비밀번호 입력 후 뒤로가기 보정  ?>
<?php // ajax 처리 }}}  ?>

<script>
// -- 권항없는  클릭시
$(document).on('click', '.js_auth_fail', function(e) {
	alert("본 게시글에 대한 권한이 없습니다.");
	return false;
});
// -- 권한요청 ::  클릭시
$(document).on('click', '.js_open_auth_pop', function(e) {
	var _uid = $(this).attr('data-uid');
	var _mode = $(this).attr('data-mode');

	var open_chk = $('.js_secret_pop').hasClass('if_open_layer');

	if(open_chk==false){
		if( _uid == undefined || _uid == '' || _mode == undefined || _mode == ''){ alert("접근 권한이 없습니다."); return false; }

		$('.js_secret_pop').addClass('if_open_layer');
		var url = '<?php echo OD_PROGRAM_URL.'/board.auth_pop.php'; ?>';
		$.ajax({
			url: url, cache: false,dataType : 'json', type: "get", data: { _uid  : _uid , _mode : _mode }, success: function(data){
				console.log(data);
				if(data.rst == 'success'){
					$('.js_secret_pop').html(data.html);
				}else{
					$('.js_secret_pop').trigger('close');
				}
			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}else{
		$('.js_secret_pop').removeClass('if_open_layer');
		// 데이터 삭제
		$('form[name="bbsAuth"]').find('[name="_uid"]').val('');
	}

	return false;
});

//  입력 시
$(document).on('click', '.secret_submit', function(e) {
	var chk = $('form[name="bbsAuth"]').find('[name="passwd"]').val();
	if( chk == undefined){ chk = ''; }
	if( chk.replace(/\s/gi,'') == ''){
		alert('비밀번호를 입력해 주세요.');
		$('form[name="bbsAuth"]').find('[name="passwd"]').focus();
		return false;
	}

	// LCY : 2023-01-20 : 권한수정 -- 게시글 비밀번호 입력 후 뒤로가기 보정
	var data = $('form[name="bbsAuth"]').serialize();
	$.ajax({
		url: $('form[name="bbsAuth"]').attr('action') , cache: false,dataType : 'html', type: "get", data: data, success: function(data){
			$('.js_board_auth_rst').html(data);
		},error:function(request,status,error){ console.log(request.responseText);}
	});
});
</script>


<?php } ?>