<?php
	$page_title = "고객센터";
	include_once($SkinData['skin_root'].'/service.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_cs_main">

	<?php include_once($SkinData['skin_root'].'/service.nav.php'); // 메뉴출력 ?>

    <div class="layout_fix">
		<div class="top_faq">
			<div class="wrapping">

				<div class="cs_info">
					<a href="tel:<?php echo $siteInfo['s_glbtel']; ?>" class="tel"><?php echo $siteInfo['s_glbtel']; ?></a>
					<?php if($siteInfo['s_cs_info']!=''){?>
						<div class="open_time"><?php echo nl2br($siteInfo['s_cs_info']); ?></div>
					<?php }?>
				</div>

				<form name="faqSearch" method="get" class="search_form" role="search" action="">
					<input type="hidden" name="pn" value="faq.list">
					<input type="hidden" name="searchMode" value="tc">
					<input type="search" class="input_search" name="searchWord" placeholder="궁금한 내용을 입력해주세요." autocomplete="off"/>
					<input type="submit" class="btn btn_search" value="" title="검색" />
				</form>

				<?php
				// -- FAQ 키워드 있을 시
				$faqKeyword = trim($siteInfo['s_faq_keyword']) != '' ? explode(",",$siteInfo['s_faq_keyword']) : array();
				if( count($faqKeyword) > 0){
				?>
					<div class="keyword">
						<?php foreach($faqKeyword as $k=>$v) {  ?>
						<a href="/?pn=faq.list&searchMode=tc&searchWord=<?php echo $v ?>" class="word"><?php echo $v;?></a>
						<?php } ?>
					</div>
				<?php } ?>

			</div><!-- end wrapping -->
		</div><!-- end faq_search -->


		<div class="recent_wrap">
			<div class="board_box">
				<div class="board_tit">
					<div class="tit">자주 묻는 질문 TOP <?php echo $arrFaqBoardConfig['bestCnt'];?></div>
					<a href="/?pn=faq.list" class="btn_more">더보기</a>
				</div>
				<div class="board_list type_faq">
                    <?php
						$resBest = _MQ_assoc("select *from smart_bbs_faq where bf_best = 'Y' order by bf_uid desc limit 0,".$arrFaqBoardConfig['bestCnt']."");
						if( count($resBest) > 0) {
							foreach($resBest as $k=>$v) {
								$_title = htmlspecialchars(stripslashes($v['bf_title']));
								$_content = stripslashes($v['bf_content']);
								$arrIcon = array();
								$_num = $k+1;
								
								$arrFaq = unserialize(stripslashes($siteInfo['s_bbs_faq_type']));
								$faqType = explode(',',$arrFaq['type']);

								// FAQ 분류 키값 1씩 더해주기
								foreach($faqType as $ftk => $ftv){
									$faqTypeVal[$ftk+1] = $ftv;
								}
								$_type = $faqTypeVal[$v['bf_type']];
                    ?>
						<li class="js_faq_list_item" data-uid="<?php echo $v['bf_uid'] ?>">
							<div class="posting">
								<a href="#none" onclick="return false" data-uid="<?php echo $v['bf_uid']; ?>" class="upper_link js_open_faq_content"></a>
								<div class="ic_q">Q</div>
								<div class="post_tit">
									<span class="field">[<?php echo $_type; ?>]</span>
									<span class="tit"><?php echo $_title; ?></span>
								</div>
							</div>
							<div class="js board_box open_conts" data-uid="<?php echo $v['bf_uid'] ?>">
								<div class="tit"><?php echo $_title; ?></div>
								<div class="editor"><?php echo $_content; ?></div>
							</div>
						</li>
                    <?php }?>
					<?php }else{ ?>
						<div class="c_none">등록된 내용이 없습니다.</div>
                    <?php }?>
                </div>
			</div><!-- end list_box -->


				<?php
					// -- 메인 노출 설정한 고객센터 게시판 정보
					$bbs_menu= _MQ("
						select bi_uid, bi_name from smart_bbs_info
						where bi_main_view='Y' and bi_view_type = 'service'
					");

					// -- 메인 노출 설정한 고객센터 게시판의 글 정보
					$resPost= _MQ_assoc("
						select * from smart_bbs as b left join smart_bbs_info as bi on (bi.bi_uid = b.b_menu) 
						where 
							b_depth = '1' and b_secret != 'Y' and b_menu='".$bbs_menu['bi_uid']."'
						order by b_rdate desc limit 0,5 
					");
				?>

				
				<div class="board_box this_recent">
					<div class="board_tit">
						<div class="tit"><?php echo $bbs_menu['bi_name'];?></div>
						<a href="/?pn=board.list&_menu=<?php echo $bbs_menu['bi_uid'] ?>" class="btn_more">더보기</a>
					</div>

					<div class="board_list">
						<div class="post_list">
							<?php if( count($resPost) > 0){ ?>
							<ul>
								<?php
								foreach($resPost as $sk=>$sv){
									$_title = htmlspecialchars(stripslashes($sv['b_title']));
									$secret_icon = $img_icon=$files_icon=$talk_icon=$new_icon='';
									
									// 비밀글 아이콘
									if($sv['b_secret']=='Y'){
										$secret_icon = '<img src="'.$SkinData['skin_url'].'/images/c_img/board_secret.svg" alt="비밀글">';
									}

									// 사진첨부 아이콘
									if($sv['b_img1'] != '' && @is_file($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BOARD.$sv['b_img1']) == true){
										$img_icon ='<img src="'.$SkinData['skin_url'].'/images/c_img/board_photo.svg" alt="사진첨부">';
									}

									// 첨부파일 아이콘
									if( getFilesCount('smart_bbs',$sv['b_uid']) > 0){
										$files_icon ='<img src="'.$SkinData['skin_url'].'/images/c_img/board_file.svg" alt="첨부파일">';
									}

									// 댓글
									if($sv['b_talkcnt'] > 0){
										$talk_icon ='<span class="ic_reply"><img src="'.$SkinData['skin_url'].'/images/c_img/board_reply.svg" alt="댓글"><em>'.number_format($sv['b_talkcnt']).'</em></span>';
									}

									// 새글 아이콘
									if(time() - strtotime($sv['b_rdate'])< (60*60*24*$sv['bi_newicon_view'])) {
										$new_icon ='<img src="'.$SkinData['skin_url'].'/images/c_img/board_new.svg" alt="새글">';
									}
									$_rdate = date('Y-m-d',strtotime($sv['b_rdate']));

								?>
									<li>
										<div class="posting">
											<a href="/?pn=board.view&_uid=<?php echo $sv['b_uid'] ?>&_menu=<?php echo $sv['b_menu']; ?>" class="upper_link"></a>
											<div class="post_tit">
												<div class="tit"><?php echo $_title ?></div>
												<?php // (주석 삭제!)아이콘 기능 추가 ?>
												<?php if($secret_icon!='' || $img_icon!='' || $files_icon!='' || $talk_icon!='' || $new_icon!=''){?>
													<span class="state_icon">
														<?php echo $secret_icon.$img_icon.$files_icon.$talk_icon.$new_icon;?>
													</span>
												<?php }?>
											</div>
											<span class="date"><?php echo $_rdate; ?></span>
										</div>
									</li>
								<?php } ?>
							</ul>
							<?php }else{?>
								<div class="c_none">등록된 내용이 없습니다.</div>
							<?php }?>
						</div>
					</div>
				</div>
		</div><!-- end recent_wrap -->

    </div><!-- end layout_fix -->
</div><!-- end c_section -->

<script>

	// -- FAQ 검색
	$(document).on('submit','form[name="faqSearch"]',function(){
		var sw = $(this).find('[name="searchWord"]').val(); // 검색값
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
			$('.js_open_faq_content.btn[data-uid="'+_uid+'"]').attr('title','내용열기');
			$('.js.board_box[data-uid="'+_uid+'"]').hide();
		}else{
			$('.js_faq_list_item[data-uid="'+_uid+'"]').addClass('if_open');
			$('.js_open_faq_content.btn[data-uid="'+_uid+'"]').attr('title','내용닫기');
			$('.js.board_box[data-uid="'+_uid+'"]').show();
		}
	});
</script>