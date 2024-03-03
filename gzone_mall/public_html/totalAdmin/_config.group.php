<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>

<form action="_config.group.pro.php" method="post" name="frm">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>등급 평가방법</th>
					<td>
						<?php echo _InputRadio('groupset_autouse', array('Y', 'N'), ($r['groupset_autouse']?$r['groupset_autouse']:'N'), '', array('자동평가', '수동평가'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('자동평가 : 설정된 자동 업데이트 기간마다 등급평가가 자동으로 진행됩니다.'); ?>
							<?php echo _DescStr('수동평가 : 관리자가 수동으로 직접 평가를 진행할 때 평가가 진행됩니다.'); ?>
							<?php echo _DescStr( rm_str($r['groupset_apply_rdate']) < 1 ? '최근 등급평가된 기록이 없습니다.':'최근 등급 평가일 : '.$r['groupset_apply_rdate'].'','blue' ); ?>
						</div>
					</td>
				</tr>
				<tr class="js_auto_daliy" <?php echo $r['groupset_autouse']=='N'?'style="display:none"':'';?>>
					<th>자동평가 업데이트</th>
					<td>
						<?php echo _InputRadio('groupset_auto_daily', array('day', 'week', 'month'), ($r['groupset_auto_daily']?$r['groupset_auto_daily']:'month'), '', array('매일', '매주','매달'), ''); ?>
					</td>
				</tr>
				<tr class="js_manual_daliy" <?php echo $r['groupset_autouse']=='Y'?'style="display:none"':'';?>>
					<th>수동평가 업데이트</th>
					<td>
						<a href="#none" onclick="return false;" class="c_btn blue groupset-auto-apply" data-apply="true">지금 수동평가 진행하기</a>
					</td>
				</tr>
				<tr>
					<th class="">평가기준 기간 설정</th>
					<td>
						<div class="lineup-row">
							<?php
								echo _InputSelect( "groupset_check_term" , array_keys($arrGroupsetCheckTerm['print']) , $r['groupset_check_term'] , "" , array_values($arrGroupsetCheckTerm['print']) , "-특정기간-");
							?>
							<a href="/totalAdmin/_member_group_set.list.php" class="c_btn sky line" target="_blank">등급관리 바로가기</a>
						</div>
						<div class="tip_box">
							<?php echo _DescStr('등급별 조건(구매금액/구매횟수) 설정 시 기준이 되는 기간을 선택합니다.'); ?>
							<?php echo _DescStr('기간옆의 기재된 날짜는 오늘을 기준으로 계산한 것이며, 설정 시 적용될 실제 날짜입니다.'); ?>
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
	$(document).on('click','.groupset-auto-apply',groupsetAutoApply)

	// -- 수동평가 클릭 시
	function groupsetAutoApply()
	{
		var apply = $('.groupset-auto-apply').attr('data-apply'); // 연속클릭방지
		if( apply != 'true'){ alert("[잠시만 기다려주세요.]\n현재 수동등급평가가 진행중입니다."); return false; }
		if( confirm("회원 등급 평가를 수동으로 진행하시겠습니까?") == false){ return false; }
		$('.groupset-auto-apply').attr('data-apply','false');
		$('[name="_mode"]').val('groupset_apply');
		$('form[name="frm"]').submit();
	}


	function switch_groupset(){
		var groupset_autouse = $("[name='groupset_autouse']:checked").val();

		if(groupset_autouse=='Y'){
			$('.js_auto_daliy').show();
			$('.js_manual_daliy').hide();
		}else{
			$('.js_auto_daliy').hide();
			$('.js_manual_daliy').show();
		}
	}
	$(document).ready(switch_groupset);
	$(document).on("change" , "[name='groupset_autouse']", switch_groupset );

	// -- 서브밋 검증
	function funcValidate()
	{
		$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
				groupset_check_term: { required: true }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
			},
			messages: {
				groupset_check_term: { required: "특정기간을 설정해 주세요." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
	}


</script>


<?php include_once('wrap.footer.php'); ?>