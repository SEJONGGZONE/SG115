<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
	<div class="c_board_view">

        <div class="view_top">
            <div class="tit">
                <?php if( $boardViewData['categoryUse'] === true && $boardViewData['category'] && $boardInfo['bi_category'] != ""){?>
                    [<?php echo $boardViewData['category']; ?>]
                <?php }?>
                <?php echo $boardViewData['title']; ?>
            </div>
            <div class="info">
                <?php if( $boardViewData['writerView'] === true) {   ?>
                    <span class="txt"><?php echo $boardViewData['writer'];?></span>
                <?php  }?>
                <span class="txt">(<?php echo $boardViewData['rdate'] ?>)</span>
                <span class="txt">조회 <?php echo $boardViewData['hit']?></span>
				<?php if( $boardViewData['optionDateUse'] === true) { ?>
					<?php if( $boardViewData['eventing'] === true) { ?>
					<?php }else{ ?>
					<?php }?>
					<div class="txt">이벤트 기간 : <?php echo $boardViewData['eventSdate']?> ~ <?php echo $boardViewData['eventEdate'] ?></div>
				<?php } ?>
            </div>
        </div>

        <div class="view_conts editor">
            <?php
                // -- 본문에 노출될 이미지가 있다면 노출
                if( $boardViewData['viewImagesUrl'] !== ''){ echo '<img src="'.$boardViewData['viewImagesUrl'].'" alt="" />'; }
                echo $boardViewData['content'];
            ?>
		</div><!-- end view_conts -->

        <?php if( $boardViewData['fileUploadUse'] === true && $boardViewData['filesLink'] != ''){ // 첨부파일 ?>
            <div class="file_down">
                <?php echo $boardViewData['filesLink']; ?>
            </div><!-- end file_down -->
        <?php } ?>

		<?php if( $boardViewData['replyMode'] === true) { // 관리자 답변 ?>
            <div class="answer">
                <div class="edge"></div>
                <div class="answer_in">
                    <div class="writer"><?php echo $boardViewData['replyTitle']; ?></div>
                    <div class="editor"><?php echo $boardViewData['replyContent']; ?></div>
                </div>
            </div><!-- end answer -->
		<?php } ?>

		<?php
			// 게시글 댓글을 사용한다면
			if( $boardViewData['commentUse'] === true ){
				echo '<div class="comment-reply-box">';
				include_once OD_PROGRAM_ROOT.'/board.view.comment.php';
				echo '</div>';
			}
		?>

		<?php if( $boardViewData['prevIs'] === true || $boardViewData['nextIs'] === true){ // 이전글,다음글 ?>
            <div class="nextprev">
                <ul>
                    <li class="prev">
                        <div class="tx">이전글</div>
                        <?php if( $boardViewData['prevIs'] === true){ ?>
                            <div class="tit">
                                <a href="<?php echo $boardViewData['prevLink']; ?>" class="link<?php echo $boardViewData['prevSecretEvtClass'] ?>" data-mode="view" data-uid="<?php echo $boardViewData['prevUid']; ?>"><?php  echo $boardViewData['prevTitle']; ?>
                                    <?php if($boardViewData['prevSecretMobileIcon'] === true) {?>
                                        <span class="icon"><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_secret.png" alt="비밀글"></span>
                                    <?php } ?>
                                </a>
                            </div>
                            <span class="date"><?php echo $boardViewData['prevRdate']; ?></span>
                        <?php }else{ // 이전글 없을때 `이전글이 없습니다` 문구 노출 ?>
                            <div class="tit"><?php echo $boardViewData['prevTitle'];?></div>
                        <?php } ?>
                    </li>
                    <li class="next">
                        <div class="tx">다음글</div>
                        <?php if( $boardViewData['nextIs'] === true){ ?>
                            <div class="tit">
                                <a href="<?php echo $boardViewData['nextLink']; ?>" class="link<?php echo $boardViewData['nextSecretEvtClass'] ?>" data-mode="view" data-uid="<?php echo $boardViewData['nextUid']; ?>"><?php  echo $boardViewData['nextTitle']; ?>
                                    <?php if($boardViewData['nextSecretMobileIcon'] === true) {?>
                                        <span class="icon"><img src="<?php echo $SkinData['skin_url'] ?>/images/c_img/board_secret.png" alt="비밀글"></span>
                                    <?php } ?>
                                </a>
                            </div>
                            <span class="date"><?php echo $boardViewData['nextRdate']; ?></span>
                        <?php }else{ // 다음글 없을때 `다음글이 없습니다` 문구 노출 ?>
                            <div class="tit"><?php echo $boardViewData['nextTitle'];?></div>
                        <?php } ?>
                    </li>
                </ul>
            </div><!-- end nextprev -->
		<?php } ?>

	</div><!-- end c_board_view -->
