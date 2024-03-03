<?php
	include_once('wrap.header.php');

	# 출력 데이터 설정
	$DiskInfo = CheckDirSize('/'); // 디스크 용량과 파일 수를 구함
?>

<div class="p_Main">
	<?php // 인사말 ================================================ ?>
	<div class="sc_top">
		<div class="greeting">
			<dl>
				<dt>Dashboard</dt>
				<dd>Hello, <?php echo $siteInfo['s_adid']; ?></dd>
			</dl>
		</div>
	</div><!-- end sc_top -->

	<div class="all_wrap">
		<div class="all_section">
			<?php
				// ----- 주문/배송 현황 -----
				include_once('_main.static_order.php');
				// ----- 주문/배송 현황 -----
			?>

			<?php // 주간현황 ================================================ ?>
			<div class="sc_weekly">
				<?php
					// ----- 쇼핑몰 주요 현황 -----
					include_once('_main.static_log.php');
					// ----- 쇼핑몰 주요 현황 -----
				?>
			</div>
		</div><!-- end sc_section -->

		<div class="all_side">
			<div class="sc_side_box this_info">
				<div class="sc_info">
					<dl>
						<a href="<?php echo OD_ADMIN_URL; ?>/_config.sms.form.php" class="upper_link"></a>
						<dt>SMS/알림톡</dt>
						<dd><div class="sms js_sms_info">조회중입니다</div></dd>
					</dl>
					<dl>
						<a href="<?php echo OD_ADMIN_URL; ?>/_config.ssl.default_form.php" class="upper_link"></a>
						<dt>보안서버</dt>
						<dd>
							<?php
								// 보안서버 남은일 체크
								$arr_ssl_time ='';

								$_now = date('Y-m-d H:i:s');
								$_edate = date('Y-m-d',strtotime($siteInfo['s_ssl_edate']));
								$arr_ssl_time = date_diff_datetime($_now,$_edate);
							?>
							<?php if($siteInfo['s_ssl_check']=='Y' && $arr_ssl_time['day']>0){?>
								<strong><?php echo $arr_ssl_time['day'];?>일 남음</strong>
							<?php }else{?>
								<em>미사용</em>
							<?php }?>
						</dd>
					</dl>
					<dl>
						<a href="<?php echo OD_ADMIN_URL; ?>/_solution.info.php" class="upper_link"></a>
						<dt>디스크 사용량</dt>
						<dd><strong><?php echo SizeText($DiskInfo['DiskSize']); ?></strong></dd>
					</dl>
				</div>
			</div>

			<div class="sc_side_box type_fill this_review">
				<?php
					// 상품후기
					$Review = _MQ_assoc(" select `pt`.*, case pt_depth when 1 then pt_uid else pt_relation end as orderby_uid from `smart_product_talk` as `pt` inner join smart_product as p on (pt.pt_pcode = p.p_code) where (1) and `pt_type` = '상품리뷰' and `pt_depth` = 1 order by orderby_uid desc , `pt_uid` asc limit 0, 7");
					if(count($Review) <= 0) $Review = array();
				?>
				<div class="side_tit">
					<strong>상품 Review</strong>
					<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?pt_type=<?php echo urlencode('상품리뷰'); ?>" class="btn_more">전체보기</a>
				</div>
				<div class="sc_post">
					<?php if(count($Review) > 0) { ?>
						<ul>
							<?php
							foreach($Review as $rk=>$rv) {
								$reply_query = _MQ_assoc(" select * from smart_product_talk where pt_depth = '2' and pt_relation = '{$rv['pt_uid']}' ");
								$is_new = false;
								if(time() - strtotime($rv['pt_rdate']) <= ((60*60*24)*7)) $is_new = true; // 7일 기준
							?>
								<li>
									<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.form.php?_mode=add&pt_type=<?php echo urlencode($rv['pt_type']); ?>&pt_uid=<?php echo $rv['pt_uid']; ?>&main_chk=Y" class="upper_link" title="<?php echo addslashes(htmlspecialchars($rv['pt_content'])); ?>"></a>
									<div class="posting"><span class="title"><?php echo trim(htmlspecialchars($rv['pt_content'])); ?></span>
									<?php echo ($is_new === true?'<span class="new">N</span>':null); ?></div>

									<?php if(count($reply_query) > 0) { ?>
										<span class="state state_ok">답변완료</span>
									<?php } else { ?>
										<span class="state">답변대기</span>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<div class="post_none">등록된 내용이 없습니다.</div>
					<?php } ?>
				</div>
			</div>

			<div class="sc_side_box type_fill this_qna">
				<?php
				// 상품문의
				$Question = _MQ_assoc(" select `pt`.*, case pt_depth when 1 then pt_uid else pt_relation end as orderby_uid from `smart_product_talk` as `pt` inner join smart_product as p on (pt.pt_pcode = p.p_code) where (1) and `pt_type` = '상품문의' and `pt_depth` = 1 order by orderby_uid desc , `pt_uid` asc limit 0, 7");
				if(count($Question) <= 0) $Question = array();
				?>
				<div class="side_tit">
					<strong>상품 Q&A</strong>
					<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.list.php?pt_type=<?php echo urlencode('상품문의'); ?>" class="btn_more">전체보기</a>
				</div>
				<div class="sc_post">
					<?php if(count($Question) > 0) { ?>
						<ul>
							<?php
							foreach($Question as $qk=>$qv) {
								$reply_query = _MQ_assoc(" select * from smart_product_talk where pt_depth = '2' and pt_relation = '{$qv['pt_uid']}' ");
								$is_new = false;
								if(time() - strtotime($qv['pt_rdate']) <= ((60*60*24)*7)) $is_new = true; // 7일 기준
							?>
								<li>
									<a href="<?php echo OD_ADMIN_URL; ?>/_product_talk.form.php?_mode=add&pt_type=<?php echo urlencode($qv['pt_type']); ?>&pt_uid=<?php echo $qv['pt_uid']; ?>&main_chk=Y" class="upper_link" title="<?php echo addslashes(htmlspecialchars($qv['pt_title'])); ?>"></a>
									<div class="posting"><span class="title"><?php echo trim(htmlspecialchars($qv['pt_title'])); ?></span>
									<?php echo ($is_new === true?'<span class="new">N</span>':null); ?></div>

									<?php if(count($reply_query) > 0) { ?>
										<span class="state state_ok">답변완료</span>
									<?php } else { ?>
										<span class="state">답변대기</span>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					<?php } else { ?>
						<!-- 내용 없을 경우 -->
						<div class="post_none">등록된 내용이 없습니다.</div>
					<?php } ?>
				</div>
			</div>

			<div class="sc_side_box this_service">
				<div class="sc_service">
					<dl>
						<a href="https://www.onedaynet.co.kr/p/service.patchup_list.html" class="upper_link" target="_blank"></a>
						<dt>패치사항을 확인해주세요</dt>
						<dd>바로가기</dd>
					</dl>
					<dl>
						<a href="https://www.onedaynet.co.kr/p/service.inqury_list.html" class="upper_link" target="_blank"></a>
						<dt>궁금한 점이 있으신가요?</dt>
						<dd>상담하기</dd>
					</dl>
					<dl>
						<a href="https://www.onedaynet.co.kr/p/customize.free_list.html" class="upper_link" target="_blank"></a>
						<dt>기능추가가 필요하신가요?</dt>
						<dd>견적문의</dd>
					</dl>
				</div>
			</div>
		</div><!-- end sc_side -->

	</div><!-- end all_wrap -->
</div><!-- end p_Main  -->


<div class="footer">
	<div class="layout_fix">
		© ONEDAYNET.CO.KR ALL RIGHTS RESERVED.
	</div>
    <a href="#none" onclick="return false;" class="fly_gotop js_scroll_stage" title="맨위로"></a>
</div>


<script>
	(function(){
		// 문자서비스 현황 조회
		$.get('/totalAdmin/ajax.simple.php?_mode=onedaynet_sms_user', function( data ) {
			if(data.code === 'U00') {
				$('.js_sms_info').html('<strong>' + data.data.comma() + '건 남음</strong>');
			}
			else {
				if(data.data === undefined) {
					$('.js_sms_info').html('<span class="error">미등록 계정</span>');
					$('.js_charge').prop('href', '#none');
					$('.js_charge').attr('onclick', "if(confirm('계정을 설정 후 이용바랍니다.\\n\\n설정 페이지로 이동하시겠습니까?')) location.href = '<?php echo OD_ADMIN_URL; ?>/_config.sms.form.php'; return false;");
				}
				else {
					$('.js_sms_info').html('<span class="error">' + data.data + '</span>');
					$('.js_charge').prop('href', '<?php echo OD_ADMIN_URL; ?>/_config.sms.out_list.php?type=charge');
					$('.js_charge').removeAttr('onclick');
				}
			}
		}, 'json');
	})();
</script>

<?php include_once('wrap.footer.php'); ?>