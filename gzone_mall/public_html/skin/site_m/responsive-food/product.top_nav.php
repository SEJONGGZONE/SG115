<?php
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$dp1cate -> 1차 카테고리 전체=> (/program/wrap.header.php에서 지정)
	$ActiveCate -> 현재카테고리의 정보를 반환<1~3차> => (/program/wrap.header.php에서 지정)
	$Category2Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
	$Category3Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
*/
?>
<?php
if($_event) { // 이벤트 페이지에서 노출(검색 네비와 같은 구조)
	$type1depth = _MQ(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_depth='1' order by dts_idx asc ");
	$type2depth = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_mobile_view = 'Y' and dts_depth='2' order by dts_idx asc ");

?>
<?php // 타입별 상품목록 ?>
<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $type1depth['dts_name'];?></div>
        </div>
	</div>
</div><!-- end c_page_tit -->


<?php if(count($type2depth) > 0) { ?>
	<div class="p_Category type_other_list">
		<div class="ctg2_box">
			<div class="layout_fix">
				<div class="swipe_box" ID="js_cate2depth_iscroll">
					<ul class="js_cate2depth_ul">
						<?php foreach($type2depth as $epk=>$epv) { ?>
							<li<?php echo ($epv['dts_uid'] == $typeuid?' class="hit"':null); ?>>
								<a href="/?pn=product.list&_event=type&typeuid=<?php echo $epv['dts_uid']; ?>" class="ctg2"><?php echo $epv['dts_name']; ?></a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div><!-- end ctg2_box -->
	</div><!-- end p_Category -->
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
<?php } ?>


<?php // 일반 상품목록 타이틀 ?>
<?php } else { ?>
	<div class="p_Subtop">
        <div class="tit_box layout_fix">
            <span class="tit"><?php echo $ActiveCate['cname'][0]; ?></span>
			<a href="#none" onclick="return false;" class="btn_open js_onoff_event" data-target=".p_Subtop" data-add="if_open_ctg"></a>
        </div>

		<div class="open_ctg_box">
			<div class="layout_fix">
				<ul>
					<?php foreach($AllCate as $k=>$v) { ?>
						<li class="li <?php echo (($v['c_uid']==$cuid) || $ActiveCate['cuid'][0]==$v['c_uid']?'hit':null); ?>"><a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg1"><?php echo $v['c_name']; ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div><!-- end p_Subtop -->
<?php } ?>

