<?PHP

	include "./inc.php";


	// 카테고리 변수 받기
	$_cuid = $pass_parent03_real ;

	//  _product.mass_modify.php 일괄수정을 위한 --- 선택 카테고리 배열화 ::: JJC ---
	function massModifyCategoryData(){
		$_tmpcode = "TMP01234"; // 임시 상품번호
		$arr_cuid = array();
		$que = "select pct_cuid from smart_product_category where pct_pcode='". $_tmpcode ."'";
		$r = _MQ_assoc($que);
		foreach( $r as $k=>$v ){
			$arr_cuid[] = $v['pct_cuid'];
		}
		return $arr_cuid;
	}




	// - 모드별 처리 ---
	switch( $_mode ){

		// ---------------- 상품가격 일괄수정 ----------------
		case "mass_price" :

			foreach($chk_pcode as $k => $v) {
				// 기존가가 판매가와 같을 경우 정상가 0 적용 kms 2019-09-19
				if ( $_screenPrice[$k] == $_price[$k] ) {
					$_screenPrice[$k] = 0;
				}

				$que = "
					p_screenPrice = '". rm_str($_screenPrice[$k]) ."' 
					, p_price = '". rm_str($_price[$k]) ."'
				";

				if( $AdminPath == 'totalAdmin'){
					$que .= "
						, p_commission_type = '". $_commission_type[$k] ."'
						, p_sPrice = '". rm_str($_sPrice[$k]) ."'
						, p_sPersent = '". $_sPersent[$k] ."' 					
					";
				}
				_MQ_noreturn("update smart_product set ".$que." where p_code = '". $k ."'");
			}
			error_loc("_product_mass.price.php?".enc('d' , $_PVSC));
			break;
		// ---------------- 상품가격 일괄수정 ----------------


		// KAY :: 2021-04-12::------- 상품적립/쿠폰 일괄수정 ----------------2개에서 5개로 늘림
		case "mass_point" :

			foreach($chk_pcode as $k => $v) {
				$_coupon	= addslashes($_coupon_title[$k]) . "|" . $_coupon_type[$k] . "|" . rm_str($_coupon_price[$k]) . "|" . $_coupon_per[$k]. "|" . rm_str($_coupon_max[$k])  ;

				// query 생성
				$que = "
					update smart_product set
						p_point_per = '". $_point_per[$k] ."' ,
						p_coupon = '" . $_coupon  . "'
					where p_code = '". $k ."'
				";
				_MQ_noreturn($que);
			}

			error_loc("_product_mass.point.php?".enc('d' , $_PVSC));

			break;

		// ---------------- 상품노출/재고관리 ----------------
		case "mass_view" :

			foreach($chk_pcode as $k => $v) {

				$que = " p_stock = '". $_stock[$k] ."' ";
				if($AdminPath == 'totalAdmin'){ $que .= " , p_view = '". $_view[$k] ."' "; }
				
				_MQ_noreturn("update smart_product set ".$que." where p_code = '". $k ."' ");
				product_soldout_check($k);
			}

			error_loc("_product_mass.view.php?".enc('d' , $_PVSC));

			break;


		// ---------------- 상품옵션관리 ----------------
		case "mass_option" :

			foreach($chk_pcode as $k => $v) {
				// query 생성
				$que = "
					update smart_product_option set
						po_poptionprice = '". rm_str($_poptionprice[$k]) ."' ,
						po_poption_supplyprice = '". rm_str($_poption_supplyprice[$k]) ."' ,
						po_cnt = '". $_cnt[$k] ."' ,
						po_view = '". $_view[$k] ."'
					where po_uid = '". $k ."'
				";
				_MQ_noreturn($que);
			}

			error_loc("_product_mass.option.php?".enc('d' , $_PVSC));

			break;



		// ---------------- 일괄수정 - 선택 상품의 전체 카테고리 제외 ----------------
		case "mass_modify_category_delete" :

			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {
					_MQ_noreturn("delete from smart_product_category where pct_pcode='". $k ."' ");
				}
			}
			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;

		// ---------------- 일괄수정 - 선택 상품에 선택 카테고리 추가 ----------------
		case "mass_modify_category_add" :

			// --- 선택 카테고리 배열화 ---
			$arr_cuid = massModifyCategoryData();
			// --- 카테고리 갯수 체크 ---
			if( sizeof($arr_cuid) == 0 ) { error_msg("추가할 카테고리를 먼저 선택해주시기 바랍니다."); }

			// --- 선택 상품에 선택 카테고리 추가 ---
			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {
					foreach($arr_cuid as $sk => $sv) {
						// 중복 배제 추가
						$que = "
							INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $k ."', '". $sv ."')
							ON DUPLICATE KEY UPDATE pct_pcode='". $k ."' , pct_cuid='". $sv ."'
						";
						_MQ_noreturn($que);
					}
				}
			}
			// --- 선택 상품에 선택 카테고리 추가 ---

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;



		// ---------------- 일괄수정 - 선택 상품의 선택 카테고리 삭제 ----------------
		case "mass_modify_category_seldel" :

			// --- 선택 카테고리 배열화 ---
			$arr_cuid = massModifyCategoryData();
			// --- 카테고리 갯수 체크 ---
			if( sizeof($arr_cuid) == 0 ) { error_msg("추가할 카테고리를 먼저 선택해주시기 바랍니다."); }

			// --- 선택 상품에 선택 카테고리 추가 ---
			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {
					foreach($arr_cuid as $sk => $sv) {
						// 삭제
						$que = "delete from smart_product_category where pct_pcode='". $k ."' and pct_cuid='". $sv ."' ";
						_MQ_noreturn($que);
					}
				}
			}
			// --- 선택 상품에 선택 카테고리 추가 ---

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;


		// ---------------- 일괄수정 - 선택 상품 복사 + 카테고리 추가 ----------------
		case "mass_modify_category_copy" :

			// --- 선택 카테고리 배열화 ---
			$arr_cuid = massModifyCategoryData();
			// --- 카테고리 갯수 체크 ---
			if( sizeof($arr_cuid) == 0 ) { error_msg("추가할 카테고리를 먼저 선택해주시기 바랍니다."); }

			// --- 선택 상품에 선택 카테고리 추가 ---
			foreach($chk_pcode as $k => $v) {
				if(trim($k)) {

					$pcode = $k;

					# 카테고리 추가
					foreach($arr_cuid as $sk => $sv) {
						// 중복 배제 추가
						$que = "
							INSERT INTO smart_product_category (pct_pcode, pct_cuid) VALUES ('". $pcode ."', '". $sv ."')
							ON DUPLICATE KEY UPDATE pct_pcode='". $pcode ."' , pct_cuid='". $sv ."'
						";
						_MQ_noreturn($que);
					}

					// 카테고리 추가 후 상품복사
					include('_product.copy.php');
				}
			}
			// --- 선택 상품에 선택 카테고리 추가 ---

			error_loc("_product_mass.move.php?".enc('d' , $_PVSC));

			break;


		// KAY :: 2021-04-12 ::------- 상품일괄 적립/쿠폰관리 개별수정 ---------------
		case "point_direct_change" :


			$_coupon	= addslashes($_coupon_title) . "|" . $_coupon_type  . "|" . addslashes($_coupon_price) . "|" . addslashes($_coupon_per) . "|" . $_coupon_max;
			if($_coupon_type=='price'&&$_coupon_price<=0){	error_msg("0이상의 값을 입력하세요.");return false;
			}
			if($_coupon_type =='per'){
				if($_coupon_per > 100){error_msg("100이하 값을 입력하세요."); return false;}
				if($_coupon_per <= 0){	error_msg("0이상 값을 입력하세요."); return false;}
			}

			// query 생성
			$que = "
				UPDATE smart_product SET
					p_point_per ='". addslashes($_point_per) ."',
					p_coupon = '". $_coupon ."'
				WHERE
					p_code = '". addslashes($pcode) ."'
			";
			_MQ_noreturn($que);
			echo json_encode(array("res"=>"success" , "msg" => "성공" , "str" => $_coupon));  exit;
			break;



		// KAY :: 2021-04-12 ::------- 상품일괄 가격 관리 개별수정 ---------------
		case "price_direct_change" :

			$que = "
				p_screenPrice = '". rm_str($_screenPrice) ."'
				, p_price = '". rm_str($_price) ."'
			";

			if( $AdminPath == 'totalAdmin'){
				$que .= "
					, p_commission_type = '". $_commission_type ."'
					, p_sPrice = '". rm_str($_sPrice) ."'
					, p_sPersent = '". $_sPersent ."' 					
				";
			}

			_MQ_noreturn("UPDATE smart_product SET ".$que." WHERE p_code = '". addslashes($pcode) ."' ");
			echo json_encode(array("res"=>"success" , "msg" => "성공"));  exit;
			break;


		// KAY :: 2021-04-20 ::------- 상품일괄 노출/재고 관리 개별수정 ---------------
		case "view_direct_change" :

			$que = " p_stock = '". $_stock ."' ";
			if($AdminPath == 'totalAdmin'){ $que .= " , p_view = '". $_view ."' "; }

			_MQ_noreturn("UPDATE smart_product SET ".$que." WHERE p_code = '". addslashes($pcode) ."'");

			product_soldout_check($pcode);

			echo json_encode(array("res"=>"success" , "msg" => "성공"));  exit;
			break;


		// KAY :: 2021-04-20 ::------- 상품일괄 옵션관리 개별수정 ---------------
		case "option_direct_change" :

			// query 생성
			$que = "
				UPDATE smart_product_option SET
					po_poption_supplyprice = '". rm_str($_poption_supplyprice) ."',
					po_poptionprice = '". rm_str($_poptionprice) ."',
					po_cnt = '". $_cnt ."',
					po_view = '". $_view ."'
				WHERE
					po_uid = '". $_po_uid ."'
			";
			_MQ_noreturn($que);
			echo json_encode(array("res"=>"success" , "msg" => "성공"));  exit;
			break;

	}
	// - 모드별 처리 ---
	exit;
?>