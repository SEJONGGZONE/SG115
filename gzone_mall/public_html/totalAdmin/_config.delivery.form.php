<?php include_once('wrap.header.php'); ?>


<form name="frm" method="post" action="_config.delivery.pro.php" ENCTYPE="multipart/form-data">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>배송비 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>기본 배송비</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_delprice" class="design number_style t_right" value="<?php echo $siteInfo['s_delprice']; ?>" placeholder="0" style="width:100px">
							<span class="fr_tx">원</span>
						</div>
						<?php echo _DescStr('설정값이 0인 경우 무료배송이 적용됩니다.'); ?>
					</td>
					<th>무료배송 적용금액</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_delprice_free" class="design number_style t_right" value="<?php echo $siteInfo['s_delprice_free']; ?>" placeholder="0" style="width:100px">
							<span class="fr_tx">원 이상</span>
						</div>
						<?php echo _DescStr('설정값이 0인 경우 무조건 배송비가 적용됩니다. (기본배송비도 0인 경우 무료배송이 적용됩니다.)'); ?>
					</td>
				</tr>
				<tr>
					<th>도서산간 추가배송비</th>
					<td colspan="3">
						<?php echo _InputRadio('_del_addprice_use', array('Y', 'N'), $siteInfo['s_del_addprice_use']? $siteInfo['s_del_addprice_use']:'N', ' class="del_addprice_use"', array('사용','미사용'), ''); ?>

						<div class="lineup-list type_left del_addprice_detail" style="<?php echo ($siteInfo['s_del_addprice_use']=='N'?'display:none;':null); ?>">
							<div class="dash_line"></div>
							<?php echo _DescStr('상품설정 시 선택 가능한 배송비 정책은 아래와 같으며, 각 정책별로 추가배송비 적용여부를 선택해주세요.','blue'); ?>
							<dl>
								<dt style="width:160px;"><span class="c_tag green line">입점업체 배송비 정책 적용 상품</span></dt>
								<dd>
									<?php echo _InputRadio('_del_addprice_use_normal', array('Y', 'N'), $siteInfo['s_del_addprice_use_normal']!=''?$siteInfo['s_del_addprice_use_normal']:'Y', '', array('적용', '미적용')); ?>
								</dd>
							</dl>
							<?php echo _DescStr('무료배송 적용금액 미만일때는 무조건 적용되고, 무료배송 적용금액이 넘어 무료배송이 되면 위 설정값이 적용됩니다.'); ?>
							<div class="dash_line"></div>
							<dl>
								<dt style="width:160px;"><span class="c_tag green line">상품별 배송비 적용 상품</span></dt>
								<dd>
									<?php echo _InputRadio('_del_addprice_use_product', array('Y', 'N'), $siteInfo['s_del_addprice_use_product']!=''?$siteInfo['s_del_addprice_use_product']:'Y', '', array('적용', '미적용')); ?>
								</dd>
							</dl>
							<?php echo _DescStr('무료배송 적용금액 미만일때는 무조건 적용되고, 무료배송 적용금액이 넘어 무료배송이 되면 위 설정값이 적용됩니다.'); ?>
							<div class="dash_line"></div>
							<dl>
								<dt style="width:160px;"><span class="c_tag green line">개별 배송비 적용 상품</span></dt>
								<dd>
									<?php echo _InputRadio('_del_addprice_use_unit', array('Y', 'N'),  $siteInfo['s_del_addprice_use_unit']!=''?$siteInfo['s_del_addprice_use_unit']:'Y', '', array('적용', '미적용')); ?>
								</dd>
							</dl>
							<div class="dash_line"></div>
							<dl>
								<dt style="width:160px;"><span class="c_tag green line">무료 배송 적용 상품</span></dt>
								<dd><?php echo _InputRadio('_del_addprice_use_free', array('Y', 'N'), $siteInfo['s_del_addprice_use_free']!=''?$siteInfo['s_del_addprice_use_free']:'Y', '', array('적용', '미적용')); ?></dd>
							</dl>
						</div>

					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>기본 배송정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>지정 택배사</th>
					<td>
						<?php echo _InputSelect( '_del_company' , array_keys($arr_delivery_company), $siteInfo['s_del_company'] , '' , '' , ''); ?>
					</td>
					<th>평균 배송기간</th>
					<td>
						<input type="text" name="_del_date" class="design" value="<?php echo $siteInfo['s_del_date'];?>" placeholder="평균 배송기간" style="width:100px">
					</td>
				</tr>
				<tr>
					<th>반송주소</th>
					<td colspan="3">
						<input type="text" name="_del_return_addr" class="design" value="<?php echo $siteInfo['s_del_return_addr']; ?>" placeholder="반송주소" style="width:250px">
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('별도로 설정하지 않을 경우 위 정보가 상품상세페이지 배송안내에 노출됩니다.' , ''); ?>
						<?php echo _DescStr('상품등록 시 상품별로 배송정책을 따로 입력하거나 '.($SubAdminMode===true?'입점업체가 ':null).'직접 설정할 수 있습니다.' , ''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	// 추가배송비 사용여부에따른 노출 설정
	$(document).on('click', '.del_addprice_use', function() {
		var Value = $(this).val();
		if(Value == 'Y') $('.del_addprice_detail').show();
		else $('.del_addprice_detail').hide();
	});
</script>

<?php include_once('wrap.footer.php'); ?>