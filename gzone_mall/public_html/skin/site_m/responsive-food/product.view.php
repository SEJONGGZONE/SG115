<?php // 상세 헤더(모바일용) ?>
<div class="p_Vtit">
    <div class="layout_fix">
        <div class="inner">
            <div class="left">
                <a href="#none" onclick="history.go(-1); return false;" class="btn btn_back" title="뒤로가기"></a>
            </div>
            <div class="tit">상품상세</div>
            <div class="right">
                <a href="#none" onclick="return false;" class="btn btn_share js_onoff_event" data-target=".p_Vshare" data-add="if_open_share" title="공유하기"></a>
                <a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"></a>
                <a href="/" class="btn btn_home" title="홈으로"></a>
            </div>
        </div>
    </div>
</div><!-- end p_Vtit -->





<?php // 상세 탑 ?>
<div class="p_Vtop">
    <div class="area layout_fix">

        <?php // 상세 사진+해시태그 =================================== ?>
        <div class="area_box this_photo">
            <div class="layout_fix">
                <?php // 상세 사진 ?>
                <div class="p_Vphoto">
                    <div class="big_photo">
                        <div class="rolling_wrap">
                            <div class="rolling_box js_photo_large_slider">
                                <div class="swiper-wrapper">
                                    <?php
                                    if(count($pro_img)>0){ // 이미지 있을때
                                        foreach($pro_img as $k=>$v){
                                            $_pimg = get_img_src($v);
                                            ?>
                                            <div class="thumb<?php echo (count($pro_img)>1?' swiper-slide':''); ?>" <?php echo ($k>0?'style="display:none;"':''); ?>>
                                                <?php // 썸네일 : 590 * 590 ?>
                                                <?php if($_pimg){ ?><img src="<?php echo $_pimg; ?>" alt="<?php echo addslashes($pro_name); ?>"><?php } ?>
                                            </div>
                                        <?php }
                                    }else{ // 이미지 없을때
                                        ?>
                                        <div class="thumb">
                                            <img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes($pro_name); ?>">
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div><!-- end rolling_wrap -->

                        <?php // 롤링 컨트롤러(롤링일때만 노출) ?>
                        <?php if(count($pro_img)>1){?>
                            <div class="rolling_ctrl">
                                <a href="#none" class="js_photo_large_prevnext prevnext prev" onclick="return false;" data-type="prev" title="이전"></a>
                                <?php // 롤링 페이징 ?>
                                <span class="pagi">
									<strong class="active js_photo_large_pagenum">1</strong><em>/</em><strong><?php echo count($pro_img);?></strong>
								</span>
                                <a href="#none" class="js_photo_large_prevnext prevnext next" onclick="return false;" data-type="next" title="다음"></a>
                            </div><!-- end rolling_ctrl -->
                        <?php }?>
                    </div><!-- end big_photo -->

                    <?php if(count($pro_img)>1){ ?>
                        <?php // 롤링 썸네일 ?>
                        <div class="rolling_thumb">
                            <ul class="js_photo_large_pager">
                                <?php
                                foreach($pro_img as $k=>$v){
                                    $_pimg = get_img_src('thumbs_s_' . $v);
                                    if($_pimg == '') $_pimg = $SkinData['skin_url'] . '/images/none_photo.png';
                                    ?>
                                    <?php // .hit 기능 안됨 ?>
                                    <li class="<?php echo ($k === 0?'hit':''); ?>">
                                        <a href="#none" onclick="return false;" data-slide-index="<?php echo $k; ?>" class="box<?php echo ($k === 0?' active':''); ?>" title="">
                                            <img src="<?php echo $_pimg; ?>" alt="<?php echo addslashes($pro_name); ?>">
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div><!-- end rolling_thumb -->
                    <?php } ?>

                    <?php if(sizeof($pro_img)>1) { ?>
                        <script type="text/javascript">
                            $(document).ready(function(){
                                $('.js_photo_large_slider .swiper-slide').show();
                                var photo_large = new Swiper('.js_photo_large_slider', {
                                    effect: 'slide',
                                    slidesPerView: 1,
                                    autoplay : 4000,
                                    speed: 500,
                                    parallax:true,
                                    autoHeight:true,
                                    autoplayDisableOnInteraction : false,
                                    loop : true,
                                    spaceBetween: 0,
                                });

                                // 슬라이드 변경시 실행되는 이벤트
                                photo_large.on('slideChangeTransitionStart', function() {

                                    // li 에 hit 클래스 추가
                                    var index = photo_large.realIndex;
                                    $('.js_photo_large_pager li').removeClass('hit');
                                    $('.js_photo_large_pager li a[data-slide-index='+index+']').parent().addClass('hit');


                                    // 롤링 페이지 '01' 두자리수 형태
                                    var pagenum_index = index+1;
                                    $('.js_photo_large_pagenum').text(pagenum_index);

                                });

                                // 작은 썸네일 클릭
                                $('.js_photo_large_pager li a').on('click', function(){
                                    var idx = $(this).attr('data-slide-index')*1 + 1;
                                    photo_large.slideTo(idx);
                                });

                                // 이전 다음 클릭시 실행되는 이벤트
                                $('.js_photo_large_prevnext').on('click', function() {
                                    var data = $(this).data();
                                    if(data.type=='next'){
                                        // 다음 슬라이드로 이동
                                        photo_large.slideNext();
                                    }else{
                                        // 이전 슬라이드로 이동
                                        photo_large.slidePrev();
                                    }
                                });
                            });

                        </script>
                    <?php } ?>
                </div><!-- end p_Vphoto -->

                <?php if(count($pro_hashtag)>0){ ?>
                    <?php // 해시태그 ?>
                    <div class="p_Vhashtag">
                        <div class="swip_box" ID="js_hashtag_iscroll">
                            <ul>
                                <?php foreach($pro_hashtag as $k=>$v){ ?>
                                    <li><a href="/?pn=product.search.list&search_word=%23<?php echo urlencode(trim($v)); ?>" class="tag" target="_blank" title="">#<?php echo trim($v); ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div><!-- end p_Vhashtag -->
                    <script>
                        // KAY :: 2022-12-12 :: 아이스크롤 스크립트
                        // ex ) cate2depth_iscroll => ID 로 변경
                        var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_hashtag_iscroll');

                        // 스와이프 적용
                        $(document).ready(function(){
                            func_hashtag_iscroll();
                        });

                        // 사용시 ID로 지정후 해당 ID를 변경
                        // ex ) #js_cate2depth_iscroll=> #ID 로 변경
                        function func_hashtag_iscroll(){

                            $.each($('#js_hashtag_iscroll li'), function(k, v){  scrollWidth += $('#js_hashtag_iscroll li').eq(k).outerWidth()*1; });
                            var len = $('#js_hashtag_iscroll li').length;
                            HashtagScroll = new IScroll('#js_hashtag_iscroll', {
                                'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
                                , mouseWheel: true
                            });

                            if(scrollIndex > 0 && $('#js_hashtag_iscroll li.hit').length > 0) {
                                var scrollOffset = ($(window).width()*1/2 - $('#js_hashtag_iscroll li.hit').outerWidth()*1/2 ) * -1;
                                HashtagScroll.scrollToElement(document.querySelector('#js_hashtag_iscroll li.hit'), 500, scrollOffset);
                            }
                        }
                    </script>
                <?php } ?>
            </div>
        </div><!-- end area_box / this_photo -->

        <?php // 상세 정보+옵션 =================================== ?>
        <div class="area_box this_info">
            <div class="layout_fix">

                <?php // 상품이름/가격 ?>
                <div class="p_Vname">
                    <?php // 타이머 ?>
                    <?php if($p_info['p_time_sale']=='Y'){?>
                        <div class="item_timer js_time_sale_timmer <?php echo $p_timesale_type_chk=='N'?'if_timeout':''?>" data-pcode="<?php echo $pcode;?>" data-chk="<?php echo $chkSecond; ?>" data-d="<?php echo $arr_current_time['day'] ?>" data-h="<?php echo $arr_current_time['hour'] ?>" data-i="<?php echo $arr_current_time['minut'] ?>" data-s="<?php echo $arr_current_time['second'] ?>" data-timesaletype="<?php echo $p_timesale_type_chk;?>" data-beforetimesale="<?php echo $p_timesale_before_chk;?>">
                            <span class="clock js_time_sale_icon"><span class="bar hour"></span><span class="bar minute"></span></span>

                            <?php if($p_timesale_before_chk=='N'){?>
                                <?php if($p_timesale_type_chk=='Y'){?>
                                    <?php if($arr_current_time['day']<=1){?>
                                        <strong class="js_timer js_time_sale_timer_h"><?php echo $arr_current_time['hour'];?></strong>
                                        <em class="js_timer">:</em>
                                        <strong class="js_timer js_time_sale_timer_m"><?php echo $arr_current_time['minut'];?></strong>
                                        <em class="js_timer">:</em>
                                        <strong class="js_timer js_time_sale_timer_s"><?php echo $arr_current_time['second'];?></strong>
                                    <?php }else{ ?>
                                        <em class="last"><?php echo $arr_current_time['day'].'일';?> 남음</em>
                                    <?php }?>
                                <?php }else{?>
                                    <em class="last">Timeout</em>
                                <?php }?>
                            <?php }else{?>
                                <em class="last">Coming</em>
                            <?php }?>


                        </div>
                    <?php }?>

                    <?php if($pro_brand_name && in_array('brand', $ex_display_add_info)){ ?>
                        <a href="/?pn=product.brand_list&uid=<?php echo $pro_brand_uid; ?>" target="_blank" class="brand">#<?php echo $pro_brand_name; ?></a>
                    <?php } ?>

                    <?php echo ($app_pro_icon ? '<div class="item_icon">'.$app_pro_icon.'</div>' : ''); ?>

                    <dl class="item_name">
                        <dt><?php echo $pro_name; ?></dt>
                        <?php if($pro_subname){ // 설명글 ?>
                            <dd><?php echo $pro_subname; ?></dd>
                        <?php } ?>
                    </dl>


                    <div class="item_price">
                        <?php if($pro_screenprice){ // 정상가 ?>
                            <div class="before"><?php echo $pro_screenprice; ?></div>
                        <?php } ?>
                        <div class="price_in">
                            <?php if($pro_price){ // 판매가 ?><div class="after"><span class="won"><?php echo $pro_price; ?></span><span class="won_t">원</span></div><?php } ?>
                            <?php if($p_info['p_screenPrice'] > $p_info['p_price'] && DCPer($p_info['p_screenPrice'], $p_info['p_price'])>0) { // 할인율 ?>
                                <div class="percent"><?php echo DCPer($p_info['p_screenPrice'], $p_info['p_price']); ?>%</div>
                            <?php } ?>
                        </div>
                    </div><!-- end item_price -->

                    <?php if($pro_point || ($groupSetUse === true && ($groupSetInfo['mgs_sale_price_per']+$groupSetInfo['mgs_give_point_per']) > 0) ){?>
                        <ul class="benefit">
                            <?php // 상품 적립금(적립율 노출) ?>
                            <?php if($pro_point && in_array('point', $ex_display_add) ){ ?>
                                <li><span class="tx"><strong><?php echo $pro_point_per;?>%(<?php echo $pro_point; ?>원)</strong> 적립</span></li>
                            <?php } ?>
                            <?php if(is_login() && in_array('indrbenefits', $ex_display_add)) { //로그인 후 ?>
                                <?php // 여기서 부터는 회원 추가적립/할인 혜택 정보 노출 ?>
                                <?php if( $groupSetInfo['mgs_give_point_per'] >0){?>
                                    <li class="mb_add"><span class="tx"><strong><?php echo odt_number_format($groupSetInfo['mgs_give_point_per'],1)?>%</strong> 추가적립</span></li>
                                <?php }?>
                                <?php if( $groupSetInfo['mgs_sale_price_per'] >0){?>
                                    <li class="mb_add"><span class="tx"><strong><?php echo odt_number_format($groupSetInfo['mgs_sale_price_per'],1)?>%</strong> 추가할인</span></li>
                                <?php }?>
                            <?php } ?>
                        </ul><!-- end benefit -->
                    <?php }?>



                </div><!-- end p_Vname -->

                <?php if($ex_coupon['name'] && $ex_coupon[1] && in_array('coupon', $ex_display_add)){ ?>
                    <?php // 상품쿠폰 ?>
                    <div class="p_Vcoupon">
                        <div class="coupon_box">
                            <dl class="name">
                                <dt>COUPON</dt>
                                <dd><?php echo stripslashes($ex_coupon['name']); ?></dd>
                            </dl>
                            <dl class="discount">
                                <?php if($ex_coupon[1]=="price"){ ?>
                                    <dt><?php echo number_format($ex_coupon['price']); ?>원 할인</dt>
                                <?php }else {?>
                                    <dt><?php echo floor($ex_coupon['per']*10)/10; ?>% 할인</dt>
                                    <?php if($ex_coupon['max']>0){?>
                                        <dd>(최대 <?php echo number_format($ex_coupon['max']); ?>원)</dd>
                                    <?php }?>
                                <?php }?>
                            </dl>
                        </div>
                    </div><!-- end p_Vcoupon -->
                <?php } ?>

                <?php if($eval_cnt > 0){ //리뷰 있을 때 노출 ?>
                    <?php // 리뷰 ?>
                    <div class="p_Vreview">
                        <div class="score" onclick="scrolltoClass('.js_eval_position',-10); return false;" >
							<span class="mark">
								<span class="star this_value" style="width:<?php echo $star_persent; ?>%"></span>
								<span class="star this_base"></span>
							</span>
                            <span class="num">
								<?php
                                if($star_persent == 0){
                                    echo number_format($star_persent/20,0);
                                }else{
                                    echo number_format($star_persent/20,1);
                                }
                                ?>
							</span>
                        </div>
                        <span class="total" onclick="scrolltoClass('.js_eval_position',-10); return false;" ><em><?php echo $eval_cnt; ?></em>개 리뷰가 있습니다.</span>
                    </div><!-- end p_Vreview -->
                <?php } ?>


                <?php
                switch($p_info['p_shoppingPay_use']){
                    case 'Y': $pro_delivery = '개별배송 ' . number_format($pro_delivery_info['price']) . '원'; break;
                    case 'N':
                        $pro_delivery = '기본 ' . number_format($pro_delivery_info['price']).'원';
                        if($pro_delivery_info['freePrice'] > 0) $pro_delivery .= ' ('.number_format($pro_delivery_info['freePrice']) . '원 이상 무료)';
                        break;
                    case 'F': $pro_delivery = '무료배송'; break; //무료배송
                    case 'P': $pro_delivery = '상품별 배송비 ' . number_format($pro_delivery_info['price']).'원'.($pro_delivery_info['freePrice'] > 0 ? ' ('.number_format($pro_delivery_info['freePrice']) . '원 이상 무료)' : null); break; // 상품별
                }
                ?>

                <?php if( $view_product_info === true){?>
                    <div class="p_Vinfo">

                        <?php if($pro_maker){ ?>
                            <dl>
                                <dt>제조사</dt>
                                <dd><?php echo $pro_maker; ?></dd>
                            </dl>
                        <?php } ?>
                        <?php if($pro_orgin){ ?>
                            <dl>
                                <dt>원산지</dt>
                                <dd><?php echo $pro_orgin; ?></dd>
                            </dl>
                        <?php } ?>

                        <?php if($p_info['p_type'] == 'delivery'){ ?>
                            <dl>
                                <dt>배송정보</dt>
                                <dd>
                                    <?php // 택배사 ?>
                                    <?php if($pro_delivery_info['del_company']!=''){?>
                                        <a href="#none" onclick="scrolltoClass('.js_guide_position',-10); return false;" class="btn_view"><?php echo $pro_delivery_info['del_company'];?></a>
                                    <?php }?>

                                    <?php // 배송비 ?>
                                    <div class="delivery_fee"><?php echo $pro_delivery;?></div>

                                    <?php if($p_info['p_delivery_info']!=''){?>
                                        <?php // 배송정보 ?>
                                        <div class="delivery_fee"><?php echo $p_info['p_delivery_info'];?></div>
                                    <?php }?>
                                </dd>
                            </dl>
                        <?php } ?>

                        <?php if( $p_info['p_com_juso'] != '' && $p_info['p_type'] == 'ticket' ){?>
                            <dl>
                                <dt>사용처</dt>
                                <dd>
                                    <?php // 장소이름(티켓 사용처 위치정보로 스크롤 이동되게) ?>
                                    <a href="#none" onclick="return false;" class="btn_place js_product_view_map_view"><?php echo $_com_locname; ?></a>
                                </dd>
                            </dl>
                        <?php } ?>


                        <?php if( $app_buy_limit_auth === true ) { ?>
                            <dl>
                                <dt>구매제한</dt>
                                <dd>
                                    <?php if( $p_info['p_buy_limit'] > 0){ // 1회최대 구매제한?>
                                        <span class="multi">옵션별 <strong>최대 <?php echo number_format($p_info['p_buy_limit']) ?>개</strong></span>
                                    <?php } ?>
                                    <?php if( $p_info['p_duplicate_use'] == 'N'){ // 중복구매?>
                                        <span class="multi">중복구매 <strong>불가</strong></span>
                                    <?php } ?>
                                </dd>
                            </dl>
                        <?php }  ?>



                        <?php
                        if($p_info['p_sale_type'] == 'T' ){ // 기간판매이고, 판매기간이 남았을 시
                            ?>
                            <?php // 상시판매일땐 비노출 ?>
                            <dl>
                                <dt>판매기한</dt>
                                <dd>
                                    <?php // 월,일은 2자리수 맞추기 ?>
                                    <span class="multi"><?php echo $app_product_sale_date_view; ?></span>
                                    <span class="multi">
										<?php // 마감일이 오늘일땐 '오늘마감'으로 표시 ?>
										<strong><?php echo $app_product_sale_day_view; ?></strong>
									</span>
                                </dd>
                            </dl>
                        <?php }?>

                        <?php
                        if($p_info['p_dateoption_use'] == 'N' && $p_info['p_type']=='ticket' ){ // 기간판매이고, 판매기간이 남았을 시
                            ?>
                            <dl>
                                <dt>유효기간</dt>
                                <dd>
                                    <span class="multi"><?php echo $app_product_expire_text; ?></span>
                                </dd>
                            </dl>
                        <?php }?>
                    </div><!-- end p_Vinfo -->
                <?php } ?>



                <?php // 떠다니는 옵션 ?>
                <div class="p_Vfix view_cart_ask ">
                    <div class="white_box">
                        <div class="scroll_box c_scroll_v">
                            <a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".p_Vfix" data-add="if_open_fix" title="닫기"></a>
                            <?php
                            if($p_info['p_dateoption_use'] == 'Y' && $p_info['p_option_type_chk'] != 'nooption' && $isSoldOut == false && $app_product_sale_use === true  && $isTimeSaleOut===false && $isTimeSaleBefore===false){
                                include_once dirname(__FILE__)."/product.view.inc_calendar.php";
                            }
                            ?>

                            <?php // 상세 옵션 ?>
                            <div class="p_Vopt">
                                <?php
                                // -- 옵션 없을 경우 ----
                                if($p_info['p_option_type_chk'] == 'nooption' && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false && $app_product_sale_use === true && $isTimeSaleOut===false && $isTimeSaleBefore===false){
                                    ?>
                                    <dl class="this_volume">
                                        <dt>수량</dt>
                                        <dd>
                                            <div class="counter_box">
                                                <?php if($p_info['p_stock'] > 0){ ?>
                                                    <?php // 1개일땐 빼기 버튼 클릭 안되게 .if_no 클래스 추가 ?>
                                                    <a href="#none" onclick="pro_cnt_down(); return false;" class="btn_down if_no js_option_cnt_down"><span class="shape"></span></a>
                                                    <input type="text" name="option_select_cnt" id="option_select_cnt" class="updown_input" value="1" readonly>
                                                    <a href="#none" onclick="pro_cnt_up(); return false;" class="btn_up"><span class="shape"></span></a>
                                                <?php }else{ ?>
                                                    일시품절<input type="hidden" name="option_select_cnt" class="input_num" id="option_select_cnt" value="0" />
                                                <?php } ?>
                                                <input type="hidden" name="option_select_expricesum" ID="option_select_expricesum" value="<?php echo ($p_info['p_price']-getGroupSetPer($p_info['p_price'],'price',$pcode)); ?>">
                                                <input type="hidden" name="option_select_type" id="option_select_type" value="<?php echo $p_info['p_option_type_chk']; ?>">
                                            </div>
                                        </dd>
                                    </dl>
                                    <?php
                                    // -- 옵션 있을 경우 ----
                                }else if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false && $app_product_sale_use === true && $isTimeSaleOut===false && $isTimeSaleBefore===false){
                                    ?>
                                    <div class="this_option">
                                        <?php // 필수 옵션 ?>
                                        <dl>
                                            <dt>필수 옵션</dt>
                                            <dd>

                                                <?php if($p_info['p_stock'] > 0){ ?>

                                                    <?php
                                                    // ------------------------- 1차 옵션 설정 -------------------------
                                                    // 1차 옵션이 normal 형일 경우 처리
                                                    //			옵션형태 : normal , color , size
                                                    if($p_info['p_option1_type'] == 'normal') {
                                                        $p_option_title = $p_info['p_option1_title']!=''?$p_info['p_option1_title']:'옵션을 선택해주세요.';
                                                        ?>
                                                        <div class="opt_dropbox js_box_optdropbox<?php echo $p_info['p_dateoption_use'] == 'Y'  ? ' before':'' ?>" id="span_option1"  data-idx="1" >
                                                            <div class="opt_tx this_selected js_btn_optdropbox">
                                                                <strong id="option_select1_poname"><?php echo $p_option_title;?></strong>
                                                            </div>
                                                            <div class="opt_list" id="option_select_1_box">
                                                                <div class="opt_tx js_btn_optdropbox">
                                                                    <strong><?php echo $p_option_title;?></strong>
                                                                </div>
                                                                <div class="c_scroll_v">
                                                                    <?php foreach( $options as $k=>$sr){ ?>
                                                                        <?php
                                                                        // 품절 체크
                                                                        $app_soldout_chk = 'N';
                                                                        if($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ){
                                                                            $app_soldout_chk ='Y';
                                                                        }
                                                                        ?>
                                                                        <a href="#none" class="opt_tx <?php echo $app_soldout_chk=='Y'?'soldout':''; ?>" value="<?php echo $sr['po_uid']; ?>" onclick="option_select_tmp('1','<?php echo $p_info['p_option_type_chk']; ?>','<?php echo $sr['po_uid']; ?>','<?php echo $sr['po_poptionname']; ?>','<?php echo $p_info['p_code']; ?>'); return false;">
                                                                            <strong><?php echo $sr['po_poptionname']; ?></strong>
                                                                            <em class="opt_remain">
                                                                                <?php
                                                                                if($p_info['p_option_type_chk'] == '1depth'){
                                                                                    echo ($app_soldout_chk =='N' ? ($isOptionStock ? ' (' . number_format( 1 * $sr['po_cnt']) . '개 남음)' : null) . ' </em><em class="opt_price"> ' . number_format( 1 * $sr['po_poptionprice']) . '원' : ' 일시품절');
                                                                                }
                                                                                ?>
                                                                            </em>
                                                                        </a>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <span class="arrow"></span>
                                                        </div>
                                                        <?php
                                                    } // 1차 옵션이 normal 형일 경우 처리


                                                    // 1차 옵션이 color 형일 경우 처리
                                                    else if($p_info['p_option1_type'] == 'color') {
                                                        $p_option_title = $p_info['p_option1_title']!=''?$p_info['p_option1_title']:'Color 옵션';
                                                        ?>
                                                        <div class="opt_other">
                                                            <div class="this_opt_tit"><?php echo $p_option_title;?></div>
                                                            <ul>
                                                                <?php
                                                                foreach( $options as $k=>$sr){

                                                                    // 품절여부
                                                                    $app_soldout_class ='';
                                                                    $app_soldout_chk ='N';

                                                                    if($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ){
                                                                        $app_soldout_class = 'soldout';
                                                                        $app_soldout_chk ='Y';
                                                                    }

                                                                    //색상 or 이미지
                                                                    $app_color_name = (
                                                                    $sr['po_color_type'] == 'img' ?
                                                                        'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
                                                                        'background:' . $sr['po_color_name']
                                                                    );
                                                                    ?>
                                                                    <li>
                                                                        <label class="<?php echo $app_soldout_class?>">
                                                                            <input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')" />
                                                                            <span class="tx">
																				<span class="shape" style="<?=$app_color_name?>"></span>
																				<strong><?=$sr['po_poptionname']?></strong>
																				<em class="opt_remain">
																				<?php
                                                                                if($p_info['p_option_type_chk'] == '1depth'){
                                                                                    echo ($app_soldout_chk =='N' ? ($isOptionStock ? ' (' . number_format( 1 * $sr['po_cnt']) . '개 남음)' : null) . ' </em><em class="opt_price"> ' . number_format( 1 * $sr['po_poptionprice']) . '원' : ' 일시품절');
                                                                                }
                                                                                ?>
																				</em>
																			</span>
                                                                        </label>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>

                                                        </div>
                                                        <?php
                                                    } // 1차 옵션이 color 형일 경우 처리


                                                    // 1차 옵션이 size 형일 경우 처리
                                                    else if($p_info['p_option1_type'] == 'size') {
                                                        $p_option_title = $p_info['p_option1_title']!=''?$p_info['p_option1_title']:'Size 옵션';
                                                        ?>
                                                        <div class="opt_other">
                                                            <div class="this_opt_tit"><?php echo $p_option_title;?></div>
                                                            <ul>
                                                                <?php
                                                                foreach( $options as $k=>$sr){

                                                                    // 품절여부
                                                                    $app_soldout_class ='';
                                                                    $app_soldout_chk ='N';

                                                                    if($p_info['p_option_type_chk'] == '1depth' && $sr['po_cnt'] <= 0 ){
                                                                        $app_soldout_class = 'soldout';
                                                                        $app_soldout_chk ='Y';
                                                                    }

                                                                    ?>
                                                                    <li>
                                                                        <label class="<?=$app_soldout_class?>">
                                                                            <input type="radio" name="_option_select1" onclick="option_select_tmp2('1' , '<?=$p_info['p_option_type_chk']?>' , '<?=$sr['po_uid']?>' , '<?=$p_info['p_code']?>')"  <?=($app_soldout_class == 'soldout' ? 'disabled' : '')?> />
                                                                            <span class="tx">
																				<strong><?php echo $sr['po_poptionname']?></strong>
																				<?php // 가격 노출 ?>
																				<em class="opt_remain">
																				<?php
                                                                                if($p_info['p_option_type_chk'] == '1depth'){
                                                                                    echo ($app_soldout_chk =='N' ? ($isOptionStock ? ' (' . number_format( 1 * $sr['po_cnt']) . '개 남음)' : null) . ' </em><em class="opt_price"> ' . number_format( 1 * $sr['po_poptionprice']) . '원' : ' 일시품절');
                                                                                }
                                                                                ?>
																				</em>
																			</span>
                                                                        </label>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>

                                                        <?php
                                                    } // 1차 옵션이 size 형일 경우 처리
                                                    // ------------------------- 1차 옵션 설정 -------------------------
                                                    ?>


                                                <?php }else{ ?>
                                                    <div class="opt_dropbox">
                                                        <select name="">
                                                            <option value="">품절</option>
                                                        </select>
                                                        <span class="arrow"></span>
                                                    </div>
                                                <?php } ?>

                                                <input type="hidden" name="_option_select1" ID="option_select1_id" value="">


                                                <?php
                                                if($p_info['p_stock'] > 0){

                                                    //일반형일 경우 2차 옵션 클래스
                                                    $app_depth2_class="opt_dropbox";
                                                    switch($p_info['p_option2_type']){
                                                        case "color": $app_depth2_class="opt_other"; break;//컬러형일 경우 옵션 클래스
                                                        case "size": $app_depth2_class="opt_other"; break;//사이즈형일 경우 옵션 클래스
                                                    }

                                                    //일반형일 경우 3차 옵션 클래스
                                                    $app_depth3_class="opt_dropbox";
                                                    switch($p_info['p_option3_type']){
                                                        case "color": $app_depth3_class="opt_other"; break;//컬러형일 경우 옵션 클래스
                                                        case "size": $app_depth3_class="opt_other"; break;//사이즈형일 경우 옵션 클래스
                                                    }

                                                    ?>

                                                    <?php if( in_array($p_info['p_option_type_chk'], array('2depth','3depth')) ){  ?>
                                                        <div class="<?php echo $app_depth2_class?> before js_box_optdropbox" id="span_option2" data-idx="2" data-type="option">
                                                            <?php if($p_info['p_option2_type'] == 'normal' ){?>
                                                                <div class="opt_tx this_selected js_btn_optdropbox" >
                                                                    <strong id="option_select1_poname"><?php echo $p_info['p_option2_title']!=''?$p_info['p_option2_title']:'상위옵션을 먼저 선택해주세요.';?></strong>
                                                                </div>
                                                                <span class="arrow"></span>
                                                            <?php }else{?>
                                                                <?php
                                                                $p_option2_title = $p_info['p_option2_type']=='color'?'Color 옵션':'Size 옵션';
                                                                $option2_title = $p_info['p_option2_title']?$p_info['p_option2_title']:$p_option2_title;
                                                                ?>
                                                                <div class="this_opt_tit"><?php echo $option2_title;?></div>
                                                            <?php }?>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if($p_info['p_option_type_chk'] == '3depth'){ ?>
                                                        <div class="<?php echo $app_depth3_class?> before js_box_optdropbox" id="span_option3" data-idx="3" data-type="option">
                                                            <?php if($p_info['p_option3_type'] == 'normal'){?>
                                                                <div class="opt_tx this_selected js_btn_optdropbox">
                                                                    <strong id="option_select1_poname"><?php echo $p_info['p_option3_title']!=''?$p_info['p_option3_title']:'상위옵션을 먼저 선택해주세요.';?></strong>
                                                                </div>
                                                                <span class="arrow"></span>
                                                            <?php }else{?>
                                                                <?php
                                                                $p_option3_title = $p_info['p_option3_type']=='color'?'Color 옵션':'Size 옵션';
                                                                $option3_title = $p_info['p_option3_title']?$p_info['p_option3_title']:$p_option3_title;
                                                                ?>
                                                                <div class="this_opt_tit"><?php echo $option3_title;?></div>
                                                            <?php }?>
                                                        </div>
                                                    <?php } ?>

                                                <?php } ?>
                                            </dd>
                                        </dl>


                                        <?php if(count($add_options)>0 && $p_info['p_stock'] > 0 && $p_info['p_type'] == 'delivery' ){ ?>
                                            <?php // 추가 옵션 ?>
                                            <dl>
                                                <dt>추가 옵션</dt>
                                                <dd>
                                                    <?php foreach($add_options as $k=>$v) { ?>
                                                        <div class="opt_dropbox js_box_optdropbox_add" data-idx="<?php echo ($k+1); ?>" >
                                                            <div class="opt_tx this_selected js_btn_optdropbox_add">
                                                                <strong><?php echo $v['pao_poptionname']!=''?$v['pao_poptionname']:'옵션을 선택해주세요.';?></strong>
                                                            </div>
                                                            <div class="opt_list" id="add_option_select_<?php echo ($k+1); ?>_box">
                                                                <div class="opt_tx js_btn_optdropbox_add">
                                                                    <strong><?php echo $v['pao_poptionname']!=''?$v['pao_poptionname']:'옵션을 선택해주세요.';?></strong>
                                                                </div>
                                                                <div class="c_scroll_v">
                                                                    <?php foreach($v['add_sub_options'] as $key=>$value){ ?>
                                                                        <?php
                                                                        // 품절 체크
                                                                        $app_soldout_chk = 'N';
                                                                        if($value['pao_cnt'] <= 0 ){
                                                                            $app_soldout_chk ='Y';
                                                                        }
                                                                        ?>

                                                                        <a href="#none" class="opt_tx <?php echo $app_soldout_chk=='Y'?'soldout':''; ?>" onclick="add_option_select_add('<?php echo $pcode; ?>', <?php echo $value['pao_uid']; ?> ,  <?php echo ($k+1); ?>,'<?php echo ($k+1); ?>'); return false;" >
                                                                            <strong><?php echo $value['pao_poptionname']; ?></strong>
                                                                            <em class="opt_remain">
                                                                                <?php
                                                                                echo ($app_soldout_chk =='N' ? ($isOptionStock ? ' (' . number_format( 1 * $value['pao_cnt']) . '개 남음)' : null) . ' </em><em class="opt_price"> ' . number_format( 1 * $value["pao_poptionprice"]) . '원' : ' 일시품절');
                                                                                ?>
                                                                            </em>
                                                                        </a>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="_add_option_select_<?php echo ($k+1); ?>" id="add_option_select_<?php echo ($k+1); ?>_id" class="add_option add_option_chk">
                                                            <span class="arrow"></span>
                                                        </div>

                                                    <?php } ?>
                                                </dd>
                                            </dl>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <?php if(count($options) > 0 && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false && $app_product_sale_use === true && $isTimeSaleOut===false&& $isTimeSaleBefore===false){ ?>
                                    <?php // 선택한 옵션 ?>
                                    <div class="type_result" id="span_seleced_list" style="display:none;">
                                    </div><!-- end type_result -->
                                <?php } ?>
                            </div><!-- end p_Vopt -->

                            <?php // 총 결제금액/버튼 ?>
                            <div class="fix_btm">
                                <?php
                                // 상품 옵션 설정이 등록되었을때만 노출
                                if(($p_info['p_option_type_chk'] == 'nooption' || count($options) > 0) && $p_info['p_option_valid_chk']=='Y' && $isSoldOut == false && $app_product_sale_use === true&& $isTimeSaleOut===false && $isTimeSaleBefore===false){
                                    ?>
                                    <div class="price_total">
                                        <span class="total_tt">총 합계금액</span>
                                        <strong id="option_select_expricesum_display">0</strong>
                                        <em>원</em>
                                    </div>
                                <?php } ?>

                                <?php
                                // 상품 옵션 설정이 등록되었을때만 노출
                                if(($p_info['p_option_type_chk'] == 'nooption' || count($options) > 0) && $p_info['p_option_valid_chk']=='Y'){
                                    ?>
                                    <ul class="buy_box">
                                        <?php if($isTimeSaleOut===true) {?>
                                            <li><a href="#none" onclick="return false;" class="btn btn_soldout">타임세일이 종료된 상품입니다</a></li>
                                        <?php }else if($isTimeSaleBefore===true) {?>
                                            <li>
                                                <a href="#none" onclick="return false;" class="btn btn_soldout">
                                                    <?php echo date('n월 j일',strtotime($p_info['p_time_sale_sdate'])).' '.date('H:i',strtotime($p_info['p_time_sale_sclock']));?> OPEN
                                                </a>
                                            </li>
                                        <?php }else if(($isSoldOut && $app_product_sale_use === true)){  ?>
                                            <li><a href="#none" onclick="return false;" class="btn btn_soldout">일시품절된 상품입니다</a></li>
                                        <?php }else if($app_product_sale_use !== true) {?>
                                            <li><a href="#none" onclick="return false;" class="btn btn_soldout"><?php echo $app_product_sale_notice ?></a></li>
                                        <?php }else{ ?>
                                            <li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','cart');"); ?>return false;" class="btn btn_cart">장바구니 담기</a></li>
                                            <li><a href="#none" onclick="<?php echo ($p_info['p_stock'] < 1 ? "app_soldout();" : "app_submit('".$pcode."','order');"); ?>return false;" class="btn btn_order">구매하기</a></li>
                                        <?php }?>
                                    </ul>
                                <?php } ?>
                            </div><!-- end ft_btn -->

                            <?php // NPAY { ?>
                            <?php
                            $NPayTrigger = 'N';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test') $NPayTrigger = 'Y';
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
                            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real' && $p_info['npay_use'] == 'Y') $NPayTrigger = 'Y'; // 버튼+주문연동 작업
                            if($NPayTrigger == 'Y') {
                                ?>
                                <div class="p_Vnaver">

                                    <?php if( is_mobile() == true){?>
                                        <script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/mobile/naverPayButton.js" charset="UTF-8"></script>

                                        <script type="text/javascript">
                                            //<![CDATA[
                                            function NPayBuy() {

                                                var pcode = '<?php echo $pcode; ?>';
                                                var _type = 'view';
                                                if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
                                                    alert("옵션을 하나 이상 선택해주시기 바랍니다.")
                                                }
                                                else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
                                                    alert("옵션 합계금액이 0원을 초과해야 합니다.")
                                                }
                                                else {
                                                    location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val());
                                                }
                                            }
                                            function NPayWish() {

                                                var pcode = '<?php echo $pcode; ?>';
                                                var _type = 'wish';
                                                var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val();
                                                location.href = LocationUrl;
                                                return false;
                                            }
                                            naver.NaverPayButton.apply({
                                                BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
                                                TYPE: "MA", // 버튼 모음 종류 설정
                                                COLOR: 1, // 버튼 모음의 색 설정
                                                COUNT: 2, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
                                                ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
                                                BUY_BUTTON_HANDLER: NPayBuy, // 구매하기 버튼
                                                WISHLIST_BUTTON_HANDLER: NPayWish, // 찜하기 버튼
                                                "":"",
                                            });
                                            //]]>
                                        </script>

                                    <?php }else{ ?>
                                        <script type="text/javascript" src="//<?php echo ($siteInfo['npay_mode'] == 'test'?'test-':null); ?>pay.naver.com/customer/js/naverPayButton.js" charset="UTF-8"></script>
                                        <script type="text/javascript">
                                            //<![CDATA[
                                            function NPayBuy() {

                                                var pcode = '<?php echo $pcode; ?>';
                                                var _type = 'view';
                                                if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
                                                    alert("옵션을 하나 이상 선택해주시기 바랍니다.")
                                                }
                                                else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
                                                    alert("옵션 합계금액이 0원을 초과해야 합니다.")
                                                }
                                                else {
                                                    location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val());
                                                }
                                            }
                                            function NPayWish() {

                                                var pcode = '<?php echo $pcode; ?>';
                                                var _type = 'wish';
                                                var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_cnt=' + $("#option_select_cnt").val();
                                                window.open(LocationUrl, '', "scrollbars=yes, width=400, height=267");
                                                return false;
                                            }

                                            naver.NaverPayButton.apply({
                                                BUTTON_KEY: "<?php echo $siteInfo['npay_bt_key']; ?>", // 페이에서 제공받은 버튼 인증 키 입력
                                                TYPE: "A", // 버튼 모음 종류 설정
                                                COLOR: 1, // 버튼 모음의 색 설정
                                                COUNT: 2, // 버튼 개수 설정. 구매하기 버튼만 있으면 1, 찜하기 버튼도 있으면 2를 입력.
                                                ENABLE: "Y", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
                                                BUY_BUTTON_HANDLER: NPayBuy, // 구매하기 버튼
                                                WISHLIST_BUTTON_HANDLER: NPayWish, // 찜하기 버튼
                                                "":"",
                                            });

                                            //]]>
                                        </script>
                                    <?php }?>
                                    <div class="npay_tip">네이버페이로 구매하실 경우,<br/><span class="red">도서산간지역은 별도의 배송료</span>가 추가됩니다.</div>
                                </div><!-- end p_Vnaver -->
                            <?php } ?>
                            <?php // } NPAY ?>

                        </div><!-- end scroll_box -->

                    </div><!-- end white_box -->
                    <?php // 배경 닫기 ?>
                    <div onclick="return false;" class="bg_close js_onoff_event" data-target=".p_Vfix" data-add="if_open_fix"></div>

                    <?php // // LCY : 2022-12-21 : 티켓기능 -- 상품의 정보를 담는다. ?>
                    <form id="frmProductView">
                        <input type="hidden" name="product_option_type_chk" value="<?php echo $p_info['p_option_type_chk'] ?>">
                        <input type="hidden" name="product_type" value="<?php echo $p_info['p_type'] ?>">
                        <input type="hidden" name="product_dateoption_use" value="<?php echo $p_info['p_dateoption_use'] ?>">
                        <input type="hidden" name="product_buy_limit" value="<?php echo $p_info['p_buy_limit'] ?>">
                        <input type="hidden" name="selDate" value="">
                    </form>


                </div><!-- end p_Vfix -->

                <?php // 떠다니는 옵션 열기 버튼(모바일) ?>
				<div class="p_Vfixbtn">
					<ul>
						<li class="type_wish"><a href="#none" onclick="return false;" class="btn_wish js_wish<?php echo (is_wish($p_info['p_code'])?' hit':null); ?>" data-pcode="<?php echo $p_info['p_code']; ?>" title="찜하기"></a></li>
						<?php if($isTimeSaleOut===true) {?>
							<li><a href="#none" class="btn btn_soldout">타임세일이 종료된 상품입니다</a></li>
						<?php }else if($isTimeSaleBefore===true) {?>
							<li>
								<a href="#none" onclick="return false;" class="btn btn_soldout"><?php echo date('n월 j일',strtotime($p_info['p_time_sale_sdate'])).' '.date('H:i',strtotime($p_info['p_time_sale_sclock']));?> OPEN</a>
							</li>
						<?php }else if($isSoldOut && $app_product_sale_use === true){ // 품절일때  ?>
							<li><a href="#none" class="btn btn_soldout">일시품절된 상품입니다</a></li>
						<?php }else if($app_product_sale_use !== true) {?>
							<li><a href="#none" class="btn btn_soldout"><?php echo $app_product_sale_notice ?></a></li>
						<?php }else{ ?>
							<li><a href="#none" onclick="return false;" class="btn btn_buy js_onoff_event" data-target=".p_Vfix" data-add="if_open_fix">구매하기</a></li>
						<?php }?>
					</ul>
				</div><!-- end p_Vfixbtn -->

            </div>
        </div><!-- end area_box / this_info -->

    </div><!-- end area / layout_fix -->
</div><!-- end p_Vtop -->


<?php
$SNSSendUse = array($siteInfo['facebook_share_use'], $siteInfo['kakao_share_use'], $siteInfo['twitter_share_use'], $siteInfo['pinter_share_use']);
if(in_array('Y', $SNSSendUse)) {
    ?>
    <?php // 공유하기 열림 ?>
    <div class="p_Vshare">
        <div class="white_box">
            <div class="tit">이 상품 공유하기</div>
            <ul>
                <?php if($siteInfo['kakao_share_use'] == 'Y' && $siteInfo['kakao_js_api'] != '') { ?>
                    <li><a href="#none" onclick="sendSNS('kakao'); return false;" class="sns kakao" title="카카오톡 공유하기"></a></li>
                <?php } ?>
                <?php if($siteInfo['facebook_share_use'] == 'Y' && $siteInfo['s_facebook_key'] != '') { ?>
                    <li><a href="#none" onclick="sendSNS('facebook'); return false;" class="sns face" title="페이스북 공유하기"></a></li>
                <?php } ?>
                <?php if($siteInfo['twitter_share_use'] == 'Y') { ?>
                    <li><a href="#none" onclick="sendSNS('twitter'); return false;" class="sns twitt" title="트위터 공유하기"></a></li>
                <?php } ?>
                <?php
                $view_url = $system['url'].'/?pn=product.view&pcode='.$pcode;
                ?>
                <li><a href="#none" class="sns url js-clipboard" data-clipboard-text="<?php echo $view_url;?>" data-clipboard-type="url" title="URL 복사"></a></li>
            </ul>
            <a href="#none" onclick="return false;" class="btn_confirm js_onoff_event" data-target=".p_Vshare" data-add="if_open_share">확인</a>
        </div>
        <?php // 배경 닫기 ?>
        <div onclick="return false;" class="bg_close js_onoff_event" data-target=".p_Vshare" data-add="if_open_share"></div>
    </div><!-- end share_open -->
<?php } ?>







<?php
if(count($ProductMiddle)>0){
    ?>
    <?php // 상세배너 (없으면 전체 숨김) ?>
    <div class="p_Vbanner">
        <div class="layout_fix">
            <?php
            foreach($ProductMiddle as $k=>$v){
                $_img = get_img_src($v['b_img'], IMG_DIR_BANNER);
                $_img_mo = get_img_src($v['b_img_mo'], IMG_DIR_BANNER);
                ?>
                <div class="banner">
                    <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?><a href="<?php echo $v['b_link']; ?>" target="<?php echo $v['b_target']; ?>"><?php } ?>
                        <?php if($v['b_img_mo']!=''){?>
                            <img src="<?php echo $_img_mo; ?>" alt="<?php echo addslashes($v['b_title']); ?>" class="img_mo">
                        <?php }?>
                        <?php // 여기 사이에 소스삽입금지 ?>
                        <img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['b_title']); ?>" class="img_pc">
                        <?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?></a><?php } ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div><!-- end p_Vbn -->
    <?php
}
?>







<?php // 컨텐츠 : 정보 ?>
<div class="p_Vconts js_info_position">
    <?php // 탭 메뉴 ?>
    <div class="tab_menu">
        <div class="layout_fix">
            <div class="tab_in">
                <ul>
                    <li class="hit">
                        <a href="#none" onclick="scrolltoClass('.js_info_position',-10); return false;" class="tab">
                            <em>상세</em><strong>정보</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_eval_position',-10); return false;" class="tab">
                            <strong>리뷰</strong><span class="num js_tap_eval_cnt">(<?php echo $eval_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_qna_position',-10); return false;" class="tab">
                            <strong>문의</strong><span class="num qna_cnt">(<?php echo $qna_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_guide_position',-10); return false;" class="tab">
                            <em>구매</em><strong>안내</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- end tab_box -->

    <?php // 탭 내용 ?>
    <div class="tab_conts">
        <div class="layout_fix">
            <div class="detail js_content_wrap">
                <div class="editor js_content_editor" data-usechk ="<?php echo $siteInfo['s_display_content_open'];?>">
                    <?php echo stripcslashes($p_info['p_content']); ?>
                </div>
                <?php if($siteInfo['s_display_content_open']=='Y'){?>
                    <div class="fold_ctrl js_pro_content" style="display:none;"><a href="#none" class="btn_ctrl js_onoff_event" data-target=".detail" data-add="if_unfold"><strong>상품정보 펼쳐보기</strong></a></div>
                <?php }?>
            </div>

        </div>
    </div><!-- end cont_box -->
</div><!-- end p_Vconts -->







<?php // 컨텐츠 : 리뷰 ?>
<div class="p_Vconts js_eval_position">
    <?php // 탭 메뉴 ?>
    <div class="tab_menu">
        <div class="layout_fix">
            <div class="tab_in">
                <ul>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_info_position',-10); return false;" class="tab">
                            <em>상세</em><strong>정보</strong>
                        </a>
                    </li>
                    <li class="hit">
                        <a href="#none" onclick="scrolltoClass('.js_eval_position',-10); return false;" class="tab">
                            <strong>리뷰</strong><span class="num js_tap_eval_cnt">(<?php echo $eval_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_qna_position',-10); return false;" class="tab">
                            <strong>문의</strong><span class="num qna_cnt">(<?php echo $qna_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_guide_position',-10); return false;" class="tab">
                            <em>구매</em><strong>안내</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- end tab_box -->

    <?php // 탭 내용 ?>
    <div class="tab_conts">
        <div class="p_Vboard type_review" id="eval_ajax">
            <?php include OD_PROGRAM_ROOT."/product.eval.form.php"; ?>
        </div>
    </div><!-- end cont_box -->
</div><!-- end p_Vconts -->







<?php // 컨텐츠 : 문의 ?>
<div class="p_Vconts js_qna_position">
    <?php // 탭 메뉴 ?>
    <div class="tab_menu">
        <div class="layout_fix">
            <div class="tab_in">
                <ul>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_info_position',-10); return false;" class="tab">
                            <em>상세</em><strong>정보</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_eval_position',-10); return false;" class="tab">
                            <strong>리뷰</strong><span class="num js_tap_eval_cnt">(<?php echo $eval_cnt; ?>)</span>
                        </a>
                    </li>
                    <li class="hit">
                        <a href="#none" onclick="scrolltoClass('.js_qna_position',-10); return false;" class="tab">
                            <strong>문의</strong><span class="num qna_cnt">(<?php echo $qna_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_guide_position',-10); return false;" class="tab">
                            <em>구매</em><strong>안내</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- end tab_box -->

    <?php // 탭 내용 ?>
    <div class="tab_conts">
        <div class="p_Vboard type_qna" id="qna_ajax">
            <?php include OD_PROGRAM_ROOT."/product.qna.form.php"; ?>
        </div>
    </div><!-- end cont_box -->
</div><!-- end p_Vconts -->








<?php // 컨텐츠 : 안내 ?>
<div class="p_Vconts js_guide_position">
    <?php // 탭 메뉴 ?>
    <div class="tab_menu">
        <div class="layout_fix">
            <div class="tab_in">
                <ul>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_info_position',-10); return false;" class="tab">
                            <em>상세</em><strong>정보</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_eval_position',-10); return false;" class="tab">
                            <strong>리뷰</strong><span class="num js_tap_eval_cnt">(<?php echo $eval_cnt; ?>)</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none" onclick="scrolltoClass('.js_qna_position',-10); return false;" class="tab">
                            <strong>문의</strong><span class="num qna_cnt">(<?php echo $qna_cnt; ?>)</span>
                        </a>
                    </li>
                    <li class="hit">
                        <a href="#none" onclick="scrolltoClass('.js_guide_position',-10); return false;" class="tab">
                            <em>구매</em><strong>안내</strong>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- end tab_box -->

    <?php // 탭 내용 ?>
    <div class="tab_conts">
        <div class="layout_fix">

            <?php if(count($notify_info) > 0) { ?>
                <?php // 상품 정보 제공고시 ?>
                <div class="notify">
                    <div class="sub_tit"><span class="tit">상품 정보 제공고시</span></div>
                    <div class="table">
                        <dl>
                            <?php
                            foreach($notify_info as $nik=>$niv) {
                                if($nik>0) echo '</dl><dl>';
                                ?>
                                <dt><?php echo stripslashes($niv['pri_key'])?></dt>
                                <dd><?php echo stripslashes(nl2br($niv['pri_value']))?></dd>
                            <?php } ?>
                        </dl>
                    </div>
                </div><!-- end notify -->
            <?php } ?>

            <?php // 판매자+배송 정보 ?>
            <div class="notify_wrap">
                <?php // 판매자 정보 ?>
                <div class="notify">
                    <div class="sub_tit"><span class="tit">판매자 정보</span></div>
                    <div class="table">
                        <dl>
                            <dt>상호명</dt>
                            <dd><?php echo $app_adshop; ?></dd>
                        </dl>
                        <dl>
                            <dt>대표전화</dt>
                            <dd><?php echo $app_glbtel; ?></dd>
                        </dl>
                        <?php if($app_ceo_name!=''){?>
                            <dl>
                                <dt>대표자</dt>
                                <dd><?php echo $app_ceo_name; ?></dd>
                            </dl>
                        <?php }?>
                        <?php if($app_fax!=''){?>
                            <dl>
                                <dt>팩스전화</dt>
                                <dd><?php echo $app_fax; ?></dd>
                            </dl>
                        <?php }?>
                        <?php if($app_company_num!=''){?>
                            <dl>
                                <dt>사업자등록번호</dt>
                                <dd><?php echo $app_company_num; ?></dd>
                            </dl>
                        <?php }?>
                        <dl>
                            <dt>대표 이메일</dt>
                            <dd><?php echo $app_ademail; ?></dd>
                        </dl>
                        <?php if($app_company_snum!=''){?>
                            <dl>
                                <dt>통신판매업신고번호</dt>
                                <dd><?php echo $app_company_snum; ?></dd>
                            </dl>
                        <?php }?>
                        <dl>
                            <dt>사업장 소재지</dt>
                            <dd><?php echo $app_company_addr; ?></dd>
                        </dl>
                    </div>
                </div><!-- end notify -->


                <?php if( $p_info['p_type'] == 'delivery') {?>
                    <?php // 배송 정보 ?>
                    <div class="notify">
                        <div class="sub_tit"><span class="tit">배송 정보</span></div>
                        <div class="table">
                            <dl>
                                <dt>지정택배사</dt>
                                <dd><?php echo $del_company; ?></dd>
                            </dl>
                            <?php if($del_date!=''){?>
                                <dl>
                                    <dt>평균배송기간</dt>
                                    <dd><?php echo $del_date; ?></dd>
                                </dl>
                            <?php }?>
                            <dl>
                                <dt>기본배송비</dt>
                                <dd><?php echo $pro_delivery; ?></dd>
                            </dl>
                            <?php if($del_return_addr!=''){?>
                                <dl>
                                    <dt>반송주소</dt>
                                    <dd><?php echo $del_return_addr; ?></dd>
                                </dl>
                            <?php }?>
                        </div>
                    </div><!-- end notify -->
                <?php } ?>

            </div><!-- end notify_wrap -->

            <?php
            if(count($arrProGuideType)>0 && $p_info['p_type'] == 'delivery'){ // 배송상품일 경우
                foreach($arrProGuideType as $_guide_key=>$_guide_title){
                    // 내용을 저장할 변수 초기화
                    $_guide_text = '';

                    // 내용 추출 - 직접입력
                    if($p_info['p_guide_type_'.$_guide_key] == 'manual'){
                        $_guide_text = $p_info['p_guide_'.$_guide_key];
                    }
                    // 내용 추출 - 선택입력
                    else if($p_info['p_guide_type_'.$_guide_key] == 'list'){
                        $_guide_text = _MQ_result(" select g_content  from smart_product_guide where g_uid = '". $p_info['p_guide_uid_'.$_guide_key] ."' and g_user in ('_MASTER_', '". $p_info['p_cpid'] ."') ");
                    }
                    // 사용안함 체크
                    else{
                        continue;
                    }

                    // 내용이 없으면 노출하지 않음
                    if(trim($_guide_text) == ''){ continue; }
                    ?>

                    <?php // 구매/배송안내 ?>
                    <div class="notify">
                        <div class="sub_tit">
                            <span class="tit"><?php echo stripslashes($_guide_title); ?></span>
                            <span class="add">※ 상품정보에 별도 기재된 경우, 아래의 내용보다 우선하여 적용됩니다.</span>
                        </div>
                        <div class="txt_box editor"><?php echo stripslashes($_guide_text); ?></div>
                    </div><!-- end guide -->

                    <?php
                }
            }
            ?>

            <?php
            if($p_info['p_type']=='ticket' && $siteInfo['kakao_js_api']!=''){
                // 주소에 다른 지도 표기 - 티켓상품일 경우
                include_once dirname(__FILE__)."/product.view.inc_map.php";
            }
            ?>

        </div>
    </div><!-- end cont_box -->
</div><!-- end p_Vconts -->




<?php // KAY :: 2024-01-10 :: 타이머 수정 ?>
<script>
	$(document).ready(function(){
		var pcode = '<?php echo $pcode;?>';
		<?php if($arr_current_time['day']<=1 && $p_info['p_time_sale']=='Y' && $p_timesale_type_chk=='Y' && $p_timesale_before_chk=='N'){?>
			time_sale_timmer_set(pcode);
		<?php }?>
    });
</script>



<?php
$list_type_class = $list_type_pcclass = $list_type_moclass=  '';
if($siteInfo['s_relation_display_type']=='box'){
    $list_type_pcclass = 'pc_type_box'.$siteInfo['s_relation_display'];
    $list_type_moclass = 'mobile_type_box'.$siteInfo['s_relation_mobile_display'];
}else{
    $list_type_pcclass = 'pc_type_list'.$siteInfo['s_relation_display'];
    $list_type_moclass = 'mobile_type_list'.$siteInfo['s_relation_mobile_display'];
    $list_type_class = 'if_list_type';
}

$relation_totalcnt = count($relation);
$relation_pc_cnt =$siteInfo['s_relation_display'];
$relation_mo_cnt =$siteInfo['s_relation_mobile_display'];
?>
<?php if($relation_totalcnt > 0){ ?>
    <?php // 컨텐츠 : 다른관련상품 ?>
    <div class="p_Vconts p_Vrelative">
        <div class="layout_fix">
            <?php // 타이틀 ?>
            <div class="tit_box">
                <div class="tit js_relation_rolling_wrap">RELATIVE</div>

                <?php // 롤링 컨트롤러(롤링일때만 노출) ?>
                <?php $relation_total_cnt = $relation_totalcnt<10?'0'.$relation_totalcnt:$relation_totalcnt;?>
                <div class="rolling_ctrl js_pro_relation_ctrl <?php echo $relation_totalcnt > $relation_cnt?'if_show':'if_hide';?>">
                    <a href="#none" class="prevnext prev js_pro_relation_prevnext" data-type="prev" title="이전"></a>
                    <?php // 롤링 페이징 ?>
                    <span class="pagi">
						<strong class="active js_pro_relation_pagenum">01</strong><em>/</em><strong><?php echo $relation_total_cnt;?></strong>
					</span>
                    <a href="#none" class="prevnext next js_pro_relation_prevnext" data-type="next" title="다음"></a>
                </div>

            </div><!-- end tit_box -->

            <div class="rolling_wrap this_item_rolling">
                <div class="rolling_box">
                    <div class="item_list js_pro_relation <?php echo $list_type_pcclass;?> <?php echo $list_type_moclass;?> <?php echo $list_type_class;?>">
                        <ul class="swiper-wrapper">
                            <?php foreach($relation as $k=>$v){ ?>
                                <li class="swiper-slide">
                                    <?php
                                    $relation_view = 'Y';
                                    $locationFile = basename(__FILE__); // 파일설정
                                    include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div><!-- end rolling_wrap -->
        </div>

    </div><!-- end p_Vrelative -->

    <script type="text/javascript">
        // 최초 슬라이드 넓이 조정 후 슬라이드 생성
        var swiper_pro_relation;
        var swiper_pro_relation_index = 0; // swiper 떨림현상

        $(document).ready(function(){
            pro_relation_rolling();
        });

        $(window).resize(function(e){
            pro_relation_rolling();
        });


        function pro_relation_rolling(){

            // 반응형
            var relation_totalCnt = '<?php echo $relation_totalcnt;?>';			// 상품개수
            var relation_pc_view =  '<?php echo $relation_pc_cnt;?>';
            var relation_mo_view =  '<?php echo $relation_mo_cnt;?>';
            var relation_chkwidth = window.innerWidth;

            if( relation_chkwidth <= 700){ relation_view = relation_mo_view;}
            else if( relation_chkwidth > 700 && relation_chkwidth <= 900){ relation_view = relation_pc_view>=3?3:relation_pc_view; }
            else if( relation_chkwidth > 900 && relation_chkwidth <= 1100){ relation_view = relation_pc_view>=4?4:relation_pc_view; }
            else if( relation_chkwidth > 1100 && relation_chkwidth <= 1300){ relation_view = relation_pc_view>5?5:relation_pc_view;}
            else if( relation_chkwidth > 1300){ relation_view = relation_pc_view;}



            var slideWidth = $('.js_pro_relation_slide li').outerWidth();

            // swiper 떨림현상
            if( typeof swiper_pro_relation == 'object'){
                swiper_pro_relation.destroy();
                swiper_pro_relation = false;
            }

            // 롤링 컨트롤러는 클래스로 제어
            if(parseInt(relation_view)>=parseInt(relation_totalCnt)){
                $('.js_pro_relation_ctrl').removeClass('if_show').addClass('if_hide');
                return false;
            }else{
                $('.js_pro_relation_ctrl').removeClass('if_hide').addClass('if_show');
            }

            swiper_pro_relation = new Swiper('.js_pro_relation', {
                effect: 'slide',
                slidesPerView: relation_view,
                paginationClickable : false,
                autoplay : {
                    pause:4000,
                    disableOnInteraction : false,
                },
                speed: 500,
                parallax:false,
                loop : true,
                loopedSlides : relation_totalCnt ,
                spaceBetween: 0,
                initialSlide:swiper_pro_relation_index, // swiper 떨림현상
                roundLengths : true, // 이미지 흐리게 보이는거 처리 옵션
            });

            // 슬라이드 변경시 실행되는 이벤트
            swiper_pro_relation.on('slideChangeTransitionStart', function() {

                var pagenum = swiper_pro_relation.realIndex+1;
                pagenum = pagenum<10?'0'+pagenum:pagenum;
                $('.js_pro_relation_pagenum').text(pagenum);

                swiper_pro_relation_index = swiper_pro_relation.realIndex;
            });

            // 이전다음 클릭이벤트
            $('.js_pro_relation_prevnext').on('click', function() {
                var relation_data = $(this).data();

                if(relation_data.type=='next'){
                    swiper_pro_relation.slideNext();
                }else{
                    swiper_pro_relation.slidePrev();
                }
            });
        }

    </script>
<?php } ?>



<?php if( $p_info['p_type']=='ticket' && $siteInfo['kakao_js_api']!=''){ ?>
	<script type = "text/javascript" src = "//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $siteInfo['kakao_js_api']; ?>&libraries=services" ></script>
<?php }?>
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">
    var old_idx = "0";
    var now_idx = "1";
    var max_idx = $(".photo_thumb img").length;
    var auto_change = true;
    function view_thumb_img(idx,mode) {
        if(!auto_change && mode=="auto") return;

        img_src = $("#thumb_"+idx).attr("src");
        img_src = img_src.replace("thumbs_s_","");
        $("#main_img").attr("src",img_src);

        // 셈네일 이미지 class on/off
        $("#thumb_"+idx).removeClass("off");
        $("#thumb_"+idx).addClass("on");
        $("#thumb_"+old_idx).removeClass("on");
        $("#thumb_"+old_idx).addClass("off");

        old_idx = idx;
        now_idx = idx*1+1 > max_idx ? 1 :idx*1+1;
    }
    $(".photo_thumb .fix").hover(
        function() {
            auto_change = false;
        },
        function() {
            auto_change = true;
        }
    );

    function view_thumb_img_auto() {
        view_thumb_img(now_idx,"auto");
        setTimeout(view_thumb_img_auto,2000);
    }

    function sale_info(mode) {
        if(mode == "show") $(".ly_notice").show();
        else $(".ly_notice").hide();
    }

    function pro_cnt_up() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가


        // 최대구매개수제한 {
        var max_cnt = $('#frmProductView [name="product_buy_limit"]').val();
        max_cnt = parseInt(max_cnt);
        if( max_cnt && max_cnt>0){
            if(cnt>=max_cnt){
                alert('본상품은 최대 '+max_cnt + "개 까지 구매가능합니다.");
                return false;
            }
        }
        // 최대구매개수제한 }


        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt+1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    $("#option_select_cnt").val(cnt+1);
                    $('.js_option_cnt_down').removeClass('if_no');
                }
                update_sum_price();
            }
        });
    }
    function pro_cnt_down() {
        cnt = $("#option_select_cnt").val()*1;
        // 2019-07-24 SSJ :: 옵션이 없는 상품의 재고체크 추가
        $.ajax({
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            data: {'_mode':'get_pstock' , 'pcode':'<?php echo $pcode; ?>'},
            type: 'post',
            dataType: 'text',
            success: function(data){
                if(data == 0){
                    alert('해당 상품은 품절된 상품입니다.');
                    location.reload();
                }else if(cnt-1 > data){
                    alert('해당 상품의 재고량이 부족합니다.');
                    $("#option_select_cnt").val(data);
                }else{
                    if(cnt > 1) $("#option_select_cnt").val(cnt-1);
                    if((cnt-1)<='1'){ $(".js_option_cnt_down").addClass('if_no');	}
                }
                update_sum_price();
            }
        });
    }
    function update_sum_price() {
        var sumprice = 0;
        sumprice = String($("#option_select_expricesum").val()*$("#option_select_cnt").val());
        if(sumprice == "NaN") sumprice = "0";
        $("#option_select_expricesum_display").html(sumprice.comma());
    }


    $(document).ready(function() {
        // 섬네일 이미지 자동 변경
        //view_thumb_img_auto();
        update_sum_price();

        var chkUse = $('.js_content_editor').data('usechk');	// content 높이값
        if(chkUse=='Y'){
            func_content_open();		// 상품상세 펼쳐보기
        }
    });


    $(window).resize(function(e){
        var open_chk = $('.js_content_wrap').hasClass('if_unfold'); // content가 열려있는지 체크
        var chkUse = $('.js_content_editor').data('usechk');	// content 높이값

        if(open_chk==true){
            $('.js_pro_content').hide();
        }else{
            if(chkUse=='Y'){
                func_content_open();		// 상품상세 펼쳐보기
            }
        }
    });


    // 상품상세 펼쳐보기
    function func_content_open(){
        var chkHeight = $('.js_content_editor').height();	// content 높이값

        if(chkHeight>='1000' ){
            $('.js_content_wrap').addClass('this_fold');
            $('.js_pro_content').show();
        }else{
            $('.js_pro_content').hide();
        }

    }


    // 선택한 상품 옵션 모두 보기
    $(document).on('click', '.js_btn_optdropbox', function(){


        var depth = $(this).closest('.js_box_optdropbox').data('idx');
        var chk = $('.js_box_optdropbox[data-idx='+depth+']').hasClass('if_open_opt');

        if(chk==true){
            $('.js_box_optdropbox[data-idx='+depth+']').removeClass('if_open_opt');
        }else{

            <?php  if( $p_info['p_dateoption_use'] == 'Y'){ // 달력옵션을 체크한다.  ?>
            if( calendar.selDate == ''){
                alert("달력에서 날짜를 먼저 선택해 주세요.");
                return false;
            }
            <?php } ?>

            // LCY : 2023-08-31 : 옵션열기 수정
            $('.js_box_optdropbox_add').removeClass('if_open_opt');
            $('.js_box_optdropbox').removeClass('if_open_opt');

            $('.js_box_optdropbox[data-idx='+depth+']').addClass('if_open_opt');
        }
    });


    // 추가 옵션 클릭시 오픈
    $(document).on('click', '.js_btn_optdropbox_add', function(){

        var depth = $(this).closest('.js_box_optdropbox_add').data('idx');
        var chk = $('.js_box_optdropbox_add[data-idx='+depth+']').hasClass('if_open_opt');

        $('.js_box_optdropbox_add').removeClass('if_open_opt');
        if(chk==true){
            $('.js_box_optdropbox_add[data-idx='+depth+']').removeClass('if_open_opt');
        }else{

            // LCY : 2023-08-31 : 옵션열기 수정
            $('.js_box_optdropbox_add').removeClass('if_open_opt');
            $('.js_box_optdropbox').removeClass('if_open_opt');

            $('.js_box_optdropbox_add[data-idx='+depth+']').addClass('if_open_opt');
        }
    });

    function cate_change(obj) {
        location.href="/?pn=product.list&cuid="+obj.value;
    }


    function option_select_tmp(idx,pro_depth,pouid,poname,pcode) {

        <?php // LCY : 2022-12-21 : 티켓기능 -- option_seject.js ?>
        dateoption_check();

        $('#option_select'+idx+'_id').val(pouid);
        $('#option_select'+idx+'_poname').html(poname);
        $('#option_select_'+idx+'_box').css({'display':'none'});
        setTimeout(function(){ $('#option_select_'+idx+'_box').attr({'style':''}); }, 100);

        if(idx+'depth' == pro_depth){
            option_select_add(pcode);
        }else{
            option_select(idx,pcode);
        }

        $('.opt_dropbox[data-idx='+idx+']').removeClass('if_open_opt');

    }


    function option_select_tmp2(idx,pro_depth,pouid,pcode) {
        $('#option_select'+idx+'_id').val(pouid);
        if(idx+'depth' == pro_depth){
            option_select_add(pcode);
        }else{
            option_select(idx,pcode);
        }
    }


    // 해시이동(주소해시에 상응하는 클래스 객체가 있다면 스크롤 자동 이동)
    $(function() {
        var UrlHash = window.location.hash;
        if(UrlHash) {
            UrlHash = UrlHash.replace('#', '');
            if($('.'+UrlHash).length > 0) {
                scrolltoClass('.'+UrlHash, -180);
            }
        }
    });

    <?php
    // KAY : 2022-12-14 : 상품목록에서 리뷰 클릭시 해당 리뷰로 이동
    if($review_mv == 'Y'){
    ?>
    $(document).ready(function(){
        scrolltoClass('.js_eval_position',-10);
    });
    <?php } ?>


	// SNS공유하기 버튼
	function sendSNS(type) {
		var url = 'http://<?=$system['host']?>/?pn=product.view&pcode=<?=$pcode?>';
		var title = '<?=htmlentities(addslashes($pro_name)); ?>';
		var image = '<?=$main_img?>';
		var desc = '<?=cutstr(trim(str_replace("  "," ",str_replace(":","-",str_replace("\t"," ",str_replace("\r"," ",str_replace("\n"," ",str_replace("'","`",stripslashes(($p_info['p_subname']?$p_info['p_subname']:$siteInfo['s_glbtlt']))))))))) , 24 , "..")?>';
		if(type == 'kakao') {
			try {
				Kakao.cleanup();
				Kakao.init('<?php echo $siteInfo['kakao_js_api']; ?>');
				Kakao.Link.sendDefault({
					objectType: 'feed',
					content: {
						title: title,
						description: desc,
						imageUrl: image,
						imageWidth: 470, // 없으면 이미지가 찌그러짐
						imageHeight: 470, // 없으면 이미지가 찌그러짐
						link: {
							mobileWebUrl: url,
							webUrl: url
						}
					},
					buttons: [
						{
							title: og_site_name,
							link: {
								mobileWebUrl: url,
								webUrl: url
							}
						}
					],
					installTalk: true,
					fail: function(err) {
						alert(JSON.stringify(err));
					}
				});
			} catch(e) {
				alert('카카오톡으로 공유 할 수 없는 상태 입니다.');
			};
		}
		else if(type=='kakao-story') {
			try {

				Kakao.Story.open({
					url: url,
					text: title
				});

			} catch(e) {
				alert('카카오스토리로 공유 할 수 없는 상태 입니다.');
			};
		}
		else if(type=='facebook') {
			postToFeed(title, desc, url, image);
		}
		else if(type=='twitter') {
			var openUrl = "http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url);
			<?php if($AppUseMode === true && function_exists('is_app') === true && is_app() === true) {?>
				location.href="<?php echo $app_info['scheme']; ?>://action?mode=outer_window&url=" + openUrl;
			<?php }else{ ?>
				var wp = window.open(openUrl, 'twitter', 'width=550,height=256');
				if(wp) { wp.focus(); }
			<?php }?>
		}
		else if(type=='pinterest') {
			var href = "http://www.pinterest.com/pin/create/button/?url="+encodeURIComponent(url)+"&media="+encodeURIComponent(image)+"&description="+encodeURIComponent(title);
			<?php if($AppUseMode === true && function_exists('is_app') === true && is_app() === true) {?>
			location.href="<?php echo $app_info['scheme']; ?>://action?mode=outer_window&url=" + href;
			<?php }else{ ?>
			var a = window.open(href, 'pinterest', 'width=734, height=734');
			if ( a ) {
				a.focus();
			}
			<?php }?>
		}
		$.ajax({
			data: {'pcode':'<?=$pcode?>','type':type},
			type: 'GET', cache: false, url: '<?php echo OD_PROGRAM_URL; ?>/ajax.sns.update.php',
			success: function(data) { return true; },
			error:function(request,status,error){ alert('현재 공유가 불가능합니다.'); /*alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);*/ }
		});
		return false;
	}



    // 사용처 클릭 시
    $(document).on('click','.js_product_view_map_view',function(){
        scrolltoClass('.js_product_view_map_position',+70);

        // $('body').animate({scrollTop:1000},500);

    });

</script>
<script src="<?php echo $SkinData['skin_url']; ?>/js/option_select.js?v=<?php echo time(); ?>" type="text/javascript"></script>
<script src="<?php echo $SkinData['skin_url']; ?>/js/time_sale_timmer.js?v=<?php echo time(); ?>" type="text/javascript"></script>