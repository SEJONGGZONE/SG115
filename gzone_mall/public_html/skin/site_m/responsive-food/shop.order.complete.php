<?php
$page_title = "주문완료";
include_once($SkinData['skin_root'].'/shop.header.php'); // 상단 헤더 출력
?>


<div class="c_process">
	<ul>
		<li class=""><span class="num">01</span><span class="tit">장바구니</span></li>
		<li class=""><span class="num">02</span><span class="tit">주문결제</span></li>
		<li class="hit"><span class="num">03</span><span class="tit">주문완료</span></li>
	</ul>
</div><!-- end c_process -->


<div class="c_section c_complete">
    <div class="layout_fix">

		<div class="greeting_box">
			<div class="order_num">주문번호 : <?php echo $row['o_ordernum']; ?></div>
			<dl>
				<dt><?php echo $row['o_oname']; ?>님의 주문이 완료되었습니다.</dt>
				<dd>
					<?php if(is_login()) { // 로그인 후 ?>
						자세한 주문결과는 마이페이지 주문내역에서 확인가능합니다.<br/>
						빠른 배송을 위해서 최선을 다하겠습니다.<br/>
						티켓의 경우 결제확인 즉시 QR코드가 발행됩니다.
					<?php } else { // 로그인 전 ?>
						자세한 주문결과는 비회원 주문조회를 통해 확인가능합니다.<br/>
						빠른 배송을 위해서 최선을 다하겠습니다.<br/>
						티켓의 경우 결제확인 즉시 QR코드가 발행됩니다.
					<?php } ?>
				</dd>
			</dl>

			<div class="c_btnbox type_full">
				<ul>
					<li><a href="/" class="c_btn h50 black line">쇼핑 계속하기</a></li>
					<?php if(is_login()) { // 로그인 후 ?>
						<li><a href="/?pn=mypage.order.list" class="c_btn h50 black ">주문내역</a></li>
					<?php } else { // 로그인 전 ?>
						<li><a href="/?pn=service.guest.order.list" class="c_btn h50 black ">비회원 주문조회</a></li>
					<?php } ?>
				</ul>
			</div>
		</div><!-- end greeting_box -->

    </div>
</div><!-- end c_section -->