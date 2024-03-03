<div class="c_section c_order js_guest_order">
    <?php 
    	if( $row['o_ordernum'] == '' || count($sres) < 1){
    		echo '<div class="c_none"><div class="gtxt">주문내역이 없습니다.</div></div>';
    	}
    	else{
	        // 주문내용 공통화 처리 
	        $app_view_file = 'guest_order'; // 비회원 주문
	        include_once $SkinData['skin_root']."/inc.order.view.php";
    	}

    ?>

</div><!-- end c_section -->

<script>
	// 비회원 주문조회 시 스크롤 이동
	$(document).ready(function(){
		setTimeout(function(){
			scrolltoClass(".js_guest_order");
		}, 300);
	});
</script>


<?php // ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----?>
<?php
	# 교환/반품 팝업
	include($SkinData['skin_root'].'/mypage.order.view.complain.php');
	# 부분취소 팝업
	include($SkinData['skin_root'].'/mypage.order.view.cancel_pop.php');


	# 가상계좌 주문취소일 경우 환불계정 레이아웃 미리 생성
	include_once($SkinData['skin_root'].'/mypage.order.pro.cancel_virtual.php');

	// 마이페이지/비회원 주문조회 공통 스크립트 
	include_once $SkinData['skin_root'].'/inc.order.js.php';
?>
