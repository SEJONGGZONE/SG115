

<div id="ID_qna_list">
	<?php
		// 내용추출
		//$pcode= $pcode;
		$_mode = 'view';
		$talk_type = 'qna';
		$listpg = 1;
		include OD_PROGRAM_ROOT.'/product.qna.pro.php';
	?>
</div><!-- end p_Vqna -->


<?php // 문의 폼 레이어 ?>
<div class="c_layer type_qna" id="js_qna_form">
	<div class="wrapping">
		<div class="tit_box">
			<div class="tit">상품문의 작성하기</div>
			<a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".c_layer.type_qna" data-add="if_open_layer" title="닫기"></a>
		</div><!-- end tit_box -->

		<form method="post" name="qna_frm" id="qna_frm" class="conts_box c_scroll_v" enctype="multipart/form-data" target="common_frame_qna" action="<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php">
			<input type=hidden name="talk_type" value="qna">
			<input type="hidden" name="_mode" value="add"/>
			<input type="hidden" name="pcode" value="<?=$pcode?>"/>
            <div class="c_form type_single">
                <ul class="form_ul">
                    <li class="form_li">
                        <?php if(is_login()){ ?>
                            <input type="text" name="_title" id="qna_title" class="input_design" placeholder="제목을 입력해주세요.">
                        <?php }else{ ?>
                            <input type="text" name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" class="input_design" value="" placeholder="제목을 입력해주세요." readonly>
                        <?php } ?>
                    </li>
                    <li class="form_li this_full">
                        <?php if(is_login()){ ?>
                            <textarea name="_content" id="qna_content" cols="" rows="6" class="text_design" placeholder="내용을 입력해주세요."></textarea>
                        <?php }else{ ?>
                            <textarea name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;"  cols="" rows="8" class="text_design" placeholder="내용을 입력해주세요." readonly></textarea>
                        <?php } ?>
                        <div class="tip_txt">상품과 관련 없는 글은 관리자에 의해 임의로 삭제될 수 있습니다.</div>
                    </li>
                </ul>
            </div><!-- end c_form -->
		</form><!-- end conts_box -->

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h40 black line js_onoff_event" data-target=".c_layer.type_qna" data-add="if_open_layer">취소</a></li>
				<li><a href="#none" onclick="qna_add(); return false;" class="c_btn h40 black">등록완료</a></li>
			</ul>
		</div><!-- end c_btnbox -->
	</div>
	<div onclick="return false;" class="bg_close js_onoff_event" data-target=".c_layer.type_qna" data-add="if_open_layer"></div>
</div><!-- end c_layer -->





<script>

function iframe_init_qna(_type)
{
	if(_type == true){
		$('body').after('<iframe name="common_frame_qna" id="common_frame_qna" width="150" height="150" frameborder="0" style="display:none;"></iframe>')
	}else{
		$('#common_frame_qna').remove();
	}
}

// 상품평 쓰기 폼 노출
function qna_write_form_view() {
	$('#js_qna_form').lightbox_me({
		centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
		overlayCSS:{background:'#000', opacity: 0.7},
		onLoad: function() {},
		onClose: function(){}
	});
}



// 상품평 쓰기
function qna_add() {
<?PHP
	if( !is_login() ) echo 'login_alert("'. urlencode($_rurl) .'"); return false;';
?>


	if(!confirm('상품문의를 등록하시겠습니까?')) return false;

	if($('#qna_frm').valid()){
		$('#qna_frm').submit();
		$('.c_layer.type_qna').removeClass('if_open_layer');
	}

}


$(document).ready(function(){
	$("#qna_frm").validate({
		rules: {
			_title: { required: true },
			_content: { required : true }
		},
		messages: {
			_title: { required: '제목을 입력하세요.' },
			_content: { required: '내용을 입력하세요.' }
		},
		submitHandler : function(form) {
			iframe_init_qna(true);
			form.submit(); setTimeout(function() { form.reset(); },100);
		}
	});
});


// 갯수 추출
function qna_get_cnt() {
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=getcnt&pcode=<?=$pcode?>",
		success: function(data){
			$(".qna_cnt").html(data);
		}
	});
}

// 리뷰 삭제
function qna_del(uid) {
<?PHP
	if( !is_login() ) {
	echo 'alert("먼저 로그인 하세요"); location.href = "/?pn=member.login.form&_rurl='.urlencode("/?".$_SERVER[QUERY_STRING]).'"; ';
	}
	else {
?>
	if(confirm("정말 삭제하시겠습니까?")) {
		$.ajax({
			url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
			cache: false,
			type: "POST",
			data: "_mode=delete&uid=" + uid ,
			success: function(data){
				if( data == "no data" ) {
					alert('등록하신 글이 아닙니다.');
				}
				else if( data == "is reply" ) {
					alert('댓글이 있으므로 삭제가 불가합니다.');
				}
				else {
					alert('정상적으로 삭제하였습니다.');
					qna_view();
				}
			}
		});
	}
<?PHP
	}
?>
}
// 리뷰 보기
function qna_view(cnt,listpg) {
	$('#js_qna_form').trigger('close');
	$('#js_qna_view').trigger('close');

	if(listpg == undefined) listpg = 1;
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=view&talk_type=qna&pcode=<?=$pcode?>&listpg="+listpg,
		success: function(data){
			$("#ID_qna_list").html(data);
		}
	});
	qna_get_cnt();
}

var old_qna_id;
function qna_show(id) {
	if($("#"+id).length < 1) return false;
	// hit cnt 처리를 위한 변수
	var _uid = id.replace('view_','');


	// 상품리뷰 불러오기
	var _html = $("#"+id).find('.popup_html').html();
	$('#js_qna_view .content_area').html(_html)
	$('#js_qna_view').lightbox_me({
		centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
		overlayCSS:{background:'#000', opacity: 0.7},
		onLoad: function() {},
		onClose: function(){}
	});


	// hit cnt 증가
	var _smode = ($('#'+id).attr('data-hit') == 'false'?'update':'nocount');
	if(_smode == 'update') {
		$.ajax({
			data: {
				_mode: 'eval_hit',
				_smode: _smode,
				_uid: _uid
			},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
			success: function(data) {
				// 중복 hit차단
				$('#'+id).attr('data-hit', 'true');
			}
		});
	}
}

</script>