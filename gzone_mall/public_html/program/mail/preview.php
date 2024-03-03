<?php
/*
	** LCY : 2023-01-05 : 메일미리보기 테스트 프로그램


*/
include_once $_SERVER['DOCUMENT_ROOT'].'/include/inc.php';
if( !$DeveMode){ die('Access Deny!'); } // 개발모드에서만 보일 수 있도록 


// /program/mail/preview.php?mailType=
$arrMailSet = array(
	'1'=>'<strong>주문시(결제완료시)</strong><br>(shop.order.mail_card.php)',
	'2'=>'<strong>주문시(무통장/가상계좌)</strong><br>(shop.order.mail_online.php)',
	'3'=>'<strong>주문시(배송처리시-주문상품기준)</strong><br>(shop.order.mail_delivery.php)',
	'33'=>'<strong>주문시(배송처리시 - 주문단위 NEW)</strong><br>(shop.order.mail_delivery.php)',
	'4'=>'<strong>회원가입</strong><br>(member.join.mail.php)',
	'5'=>'<strong>휴면전환시</strong><br>(member.sleep_backup.mail.php)',
	'55'=>'<strong>휴면전환 30일전</strong><br>(member.sleep_backup.mail.php)',
	'6'=>'<strong>휴면해제안내</strong><br>(member.sleep.mail.php)',
	'7'=>'<strong>임시비밀번호발송</strong><br>(member.temp_password.php)',
	'8'=>'<strong>제휴문의</strong><br>(service.request.mail.php)',
	'9'=>'<strong>매2년수신동의변경</strong><br>(mail.contents.2yearOpt.php)',
	'10'=>'<strong>매2년수신동의변경상태</strong><br>(changeAlert.mail.contents.2yearopt.php)',
	'11'=>'<strong>광고성정보수정시</strong><br>(changeAlert.mail.contents.modify.php)'
);
$ordernum = $ordernum ? $ordernum : '70400-82586-41060';
switch($mailType){
	case "1":  // 주문시(결제완료시)

		$_type = 'card';
		$_ordernum = $ordernum;
		$_title = "[".$siteInfo['s_adshop']."]주문하신 상품의 결제가 성공적으로 완료되었습니다!";
		include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = $mailing_app_content;
		$_content = get_mail_content($_content);
	break;

	case "2": // 주문시(무통장/가상계좌)

		$_type = 'online';
		$_ordernum = $ordernum;
		$_title = "[".$siteInfo['s_adshop']."]무통장 결제를 하셨습니다.";
		include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = $mailing_app_content;
		$_content = get_mail_content($_content);
	break;

	case "3": // 주문시(배송처리시)
		$_SendMode = 'order_product';
		$_type = 'delivery';
		$ordernum = '27842-40017-88310';
		$_ordernum = $ordernum;
		$_title = "[".$siteInfo['s_adshop']."]주문하신 상품의 결제가 성공적으로 완료되었습니다!";
		include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = $mailing_app_content;
		$_content = get_mail_content($_content);
		var_dump(mailer( 'wyoule@naver.com' , $_title , $_content ));
	break;

	case "33": // 주문시(배송처리시 - 주문단위(new)) -
		$_SendMode = 'order';
		$_type = 'delivery';
		$ordernum = '27842-40017-88310';
		$_ordernum = $ordernum;
		$_title = "[".$siteInfo['s_adshop']."]주문하신 상품의 결제가 성공적으로 완료되었습니다!";
		include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = $mailing_app_content;
		$_content = get_mail_content($_content);
	break;


	case "4": // 회원가입
		$_id = 'khy4'; // 아이디
		$mem_info = _MQ(" select * from smart_individual where in_id = '". $_id ."' and in_userlevel != '9' ");
        $_mailling = $mem_info['in_emailsend']; // 이메일
        $_sms = $mem_info['in_smssend']; // 문자
        $id = $_id;
        $join_name = $mem_info['in_name'];
        $join_email = $mem_info['in_email'];

        // $join_id ==> 적용
        // $join_id ==> 적용
        include_once(OD_MAIL_ROOT."/member.join.mail.php"); // 메일 내용 불러오기 ($mailing_content)
        $_title = "[".$siteInfo[s_adshop]."] 회원가입을 환영합니다.";

        $_content = get_mail_content($mailling_content);
        // var_dump(mailer( $join_email , $_title , $_content ));		
	break;

	case "5": // 휴면전환시
		$_id = 'gobeyond'; // 아이디
		$mem_info = _MQ(" select * from smart_individual where in_id = '". $_id ."' and in_userlevel != '9' ");

		$_id = $mem_info['in_id'];
		$_name = $mem_info['in_name'];
		// -- 메일링 작업 완료 시 메일 발송 기능 추가 --
		$_title = "[".$siteInfo[s_adshop]."] 휴면계정 인증을 위한 메일을 발송해드립니다.";
		include(OD_MAIL_ROOT."/member.sleep_backup.mail.php"); // 메일 내용 불러오기 ($mailling_content)
		$_content = get_mail_content($mailling_content);
		// mailer( $mem_info['in_email'], $_title, $_content);	
	break;

	case "55": // 휴면30일전 안내
		$_id = 'gobeyond'; // 아이디
		$mem_info = _MQ(" select * from smart_individual where in_id = '". $_id ."' and in_userlevel != '9' ");

		$_id = $mem_info['in_id'];
		$_name = $mem_info['in_name'];
		// -- 메일링 작업 완료 시 메일 발송 기능 추가 --
		$_title = "[".$siteInfo[s_adshop]."] 휴면전환 예정안내!";
		include(OD_MAIL_ROOT."/member.sleep_ready.mail.php"); // 메일 내용 불러오기 ($mailling_content)
		$_content = get_mail_content($mailling_content);
		// mailer( $mem_info['in_email'], $_title, $_content);	
	break;


	case "6": // 휴면해제안내
		$_id = 'gobeyond'; // 아이디
		$r = _MQ(" select * from smart_individual where in_id = '". $_id ."' and in_userlevel != '9' ");
		$email = $r['in_email'];

		$_app_auth = date("Y-m-d") . "§" . $_id . "§" . $email;
		// KAY :: 2023-01-10 :: 한국진흥원 보안패치 적용 -- 파일명 변경
		$_AUTH_URL = OD_PROGRAM_URL . "/member.slp_pro.php?_mode=auth&auth=" . onedaynet_encode( $_app_auth ) ;
		$_title = "[".$siteInfo[s_adshop]."] 휴면계정 인증을 위한 메일을 발송해드립니다.";
		include_once(OD_MAIL_ROOT."/member.sleep.mail.php"); // 메일 내용 불러오기 ($mailling_content)
		$_content = get_mail_content($mailling_content);
		// mailer( $r['in_email'], $_title, $_content);	
	break;

	case "7": // 임시비밀번호발송
		// 임시 비밀번호 발급 및 수정
		$tmp_pw = '';
		for($i=0; $i<6; $i++ ){
			if(rand(1,2) == 1)$tmp_pw .= rand(0,9); // 숫자
			else $tmp_pw .= chr(rand(97,122)); // 영문
		}

		$_title = "[{$siteInfo['s_adshop']}] 비밀번호 찾기";
		include_once(OD_PROGRAM_ROOT.'/mail/member.temp_password.php'); // 임시비밀번호 발급 메일 양식 (반환: $mailling_content)
		$_content = get_mail_content($mailling_content);
		// mailer($r['in_email'], $_title, $_content);
	break;

	case "8": // 제휴문의
		$_uid = '5'; // 고유번호
		$row = _MQ(" select * from smart_request where r_uid='{$_uid}' ");
		$_comname = $row['r_comname'];
		$_content = $row['r_content'];
		$_admcontent = $row['r_admcontent'];
	    include_once(OD_MAIL_ROOT."/service.request.mail.php"); // 메일 내용 불러오기 ($mailing_content)
		$_title = "[".$siteInfo[s_adshop]."] 제휴문의에 관해 답변드립니다.";
		$_content = get_mail_content($mailling_content);
		// -- 메일 발송
		// mailer( $_oemail, $_title , $_content );		
	break;

	case "9": // 매2년수신동의변경
		$_id = $id = 'gobeyond';
		$v = _individual_info($_id);

		
		$_name = $v['in_name'];// 이름 설정
		$_email = $v['in_email'];// 이메일 설정

		// ==> 메일 내용 불러오기 $mailling_content
		include(OD_MAIL_ROOT."/mail.contents.2yearOpt.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = get_mail_content($mailling_content);

		// mailer( $_email , $_title , $_content );
	break;

	case "10": // 매2년수신동의변경상태
		$_id = $id = 'gobeyond';
		$mem_info = _individual_info($_id);
		// 회원정보 추출
		$email = $mem_info['in_email'];

		include_once(OD_MAIL_ROOT."/changeAlert.mail.contents.2yearopt.php"); // 메일 내용 불러오기 ($mailing_content)
		$_title = "[".$siteInfo['s_adshop']."] 수신동의 상태가 변경되었습니다.";
		$_content = get_mail_content($mailling_content);
		// mailer( $email , $_title , $_content );
	break;

	case "11": // 광고성정보수정시
		$_id = $id = 'gobeyond';
        $mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' ");
        $join_name = $mem_info['in_name'];
		$_title = "[".$siteInfo[s_adshop]."] 정보수정으로 수신동의 상태가 변경되었습니다.";
		 include_once(OD_MAIL_ROOT."/changeAlert.mail.contents.modify.php"); // 메일 내용 불러오기 ($mailing_content)
		$_content = get_mail_content($mailling_content);
			// mailer( $join_email , $_title , $_content );
	break;

	default:
		$_content = '<h1>메뉴를 선택해 주세요.</h1>';
	break;


}
// echo '<div class="title"><strong>제목:</strong> '.$_title.'</div>';
echo $_content;

?>

<?php 
echo '<div class="menu">';
foreach($arrMailSet as $k=>$v){
	echo '<a href="/program/mail/preview.php?mailType='.$k.'">'.$v.'</a>';
}
echo '</div>';

?>
<style>
	.menu{  position: fixed; top:5px;right:5px;; text-align:center; width:300px; background-color:#fff; border:solid 1px #eee; }
	.menu a{ display:block; margin:0 8px;color:#333; padding: 5px 0; text-decoration:none; }
</style>