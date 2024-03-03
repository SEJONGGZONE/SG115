<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

// 내부패치 68번줄 kms 2019-11-05
$page_title = "쿠폰";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력


$TotalCountWait = $TotalCountReady = $TotalCounPossible = $TotalCountEnd = $TotalCountDateEnd = 0;


foreach($res as $r => $v){
	$couponSetData = $v['coup_ocsinfo'] != '' ? unserialize(stripslashes($v['coup_ocsinfo'])) : array();
	$this_day = date('Y-m-d');

	if($v['coup_use'] == 'Y'){
		$TotalCountEnd++;				// 사용완료
	}else if($v['coup_use'] == 'W'){
		$TotalCountWait ++;				// 적용대기
	}else if($v['coup_use'] == 'E'){
		$TotalCountDateEnd ++;		// 기간만료
	}else{

		if($couponSetData['ocs_use_date_type']=='date' && $this_day < date('Y-m-d', strtotime($couponSetData['ocs_sdate']))){
			$TotalCountReady++;		// 사용대기 쿠폰
		}else{
			if( $couponSetData['ocs_issued_type'] == 'auto' && in_array($couponSetData['ocs_issued_type_auto'], array(1,2)) > 0){
				// 주문조회하여 취소되지 않은 주문건중 배송완료 또는 발급완료 주문건에 한해서 처리
				$check_order = _MQ("select count(*) as cnt from smart_order where o_paystatus = 'Y' and o_canceled = 'N' and o_status in('배송완료','발급완료') and o_ordernum = '".$v['coup_checksum']."' ");
				if( $check_order['cnt'] < 1){
					$TotalCountReady++;		// 사용대기 쿠폰
				}
				else{
					$TotalCounPossible++;		// 사용가능 쿠폰
				}
			}else{
				$TotalCounPossible++;		// 사용가능 쿠폰
			}
		}
	}
}


?>
<div class="c_section c_gridpage">
    <div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
				include_once($SkinData['skin_root'].'/mypage.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->


		<div class="grid_section">
			<div class="layout_fix">

				<div class="c_my_stats">
					<ul class="ul">
						<li class="li">
							<div class="state_box">
								<span class="tit">사용대기</span>
								<span class="total"><strong><?php echo number_format( 1 * $TotalCountReady); ?></strong></span>
							</div>
						</li>
						<li class="li">
							<div class="state_box">
								<span class="tit">사용가능</span>
								<span class="total"><strong><?php echo number_format( 1 * $TotalCounPossible); ?></strong></span>
							</div>
						</li>
						<li class="li">
							<div class="state_box">
								<span class="tit">적용대기</span>
								<span class="total"><strong><?php echo number_format( 1 * $TotalCountWait); ?></strong></span>
							</div>
						</li>
						<li class="li">
							<div class="state_box">
								<span class="tit">사용완료</span>
								<span class="total"><strong><?php echo number_format( 1 * $TotalCountEnd); ?></strong></span>
							</div>
						</li>
						<li class="li">
							<div class="state_box">
								<span class="tit">기간만료</span>
								<span class="total"><strong><?php echo number_format( 1 * $TotalCountDateEnd); ?></strong></span>
							</div>
						</li>
					</ul>
				</div><!-- end c_my_stats -->

                <div class="c_board_ctrl">
                    <div class="area_box area_left">
                        <div class="board_tit">
                            <div class="name"><?php echo $page_title; ?></div>
                            <div class="total">Total <?php echo number_format( 1 * $TotalCount); ?></div>
                        </div>
                    </div><!-- end area_left -->
                </div><!-- end c_board_ctrl -->

				<?php if(count($row) <= 0) { // 내용 없을때 ?>
					<div class="c_none"><span class="gtxt">쿠폰 내역이 없습니다.</span></div>
				<?php } else { ?>
					<div class="c_my_coupon">
						<ul>
							<?php
								foreach($row as $k=>$v) {
									$couponSetData = $v['coup_ocsinfo'] != '' ? unserialize(stripslashes($v['coup_ocsinfo'])) : array();
									$printCouponSetBoon = printCouponSetBoon($couponSetData);
									$printCouponName = $couponSetData['ocs_name'] == '' ? $v['coup_name'] : $couponSetData['ocs_name'];

									if($couponSetData['ocs_use_date_type']=='date'){
										$printCouponDate = date('Y-m-d', strtotime($couponSetData['ocs_sdate'])).' ~ '.date('Y-m-d', strtotime($couponSetData['ocs_edate'])).'까지 사용가능';
									}else{
										$printCouponDate = date('Y-m-d', strtotime($v['coup_expdate'])).'까지 사용가능';
									}


									$this_day = date('Y-m-d');

									$coup_class=$v['coup_use']=='Y'|| $v['coup_use']=='E'?'if_end':'';

									// LCY : 2023-01-18 : 쿠폰추가적용 -- 첫주문/구매 쿠폰은 주문상태가 배송완료여야 한다.
									$use_wait = $couponSetData['ocs_use_date_type']=='date' && $this_day < date('Y-m-d', strtotime($couponSetData['ocs_sdate'])); // 기본 쿠폰의 사용할 수 있는 기간체크
									if($use_wait !== true && $couponSetData['ocs_issued_type'] == 'auto' && in_array($couponSetData['ocs_issued_type_auto'], array(1,2)) > 0){
										// 주문조회하여 취소되지 않은 주문건중 배송완료 또는 발급완료 주문건에 한해서 처리
										$check_order = _MQ("select count(*) as cnt from smart_order where o_paystatus = 'Y' and o_canceled = 'N' and o_status in('배송완료','발급완료') and o_ordernum = '".$v['coup_checksum']."' ");
										if( $check_order['cnt'] < 1){
											$couponSetData['ocs_desc'] .= ' (배송 완료 후 사용 가능)';
											$use_wait = true;
										}
									}


							?>
								<?php // 사용완료, 기간만료 일때 li에 .if_end 클래스 추가 ?>
								<li class="<?php echo $coup_class?>">
									<div class="area area_conts">
										<div class="coupon_num"><?php echo $v['coup_uid']; ?></div>
										<div class="tit"><?php echo $printCouponName; ?></div>
										<?php if($couponSetData['ocs_desc']!=''){?>
											<div class="descript"><?php echo $couponSetData['ocs_desc'];?></div>
										<?php }?>
										<div class="date_info">
											<div class="date">발행일 : <?php echo date('Y-m-d', strtotime($v['coup_rdate'])); ?></div>
											<div class="date"><?php echo $printCouponDate;?></div>
										</div>
									</div>
									<div class="area area_state">
										<?php if($v['coup_use'] == 'Y') { ?>
											<span class="c_tag h30 light line">사용완료</span>
										<?php } else if($v['coup_use'] == 'W') { // 무통장 입금확인전 ?>
											<span class="c_tag h30 black line">적용대기</span>
										<?php } else if($v['coup_use'] == 'E') { ?>
											<span class="c_tag h30 light">기간만료</span>
										<?php } else { ?>
											<?php if( $use_wait === true ){?>
												<span class="c_tag h30 blue line">사용대기</span>
											<?php }else{?>
												<span class="c_tag h30 blue">사용가능</span>
											<?php }?>
										<?php } ?>
										<div class="price"><?php echo $printCouponSetBoon; ?></div>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div><!-- end c_mypage_list -->

					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php } ?>


				<div class="c_user_guide">
					<div class="guide_box">
						<dl>
							<dt>쿠폰 지급 및 사용 안내사항</dt>
							<dd>쿠폰은 쇼핑몰 이용 시 출석체크 등 다양한 이벤트 참여를 통해 지급받을 수 있습니다.</dd>
							<dd>쿠폰은 주문 시 조건에 따라 총 주문금액 또는 배송비에서 할인 받을 수 있습니다.</dd>
							<dd>각 쿠폰별로 사용 조건 및 기간이 상이하니 확인 후 사용해주세요.</dd>
						</dl>
					</div>
				</div><!-- end c_user_guide -->

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->


	</div><!-- end layout_grid -->
</div><!-- end c_section -->