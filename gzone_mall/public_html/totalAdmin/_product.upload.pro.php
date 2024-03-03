<?php
set_time_limit(0);
ini_set('memory_limit','-1');
# LDD014

include_once(dirname(__file__) . '/inc.php');

# 첨부파일 확인
if($_FILES['excel_file']['size'] <= 0) error_loc_msg("_product.upload.pop.php?", "첨부파일이 없습니다.");

// -- LCY 2017-11-09 -- 입점업체 패치
$arr_customer = array_keys(arr_company());
$dfCPID = $arr_customer[0];

# Excel Class Load
include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
$Excel = ExcelLoader($_FILES['excel_file']['tmp_name']);

// 브랜드 정보
$arr_brand = brand_info('basic');
$arr_brand_trans = array_flip($arr_brand);

// th 생성
function add_table_th($title, $style='') {
	return '<th scope="col"'.(trim($style) != ''?' style="'.$style.'"':null).'>'.$title.'</th>';
}

// 항목명으로 키값 추출
$arr_key = array();
// 항목명 필수체크
$arr_required = array();

// --- _product.download.php, _product.upload.php 에서 동일하게 사용 : 배열 수정 시 2개 파일 동일하게 수정
$th = array(
	'상품코드<br>(신규등록시 생략)'=>array(
		'key'=>'p_code',
		'required'=>'Y',
		'width'=>'210'
	),
	'대표상품명'=>array(
		'key'=>'p_name',
		'required'=>'Y',
		'width'=>'195'
	),
	'부가상품명'=>array(
		'key'=>'p_subname',
		'required'=>'N',
		'width'=>'195'
	),
	'1차 분류'=>array(
		'key'=>'catename_1',
		'required'=>'Y',
		'width'=>'320',
		'title'=>'카테고리' // 업로드 시 타이틀
	),
	'2차 분류'=>array(
		'key'=>'catename_2',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'3차 분류'=>array(
		'key'=>'catename_3',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'노출여부(노출, 숨김)'=>array(
		'key'=>'p_view',
		'required'=>'Y',
		'width'=>'90',
	),
	'타임세일설정(적용, 미적용)'=>array(
		'key'=>'p_time_sale',
		'required'=>'N',
		'width'=>'90',
	),
	'타임세일 시작일'=>array(
		'key'=>'p_time_sale_sdate',
		'required'=>'N',
		'width'=>'90',
	),
	'타임세일 시작시간'=>array(
		'key'=>'p_time_sale_sclock',
		'required'=>'N',
		'width'=>'90',
	),
	'타임세일 종료일'=>array(
		'key'=>'p_time_sale_edate',
		'required'=>'N',
		'width'=>'90',
	),
	'타임세일 종료시간'=>array(
		'key'=>'p_time_sale_eclock',
		'required'=>'N',
		'width'=>'90',
	),
	'판매설정(상시판매, 기간판매)'=>array(
		'key'=>'p_sale_type',
		'required'=>'N',
		'width'=>'90',
	),
	'판매시작일(기간판매)'=>array(
		'key'=>'p_sale_sdate',
		'required'=>'N',
		'width'=>'90',
	),
	'판매종료일(기간판매)'=>array(
		'key'=>'p_sale_edate',
		'required'=>'N',
		'width'=>'90',
	),
	'브랜드'=>array(
		'key'=>'p_brand',
		'required'=>'N',
		'width'=>'140'
	),
	'과세여부(과세, 면세)'=>array(
		'key'=>'p_vat',
		'required'=>'Y',
		'width'=>'90'
	),
	'정산형태<br>(공급가, 수수료)'=>array(
		'key'=>'p_commission_type',
		'required'=>'Y',
		'width'=>'90',
	),
	'공급가(원)'=>array(
		'key'=>'p_sPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'수수료(%)'=>array(
		'key'=>'p_sPersent',
		'required'=>'N',
		'width'=>'90'
	),
	'정상가'=>array(
		'key'=>'p_screenPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'판매가'=>array(
		'key'=>'p_price',
		'required'=>'Y',
		'width'=>'90'
	),
	'상품쿠폰명'=>array(
		'key'=>'p_coupon_title',
		'required'=>'N',
		'width'=>'195'
	),
	'상품쿠폰타입<br>(할인금액, 할인율)'=>array(
		'key'=>'p_coupon_type',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰 할인금액(원)<br>(할인금액)'=>array(
		'key'=>'p_coupon_price',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰 할인율(%)<br>(할인율)'=>array(
		'key'=>'p_coupon_per',
		'required'=>'N',
		'width'=>'90'
	),
	'상품쿠폰 최대 할인금액(원)<br>(할인율)'=>array(
		'key'=>'p_coupon_max',
		'required'=>'N',
		'width'=>'90'
	),
	'회원등급추가혜택<br>(적용, 미적용)'=>array(
		'key'=>'p_groupset_use',
		'required'=>'N',
		'width'=>'150'
	),
	'네이버페이 사용유무<br>(사용, 미사용)'=>array(
		'key'=>'p_npay_use',
		'required'=>'N',
		'width'=>'150'
	),
	'재고량'=>array(
		'key'=>'p_stock',
		'required'=>'Y',
		'width'=>'60'
	),
	'적립율(%)'=>array(
		'key'=>'p_point_per',
		'required'=>'N',
		'width'=>'60'
	),
	'상품순위'=>array(
		'key'=>'p_sort_group', 
		'required'=>'N',
		'width'=>'60'
	),
	'제조사'=>array(
		'key'=>'p_maker',
		'required'=>'N',
		'width'=>'130'
	),
	'원산지'=>array(
		'key'=>'p_orgin',
		'required'=>'N',
		'width'=>'130'
	),
	'1회 최대 구매개수'=>array(
		'key'=>'p_buy_limit',
		'required'=>'N',
		'width'=>'130'
	),
	'중복구매 가능여부(가능, 불가능)'=>array(
		'key'=>'p_duplicate_use',
		'required'=>'N',
		'width'=>'130'
	),
	'배송정보'=>array(
		'key'=>'p_delivery_info',
		'required'=>'N',
		'width'=>'195'
	),
	'배송처리<br>(기본, 상품별배송, 개별배송, 무료배송)'=>array(
		'key'=>'p_shoppingPay_use',
		'required'=>'Y',
		'width'=>'210'
	),
	'개별배송 - 배송비'=>array(
		'key'=>'p_shoppingPay',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(기본배송비)'=>array(
		'key'=>'p_shoppingPayPdPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(무료배송비)'=>array(
		'key'=>'p_shoppingPayPfPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'관련상품 적용방식<br>(사용안함, 자동지정, 수동지정)'=>array(
		'key'=>'p_relation_type',
		'required'=>'N',
		'width'=>'180'
	),
	'관련상품 상품코드<br>(수동지정시 상품코드를|로 구분하여 기입)'=>array(
		'key'=>'p_relation',
		'required'=>'N',
		'width'=>'310'
	),
	'상품설명<br>(엔터제외)'=>array(
		'key'=>'p_content',
		'required'=>'N',
		'width'=>'310'
	),
	'목록이미지'=>array(
		'key'=>'p_img_list_square',
		'required'=>'N',
		'width'=>'195'
	),
	'목록오버이미지'=>array(
		'key'=>'p_img_list_over',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지1'=>array(
		'key'=>'p_img_b1',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지2'=>array(
		'key'=>'p_img_b2',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지3'=>array(
		'key'=>'p_img_b3',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지4'=>array(
		'key'=>'p_img_b4',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지5'=>array(
		'key'=>'p_img_b5',
		'type'=> 'N',
		'width'=>'195'
	),
	'옵션사용여부<br>(사용안함,1차옵션,2차옵션,3차옵션)'=>array(
		'key'=>'p_option_type_chk',
		'type'=> 'N',
		'width'=>'195'
	),
	// 옵션 컬럼추가
	'옵션<br>(1차옵션>2차옵션>3차옵션|공급가|판매가|재고)'=>array(
		'key'=>'p_option_excel',
		'required'=>'N',
		'width'=>'195'
	),
	'1차 옵션 타이틀'=>array(
		'key'=>'p_option1_title',
		'type'=> 'N',
		'width'=>'195'
	),
	'2차 옵션 타이틀'=>array(
		'key'=>'p_option2_title',
		'type'=> 'N',
		'width'=>'195'
	),
	'3차 옵션 타이틀'=>array(
		'key'=>'p_option3_title',
		'type'=> 'N',
		'width'=>'195'
	)
	// 1차 = 옵션명|공급가|판매가|재고 형태
	// 2차 = 1차옵션명>2차옵션명|공급가|판매가|재고
	// 3차 = 1차옵션명>2차옵션명>3차옵션명|공급가|판매가|재고
);

// -- 입점업체
if( $SubAdminMode === true){ $th['입점업체'] = array('key'=>'p_cpid','type'=>'N', 'width'=>'150'); }

// -- 엑셀값 추출 후 키값 변경
$idx = 0;
$tmp_th = array();
foreach($th as $kk=>$vv){
	$tmp_th[strip_tags($kk)] = $th[$kk];
	$arr_required[$vv['key']] = $vv['required'];
	$arr_key[$idx] = $vv['key'];
	$idx++;
}
$th = $tmp_th;

// 일주일지난 기록 삭제
_MQ_noreturn("DELETE FROM smart_product_upload_count WHERE puc_rdate < '". date("Y-m-d H:i:s", strtotime("-1week")) ."' ");
_MQ_noreturn("DELETE FROM smart_product_option_tmp WHERE pot_rdate < '". date("Y-m-d H:i:s", strtotime("-1week")) ."' ");

// 상품업로드 개수 설정 (프로그래스바에서 사용)
_MQ_noreturn(" insert into smart_product_upload_count set puc_cnt = '". (sizeof($Excel) * 1 - 2)."', puc_aid = '". ($SubAdminMode == true ? $com_id : $siteAdmin['a_id']) ."', puc_rdate = now() ");
$app_upload_uid = _MQ_insert_id();

// 엑셀 파일 정보추출
foreach($Excel as $key=>$val) {
	if($key < 2) continue; // 파일정보와 헤더는 제외
	else foreach($arr_key as $kk=>$vv) $val[$vv] = $val[$kk];

	$product = _MQ("select p_code from smart_product where p_code = '".$val['p_code']."' ");
	if($product['p_code']){
		$p_code = $val['p_code'];
		$mode = 'modify';
	}
	else{
		$p_code = shop_productcode_create();
		$mode = 'add';
	}

	// KAY :: 2024-02-20 :: 입점업체 상품만 수정가능하도록
	if($AdminPath=='subAdmin' && $mode=='modify'){
		if($product['p_cpid']!=$com_id){	continue; }
	}

	// 정상가가 판매가와 같을 경우 정상가 0적용
	if ( $_screenPrice[$v] == $_price[$v] ) {
		$_screenPrice[$v] = 0;
	}

	// 쿠폰 정보 추출
	if(trim($val['p_coupon_type']) == '할인율') $val['p_coupon_type'] = 'per';
	else if(trim($val['p_coupon_type']) == '할인금액') $val['p_coupon_type'] = 'price';
	else $val['p_coupon_type'] = '';

	$p_coupon = addslashes($val['p_coupon_title']) . "|" . $val['p_coupon_type']. "|" . $val['p_coupon_price'] ."|". $val['p_coupon_per'] ."|". $val['p_coupon_max'];

	// 관련상품
	$val['p_relation'] =  implode("|", array_unique(array_filter(explode("|", preg_replace('/\r\n|\r|\n| /','',$val['p_relation'])))));
	if(trim($val['p_relation_type']) == '자동지정'){
		$val['p_relation_type'] = 'category';
	}else if(trim($val['p_relation_type']) == '수동지정'){
		$val['p_relation_type'] = 'manual';
	}else{
		$val['p_relation_type'] = 'none';
	}

	// 회원 등급별 할인
	if(trim($val['p_groupset_use']) == '적용'){
		$val['p_groupset_use'] = 'Y';
	}else{
		$val['p_groupset_use'] = 'N';
	}

	// 배송형태
	if(trim($val['p_shoppingPay_use']) == '상품별배송'){
		$val['p_shoppingPay_use'] = 'P';
	}else if(trim($val['p_shoppingPay_use']) == '개별배송'){
		$val['p_shoppingPay_use'] = 'Y';
	}else if(trim($val['p_shoppingPay_use']) == '무료배송'){
		$val['p_shoppingPay_use'] = 'F';
	}else{
		$val['p_shoppingPay_use'] = 'N';
	}

	if($val['p_sale_type']=='상시판매'){
		$val['p_sale_type']='A';
		$val['p_sale_sdate'] = $val['p_sale_edate'] ='';
	}else{
		$val['p_sale_type']='T';
	}

	// 판매기간이 기간판매일경우 타임세일 무력화 
	if( $val['p_sale_type'] == 'T'){ $val['p_time_sale'] = '미적용'; }

	$val['p_npay_use']=$val['p_npay_use']=='사용'?'Y':'N';

	// 옵션 사용체크 
	$val['p_option_type_chk'] = $val['p_option_type_chk']=='1차옵션'?'1depth':($val['p_option_type_chk']=='2차옵션'?'2depth':($val['p_option_type_chk']=='3차옵션'?'3depth':'nooption'));

	// 노출여부 
	$val['p_view']=$val['p_view']=='노출'?'Y':'N';

	// 중복 구매가능여부 
	$val['p_duplicate_use']=$val['p_duplicate_use']=='가능'?'Y':'N';

	// 과세여부
	$val['p_vat']=$val['p_vat']=='과세'?'Y':'N';

	// 타임세일 적용
	if($val['p_time_sale']=='적용'){
		$val['p_time_sale']='Y';
		$val['p_time_sale_sdate'] = rm_str($val['p_time_sale_sdate']) > 0 ?  date('Y-m-d',strtotime(str_replace(".", "-", $val['p_time_sale_sdate']))) : '';
		$val['p_time_sale_edate'] = rm_str($val['p_time_sale_edate']) > 0 ?  date('Y-m-d',strtotime(str_replace(".", "-", $val['p_time_sale_edate']))) : '';
	}else{
		$val['p_time_sale']='N';
		$val['p_time_sale_sdate'] = $val['p_time_sale_edate']='';
	}

	$time_sale_sclock=$val['p_time_sale_sclock']*86400-60*60*9;
	$time_sale_sclock=ceil($time_sale_sclock*10)/10;
	$val['p_time_sale_sclock'] = date('H:i:s', $time_sale_sclock);

	$time_sale_eclock=$val['p_time_sale_eclock']*86400-60*60*9;
	$time_sale_eclock=ceil($time_sale_eclock*10)/10;
	$val['p_time_sale_eclock'] = date('H:i:s', $time_sale_eclock);

	$s_query = "
		p_name						= '".$val['p_name']."',
		p_subname					= '".$val['p_subname']."',
		p_view						= '".$val['p_view']."',
		p_sale_type				= '".$val['p_sale_type']."',
		p_sale_sdate				= '".$val['p_sale_sdate']."',
		p_sale_edate				= '".$val['p_sale_edate']."',
		p_commission_type		= '".$val['p_commission_type']."',
		p_sPrice						= '".$val['p_sPrice']."',
		p_sPersent					= '".$val['p_sPersent']."',
		p_screenPrice				= '".$val['p_screenPrice']."',
		p_price						= '".$val['p_price']."',
		p_stock						= '".$val['p_stock']."',
		p_sort_group				= '".$val['p_sort_group']."',
		p_orgin						= '".$val['p_orgin']."',
		p_maker						= '".$val['p_maker']."',
		p_point_per					= '".$val['p_point_per']."',
		p_coupon					= '".$p_coupon."',
		p_delivery_info				= '".$val['p_delivery_info']."',
		p_shoppingPay_use		= '".$val['p_shoppingPay_use']."',
		p_shoppingPayFree		= '".$val['p_shoppingPayFree']."',
		p_shoppingPay				= '".$val['p_shoppingPay']."',
		p_relation					= '".$val['p_relation']."',
		p_relation_type			= '".$val['p_relation_type']."',
		p_content				= '".addslashes($val['p_content'])."',
		p_img_list_square			= '".$val['p_img_list_square']."',
		p_img_list_over			= '".$val['p_img_list_over']."',
		p_img_b1					= '".$val['p_img_b1']."',
		p_img_b2					= '".$val['p_img_b2']."',
		p_img_b3					= '".$val['p_img_b3']."',
		p_img_b4					= '".$val['p_img_b4']."',
		p_img_b5					= '".$val['p_img_b5']."',
		p_option_type_chk		= '".$val['p_option_type_chk']."',
		p_option1_title				= '".$val['p_option1_title']."',
		p_option2_title				= '".$val['p_option2_title']."',
		p_option3_title				= '".$val['p_option3_title']."',
		p_vat							= '".$val['p_vat']."',
		p_brand						= '".$arr_brand_trans[$val['p_brand']]."',
		p_shoppingPayPdPrice	= '".$val['p_shoppingPayPdPrice']."',
		p_shoppingPayPfPrice	= '".$val['p_shoppingPayPfPrice']."',
		p_groupset_use			= '".$val['p_groupset_use']."',
		npay_use					= '".$val['p_npay_use']."',
		p_buy_limit					= '".$val['p_buy_limit']."',
		p_duplicate_use					= '".$val['p_duplicate_use']."',
		p_time_sale					= '".$val['p_time_sale']."',
		p_time_sale_sdate					= '".$val['p_time_sale_sdate']."',
		p_time_sale_edate					= '".$val['p_time_sale_edate']."',
		p_time_sale_sclock					= '".$val['p_time_sale_sclock']."',
		p_time_sale_eclock					= '".$val['p_time_sale_eclock']."'
	";

	// 입점업체
	if( $SubAdminMode == true) {
		$s_query .= " , p_cpid = '".$val['p_cpid']."' ";
	}else{
		$s_query .= " , p_cpid = '".$dfCPID."' ";
	}

	//  -----  카테고리 공백제거 -----
	$val['catename_1'] = trim($val['catename_1']);
	$val['catename_2'] = trim($val['catename_2']);
	$val['catename_3'] = trim($val['catename_3']);

	//  -----  카테고리 정보추출 -----
	// 3차까지 있는 경우
	if($val['catename_1'] && $val['catename_2'] && $val['catename_3']) {
		$cateLoad = _MQ("
						select
							c3.c_uid as cc3,
							c2.c_uid as cc2,
							c1.c_uid as cc1
						from smart_category as c3
						left join smart_category as c2 on (substring_index(c3.c_parent , ',' ,-1) = c2.c_uid and c2.c_depth = 2 and c2.c_name = '{$val['catename_2']}' )
						left join smart_category as c1 on (substring_index(c3.c_parent , ',' ,1) = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
						where
							c1.c_uid is not null and
							c2.c_uid is not null and
							c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and c3.c_name = '{$val['catename_3']}' and
							c3.c_depth = 3
					");
	}
	// 2차까지 있는 경우
	else if($val['catename_1'] && $val['catename_2']) {
		$cateLoad = _MQ("
						select
							c2.c_uid as cc2,
							c1.c_uid as cc1
						from smart_category as c2
						left join smart_category as c1 on ( c2.c_parent = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
						where
							c1.c_uid is not null and
							c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and
							c2.c_depth = 2
					");
	}
	// 1차까지 있는 경우
	else if($val['catename_1'] ) {
		$cateLoad = _MQ("
						select
							c1.c_uid as cc1
						from smart_category as c1
						where
							c1.c_uid is not null and
							c1.c_name = '{$val['catename_1']}'  and
							c1.c_depth = 1
					");
	}
	//  카테고리 정보추출
	$real_catecode = ($cateLoad['cc3'] ? $cateLoad['cc3'] : ($cateLoad['cc2'] ? $cateLoad['cc2'] : $cateLoad['cc1']	));

	// 카테고리 처리
	if($real_catecode!=''){
		$c_que = "
			INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $p_code ."', '". $real_catecode ."')
			ON DUPLICATE KEY UPDATE pct_pcode='". $p_code ."' , pct_cuid='". $real_catecode ."'
		";
		_MQ_noreturn($c_que);
	}
	
	// 옵션처리를 위한 엑셀에서 받은 옵션값 임시 저장
	if(trim($val['p_option_excel'])) {
		_MQ_noreturn("insert into smart_product_option_tmp set pot_pcode = '" . $p_code . "', pot_pucuid = '". $app_upload_uid ."', pot_info = '" .trim( $val['p_option_excel']) . "', pot_rdate = now() ");
	}


	//상품추가 & 상품수정
	switch ($mode) {
		// - 상품추가 ---
		case "add":
			$que="insert into smart_product set
					p_code			= '" . $p_code . "',
					p_rdate = now() ,
					p_type = 'delivery' ,
					{$s_query}
					";
			_MQ_noreturn($que);
		break;
		// - 상품추가 ---

		// - 상품수정 ---
		case "modify":
			$que="update smart_product set
					p_type = 'delivery' ,
					{$s_query}
					where
					p_code			= '" . $p_code. "'";
			_MQ_noreturn($que);
		break;

		// - 상품수정 ---
	} // case END

	// 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트
	product_option_validate_check($p_code);

	// 상품 품절 체크 - p_soldout_chk 정보 업데이트
	product_soldout_check($p_code);

}

// 카테고리 상품 개수 업데이트
update_catagory_product_count();

// 상품 순위 재정렬
product_resort();

// 임시 옵션 백그라운드에서 처리
$url =$system['url'].'/totalAdmin/_product.upload.option_pro.php?app_upload_uid='.$app_upload_uid;
curl_async($url);

// 부모창 프로그래스바 실행 (옵션 진행률 프로그래스 바 실행)
echo "<script>parent.progress('".$app_upload_uid."');</script>";

exit;