<?php
# 로그인 & 로그아웃 & 팝업닫기
include_once(dirname(__FILE__).'/inc.php');
if( !$_mode ) error_msg("잘못된 접근입니다.");
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


switch($_mode){

	// - 로그인 ---
	case "login":
		// --사전 체크 ---
		$login_id = trim(nullchk($login_id , "아이디를 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return
        if($_mode2<>"master_login"){
            $login_password = trim(nullchk($login_password , "패스워드를 입력해주세요." , "" , "ALT"));// nullchk - alert 형식으로 return
        }
		// --사전 체크 ---

		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 {
		if($siteInfo['member_login_cnt'] > 0 ){
			$loginChk = get_apply_auth_count('login',$login_id);
			if( count($loginChk) > 0 && $loginChk['aac_count'] >= $siteInfo['member_login_cnt']  ){
				if( $loginChk['aac_udate'] > date('Y-m-d H:i:s',time()-$siteInfo['member_login_time']) ){

					// LCY : {하이앱}
					if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){ error_msg("현재 로그인이 불가능합니다.\\n\\n잠시후에 시도해 주세요."); }
					else{ error_alt("현재 로그인이 불가능합니다.\\n\\n잠시후에 시도해 주세요.");  }
					
				}
				else{ // 이미 설정된 카운트가 되었고 시간초가 지났기 때문에 초기화
					init_apply_auth_count('login',$login_id);
				}
			}
		}
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 }

		// 아이디 , 비밀번호를 통한 회원 확인
		$r = _MQ("SELECT * FROM smart_individual where in_id='{$login_id}'  ");
		if( sizeof($r) == 0 ) {

			// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 | 에러 핸들링
			if(  $siteInfo['member_login_cnt'] > 0){ insert_apply_auth_count('login',$login_id);}
			// error_alt("회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");
			// LCY : {하이앱}
			if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){error_msg("로그인에 실패하였습니다.");}
			else{error_alt("로그인에 실패하였습니다.");}
		}

		if( $r[in_out] == 'Y' ) {

			// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 에러 핸들링
			// error_alt("탈퇴한 회원입니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");
			// LCY : {하이앱}
			if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){error_msg("로그인에 실패하였습니다.");}
			else{error_alt("로그인에 실패하였습니다.");}
		}

		// LCY 휴면계정 체크
		if($r['in_sleep_type'] == 'Y'){
			// LCY 2018-03-07 -- 휴면회원 이메일 인증없이 처리가능한경우
			if( $r['in_sleep_request'] == 'Y'){

				$r = _MQ("SELECT * FROM smart_individual_sleep where in_id='{$login_id}'  ");
				$app_login_password = db_password($login_password);
				if( !($r[in_pw] == $app_login_password && $app_login_password)) {// --- modify source ---

					// LCY - 로그인 틀린 횟수 기록
					$ad_cnt = access_deny_cnt('get');
					if($ad_cnt >= $siteInfo['member_login_cnt'] &&  $siteInfo['member_login_cnt'] > 0 ){
						loginchk_insert($login_id, "deny",true);
					}

					// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 | 에러 핸들링
					if(  $siteInfo['member_login_cnt'] > 0){ insert_apply_auth_count('login',$login_id);}

					// LCY : {하이앱}
					if($AppUseMode === true && function_exists('is_app') && is_app() === true){error_msg("로그인에 실패하였습니다.");}
					else{error_alt("로그인에 실패하였습니다.");}
					// error_alt("비밀번호가 맞지 않습니다.\\n\\n다시 한번 확인해 주세요.");

				}else{
					member_sleep_return( $r['in_id'] );
					_MQ_noreturn("update smart_individual set in_sleep_request = 'N' where in_id = '". $r['in_id']."'  ");
				}

			}else{
				// LCY : {하이앱}
				if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){
					error_loc("/?pn=member.sleep_form&_id=" . $r['in_id']);
				}
				else{ error_frame_loc("/?pn=member.slp_form&_id=" . $r['in_id']);  }

				// KAY :: 2023-01-10 :: 한국진흥원 보안패치 적용 -- 파일명 변경
				
			}
		}


		// --- add source ---
		// 관리자 로그인 처리
		if( $_mode2 == "master_login" && $_COOKIE["AuthAdmin"] == $siteAdmin['a_uid'] ){

			// -- 세션체크
			if( AdminLoginCheck() !== true){ error_msg("잘못된 접근입니다."); }

			// 비밀번호 추출 통한 회원 확인
			$admtmpr = _MQ("SELECT in_pw FROM smart_individual where in_id='{$login_id}'  ");
			$app_login_password = $admtmpr[in_pw];
		}
		else {

			// -- 승인 미승인 추가
			if( $r['in_auth'] != 'Y'){
				// LCY : {하이앱}
				if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){error_msg("가입에 대한 승인처리가 되지 않았습니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");}
				else{error_alt("가입에 대한 승인처리가 되지 않았습니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");}

				
			}
			
			$app_login_password = db_password($login_password);
		}
		// --- add source ---
		if( !($r[in_pw] == $app_login_password && $app_login_password)) {// --- modify source ---
			// LCY - 로그인 틀린 횟수 기록
			$ad_cnt = access_deny_cnt('get');

			if($ad_cnt >= $siteInfo['member_login_cnt'] &&  $siteInfo['member_login_cnt'] > 0 ){
				loginchk_insert($login_id, "deny",true);
			}

			// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -------- 인증 정책 | 에러 핸들링
			if(  $siteInfo['member_login_cnt'] > 0){ insert_apply_auth_count('login',$login_id);}
			// error_alt("비밀번호가 맞지 않습니다.\\n\\n다시 한번 확인해 주세요.");
			// LCY : {하이앱}
			if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){error_msg("로그인에 실패하였습니다.");}
			else{error_alt("로그인에 실패하였습니다.");}				
		}

		// SNS 계정연동 로그인
		if($AuthSNSEncID) {
			$sns_info = unserialize(onedaynet_decode($AuthSNSEncID));
			if($sns_info['type'] == 'facebook') _MQ_noreturn(" update smart_individual set sns_join = 'Y', fb_join = 'Y', fb_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			else if($sns_info['type'] == 'kakao') _MQ_noreturn(" update smart_individual set sns_join = 'Y', ko_join = 'Y', ko_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			else if($sns_info['type'] == 'naver') _MQ_noreturn(" update smart_individual set sns_join = 'Y', nv_join = 'Y', nv_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			samesiteCookie('AuthSNSEncID', '', time()-3600 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host'])))); // SNS 고유정보 제거
		}


		// 회원정보 업데이트
		_MQ_noreturn("update smart_individual set in_ldate=now() where in_id='{$login_id}'");


		// 로그인 쿠키 적용
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 {
		$enc_login_id = site_secure_encode($login_id);
		// samesiteCookie("AuthIndividualMember", $login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		samesiteCookie("AuthIndividualMember", $enc_login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 }

		//LCY 로그인 틀린 횟수 기록 - 로그인성궁 시 초기화
		access_deny_cnt('del');

		// 이메일 저장 체크시 쿠키 적용
		if( $login_id_chk == "Y" ) {
			samesiteCookie("AuthSDIndividualIDChk", $login_id , time()+3600*24*30 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		}
		else {
			samesiteCookie("AuthSDIndividualIDChk", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		}

		// 로그인 체크
		$login_trigger = loginchk_insert($login_id , "individual");

		// LCY 로그인 시 장바구니를 아이디로 변경하기 -- >
		if($_mode2 <> "master_login") _MQ_noreturn("update smart_cart set c_cookie='". $login_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' "); // SSJ : 관리자 자동 로그인 시 장바구니 연동 막기 : 2021-05-27
		// samesiteCookie("AuthShopCOOKIEID", $login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));

		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 {
		samesiteCookie("AuthShopCOOKIEID", $enc_login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 쿠키 보안 }

		// LCY : 2023-11-14 : 장바구니 업데이트 체크 함수 (로그인 후 실행필요)
		update_cart_check($login_id);

		// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음
		if(!$_rurl) $_rurl = "/";
		if($_rurl == 'index.php') $_rurl = "/";

		// LCY 관리자가 설정할 일 수 마다비밀번호 변경 체크
		$cpw_que = _MQ("select in_pw_rdate from smart_individual where in_id = '".$login_id."' and  in_pw_rdate  < '". date('Y-m-d H:i:s',strtotime("- ". $siteInfo['member_cpw_period'] ." month"))."'");
		if(count($cpw_que) > 0){
			$_rurl = "/?pn=member.password_form&_ckval=".sha1(date('H'));
		}
		UserLogin($login_id); // 세션로그인

		// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====
		if ( preg_match ( "/(?=pn)/", enc("d",$_rurl) )) {

			// --- JJC : 2022-07-11 : 웹 접근 제어 ---
			$_rurl = str_replace("?&","?","/?".enc("d",$_rurl)); // 2020-03-17 SSJ :: 로그인 경로설정 수정
			// error_frame_loc(str_replace("?&","?","/?".enc("d",$_rurl))); // 2020-03-17 SSJ :: 로그인 경로설정 수정
		}
		// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====

		// JJC : 2022-07-11 : 웹 접근 제어
		if( 
			(preg_match('/http/i', $_rurl ) && !preg_match('/'. reset(explode(':', $system['host'])) .'/i', $_rurl )) || //  - http 주소가 있고 외부 host일 경우 초기화
			preg_match('/\.php/i', $_rurl ) //  - 실행파일이 php일 경우 초기화
		){$_rurl = '/';}

		// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		// LCY : {하이앱}
		if($AppUseMode === true && function_exists('is_app') === true && is_app() === true){
			error_loc($_rurl);
		}
		else{ error_frame_loc($_rurl);  }

		

		break;

	// - 로그아웃 ---
	case "logout":
		// 쿠키 적용
		samesiteCookie("AuthIndividualMember", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY 장바구니 원복
		samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		UserLogout(); // 세션로그아웃

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc("/");
		break;
	// - 로그아웃 ---
	case "mobile_logout":
		// 쿠키 적용
		samesiteCookie("AuthIndividualMember", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY 장바구니 원복
		samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		UserLogout(); // 세션로그아웃

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_loc("/m");
		break;

	// [LCY] 2020-03-02 -- 추가작업 적용 -- 
	case "site_main_top_banner_close": // 팝업 닫기 적용
		SetCookie("AuthPopupClose_topbanner" , "Y" ,  time() +3600 * 24, "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	break;
}