<?php include_once('wrap.header.php'); ?>
<form action="_config.orderbank.pro.php" method="post">
<input type="hidden" name="_mode" value="modify">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용여부</th>
					<td>
						<?php echo _InputRadio( '_bank_autocheck_use' , array('Y','N') , $siteInfo['s_bank_autocheck_use'] , '' , array('사용','미사용') , ''); ?>
					</td>
					<th>APIBOX 아이디</th>
					<td>
						<input type="text" name="_apibox_id" class="design" style="width:250px;" value="<?php echo $siteInfo["s_apibox_id"]; ?>" placeholder="APIBOX 아이디"/>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<a href="http://apibox.kr" class="c_btn h22 sky sky if_with_tip" target="_blank">apibox 바로가기</a>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr("무통장 계좌번호는 무통장 입금 계좌에 등록하신 계좌번호와 반드시 일치해야 합니다.",''); ?>
						<?php echo _DescStr("apibox 홈페이지를 통해 회원가입한 아이디를 입력하세요."); ?>
						<?php echo _DescStr("apibox 로그인 후 <em>[마이페이지 > 무통장입금자동통보 > 계좌번호관리]</em>에서 계좌번호를 등록하여 사용하세요."); ?>
						<?php echo _DescStr("콜백주소 : ".$app_HTTP_URL."/addons/apibox/_api.bank.auto.check.php"); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>

</form>

<?php include_once('wrap.footer.php'); ?>