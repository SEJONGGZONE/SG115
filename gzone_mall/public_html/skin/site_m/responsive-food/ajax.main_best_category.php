<?php

$BestCateInfo = _MQ("select * from `smart_category` where (1) and c_uid = '".$cuid."' and	c_depth = 1 and	c_view = 'Y' and	c_best_product_mobile_view = 'Y'order by c_idx asc ");

// 관리자에서 상품정렬 설정이 랜덤일경우
if($siteInfo['s_main_best_order']=='random'){
	shuffle($res);
}

$list_type_class = $list_type_moclass=$list_type_pcclass = '';
if($siteInfo['s_main_best_display_type']=='box'){
	$list_type_pcclass = 'pc_type_box'.$siteInfo['s_main_best_display'];
	$list_type_moclass = 'mobile_type_box'.$siteInfo['s_main_best_mobile_display'];
}else{
	$list_type_pcclass = 'pc_type_list'.$siteInfo['s_main_best_display'];
	$list_type_moclass = 'mobile_type_list'.$siteInfo['s_main_best_mobile_display'];
	$list_type_class = 'if_list_type';
}

?>


<div class="layout_fix">
	<?php
	if(count($res) <= 0) {
		echo '
			<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
		';
		return;
	}

	$cate_total_cnt = count($res);
	$cate_pc_max_cnt = $siteInfo['s_main_best_display'];
	$cate_mo_max_cnt = $siteInfo['s_main_best_mobile_display'];
	$cate_rolling_total_page = $cate_total_cnt<10?'0'.$cate_total_cnt:$cate_total_cnt;

	?>

    <div class="rolling_wrap this_item_rolling">
        <?php // 롤링 박스 ?>
        <div class="rolling_box">
            <div class="item_list js_best_cate <?php echo $list_type_pcclass;?> <?php echo $list_type_moclass;?>">
                <ul class="swiper-wrapper">
                    <?php
						foreach($res as $k=>$v) {
                    ?>
                        <li class="swiper-slide">
							<?php
								$locationFile = basename(__FILE__); // 파일설정
								include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
							?>
                        </li>
                    <?php } ?>
                </ul>
            </div><!-- end item_list -->
        </div><!-- end rolling_box -->

		<?php // 롤링 컨트롤러 ?>
		<div class="sc_Roll_ctrl js_cate_rolling_ctrl <?php echo $cate_total_cnt > $cate_max_cnt?'if_show':'if_hide';?>">
			<a href="#none" title="이전" onclick="return false;" class="roll_prevnext prev js_main_cate_prevnext" data-type="prev"></a>
			<div class="roll_icon">
				<ul>
					<?php foreach($res as $k => $v){?>
						<li class="<?php echo $k==0?'active':'';?> js_main_best_icon" data-index="<?php echo $k;?>">
							<a href="#none" onclick="return false;" class="icon"></a>
						</li>
					<?php }?>
				</ul>
			</div>
			<div class="roll_pagi">
				<span class="active js_main_cate_pagenum">01</span>
				<span class="under">/</span>
				<span class="all"><?php echo $cate_rolling_total_page;?></span>
			</div><!-- end rolling_pagi -->
			<a href="#none" title="다음" onclick="return false;" class="roll_prevnext next js_main_cate_prevnext" data-type="next"></a>
		</div><!-- end sc_Roll_ctrl -->
    </div><!-- end rolling_wrap -->


	<script type="text/javascript">
		// 최초 슬라이드 넓이 조정 후 슬라이드 생성
		var swiper_main_best_cate;
		var swiper_main_best_cate_index = 0; // swiper 떨림현상

		$(document).ready(function(){
			main_cate_rolling();
		});

		$(window).resize(function(e){
			main_cate_rolling();
		});

		function main_cate_rolling(){

			// 반응형
			var cate_chkwidth = window.innerWidth;
			var cate_totalCnt = '<?php echo count($res);?>';
			var cate_pc_view =  '<?php echo $cate_pc_max_cnt;?>';
			var cate_mo_view =  '<?php echo $cate_mo_max_cnt;?>';

			if( cate_chkwidth <= 700){ cate_view = cate_mo_view;}
			else if( cate_chkwidth > 700 && cate_chkwidth <= 900){ cate_view = cate_pc_view>=3?3:cate_pc_view; }
			else if( cate_chkwidth > 900 && cate_chkwidth <= 1100){ cate_view = cate_pc_view>=4?4:cate_pc_view; }
			else if( cate_chkwidth > 1100 && cate_chkwidth <= 1300){ cate_view = cate_pc_view>5?5:cate_pc_view;}
			else if( cate_chkwidth > 1300){ cate_view = cate_pc_view;}

			// swiper 떨림현상
			if( typeof swiper_main_best_cate == 'object'){
				swiper_main_best_cate.destroy();
				swiper_main_best_cate = false;
			}

			// 롤링 컨트롤러는 클래스로 제어
			if(parseInt(cate_view) >= parseInt(cate_totalCnt)){
				$('.js_cate_rolling_ctrl').removeClass('if_show').addClass('if_hide');
				return false;
			}else{
				$('.js_cate_rolling_ctrl').removeClass('if_hide').addClass('if_show');
			}

			swiper_main_best_cate = new Swiper('.js_best_cate', {
				effect: 'slide',
				slidesPerView: cate_view,
				autoplay : {
					pause:4000,
					disableOnInteraction : false,
				},
				speed: 500,
				parallax:false,
				resizeObserver:false,
				loop : true,
				loopedSlides : cate_totalCnt ,
				touchRatio:1,
				spaceBetween: 0,
				paginationClickable : false,
				roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
				initialSlide:swiper_main_best_cate_index, // swiper 떨림현상
			});


			// 슬라이드 변경시 실행되는 이벤트
			swiper_main_best_cate.on('slideChangeTransitionStart', function() {

				var cate_data_index = swiper_main_best_cate.realIndex;
				var cate_pagenum = swiper_main_best_cate.realIndex+1;

				$('.js_main_best_icon').removeClass('active');
				$('.js_main_best_icon[data-index='+cate_data_index+']').addClass('active');

				// 롤링 페이지 '01' 두자리수 형태
				if(cate_pagenum<10){
					cate_pagenum = '0'+cate_pagenum;
				}else{
					cate_pagenum = cate_pagenum;
				}
				$('.js_main_cate_pagenum').text(cate_pagenum);

				swiper_main_best_cate_index = cate_data_index;
			});


			$('.js_main_cate_prevnext').on('click', function() {
				var cate_data = $(this).data();

				if(cate_data.type=='next'){
					swiper_main_best_cate.slideNext();
				}else{
					swiper_main_best_cate.slidePrev();
				}
			});

		}

		// 롤링아이콘 클릭시 해당 슬라이드로 이동
		$(document).on('click', '.js_main_best_icon', function(e) {
			if( typeof swiper_main_best_cate == 'object'){ // 변경1
				var cate_data = $(this).data();
				var cate_index = (cate_data.index*1);
				swiper_main_best_cate.slideToLoop(cate_index,500); // 변경1
			}
		});

	</script>

</div>


<div class="sc_More_btn">
	<div class="layout_fix">
		<a href="/?pn=product.list&cuid=<?php echo $cuid; ?>" class="link js_main_cate_more">
			<?php // 카테고리명 노출 ?>
			<span class="tx"><em><?php echo $BestCateInfo['c_name'];?></em> 상품 더보기</span>
		</a>
	</div>
</div><!-- end sc_More_btn -->