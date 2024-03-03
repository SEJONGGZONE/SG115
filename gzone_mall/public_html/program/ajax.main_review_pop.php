<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 클릭시 리뷰 상세
$ReviewInfo = _MQ("
	select *	from
		smart_product as p left join
		smart_product_talk as t on(p.p_code = t.pt_pcode)
	where (1) and
		p.p_view = 'Y' and
		t.pt_eval_point > 0 and
		t.pt_eval_point >= '".($siteInfo['s_main_review_score']*20)."' and
		t.pt_type = '상품리뷰' and
		t.pt_uid = '".$_GET['_uid']."'
");

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행