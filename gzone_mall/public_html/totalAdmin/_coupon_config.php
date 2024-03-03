<?php
include_once('wrap.header.php');
/*
	s_coupon_use : 쿠폰사용여부 (Y사용,N)
	s_coupon_view : 쿠폰노출 설정 (all:전체,member:회원) -- 미사용
	s_coupon_ordercancel_return :  주문취소에 따른 복원 사용여부 (Y:사용,N:미사용)
*/
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>

<form action="_coupon_config.pro.php" method="post" name="frm">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="">주문시 사용여부</th>
					<td>
						<?php echo _InputRadio('s_coupon_use', array('Y','N'), ($r['s_coupon_use']?$r['s_coupon_use']:'N'), '', array('사용','미사용')); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr("<em>사용</em> : 쿠폰 조건에 따라 회원은 주문 시 쿠폰을 사용할 수 있습니다."); ?>
							<?php echo _DescStr('<em>미사용</em> : 쿠폰 조건에 상관없이 주문 시 쿠폰을 사용할 수 없습니다.(쿠폰선택 영역 미노출)'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="">주문취소 시 복원여부</th>
					<td>
						<?php echo _InputRadio('s_coupon_ordercancel_return', array('Y', 'N'), ($r['s_coupon_ordercancel_return']?$r['s_coupon_ordercancel_return']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('<em>사용</em> : 주문취소 시 사용한 쿠폰을 다시 복원합니다.'); ?>
							<?php echo _DescStr('<em>미사용</em> : 한번 사용한 쿠폰은 주문취소 시에도 복원하지 않습니다.'); ?>
							<?php echo _DescStr('결제완료 이전에 주문취소 시 설정과 상관없이 쿠폰은 복원됩니다.'); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


	<div class="c_btnbox type_full">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
		</ul>
	</div>

</form>

<?php include_once('wrap.footer.php'); ?>