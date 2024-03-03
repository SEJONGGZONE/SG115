<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>
<!DOCTYPE HTML>
<html lang="ko">
	<head>
		<title><?php echo $siteInfo['s_glbtlt']; ?></title>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<?php /*
		// 캐싱을 사용하지 않을경우 사용 (/include/var.php 의 $cache_ver 값을 마이크로 타임으로 변경 필요)
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Pragma" content="no-cache">
		*/?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi, minimal-ui, viewport-fit=cover" />
		<meta name="format-detection" content="telephone=no" /><?php // 자동으로 전화링크되는것 방지 ?>
		<meta name="keywords" content="<?php echo str_replace(array('/', '\\', '"', "'"), '', $siteInfo['s_glbkwd']); ?>">
		<meta name="description" content="<?php echo str_replace(array('/', '\\', '"', "'"), '', $siteInfo['s_glbdsc']); ?>">

		<!-- 모바일브라우저 상단 색상 -->
		<meta content='<?php echo $siteInfo['s_mobile_header_color']; ?>' name='apple-mobile-web-app-status-bar-style'/><!-- iOS Safari -->
		<meta content='<?php echo $siteInfo['s_mobile_header_color']; ?>' name='msapplication-navbutton-color'/><!-- Windows Phone -->
		<meta content='<?php echo $siteInfo['s_mobile_header_color']; ?>' name='theme-color'/><!-- Chrome, Firefox, Opera -->

		<?php if($og_type) { ?><meta property="og:type" content="<?php echo $og_type; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_title) { ?><meta property="og:title" content="<?php echo $og_title; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_description) { ?><meta property="og:description" content="<?php echo $og_description; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_url) { ?><meta property="og:url" content="<?php echo $og_url; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_site_name) { ?><meta property="og:site_name" content="<?php echo $og_site_name; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_image) { ?><meta property="og:image" content="<?php echo $og_image; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>
		<?php if($og_app_id) { ?><meta property="fb:app_id" content="<?php echo $og_app_id; ?>" /><?php // Open Graph ?><?php echo PHP_EOL; } ?>

		<?php if($og_type2) { ?><meta property="twitter:card" content="<?php echo $og_type2; ?>" /><?php // twitter:card ?><?php echo PHP_EOL; } ?>
		<?php if($og_title) { ?><meta property="twitter:title" content="<?php echo $og_title; ?>" /><?php // twitter:card ?><?php echo PHP_EOL; } ?>
		<?php if($og_description) { ?><meta property="twitter:description" content="<?php echo $og_description; ?>" /><?php // twitter:card ?><?php echo PHP_EOL; } ?>
		<?php if($og_url) { ?><meta property="twitter:url" content="<?php echo $og_url; ?>" /><?php // twitter:card ?><?php echo PHP_EOL; } ?>
		<?php if($og_image) { ?><meta property="twitter:image" content="<?php echo $og_image; ?>" /><?php // twitter:card ?><?php echo PHP_EOL; } ?>

		<?php echo (str_replace(array('.onedaynet.co.kr', '.gobeyond.co.kr'), '', $_SERVER['HTTP_HOST']) != $_SERVER['HTTP_HOST']?'<meta name="robots" content="noindex">'.PHP_EOL:null); // 원데이넷/상상너머 2차 도메인으로 네이버 검색 노출 차단 ?>
		<meta name="NaverBot" content="ALL">
		<meta name="NaverBot" content="index,follow">
		<meta name="apple-mobile-web-app-capable" content="yes"><?php // Apple iOS / 홈화면 바로가기 ?>
		<meta name="apple-mobile-web-app-status-bar-style" content="default"><?php // Apple iOS / 홈화면 바로가기 ?>
		<meta name="apple-mobile-web-app-title" content="<?php echo $siteInfo['s_glbtlt']; ?>"><?php // Apple iOS / 홈화면 바로가기 ?>
		<link rel="canonical" href="<?php echo $canonical_url; ?>">
		<?php if($og_image) { ?><link href="<?php echo $og_image; ?>" rel="image_src" /><?php // Open Graph ?><?php echo PHP_EOL; } // 페이스북 공유하기 썸네일 ?>

        <?php // 디자인css 순서지킬것 ?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/setting.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
		<link href="<?php echo $SkinData['skin_url']; ?>/css/common.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
		<?php if(getChkIE()!=true){?><link href="<?php echo $SkinData['skin_url']; ?>/css/common_responsive.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php }?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/sub.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
		<?php if(getChkIE()!=true){?><link href="<?php echo $SkinData['skin_url']; ?>/css/sub_responsive.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php }?>
        <link href="<?php echo $SkinData['skin_url']; ?>/css/skin.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
        <?php if(getChkIE()!=true){?><link href="<?php echo $SkinData['skin_url']; ?>/css/skin_responsive.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php }?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/customize_p.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // 작업자 맞춤제작 CSS::개발팀전용 ?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/customize_d.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // 작업자 맞춤제작 CSS::디자인팀전용 ?>

		<?php if(getChkIE()===true){?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/ie_fix.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // ie에서만 사용 ?>
		<?php }?>

		<link href="<?php echo $system['__url']; ?>/include/js/bxslider/jquery.bxslider.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><?php // bxslider ?>
        <?php /* <link href="<?php echo $system['__url']; ?>/include/js/swipejs/css/swiper.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" /><!-- Swiper --> */ ?>
		<link href="<?php echo $system['__url']; ?>/include/js/swipejs/css/swiper_5.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" /><?php // Swiper ?>
		<link href="<?php echo $system['__url']; ?>/include/smarteditor2/css/ko_KR/smart_editor2_in.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><?php // 에디터 css ?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/editor.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // 에디터 css ?>
		<link href="<?php echo $system['__url']; ?>/include/js/air-datepicker/css/datepicker.min.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css"><?php // 데이트피커 추가 ?>

		<?php if($Favicon) { echo PHP_EOL; // 파비콘 ?>
		<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="apple-touch-icon-precomposed" />
		<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="shortcut icon" type="image/x-icon"/>
		<?php echo PHP_EOL; } ?>

        <?php // 영문폰트 ?>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

		<script src="<?php echo $system['__url']; ?>/include/js/jquery-1.11.2.min.js?ver=1"></script><?php // jquery ?>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery-migrate-1.2.1.min.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.easing.1.3.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery.placeholder.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.lightbox_me.js?ver=1"></script><?php // lightbox ?>
		<script src="<?php echo $system['__url']; ?>/include/js/bxslider/jquery.bxslider.js?ver=1"></script><?php // bxslider ?>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.validate.js?ver=1"></script><?php // validate ?>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery.dotdotdot.js?ver=1"></script><?php // dotdotdot ?>
		<script src="<?php echo $system['__url']; ?>/include/js/default.js?ver=<?php echo $cache_ver; ?>"></script><?php // 기본 js ?>
		<script src="<?php echo $system['__url']; ?>/include/js/shop.js?ver=<?php echo $cache_ver; ?>"></script><?php // 쇼핑몰 공통 js ?>
        <?php /* <script src="<?php echo $system['__url']; ?>/include/js/swipejs/swiper.custom.js?ver=<?php echo $cache_ver; ?>" type="text/javascript"></script><!-- Swiper 커스텀 --> */ ?>
		<script src="<?php echo $system['__url']; ?>/include/js/swipejs/swiper.custom_5.js?ver=<?php echo $cache_ver; ?>" type="text/javascript"></script><?php // Swiper 커스텀 ?>
		<script src="<?php echo $system['__url']; ?>/include/js/swipejs/swiper.addon.js?ver=<?php echo $cache_ver; ?>" type="text/javascript"></script><?php // Swiper addon ?>

        <?php // 데이트피커 추가 ?>
		<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/datepicker.js?ver=<?php echo $cache_ver; ?>"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/i18n/datepicker.ko.js?ver=<?php echo $cache_ver; ?>"></script>

		<!-- iscroll -->
		<script src="<?php echo $system['__url']; ?>/include/js/iscroll-master/build/iscroll-probe.js"></script><!-- jquery -->

		<?php // LCY : clipboard 추가 : 2020-10-26  ?>
		<script src="<?php echo $system['__url']; ?>/include/js/clipboard/clipboard.min.js"></script>

        <?php // 2017-07-12 ::: 네이버 스마트에디터2 추가 ::: SSJ ?>
		<script type="text/javascript" src="<?php echo $system['__url']; ?>/include/smarteditor2/js/service/HuskyEZCreator.js?ver=<?php echo $cache_ver; ?>" charset="utf-8"></script>
		<script type="text/javascript" src="<?php echo $system['__url']; ?>/include/smarteditor2/smarteditor2_m.js?ver=<?php echo time(); ?>" charset="utf-8"></script>

		<script type="text/javascript">
			var od_url = '<?php echo $og_url; ?>';
			var og_type = '<?php echo ($og_type?$og_type:null); ?>';
			var og_title = '<?php echo ($og_title?$og_title:null); ?>';
			var og_description = '<?php echo ($og_description?rm_enter($og_description):null); ?>';
			var og_url = '<?php echo ($og_url?$og_url:null); ?>';
			var og_site_name = '<?php echo ($og_site_name?$og_site_name:null); ?>';
			var og_image = '<?php echo ($og_image?$og_image:null); ?>';
			var og_app_id = '<?php echo ($og_app_id?$og_app_id:null); ?>';

			$(function() { $('.ellipsis').dotdotdot(); });

			<?php if(isset($siteInfo['s_facebook_key'])) { echo PHP_EOL; // 페이스북 앱 처리 ?>
			window.fbAsyncInit = function() { FB.init({ version: 'v2.12', appId: '<?php echo $siteInfo['s_facebook_key']; ?>', xfbml: true, status: true, cookie: true }); };
			$(window).bind('load', function() { (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if(d.getElementById(id)) {return;} js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/ko_KR/all.js"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk')); });
			function postToFeed(title, desc, url, image){ FB.ui({method: 'feed',link: url, picture: image, name: title,description: desc}, function() {}); }
			function postToFeedLayer(title, desc, url, image){ FB.ui({method: 'feed', display: 'popup', link: url, picture: image, name: title,description: desc}, function() {}); }
			<?php echo PHP_EOL; } // 페이스북 앱 처리 ?>
		</script>

		<script>
			$(document).ready(function(){
				// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---
				var clipboard = new Clipboard('.js-clipboard');
				clipboard.on('success', function(e) {
					var type = $(e.trigger).data('clipboard-type');
					if(type == 'url'){
						alert("URL이 복사되었습니다.\n원하는 곳에 붙여넣기 하실 수 있습니다.");
					}else{
						alert("계좌번호가 복사되었습니다.\n원하는 곳에 붙여넣기 하실 수 있습니다.");
					}
					e.clearSelection();
				});
				clipboard.on('error', function(e) {
					alert("복사기능이지원되지 않습니다.\n직접 선택복사해주세요");
				});
				// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---
			});
		</script>

		<?php if($siteInfo['npay_use'] == 'Y' && trim($siteInfo['npay_all_key']) != '') { echo PHP_EOL; // 네이버 유입경로 ?>
		<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script>
		<script type="text/javascript">
			if(!wcs_add) var wcs_add = {};
			wcs_add["wa"] = "<?php echo $siteInfo['npay_all_key']; ?>";

			// 체크아웃 whitelist가 있을 경우
			//wcs.checkoutWhitelist = ["aaa.com", "bbb.com"];
			// 유입 추적 함수 호출
			wcs.checkoutWhitelist = ["www.<?php echo str_replace(array('http:', 'https://', 'www.'), '', reset(explode(':', $_SERVER['HTTP_HOST']))); ?>", "<?php echo str_replace(array('http:', 'https://', 'www.'), '', reset(explode(':', $_SERVER['HTTP_HOST']))); ?>"];
			wcs.inflow("<?php echo str_replace('www.', '', reset(explode(':', $_SERVER["HTTP_HOST"]))); ?>");
			wcs_do();
		</script>
		<?php } ?>

		<?php if(count($NWChanel) > 0) { // 네이버 검색 연관채널 ?>
		<script type="application/ld+json">
			{
				"@context": "http://schema.org",
				"@type": "Person",
				"name": "<?php echo $og_title; ?>",
				"url": "<?php echo $og_url; ?>",
				"sameAs": [
					<?php foreach($NWChanel as $nwck=>$nwcv) { ?>
					<?php echo ($nwck>0?',':''); ?>"<?php echo $nwcv; ?>"
					<?php } echo PHP_EOL; ?>
				]
			}
		</script>
		<?php } ?>

		<?php echo $siteInfo['s_gmeta'].PHP_EOL; // 메타태그 출력 - 사용자 오류에 영향을 덜 받도록 이위치 고수 ?>
		<?php actionHook('header_insert'); // <head>~</head> 사이 후킹을 이용하여 스크립트, css, 메타등을 추가 해야 하는경우 사용 ?>

	</head>
<body class="<?php echo in_array($pn, array('','main')) == true ? ' if_main':'' ?><?php echo in_array($pn, array('product.view')) == true ? ' if_view':'' ?><?php echo in_array($pn, array('shop.order.form')) == true ? ' if_shop_order':'' ?>">


<?php
if($_COOKIE['AuthPopupClose_topbanner'] != 'Y'){
    $TopBanner = info_banner($_skin.',main_top,set_color,set_img_after', 1, 'data');
    $TB_info = $TopBanner[0];

    $_img_mo = $TB_info['b_img_mo'] ?IMG_DIR_BANNER_URL.$TB_info['b_img_mo']:IMG_DIR_BANNER_URL.$TB_info['b_img'];
    if(count($TopBanner)>0){
        ?>
        <?php // 최상단 배너 ?>
        <div class="p_Topbn<?php echo in_array($pn, array('','main')) == true ? ' if_main':'' ?> tg-mainTopbannerWrap" style="background-color: <?php echo $TB_info['b_color'];?>;">
            <div class="banner">
                <?php if($TB_info['b_target'] != '_none' && isset($TB_info['b_link'])) { ?>
                    <a href="<?php echo $TB_info['b_link']; ?>" target="<?php echo $TB_info['b_target']; ?>"  class="upper_link" ></a>
                <?php } ?>

                <?php // pc용 배너 ?>
                <img src="<?php echo IMG_DIR_BANNER_URL.$TB_info['b_img']; ?>" class="img_pc" alt="<?php echo addslashes($TB_info['b_title']) ?>" />
                <?php // 모바일용 배너 : 모바일 이미지 등록안하면 pc 이미지 계속 노출됨 ?>
                <img src="<?php echo $_img_mo; ?>" class="img_mo" alt="<?php echo addslashes($TB_info['b_title']) ?>" />
            </div>
            <a href="#none" class="btn_close tg-mainTopbannerClose" onclick="return false;" title="오늘하루 닫기"></a>
        </div><!-- end p_Topbn -->
        <script>
            // [LCY] 2020-03-02 -- 추가작업 적용 --
            $(document).on('click','.tg-mainTopbannerClose',function(){
                $.ajax({
                    url: '/program/member.login.pro.php',
                    type: 'get',
                    dataType:'json',
                    data:{_mode:'site_main_top_banner_close'}
                }).done(function(response){
                    $('.tg-mainTopbannerWrap').slideUp(1000);
                }).fail(function( jqXHR, textStatus ) {
                    console.log(jqXHR.status);
                })
            });
        </script>
    <?php } ?>
<?php } ?>


<div class="wrap js_header_position post_hide_section" name="topPosition"  id="cert_info"><?php // 본인확인 추가 ?>
