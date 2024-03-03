<?php
$list_timesale_pcclass = $list_timesale_moclass = $list_type_class= '';

if($list_type == 'list' ||($list_type=='' && $siteInfo['s_list_timesale_display_type']=='list') ){
	$list_type_class = 'if_list_type';
	$list_type='list';
}else{
	$list_type='box';
}


if($siteInfo['s_list_timesale_display_type']==$list_type && $list_type!=''){
	if($list_type=='list'){
		$list_timesale_pcclass = 'pc_type_list'.$siteInfo['s_list_timesale_display'];
		$list_timesale_moclass = 'mobile_type_list'.$siteInfo['s_list_timesale_mobile_display'];
	}else{
	$list_timesale_pcclass = 'pc_type_box'.$siteInfo['s_list_timesale_display'];
		$list_timesale_moclass = 'mobile_type_box'.$siteInfo['s_list_timesale_mobile_display'];
	}
}

if($view_type!='main'){
	// 메인에서는 목록 컨트롤러 노출x
	include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
}

// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0 ) {
	echo '<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>';
	return;
}
?>


<div class="item_list <?php echo $list_timesale_pcclass; ?> <?php echo $list_timesale_moclass;?> <?php echo $list_type_class;?>">
	<div class="layout_fix">
		<ul>
			<?php foreach($res as $k=>$v) {?>
			<li>
				<?php
					// $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
					$locationFile = basename(__FILE__); // 파일설정
					include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
				?>
			</li>
		<?php } ?>
		</ul>
	</div>
</div><!-- end item_list -->

