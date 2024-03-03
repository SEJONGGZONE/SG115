<?php
include_once('wrap.header.php');
?>
<form action="_config.member.pro.php" method="post">
<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>본인확인 서비스 사용</th>
					<td colspan="3">
						<?php echo _InputRadio('_join_auth_use', array('Y', 'N'), ($siteInfo['s_join_auth_use'] == 'Y'?'Y':'N'), '', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('본인인증 서비스를 사용할 경우 회원가입 시 휴대폰 본인인증 후에만 가입이 가능하고 수정시 마다 인증을 받아야합니다.'); ?>
					</td>
				</tr>
				<tr class="cls_join_auth" <?php echo $siteInfo['s_join_auth_use']=='N'?'style="display:none"':'';?>>
					<th>KCP 회원사 코드</th>
					<td>
						<input type="text" name="_join_auth_kcb_code" class="design" value="<?php echo $siteInfo['s_join_auth_kcb_code']; ?>" style="width:185px" placeholder="KCP 회원사 코드">
						<?php echo _DescStr('발급받은 KCP 회원사 코드를 입력해주세요.'); ?>
					</td>
					<th>KCP 가맹점 인증키</th>
					<td>
						<input type="text" name="_join_auth_kcb_enckey" class="design" value="<?php echo $siteInfo['s_join_auth_kcb_enckey']; ?>" style="width:500px" placeholder="KCP 가맹점 인증키">
						<?php echo _DescStr('발급받지 않은 경우 공란으로 비워두시기 바랍니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<div class="lineup-row">
							<a href="https://www.onedaynet.co.kr/p/add_02_1.html" class="c_btn sky " target="_blank">본인인증 서비스안내 바로가기</a>
							<a href="https://admin8.kcp.co.kr/assist/hp.HomePageMcshCertApplyAction.do?cmd=apply&host_id=sangsang" class="c_btn sky line" target="_blank">신청하기</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('위 "신청하기" 버튼을 통하지 않고, KCP와 직접 계약하여 진행할 경우 본인확인 서비스가 제대로 작동되지 않을 수 있습니다.', 'red'); ?>
							<?php echo _DescStr('KCP 본인확인 테스트 코드 사용방법', 'black'); ?>
							<?php echo _DescStr('
								1. 테스트 회원사 코드는 <em>S6186</em>입니다. 회원사 코드에 테스트 회원사 코드를 입력해 주시기 바랍니다.<br>
								2. 테스트 가맹점 인증키는 <em>E66DCEB95BFBD45DF9DFAEEBCB092B5DC2EB3BF0</em>입니다. 가맹점 인증키에 테스트 가맹점 인증키를 입력해 주시기 바랍니다.<br>
								3. 테스트 회원사 코드 입력 시 <em>KT</em>로만 인증 가능합니다. <br>
								4. 그외 인증정보(성명, 생년월일, 성별, 휴대폰번호)는 임의로 입력가능 합니다.<br>
								5. 인증 문자가 발송되는 대신 <em>OTP_NO = XXXXXXX</em>와같은 형식으로 알림창에 인증번호가 노출됩니다. <br>
								6. 알림창에 노출된 인증번호를 입력하면 인증이 완료됩니다.<br>
								7. 인증번호가 노출되지 않을 경우 임의의 6자리 숫자를 입력하시면 인증이 완료됩니다. <br>
							'); ?>
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

<script>
	function switch_join_auth(){
		var _join_auth_use = $("[name='_join_auth_use']:checked").val();

		if(_join_auth_use=='Y'){
			$(".cls_join_auth").show();
		}else{
			$(".cls_join_auth").hide();
		}
	}
	$(document).ready(switch_join_auth);
	$(document).on("change" , "[name='_join_auth_use']", switch_join_auth );

</script>
<?php include_once('wrap.footer.php'); ?>