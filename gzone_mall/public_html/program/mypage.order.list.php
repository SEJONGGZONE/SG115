<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
member_chk();
if(is_login()) $indr = $mem_info; // 개인정보 추출


# 기간변수
$today = date('Y-m-d');
$week = date('Y-m-d',strtotime('-7 day'));
$month1 = date('Y-m-d',strtotime('-1 month'));
$month3 = date('Y-m-d',strtotime('-3 month'));
$month6 = date('Y-m-d',strtotime('-6 month'));
$year = date('Y-m-d',strtotime('-1 year'));


$type_sqeury = "";
if( $order_type == 'delivery'){
	$type_sqeury = " and o_order_type in('both','delivery')  "; 
	$pro_type_squery = "and op_ptype='delivery' ";
}
else if($order_type == 'ticket'){
	$type_sqeury = " and o_order_type in('both','ticket')  "; 
	$pro_type_squery = "and op_ptype='ticket' ";
}

# 주문통계 - 결제상태별
$order_status['결제대기'] = $order_status['결제완료'] = $order_status['배송중'] = $order_status['배송완료'] = $order_status['주문취소'] = 0;

// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- PG결제에서는 결제가 안된건 카운팅하지 않는다. (+ 가상계좌 미발급건은 카운팅 제외)
$r = _MQ_assoc(" select * from smart_order_product as op left join smart_order as o on (o.o_ordernum=op.op_oordernum) where o.o_mid='".get_userid()."' and !(o.o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o.o_paystatus ='N') and o_rdate >= '".$year." 00:00:00' and  if( o_paymethod = 'virtual',  ( select count(*) as cnt from smart_order_onlinelog where ool_ordernum = o.o_ordernum ) , 1) > 0  ".$type_sqeury.$pro_type_squery);


// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- $v['o_status'] => $v['op_sendstaus'] 및 미결제/주문취소건 처리
foreach($r as $k => $v){
	if($order_type=='delivery'){


		if($v['o_paystatus'] == 'Y'){
			if($v['op_sendstatus']=='배송준비') $order_status['배송준비']++;
			else if($v['op_sendstatus']=='배송중') $order_status['배송중']++;
			else if($v['op_sendstatus']=='배송대기') $order_status['결제완료']++;
			else $order_status[$v['op_sendstatus']]++;
		}
		else{
			$order_status[$v['o_status']]++;
		}
	}

	if($order_type=='ticket'){
		if($v['o_paystatus'] == 'Y'){
			if($v['op_sendstatus']=='발급완료' || $v['op_sendstatus']=='발송완료')$ticket_order_status['발급완료']++;
			else if($v['op_sendstatus']=='배송대기')$ticket_order_status['결제완료']++;
			else if($v['op_sendstatus']=='결제대기')$ticket_order_status['결제대기']++;
			else$ticket_order_status[$v['op_sendstatus']]++; 
		}
		else{
			$ticket_order_status[$v['o_status']]++;
		}		
		$ticket_ifno = _MQ("select count(*) as cnt from smart_order_product_ticket where opt_oordernum = '".$v['op_oordernum']."' and opt_opuid = '".$v['op_uid']."' and opt_status ='만료' ");
		if($ticket_ifno['cnt']>0){
			$ticket_order_status['기간만료']++;
		}
		$ticket_use_ifno = _MQ("select count(*) as cnt from smart_order_product_ticket where opt_oordernum = '".$v['op_oordernum']."' and opt_opuid = '".$v['op_uid']."' and opt_status ='사용' ");
		if($ticket_use_ifno['cnt']>0){
			$ticket_order_status['사용완료']++;
		}
	}
}

# 기간검색
if(!$date && !$o_rdate_end && !$o_rdate_start) $date = "all";
if($date == "all") {$o_rdate_end = '';$o_rdate_start = '';}
else if($date == "today") {$o_rdate_end = $today;$o_rdate_start = $today;}
else if($date == "week")	{$o_rdate_end = $today;$o_rdate_start = $week;}
else if($date == "month1") {$o_rdate_end = $today;$o_rdate_start = $month1;}
else if($date == "month3") {$o_rdate_end = $today;$o_rdate_start = $month3;}

// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치 
else if($date == 'status'){ $o_rdate_end = ''; $o_rdate_start = $year;}

if(!$date){
	if($o_rdate_end == $today && $o_rdate_start == $today) $date = 'today';
	else if($o_rdate_end == $today && $o_rdate_start == $week) $date = 'week';
	else if($o_rdate_end == $today && $o_rdate_start == $month1) $date = 'month1';
	else if($o_rdate_end == $today && $o_rdate_start == $month3) $date = 'month3';
	else if($o_rdate_end == $today && $o_rdate_start == $year) $date = 'all';
}

// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- PG결제에서는 결제가 안된건 카운팅하지 않는다.  (+ 가상계좌 미발급건은 카운팅 제외)
$s_query = " from smart_order as o where o_mid='".get_userid()."' and !(o_paymethod in ('". implode("','", $arr_order_payment_type) ."') and o_paystatus ='N') and  if( o_paymethod = 'virtual',  ( select count(*) as cnt from smart_order_onlinelog where ool_ordernum = o.o_ordernum ) , 1) > 0 ".$type_sqeury; // 검색 체크

if($o_rdate_start) $s_query .= " and o_rdate >= '".$o_rdate_start." 00:00:00' ";
if($o_rdate_end) $s_query .= " and o_rdate <= '".$o_rdate_end." 23:59:59' ";
if( $o_status <> "" ){

	// LCY : 2023-10-05 : 마이페이지 카운팅&배송상태 패치  -- 주문상품별 카운팅 조건에 따른 수정
	if($o_status=='결제완료'){
		$s_query .= " and o_paystatus = 'Y' ";
		$s_query .= " and (select count(*) as cnt from smart_order_product as op where op.op_oordernum=o.o_ordernum and op_sendstatus = '배송대기' ) ";
	}else if($o_status=='기간만료'){
		$s_query .= " and (o_status = '발급완료' or o_status = '배송대기' or o_status='결제완료') ";
		$s_query .= " and (select count(*) as cnt from smart_order_product_ticket as opt where opt.opt_oordernum=o.o_ordernum and opt_status='만료' )";
	}else if($o_status=='사용완료'){
		$s_query .= " and (o_status = '발급완료' or o_status='결제완료') ";
		$s_query .= " and (select count(*) as cnt from smart_order_product_ticket as opt where opt.opt_oordernum=o.o_ordernum and opt_status='사용' )";
	}else{
		$s_query .= " and ( (select count(*) as cnt from smart_order_product as op where op.op_oordernum=o.o_ordernum and op_sendstatus = '".$o_status."' ) or  o_status = '". $o_status ."' ) ";
	}

	if( $o_status == "주문취소" ){
		$s_query .= " and o_canceled = 'Y' ";
	}else{
		$s_query .= " and o_canceled != 'Y' ";
	}

}


# 데이터 조회
$listmaxcount = 10 ;
if( !$listpg ) {$listpg = 1 ;}
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt $s_query ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
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
	ORDER BY o_rdate desc limit $count , $listmaxcount
";
$res = _MQ_assoc($que);






include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행