<?php
/*
	** ITEM BOX 공통화 처리 프로세서
	** 이곳에서 표준 상품 리스트내 모든 것들을 처리
	** 기본 $v 로 받으며 변경될 경우 $v = $? 로 변경바람
	** 추가되는 변수의 경우 $v로 하되 겹치지 않게 prefix 로 하이픈을 첨부
	** 사용법
		// $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
		$locationFile = __FILE__; // 파일설정
		include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
*/

$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
$_ov_img = get_img_src($v['p_img_list_over'], IMG_DIR_PRODUCT);

// 바로구매/장바구니 버튼 (pcode,_type,is_option)
$is_option = is_option($v['p_code']); // 옵션상품인지 체크
$order_link = "javascript:app_submit_from_list('{$v['p_code']}', 'order', {$is_option},'list');";
$cart_link = "javascript:app_submit_from_list('{$v['p_code']}', 'cart', {$is_option},'list');";
$wish_link = "wish_tran('{$v['p_code']}')";

$best_use = $best_use=='Y'?'Y':'N';
// 배송정보
$delivery_info = get_delivery_info($v['p_code']);

// 아이콘 설정
$auto_icon = '';	// 자동아이콘
$manual_icon = '';	// 수동아이콘 
$p_icon_array = explode(',', $v['p_icon']);

// 설명 
$v['p_subname'] = preg_replace("/\n/", " ",$v['p_subname']);

// 판매종료 상품 체크
$p_sale_type_chk = $v['p_stock'] >0 &&  $v['p_sale_type']=='T' && date('Y-m-d',strtotime($v['p_sale_sdate']))<=date('Y-m-d') && date('Y-m-d',strtotime($v['p_sale_edate']))<date('Y-m-d')?'Y':'N';

// 판매 준비중 상품체크
$p_sale_before_chk = $v['p_sale_sdate'] > date('Y-m-d') && $v['p_sale_type']=='T'?'Y':'N';

// 상품쿠폰 아이콘 :: 자동아이콘
$ex_coupon = explode('|' , $v['p_coupon']);
if($ex_coupon[0] && $ex_coupon[1] ){
	if($coupon_icon[0]['pi_view_type']=='img' && $coupon_icon_src){
		$auto_icon .= '<img src="'.$coupon_icon_src .'" alt="'. $coupon_icon[0]['pi_title'] .'">';
	}else{
		$auto_icon .= '<span style="background-color:'.$coupon_icon[0]['pi_bg_color'].'; color:'.$coupon_icon[0]['pi_text_color'].'; border-color:'.$coupon_icon[0]['pi_line_color'].';">'.$coupon_icon[0]['pi_title'].'</span>';
	}
}

// 무료배송 아이콘 :: 자동아이콘
if($delivery_info['status'] == '1') {
	if($freedelivery_icon[0]['pi_view_type']=='img' && $freedelivery_icon_src){
		$free_delivery_icon = '<img src="'.$freedelivery_icon_src .'" alt="'. $freedelivery_icon[0]['pi_title'] .'">';
	}else{
		$free_delivery_icon = '<span style="background-color:'.$freedelivery_icon[0]['pi_bg_color'].'; color:'.$freedelivery_icon[0]['pi_text_color'].'; border-color:'.$freedelivery_icon[0]['pi_line_color'].';">'.$freedelivery_icon[0]['pi_title'].'</span>';
	}
	$auto_icon .= $free_delivery_icon;
}


// 기획전 아이콘 :: 자동아이콘
if($arr_promotion_pcode[$v['p_code']] > 0 ) {
	if($promotion_icon[0]['pi_view_type']=='img' && $promotion_icon_src){
		$app_promotion_icon = '<img src="'.$promotion_icon_src .'" alt="'. $promotion_icon[0]['pi_title'] .'">';
	}else{
		$app_promotion_icon = '<span style="background-color:'.$promotion_icon[0]['pi_bg_color'].'; color:'.$promotion_icon[0]['pi_text_color'].'; border-color:'.$promotion_icon[0]['pi_line_color'].';">'.$promotion_icon[0]['pi_title'].'</span>';
	}
	$auto_icon .= $app_promotion_icon;
}



// 티켓 자동아이콘 :: 자동아이콘
if($v['p_type'] == 'ticket'){
	if($product_ticket_icon[0]['pi_view_type']=='img' && $product_ticket_icon_src){
		$app_product_ticket_icon = '<img src="'.$product_ticket_icon_src .'" alt="'. $product_ticket_icon[0]['pi_title'] .'">';
	}else{
		$app_product_ticket_icon = '<span style="background-color:'.$product_ticket_icon[0]['pi_bg_color'].'; color:'.$product_ticket_icon[0]['pi_text_color'].'; border-color:'.$product_ticket_icon[0]['pi_line_color'].';">'.$product_ticket_icon[0]['pi_title'].'</span>';
	}
	$auto_icon .= $app_product_ticket_icon;	
}


// 수동아이콘 
if(count($product_icon) > 0) {
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false){
			if($v0['pi_view_type']=='img'){
				$_tmp_arr = array('pc'=>get_img_src($v0['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($v0['pi_img_m'],IMG_DIR_ICON));
				$_tmp_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
				if($_tmp_src) $manual_icon .= '<img src="'.$_tmp_src.'" alt="'.$v0['pi_title'].'">';
			}else{
				$manual_icon .= '<span style="background-color:'.$v0['pi_bg_color'].'; color:'.$v0['pi_text_color'].'; border-color:'.$v0['pi_line_color'].';">'.$v0['pi_title'].'</span>';
			}
		}
	}
}


// 자동아이콘
$auto_pro_icon = ($auto_icon?'<div class="item_icon"><div class="type_auto">'.$auto_icon.'</div></div>':null);

// 수동아이콘
$manual_pro_icon = ($manual_icon?'<div class="item_icon"><div class="type_normal">'.$manual_icon.'</div></div>':null);


// 특정 페이지에서만 로드되도록, product.view. 는 제외
if( (in_array($locationFile, array('ajax.product.list.php','ajax.main_md.php','ajax.main_best_category.php','ajax.main_product.php','ajax.product_md.php','ajax.main_timesale.php')) > 0 || in_array($locationFile, array('product.list.php')) > 0 || (in_array($locationFile, array('product.view.php')) > 0 && $relation_view=='Y') ) ) {
	// 기타 정보
	$eval_cnt = number_format( 1 * get_talk_total($v['p_code'], 'eval', 'normal')); // 상품평 갯수
	$eval_cnt = rm_str($eval_cnt) > 99 ? "+99":$eval_cnt;
	$qna_cnt = number_format( 1 * get_talk_total($v['p_code'], 'qna', 'normal')); // 상품문의 갯수
	$qna_cnt = rm_str($qna_cnt) > 99 ? "+99":$qna_cnt;
	$star_persent = get_eval_average($v['p_code']); // 상품평점
	$pro_point = number_format( 1 * $v['p_price']*$v['p_point_per']/100); // 적립율
}

// 배송비 금액 출력
$pro_delivery = '';
switch($v['p_shoppingPay_use']){
	case 'Y': $pro_delivery = '개별배송 '.number_format( 1 * $delivery_info['price']).'원'; break;
	case 'N': $pro_delivery = '배송비 '.number_format( 1 * $delivery_info['price']).'원'; break;
	case 'F': $pro_delivery = $free_delivery_icon; break; //무료배송
}

// KAY :: 2023-08-25 :: 타임세일 진행전 패치
// 타임세일 상품일경우 타임세일 타이머 적용
$arr_current_time = $_edate_count = $p_timesale_type_chk = $p_timesale_before_chk= '';
if($v['p_time_sale']=='Y'){

	$_now = date('Y-m-d H:i:s');
	$_sdate_count = date('Y-m-d',strtotime($v['p_time_sale_sdate'])).' '.date('H:i:s',strtotime($v['p_time_sale_sclock']));
	$_edate_count = date('Y-m-d',strtotime($v['p_time_sale_edate'])).' '.date('H:i:s',strtotime($v['p_time_sale_eclock']));
	
	$p_timesale_type_chk = $v['p_stock'] > 0 && $_sdate_count <=$_now&& $_edate_count <=$_now?'N':'Y';
	$p_timesale_before_chk = $p_timesale_type_chk=='Y' && $_sdate_count > $_now && $_edate_count > $_now?'Y':'N';	// 타임세일 진행전

	// 타임세일 타이머를 사용하는경우
	if($p_timesale_type_chk=='Y' && $p_timesale_before_chk=='N'){
		$arr_current_time = date_diff_datetime2($_now,$_edate_count);
		$chkSecond = (rm_str($arr_today_time['hour'])*3600)+(rm_str($arr_today_time['minut'])*60)+rm_str($arr_today_time['hour']);
		$arr_current_time['hour'] = sprintf("%02d",$arr_current_time['hour']);
		$arr_current_time['minut'] = sprintf("%02d",$arr_current_time['minut']);
		$arr_current_time['second'] = sprintf("%02d",$arr_current_time['second']);
	}
}



// 판매유형이 기간판매인경우
$p_sale_text ='';
if($v['p_sale_type']=='T'){
	if(date('Y-m-d',strtotime($v['p_sale_sdate']))<=date('Y-m-d') && date('Y-m-d',strtotime($v['p_sale_edate']))<date('Y-m-d')){
		$p_sale_text = '판매종료';
	}else{
		$this_year = date('Y');
		
		$p_sale_sdate = date('Y',strtotime($v['p_sale_sdate']))==$this_year?date('n월 j일',strtotime($v['p_sale_sdate'])):date('Y년 n월 j일',strtotime($v['p_sale_sdate']));
		$p_sale_edate = date('Y',strtotime($v['p_sale_edate']))==$this_year?date('n월 j일',strtotime($v['p_sale_edate'])):date('Y년 n월 j일',strtotime($v['p_sale_edate']));

		//$p_sale_sdate = date('m월 d일',strtotime($v['p_sale_sdate']));
		//$p_sale_edate = date('m월 d일',strtotime($v['p_sale_edate']));

		// 판매기간 시작일, 종료일이 같은경우 하루만 노출
		if($v['p_sale_edate']==$v['p_sale_sdate']){
			$p_sale_text = $p_sale_sdate;
		}else{
			//$this_year = date('Y');
			$p_sale_text = $p_sale_sdate.' ~ '.$p_sale_edate; 
		}
	}
	
}else{
	// 상시판매인 경우
	$p_sale_text = '상시판매';

}

// 최종 뷰 파일 include
$incType = $incType ? $incType : 'type1';
$productIncTypeFilename = '';
switch ($incType) {
	case 'type1': default:
	$productIncTypeFilename = "product.list.inc_type1.php"; break; // 기본형
}


if($NotInclude !== true) {
	include($SkinData['skin_root'].'/'.$productIncTypeFilename); // 스킨폴더에서 해당 파일 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
}
