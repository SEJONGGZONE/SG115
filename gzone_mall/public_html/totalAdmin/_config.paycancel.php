<?php
	include_once('wrap.header.php');
	$r = _MQ(" select s_paycancel_method,s_order_auto_cancel_term from smart_setup where s_uid = 1 ");
?>
<form action="_config.paycancel.pro.php" method="post">
	<div class="group_title"><strong>할인금액 부분취소 환불방식</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>환불방식 선택</th>
					<td colspan="3">
						<?php echo _InputRadio('s_paycancel_method', array('B', 'D'), ($r['s_paycancel_method']?$r['s_paycancel_method']:'B'), '', array('최종 환불', '분배 환불'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>최종 환불방식 예시</th>
					<td>
						<?php echo _DescStr('마지막 상품까지 취소하여 전체 취소가 되었을 때, 사용 적립금과 쿠폰을 반환하고 할인 금액을 뺀 나머지 금액을 환불하는 방식',''); ?>

						<div class="tip_table">
							<ul class="thead">
								<li class="th t_right">상품A 500원 + 상품B 300원 + 상품C 1,000원<br/>총 상품금액 1,800원 - 할인금액 400원 = 최종결제 1,400원</li>
							</ul>
						</div>

						<div class="tip_table">
							<ul class="thead">
								<li class="th">취소상품</li>
								<li class="th">상품금액</li>
								<li class="th">할인차감</li>
								<li class="th">환불금액</li>
							</ul>
							<ul>
								<li class="th">상품A 취소 시</li>
								<li class="t_right">500원</li>
								<li class="t_right t_none">0원</li>
								<li class="t_right t_blue">500원</li>
							</ul>
							<ul>
								<li class="th">상품B 취소 시</li>
								<li class="t_right">300원</li>
								<li class="t_right t_none">0원</li>
								<li class="t_right t_blue">300원</li>
							</ul>
							<ul>
								<li class="th">상품C 취소 시</li>
								<li class="t_right">1,000원</li>
								<li class="t_right t_red">400원</li>
								<li class="t_right t_blue">600원</li>
							</ul>
							<ul>
								<li class="th">합계</li>
								<li class="t_right t_black t_bold">1,800원</li>
								<li class="t_right t_black t_bold">400원</li>
								<li class="t_right t_black t_bold">1,400원</li>
							</ul>
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>

						<?php echo _DescStr('마지막 상품 금액이 할인 금액 보다 작다면 할인 금액과 상품 금액의 차액만큼 전에 취소한 상품 금액에서 할인됩니다.',''); ?>

						<div class="tip_table">
							<ul class="thead">
								<li class="th">취소상품</li>
								<li class="th">상품금액</li>
								<li class="th">할인차감</li>
								<li class="th">환불금액</li>
							</ul>
							<ul>
								<li class="th">상품A 취소 시</li>
								<li class="t_right">500원</li>
								<li class="t_right t_none">0원</li>
								<li class="t_right t_blue">500원</li>
							</ul>
							<ul>
								<li class="th">상품C 취소 시</li>
								<li class="t_right">1,000원</li>
								<li class="t_right t_red">100원</li>
								<li class="t_right t_blue">900원</li>
							</ul>
							<ul>
								<li class="th">상품B 취소 시</li>
								<li class="t_right">300원</li>
								<li class="t_right t_red">300원</li>
								<li class="t_right t_blue">0원</li>
							</ul>
							<ul>
								<li class="th">합계</li>
								<li class="t_right t_black t_bold">1,800원</li>
								<li class="t_right t_black t_bold">400원</li>
								<li class="t_right t_black t_bold">1,400원</li>
							</ul>
						</div>

					</td>
					<th>분배 환불방식 예시</th>
					<td style="vertical-align:top">
						<?php echo _DescStr('상품을 취소할 때마다 할인된 금액이 상품 금액의 비율로 분배되어 환불하는 방식',''); ?>

						<div class="tip_table">
							<ul class="thead">
								<li class="th t_right">상품A 3,000원 + 상품B 2,000원 + 상품C 1,000원<br/>총 상품금액 6,000원 - 할인금액 1,000원 = 최종결제 5,000원</li>
							</ul>
						</div>

						<div class="tip_table">
							<ul class="thead">
								<li class="th">취소상품</li>
								<li class="th">상품금액</li>
								<li class="th">할인차감</li>
								<li class="th">환불금액</li>
							</ul>
							<ul>
								<li class="th">상품A 취소 시</li>
								<li class="t_right">3,000원</li>
								<li class="t_right t_red">500원</li>
								<li class="t_right t_blue">2,500원</li>
							</ul>
							<ul>
								<li class="th">상품B 취소 시</li>
								<li class="t_right">2,000원</li>
								<li class="t_right t_red">330원</li>
								<li class="t_right t_blue">1,670원</li>
							</ul>
							<ul>
								<li class="th">상품C 취소 시</li>
								<li class="t_right">1,000원</li>
								<li class="t_right t_red">170원</li>
								<li class="t_right t_blue">830원</li>
							</ul>
							<ul>
								<li class="th">합계</li>
								<li class="t_right t_black t_bold">6,000원</li>
								<li class="t_right t_black t_bold">1,000원</li>
								<li class="t_right t_black t_bold">5,000원</li>
							</ul>
						</div>

						<?php echo _DescStr('상품 금액 X 할인 금액 / 총 상품 금액 = 상품별 할인 금액 (소수점 이하인 경우 1원 반올림)','black'); ?>
						<?php echo _DescStr('상품A의 할인금액 : (3,000 x 1,000) ÷ 6,000 = 500원',''); ?>
						<?php echo _DescStr('상품B의 할인금액 : (2,000 x 1,000) ÷ 6,000 = 330원 (333.3원)',''); ?>
						<?php echo _DescStr('상품C의 할인금액 : (1,000 x 1,000) ÷ 6,000 = 167원 (166.6원)',''); ?>

					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>결제대기 주문 자동취소</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>자동취소 일수</th>
					<td colspan="3">
						<div class="lineup-row">
							<input type="text" name="s_order_auto_cancel_term" class="design t_right number_style" style="width:70px;" value="<?php echo $r['s_order_auto_cancel_term']?>" placeholder="0"/>
							<span class="fr_tx">일</span>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('설정값이 0인 경우 주문이 자동 취소되지 않습니다.'); ?>
						<?php echo _DescStr('자동취소는 1일 1회 작동되며, 쇼핑몰에 접속로그기록이 있을 때 실행됩니다.'); ?>
						<?php echo _DescStr('결제대기는 결제수단이 무통장입금 및 가상계좌인 주문뿐 아니라 신용카드, 계좌이체, 핸드폰 결제 진행대기중인 주문도 포함됩니다.(무통장의 경우는 입금기한)'); ?>
						<?php echo _DescStr('예) 자동취소 1일 설정 시 2023-03-01에 주문시 2023-03-02까지 입금이 되어야하고 계속 결제대기일 경우 2023-03-03 정각에 취소됩니다.'); ?>
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