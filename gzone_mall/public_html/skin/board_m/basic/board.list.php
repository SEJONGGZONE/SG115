<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>


<div class="c_board_ctrl">
	<div class="area_box area_left">
		<div class="board_tit">
			<div class="name"><?php echo $boardInfo['bi_name']; ?></div>
			<div class="total">Total <?php echo number_format($TotalCount); ?></div>
		</div>
	</div><!-- end area_left -->

	<form name="boardSearch" role="search" action="" class="area_box area_right">
		<input type="hidden" name="pn" value="board.list">
		<input type="hidden" name="_menu" value="<?php echo $_menu ?>">
		<input type="hidden" name="searchMode" value="Y">
		<div class="board_search">
			<?php if( $searchMode == 'Y') { // 검색한 후 노출 / 검색 전 숨김 ?>
				<a href="/?pn=board.list&_menu=<?php echo $_menu; ?>" class="btn_reset" title="초기화"></a>
			<?php } ?>
			<input type="search" name="searchWord" value="<?php echo $searchWord; ?>" class="input_design" autocomplete="off" placeholder="검색어 입력">
			<input type="submit" name="" value="" class="btn_search" title="검색">
		</div>
		<?php if( $boardAuthChk['write'] === true) { // 글쓰기 ?>
			<a href="/?pn=board.form&_mode=add&_menu=<?php echo $_menu; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h40 black line btn_write type_write"><strong>글쓰기</strong></a>
		<?php } ?>
	</form><!-- end area_right -->

	<?php if($boardInfo['bi_category_use']=='Y'&&$boardInfo['bi_category']){ ?>
		<div class="board_ctg">
			<div class="swipe_box" ID="js_swipe_board_ctg">
				<ul>
					<li class="<?php echo $b_category==''?'hit':'';?>"><a href="/?pn=board.list&_menu=<?php echo $_menu;?>&searchWord=<?php echo $searchWord ?>&searchMode=<?php echo $searchMode ?>" class="ctg">전체</a></li>
					<?php foreach($_categoryload as $ck=>$cv){?>
						<li class="<?php echo $b_category!='' && $cv==$b_category?'hit':'';?>"><a href="/?pn=board.list&_menu=<?php echo $_menu;?>&searchMode=<?php echo $searchMode ?>&searchWord=<?php echo $searchWord ?>&b_category=<?php echo $cv ?>" class="ctg"><?php echo $cv;?></a></li>
					<?php }?>
				</ul>
			</div>
			<script>
				var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_board_ctg'), testScroll = '';

				// 스와이프 적용
				$(document).ready(function(){
					swipe_Menu2();
				});

				function swipe_Menu2(){
					$.each($('#js_swipe_board_ctg li'), function(k, v){  scrollWidth += $('#js_swipe_board_ctg li').eq(k).outerWidth()*1; });
					var len = $('#js_swipe_board_ctg li').length;
					swipe_Scroll2 = new IScroll('#js_swipe_board_ctg', {
						'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
						, mouseWheel: true
					});

					if(scrollIndex > 0 && $('#js_swipe_board_ctg li.hit').length > 0) {
						var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_board_ctg li.hit').outerWidth()*1/2 ) * -1;
						swipe_Scroll2.scrollToElement(document.querySelector('#js_swipe_board_ctg li.hit'), 500, scrollOffset);
					}
				}
			</script>
		</div><!-- end board_ctg -->
	<?php } ?>
</div><!-- end c_board_ctrl -->



<div class="c_board_list">
	<script>
		// -- 게시물 검색 :: 공통
		$(document).on('submit','form[name="boardSearch"]',function(){
			var sw = $(this).find('[name="searchWord"]').val();
			if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="searchWord"]').focus(); return false; }
			return true;
		});
	</script>
	<?php if( count($listPost) < 1) { // 내용 없을때 ?>
		<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
		<?php }else {  ?>

		<?php foreach($listPost as $k=>$v) {  ?>
			<ul class="<?php echo $v['noticeClass'];?>">
				<li class="this_tit">
					<div class="number"><?php echo $v['num']; ?></div>
					<div class="posting">
						<a href="<?php echo $v['postUrl']; ?>" class="upper_link<?php echo $v['secretEvtClass'];?>" data-uid="<?php echo $v['uid'] ?>" data-mode="view" title=""></a>
						<div class="tit">
							<?php if($boardInfo['bi_category_use'] == 'Y' && $boardInfo['bi_category'] != "" && $v['category']){ ?>
								<span class="field">[<?php echo $v['category'];?>]</span>
							<?php } ?>
							<span class="tx"><?php echo $v['title']; ?></span>
						</div>
						<span class="state_icon">
							<?php if($v['iconSecret'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_secret.svg" alt="비밀글"> <?php } ?>
							<?php if($v['iconPhoto'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_photo.svg" alt="사진첨부"> <?php } ?>
							<?php if($v['iconFile'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_file.svg" alt="첨부파일"> <?php } ?>
							<?php  if($v['iconReply'] === true) { ?>
								<span class="ic_reply"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_reply.svg" alt="댓글"><em><?php echo $v['talkCnt']; ?></em></span>
							<?php } ?>
							<?php if($v['iconNew'] === true) {  ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_new.svg" alt="새글"> <?php } ?>
						</span>
					</div>
				</li>
				<li class="this_info">
					<div class="writer"><?php echo $v['writer']; ?></div>
					<div class="date"><?php echo $v['rdate']; ?></div>
				</li>
			</ul>
		<?php } ?>

	<?php } ?>

</div><!-- end c_board_list -->
