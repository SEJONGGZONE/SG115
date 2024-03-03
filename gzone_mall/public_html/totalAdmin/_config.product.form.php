<?php include_once('wrap.header.php'); ?>


<form name="frm" method="post" action="_config.product.pro.php" >
	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 공통 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>정산 수수료 (판매 수수료)</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_account_commission" class="design number_style t_right" value="<?php echo $siteInfo['s_account_commission']; ?>" placeholder="0" style="width:70px">
							<span class="fr_tx">%</span>
						</div>
						<div class="tip_box">
							<?php echo _DescStr(''. ($SubAdminMode===true?'입점업체가 ':null) .'상품등록 시 입력한 수수료가 적용됩니다.'); ?>
							<?php echo _DescStr(($SubAdminMode===true?'입점업체가 ':null) .'상품을 등록하면 업체 정산형태는 수수료를 자동선택합니다.'); ?>
						</div>
					</td>
					<th>자동 배송완료 처리</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_delivery_auto" class="design number_style t_right" value="<?php echo $siteInfo['s_delivery_auto']; ?>" placeholder="0" style="width:70px">
							<span class="fr_tx">일</span>
						</div>
						<div class="tip_box">
							<?php echo _DescStr('배송중으로 변경된 날로 부터 위 설정된 기간이 지나면 자동으로 배송완료 상태로 변경됩니다.'); ?>
							<?php echo _DescStr('설정값이 0인 경우 자동 배송완료 처리가 적용되지 않으니 직접 수동으로 처리가 필요합니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>네이버/다음 EP연동</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>네이버 EP</th>
					<td>
						<?php echo _InputRadio('_naver_switch', array('Y', 'N'), ($siteInfo['s_naver_switch'] == 'Y'?'Y':'N'), '', array('전체 적용', '전체 미적용'), ''); ?>
						<div class="dash_line"><!-- 점선 --></div>
						<div class="tip_box">
							<?php echo _DescStr("전체상품 DB URL : <em>http://". $system['host'] ."/addons/ep/naver/allep.php</em>")?>
						</div>
					</td>
					<th>다음 EP</th>
					<td>
						<?php echo _InputRadio('_daum_switch', array('Y', 'N'), ($siteInfo['s_daum_switch'] == 'Y'?'Y':'N'), '', array('전체 적용', '전체 미적용'), ''); ?>
						<div class="dash_line"><!-- 점선 --></div>
						<div class="tip_box">
							<?php echo _DescStr('전체상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/allep.php</em>'); ?>
							<?php echo _DescStr('요약상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/briefep.php</em>'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>EP 참고사항</th>
					<td colspan="3">
						<div class="tip_box">
							<?php echo _DescStr('전체 상품을 적용하거나 상품마다 개별적으로 설정하고자 한다면 전체 적용을 선택해주세요.'); ?>
							<?php echo _DescStr("전체 미적용인 경우에는 상품마다 개별 설정을 해도 무조건 미적용됩니다.")?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>배송상품 자동 정산대기</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용 여부</th>
					<td>
						<?php echo _InputRadio('_product_auto_on', array('Y', 'N'), $siteInfo['s_product_auto_on'], '', array('사용', '미사용')); ?>
						<?php echo _DescStr('사용 : 결제 수단별로 적용한 기간이 지나면 자동으로 정산대기로 넘어갑니다.' , ''); ?>
						<?php echo _DescStr('미사용 : 모두 수동으로 정산대기 처리를 해주셔야 합니다.' , ''); ?>
					</td>
				</tr>
				<tr class="cls_product_auto">
					<th>결제수단별 설정</th>
					<td>
						<div class="lineup-form">
						<?php foreach($arr_settlement_paymethod_name as $k=>$v) { ?>
							<div class="one_box">
								<span class="fr_tx t_black"><?php echo $v; ?></span>
								<div class="lineup-row type_center">
									<input type="text" name="_product_auto_<?php echo $k; ?>" class="design number_style t_right" value="<?php echo $siteInfo['s_product_auto_'.$k]; ?>" placeholder="0" style="width:70px">
									<span class="fr_tx">일</span>
								</div>
							</div>
						<?php } ?>
						</div>
						<div class="dash_line"></div>
						<?php echo _DescStr('배송완료 후 설정된 기간이 지나면 자동으로 정산대기로 넘어갑니다.'); ?>
						<?php echo _DescStr('설정값 0을 입력시 주문 당일 바로 정산대기로 넘어갑니다. (미입력도 0과 동일합니다)'); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>티켓상품 자동 정산대기</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용 여부</th>
					<td>
						<?php echo _InputRadio('_ticket_auto_on', array('Y', 'N'), $siteInfo['s_ticket_auto_on'], '', array('사용', '미사용')); ?>
						<?php echo _DescStr('사용 : 결제 수단별로 적용한 기간이 지나면 자동으로 정산대기로 넘어갑니다.' , ''); ?>
						<?php echo _DescStr('미사용 : 모두 수동으로 정산대기 처리를 해주셔야 합니다.' , ''); ?>
					</td>
				</tr>
				<tr class="cls_ticket_auto">
					<th>결제수단별 설정</th>
					<td>
						<div class="lineup-form">
						<?php foreach($arr_settlement_paymethod_name as $k=>$v) { ?>
							<div class="one_box">
								<span class="fr_tx t_black"><?php echo $v; ?></span>
								<div class="lineup-row">
									<input type="text" name="_ticket_auto_<?php echo $k; ?>" class="design number_style t_right" value="<?php echo $siteInfo['s_ticket_auto_'.$k]; ?>" placeholder="0" style="width:70px"><span class="fr_tx">일</span>
								</div>
							</div>
						<?php } ?>
						</div>
						<div class="dash_line"></div>
						<?php echo _DescStr('티켓발급 이후 설정된 기간이 지나면 자동으로 정산대기로 넘어갑니다.'); ?>
						<?php echo _DescStr('설정값 0을 입력시 주문 당일 바로 정산대기로 넘어갑니다. (미입력도 0과 동일합니다)'); ?>
					</td>
				</tr>

			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>최근 본 상품 노출설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>유지 시간</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_today_view_time" class="design number_style t_right" value="<?php echo number_format($siteInfo['s_today_view_time']); ?>" placeholder="0" style="width:70px">
							<span class="fr_tx">시간</span>
						</div>
						<?php echo _DescStr("최근 본 상품이 노출되는 유지시간이며, 사용자 브라우저 캐시 설정에 따라 상이할 수 있습니다."); ?>
						<?php echo _DescStr("설정값이 0인 경우 24시간으로 적용됩니다."); ?>
					</td>
					<th>최대 기록 수량</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_today_view_max" class="design number_style t_right" value="<?php echo number_format($siteInfo['s_today_view_max']); ?>" placeholder="0" style="width:70px">
							<span class="fr_tx">개</span>
						</div>
						<?php echo _DescStr("최근 본 상품에 기록되는 상품의 개수이며, 최대 50개까지 설정할 수 있습니다."); ?>
						<?php echo _DescStr("설정값이 0인 경우 12개로 적용됩니다."); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>
</form>

<script>
	function switch_product_auto(){
		var pa_type = $("[name='_product_auto_on']:checked").val();

		if(pa_type=='Y'){
			$(".cls_product_auto").show();
		}else{
			$(".cls_product_auto").hide();
		}
	}
	$(document).ready(switch_product_auto);
	$(document).on("change" , "[name='_product_auto_on']", switch_product_auto );


	function switch_ticket_auto(){
		var ta_type = $("[name='_ticket_auto_on']:checked").val();

		if(ta_type=='Y'){
			$(".cls_ticket_auto").show();
		}else{
			$(".cls_ticket_auto").hide();
		}
	}
	$(document).ready(switch_ticket_auto);
	$(document).on("change" , "[name='_ticket_auto_on']", switch_ticket_auto );
</script>

<?php include_once('wrap.footer.php'); ?>