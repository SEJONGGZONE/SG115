
<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit">휴면 회원 인증</div>
        </div>
    </div>
</div><!-- end c_page_tit -->

<?php // 휴면회원인증 ?>
<div class="c_section c_complete">
    <div class="layout_fix">

        <form action="<?php echo OD_PROGRAM_URL; ?>/member.slp_pro.php" class="greeting_box" method="post" target="common_frame">
            <input type="hidden" name="_mode" value="<?php echo $sleepData['mode'];?>">
            <input type="hidden" name="_id" value="<?php echo $_id; ?>">
            <dl>
                <dt>회원님은 휴면전환된 상태입니다.</dt>
                <dd>

                    <?php if( $sleepData['type'] == 'auth'){ ?>
					<?php // 이메일 인증방법 ?>
                    이메일로 인증을 받아 로그인을 해주세요.
                    <?php }else{ ?>
					<?php // 바로 인증방법 ?>
					인증하기 버튼 클릭 후 로그인을 해주세요.
                    <?php } ?>
                </dd>
            </dl>

            <div class="c_btnbox type_full">
                <ul>
                    <li><a href="/" class="c_btn h50 black line">다음에 인증하기</a></li>

                    <?php if( $sleepData['type'] == 'auth'){ ?>
					<?php // 이메일 인증방법 ?>
                    <li><a href="#none" onclick="$(this).closest('form').submit();" class="c_btn h50 black">인증 이메일 받기</a></li>
                    <?php }else{ ?>
					<?php // 바로 인증방법 ?>
					<li><a href="#none" onclick="$(this).closest('form').submit();" class="c_btn h50 black">바로 인증하기</a></li>
                    <?php } ?>
                </ul>
            </div>
        </form>

    </div>
</div><!-- end c_section -->