<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 주문번호 추출 후 세션 초기화
$_SESSION['session_ordernum'] = '';
unset($_SESSION['session_ordernum']);

// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();


// 회원정보 추출
if(is_login()) $indr = $mem_info;


// {{{LCY무료배송이벤트}}} -- 무료배송 이벤트 조건에 속할경우 true, 그렇지 않을경우 false
$freeEventChk = PromotionEventDeliveryChk();
// {{{LCY무료배송이벤트}}}

// - 주문서 저장 ---------------------------------------------
if( $_SESSION["order_start"] == $_COOKIE["AuthShopCOOKIEID"]){


	// -- 사후체크 ---
	$price_sum	= nullchk($price_sum , "상품이 선택되지 않았습니다.",'',"ALT");
	$_oname		= nullchk($_oname , "주문자명을 입력해주시기 바랍니다.",'',"ALT");
	$_oemail	= nullchk($_oemail , "주문자 이메일을 입력해주시기 바랍니다.",'',"ALT");
	$_ohp		= nullchk($_ohp , "주문자 휴대폰번호을 입력해주시기 바랍니다.",'',"ALT");
	$_paymethod	= nullchk($_paymethod , "결제방식을 선택해주시기 바랍니다.",'',"ALT");
	$_ohp = tel_format($_ohp); $_otel = tel_format($_otel);
	// 2018-09-27 SSJ :: 사후 체킹 - 구매총액 <> 장바구니 총액
	$cartr = _MQ(" select ifnull(sum(c_price*c_cnt) , 0) as sum_c_price from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct = 'Y' ");
	if( $price_sum <> $cartr[sum_c_price]) {
		error_alt("잘못된 정보입니다.\\n\\n다시 한번 주문서를 작성해주시기 바랍니다.");
	}

	// -- 품절체크 --
	$_soldout_chk = _MQ("
			select  count(*) as cnt
				from smart_cart as c
				left join smart_product as p on (c.c_pcode = p.p_code)
				 where
					c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."'
					and c_direct='Y'

					and (p.p_stock-c.c_cnt) < 0
	");
	if($_soldout_chk["cnt"]>0){
		error_alt("장바구니에 담긴 상품중 품절된 상품이 있습니다.\\n\\n장바구니에 담긴 상품을 확인후 다시 주문해주시기 바랍니다.");
	}

	// -- 비교체크 ---
	// 로그인하지 않은 상태에서 포인트 및 보너스쿠폰 사용불가
	if(!is_login() && ($_use_point > 0 || $use_coupon_price_individual > 0)) {
		error_alt("잘못된 정보입니다.\\n\\n다시 한번 주문서를 작성해주시기 바랍니다.");
	}
	// -- 비교체크 ---
	// 포인트 사용량 체크 LMH005
	//if( $_price_usepoint > 0 && $_price_usepoint > $indr[in_point] ) { error_loc_msg("/?pn=shop.order.form","소유한 적립금보다 사용 적립금이 많습니다."); }

	// LCY : 2023-03-14 : 프로모션 코드 할인체크 
	if( $promotion_code != '' && $use_promotion_price > 0){
		$check_promotion_code = empty($_SESSION['secure_promotion_code']) ? array() : $_SESSION['secure_promotion_code']; // shop.cart.pro.php 에서 저장
		if( empty($check_promotion_code['code']) || empty($check_promotion_code['price']) || $check_promotion_code['code'] != $promotion_code || $check_promotion_code['price'] != $use_promotion_price ){ error_alt("잘못된 정보입니다.\\n\\n다시 한번 주문서를 작성해주시기 바랍니다."); }

	}


    // SSJ :: 하이브리드 앱 - 앱 주문 추가할인
    $app_buy_sale_price = 0;
	if( function_exists('is_app') ) { 
		if($AppUseMode === true && is_app() === true){
			// 앱 추가할인 설정 사용 시
			if($siteInfo['as_app_sale_use'] == 'Y'){
				// 총 주문 상품 금액 추출
				$app_total_price = $price_sum;

				// 할인 적용 최소금액 체크
				if($siteInfo['as_app_sale_min'] < $app_total_price){
					// 앱 구매 추가할인액 추출
					if($siteInfo['as_app_sale_unit'] == 'per') $app_buy_sale_price = ceil(($app_total_price * ($siteInfo['as_app_sale_per']/100))/10)*10;
					else $app_buy_sale_price = $siteInfo['as_app_sale_price'];

					// 최대 할인 가능 금액 체크
					if($siteInfo['as_app_sale_max'] > 0) $app_buy_sale_price = min($app_buy_sale_price, $siteInfo['as_app_sale_max']);
				}
			}
		}
	}
    // SSJ :: 하이브리드 앱 - 앱 주문 추가할인


	// -- 변수 준비 ---
	$_ordernum					= shop_ordernum_create();//주문번호 생성 예) 12345-23456-34567
	$_memtype					= (is_login() ? "Y" : "N");//회원타입, Y:회원, N:비회원
	$_mid						= ( is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"] );//회원아이디, 비회원일 경우 쿠키정보 입력
	$_price_real				= $price_total;// 실제결제해야할 금액
	$_price_total				= $price_sum;// 구매총액 (상품 금액)
	$_price_delivery			= $price_delivery; //배송비
	$cpointres					= _MQ(" select ifnull(sum(c_point ),0) as sum_point from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'");
	$_price_supplypoint			= floor($cpointres[sum_point]);//제공해야할 포인트
	$_price_usepoint			= str_replace(',','',$_use_point);
	$_price_coupon_individual	= $use_coupon_price_member;//보너스쿠폰사용액
	$_price_coupon_product		= $use_coupon_price_product;//상품쿠폰사용액

	// {{{회원쿠폰}}}
	$_save_price_coupon_individual	= $use_coupon_save_price_member;//보너스 쿠폰 적립액
	// {{{회원쿠폰}}}

	$_price_promotion			= $use_promotion_price;//프로모션코드 할인금액 LMH005
	//$_paymethod = $_paymethod;//결제방식
	$_paystatus					= "N";//결제상태
	$_canceled					= "N";//결제취소상태
	$_status					= "결제대기";//주문상태

	$_otel						= ($_otel ? tel_format($_otel) : "");
	$_ohp						= ($_ohp ? tel_format($_ohp) : "");
	$_rtel						= ($_rtel ? tel_format($_rtel) : "");
	$_rhp						= ($_rhp ? tel_format($_rhp) : "");

	// 사용자 휴대폰 번호
	$_uhp						= ($_uhp ? tel_format($_uhp) : "");

	// {{{주문기기정보}}}
	$GetDeviceInfo = Get_device_info();

	// -- LCY 2016-04-10 -- 포인트 사용량 체크
	$psers = _MQ("select sum(o_price_usepoint) as use_point_sum from smart_order where o_apply_point = 'N' and
	o_price_usepoint > 0 and  o_canceled = 'N' and o_paystatus = 'N' and  o_mid = '".get_userid()."' and o_paymethod in ('online','virtual') ");
	if($_price_usepoint > 0){
		$use_point_sum = $psers['use_point_sum'] > 0 ? $psers['use_point_sum'] : 0;
		$use_able_point = $indr[in_point] - $use_point_sum;
		if($_price_usepoint > $use_able_point){
			error_alt("소유한 적립금보다 사용 적립금이 많습니다.");
		}
	}
	// -- LCY 2016-04-10 -- 포인트 사용량 체크


	// -- LCY 2016-04-10 -- 배송비 조작 패치
	/* 추가배송비개선 - 2017-05-19::SSJ  */
	//$_encode_del_chk = md5($_ecode_type.md5($_price_delivery));
	$_encode_del_chk = md5($_ecode_type.md5($_price_delivery-($price_add_delivery*1)));
	if($_ecode_type_delivery <> $_encode_del_chk){
		error_alt("배송비가 조작되었습니다. 주문 및 결제가 이루어지지 않습니다.");
	}

	# LCY 2016-04-22 :: 상품 쿠폰 조작 패치
	if(sizeof($product_coupon) > 0) { // 상품 쿠폰을 사용하였다면, (상품쿠폰 할인가격 이 있다면)
		foreach($product_coupon as $coupon_pcode => $coupon_price) {
				if(md5(sha1($_SERVER['REMOTE_ADDR'].$coupon_price)) <> $pc_check[$coupon_pcode] ) {
						error_alt("상품쿠폰이 조작되었습니다.");
				}
		}
	}



	// -- LCY 2016-04-10 --  배송문구 패치
	if($_content_select != '4'){ $_content = $_content_select; }


	// {{{회원쿠폰}}} -- 쿠폰체크
	$_chk_price_coupon_individual = $_chk_save_price_coupon_individual = 0; // 체크할 쿠폰할인,적립금액 {{ $_save_price_coupon_individual , _price_coupon_individual }}
	$resCouponForm = array();
	if(count($use_coupon_member) > 0 ){
		// 최종적으로 적용한 쿠폰 목록을 가져온다.
		$resCouponForm = _MQ_assoc(" select *  from `smart_individual_coupon_form` as ocf
			inner join smart_individual_coupon as coup on(coup.coup_uid = ocf.ocf_coupuid)
			inner join smart_individual_coupon_set as ocs on(ocs.ocs_uid = coup.coup_ocs_uid)
			where ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and coup_use='N'   ");
		foreach($resCouponForm as $k=>$v){
			if( in_array($v['coup_uid'],$use_coupon_member) == false){  error_alt("[ERROR0001]적용된 쿠폰에 문제가 있습니다."); }
			if( $v['coup_type'] == 'save'){
				$_chk_save_price_coupon_individual +=$v['coup_price'];
			}else{
				$_chk_price_coupon_individual += $v['coup_price'];
			}
		}

		// 최후체크 -- 할인쿠폰총액 및 적립총액
		if( $_price_coupon_individual != $_chk_price_coupon_individual || $_save_price_coupon_individual != $_chk_save_price_coupon_individual  ){ error_alt("[ERROR0002]적용된 쿠폰에 문제가 있습니다.");  }

		// 쿠폰에 의해 추가된 적립금을 적용해 준다.
		if( $_save_price_coupon_individual > 0) { $_price_supplypoint += $_save_price_coupon_individual; }

	}
	// {{{회원쿠폰}}}



	//  실제로 계산되어야 할 상품의 가격(배송비,포인트,쿠폰,프로모션 => 할인헤택 제외)이 0보다 크고, 할인혜택 받은 결제액이 0 이라면,..
	if($_price_total > 0 && $_price_real == 0) {
		$_price_real = $_price_total + $_price_delivery - $_price_usepoint - $_price_coupon_individual - $_price_coupon_product - $use_promotion_price;
	}

	// 주문서입력전 계산금액이 맞는지 한번더 체크
	if($_price_total <> ($_price_real - $_price_delivery + $_price_usepoint + $_price_coupon_individual + $_price_coupon_product + $use_promotion_price+$app_buy_sale_price)){
		error_alt("주문시 오류가 발생하였습니다.\\n\\n다시 한번 주문서를 작성해주시기 바랍니다.");
	}

    // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 간편결제일경우 처리 --
    $_easypay_paymethod_type = '';
    if( in_array($_paymethod, array_keys($arr_available_easypay_pg[$siteInfo['s_pg_type']])) > 0    ){
        $_easypay_paymethod_type = $_paymethod;
        $_paymethod = 'card';
    }


	if(is_mobile() == true) $_is_mobile = 'Y';
	else $_is_mobile = 'N';

	// ----- JJC : 하이앱 : 앱정보 추가 -----
	if( function_exists('is_app')){
		if(is_app() == true) $_is_mobile = 'A';
	}

		// 주문 - 지역설정
		//			arr_order_area --> var.php에서 배열변수 설정됨
		$ex_area = explode( " " , $_addr_doro);
		$app_area = $arr_order_area[trim($ex_area[0])];

	// LCY : 2023-01-04 : 바로결제처리 -- 기존데이터는 삭제처리 한다.  LCY : 2023-06-30 : 추가패치 : 3일전 데이터로 상향 조정(사용시에만 주석해제)
	// _MQ_noreturn("delete from smart_order where o_mid ='".$_mid."' and o_paystatus = 'N' and o_paymethod not in ('online','virtual','point') and o_directpay_status = 'ready' and  date(o_rdate) <= '".date('Y-m-d',strtotime("-3 day"))."' ");

	// -- smart_order 입력 ---
	$sque = "
		insert smart_order set
			o_ordernum							= '". $_ordernum ."'
			, o_memtype							= '". $_memtype ."'
			, o_mid									= '". $_mid ."'
			, o_oname							= '". $_oname ."'
			, o_otel									= '". $_otel ."'
			, o_ohp									= '". $_ohp ."'
			, o_oemail							= '". $_oemail ."'
			, o_rname								= '". $_rname ."'
			, o_rtel									= '". $_rtel ."'
			, o_rhp									= '". $_rhp ."'
			, o_rpost								= '". $_post ."'
			, o_raddr1							= '". $_addr1 ."'
			, o_raddr2							= '". $_addr2 ."'
			, o_raddr_doro						= '". $_addr_doro ."'
			, o_content							= '". $_content ."'
			, o_price_real						= '". $_price_real ."'
			, o_price_total						= '". $_price_total ."'
			, o_price_delivery					= '". $_price_delivery ."'
			, o_price_supplypoint			= '". $_price_supplypoint ."'
			, o_price_usepoint				= '". $_price_usepoint ."'
			, o_price_coupon_product	= '". $_price_coupon_product ."'
			, o_paymethod						= '". $_paymethod ."'
			, o_paystatus						= '". $_paystatus ."'
			, o_canceled						= '". $_canceled ."'
			, o_status								= '". $_status ."'
			, o_bank								= '". $_bank ."'
			, o_get_tax							= '". $_get_tax ."'
			, o_deposit							= '". $_deposit ."'
			, o_rdate								= now()
			, o_apply_point						= 'N'
			, mobile								= '".$_is_mobile."'
			, o_rzonecode						= '".$_zonecode."'
			, o_area								= '". $app_area ."'
	";
	// 프로모션코드 사용했다면 저장 LMH005
	if($_price_promotion > 0) {
		$sque .= " , o_promotion_code = '".$promotion_code."', o_promotion_price = '".$_price_promotion."' ";
	}

	// 현금 영수증 신청정보 저장
	if($_get_tax == "Y"){
		$sque .= "
			, o_tax_TradeUsage				= '". $_tax_TradeUsage ."'
			, o_tax_TradeMethod			= '". $_tax_TradeMethod ."'
			, o_tax_IdentityNum				= '". onedaynet_encode(rm_str($_tax_IdentityNum)) ."'
		";
	}

	// {{{주문기기정보}}}
	$sque .= "
		, device_info = '".$GetDeviceInfo."'
	";
	// {{{주문기기정보}}}

	//{{{회원쿠폰}}}
	if( count($use_coupon_member) > 0 ){
		$sque .= " , o_coupon_individual_uid = '".implode(",",$use_coupon_member)."'  ";
	}
	$sque .="
		, o_price_coupon_individual	= '". $_price_coupon_individual ."'
		, o_save_price_coupon_individual = '".$_save_price_coupon_individual."'
	";
	//{{{회원쿠폰}}}

    // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 간편결제일경우 처리 --
    if( $_easypay_paymethod_type != '' ){
        $sque .= " , o_easypay_paymethod_type = '".$_easypay_paymethod_type."'  ";  
    }


	// JJC : 부분취소 개선 : 2021-02-10
	//	// 포인트 환불 방식을 가져와서 주문할때 입력 (부분취소 kms 2019-03-15)
	//	$site_info = get_site_info();
	//	$sque .= ",o_paycancel_method =  '".$site_info['s_paycancel_method']."'";
	//	// 포인트 환불 방식을 가져와서 주문할때 입력 (부분취소 kms 2019-03-15)
	// 포인트 환불 방식을 가져와서 주문할때 입력
	$sque .= ",o_paycancel_method =  '".$siteInfo['s_paycancel_method']."'";
	// JJC : 부분취소 개선 : 2021-02-10


	// 주문타입 정의
	$sque .= " , o_order_type = '".addslashes($order_type)."' ";


	// 사용자 주소 추가
	if($order_type =='both' || $order_type == 'ticket') {
		$sque .="
			, o_uname	= '". addslashes($_uname) ."'
			, o_uhp		= '". addslashes($_uhp) ."'
			, o_uemail	= '". addslashes($_uemail) ."'		
		";
	}

	// LCY : 2023-01-04 : 바로결제처리 -- 주문서 상태값 처리,  무통장/전액포인트 결제가 아닐경우 ready 상태
	$_directpay_status = 'none';
	if( in_array($_paymethod, array('online','point','virtual')) < 1){
		$_directpay_status = 'ready';
	}
	$sque .= "  , o_directpay_status = '".$_directpay_status."' "; 


    // SSJ :: 하이브리드 앱 - 앱 주문 추가할인
	if( function_exists('is_app') ) { 
		if($AppUseMode === true && is_app() === true){
			$sque .= " , o_price_appsale = '". $app_buy_sale_price ."' ";	
		}
	}
    // SSJ :: 하이브리드 앱 - 앱 주문 추가할인	


	_MQ_noreturn($sque);


	// -- smart_order_product 입력 ---
	// -- 카트정보 체크 --
	include(OD_PROGRAM_ROOT."/shop.cart.inc.php");
	//return $arr_customer ;
	//Array
	//(
	//    [p_cpid] => Array
	//        (
	//            [app_delivery_price] => 5000 // 실제 배송비
	//        )
	//)
	$arr_customer_apply = array();
	$arr_product_apply = array();

	// 2017-06-16 ::: 부가세율설정 - p_vat 추가 ::: JJC
	$sres = _MQ_assoc(" select c.*,p.*
	from smart_cart as c left join smart_product as p on (p.p_code = c.c_pcode) where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y' order by p.p_cpid asc, c.c_is_addoption desc, c.c_uid asc ");// 선택 구매 2015-12-04 LDD

	// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	$arr_product_per_apply = array();
	$arr_per_product = array();
	foreach( $sres as $k=>$v ){
		$arr_per_product[$v['c_pcode']]['sum'] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
	}
	// ----- JJC : 상품별 배송비 : 2018-08-16 -----

	foreach( $sres as $k=>$v ){

		// --- 배송비 타입 설정 ---
		$_delivery_type = "입점";
		switch($v['p_shoppingPay_use']){
			case "Y": $_delivery_type ="개별"; $product_delivery_price = $v['p_shoppingPay'] * $v[c_cnt]; break;
			case "N":
				$_delivery_type ="입점";
				$product_delivery_price = ($arr_customer_apply[$v['p_cpid']] > 0 ? 0 : $arr_customer[$v['p_cpid']]['app_delivery_price']); // 입점 기본배송정책 따름.

				 if( $v['p_free_delivery_event_use'] != 'Y'){
				 	$arr_customer_apply[$v['p_cpid']] ++;// 배송비는 1회 적용
				 }
				
			break; // 일괄 추가
			case "F": $_delivery_type ="무료"; $product_delivery_price = 0; break;
			// ----- JJC : 상품별 배송비 : 2018-08-16 -----
			case "P":
				$_delivery_type ="상품별";
				$product_delivery_price = ($arr_product_per_apply[$v['c_pcode']] > 0 ? 0 : ($v['p_shoppingPayPfPrice'] == 0 || $v['p_shoppingPayPfPrice'] >  $arr_per_product[$v['c_pcode']]['sum'] ? $v['p_shoppingPayPdPrice'] : 0 )); // 상품별 배송비 설정 따름. // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
				$arr_product_per_apply[$v['c_pcode']] ++;// 배송비는 1회 적용
				break;
			// ----- JJC : 상품별 배송비 : 2018-08-16 -----
		}
		// 추가배송비
		$product_add_delivery_price = ($arr_product_apply[$v['c_pcode']] > 0 ? 0 : $op_add_delivery_price[$v['c_pcode']]);
		$arr_product_apply[$v['c_pcode']] ++;

		// ----- SSJ : 추가옵션은 개별배송비 미적용 : 2020-02-04 -----
		if($v['c_is_addoption'] == 'Y') $product_delivery_price = 0;

		// 기본 배송상태의 `배송대기` 만 있던 상태값을 티켓상태값과 분리하여 처리 
		$_sendstatus = '배송대기';
		if($v['p_type'] == 'ticket'){
			$_sendstatus = '발급대기';
			$product_delivery_price = 0;
		}
		

		$ssque = "
			insert smart_order_product set
				op_oordernum			= '". $_ordernum ."'
				, op_pcode				= '". $v[c_pcode] ."'
				, op_pouid				= '". $v[c_pouid]."'
				, op_option1			= '". addslashes($v[c_option1]) ."'
				, op_option2			= '". addslashes($v[c_option2]) ."'
				, op_option3			= '". addslashes($v[c_option3]) ."'
				, op_add_delivery_price = '".$product_add_delivery_price."'
				, op_supply_price		= '". $v[c_supply_price] ."'
				, op_price				= '". $v[c_price] ."'
				, op_point				= '". $v[c_point]."'
				, op_cnt				= '". $v[c_cnt] ."'
				, op_sendstatus			= '".$_sendstatus."'
				, op_rdate				= now()
				, op_partnerCode		= '".$v[p_cpid]."'
				, op_comSaleType		= '".$v[p_commission_type]."'
				, op_commission			= '".$v[p_sPersent]."'
				, op_pname				= '".addslashes($v[p_name])."'
				, op_delivery_price		= '". $product_delivery_price ."'
				, op_is_addoption		= '". $v['c_is_addoption'] ."'
				, op_addoption_parent	= '". $v['c_addoption_parent'] ."'
				, op_delivery_type = '". $_delivery_type ."'
		";

		// 2017-06-16 ::: 부가세율설정 ::: JJC
		$v['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $v['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다
		$ssque .= ", op_vat = '". $v['p_vat'] ."'";
		// 2017-06-16 ::: 부가세율설정 ::: JJC

		// {{{회원등급혜택}}} -- c_old_price , c_old_point , c_groupset_price_per, c_groupset_point_per 참고
		unset($groupSetUse);
		if( $v['p_groupset_use'] == 'Y' && is_login() == true ){
			if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
				$groupSetUse = true;
			}
		}

		// 변수 재정의
		$v['p_groupset_use'] = $groupSetUse === true && $v['p_groupset_use'] == 'Y' ? 'Y':'N';

		$ssque .= "  , op_groupset_use = '".$v['p_groupset_use']."'   ";
		$ssque .= "  , op_old_price = '".$v['c_old_price']."'   ";
		$ssque .= "  , op_old_point = '".$v['c_old_point']."'   ";
		$ssque .= "  , op_groupset_price_per = '".$v['c_groupset_price_per']."'   ";
		$ssque .= "  , op_groupset_point_per = '".$v['c_groupset_point_per']."'   ";
		// {{{회원등급혜택}}}

		// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 저장
		if(count($product_coupon) > 0 && count($tmp_product_coupon) < 1){ $tmp_product_coupon = $product_coupon; }
		if($tmp_product_coupon[$v['c_pcode']] > 0){
			$ssque .= "  , op_use_product_coupon = '".$tmp_product_coupon[$v['c_pcode']]."'   ";
			$tmp_product_coupon[$v['c_pcode']] = 0; // 적용 후 초기화
		}

		$ssque .="
			, op_ptype = '".$v['p_type']."'
			, op_dateoption_use = '".$v['c_dateoption_use']."'
		";		

		// 달력옵션이 있을 경우에만 처리
		if( $v['c_dateoption_use'] == 'Y'){
			$ssque .= "  , op_dateoption_date = '".$v['c_dateoption_date']."'   ";	
		}

		_MQ_noreturn($ssque);
	}
	// -- smart_order_product 입력 ---


	//{{{회원쿠폰}}} -- $resCouponForm 상단에서 가져온다.
	if(count($resCouponForm) > 0) { // 사용자 쿠폰을 사용했다면
		foreach($resCouponForm as $k=>$coupon_info){
			_MQ_noreturn("insert into smart_order_coupon_log set
							cl_type			= 'member',
							cl_title		= '".$coupon_info['ocs_name']."',
							cl_price		= '".$coupon_info['coup_price']."',
							cl_oordernum	= '".$_ordernum."',
							cl_coNo			= '".$coupon_info['coup_uid']."',
							cl_pcode		= '',
							cl_rdate		= now()");
		}

		couponFormInit(); // 주문시 적용된 쿠폰기록 삭제
	}
	//{{{회원쿠폰}}}


	# LCY 2016-04-22 :: 상품 쿠폰 체크
	if(sizeof($product_coupon) > 0) { // 상품 쿠폰을 사용하였다면, (상품쿠폰 할인가격 이 있다면)
			foreach($product_coupon as $coupon_pcode => $coupon_price) {
			$cl_info = _MQ("select p_coupon from smart_product where p_code = '".$coupon_pcode."' ");
			if(count($cl_info) > 0) {
				$ex = explode("|" , $cl_info[p_coupon]);
				_MQ_noreturn("insert into smart_order_coupon_log set
								cl_type			= 'product',
								cl_title		= '".$ex[0]."',
								cl_price		= '".$coupon_price."',
								cl_oordernum	= '".$_ordernum."',
								cl_coNo			= '',
								cl_pcode		= '".$coupon_pcode."',
								cl_rdate		= now()");
				}
			}
	}




	include_once OD_PROGRAM_ROOT."/shop.order.usepoint.php";



	// 메일발송
	// 무통장입금 : 입금요청 메일
	// 전액포인트	:	결제 성공 메일
	// 카드/이체는 order.result_pro.php 에서 처리한다.
	switch($_paymethod) {
		case "online" :

			// 장바구니 정보 삭제 - card결제는 shop.order.result.pro.php에서처리 SSJ : 2018-03-22
			_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");
			

			// 쿠폰사용상태추가- 사용대기(W) 상태로 변경  -- LCY -- 사용대기가 없어서 추가
			// 제공변수 : $_ordernum
			include_once(OD_PROGRAM_ROOT."/shop.order.couponadd_pro.php");

            // LCY : 2024-01-18 : 무통장/가상계좌 재고차감 시점 변경 패치 {
                // 상품 재고 차감 및 판매량 증가
                // 제공변수 : $_ordernum
                include_once(OD_PROGRAM_ROOT."/shop.order.salecntadd_pro.php");
            // LCY : 2024-01-18 : 무통장/가상계좌 재고차감 시점 변경 패치 }     


			if( mailCheck($_oemail) ){
				// $_ordernum ==> 주문번호
				$_type = "online"; // 결제확인처리
				include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
				$_title = "[".$siteInfo[s_adshop]."] 무통장 결제를 하셨습니다.";
				$_content = $mailing_app_content;
				$_content = get_mail_content($_content);
				mailer( $_oemail , $_title , $_content );
			}

			// 문자 발송
			//$sms_to = $_rhp ? $_rhp : $_rtel;
			$sms_to = $_ohp ? $_ohp : $_otel; // JJC : 문자발송 전화번호 변경( 수신자 -> 주문자 ) : 2019-01-28
			shop_send_sms($sms_to,"order_online",$_ordernum);

			break;

		case "point" :

			// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
			// 주문정보 보정
			$osr = get_order_info($_ordernum);

			if($osr['o_status'] == '결제대기' && $osr['o_paymethod'] == 'point' && $osr['o_paystatus'] == 'N' && $osr['o_canceled'] == 'N' && $osr['o_price_real'] == 0 ){

				// 공통결제
				//		넘길변수
				//			-> 주문번호 : $ordernum
				$ordernum = $_ordernum;
				include(OD_PROGRAM_ROOT."/shop.order.result.pro.php");
				if($pay_status == 'N') {error_alt($pay_msg);}

			}
			// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----


			break;

	}



	// LCY : 2023-01-04 : 바로결제처리 -- 바로결제의 경우 세션을 삭제하지 않는다. 
	// -- 주문서 저장 후 세션파괴 ::: 재등록 막기 ---
	if( in_array($_paymethod, array('online','point')) > 0){
		$_SESSION["order_start"] = "";
		session_destroy();
		session_start();
	}

	if(substr(phpversion(),0,3) < 5.4) { session_register("session_ordernum"); }
	$_SESSION["session_ordernum"] = $_ordernum;//주문번호

}
// - 주문서 저장 ---------------------------------------------


// PG 연동시 중간처리 -> order.result.php
// 무통장입금시  -> order.complete.php

if( $_paymethod == "online" || $_paymethod == "point") {
	error_loc("/?pn=shop.order.complete");
}
else {
	// 결제처리 진행 
	$ordernum = $_ordernum;
	$auth_order_directpay = true;
	include_once(OD_PROGRAM_ROOT.'/shop.order.directpay.load.php'); // 바로결제 라이브러리로드 후 결제 실행 (공통)	
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행