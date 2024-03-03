<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");

$r['join_email_list'] = preg_replace('/[@]/','',$r['join_email_list']);
?>
<form action="_config.join.pro.php" method="post" name="frmConfigJoin">


	<div class="group_title"><strong>회원가입 승인정책</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>승인형태</th>
					<td>
						<?php echo _InputRadio('join_approve', array('Y', 'N'), ($r['join_approve']?$r['join_approve']:'N'), '', array('자동승인', '승인 후 가입'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('자동승인 : 회원가입 후 별도 절차 없이 자동 승인되어 바로 로그인 가능합니다.'); ?>
							<?php echo _DescStr('승인 후 가입 : 회원가입 후 관리자 승인 이후에 로그인 가능합니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>가입제한 아이디</th>
					<td>
						<input type="text" name="join_ban_id"  class="design js_tag" style="width:100%;" value="<?php echo $r['join_ban_id']; ?>">
						<div class="tip_box">
							<?php echo _DescStr('가입제한 아이디를 공백없이 입력해 주세요.(Enter 혹은 Tab으로 구분)'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>필수항목 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>아이디 제한</th>
					<td colspan="3">
						<div class="lineup-row">
							<span class="fr_tx">최소</span>
							<input type="text" name="join_id_limit_min" class="design t_right" value="<?php echo $r['join_id_limit_min']; ?>" style="width:50px;" placeholder="0">
							<span class="divi"></span>
							<span class="fr_tx">최대</span>
							<input type="text" name="join_id_limit_max" class="design t_right" value="<?php echo $r['join_id_limit_max']; ?>" style="width:50px;" placeholder="0">
						</div>
						<div class="tip_box">
							<?php echo _DescStr('가입 시 입력가능한 아이디 길이에 대한 최소~최대값을 설정합니다.'); ?>
							<?php echo _DescStr('최소 4자리 이상 입력하고, 최대값을 제한하지 않을 경우 0을 입력하세요.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>비밀번호 제한</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx">최소</span>
							<input type="text" name="join_pw_limit_min" class="design t_right" value="<?php echo $r['join_pw_limit_min']; ?>" style="width:50px;" placeholder="0">
							<span class="divi"></span>
							<span class="fr_tx">최대</span>
							<input type="text" name="join_pw_limit_max" class="design t_right" value="<?php echo $r['join_pw_limit_max']; ?>" style="width:50px;" placeholder="0">
						</div>
						<div class="tip_box">
							<?php echo _DescStr('가입 시 입력가능한 비밀번호 길이에 대한 최소~최대값을 설정합니다.'); ?>
							<?php echo _DescStr('최소 4자리 이상 입력하고, 최대값을 제한하지 않을 경우 0을 입력하세요.'); ?>
						</div>
					</td>
					<th>비밀번호 보안강화</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx" style="width:80px">특수문자 혼용</span>
							<?php echo _InputRadio('join_pw_sp_use', array('Y', 'N'), ($r['join_pw_sp_use']?$r['join_pw_sp_use']:'N'), '', array('필수', '비필수'), ''); ?>
							<input type="text" name="join_pw_sp_length" class="design js_join_pw_sp_use t_right" value="<?php echo $r['join_pw_sp_length']; ?>" style="width:50px;">
							<span class="fr_tx">개 이상 포함</span>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
							<span class="fr_tx" style="width:80px">대문자 혼용</span>
							<?php echo _InputRadio('join_pw_up_use', array('Y', 'N'), ($r['join_pw_up_use']?$r['join_pw_up_use']:'N'), '', array('필수', '비필수'), ''); ?>
							<input type="text" name="join_pw_up_length" class="design js_pw_up_use t_right" value="<?php echo $r['join_pw_up_length']; ?>" style="width:50px;">
							<span class="fr_tx">개 이상 포함</span>
						</div>
					</td>
				</tr>
				<tr>
					<th>이메일 선택 도메인</th>
					<td colspan="3">
						<input type="text" class="design js_tag" name="join_email_list" value="<?php echo $r['join_email_list']; ?>" style="width:100%;">
						<div class="tip_box">
							<?php echo _DescStr('회원가입/정보수정 시 이메일 선택 박스의 도메인 항목을 지정합니다. (공백없이 Enter 혹은 Tab으로 구분)'); ?>
							<?php echo _DescStr('위에서 지정한 도메인 외에도 "직접입력"항목이 있어 이메일 주소를 고객이 직접 입력 할수도 있습니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('여기서 설정하지 않은 항목은 정해진 규칙대로 입력해야 하고 별도로 설정할 수 없습니다.', ''); ?>
						<?php echo _DescStr('이름은 한글로만 입력하고, 휴대폰은 전화번호 형식대로 입력합니다.', ''); ?>
						<?php echo _DescStr('SNS가입은 해당 SNS아이디로 간편하게 가입하는 방식이므로 위 규칙을 적용할 수 없습니다.', 'red'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>추가항목 선택</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>주소</th>
					<td>
						<div class="lineup-row">
							<?php echo _InputRadio('join_addr', array('Y', 'N'), ($r['join_addr']?$r['join_addr']:'N'), '', array('사용', '미사용'), ''); ?>
							<span class="divi"></span>
							<?php echo _InputRadio('join_addr_required', array('Y', 'N'), ($r['join_addr_required']?$r['join_addr_required']:'N'), '', array('필수', '비필수'), ''); ?>
						</div>
					</td>
					<th>전화번호</th>
					<td>
						<div class="lineup-row">
							<?php echo _InputRadio('join_tel', array('Y', 'N'), ($r['join_tel']?$r['join_tel']:'N'), '', array('사용', '미사용'), ''); ?>
							<span class="divi"></span>
							<?php echo _InputRadio('join_tel_required', array('Y', 'N'), ($r['join_tel_required']?$r['join_tel_required']:'N'), '', array('필수', '비필수'), ''); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>생년월일</th>
					<td>
						<div class="lineup-row">
							<?php echo _InputRadio('join_birth', array('Y', 'N'), ($r['join_birth']?$r['join_birth']:'N'), '', array('사용', '미사용'), ''); ?>
							<span class="divi"></span>
							<?php echo _InputRadio('join_birth_required', array('Y', 'N'), ($r['join_birth_required']?$r['join_birth_required']:'N'), '', array('필수', '비필수'), ''); ?>
						</div>
					</td>
					<th>성별</th>
					<td>
						<div class="lineup-row">
							<?php echo _InputRadio('join_sex', array('Y', 'N'), ($r['join_sex']?$r['join_sex']:'N'), '', array('사용', '미사용'), ''); ?>
							<span class="divi"></span>
							<?php echo _InputRadio('join_sex_required', array('Y', 'N'), ($r['join_sex_required']?$r['join_sex_required']:'N'), '', array('필수', '비필수'), ''); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>스팸방지</th>
					<td colspan="3">
						<div class="lineup-row">
							<?php echo _InputRadio('join_spam', array('Y', 'N'), ($r['join_spam']?$r['join_spam']:'N'), '', array('사용', '미사용'), ''); ?>
							<span class="divi"></span>
							<?php echo _InputRadio('join_spam_required', array('Y', 'N'), 'Y', 'disabled', array('필수', '비필수'), ''); ?>
							<a href="_config.sns.form.php?menuUid=15" class="c_btn sky line" target="_blank">구글 API설정 바로가기</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>

						<?php echo _DescStr('스팸방지는 구글 reCAPTCHA를 적용하며, 환경설정에서 API를 먼저 설정해주세요.'); ?>
						<?php echo _DescStr('스팸방지는 기능 특성 상 사용여부는 설정가능하나, 무조건 필수 항목으로 분류됩니다.'); ?>
						<?php echo _DescStr('기능 사용 시 회원가입 정보입력 페이지 마지막에 [로봇이 아닙니다]를 체크해야만 가입이 완료됩니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	$(document).ready(function(){
		// -  validate ---
		$('form[name=frmConfigJoin]').validate({
			ignore: '.ignore',
			rules: {
				join_id_limit_min: { required: true , min: 4  }
				, join_pw_limit_min: { required: true , min : 4 }
			},
			messages: {
				join_id_limit_min: { required: '(아이디) 최소 길이 값을 입력해 주세요,' , min: '(아이디) 최소 길이는 4 이상 입력하셔야합니다.'  }
				, join_pw_limit_min: { required: '(비밀번호) 최소 길이 값을 입력해 주세요,' , min: '(비밀번호) 최소 길이는 4 이상 입력하셔야합니다.'  }
			},
			submitHandler : function(form) {

				form.submit();
			}
		});
		// - validate ---
	});
</script>

<?php include_once('wrap.footer.php'); ?>