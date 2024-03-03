<?php
	// -- 타임세일 상품을 가져온다.
	$resTimesale = _MQ_result("select count(*) as cnt from smart_product where p_view='Y' and p_time_sale='Y'");
	if(count($resTimesale) > 0) {
?>


<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $siteInfo['s_main_timesale_title'];?></div>
        </div>
	</div>
</div><!-- end c_page_tit -->

<div>
    <?php
        $_event = 'product_timesale';
        $_list_type = 'ajax.product_timesale';
		$view_type='list';
        include(OD_PROGRAM_ROOT.'/product.list.php');
    ?>
</div>

<?php } // 타임세일 상품이 노출일경우 ?>
