<?php 
	

	if( $AppUseMode !== true || !function_exists('is_app') || (function_exists('is_app') &&  is_app() !== true) ){ error_msg("앱에서만 이용가능한 서비스 입니다."); }
	

	// 알림함 데이터를 가져온다. 
	$res = _MQ_assoc("select *from smart_push_msg_data_late where pmdl_auuid = '".$appUserInfo['au_uid']."' order by pmdl_reg_dt desc limit 0, 999 ");
	if( count($res) > 0) { 
		// 앱을 전부 읽음으로 표시
		_MQ_noreturn(" update smart_push_msg_data_late set pmdl_status = 'view' where pmdl_auuid = '".$appUserInfo['au_uid']."' and pmdl_status = 'none' ");
	}
?>
<!-- ******************************************
     앱 알림함 
	 header/rm_footer div만 숨김처리
  -- ****************************************** -->
<div class="alarm_app">
	
	<!-- 탑영역 -->
	<div class="am_top">
		<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로">
			<svg id="Layer_2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 2"><path d="m22 11h-17.586l5.293-5.293a1 1 0 1 0 -1.414-1.414l-7 7a1 1 0 0 0 0 1.414l7 7a1 1 0 0 0 1.414-1.414l-5.293-5.293h17.586a1 1 0 0 0 0-2z"/></svg>
		</a>
		<div class="tit">알림함</div>
	</div>
	<!-- / 탑영역 -->
	
	<!-- 알림내역 목록 (페이징 없음) -->
	<div class="am_list">

		
		<?php if( count($res) < 1) { ?>
		<!-- 내역없음 : ul 숨기고 노출 -->
		<div class="no_list">
			<div class="ic">
				<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="Don_t_ring" fill="rgb(0,0,0)" data-name="Don't ring"><path d="m19 28h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0-2z"/><path d="m29.707 3.707a1 1 0 1 0 -1.414-1.414l-4.0486 4.0486a10 10 0 0 0 -18.2444 5.6584v6.3818l-2.0806 1.04a3.4668 3.4668 0 0 0 .7649 6.48l-2.3913 2.3912a1 1 0 0 0 1.414 1.414zm-24.2348 20.293a1.4722 1.4722 0 0 1 -.6582-2.7891l2.6333-1.3164a1 1 0 0 0 .5527-.8945v-7a7.9877 7.9877 0 0 1 14.7882-4.2023l-16.2023 16.2023z"/><path d="m28.0806 19.4219-2.0806-1.0401v-5.3818a1 1 0 0 0 -2 0v6a1 1 0 0 0 .5527.8945l2.6333 1.3164a1.4722 1.4722 0 0 1 -.6582 2.7891h-13.5278a1 1 0 0 0 0 2h13.5278a3.4724 3.4724 0 0 0 1.5528-6.5781z"/></g></svg>
			</div>
			<strong>알림 내역이 없습니다.</strong>
		</div>
		<!-- / 내역없음 -->
		<?php }else{ ?>
		
		<ul>
		<?php 
			foreach($res as $k=>$v) {

				if( $v['pmdl_data'] != ''){
					$pushDataSet = @unserialize(stripslashes($v['pmdl_data']));
				}

				if( $v['pmdl_status'] == 'none'){ 
					$readOk[] = $v['pmdl_uid']; // 읽음 업데이트를 위한 다중 배열 저장
				}
			
				$push_image = '';
				if( $v['pmdl_image'] != ''){
					$push_image = $siteUrl.$v['pmdl_image'];
				}
				
				$push_link = '';
				$checkDefaultUri = $VARAPP['push_uri']['site'];
				if( $pushDataSet['redirectUrl'] == '' || $pushDataSet['redirectUrl'] == '/' || $pushDataSet['redirectUrl'] == $checkDefaultUri ){
					$push_link = '';				
				}else{
					$push_link = $pushDataSet['redirectUrl'].(preg_match("/[\?]/",$pushDataSet['redirectUrl']) ? "&" : "?")."pushUid=".$v['pmdl_uid'];				
				}
		?>

			<!-- 알림 내역 반복구간 -->
			<li>
				<div class="am_box">					
					<!-- 월/일 2자리수 표현, 시간은 오전,오후 표현 (ex. 2022-01-26 오후 02:36) -->
					<div class="date"><?php echo date('Y-m-d',strtotime($v['pmdl_reg_dt'])); ?> <?php date('H',strtotime($v['pmdl_reg_dt'])) >= 12 ? '오후':'오전'  ?> <?php echo  date('H:i',strtotime($v['pmdl_reg_dt'])) ?></div>
					<div class="conts">
						<dl>

							<?php if( $v['pmdl_title'] != '') { ?>
							<!-- 제목 : 없으면 숨김 -->
							<dt><?php echo $v['pmdl_title']; ?></dt>
							<?php } ?>

							<?php if( $v['pmdl_body'] != '') { ?>
							<!-- 내용  -->
							<dd><?php echo $v['pmdl_body']; ?></dd>
							<?php } ?>
						</dl>
	
						<?php if( $push_image != '') {?>
						<!-- 등록이미지 : 없으면 숨김 -->
						<div class="photo" onclick="return false;"><img src="<?php echo $push_image; ?>" alt="<?php echo addslashes($v['pmdl_title']) ?>" /></div>
						<?php } ?>
					</div>

					<?php if( $push_link != '') { ?>
					<!-- 링크 : 없으면 숨김 -->
					<a href="<?php echo $push_link; ?>" class="btn_link">
						<strong>바로가기</strong>
						<span class="ic">
							<svg id="svg10654" viewBox="0 0 6.3499999 6.3500002" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg"><g id="layer1" transform="translate(0 -290.65)"><path id="path9429" d="m2.2580394 291.96502a.26460982.26460982 0 0 0 -.1741496.46871l1.6190225 1.38699-1.6190225 1.38648a.26460982.26460982 0 1 0 .3436483.40049l1.8536335-1.58595a.26460982.26460982 0 0 0 0-.40256l-1.8536335-1.5875a.26460982.26460982 0 0 0 -.1694987-.0667z" font-variant-ligatures="normal" font-variant-position="normal" font-variant-caps="normal" font-variant-numeric="normal" font-variant-alternates="normal" font-feature-settings="normal" text-indent="0" text-align="start" text-decoration-line="none" text-decoration-style="solid" text-decoration-color="rgb(0,0,0)" text-transform="none" text-orientation="mixed" white-space="normal" shape-padding="0" isolation="auto" mix-blend-mode="normal" solid-color="rgb(0,0,0)" solid-opacity="1" vector-effect="none"/></g></svg>
						</span>
					</a>
					<?php } ?>
				</div>
			</li>

		<?php } ?>
		</ul>

		<?php } ?>
	</div>
	<!-- / 알림내역 목록 -->

</div>
<!-- / 앱 알림함 -->