<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit">비밀번호 변경</div>
        </div>
    </div>
</div><!-- end c_page_tit -->


<?php // 비밀번호 변경안내 ?>
<div class="c_section c_login">
    <div class="layout_fix">
        <form name="frm_cpw" class="frm_cpw c_form" action="<?php echo OD_PROGRAM_URL; ?>/member.join.pro.php" method="post" target="common_frame">
            <input type="hidden" name="_mode" value="password_change">
            <input type="hidden" name="_site_access" value="<?php echo sha1($_id); ?>">
            <ul class="form_ul">
                <li class="form_li">
                    <input type="password" name="_pw" class="input_design" placeholder="현재 비밀번호" autocomplete="new-password" />
                    <div class="tip_txt">
                        비밀번호를 변경하신 지 <strong><?php echo number_format( 1 * $siteInfo['member_cpw_period']); ?>개월</strong>이 지난 경우에 변경을 권유하고 있습니다.
                    </div>
                </li>
            </ul>
            <ul class="form_ul">
                <li class="form_li">
                    <input type="password" name="_cpw" class="input_design js_rcpw" placeholder="새 비밀번호" autocomplete="new-password" />
                    <div class="tip_txt">
                        <?php
                        $pw_length_text = '숫자, 영문';
                        if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= '(대문자 '.$siteInfo['join_pw_up_length'].'자 이상 포함)';
                        if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) $pw_length_text .= ', 특수문자(~!@#$%^&*()_+|<>?:{} 중 '.$siteInfo['join_pw_up_length'].'자 이상)';
                        if($siteInfo['join_pw_limit_max'] > 0) $pw_length_text .= '을 포함하여 <em>'.$siteInfo['join_pw_limit_min'].'자~'.$siteInfo['join_pw_limit_max'].'자</em> 이내로 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
                        else $pw_length_text .= '을 포함하여 <em>'.$siteInfo['join_pw_limit_min'].'자</em> 이상 입력해주세요.';// 최대 글자 수에 따른 안내 메시지 변경
                        echo $pw_length_text;
                        ?>
                    </div>
                </li>
                <li class="form_li">
                    <input type="password" name="_rcpw" class="input_design" placeholder="새 비밀번호 확인" autocomplete="new-password" />
                </li>
            </ul>
            <div class="c_btnbox type_full">
                <ul>
                    <li><a href="/" class="c_btn h50 black line">다음에 변경하기</a></li>
                    <li><a href="#none" onclick="$(this).closest('form').submit(); return false;" class="c_btn h50 black">지금 변경하기</a></li>
                </ul>
            </div>
        </form><!-- end c_form -->
	</div>
</div><!-- end c_section -->


<script type="text/javascript">
	$(document).ready(function() {
		// - 대문자 검증
		jQuery.validator.addMethod('upper_alpha', function(value, element, length) {
			var pattern = /[A-Z]/;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 대문자가 {0}개 이상 포함되어야합니다');

		// - 특수문자 검증
		jQuery.validator.addMethod('special_string', function(value, element, length) {
			var pattern = /[~!@#$%^&*()_+|<>?:{}]/;
			var mc = value.match(pattern);
			if(mc == null) return this.optional(element) || false;
			return this.optional(element) || (mc.length < length?false:true);
		}, '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 {0}개 이상 포함되어야합니다');

		$('.frm_cpw').validate({
			ignore: 'input[type=text]:hidden',
			rules: {
				  _pw: { required : true }
				, _cpw: {
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_id_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_id_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
				}
				, _rcpw: {
					  required : true
					, minlength: <?php echo (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>
					<?php if($siteInfo['join_id_limit_max'] > $siteInfo['join_pw_limit_min']) { ?>
						, maxlength: <?php echo ((int)$siteInfo['join_id_limit_max']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, special_string: <?php echo ((int)$siteInfo['join_pw_up_length']); ?>
					<?php } ?>
					, equalTo: '.js_rcpw'
				}
			},
			messages: {
				  _pw: { required : '현재 비밀번호를 입력해주세요' }
				, _cpw: {
					  required : '새 비밀번호를 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_id_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_id_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
				}
				, _rcpw: {
					  required : '새 비밀번호 확인을 입력해주세요'
					, minlength: '비밀번호는 <?php echo (isset($siteInfo['join_pw_limit_min']) >= 4?(int)$siteInfo['join_pw_limit_min']:4); ?>자 이상 입력해주세요'
					<?php if($siteInfo['join_id_limit_max'] > 4) { ?>
						, maxlength: '비밀번호는 최대 <?php echo ((int)$siteInfo['join_id_limit_max']); ?>자 까지만 입력가능합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) { ?>
						, upper_alpha: '비밀번호에는 대문자가 <?php echo ((int)$siteInfo['join_pw_up_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					<?php if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) { ?>
						, special_string: '비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 <?php echo ((int)$siteInfo['join_pw_sp_length']); ?>개 이상 포함되어야합니다'
					<?php } ?>
					, equalTo: '비밀번호가 일치하지않습니다'
				}
			}
		});
	});
</script>