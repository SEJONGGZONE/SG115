<?php
include_once('wrap.header.php');
// $_skin_list -> include/inc.path.php


// 사용중인 스킨을 가장 우선 순위로 노출
$_skin_list_tmp[$siteInfo['s_skin']] = $_skin_list[$siteInfo['s_skin']];
unset($_skin_list[$siteInfo['s_skin']]);
$_skin_list_tmp = array_merge($_skin_list_tmp, $_skin_list);
$_skin_list = $_skin_list_tmp;
unset($_skin_list_tmp);
?>


<div class="skin_list">
	<ul>
		<?php
		foreach($_skin_list as $k=>$v) {
			$SkinInfo = array();
			if(file_exists(OD_SKIN_ROOT.'/site_m/'.$k.'/skin.xml')) $SkinInfo = xml2array(file_get_contents(OD_SKIN_ROOT.'/site_m/'.$k.'/skin.xml'));
			if(!$SkinInfo['skin']['title']) $SkinInfo['skin']['title'] = $k;
		?>
			<li<?php echo ($k == $siteInfo['s_skin']?' class="skin_hit"':null); ?>>
				<span class="active_icon">현재 스킨</span>
				<div class="thumb">
					<?php echo (file_exists(OD_SKIN_ROOT.'/site_m/'.$k.'/thumb.png')?'<img src="'.OD_SKIN_URL.'/site_m/'.$k.'/thumb.png'.'" data-img="'.OD_SKIN_URL.'/site_m/'.$k.'/thumb.png'.'" alt="">':null); ?>
				</div>
				<div class="skin_info">
					<?php if($SkinInfo['skin']['date']) { ?>
					<?php } ?>
					<div class="name"><?php echo $SkinInfo['skin']['title']; ?></div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<?php
					/*
					<div class="info_tx">스킨등록일 : <?php echo $SkinInfo['skin']['date']; ?></div>
					*/
					?>
					<?php
					if($SkinInfo['skin']['info']) {
						$Info = explode(PHP_EOL, trim($SkinInfo['skin']['info']));
						if(count($Info) <= 0) $Info = array();
						echo '';
						foreach($Info as $kk=>$vv) {
							$vv = trim($vv);
							if(!$vv) continue;
							echo '<div class="info_tx">'.trim($vv).'</div>';
						}
					}
					?>
				</div>
				<div class="skin_ctrl">
					<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 line sky t5" target="_blank">미리보기</a>
					<?php if($k == $siteInfo['s_skin']) { ?>
						<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 t3 red line" onclick="alert('이미 적용된 스킨입니다.'); return false;">적용완료</a>
					<?php } else { ?>
						<a href="/?_pskin=<?php echo $k; ?>" class="c_btn h26 t3 red js_active_skin" data-skin="<?php echo $k; ?>">적용하기</a>
					<?php } ?>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>


<div class="tip_box">
	<?php echo _DescStr('반응형 스킨만 적용가능하고, 스킨 변경 시 배너를 다시 설정해야 합니다. (단, 이미 설정된 배너는 초기화 되지 않습니다.)', ''); ?>
</div>


<script type="text/javascript">
	$(document).delegate('.js_active_skin', 'click', function(e) {
		e.preventDefault();
		var _skin = $(this).data('skin');
		if(confirm('스킨 변경 시 각 상품 진열설정이 초기화됩니다.\n그래도 변경하시겠습니까?')==false){return false;}
		location.href = '_skin.pro.php?_mode=update&set_skin='+_skin;
	});
</script>
<?php include_once('wrap.footer.php'); ?>