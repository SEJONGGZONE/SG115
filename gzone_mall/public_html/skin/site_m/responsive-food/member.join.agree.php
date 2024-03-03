<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '회원가입'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비

?>

<div class="c_section c_formpage">
    <div class="layout_fix">
        <?php if($is_sns_login_form === true) { // 소셜로그인 가능 시 작동 ?>
            <div class="c_sns_login">
                <ul>
                    <?php
                    if($SNSField['naver']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['naver']['callback_url'];
                    ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn naver" title="네이버 로그인"><span class="icon"></span></a></li>
                    <?php } ?>
                    <?php
                    if($SNSField['kakao']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['kakao']['callback_url'];
                    ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn kakao" title="카카오톡 로그인"><span class="icon"></span></a></li>
                    <?php } ?>
                    <?php
                    if($SNSField['facebook']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['facebook']['callback_url'];
                    ?>
                        <li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn facebook" title="페이스북 로그인"><span class="icon"></span></a></li>
                    <?php } ?>
                        

                    <?php if($SNSField['apple']['login_use'] == 'Y') { ?>
                    <li><a href="#none" onclick="apply_apple_login(); return false;" class="btn apple" title="애플 로그인"><span class="icon"></span></a></li>
                    <?php } ?>
                    
                    <?php
						// KAY : 2023-11-06 :: 구글 로그인 추가
						if($SNSField['google']['login_use'] == 'Y') {
                        $sns_callback_url = $SNSField['google']['callback_url'];
                        ?>
					<li><a href="#none" onclick="window.open('<?php echo $sns_callback_url; ?>', 'sns_login', 'width=800, height=500'); return false;" class="btn google" title="구글 로그인"><span class="icon"></span></a></li>
					<?php }?>
                    
                </ul>
                <div class="or_box"><span class="tx">또는</span></div>
            </div><!-- end c_sns_login -->
        <?php } ?>

        <form name="frmJoinAgree" class="c_form" action="/" method="get" autocomplete="off">
            <input type="hidden" name="pn" value="<?php echo $next_pn; ?>">
            <ul class="form_ul">


                <li class="form_li js_box_open">
                    <div class="form_tit">
                        <label class="c_label"><input type="checkbox" name="join_agree" value="Y"/><span class="tx"><span class="icon"></span><strong>[필수]</strong>이용약관 동의</span></label>
                        <a href="#none" class="btn_open js_btn_open" onclick="return false;" title="약관 열고닫기"></a>
                    </div>

                    <div class="open_box">
                        <textarea rows="8" class="text_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($agree_arr['agree']['po_content']))); ?></textarea>
                        <div class="tip_txt"><em>14세 미만</em>은 가입을 제한하며, 사이트 이용에 제약이 있을 수 있습니다.</div>
                    </div>
                </li>

                <li class="form_li js_box_open">
                    <div class="form_tit">
                        <label class="c_label"><input type="checkbox" name="join_privacy" value="Y"/><span class="tx"><span class="icon"></span><strong>[필수]</strong>개인정보 수집 및 이용 동의</span></label>
                        <a href="#none" class="btn_open js_btn_open" onclick="return false;" title="약관 열고닫기"></a>
                    </div>

                    <div class="open_box">
                        <textarea rows="8" class="text_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($agree_arr['join_privacy']['po_content']))); ?></textarea>

                        <?php
                        // $privacy_table -> 개인정보처리방침 하단 출력될 내용변수 => (/program/member.join.agree.php에서 지정)
                        if(count($privacy_table) > 0) {
                            ?>
                            <?php // 개인정보수집 항목 ?>
                            <div class="user_info_box">
								<?php foreach($privacy_table as $jtk=>$jtv) { ?>
									<?php foreach($jtv as $jtsk=>$jtsv) { ?>
										<dl>
											<dt><?php echo ($jtsv['required'] == 'Y'?'필수':'선택'); // 필수여부 ?></dt>
											<dd>
												이용 목적 : <?php echo $jtsv['name']; // 이용목적 ?><br/>
												수집 항목 : <?php echo implode(', ', $jtsv['item']); // 수집항목 ?>
											</dd>
										</dl>
									<?php } ?>
								<?php } ?>
								
								<?php foreach($privacy_table as $jtk=>$jtv) { ?>
									<dl>
										<dt></dt>
										<dd>
											<?php echo $jtv[0]['destruction']; // 보존 및 파기 ?>
										</dd>
									</dl>
								<?php }?>
                            </div><!-- end user_info_box -->
                        <?php } ?>
                    </div>
                </li>
            </ul>

            <?php
            // $agree_other -> 선택약관 변수 => (/program/member.join.agree.php에서 지정)
            if(count($agree_other) > 0) {
                ?>
				<ul class="form_ul">
				<?php foreach($agree_other as $sak=>$sav) { ?>

					<?php
					if(count($sav['agree']) > 0) {
						foreach($sav['agree'] as $ssak=>$ssav) {
							?>
							<li class="form_li js_box_open">
								<div class="form_tit">
									<label class="c_label"><input type="checkbox" name="agree_other[]" value="<?php echo $ssav['uid']; ?>"/><span class="tx"><span class="icon"></span><em>[선택]</em><?php echo $ssav['title']; ?></span></label>
									<a href="#none" class="btn_open js_btn_open" onclick="return false;" title="약관 열고닫기"></a>
								</div>

								<div class="open_box">
									<textarea rows="8" class="text_design" readonly="readonly"><?php echo stripslashes(htmlspecialchars(ConfigReplace($ssav['content']))); ?></textarea>
								</div>
							</li>
					<?php }} ?>

				<?php } ?>
				</ul>
            <?php } ?>

            <ul class="form_ul">
                <li class="form_li js_box_terms">
                    <div class="form_tit">
                        <label class="c_label"><input type="checkbox" name="all_check" value="Y" class="js_all_check"/><span class="tx"><span class="icon"></span>모든 약관을 읽고, 전체 동의합니다.</span></label>
                    </div>
                </li>
            </ul>

            <div class="c_btnbox type_full">
                <ul>
                    <li><a href="/" class="c_btn h50 black line">가입취소</a></li>
                    <li><a href="#none" onclick="$('form[name=frmJoinAgree]').submit(); return false;" class="c_btn h50 black">다음단계</a></li>
                </ul>
            </div>
        </form><!-- end c_form -->
    </div>
</div><!-- end c_section -->


<script type="text/javascript">
	$(document).ready(function() {
		$('form[name=frmJoinAgree]').validate({
			ignore: '.ignore',
			rules: {
				  join_agree: { required: true }
				, join_privacy: { required: true }
			},
			messages: {
				  join_agree: { required: '이용약관에 동의해주시기 바랍니다' }
				, join_privacy: { required: '개인정보수집 및 이용에 동의해주시기 바랍니다' }
			}
		});
	});

	$(document).on('click', '.js_all_check', AgreeAllCheck);
	function AgreeAllCheck(trigger) {
		var ck = $('.js_all_check').is(':checked');
		if(typeof trigger != 'object') ck = trigger;
		if(ck === true) $('form[name=frmJoinAgree]').find('input:checkbox').not('.js_all_check').prop('checked', true);
		else $('form[name=frmJoinAgree]').find('input:checkbox').not('.js_all_check').prop('checked', false);
	}
</script>