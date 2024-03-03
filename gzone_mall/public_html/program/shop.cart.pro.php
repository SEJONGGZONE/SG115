<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// --> 옵션/장바구니/비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();



switch($mode){

	// 프로모션 코드 LMH005
	case "promotion_code":

		// 보안용 프로모션 세션 초기화 
		if( !empty($_SESSION['secure_promotion_code'])) unset($_SESSION['secure_promotion_code']);

		$do = !$do ? 'add' : $do;
		$__result_text = ""; $__result_code = "OK"; $__result_array = array();

		function promo_output($txt,$code,$result='') { echo json_encode(array('text'=>$txt,'code'=>$code,'result'=>$result)); exit; }

		if($promotion_code=='') { $__result_text = "프로모션코드를 입력하세요."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }
		else {
			// 존재하는 코드 체크
			$chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$promotion_code."' and pr_use = 'Y' ");
			if($chk==0) { $__result_text = "잘못된 코드입니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }

			// 만료여부 체크
			$chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$promotion_code."' and pr_expire_date >= CURDATE() and pr_use = 'Y' ");
			if($chk==0) { $__result_text = "만료된 코드입니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }

			// 정보 반환
			$p = _MQ(" select * from smart_promotion_code where pr_code = '".$promotion_code."' and pr_use = 'Y' ");

			// 최소주문금액 체크 
			if( $p['pr_min_order_price'] > 0 && $priceData['priceTotal'] < $p['pr_min_order_price']){
				$__result_text = "본 프로모션코드는 ".number_format($p['pr_min_order_price'])."원 이상 구매시 사용 가능합니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code);
			}

			// 주문에 사용된 쿠폰인지 중복 체크
			if( $p['pr_due_use'] == 'N'){
				$chk2 = _MQ("select count(*) as cnt from smart_order where o_promotion_code = '".$promotion_code."' and o_mid = '".get_userid()."' and o_canceled = 'N' and if( o_paymethod in('online','virtual') , 1 , o_paystatus = 'Y')");
			
				if( $chk2['cnt'] > 0){
					$__result_text = "이미 사용된 프로모션코드 입니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); 
				}
			}

			// 할인율일 경우 최대 금액 체크 
			$price_max = 0;
			if( $p['pr_type'] == 'P' && $p['pr_price_max_use'] == 'Y' && $p['pr_price_max'] > 0 ){
				$price_max = $p['pr_price_max'];
			}

			// 프로모션 코드 할인금액/할인율에 따른 계산
			$price = 0; // 최종금액
			if( $p['pr_type'] == 'P'){
				$price = floor($priceData['priceSum']*$p['pr_amount']/100);
			}
			else{
				$price = $p['pr_amount'];
			}

			// 할인율일경우 최대할인금액 있고, 총 프로모션 헤택 금액이 최대 할인금액을 초과했을 경우
			if( $price_max > 0 && $price > $price_max){ $price = $price_max;}

			// 보안을 위해 사용한 프로모션은 배열에 저장한다. 
			$_SESSION['secure_promotion_code'] = array("code"=>$promotion_code,'price'=>$price);

			// 최종 결과 출력
			$__result_array = $_SESSION['secure_promotion_code'];
			$__result_text = "프로모션코드가 적용되었습니다."; promo_output($__result_text,$__result_code,$__result_array);
		}

	break;

	// 선택삭제
	case "select_onlydelete":
	case "_onlydelete": // JJC : 2022-07-11 : 웹 접근 제어
		// 상품코드 추출
		$_product = _MQ(" select c_pcode from smart_cart where c_uid = '".$cuid."' ");
		$code = $_product['c_pcode'];

		$que = "delete from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";
		_MQ_noreturn($que);

		// 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
		$no_addoption_cnt = _MQ(" select count(*) as cnt from smart_cart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' and c_is_addoption = 'N' ");
		if($no_addoption_cnt['cnt']==0) {
			_MQ_noreturn(" delete from smart_cart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' ");
		}

		echo 'ok'; exit;
		break;




	// 선택수량변경 - for ajax
	case "select_modify":
	case "_modify": // JJC : 2022-07-11 : 웹 접근 제어
		if(!$app_cnt) $app_cnt = $_ccnt[$cuid];
		if( $app_cnt <= 0 ) {
			echo 'error1'; exit; // 수정하실 수량은 0보다 커야 합니다.
		}


		// LCY : 2023-01-19 : 최대구매수량 체크 추가 p_buy_limit 
      	$tmpVar = _MQ("select  c.* , p_point_per , p_groupset_use , p_code , p_buy_limit from smart_product as p inner join smart_cart c on (c.c_pcode = p.p_code) where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c.c_uid = '".$cuid."'");
      	if( $tmpVar['p_buy_limit'] > 0 && $app_cnt > $tmpVar['p_buy_limit'] ){
      		die('error_buy_limit');
      	}


			// {{{회원등급혜택}}}
			if(is_login() == true && $tmpVar['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
				$c_old_price = $tmpVar['c_old_price'];
				$c_old_point = ( ($c_old_price*$app_cnt)*($tmpVar[p_point_per]/100) );
				$c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$tmpVar['p_code']);
				$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$app_cnt),'point',$tmpVar['p_code']);
				$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
				$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
			}else{
				$c_old_price = $tmpVar['c_old_price'];
				$c_old_point = ( ($c_old_price*$app_cnt)*($tmpVar[p_point_per]/100) );
				$c_price = $c_old_price;
				$c_point = $c_old_point;
				$c_groupset_price_per = 0;
				$c_groupset_point_per = 0;
			}
			$updateQue = "
				, c_price = '".$c_price."'
				, c_point = '".floor($c_point)."'
				, c_old_price = '".$c_old_price."'
				, c_old_point = '".floor($c_old_point)."'
				, c_groupset_price_per = '".$c_groupset_price_per."'
				, c_groupset_point_per = '".$c_groupset_point_per."'
			";
			// {{{회원등급혜택}}}


        // 옵션에 따른 적립금 계산 적용값을 넣어준다.
        $que = "update smart_cart set c_cnt='".$app_cnt."' ".$updateQue." where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";


		_MQ_noreturn($que);

		/* ------------ 주문 상품 재고 체크 ------------------*/
		// 주문을 위한 상품 재고 체크
		// 카트에 담긴 상품 수량을 현재 재고와 확인하여. 만약 보유 수량보다 주문량이 더 많을시,
		// 카트에 담긴 상품 수량을 강제 조정한다.
		// 함수 리턴값 (품절 : soldout , 수량이 부족 : notenough , 그외 ok)
		// 그후 엑션은 페이지에 따라서 처리한다.
		echo order_product_stock_check($_COOKIE["AuthShopCOOKIEID"]); exit;
		// case "soldout" : 장바구니 담긴 상품중 품절 된 상품이 있습니다.
		// case "notenough" : 해당 상품의 재고량이 부족합니다.
		// case "ok" : 성공
		/* ------------ // 주문 상품 재고 체크 ------------------*/
		break;


	// 선택 구매 2015-12-04 LDD
	case "select_buy":
	case "_buy": // JJC : 2022-07-11 : 웹 접근 제어

	//	error_msg( count($_cuid));
		if( count($_code) > 0 ) {
			_MQ_noreturn(" update smart_cart set c_direct = 'Y' where c_pcode in ('".implode("','",$_code)."') and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
			// 2020-03-25 SSJ :: 비회원 주문 시 로그인 페이지로 이동
			if ( $siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' ) {
				error_frame_loc("/?pn=member.login.form&_rurl=".enc('e' , 'pn=shop.order.form'));
			}else{
				error_frame_loc("/?pn=shop.order.form");
			}
		}else{
			error_frame_loc_msg("/?pn=shop.cart.list",'선택된 상품이 없습니다.');
		}

	break;




	// 다수선택삭제
	case "select_delete":
	case "_delete":// JJC : 2022-07-11 : 웹 접근 제어

		if( sizeof($_code) == 0 ) {
			echo 'error1'; exit;  // 1개이상 선택해주시기 바랍니다.
		}

		// 값이 key 에 있는지 val 에 있는지 체크하여 처리한다.
		if($_code[0]) $_code_array = implode("','" , $_code);
		else $_code_array = implode("','" , array_keys($_code));

		$que = "delete from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_pcode  in ('".$_code_array."') ";
		_MQ_noreturn($que);

		echo 'ok'; exit;
		break;



	// - 다수 선택 추가  ---
	case "select_add":
	case "_add":// JJC : 2022-07-11 : 웹 접근 제어

		if( sizeof($pcode_array) == 0 ) {
			error_msg("1개이상 선택해주시기 바랍니다.");
		}

		for($i=0;$i<count($pcode_array);$i++) {
			$pcode = $pcode_array[$i];

			if( !$pass_type ) {
				$pass_type = "cart";
			}

			// 이미 담긴 상품인지 체크
			$cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
			if($cnt_tmp[cnt] > 0) continue;

			// 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
			$pinfo = get_product_info($pcode);
			$c_supply_price = $pinfo[p_commission_type] == "공급가" ? $pinfo[p_sPrice] : $pinfo[p_price] - round($pinfo[p_price] * $pinfo[p_sPersent] / 100);

			// {{{회원등급혜택}}}
			if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
				$c_old_price = $pinfo['p_price'];
				$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
				$c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$pinfo['p_code']);
				$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$c_cnt),'point',$pinfo['p_code']);
				$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
				$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
			}else{
				$c_old_price = $pinfo['p_price'];
				$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
				$c_price = $c_old_price;
				$c_point = $c_old_point;
				$c_groupset_price_per = 0;
				$c_groupset_point_per = 0;
			}

			$add_que = "
				,c_old_price = '".$c_old_price."'
				,c_old_point = '".floor($c_old_point)."'
				,c_groupset_price_per = '".$c_groupset_price_per."'
				,c_groupset_point_per = '".$c_groupset_point_per."'
			";
			// {{{회원등급혜택}}}


			// JJC : 2022-07-11 : 웹 접근 제어
			$sque = "
				insert smart_cart set
					c_pcode = '". $pcode ."'
				, c_cnt = '1'
				, c_pouid = '0'
				".$sque_tmp."
				, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
				, c_rdate = now()
				, c_supply_price = '".$c_supply_price."'
				, c_price = '". $c_price ."'
				, c_point = '".floor($c_point)."'
				, c_direct			= '".($pass_type=='order'?'Y':'N')."'

				".$add_que."
			";

			_MQ_noreturn($sque);

		}	// end for



		if( $pass_type == "order" ) {
			// 2020-03-25 SSJ :: 비회원 주문 시 로그인 페이지로 이동
			if ( $siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' ) {
				error_loc("/?pn=member.login.form&_rurl=".enc('e' , 'pn=shop.order.form'));
			}else{
				error_loc("/?pn=shop.order.form");
			}
		}
		else {
			error_loc("/?pn=shop.cart.list");
		}
		break;




	// - 추가 (상세페이지로부터 넘겨져옴) ---
	case "add":
		// 넘겨져온 변수
		//pcode=$code&pass_type=type(order:주문하기/cart:장바구니)
		$pcode = nullchk($pcode , "상품을 선택해주시기 바랍니다.");
		if( !$pass_type ) {
			$pass_type = "cart";
		}

		_MQ_noreturn(" update smart_cart set c_direct = 'N' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");// 선택 구매 2015-12-04 LDD

		// LCY : 2021-03-22 : 상품 티켓유형 추가 -- 중복구매체크 - 보류
		$_duplicate_use = _MQ_result("select p_duplicate_use from smart_product where p_code = '".$pcode."' ");
		if($_duplicate_use != 'Y') {

			$orderCheckTmp = _MQ_result("
				select count(*) from smart_order_product as op
				inner join smart_order as o on (o.o_ordernum=op.op_oordernum and o.o_paystatus='Y' and o_memtype = 'Y' and o.o_canceled='N' and o_mid= '".get_userid()."' )
				where op.op_pcode ='".$pcode."' and op.op_cancel = 'N'
			");
			if($orderCheckTmp > 0) {
				error_alt("중복구매가 불가능한 상품입니다.");
			}
			else{
				// SSJ: 2017-10-12 중복구입이 불가능한 상품 체크후 무통장/가상계좌로 주문된 건이 있는지 체크 
				$orderCheckTmp = _MQ_result("
					select count(*) from smart_order_product as op
					inner join smart_order as o on (o.o_ordernum=op.op_oordernum and (o.o_paystatus='N' and o.o_paymethod in ('online','virtual')) and o.o_canceled='N' and o_mid= '".get_userid()."'   )
					where op.op_pcode ='".$pcode."' and op.op_cancel = 'N'
				");
				if($orderCheckTmp > 0) {
					error_alt("중복구매가 불가능한 상품입니다.\\n무통장/가상계좌 주문을 확인해주시기 바랍니다.");
				}
			}
		}


		// 옵션 없는 경우
		if( $option_select_type == "nooption" ) {
			// 장바구니 넣기

			// 이미 담긴 상품인지 체크
			$cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
			//if($cnt_tmp[cnt] > 0) error_frame_loc_msg("/?pn=shop.cart.list","이미 장바구니에 담긴 상품입니다.");
			//if($cnt_tmp[cnt] > 0 && $pass_type=='cart') error_alt("이미 장바구니에 담긴 상품입니다.");


			// 같은 상품은 삭제한다
			_MQ_noreturn("delete from smart_cart where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");


			$c_cnt = $option_select_cnt > 1 ? $option_select_cnt : 1;

			// 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
			$pinfo = get_product_info($pcode);
			$c_supply_price = $pinfo[p_commission_type] == "공급가" ? $pinfo[p_sPrice] : $pinfo[p_price] - round($pinfo[p_price] * $pinfo[p_sPersent] / 100);

			// {{{회원등급혜택}}}
			if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
				$c_old_price = $pinfo['p_price'];
				$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
				$c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$pinfo['p_code']);
				$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$c_cnt),'point',$pinfo['p_code']);
				$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
				$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
			}else{
				$c_old_price = $pinfo['p_price'];
				$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
				$c_price = $c_old_price;
				$c_point = $c_old_point;
				$c_groupset_price_per = 0;
				$c_groupset_point_per = 0;
			}

			$add_que = "
				,c_old_price = '".$c_old_price."'
				,c_old_point = '".floor($c_old_point)."'
				,c_groupset_price_per = '".$c_groupset_price_per."'
				,c_groupset_point_per = '".$c_groupset_point_per."'
			";
			// {{{회원등급혜택}}}

			$sque = "
				insert smart_cart set
				  c_pcode = '". $pcode ."'
				, c_cnt = '".$c_cnt."'
				, c_pouid = '0'
				".$sque_tmp."
				, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
				, c_rdate = now()
				, c_supply_price = '".$c_supply_price."'
				, c_price = ".$c_price."
				, c_point = ".floor($c_point)."
				, c_direct				= '".($pass_type=='order'?'Y':'N')."'

				".$add_que."
			";


			_MQ_noreturn($sque);
		}
		else {
			// 선택옵션 정보 추출
			$que = "select * from smart_product_tmpoption where pto_mid='".$_COOKIE["AuthShopCOOKIEID"]."' order by pto_uid asc ";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){


				// LCY : 2022-12-21 : 티켓기능 -- 달력옵션
				$add_que_date = ''; 
				if($v['pto_dateoption_use'] == 'Y'){
					$add_que_date = " and c_dateoption_date = '".$v['pto_dateoption_date']."' and c_dateoption_use = '".$v['pto_dateoption_use']."' "; // 같은 날짜의 경우 제외한다.
				}
				// LCY : 2022-12-21 : 티켓기능 -- 달력옵션



				// 같은 상품은 삭제한다
				_MQ_noreturn("delete from smart_cart where c_pcode = '". $pcode ."' and c_pouid = '".$v[pto_pouid]."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ".$add_que_date." ");

				// 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
				$pinfo = get_product_info($pcode);
				$c_supply_price = $pinfo[p_commission_type] == "공급가" ? $v[pto_poption_supplyprice] : $v[pto_poptionprice] - round($v[pto_poptionprice] * $pinfo[p_sPersent] / 100);



				// {{{회원등급혜택}}}
				if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
					$c_old_price = $v[pto_poptionprice];
					$c_old_point = ( ($c_old_price*$v[pto_cnt])*($pinfo[p_point_per]/100) );
					$c_price = $c_old_price - getGroupSetPer( $c_old_price,'price',$v['pto_pcode']);
					$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$v[pto_cnt]),'point',$v['pto_pcode']);
					$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
					$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
				}else{
					$c_old_price = $v[pto_poptionprice]; // 기존금액
					$c_old_point = ( ($c_old_price*$v[pto_cnt])*($pinfo[p_point_per]/100) );  // 기존금액
					$c_price = $c_old_price;
					$c_point = $c_old_point;
					$c_groupset_price_per = 0;
					$c_groupset_point_per = 0;
				}
				$add_que = "
					,c_old_price = '".$c_old_price."'
					,c_old_point = '".floor($c_old_point)."'
					,c_groupset_price_per = '".$c_groupset_price_per."'
					,c_groupset_point_per = '".$c_groupset_point_per."'
				";
				// {{{회원등급혜택}}}


				// LCY : 2022-12-21 : 티켓기능 -- 달력옵션 { 
				if( $v['pto_dateoption_use'] == 'Y') { 
					$add_que .= " , c_dateoption_use = '".$v['pto_dateoption_use']."' ";
					$add_que .= " , c_dateoption_date = '".$v['pto_dateoption_date']."' ";							
				}
				// LCY : 2022-12-21 : 티켓기능 -- 달력옵션 }

				// 장바구니 넣기
				$sque = "
					insert smart_cart set
						c_pcode = '". $pcode ."'
						, c_option1 = '". addslashes($v[pto_poptionname1])."'
						, c_option2 = '". addslashes($v[pto_poptionname2])."'
						, c_option3 = '". addslashes($v[pto_poptionname3])."'
						, c_cnt = '".$v[pto_cnt]."'
						, c_pouid = '".$v[pto_pouid]."'
						".$sque_tmp."
						, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
						, c_rdate = now()
						, c_supply_price = '". $c_supply_price."'
						, c_price = '". $c_price."'
						, c_point = '".  floor($c_point) ."'
						, c_direct = '".($pass_type=='order'?'Y':'N')."'
						, c_is_addoption = '". $v['pto_is_addoption']."'
						, c_addoption_parent = '". $v['pto_addoption_parent']."'

						".$add_que."


				";
				_MQ_noreturn($sque);
			}
			_MQ_noreturn("delete from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' ");
		}

		if( $pass_type == "order" ) {
			// === 비회원구매 설정 kms 2019-06-25 ====
			if ( $none_member_buy === true ) {
				error_confirm_msg("/?pn=shop.order.form", '구매하기는 로그인 후 이용하실 수 있습니다.\n\n로그인 페이지로 이동하시겠습니까?', '/?pn=shop.cart.list' );
			}else{
				// 2020-03-25 SSJ :: 비회원 주문 시 로그인 페이지로 이동
				if ( $siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' ) {
					error_frame_loc("/?pn=member.login.form&_rurl=".enc('e' , 'pn=shop.order.form'));
				}else{
					error_frame_loc("/?pn=shop.order.form");
				}
			}
			// === 비회원구매 설정 kms 2019-06-25 ====

		}
		else {

			$cart_cnt = get_cart_cnt();
			// 2016-05-23 장바구니 담은 후 레이어팝업으로 물어보기 - 추가
			
			if( preg_match("/product.view/i" , $_SERVER["HTTP_REFERER"]) && $pass_mode == '' ) {
				/*echo '
					<script src="/include/js/jquery-1.11.2.min.js"></script>
					<script >
						$(document).ready(function(){
							$(".js_product_view_cart[data-pcode=\''.$pcode.'\']" , parent.document).addClass("hit");
							$(".view_cart_ask" , parent.document).addClass("if_cart_save");
							$(".glb_cart_cnt" , parent.document).text('. $cart_cnt .');
							$("iframe[name=common_frame]" , parent.document).attr("src" , "about:blank");
						});
					</script>
				';*/

				echo '
					<script src="/include/js/jquery-1.11.2.min.js"></script>
					<script >
						$(document).ready(function(){
							parent.reload_cart("add", "'.$pcode.'");
							$("body" , parent.document).addClass("if_cart_save");
							$(".js_cart_cnt" , parent.document).show();
							$(".js_cart_cnt" , parent.document).text('. $cart_cnt .');
							$("iframe[name=common_frame]" , parent.document).attr("src" , "about:blank");
						});
					</script>
				';
			}
			// 목록에서 바로 담을 경우 처리
			else {
				//error_frame_loc('/?pn=shop.cart.list');
				echo '
					<script src="/include/js/jquery-1.11.2.min.js"></script>
					<script >
						$(document).ready(function(){
							parent.reload_cart("add", "'.$pcode.'");
							$(".js_cart_cnt" , parent.document).show();
							$(".js_cart_cnt" , parent.document).text('. $cart_cnt .');
				';
				
				// 최근본 상품에서 장바구니 담았을때만 새로고침없이 장바구니 불러오도록 카트 list 불러오기
				if($pass_view_type=='latest'){
						echo '
							parent.cart_view();
						';
				}

				echo'
							$("iframe[name=common_frame_tmp]" , parent.document).attr("src" , "about:blank");
							$("iframe[name=common_frame_tmp]" , parent.document).remove();
						});
					</script>
				';
			}
		}
		break;




}


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행