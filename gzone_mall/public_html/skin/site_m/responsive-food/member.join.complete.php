<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
if(!is_login()) error_loc("/?pn=member.login.form");
$page_title = '회원가입 완료'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>

<div class="c_section c_complete">
    <div class="layout_fix">

		<div class="greeting_box">
			<dl>
				<dt><!-- <?php echo $page_title; ?> --><?php echo $mem_info['in_name']; ?>님 회원가입을 환영합니다!</dt>
				<dd><?php echo $siteInfo['s_adshop']; ?> 가입이 완료되었습니다.<br/>보다 나은 서비스 제공을 위해 노력하겠습니다.<br/>감사합니다.</dd>
			</dl>

			<div class="c_btnbox type_full">
				<ul>
					<li><a href="/" class="c_btn h50 black line">홈으로</a></li>
					<li><a href="/?pn=mypage.main" class="c_btn h50 black ">마이페이지</a></li>
				</ul>
			</div>
		</div><!-- end greeting_box -->

    </div>
</div><!-- end c_section -->


