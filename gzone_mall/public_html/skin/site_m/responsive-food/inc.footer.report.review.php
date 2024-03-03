<?php 
    $eval_id = LastCut2($row['pt_inid'], 3);
?>

<div class="wrapping">
	<div class="tit_box">
		<div class="tit">신고하기</div>
		<a href="#none" onclick="return false;" class="btn_close js_btn_report"  title="닫기"></a>
	</div><!-- end tit_box -->

	<div class="conts_box c_scroll_v">
		<form class="report" id="formReviewReport">
            <input type="hidden" name="_mode" value="set_report">
            <input type="hidden" name="_type" value="<?php echo $_type ?>">
            <input type="hidden" name="_uid" value="<?php echo $_uid ?>">

			<div class="top_tit">
				<?php // 아이디 노출(뒤에 3자리 별표) ?>
				<strong><?php echo $eval_id; ?>님</strong>의<br/>
				리뷰를 신고하는 이유를 선택해주세요.
			</div>
			<?php // 신고이유 다중선택 불가능(radio) ?>
			<ul>
                <?php 
                    foreach ($arr_reposrt_reason['review'] as $k => $v) {
                        echo '<li><label class="label_d"><input type="radio" name="_reason" value="'.$k.'"><span class="tx">'.$v.'</span></label></li>';
                    }
                ?>
				<?php /*
				<li>
					<label class="label_d">
						<input type="radio" name="" value="">
						<span class="tx">상품과 관련없는 내용</span>
					</label>
				</li>
				<li>
					<label class="label_d">
						<input type="radio" name="" value="">
						<span class="tx">개인정보 누출 위험</span>
					</label>
				</li>
				<li>
					<label class="label_d">
						<input type="radio" name="" value="">
						<span class="tx">저작권 불법 도용(타인이 작성한 글, 사진 등)</span>
					</label>
				</li>
				<li>
					<label class="label_d">
						<input type="radio" name="" value="">
						<span class="tx">기타</span>
					</label>
				</li>
				*/?>
				<?php // textarea는 기타를 체크했을때만 노출(노출 안될땐 li까지 통째로 비노출) ?>
				<li style="display:none;">
					<textarea class="text_design" rows="4" name="_reason_etc" placeholder="자세한 내용을 입력해주세요."></textarea>
				</li>
			</ul>
		</form>
	</div><!-- end conts_box -->

	<div class="c_btnbox">
		<ul>
			<li>
				<?php // 신고 이유 선택안했을때 경고창 : 신고 이유를 선택해주세요. ?>
				<a href="#none" class="c_btn h40 black js_submit_ok" onclick="return false;">신고 접수하기</a>
			</li>
			<li>
				<?php
				/*
					차단하기 누르면 경고창 띄우기

					선택한 사용자의 모든 리뷰를 차단할까요?
					확인 후 취소는 불가합니다.
					[확인][취소]
				*/
				?>
				<a href="#none" class="c_btn h40 black line js_btn_review_block_user" data-uid="<?php echo $_uid; ?>" onclick="return false;">이 사용자 차단하기</a>
			</li>
		</ul>
	</div><!-- end c_btnbox -->
</div>
<div onclick="return false;" class="bg_close js_btn_report" ></div>