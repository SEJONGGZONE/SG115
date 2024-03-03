

<?php // 리뷰 타이틀 ?>
<div id="ID_eval_list">

<?php // 리뷰 목록 ?>
<?php
	// ajax를 통해 파일불러옴 skin/site_m/product.eval.view.php 에서 수정
	// 내용추출
	//$pcode= $pcode;
	$_mode = 'view';
	$talk_type = 'eval';
	$listpg = 1;
	include OD_PROGRAM_ROOT.'/product.eval.pro.php';
?>

</div>

<?php // 리뷰 폼 레이어 ?>
<div class="c_layer type_review" id="js_eval_form">
	<div class="wrapping">
		<div class="tit_box">
			<div class="tit">상품리뷰 작성하기</div>
			<a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".c_layer.type_review" data-add="if_open_layer" title="닫기"></a>
		</div><!-- end tit_box -->

		<form method="post" name="eval_frm" id="eval_frm" class="conts_box c_scroll_v" enctype="multipart/form-data" target="common_frame_eval" action="<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php">
			<input type=hidden name='talk_type' value='eval'>
			<input type="hidden" name="_mode" value="add"/>
			<input type="hidden" name="pcode" value="<?=$pcode?>"/>
			<input type="hidden" name="_eval_point" id="eval_point" value="100"/>
            <div class="c_form type_single">
                <ul class="form_ul">
                    <li class="form_li">
                        <div class="score_box if_score5 toggleScore">
                            <div class="star_box">
                                <ul>
                                    <li><a href="#none" onclick="return false;" data-score="20" class="click" title="1점"></a></li>
                                    <li><a href="#none" onclick="return false;" data-score="40" class="click" title="2점"></a></li>
                                    <li><a href="#none" onclick="return false;" data-score="60" class="click" title="3점"></a></li>
                                    <li><a href="#none" onclick="return false;" data-score="80" class="click" title="4점"></a></li>
                                    <li><a href="#none" onclick="return false;" data-score="100" class="click" title="5점"></a></li>
                                </ul>
                                <span class="mark">
                                    <span class="star this_value"></span>
                                    <span class="star this_base"></span>
                                </span>
                                <script>
                                    $(document).ready(function(){
                                        $('.toggleScore a.click').on('click',function(){
                                            $('.toggleScore').attr('class','score_box toggleScore');
                                            $('.toggleScore').addClass('if_score'+($(this).data('score')/20));
                                            $('#eval_point').val($(this).data('score'));
                                        });
                                    });
                                </script>
                            </div>
                            <div class="tip_txt">별을 클릭하면 원하는 점수를 선택하실 수 있습니다.</div>
                        </div>
                    </li>
                    <li class="form_li js_image_preview_box">
                        <div class="upload_box">
                            <label class="label_photo">
                                <?php if(is_login()){ ?>
                                    <input type="file" name="_img" accept="image/*"  class="input_photo js_image_preview_input_file"  />
                                    <span class="upper_txt fakeFileTxt js_img_title">사진등록</span>
                                <?php }else{ ?>
                                    <input type="file" name="_img" accept="image/*"  class="input_photo js_image_preview_input_file"  onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" />
                                    <span class="upper_txt fakeFileTxt js_img_title">사진등록</span>
                                <?php } ?>
                            </label>
                            <div class="preview js_image_preview" style="display:none;"><img id="img_preview"></div>
                        </div>
                        <div class="tip_txt">사진과 함께 등록 시 <strong><?php echo number_format($siteInfo['s_productevalpoint']);?>원의 적립금</strong>을 드립니다.</div>
                        <script type="text/javascript">
                            // 첨부이미지 미리보기
                            $(document).on('change', '.js_image_preview_input_file', function(e) {
                            	var su = $(this);
                            	var errmsg = '';
								try{
									var fileSzieChcek = <?php echo $arrUpfileConfig['size'] > 0 ? $arrUpfileConfig['size'] : 0 ; ?>;
									var files = $(this)[0].files[0];
									if( typeof files.name != 'string'){ throw errmsg;}
									var split = files.name.split(".");
									var ext = split[split.length-1];
									if( typeof ext == 'undefined'){throw errmsg;}
									if( files.size > fileSzieChcek){
										throw "이미지 용량이 초과되었습니다. 최대 "+Math.round(fileSzieChcek/1048576)+"MB 까지 가능합니다.";
									}


									check = true;
								}
								catch(se){
									if( typeof se != 'object'){ errmsg = se;}
								}

								if( errmsg !=''){ 
									su.val(''); 

                                    su.closest('.js_image_preview_box').find('.fakeFileTxt').text('(jpg,jpeg,png,gif)');
                                    su.closest('.js_image_preview_box').find('.js_image_preview').find('img').attr('src', '');
                                    su.closest('.js_image_preview_box').find('.js_image_preview').slideUp();

									alert(errmsg);  
									return false; 
								}

                                var fval = su.val();
                                var ext = su.val().split('.').pop().toLowerCase();
                                if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                                    su.val('');
                                    su.closest('.js_image_preview_box').find('.fakeFileTxt').text('(jpg,jpeg,png,gif)');
                                    su.closest('.js_image_preview_box').find('.js_image_preview').find('img').attr('src', '');
                                    su.closest('.js_image_preview_box').find('.js_image_preview').slideUp();                                    
                                    alert('이미지만 첨부가능 합니다. (gif, png, jpg, jpeg)');
                                }
                                else {
                                    var file = su.prop('files')[0];
                                    var blobURL = window.URL.createObjectURL(file);
                                    su.closest('.js_image_preview_box').find('.fakeFileTxt').text(fval);
                                    su.closest('.js_image_preview_box').find('.js_image_preview').find('img').attr('src', blobURL);
                                    su.closest('.js_image_preview_box').find('.js_image_preview').slideDown();
                                }
                            });
                        </script>
                    </li>
                    <li class="form_li this_full">
                        <?php if(is_login()){ ?>
                            <textarea name="_content" id="eval_content" cols="" rows="6" class="text_design" placeholder="내용을 입력해주세요."></textarea>
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
				<li><a href="#none" onclick="return false;" class="c_btn h40 black line js_onoff_event" data-target=".c_layer.type_review" data-add="if_open_layer">취소하기</a></li>
				<li><a href="#none" onclick="eval_add(); return false;" class="c_btn h40 black">등록완료</a></li>
			</ul>
		</div><!-- end c_btnbox -->
	</div>
	<div onclick="return false;" class="bg_close js_onoff_event" data-target=".c_layer.type_review" data-add="if_open_layer"></div>
</div><!-- end c_layer -->



<script>

	function iframe_init(_type)
	{
		if(_type == true){
			if( $('#common_frame_eval').length < 1) {
				$('body').after('<iframe name="common_frame_eval" id="common_frame_eval" width="150" height="150" frameborder="0" style="display:none;"></iframe>')
			}
		}else{
			$('#common_frame_eval').remove();
		}
	}


	// 상품리뷰 레이어 창 열기
	$(document).on('click', '.js_review_open', function(){
		var review_form_chk = $('.c_layer.type_review').hasClass('if_open_layer');

		if(review_form_chk==true){
			$('.c_layer.type_review').removeClass('if_open_layer');
		}else{
			$('.c_layer.type_review').addClass('if_open_layer');
			$('#img_preview').removeAttr('src');	// 리뷰내용 초기화
			$('#eval_content').val('');	// 리뷰내용 초기화
			$('.js_img_title').text('사진 등록 (jpg,jpeg,png,gif)');
			$('.js_image_preview').hide();
		}
	});



	// 상품평 쓰기
	function eval_add() {
	<?PHP
		if( !is_login() ) echo 'login_alert("'. urlencode($_rurl) .'"); return false;';
	?>

		if(!confirm("상품리뷰를 등록하시겠습니까?")) return false;

		if($('#eval_frm').valid()){
			$('#eval_frm').submit();
			$('.c_layer.type_review').removeClass('if_open_layer');
		}

	}


	$(document).ready(function(){
		$("#eval_frm").validate({
			rules: {
				_eval_point: { required: true },
				_content: { required : true }
			},
			messages: {
				_eval_point: { required: "별점을 선택하세요." },
				_content: { required: "내용을 입력하세요." }
			},
			submitHandler : function(form) {
				iframe_init(true);
				form.submit();
			}
		});
	});

	// 갯수 추출
	function eval_get_cnt() {
		$.ajax({
			url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
			cache: false,
			type: "POST",
			data: "_mode=getcnt&pcode=<?=$pcode?>",
			success: function(data){
				// 탭에 있는 리뷰카운트 수
				$(".js_tap_eval_cnt").html('('+data+')');

				// 건의 리뷰가 있습니다의 리뷰카운트 수
				$(".js_review_top .eval_cnt").html(data);

			}
		});
	}

	// 리뷰 삭제
	function eval_del(uid) {
	<?PHP
		if( !is_login() ) {
		echo 'alert("로그인 후 삭제가 가능합니다."); location.href = "/?pn=member.login.form&_rurl='.urlencode("/?".$_SERVER[QUERY_STRING]).'"; ';
		}
		else {
	?>
		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
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
						eval_view();
					}
				}
			});
		}
	<?PHP
		}
	?>
	}
	// 리뷰 보기
	function eval_view(cnt,listpg) {
		$('#js_eval_form').trigger('close');
		$('#js_eval_view').trigger('close');

		if(cnt<=0){
			$('.js_review_top').hide();
		}else{
			$('.js_review_top').show();
		}


		if(listpg == undefined) listpg = 1;
		$.ajax({
			url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
			cache: false,
			type: "POST",
			data: "_mode=view&talk_type=eval&pcode=<?=$pcode?>&listpg="+listpg,
			success: function(data){
				$("#ID_eval_list").html(data);
			}
		});
		eval_get_cnt();

	}

	var old_eval_id;
	function eval_show(id) {
		if($("#"+id).length < 1) return false;
		// hit cnt 처리를 위한 변수
		var _uid = id.replace('view_','');


		// 상품리뷰 불러오기
		var _html = $("#"+id).find('.popup_html').html();
		$('#js_eval_view .content_area').html(_html)
		$('#js_eval_view').lightbox_me({
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