<?php
include_once(dirname(__FILE__).'/inc.php');
$ordernum = $_SESSION["session_ordernum"];//주문번호
if( $siteInfo[s_pg_type] != 'kcp'){  error_loc_msg("/","올바르지 않은 경로로 접근하셨습니다."); }
if( !$_POST[ "res_cd"] ){  error_loc_msg("/","올바르지 않은 경로로 접근하셨습니다."); }
include_once(OD_PROGRAM_ROOT.'/shop.order.result_kcp_m.pro.php');
//if( $_POST[ "res_cd"] == '0000'){
//	error_loc("/?pn=shop.order.complete");
//}
//else {
//	error_loc_msg("/?pn=shop.order.form",$_POST[ "res_msg"]."(주문서를 다시 작성해 주세요)");
//}
