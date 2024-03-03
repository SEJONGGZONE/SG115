<?php
include_once('wrap.header.php');

# 출력 데이터 설정
$DiskInfo = CheckDirSize('/'); // 디스크 용량과 파일 수를 구함
?>

<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th>솔루션 타입</th>
				<td class="t_red">원데이넷 하이센스 PLUS</td>
				<th>라이선스</th>
				<td class="t_blue"><?php echo $siteInfo['s_license']; ?></td>
			</tr>
			<tr>
				<th>디스크 사용량</th>
				<td><?php echo SizeText($DiskInfo['DiskSize']); ?></td>
				<th>디스크 파일수</th>
				<td><?php echo number_format($DiskInfo['DiskFile']); ?>개</td>
			</tr>
			<tr>
				<th>상품 수</th>
				<td colspan="3">
					<div class="tip_table">
						<ul class="thead">
							<li class="th">전체</li>
							<li class="th">노출</li>
							<li class="th">숨김</li>
						</ul>
						<ul>
							<li><?php echo number_format(DivisionProduct('all')); ?>개</li>
							<li><?php echo number_format(DivisionProduct('view')); ?>개</li>
							<li><?php echo number_format(DivisionProduct('hide')); ?>개</li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th>회원 수</th>
				<td colspan="3">
					<div class="tip_table">
						<ul class="thead">
							<li class="th">전체</li>
							<li class="th">정상</li>
							<li class="th">휴면</li>
							<li class="th">탈퇴</li>
						</ul>
						<ul>
							<li><?php echo number_format(DivisionMember('all')); ?>명</li>
							<li><?php echo number_format(DivisionMember('use')); ?>명</li>
							<li><?php echo number_format(DivisionMember('sleep')); ?>명</li>
							<li><?php echo number_format(DivisionMember('leave')); ?>명</li>
						</ul>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php include_once('wrap.footer.php'); ?>