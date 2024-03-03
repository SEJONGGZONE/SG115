<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$agree_nomal_page -> 약관관련페이지에 출력 되는 일반페이지의 아이디 배열 => (/program/pages.view.php에서 지정)
	$page_content -> 페이지 제목, 내용정보 => (/program/pages.view.php에서 지정)
	$page_menu -> 페이지 메뉴정보 => (/program/pages.view.php에서 지정)
*/

// 타이틀
// KAY :: 2022-08-02 :: 일반페이지 타이틀 변경
$page_add_class ='';
if($type=='agree'){
	// 이용안내 타이틀
	$page_content['title'] = 'User guide';
}else{
	$page_add_class = 'type_only_page';
	if($page_row['np_menu']=='default'){
		// 회사소개 타이틀
		$page_content['title'] = 'About us';
	}else{
		// 단독메뉴일 경우 => 관리자에서 설정한 페이지명 노출
		$page_content['title'] = $page_row['np_title'];
	}
}

$page_title = $page_content['title']; // 페이지 대표 타이틀
include_once($SkinData['skin_root'].'/pages.header.php'); // PC 탑 네비
?>

<div class="c_section">

	<?php if(count($page_menu) > 1) { // 네비 ?>
		<div class="c_nav type_only">
			<div class="layout_fix">
				<div class="swipe_box" ID="js_swipe_menu_page">
					<ul>
						<?php foreach($page_menu as $tn_k=>$tn_v) { ?>
							<li<?php echo ($tn_v['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $tn_v['link']; ?>" class="menu"><strong><?php echo $tn_v['title']; ?></strong></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div><!-- end c_nav -->
		<script>
			// KAY :: 2022-12-12 :: 아이스크롤 스크립트(스와이프)
			// js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
			var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_menu_page'), swipe_Scroll_page = '';

			// 스와이프 적용
			$(document).ready(function(){
				swipe_Menu_page/*숫자만변경*/();
			});

			function swipe_Menu_page/*숫자만변경*/(){
				$.each($('#js_swipe_menu_page li'), function(k, v){  scrollWidth += $('#js_swipe_menu_page li').eq(k).outerWidth()*1; });
				var len = $('#js_swipe_menu_page li').length;
				swipe_Scroll_page/*숫자만변경*/ = new IScroll('#js_swipe_menu_page', {
					'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
					, mouseWheel: true
				});

				if(scrollIndex > 0 && $('#js_swipe_menu_page li.hit').length > 0) {
					var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_menu_page li.hit').outerWidth()*1/2 ) * -1;
					swipe_Scroll_page/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_menu_page li.hit'), 500, scrollOffset);
				}
			}
		</script>
	<?php } ?>

	<div class="layout_fix">
		<div class="c_editor_box editor <?php echo $page_add_class;?>"><?php // 회사소개,단독메뉴일때는 .type_only_page 추가 ?>
			<?php echo $page_content['content']; ?>
		</div>
	</div>
</div><!-- end c_section -->
