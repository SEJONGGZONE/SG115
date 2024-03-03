<?php

// -- 해당 메인 상품의 설정을 가져온다.
$itemMd = _MQ("select *from `smart_display_main_set` where `dms_type` = 'md' and `dms_depth` = '2' and `dms_view` = 'Y' and `dms_uid` = '".$dmsuid."'
and `dms_list_product_mobile_view` = 'Y'");

$list_type_class = $main_md_pcclass = $main_md_moclass  = '';
if($itemMd['dms_main_product_display_type']=='box'){
	$main_md_pcclass = 'pc_type_box'.$itemMd['dms_main_product_display'];
	$main_md_moclass = 'mobile_type_box'.$itemMd['dms_main_product_mobile_display'];
}else{
	$main_md_pcclass = 'pc_type_list'.$itemMd['dms_main_product_display'];
	$main_md_moclass = 'mobile_type_list'.$itemMd['dms_main_product_mobile_display'];
	$list_type_class = 'if_list_type';
}

if($view_type!='main'){
	// 메인에서는 목록 컨트롤러 노출x
	include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
}


// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0 || count($itemMd) < 1 ) {
	echo '
        <div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
	';
	return;
}

$md_total_cnt = count($res);
$md_pc_max_cnt = $itemMd['dms_main_product_display'];
$md_mo_max_cnt = $itemMd['dms_main_product_mobile_display'];
$md_rolling_total_page = $md_total_cnt<10?'0'.$md_total_cnt:$md_total_cnt;

?>

	<div class="rolling_wrap this_item_rolling">
		<?php // 롤링 박스 ?>
		<div class="rolling_box">
			<div class="item_list js_main_md_rolling <?php echo $main_md_pcclass; ?> <?php echo $main_md_moclass;?> <?php echo $list_type_class;?>">
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
		<div class="sc_Roll_ctrl js_md_rolling_ctrl <?php echo $md_total_cnt > $md_pc_max_cnt?'if_show':'if_hide';?>">
			<a href="#none" title="이전" onclick="return false;" class="roll_prevnext prev js_main_md_prevnext" data-type="prev"></a>
			<div class="roll_icon">
				<ul>
					<?php foreach($res as $k => $v){?>
						<li class="<?php echo $k==0?'active':'';?> js_main_md_icon" data-index="<?php echo $k;?>">
							<a href="#none" onclick="return false;" class="icon"></a>
						</li>
					<?php }?>
				</ul>
			</div>
			<div class="roll_pagi">
				<span class="active js_main_md_pagenum">01</span>
				<span class="under">/</span>
				<span class="all"><?php echo $md_rolling_total_page;?></span>
			</div><!-- end rolling_pagi -->
			<a href="#none" title="다음" onclick="return false;" class="roll_prevnext next js_main_md_prevnext" data-type="next"></a>
		</div><!-- end sc_Roll_ctrl -->

	</div><!-- end rolling_wrap -->



	<script type="text/javascript">
		// 최초 슬라이드 넓이 조정 후 슬라이드 생성
		var swiper_main_md;
		var swiper_main_md_index = 0; // swiper 떨림현상

		$(document).ready(function(){
			main_md_rolling();
		});

		$(window).resize(function(e){
			main_md_rolling();
		});

		function main_md_rolling(){

			// 반응형
			var md_chkwidth = window.innerWidth;
			var md_totalCnt = '<?php echo $md_total_cnt;?>';
			var md_pc_view =  '<?php echo $md_pc_max_cnt;?>';
			var md_mo_view =  '<?php echo $md_mo_max_cnt;?>';

			if( md_chkwidth <= 700){ md_view = md_mo_view;}
			else if( md_chkwidth > 700 && md_chkwidth <= 900){ md_view = md_pc_view>=3?3:md_pc_view; }
			else if( md_chkwidth > 900 && md_chkwidth <= 1100){ md_view = md_pc_view>=4?4:md_pc_view; }
			else if( md_chkwidth > 1100 && md_chkwidth <= 1300){ md_view = md_pc_view>5?5:md_pc_view;}
			else if( md_chkwidth > 1300){ md_view = md_pc_view;}

			// swiper 떨림현상
			if( typeof swiper_main_md == 'object'){
				swiper_main_md.destroy();
				swiper_main_md = false;
			}

			// 롤링 컨트롤러는 클래스로 제어
			if(parseInt(md_view) >= parseInt(md_totalCnt)){
				$('.js_md_rolling_ctrl').removeClass('if_show').addClass('if_hide');
				return false;
			}else{
				$('.js_md_rolling_ctrl').removeClass('if_hide').addClass('if_show');
			}

			swiper_main_md = new Swiper('.js_main_md_rolling', {
				effect: 'slide',
				slidesPerView: md_view,
				autoplay : {
					pause:4000,
					disableOnInteraction : false,
				},
				speed: 500,
				parallax:false,
				resizeObserver:false,
				loop : true,
				loopedSlides : md_totalCnt ,
				touchRatio:1,
				spaceBetween: 0,
				paginationClickable : false,
				roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
				initialSlide:swiper_main_md_index, // swiper 떨림현상
			});


			// 슬라이드 변경시 실행되는 이벤트
			swiper_main_md.on('slideChangeTransitionStart', function() {

				var md_data_index = swiper_main_md.realIndex;
				var md_pagenum = swiper_main_md.realIndex+1;

				$('.js_main_md_icon').removeClass('active');
				$('.js_main_md_icon[data-index='+md_data_index+']').addClass('active');

				// 롤링 페이지 '01' 두자리수 형태
				if(md_pagenum<10){
					md_pagenum = '0'+md_pagenum;
				}else{
					md_pagenum = md_pagenum;
				}
				$('.js_main_md_pagenum').text(md_pagenum);

				swiper_main_md_index = md_data_index;
			});

			$('.js_main_md_prevnext').on('click', function() {
				var md_data = $(this).data();

				if(md_data.type=='next'){
					swiper_main_md.slideNext();
				}else{
					swiper_main_md.slidePrev();
				}
			});
		}

		// 롤링아이콘 클릭시 해당 슬라이드로 이동
		$(document).on('click', '.js_main_md_icon', function(e) {
			if( typeof swiper_main_md == 'object'){ // 변경1
				var md_data = $(this).data();
				var md_index = (md_data.index*1);
				swiper_main_md.slideToLoop(md_index,500); // 변경1
			}
		});

	</script>