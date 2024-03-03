<?php
	// 메뉴 고정
	include_once('wrap.header.php');


	/*
		$siteInfo['s_bbs_forbidden_word'] :: serialize 화 되어있음
		array['writer']
		array['title']
		array['content']
	*/

	// -- 금지어를 가져 온다 :: serialize
	if( $siteInfo['s_bbs_forbidden_word'] != ''){
		$arrFw = unserialize(stripslashes($siteInfo['s_bbs_forbidden_word']));
		$fwWriter = 	$arrFw['writer'] ;
		$fwTitle = 		$arrFw['title'] ;
		$fwContent = 	$arrFw['content'] ;
	}
?>
<form id="frmBbsFw" name="frmBbsFw" method="post" action="_bbs.forbidden_word.pro.php">


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>제목 금지어</th>
						<td>
							<input type="text" name="fwTitle" class="design js_tag item-title" value="<?php echo $fwTitle; ?>" style="width:100%;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="return false;" class="c_btn h27 black line delete-item" data-idx="title">전체삭제</a>
						</td>
					</tr>
					<tr>
						<th>내용 금지어</th>
						<td>
							<input type="text" name="fwContent" class="design js_tag item-content" value="<?php echo $fwContent; ?>" style="width:100%;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="return false;" class="c_btn h27 black line delete-item" data-idx="content">전체삭제</a>

						</td>
					</tr>
					<tr>
						<th>작성자명 금지어</th>
						<td>
							<input type="text" name="fwWriter" class="design js_tag item-writer" value="<?php echo $fwWriter; ?>" style="width:100%;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="return false;" class="c_btn h27 black line delete-item" data-idx="writer">전체삭제</a>
						</td>
					</tr>
					<tr>
						<th>참고사항</th>
						<td>
							<div class="tip_box">
								<?php echo _DescStr('금지할 단어를 입력 후 Enter 혹은 Tab을 클릭하면 자동으로 추가되고 꼭 확인버튼을 눌러주셔야 최종 저장됩니다.'); ?>
								<?php echo _DescStr('단어를 다시 클릭하면 수정가능하고, 단어 옆에 x 버튼을 클릭하면 삭제됩니다.'); ?>
								<?php echo _DescStr('금지어는 사용자(회원,비회원)에게만 적용되고, 관리자는 자유롭게 작성가능합니다.'); ?>
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


<script>
	$(document).on('click','.delete-item',function(){
		var idx = $(this).attr('data-idx');
		$('.item-'+idx).tagEditor('destroy').val('').tagEditor();
	});
</script>


<?php include_once('wrap.footer.php'); ?>
