<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// === 비회원 구매 설정 통합 kms 2019-06-24 ====
//if ( !$none_member_buy ) {
	//$TopNavArr['주문/결제'] = array(	'link'=>'#none', 'hit'=>($pn == 'shop.order.form'|$pn == 'shop.order.result'?true:false) );
	//$TopNavArr['주문완료'] = array( 'link'=>'#none', 'hit'=>($pn == 'shop.order.complete'?true:false) );
//}
// === 비회원 구매 설정 통합 kms 2019-06-24 ====
?>
<div class="c_page_tit js">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"></a>
            <div class="tit"><?php echo $page_title; ?></div>
        </div>
	</div>
</div><!-- end c_page_tit -->

<?php
/*
<!-- ◆ [공통] 네비 (없으면숨김) -->
<div class="c_nav">
    <div class="layout_fix">
        <div class="nav_in">
            <!-- li 3개 채워서 ul반복 -->
            <ul>
                <?php
                $maxCnt = 3; // 몇개씩 반복
                $forCnt = 0;
                $padCnt = (count($TopNavArr)%$maxCnt) != 0 ? $maxCnt - (count($TopNavArr)%$maxCnt) : 0;
                foreach($TopNavArr as $k=>$v) {
                    if($forCnt != 0 && ($forCnt%3) == 0){
                        // echo "</ul><ul>";
                    }
                    $forCnt ++;
                    ?>
                    <li class="<?php echo $v['hit'] === true ? 'hit':null?>"><a href="<?php echo $v['link'] ?>" class="btn"><?php echo $k ?></a></li>
                <?php } echo $padCnt > 0 ? implode(array_fill(0,$padCnt,"<!--<li></li>-->")) : null; // 남은 공간만큼 li로 채우기 ?>
            </ul>
        </div>
    </div>
</div>
<!-- / [공통] 네비 (없으면숨김) -->
*/
?>

<script>
	$(document).on('click','.js.btn_ctrl',function(){
		var chkOpen = $('.js.c_page_tit').hasClass('if_open');
		var chkNomenu = $('.js.c_page_tit').hasClass('if_nomenu');
		if( chkNomenu == true){ return false; }
		if( chkOpen == true){ $('.js.c_page_tit').removeClass('if_open'); }
		else{ $('.js.c_page_tit').addClass('if_open'); }
	});
</script>
