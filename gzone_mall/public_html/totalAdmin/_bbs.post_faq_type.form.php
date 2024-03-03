<?php
	// 메뉴 고정
	include_once('wrap.header.php');

	/*
		$siteInfo['s_bbs_faq_type'] :: serialize 화 되어있음
		array['type']
	*/

	// -- 금지어를 가져 온다 :: serialize
	if( $siteInfo['s_bbs_faq_type'] != ''){
		$arrFaq = unserialize(stripslashes($siteInfo['s_bbs_faq_type']));
		$faqType = $arrFaq['type'] ;
	}
?>
<form id="frmBbsFaqType" name="frmBbsFaqType" method="post" action="_bbs.post_faq_type.pro.php">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>

					<tr>
						<th>FAQ 분류</th>
						<td>
							<input type="text" name="faqType" class="design js_tag item-type" value="<?php echo $faqType; ?>" style="width:100%;">
							<div class="tip_box">
								<?php echo _DescStr('FAQ 게시글 작성시 내용에 적용될 FAQ 분류를 입력해 주세요.'); ?>
								<?php echo _DescStr('작성된 글이 있는 경우에 분류를 삭제하면 해당 글을 다시 수정해주어야 합니다.'); ?>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="return false;" class="c_btn h27 black line delete-item" data-idx="type">전체삭제</a>
						</td>
					</tr>
					<tr>
						<th class="ess">FAQ 인기키워드</th>
						<td>
							<input type="text" name="s_faq_keyword" class="design js_tag item-keyword" value="<?php echo $siteInfo['s_faq_keyword']; ?>" style="width:100%">
							<div class="tip_box">
								<?php echo _DescStr('고객센터 메인 FAQ에 노출될 검색 키워드입니다.'); ?>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="return false;" class="c_btn h27 black line delete-item" data-idx="keyword">전체삭제</a>
						</td>
					</tr>
					<tr>
						<th>참고사항</th>
						<td>
							<div class="tip_box">
								<?php echo _DescStr('추가할 단어를 입력 후 Enter 혹은 Tab을 클릭하면 자동으로 추가되고 꼭 확인버튼을 눌러주셔야 최종 저장됩니다.'); ?>
								<?php echo _DescStr('단어를 다시 클릭하면 수정가능하고, 단어 옆에 x 버튼을 클릭하면 삭제됩니다.'); ?>
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
