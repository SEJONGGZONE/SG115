<?php

$list_type_class = $main_timesale_pcclass = $main_timesale_moclass=  '';
if($siteInfo['s_main_timesale_display_type']=='box'){
	$main_timesale_pcclass = 'pc_type_box'.$siteInfo['s_main_timesale_display'];
	$main_timesale_moclass = 'mobile_type_box'.$siteInfo['s_main_timesale_mobile_display'];
}else{
	$main_timesale_pcclass = 'pc_type_list'.$siteInfo['s_main_timesale_display'];
	$main_timesale_moclass = 'mobile_type_list'.$siteInfo['s_main_timesale_mobile_display'];
	$list_type_class = 'if_list_type';
}

if($view_type!='main'){
	// 메인에서는 목록 컨트롤러 노출x
	include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
}


// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0) {
	echo '
        <div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
	';
	return;
}

if($siteInfo['s_main_timesale_order']=='random'){
	shuffle($res);
}

$timesale_total_cnt = count($res);
$timesale_pc_max_cnt = $siteInfo['s_main_timesale_display'];
$timesale_mo_max_cnt = $siteInfo['s_main_timesale_mobile_display'];
$timesale_rolling_total_page = $timesale_total_cnt<10?'0'.$timesale_total_cnt:$timesale_total_cnt;

?>

	<div class="rolling_wrap this_item_rolling">
		<?php // 롤링 박스 ?>
		<div class="rolling_box">
			<div class="item_list js_main_timesale_rolling <?php echo $main_timesale_pcclass; ?> <?php echo $main_timesale_moclass;?> <?php echo $list_type_class;?>">
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
		<div class="sc_Roll_ctrl js_timesale_rolling_ctrl <?php echo $timesale_total_cnt > $timesale_pc_max_cnt?'if_show':'if_hide';?>">
			<a href="#none" title="이전" onclick="return false;" class="roll_prevnext prev js_main_timesale_prevnext" data-type="prev"></a>
			<div class="roll_icon">
				<ul>
					<?php foreach($res as $k => $v){?>
						<li class="<?php echo $k==0?'active':'';?> js_main_timesale_icon" data-index="<?php echo $k;?>">
							<a href="#none" onclick="return false;" class="icon"></a>
						</li>
					<?php }?>
				</ul>
			</div>
			<div class="roll_pagi">
				<span class="active js_main_timesale_pagenum">01</span>
				<span class="under">/</span>
				<span class="all"><?php echo $timesale_rolling_total_page;?></span>
			</div><!-- end rolling_pagi -->
			<a href="#none" title="다음" onclick="return false;" class="roll_prevnext next js_main_timesale_prevnext" data-type="next"></a>
		</div><!-- end sc_Roll_ctrl -->

	</div><!-- end rolling_wrap -->



	<script type="text/javascript">
		// 최초 슬라이드 넓이 조정 후 슬라이드 생성
		var swiper_main_timesale;
		var swiper_main_timesale_index = 0; // swiper 떨림현상

		$(document).ready(function(){
			main_timesale_rolling();
		});

		$(window).resize(function(e){
			main_timesale_rolling();
		});

		function main_timesale_rolling(){

			// 반응형
			var timesale_chkwidth = window.innerWidth;
			var timesale_totalCnt = '<?php echo $timesale_total_cnt;?>';
			var timesale_pc_view =  '<?php echo $timesale_pc_max_cnt;?>';
			var timesale_mo_view =  '<?php echo $timesale_mo_max_cnt;?>';

			if( timesale_chkwidth <= 700){ timesale_view = timesale_mo_view;}
			else if( timesale_chkwidth > 700 && timesale_chkwidth <= 900){ timesale_view = timesale_pc_view>=3?3:timesale_pc_view; }
			else if( timesale_chkwidth > 900 && timesale_chkwidth <= 1100){ timesale_view = timesale_pc_view>=4?4:timesale_pc_view; }
			else if( timesale_chkwidth > 1100 && timesale_chkwidth <= 1300){ timesale_view = timesale_pc_view>5?5:timesale_pc_view;}
			else if( timesale_chkwidth > 1300){ timesale_view = timesale_pc_view;}


			// swiper 떨림현상
			if( typeof swiper_main_timesale == 'object'){
				swiper_main_timesale.destroy();
				swiper_main_timesale = false;
			}

			// 롤링 컨트롤러는 클래스로 제어
			if(parseInt(timesale_view) >= parseInt(timesale_totalCnt)){
				$('.js_timesale_rolling_ctrl').removeClass('if_show').addClass('if_hide');
				return false;
			}else{
				$('.js_timesale_rolling_ctrl').removeClass('if_hide').addClass('if_show');
			}

			swiper_main_timesale = new Swiper('.js_main_timesale_rolling', {
				effect: 'slide',
				slidesPerView: timesale_view,
				autoplay : {
					pause:4000,
					disableOnInteraction : false,
				},
				speed: 500,
				parallax:false,
				resizeObserver:false,
				loop : true,
				loopedSlides : timesale_totalCnt ,
				touchRatio:1,
				spaceBetween: 0,
				paginationClickable : false,
				roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
				initialSlide:swiper_main_timesale_index, // swiper 떨림현상
			});


			// 슬라이드 변경시 실행되는 이벤트
			swiper_main_timesale.on('slideChangeTransitionStart', function() {

				var timesale_data_index = swiper_main_timesale.realIndex;
				var timesale_pagenum = swiper_main_timesale.realIndex+1;

				$('.js_main_timesale_icon').removeClass('active');
				$('.js_main_timesale_icon[data-index='+timesale_data_index+']').addClass('active');

				// 롤링 페이지 '01' 두자리수 형태
				if(timesale_pagenum<10){
					timesale_pagenum = '0'+timesale_pagenum;
				}else{
					timesale_pagenum = timesale_pagenum;
				}
				$('.js_main_timesale_pagenum').text(timesale_pagenum);

				swiper_main_timesale_index = timesale_data_index;
			});

			$('.js_main_timesale_prevnext').on('click', function() {
				var timesale_data = $(this).data();

				if(timesale_data.type=='next'){
					swiper_main_timesale.slideNext();
				}else{
					swiper_main_timesale.slidePrev();
				}
			});
		}

		// 롤링아이콘 클릭시 해당 슬라이드로 이동
		$(document).on('click', '.js_main_timesale_icon', function(e) {
			if( typeof swiper_main_timesale == 'object'){ // 변경1
				var timesale_data = $(this).data();
				var timesale_index = (timesale_data.index*1);
				swiper_main_timesale.slideToLoop(timesale_index,500); // 변경1
			}
		});

	</script>