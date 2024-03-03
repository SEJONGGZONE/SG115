<?php
	include_once($SkinData['skin_root'].'/shop.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_cart">
    <div class="layout_fix">

        <div class="cart_wrap">

            <div class="left_box">
                <div class="c_order_box this_first">
                    <div class="c_group_tit order"><span class="tit">주문 상품 (<?php echo number_format( 1 * count($sres)); ?>)</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"><span class="ic"></span></a></div>

                    <div class="cart_list conts_box">
                        <?php /*
                        <div class="table_top">
                            <div class="tit_box">
                                <span class="txt">업체배송</span>
                                <span class="txt shop_tit">(주)쇼핑몰명</span>
                            </div>
                        </div>
                        */ ?>

                        <ul class="ul">
                            <?php
                            unset($op_price_delivery,$op_price_sum_total, $_num);
                            foreach( $sres as $k=>$v ){
                                // No. 설정
                                $_num++;

								// JJC : 2023-01-13 : 추가옵션 부분취소 - 위치조정 : 지정된 기본옵션 하위에 추가옵션 위치함
								$res = _MQ_assoc("
									select *, IF(op_is_addoption = 'Y' , op_addoption_parent , op_pouid) AS order_uid
									from smart_order_product as op
									inner join smart_product as p on ( p.p_code=op.op_pcode )
									where op_pcode = '".$v['op_pcode']."' AND op_oordernum='{$ordernum}'
									order by order_uid ASC , op_uid ASC
								");
                                //ViewArr($res);
                                unset($op_option_print,$option_name,$option_cnt,$op_total_price, $add_delivery_print,$op_total_point,$op_status_class,$op_delivery_price, $op_add_delivery_price);
                                foreach($res as $sk=>$sv) {

                                    /*------- 상품명 (결제시 상품명으로 사용됨) ------*/
                                    if(!$app_product_name)  {
                                        $app_product_name_tmp = $sv['op_pname'];
                                        $app_product_name = $sv['op_pname'];
                                    } else {
                                        $app_product_cnt++;
                                        $app_product_name = $app_product_name_tmp ." 외 ".$app_product_cnt."건";
                                    }
                                    /*------- // 상품명 (결제시 상품명으로 사용됨) ------*/

                                    # 상품 가격 및, 배송비 정보
                                    $op_total_price += $sv['op_price'] * $sv['op_cnt'];
                                    # 상품의 갯수
                                    $p_total_cnt += $sv['op_cnt'];

                                    # 적립금
                                    $op_total_point += $sv['op_point'];

                                    // 2017-10-13 ::: 배송비 오류 수정 ::: JJC
                                    $op_delivery_price += $sv['op_delivery_price'];
                                    $op_add_delivery_price += $sv['op_add_delivery_price'];

									// KAY :: 2022-12-07 :: 옵션 구분 수정
									$arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );
									$arr_option_name = array_filter($arr_option_name);

                                    # 옵션처리
                                    $option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . implode(" / ",$arr_option_name));
                                    $option_cnt			= $sv['op_cnt'];
                                    $op_option_print .= '
                                        <li class="op_li">
                                            <div class="opt_tit">
                                                '. $option_name .'
                                            </div>
                                            <div class="opt_cont">
                                                <div class="price">'. number_format( 1 * $sv['op_price']) .'원</div>
                                            </div>
                                        </li>
                                    ';

                                }
                                // 옵션처리끝

                                // {{{LCY무료배송이벤트}}}
                                if( $v['op_free_delivery_event_use'] == 'Y' ){
                                    $op_delivery_price = 0;
                                }
                                // {{{LCY무료배송이벤트}}}

                                # 배송처리
                                $delivery_price = $op_delivery_price;; // |개별배송패치| - $sum_product_cnt : 상품갯수를 곱해준다. -- 계산이 되어서 들어감
                                $deliver_price_print = '무료배송';
                                if($delivery_price > 0 ){
                                    $deliver_price_print = ($delivery_price > 0 ? '<strong>'.number_format( 1 * $delivery_price).'</strong>원':'' );
                                }
                                if($v['op_delivery_type'] == '무료'){
                                    $deliver_price_print = '무료배송';
                                }else if($v['op_delivery_type'] == '개별'){
                                    $deliver_price_print .= '<br>(개별배송)';
                                }

                                // {{{LCY무료배송이벤트}}}
                                if( $v['op_free_delivery_event_use'] == 'Y' ){
                                    $deliver_price_print = "무료배송(이벤트)";
                                }
                                // {{{LCY무료배송이벤트}}}


                                /* 추가배송비개선 - 2017-05-19::SSJ  */
                                $add_delivery_price = $op_add_delivery_price;;
                                $deliver_price_print .= ($add_delivery_price > 0 ? '<div style="margin-top: 10px;"><strong>+ '.number_format( 1 * $add_delivery_price).'</strong>원<br>(추가배송비)</div>':'' );
                                $delivery_price += $add_delivery_price;


                                # 상품 쿠폰 처리
                                $product_coupon_normal_use = ''; // 변수 초기화 : 필수
                                $product_coupon_normal_check = _MQ(" select * from smart_order_coupon_log where cl_oordernum = '".$v['o_ordernum']."' and cl_pcode = '".$v['p_code']."' and cl_type = 'product' ");
                                if( count($product_coupon_normal_check) > 0){
                                    $product_coupon_normal_use = "Y"; // 상품 쿠폰 있는지 처리
                                    $product_coupon_normal_title = $product_coupon_normal_check['cl_title'];
                                    $product_coupon_normal_price = number_format( 1 * $product_coupon_normal_check['cl_price']);
                                    $product_coupon_normal_per = number_format( 1 * $product_coupon_normal_check['cl_price']/$op_total_price*100);
                                }

                                # 상품의 url
                                $p_name	= strip_tags($v['p_name']);	// 상품명
                                $p_url = "/?pn=product.view&pcode=".$v['p_code']; // 상품의 주소
                                # 상품의 썸네일
                                $p_thumb	= get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지
                                if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';
                                # 총 배송비 합계
                                $op_price_delivery += $delivery_price;
                                # 총 합계
                                $op_price_sum_total +=  $op_total_price;

                                # 상품 적립금 + 상품 적립쿠폰 금액
                                $op_product_total_point = $v['o_price_supplypoint'];
                                ?>
                                <li class="li">

                                    <dl class="cart_item">
                                        <dt>
                                            <div class="thumb">
                                                <a href="<?php echo $p_url; ?>" target="_blank"><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes($p_name); ?>"></a>
                                            </div>
                                        </dt>
                                        <dd>
                                            <div class="info">
                                                <a href="<?php echo $p_url; ?>" class="item_name" target="_blank"><?php echo $p_name; ?></a>
                                            </div>

                                            <?php if($product_coupon_normal_use == 'Y') { // 상품 쿠폰을 사용한다면?>
                                                <div class="cart_coupon" title="쿠폰명">
                                                    <div class="coupon_box">
                                                        <span class="coupon_tit">
                                                            <span class="tx">쿠폰</span>
                                                        </span>
                                                        <span class="coupon_info">
                                                            <span class="name"><?php echo $product_coupon_normal_title; ?> </span>
                                                            <span class="benefit"><strong><?php echo $product_coupon_normal_price; ?>원</strong>할인</span>
                                                        </span>
                                                    </div>
                                                    <span class="shape ic_top"></span><span class="shape ic_bottom"></span>
                                                </div>
                                            <?php } ?>

                                            <div class="cart_option if_only">
                                                <ul>
                                                    <?php echo $op_option_print; ?>
                                                </ul>
                                            </div>
                                        </dd>
                                    </dl>
                                </li>
                            <?php } ?>
                        </ul>

                    </div>
                </div><!-- end c_order_box -->



                <div class="c_order_box">
                    <div class="c_group_tit"><span class="tit">주문자 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"><span class="ic"></span></a></div>

                    <div class="c_form conts_box">
                        <div class="form_in">
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">주문자 이름</span></dt>
                                <dd><?php echo $row['o_oname']; ?></dd>
                            </dl>
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">주문자 휴대폰</span></dt>
                                <dd><?php echo $row['o_ohp']; ?></dd>
                            </dl>
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">주문자 이메일</span></dt>
                                <dd><?php echo $row['o_oemail']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div><!-- end c_order_box -->



                <div class="c_order_box">
                    <div class="c_group_tit"><span class="tit">배송지 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"><span class="ic"></span></a></div>

                    <div class="c_form conts_box">
                        <div class="form_in">
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">받는분 이름</span></dt>
                                <dd><?php echo $row['o_rname']; ?></dd>
                            </dl>
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">받는분 휴대폰</span></dt>
                                <dd><?php echo $row['o_rhp']; ?></dd>
                            </dl>
                            <?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">받는분 주소</span></dt>
                                <dd>
                                    <?php // 우편번호 ?>
                                    <?php echo $row['o_rzonecode']; ?><br>
                                    <?php // 주소 ?>
                                    <?php echo $row['o_raddr_doro']; ?>
                                    <?php // 상세 주소 ?>
                                    <?php echo $row['o_raddr2']; ?>
                                </dd>
                            </dl>
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">지번주소</span></dt>
                                <dd>
                                    <?php echo (rm_str($row['o_rpost'])>0?'('.$row['o_rpost'].') ' : ''); ?>
                                    <?php // 주소 ?>
                                    <?php echo $row['o_raddr1']; ?>
                                    <?php // 상세 주소 ?>
                                    <?php echo $row['o_raddr2']; ?>
                                </dd>
                            </dl>
                            <?php // ----- JJC : 지번주소 패치 : 2020-04-27 : 구 우편번호 제공되지 않음  -----?>
                            <dl class="this_value">
                                <dt class=""><span class="tit ">배송 메세지</span></dt>
                                <dd><?php echo ($row['o_content']?nl2br($row['o_content']):''); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div><!-- end c_order_box -->



                <div class="c_order_box">
                    <div class="c_group_tit"><span class="tit">결제 정보</span><a href="#none" class="btn_ctrl js_box_ctl" title="열고닫기"><span class="ic"></span></a></div>

                    <div class="c_form conts_box">
                        <div class="form_in">
                            <dl class="this_value">
                                <dt class="ess"><span class="tit ">결제수단</span></dt>
                                <dd>
                                    <?php echo $arr_payment_type[$row['o_paymethod']]; ?>
                                    <?php
                                    $arr_occontent = array();
                                    $ex = explode("§§" , $row['oc_content']);
                                    foreach( $ex as $sk=>$sv ){
                                        $ex2 = explode("||" , $sv);
                                        $arr_occontent[$ex2[0]] = $ex2[1];
                                    }

                                    //- 카드 영수증 출력 ---
                                    if($row['oc_tid']) echo link_credit_receipt($row['o_ordernum'],'[영수증출력]');
                                    ?>
                                </dd>
                            </dl>
                            <?php if($row['o_paymethod'] == 'online') { ?>
                                <dl class="this_value">
                                    <dt class="ess"><span class="tit ">입금은행</span></dt>
                                    <dd><?php echo $row['o_bank']; ?></dd>
                                </dl>
                                <dl class="this_value">
                                    <dt class="ess"><span class="tit">입금자명</span></dt>
                                    <dd><?php echo $row['o_deposit']; ?> <?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?></dd>
                                </dl>
                            <?php } ?>
                        </div>
                    </div>
                </div><!-- end c_order_box -->

            </div><!-- end left_box -->

            <div class="right_box">

                <div class="total_box">
                    <div class="c_total_price">
                        <dl class="point">
                            <dt>포인트 적립</dt>
                            <dd><strong><?php echo number_format( 1 * $op_product_total_point); ?></strong>원</dd>
                        </dl>
                        <dl>
                            <dt>총 상품금액</dt>
                            <dd><strong><?php echo number_format( 1 * $op_price_sum_total); ?></strong>원</dd>
                        </dl>
                        <dl>
                            <dt>총 배송비</dt>
                            <dd>+ <strong><?php echo number_format( 1 * $op_price_delivery); ?></strong>원</dd>
                        </dl>
                        <dl class="this_discount">
                            <dt>총 할인금액</dt>
                            <dd>- <strong><?php echo number_format( 1 * $row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real']); ?></strong>원</dd>
                        </dl>
                        <dl class="total_num">
                            <dt>총 주문금액</dt>
                            <dd><strong><?php echo number_format( 1 * $row['o_price_real']); ?></strong>원</dd>
                        </dl>
                    </div>
                </div><!-- end total_box -->

                <div class="c_btnbox">
                    <a href="#none" onclick="<?php echo $submit_onclick; ?>" class="c_btn h60 color">결제하기</a>
                    <a href="#none" onclick="if(confirm('작성중인 주문정보가 있습니다.\n이전페이지로 이동하시겠습니까?')){location.href=('/?pn=shop.order.form');}return false;" class="c_btn h35 light line">이전 단계</a>
                </div><!-- end c_btnbox -->

            </div><!-- end right_box -->



        <?php
            // 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
            //$ordernum = $ordernum; // --> 주문번호
            $order_row = $row; // --> 주문배열정보

            include(OD_PROGRAM_ROOT."/shop.order.result.vat_calc.php");
            // 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC

            //{{{페이코}}}
            if($row['o_paymethod'] == 'payco'){
                require_once(OD_PROGRAM_ROOT."/shop.order.result.payco.php");
                $submit_onclick = 'payco_open();';
            }else{
                // PG사 결제 인증요청 페이지
                switch($siteInfo[s_pg_type]) {
					case "inicis" :
						# -- 모듈별 처리

						// LCY : 2022-12-28 : 이니시스TX모듈 교체 -- 결제수단 추가 {
						//require_once(OD_PROGRAM_ROOT."/shop.order.result_inicis_m.php");
						require_once(OD_PROGRAM_ROOT."/shop.order.result_inicis_api_m.php");
						// LCY : 2022-12-28 : 이니시스TX모듈 교체 -- 결제수단 추가 }

						$submit_onclick = "call_pay_form();";
						break;
                    case "lgpay" :
                        require_once(OD_PROGRAM_ROOT."/shop.order.result_lgpay_m.php");
                        $submit_onclick = "launchCrossPlatform();";

                        break;
                    case "kcp" :
                        require_once(OD_PROGRAM_ROOT."/shop.order.result_kcp_m.php");
                        $submit_onclick = "kcp_AJAX();";
                        break;
                    case "billgate" :
                        require_once(OD_PROGRAM_ROOT."/shop.order.result_billgate_m.php");
                        $submit_onclick = "checkSubmit();";
                        break;
                    case "daupay" :
                        require_once(OD_PROGRAM_ROOT."/shop.order.result_daupay.php"); // 웹뷰방식은 계좌이체 미지원으로 기존방식 그대로 사용 PC와 공통
                        $submit_onclick = "fnSubmit();";
                        break;
                }
            }
        ?>

        </div><!-- end cart_wrap -->
    </div>

</div><!-- end c_section -->


<script>
// 항목별로 열고/닫기 버튼
$(document).on('click', '.js_box_ctl', function(){
	$box = $(this).closest('.c_order_box');
	var is_open = $box.hasClass('if_closed');
	if(is_open) $box.removeClass('if_closed');
	else $box.addClass('if_closed');
});
</script>