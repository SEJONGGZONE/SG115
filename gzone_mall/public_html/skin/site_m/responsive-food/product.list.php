<?php include_once(OD_PROGRAM_ROOT.'/product.top_nav.php'); // 상단 네비게이션 출력 ?>


<?php
if(
    $category_info['c_img_top_mobile_banner_use'] == 'Y' &&
    !empty($category_info['c_img_top_mobile_banner']) &&
    file_exists(IMG_DIR_CATEGORY_ROOT.$category_info['c_img_top_mobile_banner'])
) {
    ?>
    <?php // 카테고리별 상단 배너 ?>
    <div class="p_Subimg">
        <div class="layout_fix">
            <?php if($category_info['c_img_top_mobile_banner_target'] != '_none' && $category_info['c_img_top_mobile_banner_link']) { ?><a href="<?php echo $category_info['c_img_top_mobile_banner_link']; ?>" target="<?php echo $category_info['c_img_top_mobile_banner_target']; ?>"><?php } ?>
                <img src="<?php echo IMG_DIR_CATEGORY_URL.$category_info['c_img_top_mobile_banner']; ?>" alt="<?php echo $ActiveCate['cname'][0]; ?>" />
            <?php if($category_info['c_img_top_mobile_banner_target'] != '_none' && $category_info['c_img_top_mobile_banner_link']) { ?></a><?php } ?>
        </div>
    </div><!-- end p_Topbn -->
<?php } ?>

<?php
// LCY : 2023-08-11 : 타입별 카테고리 상단 배너 사용자 노출 적용패치
if($_event == 'type' && $typeuid ){
	$displayTypeInfo = _MQ(" select * from `smart_display_type_set` where (1) and dts_uid = '".$typeuid."' ");

if(
    $displayTypeInfo['dts_img_top_banner_use'] == 'Y' &&
    !empty($displayTypeInfo['dts_img_top_banner']) &&
    file_exists(IMG_DIR_CATEGORY_ROOT.$displayTypeInfo['dts_img_top_banner'])
) {
    ?>
    <?php // 타입별 상단 배너 ?>
    <div class="p_Subimg">
        <div class="layout_fix">
            <?php if($displayTypeInfo['dts_img_top_banner_target'] != '_none' && $displayTypeInfo['dts_img_top_banner_link']) { ?><a href="<?php echo $displayTypeInfo['dts_img_top_banner_link']; ?>" target="<?php echo $displayTypeInfo['dts_img_top_banner_target']; ?>"><?php } ?>
                <img src="<?php echo IMG_DIR_CATEGORY_URL.$displayTypeInfo['dts_img_top_banner']; ?>" alt="<?php echo $displayTypeInfo['dts_name']; ?>" />
            <?php if($displayTypeInfo['dts_img_top_banner_target'] != '_none' && $displayTypeInfo['dts_img_top_banner_link']) { ?></a><?php } ?>
        </div>
    </div><!-- end p_Topbn -->
<?php } } ?>



<?php if(count($Category2Depth) > 0) { // 2차 카테고리가 있는 경우 노출 ?>
	<div class="p_Category">
		<div class="ctg2_box">
			<div class="layout_fix">
				<div class="swipe_box"  ID="js_cate2depth_iscroll">
					<ul class="js_cate2depth_ul">
						<li class="<?php echo ($ActiveCate['cuid'][0] == $cuid?'hit':null); ?>">
							<a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $ActiveCate['cuid'][0]; ?>" class="ctg2">전체</a>
						</li>
						<?php foreach($Category2Depth as $k=>$v) { ?>
							<li class="<?php echo ($ActiveCate['cuid'][1] == $v['c_uid']?'hit':null); ?>">
								<a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $v['c_uid']; ?>" class="ctg2"><?php echo $v['c_name']; ?></a>
							</li>
						<?php } ?>
					</ul>
				</div><!-- end swip_box -->
			</div>
		</div><!-- end ctg2_box -->
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

		<?php if(count($Category3Depth)>0){ // 3차 카테고리가 있는 경우 노출 ?>
			<div class="ctg3_box">
				<div class="layout_fix">
					<div class="swipe_box" ID="js_cate3depth_iscroll">
						<ul>
							<li class="<?php echo ($ActiveCate['cuid'][1] == $cuid?' hit':null); ?>"><a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $ActiveCate['cuid'][1]; ?>" class="ctg3"><?php // echo $ActiveCate['cname'][1];?>전체</a></li>
							<?php foreach($Category3Depth as $kk=>$vv){?>
								<li class="<?php echo ($ActiveCate['cuid'][2] == $vv['c_uid']?' hit':null); ?>"><a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $vv['c_uid']; ?>" class="ctg3"><?php echo $vv['c_name']; ?></a></li>
							<?php }?>
						</ul>
					</div>
				</div>
			</div><!-- end ctg3_box -->
			<script>
				// KAY :: 2022-12-12 :: 아이스크롤 스크립트
				// ex ) cate2depth_iscroll => ID 로 변경
				var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_cate3depth_iscroll'), Cate2depthScroll = '';

				// 스와이프 적용
				$(document).ready(function(){
					func_cate3depth_iscroll();
				});

				// 사용시 ID로 지정후 해당 ID를 변경
				// ex ) #js_cate2depth_iscroll=> #ID 로 변경
				function func_cate3depth_iscroll(){

					$.each($('#js_cate3depth_iscroll li'), function(k, v){  scrollWidth += $('#js_cate3depth_iscroll li').eq(k).outerWidth()*1; });
					var len = $('#js_cate3depth_iscroll li').length;
					Cate3depthScroll = new IScroll('#js_cate3depth_iscroll', {
						'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
						, mouseWheel: true
					});

					if(scrollIndex > 0 && $('#js_cate3depth_iscroll li.hit').length > 0) {
						var scrollOffset = ($(window).width()*1/2 - $('#js_cate3depth_iscroll li.hit').outerWidth()*1/2 ) * -1;
						Cate3depthScroll.scrollToElement(document.querySelector('#js_cate3depth_iscroll li.hit'), 500, scrollOffset);
					}
				}
			</script>
		<?php }?>
	</div><!-- end p_Category -->
<?php } ?>




<?php
// 베스트 아이템
if($category_info['c_best_product_mobile_view'] == 'Y' && isset($cuid)) {
    /* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
    $BestItem = _MQ_assoc("
        select
            *
            ,if(p_soldout_chk='N',p_stock,0) as p_stock
        from
            `smart_product` as p left join
            `smart_product_category_best` as pcb on(p.p_code = pcb.pctb_pcode)
        where (1) and
            p_view = 'Y' and p_option_valid_chk = 'Y' and
            pctb_cuid = '{$cuid}'
        order by
            pctb_idx asc
    ");
    if(count($BestItem) > 0) {

		$list_type_class = $list_type_pcclass = $list_type_moclass=  '';
		if($category_info['c_best_product_display_type']=='box'){
			$list_type_pcclass = 'pc_type_box'.$category_info['c_best_product_display'];
			$list_type_moclass = 'mobile_type_box'.$category_info['c_best_product_mobile_display'];
		}else{
			$list_type_pcclass = 'pc_type_list'.$category_info['c_best_product_display'];
			$list_type_moclass = 'mobile_type_list'.$category_info['c_best_product_mobile_display'];
			$list_type_class = 'if_list_type';
		}

		$item_list_view_total = count($BestItem);
		// 처음에 보여질 개수
		$item_list_pc_view_cnt = $category_info['c_best_product_display'];
		$item_list_mo_view_cnt = $category_info['c_best_product_mobile_display'];

?>
    <?php // 카테고리 베스트 ?>
    <div class="p_Best">
        <div class="layout_fix">
            <div class="best_tit">
                <div class="tit">Best Seller</div>

                <?php // 롤링 컨트롤러(롤링일때만 노출) ?>
				<?php // 베스트 4단으로 시작?>
				<?php $best_total_page = count($BestItem)<10?'0'.count($BestItem):count($BestItem);?>
				<div class="rolling_ctrl js_list_best_ctrl <?php echo $item_list_view_total > $item_list_view_cnt?'if_show':'if_hide';?>">
					<a href="#none" class="prevnext prev js_list_best_prevnext" onclick="return false;"  data-type="prev"  title="이전"></a>
					<?php // 롤링 페이징 ?>
					<span class="pagi js_list_best_pagi">
						<strong class="active js_list_best_pagenum">01</strong><em>/</em><strong><?php echo $best_total_page;?></strong>
					</span>
					<a href="#none" class="prevnext next js_list_best_prevnext" onclick="return false;" data-type="next" title="다음"></a>
				</div>

            </div>

            <?php // 롤링 박스 ?>
            <div class="rolling_wrap">
                <div class="rolling_box">
                    <div class="item_list js_product_best_slide <?php echo $list_type_pcclass;?> <?php echo $list_type_moclass;?> <?php echo $list_type_class?>">
                        <ul class="swiper-wrapper">
                            <?php
                            foreach($BestItem as $bi_k=>$bi_v) {
                            ?>
                                <li class="swiper-slide">
                                    <?php
                                        // $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
                                        $v = $bi_v; $k = $bi_k;
                                        $best_use = 'Y';
                                        $locationFile = basename(__FILE__); // 파일설정
                                        include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div><!-- end rolling_wrap -->


			<script type="text/javascript">
				// 최초 슬라이드 넓이 조정 후 슬라이드 생성
				var swiper_pro_best_cate;
				var swiper_pro_best_cate_index = 0; // swiper 떨림현상

				$(document).ready(function(){
					pro_cate_rolling();
				});

				$(window).resize(function(e){
					pro_cate_rolling();
				});

				function pro_cate_rolling(){

					// 반응형
					var list_totalCnt = '<?php echo $item_list_view_total;?>';			// 상품개수
					var list_pc_view =  '<?php echo $item_list_pc_view_cnt;?>';				
					var list_mo_view =  '<?php echo $item_list_mo_view_cnt;?>';				
					var list_chkwidth = window.innerWidth;

					if( list_chkwidth <= 700){ list_view = list_mo_view;}
					else if( list_chkwidth > 700 && list_chkwidth <= 900){ list_view = list_pc_view>=3?3:list_pc_view; }
					else if( list_chkwidth > 900 && list_chkwidth <= 1100){ list_view = list_pc_view>=4?4:list_pc_view; }
					else if( list_chkwidth > 1100 && list_chkwidth <= 1300){ list_view = list_pc_view>5?5:list_pc_view;}
					else if( list_chkwidth > 1300){ list_view = list_pc_view;}

					// swiper 떨림현상
					if( typeof swiper_pro_best_cate == 'object'){
						swiper_pro_best_cate.destroy(true,true);
						swiper_pro_best_cate = false;
					}


					// 롤링 컨트롤러는 클래스로 제어
					if(parseInt(list_view)>=parseInt(list_totalCnt)){
						$('.js_list_best_ctrl').removeClass('if_show').addClass('if_hide');
						return false;
					}else{
						$('.js_list_best_ctrl').removeClass('if_hide').addClass('if_show');
					}


					swiper_pro_best_cate = new Swiper('.js_product_best_slide', {
						effect: 'slide',
						slidesPerView: list_view,
						autoplay : {
							pause:4000,
							disableOnInteraction : false,
						},
						speed: 500,
						parallax:false,
						loop : true,
						loopedSlides : list_totalCnt ,
						touchRatio:1,
						spaceBetween: 0,
						paginationClickable : false,
						roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
						initialSlide:swiper_pro_best_cate_index, // swiper 떨림현상
					});

					// 슬라이드 변경시 실행되는 이벤트
					swiper_pro_best_cate.on('slideChangeTransitionStart', function() {
						// 롤링 페이지 '1' 한자리수 형태
						var pagenum = swiper_pro_best_cate.realIndex+1;
						pagenum = pagenum<10?'0'+pagenum:pagenum;
						$('.js_list_best_pagenum').text(pagenum);

						swiper_pro_best_cate_index = swiper_pro_best_cate.realIndex;
					});

				}


				// 롤링아이콘 클릭시 해당 슬라이드로 이동
				$(document).on('click', '.js_list_best_prevnext', function(e) {
					var list_data = $(this).data();

					if(list_data.type=='next'){
						swiper_pro_best_cate.slideNext();
					}else{
						swiper_pro_best_cate.slidePrev();
					}
				});

			</script>
        </div>
    </div><!-- end p_Best -->
<?php }} // 베스트 아이템 ?>



<?php

	// 이벤트가 타입일경우 기본 진열을가져온다.
	if($_event == 'type'){
		$displayTypeInfo = _MQ(" select * from `smart_display_type_set` where (1) and dts_uid = '".$typeuid."' ");

		$list_type_pcclass = $list_type_moclass = $list_type_class= '';

		if($list_type == 'list' ||($list_type=='' && $displayTypeInfo['dts_list_product_display_type']=='list') ){
			$list_type_class = 'if_list_type';
			$list_type='list';
		}else{
			$list_type='box';
		}


		if($displayTypeInfo['dts_list_product_display_type']==$list_type && $list_type!=''){
			if($list_type=='list'){
				$list_type_pcclass = 'pc_type_list'.$displayTypeInfo['dts_list_product_display'];
				$list_type_moclass = 'mobile_type_list'.$displayTypeInfo['dts_list_product_mobile_display'];
			}else{
				$list_type_pcclass = 'pc_type_box'.$displayTypeInfo['dts_list_product_display'];
				$list_type_moclass = 'mobile_type_box'.$displayTypeInfo['dts_list_product_mobile_display'];
			}
		}
	}else{
		$list_type_pcclass = $list_type_moclass = $list_type_class= '';

		if($list_type == 'list' ||($list_type=='' && $category_info['c_list_product_display_type']=='list') ){
			$list_type_class = 'if_list_type';
			$list_type='list';
		}else{
			$list_type='box';
		}


		if($category_info['c_list_product_display_type']==$list_type && $list_type!=''){
			if($list_type=='list'){
				$list_type_pcclass = 'pc_type_list'.$category_info['c_list_product_display'];
				$list_type_moclass = 'mobile_type_list'.$category_info['c_list_product_mobile_display'];
			}else{
				$list_type_pcclass = 'pc_type_box'.$category_info['c_list_product_display'];
				$list_type_moclass = 'mobile_type_box'.$category_info['c_list_product_mobile_display'];
			}
		}
	}

    if(count($res)>0 || $search_mode=='Y'){
        include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
    }

    // 상품리스트 호출
    $best_use = 'N';
    include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
?>


<?php if(count($res)>0 ){?>
    <?php // 페이지네이트(상품없으면 비노출) ?>
    <div class="c_pagi">
        <div class="layout_fix">
            <?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
        </div>
    </div><!-- end c_pagi -->
<?php }?>