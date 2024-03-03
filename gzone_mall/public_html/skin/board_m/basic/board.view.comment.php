<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<div class="reply">

	<form name="boardComment" onsubmit="return false;" class="form">
		<input type="hidden" name="_boardSkin" value="<?php echo $boardInfo['_board_skin']; // 스킨명 ?>">
		<input type="hidden" name="_buid" value="<?php echo $_buid; // 스킨명 ?>">
		<input type="hidden" name="_menu" value="<?php echo $_menu; // 스킨고유아이디 ?>">
		<input type="hidden" name="_auth" value="<?php echo $commentData['writeAuthType']; // 스킨고유아이디 ?>">
		<textarea cols="" rows="" name="_content" maxlength="<?php echo $varCommentWriteLen; ?>" class="text_design" placeholder="<?php echo $commentData['placeholder']; ?>" <?php echo $commentData['writeAttr'] ;?>></textarea>
		<input type="submit" name="" class="btn_ok submit_comment" value="댓글등록" />
		<?php if($commentData['recaptchaUse'] === true) { ?>
		<div class="spam_form ">
			<script src='https://www.google.com/recaptcha/api.js'></script>
			<div class="g-recaptcha"  data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
			<div class="tip_txt black">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
		</div>
		<?php } ?>
	</form><!-- end form -->

	<?php if( count($listComment) > 0) { // 댓글리스트 / 댓글 없을땐 div 숨김 ?>
	<div class="list">
		<ul>
		<?php foreach($listComment as $k=>$v) {?>
			<li>
				<span class="name"><?php echo $v['writer']; ?></span>
				<span class="date">(<?php echo $v['rdate']?>)</span>
                <?php // 댓글삭제버튼/ 내글일때만 노출 ?>
				<?php if( $v['inid'] == get_userid() ){?>
					<a href="#none" onclick="return false;" class="btn_delete delete-comment" data-uid="<?php echo $v['uid']  ?>" title="댓글삭제"></a>
				<?php }?>
				<div class="conts"><?php echo $v['content'];?></div>
			</li>
		<?php } ?>
		</ul>
	</div><!-- end list -->
	<?php } ?>

</div><!-- end reply -->