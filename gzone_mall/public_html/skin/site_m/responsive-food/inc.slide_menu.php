
<?php // 슬라이드 메뉴 ?>
<div class="p_Slide">
    <div class="slide_in c_scroll_v">

		<?php
			// == 등급전체 정보를 가져옴
			$getGroupInfo = getGroupInfo();
			$coupon_cnt = get_coupon_enable_cnt();
	   ?>
		<?php // 내 정보 ?>
		<div class="my_info">
			<div class="slide_layout">
				<?php // 내 정보 ?>
				<div class="top_box">
					<div class="area_box left_box">
						<?php if(is_login()) { // 로그인 후 ?>
							<a href="/?pn=mypage.main" class="who">
								<span class="level"><img src="<?php echo get_img_src($getGroupInfo[$mem_info['in_mgsuid']]['mobile_icon'],IMG_DIR_ICON); ?>" alt="" /></span>
								<span class="name"><?php echo $mem_info['in_name']; ?>님</span>
							</a>
						<?php } else { // 로그인 전 ?>
							<a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="who">로그인을 해주세요</a>
						<?php } ?>
					</div>
                    
					<div class="area_box right_box">
                        <?php // // {LCY} : 하이앱 { ?>
                        <?php if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){?>
                            <div class="Add_app_ctrl">
                                <a href="#none" onclick="return false;" class="btn type_set js_onoff_event" data-target=".Add_app_set" data-add="if_open_set">설정</a>
                                <a href="/?pn=alarm" class="btn type_push" title="알림">알림
                                	<?php if( $appUserInfo['var']['push_latest_count'] > 0){ ?>
                                    <span class="dot"></span><?php // 안읽은 알림 있을때 노출 ?>
	                                <?php } ?>
                                </a>
                            </div>
                        <?php } ?>
                        <?php // // {LCY} : 하이앱 } ?>
						<a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".p_Slide" data-add="if_open_slide" title="닫기"></a>
					</div>
				</div><!-- end top_box -->

				<?php if( $AppUseMode === true && function_exists('is_app') === true && is_app() === true) { ?>
				<?php // // {LCY} : 하이앱 { ?>
				<?php // 앱설정-레이어 ?>
				<div class="Add_app_set">
					<div class="slide_layout white_box">
						<ul class="ul">
							<li class="li">
								<div class="set_tit">알림 설정</div>
								<dl>
									<dt>쇼핑 혜택 및 이벤트 알림</dt>
									<dd>
										<?php // 알림 체크 ?>
										<label class="label">
											<input type="checkbox" name="app_alram" class="js_app_alram_set" value="Y" <?php echo $appUserInfo['au_push_send'] == 'Y' ? ' checked':''  ?>>
											<span class="icon"></span>
										</label>
									</dd>
								</dl>
							</li>
							<li class="li">
								<div class="set_tit">앱 버전정보</div>
								<dl>
									<dt class="this_ver"><?php // 현재버전 ?><?php echo $app_info['version']; ?></dt>
									<dd>
										<?php if( $app_update_use !== true) { ?>
										<div class="btn_update this_up" >최신버전</div><?php // 최신버전이면 노출 ?>
										<?php }else{ ?>
										<a href="<?php echo $app_update_url; ?>" target="_blank" class="btn_update" >업데이트</a><?php // 최신버전 아니면 노출 ?>
										<?php } ?>
									</dd>
								</dl>
							</li>
						</ul>
						<?php // 닫기 버튼 ?>
						<a href="#none" onclick="return false;" class="btn_set_close js_onoff_event" data-target=".Add_app_set" data-add="if_open_set">확인</a>
					</div>

			        <script>
			        $(document).on('change', '.js_app_alram_set',function(){
			            var chk = $(this).prop('checked');
			            var pushState = '';
			            if( chk == true){
			                pushState  = 'Y';
			            }else{
			                pushState  = 'N';
			            }
			            location.href="<?php echo $VARAPP['action/url/pushState']; ?>"+pushState;
			        });
			        
			        function updatePushState(pushState){
			            $.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/inc.app.php',type:'post',dataType:'json',data:{_mode:'push_status',pushState:pushState}})
			            .done(function(e){
			                try{
			                    if(e.rst == 'success'){}
			                }catch(e){}
			            }).fail(function(){
			                alert('fail');
			            });
			        }
			        </script>

				</div><!-- end sc_Set -->
				<?php } ?>
				<?php // // {LCY} : 하이앱 } ?>

				<?php
					if(is_login()) { // 로그인 후
						$SlideOrderCnt = get_order_ing_cnt(array('결제대기', '결제완료', '배송대기', '배송준비', '배송중'));
				?>
					<?php // 바로가기 메뉴 ?>
					<ul class="go_mypage">
						<li>
							<a href="/?pn=mypage.order.list" class="btn type_my"><span class="tx">마이쇼핑</span></a>
						</li>
						<li>
							<a href="/?pn=shop.cart.list" class="btn type_cart"><span class="tx">장바구니</span>
								<?php if($cart_cnt > 0){?>
									<span class="dot"></span>
								<?php }?>
							</a>
						</li>
						<li>
							<a href="<?php echo (is_login()?'/?pn=mypage.order.list':'/?pn=member.login.form&_rurl='.urlencode('/?pn=mypage.order.list')); ?>" class="btn type_deliver">
								<span class="tx">주문배송</span>
								<?php if($SlideOrderCnt>0){?>
									<span class="dot"></span>
								<?php }?>
							</a>
						</li>
						<li>
							<a href="/?pn=mypage.wish.list" class="btn type_wish"><span class="tx">찜한상품</span></a>
						</li>
					</ul><!-- end go_mypage -->

					<ul class="member_link">
						<li><a href="/?pn=mypage.point.list" class="btn">적립금 <?php echo number_format( 1 * $mem_info['in_point']); ?>원</a></li>
						<li><a href="/?pn=mypage.coupon.list" class="btn">쿠폰 <?php echo number_format( 1 * $coupon_cnt); ?>장</a></li>
					</ul><!-- end member_link -->
					<div class="member_tx this_login">
						<a href="<?php echo OD_PROGRAM_URL; ?>/member.login.pro.php?_mode=logout" class="btn"><span class="tx">로그아웃</span></a>
					</div>
				<?php } else { // 로그인 전 ?>
					<ul class="member_link">
						<li><a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn">로그인</a></li>
						<li><a href="/?pn=member.join.agree" class="btn">회원가입</a></li>
					</ul><!-- end member_link -->
					<?php // 비회원 주문불가일 때 숨김 ?>
					<?php if($siteInfo['s_none_member_buy']=='Y'){?>
						<div class="member_tx">
							<span class="tt">비회원으로 주문하셨나요?</span>
							<a href="/?pn=service.guest.order.list" class="btn"><span class="tx">비회원 주문조회</span></a>
						</div><!-- end member_tx -->
					<?php }?>
				<?php } ?>
			</div>
		</div><!-- end my_info -->

		<?php // 카테고리 ?>
		<div class="category">
			<div class="slide_layout">
				<ul class="ul">
					<?php foreach($AllCate as $k=>$v) { ?>
					<li class="li js_box_sd_cate<?php echo (count($v['sub']) <= 0?' if_no2':null); ?>">
						<div class="first_ctg">
							<a href="/?pn=product.list&cuid=<?php echo $v['c_uid']; ?>" class="ctg_name"><?php echo $v['c_name']; ?></a>
							<a href="#none" class="btn_open js_btn_sd_cate"><span class="ic"></span></a>
						</div><!-- end depth_1 -->
						<div class="second_ctg">
							<ul>
								<?php foreach($v['sub'] as $kk=>$vv) { ?>
									<li><a href="/?pn=product.list&cuid=<?php echo $vv['c_uid']; ?>" class="ctg_name"><?php echo $vv['c_name']; ?></a></li>
								<?php } ?>
							</ul>
						</div><!-- end depth_2 -->
					</li>
					<?php } ?>
				</ul>

				<?php // 타입별 상품 ?>
				<?php $TopEventList = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_view = 'Y' order by dts_idx asc "); ?>
				<?php  if(count($TopEventList) > 0){ ?>
					<div class="type_menu">
						<ul>
							<?php foreach($TopEventList as $k=>$v) { ?>
								<li><a href="/?pn=product.list&_event=type&typeuid=<?php echo $v['dts_uid']; ?>" class="type_name"><?php echo $v['dts_name']; ?></a></li>
							<?php } ?>
						</ul>
					</div><!-- end type_menu -->
				<?php } ?>
			</div>
		</div><!-- end cate_box -->

		<?php // 전체 서비스 ?>

		<div class="category type_event">
			<div class="slide_layout">
				<ul class="ul">
					<?php if($siteInfo['s_brand_view']=='Y'){?>
						<li class="li">
							<div class="first_ctg"><a href="/?pn=product.brand_list" class="ctg_name">브랜드</a></div>
						</li>
					<?php }?>

					<?php if($siteInfo['s_promotion_plan_view']=='Y'){?>
						<?php $promotion_cnt = _MQ_result(" select count(*) as cnt from smart_promotion_plan where pp_view='Y' "); ?>
						<li class="li">
							<div class="first_ctg">
								<a href="/?pn=product.promotion_list" class="ctg_name">
									<?php echo $siteInfo['s_promotion_plan_title'];?>
									<?php if($promotion_cnt>0){?><strong><?php echo $promotion_cnt;?></strong><?php }?>
								</a>
							</div>
						</li>
					<?php }?>

					<?php if($siteInfo['s_promotion_attend_view']=='Y'){?>
						<li class="li">
							<div class="first_ctg"><a href="/?pn=promotion.attend" class="ctg_name">출석체크</a></div>
						</li>
					<?php }?>

					<?php $my_eval_cnt = number_format(get_talk_total("all","eval","normal")); ?>
					<li class="li">
						<div class="first_ctg">
							<a href="/?pn=service.eval.list" class="ctg_name">상품리뷰
								<?php if($my_eval_cnt>0){?><strong><?php echo $my_eval_cnt;?></strong><?php }?>
							</a>
						</div>
					</li>

					<?php $my_qna_cnt = number_format(get_talk_total("all","qna","normal")); ?>
					<li class="li">
						<div class="first_ctg">
							<a href="/?pn=service.qna.list" class="ctg_name">상품문의
							<?php if($my_qna_cnt>0){?><strong><?php echo $my_qna_cnt;?></strong><?php }?>
							</a>
						</div>
					</li>
					<?php if(count($event_bbsList)>0){?>
						<?php
							foreach($SlideEventboardMenu as $ebk=>$ebv){
								$event_cnt = get_board_cnt($ebv['uid']);
						?>
						<li class="li">
							<div class="first_ctg">
								<a href="/?pn=board.list&_menu=event" class="ctg_name"><?php echo $ebv['title'];?>
								<?php if($event_cnt>0){?><strong><?php echo $event_cnt;?></strong><?php }?>
								</a>
							</div>
						</li>
						<?php }?>
					<?php }?>

				</ul>
			</div>
		</div><!-- end cate_box -->

		<?php // 게시판메뉴 : 게시판 순서,비노출,분류 구분 ?>
		<div class="board_menu">
			<div class="slide_layout">
				<ul class="tab_tit">
					<li><a href="#none" onclick="return false" class="tab js_tab1 hit">SERVICE</a></li>
					<li><a href="#none" onclick="return false" class="tab js_tab1">COMMUNITY</a></li>
				</ul>

				<div class="tab_cont js_tab1_content">
					<ul>
						<li><a href="/?pn=service.main" class="menu">고객센터 바로가기</a></li>
						<?php foreach($SlideServiceArr as $sk => $sv){?>
							<li><a href="<?php echo $sv['link'];?>" class="menu"><?php echo $sv['title'];?></a></li>
						<?php }?>
					</ul>
				</div>

				<div class="tab_cont js_tab1_content" style="display: none;">
					<ul>
						<?php foreach($SlideCommArr as $ck=>$cv) { ?>
							<li><a href="<?php echo $cv['link'];?>" class="menu"><?php echo $cv['title'];?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div><!-- end sitemap_box -->

		<?php // cs 정보 ?>
		<div class="cs_info">
			<div class="slide_layout">
				<div class="cs_in">
					<a href="tel:<?php echo $siteInfo['s_glbtel']; ?>" class="tel"><?php echo $siteInfo['s_glbtel']; ?></a>
					<div class="open_time"><?php echo nl2br($siteInfo['s_cs_info']); ?></div>
				</div>
				<ul class="go_page">
					<?php if($siteInfo['s_request_partner_view']=='Y'){?>
						<li><a href="/?pn=service.partner.form" class="btn">제휴문의</a></li>
					<?php }?>
					<li><a href="/?pn=pages.view&type=pages&data=company" class="btn">회사소개</a></li>
				</ul>
			</div>
		</div><!-- end cs_box -->
    </div><!-- end slide_in -->
    <div onclick="return false;" class="slide_bg js_onoff_event" data-target=".p_Slide" data-add="if_open_slide"></div>
</div><!-- end sc_Slide -->