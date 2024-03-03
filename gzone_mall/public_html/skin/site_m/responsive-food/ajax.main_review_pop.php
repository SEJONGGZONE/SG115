
<?php 
	$_link = '/?pn=product.view&cuid='.$ReviewInfo['p_cuid'].'&pcode='.$ReviewInfo['p_code'];		// 상품상세 링크
	$_pro_img = get_img_src($ReviewInfo['p_img_list_square'], IMG_DIR_PRODUCT);						// 상품이미지
	$_talk_img = get_img_src($ReviewInfo['pt_img'], IMG_DIR_PRODUCT);									// 상품리뷰 이미지
	$star_persent = get_eval_average($ReviewInfo['p_code']);													// 별점 퍼센트
	$_content = stripslashes(htmlspecialchars($ReviewInfo['pt_content']));									// 내용
?>


	<div class="wrapping">
		<div class="tit_box">
            <div class="tit">리뷰보기</div>
            <a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".c_layer.type_main_review" data-add="if_open_layer" title="닫기"></a>
		</div><!-- end tit_box -->

		<div class="conts_box c_scroll_v">
            <div class="review_conts">
                <?php if($_talk_img!=''){?>
                    <div class="photo"><img src="<?php echo $_talk_img;?>" alt="" /></div>
                <?php }?>
                <div class="rv_cont"><?php echo nl2br($_content); ?></div>
            </div>
		</div><!-- end conts_box -->
	</div><!-- end wrapping -->

	<div onclick="return false;" class="bg_close js_onoff_event" data-target=".c_layer.type_main_review" data-add="if_open_layer"></div>