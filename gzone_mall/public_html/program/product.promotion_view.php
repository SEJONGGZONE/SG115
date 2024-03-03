<?php

defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


$que = " select * from smart_promotion_plan where pp_uid='" . addslashes(htmlspecialchars($uid)) . "'  ";
$row = _MQ($que);


// 숨김 상품 체크
if($row['pp_view'] == "N") error_loc_msg("/","비공개 기획전입니다.");

// smart_table_text를 통해 pp_content(PC내용), pp_content_m(모바일 내용) 정보 가져옴
$row = array_merge($row , _text_info_extraction( "smart_promotion_plan" , $row['pp_uid'] ));

// === 비회원 구매 설정 추가 통합 kms 2019-06-21 ====
// - 넘길 변수 설정하기 ---
//$_PVS = ""; // 링크 넘김 변수
//foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
//$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---
// === 비회원 구매 설정 추가 통합 kms 2019-06-21 ====


// 기획전 - 상품정보 추출
$s_query = "where ppps.ppps_ppuid = '". addslashes(htmlspecialchars($uid)) ."' and p.p_view = 'Y' ";

// KAY :: 2022-08-26 :: 상품목록 품절제외 체크박스 검색
if($pstock_chk =='Y'){	$s_query .= " and p.p_stock > 0 and p_soldout_chk='N'  ";	}

// KAY :: 2022-08-26 :: 상품목록 무료배송 체크박스 검색
if($free_delivery_chk =='Y'){	$s_query .= " and p.p_shoppingPay_use ='F' ";	}

// KAY :: 2022-08-26 :: 상품목록 무료배송 체크박스 검색
if($coupon_chk =='Y'){	$s_query .= " and cast(replace(substring_index(p_coupon, '|', 1), ',', '') as char(1)) !='' ";	}


// 정렬방식
switch($_order) {
	case 'price_asc': $s_order = ' order by p.p_price asc, ppps.ppps_idx asc '; break; // 가격 낮은순
	case 'price_desc': $s_order = ' order by p.p_price desc, ppps.ppps_idx asc '; break; // 가격 높은순
	case 'pname': $s_order = ' order by p.p_name asc, ppps.ppps_idx asc '; break; // 상품이름순(abc..)
	case 'date': $s_order = ' order by p.p_rdate desc, ppps.ppps_idx asc '; break; // 등록일 순
	case 'sale': $s_order = ' order by p.p_salecnt desc, ppps.ppps_idx asc '; break; // 판매순(인기순)
	default: $s_order = ' order by ppps.ppps_idx asc '; break; // 기본 설정 정렬 순
}
if(isset($order_field) && $order_field != '') { // 사용자 지정 정렬 방식이 있다면
	if(empty($order_sort) || $order_sort == '') $order_sort = 'asc'; // 정령 방식이 없다면 기본값 asc
	$s_order = " order by {$order_field} {$order_sort}  ";
}

$ppps_que = "
	select p.*, ppps.ppps_pcode as p_code
	from smart_promotion_plan_product_setup  as ppps
	left join smart_product as p on (ppps.ppps_pcode = p.p_code)
	${s_query}
	". $s_order ."
";  //=> SSJ:2017-12-13 상품이 삭제됬을때 선택삭제를 위해 p_code는 smart_promotion_plan_product_setup테이블에서 추출
$res = _MQ_assoc($ppps_que);
$TotalCount = sizeof($res);


// 상품 기본 아이콘
$product_icon = get_product_icon_info('product_name_small_icon');

// 자동적용아이콘 - 무료쿠폰
$freedelivery_icon = get_product_icon_info('product_freedelivery_small_icon');
$_tmp_arr = array('pc'=>get_img_src($freedelivery_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($freedelivery_icon[0]['pi_img_m'],IMG_DIR_ICON));
$freedelivery_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);


// 자동적용아이콘 - 상품쿠폰
$coupon_icon = get_product_icon_info('product_coupon_small_icon');
$_tmp_arr = array('pc'=>get_img_src($coupon_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($coupon_icon[0]['pi_img_m'],IMG_DIR_ICON));
$coupon_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

// 자동적용아이콘 - 기획전
$promotion_icon = get_product_icon_info('product_promotion_small_icon');
$_tmp_arr = array('pc'=>get_img_src($promotion_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($promotion_icon[0]['pi_img_m'],IMG_DIR_ICON));
$promotion_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

// 자동적용아이콘 - 티켓상품
$product_ticket_icon = get_product_icon_info('product_ticket_small_icon');
$_tmp_arr = array('pc'=>get_img_src($product_ticket_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($product_ticket_icon[0]['pi_img_m'],IMG_DIR_ICON));
$product_ticket_icon_src = $_tmp_arr['mo'];




// 기획전 상품에 기획전 아이콘 적용을 위한 배열 추출
$que_promotion_pcode = " 
	SELECT 
		ppps.ppps_pcode
	FROM  smart_promotion_plan as pp
	INNER JOIN smart_promotion_plan_product_setup AS ppps ON ( ppps.ppps_ppuid = pp.pp_uid )
	WHERE 
		pp.pp_view = 'Y' AND 
		CURDATE() BETWEEN pp.pp_sdate AND pp.pp_edate
";
$res_promotion_pcode = _MQ_assoc($que_promotion_pcode);
foreach($res_promotion_pcode as $ppk => $ppv){
	$arr_promotion_pcode[$ppv['ppps_pcode']]++;
}




include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 스킨 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행