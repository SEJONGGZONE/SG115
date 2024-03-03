<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// 선택 구매 초기화 2015-12-04 LDD
if($_COOKIE["AuthShopCOOKIEID"] && $pn == 'shop.cart.list') _MQ_noreturn(" update smart_cart set c_direct = 'N' where c_cookie = '{$_COOKIE["AuthShopCOOKIEID"]}' ");
// 선택 구매 초기화 2015-12-04 LDD


// --- 장바구니 정보 추출 ---
$arr_cart = $arr_customer = $arr_delivery = $arr_product_info = $arr_push_relation = $arr_push_code = array();

// LCY : 2022-12-21 : 티켓기능 -- 상품 형태 - 둘다 Y 인경우 both, 
$arr_order_type_delivery = $arr_order_type_ticket = array();

/* 추가배송비개선 - 2017-05-19::SSJ  */

// JJC : 2023-01-13 : 추가옵션 부분취소 - 위치조정 : 지정된 기본옵션 하위에 추가옵션 위치함
$que = "
    select
        c.* , p.*, po.*,
        m.cp_name, m.cp_id , m.cp_delivery_price , m.cp_delivery_freeprice , m.cp_delivery_company ,m.cp_delivery_use,
        c_pouid as app_pouid, cp_del_addprice_use, cp_del_addprice_use_normal, cp_del_addprice_use_unit, cp_del_addprice_use_free, cp_del_addprice_use_product
		, IF(c_is_addoption = 'Y' , c_addoption_parent , c_pouid) AS order_uid
    from smart_cart as c
    inner join smart_product as p on (p.p_code=c.c_pcode)
    inner join smart_company as m on (m.cp_id=p.p_cpid)
    left join smart_product_option as po on (po.po_uid = c.c_pouid)
    where
        c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
        ".($pn!='shop.cart.list'?"and c.c_direct = 'Y'":"")."
    order by p_cpid asc, order_uid ASC, c_uid asc
";
/* 추가배송비개선 - 2017-05-19::SSJ  */

$r = _MQ_assoc($que);
foreach( $r as $k=>$v ){

	// LCY : 2022-12-21 : 티켓기능 -- 달력옵션
		$add_key = '';
		if( $v['c_dateoption_use'] == 'Y'){ $add_key = $v['c_dateoption_date']; } // 같은날짜 같은 옵션 노출을 위한 처리	
	// LCY : 2022-12-21 : 티켓기능 -- 달력옵션

	// 장바구니 정보 저장
	foreach( $v as $sk=>$sv ){
		$arr_cart[$v['p_cpid']][$v['c_pcode']][$v['c_pouid'].$add_key][$sk] = $sv;
		$arr_product_info[$v['c_pcode']][$sk] = $sv;
	}

	// 함께 구매하면 좋은 상품 정보 추출을 위한 변수 적용
	if($v['p_relation']) {$arr_push_relation[]= $v['p_relation'];}
	if($v['c_pcode']) {$arr_push_code[]= $v['c_pcode'];}

	/* 추가배송비개선 - 2017-05-19::SSJ  */
	// 입점업체 정보 저장
	if($v['cp_delivery_use'] == "N" || $SubAdminMode === false ) {// 쇼핑몰  배송비 정책을 사용한다.
		$v['cp_delivery_price'] = $siteInfo['s_delprice'];
		$v['cp_delivery_freeprice'] = $siteInfo['s_delprice_free'];
		$v['cp_delivery_company'] = $siteInfo['s_del_company'];

		$v['cp_del_addprice_use'] = $siteInfo['s_del_addprice_use'];
		$v['cp_del_addprice_use_normal'] = $siteInfo['s_del_addprice_use_normal'];
		$v['cp_del_addprice_use_unit'] = $siteInfo['s_del_addprice_use_unit'];
		$v['cp_del_addprice_use_free'] = $siteInfo['s_del_addprice_use_free'];
		$v['cp_del_addprice_use_product'] = $siteInfo['s_del_addprice_use_product'];// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	}
	$arr_customer[$v['p_cpid']] = array('cName'=>$v['cp_name'] , 'com_delprice'=>$v['cp_delivery_price'] , 'com_delprice_free'=>$v['cp_delivery_freeprice'], 'com_del_company'=>$v['cp_delivery_company'], 'cp_del_addprice_use'=>$v['cp_del_addprice_use'], 'cp_del_addprice_use_normal'=>$v['cp_del_addprice_use_normal'], 'cp_del_addprice_use_unit'=>$v['cp_del_addprice_use_unit'], 'cp_del_addprice_use_free'=>$v['cp_del_addprice_use_free'],
	'cp_del_addprice_use_product'=>$v['cp_del_addprice_use_product'],// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	);
	/* 추가배송비개선 - 2017-05-19::SSJ  */


	// 배송비용 계산을 위한 입점업체별 주문금액합산 - 개별배송 , 무료배송일 경우 가격 포함 하지 않음.
	if( $v['p_shoppingPay_use']=="N" && $v['p_type'] == 'delivery' ){
		$arr_delivery[$v['p_cpid']] += $v['c_cnt'] * $v['c_price'];
	}


	// LCY : 2022-12-21 : 티켓기능 -- 상품 형태 - 둘다 Y 인경우 both, 
	if($v['p_type'] == "delivery"){ $order_type_delivery = "Y"; $arr_order_type_delivery[$v['p_cpid']]['delivery'] = 'Y'; }
	if($v['p_type'] == "ticket")  { $order_type_ticket = "Y"; $arr_order_type_ticket[$v['p_cpid']]['ticket'] = 'Y'; }	



}
// --- 업체별 배송비 정보 계산 ---


// --- 업체별 배송비 처리 ---
if(sizeof(array_filter($arr_delivery)) > 0 ) {
	foreach( array_filter($arr_delivery) as $k=>$v ){
		$arr_customer[$k]['app_delivery_price'] = 0; //무료배송
		if($arr_customer[$k]['com_delprice_free'] > 0) {
			$arr_customer[$k]['app_delivery_price'] = ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
		}
		else {
			$arr_customer[$k]['app_delivery_price'] = $arr_customer[$k]['com_delprice'];//배송비적용
		}
	}
}
// --- 업체별 배송비 처리 ---

// {{{LCY무료배송이벤트}}} -- 무료배송 이벤트 조건에 속할경우 true, 그렇지 않을경우 false
$freeEventChk = PromotionEventDeliveryChk();


// 주문 타입 both : 둘다 , product : 배송상품, coupon : 쿠폰 상품
if($order_type_delivery == "Y" && $order_type_ticket == "Y") { $order_type = "both"; }
if($order_type_delivery == "Y" && $order_type_ticket != "Y") { $order_type = "delivery"; }
if($order_type_delivery != "Y" && $order_type_ticket == "Y") { $order_type = "ticket"; }


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행