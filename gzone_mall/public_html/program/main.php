<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
include_once(OD_PROGRAM_ROOT.'/inc.popup.php'); // 팝업
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 상품 출력개수(사이트 스킨마다 다름)
$ReviewCount = ($siteInfo['s_main_review_limit'] > 0?$siteInfo['s_main_review_limit']:1);

// 정렬설정
$ReviewOrder = 'order by pt_rdate desc'; // 리뷰 최신순

// 리뷰상품 호출
$ReviewData = _MQ_assoc("
	select *	from
		smart_product as p left join
		smart_product_talk as t on(p.p_code = t.pt_pcode)
	where (1) and
		".(isset($siteInfo['s_main_review_view']) && $siteInfo['s_main_review_view'] == 'P'?" pt_img != '' and ":null)."
		p.p_view = 'Y' and
		t.pt_eval_point > 0 and
		t.pt_eval_point >= '".($siteInfo['s_main_review_score']*20)."' and
		t.pt_type = '상품리뷰'
	{$ReviewOrder}
	limit 0, {$ReviewCount}
");


if($siteInfo['s_main_review_porder'] == 'R'){ shuffle($ReviewData);} // 랜덤

// {LCY} : 하이앱
if( $AppUseMode === true){
	if(function_exists('is_app') == true && is_app() === false && is_mobile() === true && in_array($pn, array('','main')) && !preg_match('/totalAdmin/i', $_SERVER['REQUEST_URI']) && !preg_match('/subAdmin/i', $_SERVER['REQUEST_URI']) ){
	    include_once($_SERVER['DOCUMENT_ROOT'].'/addons/app/pop/download.php'); // 앱 다운로드 유도 팝업
	}
}

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행