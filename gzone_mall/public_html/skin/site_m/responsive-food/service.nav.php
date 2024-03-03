<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
// -- 고객센터로 적용된 게시판을 가져온다.
$bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'service' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$TopNavArr = $normalMenu =  $boardMenu  = array();

// -- 게시판메뉴
foreach($bbsList as $k=>$v) {
	$chkHit = preg_match("/(board.)/",$pn) == true && $_menu == $v['bi_uid'] ? true : false;
	$skinNameView = $skinNameViewVal === true ? '('.$v['bi_skin'].')' : ' '; // 키값이 숫자일 경우 array_merge 시 0부터 순번 순으로 초기화
	$boardMenu[$v['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$v['bi_uid'],
		'hit'=> $chkHit,
		'title'=> $v['bi_name']
	);
}

// -- 일반메뉴
$normalMenu = array(
	'자주 묻는 질문'=>array(
		'link'=>'/?pn=faq.list',
		'hit'=>($pn == 'faq.list'?true:false),
		'title'=> '자주 묻는 질문'
	)
);
// -- 미확인 입금자 리스트
if($siteInfo['s_online_notice_use'] == 'Y'){
	$normalMenu['미확인 입금자'] = array(
		'link'=>'/?pn=service.deposit.list',
		'hit'=>($pn == 'service.deposit.list'?true:false),
		'title'=> '미확인 입금자'
	);
}
$TopNavArr = array_merge($boardMenu, $normalMenu );
?>

<div class="c_nav<?php echo in_array($pn,array('service.main')) ? ' type_only' : null ?>">
    <div class="layout_fix">
		<div class="swipe_box" ID="js_swipe_menu_service_nav">
			<ul>
				<?php foreach($TopNavArr as $k=>$v) { ?>
					<li class="<?php echo $v['hit'] === true ? 'hit':null?>"><a href="<?php echo $v['link'] ?>" class="menu"><strong><?php echo $v['title']; ?></strong></a></li>
				<?php }?>
			</ul>
		</div>
    </div>
	<script>
		var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_menu_service_nav'), swipe_Scroll_service_nav = '';

		// 스와이프 적용
		$(document).ready(function(){
			swipe_Menu_service_nav();
		});

		function swipe_Menu_service_nav(){
			$.each($('#js_swipe_menu_service_nav li'), function(k, v){  scrollWidth += $('#js_swipe_menu_service_nav li').eq(k).outerWidth()*1; });
			var len = $('#js_swipe_menu_service_nav li').length;
			swipe_Scroll_service_nav = new IScroll('#js_swipe_menu_service_nav', {
				'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
				, mouseWheel: true
			});

			if(scrollIndex > 0 && $('#js_swipe_menu_service_nav li.hit').length > 0) {
				var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_menu_service_nav li.hit').outerWidth()*1/2 ) * -1;
				swipe_Scroll_service_nav.scrollToElement(document.querySelector('#js_swipe_menu_service_nav li.hit'), 500, scrollOffset);
			}
		}
	</script>
</div><!-- end c_nav -->


<script>
	$(document).on('click','.js.btn_ctrl',function(){
		var chkOpen = $('.js.c_page_tit').hasClass('if_open');
		var chkNomenu = $('.js.c_page_tit').hasClass('if_nomenu');
		if( chkNomenu == true){ return false; }
		if( chkOpen == true){ $('.js.c_page_tit').removeClass('if_open'); }
		else{ $('.js.c_page_tit').addClass('if_open'); }
	});
</script>
