<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>

<?php // 푸터 ?>
<div class="sc_Footer">
	<div class="layout_fix">

		<div class="menu_box">
			<ul>
				<li><a href="/?pn=pages.view&type=pages&data=company" class="menu">회사소개</a></li>
				<li><a href="/?pn=pages.view&type=agree&data=guide" class="menu">이용안내</a></li>
				<li><a href="/?pn=pages.view&type=agree&data=privacy" class="menu">개인정보처리방침</a></li>
				<li><a href="/?pn=pages.view&type=agree&data=agree" class="menu">이용약관</a></li>
				<?php if($siteInfo['s_request_partner_view']=='Y'){?>
					<li><a href="/?pn=service.partner.form" class="menu">제휴문의</a></li>
				<?php }?>
			</ul>
		</div><!-- end menu_box -->

		<div class="company_box">
			<div class="site_name"><?php echo $siteInfo['s_adshop']; ?></div>
			<div class="txt">상호명 : <?php echo $siteInfo['s_company_name']; ?></div>
			<div class="txt">대표자 : <?php echo $siteInfo['s_ceo_name']; ?></div>
			<div class="txt">전화 : <a href="tel:<?php echo $siteInfo['s_glbtel']; ?>" class="tel"><?php echo $siteInfo['s_glbtel']; ?></a></div>
			<?php if($siteInfo['s_fax']) { ?>
				<div class="txt">팩스 : <?php echo $siteInfo['s_fax']; ?></div>
			<?php } ?>
			<div class="txt">이메일 : <?php echo $siteInfo['s_ademail']; ?></div>
			<div class="txt">통신판매업 신고번호 : <?php echo $siteInfo['s_company_snum']; ?></div>
			<?php if($siteInfo['s_view_network_company_info'] == 'Y') { ?>
				<div class="txt">
					사업자 등록번호 : <a href="#none" onclick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=str_replace("-","",$siteInfo['s_company_num'])?>', 'communicationViewPopup', 'width=750, height=700;'); return false;" class="btn_info"><?php echo $siteInfo['s_company_num']; ?></a>
				</div>
			<?php } ?>
			<?php if($siteInfo['s_view_network_company_info'] == 'Y') { ?>
				<div class="txt">개인정보처리 책임자 : <?php echo $siteInfo['s_privacy_name']; ?></div>
			<?php } ?>

			<div class="txt">주소 : <?php echo $siteInfo['s_company_addr']; ?></div>
			<?php if($siteInfo['s_hosting_by'] != '') { ?>
				<div class="txt type_host">Hosting by <?php echo $siteInfo['s_hosting_by'] ;?></div>
			<?php }?>
			<div class="copyright">© <?php echo $siteInfo['s_adshop']; ?>. All Rights Reserved.</div>
		</div><!-- end company_box -->

		<div class="auth_box">

            <?php
            /*
                /program/wrap.footer.php 에서 정의
                $escrow_icon : 에스크로 이미지
                $escrow_link : 에스크로 확인 URL
            */
            if($escrow_link) {
            ?>
                <div class="pg">
                    <div class="guide">본 쇼핑몰에서는 현금 등으로 결제 시<br/>구매 안전 서비스를 이용하실 수 있습니다</div>
                    <div class="logo" onclick="<?php echo $escrow_link; ?>"><img src="<?php echo $escrow_icon; ?>" alt="<?php echo $arr_pg_type[$siteInfo['s_pg_type']];?>" /></div>
                </div>
            <?php }?>


            <?php
            /*
                /program/wrap.footer.php 에서 정의
                $ssl_etc : 기타 인증서
                $escrow_icon : ssl 이미지
                $escrow_link : ssl 확인 URL
            */
            if($siteInfo['s_ssl_check']=='Y') {
                ?>
                <?php if($ssl_etc) { ?>
                    <?php echo $ssl_etc; // 기타 ?>
                <?php } else { ?>

					<?php if($siteInfo['s_ssl_pc_img'] == 'C' ){ // LCY : 2023-11-23 : SSL 변경작업 (sectigo positive ssl) ?>
					<div class="ssl">
						<div class="js_sectigo">
						<?php // LCY : 2023-11-20 : SSL보안서버추가 -- 섹티고추가 -- ?>
						<script type="text/javascript"> //<![CDATA[
						var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
						document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
						//]]>
						</script>
						<script language="JavaScript" type="text/javascript">
						try{
							TrustLogo("<?php echo $ssl_icon; ?>", "CL1", "none");
							$(function(){ $('#tl_popupCL1').remove(); $('.js_sectigo a').removeAttr('onmouseover'); });
							$(document).on('click','.js_sectigo',function(e){
								<?php echo $ssl_link; ?>
							});
						}catch(e){  }
						</script>
						</div>
					</div>
					<?php }else{ ?>
                    <div class="ssl">
                        <div class="logo"><img src="<?php echo $ssl_icon;?>" align="absmiddle" border="0" onclick="<?php echo $ssl_link;?>"></div>
                    </div>
					<?php } ?>
                <?php }?>
            <?php }?>

		</div><!-- end auth_box -->

	</div><!-- end layout_fix -->
</div><!-- end sc_Footer -->



<?php // 장바구니 담기창 ?>
<div class="p_Popcart js_toast_box">
	<div class="white_box cart">
		<div class="tip">상품을 장바구니에 담았습니다!</div>
		<ul>
			<li><a href="#none" onclick="return false;" class="btn no_cart">계속 쇼핑하기</a></li>
			<li><a href="/?pn=shop.cart.list" class="btn go_cart">바로 주문하기</a></li>
		</ul>
	</div>
</div><!-- end p_Popcart -->


<?php
	// KAY :: 2023-04-10 :: 리뷰 신고하기 레이어 팝업
	include_once dirname(__FILE__)."/inc.footer.report.php";
?>



<script>

    // 장바구니 메세지 노출 추가
	// --토스트 메세지 추가
	function toast_msg(){
		$('.js_toast_box').hide();
		$('.js_toast_box').show();
		$('.p_Popcart').addClass('if_cart_save');
		setTimeout(function(){ $('.p_Popcart').removeClass('if_cart_save'); }, 2500);
	}

	// 장바구니 다시불러오기
	function reload_cart(type, pcode){

        // 아이콘 hit처리
        if(type == 'add'){
            toast_msg(); // 토스트 메세지 추가
            $('.js_cart[data-pcode='+pcode+']').addClass('hit');
        }
	}

	$(document).ready(function(){
		$('.js_toast_box .no_cart').click(function(){
			$('.p_Popcart').removeClass('if_cart_save');
		});
	});

	 // 장바구니 메세지 노출 추가

	// 최근본 상품 삭제 및 카운트
	function late_delete(uid) {
		common_frame.location.href = '<?php echo OD_PROGRAM_URL; ?>/common.pro.php?_mode=latest_del&uid='+uid;
	}

	function latest_view() {
		$.ajax({
			url: '<?php echo OD_PROGRAM_URL; ?>/inc.latest.php',
			cache: false,
			type: 'POST',
			success: function(data){
				$('.js_latest_box_wrap').html(data);

				var total_cnt = $('.js_latest_box_wrap .js_latest_cnt').data('cnt');
				var _img = $('input[name="item_pro_img"][data-index="0"]').val();

				view_img = _img=='' || _img==undefined?'':_img;
				if(total_cnt>0){
					total_text = '최근본 상품('+total_cnt+')';
					$('.js_latest_cnt_view').text(total_text);
					$('.js_latest_img_view').css('background-image', 'url('+view_img+')');
				}else{
					$('.js_recent').remove();
					$('.js_recent_chk').removeClass('if_recent');
				}
			}
		});
	}

    // 맨위로 가는 버튼
    $( document ).ready( function() {
        $( '.go_top' ).click( function() {
            $( 'html, body' ).animate( { scrollTop : 0 }, 400 );
            return false;
        } );
    } );

    // 스크롤 내리면 나타나는 스크립트
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 30) {
            $(".js_scroll_stage").addClass("if_scroll");
        } else {
            $(".js_scroll_stage").removeClass("if_scroll");
        }
    });

	// 연관되지 않는 요소 열고닫기
    $(document).on('click','.js_onoff_event',function(e){
        var data = $(this).data();
        var targetClass = data.target;
        var addClass = data.add;
        var chk = $(targetClass).hasClass(addClass);
        if( chk == true){ // 이미 있다면
            $(targetClass).removeClass(addClass);
        }else{
            $(targetClass).addClass(addClass);

            // 검색 클릭했을경우 인풋에 포커스
            if(addClass=='if_search_open'){
                $('form[name="main_search"]').find('.search_word').focus();
            }
        }
    });

	// 슬라이드 - 사이트맵 탭
    $(function(){
        /* 설정 */
        var tabClass = '.js_tab1'; // 탭 클래스
        var tabHitClass = 'hit'; // 탭 클릭시 추가될 클래스
        var tabContentClass = '.js_tab1_content'; // 탭 컨텐츠

        $(document).ready(function(){
            $(tabClass).each(function(i,v){
                $(v).attr('data-index',i)
                $(tabContentClass).eq(i).attr('index',i);
            })
        });
        $(document).on('click',tabClass,function(){
            var index = $(this).data('index');
            $(tabClass).removeClass(tabHitClass);
            $(this).addClass(tabHitClass);
            $(tabContentClass).hide();
            $(tabContentClass).eq(index).show();
            return true;
        })
    })

    // 약관 열고 닫기(li) :: 회원가입 약관동의, 주문결제 비회원 약관동의, 게시판 등록폼 비회원 약관동의
    $(document).on('click',".js_btn_open",function(){
        var targetClass = "js_box_open";
        var eventClass = "if_open_box";
        var chk = $(this).closest('.'+targetClass).hasClass(eventClass);
        $("."+targetClass).removeClass(eventClass);
        if( chk == false) $(this).closest('.'+targetClass).addClass(eventClass);
    });

    // 슬라이드 - 카테고리 열고닫기(li)
    $(document).on('click',".js_btn_sd_cate",function(){
        var targetClass = "js_box_sd_cate";
        var eventClass = "if_open_sd_cate";
        var chk = $(this).closest('.'+targetClass).hasClass(eventClass);
        $("."+targetClass).removeClass(eventClass);
        if( chk == false) $(this).closest('.'+targetClass).addClass(eventClass);
    });

    // 상품상세 리뷰/문의 목록 열고 닫기(li)
    $(document).on('click',".js_btn_Vboard",function(){
        var targetClass = "js_box_Vboard";
        var eventClass = "if_open_Vboard";
        var chk = $(this).closest('.'+targetClass).hasClass(eventClass);
        $("."+targetClass).removeClass(eventClass);
        if( chk == false) $(this).closest('.'+targetClass).addClass(eventClass);
    });

	<?php if(preg_match("/(main)|(product\.)|(product\.md_list)|(product\.time_list)|(product\.list)/",$pn) > 0 ){?>
		$(document).ready(function(){
			var timer_auth_chk = [];
			$('.js_item_box').each(function(i,v){
				var data = $(v).data();
				var check = $(v).closest('li').hasClass('swiper-slide-duplicate');
				if( check == true){ return true; }
				if( typeof data.timesale == 'undefined' || !data.timesale || data.timesale=='N'){ return true; }
				if(data.timesale=='Y' && data.timesaletype=='Y' && data.timeday<=1 && data.beforetimesale=='N'){
					time_sale_timmer_set(data.pcode);
				}
			});
		});
	<?php }?>
</script>

<script src="<?php echo $SkinData['skin_url']; ?>/js/time_sale_timmer.js" type="text/javascript"></script>
<?php include_once(OD_PROGRAM_ROOT.'/inc.footer.php'); // 스킨 내부파일로 직접 include 하면 안됨. ?>

