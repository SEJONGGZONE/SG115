<?php include_once('wrap.header.php'); ?>
<form action="_config.device.pro.php" method="post" enctype="multipart/form-data">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>PC 파비콘</th>
					<td>
						<div class="lineup-row">
							<?php echo _PhotoForm('../upfiles/banner', 's_favicon', $siteInfo['s_favicon'], 'style="width:250px"'); ?>
						</div>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel), png" , 'black'); ?>
							<?php echo _DescStr("PC브라우저 제목 앞에 노출되는 아이콘입니다."); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>Mobile 홈 아이콘</th>
					<td>
						<div class="lineup-row">
							<?php echo _PhotoForm('../upfiles/banner', 's_home_icon', $siteInfo['s_home_icon'], 'style="width:250px"'); ?>
						</div>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel), png" , 'black'); ?>
							<?php echo _DescStr("모바일 브라우저 홈아이콘 또는 바로가기를 만들때 노출되는 아이콘입니다."); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>URL 공유이미지</th>
					<td>
						<div class="lineup-row">
							<?php echo _PhotoForm('../upfiles/banner', 's_share_favicon', $siteInfo['s_share_favicon'], 'style="width:250px"'); ?>
						</div>
						<div class="tip_box">
							<?php echo _DescStr("권장사이즈 : 300 x 300 (pixel), png" , 'black'); ?>
							<?php echo _DescStr("채팅프로그램 또는 블로그 등에 URL등록 시 대표이미지로 노출되며, 해당 플랫폼 환경에 따라 노출되지않을 수 있습니다."); ?>
						</div>
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