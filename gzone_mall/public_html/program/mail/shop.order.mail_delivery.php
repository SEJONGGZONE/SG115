<?PHP
unset($mailing_app_content,$mailing_order_product_content); // 기본변수 초기화 <><>
$arrMailItem = array();

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// ------------------- 주문자/기본정보  -------------------
$__first = mb_substr($or[o_oname],0,1,'utf8'); // 보안처리
$__last = mb_substr($or[o_oname],2,mb_strlen($or[o_oname], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['orderName'] = $__first."*".(mb_strlen($or[o_oname],'utf8') > 2 ? $__last : null); // 주문자명 가운데 * 처리
$arrMailItem['orderLink'] = $or['o_memtype'] == 'Y' ? $app_HTTP_URL."/?pn=mypage.order.view&ordernum=".$or['o_ordernum']:$app_HTTP_URL."/?pn=member.login.form"; // 주문버호 링크
$arrMailItem['orderDate'] = date("Y.m.d H:i",strtotime($or['o_rdate'])); // 주문일자
$arrMailItem['orderSendcompany'] = $_SendMode == 'order' ? $or['o_sendcompany'] : ''; // 택배사
$arrMailItem['orderSendnum'] = $_SendMode == 'order' ? $or['o_sendnum'] : ''; // 송장번호
$arrMailItem['orderSenddate'] = $_SendMode == 'order' && rm_str($or['o_senddate']) > 0 ? date("Y.m.d",strtotime($or['o_senddate'])) : date('Y.m.d',strtotime($or['o_completedate'])); // 발송일자
$arrMailItem['orderDeliveryUrl'] = $arr_delivery_company[$or[o_sendcompany]]. rm_str($or[o_sendnum]); // 배송조회 URL
// ------------------- 주문자/기본정보  끝 -------------------


// ------------------- 주문  결제정보  -------------------
$arrMailItem['orderPriceTotal'] = number_format($or[o_price_total]); // 총 주문금액
$arrMailItem['orderPriceDelivery'] = number_format($or[o_price_delivery]); // 총 배송금액
$arrMailItem['orderSpriceTotal'] = number_format($or[o_price_usepoint]+$or[o_price_coupon_individual]+$or[o_price_coupon_product]+$or[o_promotion_price]); // 총 할인금액
$arrMailItem['orderPriceSupplypoint'] = number_format($or[o_price_supplypoint]); // 총 적립금
$arrMailItem['orderPaymethod'] = $arr_payment_type[$or[o_paymethod]]; // 결제수단

// LCY : 2021-07-04 : 신용카드 간편결제 추가
if( $or['o_easypay_paymethod_type'] != ''){
    $arrMailItem['orderPaymethod'] .= '('.$arr_available_easypay_pg_list[$or['o_easypay_paymethod_type']].')';
}

$arrMailItem['orderBank'] = $or[o_bank]; // 결제계좌
$arrMailItem['orderPriceReal'] = number_format($or[o_price_real]); // 최총 결제금액

// ------------------- 주문  결제정보 끝 -------------------

// ------------------- 배송지 정보  -------------------
$__first = mb_substr($or[o_rname],0,1,'utf8'); // 보안처리
$__last = mb_substr($or[o_rname],2,mb_strlen($or[o_rname], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['receiveName'] = $__first."*".(mb_strlen($or[o_rname],'utf8') > 2 ? $__last : null); // 수령인 가운데 * 처리
$__arrRhp = explode("-",tel_format($or['o_rhp']));
$arrMailItem['receiveHp'] =  count($__arrRhp) > 2 ? $__arrRhp[0].'-'.$__arrRhp[1].'-****' : null;   // 수령인 연락처
$arrMailItem['receiveZonecode'] =  $or[o_rzonecode]; // 새우편주소
$arrMailItem['receiveAddrDoro'] =  $or[o_raddr_doro]; // 도로명 주소
$arrMailItem['receiveAddr2'] =  LastCut($or[o_raddr2],2); // 나머지 주소
$arrMailItem['o_content'] =  stripslashes($or['o_content']); // 배송시 문구
// ------------------- 배송지 정보  끝 -------------------


// ------------------- 주문상품정보 :: $_SendMode == 'order_product' -------------------
if( $_SendMode == 'order_product'){
	$arrOrderPrdocutOption    = array();
	if( count($opr) > 0) {
		foreach( $opr as $opk=>$opv ){
			$arrOptionInfo = array();
			if( $opv['op_pouid'] > 0){

				// KAY :: 2022-12-07 :: 옵션 구분 수정
				$arr_option_name = array($opv['op_option1'] ,$opv['op_option2'],$opv['op_option3'] );
				$arr_option_name = array_filter($arr_option_name);
				$arrOptionInfo['optionName'] = ($opv['op_is_addoption'] == 'Y' ? '추가 : ':'선택 : ' ).implode(" / ",$arr_option_name)." ".number_format($opv['op_price'])."원 x ".$opv['op_cnt'];
			}

			$arrOptionInfo['totalCnt'] = $opv['op_cnt']; // 총 개수
			$arrOptionInfo['totalPrice'] =  $opv['op_price'] * $opv['op_cnt']; // 옵션 총금액

			$arrOptionInfo['sendcompany'] =$opv['op_sendcompany']; // 택배사
			$arrOptionInfo['sendnum'] =$opv['op_sendnum']; // 송장번호
			$arrOptionInfo['senddate'] =  $opv['op_senddate'] > 0 ?  date("Y.m.d",strtotime($opv['op_senddate'])) : ''; // 발송일자
			$arrOptionInfo['deliveryUrl'] = $arr_delivery_company[$opv[op_sendcompany]]. rm_str($opv[op_sendnum]); // 배송조회 URL


			$mailing_order_product_content .= "
				<tr>
					<td style='padding:10px 0; border-bottom:1px dashed #EBEBF2'>
						<a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='width:80px; overflow:hidden; box-sizing:border-box; display:block; margin-right:10px; font-size:0' target='_blank'>
							<img src='".get_img_src($opv['p_img_list_square'], IMG_DIR_PRODUCT)."' alt='".$opv['p_name']."' style='width:100%; border:0 !important'/>
						</a>
					</td>
					<td style='padding:10px 0; border-bottom:1px dashed #EBEBF2;'>
						<table style='width:100%;border-spacing:0; font-size:inherit; color:inherit'>
							<tbody>
								<tr>
								<td><a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='font-weight:600; color:#000 ; target='_blank'>".$opv['p_name']."</a></td>
							</tr>
			";


			// ------------------- 주문상품옵션명 출력/없을경우 안나옴   -------------------
			if( $arrOptionInfo['optionName'] != '') {
				$mailing_order_product_content .= "
								<tr>
									<td>
									".implode("<br/>",$arrOptionInfo['optionName'])."
									</td>
								</tr>
				";
			}
			// ------------------- 주문상품옵션명 출력/없을경우 안나옴 끝  -------------------

			$mailing_order_product_content .= "
								<tr>
									<td style='font-weight:700; color:#000; padding-top:10px;'>
										".number_format($arrOptionInfo['totalPrice'])."원 (총 ".number_format($arrOptionInfo['totalCnt'])."개)
										<div style='margin-top:10px; background:#F1F1F6; font-weight:400; padding:5px'>
											".$arrOptionInfo['sendcompany']." <a href='".$arrOptionInfo['deliveryUrl']."' style='' target='_blank'>송장번호 ".$arrOptionInfo['sendnum']."</a>
											(발송일 : ".$arrOptionInfo['senddate'].")
										</div>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
			";
		}
	}
}
// ------------------- 주문상품정보 :: $_SendMode == 'order_product' 끝 -------------------


// ------------------- 주문상품정보 :: $_SendMode == 'order'  -------------------
if( $_SendMode == 'order'){
	$arrOrderPrdocutOption = $group_opr = $chk_group_opr  = array();
	if( count($opr) > 0) {
		foreach( $opr as $opk=>$opv ){

			if($chk_group_opr[$opv['p_code']] !== true){  $group_opr[$opk] = $opr[$opk]; $chk_group_opr[$opv['p_code']] = true;  }
			$arrOrderPrdocutOption[$opv['p_code']][$opv['op_uid']] = $opv;
		}

		foreach( $group_opr as $key=>$opv ){

			// ------------------- 주문상품옵션 정보/합산 -------------------
			$arrOptionInfo = array();
			foreach($arrOrderPrdocutOption[$opv['p_code']] as  $sk => $sv){
				if( $sv['op_pouid'] > 0){


					// KAY :: 2022-12-07 :: 옵션 구분 수정
					$arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );

					// LCY : 2023-01-05 : 티켓 -- 달력옵션 적용
					if($sv['op_dateoption_use'] == 'Y' && rm_str($sv['op_dateoption_date']) > 0  ){
						$arr_option_name =  array_merge(array($sv['op_dateoption_date']),$arr_option_name );
					}
					// LCY : 2023-01-05 : 티켓 -- 달력옵션 적용

					$arr_option_name = array_filter($arr_option_name);

					$arrOptionInfo['optionName'][] = ($sv['op_is_addoption'] == 'Y' ? '추가 : ':'필수 : ' ).implode(" / ",$arr_option_name)." ".number_format($sv['op_price'])."원 x ".$sv['op_cnt'];
				}

				$arrOptionInfo['totalCnt'] += $sv['op_cnt'];
				$arrOptionInfo['totalPrice'] +=  $sv['op_price'] * $sv['op_cnt'];
			}
			// ------------------- 주문상품옵션 정보/합산 끝  -------------------


			$mailing_order_product_content .= "
				<tr>
					<td style='padding:10px 0; border-bottom:1px dashed #EBEBF2'>
						<a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='width:80px; overflow:hidden; box-sizing:border-box; display:block; margin-right:10px; font-size:0' target='_blank'>
							<img src='".get_img_src($opv['p_img_list_square'], IMG_DIR_PRODUCT)."' alt='".$opv['p_name']."' style='width:100%; border:0 !important'/>
						</a>
					</td>

					<td style='padding:10px 0; border-bottom:1px dashed #EBEBF2;'>
						<table style='width:100%;border-spacing:0; font-size:inherit; color:inherit'>
							<tbody>
								<tr>
									<td><a href='".$app_HTTP_URL."/?pn=product.view&pcode=".$opv['p_code']."' style='font-weight:600; color:#000 ; target='_blank'>".$opv['p_name']."</a></td>
								</tr>
			";


			// ------------------- 주문상품옵션명 출력/없을경우 안나옴   -------------------
			if( count($arrOptionInfo['optionName']) > 0) {
				$mailing_order_product_content .= "
								<tr>
									<td>
									".implode("<br/>",$arrOptionInfo['optionName'])."
									</td>
								</tr>
				";
			}
			// ------------------- 주문상품옵션명 출력/없을경우 안나옴 끝  -------------------

			$mailing_order_product_content .= "
								<tr>
									<td style='font-weight:700; color:#000; padding-top:10px;'>
                                        ".number_format($arrOptionInfo['totalPrice'])."원 (총 ".number_format($arrOptionInfo['totalCnt'])."개)
                                        <div style='margin-top:10px; background:#F1F1F6; font-weight:400; padding:5px'>
                                            ".$arrMailItem['orderSendcompany']." <a href='".$arrMailItem['orderDeliveryUrl']."' style='' target='_blank'>송장번호 ".$arrMailItem['orderSendnum']."</a>
                                                (발송일 : ".$arrMailItem['orderSenddate'].")
                                        </div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			";
		}
	}
}
// ------------------- 주문상품정보 :: $_SendMode == 'order' 끝 -------------------



$mailing_app_content = "


		<table style='width:100%; border-spacing:0; font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>상품이 발송되었습니다.</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%; border-spacing:0; margin-top:10px; font-size:inherit; color:inherit'>
							<colgroup>
								<col width='80'/><col width='*'/>
							</colgroup>
							<tbody>
								<tr>
									<td style='padding-top:5px'>주문자명</td>
									<td style='padding-top:5px'>".$arrMailItem['orderName']."</td>
								</tr>
								<tr>
									<td style='padding-top:5px'>주문번호</td>
									<td style='padding-top:5px'><a href='".$arrMailItem['orderLink']."' style='color:#000; font-weight:600;text-decoration:none;' target='_blank'>".$or['o_ordernum']."</a></td>
								</tr>
								<tr>
									<td style='padding-top:5px'>주문일자</td>
									<td style='padding-top:5px'>".$arrMailItem['orderDate']."</td>
								</tr>
							</tbody>
						</table>


						<table style='width:100%; border-spacing:0; margin-top:30px;font-size:inherit; color:inherit'>
							<colgroup>
								<col width='80'/><col width='*'/>
							</colgroup>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>주문상품</th>
								</tr>
							</thead>
							<tbody>
							".$mailing_order_product_content."
							</tbody>
						</table>


						<table style='width:100%;  border-spacing:0;  margin-top:30px;font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>
										결제정보 : ".$arrMailItem['orderPaymethod']."
										<div style='font-size:12px; margin-top:5px; font-weight:400'>입금계좌 : ".$arrMailItem['orderBank']."</.div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='text-align:right; padding-top:10px'>
										<table style='border-spacing:0; float:right;font-size:inherit; color:inherit'>
											<tbody>
												<tr>
													<td style='padding:5px 20px 0 0'>총 주문금액</td>
													<td style='padding:5px 0 0 0; font-weight:700; color:#000'>".$arrMailItem['orderPriceTotal']."원</td>
												</tr>
												<tr>
													<td style='padding:5px 20px 0 0;'>배송비</td>
													<td style='padding:5px 0 0 0; font-weight:700; color:#000'>+ ".$arrMailItem['orderPriceDelivery']."원</td>
												</tr>
												<tr>
													<td style='padding:5px 20px 0 0; color:#0351FF'>할인금액</td>
													<td style='padding:5px 0 0 0; font-weight:700; color:#0351FF'>- ".$arrMailItem['orderSpriceTotal']."원</td>
												</tr>
												<tr>
													<td style='padding:5px 20px 0 0; font-size:17px; font-weight:700; color:#000'>최종결제 금액</td>
													<td style='padding:5px 0 0 0; font-size:17px; font-weight:700; color:#FF1434'>".$arrMailItem['orderPriceReal']."원</td>
												</tr>
												<tr>
													<td colspan='2' style='text-align:right; color:#5dac00'>(적립금 ".$arrMailItem['orderPriceSupplypoint']."원)</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>

						<table style='width:100%; border-spacing:0; margin-top:30px; font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>배송정보</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding-top:10px;'>
										<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
											<colgroup>
												<col width='80'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style='padding-top:5px;'>수령인</td>
													<td style='padding-top:5px;'>".$arrMailItem['receiveName']."</td>
												</tr>
												<tr>
													<td style='padding-top:5px;'>연락처</td>
													<td style='padding-top:5px;'>".$arrMailItem['receiveHp']."</td>
												</tr>
												<tr>
													<td style='padding-top:5px;'>주소</td>
													<td style='padding-top:5px;'>
														(".$arrMailItem['receiveZonecode'].") ".$arrMailItem['receiveAddrDoro']." ".$arrMailItem['receiveAddr2']."
													</td>
												</tr>
												".($arrMailItem['o_content'] != '' ? "
												<tr>
													<td style='padding-top:5px;'>배송메모</td>
													<td style='padding-top:5px;'>".$arrMailItem['o_content']."</td>
												</tr>
												":null)."

											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

";

