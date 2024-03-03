<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = "마이페이지";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력
?>

<div class="c_section ">

	<?php
	include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
	?>

	<div class="layout_fix">

		<div class="c_my_main">
			<ul class="flex_bx">
				<li class="li box_info js_level_stage">
					<div class="stats_bx">
						<?php
							// == 등급전체 정보를 가져옴
							$getGroupInfo = getGroupInfo();
						?>
						<dl>
							<dt><div class="level_img"><img src="<?php echo get_img_src($getGroupInfo[$mem_info['in_mgsuid']]['mobile_icon'],IMG_DIR_ICON); ?>" alt="" /></div></dt>
							<dd>
								<div class="level_name"><?php echo $getGroupInfo[$mem_info['in_mgsuid']]['name'] ?></div>
								<div class="name"><strong><?php echo $mem_info['in_name']; ?></strong>님의 Mypage</div>
								<a href="#none" class="more js_onoff_event" data-target=".c_layer.type_level" data-add="if_open_layer" onclick="return false;"><strong>등급별 혜택 보기</strong></a>
							</dd>
						</dl>
					</div>
					<div class="history">
						<?php
							if($mem_info['in_tel2']){
								// 전화번호 부분 감추기
								$ex_hp = explode('-', tel_format($mem_info['in_tel2']));
								foreach($ex_hp as $k=>$v){
									if($k>0) $ex_hp[$k] = LastCut($v, (strlen($v)-2));
								}
								$private_hp = implode('-', $ex_hp);
						?>
							<dl>
								<dt>휴대폰</dt>
								<dd><?php echo $private_hp; ?></dd>
							</dl>
						<?php } ?>
						<?php if($mem_info['in_address_doro']){ ?>
							<dl>
								<dt>배송지</dt>
								<dd><?php echo $mem_info['in_address_doro']; ?> ****</dd>
							</dl>
						<?php }else if($mem_info['in_address1']){ ?>
						<?php } ?>
					</div>
					<?php // 등급별 혜택팝업 ?>
					<div class="c_layer type_level">
						<div class="wrapping">
							<div class="tit_box">
								<div class="tit">등급별 조건 및 혜택</div>
								<a href="#none" class="btn_close js_onoff_event" title="닫기" data-target=".c_layer.type_level" data-add="if_open_layer" onclick="return false;"></a>
							</div>

							<div class="conts_box c_scroll_v">
								<div class="table_list">
									<ul class="thead">
										<li>등급명</li>
										<li>조건</li>
										<li>혜택</li>
									</ul>
									<?php
									foreach($getGroupInfo as $mgsuid=>$val){
										// 등급조건
										$arrCondition = array(); $printCondition = '';
										if($val['condition_totprice'] > 0){ $arrCondition[] = number_format( 1 * $val['condition_totprice']).'원 이상 구매'; }
										if($val['condition_totcnt'] > 0){ $arrCondition[] = number_format( 1 * $val['condition_totcnt']).'회 이상 구매'; }
										if(count($arrCondition) > 0){ $printCondition = implode("<br>",$arrCondition); }
										else{ $printCondition = '제한없음'; }

										// 등급혜택
										$arrBoon = array(); $printBoon = '';
										if($val['give_point_per'] > 0){ $arrBoon[] = odt_number_format( 1 * $val['give_point_per'],1).'% 적립'; }
										if($val['sale_price_per'] > 0){ $arrBoon[] = odt_number_format( 1 * $val['sale_price_per'],1).'% 할인'; }
										if(count($arrBoon) > 0){ $printBoon = implode("<br>",$arrBoon); }
										else{ $printBoon = '없음'; }
										?>
										<?php // 한 등급당 반복구간 ?>
										<ul <?php echo $mgsuid == $mem_info['in_mgsuid']  ? ' class="hit" ': null // 자신의 등급에 표기 ?>>
											<li class="tit"><?php echo $val['name']; ?></li>
											<li><?php echo $printCondition; ?></li>
											<li><?php echo $printBoon; ?></li>
										</ul>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="bg_close js_onoff_event" data-target=".c_layer.type_level" data-add="if_open_layer" onclick="return false;"></div>
					</div><!-- end c_layer -->
				</li>
				<li class="li box_benefit">
					<?php
						// 적립예정 포인트
						$arr_point = _MQ_assoc(" select * from smart_point_log where (1) and pl_inid='". get_userid() ."' and pl_delete = 'N' and pl_point>0 and pl_status!='Y' ");
						foreach($arr_point as $k=>$v){
							// 적립예정 포인트
							$all_expect_point += $v['pl_point'];
						}
					?>
					<div class="stats_bx">
						<div class="tit"><a href="/?pn=mypage.point.list">적립금</a></div>
						<div class="mine"><a href="/?pn=mypage.point.list"><strong><?php echo number_format( 1 * $mem_info['in_point']); ?></strong></a></div>
					</div>
					<div class="history">
						<dl>
							<dt>사용가능</dt>
							<dd><?php echo number_format( 1 * $mem_info['in_point']); ?>원</dd>
						</dl>
						<dl>
							<dt>지급예정</dt>
							<dd><?php echo number_format( 1 * $all_expect_point); ?>원</dd>
						</dl>
					</div>
				</li>
				<li class="li box_benefit">
					<div class="stats_bx">
						<div class="tit"><a href="/?pn=mypage.coupon.list">쿠폰</a></div>
						<div class="mine"><a href="/?pn=mypage.coupon.list"><strong><?php echo number_format( 1 * get_coupon_enable_cnt()); ?></strong></a></div>
					</div>
					<div class="history">
						<dl>
							<dt>사용가능</dt>
							<dd><?php echo $TotalCountReady;?>장</dd>
						</dl>
						<dl>
							<dt>사용대기</dt>
							<dd><?php echo $TotalCountWait;?>장</dd>
						</dl>
					</div>
				</li>
			</ul>
			<div class="c_my_go">
				<ul>
					<li><a href="/?pn=mypage.eval.list" class="menu">내가 쓴 리뷰</a></li>
					<li><a href="/?pn=mypage.qna.list" class="menu">내가 쓴 Q&A</a></li>
					<li><a href="/?pn=mypage.inquiry.list" class="menu">1:1 온라인문의</a></li>
				</ul>
			</div>
		</div><!-- end my_main -->

		<div class="c_group_tit">
			<div class="tit">최근 주문내역</div>
			<a href="/?pn=mypage.order.list" class="btn_more"><strong>전체보기</strong></a>
		</div>

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
							if($v['o_canceled'] != "Y"  && $v['npay_order'] != 'Y'  ) {  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
								if( in_array($v['o_status'] , array('결제대기','결제완료','배송대기')) ){

									if($v['o_status']!='결제대기'&&($v['o_paymethod']=='virtual'||$v['o_paymethod']=='online')) { // 주문상태가 결제대기가 아니고, 결제방법이 가상,무통장 인건만
										$cancel_function = 'order_cancel_virtual(\''.$v['o_ordernum'].'\', \''.$v['o_price_real'].'\')'; // 가상계좌
									}else {
										$cancel_function = 'order_cancel(\''.$v['o_ordernum'].'\')'; // 일반
									}

									// 주문취소 생성
									$app_btn_cancel = '<a href="#none" onclick="'. $cancel_function .'" class="c_btn h25 light">주문취소</a>';

									// 상품이 /취소/반품/교환 요청중인 상품 검사
									$chk_part_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$v['o_ordernum']."' and op_cancel != 'N' ");
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
									$o_status_print = '<span class="c_tag light line">결제대기</span>';
									$class_ostatus = 'if_wait';
								break;

								case '배송준비':
									$o_status_print = '<span class="c_tag light">배송준비</span>';
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
									$o_status_print = '<span class="c_tag light">주문취소</span>';
									$class_ostatus = 'if_cancel';
								break;

								case '환불요청':
									$o_status_print = '<span class="c_tag light">환불요청</span>';
								break;

								// <티켓> 발급완료 추가
								case '발급완료':
									$o_status_print = '<span class="c_tag black line">발급완료</span>';
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
								<div class="area_conts">
									<div class="thumb">
										<a href="<?php echo $order_view_url; ?>" class="upper_link"></a>
										<img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($app_product_name); ?>">
									</div>
									<div class="conts_wrap">
										<?php if( $v['npay_order'] == 'Y'){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 네이버페이의 경우 아이콘 표기  --   ?>
											<div class="nv_icon"><strong>네이버페이</strong></div>
										<?php } ?>
										<div class="order_num"><?php echo $v['o_ordernum']; ?></div>
										<div class="tit">
											<a href="<?php echo $order_view_url; ?>" class="upper_link"></a>
											<?php echo $app_product_name; ?>
										</div>
										<?php echo $delivery_print; ?>
										<?php echo $app_btn_cancel; ?>
									</div>
								</div>
								<div class="area_state">
									<?php echo $o_status_print; ?>

									<div class="date">주문일 : <?php echo date('Y-m-d',strtotime($v['o_rdate'])); ?></div>
									<div class="price"><?php echo number_format( 1 * $v['o_price_real']); ?>원</div>
								</div>
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

		<div class="c_group_tit">
			<div class="tit">찜한상품</div>
			<a href="/?pn=mypage.wish.list" class="btn_more"><strong>전체보기</strong></a>
		</div>

		<div class="c_my_wish type_main">
			<?php if(count($myWishList) > 0){ ?>
				<ul>
					<?php
						foreach($myWishList as $k=>$v){
							$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
					?>
						<li>
							<div class="thumb">
								<a href="/?pn=product.view&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title=""></a>
								<img src="<?php echo $_img; ?>" alt="<?php echo addslashes($v['p_name']); ?>" />
								<?php if($v['p_stock'] <= 0) { ?>
									<div class="soldout">SOLD OUT</div>
								<?php } ?>
							</div>
							<a href="/?pn=product.view&pcode=<?php echo $v['p_code']; ?>" class="item_name"><?php echo stripslashes($v['p_name']); ?></a>
							<div class="item_price"><?php echo number_format( 1 * $v['p_price']); ?>원</div>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
			<?php if(count($myWishList) < 1){ // 내용 없을때 노출 ?>
				<div class="c_none"><span class="gtxt">찜한 상품이 없습니다.</span></div>
			<?php } ?>
		</div><!-- end c_my_wish -->


	</div><!-- end layout_fix -->
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
		// 콤마추가
		price = (price + '').comma();

		// 데이터 입력
		$('.cancel_virtual').find('input[name=ordernum]').val(ordernum);
		$('.cancel_virtual').find('.js_data_ordernum').text(ordernum);
		$('.cancel_virtual').find('.js_data_price').text(price);

		// KAY :: 2022-12-26 :: 레이어팝업 열고 닫기
		var layer_open_chk = $('.c_layer.type_order_cancel_virtual').hasClass('if_open_layer');
		if(layer_open_chk==false){
			 $('.c_layer.type_order_cancel_virtual').addClass('if_open_layer');
			 $('.cancel_virtual').find('input:first').focus();
		}else{
			 $('.c_layer.type_order_cancel_virtual').removeClass('if_open_layer');
			$('.cancel_virtual').find('input[name=ordernum]').val('');
			$('.cancel_virtual').find('.js_data_ordernum').text('');
			$('.cancel_virtual').find('.js_data_price').text('');
		}

	}

</script>