<?php 
	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

	$pn = 'shop.cart.list';
	//  장바구니 공통 프로세스
	include_once(dirname(__FILE__).'/shop.cart.inc.php');	
?>
<?php // LDD NPAY { ?>	
    <?php
    $NPayTrigger = 'N';
    if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
    if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
    if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
    if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real') $NPayTrigger = 'Y'; // 버튼+주문연동 작업
    if(sizeof($arr_cart) <= 0) $NPayTrigger = 'N';
    if($NPayTrigger == 'Y') {
    ?>
    <script src="<?php echo $system['__url']; ?>/include/js/jquery-1.11.2.min.js?ver=1"></script><?php // jquery ?>


			<?php if( is_mobile() === true) {?>
			<script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/mobile/naverPayButton.js" charset="UTF-8"></script>
			<script type="text/javascript">
			//<![CDATA[

				naver.NaverPayButton.apply({
					BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
					TYPE: "A", // 버튼 모음 종류 설정
					COLOR: 1, // 버튼 모음의 색 설정
					COUNT: 1, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
					ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
					BUY_BUTTON_HANDLER: parent.NPayBuy, // 구매하기 버튼
					"":"",
				});
			//]]>
			</script>
			<?php }else{ ?>

			<script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
			<script type="text/javascript">
			//<![CDATA[
				naver.NaverPayButton.apply({
					BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
					TYPE: "A", // 버튼 모음 종류 설정
					COLOR: 1, // 버튼 모음의 색 설정
					COUNT: 1, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
					ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
					BUY_BUTTON_HANDLER: parent.NPayBuy, // 구매하기 버튼
					"":"",
				});
			//]]>
			</script>
			<?php } ?>

    <?php } ?>
<?php // } LDD NPAY ?>
