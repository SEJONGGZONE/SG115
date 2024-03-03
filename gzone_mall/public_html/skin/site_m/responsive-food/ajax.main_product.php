<?php
	// -- 해당 메인 상품의 설정을 가져온다.
	$itemMain = _MQ("select *from `smart_display_main_set` where `dms_type` = 'main' and `dms_depth` = '2' and `dms_view` = 'Y' and `dms_uid` = '".$dmsuid."'
	and `dms_list_product_mobile_view` = 'Y'");

	// -- 검색된 값이 없거나 상품이 노출이 아닐 시
	if(count($res) <= 0 || count($itemMain) < 1 ) {
		echo '
			<div class="item_list">
				<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
			</div>
		';
		return;
	}

	$main_pro_pcclass = $main_pro_moclass = $list_type_class = '';
	if($itemMain['dms_list_product_display_type']=='box'){
		$main_pro_pcclass = 'pc_type_box'.$itemMain['dms_list_product_display'];
		$main_pro_moclass = 'mobile_type_box'.$itemMain['dms_list_product_mobile_display'];
	}else{
		$main_pro_pcclass = 'pc_type_list'.$itemMain['dms_list_product_display'];
		$main_pro_moclass = 'mobile_type_list'.$itemMain['dms_list_product_mobile_display'];
		$list_type_class = 'if_list_type';
	}
?>

<div class="item_list <?php echo $main_pro_pcclass;?> <?php echo $main_pro_moclass;?> <?php echo $list_type_class;?>">
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
