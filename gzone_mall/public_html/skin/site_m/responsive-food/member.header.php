
<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $page_title; ?></div>
        </div>
    </div>
</div><!-- end c_page_tit -->


<div class="c_process">
	<ul>
		<li class="<?php echo in_array($pn, array('member.join.agree')) == true ? ' hit':'' ?>"><span class="num">01</span><span class="tit">약관동의</span></li>
		<li class="<?php echo in_array($pn, array('member.join.form')) == true ? ' hit':'' ?>"><span class="num">02</span><span class="tit">정보입력</span></li>
		<li class="<?php echo in_array($pn, array('member.join.complete')) == true ? ' hit':'' ?>"><span class="num">03</span><span class="tit">가입완료</span></li>
	</ul>
</div><!-- end c_process -->
