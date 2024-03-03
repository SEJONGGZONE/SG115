<?php
//define('_SITE_SKIN_NAME', str_replace(array($_SERVER['DOCUMENT_ROOT'], OD_SITE_SKIN_DIR, OD_SITE_MSKIN_DIR, '/'), '', dirname(__FILE__))); // 현재스킨의 폴더명
$_SITE_SKIN_NAME = str_replace(array($_SERVER['DOCUMENT_ROOT'], OD_SITE_MSKIN_DIR, OD_SITE_SKIN_DIR, '/'), '', dirname(__FILE__)); // 스킨명 변수

// 스킨별 배너
$skin_banner_loc = array(
	// set_color : 색상설정(컬러피커)
	// set_img_after : 반응형이미지(이미지)
	// set_detail_text : 배너설명(textarea)
	// set_detail_info : 배너설명(text)
	// set_linkmore_btn : 링크 더보기 버튼 사용함
	// not_set_link_target : 링크 사용안함
	// not_set_view : 노출여부 사용안함
	// not_set_term : 기간설정 사용안함
	// not_set_color  : 배경색상 사용안함
	// not_set_image : 이미지 사용안함
	// not_set_title  : 타이틀 사용안함
	// not_set_detail  : 배너설명 사용안함
	// not_set_link_target : 배너 타겟 사용안함
	// not_set_banner_link  : 배너 링크 사용안함
	'mailing,not_set_view,not_set_term,not_set_link_target,not_set_banner_link' =>'메일링 로고 (1개)',
	'mobile_top_logo'=>'스킨별 :: 상단 로고 (1개)',
	'mobile_main_visual,set_img_after'=>'스킨별 :: 메인 비주얼배너 (슬라이드형 무제한)',
	'main_middle2,set_detail_text,set_linkmore_btn'=>'스킨별 :: 메인 2단배너 (무제한)',
	'main_middle1,set_img_after'=>'스킨별 :: 메인 1단배너  (무제한)',
	'main_top,set_color,set_img_after'=>'최상단 배너 (1개)',
	'mobile_product_middle,set_img_after'=>'상품상세 중간배너 (무제한)',
);

$skin_banner_loc_size = array(
	// 관리자페이지 도움말로 노출 : 배너 코드값 동일하게 맞춰야함
	'mailing,not_set_view,not_set_term,not_set_link_target,not_set_banner_link' =>'자동리사이즈',
	'mobile_top_logo'=>'최대 200 X 100 자동리사이즈',
	'mobile_main_visual,set_img_after'=>'최대 1,800 x free',
	'main_middle2,set_detail_text,set_linkmore_btn'=>'670 x free',
	'main_middle1,set_img_after'=>'1360 x free',
	'main_top,set_color,set_img_after'=>'최대 100% x free',
	'mobile_product_middle,set_img_after'=>'최대 1,360 x free',
);

$skin_mobile_banner_loc = $skin_banner_loc; // mobile

// 키에 스킨명을 추가
if(count($skin_banner_loc) > 0) {
	foreach($skin_banner_loc as $loc_k=>$loc_v) {
		$skin_banner_loc[$_SITE_SKIN_NAME.','.$loc_k] = $loc_v; // 스킨명 변수 변경
		unset($skin_banner_loc[$loc_k]);
	}
}

// 배너변수에 머지
if(empty($merge_no)) $arr_banner_loc = array_merge($arr_banner_loc, $skin_banner_loc);
else $arr_banner_loc = $skin_banner_loc;
