<?php
	$arr_product_sum = $arr_push_product = array();  // 변수 초기화
	$arr_open_pcode = array_filter(explode('|', $open_pcode)); // 열리 옵션 정보 체크
?>

<?php if(count($arr_cart) > 0){ ?>
	<form name="frm" method="post" class="cart_wrap">
		<input type="hidden" name="mode" value=""/>
		<input type="hidden" name="cuid" value=""/>
		<input type="hidden" name="code" value=""/>
		<input type="hidden" name="allcheck" value="Y"/>

		<div class="area_item">

			<div class="cart_ctrl">
				<dl>
					<dt>
						<a href="#none" onclick="cart_all_select(); return false;" class="btn btn_sel"><strong>전체선택</strong></a>
						<a href="#none" onclick="cart_select_delete(); return false;" class="btn btn_del"><strong>선택삭제</strong></a>
					</dt>
					<dd><a href="#none" onclick="cart_remove_all(); return false;" class="btn btn_clear"><strong>장바구니 비우기</strong></a></dd>
				</dl>
			</div><!-- end cart_ctrl -->

			<?php foreach($arr_cart as $crk=>$crv) {  ?>
				<div class="c_group_tit">
					<div class="shop_name"><?php echo $arr_customer[$crk]['cName']; ?></div>
					<div class="sub_txt">
						<?php echo ($arr_customer[$crk]['com_delprice_free'] > 0 ? '<em>'. number_format( 1 * $arr_customer[$crk]['com_delprice_free']) .'원</em>이상 무료배송 (개별배송 제외)' : ''); ?>
					</div>
				</div><!-- end cart_info -->

				<div class="cart_list">
					<ul class="ul">
						<?php
							// -- 변수 초기화
							unset($del_chk_customer);
							$arr_product = array(); // 업체별 상품 합계
							$arr_per_product = array(); // 상품별 합계 // ----- JJC : 상품별 배송비 : 2018-08-16 -----
							foreach($crv as $k=>$v) { // 업체별 상품 반복 구간

								/* 상품 정보 */
								$pr = $arr_product_info[$k]; // 업체 상품의 정보를 담는다.
								$pro_name	= strip_tags($pr['p_name']);	// 상품명
								$thumb_img	= get_img_src('thumbs_s_'.$pr['p_img_list_square']); // 상품 이미지
								if($thumb_img=='') $thumb_img = $SkinData['skin_url']. '/images/skin/thumb.gif';
								$pro_url = "/?pn=product.view&pcode=".$pr['p_code']; // 상품의 주소
								/* 상품 정보 끝 */

								// {{{회원등급혜택}}}
								unset($groupSetUse);
								if( $pr['p_groupset_use'] == 'Y' && is_login() == true ){
									if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
										$groupSetUse = true;
									}
								}
								// {{{회원등급혜택}}}

								// 체크박스 체크 여부 체크 - 총합계는 체크된 상품만 합산
								$_is_checked = false;
								if($app_cart_init){
									$_is_checked = true;
								}
								else if(is_array($_code) && in_array($pr['p_code'],$_code)){
									$_is_checked = true;
								}

								// -- 변수 초기화
								unset($option_html , $sum_price , $sum_product_cnt , $sum_point); // 2016-12-13 ::: 포인트 적용 수정 - JJC --> sum_point 추가
								$arr_options = array(); // 옵션저장배열
								foreach($v as $sk => $sv) {
									// KAY :: 2022-12-07 :: 옵션 구분 수정
									$arr_option_name = array($sv['c_option1'] ,$sv['c_option2'],$sv['c_option3'] );
									$arr_option_name = array_filter($arr_option_name);

									$option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim(($sv['c_is_addoption']=='Y' ? '<span class="ess_tx add">추가</span>' : '<span class="ess_tx">필수</span>') . implode(" / ",$arr_option_name));
									$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
									$option_tmp_cnt			= $sv['c_cnt'];
									$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
									$app_point				= $sv['c_point'];

									// 상품 수량 select 값
									$buy_limit_array = array();
									$buy_max = 200; // 최고 구매갯수 설정
									$buy_limit = $sv['buy_limit'] ? min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$sv['buy_limit']) : min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
									for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

									// LCY : 2022-12-21 : 티켓기능 -- 달력의 날짜 표시 {
									$dateoption_view = '';
									if( $sv['c_dateoption_use'] == 'Y' && !empty($sv['c_dateoption_date'])){
										$dateoption_view = '
										    <!-- 날짜요일 추가(띄어쓰기 유지) -->
		                                    <span class="date">'.$sv['c_dateoption_date'].' ('.$arr_day_week_short[date('w',strtotime($sv['c_dateoption_date']))].')</span>
		                                ';
	                            	}
	                            	// LCY : 2022-12-21 : 티켓기능 -- 달력의 날짜 표시 }

									$arr_options[] = '
										<li>
											<div class="opt_name">
												'.$dateoption_view.'
												<strong>'. $option_tmp_name .'</strong>
											</div>
											<div class="opt_price">
                                                <div class="price">'. number_format( 1 * $option_tmp_price) .'원</div>
                                                <div class="counter">
                                                    <a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'down\',\'\'); return false;" class="btn btn_minus '.($sv['c_cnt']<='1'?'none':'').' "><span class="shape"></span></a>
                                                    <input type="text" name="_ccnt['.$sv['c_uid'].']" id="cart_cnt_'. $sv['c_uid'] .'" value="'. $sv['c_cnt'] .'" class="counter_input" readonly="readonly">
                                                    <a href="#none" onclick="cart_modify('. $sv['c_uid'] .', \'up\', \''.($sv['p_buy_limit'] > 0 ? $sv['p_buy_limit'] : '').'\' ); return false;" class="btn btn_plus"><span class="shape"></span></a>
                                                </div>
                                                <a href="#none" onclick="cart_delete('. $sv['c_uid'] .');return false;" class="btn_delete" title="삭제"></a>
											</div>
										</li>
									';

									//상품수 , 포인트 , 상품금액
									if($_is_checked) $arr_product["cnt"] += $option_tmp_cnt;//상품수
									// ----- SSJ : 추가옵션은 배송비 미적용 : 2020-02-04 -----
									if($sv['c_is_addoption']<>'Y') $sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
									if($_is_checked) $arr_product["point"] += $app_point ;//포인트
									if($_is_checked) {
										$arr_product["sum"] += $option_tmp_sum_price;//상품금액
										$arr_per_product[$k]['sum'] += $option_tmp_sum_price;//상품금액// ----- JJC : 상품별 배송비 : 2018-08-16 -----
									}
									$sum_price += $option_tmp_sum_price;//상품금액
									$sum_point += $app_point;//상품당 포인트 합계 // 2016-12-13 ::: 포인트 적용 수정 - JJC

								} // end foreach => $v

								// 상품쿠폰 정보

								// 	KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
								$ex_coupon = explode("|", $pr['p_coupon']);
								$coupon_html = '';
								if($ex_coupon[0] && $ex_coupon[1]){
									$ex_coupon['name'] = stripslashes($ex_coupon[0]);
									$ex_coupon['price'] = rm_comma($ex_coupon[2]);
									$ex_coupon['per'] = floor(rm_comma($ex_coupon[3])*10)/10;
									$ex_coupon['max'] = rm_comma($ex_coupon[4]); //쿠폰 최댓값 콤마 제거
									$ex_coupon['perprice'] = floor($sv['c_price']*$sv['c_cnt']*$ex_coupon['per']/100); //퍼센트 계산

									//per일때 최대값 비교, per이 아닌경우 원 출력
									$ex_coupon_perprice = 0;
									if($ex_coupon[1] == 'per'){
										if($ex_coupon['max'] > 0 && $ex_coupon['max'] < $ex_coupon['perprice']){
											$ex_coupon_perprice = $ex_coupon['max'];
										}else{
											$ex_coupon_perprice= $ex_coupon['perprice'];
										}
									}else{
										$ex_coupon_perprice= $ex_coupon[2];
									}

									$ex_coupon_p = ($ex_coupon[1] == 'per' ? "" . $ex_coupon['per'] ."%" : "" . number_format($ex_coupon['price']) ."원"); //per일 경우 per price일경우 price
									$ex_coupon_max = ($ex_coupon[1] == 'per' && $ex_coupon['max'] > 0 ? "<em>(최대 ". number_format($ex_coupon['max']) . "원)</em>" : null); //max

									$coupon_html .= '
										<label class="coupon" title="'. $ex_coupon['name'] .'">
											<input type="checkbox" onclick="app_order_price()" class="product_coupon_check"  style="display:none" checked="checked" name="product_coupon['. $pr['p_code'] .']"  value="'. $ex_coupon_perprice .'"/>
											<input type="hidden" name="pc_check['. $pr['p_code'] .']" value="'. md5(sha1($_SERVER['REMOTE_ADDR'].$ex_coupon_perprice)) .'">
											<span class="name"><strong>'. $ex_coupon['name'] .'</strong></span>
											<span class="discount">
												<strong>'. $ex_coupon_p .' 할인</strong>
												' . $ex_coupon_max . '
											</span>
										</label>
									';
									if( $ex_coupon_perprice > $option_tmp_sum_price) { $coupon_html = ""; }
								}// 	KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
								$ex_coupon = explode("|", $pr['p_coupon']);
								$coupon_html = '';
								if($ex_coupon[0] && $ex_coupon[1]){
									$ex_coupon['name'] = stripslashes($ex_coupon[0]);
									$ex_coupon['price'] = rm_comma($ex_coupon[2]);
									$ex_coupon['per'] = floor(rm_comma($ex_coupon[3])*10)/10;
									$ex_coupon['max'] = rm_comma($ex_coupon[4]); //쿠폰 최댓값 콤마 제거
									$ex_coupon['perprice'] = floor($sv['c_price']*$sv['c_cnt']*$ex_coupon['per']/100); //퍼센트 계산

									//per일때 최대값 비교, per이 아닌경우 원 출력
									$ex_coupon_perprice = 0;
									if($ex_coupon[1] == 'per'){
										if($ex_coupon['max'] > 0 && $ex_coupon['max'] < $ex_coupon['perprice']){
											$ex_coupon_perprice = $ex_coupon['max'];
										}else{
											$ex_coupon_perprice= $ex_coupon['perprice'];
										}
									}else{
										$ex_coupon_perprice= $ex_coupon[2];
									}

									$ex_coupon_p = ($ex_coupon[1] == 'per' ? "" . $ex_coupon['per'] ."%" : "" . number_format($ex_coupon['price']) ."원"); //per일 경우 per price일경우 price
									$ex_coupon_max = ($ex_coupon[1] == 'per' && $ex_coupon['max'] > 0 ? "<em>(최대 ". number_format($ex_coupon['max']) . "원)</em>" : null); //max

									$coupon_html .= '
										<label class="coupon" title="'. $ex_coupon['name'] .'">
											<input type="checkbox" onclick="app_order_price()" class="product_coupon_check"  style="display:none" checked="checked" name="product_coupon['. $pr['p_code'] .']"  value="'. $ex_coupon_perprice .'"/>
											<input type="hidden" name="pc_check['. $pr['p_code'] .']" value="'. md5(sha1($_SERVER['REMOTE_ADDR'].$ex_coupon_perprice)) .'">
											<span class="name"><strong>'. $ex_coupon['name'] .'</strong></span>
											<span class="discount">
												<strong>'. $ex_coupon_p .' 할인</strong>
												' . $ex_coupon_max . '
											</span>
										</label>
									';
									if( $ex_coupon_perprice > $option_tmp_sum_price) { $coupon_html = ""; }
								}



								if($pr['p_type'] == 'delivery'){

									// 배송비 추출
									$app_delivery = '무료배송';
									$app_delivery_type = '';
									switch($pr['p_shoppingPay_use']){
										case 'Y':
											$cart_delivery_price = $pr['p_shoppingPay'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치|
											if($_is_checked) $arr_product['delivery']+= $pr['p_shoppingPay'] * $sum_product_cnt;
											$app_delivery = $cart_delivery_price > 0 ? "" . number_format(1 * $cart_delivery_price) . "원":"무료배송";
											if($pr['p_shoppingPay'] > 0){ /* 추가배송비개선 - 2017-05-19::SSJ  */
													$app_delivery_type = '(개별배송)';
											}
											break;
										case 'F': $app_delivery = '무료배송'; $cart_delivery_price = 0; break;
										case 'N':
											$app_delivery = '무료배송';
											$cart_delivery_price = 0;
											if($del_chk_customer <> $crk) {
												$app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? '' . number_format($arr_customer[$crk]['app_delivery_price']) . '원' : '무료배송') ;
												if($_is_checked) $arr_product['delivery']+=$arr_customer[$crk]['app_delivery_price'];

	                                            // 무료배송이벤트 가 아닐 경우
	                                            if( $pr['p_free_delivery_event_use'] != 'Y'){
	                                                $del_chk_customer = $crk;
	                                            }

												$cart_delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD
											}
											break;
										// ----- JJC : 상품별 배송비 : 2018-08-16 -----
										case "P":
											$cart_delivery_price = ($pr['p_shoppingPayPfPrice'] == 0 || $pr['p_shoppingPayPfPrice'] >  $arr_per_product[$k]['sum'] ? $pr['p_shoppingPayPdPrice'] : 0 );  // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
											if($_is_checked) {
												$arr_product["delivery"]+= $cart_delivery_price;
											}
											$app_delivery = ($cart_delivery_price > 0 ? "" . number_format($cart_delivery_price) . "원" : "무료배송");

											break;
										// ----- JJC : 상품별 배송비 : 2018-08-16 -----

									}
								}
								else{
									$cart_delivery_price = 0;
								}





						?>
								<li class="li">

									<dl class="cart_item">
										<dt>
											<label class="c_label">
												<input type="checkbox" name="_code[]" class="cls_code" value="<?php echo $pr['p_code']; ?>" <?php echo ($_is_checked?' checked="checked"':null); ?>>
												<span class="tx"><span class="icon"></span></span>
											</label>
											<input type="hidden" name="cart_price_<?php echo $pr['p_code']; ?>" value="<?php echo $sum_price; ?>">
											<input type="hidden" name="cart_delivery_<?php echo $pr['p_code']; ?>" value="<?php echo $cart_delivery_price; ?>">
											<input type="hidden" name="cart_point_<?php echo $pr['p_code']; ?>" value="<?php echo floor($sum_point); ?>">
											<a href="<?php echo $pro_url; ?>" class="thumb" target="_blank"><img src="<?php echo $thumb_img; ?>" alt="<?php echo addslashes($pro_name); ?>"></a>
										</dt>
										<dd>
											<a href="<?php echo $pro_url; ?>" class="item_name"><?php echo $pro_name; ?></a>

											<?php // 상품 쿠폰 ?>
											<?php echo $coupon_html; ?>

                                            <?php // 옵션 노출 ?>
											<ul class="option_list js_pinfo<?php echo (count($arr_options)<2 ? ' if_only':null); ?><?php echo (in_array($pr['p_code'], $arr_open_pcode) ? ' if_open':null); ?>" data-pcode="<?php echo $pr['p_code']; ?>">
												<?php echo implode('', $arr_options); ?>
											</ul><!-- end cart_option -->

											<div class="option_total">
												<ul>
													<li>
														<div class="opt">상품</div>
														<div class="value">
															<?php echo number_format( 1 * $sum_price); ?>원
															<?php if($groupSetUse === true && $groupSetInfo['mgs_sale_price_per'] > 0 ) {  // {{{회원등급혜택}}}?>
																<strong>(회원할인 <?php echo odt_number_format( 1 * $groupSetInfo['mgs_sale_price_per'],1) ?>%)</strong>
															<?php } // {{{회원등급혜택}}}?>
														</div>
													</li>
                                                    <?php if( $pr['p_type'] == 'delivery'){ ?>
                                                        <li>
                                                            <div class="opt">배송비<?php echo $app_delivery_type; ?></div>
                                                            <div class="value"><?php echo $app_delivery; ?></span></div>
                                                        </li>
                                                    <?php } ?>
												</ul>
                                                <ul>
                                                    <li>
                                                        <div class="opt">적립금</div>
                                                        <div class="value">
                                                            <?php echo number_format( 1 * floor($sum_point)) ?>원
                                                            <?php if($groupSetUse === true && $groupSetInfo['mgs_give_point_per'] > 0) { // {{{회원등급혜택}}} ?>
                                                                <strong>(추가적립 <?php echo odt_number_format( 1 * $groupSetInfo['mgs_give_point_per'],1) ?>%)</strong>
                                                            <?php } // {{{회원등급혜택}}} ?>
                                                        </div>
                                                    </li>
                                                </ul>
											</div><!-- end option_total -->
										</dd>
									</dl>

								</li>
						<?php
							}
							// 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
							foreach($arr_product as $ak=>$av){
								$arr_product_sum[$ak] += $av;
							}
						?>
					</ul>
				</div><!-- end cart_list -->
			<?php } ?>
		</div><!-- end left_box -->

		<div class="area_ctrl">

			<div class="cart_ctrl type_hidden">
				<dl>
					<dt>
						<a href="#none" onclick="cart_all_select(); return false;" class="btn btn_sel"><strong>전체선택</strong></a>
						<a href="#none" onclick="cart_select_delete(); return false;" class="btn btn_del"><strong>선택삭제</strong></a>
					</dt>
					<dd><a href="#none" onclick="cart_remove_all(); return false;" class="btn btn_clear"><strong>장바구니 비우기</strong></a></dd>
				</dl>
			</div><!-- end cart_ctrl -->

			<div class="cart_sum">
				<div class="total_number"><em>전체상품</em><strong><?php echo number_format( 1 * $arr_product_sum['cnt']); ?>개</strong></div>
				<dl class="this_point">
                    <dt>총 적립금</dt>
                    <dd><strong id="cart_point"><?php echo number_format( 1 * $arr_product_sum['point']); ?></strong><em>원</em></dd>
				</dl>
				<dl>
					<dt>상품금액</dt>
					<dd><strong id="cart_price"><?php echo number_format( 1 * $arr_product_sum['sum']); ?></strong><em>원</em></dd>
				</dl>


				<dl style="<?php echo ($order_type == 'delivery' || $order_type == 'both') ? null:'display:none;'  ?>">
					<dt>배송비</dt>
					<dd>+ <strong id="cart_delivery"><?php echo number_format( 1 * $arr_product_sum['delivery']); ?></strong><em>원</em></dd>
				</dl>

				 <dl class="this_discount">
					<dt>할인</dt>
					<dd>- <strong class="ID_sale_point">0</strong>원</span><div class="ic_price ic_minus"></div></dd>
				</dl>
				<dl class="this_last">
					<dt>결제 예정금액</dt>
					<dd><strong id="cart_total"><?php echo number_format( 1 * $arr_product_sum['sum'] + $arr_product_sum['delivery']); ?></strong><em>원</em></dd>
				</dl>
				<div class="c_btnbox type_full">
					<?php if(is_login() ){ // 로그인 후 ?>
						<a href="#none" onclick="cart_submit();return false;" class="c_btn h50 color ">구매하기</a></li>
					<?php }else { // 로그인 전 ?>
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
						<?php if (  $none_member_buy === true ) { ?>
							<a href="#none" onclick="login_alert('<?php echo $_PVSC ; ?>');return false;" class="c_btn h50 color">구매하기</a>
						<?php } else { ?>
							<a href="#none" onclick="cart_submit();return false;" class="c_btn h50 color ">구매하기</a>
						<?php } ?>
						<?php // === 비회원 구매 설정 통합 kms 2019-06-24 ==== ?>
					<?php } ?>
				</div><!-- end c_btnbox -->
			</div><!-- end cart_sum -->


	        <?php // LDD NPAY { ?>
	            <?php
	            $NPayTrigger = 'N';
	            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
	            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'test' && $nt == 'test' && sizeof($arr_cart) > 0) $NPayTrigger = 'Y';
	            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'test' && $nt != 'test') $NPayTrigger = 'N'; // 버튼+주문연동 작업
	            if($siteInfo['npay_use'] == 'Y' && $siteInfo['npay_mode'] == 'real' && $siteInfo['npay_lisense'] != '' && $siteInfo['npay_sync_mode'] == 'real') $NPayTrigger = 'Y'; // 버튼+주문연동 작업
	            if(sizeof($arr_cart) <= 0) $NPayTrigger = 'N';
	            if($NPayTrigger == 'Y') {
	            ?>
	            <div class="cart_npay">
	            	<iframe src="/program/shop.cart.npay.php<?php echo $nt ==  'test' ? '?nt=test':''?>" id="js_npay_view" style="border:none;"></iframe>
	            </div>
	            <?php } ?>
	        <?php // } LDD NPAY ?>

		</div><!-- end right_box -->
	</form><!-- end cart_wrap -->

<?php }else{ // 장바구니 없을때 ?>
	<div class="c_none">
		<div class="gtxt">장바구니에 담긴 상품이 없습니다.</div>
	</div>
<?php } ?>