<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// -- 커뮤니티로 적용된 게시판을 가져온다.
$bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'community' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$TopNavArr = $normalMenu =  $boardMenu  = array();

// -- 게시판메뉴
foreach($bbsList as $k=>$v) {
	$chkHit = preg_match("/(board.)/",$pn) == true && $_menu == $v['bi_uid'] ? true : false;
	$boardMenu[$v['bi_name']] = array(
		'link'=>'/?pn=board.list&_menu='.$v['bi_uid'],
		'hit'=> $chkHit,
		'title'=> $v['bi_name']
	);
}

// -- 일반메뉴 첫번째
$normalMenu[0] = array(
	'상품리뷰'=>array(
		'link'=>'/?pn=service.eval.list',
		'hit'=>($pn == 'service.eval.list'?true:false),
		'title'=> '상품리뷰'
	),
	'상품문의'=>array(
		'link'=>'/?pn=service.qna.list',
		'hit'=>($pn == 'service.qna.list'?true:false),
		'title'=> '상품문의'
	)
);

// -- 일반메뉴 두번째
$normalMenu[1] = array(
	//'제휴문의'=>array(
	//	'link'=>'/?pn=service.partner.form',
	//	'hit'=>($pn == 'service.partner.form'?true:false),
	//	'title'=> '제휴문의'
	//),
	//'출석체크'=>array(
	//	'link'=>'/?pn=promotion.attend',
	//	'hit'=>($pn == 'promotion.attend'?true:false),
	//	'title'=> '제휴문의'
	//),
);



// -- 순서조절 하여 추가
$TopNavArr = array_merge($normalMenu[0] , $boardMenu , $normalMenu[1] );
?>

<div class="c_nav">
    <div class="layout_fix">
        <div class="swipe_box" ID="js_swipe_menu_comm_nav">
            <ul>
                <?php
                $maxCnt = 3; // 몇개씩 반복
                $forCnt = 0;
                $padCnt = (count($TopNavArr)%$maxCnt) != 0 ? $maxCnt - (count($TopNavArr)%$maxCnt) : 0;
                foreach($TopNavArr as $k=>$v) {
                    if($forCnt != 0 && ($forCnt%3) == 0){
                        // echo "</ul><ul>";
                    }
                    $forCnt ++;
                    ?>
                    <li class="<?php echo $v['hit'] === true ? 'hit':null?>"><a href="<?php echo $v['link'] ?>" class="menu"><strong><?php echo $v['title'] ?></strong></a></li>
                <?php } echo $padCnt > 0 ? implode(array_fill(0,$padCnt,"<!--<li></li>-->")) : null; // 남은 공간만큼 li로 채우기 ?>
            </ul>
        </div>
    </div>
	<script>
		// KAY :: 2022-12-12 :: 아이스크롤 스크립트(스와이프)
		// js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
		var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_menu_comm_nav'), swipe_Scroll_comm_nav = '';

		// 스와이프 적용
		$(document).ready(function(){
			swipe_Menu_comm_nav/*숫자만변경*/();
		});

		function swipe_Menu_comm_nav/*숫자만변경*/(){
			$.each($('#js_swipe_menu_comm_nav li'), function(k, v){  scrollWidth += $('#js_swipe_menu_comm_nav li').eq(k).outerWidth()*1; });
			var len = $('#js_swipe_menu_comm_nav li').length;
			swipe_Scroll_comm_nav/*숫자만변경*/ = new IScroll('#js_swipe_menu_comm_nav', {
				'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
				, mouseWheel: true
			});

			if(scrollIndex > 0 && $('#js_swipe_menu_comm_nav li.hit').length > 0) {
				var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_menu_comm_nav li.hit').outerWidth()*1/2 ) * -1;
				swipe_Scroll_comm_nav/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_menu_comm_nav li.hit'), 500, scrollOffset);
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
