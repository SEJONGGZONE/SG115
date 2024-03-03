<?php
include_once('wrap.header.php');

// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 로그인보안 {
if( !IsField('smart_setup','member_login_time')){
	_MQ_noreturn("ALTER TABLE `smart_setup` ADD COLUMN `member_login_time` INT NULL DEFAULT '60' COMMENT '로그인 시도 횟수가 넘을 경우 중지될 시간초'");
}

$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>

<form class="defaut_form" method="post" action="_config.member.orderway.pro.php" onsubmit="return validate_check();">

	<div class="group_title"><strong>비회원 구매</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>가능 여부</th>
					<td>
						<?php echo _InputRadio('_none_member_buy', array('Y', 'N'), ($r['s_none_member_buy'] == 'Y'?'Y':'N'), '', array('가능', '불가'), ''); ?>
						<?php echo _DescStr('불가를 선택한 경우 비회원은 구매가 불가능하며, 회원만 가능해집니다. (비회원 주문조회도 불가)'); ?>
					</td>
				</tr>
				<tr class="js_none_member_buy_use" style="<?php echo ($r['s_none_member_buy'] <> 'Y' ? 'display:none;' : null); ?>">
					<th>구매 방법</th>
					<td>
						<?php echo _InputRadio('_none_member_login_skip', array('N', 'Y'), ($r['s_none_member_login_skip'] == 'Y'?'Y':'N'), '', array('로그인 경유', '바로 주문하기'), ''); ?>
						<script>
							$(document).ready(function(){
								$('input[name=_none_member_buy]').on('click', function(){
									var _v = $(this).val();
									if(_v == 'Y') $('.js_none_member_buy_use').show();
									else  $('.js_none_member_buy_use').hide();
								});
							});
						</script>
						<?php echo _DescStr('로그인 경유 : 구매하기 버튼 클릭 시 로그인 페이지를 한번 경유한 후 주문페이지로 이동합니다.'); ?>
						<?php echo _DescStr('바로 주문하기 : 구매하기 버튼 클릭 시 바로 주문페이지로 이동합니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>로그인 보안</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<?php // LCY :: 2017-12-09 -- 휴면계정전환 개월 수 이동 ==> 회원관리 > 휴면회원정책 ?>
					<?php // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 ?>
					<tr>
						<th>로그인 시도횟수</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="member_login_cnt" class="design t_right" value="<?php echo $r['member_login_cnt']; ?>" style="width:70px" placeholder="0">
								<span class="fr_tx">회 로그인 실패 시</span>
								<input type="text" name="member_login_time" class="design t_right" value="<?php echo $r['member_login_time']; ?>" style="width:70px" placeholder="0">
								<span class="fr_tx">초 동안 로그인 제한</span>
							</div>
						</td>
					</tr>
					<tr>
						<th>참고사항</th>
						<td>
							<?php echo _DescStr('로그인 시도횟수 설정값이 0인 경우 해당 기능은 작동되지 않습니다.'); ?>
							<?php echo _DescStr('로그인 성공 기록은 모두 “마이페이지 > 로그인기록”에 기록되고, 실패는 위 설정한 횟수이상 틀렸을 경우 기록됩니다.'); ?>
							<?php echo _DescStr('로그인 시도횟수 설정 시에는 실패할 경우에 대한 시간(초)을 반드시 설정하고, 설정값이 0인 경우 기본 60초가 적용됩니다.'); ?>
						</td>
					</tr>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>회원 탈퇴 안내</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>회원탈퇴 주의사항</th>
					<td>
						<textarea name="_leave_guidance" rows="5" class="design" placeholder="회원탈퇴 주의사항"><?php echo $r['s_leave_guidance']; ?></textarea>
						<?php echo _DescStr('회원탈퇴 페이지 하단 도움말에 노출할 내용을 별도로 입력할 수 있으며, 미입력 시 기본 내용이 노출됩니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>
</form>



<script type="text/javascript">
	// 비밀번호 변경 체크 동작
	$(document).delegate('.js_change_apw', 'change click', function(e) {
		var is_checked = $(this).is(':checked');
		if(is_checked === true) {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').show();
			$('.js_change_apw_box').find('input').eq(0).focus();
		}
		else {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').hide();
		}
	});

	// 등록 검증
	function validate_check() {

		// 비밀번호 변경 체크
		var pw_change = $('.js_change_apw').is(':checked');
		var pw_length = 6; // 최소 비밀번호 글자수
		if(pw_change === true) {
			var pw = $('.js_pw_input').val();
			var pw_ck = $('.js_pw_ckinput').val();
			if(!pw || !pw_ck) { // 변경 비밀번호 입력 체크
				alert('비밀번호를 입력해 주세요.');
				if(!pw) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw.length < pw_length || pw_ck.length < pw_length) { // 6자리 비밀번호 확인
				alert(pw_length+'자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.');
				if(pw.length < pw_length) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw != pw_ck) { // 비밀번호 일치성 확인
				alert('비밀번호와 비밀번호확인이 일치하지 않습니다.');
				$('.js_pw_ckinput').focus();
				return false;
			}
		}
	}
</script>
<?php include_once('wrap.footer.php'); ?>