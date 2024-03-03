<?php include_once('wrap.header.php');?>

<form action="_config.display.review.pro.php" method="post" target="common_frame">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio('s_main_review', array('Y', 'N'), (isset($siteInfo['s_main_review'])?$siteInfo['s_main_review']:'A'), ' class="js_main_review"', array('노출', '숨김'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('사용자 메인페이지에 베스트리뷰 노출 여부를 설정할 수 있습니다.'); ?>
							<?php echo _DescStr('설정한 스킨 디자인에 따라, 또는 아래 노출 조건에 맞지 않는 경우 노출되지 않을 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
				<tr class="js_detail_setting">
					<th>노출조건</th>
					<td>
                        <div class="lineup-row">
							<div class="fr_tx" style="width:70px">정렬 기준</div>
                            <?php echo _InputRadio('s_main_review_porder', array('S','R'), $siteInfo['s_main_review_porder'], '', array('최신 등록순', '랜덤')); ?>
                        </div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
                            <div class="fr_tx" style="width:70px">최소 평점</div>
                            <?php echo _InputRadio('s_main_review_score', array(5, 4, 3, 2, 1), $siteInfo['s_main_review_score'], '', array(5, 4, 3, 2, 1)); ?>
                        </div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row">
                            <div class="fr_tx" style="width:70px">포토등록</div>
                            <?php echo _InputRadio('s_main_review_view', array('A', 'P'), $siteInfo['s_main_review_view'], '', array('모든 리뷰', '포토 리뷰만')); ?>
                        </div>
						<div class="dash_line"><!-- 점선라인 --></div>
						 <div class="lineup-row">
						 	 <div class="fr_tx" style="width:70px">최대노출</div>
                            <input type="text" class="design" style="width:70px; text-align:right;" placeholder="0" name="s_main_review_limit" value="<?php echo ($siteInfo['s_main_review_limit']?$siteInfo['s_main_review_limit']:0) ?>">
                            <div class="fr_tx">개</div>
                        </div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	function manual_setting() {
		var _mode = $('.js_main_review:checked').val();
		if(_mode == 'Y') $('.js_detail_setting').show();
		else $('.js_detail_setting').hide();
	}
	$(document).ready(manual_setting);
	$(document).on('click', '.js_main_review', function() { manual_setting(); });
</script>
<?php include_once('wrap.footer.php');?>