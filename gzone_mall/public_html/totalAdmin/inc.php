<?php
if (!defined('_OD_DIRECT_')) define('_OD_DIRECT_', true);
@ini_set("precision", "20"); // 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
if(empty($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');


// - 넘길 변수 설정하기 ---
if(preg_match("/.list.php/i" , $CURR_FILENAME)){
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {if(is_array($val)) {foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }}else {$_PVS .= "&$key=$val";}}
	$_PVSC = enc('e' , $_PVS);
}
// - 넘길 변수 설정하기 ---


# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData);

// 상세검색 캐시 이름 정의
$searchDetailCacheKey = $AdminPath.':'.$CURR_FILENAME;

// 검색 상세 열고/닫기 캐시가 있을 경우
$search_detail_cache = array();
if( $_COOKIE['search_detail_cache'] != ''){
	$search_detail_cache = base64_unserialize($_COOKIE['search_detail_cache']);
	$search_detail_cache = array_filter($search_detail_cache);
}


// -- 관리자체크 ==> 처음로그인시 저장된 암호화 세션(라이선스+...+db계정uid) 와 쿠키에 저장도니 값을 비교하여 조작이 있을 시 튕겨내기
if(isset($app_mode) && $app_mode === 'popup' && AdminLoginCheck('value') === false) {
	if($AdminPath == 'subAdmin' && SubAdminLoginCheck('value') === false) error_msgPopup('권한이 없습니다.'); // 팝업모드에서는 권한이 없는 경우 팝업을 닫고 부모창을 새로고침 한다.
	else error_msgPopup('권한이 없습니다.'); // 팝업모드에서는 권한이 없는 경우 팝업을 닫고 부모창을 새로고침 한다.
}
if($AdminPath == 'subAdmin') SubAdminLoginCheck();
else AdminLoginCheck();

// -- 현재 페이지 체크  :: 페이지가 파일명 + 파라미터로 되어있다면
$app_current_link = (isset($app_current_link)?$app_current_link:$CURR_FILENAME);
if(isset($tempUid) && $tempUid != '') $menuUid = $tempUid;
if(empty($menuUid)) $menuUid = '';


if($AdminPath == 'totalAdmin') {

	// -- 현재 페이지로 부터 고유번호 추출 -- 2017-07-25 LCY
	$temp_key = array('uid','idx','depth','view','parent','name','link');
	$temp_select = array(); // or am1.am_uid = '".$uid."'  or am2.am_uid = '".$uid."' or am3.am_uid = '".$uid."'
	for( $i=1; $i <= 3; $i++) { foreach($temp_key as $k=>$v){ $temp_select[] = 'am'.$i.'.am_'.$v.' as am'.$i.'_'.$v;   } }
	$current_page_info = _MQ(" select ".( count($temp_select) > 0 ? implode(",",$temp_select):'*' )."
	from smart_admin_menu as am3
	inner join smart_admin_menu as am2 on (substring_index(am3.am_parent , ',' ,-1) = am2.am_uid and am2.am_depth=2)
	inner join smart_admin_menu as am1 on (substring_index(am3.am_parent , ',' ,1) = am1.am_uid and am1.am_depth=1)
	where am3.am_link = '".$app_current_link."' or am1.am_uid = '".$menuUid."'  or am2.am_uid = '".$menuUid."' or am3.am_uid = '".$menuUid."'
	");

	// -- 접근 가능한 운영자 메뉴정보를 배열로 받는다
	$siteAdminMenuChk = adminMenuChk($app_current_link , $menuUid); // 메뉴체크
	$siteAdminMenuSet = adminMenuSet(); // 메뉴권한 배열로 받기
}



# 페이지별 메뉴얼
$MLink = parse_url($_SERVER['REQUEST_URI']);
$MLink = end(explode('/', $MLink['path']));
if($_menuType) $MLink = $MLink.'?_menuType='.$_menuType;
if($pass_menu && !in_array($MLink, array('_cntlog.php', '_cntlog_route.php'))) $MLink = $MLink.'?pass_menu='.$pass_menu;
if($MLink == '_mailing_premium.view.php' && $_view) $MLink = $MLink.'?_view='.$_view;
if($MLink == '_order.form.php' && $view) $MLink = $MLink.'?view='.$view;
if($MLink == '_order.list.php' && $view) $MLink = $MLink.'?view='.$view;
$ManualBaseLink = 'http://www.onedaynet.co.kr/manual/hyssence_plus/pages/';
$ManualLink = array(
	// =============================================================== //
	// 환경설정 -> 기본설정
		  '_config.default.form.php'=>$ManualBaseLink.'set_1.html#1_1' // 쇼핑몰 기본정보
		, '_config.agree.form.php'=>$ManualBaseLink.'set_1.html#1_2' // 약관 및 정책 설정
        , '_config.usage.php'=>$ManualBaseLink.'set_1.html#1_3' // 이용안내 설정
        , '_addons.php?_menuType=smsEmail'=>$ManualBaseLink.'set_1.html#1_4' // 문자/이메일 수신관련설정
        , '_addons.php?_menuType=080'=>$ManualBaseLink.'set_1.html#1_5' // 080 수신거부 관련설정
		, '_config.title.form.php'=>$ManualBaseLink.'set_1.html#1_6' // 페이지 타이틀 설정
        , '_config.ssl.default_form.php'=>$ManualBaseLink.'set_1.html#1_7' // 보안서버 설정

    // 환경설정 -> 운영 관리 설정
		, '_config.admin_menu.list.php'=>$ManualBaseLink.'set_2.html#1_1' // 관리자 메뉴관리
        , '_config.admin.list.php'=>$ManualBaseLink.'set_2.html#1_2' // 운영자 목록
		, '_config.admin.form.php'=>$ManualBaseLink.'set_2.html#1_2' // 운영자 등록/수정
		, '_config.admin_menuset.form.php'=>$ManualBaseLink.'set_2.html#1_3' // 운영자별 메뉴 노출설정
        , '_solution.info.php'=>$ManualBaseLink.'set_2.html#1_4' // 솔루션 이용현황
        , '_config.sns.form.php'=>$ManualBaseLink.'set_2.html#1_5' // SNS 로그인/API 설정
        , '_config.device.form.php'=>$ManualBaseLink.'set_2.html#1_6' // 파비콘/공유이미지 설정
        , '_config.editor_img.list.php'=>$ManualBaseLink.'set_2.html#1_7' // 에디터 등록 이미지 관리
        , '_config.favmenu.list.php'=>$ManualBaseLink.'set_2.html#1_8' // 자주쓰는 메뉴 설정

	// 환경설정 -> SMS/알림톡 관리
		, '_config.sms.form.php'=>$ManualBaseLink.'set_3.html#1_1' // SMS/알림톡 정보설정
		, '_sms.form.php'=>$ManualBaseLink.'set_3.html#1_2' // 개별/전체 SMS 발송
		, '_config.sms.out_send_list.php'=>$ManualBaseLink.'set_3.html#1_3' // 발송내역
		, '_config.sms.out_list.php'=>$ManualBaseLink.'set_3.html#1_4' // 충전관리
		, '_sms.log.php'=>$ManualBaseLink.'set_3.html#1_5' // SMS 에러로그

    // 환경설정 -> 결제 관련 설정
		, '_config.paymethod.php'=>$ManualBaseLink.'set_4.html#1_1' // 결제 수단 사용 설정
        , '_config.pg.form.php'=>$ManualBaseLink.'set_4.html#1_2' // 통합 전자결제(PG) 관리
        , '_config.pg_easypay.form.php'=>$ManualBaseLink.'set_4.html#1_3' // 간편결제 설정
        , '_config.pg_naver.form.php'=>$ManualBaseLink.'set_4.html#1_4' // 네이버페이 설정
        , '_config.vat.form.php'=>$ManualBaseLink.'set_4.html#1_5' // 부가세율 설정
        , '_config.tax.form.php'=>$ManualBaseLink.'set_4.html#1_6' // 바로빌 서비스
        , '_config.none_bank.php'=>$ManualBaseLink.'set_4.html#1_7' // 무통장입금 은행 관리
        , '_config.orderbank.form.php'=>$ManualBaseLink.'set_4.html#1_8' // 실시간 입금확인 서비스
		, '_config.paycancel.php'=>$ManualBaseLink.'set_4.html#1_9' // 주문 취소 관련설정

	// 환경설정 -> 상품/배송/정산 설정
		, '_config.product.form.php'=>$ManualBaseLink.'set_5.html#1_1' // 상품/정산 기본설정
		, '_config.delivery.form.php'=>$ManualBaseLink.'set_5.html#1_2' // 배송 기본설정
		, '_config.delivery_addprice.list.php'=>$ManualBaseLink.'set_5.html#1_3' // 도서산간 추가배송비 설정

	// 환경설정 -> 회원 관련 설정
		, '_config.member.orderway.form.php'=>$ManualBaseLink.'set_6.html#1_1' // 회원/비회원 기본 설정
		, '_config.password.config.php'=>$ManualBaseLink.'set_6.html#1_2' // 비밀번호 관련설정
		, '_config.member.form.php'=>$ManualBaseLink.'set_6.html#1_3' // 본인확인 서비스
        , '_config.cntlog.form.php'=>$ManualBaseLink.'set_6.html#1_4' // 접속 로그 설정



	// =============================================================== //
	// 회원관리 -> 회원 관리
		, '_individual.list.php'=>$ManualBaseLink.'user_1.html#1_1' // 회원 목록
		, '_individual.form.php'=>$ManualBaseLink.'user_1.html#1_1' // 회원 등록/수정
        , '_individual_sleep.list.php'=>$ManualBaseLink.'user_1.html#1_2' // 휴면 회원 목록
        , '_individual_out.list.php'=>$ManualBaseLink.'user_1.html#1_3' // 탈퇴 회원 목록

    // 회원관리 -> 회원등급 및 정책
        , '_member_group_set.list.php'=>$ManualBaseLink.'user_2.html#1_1' // 회원등급 관리
        , '_member_group_set.form.php'=>$ManualBaseLink.'user_2.html#1_1' // 회원등급 등록/수정
        , '_config.group.php'=>$ManualBaseLink.'user_2.html#1_2' // 회원등급 정책
        , '_config.join.php'=>$ManualBaseLink.'user_2.html#1_3' // 가입 승인 및 입력항목
        , '_config.sleep.php'=>$ManualBaseLink.'user_2.html#1_4' // 휴면회원 정책

	// 회원관리 -> 적립금 관리
		, '_point.list.php'=>$ManualBaseLink.'user_3.html#1_1' // 적립금 내역
		, '_point.form.php'=>$ManualBaseLink.'user_3.html#1_1' // 적립금 등록/수정
		, '_config.point.form.php'=>$ManualBaseLink.'user_3.html#1_2' // 자동 적립금 설정

	// 회원관리 -> 메일링 관리
		, '_mailing_data.list.php'=>$ManualBaseLink.'user_4.html#1_1' // 이메일 발송 목록
		, '_mailing_data.form.php'=>$ManualBaseLink.'user_4.html#1_1' // 이메일 발송 목록(폼)



	// =============================================================== //
	// 상품관리 -> 상품 관리
		, '_product.list.php'=>$ManualBaseLink.'item_1.html#1_1' // 배송상품 목록
        , '_product.form.php'=>$ManualBaseLink.'item_1.html#1_1' // 배송상품 등록/수정
		, '_product_ticket.list.php'=>$ManualBaseLink.'item_1.html#1_2' // 티켓상품 목록
		, '_product_ticket.form.php'=>$ManualBaseLink.'item_1.html#1_2' // 티켓상품 등록/수정
		, '_product.common_option_set.list.php'=>$ManualBaseLink.'item_1.html#1_3' // 자주쓰는 옵션 관리
		, '_product.common_option_set.form.php'=>$ManualBaseLink.'item_1.html#1_3' // 자주쓰는 옵션 등록/수정
		, '_product_wish.list.php'=>$ManualBaseLink.'item_1.html#1_4' // 찜한상품 목록

    // 상품관리 -> 상품 일괄 관리
		, '_product_mass.price.php'=>$ManualBaseLink.'item_2.html#1_1' // 가격/정산 설정
		, '_product_mass.view.php'=>$ManualBaseLink.'item_2.html#1_2' // 노출/재고 설정
		, '_product_mass.point.php'=>$ManualBaseLink.'item_2.html#1_3' // 적립율/상품쿠폰 설정
		, '_product_mass.move.php'=>$ManualBaseLink.'item_2.html#1_4' // 카테고리 이동/복사/추가
		, '_product_mass.option.php'=>$ManualBaseLink.'item_2.html#1_5' // 옵션 설정

	// 상품관리 -> 상품 진열 관리
		, '_config.display.main.php'=>$ManualBaseLink.'item_3.html#1_1' // 메인 노출 상품
        , '_config.display.category.php'=>$ManualBaseLink.'item_3.html#1_2' // 메인 카테고리 베스트 상품
		, '_config.display.timesale.php'=>$ManualBaseLink.'item_3.html#1_3' // 타임세일 상품설정
		, '_config.display.search.php'=>$ManualBaseLink.'item_3.html#1_4' // 상품 검색 설정
		, '_config.display.type.php'=>$ManualBaseLink.'item_3.html#1_5' // 타입별 상품관리
        , '_product_icon.list.php'=>$ManualBaseLink.'item_3.html#1_6' // 상품 아이콘 관리
        , '_product_icon.form.php'=>$ManualBaseLink.'item_3.html#1_6' // 상품 아이콘 등록/수정

    // 상품관리 -> 상품 상세페이지 설정
        , '_config.display.pinfo.php'=>$ManualBaseLink.'item_4.html#1_1' // 노출 항목설정
        , '_product.guide.list.php'=>$ManualBaseLink.'item_4.html#1_2' // 이용안내 관리
        , '_product.guide.form.php'=>$ManualBaseLink.'item_4.html#1_2' // 이용안내 등록/수정

	// 상품관리 -> 카테고리 관리
		, '_category.list.php'=>$ManualBaseLink.'item_5.html#1_1' // 상품 카테고리
        , '_brand.list.php'=>$ManualBaseLink.'item_5.html#1_2' // 브랜드 설정




    // =============================================================== //
	// 주문/배송 -> 주문관리
		, '_order.list.php'=>$ManualBaseLink.'order_1.html#1_1' // 전체 주문
		, '_order.form.php'=>$ManualBaseLink.'order_1.html#1_1' // 전체 주문 상세
        , '_order.list.php?view=online'=>$ManualBaseLink.'order_1.html#1_2' // 입금대기 주문
        , '_order.form.php?view=online'=>$ManualBaseLink.'order_1.html#1_2' // 입금대기 주문 상세
        , '_npay_order.list.php'=>$ManualBaseLink.'order_1.html#1_3' // 네이버페이 주문
		, '_npay_order.form.php'=>$ManualBaseLink.'order_1.html#1_3' // 네이버페이 주문 상세

	// 주문/배송 -> 배송/발급 관리
		, '_order_delivery.list.php'=>$ManualBaseLink.'order_2.html#1_1' // 주문별 배송처리
		, '_order.form.php?view=order_delivery'=>$ManualBaseLink.'order_2.html#1_1' // 주문별 배송처리 상세
		, '_order_delivery.excel_form.php'=>$ManualBaseLink.'order_2.html#1_1' // 주문별 배송처리 엑셀 업로드
		, '_order_product.list.php'=>$ManualBaseLink.'order_2.html#1_2' // 상품별 배송처리
		, '_order.form.php?view=order_product'=>$ManualBaseLink.'order_2.html#1_2' // 상품별 배송처리 상세
		, '_order_product.excel_form.php'=>$ManualBaseLink.'order_2.html#1_2' // 상품별 배송처리 엑셀 업로드
		, '_order_ticket.list.php'=>$ManualBaseLink.'order_2.html#1_3' // 티켓 발급목록
		, '_order.form.php?view=order_ticket'=>$ManualBaseLink.'order_2.html#1_3' // 티켓 발급목록 상세

	// 주문/배송 -> 취소/교환/반품/환불
		, '_order.cancel_list.php'=>$ManualBaseLink.'order_3.html#1_1' // 주문 취소 목록
		, '_order.form.php?view=cancel'=>$ManualBaseLink.'order_3.html#1_1' // 주문 취소 목록 상세
		, '_cancel.list.php'=>$ManualBaseLink.'order_3.html#1_2' // 부분 취소 목록
        , '_cancel.form.php'=>$ManualBaseLink.'order_3.html#1_2' // 부분 취소 목록 상세
		, '_order_complain.list.php'=>$ManualBaseLink.'order_3.html#1_3' // 교환/반품 신청 목록
		, '_order.form.php?view=order_complain'=>$ManualBaseLink.'order_3.html#1_3' // 교환/반품 신청 목록 상세
		, '_cancel_order.list.php'=>$ManualBaseLink.'order_3.html#1_4' // 환불 요청 목록

    // 주문/배송 -> 정산관리
        , '_order3.list.php'=>$ManualBaseLink.'order_4.html#1_1' // 정산 대기 목록
        , '_order3.view.php'=>$ManualBaseLink.'order_4.html#1_1' // 정산 대기 목록 상세
        , '_order4.list.php'=>$ManualBaseLink.'order_4.html#1_2' // 정산 완료 목록
        , '_order4.view.php'=>$ManualBaseLink.'order_4.html#1_2' // 정산 완료 목록 상세
        , '_ordercalc.view.php'=>$ManualBaseLink.'order_4.html#1_3' // 정산 현황

	// 주문/배송 -> 자동 입금 확인
		, '_orderbanklog.list.php'=>$ManualBaseLink.'order_5.html#1_1' // 실시간 입금 확인 내역
		, '_online_notice.list.php'=>$ManualBaseLink.'order_5.html#1_2' // 미확인 입금자 관리

	// 주문/배송 -> 현금영수증 관리
		, '_cashbill.list.php'=>$ManualBaseLink.'order_6.html#1_1' // 현금영수증 발급 내역
        , '_cashbill.form.php'=>$ManualBaseLink.'order_6.html#1_2' // 현금영수증 개별발급

	// 주문/배송 -> 전자세금계산서 관리
		, '_tax.list.php'=>$ManualBaseLink.'order_7.html#1_1' // 세금계산서 발급 내역
        , '_tax.form.php'=>$ManualBaseLink.'order_7.html#1_2' // 세금계산서 개별발급




    // =============================================================== //
    // 리뷰/문의 -> 상품 리뷰/문의 관리
        , '_product_talk.list.php'=>$ManualBaseLink.'talk_1.html#1_1' // 상품 리뷰 목록
        , '_product_talk.form.php'=>$ManualBaseLink.'talk_1.html#1_1' // 상품 리뷰 수정
        , '_product_talk.list.php'=>$ManualBaseLink.'talk_1.html#1_1' // 상품 문의 목록
        , '_product_talk.form.php'=>$ManualBaseLink.'talk_1.html#1_1' // 상품 문의 수정
        , '_config.display.review.php'=>$ManualBaseLink.'talk_1.html#1_2' // 메인 리뷰 설정

    // 리뷰/문의 -> 상담 관리
        , '_request.list.php?pass_menu=inquiry'=>$ManualBaseLink.'talk_2.html#1_1' // 1:1 온라인 문의 목록
        , '_request.list.php?pass_menu=partner'=>$ManualBaseLink.'talk_2.html#1_2' // 제휴 문의 목록




    // =============================================================== //
	// 게시판 -> 게시판 관리
		, '_bbs.board.list.php'=>$ManualBaseLink.'bbs_1.html#1_1' // 게시판 목록
		, '_bbs.board.form.php'=>$ManualBaseLink.'bbs_1.html#1_1' // 게시판 등록/수정
		, '_bbs.post_mng.list.php'=>$ManualBaseLink.'bbs_1.html#1_2' // 게시글 전체 목록
		, '_bbs.post_mng.form.php'=>$ManualBaseLink.'bbs_1.html#1_2' // 게시글 등록/수정
		, '_bbs.forbidden_word.form.php'=>$ManualBaseLink.'bbs_1.html#1_3' // 게시판 금지어 관리
		, '_bbs.post_template.list.php'=>$ManualBaseLink.'bbs_1.html#1_4' // 게시글 양식 관리
		, '_bbs.post_template.form.php'=>$ManualBaseLink.'bbs_1.html#1_4' // 게시글 양식 등록/수정

    // 게시판 -> 게시판 관리
        , '_bbs.post_faq.list.php'=>$ManualBaseLink.'bbs_2.html#1_1' // FAQ 전체 목록
        , '_bbs.post_faq.form.php'=>$ManualBaseLink.'bbs_2.html#1_1' // FAQ 등록/수정
        , '_bbs.post_faq_type.form.php'=>$ManualBaseLink.'bbs_2.html#1_2' // 분류 및 키워드 관리



	// =============================================================== //
	// 디자인 -> 배너/팝업 관리
        , '_banner.list.php'=>$ManualBaseLink.'design_1.html#1_1' // 배너 관리
        , '_banner.form.php'=>$ManualBaseLink.'design_1.html#1_1' // 배너 등록/수정
        , '_popup.list.php'=>$ManualBaseLink.'design_1.html#1_2' // 팝업 관리
        , '_popup.form.php'=>$ManualBaseLink.'design_1.html#1_2' // 팝업 등록/수정

	// 디자인 -> 디자인 관리
        , '_normalpage.list.php'=>$ManualBaseLink.'design_2.html#1_1' // 일반페이지 관리
        , '_normalpage.form.php'=>$ManualBaseLink.'design_2.html#1_1' // 일반페이지 등록/수정
        , '_skin.php'=>$ManualBaseLink.'design_2.html#1_2' // 스킨 관리



	// =============================================================== //
	// 프로모션 -> 쿠폰 관리
        , '_coupon_set.list.php'=>$ManualBaseLink.'pro_1.html#1_1' // 쿠폰 목록
        , '_coupon_set.form.php'=>$ManualBaseLink.'pro_1.html#1_1' // 쿠폰 등록/수정
		, '_coupon_config.php'=>$ManualBaseLink.'pro_1.html#1_2' // 쿠폰 주문관련 설정

	// 프로모션 -> 프로모션코드 관리
		, '_promotion.list.php'=>$ManualBaseLink.'pro_2.html#1_1' // 프로모션코드 목록
		, '_promotion.form.php'=>$ManualBaseLink.'pro_2.html#1_1' // 프로모션코드 등록/수정

	// 프로모션 -> 기획전 관리
        , '_promotion_plan.list.php'=>$ManualBaseLink.'pro_3.html#1_1' // 기획전 목록
        , '_promotion_plan.form.php'=>$ManualBaseLink.'pro_3.html#1_1' // 기획전 등록/수정

	// 프로모션 -> 출석체크 관리
		, '_promotion_attend.list.php'=>$ManualBaseLink.'pro_4.html#1_1' // 출석체크 목록
		, '_promotion_attend.form.php'=>$ManualBaseLink.'pro_4.html#1_1' // 출석체크 등록/수정



	// =============================================================== //
	// 로그분석 -> 방문자 분석
		, '_cntlog.php'=>$ManualBaseLink.'log_1.html#1_1' // 시간 및 날짜별
		, '_cntlog_route.php'=>$ManualBaseLink.'log_1.html#1_2' // 접속경로 및 키워드별
		, '_cntlog_env.php'=>$ManualBaseLink.'log_1.html#1_3' // 접속환경 및 회원분석
		, '_cntlog_detail.php'=>$ManualBaseLink.'log_1.html#1_4' // 상세 유입경로

	// 로그분석 -> 회원분석
		, '_static_mem.method.php'=>$ManualBaseLink.'log_2.html#1_1' // 회원 가입 형태
		, '_static_mem.type.php'=>$ManualBaseLink.'log_2.html#1_2' // 회원 상
		, '_static_mem.point.php'=>$ManualBaseLink.'log_2.html#1_3' // 회원 적립금

	// 로그분석 -> 상품분석
        , '_static_product.order.php'=>$ManualBaseLink.'log_3.html#1_1' // 구매통계 및 상품 판매순위
		, '_static_product.category.php'=>$ManualBaseLink.'log_3.html#1_2' // 카테고리 판매 순위
		, '_static_product.cart.php'=>$ManualBaseLink.'log_3.html#1_3' // 장바구니 담긴 상품 순위
		, '_static_product.wish.php'=>$ManualBaseLink.'log_3.html#1_4' // 찜한상품 순위

	// 로그분석 -> 매출분석
		, '_static_sale.all.php'=>$ManualBaseLink.'log_4.html#1_1' // 전체 매출 통계
		, '_static_sale.method.php'=>$ManualBaseLink.'log_4.html#1_2' // 결제수단별 매출
		, '_static_sale.age.php'=>$ManualBaseLink.'log_4.html#1_3' // 연령별 매출
		, '_static_sale.area.php'=>$ManualBaseLink.'log_4.html#1_4' // 배송지별 매출

	// 로그분석 -> 주문분석
		, '_static_order.all.php'=>$ManualBaseLink.'log_5.html#1_1' // 전체 주문 통계
		, '_static_order.age.php'=>$ManualBaseLink.'log_5.html#1_2' // 연령별 주문
		, '_static_order.sex.php'=>$ManualBaseLink.'log_5.html#1_3' // 성별 주문
        , '_static_order.area.php'=>$ManualBaseLink.'log_5.html#1_4' // 지역별 주문
);