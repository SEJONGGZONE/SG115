<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
// member_return_type
?>

<form action="_config.sleep.pro.php" method="post" name="frm">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="">해제방법</th>
					<td>
						<?php echo _InputRadio('member_return_type', array('login','auth'), ($r['member_return_type']?$r['member_return_type']:'auth'), '', array('인증없이 휴면해제','이메일 인증 후 휴면해제')); ?>
						<div class="tip_box">
							<?php echo _DescStr('인증없이 휴면해제 : 로그인 후 바로 노출되는 인증버튼을 클릭하면 해제가 됩니다.'); ?>
							<?php echo _DescStr('이메일 인증 후 휴면해제 : 가입한 이메일로 받은 휴면해제 인증버튼을 클릭하면 해제가 됩니다. '); ?>
						</div>
					</td>
					<th class="">휴면전환 개월 수</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="member_sleep_period" class="design t_right" value="<?php echo $r['member_sleep_period']; ?>" style="width:70px" placeholder="0" required>
							<span class="fr_tx">개월</span>
						</div>
						<div class="tip_box">
							<?php echo _DescStr('마지막 로그인 날짜로부터 위 기간이 경과될 동안 로그인 기록이 없으면 1일 1회 체크하여 자동으로 휴면전환됩니다.'); ?>
							<?php echo _DescStr('정보통신망법이 변경됨에 따라 자유롭게 설정하여 사용가능합니다.','red'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>해제 후 등급초기화</th>
					<td colspan="3">
						<?php echo _InputRadio('member_return_groupinit', array('Y', 'N'), ($r['member_return_groupinit']?$r['member_return_groupinit']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('사용 : 휴면해제시 기존 등급과 상관없이 기본순위(최하위) 등급으로 변경됩니다.'); ?>
							<?php echo _DescStr('미사용 : 휴면해제 시 기존 등급이 그대로 유지됩니다.'); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


		<div class="c_btnbox type_full">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
			</ul>
		</div>

</form>


<script>


$(document).ready(funcValidate); // validate 검사

// -- 서브밋 검증
function funcValidate()
{
	$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					member_sleep_period: { required: true , min : 12 }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
			},
			messages: {
					member_sleep_period: { required: "휴면회원전환 개월 수를 입력해 주세요." , min : "휴면회원 전환 개월 수는 최소 12개월 이상 입력해 주세요." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
	});
}


</script>


<?php include_once('wrap.footer.php'); ?>