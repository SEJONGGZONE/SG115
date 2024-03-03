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
                <?php if( $boardViewData['optionDateUse'] === true) { // 태그표기 / 사용하지 않을 경우에는 숨김 ?>
                    <?php if( $boardViewData['eventing'] === true) { ?>
                        <span class="c_tag black line">진행중</span>
                    <?php }else{ ?>
                        <span class="c_tag light line">이벤트 종료</span>
                    <?php }?>
                    <div class="txt">이벤트 기간 : <?php echo $boardViewData['eventSdate']?> ~ <?php echo $boardViewData['eventEdate'] ?></div>
                <?php } ?>
            </div>
        </div><!-- end view_top -->

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

		<?php if( $boardViewData['replyMode'] === true && $boardViewData['replyChk']==true) { // 관리자 답변 ?>
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


	</div><!-- end c_board_view -->
