<?php
	include_once('wrap.header.php');
	$pass_menu = ($pass_menu ? $pass_menu : 'day'); // 출력모드

	$_addons_menu = array(
		"일자별" => "day",
		"시간별" => "hour",
		"요일별" => "week",
		"월별" => "month",
	);
?>

<!-- 탭메뉴 -->
<div class="c_tab type_fill">
	<ul>
		<?php
			foreach($_addons_menu as $k=>$v){
				echo '<li ' . ($pass_menu == $v ? ' class="hit"':null) . '><a href="_cntlog.php?pass_menu='. $v .'" class="btn"><strong>'. $k .'</strong></a></li>';
			}
		?>
	</ul>
</div>
<!-- / 탭메뉴 -->

<?php

	# 메뉴형태 => 파일명
	if($pass_menu) {
		// ----- JJC : 하이앱 : 2020-04-22 : 앱정보 추가 -----

		include_once("_cntlog." . $pass_menu . ".php");
		
	}
	include_once('wrap.footer.php');

?>