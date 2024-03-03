<div class="c_page_tit">
	<div class="tit_box">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
		<div class="tit"><?php echo $siteInfo['s_promotion_plan_title'];?></div>
	</div>
</div><!-- end c_page_tit -->

<div class="c_section">
	<div class="layout_fix">

		<div class="c_promo_list">

			<?php if(count($res) < 1){ // 내용 없을때 ?>
			   <div class="c_none">
					<span class="gtxt">등록된 기획전이 없습니다.</span>
			   </div>
			<?php }else{ ?>
				<ul>
					<?php
						foreach($res as $k=>$v){
							// 스킨 이미지 PATH
							$app_path = $SkinData['skin_path'] . '/';

							// 타이틀
							$app_title = htmlentities($v['pp_title']);

							// 목록 이미지 // 기획전 목록 썸네일 (660 * free) / 기획전목록 썸네일 등록 안했을경우 기획전 상세 배너 불러옴
							$app_img = '';
							$_img = IMG_DIR_BANNER.$v['pp_img'];
							if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) {
								$app_img = '<img src="'. $_img.'" alt="'. $app_title .'">';
							}

							// 링크
							$app_link = "/?pn=product.promotion_view&uid=" . $v['pp_uid'];

							// 기획전 상태
							//			시작전 -> D-123
							//			진행중 -> 진행 (진행시 d_day 클래스에 if_day 클래스 추가)
							//			종료후 -> 마감 (종료된 기획전일 경우 d_day 클래스에 if_close 클래스 추가 ,  li에 if_end_promo 클래스 추가)
							$app_status = $app_li_class = $app_dday_class = $app_close_string = '';
							
							//종료후
							if($v['pp_edate']<DATE('Y-m-d')) {
								$app_status = '마감'; // 진행상태
								$app_li_class = 'if_end_promo';
								$app_dday_class = 'if_close';
								$app_close_string = '<span class="end_tx">END</span>';// 종료문구
							}
							//시작전
							else if($v['pp_sdate']>DATE('Y-m-d')) {
								$app_status = 'D-'.fn_date_diff($v['pp_sdate'],DATE("Y-m-d")); // 진행상태
							}
							//진행중
							else {
								$app_status = '진행'; // 진행상태
								$app_dday_class = 'if_day';
							}

							// 시작일
							$app_sdate = $v['pp_sdate'];
						 
							//종료일
							$app_edate = $v['pp_edate'];

							// 상품갯수 추출
							$app_pcnt = _MQ_result(" select 
															count(*) as cnt 
														from smart_promotion_plan_product_setup as ppps 
														left join smart_product as p on (ppps.ppps_pcode = p.p_code) 
														where p_view='Y' and ppps_ppuid = '". $v['pp_uid'] ."'
														");

						?>
						<li class="<?php echo $app_li_class?>">
							<div class="promo_box">
								<div class="thumb">
									<a href="<?php echo $app_link?>" class="upper_link"></a>
									<?php echo $app_close_string?>
									<?php echo $app_img?>
									<span class="total"><?php echo number_format( 1 * $app_pcnt)?> ITEMS</span>
								</div>
								<div class="tit_info">
									<span class="tit"><a href="<?php echo $app_link?>" class="upper_link"></a><?php echo $app_title?></span>
									<span class="date"><?php echo $app_sdate?> ~ <?php echo $app_edate?></span>
								</div>
							</div>
						</li>
					<?php } ?>
				</ul>
			<?php }?>

		</div><!-- end c_promo_list -->

	<?php if(count($res)>0){?>
		<div class="c_pagi">
			<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
		</div>
	<?php }?>


	</div><!-- end layout_fix -->
</div><!-- end c_section -->