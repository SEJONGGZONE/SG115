<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '회원가입'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/member.header.php'); // 모바일 탑 네비
?>
<div class="c_section c_member c_auth">
    <div class="layout_fix">

        <form name="join_auth" class="js_join_auth" action="<?php echo OD_PROGRAM_URL; ?>/member.join.auth.step2.php" method="post" autocomplete="off">
            <input type="hidden" name="in_tp_bit" value="8">
            <div class="c_group_tit">
                <span class="tit">휴대폰 본인인증</span>
                <?php // <span class="sub_txt">체크된 항목은 필수 항목입니다. 꼭 입력해주시기 바랍니다.</span> ?>
            </div>

            <div class="c_form">
                <div class="form_in">
                    <?php // 필수일 경우 dt에 ess 클래스 추가 ?>
                    <dl>
                        <dt class="ess"><span class="tit">휴대폰 통신사</span></dt>
                        <dd>
                            <div class="label_wrap">
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="01" checked /><span class="tx"><span class="icon"></span>SKT</span></label>
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="02"/><span class="tx"><span class="icon"></span>KT</span></label>
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="03"/><span class="tx"><span class="icon"></span>LGU+</span></label>
                                <?php // 알뜰폰 클릭시 각각 tip_txt 가이드 노출 ?>
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="04"/><span class="tx"><span class="icon"></span>알뜰폰(SKT)</span></label>
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="05"/><span class="tx"><span class="icon"></span>알뜰폰(KT)</span></label>
                                <label class="c_label"><input type="radio" name="tel_com_cd" class="js_tip_view" value="06"/><span class="tx"><span class="icon"></span>알뜰폰(LGU+)</span></label>
                            </div>

                            <?php // 알뜰폰(SKT) 클릭시 노출 ?>
                            <div class="tip_txt js_tipbox js_tipbox_04" style="display: none;"><strong>알뜰폰(SKT망) </strong> : KCT(Tplus), KD링크, 이마트, 아이즈비전, 유니컴즈, SK텔링크, 큰사람컴퓨터, 스마텔, 에스원, 씨엔커뮤니케이션</div>
                            <?php // 알뜰폰(KT) 클릭시 노출 ?>
                            <div class="tip_txt js_tipbox js_tipbox_05" style="display: none;"><strong>알뜰폰(KT망) </strong>: CJ헬로비전, KT 파워텔, 홈플러스, 씨엔커뮤니케이션, 에넥스텔레콤, 에스원, 위너스텔, 에이씨앤코리아, 세종텔레콤, KT텔레캅, 프리텔레콤, 에버그린모바일, <br/>착한통신, kt M모바일, 앤텔레콤, 에스원(안심폰), 아이즈비전, 제이씨티, 머천드코리아, 장성모바일, 유니컴즈</div>
                            <?php // 알뜰폰(LGU+) 클릭시 노출 ?>
                            <div class="tip_txt js_tipbox js_tipbox_06" style="display: none;"><strong>알뜰폰(LGU+망) </strong>: (주)미디어로그, (주)스페이스네트, 머천드코리아, (주)엠티티텔레콤, 홈플러스㈜, (주)알뜰폰, 이마트, 서경방송, 울산방송, 푸른방송, 남인천방송, 금강방송, <br/>제주방송</div>
                        </dd>
                    </dl>
                    <dl>
                        <dt class="ess"><span class="tit">휴대폰 번호</span></dt>
                        <dd>
                            <div class="input_box if_btn">
                                <input type="tel" name="tel_no" class="input_design" placeholder="휴대폰 번호"/>
                                <a href="#none" onclick="$(this).closest('form').submit(); return false;" class="c_btn h50 light ">본인인증</a>
                            </div>
                            <div class="tip_txt ">본인 명의의 휴대폰 번호를 입력하고 본인인증을 클릭한 후 인증을 진행할 수 있습니다.</div>
                        </dd>
                    </dl>
                </div>
            </div><!-- end c_form -->
        </form><!-- end js_join_auth -->

        <form name="kcbResultForm" class="js_join_auth_next" action="/?pn=member.join.form" method="post" onsubmit="return kcb_submit();" autocomplete="off">
            <input type="hidden" name="idcf_mbr_com_cd" value="">
            <input type="hidden" name="hs_cert_svc_tx_seqno" value="">
            <input type="hidden" name="hs_cert_rqst_caus_cd" value="">
            <input type="hidden" name="result_cd" value="">
            <input type="hidden" name="result_msg" value="">
            <input type="hidden" name="cert_dt_tm" value="">
            <input type="hidden" name="di" value="">
            <input type="hidden" name="ci" value="">
            <input type="hidden" name="name" value="">
            <input type="hidden" name="birthday" value="">
            <input type="hidden" name="gender" value="">
            <input type="hidden" name="nation" value="">
            <input type="hidden" name="tel_com_cd" value="">
            <input type="hidden" name="tel_no" value="">
            <input type="hidden" name="return_msg" value="">
            <div class="c_btnbox ">
                <ul>
                    <li><a href="#none" onclick="history.go(-1); return false;" class="c_btn h60 black line">이전단계</a></li>
                    <li><a href="#none" onclick="$(this).closest('form').submit(); return false;" class="c_btn h60 black ">다음단계</a></li>
                </ul>
            </div>
        </form><!-- end js_join_auth_next -->
    </div><!-- end layout_fix -->
</div><!-- end c_section -->

<script type="text/javascript">
	$(document).ready(function() { $('.js_tipbox').hide(); });
	$(document).on('click', '.js_tip_view', function(e) {
		var tnum = $(this).val();
		$('.js_tipbox').hide();
		if($('.js_tipbox_'+tnum).length > 0) $('.js_tipbox_'+tnum).show();
	});
	$(document).ready(function() {

		// - 휴대폰 검증
		jQuery.validator.addMethod("htel_check", function(value, element) {
			var pattern = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
			return this.optional(element) || pattern.test(value);
		}, "휴대폰번호 형식이 유효하지않습니다.");

		$('.js_join_auth').validate({
			ignore: 'input[type=text]:hidden',
			rules: {
				tel_no: { required: true, htel_check: true }
			},
			messages: {
				tel_no: { required: '휴대폰 번호를 입력해주세요', htel_check: '휴대폰번호 형식이 유효하지않습니다' }
			},
			submitHandler: function(form) {
				window.open('', 'auth_popup', 'width=430,height=590,scrollbar=yes');
				$(form).prop('target', 'auth_popup');
				form.submit();
			}
		});
	});

	function kcb_submit() {
		var frm = document.kcbResultForm;
		if(!frm.result_cd.value) {
			alert('본인 인증후 회원가입이 가능합니다.');
			return false;
		}
		else if(frm.result_cd.value != 'B000') {
			alert('본인 인증에 실패하였습니다.\n사유: '+frm.result_msg.value);
			return false;
		}
		return true;
	}
</script>