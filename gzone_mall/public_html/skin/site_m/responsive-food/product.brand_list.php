<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit">BRAND</div>
        </div>
	</div>
</div><!-- end c_page_tit -->


<div class="c_section c_brand">

	<div class="word_list">
		<div class="layout_fix">
			<ul>
				<li><a href="#none" class="btn all <?php echo $brand_prefix != 'all' ? '' : 'hit'?>" data-key="all">ALL</a></li>
				<?php
					// 한글
					foreach( $arr_prefix_kor as $k=>$v ){
						echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<li><a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a></li>' : '<li><span class="btn none">'. $v .'</span></li>') ;
					}
					// 기타
					echo (sizeof($arr_brand_prefix['기타']) > 0 ? '<li><a href="#none" class="btn all '. ($brand_prefix == '기타' || $brand_prefix == 'etc'  ? 'hit' : '') .'" data-key="etc">기타</a></li>' : '<li><span class="btn none">기타</span></li>') ;
				?>
			</ul>
			<ul>
				<?php
					// 영문
					foreach( $arr_prefix_eng as $k=>$v ){
						echo (sizeof($arr_brand_prefix[$v]) > 0 ? '<li><a href="#none" class="btn '. ($brand_prefix == $v ? 'hit' : '') .'" data-key="'. $v .'">'. $v .'</a></li>' : '<li><span class="btn none">'. $v .'</span></li>') ;
					}
				?>
			</ul>
		</div>
	</div><!-- end word_list -->

	<?php // 해당 단어 브랜드 목록 ?>
	<div class="brand_list">
		<div class="layout_fix menu" id="brand_menu_box">
			<?php include OD_PROGRAM_ROOT."/product.brand_ajax.menu_box.php"; ?>
		</div>
	</div>

	<script>
		// 아이스크롤 스크립트(스와이프)
		// js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
		var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_menu_brand'), swipe_Scroll_brand = '';

		// 스와이프 적용
		$(document).ready(function(){
			swipe_Menu_brand/*숫자만변경*/();
		});

		function swipe_Menu_brand/*숫자만변경*/(){
			$.each($('#js_swipe_menu_brand li'), function(k, v){  scrollWidth += $('#js_swipe_menu_brand li').eq(k).outerWidth()*1; });
			var len = $('#js_swipe_menu_brand li').length;
			swipe_Scroll_brand/*숫자만변경*/ = new IScroll('#js_swipe_menu_brand', {
				'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
				, mouseWheel: false
			});

			if(scrollIndex > 0 && $('#js_swipe_menu_brand li.hit').length > 0) {
				var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_menu_brand li.hit').outerWidth()*1/2 ) * -1;
				swipe_Scroll_brand/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_menu_brand li.hit'), 500, scrollOffset);
			}
		}
	</script>


	<script type="text/javascript">
		// 브랜드 클릭
		$(document).on('click', '.word_list ul li a.btn', function(e) {
			e.preventDefault();
			var brand = $(this).data("key");
			$(".word_list ul li a.btn").removeClass("hit");// 전체 브랜드 hit 풀기
			$(this).addClass("hit");// 선택 브랜드 hit 적용
			// 클릭 브랜드 - box 불러오기
			$.ajax({
				data: {brand: brand},
				type: 'POST',
				cache: false,
				url : "<?php echo OD_PROGRAM_URL; ?>/product.brand_ajax.menu_box.php" ,
				success: function(data) {
					$("#brand_menu_box").html(data);
					
					//history.replaceState(null,null,"/?pn=product.brand_list&brand="+brand+"&uid=<?php echo $uid ?>");
					location.href="/?pn=product.brand_list&brand="+brand;
					if(brand =='all'){
						//location.href="/?pn=product.brand_list ";
					}
				}
			});
		});
	</script>
    <?php
    // 브랜드가 선택된 경우만 노출
    if( $brand_prefix ) {
        ?>

        <?php
			$list_type_moclass = $list_type_pcclass = $list_type_class = '';
			if($list_type == 'list' ||($list_type=='' && $siteInfo['s_brand_display_type']=='list') ){
				$list_type_class = 'if_list_type';
				$list_type='list';
			}else{
				$list_type='box';
			}

			if($siteInfo['s_brand_display_type']==$list_type && $list_type!=''){
				if($list_type=='list'){
					$list_type_pcclass = 'pc_type_list'.$siteInfo['s_brand_display'];
					$list_type_moclass = 'mobile_type_list'.$siteInfo['s_brand_mobile_display'];
				}else{
				$list_type_pcclass = 'pc_type_box'.$siteInfo['s_brand_display'];
					$list_type_moclass = 'mobile_type_box'.$siteInfo['s_brand_mobile_display'];
				}
			}


            if($search_mode=='Y' || count($p_res)>0){
                include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
            }
        ?>

        <?php
			
            $res = $p_res;
            include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
        ?>

        <?php if(count($p_res)>0){?>
            <?php // 페이지네이트 ?>
            <div class="c_pagi">
                <div class="layout_fix">
                    <?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
                </div>
            </div><!-- end c_pagi -->
        <?php }?>

    <?php } else { ?>
        <div class="c_none">
            <div class="gtxt">브랜드를 선택해주세요.</div>
        </div>
    <?php } ?>


</div><!-- end c_section -->

