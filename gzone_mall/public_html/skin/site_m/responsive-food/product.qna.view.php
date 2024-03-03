<?php
	if(sizeof($res) < 1 ){
		echo '<div class="none_box"><span class="icon"></span><div class="c_btnbox"><ul><li><a href="#none" onclick="return false;" class="c_btn black line h40 js_onoff_event" data-target=".c_layer.type_qna" data-add="if_open_layer">상품문의 작성하기</a></li><li><a href="/?pn=service.qna.list" class="c_btn h40 light line">다른문의 더보기</a></li></ul></div></div>';

	}else{
		// 상품문의 갯수
		$qna_cnt = number_format(get_talk_total($pcode,"qna","normal"));

		// 상품문의 답변개수
		$reply_total_cnt = _MQ_result("select count(*) as cnt from smart_product_talk where pt_depth=2 and pt_pcode='".$pcode."' and pt_type='상품문의' ");

		$qna_reply_percent = (100/$qna_cnt)*$reply_total_cnt;

		 // 문의 답변완료 퍼센트
		 if($qna_reply_percent == 0 || is_int($qna_reply_percent)==true){
			 $qna_reply_per = number_format($qna_reply_percent,0);
		 }else{
			 $qna_reply_per = number_format($qna_reply_percent,1);
		 }

?>
    <?php // 문의 타이틀 ?>
    <div class="board_top">
		<div class="layout_fix">
			<div class="inner">
				<div class="left_box">
					<div class="score">
						<div class="average"><i>답변완료</i><strong><?php echo $qna_reply_per;?>%</strong></div>
					</div>
					<span class="total_write">총 <em><?php echo $qna_cnt; ?></em>건의 문의가 있습니다.</span>
				</div>
				<div class="right_box">
					<div class="c_btnbox">
						<ul>
							<li class="this_write">
								<a href="#none" onclick="return false;" class="c_btn black line h40 write js_onoff_event" data-target=".c_layer.type_qna" data-add="if_open_layer"><strong>상품문의 작성하기</strong></a>
							</li>
							<li><a href="/?pn=service.qna.list" class="c_btn h40 light line">전체보기</a></li>
						</ul>
					</div>
					<div class="guide_tx">작성한 글은 <a href="/?pn=mypage.qna.list">마이페이지 &gt; 나의 상품문의</a>에서도 확인가능합니다.</div>
				</div>
			</div><!-- end inner -->
		</div>
    </div><!-- end board_tit -->

	<div class="board_list">
		<div class="layout_fix">
			<ul>
			<?php
				foreach( $res as $k=>$v ){
					unset($qna_btn,$reply_content);

					$num = $TotalCount - $k;

					$qna_content_uid = 'view_'.$v['pt_uid'];
					$qna_title = stripslashes(htmlspecialchars($v['pt_title']));
					$qna_id = LastCut2($v['pt_inid'], 3);
					$qna_rdate = date('Y-m-d',strtotime($v['pt_rdate']));
					$qna_content = nl2br(stripslashes(htmlspecialchars($v['pt_content'])));
					if($v['pt_inid'] == get_userid())	$qna_btn = '<a href="#none" onclick="qna_del(\''.$v['pt_uid'].'\'); return false; " class="btn_ctrl">삭제</a>';
					$qna_img = $v['pt_img'] ? '<div class="img_box"><img src="'. get_img_src($v['pt_img'], IMG_DIR_PRODUCT).'" alt=""></div>' : '';

					// 리플 추출
					$reply_icon = '<span class="c_tag light line">답변대기</span>';
					$reply_r = _MQ_assoc("select * from smart_product_talk where pt_depth=2 and pt_relation = '".$v['pt_uid']."'");
					foreach($reply_r as $kk=>$vv) {

						$reply_content .= '
								<div class="post_reply">
									<div class="edge"></div>
									<div class="reply_in">
										<div class="reply_info">
											<span class="name">관리자 답변</span>
											<span class="date">('. date('Y-m-d',strtotime($vv['pt_rdate'])) .')</span>
										</div>
										<div class="reply_content">'. nl2br(stripslashes(htmlspecialchars($vv['pt_content']))) .'</div>
									</div>
								</div><!-- end reply -->
						';

						$reply_icon = '<span class="c_tag black line">답변완료</span>';

					}

					unset($new_icon);
					if(time()-strtotime($v['pt_rdate']) < 60*60*24*2){
						$new_icon .= '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_new.gif" alt="새글">';
					}
					$new_icon .= $v['pt_img'] ? '<img src="'. $SkinData['skin_url'] .'/images/c_img/ic_image.gif" alt="사진첨부">' : '';
					/* <img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_secret.gif" alt="비밀글">
						<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_photo.gif" alt="사진첨부">
						<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_image.gif" alt="사진첨부">
						<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ic_new.gif" alt="새글"> */


				?>
						<li id="<?php echo $qna_content_uid; ?>" class="js_box_Vboard" data-hit="false">
							<div class="posting">
								<div class="post_info">
									<?php echo $reply_icon; ?>
									<div class="writer">
										<span class="name"><?php echo $qna_id; ?></span>
										<span class="date">(<?php echo $qna_rdate; ?>)</span>
									</div>
								</div>
								<div class="post_conts">
									<a href="#none" onclick="return false;" class="upper_link js_btn_Vboard" title=""></a>
									<div class="title"><?php echo $qna_title; ?></div>
									<div class="content"><?php echo $qna_content; ?></div>
									<?php echo $reply_content; ?>
									<?php echo $qna_btn; ?>
								</div>
							</div><!-- end posting -->
						</li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<?php if($qna_cnt>$listmaxcount){?>
		<div class="c_pagi">
			<?php echo ajax_pagelisting_mobile($listpg, $Page, $listmaxcount,'qna_view',$qna_cnt); ?>
		</div><!-- end c_pagi -->
	<?php }?>

<?php } ?>


