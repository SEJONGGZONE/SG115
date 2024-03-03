<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = "상품리뷰";
include_once($SkinData['skin_root'].'/community.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_gridpage">
	<div class="layout_fix layout_grid">


		<div class="grid_aside">
			<?php
				include_once($SkinData['skin_root'].'/community.nav.php'); // 메뉴출력
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

                    <form role="search" name="evalSearch" action="/" method="get" class="area_box area_right">
                        <input type="hidden" name="pn" value="<?php echo $pn; ?>">
                        <input type="hidden" name="search_type" value="search_title,search_content">
                        <div class="board_search">
                            <?php if(isset($search_word) && $search_word != '') { // 검색한 후 노출 / 검색 전 숨김 ?>
                                <a href="/?pn=<?php echo $pn; ?>" class="btn_reset" title="초기화"></a>
                            <?php } ?>
                            <input type="search" name="search_word" value="<?php echo $search_word; ?>" class="input_design" autocomplete="off" placeholder="검색어 입력">
                            <input type="submit" value="" class="btn_search" title="검색">
                        </div>
                    </form><!-- end area_right -->
                </div><!-- end c_board_ctrl -->

				<div class="c_board_rv">
					<?php if(count($row) <= 0) { // 내용 없을때 ?>
						<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
					<?php } else { ?>
						<ul>
							<?php
							foreach($row as $k=>$v) {
								$_num = $TotalCount-$count-$k;
								// 후기에 등록딘 이미지 노출 없으면 상품이미지 노출 kms 2019-08-02
								$pro_img = $v['pt_img'] == "" ? get_img_src($v['p_img_list2'], IMG_DIR_PRODUCT) : get_img_src($v['pt_img'], IMG_DIR_PRODUCT); // 후기이미지 없으면 상품이미지
								$is_wish = (is_login() ? _MQ_result("select count(*) from smart_product_wish where pw_pcode = '{$v['p_code']}' and pw_inid = '".get_userid()."' "):0); // 위시 여부
								$eval_point = get_eval_average($v['p_code']); // 평균평점
								$pro_link = '/?pn=product.view&pcode='.$v['p_code']; // 상품 링크
								$is_file = ($v['pt_img']?true:false); // 파일첨부 여부(포토후기)
								$is_new = ((time()-strtotime($v['pt_rdate'])<(60*60*24*5))?true:false);
								$talk_img = get_img_src($v['pt_img'], IMG_DIR_PRODUCT); // 첨부 이미지
								$talk_title = stripslashes(htmlspecialchars($v['pt_title'])); // 제목
								$talk_content = stripslashes(htmlspecialchars($v['pt_content'])); // 내용
								$star_persent = $v['pt_eval_point']; // 지급점수

								$reply_content = '';
								$reply_r = _MQ_assoc("select * from smart_product_talk as pt where pt_relation = '{$v['pt_uid']}' and pt_depth=2 order by pt_rdate asc");
								if(count($reply_r) <= 0) $reply_r = array();
								foreach($reply_r as $kk=>$vv) {
									if(!$vv['pt_content']) continue;
									$reply_content .= '
										<div class="answer">
											<div class="edge"></div>
											<div class="answer_in">
												<div class="admin">
													<span class="name">관리자 답변</span><span class="date">'.date('Y-m-d', strtotime($vv['pt_rdate'])).'</span>
												</div>
												<div class="editor">
													'.nl2br(stripslashes(htmlspecialchars($vv['pt_content']))).'
												</div>
											</div>
										</div>
									';
								}
								$del_btn = ($v['pt_inid'] == get_userid() ?'<a href="#none" onclick="eval_del('.$v['pt_uid'].'); return false;" class="btn_del"></a>':null);
							?>
								<li class="js_view" data-uid="<?php echo $v['pt_uid']; ?>" data-hit="false">
									<div class="posting">
										<a href="#none" onclick="return false;" class="upper_link js_detail_btn" title=""></a>
										<div class="mypost">
											<span class="mark">
												<span class="star this_value" style="width:<?php echo $star_persent; ?>%"></span>
												<span class="star this_base"></span>
											</span>
											<div class="conts"><?php echo ($talk_content); ?></div><!-- <?php echo nl2br($talk_content); ?> -->
											<div class="writer">
												<span class="name"><?php echo LastCut2($v['pt_inid'], 3); ?></span>
												<span class="date">(<?php echo date('Y-m-d', strtotime($v['pt_rdate'])); ?>)</span>
												<?php echo $del_btn; // <!-- 내글일때 노출 --> ?>
											</div>
										</div>
										<div class="thumb">
											<img src="<?php echo $pro_img; ?>" alt="<?php echo htmlspecialchars($v['p_name']); ?>" />
										</div>
										<a href="<?php echo $pro_link; ?>" class="about_item" target="_blank">상품명 : <?php echo htmlspecialchars($v['p_name']); ?></a>
									</div><!-- end posting -->

                                    <?php // 레이어 : 리뷰보기 ?>
									<div class="c_layer type_allreview">
                                        <div class="wrapping">
                                            <div class="tit_box">
                                                <div class="tit">리뷰보기</div>
                                                <a href="#none" onclick="return false;" class="btn_close js_detail_btn" title="닫기"></a>
                                            </div><!-- end tit_box -->

                                            <div class="conts_box c_scroll_v">
                                                <div class="review_conts">
                                                    <?php if($talk_img) { // 이미지 ?>
                                                        <div class="photo"><img src="<?php echo $talk_img; ?>" alt="" /></div>
                                                    <?php } ?>
                                                    <div class="rv_cont"><?php echo nl2br($talk_content); ?></div>
                                                    <?php if(count($reply_r) > 0) { // 관리자 답변 ?>
														<?php echo $reply_content; ?>
                                                    <?php } ?>
                                                </div>
                                            </div><!-- end conts_box -->
                                        </div>
                                        <div onclick="return false;" class="bg_close js_detail_btn"></div>
									</div><!-- end c_layer -->

								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</div><!-- end c_board_rv -->

				<?php if(count($row) > 0) { // 내용 없을때 ?>
					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php }?>

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->


<script type="text/javascript">

	// -- 상품리뷰 검색 :: 공통
	$(document).on('submit','form[name="evalSearch"]',function(){
		var sw = $(this).find('[name="search_word"]').val();
		if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="search_word"]').focus(); return false; }
		return true;
	});

	$(document).on('click', '.js_detail_btn', function(e) {
		e.preventDefault();
		var su = $(this).closest('.js_view');
		var _uid = su.data('uid');
		var _visible = su.hasClass('if_open');
		$('.js_view').removeClass('if_open');
		$('.js_detail_btn').attr('title', '');
		if(_visible === false) {
			su.addClass('if_open');
			su.find('.js_detail_btn').attr('title', '');
			var _smode = (su.attr('data-hit') == 'false'?'update':'nocount');
			if(_smode == 'update') {
				$.ajax({
					data: {
						_mode: 'eval_hit',
						_smode: _smode,
						_uid: _uid
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
					success: function(data) {
						// hit수 증가
						var _num = su.find('.js_eval_hit').text();
						_num.replace(/[^0-9]/g, '')*1;
						_num = _num*1;
						su.find('.js_eval_hit').text(number_format( 1 * _num+1));

						// 중복 hit차단
						su.attr('data-hit', 'true');
					}
				});
			}
		}
	});


	// 리뷰 삭제
	function eval_del(uid) {

		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
					}
					else if( data == "is reply" ) {
						alert('댓글이 있으므로 삭제가 불가합니다.');
					}
					else {
						alert('정상적으로 삭제하였습니다.');
						location.reload();
					}
				}
			});
		}
	}
</script>