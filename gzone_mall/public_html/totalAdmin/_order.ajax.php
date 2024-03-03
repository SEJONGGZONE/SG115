<?php  // ajax 처리가 있을 경우 order.pro.php 보단 ajax 처리전용 파일에서 할 수 있도록 신규 추가
	include_once("./inc.php");


	$response = array();
	try{
		switch ($_mode) {

			// 티켓조회  $opuid 필요
			case 'ticket_search':

				// 티켓정보 조회
				$res = _MQ_assoc("select *from smart_order_product_ticket where opt_opuid = '".$opuid."' ");
				if( count($res) < 1){ throw new Exception("티켓조회에 실패하였습니다.", __LINE__);}

				// 주문상품 조회
				$row = _MQ("select *from smart_order_product as op  left join smart_product as p on (p.p_code = op.op_pcode) where op_uid = '".$opuid."' ");
				if( count($row) < 1){ throw new Exception("주문상품 조회에 실패하였습니다.", __LINE__); }

				// 팝업을 불러온다.
				ob_start();
				include_once dirname(__FILE__)."/_order.popup.inc_ticket_view.php";
				$view = ob_get_clean();

				$response['rst'] = 'success';
				$response['view'] = $view;

			break;
		}
	}
	catch(Exception $e){
		if( $e->getMessage() != ''){ $response['msg'] = $e->getMessage();  }
		if( $e->getCode() != ''){ $response['code'] = $e->getCode();  }
	}


	// 결과값 처리
	die(json_encode($response));