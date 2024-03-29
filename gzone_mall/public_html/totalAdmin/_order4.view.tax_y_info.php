<?php
	// /totalAdmin/_order4.view.php 에 include 됨.
	// * 입점업체 "세금계산서"(공급받는자) 정보
	// $_tax_type = 'Y';//과세
	// 과세수수료 - app_vat_Y_discount
	// $r  ==> 정산 배열 정보
	// $partner ==> 입점업체 배열 정보

	// 수수료
	$app_document_discount = $app_vat_Y_discount;
?>
<!-- 입점업체세금계산서(공급받는자) 정보 -->
<div class="group_title">
	<strong>입점업체 세금계산서(공급받는자) 정보</strong>
	<?php if($AdminPath == 'totalAdmin') { ?>
	<div class="btn_box">
		<?php echo $app_tax_btn_vatY; ?>
		<?php
			if($r['s_tax_mgtnum'] &&  in_array($r['s_tax_status'] , array(2010 ,2011 , 3014 , 3011))) {
				// 인쇄 팝업 URL
				$app_tax_mgtnum = $r['s_tax_mgtnum'];
				$app_tax_type = 'Y'; // Y:과세(세금계산서), N:면세(계산서)
				include(OD_ADDONS_ROOT.'/barobill/api_ti/_tax.GetTaxInvoicePrintURL.php');
			}
		?>
	</div>
	<?php } ?>
</div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>세금계산서 연동상태</th>
				<td colspan="3">
					<?php echo ($r['s_tax_status']?($r['s_tax_status'] < 0?$arr_error_code[$r['s_tax_status']]:$arr_inner_state_table[$r['s_tax_status']]):'미발행상태'); ?>
					<?php
					if($r['s_tax_mgtnum'] && in_array($r['s_tax_status'], array(1000 , 2010 , 2011 , 3014 , 3011))) {
						include(OD_ADDONS_ROOT.'/barobill/api_ti/_tax.GetBalanceCostAmount.php'); // 세금계산서 잔여포인트 추출 - return_balance
					?>
						<div class="t_orange">(바로빌 잔여포인트 : <u><?php echo number_format($return_balance); ?>P</u>)</div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>업체명</th>
				<td><?php echo ($partner['cp_name']?$partner['cp_name']:'-'); ?></td>
				<th>사업자번호</th>
				<td><?php echo ($partner['cp_number']?$partner['cp_number']:'-'); ?></td>
			</tr>
			<tr>
				<th>대표자</th>
				<td><?php echo ($partner['cp_ceoname']?$partner['cp_ceoname']:'-'); ?></td>
				<th>주소</th>
				<td><?php echo ($partner['cp_address']?$partner['cp_address']:'-'); ?></td>
			</tr>
			<tr>
				<th>업태</th>
				<td><?php echo ($partner['cp_item1']?$partner['cp_item1']:'-'); ?></td>
				<th>업종</th>
				<td><?php echo ($partner['cp_item2']?$partner['cp_item2']:'-'); ?></td>
			</tr>
			<tr>
				<th>세금계산서 발행금액</th>
				<td colspan="3">
					수수료 <u><?php echo number_format($app_document_discount); ?>원</u>에 대한 세금계산서를 발행합니다.
				</td>
			</tr>
			<?php if($AdminPath == 'totalAdmin') { ?>
				<tr>
					<th>세금계산서 발행순서</th>
					<td colspan="3">
						<?php echo _DescStr('미발행(해당 문서 정보가 없습니다. 라고 표기됩니다.',''); ?>
						<?php echo _DescStr('1. 세금계산서 임시저장',''); ?>
						<?php echo _DescStr('2. 세금계산서 발행 (임시저장시 발행가능)',''); ?>
						<?php echo _DescStr('3. 세금계산서 취소 (발행시 취소가능)',''); ?>
						<?php echo _DescStr('4. 세금계산서 삭제 (임시저장, 발행시 삭제가능)',''); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>