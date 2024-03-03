<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
// $page_title = (empty($_mode) || $_mode == 'find_id'?'아이디찾기':'비밀번호 찾기'); // 페이지 타이틀
$page_title = '가입정보 찾기'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.login.header.php'); // 모바일 탑 네비
?>
<div class="c_section c_login">
    <div class="layout_fix">

		<div class="find_tab">
			<ul>
				<li<?php echo (empty($_mode) || $_mode == 'find_id'?' class="hit"':null); ?>><a href="/?pn=<?php echo $pn; ?>&_mode=find_id" class="tab">아이디 찾기</a></li>
				<li<?php echo ($_mode == 'find_pw'?' class="hit"':null); ?>><a href="/?pn=<?php echo $pn; ?>&_mode=find_pw" class="tab">비밀번호 찾기</a></li>
			</ul>
		</div><!-- end find_tab -->

		<?php if(empty($_mode) || $_mode == 'find_id') { // 아이디 찾기 ?>
			<form class="js_find_id_from c_form"  autocomplete="off">
				<ul class="form_ul">
					<li class="form_li"><input type="text" name="find_id_name" class="input_design" autocomplete="off" placeholder="이름" required/></li>
					<li class="form_li"><input type="tel" name="find_id_tel" class="input_design" autocomplete="off" placeholder="휴대폰 번호" required/></li>
				</ul>

				<div class="c_btnbox type_full">
					<a href="#none" onclick="return false;" class="c_btn black h50 js_findid_submit" title="">아이디 찾기</a>
				</div>
				<dl class="other_link">
					<dt>
						<a href="/?pn=member.join.agree" class="btn"><em>회원가입</em></a>
					</dt>
					<dd>
						<a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn"><em>로그인하기</em></a>
					</dd>
				</dl><!-- end other_link -->

				<div class="find_result js_find_id_result" style="display:none">
					<div class="js_success" style="display:none">
						<div class="first_tx">고객님 아이디를 찾았습니다.<strong class="js_id">***</strong></div>
						<div class="second_tx">개인정보보호를 위해 일부를 별표(*)로 표시합니다.</div>
					</div>
					<div class="js_error" style="display:none">
						<div class="first_tx">죄송합니다. 고객님<br/>회원정보를 찾을 수 없습니다.</div>
						<div class="second_tx">정보 확인 후 다시 조회해주시기 바랍니다.</div>
					</div>
					<div class="c_btnbox type_full">
						<ul>
							<li><a href="#none" class="c_btn h40 light line js_find_id_re">다시찾기</a></li>
							<li><a href="/?pn=member.login.form" class="c_btn h40 color line">로그인하기</a></li>
						</ul>
					</div><!-- end c_btnbox -->
				</div>
			</form>

			<script type="text/javascript">


				// 아이디 찾기 클릭시
				$(document).on('click', '.js_findid_submit', function(e) {
					e.preventDefault();
					$('.js_find_id_from').submit();
				})

				// 아이디 찾기
				$(document).on('submit', '.js_find_id_from', function(e) {
					e.preventDefault();
					var su = $(this);
					var _name = $(this).find('input[name=find_id_name]').val();
					var _tel = $(this).find('input[name=find_id_tel]').val();
					$.ajax({
						data: {
							_mode: 'find_id',
							_name: _name,
							_tel: _tel
						},
						type: 'POST',
						cache: false,
						url: '<?php echo OD_PROGRAM_URL; ?>/member.find.pro.php',
						success: function(data) {

							// 전달된 데이터를 array로 변환
							try { var result = $.parseJSON(data); }
							catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

							// 결과처리
							if(result['result'] == 'success') {
								su.find('div.c_form').hide();
								// su.find('.c_user_guide').hide();
								su.find('div.js_find_id_result .js_success').hide();
								su.find('div.js_find_id_result .js_error').hide();
								su.find('div.js_find_id_result .js_success .js_id').text('***');
								su.find('div.js_find_id_result .js_success .js_id').text(result['id']);
								su.find('div.js_find_id_result .js_success').show();
								su.find('div.js_find_id_result').show();

								// alert 안내가 있다면
								if(result['alert'] && result['alert'] != '') alert(result['alert']);
							}
							else if(result['result'] == 'error') {
								su.find('div.c_form').hide();
								// su.find('.c_user_guide').hide();
								su.find('div.js_find_id_result .js_success').hide();
								su.find('div.js_find_id_result .js_error').hide();
								su.find('div.js_find_id_result .js_success .js_id').text('***');
								su.find('div.js_find_id_result .js_error').show();
								su.find('div.js_find_id_result').show();

								// alert 안내가 있다면
								if(result['alert'] && result['alert'] != '') alert(result['alert']);
							}
							else { // 기타에러
								if(result['msg']) { alert(result['msg']); }
								else {
									alert('통신중 에러가 발생하였습니다.');
									if(typeof console === 'object') console.log(data);
								}
							}
						}
					});
				});
				$(document).on('click', '.js_find_id_re', function(e) {
					e.preventDefault();
					var su = $(this).closest('form');
					su.find('div.js_find_id_result').hide();
					su.find('div.js_find_id_result .js_success').hide();
					su.find('div.js_find_id_result .js_error').hide();
					su.find('div.js_find_id_result .js_success .js_id').text('***');
					su.find('div.c_form').show();
					// su.find('.c_user_guide').show();
					su.find('input').not('input:submit').not('input[name=_type]').val('');
					su.find('input').eq(0).focus();
				});
			</script>
		<?php } else { // 비밀번호찾기 ?>
			<form class="js_find_pw_from c_form" autocomplete="off">
				<ul class="form_ul">
					<?php if(count($PasswordFindType) == 1) { ?>
						<input type="hidden" name="_type" value="<?php echo $PasswordFindType[0]; ?>">
					<?php } else { ?>
					<li class="form_li">
						<div class="c_labelbox js_type">
							<label class="c_label"><input type="radio" name="_type" value="email" checked><span class="tx"><span class="icon"></span>이메일</span></label>
							<label class="c_label"><input type="radio" name="_type" value="sms"><span class="tx"><span class="icon"></span>휴대폰 번호</span></label>
						</div>
					</li>
					<?php } ?>
					<li class="form_li">
						<input type="text" name="find_pw_id" class="input_design" autocomplete="off" placeholder="아이디"/>
					</li>
					<li class="form_li js_email_field">
						<input type="email" name="find_pw_email" class="input_design" autocomplete="off" placeholder="이메일 주소"/>
					</li>
					<li class="form_li js_sms_field" style="display:none;">
						<input type="tel" name="find_pw_tel" class="input_design" autocomplete="off" placeholder="휴대폰 번호"/>
					</li>
				</ul>
				<div class="c_btnbox type_full">
					<a href="#none" onclick="return false;" class="c_btn black h50 js_findpw_submit" title="">임시비밀번호 발급받기</a>
				</div>
				<dl class="other_link">
					<dt>
						<a href="/?pn=member.join.agree" class="btn"><em>회원가입</em></a>
					</dt>
					<dd>
						<a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn"><em>로그인하기</em></a>
					</dd>
				</dl><!-- end other_link -->

				<div class="find_result js_find_pw_result" style="display:none">
					<div class="js_success" style="display:none">
						<div class="first_tx">임시비밀번호를 발송했습니다.<strong class="js_send_data"></strong></div>
						<div class="second_tx">로그인 후 임시비밀번호는 꼭 수정해주세요.</div>
					</div>
					<div class="js_error" style="display:none">
						<div class="first_tx">죄송합니다. 고객님<br/>회원정보를 찾을 수 없습니다.</div>
						<div class="second_tx">정보 확인 후 다시 조회해주시기 바랍니다.</div>
					</div>
					<div class="c_btnbox type_full">
						<ul>
							<li><a href="#none" class="c_btn h40 light line js_find_pw_re">다시찾기</a></li>
							<li><a href="/?pn=member.login.form" class="c_btn h40 color line">로그인하기</a></li>
						</ul>
					</div><!-- end c_btnbox -->
				</div>
			</form>
			<script type="text/javascript">

				// 비밀번호 찾기 클릭시
				$(document).on('click', '.js_findpw_submit', function(e) {
					e.preventDefault();
					$('.js_find_pw_from').submit();
				})

				// 비밀번호 찾기 유효성체크
				$(document).on('submit', '.js_find_pw_from', function(e) {
					e.preventDefault();
					var su = $(this);
					var _type = (su.find('input[name=_type]').attr('type') == 'hidden'?su.find('input[name=_type]').val():su.find('input[name=_type]:checked').val());
					var _id = $(this).find('input[name=find_pw_id]').val();
					var _tel = $(this).find('input[name=find_pw_tel]').val();
					var _email = $(this).find('input[name=find_pw_email]').val();
					$.ajax({
						data: {
							_mode: 'find_pw',
							_type: _type,
							_id: _id,
							_tel: _tel,
							_email: _email
						},
						type: 'POST',
						cache: false,
						url: '<?php echo OD_PROGRAM_URL; ?>/member.find.pro.php',
						success: function(data) {

							// 전달된 데이터를 array로 변환
							try { var result = $.parseJSON(data); }
							catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

							// 결과처리
							if(result['result'] == 'success') {
								su.find('div.c_form').hide();
								// su.find('.c_user_guide').hide();
								su.find('.js_type').hide();
								su.find('div.js_find_pw_result .js_success').hide();
								su.find('div.js_find_pw_result .js_error').hide();
								su.find('div.js_find_pw_result .js_success .js_send_data').text('');
								su.find('div.js_find_pw_result .js_success .js_send_data').text(result['send']);
								su.find('div.js_find_pw_result .js_success').show();
								su.find('div.js_find_pw_result').show();

								// alert 안내가 있다면
								if(result['alert'] && result['alert'] != '') alert(result['alert']);
							}
							else if(result['result'] == 'error') {
								su.find('div.c_form').hide();
								// su.find('.c_user_guide').hide();
								su.find('.js_type').hide();
								su.find('div.js_find_pw_result .js_success').hide();
								su.find('div.js_find_pw_result .js_error').hide();
								su.find('div.js_find_pw_result .js_success .js_send_data').text('');
								su.find('div.js_find_pw_result .js_error').show();
								su.find('div.js_find_pw_result').show();

								// alert 안내가 있다면
								if(result['alert'] && result['alert'] != '') alert(result['alert']);
							}
							else { // 기타에러
								if(result['msg']) { alert(result['msg']); }
								else {
									alert('통신중 에러가 발생하였습니다.');
									if(typeof console === 'object') console.log(data);
								}
							}
						}
					});
				});
				$(document).on('click', '.js_find_pw_re', function(e) {
					e.preventDefault();
					var su = $(this).closest('form');
					su.find('div.js_find_pw_result').hide();
					su.find('div.js_find_pw_result .js_success').hide();
					su.find('div.js_find_pw_result .js_error').hide();
					su.find('div.js_find_pw_result .js_success .js_send_data').text('');
					su.find('div.c_form').show();
					// su.find('.c_user_guide').show();
					su.find('.js_type').show();
					su.find('input').not('input:submit').not('input[name=_type]').val('');
					su.find('input').eq(0).focus();
				});
				<?php if(count($PasswordFindType) > 1) { ?>
					$(document).on('click', '.js_find_pw_from input[name=_type]', FindPwInput);
				<?php } ?>
				$(document).ready(FindPwInput);
				function FindPwInput() {
					var su = $('.js_find_pw_from');
					var _type = (su.find('input[name=_type]').attr('type') == 'hidden'?su.find('input[name=_type]').val():su.find('input[name=_type]:checked').val());
					su.find('.js_email_field').hide();
					su.find('.js_sms_field').hide();
					if(su.find('.js_email_field input').length > 0) su.find('.js_email_field input').val('');
					if(su.find('.js_sms_field input').length > 0) su.find('.js_sms_field input').val('');
					su.find('.js_'+_type+'_field').show();
				}
			</script>
		<?php } ?>

    </div>
</div><!-- end c_section -->