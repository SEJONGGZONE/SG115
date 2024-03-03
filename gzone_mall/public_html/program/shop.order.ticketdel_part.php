<?php
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

if( $ordr['op_ptype'] == 'ticket'){
	$res_ticket = _MQ_assoc("select opt_uid from smart_order_product_ticket where opt_oordernum = '".$_ordernum."' and opt_opuid = '".$_uid."' ");
	foreach( $res_ticket as $rtk=>$rtv){
		update_order_ticket_status($rtv['opt_uid'],'취소');
	}
}


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행