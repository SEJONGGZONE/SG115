<?php
$page_title = "자주 묻는 질문";
include_once($SkinData['skin_root'].'/service.header.php'); // 상단 헤더 출력

$arrFaq = unserialize(stripslashes($siteInfo['s_bbs_faq_type']));
$faqType = explode(',',$arrFaq['type']);

// FAQ 분류 키값 1씩 더해주기
foreach($faqType as $ftk => $ftv){
	$faqTypeVal[$ftk+1] = $ftv;
}

?>


<div class="c_section c_gridpage">
	<div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/service.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->


		<div class="grid_section">
			<div class="layout_fix">
                <div class="c_board_ctrl">
                    <div class="area_box area_left">
                        <div class="board_tit">
                            <div class="name"><?php echo $page_title; ?></div>
                            <div class="total">Total <?php echo number_format( 1 * $TotalCount); ?></div>
                        </div>
                    </div><!-- end area_left -->

                    <form name="boardSearch" role="search" action="" class="area_box area_right">
                        <input type="hidden" name="pn" value="faq.list"  role="search">
                        <input type="hidden" name="_type" value="<?php echo $_type ?>">
                        <div class="board_search">
                            <?php if( in_array($searchMode,array('t','c','tc')) == true) { // 검색한 후 노출 / 검색 전 숨김 ?>
                                <a href="/?pn=faq.list&_menu=<?php echo $_menu; ?>" class="btn_reset" title="초기화"></a>
                            <?php } ?>
                            <input type="text" name="searchWord" value="<?php echo $searchWord; ?>" class="input_design" autocomplete="off" placeholder="검색어 입력">
                            <input type="submit" name="" value="" class="btn_search" title="검색">
                        </div>
                        <select name="searchMode" style="display: none;">
                            <option value="tc">제목 + 내용</option>
                            <option value="t">제목</option>
                            <option value="c">내용</option>
                        </select>
                    </form><!-- end area_right -->

                    <div class="board_ctg">
                        <div class="swipe_box" ID="js_swipe_board_ctg">
                            <ul>
                                <li class="<?php echo $_type == '' ? ' hit':'' ?>"><a href="/?pn=faq.list" class="ctg">전체</a></li>
                                <?php foreach($faqTypeVal as $k=>$v) { ?>
                                    <li class="<?php echo $k == $_type ? ' hit':''; ?>"><a href="/?pn=faq.list&_type=<?php echo $k ?>" class="ctg"><?php echo $v?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <script>
                            // KAY :: 2022-12-12 :: 아이스크롤 스크립트(스와이프)
                            // js_swipe_블라블라 / #js_swipe_board_ctg => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
                            var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_board_ctg'), testScroll = '';

                            // 스와이프 적용
                            $(document).ready(function(){
                                swipe_Menu2/*숫자만변경*/();
                            });

                            function swipe_Menu2/*숫자만변경*/(){
                                $.each($('#js_swipe_board_ctg li'), function(k, v){  scrollWidth += $('#js_swipe_board_ctg li').eq(k).outerWidth()*1; });
                                var len = $('#js_swipe_board_ctg li').length;
                                swipe_Scroll2/*숫자만변경*/ = new IScroll('#js_swipe_board_ctg', {
                                    'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
                                    , mouseWheel: true
                                });

                                if(scrollIndex > 0 && $('#js_swipe_board_ctg li.hit').length > 0) {
                                    var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_board_ctg li.hit').outerWidth()*1/2 ) * -1;
                                    swipe_Scroll2/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_board_ctg li.hit'), 500, scrollOffset);
                                }
                            }
                        </script>
                    </div><!-- end board_ctg -->
                </div><!-- end c_board_ctrl -->

				<div class="c_board_list type_faq">
                    <?php if(count($listFaq) < 1){ // 내용 없을때 ?>
                        <div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
                        <?php }else{  ?>

                        <?php foreach($listFaq as $k=>$v) { // 클릭시 if_open 클래스 추가  ?>
                            <ul class="js_faq_list_item" data-uid="<?php echo $v['uid'] ?>">
                                <li class="this_tit">
                                    <div class="ic_q">Q</div>
                                    <div class="posting">
                                        <a href="#none" onclick="return false;" data-uid="<?php echo $v['uid']; ?>" class="upper_link js_open_faq_content" title="<?php echo $_title; ?>"></a>
                                        <div class="tit">
                                            <span class="field">[<?php echo $v['type'] ?>]</span>
                                            <?php echo $v['title']; ?>
                                        </div>
                                        <?php
                                        /* 아이콘
                                            <span class="state_icon">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_secret.svg" alt="비밀글">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_photo.svg" alt="사진첨부">
                                                <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_file.svg" alt="첨부파일">
                                                <?php if( $v['iconNew'] === true) { ?> <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/board_new.svg" alt="새글"> <?php } ?>
                                            </span>
                                        */
                                        ?>
                                    </div>
                                </li>
                                <li class="open_conts js" data-uid="<?php echo $v['uid'] ?>">
                                    <div class="tit"><?php echo $v['title']; ?></div>
                                    <div class="editor"><?php echo $v['content']; ?></div>
                                </li>
                            </ul>
                        <?php }?>
                    <?php } ?>
				</div><!-- end c_board_list -->

				<?php if(count($listFaq)>0){?>
					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php }?>
			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->

<script>
	// -- 게시물 검색 :: 공통
	$(document).on('submit','form[name="boardSearch"]',function(){
		var sw = $(this).find('[name="searchWord"]').val();
		if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="searchWord"]').focus(); return false; }
		return true;
	});

	// -- faq 컨텐츠 보기/숨기기
	$(document).on('click','.js_open_faq_content',function(){
		var _uid = $(this).attr('data-uid');
		if( _uid == '' || _uid == undefined){ return false; }
		var chk = $('.js_faq_list_item[data-uid="'+_uid+'"]').hasClass('if_open');

		$('.js_faq_list_item').removeClass('if_open');

		if(chk == true){
			$('.js_faq_list_item[data-uid="'+_uid+'"]').removeClass('if_open');
			$('.js_open_faq_content.btn[data-uid="'+_uid+'"]').attr('title','');
			$('.js.open_conts[data-uid="'+_uid+'"]').hide();
		}else{
			$('.js_faq_list_item[data-uid="'+_uid+'"]').addClass('if_open');
			$('.js_open_faq_content.btn[data-uid="'+_uid+'"]').attr('title','');
			$('.js.open_conts[data-uid="'+_uid+'"]').show();
		}

	});

</script>