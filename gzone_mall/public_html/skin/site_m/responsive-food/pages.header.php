<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$page_title -> 페이지 대표 타이틀 => (스킨/pages.view.php)
	$page_menu -> 페이지 메뉴정보 => (스킨/pages.view.php -> /program/pages.view.php에서 지정)
*/
?>

<div class="c_page_tit<?php echo (count($page_menu) <= 0?' if_nomenu':' if_open'); ?> js_top_nav_wrap">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $page_title;?></div>
        </div>
    </div>
</div><!-- end c_page_tit -->


<script type="text/javascript">
	$(document).on('click', '.js_top_nav_toggle', function(e) {
		e.preventDefault();
		$(this).closest('.js_top_nav_wrap').toggleClass('if_open');
	});
</script>