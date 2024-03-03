<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '회원탈퇴'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/mypage.header.php'); // 모바일 탑 네비
?>


<div class="c_section c_gridpage">
    <div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->

		<div class="grid_section type_miniform">
			<div class="layout_fix">

				<?php if($sns_join_type == 'direct') { ?>
				<form action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" class="js_leave_form c_form" method="post" target="common_frame" autocomplete="off" onsubmit="return leaveSubmit();">
					<input type="hidden" name="_mode" value="delete">
					<div class="c_group_tit">
						<span class="tit">회원탈퇴</span>
					</div>
					<ul class="form_ul">
						<li class="form_li"><input type="text" name="leave_id" class="input_design" placeholder="아이디" value="<?php echo $mem_info['in_id']; ?>" readonly disabled/></li>
						<li class="form_li"><input type="password" name="leave_pw" class="input_design " placeholder="비밀번호" autocomplete="new-password"/></li>
					</ul>
					<div class="c_btnbox type_full">
						<ul>
							<li><a href="#none" class="c_btn h50 black line"  onclick="history.go(-1); return false;">취소</a></li>
							<li><a href="#none" class="c_btn black h50 js_leave_submit" onclick="return false;" title="">탈퇴하기</a></li>
						</ul>

					</div>
				</form><!-- end c_form -->
				<?php } else { ?>
					<div class="c_sns_login type_leave">
						<div class="c_group_tit">
							<span class="tit">SNS 회원탈퇴</span>
						</div>
						<ul>
							<?php
							if($SNSField['naver']['login_use'] == 'Y' && $SNSField['naver']['callback_url']) {
								$sns_callback_url = $SNSField['naver']['callback_url'];
								?>
								<li>
									<a href="#none" onclick="if(confirm('탈퇴 후 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn naver">
										<span class="icon"></span><span class="tx">네이버 탈퇴하기</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['kakao']['login_use'] == 'Y' && $SNSField['kakao']['callback_url']) {
								$sns_callback_url = $SNSField['kakao']['callback_url'];
								?>
								<li>
									<a href="#none" onclick="if(confirm('탈퇴 후 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn kakao">
										<span class="icon"></span><span class="tx">카카오톡 탈퇴하기</span>
									</a>
								</li>
							<?php } ?>
							<?php
							if($SNSField['facebook']['login_use'] == 'Y' && $SNSField['facebook']['callback_url']) {
								$sns_callback_url = $SNSField['facebook']['callback_url'];
								?>
								<li>
									<a href="#none" onclick="if(confirm('탈퇴 후 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn facebook">
										<span class="icon"></span><span class="tx">페이스북 탈퇴하기</span>
									</a>
								</li>
							<?php } ?>
							
							<?php  if($SNSField['apple']['login_use'] == 'Y' && $SNSField['apple']['callback_url']) { 

								$sns_callback_url = $SNSField['apple']['callback_url'];
							?>
							<li>
								<a href="#none" onclick="if(confirm('탈퇴 하시면 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) apply_apple_login('<?php echo $sns_callback_url; ?>'); return false;" class="btn apple">
									<span class="icon"></span><span class="tx">애플 탈퇴하기</span>
								</a>
							</li>
							<?php } ?>

							<?php
							// KAY : 2023-11-06 :: 구글 로그인 추가
							if($SNSField['google']['login_use'] == 'Y' && $SNSField['google']['callback_url']) {
								$sns_callback_url = $SNSField['google']['callback_url'];
								?>
								<li>
									<a href="#none" onclick="if(confirm('탈퇴 후 같은 소셜 아이디로 가입이 불가능 합니다.\n정말 탈퇴하시겠습니까?')) window.open('<?php echo $sns_callback_url; ?>', 'sns_leave', 'width=800, height=500'); return false;" class="btn google">
										<span class="icon"></span><span class="tx">구글 탈퇴하기</span>
									</a>
								</li>
							<?php } ?>
							
						</ul>
					</div><!-- end c_sns_login -->
				<?php } ?>


				<div class="c_user_guide">
					<?php
					$leave_guidance = array_filter(explode(PHP_EOL, $siteInfo['s_leave_guidance']));
					if(count($leave_guidance) <= 0) {
						// 관리자 > 회원탈퇴 주의사항 작성을 안했을경우
					?>
						<dl>
							<dt>꼭 확인해주세요.</dt>
							<?php if($sns_join_type == 'direct') { ?>
								<dd>탈퇴후에는 같은 아이디로 재가입 할 수 없습니다.</dd>
							<?php } else { ?>
								<dd>탈퇴후에는 같은 소셜 아이디로 재가입 할 수 없습니다.</dd>
							<?php } ?>
							<dd>탈퇴 즉시 개인정보는 삭제되며, 이용 시 발생한 적립금 및 쿠폰, 주문, 문의 내역등은 복원되지 않습니다.</dd>
							<dd>다만, 거래내역은 전자상거래등에서의 소비자보호에 관한 법률에 의거하여 보관됩니다.</dd>
						</dl>
					<?php
						} else {
						// 관리자 > 회원탈퇴 주의사항 작성을 했을경우
					?>
						<dl>
						   <dt>꼭 확인해주세요.</dt>
							<?php if($sns_join_type == 'direct') { ?>
								<dd>탈퇴후에는 같은 아이디로 재가입 할 수 없습니다.</dd>
							<?php } else { ?>
								<dd>탈퇴후에는 같은 소셜 아이디로 재가입 할 수 없습니다.</dd>
							<?php } ?>
							<?php foreach($leave_guidance as $k=>$v) { ?>
								<dd><?php echo htmlspecialchars($v); ?></dd>
							<?php } ?>
						</dl>
					<?php } ?>
				</div><!-- end c_user_guide -->


			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->


<?php if($sns_join_type == 'direct') { ?>
	<script type="text/javascript">

		// 회원탈퇴 저장
		$(document).on('click', '.js_leave_submit', function(e) {
			e.preventDefault();
			$(this).closest('form').submit();
		});

		function leaveSubmit() {
			if(!$('input[name=leave_pw]').val()) {
				alert('비밀번호를 입력해주세요');
				$('input[name=leave_pw]').focus();
				return false;
			}
			if(!confirm('정말 탈퇴하시겠습니까?')) return false;
			return true;
		}
	</script>
<?php } ?>