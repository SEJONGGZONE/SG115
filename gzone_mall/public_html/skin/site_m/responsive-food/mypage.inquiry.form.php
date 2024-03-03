<?php
$page_title = "1:1 온라인 문의";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_gridpage">
    <div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->


		<div class="grid_section type_formpage">
			<div class="layout_fix">

				<div class="c_group_tit">
					<span class="tit">1:1 온라인문의 작성</span>
					<span class="sub_txt"><strong>* </strong>체크된 항목은 필수 항목입니다.</span>
				</div>

				<form name="frm_request" id="frm_request" class="c_form" method=post action="<?php echo OD_PROGRAM_URL.'/mypage.inquiry.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
					<input type="hidden" name="_menu" value="inquiry">
					<dl class="form_dl">
						<dt class="form_dt ess"><span class="tit">제목</span></dt>
						<dd class="form_dd">
							<input type="text" name="_title" class="input_design" autocomplete="off" placeholder="제목을 입력해주세요." />
						</dd>
					</dl>
					<dl class="form_dl">
						<dt class="form_dt ess"><span class="tit">내용</span></dt>
						<dd class="form_dd">
							<textarea name="_content" rows="10" style="" class="text_design" placeholder="내용을 입력해주세요."></textarea>
							<div class="tip_txt">글 등록 시 개인정보 입력은 삼가해 주시기 바랍니다.</div>
						</dd>
					</dl>
					<dl class="form_dl">
						<dt class="form_dt"><span class="tit">첨부파일</span></dt>
						<dd class="form_dd">
							<div class="attach_box">
								<div class="duplicate list-files" data-mode="add">
									<div class="input_file_box">
										<input type="text" id="fakeFileTxt1" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="파일 등록">
										<div class="fileDiv">
											<input type="button" class="buttonImg" value="파일찾기">
											<input type="file" class="realFile" name="addFile[]"<?php echo is_mobile() === true? ' accept="image/*" ': null?> onchange="javascript:document.getElementById('fakeFileTxt1').value = this.value">
										</div>
									</div>
									<span class="add_btn_box"><a href="#none" onclick="return false;" class="file_btn exec-addfile">+ 추가</a></span>
								</div>
							</div>
							<div class="tip_txt">최대 5개까지만 등록 가능합니다.</div>
						</dd>
					</dl>
					<?php if( $inquiryData['recaptchaUse'] === true){ ?>
						<dl class="form_dl tr-recaptcha">
							<dt class="form_dt ess"><span class="tit">스팸방지</span></dt>
							<dd class="form_dd">
								<script src='https://www.google.com/recaptcha/api.js'></script>
								<div class="g-recaptcha" data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
								<div class="tip_txt">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
							</dd>
						</dl>
					<?php } ?>
				</form><!-- end c_form -->

				<div class="c_btnbox type_full">
					<ul>
						<li><a href="<?php echo $_GET['_PVSC']?"/?".enc('d',$_PVSC):"/?pn=mypage.inquiry.list" ?>" class="c_btn h50 black line">취소</a></li>
						<li><a href="#none" onclick="return false;" class="c_btn h50 black js_inquiry_submit" data-switch="on">등록완료</a></li>
					</ul>
				</div>

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->


    </div><!-- end layout_grid -->
</div><!-- end c_section -->


<script type="text/javascript">

	// 리캡챠의 크기를 재조정 한다.
	$(document).ready(function() { // 리캽챠의 크기를 고정한다.(늘아났다가 줄어드는 현상 방지)
		recaptcha_resize();
		$('.g-recaptcha').css({
			'width': $('input[name="_title"]').outerWidth()+'px'
		});
	});
	$(window).load(recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).resize(recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).on('orientationchange', recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	function recaptcha_resize() {
		var i_width = $('input[name="_title"]').outerWidth();
		var rscale = i_width/$('.g-recaptcha iframe').width();
		if(rscale > 1) return; // 스케일이 1보다 크지 않도록 조정
		$('.g-recaptcha').css({
			'width': i_width+'px',
			'transform': 'scale('+rscale+')',
			'-webkit-transform': 'scale('+rscale+')',
			'transform-origin': '0 0',
			'-webkit-transform-origin': '0 0'
		});
	}

	$(document).on('click','.js_inquiry_submit',function(){
		$('#frm_request').submit();;
	});

	$(function(){
		document.frm_request.reset(); // form 초기화
		$("#frm_request").validate({
			ignore: "input[type=text]:hidden",
		    rules: {
			 _title: { required: true, minlength: 2 }
			, _content: { required: true, minlength: 2 }
		    },
		    messages: {
				 _title: { required: "문의제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _content: { required: "문의내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
		    },
			submitHandler: function(form) {
				// -- 서브밋 연속 클릭 방지
				var chk = $('.js_inquiry_submit').attr('data-switch');
				if( chk == 'on'){
					$('.js_inquiry_submit').attr('data-switch','off');
					form.submit();
					setTimeout(function(){$('.js_inquiry_submit').attr('data-switch','on'); },3000)
				}else{
					alert("잠시만 기다려 주세요.");
					return false;
				}
			}
		});
	})

    // -- 파일 삭제
    $(document).on('click','.exec-delfile',function(){
        $(this).closest('.list-files[data-mode="add"]').remove();
        $('.list-files[data-mode="add"]').each(function(i,v){
            $(v).find('.files-idx').text(i+1);
        });
    });

    // -- 파일 추가 --
    $(document).on('click','.exec-addfile',function(){
        var idx = $('.list-files').length*1;
        var buid = $('#frmBbs [name="_uid"]').val();
        var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;

        if( idx >= upfileCnt){ alert("파일첨부는 최대 <?php echo $arrUpfileConfig['cnt']; ?>개 까지 첨부가능합니다.\n등록된 파일이 있으실경우 삭제 하신 후 추가해 주세요."); return false; }
        var url = '<?php echo OD_PROGRAM_URL.'/_pro.php'; ?>';
        $.ajax({
            url: url, cache: false,dataType : 'json', type: "get", data: {_mode:'request_add_files',idx : idx , buid : buid  }, success: function(data){
                if( data.rst == 'success') {
                    $('.list-files:last-child').after(data.html);
                    return true;
                }else{
                    return false;
                }
            },error:function(request,status,error){ console.log(request.responseText);}
        });
    });

</script>
