<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$_rurl -> 로그인 후 이동 할 주소 => (/program/member.login.form.php에서 지정)
	$sns_login_count -> 사용중인 SNS로그인 개수 => (/program/member.login.form.php에서 지정)
*/
$page_title = '비회원 주문조회'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.login.header.php'); // 모바일 탑 네비
?>


<div class="c_section c_login">
    <div class="layout_fix ">

		<form class="js_guest_order_form c_form" action="/?pn=<?php echo $pn; ?>" method="post">
			<input type="hidden" name="_rurl" value="<?php echo $_rurl; ?>">
			<ul class="form_ul">
				<li class="form_li"><input type="text" name="_oname" class="input_design" autocomplete="off" value="<?php echo $_oname; ?>" placeholder="주문자명"/></li>
				<li class="form_li"><input type="text" name="_onum" class="input_design js_onum" autocomplete="off" value="<?php echo $_onum; ?>" placeholder="주문번호"/></li>
			</ul>
			<div class="c_btnbox type_full">
				<a href="#none" onclick="return false;" class="c_btn black h50 js_order_btn" title="">주문 조회하기</a>
			</div>
			<dl class="other_link">
				<dt>
					<a href="/?pn=member.login.form&_rurl=<?php echo urlencode($_rurl); ?>" class="btn"><em>로그인하기</em></a>
				</dt>
			</dl><!-- end other_link -->
		</form>

    </div>
</div><!-- end c_section -->

<?php
	if($_oname && $_onum) {
		$is_order = true;
		if($is_order) include_once(OD_PROGRAM_ROOT.'/service.guest.order.view.php');
	}
?>

<script type="text/javascript">

	// 비회원 주문번호 유효성체크
	$(document).on('click','.js_order_btn',function(){
		var onum = $('.js_onum').val();
		var pattern = /[0-9]{5}-[0-9]{5}-[0-9]{5}/;
		var onum_chk = pattern.test(onum);

		if(onum && onum_chk!=true){
			alert('올바른 주문번호를 입력해주세요.');
			return false;
		}else{
			$(this).closest('form').submit();
		}
	});

	$(document).ready(function() {
		// 비회원 주문조회
		$('.js_guest_order_form').validate({
			rules: {
				_oname: { required: true },
				_onum: { required: true }
			},
			messages: {
				_oname: { required: '주문자의 이름을 입력하세요' },
				_onum: { required: '주문번호를 입력하세요' }
			}
		});
	});
</script>