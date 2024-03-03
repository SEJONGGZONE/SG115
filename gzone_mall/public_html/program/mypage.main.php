<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }


# 기본처리
member_chk();
if(is_login()) $indr = $mem_info; // 개인정보 추출


// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치 -- 불필요 변수 삭제 및 PG미결제건 비노출 처리 ( and o_canceled='N') (+ 가상계좌 미발급건은 카운팅 제외)
// SSJ : 주문/결제 통합 패치 : 2021-02-24 : arr_order_payment_type 변수적용 
$s_query = " from smart_order as o where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N' ) and  if( o_paymethod = 'virtual',  ( select count(*) as cnt from smart_order_onlinelog where ool_ordernum = o.o_ordernum ) , 1) > 0  ";
$que = "
	select
		o.* ,
		(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
		(
			select
				p.p_name
			from smart_order_product as op
			inner join smart_product  as p on ( p.p_code=op.op_pcode )
			where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
		) as p_name
	$s_query
	ORDER BY o_rdate desc limit 0 , 5
";
$res = _MQ_assoc($que);



# 나의 찜한 상품 추출
$listmaxcount = 7;
$pwque = "
	select
		pw.*, p.* ,
		(select count(*) from smart_product_wish as pw2 where pw2.pw_pcode=pw.pw_pcode) as cnt_product_wish
	from smart_product_wish as pw
	inner join smart_product as p on ( p.p_code=pw.pw_pcode )
	where pw.pw_inid='". get_userid() ."'
	order by pw_uid desc limit 0 , $listmaxcount
";
$myWishList = _MQ_assoc($pwque);




# 데이터 조회
$TotalCountReady = _MQ_result(" select count(*) as cnt from smart_individual_coupon where (1) and coup_inid='".get_userid()."' and coup_use = 'N' "); // 사용가능 쿠폰
$TotalCountWait = _MQ_result(" select count(*) as cnt from smart_individual_coupon where (1) and coup_inid='".get_userid()."' and coup_use = 'W' ");	// 사용대기 쿠폰

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행