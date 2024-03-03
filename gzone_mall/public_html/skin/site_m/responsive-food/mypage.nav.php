<?php
// 탑네비 메뉴 리스트
$TopNavArr = array();
if(is_login() === true) { // 로그인 후
	$TopNavArr = array(
		'주문내역'=>array(
			'link'=>'/?pn=mypage.order.list',
			'hit'=>(in_array($pn,array('mypage.order.list','mypage.order.view'))?true:false)
		),
		'적립금'=>array(
			'link'=>'/?pn=mypage.point.list',
			'hit'=>($pn == 'mypage.point.list'?true:false)
		),
		'쿠폰'=>array(
			'link'=>'/?pn=mypage.coupon.list',
			'hit'=>($pn == 'mypage.coupon.list'?true:false)
		),

		'찜한상품'=>array(
			'link'=>'/?pn=mypage.wish.list',
			'hit'=>($pn == 'mypage.wish.list'?true:false)
		),
		'나의 상품리뷰'=>array(
			'link'=>'/?pn=mypage.eval.list',
			'hit'=>($pn == 'mypage.eval.list'?true:false)
		),
		'나의 상품문의'=>array(
			'link'=>'/?pn=mypage.qna.list',
			'hit'=>($pn == 'mypage.qna.list'?true:false)
		),
		'1:1 온라인 문의'=>array(
			'link'=>'/?pn=mypage.inquiry.list',
			'hit'=> ($pn == 'mypage.inquiry.list' || $pn == 'mypage.inquiry.form' ?true:false)
		),
		'정보수정'=>array(
			'link'=>'/?pn=mypage.modify.form',
			'hit'=>($pn == 'mypage.modify.form'?true:false)
		),
		'로그인 기록'=>array(
			'link'=>'/?pn=mypage.login.log',
			'hit'=>($pn == 'mypage.login.log'?true:false)
            ),
		'회원탈퇴'=>array(
			'link'=>'/?pn=mypage.leave.form',
        	'hit'=>($pn == 'mypage.leave.form'?true:false)
		)
	);
}
?>


<?php if(count($TopNavArr) > 0) { // 네비 ?>
	<div class="c_nav<?php echo in_array($pn,array('mypage.main','mypage.order.view')) ? ' type_only' : null ?>">
		<div class="layout_fix">
			<div class="swipe_box" ID="js_swipe_menu_my_nav">
				<ul>
					<?php
						foreach($TopNavArr as $tn_k=>$tn_v) {
					?>
						<li<?php echo ($tn_v['hit'] === true?' class="hit"':null); ?>><a href="<?php echo $tn_v['link']; ?>" class="menu"><strong><?php echo $tn_k; ?></strong></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<script>
			// KAY :: 2022-12-12 :: 아이스크롤 스크립트(스와이프)
			// js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
			var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_menu_my_nav'), swipe_Scroll_my_nav = '';

			// 스와이프 적용
			$(document).ready(function(){
				swipe_Menu_my_nav/*숫자만변경*/();
			});

			function swipe_Menu_my_nav/*숫자만변경*/(){
				$.each($('#js_swipe_menu_my_nav li'), function(k, v){  scrollWidth += $('#js_swipe_menu_my_nav li').eq(k).outerWidth()*1; });
				var len = $('#js_swipe_menu_my_nav li').length;
				swipe_Scroll_my_nav/*숫자만변경*/ = new IScroll('#js_swipe_menu_my_nav', {
					'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
					, mouseWheel: true
				});

				if(scrollIndex > 0 && $('#js_swipe_menu_my_nav li.hit').length > 0) {
					var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_menu_my_nav li.hit').outerWidth()*1/2 ) * -1;
					swipe_Scroll_my_nav/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_menu_my_nav li.hit'), 500, scrollOffset);
				}
			}
		</script>
	</div><!-- end c_nav -->
<?php } ?>
