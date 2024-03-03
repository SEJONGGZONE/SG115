<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 내부패치 68번줄 kms 2019-11-05
$page_title = "1:1 온라인 문의";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력
?>
<div class="c_section c_gridpage">
    <div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
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


					<form role="search" name="inquirySearch" action="/" method="get" class="area_box area_right">
						 <a href="/?pn=mypage.inquiry.form" class="c_btn h40 black line btn_write type_write"><strong>문의하기</strong></a>

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


				<?php if(count($row) <= 0) { // 내용 없을때 ?>
					<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
				<?php } else { ?>
					<div class="c_board_list type_qna">

                        <?php foreach($row as $k=>$v) {
                            $_num = $TotalCount-$count-$k;
                        ?>
                            <ul class="js_view">
								<li class="this_tit" data-uid="<?php echo $v['r_uid']; ?>" data-hit="false">
                                    <div class="number"><?php echo $_num; ?></div>
									<?php if($v['r_status'] == '답변완료') { ?>
										<div class="c_tag black line">답변완료</div>
									<?php } else { ?>
										<div class="c_tag light line">답변대기</div>
									<?php } ?>
									<div class="posting">
                                        <a href="#none" onclick="return false;" class="upper_link js_detail_btn" title=""></a>
                                        <div class="tit"><?php echo htmlspecialchars($v['r_title']); ?></div>
									</div>
								</li>
                                <li class="this_info">
                                    <div class="date"><?php echo date('Y-m-d', strtotime($v['r_rdate'])); ?></div>
                                </li>
                                <li class="open_conts">
                                    <div class="my_post">
                                        <div class="tit"><?php echo htmlspecialchars($v['r_title']); ?></div>
                                        <div class="conts"><?php echo nl2br(stripslashes(htmlspecialchars($v['r_content']))); ?></div>
                                        <?php
                                        $getBoardFile = getFilesRes('smart_request', $v['r_uid'].'_user');
                                        if(count($getBoardFile) > 0) {
                                            ?>
                                            <div class="file_down">
                                                <?php foreach($getBoardFile as $kk=>$vv) { ?>
                                                    <a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <a href="#none" onclick="inquiry_del(<?php echo $v['r_uid']; ?>); return false;" class="c_btn h25 light">삭제하기</a>
                                    </div>

                                    <?php if($v['r_status'] == '답변완료' && $v['r_admcontent']) { // 관리자 답변 ?>
                                        <div class="answer">
                                            <div class="edge"></div>
                                            <div class="answer_in">
                                                <div class="admin">
                                                    <span class="name">관리자 답변</span>
													<span class="date"><?php echo date('Y-m-d', strtotime($v['r_admdate'])); ?></span>
                                                </div>
                                                <div class="editor"><?php echo nl2br(stripslashes(htmlspecialchars($v['r_admcontent']))); ?></div>
                                                <?php
                                                $getBoardFile = getFilesRes('smart_request', $v['r_uid']);
                                                if(count($getBoardFile) > 0) {
                                                    ?>
                                                    <div class="file_down">
                                                        <?php foreach($getBoardFile as $kk=>$vv) { ?>
                                                            <a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$vv['f_uid']; ?>" class="link"><?php echo $vv['f_oldname']; ?></a>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </li>
                            </ul>
                        <?php } ?>

					</div><!-- end c_board_list -->

					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php } ?>


			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->



<script type="text/javascript">


	// -- 1:1문의 검색 :: 공통
	$(document).on('submit','form[name="inquirySearch"]',function(){
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
		}
	});


	// 문의삭제
	function inquiry_del(uid) {
		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/mypage.inquiry.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
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
