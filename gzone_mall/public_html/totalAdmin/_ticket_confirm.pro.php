<?php
	include_once('inc.php');

	switch($_mode){
		case "ticket_use":

			if( $type == "use" ) {

				// 티켓정보 체크 
				$ticketInfo = _MQ("select *from smart_order_product_ticket where opt_uid ='".$uid."'  ");
				if( count($ticketInfo) < 1){ die(json_encode(array('rst'=>'fail','msg'=>'사용할 수 없는 티켓입니다.'))); }
				if( $ticketInfo['opt_status'] != '대기'){ die(json_encode(array('rst'=>'fail','msg'=>'사용할 수 없는 티켓입니다.')));  }
				update_order_ticket_status($ticketInfo['opt_uid'],'사용');


				// 문자 발송유형
				$ss_uid = "ticket_use";
				$result = true;
			}
			else if( $type == "unuse" ) {

				// 티켓정보 체크 
				$ticketInfo = _MQ("select *from smart_order_product_ticket where opt_uid ='".$uid."'   ");
				if( count($ticketInfo) < 1){ die(json_encode(array('rst'=>'fail','msg'=>'사용할 수 없는 티켓입니다.')));	}
				if( $ticketInfo['opt_status'] != '사용'){ die(json_encode(array('rst'=>'fail','msg'=>'현재 사용처리가 불가능한 티켓입니다.'))); }

				update_order_ticket_status($ticketInfo['opt_uid'],'대기');	

				// 문자 발송유형
				$ss_uid = "ticket_unuse";
				$result = true;
			}
			else{
				die(json_encode(array('rst'=>'fail','msg'=>'티켓 처리에 실패하였습니다.')));	
			}

			// 티켓사용시 문자알림
			if($result) {
				$r = _MQ("select o_ohp, o_uhp from smart_order where o_ordernum = '".$ticketInfo['opt_oordernum']."' ");
				$sms_to = $r['o_uhp'] ? $r['o_uhp'] : $r['o_ohp'];
				// shop_send_sms($sms_to,'ticket_'.$type,$ticketInfo['opt_uid']);
			}
			
			die(json_encode(array('rst'=>'success')));

		break;

	}
