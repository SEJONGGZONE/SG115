<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '회원가입'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>

<div class="c_section c_formpage">
	<div class="layout_fix">

		<form name="join_form" class="js_join_form c_form" action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" method="post" target="common_frame" autocomplete="off">
			<input type="hidden" name="_mode" value="join">
			<input type="hidden" name="_ordr_idxx" value=""><?php // 2018-10-04 SSJ :: 본인인증 사용 시 ?>

			<div class="c_group_tit">
				<span class="tit">기본정보</span>
				<span class="sub_txt"><strong>* </strong>체크된 항목은 필수 항목입니다.</span>
			</div>

			<dl class="form_dl">
				<dt class="form_dt ess"><span class="tit">아이디</span></dt>
				<dd class="form_dd">
					<div class="with_btn">
						<input type="text" name="join_id" class="input_design js_join_id" placeholder="아이디" />
						<a href="#none" class="c_btn h45 black line js_id_overlap_check">중복체크</a>
						<input type="hidden" name="join_id_check" class="js_join_id_check" value="">
					</div>
					<div class="tip_txt">아이디는 한번 가입한 이후에는 변경할 수 없습니다.</div>
					<div class="tip_txt">
						<?php
						$id_length_text = '영문, 숫자로 <em>'.$id_min_length.'자</em> 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
						if($id_max_length > 0) $id_length_text = '영문, 숫자로 <em>'.$id_min_length.'자~'.$id_max_length.'자</em> 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
						echo $id_length_text;
						?>
					</div>
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt ess"><span class="tit">비밀번호</span></dt>
				<dd class="form_dd">
					<input type="password" name="join_pw" class="input_design js_join_pw" placeholder="비밀번호" autocomplete="new-password" />
                    <input type="password" name="join_repw" class="input_design js_join_repw" placeholder="비밀번호 확인" autocomplete="new-password"/>
					<div class="tip_txt">
						<?php
						$pw_length_text = '숫자, 영문';
						if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= '(대문자 '.$siteInfo['join_pw_up_length'].'자 이상 포함)';
						if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) $pw_length_text .= ', 특수문자(~!@#$%^&*()_+|<>?:{} 중 '.$siteInfo['join_pw_sp_length'].'자 이상)';
						if($pw_max_length > 0) $pw_length_text .= '을 포함하여 <em>'.$pw_min_length.'자~'.$pw_max_length.'자</em> 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
						else $pw_length_text .= '을 포함하여 <em>'.$pw_min_length.'자</em> 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
						echo $pw_length_text;
						?>
					</div>
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt ess"><span class="tit">이름</span></dt>
				<dd class="form_dd">
					<?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
						<input type="text" name="join_name" class="input_design js_auth_before auth_name" placeholder="이름" readonly />
					<?php } else { ?>
						<input type="text" name="join_name" autocomplete="off" class="input_design" placeholder="이름" />
					<?php } ?>
					<div class="tip_txt">실명을 입력해주세요.</div>
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt ess"><span class="tit">휴대폰 번호</span></dt>
				<dd class="form_dd">
					<?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
						<div class="with_btn">
							<input type="tel" name="join_tel2" class="input_design js_auth_before auth_phone" placeholder="휴대폰 번호" readonly/>
							<a href="#none" class="c_btn h45 black line" onclick="auth_type_check(); return false;">본인인증</a>
						</div>
					<?php } else { ?>
						<input type="tel" name="join_tel2" class="input_design" placeholder="휴대폰 번호"  />
					<?php } ?>
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt"><span class="tit">SMS 수신</span></dt>
				<dd class="form_dd">
					<div class="c_labelbox">
						<label class="c_label"><input type="radio" name="join_smssend" value="Y" checked/><span class="tx"><span class="icon"></span>수신</span></label>
						<label class="c_label"><input type="radio" name="join_smssend" value="N"/><span class="tx"><span class="icon"></span>수신거부</span></label>
					</div>
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt ess"><span class="tit">이메일 주소</span></dt>
				<dd class="form_dd">
					<div class="with_btn">
                        <input type="text" name="_email_prefix" class="input_design js_email_prefix" placeholder="이메일 아이디" />
                        <span class="unit">＠</span>
                            <div class="c_select">
                                <select name="_email_suffix_select" class="js_email_suffix_select">
                                    <option value="">선택</option>
                                    <?php foreach($email_suffix as $ek=>$ev) { ?>
                                        <option value="<?php echo $ev; ?>"><?php echo ($ev == 'direct'?'직접입력':str_replace('@', '', $ev)); ?></option>
                                    <?php } ?>
                                </select>
                                <span class="icon"></span>
                            </div>
                        <a href="#none" class="c_btn h45 black line js_email_overlap_check">중복체크</a>
					</div>
					<div class="other" style="display: none;"><input type="text" name="_email_suffix_input" class="input_design js_email_suffix_input" placeholder="직접입력" /></div>
                    <input type="hidden" name="join_email" class="js_join_email" value="">
                    <input type="hidden" name="join_email_check" class="js_join_email_check" value="">
				</dd>
			</dl>
			<dl class="form_dl">
				<dt class="form_dt"><span class="tit">이메일 수신</span></dt>
				<dd class="form_dd">
					<div class="c_labelbox">
						<label class="c_label"><input type="radio" name="join_emailsend" value="Y" checked/><span class="tx"><span class="icon"></span>수신</span></label>
						<label class="c_label"><input type="radio" name="join_emailsend" value="N"/><span class="tx"><span class="icon"></span>수신거부</span></label>
					</div>
				</dd>
			</dl>

			<?php if($siteInfo['join_birth']=='Y' || $$siteInfo['join_sex']=='Y' || $siteInfo['join_tel'] == 'Y' || $siteInfo['join_addr'] == 'Y' ||  $siteInfo['join_spam'] == 'Y'){?>
            <div class="c_group_tit">
                <span class="tit">부가정보</span>
                <span class="sub_txt"><strong>* </strong>체크된 항목은 필수 항목입니다.</span>
            </div>
			<?php }?>

            <?php if($siteInfo['join_birth'] == 'Y') { ?>
                <dl class="form_dl">
                    <dt class="form_dt<?php echo ($siteInfo['join_birth_required'] == 'Y'?' ess':null); ?>"><span class="tit">생년월일</span></dt>
                    <dd class="form_dd">
                        <?php if($siteInfo['s_join_auth_use'] == 'Y') { // 2018-10-04 SSJ :: 본인인증 사용 시 ?>
                            <input type="text" name="_birth" class="input_design type_date js_auth_before auth_birth" value="" placeholder="생년월일 선택" data-position="bottom right" readonly = "readonly"  />
                        <?php } else { ?>
                            <input type="text" name="_birth" id="_birth" class="input_design type_date js_pic_day_max_today" value="" placeholder="생년월일 선택" data-position="bottom right" readonly = "readonly" ontouchend="document.getElementById('_birth').readOnly = true;" />
                        <?php } ?>
						<div class="tip_txt">쇼핑몰 정책에 따라 생일쿠폰이 발급될 수 있습니다.</div>
                    </dd>
                </dl>
            <?php } ?>
            <?php if($siteInfo['join_sex'] == 'Y') { ?>
                <dl class="form_dl">
                    <dt class="form_dt<?php echo ($siteInfo['join_sex_required'] == 'Y'?' ess':null); ?>"><span class="tit">성별</span></dt>
                    <dd class="form_dd">
                        <div class="c_labelbox">
                            <label class="c_label"><input type="radio" name="_sex" value="M"<?php echo ($siteInfo['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':null); ?>/><span class="tx"><span class="icon"></span>남성</span></label>
                            <label class="c_label"><input type="radio" name="_sex" value="F"<?php echo ($siteInfo['s_join_auth_use'] == 'Y'?' onclick="return false;" class="js_auth_before auth_sex"':null); ?>/><span class="tx"><span class="icon"></span>여성</span></label>
                        </div>
                    </dd>
                </dl>
            <?php } ?>
            <?php if($siteInfo['join_tel'] == 'Y') { ?>
                <dl class="form_dl">
                    <dt class="form_dt<?php echo ($siteInfo['join_tel_required'] == 'Y'?' ess':null); ?>"><span class="tit">전화번호</span></dt>
                    <dd class="form_dd">
                        <input type="tel" name="join_tel" class="input_design" placeholder="전화번호" />
                        <div class="tip_txt">휴대폰 이외 유선전화가 필요한 경우 입력해주세요.</div>
                    </dd>
                </dl>
            <?php } ?>
            <?php if($siteInfo['join_addr'] == 'Y') { ?>
                <dl class="form_dl">
                    <dt class="form_dt<?php echo ($siteInfo['join_addr_required'] == 'Y'?' ess':null); ?>"><span class="tit">주소</span></dt>
                    <dd class="form_dd">
                        <div class="with_btn type_fixed">
                            <input type="text" name="join_zonecode" id="_zonecode" class="input_design onclick" onclick="post_popup_show(); return false;" value="" placeholder="우편번호" readonly="readonly"/>
                            <a href="#none" onclick="post_popup_show(); return false;" class="c_btn h45 black line">주소검색</a>
                        </div>
                        <input type="text" name="join_address_doro" id="_addr_doro" class="input_design onclick" onclick="post_popup_show(); return false;" placeholder="도로명 주소" readonly="readonly"/>
                        <input type="text" name="join_address2" id="_addr2" class="input_design" placeholder="나머지 주소" />

						<input type="hidden" name="join_zip1" id="_post1" value="" />
						<input type="hidden" name="join_zip2" id="_post2" value="" />
						<input type="hidden" name="join_address1" id="_addr1" class="js_addr" value="" />
                    </dd>
                </dl>
            <?php } ?>
            <?php if($siteInfo['join_spam'] == 'Y' && $siteInfo['recaptcha_api'] && $siteInfo['recaptcha_secret']) { //2020-05-14 SSJ :: 회원가입 정책 스팸방지 설정 적용 ?>
                <dl class="form_dl">
                    <dt class="form_dt ess"><span class="tit">스팸방지</span></dt>
                    <dd class="form_dd">
                        <?php // 스팸방지 들어감 ?>
                        <script src="//www.google.com/recaptcha/api.js"></script>
                        <div class="g-recaptcha" data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
                    </dd>
                </dl>
            <?php } ?>

			<div class="c_btnbox type_full">
				<ul>
					<li><a href="/" onclick="return confirm('회원가입을 취소하고 홈으로 이동합니다. 계속하시겠습니까?');" class="c_btn h50 black line">취소하기</a></li>
					<li><a href="#none" class="c_btn h50 black js_submit">가입완료</a></li>
				</ul>
			</div>

		</form><!-- end c_form -->

	</div>
</div><!-- end c_section -->

<script type="text/javascript">

	// 주소검색시 지번주소 노출
	$(document).on('focus', 'input[name="join_address2"]', func_addr_view);
	function func_addr_view() {
		var _addr = $('input[name="join_address1"]').val();
		if(_addr!='' && _addr!='undefined'){
			var _addr_text = '<div class="tip_txt js_addr1_view">지번주소 : '+_addr+'</div>';
			$('.js_addr1_view').remove();
			$('.js_addr').after(_addr_text);
		}
	}
    
	// 아이디 중복체크
	$(document).on('click', '.js_id_overlap_check', function(e) {
		e.preventDefault();
		var _id = $('.js_join_id').val();
		_id = _id.trim();
		$('.js_join_id').val(_id);
		if(!_id) {
			alert('아이디를 입력해주세요.');
			$('.js_join_id').focus();
			return false;
		}
		$.ajax({
			data: {
				_mode: 'id_check',
				_id: _id
			},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php',
			success: function(data) {

				// 전달된 데이터를 array로 변환
				try { var result = $.parseJSON(data); }
				catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

				if(result['msg']) {
					var msg = result['msg'];
					msg = msg.replace(/\\n/gi, '\n');
				}
				if(result['alert']) {
					var re_alert = result['alert'];
					re_alert = re_alert.replace(/\\n/gi, '\n');
				}

				if(result['result'] == 'success') {
					$('.js_join_id_check').val(1);
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
				else {
					$('.js_join_id_check').val('');
					alert(msg);

					// 아이디 항목에 포커스 주기
					$('.js_join_id').focus();

					// 다시 들어간 포커스를 글자 맨뒤로 밀기 위한 처리
					$('.js_join_id').val('');
					$('.js_join_id').val(_id);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
			}
		});
	});


	// 이메일 항목제어
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
					su.closest('div').hide();
					$('.js_email_suffix_select').val($(v).val());
				}
			});
		}
		r_val = i_value.replace('@', '')+'@'+s_value.replace('@', '');
		$('.js_join_email').val(r_val);
	});



	// 이메일 중복체크
	$(document).on('click', '.js_email_overlap_check', function(e) {
		e.preventDefault();
		var i_value = $('.js_email_prefix').val();
		var s_value = $('.js_email_suffix_input').val();
		var _email = $('.js_join_email').val();
		var UserInput = $('.js_email_suffix_input').closest('div').is(':visible');
		var o_email = '<?php echo $mem_info['in_email']; ?>';
		if(!i_value) {
			alert('이메일 아이디를 입력해주세요.');
			$('.js_email_prefix').focus();
			return false;
		}
		if(!s_value) {
			if(UserInput === true) {
				alert('이메일 주소를 입력해주세요');
				$('.js_email_prefix').focus();
			}
			else {
				alert('이메일 주소를 선택해주세요');
				$('.js_email_suffix_select').focus();
			}
			return false;
		}
		$.ajax({
			data: {
				_mode: 'email_check',
				_email: _email
			},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php',
			success: function(data) {

				// 전달된 데이터를 array로 변환
				try { var result = $.parseJSON(data); }
				catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

				if(result['msg']) {
					var msg = result['msg'];
					msg = msg.replace(/\\n/gi, '\n');
				}
				if(result['alert']) {
					var re_alert = result['alert'];
					re_alert = re_alert.replace(/\\n/gi, '\n');
				}

				if(result['result'] == 'success') {
					$('.js_join_email_check').val(1);
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
				else {
					$('.js_join_email_check').val('');
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
			}
		});
	});



	// 서브미트
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
	});
	$(document).ready(function() {
		// - 대문자 검증
		jQuery.validator.addMethod('upper_alpha', function(value, element, length) {
			var pattern = /[A-Z]/g;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 대문자가 {0}개 이상 포함되어야합니다');

		// - 특수문자 검증
		jQuery.validator.addMethod('special_string', function(value, element, length) {
			var pattern = /[~!@#$%^&*()_+|<>?:{}]/g;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 {0}개 이상 포함되어야합니다');

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");

		// - 전화번호 검증
		jQuery.validator.addMethod("tel_check", function(value, element) {
			var pattern = /^\d{2,3}-\d{3,4}-\d{4}$/;
			return this.optional(element) || pattern.test(value);
		}, "전화번호 형식이 유효하지않습니다.");

		// - 휴대폰 검증
		jQuery.validator.addMethod("htel_check", function(value, element) {
			var pattern = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
			return this.optional(element) || pattern.test(value);
		}, "휴대폰번호 형식이 유효하지않습니다.");

		// - 영문+숫자 - 특정위치에서 alphanumeric가 증발하는 경우 있음으로 재지정
		jQuery.validator.addMethod("alphanumeric", function(value, element) {
			var pattern = /[0-9a-zA-Z]$/ig;
			return this.optional(element) || pattern.test(value);
		}, "영문, 숫자로 입력해주세요");


		// 벨리데이션
		$('.js_join_form').validate({
			ignore: 'input[type=text]:hidden',
			rules: {
				join_id: {
					  required : true
					, alphanumeric: true
					, minlength: <?php echo $id_min_length; ?>
					<?php if($id_max_length > 0) { ?>, maxlength: <?php echo $id_max_length; ?><?php } echo PHP_EOL; ?>
				}
				, join_id_check: { required : true }
				, join_name: { required : true }
				, join_pw: {
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_pw_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>
					<?php } ?>
				}
				, join_repw: {
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_pw_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>
					<?php } ?>
					, equalTo: '.js_join_pw'
				}
				<?php if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') { ?>
					, _birth: { required : true }
				<?php } ?>
				<?php if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') { ?>
					, _sex: { required : true }
				<?php } ?>
				, join_tel2: { required : true, htel_check: true }
				<?php if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y'){ ?>
					, join_tel: { required : true, tel_check: true }
				<?php } ?>
				, _email_prefix: { required : true }
				, _email_suffix_input: { required : true }
				, join_email: { required : true, email_check: true }
				, join_email_check: { required : true }
				, join_emailsend: { required : true }
				<?php if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y'){ ?>
					, join_address1: { required : true }
					, join_address_doro: { required : true }
					, join_zonecode: { required : true }
					, join_address2: { required : true }
				<?php } ?>
			},
			messages: {
				join_id: {
					  required : '아이디를 입력해주세요'
					, minlength: '아이디는 최소 <?php echo $id_min_length; ?>자 이상 입력해주세요'
					<?php if($id_max_length > 0) { ?>, maxlength: '아이디는 최대 <?php echo $id_max_length; ?>자 이하로 입력해주세요'<?php } echo PHP_EOL; ?>
					, alphanumeric: '아이디는 영문, 숫자로 입력해주세요'
				}
				, join_id_check: { required : '아이디 중복검사를 해주세요' }
				, join_name: { required : '이름을 입력해주세요' }
				, join_pw: {
					  required : '비밀번호를 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_pw_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
				}
				, join_repw: {
					  required : '비밀번호 확인을 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_pw_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_pw_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					, equalTo: '비밀번호가 일치하지않습니다'
				}
				<?php if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') { ?>
					, _birth: { required : '생년월일을 입력해주세요' }
				<?php } ?>
				<?php if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') { ?>
					, _sex: { required : '성별을 선택해주세요' }
				<?php } ?>
				, join_tel2: { required: '휴대폰 번호를 입력해주세요', htel_check: '휴대폰번호 형식이 유효하지않습니다' }
				<?php if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y') { ?>
					, join_tel: { required: '전화번호를 입력해주세요', tel_check: '전화번호 형식이 유효하지않습니다' }
				<?php } ?>
				, _email_prefix: { required : '이메일 아이디를 입력해주세요' }
				, _email_suffix_input: { required : '이메일 주소를 '+($('.js_email_suffix_input').is(':visible')?'입력':'선택')+'해주세요' }
				, join_email: { required : '이메일 주소를 입력해주세요', email_check: '유효하지 않은 E-Mail주소입니다' }
				, join_email_check: { required : '이메일 중복검사를 해주세요' }
				, join_emailsend: { required : '이메일 수신여부를 선택해주세요' }
				<?php if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y'){ ?>
					, join_address1: { required : '주소검색을 통하여 기본주소를 입력해주세요' }
					, join_address_doro: { required : '주소검색을 통하여 도로명주소를 입력해주세요' }
					, join_zonecode: { required : '주소검색을 통하여 새 우편번호를 입력해주세요' }
					, join_address2: { required : '나머지주소를 입력해주세요' }
				<?php } ?>
			}
            ,submitHandler: function(form){
                // do other things for a valid form
                if(typeof kcp_submit == 'function') if(!kcp_submit()) return false;
                form.submit();
            }
		});
	});
</script>
<?php include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_m.php'); // 다음주소찾기 ?>