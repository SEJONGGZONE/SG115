<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 기본파일 include
include_once(OD_PROGRAM_ROOT.'/inc.header.php'); // 스킨 내부파일로 직접 include 하면 안됨.
include_once($SkinData['skin_root'].'/inc.slide_menu.php'); // 슬라이드 메뉴

?>


<?php // 헤더 ?>
<div class="sc_Header">
    <div class="wrapping layout_fix">
        <?php // gnb 메뉴 ?>
        <div class="gnb_menu">
            <a href="#none" onclick="return false;" class="btn_slide js_onoff_event" data-target=".p_Slide" data-add="if_open_slide" title="슬라이드 열기"></a>

            <?php // 네비 ?>
            <ul class="nav_box">
                <?php
                $chkHit = false;
                if( preg_match("/(product\.list)/",$pn) > 0 && $cuid!=''){ $chkHit = true; }
                ?>
                <li class="nav_li <?php echo $chkHit==true?'hit':'';?>">
                    <a href="/?pn=product.list&cuid=<?php echo $AllCate[0]['c_uid']; ?>" class="first_menu">SHOP</a>
                    <div class="depth_box">
                        <ul>
                            <?php foreach($AllCate as $k=>$v) { ?>
                                <li><a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="second_menu"><span class="tx"><?php echo $v['c_name']; ?></span></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>

                <?php
                $TopEventList1 = _MQ(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_depth='1' order by dts_idx asc ");
                $TopEventList2 = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_view = 'Y' and dts_depth='2' and dts_parent='".$TopEventList1['dts_uid']."' order by dts_idx asc ");
                $chkHit = false;
                if( preg_match("/(product\.list)/",$pn) > 0 && $_event=='type'){ $chkHit = true; }
                if(count($TopEventList2) > 0) {
                    ?>
                    <li class="nav_li <?php echo $chkHit==true?'hit':''?>">
                        <a href="/?pn=product.list&_event=type&typeuid=<?php echo $TopEventList2[0]['dts_uid']; ?>" class="first_menu"><?php echo $TopEventList1['dts_name'];?></a>
                        <div class="depth_box">
                            <ul>
                                <?php foreach($TopEventList2 as $k=>$v) { ?>
                                    <li><a href="/?pn=product.list&_event=type&typeuid=<?php echo $v['dts_uid']; ?>" class="second_menu"><span class="tx"><?php echo $v['dts_name']; ?></span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </li>
                <?php } ?>

                <?php
                $chkHit = false;
                if($pn=='product.brand_list'){ $chkHit = true; }
                if($siteInfo['s_brand_view']=='Y'){ // 관리자 브랜드 관리에서 노출 설정
                    ?>
                    <li class="nav_li <?php echo $chkHit==true?'hit':'';?>">
                        <a href="/?pn=product.brand_list" class="first_menu">BRAND</a>
                    </li>
                <?php }?>

                <?php
                $chkHit = false;
                if( (preg_match("/(board\.)/",$pn) > 0 && $_menu=='event' )
                    || preg_match("/(promotion\.attend)/",$pn) > 0
                    || preg_match("/(service\.qna)/",$pn) > 0
                    || preg_match("/(service\.eval)/",$pn) > 0
                    || preg_match("/(product\.promotion_)/",$pn) > 0
                ){ $chkHit = true; }

                $arr_event_menu = array_keys($SlideEventArr);
                $FirstEventMenu = $arr_event_menu[0];
                ?>
                <li class="nav_li <?php echo $chkHit==true?'hit':'';?>">
                    <a href="<?php echo $SlideEventArr[$FirstEventMenu]['link'];?>" class="first_menu">EVENT</a>
                    <div class="depth_box">
                        <ul>
                            <?php foreach($SlideEventArr as $sek=>$sev){?>
                                <li><a href="<?php echo $sev['link'];?>" class="second_menu"><span class="tx"><?php echo $sev['title'];?></span></a></li>
                            <?php }?>
                        </ul>
                    </div>
                </li>
            </ul><!-- end nav_box -->
        </div><!-- end gnb_menu -->

        <?php // 로고 ?>
        <?php
        $TopLogo = info_banner($_skin.',mobile_top_logo', 1, 'data');
        if(count($TopLogo) > 0) {
            ?>
            <div class="logo_box">
                <?php if($TopLogo[0]['b_target'] != '_none' && isset($TopLogo[0]['b_link'])) { ?><a href="<?php echo $TopLogo[0]['b_link']; ?>" target="<?php echo $TopLogo[0]['b_target']; ?>"><?php }?>
                    <?php // 로고 이미지 : 가로 300 이하 * 세로 200 이하 권장 / 자동조정 ?>
                    <img src="<?php echo IMG_DIR_BANNER_URL.$TopLogo[0]['b_img']; ?>" alt="<?php echo addslashes($TopLogo[0]['b_title']); ?>" />
                    <?php if($TopLogo[0]['b_target'] != '_none' && isset($TopLogo[0]['b_link'])) { ?></a><?php }?>
            </div>
        <?php } else { ?>
            <div class="logo_box"><a href="/"><span class="tx"><?php echo $siteInfo['s_adshop']; ?></span></a></div>
        <?php } ?>

        <?php // 사용자+아이콘 메뉴 ?>
        <div class="other_menu">
            <ul class="user_link">
                <?php if(is_login()) { ?>
                    <li><a href="/?pn=mypage.main" class="menu">MY PAGE</a></li>
                    <li><a href="/?pn=mypage.order.list" class="menu">ORDER</a></li>
                    <li><a href="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php?_mode=logout" class="menu">LOGOUT</a></li>
                <?php } else { ?>
                    <li><a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="menu">LOGIN</a></li>
                    <li><a href="/?pn=member.join.agree" class="menu">JOIN</a></li>
					<?php // 비회원 주문을 가능으로 했을경우 비회원주문조회로 이동?>
					<?php if($siteInfo['s_none_member_buy'] == "Y" ){?>
						<li><a href="/?pn=service.guest.order.list" class="menu">ORDER</a></li>
					<?php }else{?>
						<li><a href="/?pn=mypage.order.list" class="menu">ORDER</a></li>
					<?php }?>
                <?php } ?>
            </ul><!-- end user_link -->

            <ul class="icon_link">
                <li>
                    <a href="#none" onclick="return false;" class="btn btn_search js_onoff_event js_search_open" data-target=".search_open" data-add="if_search_open" title="검색"></a>
                </li>
                <li>
                    <a href="/?pn=shop.cart.list" class="btn btn_cart" title="장바구니">
                        <span class="cart_num js_cart_cnt" style="<?php echo ($cart_cnt < 1 ? 'display:none;':null); ?>"><?php echo $cart_cnt; ?></span>
                    </a>
                </li>
                <li class="this_slide">
                    <a href="#none" onclick="return false;" class="btn btn_slide js_onoff_event" data-target=".p_Slide" data-add="if_open_slide" title="슬라이드 열기"></a>
                </li>
            </ul><!-- end icon_link -->
        </div><!-- end other_menu -->
    </div><!-- end wrapping -->

    <?php // 검색 열림 ?>
    <div class="search_open">
        <div class="inner layout_fix">
            <div class="tit">Search</div>

            <form name="main_search" role="search" class="form_box" action="/" method="get" onsubmit="return searchFunction(this);">
                <input type="hidden" name="pn" value="product.search.list">
                <input type="search" name="search_word"  autocomplete="off" class="input_search search_word" placeholder="<?php echo $siteInfo['s_saerch_text']!=''?$siteInfo['s_saerch_text']:'Search';?>">
                <a href="#none" class="btn_search" onclick="searchFunction(); return false;" title="검색"></a>
            </form><!-- end form_box -->

            <script type="text/javascript">
                function searchFunction() {
                    var $parents = $('form[name="main_search"]');
                    if($parents.find('.search_word').val() == '' || $parents.find('.search_word').val() == '상품을 검색하세요.') {
                        alert('검색할 단어를 입력하세요');
                        $parents.find('.search_word').focus();
                        return false;
                    }else{
                        document.main_search.submit();
                    }
                }
            </script>
            <?php
            $GetKeyword = explode(',', $siteInfo['s_recommend_keyword']); // 추천 검색어
            $GetHashTag = explode(',', $siteInfo['s_recommend_hashtag']); // 인기 해시태그
            if(count($GetHashTag) == 1 && $GetHashTag[0] == '')  $GetHashTag = array();
            if(count($GetKeyword) == 1 && $GetKeyword[0] == '')  $GetKeyword = array();
            if(count($GetHashTag) > 0 || count($GetKeyword) > 0 ) {
                $GetHashTag = array_unique($GetHashTag); // 중복값제거
                //if(count($GetHashTag) > 1) shuffle($GetHashTag); // 셔플(랜덤)

                $rand_hashword = array();
                foreach($GetHashTag as $k=>$v){
                    $rand_hashword[] = array('data'=>'#'.$v,'search'=>'%23'.urlencode($v));
                }

                foreach($GetKeyword as $k=>$v){
                    $rand_hashword[] = array('data'=>$v,'search'=>urlencode($v));
                }

                // 추천검색어, 인기해시태그 합쳐서 랜덤으로 노출
                if(count($rand_hashword) > 0) shuffle($rand_hashword); // 셔플(랜덤)
                ?>
                <div class="keyword_box">
                    <?php foreach($rand_hashword as $rk=>$rv) { ?>
                        <a href="/?pn=product.search.list&search_word=<?php echo $rv['search']; ?>" class="link"><span class="tx"><?php echo $rv['data']; ?></span></a>
                    <?php } ?>
                </div>
            <?php } ?>

            <a href="#none" onclick="return false;" class="close_btn js_onoff_event" data-target=".search_open" data-add="if_search_open" title="검색 닫기"></a>
        </div><!-- end inner -->
    </div><!-- end search_open -->

    <?php // 1차카테고리(모바일용) ?>
    <div class="category">
        <div class="layout_fix">
            <div class="swipe_box" ID="js_swipe_header_ctg">
                <ul>
                    <?php foreach($AllCate as $k=>$v) { ?>
                        <li class="<?php echo (($v['c_uid']==$cuid) || $ActiveCate['cuid'][0]==$v['c_uid']?'hit':null); ?>"><a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg"><strong><?php echo $v['c_name']; ?></strong></a></li>
                    <?php } ?>

                    <?php
                    $TopEventList1 = _MQ(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_depth='1' order by dts_idx asc ");
                    $TopEventList2 = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_view = 'Y' and dts_depth='2' and dts_parent='".$TopEventList1['dts_uid']."' order by dts_idx asc ");
                    $chkHit = false;
                    if( preg_match("/(product\.list)/",$pn) > 0 && $_event=='type'){ $chkHit = true; }
                    if(count($TopEventList2) > 0) {
                        ?>
                        <li class="type_event <?php echo $chkHit==true?'hit':''?>"><a href="/?pn=product.list&_event=type&typeuid=<?php echo $TopEventList2[0]['dts_uid']; ?>" class="ctg"><strong><?php echo $TopEventList1['dts_name'];?></strong></a></li>
                    <?php } ?>

                    <?php
                    $chkHit = false;
                    if($pn=='product.brand_list'){ $chkHit = true; }
                    if($siteInfo['s_brand_view']=='Y'){ // 관리자 브랜드 관리에서 노출 설정
                        ?>
                        <li class="type_event <?php echo $chkHit==true?'hit':'';?>"><a href="/?pn=product.brand_list" class="ctg"><strong>BRAND</strong></a></li>
                    <?php }?>

                    <?php
                    $chkHit = false;
                    if( (preg_match("/(board\.)/",$pn) > 0 && $_menu=='event' )
                        || preg_match("/(promotion\.attend)/",$pn) > 0
                        || preg_match("/(service\.qna)/",$pn) > 0
                        || preg_match("/(service\.eval)/",$pn) > 0
                        || preg_match("/(product.promotion\_)/",$pn) > 0
                    ){ $chkHit = true; }

                    $arr_event_menu = array_keys($SlideEventArr);
                    $FirstEventMenu = $arr_event_menu[0];
                    ?>
                    <li class="type_event <?php echo $chkHit==true?'hit':'';?>"><a href="<?php echo $SlideEventArr[$FirstEventMenu]['link'];?>" class="ctg"><strong>EVENT</strong></a></li>

                </ul>
            </div>
        </div>
        <script>
            // 아이스크롤 스크립트(스와이프)
            // js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
            var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_header_ctg'), testScroll = '';

            // 스와이프 적용
            $(document).ready(function(){
                swipe_Menu1/*숫자만변경*/();
            });

            function swipe_Menu1/*숫자만변경*/(){
                $.each($('#js_swipe_header_ctg li'), function(k, v){  scrollWidth += $('#js_swipe_header_ctg li').eq(k).outerWidth()*1; });
                var len = $('#js_swipe_header_ctg li').length;
                swipe_Scroll1/*숫자만변경*/ = new IScroll('#js_swipe_header_ctg', {
                    'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
                    , mouseWheel: true
                });

                if(scrollIndex > 0 && $('#js_swipe_header_ctg li.hit').length > 0) {
                    var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_header_ctg li.hit').outerWidth()*1/2 ) * -1;
                    swipe_Scroll1/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_header_ctg li.hit'), 500, scrollOffset);
                }
            }
        </script>
    </div><!-- end sc_Category -->
</div><!-- end sc_Header -->




