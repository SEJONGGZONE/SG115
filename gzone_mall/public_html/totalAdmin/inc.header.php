<?php
include_once("inc.php");
AutoHTTPSMove('admin');

	// 서브운영자 권한 확인
	AdminMenuCheck();

	# 파비콘
	$Favicon = $siteInfo['s_favicon'];

?>
<!DOCTYPE HTML>
<html lang="ko">
<head>
	<title><?=$siteInfo['s_adshop']?> : 통합관리자</title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="robots" content="noindex"><!-- 관리자 모드 네이버 검색 등록 차단 -->

	<!-- 모바일브라우저 상단 색상 -->
	<meta content='#21212D' name='apple-mobile-web-app-status-bar-style'/><!-- iOS Safari -->
	<meta content='#21212D' name='msapplication-navbutton-color'/><!-- Windows Phone -->
	<meta content='#21212D' name='theme-color'/><!-- Chrome, Firefox, Opera -->

	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
	<link href="<?php echo OD_ADMIN_URL; ?>/css/setting.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/totalAdmin.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/responsive.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/customize.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" />
	<?php if(getChkIE()===true){?>
	<link href="<?php echo OD_ADMIN_URL; ?>/css/ie_fix.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // ie에서만 사용 ?>
	<?php }?>

	<link href="/include/js/jquery/jqueryui/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<link href="/include/js/tagEditor/jquery.tag-editor.css" rel="stylesheet" type="text/css">
	<link href="/include/js/tagEditor/admin.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $SkinData['skin_url']; ?>/css/editor.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css" /><?php // 에디터 css ?>
	<link href="/include/js/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">

	<?php if($Favicon) { echo PHP_EOL; // 파비콘 ?>
	<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="apple-touch-icon-precomposed" />
	<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="shortcut icon" type="image/x-icon"/>
	<?php echo PHP_EOL; } ?>


	<?php
		// ----- JJC : 하이앱 : 2020-04-22 : 앱정보 추가 -----
		// 앱작동일 경우에만 실행
		if($AppUseMode === true) {
			echo '<link href="/addons/app/totalAdmin/css/hyapp.css" rel="stylesheet" type="text/css">';
		}
		// ----- JJC : 하이앱 : 2020-04-22 : 앱정보 추가 -----
	?>


	<script src="/include/js/jquery-1.11.2.min.js"></script>
	<script src="/include/js/jquery.placeholder.js"></script>
	<script src="/include/js/jquery/jquery.lightbox_me.js"></script>
	<script src="/include/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="/include/js/jquery/jquery.validate.js"></script>
	<script src="/include/js/jquery/jquery.easing.1.3.js"></script>
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.caret.min.js"></script>
	<script src="/include/js/clipboard/clipboard.min.js"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.js"></script>
	<script src="/include/js/air-datepicker/js/datepicker.js?v=<?php echo time(); ?>"></script>
	<script src="/include/js/air-datepicker/js/i18n/datepicker.ko.js"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/common.js?v=<?=time()?>"></script>

	<!-- 네이버 스마트에디터2 추가-->
	<script type="text/javascript" src="/include/smarteditor2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
	<script type="text/javascript" src="/include/smarteditor2/smarteditor2.js" charset="utf-8"></script>

	<!-- iscroll -->
	<script src="/include/js/iscroll-master/build/iscroll-probe.js"></script><!-- jquery -->

	<script type="text/javascript">
	$(document).ready(function(){

		var $root = $('html, body');
		$(document).delegate('.scrollto','click',function() {
			var target = $(this).data('scrollto');
			$root.animate({

				scrollTop: $('[data-name="' + target + '"]').offset().top - 10
			}, 500, 'easeInOutCubic');
			return false;
		});

		// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---
		var clipboard = new Clipboard('.js-clipboard');
		clipboard.on('success', function(e) {
			alert("복사되었습니다.");
			e.clearSelection();
		});
		clipboard.on('error', function(e) {
			alert("복사기능이지원되지 않습니다.\n직접 선택복사해주세요");
		});
		// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---

	});
	</script>
</head>
<body<?php echo (isset($app_mode) && $app_mode == 'popup' ? ' style="min-width: auto;" ' : null); ?> class="<?php echo in_array($CURR_FILENAME, array('_ticket_confirm.php')) == true ? ' if_ticket_confirm':'' ?>">
<div class="wrap post_hide_section js-menu-container <?php echo in_array($CURR_FILENAME, array('_main.php')) == true ? ' if_main':'' ?>"<?php echo (isset($app_mode) && $app_mode == 'popup' ? ' style="padding-bottom: 0;" ' : null); ?>>

