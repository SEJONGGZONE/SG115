<?php
include_once('inc.php');

if($siteInfo['s_ssl_status'] != '진행' || $siteInfo['s_ssl_check'] != 'Y') $s_facebook_login_use = 'N';
// update
$sque = "
	update smart_setup set
		  s_facebook_key = '{$s_facebook_key}'
		, s_facebook_secret = '{$s_facebook_secret}'
		, kakao_api = '{$kakao_api}'
		, kakao_js_api = '{$kakao_js_api}'
		, recaptcha_api = '{$recaptcha_api}'
		, recaptcha_secret = '{$recaptcha_secret}'
		, nv_login_key = '{$nv_login_key}'
		, nv_login_secret = '{$nv_login_secret}'
		, s_facebook_login_use = '".($s_facebook_login_use?$s_facebook_login_use:'N')."'
		, kakao_login_use = '".($kakao_login_use?$kakao_login_use:'N')."'
		, nv_login_use = '".($nv_login_use?$nv_login_use:'N')."'
		, sns_link_instagram = '{$sns_link_instagram}'
		, sns_link_facebook = '{$sns_link_facebook}'
		, sns_link_twitter = '{$sns_link_twitter}'
		, sns_link_blog = '{$sns_link_blog}'
		, sns_link_youtube = '{$sns_link_youtube}'
		, sns_link_kkp = '{$sns_link_kkp}'
		, facebook_share_use = '".($facebook_share_use?$facebook_share_use:'N')."'
		, kakao_share_use = '".($kakao_share_use?$kakao_share_use:'N')."'
		, twitter_share_use = '".($twitter_share_use?$twitter_share_use:'N')."'

		/* {LCY} : 하이앱 */
		, apple_login_use = '".($apple_login_use ? $apple_login_use : 'N')."'
		, apple_clientid = '".$apple_clientid."'

		/* KAY :: 2023-11-06 :: 구글 로그인 추가 */
		, s_google_login_use = '".($s_google_login_use ? $s_google_login_use : 'N')."'
		, s_google_client_id = '".$s_google_client_id."'
		, s_google_client_pw = '".$s_google_client_pw."'

	where
		s_uid = 1
	";
_MQ_noreturn($sque);


// 설정페이지 이동
error_loc('_config.sns.form.php');