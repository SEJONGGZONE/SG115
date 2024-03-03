<?php
// 기획전 상태
//<!-- 이벤트 기간일 경우 if_day 클래스 추가 및 'D-DAY' 문구 변경 / 마감일 경우 if_close 클래스 추가 및 '마감' 문구 변경 -->
//			시작전 -> D-123
//			진행중 -> 진행 (진행시 d_day 클래스에 if_day 클래스 추가)
//			종료후 -> 마감 (종료된 기획전일 경우 d_day 클래스에 if_close 클래스 추가 ,  li에 if_end_promo 클래스 추가)
$app_status = '';
//종료후
if($row['pp_edate']<DATE('Y-m-d')) {
    $app_status = '<span class="d_day if_close">마감</span>';// 종료문구
}
//시작전
else if($row['pp_sdate']>DATE('Y-m-d')) {
    $app_status = '<span class="d_day">D-'. fn_date_diff($row['pp_sdate'],DATE("Y-m-d")) .'</span>';
}
//진행중
else {
    $app_status = '<span class="d_day if_day">진행</span>';
}

// 기획전별 배너 (1,360 x free)
$app_content = ($row['pp_content_m'] ? '<div class="editor">' . stripslashes($row['pp_content_m']) . '</div>' : ($row['pp_content'] ? '<div class="editor">' . stripslashes($row['pp_content']) . '</div>' : ''));

// 상품리스트
$ActiveListColClass = '';
?>

<div class="c_page_tit">
    <div class="tit_box">
        <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
        <div class="tit"><?php echo $siteInfo['s_promotion_plan_title'];?></div>
    </div>
</div><!-- end c_page_tit -->


<div class="c_section">

    <div class="c_promo_view">
        <div class="layout_fix">
            <div class="tit_box">
                <a href="/?pn=product.promotion_list" class="btn_back" title="목록"></a>
                <strong><?php echo stripslashes(strip_tags($row['pp_title'])); ?></strong>
            </div>
            <?php
            echo $app_content;
            ?>
        </div>
    </div><!-- end c_promo_view -->

    <?php

		$list_type_pcclass = $list_type_moclass = $list_type_class='';

		if($list_type == 'list' ||($list_type=='' && $siteInfo['s_promotion_display_type']=='list') ){
			$list_type_class = 'if_list_type';
			$list_type='list';
		}else{
			$list_type='box';
		}


		if($siteInfo['s_promotion_display_type']==$list_type && $list_type!=''){
			if($list_type=='list'){
				$list_type_pcclass = 'pc_type_list'.$siteInfo['s_promotion_display'];
				$list_type_moclass = 'mobile_type_list'.$siteInfo['s_promotion_mobile_display'];
			}else{
			$list_type_pcclass = 'pc_type_box'.$siteInfo['s_promotion_display'];
				$list_type_moclass = 'mobile_type_box'.$siteInfo['s_promotion_mobile_display'];
			}
		}

		if(count($res) > 0 || $search_mode=='Y') {
			include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php'); // 목록컨트롤러
			include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
		}

    ?>



</div><!-- end c_section -->