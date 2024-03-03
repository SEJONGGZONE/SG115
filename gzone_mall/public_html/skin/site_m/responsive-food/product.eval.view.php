<?php

	if(sizeof($res) < 1 ){
		echo '<div class="none_box"><span class="icon"></span><div class="c_btnbox"><ul><li><a href="#none" onclick="return false;" class="c_btn black line h40 js_review_open">상품리뷰 작성하기</a></li><li><a href="/?pn=service.eval.list" class="c_btn light line h40">다른리뷰 더보기</a><li></ul></div></div>';
	}else{
		$star_persent = get_eval_average($pcode);
		$eval_cnt = number_format(get_talk_total($pcode,"eval","normal"));
?>

	<div class="board_top js_review_top">
		<div class="layout_fix">
			<div class="inner">

				<div class="left_box">
					<div class="score eval_mark">
						<span class="mark">
							<span class="star this_value eval_percent" style="width:<?php echo $star_persent; ?>%"></span>
							<span class="star this_base"></span>
						</span>
						<span class="average eval_avg">
							<strong>
								 <?php
								 // 평점 5.0 만점. 소수점 한자리까지 노출
								 if($star_persent == 0){
									 echo number_format($star_persent/20,0);
								 }else{
									 echo number_format($star_persent/20,1);
								 }
								 ?>
							</strong>
							<em>/</em>
							<strong>5.0</strong>
						</span>
					</div>
					<span class="total_write">총 <em class="eval_cnt"><?php echo $eval_cnt; ?></em>건의 리뷰가 있습니다.</span>
				</div>
				<div class="right_box">
					<div class="c_btnbox">
						<ul>
							<li class="this_write"><a href="#none" onclick="return false;" class="c_btn black line h40 write js_review_open"><strong>상품리뷰 작성하기</strong></a></li>
							<li><a href="/?pn=service.eval.list" class="c_btn light line h40">전체보기</a></li>
						</ul>
					</div>
					<div class="guide_tx">작성한 글은 <a href="/?pn=mypage.eval.list">마이페이지 &gt; 나의 상품리뷰</a>에서도 확인가능합니다.</div>
				</div>
			</div><!-- end inner -->
		</div>
	</div><!-- end board_top -->

	<div class="board_list">
		<div class="layout_fix js_review_cnt" data-cnt="<?php echo count($res);?>">
			<ul>
			<?php
				foreach( $res as $k=>$v ){
					unset($eval_btn,$reply_content);

					// KAY : 2023-04-10 : 신고하기 기능 추가
					if($v['blockcnt'] > 0){  $v['repcnt'] += $v['blockcnt'];  }

					$num = $TotalCount - $k;

					$eval_content_uid = 'view_'.$v['pt_uid'];
					$eval_point = $v['pt_eval_point'];
					$eval_title = stripslashes(htmlspecialchars($v['pt_title']));
					$eval_id = LastCut2($v['pt_inid'], 3);
					$eval_rdate = date('Y-m-d',strtotime($v['pt_rdate']));
					$eval_content = $v['repcnt']>0?'신고된 리뷰로 블라인드 처리되었습니다.':nl2br(stripslashes(htmlspecialchars($v['pt_content'])));
					if($v['pt_inid'] == get_userid())	$eval_btn = '<a href="#none" onclick="eval_del(\''.$v['pt_uid'].'\'); return false; " class="btn_ctrl">삭제</a>';
					$eval_img = $v['pt_img'] ? '<div class="upload"><img src="'. get_img_src($v['pt_img'], IMG_DIR_PRODUCT).'" alt=""></div>' : '';

					// 리뷰 이미지
					$eval_img_src = get_img_src($v['pt_img'], IMG_DIR_PRODUCT);
					if($eval_img_src <> ''){
						$eval_img = '<div class="upload"><img src="'. $eval_img_src .'" alt=""></div>';
					}else{
						$eval_img = '';
					}

					// 리플 추출
					$reply_r = _MQ_assoc("select * from smart_product_talk where pt_depth=2 and pt_relation = '".$v['pt_uid']."'");
					if(count($reply_r) <= 0) $reply_r = array();
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

					}

				?>
					<li id="<?php echo $eval_content_uid; ?>" class="js_box_Vboard <?php echo $v['repcnt'] > 0 ? 'if_reported':'' ?>" data-hit="false">
						<div class="posting">
							<div class="post_info">
								<span class="mark">
									<span class="star this_value" style="width:<?php echo $eval_point; ?>%"></span>
									<span class="star this_base"></span>
								</span>
								<div class="writer">
									<span class="name"><?php echo $eval_id; ?></span>
									<span class="date">(<?php echo $eval_rdate; ?>)</span>
								</div>
							</div>
							<div class="post_conts">
								<?php if($v['repcnt']<=0){?>
									<a href="#none" onclick="return false;" class="upper_link js_btn_Vboard" title=""></a>
								<?php }?>
								<?php echo $eval_img; ?>
								<div class="content"><?php echo $eval_content; ?></div>
								<?php echo $reply_content; ?>
								<?php echo $eval_btn; ?>

                                 <?php if($v['pt_inid'] != get_userid() && $v['repcnt']<=0 && is_login()){?>
									<a href="#none" onclick="return false;" class="btn_ctrl js_btn_report" data-type="review" data-uid="<?php echo $v['pt_uid'] ?>">신고하기</a>
								 <?php }?>
							</div>
						</div><!-- end posting -->

						 <?php if($eval_img_src <> '' && $v['repcnt']<=0){ ?>
							<div class="post_photo">
								<a href="#none" onclick="return false;" class="upper_link js_btn_Vboard" title=""></a>
								<span class="photo_in">
									<span class="icon_h"></span>
									<span class="icon_v"></span>
									<div class="img" style="background-image: url('<?php echo $eval_img_src; ?>')"></div>
								</span>
							</div>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>


			<?php if($eval_cnt>$listmaxcount){?>
				<div class="c_pagi">
					<?php echo ajax_pagelisting_mobile($listpg, $Page, $listmaxcount,'eval_view',$eval_cnt); ?>
				</div><!-- end c_pagi -->
			<?php }?>
		</div>
	</div>
<?php } ?>




