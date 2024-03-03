<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
if($NotInclude !== true) actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



if(!$pcode) {
	if($code) $pcode = $code;
	else error_loc_msg("/","잘못된 접근입니다.");
}

// 스킨정보
$SkinInfo = SkinInfo('all', 'auto', 'cookie');

// - 임시 옵션 삭제 ---
if($_COOKIE["AuthShopCOOKIEID"]) _MQ_noreturn("delete from smart_product_tmpoption where pto_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");


// 상품정보 추출
$p_info = get_product_info($pcode);
if(!$p_info['p_name']) error_loc_msg("/","잘못된 상품정보입니다.");

// - 텍스트 정보 추출 ---
$p_info = array_merge($p_info , _text_info_extraction( "smart_product" , $pcode ));

// - JJC : 입점업체 정보 추출 - 2019-01-02 : //
$p_com_info = _company_info( $p_info['p_cpid'] );
$product_company_name = $p_com_info['cp_name']; // 입점업체명
$product_company_tel = $p_com_info['cp_tel']; // 입점 전화
$product_company_fax = $p_com_info['cp_fax']; // 입점 팩스
$product_company_homepage = $p_com_info['cp_homepage']; // 입점 홈페이지
$product_company_addr = $p_com_info['cp_address']; // 입점 주소
$product_company_charge = $p_com_info['cp_charge']; // 입점 담당자명
$product_company_tel2 = $p_com_info['cp_tel2']; // 입점 담당자 핸드폰
$product_company_email = $p_com_info['cp_email']; // 입점 담당자이메일

$product_company_delivery_company = $p_com_info['cp_delivery_company']; // 입점 지정택배사
$product_company_delivery_date = $p_com_info['cp_delivery_date']; // 입점 평균배송기간
$product_company_delivery_return_addr = $p_com_info['cp_delivery_return_addr']; // 입점 반송주소
// - JJC : 입점업체 정보 추출 - 2019-01-02 : //


// 티켓에서만 노출되는 사용처
$_com_locname = '';
if( $p_info['p_type'] == 'ticket'){
	$_com_locname = $p_info['p_com_locname'] != '' ? $p_info['p_com_locname'] : $p_com_info['cp_name'];
}



// 숨김 상품 체크
if($p_info['p_view'] == "N") error_loc_msg("/","판매종료된 상품입니다.");

// 모바일 상품상세 설명
if(is_mobile() && $p_info['p_use_content'] <> 'Y') $p_info['p_content'] = $p_info['p_content_m'];

// 판매기간에 따른 처리
$app_product_sale_use = false;
$app_product_sale_date_view = ''; // 남은기간 표기 (ex: 2022-12-05 ~ 2022-12-30)
$app_product_sale_day_view = ''; // 남은기간 표기 (ex: 25일 남음, 또는 오늘마감)
$app_product_sale_notice = ''; // 안내문구
if( $p_info['p_sale_type'] == 'T'){ // 기간

	$diff_day = getDateDiffDay(date('Y-m-d'),$p_info['p_sale_edate']);

	if( $diff_day == 0 && $p_info['p_sale_edate'] == date('Y-m-d') ){ $app_product_sale_day_view = '오늘마감';}
	else if($p_info['p_sale_sdate'] > date('Y-m-d') ){
		 $app_product_sale_day_view = '판매 준비중';
		 $app_product_sale_notice = '판매 준비중인 상품입니다';
	}
	else if($p_info['p_sale_edate'] < date('Y-m-d') ){
		 $app_product_sale_day_view = '판매 종료';
		 $app_product_sale_notice = '판매 종료된 상품입니다';
	}
	else{ $app_product_sale_day_view = $diff_day."일 남음"; }

	// 시작일이랑 종료일이 같은경우 하루만 노출
	if($p_info['p_sale_sdate']==$p_info['p_sale_edate']){
		$app_product_sale_date_view = $p_info['p_sale_sdate'];
	}else{
		$app_product_sale_date_view = $p_info['p_sale_sdate'].' ~ '.$p_info['p_sale_edate'];
	}


	if( $p_info['p_sale_sdate'] <= date('Y-m-d') && $p_info['p_sale_edate'] >= date('Y-m-d')){
		$app_product_sale_use = true;
	}
}
else if($p_info['p_sale_type'] == 'A'){ // 상시
	$app_product_sale_use = true; // 판매가능
}

// 상품설정 잘못되었을 경우
else error_loc_msg("/","판매종료된 상품입니다.");



// 유효기간 체크
$app_product_expire_use = false;
$app_product_expire_text = '';
// 만료일 지정
if($p_info['p_expire_type']=='day'){
	$app_product_expire_use = true;
	if($p_info['p_expire_day']>0){
		$app_product_expire_text = '발급일로부터 '.$p_info['p_expire_day'].'일까지';
	}else{
		$app_product_expire_text = '당일사용';
	}

}else if($p_info['p_expire_type']=='date'){ // 만료날짜 지정
	$app_product_expire_use = true;
	$app_product_expire_date = date('w',strtotime($p_info['p_expire_date']));

	$app_product_expire_text = date('Y-m-d',strtotime($p_info['p_expire_date'])).'('.$arr_day_week_short[$app_product_expire_date].')'.'까지';
}else{
	//제한없음
	$app_product_expire_text = '제한없음';
}



// 큰이미지
$main_img = get_img_src($p_info['p_img_b1']);
$pro_img = array();
for($i=1; $i<=10; $i++){
	$pro_img[] = $p_info['p_img_b'.$i];
}
$pro_img = array_values(array_filter($pro_img));



// 추가 노출항목 설정 추출
$ex_display_add = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add'])));	// 정상가, 적립금, 상품쿠폰, 회원혜택
$ex_display_add_info = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add_info'])));	// 브랜드, 제조사, 원산지, 배송정보, 부가 상품명, 옵션재고량

// -- 기본 정보 -----
$pro_name = stripslashes($p_info['p_name']); // 대표 상품명

// 노출항목 설정에 따라 노출여부 결정
$pro_screenprice = in_array('screenPrice', $ex_display_add)? number_format($p_info['p_screenPrice']):''; // 정상가


// 부가상품명 설정에 따라 노출여부 결정

$pro_maker = in_array('maker', $ex_display_add_info)?$p_info['p_maker']:'';  // 제조사
$pro_orgin = in_array('orgin', $ex_display_add_info)?$p_info['p_orgin']:'';  // 제조사

$pro_subname = in_array('subname', $ex_display_add_info)?nl2br(stripslashes($p_info['p_subname'])):''; // 부가 상품명
$isOptionStock = in_array('optionStock', $ex_display_add_info)?true:false; // 옵션 재고량


$pro_price = number_format($p_info['p_price']); // 판매가
$pro_point = number_format($p_info['p_price']*$p_info['p_point_per']/100); // 적립율
$pro_point_per = number_format($p_info['p_point_per'],1);



// -- 기본 정보 -----



// 브랜드정보 추출
if($p_info['p_brand']){
	$brand_info = _MQ(" select * from smart_brand where c_uid = '". $p_info['p_brand'] ."' ");
	$pro_brand_name = $brand_info['c_name'];
	$pro_brand_uid = $brand_info['c_uid'];
}



// 배송비 정책 추출
$pro_delivery_info = get_delivery_info($p_info['p_code']);
// 2019-01-04 SSJ :: 배송비가 0원일경우 무료배송처리
if($pro_delivery_info['price'] <= 0) $p_info['p_shoppingPay_use'] = 'F';


// LCY : 2022-12-21 : 티켓기능 -- 티켓상품일경우 네이버페이 강제 미사용처리
if($p_info['p_type'] == 'ticket'){
	$p_info['npay_use'] = 'N';

	// 티켓이고 장소이름이 비 노출일 경우 업체

}



// 구매제한 - 1회 최대구매개수, 중복구매 체크
$app_buy_limit_auth = false;
if( $p_info['p_buy_limit'] > 0 || $p_info['p_duplicate_use'] == 'N' ){ $app_buy_limit_auth = true; }


// 상품 아이콘정보.
$product_icon = get_product_icon_info('product_name_small_icon');

// 자동적용아이콘 - 무료배송
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


// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
$ex_coupon = explode('|' , $p_info['p_coupon']);
if(($ex_coupon[1] == 'price' || $ex_coupon[1] == 'per') && $ex_coupon[0] <> ''){
	if($coupon_icon[0]['pi_view_type']=='img'){
		// 이미지형태인경우
		if($coupon_icon_src){
			$tmpicon .= '<img src="'.$coupon_icon_src .'" alt="'. $coupon_icon[0]['pi_title'] .'">';  // 상품쿠폰 아이콘
		}
	}else{
		// 텍스트형태인경우
		$tmpicon .= '<span style="background-color:'.$coupon_icon[0]['pi_bg_color'].'; color:'.$coupon_icon[0]['pi_text_color'].'; border-color:'.$coupon_icon[0]['pi_line_color'].';">'.$coupon_icon[0]['pi_title'].'</span>';  // 상품쿠폰 아이콘
	}

	$ex_coupon['name'] = stripslashes($ex_coupon[0]);
	$ex_coupon['price'] = rm_comma($ex_coupon[2]);
	$ex_coupon['per'] = rm_comma($ex_coupon[3]);
	$ex_coupon['max'] = rm_comma($ex_coupon[4]);
}


// 무료배송 - 아이콘
if($pro_delivery_info['status'] == '1'){
	if($freedelivery_icon[0]['pi_view_type']=='img' && $freedelivery_icon_src){
		// 이미지형태인경우
		$tmpicon .= '<img src="'.$freedelivery_icon_src .'" alt="'. $freedelivery_icon[0]['pi_title'] .'">';
	}else{
		// 텍스트형태인경우
		$tmpicon .= '<span style="background-color:'.$freedelivery_icon[0]['pi_bg_color'].'; color:'.$freedelivery_icon[0]['pi_text_color'].'; border-color:'.$freedelivery_icon[0]['pi_line_color'].';">'.$freedelivery_icon[0]['pi_title'].'</span>';
	}
}// 무료배송 아이콘


// 기획전 상품에 기획전 아이콘
$que_promotion_pcode = "
	SELECT
		count(*) as cnt
	FROM  smart_promotion_plan as pp
	INNER JOIN smart_promotion_plan_product_setup AS ppps ON ( ppps.ppps_ppuid = pp.pp_uid )
	INNER JOIN smart_product AS p ON ( p.p_code = ppps.ppps_pcode AND p.p_code = '". $pcode ."' )
	WHERE
		pp.pp_view = 'Y' AND
		CURDATE() BETWEEN pp.pp_sdate AND pp.pp_edate
";
$res_promotion_pcode = _MQ_result($que_promotion_pcode);
if($res_promotion_pcode > 0 ) {
	if($promotion_icon[0]['pi_view_type']=='img' && $promotion_icon_src){
		// 이미지형태인경우
		$tmpicon .= '<img src="'.$promotion_icon_src .'" alt="2022-12-07'. $promotion_icon[0]['pi_title'] .'">';
	}else{
		// 텍스트 형태인경우
		$tmpicon .= '<span style="background-color:'.$promotion_icon[0]['pi_bg_color'].'; color:'.$promotion_icon[0]['pi_text_color'].'; border-color:'.$promotion_icon[0]['pi_line_color'].';">'.$promotion_icon[0]['pi_title'].'</span>';
	}
}

// 티켓상품 - 아이콘
if($p_info['p_type'] == 'ticket'){
	if($product_ticket_icon[0]['pi_view_type']=='img' && $product_ticket_icon_src){
		// 이미지형태인경우
		$tmpicon .= '<img src="'.$product_ticket_icon_src .'" alt="'. $product_ticket_icon[0]['pi_title'] .'">';
	}else{
		// 텍스트형태인경우
		$tmpicon .= '<span style="background-color:'.$product_ticket_icon[0]['pi_bg_color'].'; color:'.$product_ticket_icon[0]['pi_text_color'].'; border-color:'.$product_ticket_icon[0]['pi_line_color'].';">'.$product_ticket_icon[0]['pi_title'].'</span>';
	}
}// 티켓상품 아이콘


// 아이콘 설정
$p_icon_array = explode(",",$p_info['p_icon']);
if(count($product_icon) > 0) {
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false){
			if($v0['pi_view_type']=='img'){
				// 수동아이콘이 이미지 형태인경우
				$_tmp_arr = array('pc'=>get_img_src($v0['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($v0['pi_img_m'],IMG_DIR_ICON));
				$_tmp_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
				if($_tmp_src) $tmpicon .= '<img src="'.$_tmp_src.'" alt="'.$v0['pi_title'].'">';
			}else{
				// 수동아이콘이 텍스트 형태인경우
				$tmpicon .= '<span style="background-color:'.$v0['pi_bg_color'].'; color:'.$v0['pi_text_color'].'; border-color:'.$v0['pi_line_color'].';">'.$v0['pi_title'].'</span>';
			}
		}
	}
}
$app_pro_icon = ($tmpicon ? $tmpicon : '');



// 상품평 갯수
$eval_cnt = number_format(get_talk_total($p_info['p_code'],"eval","normal"));
if( rm_str($eval_cnt) > 99){ $eval_cnt = "+99"; }

// 상품문의 갯수
$qna_cnt = number_format(get_talk_total($p_info['p_code'],"qna","normal"));
if( rm_str($qna_cnt) > 99){ $qna_cnt = "+99"; }

// 상품평점
$star_persent = get_eval_average($p_info['p_code']);





// 상품 해시테그 추출
$pro_hashtag = array_filter(array_unique(explode(',', $p_info['p_hashtag'])));


if($SkinData['device']=='pc'){
	// [PC]공통 : 상품상세 중간 배너 (1050 x free)
	$ProductMiddle = info_banner($_skin.',site_product_middle', 999999, 'data');
}else{
	// [MOBILE]공통 : 상품상세 중간 배너 (940 x free, 1개)
	$ProductMiddle = info_banner($_skin.',mobile_product_middle,set_img_after', 999999, 'data');
}



// 관련상품 추출 - 상세페이지 노출설정에따라
$relation = array();

if($siteInfo['s_display_relation_mo_use'] == 'Y'){
	$relation = ProductRelation($p_info,'50');
}



// 타임세일 상품일경우 타임세일 타이머 적용
$arr_current_time = $_edate_count = $p_timesale_type_chk=$p_timesale_before_chk= '';
if($p_info['p_time_sale']=='Y'){

	$_now = date('Y-m-d H:i:s');
	$_edate_count = date('Y-m-d',strtotime($p_info['p_time_sale_edate'])).' '.date('H:i:s',strtotime($p_info['p_time_sale_eclock']));
	$_sdate_count = date('Y-m-d',strtotime($p_info['p_time_sale_sdate'])).' '.date('H:i:s',strtotime($p_info['p_time_sale_sclock']));

	$p_timesale_type_chk = $_sdate_count <=$_now&& $_edate_count <=$_now?'N':'Y';
	$p_timesale_before_chk = $_sdate_count > $_now && $_edate_count >$_now?'Y':'N';

	if($p_timesale_type_chk=='Y' && $p_timesale_before_chk=='N'){
		$arr_current_time = date_diff_datetime2($_now,$_edate_count);

		$chkSecond = (rm_str($arr_today_time['hour'])*3600)+(rm_str($arr_today_time['minut'])*60)+rm_str($arr_today_time['hour']);
		$arr_current_time['hour'] = $arr_current_time['hour']<10?'0'.$arr_current_time['hour']:$arr_current_time['hour'];
		$arr_current_time['minut'] = $arr_current_time['minut']<10?'0'.$arr_current_time['minut']:$arr_current_time['minut'];
		$arr_current_time['second'] = $arr_current_time['second']<10?'0'.$arr_current_time['second']:$arr_current_time['second'];
	}
}


// 2018-07-27 SSJ :: 재고 체크
$isSoldOut = false;
if($p_info['p_stock'] < 1){ $isSoldOut = true; }
else if($p_info['p_soldout_chk'] == 'Y'){ $isSoldOut = true; }


$isTimeSaleOut = $isTimeSaleBefore =  false;
if($p_info['p_time_sale']=='Y' && $p_timesale_type_chk == 'N'){ $isTimeSaleOut = true; }
if($p_info['p_time_sale']=='Y' && $p_timesale_before_chk == 'Y'){ $isTimeSaleBefore = true; }



// 옵션정보 불러오기
$options = array(); $add_options = array();
if($p_info['p_option_type_chk'] <> 'nooption'){
	// 필수옵션
	$option_que = " select po_uid , po_poptionname, po_cnt,po_poptionprice , po_color_type , po_color_name  from smart_product_option where po_view = 'Y' and po_pcode='" . $pcode . "' and po_depth='1' and po_poptionname != '' ORDER BY po_sort , po_uid ASC ";
	$options = _MQ_assoc($option_que);

	// 추가옵션은 배송 상품만 가능
	if($p_info['p_type'] == 'delivery'){
		// 추가옵션
		$add_option_que = "select * from smart_product_addoption where pao_pcode='". $pcode ."' and pao_depth='1' and pao_view = 'Y' and pao_poptionname != '' order by pao_sort asc, pao_uid asc ";

		$add_options = _MQ_assoc($add_option_que);
	    foreach($add_options as $k=>$v){
	        //$add_sub_options = _MQ_assoc("select * from smart_product_addoption where pao_pcode='".$pcode."' and pao_depth='2' and pao_parent='".$v['pao_uid']."' and pao_poptionname != '' order by pao_sort asc, pao_uid asc  ");
	        // 2019-04-01 SSJ :: 추가옵션의 숨김옵션 제외 처리
	        $add_sub_options = _MQ_assoc("select * from smart_product_addoption where pao_pcode='".$pcode."' and pao_depth='2' and pao_parent='".$v['pao_uid']."' and pao_view = 'Y' and pao_poptionname != '' order by pao_sort asc, pao_uid asc  ");
	        if(count($add_sub_options)>0) $add_options[$k]['add_sub_options'] = $add_sub_options;
	        else unset($add_options[$k]); // 2차 옵션이 없으면 비노출
	    }
	}
}



// 티켓상품은 정보제공고시가 미사용 처리
$notify_info = array();
// 정보제공고시 추출
$notify_info = _MQ_assoc("select * from smart_product_req_info where pri_value != '' and pri_pcode='".$pcode."' order by pri_uid asc");




// 배송정보 추출
$com_info = _company_info($p_info['p_cpid']);
if($com_info['cp_delivery_use'] == "Y") {
	$del_company = $com_info['cp_delivery_company'];
	$del_date = $com_info['cp_delivery_date'];
	$del_complain_price = $com_info['cp_delivery_complain_price'];
	$del_return_addr = $com_info['cp_delivery_return_addr'];
	$complain_ok = htmlspecialchars_decode($com_info['cp_complain_ok']);
	$complain_fail = htmlspecialchars_decode($com_info['cp_complain_fail']);
} else {
	$del_company = $siteInfo['s_del_company'];
	$del_date = $siteInfo['s_del_date'];
	$del_complain_price = $siteInfo['s_del_complain_price'];
	$del_return_addr = $siteInfo['s_del_return_addr'];
	$complain_ok = htmlspecialchars_decode($siteInfo['s_complain_ok']);
	$complain_fail = htmlspecialchars_decode($siteInfo['s_complain_fail']);
}

// {{{회원등급혜택}}} or getGroupSetPer 검색
/*
	* 회원할인 이나 적립은 추가로 발생된다.
	* 상품 할인전 금액 기준으로 적립금 추가 적용
	* 할인 또는 적립금액을 가져오며 합산된 급액은 가져오지 않는다.
	- 금액 할인율 getGroupSetPer($p_info['p_price'],'price',$pcode) 형태로 사용
	- 추가 적립률 getGroupSetPer($p_info['p_price'],'point',$pcode) 형태로 사용

*/
$groupSetUse = false;
if(is_login() == true && $p_info['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
	if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
		$groupSetUse = true;
	}
}
// {{{회원등급혜택}}}


// {{{LCY무료배송이벤트}}} -- 무료배송 이벤트 조건에 속할경우 true, 그렇지 않을경우 false
$freeEventChk = PromotionEventDeliveryChk('view');
$freeEventInfo = getPromotionEventDelivery();
// {{{LCY무료배송이벤트}}}

// JJC : 2019-05-15 : 판매자 정보
//      입점기능일 경우
if($SubAdminMode === true){
    $app_adshop = $com_info['cp_name'];//상호명
    $app_glbtel = $com_info['cp_tel'];//대표전화
    $app_ceo_name = $com_info['cp_ceoname'];//대표자
    $app_fax = $com_info['cp_fax'];//팩스전화
    $app_company_num = $com_info['cp_number'];//사업자등록번호
    $app_ademail = $com_info['cp_email'];//대표 이메일
    $app_company_snum = $com_info['cp_snumber'];//통신판매업번호----------------------
    $app_company_addr = $com_info['cp_address'];//사업장소재지
}
//      입점기능이 아닐 경우
else {
	$app_adshop = $siteInfo['s_company_name'];//상호명
    $app_glbtel = $siteInfo['s_glbtel'];//대표전화
    $app_ceo_name = $siteInfo['s_ceo_name'];//대표자
    $app_fax = $siteInfo['s_fax'];//팩스전화
    $app_company_num = $siteInfo['s_company_num'];//사업자등록번호
    $app_ademail = $siteInfo['s_ademail'];//대표 이메일
    $app_company_snum = $siteInfo['s_company_snum'];//통신판매업번호
    $app_company_addr = $siteInfo['s_company_addr'];//사업장소재지
}


// 2020-03-11 SSJ :: 이용안내 기본값 설정
if(count($arrProGuideType)>0){
	foreach($arrProGuideType as $_guide_key=>$_guide_title){
		if($p_info['p_guide_type_'.$_guide_key] == ''){
			$p_info['p_guide_type_'.$_guide_key] = 'list';
			$guide_r = _MQ(" select g_uid from smart_product_guide where (1) and g_type = '". $_guide_key ."' and g_user in ('_MASTER_', '". $p_info['p_cpid'] ."') order by g_default asc limit 1  ");
			$p_info['p_guide_uid_'.$_guide_key] =$guide_r['g_uid'];
		}
	}
}




// 최종 상품 정보 노출을 일괄 체크
$view_product_info = false;
if( $pro_maker != '' || $pro_orgin || ($v == 'deliveryInfo' && $p_info['p_type'] == 'delivery')
	|| ($p_info['p_com_juso'] != '' && $p_info['p_type'] == 'ticket')
	|| $app_buy_limit_auth === true
	|| $p_info['p_sale_type'] == 'T'
	|| ($p_info['p_dateoption_use'] == 'N' && $p_info['p_type']=='ticket' )
){ $view_product_info = true; }


if($NotInclude !== true) {
	include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
}