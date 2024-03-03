<div class="layout_fix">

    <div class="cart_wrap">

        <div class="area_item">
            <?php // 주문상품 ------------------------------------------------------ ?>
            <div class="c_group_tit">
                <span class="tit">주문상품 (<?php echo number_format( 1 * count($sres)); ?>개)</span>
            </div>
            <div class="cart_list">
                <ul class="ul">
                    <?php
                    $NpayDcPrice = 0; // LDD: 2018-07-21 네이버 페이 할인 (N포인트+N적립금)
                    unset($op_price_delivery,$op_price_sum_total, $_num);
                    foreach( $sres as $k=>$v ){
                        // No. 설정
                        $_num++;

                        // JJC : 2023-01-13 : 추가옵션 부분취소 - 위치조정 : 지정된 기본옵션 하위에 추가옵션 위치함
                        $res = _MQ_assoc("
							select *, IF(op_is_addoption = 'Y' , op_addoption_parent , op_pouid) AS order_uid
							from smart_order_product as op
							left join smart_product as p on ( p.p_code=op.op_pcode )
							where op_pcode = '".$v['op_pcode']."' AND op_oordernum='{$ordernum}'
							order by order_uid ASC , op_uid ASC
						");
                        unset($op_option_print,$option_name,$option_cnt,$op_total_price, $add_delivery_print,$op_total_point,$op_status_class,$op_delivery_price, $op_add_delivery_price);
                        $op_status = array(); // 주문상품별 주문/배송 진행상태 체크 SSJ : 2018-02-14
                        foreach($res as $sk=>$sv) {

                            /* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */
                            $NpayDcPrice += ($sv['npay_point']+$sv['npay_point2']);
                            /* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */

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

                            # 진행상태
                            $op_status['total']++;
                            if($v['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
                                $op_status['cancel']++;
                            }else if($v['o_status'] == '결제실패'){ // 결제실패일경우
                                $op_status['fail']++;
                            }else{
                                if($v['o_paystatus'] =='Y'){ // 주문결제를 했다면,
                                    if($sv['op_sendstatus'] == '배송대기') {
                                        $op_status['pay']++;
                                    }else if($sv['op_sendstatus'] == '배송준비'){
                                        $op_status['del_ready']++;
                                    }else if($sv['op_sendstatus'] == '배송중'){
                                        $op_status['delivery']++;
                                    }else if($sv['op_sendstatus'] == '배송완료'){
                                        $op_status['complete']++;
                                    }

                                    // 티켓은 발급완료
                                    else if($sv['op_sendstatus'] == '발급완료'){
                                        $op_status['issued']++;
                                    }
                                    else{
                                        $op_status['cancel']++;
                                    }
                                }else{ // 주문결제를 하지 않았다면
                                    $op_status['ready']++;
                                }
                            }

                            # 부분 취소
                            unset($app_btn_cancel);  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
                            if($v['o_paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_settlementstatus']=='none' && $v['npay_order'] != 'Y'  ) {
                                switch($sv['op_cancel']) {
                                    case 'Y': // 취소완료
                                        $app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h25 light product_cancel_view js_onoff_event' data-target='.c_layer.type_order_cancel_view' data-add='if_open_layer'>취소완료</a>";
                                        break;
                                    case 'R': // 취소진행
                                        $app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h25 light product_cancel_view js_onoff_event' data-target='.c_layer.type_order_cancel_view' data-add='if_open_layer'>취소 진행중</a>";
                                        break;
                                    default:
                                        if($v['o_canceled']=='N' && ($sv['op_sendstatus'] == '' || $sv['op_sendstatus'] == '배송대기' || $sv['op_sendstatus'] == '배송준비')) {
                                            $app_btn_cancel = "<a href='#none' onclick=\"return false;\" data-ordernum='".$row['o_ordernum']."' data-opuid='".$sv['op_uid']."' class='c_btn h25 light product_cancel'>부분취소 신청</a>";
                                        }
                                        break;
                                }
                            }



                            // KAY :: 2022-12-07 :: 옵션 구분 수정
                            $arr_option_name = array($sv['op_option1'] ,$sv['op_option2'],$sv['op_option3'] );
                            $arr_option_name = array_filter($arr_option_name);

                            # 옵션처리
                            $option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="ess_tx add">추가</span>' : '<span class="ess_tx">필수</span>') .implode(" / ",$arr_option_name));

                            $complain_option_name = !$sv['op_option1'] ? '옵션없음' : trim(implode(" / ",$arr_option_name));
                            $option_cnt         = $sv['op_cnt'];

                            # 배송상태에 따른 버튼 및 상태값 출력
                            unset($delivery_search, $complete_button, $complain_button);
                            if($v['o_paystatus']=='Y' && $sv['op_is_addoption']!='Y' && $sv['op_cancel'] == 'N'){
                                switch($sv['op_sendstatus']) {
                                    case "":
                                    case "배송대기":
                                    case "배송준비":
                                        $delivery_search = "";
                                        $complete_button = "";
                                        $complain_button = "";
                                        break;
                                    case "배송중":
                                        $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv['op_sendcompany']]?$NPayCourier[$sv['op_sendcompany']]:$arr_delivery_company[$sv['op_sendcompany']]):$arr_delivery_company[$sv['op_sendcompany']]). rm_str($sv['op_sendnum']) . "' class='c_btn h25 black line' title='' target='_blank'>배송조회</a>";
                                        $complete_button = "";

                                        if( $row['npay_order'] != 'Y' ){
                                            if(!$sv['op_complain']   ){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치
                                                $complete_button = "<a href='#none' onclick=\"order_complete('".$ordernum."','".$sv['op_pouid']."');return false;\" class='c_btn h25 black' title='' >수령확인</a>";
                                                $complain_button = "<a href='#none' onclick=\"complain_view('".str_replace("'","`",strip_tags($sv['p_name']))."','".$sv['op_uid']."','".$complain_option_name."','".$option_cnt."');return false;\" class='c_btn h25 light line' title='' >교환/반품</a>";
                                            }else{
                                                $complain_button = "<span class='c_btn h25 light line'>".$arr_massage_conv[$sv['op_complain']]."</span>";
                                            }
                                        }
                                        break;

                                    case "배송완료" :

                                        $delivery_search = "<a href='".($row['npay_order'] == 'Y'?($NPayCourier[$sv['op_sendcompany']]?$NPayCourier[$sv['op_sendcompany']]:$arr_delivery_company[$sv['op_sendcompany']]):$arr_delivery_company[$sv['op_sendcompany']]). rm_str($sv['op_sendnum']) . "' class='c_btn h25 black line' target='_blank' title='' >배송조회</a>";
                                        $complete_button = "";

                                        if( $row['npay_order'] != 'Y' ){
                                            if(!$sv['op_complain'] ){  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치
                                                $complain_button = "<a href='#none' onclick=\"complain_view('".str_replace("'","`",strip_tags($sv['p_name']))."','".$sv['op_uid']."','".$complain_option_name."','".$option_cnt."');return false;\" class='c_btn h25 light line' title='' >교환/반품</a>";
                                            }else{
                                                $complain_button = "<span class='c_btn h25 light line'>".$arr_massage_conv[$sv['op_complain']]."</span>";
                                            }
                                        }
                                        break;

                                    case "발급완료":
                                        $delivery_search = "";
                                        $complete_button = "";
                                        $complain_button = "";
                                        break;

                                    default :
                                        echo "잘못된 배송단계 : ".$sv['op_sendstatus'];
                                        break;
                                }
                            }

                            # 주문취소 / 배송조회 / 교환반품 / 수령확인 버튼
                            $arr_btn = array_filter(array($app_btn_cancel, $complete_button, $delivery_search, $complain_button));
                            $btn_box_html = (count($arr_btn) > 0 ? '' . implode($arr_btn) . '' : null);

                            // LCY : 2022-12-21 : 티켓기능 -- 달력의 날짜 표시 {
                            $dateoption_view = '';
                            if( $sv['op_dateoption_use'] == 'Y' && !empty($sv['op_dateoption_date'])){
                                $dateoption_view = '
                                    <!-- 날짜요일 추가(띄어쓰기 유지) -->
                                    <span class="date">'.$sv['op_dateoption_date'].' ('.$arr_day_week_short[date('w',strtotime($sv['op_dateoption_date']))].')</span>
                                ';
                            }
                            // LCY : 2022-12-21 : 티켓기능 -- 달력의 날짜 표시 }

                            // 티켓상품일 경우 QR코드, 지도보기 노출
                            $btn_ticket_html = '';
                            $ticket_expire_end_text = '';
                            if($sv['op_ptype'] == 'ticket'){
                                $ticket_cnt = _MQ_result("select count(*) as cnt from smart_order_product_ticket where opt_oordernum = '".$sv['op_oordernum']."' and opt_opuid = '".$sv['op_uid']."' ");
                                $ticket_ifno = _MQ("select * from smart_order_product_ticket where opt_oordernum = '".$sv['op_oordernum']."' and opt_opuid = '".$sv['op_uid']."' ");

                                if( $row['o_paystatus'] != 'Y' || $row['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y' ){ $ticket_cnt = 0; }

                                if(count($ticket_ifno)>0){

                                    if($ticket_ifno['opt_expire_type']=='day' || $opt_expire_type=='date'){
                                        $ticket_expire_date = date('Y-m-d',strtotime($ticket_ifno['opt_expire_date']));
                                        $ticket_expire = '<span class="limit_day">'.$ticket_expire_date.' ('.$arr_day_week_short[date('w',strtotime($ticket_ifno['opt_expire_date']))].')까지 사용가능</span>';
                                    }else{
                                        $ticket_expire='';
                                    }

                                    if($ticket_ifno['opt_expire_date']<date('Y-m-d') && $ticket_ifno['opt_expire_date']!=''){
                                        $ticket_expire_end_text = '<span class="c_btn h25 light">기간만료</span>';
                                        $ticket_cnt=0;
                                    }
                                }


                                $btn_ticket_html = '
									<div class="this_ticket">
										'.($ticket_cnt > 0 ?$ticket_expire. '<a href="#none" onclick="return false;" class="c_btn h25 color js_qrcode_view" data-ordernum="'.$sv['op_oordernum'].'" data-opuid="'.$sv['op_uid'].'" data-secure-code="'.enc('e',$sv['op_oordernum'].$sv['op_uid'].$row['o_mid']).'">티켓보기</a>': null).$ticket_expire_end_text.'

									</div>
                                ';
                            }

                            // LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- 주문상품별 배송상태 처리 {
                            $op_new_status_icon = '';
                            if($row['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
                                $op_new_status_icon = '<span class="c_tag gray">주문취소</span>'; 
                            }else if($row['o_status'] == '결제실패'){ // 결제실패일경우
                                $op_new_status_icon = '<span class="c_tag gray">결제실패</span>';
                            }else{
                                if($row['o_paystatus'] =='Y'){ // 주문결제를 했다면,
                                    if($sv['op_sendstatus'] == '배송대기') {
                                        $op_new_status_icon = '<span class="c_tag gray">배송대기</span>';
                                    }else if($sv['op_sendstatus'] == '배송준비'){
                                        $op_new_status_icon = '<span class="c_tag gray">배송준비</span>';
                                    }else if($sv['op_sendstatus'] == '배송중'){
                                        $op_new_status_icon = '<span class="c_tag green">배송중</span>';
                                    }else if($sv['op_sendstatus'] == '배송완료'){
                                        $op_new_status_icon = '<span class="c_tag green line">배송완료</span>';
                                    }

                                    // 티켓은 발급완료
                                    else if($sv['op_sendstatus'] == '발급완료'){
                                        $op_new_status_icon = '<span class="c_tag blue line">발급완료</span>';
                                    }
                                    else{
                                        $op_new_status_icon = '<span class="c_tag gray">주문취소</span>';
                                    }
                                }else{ // 주문결제를 하지 않았다면
                                    $op_new_status_icon = '<span class="c_tag gray">결제대기</span>';
                                }
                            }
                            // LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- 주문상품별 배송상태 처리 }
							
							// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치 -- $op_new_status_icon 추가
                            $op_option_print .= '
                                <li>
                                    <div class="opt_name">
                                        '.$dateoption_view.'
                                        <strong>'. $option_name .' ('.number_format( 1 * $option_cnt).'개)</strong>
                                    </div>
                                    <div class="opt_price">
                                        <div class="price">'. number_format( 1 * $sv['op_price']) .'원</div>
                                    </div>
                                    <div class="opt_ctrl">
										'. $op_new_status_icon .'
                                        <div class="this_normal">
                                            '. $btn_box_html .'
                                        </div>
                                        '.$btn_ticket_html.'
                                    </div>
                                </li>
                            ';
                        }
                        // 옵션처리끝

                        # 진행상태
                        $op_status_icon = '';
                        if($row['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패]
                            $op_status_icon = '<span class="icon cancel">주문취소</span>';
                        }
                        else if($op_status['fail']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제대기]
                            $op_status_icon = '<span class="icon cancel">결제실패</span>';
                        }
                        else if($op_status['ready']>0){ // 결제대기가 하나라도 있으면 결제대기상태 :: [결제대기, 결제완료, 배송중, 배송완료, 주문취소] - [결제대기]
                            $op_status_icon = '<span class="icon wait">결제대기</span>';
                        }
                        else if($op_status['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
                            $op_status_icon = '<span class="icon ing">배송중</span>';
                        }
                        else if($op_status['del_ready']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [결제완료, 배송중, 배송완료, 주문취소] - [배송중]
                            $op_status_icon = '<div class="state_icon"><span class="icon ing">배송준비</span></div>';
                        }
                        else if($op_status['pay']>0){ // 결제완료가 하나라도 있으면 결제완료상태 :: [결제완료, 배송완료, 주문취소] - [결제완료]
                            $op_status_icon = '<span class="icon complete">결제완료</span>';
                        }
                        else if($op_status['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
                            $op_status_icon = '<span class="icon delivery">배송완료</span>';
                        }


                        else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
                            $op_status_icon = '<span class="icon cancel">주문취소</span>';
                        }

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

                        /* 추가배송비개선 - 2017-05-19::SSJ  */
                        $add_delivery_price = $op_add_delivery_price;
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
                        $p_name =$v['p_name']!=""? strip_tags($v['p_name']):"삭제된 상품입니다."; // 상품명
                        $p_url = $v['p_code']!=""?"/?pn=product.view&pcode=".$v['p_code']:""; // 상품의 주소
                        # 상품의 썸네일
                        $p_thumb    = get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지
                        if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/c_img/thumb.gif';
                        # 총 배송비 합계
                        $op_price_delivery += $delivery_price;
                        # 총 합계
                        $op_price_sum_total +=  $op_total_price;

                        $p_code = $v['p_code'];
                        $p_com_juso = $v['p_com_juso'];
                        $p_type = $v['p_type'];

                        // // 지도보기
                        // '.($sv['p_com_juso'] != '' ? '<a href="#none" onclick="return false;" class="c_btn h25 color line js_map_view" data-ordernum="'.$sv['op_oordernum'].'" data-opuid="'.$v['op_uid'].'">지도보기</a>':null).'


                        /*
                            LCY -- 장바구니 다시 담기
                            $option_select_type  => 넘겨준다. 옵션값을
                        */
                        $cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $v['p_code'] ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '".$v['op_pouid']."' and c_dateoption_date != 'Y' ");
                        $re_cart_html = '';
                        if($cnt_tmp['cnt'] > 0) { //이미 담긴 상품
                            $re_cart_html = '<span class="c_tag h25 light line">이미 담긴상품</span>';
                        }else{
                            if($v['p_view'] == 'N'){
                                $re_cart_html = '<span class="c_tag h25 light line">판매종료된 상품</span>';
                            }else if($v['p_stock'] <= 0 ){
                                $re_cart_html = '<span class="c_tag h25 light line">품절된 상품</span>';
                            }else{
                                $re_cart_html = '<a href="#none" onclick="_re_cart_pro(\''. $v['op_oordernum'] .'\', \''. $v['p_code'] .'\', \'\', \''. ($v['p_stock']  < $v['op_cnt'] ? 'stock' : 'none') .'\')" class="c_btn h25 dark line">장바구니 담기</a>';
                            }
                        }

                        ?>
                        <li class="li">
                            <dl class="cart_item">
                                <?php if($p_url!=''){?>
                                    <dt>
                                        <a href="<?php echo $p_url; ?>" class="thumb" target="_blank"><img src="<?php echo $p_thumb; ?>" alt="<?php echo addslashes($p_name); ?>"></a>
                                    </dt>
                                <?php }?>
                                <dd>
                                    <div class="top_name">
                                        <?php if($p_url!=''){?><a href="<?php echo $p_url; ?>" class="item_name" target="_blank"><?php }?>
                                            <?php echo $p_name; ?>
                                        </a>

                                        <?php if( $p_com_juso != '' &&$p_type == 'ticket' && $siteInfo['kakao_js_api']!='') {?>
                                            <div class="top_ctrl">
                                                <a href="#none" onclick="return false;" class="c_btn h25 color line js_map_view" data-pcode="<?php echo $p_code ?>">지도보기</a>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <ul class="option_list">
                                        <?php echo $op_option_print; ?>
                                    </ul>
                                </dd>
                            </dl>
                        </li>
                    <?php } ?>
                </ul>
            </div><!-- end cart_list -->


            <?php // 주문자 정보 ------------------------------------------------------ ?>
            <div class="c_group_tit">
                <span class="tit">주문자 정보</span>
            </div>
            <div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit ">이름</span></dt>
                    <dd class="form_dd"><?php echo $row['o_oname']; ?></dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit ">휴대폰 번호</span></dt>
                    <dd class="form_dd"><?php echo $row['o_ohp']; ?></dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit ">이메일 주소</span></dt>
                    <dd class="form_dd"><?php echo $row['o_oemail']; ?></dd>
                </dl>
            </div><!-- end c_form -->

            <?php if( in_array($row['o_order_type'], array('ticket','both')) > 0) {?>
                <?php // 사용자 정보(티켓 주문일때만) ------------------------------------------------------ ?>
                <div class="c_group_tit">
                    <span class="tit">사용자 정보</span>
                </div>
                <div class="c_form">
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">이름</span></dt>
                        <dd class="form_dd"><?php echo $row['o_uname'] ?></dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">휴대폰 번호</span></dt>
                        <dd class="form_dd"><?php echo $row['o_uhp'] ?></dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">이메일 주소</span></dt>
                        <dd class="form_dd"><?php echo $row['o_uemail'] ?></dd>
                    </dl>
                </div><!-- end c_form -->
            <?php } ?>

            <?php if( in_array($row['o_order_type'], array('delivery','both')) > 0) {?>
                <?php // 배송지 정보(배송 주문일때만) ------------------------------------------------------ ?>
                <div class="c_group_tit">
                    <span class="tit">배송지 정보</span>
                </div>
                <div class="c_form">
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">받는분 이름</span></dt>
                        <dd class="form_dd"><?php echo $row['o_rname']; ?></dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">받는분 휴대폰</span></dt>
                        <dd class="form_dd"><?php echo $row['o_rhp']; ?></dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">받는분 주소</span></dt>
                        <dd class="form_dd">
                            <?php // 우편번호 ?>
                            (<?php echo $row['o_rzonecode']; ?>)
                            <?php // 주소 ?>
                            <?php echo $row['o_raddr_doro']; ?>
                            <?php // 상세 주소 ?>
                            <?php echo $row['o_raddr2']; ?>

                            <div class="tip_txt">(지번주소 : <?php echo $row['o_raddr1']; ?>)</div>
                        </dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit">배송 메시지</span></dt>
                        <dd class="form_dd"><?php echo ($row['o_content']?nl2br($row['o_content']):'없음'); ?></dd>
                    </dl>
                </div><!-- end c_form -->
            <?php } ?>


            <?php // 결제 정보 ------------------------------------------------------ ?>
            <div class="c_group_tit">
                <span class="tit">결제 정보</span>
            </div>
            <div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit ">결제수단</span></dt>
                    <dd class="form_dd">
                        <?php echo $arr_payment_type[$row['o_easypay_paymethod_type'] != '' ? $row['o_easypay_paymethod_type'] : $row['o_paymethod']]; // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 결제수단 표기 -- ?>
                        <?php echo $row['npay_order'] == 'Y' ? ' (네이버페이)':null; // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: 결제수단에 네이버페이 표기  --  ?>
                        <?php
                        $arr_occontent = array();
                        $ex = explode("§§" , $row['oc_content']);
                        foreach( $ex as $sk=>$sv ){
                            $ex2 = explode("||" , $sv);
                            $arr_occontent[$ex2[0]] = $ex2[1];
                        }

                        //- 카드 영수증 출력 ---
                        if($row['oc_tid']) echo link_credit_receipt($row['o_ordernum'],'영수증 보기');
                        ?>
                    </dd>
                </dl>
                <?php if($row['o_paymethod'] == 'online') { ?>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">입금은행</span></dt>
                        <dd class="form_dd"><?php echo $row['o_bank']; ?></dd>
                    </dl>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit">입금자명</span></dt>
                        <dd class="form_dd"><?php echo $row['o_deposit']; ?> <?php echo ($row['o_get_tax']=='Y'?'(현금영수증 발행을 신청하였습니다)':''); ?></dd>
                    </dl>
                <?php } ?>

                <?php
                //![LCY] 2020-07-16 가상계좌 입금정보 추가
                if( $row['o_paymethod'] == "virtual" ) {
                    $ol = _MQ("select * from smart_order_onlinelog where ool_ordernum = '".$row['o_ordernum']."' and ool_type='R' order by ool_uid desc limit 1");
                    $row['o_price_real'] = $row['o_price_real'] + $ol['ool_escrow_fee']; // 총액 수수료더하기

                    // 가상 계좌 입금자명 체크
                    $deposit_name_exist = true;
                    if ( $ol['ool_deposit_name'] == "" ) {
                        $deposit_name_exist = false;
                    }

                    ?>
                    <dl class="form_dl">
                        <dt class="form_dt"><span class="tit ">입금은행</span></dt>
                        <dd class="form_dd"><?=$ol['ool_account_num']?> (<?=$ol['ool_bank_name']?>)</dd>
                    </dl>
                    <?php if ($deposit_name_exist) { ?>
                        <dl class="form_dl">
                            <dt class="form_dt"><span class="tit ">입금자명</span></dt>
                            <dd class="form_dd"><?=$ol['ool_deposit_name']?> <?=$row['o_get_tax']=="Y"?"(현금영수증 발행을 신청하였습니다)":""?></dd>
                        </dl>
                    <?php  } ?>
                <?php } ?>
            </div><!-- end c_form -->

        </div><!-- end area_item -->


        <div class="area_ctrl">

            <div class="cart_ctrl">
                <div class="order_num">주문번호 : <?php echo $row['o_ordernum']; ?></div>
                <div class="order_date">주문일 : <?php echo date('Y-m-d',strtotime($v['o_rdate'])); ?></div>
            </div>

            <div class="cart_sum">
                <div class="total_number"><em>전체상품</em><strong><?php echo number_format( 1 * count($sres)); ?>개</strong></div>
                <dl class="this_point">
                    <dt>총 적립금</dt>
                    <dd><strong><?php echo number_format( 1 * $op_total_point); ?></strong><em>원</em></dd>
                </dl>
                <dl>
                    <dt>상품금액</dt>
                    <dd><strong><?php echo number_format( 1 * $op_price_sum_total); ?></strong><em>원</em></dd>
                </dl>
                <dl style="<?php echo in_array($row['o_order_type'], array('delivery','both')) > 0 ? null:'display:none;'; ?>">
                    <dt>배송비</dt>
                    <dd>+ <strong><?php echo number_format( 1 * $op_price_delivery); ?></strong><em>원</em></dd>
                </dl>
                <dl class="this_discount">
                    <dt>할인</dt>
                    <dd>- <strong><?php echo number_format( 1 * $row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real'] + $NpayDcPrice); ?></strong><em>원</em></dd>
                </dl>
                <dl class="this_last">
                    <dt>총 주문금액</dt>
                    <dd><strong><?php echo number_format( 1 * $row['o_price_real']); ?></strong><em>원</em></dd>
                </dl>
                <div class="c_btnbox type_full type_mypage">
                    <a href="#none" onclick="open_window('print', '<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php?_mode=indprint&ordernum=<?=$ordernum?>', '100', '100', '860', '820', '', '', '', 'yes', ''); return false;" class="c_btn h50 black type_print">인쇄하기</a>
                    <a href="/?<?php echo ($_PVSC ? enc('d',$_PVSC) : 'pn=mypage.order.list'); ?>" class="c_btn h50 line black">목록으로</a>
                    <?php // 목록과 같이 주문 취소기능 추가(노출여부는 목록과 같음) ?>
                    <?php
                    # 주문 상태에 따른 취소 버튼
                    if($row['o_canceled'] == "N"  && $row['npay_order'] != 'Y'  ) {  // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치 :: && $v['npay_order'] != 'Y'  --
                        if( in_array($row['o_status'] , array('결제대기','결제완료','배송대기'))  ){

                            if($row['o_status']!='결제대기'&&($row['o_paymethod']=='virtual'||$row['o_paymethod']=='online')) { // 주문상태가 결제대기가 아니고, 결제방법이 가상,무통장 인건만
                                $view_cancel_function = 'order_view_cancel_virtual(\''.$row['o_ordernum'].'\', \''.$row['o_price_real'].'\')'; // 가상계좌
                            }else {
                                $view_cancel_function = 'order_view_cancel(\''.$row['o_ordernum'].'\')'; // 일반
                            }

                            // 주문취소 생성
                            $app_view_btn_cancel = '<a href="#none" onclick="'. $view_cancel_function .'" class="c_btn h50 light line">전체 주문취소</a>';

                            // 상품이 /취소/반품/교환 요청중인 상품 검사
                            $chk_part_view_cancel = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$row['o_ordernum']."' and op_is_addoption = 'N' and op_cancel != 'N' ");
                            if( $chk_part_view_cancel > 0){
                                $app_view_btn_cancel = "<a href='#none' onclick='alert(\"취소/반품/교환 요청중인 상품이 있습니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h50 light line'>전체 주문취소</a>" ;
                            }
                        }
                        else {
                            $app_view_btn_cancel = "<a href='#none' onclick='alert(\"주문취소가 불가능한 상태입니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h50 light line'>전체 주문취소</a>" ;
                        }
                    }

                    // 티케주문은 예외처리
                    if( in_array($row['o_order_type'], array('ticket','both')) && $row['o_canceled'] == "N" && $row['o_paystatus'] == "Y"){
                        $app_view_btn_cancel = "<a href='#none' onclick='alert(\"티켓이 발급된 경우 주문취소가 불가능합니다. 고객센터 ".$siteInfo['s_glbtel'] ."로 문의하세요.\")' class='c_btn h50 light line'>전체 주문취소</a>" ;
                    }

                    // 전체 주문취소 버튼 노출 :: 마이페이지 목록이랑 노출 조건 동일하게
                    echo $app_view_btn_cancel;
                    ?>

                </div><!-- end c_btnbox -->
            </div><!-- end cart_sum -->

        </div><!-- end area_ctrl -->

    </div><!-- end cart_wrap -->

</div>