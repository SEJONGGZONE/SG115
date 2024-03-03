<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>

<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1);return false;" class="btn_back" title="뒤로"></a>
            <div class="tit">출석체크</div>
        </div>
    </div>
</div><!-- end c_page_tit -->


<div class="c_section c_attend">
    <div class="layout_fix">

		<?php if(!$event_trigger) { // 진행중인 출석체크 없을때 ?>
			<div class="c_none">
				<div class="gtxt">출석체크 이벤트를 준비중입니다.</div>
			</div>

		<?php }else{ // 진행중인 출석체크 있을때 ?>
			<?php if($title_img <> ''){ ?>
				<div class="banner">
					<img src="<?php echo $title_img; ?>" alt="<?php echo $event_title;?>">
				</div>
			<?php }else{ ?>
			<?php } ?>

			<div class="attend_top">
				<div class="year_box">
					<?php if($prev_month){ ?><?php } ?>
					<a class="prevnext prev" href="<?php if($prev_month){ echo '/?pn=promotion.attend&selected=' . $prev_month_date; }else{ echo '#none'; }?>" title="이전달"></a>
					<div class="year"><a href="/?pn=promotion.attend&selected=<?php echo $today_date; ?>" title="이번달 보기"><?php echo $selected_year; ?>년 <?php echo $selected_month; ?>월</a></div>
					<?php if($next_month){ ?><?php } ?>
					<a class="prevnext next" href="<?php if($next_month){ echo '/?pn=promotion.attend&selected=' . $next_month_date; }else{ echo '#none'; }?>" title="다음달"></a>
				</div><!-- end year_box -->

				<?php if(is_login()) { ?>
					<div class="ctrl_box">
						<div class="total">나의 총 출석 : <strong><?php echo number_format( 1 * $chk_total_cnt); ?></strong>일</div>
						<?php if($today_status === true) { ?>
							<a href="#none" class="btn_attend if_ok">출석체크 완료</a>
						<?php } else { ?>
							<a href="#none" class="btn_attend" onclick="checkin(); return false;">오늘 출석체크</a>
						<?php } ?>
					</div><!-- end ctrl_box -->
				<?php } ?>
			</div><!-- end attend_top -->

			<div class="calendar_box">
				<ul class="week">
					<li><strong>SUN</strong></li>
					<li><strong>MON</strong></li>
					<li><strong>TUE</strong></li>
					<li><strong>WED</strong></li>
					<li><strong>THU</strong></li>
					<li><strong>FRI</strong></li>
					<li><strong>SAT</strong></li>
				</ul>
				<ul class="days">
					<?php
						// 이번달의 첫일, 말일
						$this_first_day = $selected_year . "-" . $selected_month . "-01";
						$this_end_day = $selected_year . "-" . $selected_month . "-" . date("t" , strtotime($selected_date));
						// 첫일의 요일
						$this_first_week = date('w', strtotime($this_first_day));

						// 이벤트의 첫일, 말일
						$event_first_day = $event_start;
						$event_end_day = $event_end;
						// 이벤트첫일의 요일
						$event_first_week = date('w', strtotime($event_first_day));

						// 첫일까지 공백 추가 --  한달씩 출력 ****************************************************************
						for($i=0; $i<$this_first_week; $i++){ echo "<li class='no'></li>"; }

						// 날짜 출력
						for($i=1; $i<=date("t" , strtotime($selected_date)); $i++){
							// 현재날짜
							$td_cell_date = date("Y-m-d", strtotime($selected_year . "-" . $selected_month . "-" . $i));
							// 요일추출
							$_week = date('w', strtotime($td_cell_date));
							if($i > 1 && $_week ==0){ echo "</ul><ul class='days'>"; }

							// 날짜
							$date_print = date('d',strtotime($td_cell_date));

							// 클래스 설정
							$app_class = "";
							if($event_start <= $td_cell_date && $event_end >= $td_cell_date){
								// 오늘 날짜
								if($td_cell_date == $today_date) $app_class .= " today";

								// 출석체크시
								$chk = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_date = '".$td_cell_date."' and atl_event = '".$event_uid."' ");
								if($chk > 0) $app_class .= " hit";
							}

							// 출력
							echo "<li class='". $app_class ."'><strong>" . $date_print . "</strong></li>";
						}
						// 첫일까지 공백 추가 --  한달씩 출력 ****************************************************************

						// 나머지 공백추가
						for($i=$_week; $i < 6; $i++){ echo "<li class='no'></li>"; }
					?>
				</ul>
			</div><!-- end calendar_box -->

			<div class="c_attend_guide">
				<div class="box">
					<div class="tit">출석체크 참여안내</div>
					<div class="info">
						<dl>
							<dt>출석 기간</dt>
							<dd>
                                <?php // 관리자 설정에 따라 둘중 한가지 노출 ?>
								<?php if($event_info['atc_limit'] == 'Y'){ ?>
									<div class="tx"><strong><?php echo date('Y년 m월 d일',strtotime($event_start)); ?> ~ <?php echo date('Y년 m월 d일',strtotime($event_end)); ?>
											(<?php echo (strtotime($event_end) - strtotime($event_start)) / (60 * 60 *24) +1;?>일)</strong></div>
								<?php }else{ ?>
									<div class="tx">기간제한이 없는 이벤트</div>
								<?php } ?>
							</dd>
						</dl>
						<dl>
							<dt>참여 방식</dt>
							<dd>
								<?php if($event_info['atc_type'] == 'T'){ ?>
									<div class="tx">누적 참여형</div>
								<?php }else{ ?>
									<div class="tx">연속 참여형</div>
								<?php } ?>
							</dd>
						</dl>
						<dl>
							<dt>혜택 지급</dt>
							<dd>
                                <?php // 관리자 설정에 따라 둘중 한가지 노출 ?>
								<?php if($event_info['atc_duplicate'] == 'Y'){ ?>
									<div class="tx">이벤트 기간 중 조건 만족 시마다 계속 지급</div>
								<?php }else{ ?>
									<div class="tx">이벤트 기간 중 조건 만족 시 한번만 지급</div>
								<?php } ?>
							</dd>
						</dl>
					</div>
				</div><!-- end box -->

				<div class="box">
					<div class="tit">출석체크 혜택</div>
						<?php
						foreach($event_info['info'] as $k=>$v){
							// 지급일
							$_apply_str = '';

							// 쿠폰정보
							$_coupon_str = '';
							if($v['ata_coupon']>0){
								$_coupon = _MQ(" select * from smart_individual_coupon_set where ocs_uid = '". $v['ata_coupon'] ."' ");
								// 이벤트 쿠폰
								if($_coupon['ocs_type'] <> 'express'){
									if($_coupon['ocs_dtype'] == 'price'){ // 할인액
										$str_dprice = '<strong>' . number_format( 1 * $_coupon['ocs_price']) . '원 할인</strong>';
									}else{ // 할인율
										$str_dprice = '<strong>' . number_format( 1 * $_coupon['ocs_per']) . '% 할인</strong>';
									}
								}else{
									// 무료배송 쿠폰
									$str_dprice = '';
								}
								//$_coupon_str = '['. $arr_coupon_type[$_coupon['ocs_type']] .'] ' . trim(stripslashes($_coupon['ocs_name'])) . $str_dprice;
								// 2019-09-05 SSJ :: 쿠폰타입 사용안함
								$_coupon_str = trim(stripslashes($_coupon['ocs_name'])) . $str_dprice;
								// 2018-12-12 SSJ :: 지급일 노출 위치 변경
								if($v['ata_coupon_delay'] > 0) $_coupon_str .= '<em class="date">('.$v['ata_coupon_delay'].'일 후 지급)</em>';
								else $_coupon_str .= '<em class="date">(당일 지급)</em>';

								// 쿠폰 지급일
								//if($_apply_str <> '') $_apply_str .= ' / ';
								//if($v['ata_coupon_delay'] > 0) $_apply_str .= $v['ata_coupon_delay'].'일 후';
								//else $_apply_str .= '당일';
							}

							// 포인트
							$_point_str = '';
							if($v['ata_point']>0){
								$_point_str = number_format( 1 * $v['ata_point']) . '원';
								// 2018-12-12 SSJ :: 지급일 노출 위치 변경
								if($v['ata_point_delay'] > 0) $_point_str .= '</strong><em class="date">('.$v['ata_point_delay'].'일 후 지급)</em>';
								else $_point_str .= '</strong><em class="date">(당일 지급)</em>';

								// 포인트 지급일
								//if($_apply_str <> '') $_apply_str .= ' / ';
								//if($v['ata_point_delay'] > 0) $_apply_str .= $v['ata_point_delay'].'일 후';
								//else $_apply_str .= '당일';
							}
							?>
							<div class="benefit">
								<div class="inbox this_condi"><?php echo $v['ata_days']?>일 이상 <br/> 출석 시</div>
								<div class="inbox this_benefit">
									<ul>
									<?php if($v['ata_coupon'] > 0){ ?>
										<li>
											<span class="tt">Coupon</span>
											<span class="exp"><?php echo $_coupon_str; ?></span>
										</li>
									<?php } ?>
									<?php if($v['ata_point'] > 0){ ?>
										<li>
											<span class="tt">Point</span>
											<span class="exp"><strong><?php echo $_point_str; ?></span>
										</li>
									<?php } ?>
									</ul>
								</div>
							</div><!-- end benefit -->
						<?php } ?>
				</div><!-- end box -->
			</div><!-- end c_attend_guide -->

		<?php } ?>

    </div>
</div><!-- end c_section -->




<?php if(!$event_trigger){ ?>
	<script>
	// 출석체크
	function checkin(){
		alert('출석체크 이벤트가 종료되었습니다.');
		return false;
	}
	</script>
<?php }else if(!is_login()){ ?>
	<script>
	// 출석체크
	function checkin(){
		login_alert("<?php echo urlencode('/?pn='.$pn)?>");
		return false;
	}
	</script>
<?php }else if($event_trigger){ ?>
	<script>
	// 출석체크
	function checkin(){
		$.ajax({
			data: {'mode':'checkin','today_date':'<?php echo $today_date; ?>','uid':'<?php echo $event_uid; ?>'},
			type: 'POST',
			cache: false,
			dataType: 'JSON',
			url: '<?php echo OD_PROGRAM_DIR; ?>/promotion.attend.pro.php',
			success: function(data) {
				if( data['code']=='OK' ) {
					alert(data['msg']); location.reload();
				} else {
					alert(data['msg']);
				}
			}
		})
		return false;
	}
	</script>
<?php } ?>