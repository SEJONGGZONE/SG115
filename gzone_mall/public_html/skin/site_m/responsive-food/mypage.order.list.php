<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = "주문내역";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_gridpage">
	<div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->

		<div class="grid_section">
			<div class="layout_fix">

				<div class="c_page_tab">
					<ul>
						<li<?php echo $order_type == '' ? ' class="hit"': '' ?>><a href="/?pn=mypage.order.list" class="tab">전체 주문내역</a></li>
						<li<?php echo $order_type == 'delivery' ? ' class="hit"': '' ?>><a href="/?pn=mypage.order.list&order_type=delivery" class="tab">배송 주문내역</a></li>
						<li<?php echo $order_type == 'ticket' ? ' class="hit"': '' ?>><a href="/?pn=mypage.order.list&order_type=ticket" class="tab">티켓 주문내역</a></li>
					</ul>
				</div><!-- end c_page_tab -->

				<?php if( in_array($order_type, array('delivery','ticket')) > 0){?>
				<?php  // LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- date => status 추가 ?>
				<?php // 배송주문통계 ?>
				<div class="c_my_stats">
					<?php if($order_type=='delivery'){?>
						<ul class="ul">
							<li class="li <?php echo $o_status=='결제대기'?'hit':'';?>" >
								<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'결제대기','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a><?php // 각 상태별 리스팅 ?>
								<div class="state_box">
									<div class="tit">결제대기</div>
									<div class="total"><strong><?php echo number_format( 1 * $order_status['결제대기']); ?></strong></div>
								</div>
							</li>
							<li class="li <?php echo $o_status=='결제완료'?'hit':'';?>" >
							   <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'결제완료','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
								<div class="state_box">
									<div class="tit">결제완료</div>
									<div class="total"><strong><?php echo number_format( 1 * $order_status['결제완료']); ?></strong></div>
								</div>
							</li>
							<li class="li <?php echo $o_status=='배송준비'?'hit':'';?>" >
								<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'배송준비','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
								<div class="state_box">
									<div class="tit">배송준비</div>
									<div class="total"><strong><?php echo number_format( 1 * $order_status['배송준비']); ?></strong></div>
								</div>
							</li>
							<li class="li <?php echo $o_status=='배송중'?'hit':'';?>" >
								<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'배송중','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
								<div class="state_box">
									<div class="tit">배송중</div>
									<div class="total"><strong><?php echo number_format( 1 * $order_status['배송중']); ?></strong></div>
								</div>
							</li>
							<li class="li <?php echo $o_status=='배송완료'?'hit':'';?>" >
								<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'배송완료','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
								<div class="state_box">
									<div class="tit">배송완료</div>
									<div class="total"><strong><?php echo number_format( 1 * $order_status['배송완료']); ?></strong></div>
								</div>
							</li>
						</ul>
					<?php }?>

					<?php if($order_type=='ticket'){?>
					<ul class="ul">
						<li class="li <?php echo $o_status=='결제대기'?'hit':'';?>" >
							<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'결제대기','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a><?php // 각 상태별 리스팅 ?>
							<div class="state_box">
								<div class="tit">결제대기</div>
								<div class="total"><strong><?php echo number_format( 1 * $ticket_order_status['결제대기']); ?></strong></div>
							</div>
						</li>
						<li class="li <?php echo $o_status=='발급완료'?'hit':'';?>" >
						   <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'발급완료','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
							<div class="state_box">
								<div class="tit">발급완료</div>
								<div class="total"><strong><?php echo number_format( 1 * $ticket_order_status['발급완료']); ?></strong></div>
							</div>
						</li>
						<li class="li <?php echo $o_status=='사용완료'?'hit':'';?>" >
							<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'사용완료','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
							<div class="state_box">
								<div class="tit">사용완료</div>
								<div class="total"><strong><?php echo number_format( 1 * $ticket_order_status['사용완료']); ?></strong></div>
							</div>
						</li>
						<li class="li <?php echo $o_status=='기간만료'?'hit':'';?>" >
							<a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'기간만료','date'=>'status','order_type'=>$order_type)); ?>" class="upper_link"></a>
							<div class="state_box">
								<div class="tit">기간만료</div>
								<div class="total"><strong><?php echo number_format( 1 * $ticket_order_status['기간만료']); ?></strong></div>
							</div>
						</li>
					</ul>
					<?php }?>

					<div class="c_my_go">
						<span class="due_date">최근 1년 상품단위 통계</span>
						<ul>
							<li><a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'주문취소','date'=>$date,'order_type'=>$order_type)); ?>" class="menu">주문취소(<?php echo number_format( 1 * $order_status['주문취소']); ?>)</a></li>
							<?php if($order_type=='delivery'){?>
								<?php // 환불요청 토탈수 노출(0도 표시) ?>
								<li><a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list','o_status'=>'환불요청','date'=>$date,'order_type'=>$order_type)); ?>" class="menu">환불요청(<?php echo number_format( 1 * $order_status['환불요청']); ?>)</a></li>
							<?php }?>
						</ul>
					</div>
				</div><!-- end c_my_stats -->
				<?php } ?>


				<form name="od_search" method="get" class="c_board_ctrl">
					<input type="hidden" name="pn" value="mypage.order.list">
					<input type="hidden" name="o_status" value="<?php echo $o_status; ?>">
					<input type="hidden" name="order_type" value="<?php echo $order_type; ?>">
                    <div class="area_box area_left">
                        <div class="board_tit">
                            <div class="name"><?php echo $page_title; ?></div>
                            <div class="total">Total <?php echo number_format( 1 * $TotalCount); ?></div>
                        </div>
                    </div><!-- end area_left -->

                    <div class="area_box area_right">
                        <div class="period_box">
                            <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'all', 'o_status'=>$o_status,'order_type'=>$order_type)); ?>" class="btn<?php echo ($date == 'all' ? ' hit' : null); ?>">전체</a>
                            <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'today', 'o_status'=>$o_status,'order_type'=>$order_type)); ?>" class="btn<?php echo ($date == 'today' ? ' hit' : null); ?>">오늘</a>
                            <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'week', 'o_status'=>$o_status,'order_type'=>$order_type)); ?>" class="btn<?php echo ($date == 'week' ? ' hit' : null); ?>">일주일</a>
                            <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month1', 'o_status'=>$o_status,'order_type'=>$order_type)); ?>" class="btn<?php echo ($date == 'month1' ? ' hit' : null); ?>">1개월</a>
                            <a href="/<?php echo URI_Rebuild('?', array('pn'=>'mypage.order.list', 'date'=>'month3', 'o_status'=>$o_status,'order_type'=>$order_type)); ?>" class="btn<?php echo ($date == 'month3' ? ' hit' : null); ?>">3개월</a>
                        </div>
                        <div class="due_box">
                            <input type="text" name="o_rdate_start" class="input_design type_date js_pic_day" value="<?php echo $o_rdate_start; ?>" placeholder="날짜선택" readonly>
                            <span class="dash">~</span>
                            <input type="text" name="o_rdate_end" class="input_design type_date js_pic_day right" value="<?php echo $o_rdate_end; ?>" placeholder="날짜선택"  readonly>
							 <a href="#none" onclick="document.od_search.submit();" class="c_btn h40 black line">조회</a>
                        </div>
                    </div><!-- end area_right -->
				</form><!-- end c_board_ctrl -->

				<div class="c_my_list type_link">
					<?php
						// 주문내역이 있을때
						if(count($res) > 0 ) {
					?>
						<ul>
							<?php
								foreach($res as $k=>$v){
									# 상품별 정보를 가져온다
									$app_product_list = array();
									$app_product_list = _MQ_assoc("
										select p.p_name, p.p_img_list_square, op.*, sum( op_cnt * (op_price) ) as op_tPrice
										from smart_order_product as op
										left join smart_product as p on (p.p_code=op.op_pcode) where op_oordernum = '".$v['o_ordernum']."' group by op_pcode order by op_uid asc
									");
									$app_product_name = $app_product_list[0]['p_name'];
									if( count($app_product_list)>1 ) { $app_product_name .= ' 외 '.(count($app_product_list)-1).'개 '; }

									# 주문 상세보기 URL
									$order_view_url = '/' . URI_Rebuild('?', array('pn'=>'mypage.order.view', 'ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC));

									# 상품 이미지
									$thumb_img	= get_img_src('thumbs_s_'.$app_product_list[0]['p_img_list_square']);
									if($thumb_img=='') $thumb_img = $SkinData['skin_url']. '/images/c_img/thumb.gif';


									# 주문 상태에 따른 취소 버튼
									unset($app_btn_cancel);
									if($v['o_canceled'] == "N"  && $v['npay_order'] != 'Y'  ) {  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
										if( in_array($v['o_status'] , array('결제대기','결제완료','배송대기')) ){

											if($v['o_status']!='결제대기'&&($v['o_paymethod']=='virtual'||$v['o_paymethod']=='online')) { // 주문상태가 결제대기가 아니고, 결제방법이 가상,무통장 인건만
												$cancel_function = 'order_cancel_virtual(\''.$v['o_ordernum'].'\', \''.$v['o_price_real'].'\')'; // 가상계좌
											}else {
												$cancel_function = 'order_cancel(\''.$v['o_ordernum'].'\')'; // 일반
											}

											// 주문취소 생성
											$app_btn_cancel = '<a href="#none" onclick="'. $cancel_function .'" class="c_btn h25 light">주문취소</a>';

											// 상품이 /취소/반품/교환 요청중인 상품 검사
											$chk_part_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$v['o_ordernum']."' and op_is_addoption = 'N' and op_cancel != 'N' ");
											if( $chk_part_cancel > 0){
												$app_btn_cancel = "<a href='#none' onclick='alert(\"취소/반품/교환 요청중인 상품이 있습니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h25 light'>주문취소</a>" ;
											}
										}
										else {
											$app_btn_cancel = "<a href='#none' onclick='alert(\"주문취소가 불가능한 상태입니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h25 light'>주문취소</a>" ;
										}
									}

			                        // <티켓> 주문취소시 에외처리
			                        if( in_array($v['o_order_type'], array('ticket','both')) && $v['o_canceled'] == "N" && $v['o_paystatus'] == "Y"){
			                            $app_btn_cancel = "<a href='#none' onclick='alert(\"티켓이 발급된 경우 주문취소가 불가능합니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h25 light'>주문취소</a>" ;
			                        }


									# 주문상태 // 각주문 단계별 클래스 추가해주세요 : if_wait / if_complete / if_ing / if_delivery / if_cancel
									unset($o_status_print, $class_ostatus);
									switch($v['o_status']){
										case '배송대기':
										case '결제완료':
											$o_status_print = '<span class="c_tag red line">결제완료</span>';
											$class_ostatus = 'if_complete';
										break;

										case '결제대기':
											$o_status_print = '<span class="c_tag gray">결제대기</span>';
											$class_ostatus = 'f_wait';
										break;

										case '배송준비':
											$o_status_print = '<span class="c_tag gray">배송준비</span>';
											$class_ostatus = 'if_ing';
										break;

										case '배송완료':
											$o_status_print = '<span class="c_tag green line">배송완료</span>';
											$class_ostatus = 'if_delivery';
										break;

										case '배송중':
											$o_status_print = '<span class="c_tag green">배송중</span>';
											$class_ostatus = 'if_ing';
										break;

										case '주문취소':
											$o_status_print = '<span class="c_tag gray">주문취소</span>';
											$class_ostatus = 'if_cancel';
										break;

										case '환불요청':
											$o_status_print = '<span class="c_tag gray">환불요청</span>';
										break;

										// <티켓> 발급완료 추가
										case '발급완료':
											$o_status_print = '<span class="c_tag blue line">발급완료</span>';
										break;

									}

									# 배송조회
									$delivery_print = ''; $arr_sendnum = array();
									if(count($app_product_list)>0){
										foreach($app_product_list as $sk=>$sv){
											if(in_array($sv['op_sendstatus'], array('배송중','배송완료')) && $sv['op_sendcompany'] && $sv['op_sendnum']){
												if($arr_sendnum[$sv['op_sendnum']] > 0) continue; // 중복제거
												$arr_sendnum[$sv['op_sendnum']]++;
												$delivery_print .= '
													<a href="'. ($v['npay_order'] == 'Y' ? ($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]) : $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="delivery" target="_blank">배송조회 : '. $sv['op_sendcompany'] .' '. $sv['op_sendnum'] .'</a>
												';
											}
										}
									}

									/*****
									# 이미지 스크립트 효과
									unset($img_bxSlider);
									if(count($app_product_list)>1 ) {
									$img_bxSlider = "
										<script>
											$(window).on('load',function(){
												var mypage_main_product_slider_".$k." = $('.mypage_main_product_slider_".$k."').bxSlider({
													auto: true, autoHover: false, speed: 700, mode: 'fade',
													slideSelector: '', easing: 'easeInOutCubic', useCSS: false,
													slideMargin: 0, slideWidth: 0, minSlides: 1, maxSlides: 1,
													pager: false, controls: false,
													onSlideBefore: function() { mypage_main_product_slider_".$k.".stopAuto(); },
													onSlideAfter: function() { mypage_main_product_slider_".$k.".startAuto(); }
												});
											});
										</script>";
									}
									*****/
							?>
									<li class="<?php echo $class_ostatus; ?>">
										<a href="<?php echo $order_view_url; ?>" class="upper_link"></a>
										<div class="area_conts">
											<div class="thumb"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($app_product_name); ?>"></div>
											<div class="conts_wrap">
												<?php if( $v['npay_order'] == 'Y'){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 네이버페이의 경우 아이콘 표기  --   ?>
													<span class="naver_pay"><strong>네이버페이</strong></span>
												<?php } ?>
												<div class="order_num"><strong><?php echo $v['o_ordernum']; ?></strong></div>
												<div class="tit"><?php echo $app_product_name; ?></div>
												<?php echo $delivery_print; ?>
												<?php echo $app_btn_cancel; ?>
											</div>
										</div><!-- end area_conts -->
										<div class="area_state">
											<?php echo $o_status_print; ?>
											<div class="date">주문일 : <?php echo date('Y-m-d',strtotime($v['o_rdate'])); ?></div>
											<div class="price"><?php echo number_format( 1 * $v['o_price_real']); ?>원</div>
										</div><!-- end area_state -->
									</li>
							<?php } ?>
						</ul>
					<?php
						}
					?>
					<?php if(count($res) < 1 ) { // 내용 없을때 노출 ?>
							<div class="c_none"><span class="gtxt">주문내역이 없습니다.</span></div>
					<?php } ?>
				</div><!-- end c_my_list -->

				<div class="c_pagi">
					<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div><!-- end c_pagi -->

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

    </div><!-- end layout_grid -->
</div><!-- end c_section -->


<?php
	# 가상계좌 주문취소일 경우 환불계정 레이아웃 미리 생성
	include_once($SkinData['skin_root'].'/mypage.order.pro.cancel_virtual.php');
?>


<script id="mypage_order_list">
	// 주문취소
	var cancel_trigger = true; // SSJ : 중복취소 방지 : 2021-12-31
	function order_cancel(ordernum){
		if(ordernum == '' || ordernum == undefined){
			alert('잘못된 접근입니다.');
			return false;
		}

		// SSJ : 중복취소 방지 : 2021-12-31
		if(cancel_trigger === false){
			alert('주문 취소를 진행중입니다. 잠시만 기다려 주시기 바랍니다.');
			return false;
		}

		if( confirm('정말 주문을 취소하시겠습니까?') == true ) {
			cancel_trigger = false; // SSJ : 중복취소 방지 : 2021-12-31
			common_frame.location.href=("<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?php echo $_PVSC; ?>");
		}

	}

	// 가상계좌/무통장 주문취소
	function order_cancel_virtual(ordernum, price){

		var open_chk = $('.c_layer.type_order_cancel_view').hasClass('if_open_layer');
		// 콤마추가
		price = (price + '').comma();

		// 데이터 입력
		$('.cancel_virtual').find('input[name=ordernum]').val(ordernum);
		$('.cancel_virtual').find('.js_data_ordernum').text(ordernum);
		$('.cancel_virtual').find('.js_data_price').text(price);

		if(open_chk==false){
			$('.c_layer.type_order_cancel_virtual').addClass('if_open_layer');
		}else{
			$('.c_layer.type_order_cancel_virtual').removeClass('if_open_layer');
			// 데이터 삭제
			$('.cancel_virtual').find('input[name=ordernum]').val('');
			$('.cancel_virtual').find('.js_data_ordernum').text('');
			$('.cancel_virtual').find('.js_data_price').text('');
		}
	}
</script>