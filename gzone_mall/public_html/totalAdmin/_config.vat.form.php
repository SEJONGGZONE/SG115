<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form method="post" action="_config.vat.pro.php" >


	<!-- 상품 과세설정 -->
	<div class="group_title"><strong>기본 과세설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>상품 과세 여부</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세만 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_product' value='Y'>";
							}
							else {
								echo _InputRadio( "s_vat_product" , array('Y','N','C') , $siteInfo['s_vat_product'] ? $siteInfo['s_vat_product'] : "Y" , "" , array('과세','면세','복합과세') , "");
								echo '
								';
							}
						?>
					</td>
					<th>배송비 과세 여부</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세만 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_product' value='Y'>";
							}
							else {
								if($SubAdminMode){
									echo _InputRadio( "s_vat_delivery" , array('Y','N','C') , $siteInfo['s_vat_delivery'] ? $siteInfo['s_vat_delivery'] : "Y" , "" , array('과세','면세','복합과세') , "");
								}
								else {
									echo _InputRadio( "s_vat_delivery" , array('Y','N') , $siteInfo['s_vat_delivery'] ? $siteInfo['s_vat_delivery'] : "Y" , "" , array('과세','면세') , "");
								}
								echo '

								';
							}
						?>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('과세 : 별도 선택없이, 전체상품이 과세로 자동 적용됩니다.'); ?>
						<?php echo _DescStr('면세 : 별도 선택없이, 전체상품이 면세로 자동 적용됩니다.'); ?>
						<?php echo _DescStr('복합과세 : 상품 별로 과세여부를 선택할 수 있습니다.'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('면세나 복합과세로 결제할 경우 PG사에 면세, 복합과세로의 사용 요청을 하셔야 합니다.','red'); ?>
						<?php echo _DescStr('과세일 경우 세금계산서, 면세일 경우 계산서가 발급되며, 정산시 과세와 면세로 분리하여 확인할 수 있습니다.'); ?>
						<?php echo _DescStr('주문배송>정산관리>정산완료목록에서 상품별로 상품금액과 배송비에 대한 과세와 면세를 확인할 수 있습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 상품 과세설정 -->




	<!-- 할인액 과세설정 -->
	<div class="group_title"><strong>할인금액 과세설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>적용방식</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세부터 차감으로 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_discount' value='Y'>";
							}
							else {
								echo _InputRadio( "s_vat_discount" , array('Y','N' ,'D') , $siteInfo['s_vat_discount'] ? $siteInfo['s_vat_discount'] : "Y" , "" , array('과세 상품부터 차감','면세 상품부터 차감' , '비율로 차감') , "");

								echo '
									<div class="dash_line"><!-- 점선라인 --></div>
										' . _DescStr("PG사에 의한 결제(카드, 실시계좌이체 등) 시 복합과세로 결제가 될 경우, 할인 비용에 대한 설정이며, 이는 쇼핑몰 내 정산과는 무관합니다.") . '
										' . _DescStr("할인액은 적립금, 예치금, 쿠폰, 프로모션 코드 등의 사용에 의해 결제시 할인된 비용을 의미합니다.") . '
										' . _DescStr("할인액에 대한 과세설정을 변경할 경우 PG적용 비용과 정산내역이 달라질 수 있으니 주의하시기 바랍니다.","red") . '
										' . _DescStr("<div class='dash_line'><!-- 점선라인 --></div>설정 예시) 과세 : 50,000원, 면세 : 50,000원, 할인액 : 10,000원의 복합 과세로 90,000원이 결제될 경우 다음과 같습니다.","") . '
										' . _DescStr("

											<div class='tip_table'>
												<ul class='thead'>
													<li class='th'>구분</th>
													<li class='th'>과세 상품부터 차감<br/>(과세 100%)</li>
													<li class='th'>면세 상품부터 차감<br/>(면세 100%)</li>
													<li class='th'>비율로 차감<br/>(각 50%)</li>
												</ul>
												<ul>
													<li class='th'>과세상품 (공급가 + 부가세(10%))</li>
													<li class='t_right'>45,000원<br/>+ 5,000원<br/>= 50,000원</li>
													<li class='t_right'>45,000원<br/>+ 5,000원<br/>= 50,000원</li>
													<li class='t_right'>45,000원<br/>+ 5,000원<br/>= 50,000원</li>
												</ul>
												<ul>
													<li class='th'>할인적용</li>
													<li class='t_right t_red t_bold'>-10,000원</li>
													<li class='t_right t_none'>미적용</li>
													<li class='t_right t_blue t_bold'>-5,000원</li>
												</ul>
												<ul>
													<li class='th'>면세상품 (공급가 + 부가세(없음))</li>
													<li class='t_right'>50,000원</li>
													<li class='t_right '>50,000원</li>
													<li class='t_right'>50,000원</li>
												</ul>
												<ul>
													<li class='th'>할인적용</li>
													<li class='t_right t_none'>미적용</li>
													<li class='t_right t_red t_bold'>-10,000원</li>
													<li class='t_right t_blue t_bold'>-5,000원</li>
												</ul>
												<ul>
													<li class='th'>최종 금액</li>
													<li class='t_right'>90,000원</li>
													<li class='t_right'>90,000원</li>
													<li class='t_right'>90,000원</li>
												</ul>
											</div>
										") . '
								';

							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 할인액 과세설정 -->


	<?php echo _submitBTNsub(); ?>

</form>



<?php include_once('wrap.footer.php'); ?>