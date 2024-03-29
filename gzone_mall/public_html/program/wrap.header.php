<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 카테고리 정보 변수화
$AllCate = array();
$dp1cate_num = 0;
$dp1cate = _MQ_assoc(" select * from smart_category where (1) and c_view = 'Y' and c_depth = '1' order by c_idx asc, c_uid asc ");
if(count($dp1cate) <= 0) $dp1cate = array();
foreach($dp1cate as $k=>$v) {
	$AllCate[$dp1cate_num] = $v; // 1차 카테고리 등록
	$dp2cate_num = 0;
	$dp2cate[$v['c_uid']] = _MQ_assoc(" select * from smart_category where (1) and c_view = 'Y' and c_depth = 2 and c_parent = '{$v['c_uid']}' order by c_idx asc, c_uid asc ");
	if(count($dp2cate[$v['c_uid']]) <= 0) $dp2cate[$v['c_uid']] = array();
	$AllCate[$dp1cate_num]['sub'] = array();
	foreach($dp2cate[$v['c_uid']] as $kk=>$vv) {
		$AllCate[$dp1cate_num]['sub'][$dp2cate_num] = $vv; // 2차 카테고리 등록
		$dp3cate_num = 0;
		$dp3cate[$vv['c_uid']] = _MQ_assoc(" select * from `smart_category` where (1) and c_view = 'Y' and c_depth = 3 and c_parent = '{$v['c_uid']},{$vv['c_uid']}' order by c_idx asc ");
		if(count($dp3cate[$vv['c_uid']]) <= 0) $dp3cate[$vv['c_uid']] = array();
		$AllCate[$dp1cate_num]['sub'][$dp2cate_num]['sub'] = array();
		foreach($dp3cate[$vv['c_uid']] as $kkk=>$vvv) {
			$AllCate[$dp1cate_num]['sub'][$dp2cate_num]['sub'][$dp3cate_num] = $vvv;
			$dp3cate_num++;
		}
		$dp2cate_num++;
	}
	$dp1cate_num++;
}
// 편리한 검색을 위하여 카테고리 2차가공
/*
	* name값이 빈것으로 들어 갈수 있음 continue로 제어 하거나 구조 개선
	$dp1cateName['1차카테코드'] = '1차 카테네임';
	$dp2cateName['1차카테코드']['2차카테코드'] = '2차 카테네임';
	$dp3cateName['1차카테코드']['2차카테코드']['3차카테코드'] = '3차 카테네임';
*/
$dp1cateName = $dp2cateName = $dp3cateName = array();
if(count($dp1cate) > 0) {
	foreach($dp1cate as $cnk=>$cnv) {
		if($cnv['c_view'] == 'N' || $cnv['c_name'] == '') continue;
		$dp1cateName[$cnv['c_uid']] = $cnv['c_name'];
	}
}
if(count($dp2cate) > 0) {
	foreach($dp2cate as $cnk=>$cnv) {
		$dp2cateName[$cnk] = array();
		if(count($cnv) <= 0) $cnv = array();
		foreach($cnv as $ccnk=>$ccnv) {
			if($ccnv['c_view'] == 'N' || $ccnv['c_name'] == '') continue;
			$dp2cateName[$cnk][$ccnv['c_uid']] = $ccnv['c_name'];
		}
	}
}
if(count($dp3cate) > 0) {
	foreach($dp3cate as $cnk=>$cnv) {
		if(count($cnv) <= 0) $cnv = array();
		foreach($cnv as $ccnk=>$ccnv) {
			if($ccnv['c_view'] == 'N' || $ccnv['c_name'] == '') continue;
			$pkey = explode(',', $ccnv['c_parent']);
			if(!is_array($dp3cateName[$pkey[0]])) $dp3cateName[$pkey[0]] = array();
			if(!is_array($dp3cateName[$pkey[0]][$pkey[1]])) $dp3cateName[$pkey[0]][$pkey[1]] = array();
			$dp3cateName[$pkey[0]][$pkey[1]][$ccnv['c_uid']] = $ccnv['c_name'];
		}
	}
}


// 현제 페이지의 카테고리 정보를 찾는다.
$ActiveCate = array();
if(in_array($pn, array('product.list', 'product.view'))) {
	if(isset($cuid) && $cuid != '') {
		$ct_info = info_category($cuid);
		$ActiveCate['cuid'] = array(
			$ct_info['depth1_cuid'],
			$ct_info['depth2_cuid'],
			$ct_info['depth3_cuid']
		);
		$ActiveCate['cname'] = array(
			$ct_info['depth1_cname'],
			$ct_info['depth2_cname'],
			$ct_info['depth3_cname']
		);
	}
	else if(isset($pcode) && $pcode != '') {
		$cp_info = get_pro_depth_info('', $pcode);
		$cp_info_cuid = (count($cp_info[0]) > 0?array_values($cp_info[0]):array());
		$cp_info_cname = (count($cp_info[1]) > 0?array_values($cp_info[1]):array());
		$ActiveCate['cuid'] = $cp_info_cuid;
		$ActiveCate['cname'] = $cp_info_cname;
	}
}
if(count($ActiveCate) > 0 && count($ActiveCate['cuid']) > 0) $ActiveCate['cuid'] = array_filter($ActiveCate['cuid']); // 빈값제거
if(count($ActiveCate) > 0 && count($ActiveCate['cname']) > 0) $ActiveCate['cname'] = array_filter($ActiveCate['cname']); // 빈값제거

// 카트 상품 수
$cart_cnt = get_cart_cnt();


// KAY :: 2022-08-03 :: 슬라이드에서 게시판 메뉴 노출
// -- 고객센터 메뉴 추출 --------------------------------------------------
$service_bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'service' and bi_view = 'Y' order by bi_view_idx asc ");

$SlideServiceArr = $SlideServiceNormalMenu =  $SlideServiceboardMenu  = array();
// -- 게시판메뉴
foreach($service_bbsList as $sk=>$sv) {
	$skinNameView = $skinNameViewVal === true ? '('.$sv['bi_skin'].')' : null;
	$SlideServiceboardMenu[$sv['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$sv['bi_uid'],
		'title'=> $sv['bi_name']
	);
}
// -- 일반메뉴
$SlideServiceNormalMenu = array(
	'자주 묻는 질문'=>array(
		'link'=>'/?pn=faq.list',
		'title'=> '자주 묻는 질문'
	),
);
// -- 미확인 입금자 리스트
if($siteInfo['s_online_notice_use'] == 'Y'){
	$SlideServiceNormalMenu['미확인 입금자'] = array(
		'link'=>'/?pn=service.deposit.list',
		'title'=> '미확인 입금자'
	);
}
$SlideServiceArr = array_merge($SlideServiceboardMenu, $SlideServiceNormalMenu );
// -- 고객센터 메뉴 추출 --------------------------------------------------

// -- 커뮤니티 메뉴 추출 --------------------------------------------------
$comm_bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'community' and bi_view = 'Y' order by bi_view_idx asc ");
// 탑네비 메뉴 리스트
$SlideCommArr = $SlideCommNormalMenu =  $SlideCommMenu  = array();
// -- 게시판메뉴
foreach($comm_bbsList as $ck=>$cv) {
	$skinNameView = $skinNameViewVal === true ? '('.$cv['bi_skin'].')' : null;
	$SlideCommMenu[$cv['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$cv['bi_uid'],
		'title'=> $cv['bi_name']
	);
}
// -- 일반메뉴
$SlideCommNormalMenu = array(
	'상품리뷰'=>array(
		'link'=>'/?pn=service.eval.list',
		'title'=> '상품리뷰'
	),
	'상품문의'=>array(
		'link'=>'/?pn=service.qna.list',
		'title'=> '상품문의'
	),
);
$SlideCommArr = array_merge($SlideCommNormalMenu, $SlideCommMenu );
// -- 커뮤니티 메뉴 추출 --------------------------------------------------


// KAY :: 2022-08-03 :: 슬라이드에서 게시판 메뉴 노출
// -- 이벤트 게시판 적용된 게시판을 가져온다.
$event_bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_list_type = 'event' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$SlideEventArr = $SlideEventNormalMenu1 = $SlideEventNormalMenu2 =  $SlideEventboardMenu  = array();

// -- 게시판메뉴
foreach($event_bbsList as $ek=>$ev) {
	$skinNameView = $skinNameViewVal === true ? '('.$ev['bi_skin'].')' : null;
	$SlideEventboardMenu[$ev['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$ev['bi_uid'],
		'uid'=>$ev['bi_uid'],
		'title'=> $ev['bi_name']
	);
}

// -- 기획전
if($siteInfo['s_promotion_plan_view']=='Y'){
	$SlideEventNormalMenu1[$siteInfo['s_promotion_plan_title']] = array(
		'link'=>'/?pn=product.promotion_list',
		'title'=> $siteInfo['s_promotion_plan_title']
	);
}
// -- 출석체크
if($siteInfo['s_promotion_attend_view']=='Y'){
	$SlideEventNormalMenu1['출석체크'] = array(
		'link'=>'/?pn=promotion.attend',
		'title'=> '출석체크'
	);
}

// -- 상품리뷰, 상품문의
$SlideEventNormalMenu2 = array(
	'상품리뷰'=>array(
		'link'=>'/?pn=service.eval.list',
		'title'=> '상품리뷰'
	),
	'상품문의'=>array(
		'link'=>'/?pn=service.qna.list',
		'title'=> '상품문의'
	),
);

$SlideEventArr = array_merge($SlideEventNormalMenu1,$SlideEventNormalMenu2,$SlideEventboardMenu );


// 로그인 후 이동 위치
if(isset($_rurl) && $_rurl != '') $_rurl = (preg_match('/login|join|find|pro.php/i', $_rurl)?'':$_rurl);
$_rurl = (isset($_rurl) && $_rurl != ''?$_rurl:(preg_match('/login|join|find|pro.php/i', $_SERVER['REQUEST_URI'])?'':$_SERVER['REQUEST_URI']));

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행}