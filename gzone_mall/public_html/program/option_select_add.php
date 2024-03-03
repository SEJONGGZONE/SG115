<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


if($uid1 == "undefined") {
    $uid1 = "";
}
if($uid2 == "undefined") {
    $uid2 = "";
}
if($uid3 == "undefined") {
    $uid3 = "";
}



// LCY : 2022-12-21 : 티켓기능 -- p_dateoption_use 추가
$pque = "select p_option_type_chk, p_dateoption_use from smart_product where p_code='". $code ."' ";
$pr = _MQ($pque);
$p_option_type_chk = $pr[p_option_type_chk];
switch($p_option_type_chk){
	case "1depth": $app_uid = $uid1; break;
	case "2depth": $app_uid = $uid2; break;
	case "3depth": $app_uid = $uid3; break;
}


// ----------------- 사전체크 ---------------------//
// 필수 변수 체크
if( !$code || (!$uid1 && !$uid2 && !$uid3) ) {
    echo "error1"; //잘못된 접근입니다.
    exit;
}

// LCY : 2022-12-21 : 티켓기능 -- 날짜옵션
$add_query = "";
if( $pr['p_dateoption_use'] == 'Y'){
    if($selDate == ''){ die('error_date'); }
    $add_query .= " and pto_dateoption_date = '".$selDate."' ";
}

// 넘어온 정보의 중복체크
$cntr = _MQ(" select count(*) as cnt from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' and pto_pouid='" . $app_uid . "' ".$add_query);
if($cntr[cnt] > 0 ) {
    echo "error2"; //이미 선택한 옵션입니다.
    exit;
}
// ----------------- 사전체크 ---------------------//





// 상품정보, 옵션정보 추출
include_once(OD_PROGRAM_ROOT."/option_select.top_inc.php");




//현재옵션의 재고수량 -- 옵션이 있을 경우에만
if( $app_uid ) {
    $option_stock = $arr_option_data[$app_uid]['option_cnt'] - 1; // 한개를 추가하므로 - 1을 적용함
    if($option_stock < 0 ) {
        echo "error3"; //선택 옵션의 재고량이 부족합니다.
        exit;
    }
}

// 회원등급혜택 추가 {{

// 회원등급혜택 추가 }}

// 넘어온 정보 추가
$sque = "
    insert smart_product_tmpoption set 
        pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."',
		pto_pouid = '". $app_uid ."',
        pto_pcode='" . $code . "',
        pto_cnt=1,
        pto_poptionname1 ='".addslashes($arr_option_data[$app_uid]['option_name1'])."',
		pto_poptionname2 ='".addslashes($arr_option_data[$app_uid]['option_name2'])."',
		pto_poptionname3 ='".addslashes($arr_option_data[$app_uid]['option_name3'])."',
        pto_poption_supplyprice ='".$arr_option_data[$app_uid]['option_supplyprice']."'
       , pto_poptionprice = '".$arr_option_data[$app_uid]['option_price']."' 
";

// LCY : 2022-12-21 : 티켓기능 -- 날짜옵션 
if( $pr['p_dateoption_use'] == 'Y'){
    $sque .= "
        , pto_dateoption_use = 'Y'
        , pto_dateoption_date = '".$selDate."'
    ";
}

_MQ_noreturn($sque);


// 옵션목록 적용
include_once(OD_PROGRAM_ROOT."/option_select.bottom_inc.php");



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행