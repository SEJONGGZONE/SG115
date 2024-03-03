<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$_rurl -> 로그인 후 이동 할 주소 => (/program/member.login.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.login.form.php에서 지정)
*/
$page_title = '로그인'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.login.header.php'); // 모바일 탑 네비
?>
<div class="c_section c_login">
    <div class="layout_fix">

        <?php if($is_sns_login_form === true) { ?>
            <div class="c_sns_login">
                <ul>
                    <?php
                    if($SNSField['naver']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['naver']['callback_url'];
                        ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver" title="네이버 로그인"><span class="icon"></span></span></a></li>
                    <?php } ?>
                    <?php
                    if($SNSField['kakao']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['kakao']['callback_url'];
                        ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao" title="카카오톡 로그인"><span class="icon"></span></a></li>
                    <?php } ?>
                    <?php
                    if($SNSField['facebook']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['facebook']['callback_url'];
                        ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn facebook" title="페이스북 로그인"><span class="icon"></span></a></li>
                    <?php } ?>

                    <?php if($SNSField['apple']['login_use'] == 'Y') { ?>
						<li><a href="#none" onclick="apply_apple_login(); return false;" class="btn apple" title="애플 로그인"><span class="icon"></span></a></li>
					<?php } ?>

					<?php 
						if($SNSField['google']['login_use'] == 'Y') { 
							$sns_callback_url = $SNSField['google']['callback_url'];
					?>
						<li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn google" title="구글 로그인"><span class="icon"></span></a></li>
					<?php } ?>
					
                </ul>
                <div class="or_box"><span class="tx">또는</span></div>
            </div><!-- end c_sns_login -->
        <?php } ?>


		<form action="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php" class="js_login_form login_form c_form" method="post" target="<?php echo ($AppUseMode === true && function_exists('is_app') === true && is_app() === true ? null : 'common_frame'); ?>" autocomplete="off">
			<input type="hidden" name="_mode" value="login">
			<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
			<ul class="form_ul">
				<li class="form_li"><input type="text" name="login_id" class="input_design"<?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' value="'.$_COOKIE['AuthSDIndividualIDChk'].'"': null ); ?> placeholder="아이디" required /></li>
				<li class="form_li"><input type="password" name="login_password" class="input_design" value="" placeholder="비밀번호" autocomplete="new-password" required /></li>
				<li class="form_li">
					<div class="c_label_box">
						<label class="c_label">
							<input type="checkbox" name="login_id_chk" value="Y" <?php echo (isset($_COOKIE['AuthSDIndividualIDChk'])?' checked':null); ?> />
							<span class="tx"><span class="icon"></span>아이디 저장</span>
						</label>
					</div>
				</li>
			</ul>
			<div class="c_btnbox type_full">
				<a href="#none" class="c_btn black h50 js_login_btn js_login_submit" onclick="return false;" title="">로그인</a>
				<?php if($isNoneMemberBuy){ // 비회원 주문 버튼 추가 ?>
					<a href="/?pn=shop.order.form" class="c_btn color line h50">비회원으로 구매하기</a>
				<?php } ?>
			</div>
		</form><!-- end c_form -->

		<dl class="other_link">
			<dt>
				<a href="/?pn=member.join.agree" class="btn"><em>회원가입</em></a>
			</dt>
			<dd>
				<a href="/?pn=member.find.form&_mode=find_id" class="btn">아이디 찾기</a>
				<a href="/?pn=member.find.form&_mode=find_pw" class="btn">비밀번호 찾기</a>
			</dd>
		</dl><!-- end other_link -->


		<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ====  ?>
		<?php if ( !$none_member_buy && !$isNoneMemberBuy ) {	 ?>
			<a href="/?pn=service.guest.order.list" class="btn_guest"><em>비회원으로 주문하셨나요?</em><strong>비회원 주문조회</strong></a>
		<?php } ?>
		<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ====  ?>

    </div>
</div><!-- end c_section -->

<script type="text/javascript">

	// 로그인 함수
	$(document).on('click', '.js_login_submit', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
	})

	$(document).ready(function() {
		// 로그인
		$('.js_login_form').validate({
			rules: {
				login_id: { required: true },
				login_password: { required: true }
			},
			messages: {
				login_id: { required: '아이디를 입력하세요' },
				login_password: { required: '패스워드를 입력하세요' }
			}
		});
	});
</script>