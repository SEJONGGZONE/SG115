


<?php // 우측 퀵메뉴 ?>
<div class="p_Fly js_scroll_stage">
    <?php if(count($LatestList)>0){?>
        <a href="#none" onclick="return false;" class="btn_recent js_onoff_event js_recent" data-target=".p_Recent" data-add="if_open_recent">
            <span class="tx js_latest_cnt_view">최근 본 상품(<?php echo count($LatestList);?>)</span>
            <?php // 마지막 본 상품 이미지 ?>
            <?php
				$latest_pro_img = get_img_src($LatestList[0]['p_img_list_square'], IMG_DIR_PRODUCT);
            ?>
            <span class="thumb js_latest_img_view" style="background-image: url('<?php echo $latest_pro_img; ?>');"></span>
        </a>
    <?php } ?>

    <?php // 맨위로 버튼 ?>
    <a href="#none" onclick="return false;" class="go_top" title="맨위로"></a>

    <?php // LCY : 2023-06-12 : 퀵메뉴 앱 프로그램 적용 ?>
    <?php if( $AppUseMode === true && function_exists('is_app') == true &&  is_app() ===  true){ ?>
    <?php // 앱알림 버튼(앱에서만 노출) ?>

    <?php if( $appUserInfo['au_push_send'] != 'Y'){?>
	    <?php // 알림설정을 안했을때 ?>
	    <a href="#none" class="btn_alarm if_deny" onclick="alert('현재 알림설정이 해지된 상태입니다.'); return false;" title="알림"><span class="icon"></span></a>
	<?php }else{?>

		<?php if( $appUserInfo['var']['push_latest_count'] < 1 ){?>
	    <?php // 기본 상태(알림함 링크) ?>
	    <a href="/?pn=alarm" class="btn_alarm" title="알림"><span class="icon"></span></a>
	    <?php }else{ ?>
	    <?php // 알림이 1개라도 있을때(알림함 링크) ?>
	    <a href="/?pn=alarm" class="btn_alarm if_noread" title="알림">
	        <span class="icon"></span>
	        <span class="total"><?php echo $appUserInfo['var']['push_latest_count']; ?></span><?php // 안읽은 알림수 99개까지만 표시 ?>
	    </a>
		<?php } ?>
	<?php } ?>

	<?php } ?>

</div><!-- end p_Fly -->




<?php // 최근 본 상품  ?>
<div class="p_Recent">
	<div class="layer_in js_latest_box_wrap">
		<?php
			/* $LatestList : 최근본상품 */
			include_once(OD_PROGRAM_ROOT.'/inc.latest.php');
		?>
	</div>
	<div onclick="return false;" class="bg_close js_onoff_event" data-target=".p_Recent" data-add="if_open_recent"></div>
</div><!-- end p_Recent -->




<?php // 하단 fix 메뉴(모바일용) ?>
<div class="p_Fix<?php echo in_array($pn, array('shop.cart.list','shop.order.form','product.view','mypage.order.view','service.guest.order.list')) == true ? ' if_hide':'' ?>">
    <ul class="ul">
        <?php // 활성화시 li에 .hit 클래스 추가 ?>
        <li class="li">
            <a href="#none" onclick="return false;" class="btn js_onoff_event" data-target=".p_Slide" data-add="if_open_slide">
                <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_menu.svg" alt="" /></span>
                <span class="tx">MENU</span>
            </a>
        </li>
        <?php
			if(is_login()) { // 로그인 후
				$chkHit = false;
				if( preg_match("/(mypage\.)/",$pn) > 0 || preg_match("/(mypage\.main)/",$pn) > 0 ){ $chkHit = true; }
		?>
            <li class="li <?php echo $chkHit === true ? 'hit':null; ?>">
                <a href="/?pn=mypage.main" class="btn">
                    <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_my.svg" alt="" /></span>
                    <span class="tx">MYPAGE</span>
                </a>
            </li>
        <?php
			} else { // 로그인 전

			$chkHit = false;
			if( preg_match("/(member\.)/",$pn) > 0 && preg_match("/(member\.join\.)/",$pn) < 1 ){ $chkHit = true; }
		?>
            <li class="li <?php echo $chkHit === true ? 'hit':null; ?>">
                <a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn">
                    <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_my.svg" alt="" /></span>
                    <span class="tx">LOGIN</span>
                </a>
            </li>
        <?php } ?>

		<?php
			$chkHit = false;
			if( in_array($pn, array('main'))){ $chkHit = true; }
		?>
        <li class="li <?php echo $chkHit === true ? 'hit':null; ?>">
            <a href="/" class="btn">
                <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_home.svg" alt="" /></span>
                <span class="tx">HOME</span>
            </a>
        </li>
		<?php
			$chkHit = false;
			if( in_array($pn, array('shop.cart.list'))){ $chkHit = true; }
		?>
        <li class="li <?php echo $chkHit === true ? 'hit':null; ?>">
            <a href="/?pn=shop.cart.list" class="btn">
                <span class="ic">
					<span class="cart_num js_cart_cnt" style="<?php echo ($cart_cnt < 1 ? 'display:none;':null); ?>"><?php echo $cart_cnt; ?></span>
					<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_cart.svg" alt="" />
				</span>
                <span class="tx">CART</span>
            </a>
        </li>
        <?php // 최근본 상품이 있으면 li에 .if_recent 클래스 추가 ?>
        <li class="li this_recent js_recent_chk <?php echo count($LatestList)>0?'if_recent':'';?>">
            <a href="#none" onclick="return false;" class="btn js_onoff_event" data-target=".p_Recent" data-add="if_open_recent">
                <span class="ic"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/fix_today.svg" alt="" /></span>
                <?php // 최근 본 상품 이미지 노출 마지막 본 상품으로 이미지노출 ?>
				<?php
					$latest_pro_img = get_img_src($LatestList[0]['p_img_list_square'], IMG_DIR_PRODUCT);
				?>
                <span class="thumb js_latest_img_view" style="background-image: url('<?php echo $latest_pro_img; ?>');"></span>
                <span class="tx">HISTORY</span>
            </a>
        </li>
    </ul>
</div><!-- end p_Fix -->







</div><!-- end wrap -->
<div class="js_footer_position"></div>
	<?php 
		// {LCY} : 하이앱 -- 애플로그인 -- body 하단에 추가 (pc모바일 공통)
		include_once(OD_ADDONS_ROOT.'/sns_login/apple/callback.apple.php');
	?>

    <?php // container ?>
	<iframe name="common_frame" width="150" height="150" frameborder="0" style="display:none;"></iframe>
    <?php // validate setting ?>
	<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>
	<script src="<?php echo $system['__url']; ?>/include/js/common.footer.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			// 카트에 담긴 상품을 페이지 전체적으로 일괄 적용한다. class명은 glb_cart_cnt or js_cart_cnt 이다.
			// 인자 : 카트상품갯수, 상품갯수가 0일때 보이게 할것인지(show), 안보이게 할것인지(hide - 기본값)
			glb_cart_cnt_update('<?php echo $cart_cnt; ?>', 'hide'); // /include/js/shop.js

			if($('.js_wish_cnt').length > 0) $('.js_wish_cnt').text('<?php echo (get_wish_cnt()?get_wish_cnt():'0'); ?>'); // 찜한상품 개수 반영
		});

		// -- 로그인공통 로그인 필요할경우 qnxdlsek.
		$(document).on('click','.js_login',function(){
			if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
			location.href="/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER['QUERY_STRING']); ?>";
			return false;
		});

		// 이미지 onerror 처리
		$('img').error(function() {
			$(this).unbind('error');
			$(this).attr('src', '<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif');
		});
		$(document).ajaxComplete(function() {
			$('img').error(function() {
				$(this).unbind('error');
				$(this).attr('src', '<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif');
			});
		});

		$(document).on('click', '.js_wish', function(e) {
			e.preventDefault();
			var _pcode = $(this).data('pcode');
			var su = $(this);
			<?php if(is_login()) { ?>
				$.ajax({
					data: {
						mode: 'add',
						code: _pcode
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/ajax.product.wish.php',
					success: function(data) {
						data = data*1;
						if(data > 0) {
							$('.wish_message').addClass('if_hit');
							setTimeout(function(){ $('.wish_message').removeClass('if_hit'); }, 1100);

							su.addClass('hit');
							su.prop('title', '찜삭제');
						}
						else {
							su.removeClass('hit');
							su.prop('title', '찜하기');
						}
					}
				});
			<?php } else { ?>
				if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
				location.href = '/?pn=member.login.form&_rurl=<?php echo urlencode('/?'.$_SERVER['QUERY_STRING']); ?>';
				return;
			<?php } ?>
		});
	</script>

	<?php DeveModeFooter(); ?>
	<?php actionHook('footer_insert'); // 푸터에 스크립트등 삽입에 사용(로그 스크립트 등) ?>
</body>
</html>
