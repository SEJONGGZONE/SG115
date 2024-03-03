<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = "제휴문의";
//include_once($SkinData['skin_root'].'/community.header.php'); // 상단 헤더 출력
?>

<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $page_title;?></div>
        </div>
    </div>
</div><!-- end c_page_tit -->



<div class="c_section c_formpage">
    <div class="layout_fix">

        <form name="frm_partner" id="frm_partner" class="c_form" method=post action="<?php echo OD_PROGRAM_URL.'/service.partner.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
            <input type="hidden" name="_menu" value="partner">

            <div class="c_group_tit">
                <span class="tit"><?php echo $page_title;?> 작성하기</span>
                <span class="sub_txt"><strong>* </strong>체크된 항목은 필수 항목입니다.</span>
            </div>

            <dl class="form_dl">
                <dt class="form_dt ess"><span class="tit ">이름/상호명</span></dt>
                <dd class="form_dd">
                    <input type="text" name="_comname" class="input_design" value="<?php echo $mem_info['in_name']; ?>" autocomplete="off" placeholder="이름/상호명"/>
                </dd>
            </dl>
            <dl class="form_dl">
                <dt class="form_dt ess"><span class="tit ">연락처</span></dt>
                <dd class="form_dd">
                    <input type="tel" name="_tel" class="input_design" value="<?php echo $mem_info['in_tel2']; ?>" autocomplete="off"  placeholder="연락처"/>
                </dd>
            </dl>
            <dl class="form_dl">
                <dt class="form_dt ess"><span class="tit ">이메일 주소</span></dt>
                <dd class="form_dd">
                    <div class="with_btn">
                        <input type="hidden" name="join_email_check" class="js_join_email_check" value="<?php echo ($mem_info['in_email'] != ''?'1':''); ?>">
                        <input type="hidden" name="join_email" class="js_join_email" value="<?php echo $mem_info['in_email']; ?>">
                        <?php
                            $_email_prefix = $_email_suffix = '';
                            if($mem_info['in_email']) {
                                $_email_arr = explode('@', $mem_info['in_email']);
                                $_email_prefix = $_email_arr[0];
                                $_email_suffix = $_email_arr[1];
                            }
                        ?>
                        <input type="text" name="_email_prefix" class="input_design js_email_prefix" value="<?php echo $_email_prefix; ?>" autocomplete="off" placeholder="이메일 아이디" />
                        <span class="unit">＠</span>
                        <div class="c_select">
                            <select name="_email_suffix_select" class="js_email_suffix_select">
                                <option value="" selected="">선택</option>
                                <?php foreach($email_suffix as $ek=>$ev) { ?>
                                    <option value="<?php echo $ev; ?>"<?php echo ($_email_suffix == $ev?' selected':($_email_suffix!='' && !in_array($_email_suffix, $email_suffix) && $ev == 'direct'?' selected':null)); ?>><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
                                <?php } ?>
                            </select>
                            <span class="icon"></span>
                        </div>
                    </div>
                    <div class="other" style="display: none;"><input type="text" name="_email_suffix_input" autocomplete="off" placeholder="직접입력" value="<?php echo $_email_suffix; ?>" class="input_design js_email_suffix_input" /></div>
                </dd>
            </dl>
            <dl class="form_dl">
                <dt class="form_dt ess"><span class="tit ">제목</span></dt>
                <dd class="form_dd">
                    <input type="text" name="_title" class="input_design" autocomplete="off" placeholder="제목을 입력해주세요." />
                </dd>
            </dl>
            <dl class="form_dl">
                <dt class="form_dt ess"><span class="tit ">내용</span></dt>
                <dd class="form_dd">
                    <textarea name="_content" rows="5" class="text_design" placeholder="내용을 입력해주세요."></textarea>
                    <div class="tip_txt">글 등록 시 개인정보 입력은 삼가해 주시기 바랍니다.</div>
                </dd>
            </dl>
            <dl class="form_dl">
                <dt class="form_dt"><span class="tit ">첨부파일</span></dt>
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
            <?php if( $partnerData['recaptchaUse'] === true) { ?>
            <dl class="form_dl tr-recaptcha">
                <dt class="form_dt ess"><span class="tit ">스팸방지</span></dt>
                <dd class="form_dd">
                    <?php // 스팸방지 들어감 ?>
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    <div class="g-recaptcha"  data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
                    <div class="tip_txt">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
                </dd>
            </dl>
            <?php } ?>

            <dl class="form_dl">
                <dd class="form_dd js_box_open">
                    <div class="form_tit">
                        <label class="c_label"><input type="checkbox" name="_agree" value="Y"/><span class="tx"><span class="icon"></span>개인정보처리방침 동의</span></label>
                        <a href="#none" class="btn_open js_btn_open" onclick="return false;" title="약관 열고닫기"></a>
                    </div>
                    <div class="open_box">
                        <textarea cols="" rows="8" class="text_design" readonly="readonly"><?php echo $partnerData['partnerAgree']; ?></textarea>

                        <div class="user_info_box">
							<?php foreach($privacy_table as $stk=>$stv) { ?>
								<?php foreach($stv as $stsk=>$stsv) { ?>
								<dl>
									<dt><?php echo ($stsv['required'] == 'Y'?'필수':'선택'); // 필수여부 ?></dt>
									<dd>
										이용 목적 : <?php echo $stsv['name']; // 이용목적 ?><br/>
										수집 항목 : <?php echo implode(', ', $stsv['item']); // 수집항목 ?>
									</dd>
								</dl>
								<?php }?>
							<?php }?>

							<?php foreach($privacy_table as $stk=>$stv) { ?>
                            <dl>
                                <dt></dt>
                                <dd>
                                    <?php echo $stv[0]['destruction']; // 보존 및 파기 ?>
                                </dd>
                            </dl>
							<?php }?>
                        </div>
                    </div><!-- end open_box -->
                </dd>
            </dl>

            <div class="c_btnbox type_full">
                <ul>
                    <li><a href="#none" class="c_btn h50 black line" onclick="history.go(-1); return false;">취소</a></li>
                    <li><a href="#none" onclick="return false;" class="c_btn h50 black js_partner_submit" data-switch="on">등록완료</a></li>
                </ul>
            </div>
        </form><!-- end c_form -->

    </div>
</div><!-- end c_section -->

<script>

	// 파일첨부시 유효성 체크 
	$(document).on('change','input[name="addFile[]"]',function(){
		if($(this).val() == ''){ return true; }
		var $parents = $(this).closest('.duplicate.list-files');
		var fakeFileTxt_ID = $parents.find('.fakeFileTxt').attr('id');
		var files = $(this)[0].files[0];
		var check = false;
		var errmsg = "해당 파일은 첨부하실 수 없습니다.\n(<?php echo implode(",",$arrUpfileConfig['ext']['all']) ?> 파일만 가능)";
		try{
			var fileExtCheck = new Array('<?php echo implode("','",$arrUpfileConfig['ext']['all']);  ?>');
			var fileSzieChcek = <?php echo $arrUpfileConfig['size'] > 0 ? $arrUpfileConfig['size'] : 0 ; ?>;

			if( typeof files.name != 'string'){ throw errmsg;}
			var split = files.name.split(".");
			var ext = split[split.length-1];
			if( typeof ext == 'undefined'){throw errmsg;}
			if( $.inArray(ext,fileExtCheck) < 0){ throw errmsg; }
			if( files.size > fileSzieChcek){
				throw "파일용량이 초과되었습니다. 최대 "+Math.round(fileSzieChcek/1048576)+"MB 까지 가능합니다.";
			}
			check = true;
		}
		catch(e){
			if( typeof e != 'object'){ errmsg = e;}
		}
		if( check !== true){
			alert(errmsg);
			$(this).val('');
			$('#'+fakeFileTxt_ID).val('');
		}
		return true;
	});

	// 리캡챠의 크기를 재조정 한다.
	$(document).ready(function() { // 리캽챠의 크기를 고정한다.(늘아났다가 줄어드는 현상 방지)
		recaptcha_resize();
		$('.g-recaptcha').css({
			'width': $('input[name="_title"]').outerWidth()+'px'
		});

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");

	});
	$(function(){recaptcha_resize();}); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
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

	// 이메일 항목제어
	$(function(){join_email_form_view();})
	$(document).on('change', '.js_email_suffix_select', join_email_form_view);
	function join_email_form_view() {
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_select option:selected').val();
		var save_value = $('.js_join_email').val();
		var r_val = '';
		if(save_value != i_value.replace('@', '')+'@'+$('.js_email_suffix_input').val().replace('@', '')) $('.js_join_email_check').val('');
		if(s_value == 'direct') {
			$('.js_email_suffix_input').closest('div').show();
		}
		else {
			$('.js_email_suffix_input').val(s_value);
			$('.js_email_suffix_input').closest('div').hide();
			r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
			$('.js_join_email').val(r_val);
		}
	}

	$(document).on('keyup', '.js_email_prefix', function(e) {
		var i_value = $(this).val();
		var s_value = $('.js_email_suffix_input').val();
		var r_val = '';
		$('.js_join_email_check').val('');
		if(i_value.split('@').length > 1) {
			$(this).val($(this).val().replace('@', ''));
			$('.js_email_suffix_input').val('');
			$('.js_email_suffix_select').val('direct');
			$('.js_email_suffix_input').closest('div').show();
			$('.js_email_suffix_input').focus();
		}
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	});
	$(document).on('keyup', '.js_email_suffix_input', function(e) {
		var su = $(this);
		var i_value = $('.js_email_prefix').val();
		var s_value = $(this).val().replace('@', '');
		var r_val = '';
		$('.js_join_email_check').val('');
		if(s_value) {
			$.each($('.js_email_suffix_select option'), function(k, v){
				if($(v).val() == s_value.replace('@', '')) {
					su.hide();
					$('.js_email_suffix_select').val($(v).val());
				}
			});
		}
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	});

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
        var url = '<?php echo OD_PROGRAM_URL.'/board.ajax.php'; ?>';
        $.ajax({
            url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
                if( data.rst == 'success') {
                    $('.list-files:last-child').after(data.html);
                    return true;
                }else{
                    return false;
                }
            },error:function(request,status,error){ console.log(request.responseText);}
        });
    });

	$(document).on('click','.js_partner_submit',function(){
		$('#frm_partner').submit();
	});

	$(function(){
		//document.frm_partner.reset(); // 2019-04-09 SSJ :: 폼이 리셋되면서 이메일 유효성검사 시 오류 발생
		$("#frm_partner").validate({
			ignore: "input[type=text]:hidden",
		    rules: {
			 _comname: { required: true, minlength: 2 }
			, _tel: { required: true, minlength: 8 }
			, _title: { required: true, minlength: 2 }
			, _content: { required: true, minlength: 2 }
			, _email_prefix: { required : true }
			, _email_suffix_input: { required : true }
			, join_email: { required : true, email_check: true }
		    , _agree: { required: true }
		    },
		    messages: {
				_comname: { required: "이름/상호명을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _tel: { required: "연락처를 입력하세요", minlength: "8글자 이상 등록하셔야 합니다." }
				, _title: { required: "문의제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _content: { required: "문의내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _email_prefix: { required : '이메일 아이디를 입력해주세요' }
				, _email_suffix_input: { required : '이메일 주소를 '+($('.js_email_suffix_input').is(':visible')?'입력':'선택')+'해주세요' }
				, join_email: { required : '이메일 주소를 입력해주세요', email_check: '유효하지 않은 E-Mail주소입니다' }
				, _agree: { required: "개인정보처리방침 동의후 이용가능합니다." }
		    },
			submitHandler: function(form) {


				// -- 서브밋 연속 클릭 방지
				var chk = $('.js_partner_submit').attr('data-switch');
				if( chk == 'on'){
					$('.js_partner_submit').attr('data-switch','off');
					form.submit();
					setTimeout(function(){$('.js_partner_submit').attr('data-switch','on'); },3000)
				}else{
					alert("잠시만 기다려 주세요.");
					return false;
				}
			}
		});

	})


</script>
