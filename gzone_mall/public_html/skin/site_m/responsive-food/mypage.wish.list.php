<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "찜한상품";
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

				<form class="wish_frm c_my_wish" action="<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php" method="post" target="common_frame">
					<input type="hidden" name="mode" value="choice_delete">

					<div class="c_board_ctrl">
                        <div class="area_box area_left">
                            <div class="board_tit">
                                <div class="name"><?php echo $page_title; ?></div>
                                <div class="total">Total <?php echo number_format( 1 * $TotalCount); ?></div>
                            </div>
                        </div><!-- end area_left -->

                        <div class="area_box area_right">
                            <a href="#none" onclick="all_check(); return false;"; class="c_btn h30 light line">전체선택</a>
                            <a href="#none" onclick="all_uncheck(); return false;"; class="c_btn h30 light line">선택해제</a>
                            <a href="#none" onclick="select_delete(); return false;"; class="c_btn h30 black line" >선택삭제</a>
                        </div>
                    </div><!-- end c_board_ctrl -->

					<?php if(count($row) > 0) { ?>
						<ul>
							<?php
							foreach($row as $k=>$v) {
								$pro_img = get_img_src($v['p_img_list2'], IMG_DIR_PRODUCT); // 상품이미지
								$pro_link = '/?pn=product.view&pcode='.$v['p_code'];
							?>
								<li>
									<div class="thumb">
										<a href="<?php echo $pro_link; ?>" class="upper_link"></a>
										<img src="<?php echo $pro_img; ?>" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" />
										<?php if($v['p_stock'] <= 0) { ?>
											<div class="soldout">SOLD OUT</div>
										<?php } ?>

										<?php if($v['p_stock'] >0 &&  $v['p_sale_type']=='T' && date('Y-m-d',strtotime($v['p_sale_sdate']))<=date('Y-m-d') && date('Y-m-d',strtotime($v['p_sale_edate']))<=date('Y-m-d')   ){ ?>
											<div class="soldout">판매종료</div>
										<?php } ?>
									</div>
									<a href="<?php echo $pro_link; ?>" class="item_name"><?php echo htmlspecialchars($v['p_name']); ?></a>
									<div class="item_price"><?php echo number_format( 1 * $v['p_price']); ?>원</div>
									<div class="ctrl_box">
										<label class="c_label"><input type="checkbox" name="_chk[<?php echo $v['pw_uid']?>]" class="_chk_class" value="Y" /><span class="tx"><span class="icon"></span>선택</span></label>
										<a href="#none" class="btn_del" onclick="wish_del('<?php echo $v['pw_uid']; ?>'); return false;" title="찜삭제"></a>
									</div>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>

				</form><!-- end c_my_wish -->

				<?php if(count($row) <= 0) { // 내용 없을때 ?>
					<div class="c_none"><span class="gtxt">찜한 상품이 없습니다.</span></div>
				<?php } ?>

				
				<?php if(count($row) > 0) { ?>
					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php }?>

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->


<script type="text/javascript">
function select_delete() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	if($('._chk_class').is(':checked') != true) { alert('삭제할 상품을 선택하세요.'); return; }
	if(!confirm('선택한 찜 상품을 삭제하시겠습니까?')) return;
	$('.wish_frm').submit();
}
function all_check() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	$('._chk_class').attr('checked', true);
}
function all_uncheck() {
	if($('._chk_class').length < 1) { alert('찜한 상품이 없습니다.'); return; }
	$('._chk_class').attr('checked', false);
}
function wish_del(pw_uid) {
    if(confirm('정말 삭제하시겠습니까?')) common_frame.location.href=('<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php?mode=delete&uid='+pw_uid);
}
</script>