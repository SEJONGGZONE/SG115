<?php
// -- md 상품을 가져온다. smart_display_main_set
$resMd = _MQ_assoc("
	select
		dms2.*  ,
		dms1.dms_view as dms1_view , dms1.dms_name as dms1_name ,
		(select count(*) from smart_product as p INNER join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) where dmp_dmsuid = `dms2`.`dms_uid` ) as cnt
	from `smart_display_main_set` as dms2
	INNER JOIN `smart_display_main_set` as dms1 ON ( `dms1`.`dms_type` =  `dms2`.`dms_type` and `dms1`.`dms_depth` = '1' and `dms1`.`dms_view` = 'Y')
	where
		`dms2`.`dms_type` = 'md' and `dms2`.`dms_depth` = '2' and `dms2`.`dms_view` = 'Y'
	having cnt > 0
	order by `dms2`.`dms_idx` asc
");
$firstUid = $firstUid;
if(count($resMd) > 0) {
    ?>


<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo stripslashes($resMd[0]['dms1_name']) ?></div>
        </div>
	</div>
</div><!-- end c_page_tit -->



<div class="p_Category type_other_list">
	<div class="ctg2_box">
		 <div class="layout_fix">
			<div class="swipe_box" ID="js_cate2depth_iscroll">
			   <ul class="js_cate2depth_ul">
					<?php foreach($resMd as $mk=>$mv){ ?>
						<li class="js_list_md_li<?php echo ($firstUid == $mv['dms_uid']?' hit':null); ?>"><a href="#none" onclick="return false;" class="ctg2 js_list_md_tab" data-dmsuid="<?php echo $mv['dms_uid'] ?>"><span class="tx"><?php echo stripslashes($mv['dms_name']);  ?></span></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<script>
		// KAY :: 2022-12-12 :: 아이스크롤 스크립트
		// ex ) cate2depth_iscroll => ID 로 변경
		var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_cate2depth_iscroll'), Cate2depthScroll = '';

		// 스와이프 적용
		$(document).ready(function(){
			func_cate2depth_iscroll();
		});

		// 사용시 ID로 지정후 해당 ID를 변경
		// ex ) #js_cate2depth_iscroll=> #ID 로 변경
		function func_cate2depth_iscroll(){

			$.each($('#js_cate2depth_iscroll li'), function(k, v){  scrollWidth += $('#js_cate2depth_iscroll li').eq(k).outerWidth()*1; });
			var len = $('#js_cate2depth_iscroll li').length;
			Cate2depthScroll = new IScroll('#js_cate2depth_iscroll', {
				'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
				, mouseWheel: true
			});

			if(scrollIndex > 0 && $('#js_cate2depth_iscroll li.hit').length > 0) {
				var scrollOffset = ($(window).width()*1/2 - $('#js_cate2depth_iscroll li.hit').outerWidth()*1/2 ) * -1;
				Cate2depthScroll.scrollToElement(document.querySelector('#js_cate2depth_iscroll li.hit'), 500, scrollOffset);
			}
		}
	</script>
</div><!-- end p_Category -->

<div class="js_list_md">
    <?php
        $dmsuid = $firstUid;
        $_event = 'main_md';
        $_list_type = 'ajax.product_md';
		$view_type='list';
        include(OD_PROGRAM_ROOT.'/product.list.php');
    ?>
</div><!-- end p_Md -->


<script type="text/javascript">
    $(document).on('click', '.js_list_md_tab', function(e) {
        e.preventDefault();
        var dmsuid = $(this).data('dmsuid');
        $('.js_list_md_li').removeClass('hit');
        $(this).closest('.js_list_md_li').addClass('hit');
        mainMdTab();
    });


    function mainMdTab() {
        var dmsuid = $('.js_list_md_li.hit').find('a').data('dmsuid');
        $.ajax({
            data: {
                dmsuid: dmsuid,
                _event: 'main_md',
                _list_type: 'ajax.product_md',
            },
            type: 'get',
            cache: true,
            url: '<?php echo OD_PROGRAM_URL; ?>/product.list.php',
            success: function(data) {
                if(typeof MainMdSlideDes != 'undefined') MainMdSlideDes();
                $('.js_list_md').html(data);
            }
        });
    }
</script>

<?php } // md가 노출일경우 ?>
