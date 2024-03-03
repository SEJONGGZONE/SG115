<?php

	include_once('wrap.header.php');
	$r = _MQ("select * from smart_cntlog_config where clc_uid = 1 ");

?>
<form action="_config.cntlog.pro.php" method="post">
<input type="hidden" name="_mode" value="config">
<input type="hidden" name="Now_Connect_Use" value="N">
<input type="hidden" name="Now_Connect_Term" value="30">

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>카운터 사용여부</th>
					<td >
						<?php echo _InputRadio('_counter_use', array('Y', 'N'), ($r['clc_counter_use'] == 'Y'?'Y':'N'), '', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('미사용 시에는 방문자를 기록하지 않습니다.'); ?>
					</td>
					<th>접속자료 초기화</th>
					<td>
						<a href="_config.cntlog.pro.php?_mode=all" onclick="if(!confirm('통계 자료에 대해 정말 초기화 하시겠습니까?')) return false;" class="c_btn h28 red line">전체 접속자료 초기화</a>
						<?php echo _DescStr('한번 초기화하면 복구가 불가하니 신중하게 선택해주세요.'); ?>
					</td>
				</tr>
				<tr>
					<th>중복 접속설정</th>
					<td >
						<?php echo _InputRadio('_cookie_use', array('A', 'T', 'O'), ($r['clc_cookie_use']?$r['clc_cookie_use']:'O'), '', array('접속하는대로 증가' , '지정된 시간대로 증가' , '하루에 한번 증가'), ''); ?>
						<?php echo _DescStr('접속하는 대로 증가 : 새로고침 시 마다 모두 기록합니다.'); ?>
						<?php echo _DescStr('지정된 시간대로 증가 : 중복접속 시간설정에 입력한 시간동안은 1회 접속으로 기록합니다.'); ?>
						<?php echo _DescStr('하루에 한번 증가 : 24시간 기준 하루 한번 기록합니다.'); ?>
					</td>
					<th>중복접속 시간설정</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_cookie_term" class="design t_right" value="<?php echo $r['clc_cookie_term']; ?>" style="width:100px">
							<span class="fr_tx">초</span>
						</div>
						<?php echo _DescStr('중복 접속 설정에서 지정된 시간대로 설정할 경우 위 입력된 시간동안은 1회 접속으로만 기록합니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>관리자 접속 통계</th>
					<td>
						<?php echo _InputRadio('_admin_check_use', array('Y', 'N'), ($r['clc_admin_check_use'] == 'Y'?'Y':'N'), '', array('포함', '미포함'), ''); ?>
						<?php echo _DescStr('포함 : 관리자의 접속까지 모두 통계에 포함합니다.'); ?>
						<?php echo _DescStr('미포함 : 관리자의 IP는 접속 통계에서 제외합니다.'); ?>
					</td>
					<th>관리자 접속 IP</th>
					<td>
						<input type="text" name="_admin_ip" class="design" value="<?php echo $r['clc_admin_ip']; ?>" style="width:120px" placeholder="관리자접속 IP">
						<?php echo _DescStr('관리자 접속을 포함시키지 않을 때 위 입력된 IP접속자를 통계에서 제외합니다.'); ?>
						<?php echo _DescStr('아래 현재 아이피를 그대로 입력해주시면 됩니다.'); ?>
						<?php echo _DescStr('현재 아이피 : '.$_SERVER['REMOTE_ADDR'], 'blue'); ?>
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