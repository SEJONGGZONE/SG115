<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

if( !$ordernum){
	$ordernum = $_SESSION["session_ordernum"];//주문번호
}


if($siteInfo[s_pg_type] == 'kcp' && $_POST[ "res_cd"]) {
    include_once(OD_PROGRAM_ROOT.'/shop.order.result_kcp_m.pro.php');
    exit;
}


// 주문정보 추출
$row = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");
if($row['o_ordernum'] == ''){ error_loc_msg('/','필수 정보가 누락되었습니다. 메인페이지로 이동합니다.'); }

// 주문상품정보 추출
$sres = _MQ_assoc("
	select
		op.*,o.*, p.p_name,p.p_cpid, p.p_img_list_square , p.p_code, p.p_coupon,p.p_stock, p.p_shoppingPay, p_shoppingPay_use
	from smart_order as o
	inner join smart_order_product as op on (op.op_oordernum = o.o_ordernum )
	inner join smart_product as p on ( p.p_code=op.op_pcode )
	where op_oordernum='{$ordernum}'
	group by op_pcode
	order by op_uid
");



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행