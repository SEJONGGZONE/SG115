<?php
/*
	-- "080수신거부설정" => "080deny/_receipt.form"
*/
?>

<form name='frmReceipt' id="frmReceipt" method='post' action="/addons/080deny/_receipt.pro.php">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>신청 전화번호</th>
					<td>
						<input type="text" name="_deny_tel" class="design" value="<?php echo $siteInfo['s_deny_tel']; ?>" placeholder="신청 전화번호">
						<?php echo _DescStr('biz080.com에 신청하신 080 번호를 입력하시기 바랍니다.'); ?>
					</td>
					<th>사용여부</th>
					<td>
						<?php echo _InputRadio("_deny_use", array('Y','N'), $siteInfo['s_deny_use']?$siteInfo['s_deny_use']:'N', '', array('사용','미사용'), '') ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<a href="https://www.onedaynet.co.kr/p/add_10.html" class="c_btn h27 sky  if_with_tip" target="_blank">080 수신거부 서비스소개 바로가기</a>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php
								echo _DescStr('biz080.com 사이트 신청 이후 아래 URL 주소를 help@biz080.com 로 보내주시기 바랍니다.');
								echo _DescStr("080 수신거부 연동 URL : <em>".$system['url']."/addons/080deny/deny.php</em>");
								echo _DescStr("080 수신거부 기록은 <em>080 수신거부 기록관리</em>에서 확인 가능합니다.","black");
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php  echo _submitBTNsub(); ?>
</form>
<script>

	// -- 080 수신거부 설정 서브밋 이벤트
	$(document).on('submit','#frmReceipt',function(){
		var chkVal = $(this).find('input[name="_deny_use"]:checked').val();
		var chkNum = $(this).find('input[name="_deny_tel"]').val();
		if( chkVal == 'Y'){
			if( chkNum == '' ||  chkNum == undefined){ alert("신청 전화번호를 입력해 주세요."); return false; }
			var regChkNum = /^[0-9]+$/;
			if( regChkNum.test(chkNum.replace(/-/gi,'')) == false){ alert('올바른 신청 전화번호를 입력해 주세요.'); return false; }
		}
		return true;
	});

	$(document).on('click', '.js_pg_popup', function(e) {
		e.preventDefault();
		var _mode = $(this).data('mode');
		var _page = $(this).data('page');
		var defaultPopWidth = 1120; // 팝업 기본 넓이값
		var defaultPopHeight = 540; // 팝업 기본 높이값
		var popWidth = $(this).data('width')*1;
		var popHeight = $(this).data('height')*1;

		if( popWidth == '' || popWidth == undefined){ $popWidth = defaultPopWidth; }
		if( popHeight == '' || popHeight == undefined){ $popHeight = defaultPopHeight; }

		window.open(_page+'?_mode='+_mode, _mode, 'width='+popWidth+',height='+popHeight+',top=100,scrollbars=yes');
	});
</script>

