<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) 
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림

include_once('inc.php');
$fileName = 'product';
$toDay = date('Y-m-d', time());

$arr_customer = array_keys(arr_company());
$dfCPID = $arr_customer[0];

# header 설정
if(!$c) {
	header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
	header( "Content-Disposition: attachment; filename=$fileName-$toDay.xls" );
	print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
}
$pr = array();

// 검색엑셀 다운로드
if($_mode == 'search'){
	$s_query = enc('d', $_search);
	$pr = _MQ_assoc(" select *  " . $s_query . $orderby);

// 선택엑셀다운로드
}else if($_mode == 'select'){
	$arr_pcode = array();
	foreach($chk_pcode as $k=>$v){
		if($v == 'Y') $arr_pcode[] = $k;
	}
	if(sizeof($arr_pcode)){
		$pr = _MQ_assoc(" select * from `smart_product` where p_code in ('". implode("','" , $arr_pcode) ."') " . $orderby);
	}

// 전체상품
}else{
	$pr = _MQ_assoc(" select * from `smart_product` " . $orderby);
}
// th 생성
function add_table_th($title, $style='') {

	return '<th'.(trim($style) != ''?' style="'.$style.'"':null).'>'.strip_tags($title).'</th>';
}

// --- _product_ticket.download.php, _product_ticket.upload.php 에서 동일하게 사용 : 배열 수정 시 2개 파일 동일하게 수정
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
		'width'=>'120'
	),
	'상품쿠폰 할인금액(원)<br>(할인금액)'=>array(
		'key'=>'p_coupon_price',
		'required'=>'N',
		'width'=>'123'
	),
	'상품쿠폰 할인율(%)<br>(할인율)'=>array(
		'key'=>'p_coupon_per',
		'required'=>'N',
		'width'=>'123'
	),
	'상품쿠폰 최대 할인금액(원)<br>(할인율)'=>array(
		'key'=>'p_coupon_max',
		'required'=>'N',
		'width'=>'140'
	),
	'회원등급추가혜택<br>(적용, 미적용)'=>array(
		'key'=>'p_groupset_use',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 사용여부<br>(사용, 미사용)'=>array(
		'key'=>'p_dateoption_use',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 선택기간<br>(일자지정, 날짜지정)'=>array(
		'key'=>'p_dateoption_date_type',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 선택가능 시작일<br>(일자지정)'=>array(
		'key'=>'p_dateoption_sday',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 선택가능 종료일<br>(일자지정)'=>array(
		'key'=>'p_dateoption_eday',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 선택가능 시작일<br>(날짜지정)'=>array(
		'key'=>'p_dateoption_sdate',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 선택가능 종료일<br>(날짜지정)'=>array(
		'key'=>'p_dateoption_edate',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 당일 선택가능 시작시간'=>array(
		'key'=>'p_dateoption_stime',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 당일 선택가능 종료시간'=>array(
		'key'=>'p_dateoption_etime',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 요일별 선택불가'=>array(
		'key'=>'p_dateoption_ex_week',
		'required'=>'N',
		'width'=>'150'
	),
	'달력옵션 - 날짜별 선택불가'=>array(
		'key'=>'p_dateoption_ex_date',
		'required'=>'N',
		'width'=>'150'
	),
	'티켓유효기간 (없음, 만료일, 만료날짜)'=>array(
		'key'=>'p_expire_type',
		'required'=>'Y',
		'width'=>'60'
	),
	'티켓만료일(숫자만)'=>array(
		'key'=>'p_expire_day',
		'required'=>'Y',
		'width'=>'60'
	),	
	'티켓만료날짜(년.월.일)'=>array(
		'key'=>'p_expire_date',
		'required'=>'Y',
		'width'=>'60'
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
	'주소 설정'=>array(
		'key'=>'p_com_juso',
		'required'=>'N',
		'width'=>'195'
	),
	'좌표 설정(사용 ,미사용)'=>array(
		'key'=>'p_apply_mapxy',
		'required'=>'N',
		'width'=>'195'
	),
	'X좌표(경도)'=>array(
		'key'=>'p_com_mapx',
		'required'=>'N',
		'width'=>'195'
	),
	'Y좌표(위도)'=>array(
		'key'=>'p_com_mapy',
		'required'=>'N',
		'width'=>'195'
	),
	'장소이름'=>array(
		'key'=>'p_com_locname',
		'required'=>'N',
		'width'=>'195'
	),
	'전화번호'=>array(
		'key'=>'p_com_tel',
		'required'=>'N',
		'width'=>'195'
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
	'옵션<br>(1차옵션>2차옵션>3차옵션|공급가|판매가|재고)'=>array(
		'key'=>'p_option_excel',
		'type'=> 'N',
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
);

if( $SubAdminMode === true){ $th['입점업체'] = array('key'=>'p_cpid','required'=>'N', 'width'=>'150'); }

// 브랜드 정보 추출
$arr_brand = brand_info('basic');


// ----- 티켓유효기간 처리 -----
$arr_excel_expire_type = array(''=>'없음','day'=>'만료일','date'=>'만료날짜');

?>
<table border="1">
	<thead>
		<tr>
			<?php
				foreach($th as $k=>$v) {
					echo add_table_th($k, ($v['required']=='Y'?'background-color:#F79646; color:#fff':null));
				}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($pr as $k=>$v) {
			// 배송 출력을 위한 처리
			if($v['p_shoppingPay_use'] == 'P') $v['p_shoppingPay_use'] = '상품별배송';
			else if($v['p_shoppingPay_use'] == 'F') $v['p_shoppingPay_use'] = '무료배송';
			else if($v['p_shoppingPay_use'] == 'Y') $v['p_shoppingPay_use'] = '개별배송';
			else $v['p_shoppingPay_use'] = '기본';

			$v['p_view']=$v['p_view']=='Y'?'노출':'숨김';		// 노출여부
			$v['p_vat']= $v['p_vat']=='Y'?'과세':'면세';		// 과세,면세

			// 상시판매, 기간판매에 따른 시작일,종료일
			if($v['p_sale_type']=='T'){
				$v['p_sale_type']='기간판매';
				$v['p_sale_sdate'] = rm_str($v['p_sale_sdate']) > 0 ? date('Y.m.d',strtotime($v['p_sale_sdate'])) : '' ;
				$v['p_sale_edate'] = rm_str($v['p_sale_edate']) > 0 ? date('Y.m.d',strtotime($v['p_sale_edate'])) : '' ;
			}else{
				$v['p_sale_type']='상시판매';
				$v['p_sale_sdate'] = $v['p_sale_edate'] ='';
			}

			if($v['p_time_sale']=='Y'){
				$v['p_time_sale']='적용';
				$v['p_time_sale_sdate'] = rm_str($v['p_time_sale_sdate']) > 0 ? date('Y.m.d',strtotime($v['p_time_sale_sdate'])) : '' ;
				$v['p_time_sale_edate'] = rm_str($v['p_time_sale_edate']) > 0 ? date('Y.m.d',strtotime($v['p_time_sale_edate'])) : '' ;
				$v['p_time_sale_sclock'] = rm_str($v['p_time_sale_sclock']) > 0 ? date('H:i',strtotime($v['p_time_sale_sclock'])) : '' ;
				$v['p_time_sale_eclock'] = rm_str($v['p_time_sale_eclock']) > 0 ? date('H:i',strtotime($v['p_time_sale_eclock'])) : '' ;
			}else{
				$v['p_time_sale']='미적용';
				$v['p_time_sale_sdate'] = $v['p_time_sale_edate'] = $v['p_time_sale_sclock'] = $v['p_time_sale_eclock']='';
			}

			// 중복구매 가능여부
			$v['p_duplicate_use']= $v['p_duplicate_use']=='Y'?'가능':'불가능';

			// 1일 최대 구매개수
			$v['p_buy_limit']= $v['p_buy_limit']>0?$v['p_buy_limit']:'';

			// 좌표 설정
			$v['p_apply_mapxy']= $v['p_apply_mapxy']=='Y'?'사용':'미사용';

			// 적립율
			$v['p_point_per'] = $v['p_point_per']>0?$v['p_point_per']:'';

			// 카테고리 정보 추출
			$FirstCate = _MQ(" select `pct_cuid` from `smart_product_category` where `pct_pcode` = '{$v['p_code']}' order by `pct_uid` asc ");
			$Data = _MQ(" select `c_uid`, `c_parent` from `smart_category` where `c_uid` = '{$FirstCate['pct_cuid']}' ");
			$code = array();
			$code[] = $Data['c_uid'];
			$code = @array_merge($code, explode(',', $Data['c_parent']));
			@asort($code); // value 값으로 asc 정렬
			$CateInfo = _MQ_assoc(" select * from `smart_category` where `c_uid` in ('".implode("','", $code)."') order by `c_depth` asc ");
			$v['catename_1'] = $CateInfo[0]['c_name'];
			$v['catename_2'] = $CateInfo[1]['c_name'];
			$v['catename_3'] = $CateInfo[2]['c_name'];

			// 상품쿠폰 정보 추출
			$CouponInfo = explode("|" , $v['p_coupon']);
			if(count($CouponInfo) < 4){  $_tmp = $CouponInfo; $CouponInfo[0] = $_tmp[0]; $CouponInfo[1] = ''; $CouponInfo[2] = $_tmp[1]; }// 이전 데이터 예외 처리
			$v['p_coupon_title'] = $CouponInfo[0];
			$v['p_coupon_type'] = '';
			$p_coupon_price = $CouponInfo[2];
			$p_coupon_per = number_format(floor($CouponInfo[3]*10)/10,1);
			$p_coupon_max = $CouponInfo[4];
			if($CouponInfo[1] == 'price'){ $v['p_coupon_type'] = '할인금액'; }
			else if($CouponInfo[1] == 'per'){ $v['p_coupon_type'] = '할인율'; }
			$v['p_coupon_price'] = $p_coupon_price>0?$p_coupon_price:'';
			$v['p_coupon_per'] = $p_coupon_per>0?$p_coupon_per:'';
			$v['p_coupon_max'] = $p_coupon_max>0?$p_coupon_max:'';

			// 상품정보 html화
			$v['p_content'] = rm_enter(htmlspecialchars($v['p_content']));

			// 브랜드 정보 변경
			$v['p_brand'] = $arr_brand[$v['p_brand']];

			// 회원 등급 혜택
			$v['p_groupset_use'] =  $v['p_groupset_use'] == 'Y' ? '적용':'미적용';

			// 관련상품지정방식
			if($v['p_relation_type'] == 'category'){ $v['p_relation_type'] =  '자동지정'; }
			else if($v['p_relation_type'] == 'manual'){ $v['p_relation_type'] =  '수동지정'; }
			else{ $v['p_relation_type'] =  '사용안함'; }

			// 티켓유효기간
			$v['p_expire_type'] = $arr_excel_expire_type[trim($v['p_expire_type'])];
			$v['p_expire_day'] = rm_str(trim($v['p_expire_day']));
			$v['p_expire_date'] = rm_str($v['p_expire_date']) > 0 ? date('Y.m.d',strtotime($v['p_expire_date'])) : '' ;

			// 달력옵션
			if($v['p_dateoption_date_type']=='day'){
				$v['p_dateoption_date_type']='일자지정';
				$v['p_dateoption_sdate'] = $v['p_dateoption_edate'] ='';
				$v['p_dateoption_sday'] = $v['p_dateoption_sday']>0 ?$v['p_dateoption_sday']:'';
				$v['p_dateoption_eday'] = $v['p_dateoption_eday'] >0?$v['p_dateoption_eday'] :'';
			}else{
				$v['p_dateoption_date_type']='날짜지정';
				$v['p_dateoption_sday'] = $v['p_dateoption_eday'] ='';
				$v['p_dateoption_sdate'] = rm_str($v['p_dateoption_sdate']) > 0 ? date('Y.m.d',strtotime($v['p_dateoption_sdate'])) : '' ;
				$v['p_dateoption_edate'] = rm_str($v['p_dateoption_edate']) > 0 ? date('Y.m.d',strtotime($v['p_dateoption_edate'])) : '' ;
			}

			// 달력옵션 사용여부
			$v['p_dateoption_use'] = $v['p_dateoption_use']=='Y'?'사용':'미사용';
			
			// 달력옵션 요일별 선택불가
			$arr_dateoption_week = array();
			$arr_dateoption_ex_week = array_filter(explode(',',$v['p_dateoption_ex_week']));
			foreach($arr_dateoption_ex_week as $dewk=>$dewv){
				$arr_dateoption_week[] = $arr_day_week_short2[$dewv];
			}
			$v['p_dateoption_ex_week'] = implode(',',array_filter($arr_dateoption_week));


			
			$v['p_sPrice']=$v['p_sPrice']>0?$v['p_sPrice']:'';						// 공급가
			$v['p_sPersent']=$v['p_sPersent']>0?$v['p_sPersent']:'';			// 수수료

			// 옵션 정보 추출
			$option_array = array();
			if($v['p_option_type_chk'] == 'nooption') {
				$v['p_option_excel'] = '';
				$v['p_option_type_chk']='사용안함';
			}
			else if($v['p_option_type_chk'] == '1depth') {
				$que = "
					SELECT
						 po_poptionname, po_uid, po_poption_supplyprice , po_poptionprice , po_cnt
					FROM smart_product_option
					WHERE
						po_view='Y' AND
						po_depth=1 AND
						po_pcode='{$v['p_code']}'
					ORDER BY po_sort , po_uid ASC
				 ";
				$res = _MQ_assoc($que);

				foreach($res as $k=>$r) {
					$option_array[] = $r['po_poptionname']."|".$r['po_poption_supplyprice'] ."|".$r['po_poptionprice'] ."|". $r['po_cnt'];
					$v['p_option_excel'] = implode( '§', $option_array );
				}

				$v['p_option_type_chk']='1차옵션';
			}
			else if($v['p_option_type_chk'] == '2depth') {
				$que2 = "
					SELECT
						po2.po_poption_supplyprice , po2.po_poptionprice , po2.po_cnt ,po2.po_poptionname , po2.po_cnt ,
						po1.po_uid as po1_uid, po1.po_poptionname as po1_poptionname
					FROM smart_product_option as po2
					INNER JOIN smart_product_option as po1 on ( po1.po_uid = SUBSTRING_INDEX(po2.po_parent,',',1) and po1.po_depth=1 AND po1.po_view='Y' )
					WHERE
						po2.po_view='Y' AND
						po2.po_depth=2 AND
						po2.po_pcode='{$v['p_code']}'
					ORDER BY po2.po_sort , po2.po_uid ASC
				";
				$res2 = _MQ_assoc($que2);
				foreach($res2 as $k2=>$r2) {
					$option_array[] =$r2['po1_poptionname'] .">". $r2['po_poptionname'] ."|". $r2['po_poption_supplyprice'] ."|". $r2['po_poptionprice'] ."|". $r2['po_cnt'];
					$v['p_option_excel'] = implode( '§', $option_array );
				}
				$v['p_option_type_chk']='2차옵션';
			}
			//컬러타입 = 이미지,컬러코드 :: type이 color일 경우 colorpicker, type이 img일경우 파일명
			else if($v['p_option_type_chk'] == '3depth') {
				$que3 = "
					SELECT
						po3.po_poption_supplyprice , po3.po_poptionprice , po3.po_cnt , po3.po_poptionname ,
						po2.po_uid as po2_uid, po2.po_poptionname as po2_poptionname,
						po1.po_uid as po1_uid, po1.po_poptionname as po1_poptionname
					FROM smart_product_option as po3
					INNER JOIN smart_product_option as po2 on ( po2.po_uid = SUBSTRING_INDEX(po3.po_parent,',',-1) AND po2.po_depth=2 AND po2.po_view='Y' )
					INNER JOIN smart_product_option as po1 on ( po1.po_uid = SUBSTRING_INDEX(po3.po_parent,',',1) AND po1.po_depth=1 AND po1.po_view='Y' )
					WHERE
						po3.po_view='Y' AND
						po3.po_depth=3 AND
						po3.po_pcode='{$v['p_code']}'
					ORDER BY po3.po_sort , po3.po_uid ASC
				";
				$res3 = _MQ_assoc($que3);
				foreach($res3 as $k3=>$r3) {
					$option_array[] = $r3['po1_poptionname'] .">". $r3['po2_poptionname'] .">". $r3['po_poptionname'] ."|". $r3['po_poption_supplyprice'] ."|". $r3['po_poptionprice'] ."|". $r3['po_cnt'];
					$v['p_option_excel'] = implode('§', $option_array );
				}
				$v['p_option_type_chk']='3차옵션';
			}

		?>
		<tr>
			<?php
				foreach($th as $kk=>$vv) {
			
					echo '<td>'.$v[$vv['key']].'</td>';

				}
			?>
		</tr>
		<?php } ?>
	</tbody>
</table>