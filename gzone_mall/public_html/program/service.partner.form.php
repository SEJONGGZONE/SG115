<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$partnerData = array();
$partnerData['recaptchaUse'] = ($siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != '') ? true : false;
$partnerAgree = arr_policy('Y','partner_agree');
$partnerData['partnerAgree'] = strip_tags(stripslashes($partnerAgree['partner_agree']['po_content']));


$privacy_table = array();
$privacy_table['제휴문의'][0]['required'] = 'Y';
$privacy_table['제휴문의'][0]['name'] = '서비스 이용 및 상담';
$privacy_table['제휴문의'][0]['destruction'] = '※ 회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존';
$privacy_table['제휴문의'][0]['item'] = array('이름/상호명','연락처','이메일 주소');

$privacy_table['제휴문의'][1]['required'] = 'N';
$privacy_table['제휴문의'][1]['name'] = '서비스 이용 및 상담';
$privacy_table['제휴문의'][1]['destruction'] = '※ 회원 탈퇴 이후 부정 이용을 방지하기 위해 1년간 보존';
$privacy_table['제휴문의'][1]['item'] = array('첨부파일');


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행