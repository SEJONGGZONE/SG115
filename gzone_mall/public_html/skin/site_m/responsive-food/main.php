

<?php
$MainVisual = info_banner($_skin.',mobile_main_visual,set_img_after', 99999, 'data');
$visual_total_cnt = count($MainVisual);
$visual_max_cnt = 1;
$visual_rolling_total_page = $visual_total_cnt<10?'0'.$visual_total_cnt:$visual_total_cnt;

if($visual_total_cnt > 0) {
?>
    <?php // 메인 비주얼 ?>
	<div class="sc_Visual">
        <div class="layout_fix">
            <div class="rolling_wrap">
                <?php // 롤링 박스 ?>
                <div class="rolling_box">
					<div class="banner_list js_main_visual">
						<ul class="swiper-wrapper">
                            <?php
                                foreach($MainVisual as $k => $v){
                                    $_main_visual_mo = $v['b_img_mo']?IMG_DIR_BANNER_URL.$v['b_img_mo']:IMG_DIR_BANNER_URL.$v['b_img'];
                            ?>
                                <li class="swiper-slide">
                                    <div class="visual_img">
                                      <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>"><?php } ?>
											<?php // 메인비주얼배너 : 1,800 x free 무제한 ?>
											<img src="<?php echo IMG_DIR_BANNER_URL.$v['b_img']; ?>" class="this_pc" alt="<?php echo addslashes($v['b_title']);?>" />
											<img src="<?php echo $_main_visual_mo; ?>" class="this_mo" alt="<?php echo addslashes($v['b_title']);?>" />
									   <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
                                    </div>
                                </li>
                            <?php }?>
						</ul>
					</div>
                </div><!-- end rolling_box -->

				<?php // 롤링 컨트롤러 ?>
				<div class="sc_Roll_ctrl js_main_visual_crtl <?php echo $visual_total_cnt > $visual_max_cnt?'if_show':'if_hide';?>">
					<a href="#none" title="이전" onclick="return false;" class="roll_prevnext prev js_main_visual_prevnext" data-type="prev"></a>
					<div class="roll_icon">
						<ul>
							<?php foreach($MainVisual as $k=>$v) { ?>
								<li class="<?php echo ($k === 0?' active':null); ?> js_main_visual_icon" data-index="<?php echo $k; ?>">
									<a href="#none" class="icon" onclick="return false;" ></a>
								</li>
							<?php } ?>
						</ul>
					</div><!-- end roll_icon -->
					<div class="roll_pagi">
						<span class="active js_main_visual_pagenum">01</span>
						<span class="under">/</span>
						<span class="all"><?php echo $visual_rolling_total_page?></span>
					</div><!-- end rolling_pagi -->
					<a href="#none" title="다음" onclick="return false;" class="roll_prevnext next js_main_visual_prevnext" data-type="next"></a>
				</div><!-- end sc_Roll_ctrl -->

				<script type="text/javascript">

					var swiper_main_visual;
					var swiper_main_visual_index = 0;

					$(document).ready(function(){
						 main_visual_rolling();
					});

					function main_visual_rolling(){

						// 반응형
						var visual_chkwidth = window.innerWidth;
						var visual_totalCnt = '<?php echo $visual_total_cnt;?>';

						if( typeof swiper_main_visual == 'object'){
							swiper_main_visual.destroy();
							swiper_main_visual = false;
						}

						// 롤링 컨트롤러는 클래스로 제어
						if(1>=visual_totalCnt){
							$('.js_main_visual_crtl').removeClass('if_show').addClass('if_hide');
							return false;
						}else{
							$('.js_main_visual_crtl').removeClass('if_hide').addClass('if_show');
						}

						swiper_main_visual = new Swiper('.js_main_visual', {
							effect: 'slide',
							slidesPerView: 1,
							autoplay : {
								pause:4000,
								disableOnInteraction : false,
							},
							speed: 500,
							resizeObserver:false,
							loop : true,
							loopedSlides : visual_totalCnt ,
							touchRatio:1,
							spaceBetween: 0,
							paginationClickable : false,
							parallax:false,
							initialSlide : swiper_main_visual_index,
						});


						// 슬라이드 변경시 실행되는 이벤트
						swiper_main_visual.on('slideChangeTransitionStart', function() { // 변경1

							// li 에 active 클래스 추가
							var visual_index = swiper_main_visual.realIndex; // 변경1
							var visual_pagenum = swiper_main_visual.realIndex+1;

							$('.js_main_visual_icon').removeClass('active');
							$('.js_main_visual_icon[data-index='+visual_index+']').addClass('active');

							// 롤링 페이지 '01' 두자리수 형태
							if(visual_pagenum<10){
								visual_pagenum = '0'+visual_pagenum;
							}else{
								visual_pagenum = visual_pagenum;
							}
							$('.js_main_visual_pagenum').text(visual_pagenum);

							swiper_main_visual_index = visual_index;
						});
					}

					// 이전 다음 클릭시 실행되는 이벤트
					$(document).on('click', '.js_main_visual_prevnext', function(e) {
						if( typeof swiper_main_visual == 'object'){	// 변경1
							var data = $(this).data();
							if(data.type=='next'){
								swiper_main_visual.slideNext(); // 변경1
							}else{
								swiper_main_visual.slidePrev(); // 변경1
							}
						}
					});

					// 롤링아이콘 클릭시 해당 슬라이드로 이동
					$(document).on('click', '.js_main_visual_icon', function(e) {
						if( typeof swiper_main_visual == 'object'){	// 롤링안될때 클릭방지
							var visual_data = $(this).data();
							var visual_index = (visual_data.index*1);
							swiper_main_visual.slideToLoop(visual_index,500);
						}
					});


				</script>


            </div><!-- end rolling_wrap -->
        </div><!-- end layout_fix -->
	</div><!-- end sc_Visual -->
<?php } ?>







<?php
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
if(count($resMd) > 0) {
?>
    <?php
		// 메인 MD's Pick
	?>
    <div class="sc_Group sc_Md">
        <div class="sc_Group_tit">
            <div class="layout_fix">
                <span class="tit"><?php echo stripslashes($resMd[0]['dms1_name']) ?></span>
            </div>
        </div><!-- end sc_Group_tit -->

        <?php // 탭 메뉴 ?>
        <div class="tab_box">
            <div class="layout_fix">
                <div class="swipe_box" ID="js_mainmd_iscroll">
                    <ul>
                        <?php
							foreach($resMd as $mk=>$mv){
								if($mk == 0) {
									$firstUid = $mv['dms_uid'];
									$md_limit = $mv['dms_list_product_mobile_limit'];
								}
						?>
                            <li class="js_main_md_li<?php echo ($mk <= 0?' hit':null); ?>" data-dmsuid="<?php echo $mv['dms_uid'] ?>">
                                <a href="#none" onclick="return false;" class="tab js_main_md_tab" data-dmslimit="<?php echo $mv['dms_list_product_mobile_limit'];?>" data-dmsuid="<?php echo $mv['dms_uid'] ?>"><?php echo stripslashes($mv['dms_name']);  ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div><!-- end swipe_box -->
            </div>
        </div><!-- end tab_box -->
        <script>
            // 아이스크롤 스크립트
            // ex ) js_mainmd_iscroll => ID 로 변경
            var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_mainmd_iscroll'), Cate2depthScroll = '';

            // 스와이프 적용
            $(document).ready(function(){
                func_md_iscroll();
            });

            // 사용시 ID로 지정후 해당 ID를 변경
            // ex ) #js_mainmd_iscroll=> #ID 로 변경
            function func_md_iscroll(){

                $.each($('#js_mainmd_iscroll li'), function(k, v){  scrollWidth += $('#js_mainmd_iscroll li').eq(k).outerWidth()*1; });
                var len = $('#js_mainmd_iscroll li').length;
                Cate2depthScroll = new IScroll('#js_mainmd_iscroll', {
                    'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
                    , mouseWheel: true
                });

                if(scrollIndex > 0 && $('#js_mainmd_iscroll li.hit').length > 0) {
                    var scrollOffset = ($(window).width()*1/2 - $('#js_mainmd_iscroll li.hit').outerWidth()*1/2 ) * -1;
                    Cate2depthScroll.scrollToElement(document.querySelector('#js_mainmd_iscroll li.hit'), 500, scrollOffset);
                }
            }
        </script>

        <div class="layout_fix js_main_md">
            <?php
                $dmsuid = $firstUid;
                $_event = 'main_md';
                $_list_type = 'ajax.main_md';
                $listmaxcount = $md_limit;
                $best_use ='N';
                $view_type ='main';
                include(OD_PROGRAM_ROOT.'/product.list.php');
            ?>
        </div><!-- end layout_fix -->

        <div class="sc_More_btn">
            <div class="layout_fix">
                <a href="#none" class="link js_more_md" onclick="return false;"><span class="tx">더보기</span></a>
            </div>
        </div><!-- end sc_More_btn -->

        <script type="text/javascript">
            $(document).on('click', '.js_main_md_tab', function(e) {
                e.preventDefault();
                var dmsuid = $(this).data('dmsuid');
                $('.js_main_md_li').removeClass('hit');
                $(this).closest('.js_main_md_li').addClass('hit');
                mainMdTab();
            });

            $(document).on('click', '.js_more_md', function(e) {
                e.preventDefault();
                var dmsuid = $('.js_main_md_li.hit').data('dmsuid');
				location.href='/?pn=product.md_list&firstUid='+dmsuid;
            });

            function mainMdTab() {
                var dmsuid = $('.js_main_md_li.hit').find('a').data('dmsuid');
				var dmslimit = $('.js_main_md_li.hit').find('a').data('dmslimit');
                $.ajax({
                    data: {
                        dmsuid: dmsuid,
                        _event: 'main_md',
                        _list_type: 'ajax.main_md',
						listmaxcount : dmslimit,
						view_type:'main'
                    },
                    type: 'get',
                    cache: true,
                    url: '<?php echo OD_PROGRAM_URL; ?>/product.list.php',
                    success: function(data) {
                        if(typeof MainMdSlideDes != 'undefined') MainMdSlideDes();
                        $('.js_main_md').html(data);
                    }
                });
            }
        </script>

    </div><!-- end sc_Md -->
<?php } ?>







<?php
$resTimesale = _MQ_result("select count(*) as cnt from smart_product where p_view='Y' and p_time_sale='Y'");
if($resTimesale > 0 && $siteInfo['s_main_timesale_view']=='Y') {
?>
	

    <?php // 메인 타임세일 ?>
    <div class="sc_Group sc_Time">
        <div class="sc_Group_tit">
            <div class="layout_fix">
                <?php // 타이틀 관리자 설정 ?>
                <span class="tit"><?php echo $siteInfo['s_main_timesale_title'];?></span>
            </div>
        </div><!-- end sc_Group_tit -->

        <div class="layout_fix js_main_timesale">
            <?php
                $_event = 'main_timesale';
                $_list_type = 'ajax.main_timesale';
                $listmaxcount = $siteInfo['s_main_timesale_limit'];
                $best_use ='N';
                $view_type ='main';
                include(OD_PROGRAM_ROOT.'/product.list.php');
            ?>
        </div><!-- end layout_fix -->

        <div class="sc_More_btn">
            <div class="layout_fix">
                <a href="/?pn=product.time_list" class="link"><span class="tx">더보기</span></a>
            </div>
        </div><!-- end sc_More_btn -->

    </div><!-- end sc_Time -->
<?php } ?>








<?php
$Depth1Cate = _MQ_assoc("
	select
		c1.*,
		(
			select
				count(*)
			from
				smart_product as p left join
				smart_product_category_best as pcb on(p.p_code = pcb.pctb_pcode)
			where (1) and
			pcb.pctb_cuid = c1.c_uid
		) as pcnt
	from
		`smart_category` as c1
	where (1) and
		c_depth = 1 and
		c_view = 'Y' and
		c_best_product_mobile_view = 'Y'
	having pcnt > 0
	order by c_idx asc
");
if(count($Depth1Cate) > 0 && $siteInfo['s_main_best_view']=='Y') {
?>
    <?php // 메인 카테고리 베스트 ?>
	<div class="sc_Group sc_Ctg">
        <div class="sc_Group_tit">
            <div class="layout_fix">
                <span class="tit"><?php echo $siteInfo['s_main_best_title'];?></span>
            </div>
        </div><!-- end sc_Group_tit -->

        <?php // 탭 메뉴 ?>
        <div class="tab_box">
            <div class="layout_fix">
				<div class="inner">
					<div class="swipe_box" ID="js_mainctg_iscroll">
						<ul>
							<?php
							$MainFirstCuid = $Depth1Cate[0]['c_uid'];
							foreach($Depth1Cate as $dk=>$dv) {
								?>
								<li class="js_main_cate_li<?php echo ($dk <= 0?' hit':null); ?>">
									<a href="#none" class="tab js_main_cate_tab" data-cuid="<?php echo $dv['c_uid']; ?>"><strong><?php echo $dv['c_name']; ?></strong></a>
								</li>
							<?php } ?>
						</ul>
					</div><!-- end swipe_box -->
				</div>
            </div>
        </div><!-- end tab_box -->

        <script>
            // 아이스크롤 스크립트
            // ex ) js_mainctg_iscroll => ID 로 변경
            var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_mainctg_iscroll'), Cate2depthScroll = '';

            // 스와이프 적용
            $(document).ready(function(){
                func_mainctg_iscroll();
            });

            // 사용시 ID로 지정후 해당 ID를 변경
            // ex ) #js_mainctg_iscroll=> #ID 로 변경
            function func_mainctg_iscroll(){

                $.each($('#js_mainctg_iscroll li'), function(k, v){  scrollWidth += $('#js_mainctg_iscroll li').eq(k).outerWidth()*1; });
                var len = $('#js_mainctg_iscroll li').length;
                Cate2depthScroll = new IScroll('#js_mainctg_iscroll', {
                    'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
                    , mouseWheel: true
                });

                if(scrollIndex > 0 && $('#js_mainctg_iscroll li.hit').length > 0) {
                    var scrollOffset = ($(window).width()*1/2 - $('#js_mainctg_iscroll li.hit').outerWidth()*1/2 ) * -1;
                    Cate2depthScroll.scrollToElement(document.querySelector('#js_mainctg_iscroll li.hit'), 500, scrollOffset);
                }
            }
        </script>

		<div class="category_item js_main_cate_box">
			<?php
				$cuid = $MainFirstCuid;
				$best_use ='N';
				$_event = 'main_category_best';
				$_list_type = 'ajax.main_best_category';
				include(OD_PROGRAM_ROOT.'/product.list.php');
			?>
		</div>


        <script type="text/javascript">
            // 카테고리 탭 클릭 동작 처리
            $(document).on('click', '.js_main_cate_tab', function(e) {
                e.preventDefault();
                var _cuid = $(this).data('cuid');
                $('.js_main_cate_li').removeClass('hit');
                $('.js_main_cate_more').prop('href', '/?pn=product.list&cuid='+_cuid);
                $(this).closest('.js_main_cate_li').addClass('hit');
                MainBestCategory();
            });

            function MainBestCategory() {
                var _cate = $('.js_main_cate_li.hit').find('a').data('cuid');
                $.ajax({
                    data: {
                        cuid: _cate,
                        _event: 'main_category_best',
                        _list_type: 'ajax.main_best_category',
                    },
                    type: 'POST',
                    cache: false,
                    url: '<?php echo OD_PROGRAM_URL; ?>/product.list.php',
                    success: function(data) {
                        if(typeof MainBestCategoryDes != 'undefined') MainBestCategoryDes();
                        $('.js_main_cate_box').html(data);
                    }
                });
            }
        </script>
	</div><!-- end sc_Ctg -->
<?php } ?>








<?php
	$resMain = _MQ_assoc("
		select
			dms1.*,
			(select count(*) from smart_product as p inner join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) where dmp_dmsuid = `dms1`.`dms_uid` ) as pcnt
		from
			`smart_display_main_set` as dms1 inner join
			`smart_display_main_set` as dms2 on(dms2.dms_type = dms1.dms_type and dms2.dms_depth = 1 and dms2.dms_view = 'Y')
		where
			dms1.dms_type = 'main' and
			dms1.dms_depth = '2' and
			dms1.dms_view = 'Y'
		having pcnt > 0
		order by `dms_idx` asc
	");
	if(count($resMain) > 0) {
		foreach($resMain as $mk => $mv) {
?>
    <?php // 메인 상품 ?>
	<div class="sc_Group sc_Item">
		<div class="sc_Group_tit">
			<div class="layout_fix">
				<span class="tit"><?php echo $mv['dms_name'] ?></span>
			</div>
		</div><!-- end sc_Group_tit -->

		<?php
			$dmsuid = $mv['dms_uid'];
			$_event = 'main_product';
			$_list_type = 'ajax.main_product';
			$best_use ='N';
			$listmaxcount = $mv['dms_list_product_mobile_limit'];
			include(OD_PROGRAM_ROOT.'/product.list.php');
		?>
	</div><!-- end sc_Item -->
<?php
		} // -- end foreach
	}  // -- end if
?>










<?php
	$MainMiddle2 = info_banner($_skin.',main_middle2,set_detail_text,set_linkmore_btn', 99999, 'data');

	$middle2_total_cnt = count($MainMiddle2);
	$middle2_max_cnt = 2;

	if($middle2_total_cnt > 0) {
?>
    <?php // 메인 2단 배너 ?>
	<div class="sc_Group sc_Ad">
		<div class="layout_fix">
			<div class="rolling_wrap">
                <?php // 롤링 박스 ?>
				<div class="rolling_box">
					<div class="banner_list js_main_middle2">
						<ul class="swiper-wrapper">
							<?php
								foreach($MainMiddle2 as $mmk => $mmv){
							?>
								<li class="swiper-slide">
									<div class="banner">
										<div class="img_box">
											<?php if($mmv['b_target'] != '_none' && isset($mmv['b_link'])) { ?><a href="<?php echo $mmv['b_link'];?>"  target="<?php echo $mmv['b_target']; ?>" class="upper_link" title=""></a><?php }?>
											<?php // 메인 2단배너 : 670 * free ?>
											<img src="<?php echo IMG_DIR_BANNER_URL.$mmv['b_img']; ?>" alt="<?php echo addslashes($mmv['b_title']);?>" />
										</div>

										<?php if($mmv['b_title'] != ''||$mmv['b_text'] != '' || ($mmv['b_target'] != '_none' && isset($mmv['b_link']) && $mmv['b_link_btnname']!='' )){?>
											<div class="txt_box">
												<?php if($mmv['b_title'] != '' ){ ?><div class="tit"><?php echo $mmv['b_title'];?></div><?php } ?>
												<?php if($mmv['b_text'] != '' ){ ?><div class="text"><?php echo nl2br($mmv['b_text']);?></div><?php }?>
												<?php if($mmv['b_target'] != '_none' && isset($mmv['b_link']) && $mmv['b_link_btnname']!='') { ?><a href="<?php echo $mmv['b_link'];?>" target="<?php echo $mmv['b_target']; ?>" class="btn_view"><span class="tx"><?php echo addslashes($mmv['b_link_btnname']);?></span></a><?php }?>
											</div>
										<?php }?>
									</div><!-- end banner -->
								</li>
							<?php }?>
						</ul>
					</div><!-- end banner_list -->
				</div><!-- end rolling_box -->

				<div class="sc_Roll_ctrl js_middle2_rolling_ctrl <?php echo $middle2_total_cnt > $middle2_max_cnt?'if_show':'if_hide';?>">
					<div class="roll_icon">
						<ul>
							<?php foreach($MainMiddle2 as $mmk =>$mmv){?>
								<li class="<?php echo $mmk==0?'active':null ?> js_main_middle2_icon" data-index="<?php echo $mmk; ?>">
									<a href="#none" onclick="return false;" class="icon"></a>
								</li>
							<?php }?>
						</ul>
					</div><!-- end roll_icon -->
				</div>

			</div><!-- end rolling_wrap -->
		</div><!-- end layout_fix -->

		<?php if($middle2_total_cnt>1){?>
			<script type="text/javascript">

				// 최초 슬라이드 넓이 조정 후 슬라이드 생성
				var swiper_main_middle2;
				var swiper_main_middle2_index = 0; // swiper 떨림현상

				$(document).ready(function(){
					main_middle2_rolling();
				});

				$(window).resize(function(e){
					main_middle2_rolling();
				});


				function main_middle2_rolling(){

					// 반응형
					var chkwidth = window.innerWidth;
					var middle2_totalCnt = '<?php echo count($MainMiddle2);?>'*1;
					var middle2_view =  '<?php echo $middle2_max_cnt;?>' *1;
					if( chkwidth <= 700){ middle2_view = 1;}

					// swiper 떨림현상
					if( typeof swiper_main_middle2 == 'object'){
						swiper_main_middle2.destroy();
						swiper_main_middle2 = false;
					}

					// 롤링 컨트롤러는 클래스로 제어
					if(middle2_view>=middle2_totalCnt){
						$('.js_middle2_rolling_ctrl').removeClass('if_show').addClass('if_hide');
						return false;
					}else{
						$('.js_middle2_rolling_ctrl').removeClass('if_hide').addClass('if_show');
					}


					swiper_main_middle2 = new Swiper('.js_main_middle2', {
						effect: 'slide',
						slidesPerView: middle2_view,
						autoplay : {
							pause:4000,
							disableOnInteraction : false,
						},
						speed: 500,
						parallax:false,
						resizeObserver:false,
						loop : true,
						loopedSlides : middle2_totalCnt ,
						touchRatio:1,
						spaceBetween: 0,
						paginationClickable : false,
						roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
						initialSlide:swiper_main_middle2_index, // swiper 떨림현상
					});


					// 슬라이드 변경시 실행되는 이벤트
					swiper_main_middle2.on('slideChangeTransitionStart', function() { // 변경1

						// 롤링아이콘2 active 추가 (2자리수)
						var middle2_data_index = swiper_main_middle2.realIndex; // 변경1
						$('.js_main_middle2_icon').removeClass('active');
						$('.js_main_middle2_icon[data-index='+middle2_data_index+']').addClass('active');

						swiper_main_middle2_index = middle2_data_index;
					});
				}

				// 롤링아이콘 클릭시 해당 슬라이드로 이동
				$(document).on('click', '.js_main_middle2_icon', function(e) {
					if( typeof swiper_main_middle2 == 'object'){ // 변경1
						var middle2_data = $(this).data();
						var middle2_index = (middle2_data.index*1);
						swiper_main_middle2.slideToLoop(middle2_index,500); // 변경1
					}
				});

			</script>
		<?php }?>

	</div><!-- end sc_Ad -->
<?php }?>




<?php
	$MainMiddle1 = info_banner($_skin.',main_middle1,set_img_after', 99999, 'data');

	$middle1_total_cnt = count($MainMiddle1);
	$middle1_max_cnt = 1;
	$middle1_rolling_total_page = $middle1_total_cnt<10?'0'.$middle1_total_cnt:$middle1_total_cnt;

	if($middle1_total_cnt > 0) {
?>
    <?php // 메인 1단 배너 ?>
	<div class="sc_Group sc_Single">
		<div class="layout_fix">
            <div class="rolling_wrap">
                <div class="rolling_box">
                    <div class="banner_list js_main_middle1">
                        <ul class="swiper-wrapper">
                            <?php
                                foreach($MainMiddle1 as $mk => $mv){
                                    $_main_middle1_mo = $mv['b_img_mo']?IMG_DIR_BANNER_URL.$mv['b_img_mo']:IMG_DIR_BANNER_URL.$mv['b_img'];
                            ?>
                                <li class="swiper-slide">
                                    <div class="banner">
                                       <?php if($mv['b_target'] != '_none' && isset($mv['b_link'])) { ?><a href="<?php echo $mv['b_link'];?>"  target="<?php echo $mv['b_target']; ?>" class="upper_link" title=""></a><?php }?>
                                       <img src="<?php echo IMG_DIR_BANNER_URL.$mv['b_img']; ?>" class="this_pc" alt="<?php echo addslashes($mv['b_title']);?>" />
                                       <img src="<?php echo $_main_middle1_mo; ?>" class="this_mo" alt="<?php echo addslashes($mv['b_title']);?>" />
                                    </div>
                                </li>
                            <?php }?>
                        </ul>
                    </div><!-- end banner_list -->
                </div><!-- end rolling_box -->

				<div class="sc_Roll_ctrl js_middle1_rolling_ctrl <?php echo $rv_total_cnt > $rv_max_cnt?'if_show':'if_hide';?>">
					<a href="#none" onclick="return false;" class="roll_prevnext prev js_main_middle1_prevnext" data-type="prev" title="이전"></a>
					<div class="roll_icon">
						<ul>
							<?php foreach($MainMiddle1 as $mk=>$mv){?>
								<li class="<?php echo $mk==0?'active':'';?> js_main_middle1_icon" data-index="<?php echo $mk;?>">
									<a href="#none" class="icon" onclick="return false;"></a>
								</li>
							<?php }?>
						</ul>
					</div>
					<div class="roll_pagi">
						<span class="active js_main_middle1_pagenum">01</span>
						<span class="under">/</span>
						<span class="all"><?php echo $middle1_rolling_total_page?></span>
					</div><!-- end rolling_pagi -->
					<a href="#none" onclick="return false;" class="roll_prevnext next js_main_middle1_prevnext" data-type="next" title="다음"></a>
				</div><!-- end sc_Roll_ctrl -->

            </div><!-- end rolling_wrap -->
		</div><!-- end layout_fix -->

		<script type="text/javascript">
			var swiper_main_middle1;
			var swiper_main_middle1_index = 0; // swiper 떨림현상

			$(document).ready(function(){
				main_middle1_rolling();
			});

			$(window).resize(function(e){
				main_middle1_rolling();
			});

			function main_middle1_rolling(){

				var middle1_totalCnt = '<?php echo $middle1_total_cnt;?>';
				var middle1_view =  '<?php echo $middle1_max_cnt;?>';

				// swiper 떨림현상
				if( typeof swiper_main_middle1 == 'object'){
					swiper_main_middle1.destroy();
					swiper_main_middle1 = false;
				}


				// 롤링 컨트롤러는 클래스로 제어
				if(middle1_view >= middle1_totalCnt){
					$('.js_middle1_rolling_ctrl').removeClass('if_show').addClass('if_hide');
					return false;
				}else{
					$('.js_middle1_rolling_ctrl').removeClass('if_hide').addClass('if_show');
				}

				swiper_main_middle1 = new Swiper('.js_main_middle1', {
					effect: 'slide',
					slidesPerView: middle1_view,
					autoplay : {
						pause:4000,
						disableOnInteraction : false,
					},
					speed: 500,
					parallax:false,
					resizeObserver:false,
					loop : true,
					loopedSlides : middle1_totalCnt ,
					touchRatio:1,
					spaceBetween: 0,
					paginationClickable : false,
					roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
					initialSlide:swiper_main_middle1_index, // swiper 떨림현상
				});

				// 슬라이드 변경시 실행되는 이벤트
				swiper_main_middle1.on('slideChangeTransitionStart', function() { // 변경1
					var middle1_data_index = swiper_main_middle1.realIndex; // 변경1
					var middle1_pagenum = swiper_main_middle1.realIndex+1;

					$('.js_main_middle1_icon').removeClass('active');
					$('.js_main_middle1_icon[data-index='+middle1_data_index+']').addClass('active');

					// 롤링 페이지 '01' 두자리수 형태
					if(middle1_pagenum<10){
						middle1_pagenum = '0'+middle1_pagenum;
					}else{
						middle1_pagenum = middle1_pagenum;
					}
					$('.js_main_middle1_pagenum').text(middle1_pagenum);

					swiper_main_middle1_index = middle1_data_index;
				});


				$('.js_main_middle1_prevnext').on('click', function() {
					var middle1_data = $(this).data();

					if(middle1_data.type=='next'){
						swiper_main_middle1.slideNext();
					}else{
						swiper_main_middle1.slidePrev();
					}
				});

			}

			// 롤링아이콘 클릭시 해당 슬라이드로 이동
			$(document).on('click', '.js_main_middle1_icon', function(e) {
				if( typeof swiper_main_middle1 == 'object'){ // 변경1
					var middle1_data = $(this).data();
					var middle1_index = (middle1_data.index*1);
					swiper_main_middle1.slideToLoop(middle1_index,500); // 변경1
				}
			});

		</script>
	</div><!-- end sc_Single -->
<?php }?>






<?php

$rv_total_cnt = count($ReviewData);
$rv_max_cnt = 5;
$rv_rolling_total_page = $rv_total_cnt<10?'0'.$rv_total_cnt:$rv_total_cnt;

if(count($ReviewData)>0 && $siteInfo['s_main_review']=='Y'){
?>
    <?php // 메인 리뷰 ?>
    <div class="sc_Group sc_Review">
        <div class="sc_Group_tit">
            <div class="layout_fix">
                <a href="/?pn=service.eval.list" class="tit">BEST REVIEW</a>
				<?php if($siteInfo['s_productevalpoint']>0){?>
					<span class="tx_info">포토리뷰 등록 시 <strong><?php echo number_format($siteInfo['s_productevalpoint']);?>원</strong>의 적립금을 드립니다.</span>
				<?php }?>
            </div>
        </div><!-- end sc_Group_tit -->

        <div class="layout_fix">
            <div class="rolling_wrap">
                <div class="rolling_box">
                    <div class="review_list js_main_review">
                        <ul class="ul swiper-wrapper">
                            <?php
                                foreach($ReviewData as $rk => $rv){
                                    $_link = '/?pn=product.view&cuid='.$rv['p_cuid'].'&pcode='.$rv['p_code'];
                                    $rv_img =($rv['pt_img']!=''?get_img_src($rv['pt_img'], IMG_DIR_PRODUCT):get_img_src($rv['p_img_list_square'], IMG_DIR_PRODUCT));
                                    // $star_persent = get_eval_average($rv['p_code']);
                                    $star_persent = $rv['pt_eval_point'];
                                    $_content = stripslashes(htmlspecialchars($rv['pt_content'])); // 내용
                            ?>
                                <li class="li swiper-slide">
                                    <div class="review_box">
                                        <div onclick="return false;" class="photo js_onoff_event js_best_review" data-uid="<?php echo $rv['pt_uid'];?>" >
                                            <span class="icon_h"></span>
                                            <span class="icon_v"></span>
                                            <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/review_thumb.gif" style="background-image: url('<?php echo $rv_img; ?>');" alt="" />
                                        </div>
                                        <div class="about_item">
                                            <div class="thumb">
												 <a href="<?php echo $_link;?>" class="upper_link"></a>
												<img src="<?php echo get_img_src($rv['p_img_list_square'], IMG_DIR_PRODUCT); ?>" alt="<?php echo addslashes($rv['p_name']);?>" />
											</div>
                                            <div class="info">
												 <a href="<?php echo $_link;?>" class="name"><?php echo addslashes($rv['p_name']);?></a>
                                                <div class="price"><?php echo number_format($rv['p_price']);?>원</div>
                                            </div>
                                        </div>
                                        <div class="rv_info js_onoff_event js_best_review" onclick="return false;" data-uid="<?php echo $rv['pt_uid'];?>">
                                            <span class="mark">
                                                <span class="star this_value" style="width: <?php echo $star_persent;?>%;"></span>
                                                <span class="star this_base"></span>
                                            </span>
                                            <div class="tx_conts"><?php echo ($_content); ?></div>
                                            <div class="writer">
                                                <span class="id"><?php echo LastCut($rv['pt_inid'],3);?></span>
                                                <span class="date">(<?php echo date('Y-m-d',strtotime($rv['pt_rdate']));?>)</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php }?>
                        </ul>
                    </div><!-- end review_list -->
                </div><!-- end rolling_box -->

				<div class="sc_Roll_ctrl js_review_rolling_ctrl <?php echo $rv_total_cnt > $rv_max_cnt?'if_show':'if_hide';?>">
					<a href="#none" onclick="return false;" class="roll_prevnext prev js_main_review_prevnext" data-type="prev" title="이전"></a>
					<div class="roll_icon">
						<ul>
							<?php foreach($ReviewData as $rk=>$rv){?>
								<li class="<?php echo $rk==0?'active':'';?> js_main_review_icon" data-index="<?php echo $rk;?>">
									<a href="#none" class="icon" onclick="return false;"></a>
								</li>
							<?php }?>
						</ul>
					</div>
					<div class="roll_pagi">
						<span class="active js_main_rv_pagenum">01</span>
						<span class="under">/</span>
						<span class="all"><?php echo $rv_rolling_total_page;?></span>
					</div><!-- end rolling_pagi -->
					<a href="#none" onclick="return false;" class="roll_prevnext next js_main_review_prevnext" data-type="next" title="다음"></a>
				</div><!-- end sc_Roll_ctrl -->

            </div><!-- end rolling_wrap -->
        </div><!-- end layout_fix -->

        <?php // 레이어 : 리뷰 ?>
        <div class="c_layer type_main_review js_open_review">
            <?php include(dirname(__file__).'/ajax.main_review_pop.php'); ?>
        </div><!-- end c_layer -->

        <script>
           // 리뷰 열고 닫기
            $(document).on('click','.js_best_review',function(){ // 버튼에게
                var _uid = $(this).data('uid');
                var targetClass = '.c_layer.type_main_review'; // 열리는 요소를 포함한 부모에게
                var addClassName = 'if_open_layer'; // 열리게 될 클래스값
                var chk = $(targetClass).hasClass(addClassName);
                if( chk == false){
                    $.ajax({
                        data: {_uid:_uid},
                        type: 'GET',
                        cache: false,
                        url: '<?php echo OD_PROGRAM_URL; ?>/ajax.main_review_pop.php',
                        success: function(data) {
                            $('.js_open_review').html(data);
                            $(targetClass).addClass(addClassName);
                        }
                    });
                }
                else {
                    $(targetClass).removeClass(addClassName);
                }
            });


            // 최초 슬라이드 넓이 조정 후 슬라이드 생성
            var swiper_main_review;
			 var swiper_main_review_index = 0; // swiper 떨림현상

			$(document).ready(function(){
				main_review_rolling();
			});

			$(window).resize(function(e){
				main_review_rolling();
			});


            function main_review_rolling(){

                // 반응형
				var rv_chkwidth = window.innerWidth;
				var rv_totalCnt = '<?php echo $rv_total_cnt;?>';
				var rv_view =  '<?php echo $rv_max_cnt;?>';
                if( rv_chkwidth <= 700){ rv_view = 2;}
                else if( rv_chkwidth > 700 && rv_chkwidth <= 900){ rv_view = 3;}
                else if( rv_chkwidth > 900 && rv_chkwidth <= 1300){ rv_view = 4;}

                // swiper 떨림현상
                if( typeof swiper_main_review == 'object'){
                    swiper_main_review.destroy();
                    swiper_main_review = false;
                }

				// 롤링 컨트롤러는 클래스로 제어
				if(parseInt(rv_view)>=parseInt(rv_totalCnt)){
					$('.js_review_rolling_ctrl').removeClass('if_show').addClass('if_hide');
					return false;
				}else{
					$('.js_review_rolling_ctrl').removeClass('if_hide').addClass('if_show');
				}

                swiper_main_review = new Swiper('.js_main_review', {
                    effect: 'slide',
                    slidesPerView: rv_view,
                    autoplay : {
                        pause:4000,
                        disableOnInteraction : false,
                    },
                    speed: 500,
                    parallax:false,
                    resizeObserver:false,
                    loop : true,
					loopedSlides : rv_totalCnt ,
					touchRatio:1,
                    spaceBetween: 0,
					paginationClickable : false,
                    roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
                    initialSlide:swiper_main_review_index, // swiper 떨림현상
                });

				// 슬라이드 변경시 실행되는 이벤트
				swiper_main_review.on('slideChangeTransitionStart', function() {

					var rv_data_index = swiper_main_review.realIndex;
					var rv_pagenum = swiper_main_review.realIndex+1;
					$('.js_main_review_icon').removeClass('active');
					$('.js_main_review_icon[data-index='+rv_data_index+']').addClass('active');

					// 롤링 페이지 '01' 두자리수 형태
					if(rv_pagenum<10){
						rv_pagenum = '0'+rv_pagenum;
					}else{
						rv_pagenum = rv_pagenum;
					}
					$('.js_main_rv_pagenum').text(rv_pagenum);

					swiper_main_review_index = rv_data_index;

				});


                $('.js_main_review_prevnext').on('click', function() {
                    var data = $(this).data();
                    if(data.type=='next'){
                        swiper_main_review.slideNext();
                    }else{
                        swiper_main_review.slidePrev();
                    }
                });
            }

			// 롤링아이콘 클릭시 해당 슬라이드로 이동
			$(document).on('click', '.js_main_review_icon', function(e) {
				if( typeof swiper_main_review == 'object'){ // 변경1
					var rv_data = $(this).data();
					var rv_index = (rv_data.index*1);
					swiper_main_review.slideToLoop(rv_index,500); // 변경1
				}
			});
        </script>

    </div><!-- end sc_Review -->
<?php }?>





<?php // 고객센터 ?>
<div class="sc_Group sc_Service">
    <div class="layout_fix">
		<div class="inner">
			<dl>
				<dt>
					 <div class="icon_box"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cs_order.svg" alt="주문배송조회" /></div>
					 <div class="tit_en">ORDER</div>
					 <div class="tit_ko">주문조회</div>
				</dt>
				<dd>
					<a href="<?php echo (is_login() || $none_member_buy==true?'/?pn=mypage.order.list':'/?pn=service.guest.order.list'); ?>" class="btn_order">나의 주문/배송조회</a>
				</dd>
			</dl>
			<dl>
				<dt>
					<div class="icon_box"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cs_service.svg" alt="고객센터" /></div>
					 <div class="tit_en"><a href="/?pn=service.main">SERVICE</a></div>
					 <div class="tit_ko">고객센터</div>
				</dt>
				<dd>
					 <a href="tel:<?php echo $siteInfo['s_glbtel']; ?>" class="tel"><?php echo $siteInfo['s_glbtel']; ?></a>
					 <div class="open_time"><?php echo nl2br($siteInfo['s_cs_info']); ?></div>
				</dd>
			</dl>
			<?php
				$NoneBank = _MQ_assoc(" select * from smart_bank_set where (1) order by bs_idx asc ");
				if(count($NoneBank)>0){
				?>
			<dl>
				<dt>
					<div class="icon_box"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cs_bank.svg" alt="무통장 안내" /></div>
					<div class="tit_en">BANK</div>
					<div class="tit_ko">입금안내</div>
				</dt>
				<dd>
					<ul class="bank_list">
						<?php foreach($NoneBank as $k=>$v) { ?>
							<li><?php echo $v['bs_bank_name']; ?> <?php echo $v['bs_bank_num']; ?> (<?php echo $v['bs_user_name']; ?>)</li>
						<?php } ?>
					</ul><!-- end bank_list -->
				</dd>
			</dl>
			<?php }?>

			<?php if($siteInfo['sns_link_instagram'].$siteInfo['sns_link_facebook'].$siteInfo['sns_link_twitter'].$siteInfo['sns_link_blog'].$siteInfo['sns_link_cafe'].$siteInfo['sns_link_youtube'].$siteInfo['sns_link_kkp'].$siteInfo['sns_link_kks'] != '') { ?>
				<dl>
					<dt>
						<div class="icon_box"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/cs_sns.svg" alt="소셜채널" /></div>
						<div class="tit_en">SNS</div>
						<div class="tit_ko">공식채널</div>
					</dt>
					<dd>
						<ul class="sns_list">
							<?php if($siteInfo['sns_link_instagram']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_instagram']; ?>" class="btn_sns" title="인스타그램" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_insta.svg" alt="인스타그램" />
									</a>
								</li>
							<?php } ?>
							<?php if($siteInfo['sns_link_facebook']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_facebook']; ?>" class="btn_sns" title="페이스북" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_facebook.svg" alt="페이스북" />
									</a>
								</li>
							<?php } ?>
							<?php if($siteInfo['sns_link_twitter']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_twitter']; ?>" class="btn_sns" title="트위터" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_twiter.svg" alt="트위터" />
									</a>
								</li>
							<?php } ?>
							<?php if($siteInfo['sns_link_blog']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_blog']; ?>" class="btn_sns" title="블로그" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_blog.svg" alt="블로그" />
									</a>
								</li>
							<?php } ?>
							<?php if($siteInfo['sns_link_kkp']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_kkp']; ?>" class="btn_sns" title="카카오톡 채널" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_kakao.svg" alt="카카오톡 채널" />
									</a>
								</li>
							<?php } ?>
							<?php if($siteInfo['sns_link_youtube']) { ?>
								<li>
									<a href="<?php echo $siteInfo['sns_link_youtube']; ?>" class="btn_sns" title="유튜브" target="_blank">
										<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/sns_youtube.svg" alt="유튜브" />
									</a>
								</li>
							<?php } ?>
						</ul><!-- end sns_list -->
					</dd>
				</dl>
			<?php } ?>
		</div>
    </div><!-- end layout_fix -->
</div><!-- end sc_Service -->

