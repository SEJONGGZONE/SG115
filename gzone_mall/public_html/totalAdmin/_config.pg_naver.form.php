<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.pg_naver.pro.php" method="post">
	<div class="group_title"><strong>기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>사용여부</th>
					<td>
						<?php echo _InputRadio('npay_use', array('Y', 'N'), ($r['npay_use']?$r['npay_use']:'N'), '', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('네이버페이를 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
						<?php echo _DescStr('정상적인 심사 후에 사용 시 상품상세 구매하기 버튼과 장바구니 버튼 하단에 노출됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td>
						<a href="https://admin.pay.naver.com" class="c_btn h22 sky if_with_tip" target="_blank">네이버센터 바로가기</a>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_table">
							<ul class="thead">
								<li class="th" style="width:80px">순서</li>
								<li class="th">진행과정</li>
							</ul>
							<ul>
								<li class="th">계약/심사</li>
								<li class="t_left">
									- 네이버페이 센터 가입 및 계약을 진행합니다.<br/>
									- 승인이 완료되면 네이버에서 이메일로 네이버페이ID, 네이버페이 공통 인증키, 가맹점 인증키, 버튼 인증키를 받을 수 있습니다.<br/>
									- [버튼연동 설정]에 해당 값을 모두 입력해주세요.
								</li>
							</ul>
							<ul>
								<li class="th">버튼연동 심사</li>
								<li class="t_left">
									- [버튼연동 설정]에서 테스트모드로 적용합니다.<br/>
									- [버튼 연동심사 발송내용 보기]에 있는 이메일 그대로 보내주세요. (수신자,제목, 내용 그대로)<br/>
									- 승인이 완료되면 네이버에서 Access License와 Secret Key를 받을 수 있습니다.<br/>
									- [주문연동 설정]에 해당 값을 모두 입력해주세요.
								</li>
							</ul>
							<ul>
								<li class="th">주문연동 심사</li>
								<li class="t_left">
									- [주문연동 설정]에서 테스트모드로 적용합니다.<br/>
									- [주문 연동심사 발송내용 보기]에 있는 이메일 그대로 보내주세요. (수신자,제목, 내용 그대로)<br/>
									- 승인이 완료되면 네이버에서 Access License와 Secret Key를 다시 받을 수 있습니다.<br/>
									- [주문연동 설정]에 해당 값을 모두 새로 교체해주세요.
								</li>
							</ul>
							<ul>
								<li class="th">적용완료</li>
								<li class="t_left">
									- [버튼연동 설정]에 실적용모드로 변경해주세요.<br/>
									- [주문연동 설정]에 실적용모드로 변경해주세요.
								</li>
							</ul>
						</div>
						<?php echo _DescStr('네이버페이는 실 도메인 적용 및 PG사 계약 후 신청이 가능합니다.','blue'); ?>
						<?php echo _DescStr('주문연동 시 반품 및 교환은 연동되지 않으며 정산에 포함되지 않습니다.','red'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>버튼연동 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>활성화 모드</th>
					<td colspan="3">
						<?php echo _InputRadio('npay_mode', array('real', 'test'), ($r['npay_mode']?$r['npay_mode']:'test'), '', array('실적용 모드', '테스트 모드'), ''); ?>
						<?php echo _DescStr('연동 확인 심사 승인 전 까지 테스트 모드로 사용하며 승인 이후 실적용 모드로 변경하시기 바랍니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>네이버페이 ID</th>
					<td>
						<input type="text" name="npay_id" class="design" value="<?php echo $r['npay_id']; ?>" placeholder="네이버페이 ID">
						<?php echo _DescStr('네이버페이(NPay, 네이버체크아웃) 가입(계약)시 사용한 아이디를 입력하세요.'); ?>
					</td>
					<th>네이버 공통 인증키</th>
					<td>
						<input type="text" name="npay_all_key" class="design" value="<?php echo $r['npay_all_key']; ?>" placeholder="네이버 공통 인증키">
					</td>
				</tr>
				<tr>
					<th>가맹점 인증키</th>
					<td>
						<input type="text" name="npay_key" class="design" value="<?php echo $r['npay_key']; ?>" style="width:280px" placeholder="가맹점 인증키">
					</td>
					<th>버튼 인증키</th>
					<td>
						<input type="text" name="npay_bt_key" class="design" value="<?php echo $r['npay_bt_key']; ?>" style="width:280px" placeholder="버튼 인증키">
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('네이버페이 테스트 연동 요청을 먼저 하신 후 진행하여 주시기 바랍니다.'); ?>
						<?php echo _DescStr('네이버페이 계약 시 메일에 표시된 담당자에게 "<u class="js_npay_popup" data-mode="btn_notice">버튼 연동심사 발송내용</u>" 을 복사하여 보내주십시오.'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
							<a href="#none" class="c_btn h22 sky line js_npay_popup if_with_tip" data-mode="btn_notice">버튼 연동심사 발송내용 보기</a>
							<a href="https://www.onedaynet.co.kr/p/service.inqury_list.html" class="c_btn h22 sky if_with_tip" target="_blank">관련 오류문의</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>주문연동 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문연동</th>
					<td colspan="3">
						<?php echo _InputRadio('npay_sync_mode', array('real', 'test'), ($r['npay_sync_mode']?$r['npay_sync_mode']:'test'), '', array('실적용 모드', '테스트 모드'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>Access License</th>
					<td>
						<input type="text" name="npay_lisense" class="design" value="<?php echo $r['npay_lisense']; ?>" style="width:510px" placeholder="Access License">
					</td>
					<th>Secret Key</th>
					<td>
						<input type="text" name="npay_secret" class="design" value="<?php echo $r['npay_secret']; ?>" style="width:510px" placeholder="Secret Key">
					</td>
				</tr>
				<tr>
					<th>연동 심사 및 완료 메일</th>
					<td colspan="3">
						<div class="lineup-row">
							<a href="#none" class="c_btn h22 sky line js_npay_popup if_with_tip" data-mode="sync_notice">주문 연동심사 발송내용 보기</a>
							<a href="#none" class="c_btn h22 sky line js_npay_popup if_with_tip" data-mode="last_notice">최종 연동완료 발송내용 보기</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('심사를 위해 "주문 연동심사 발송내용"을 복사하여 보내주십시오.'); ?>
						<?php echo _DescStr('모든 연동을 완료 후 "최종 연동완료 발송내용"을 복사하여 보내주십시오.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">
	$(document).on('click', '.js_npay_popup', function(e) {
		e.preventDefault();
		var _mode = $(this).data('mode');
		window.open('_config.pg_naver.popup.php?_mode='+_mode, 'npay_notice', 'width=1120,height=800,top=100,scrollbars=yes');
	});
</script>
<?php include_once('wrap.footer.php'); ?>