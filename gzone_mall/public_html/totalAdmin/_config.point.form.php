<?php
include_once('wrap.header.php');
?>
<form action="_config.point.pro.php" method="post">

	<div class="group_title"><strong>적립금 사용제한</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>사용가능 금액</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_pointusevalue" value="<?php echo $siteInfo['s_pointusevalue']; ?>" class="design number_style" style="width:100px" placeholder="0">
							<span class="fr_tx">원부터</span>
						</div>
						<?php echo _DescStr('위 설정된 금액이상 누적된 경우 현금처럼 사용가능합니다.'); ?>
					</td>
					<th>사용제한 금액</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx">한 주문당</span>
							<input type="text" name="_pointuselimit" value="<?php echo $siteInfo['s_pointuselimit']; ?>" class="design number_style" style="width:100px" placeholder="0">
							<span class="fr_tx">원까지</span>
						</div>
						<?php echo _DescStr('설정값 0을 입력한 경우 적립금 사용에 제한이 없습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>적립금 지급설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>회원가입 시</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_joinpoint" value="<?php echo $siteInfo['s_joinpoint']; ?>" class="design number_style" style="width:100px" placeholder="0">
							<span class="fr_tx">원을</span>
							<input type="text" name="_joinpointprodate" value="<?php echo $siteInfo['s_joinpointprodate']; ?>" class="design number_style" style="width:70px" placeholder="0">
							<span class="fr_tx">일 후 적립</span>
						</div>
						<?php echo _DescStr('설정값 0을 입력한 경우 가입 후 즉시 지급됩니다.'); ?>
					</td>
					<th>상품구매 시</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx">상품별 적립금을</span>
							<input type="text" name="_orderpointprodate" value="<?php echo $siteInfo['s_orderpointprodate']; ?>" class="design number_style" style="width:70px" placeholder="0">
							<span class="fr_tx">일 후 적립</span>
						</div>
						<?php echo _DescStr('설정값 0을 입력한 경우 결제확인 후 즉시 지급됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>포토후기 등록 시</th>
					<td colspan="3">
						<div class="lineup-row">
							<input type="text" name="_productevalpoint" value="<?php echo $siteInfo['s_productevalpoint']; ?>" class="design number_style" style="width:100px" placeholder="0">
							<span class="fr_tx">원을</span>
							<input type="text" name="_productevalprodate" value="<?php echo $siteInfo['s_productevalprodate']; ?>" class="design number_style" style="width:70px" placeholder="0">
							<span class="fr_tx">일 후 적립</span>
						</div>
						<?php echo _DescStr('설정값 0을 입력한 경우 포토후기작성 후 즉시 지급됩니다.'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php
							echo _InputRadio('_producteval_limit', array('N', 'Y', 'B'), ($siteInfo['s_producteval_limit']?$siteInfo['s_producteval_limit']:'N'), '', array('제한없음 (한번만 지급)', '구매자만 작성 (한번만 지급)', '구매자만 작성 (구매한 만큼 지급)'));
						?>
						<?php echo _DescStr('포인트 지급 조건은 기본적으로 모두 이미지(사진)까지 등록되었을때만 지급되며, 사진없이 작성되면 적립금은 지급되지 않습니다.'); ?>
						<?php echo _DescStr('포토후기 적립금을 지급받고 후기를 삭제하면 적립금은 회수되며, 이미 적립금을 사용해서 회수금액보다 남은 적립금이 적은 경우 0원이 됩니다.'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>

						<div class="tip_table">
							<ul class="thead">
								<li class="th">구분</li>
								<li class="th">제한없음 (한번만 지급)</li>
								<li class="th">구매자만 작성 (한번만 지급)</li>
								<li class="th">구매자만 작성 (구매한 만큼 지급)</li>
							</ul>
							<ul>
								<li class="th">작성 권한</li>
								<li>제한없음(회원)</li>
								<li>구매자만 작성가능</li>
								<li>구매자만 작성가능</li>
							</ul>
							<ul>
								<li class="th">작성 횟수</li>
								<li>제한없음</li>
								<li>제한없음</li>
								<li>구매한 횟수만큼</li>
							</ul>
							<ul>
								<li class="th">구매여부</li>
								<li>상관없음</li>
								<li>구매자만 작성가능</li>
								<li>구매한 횟수만큼</li>
							</ul>
							<ul>
								<li class="th">지급여부</li>
								<li>최초 1회</li>
								<li>최초 1회</li>
								<li>구매한 횟수만큼</li>
							</ul>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>
<?php include_once('wrap.footer.php'); ?>