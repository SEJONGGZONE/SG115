<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.paymethod.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>무통장</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_B', array('Y', 'N'), ($r['s_pg_paymethod_B']?$r['s_pg_paymethod_B']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
					<th>카드</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_C', array('Y', 'N'), ($r['s_pg_paymethod_C']?$r['s_pg_paymethod_C']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>실시간 계좌이체</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_L', array('Y', 'N'), ($r['s_pg_paymethod_L']?$r['s_pg_paymethod_L']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
					<th>가상계좌</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_V', array('Y', 'N'), ($r['s_pg_paymethod_V']?$r['s_pg_paymethod_V']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>휴대폰 결제</th>
					<td colspan="3">
						<?php echo _InputRadio('s_pg_paymethod_H', array('Y', 'N'), ($r['s_pg_paymethod_H']?$r['s_pg_paymethod_H']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<div class="tip_box">
							<?php
								echo _DescStr('실시간 계좌이체는 PG사에 따라 모바일에서는 제공되지 않을 수 있습니다.','');
								echo _DescStr('휴대폰 결제 서비스 사용여부를 설정하기 전 이용중인 통합  전자결제(PG) 서비스를 확인 후 해당 PG사에서 휴대폰 결제 서비스를 신청해 주셔야 합니다.','');
								echo _DescStr("이용중인 통합  전자결제(PG) 서비스가 아닌 별도의 휴대폰 결제 서비스를 신청하실경우 사용이 불가능 합니다.","");
								echo _DescStr("휴대폰 결제 취소는 결제 월 말 일까지만 처리 가능하며 결제 월 이후에는 주문취소 후 고객에게 직접 환불 해야 합니다.","");
								echo _DescStr("복합과세의 경우 반드시 PG와 먼저 복합과세 계약을 신청하셔야합니다.","");
							?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<!-- 저장 -->
	<div class="c_btnbox type_full">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" value="확인" /></span></li>
		</ul>
	</div>
	<!-- 저장 -->
</form>
<?php include_once('wrap.footer.php'); ?>