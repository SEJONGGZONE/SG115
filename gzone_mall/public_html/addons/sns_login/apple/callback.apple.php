<?php
# LCY : 애플로그인
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if(in_array($pn, array('member.login.form','member.join.agree','mypage.modify.form','mypage.leave.form')) < 1){ return; }
include_once(OD_ADDONS_ROOT.'/sns_login/sns_login.hook.php');

// 필요항목 정리
$sns_login_addon_type = str_replace(array(str_replace('//', '/', OD_ADDONS_ROOT), '/sns_login/'), '', dirname(__FILE__));
if($siteInfo[$SNSField[$sns_login_addon_type]['config_use']] == 'N' || !$siteInfo[$SNSField[$sns_login_addon_type]['config_key']]) return;; // 사용여부
$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php';
$key = $siteInfo[$SNSField[$sns_login_addon_type]['config_key']]; // 설정 키

if( $pn == 'mypage.leave.form'){
	$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php?_mode=leave';	
}
else{
	$redirect = $system['url'].OD_ADDONS_DIR.'/sns_login/'.$sns_login_addon_type.'/callback.php';	
}



// 세션 검증 코드 생성
$_SESSION['state'] = bin2hex(RandomToken(16)); // 랜덤토큰생성

// 요청 정보 생성
$parmArr = array(
	'clientId'=>$key
	,'scope'=>'name email'
	,'redirectURI'=>$redirect
	,'response_type'=>'code id_token user'
	,'state'=>$_SESSION['state']
	,'usePopup'=>false);
$parm = json_encode($parmArr);

?>
<script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>
<!-- /공통페이지 섹션 -->
<script type="text/javascript">
	AppleID.auth.init(<?php echo $parm; ?>);
	function apply_apple_login(){
		async_apply_login();
	}

	function async_apply_login(){
		try{ 
			data = AppleID.auth.signIn(); 
		}catch(e){
			alert("로그인에 실패하였습니다.\n"+e.message);
		}
	}
</script>


