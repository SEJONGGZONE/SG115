<?php
	include_once('wrap.header.php');
	$r = _MQ("select * from smart_setup where s_uid = 1 ");
	$SMSUser = onedaynet_sms_user();
	$SMSCnt = 0;
	if($SMSUser['code'] == 'U00') $SMSCnt = $SMSUser['data'];
	else $SMSCnt = '<a href="_config.sms.out_list.php" class="c_btn dark line">발송불가</a>';
?>
<form action="_config.password.pro.php" method="post">


	<div class="group_title"><strong>비밀번호 보안설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">변경 안내주기</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="member_cpw_period" class="design t_right" value="<?php echo $r['member_cpw_period']; ?>" style="width:70px" required placeholder="0">
							<span class="fr_tx">개월</span>
						</div>
						<div class="tip_box">
							<?php echo _DescStr('월 단위로 지정할 수 있으며, 회원이 비밀번호를 변경한 날로부터 지정한 개월 수를 넘을 경우 작동됩니다.'); ?>
							<?php echo _DescStr('예시) 3개월로 입력한 경우 3개월 동안 한 번도 비밀번호를 변경하지 않은 회원은 3개월 후 로그인 시 비밀번호 변경 안내페이지가 노출됩니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>비밀번호 찾기 방법</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">이메일</th>
					<td colspan="3">
						<?php echo _InputRadio('_find_pw_email', array('Y', 'N'), ($r['s_find_pw_email'] == 'Y'?'Y':'N'), ' class="js_find_pw_email"', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('등록된 이메일 주소로 임시 비밀번호를 발송합니다.'); ?>
					</td>
				</tr>
				<tr>
					<th class="">휴대폰</th>
					<td>
						<?php echo _InputRadio('_find_pw_sms', array('Y', 'N'), ($r['s_find_pw_sms'] == 'Y'?'Y':'N'), ' class="js_find_pw_sms"', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('등록된 휴대폰으로 임시 비밀번호를 발송합니다.'); ?>
						<?php echo _DescStr('문자 발송을 위하여 SMS 잔여건수를 꼭 확인해주시기 바랍니다.'); ?>
					<th>SMS정보</th>
					<td>
						<span class="t_red t_big "><?php echo (is_numeric($SMSCnt)?number_format($SMSCnt , 1).'건':$SMSCnt); ?></span>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
							<a href="_config.sms.out_list.php" class="c_btn h27 sky only_pc_view">SMS 충전관리</a>
							<a href="_config.sms.out_list.php" class="c_btn h27 sky line">SMS 발송내역</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="c_btnbox type_full">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" value="확인" /></span></li>
		</ul>
	</div>

</form>
<?php include_once('wrap.footer.php'); ?>