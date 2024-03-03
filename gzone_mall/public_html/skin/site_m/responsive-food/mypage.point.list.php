<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "적립금";
include_once($SkinData['skin_root'].'/mypage.header.php'); // 상단 헤더 출력

$all_com_point = $all_expect_point = 0;
foreach($row as $k=>$v){
	if($v['pl_point'] > 0 ){
		if(!in_array($v['pl_status'],array('Y','C'))){
			// 적립예정 포인트
			$all_expect_point += $v['pl_point'];
		}
	}else{
		// 사용 완료 포인트
		$all_com_point += $v['pl_point'];
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
								<span class="tit">사용가능</span>
								<span class="total"><strong><?php echo number_format( 1 * $mem_info['in_point']); ?></strong></span>
							</div>
						</li>
                        <li class="li">
							<div class="state_box">
								<span class="tit">사용완료</span>
								<span class="total"><strong><?php echo number_format((-1)*$all_com_point);?></strong></span>
							</div>
						</li>
                        <li class="li">
							<div class="state_box">
								<span class="tit">적립예정</span>
								<span class="total"><strong><?php echo number_format( 1 * $all_expect_point); ?></strong></span>
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
					<div class="c_none"><span class="gtxt">적립된 내역이 없습니다.</span></div>
				<?php } else { ?>
					<div class="c_my_list">
						<ul>
							<?php
								foreach($row as $k=>$v){
							?>
								<li>
									<div class="area_conts">
										<div class="conts_wrap">
											<div class="tit"><?php echo htmlspecialchars($v['pl_title']); ?></div>

											<div class="date">
												<?php if($v['pl_point'] > 0 ) { ?>
													<?php if(!in_array($v['pl_status'],array('Y','C'))){ ?>
														<?php echo '지급 예정일 : '.date('Y-m-d', strtotime($v['pl_appdate'])); ?>
													<?php }  ?>
												<?php } else { ?>
													<?php echo '사용 완료일 : '.date('Y-m-d', strtotime($v['pl_appdate'])); ?>
												<?php } ?>
											</div>

										</div>
									</div>
									<div class="area_state">
										<?php if($v['pl_point'] > 0 ) { ?>											
											<?php if($v['pl_status'] == 'Y') { ?>
												<span class="c_tag h30 light line">지급완료</span>
											<?php } else if($v['pl_status']== 'C') { ?>
												<span class="c_tag h30 light">적립취소</span>
											<?php }else{?>
												<span class="c_tag h30 blue">적립예정</span>
											<?php } ?>
										<?php } else { ?>
											<span class="c_tag h30 red line">사용완료</span>
										<?php } ?>
										<div class="date"><?php echo date('Y-m-d', strtotime($v['pl_rdate'])); ?></div>
										<div class="price<?php echo ($v['pl_point'] <= 0?' if_minus':null); ?> <?php echo ($v['pl_point'] > 0 && !in_array($v['pl_status'],array('Y','C'))?' if_wait':null); ?>"><?php echo number_format( 1 * $v['pl_point']); ?>원</div>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div><!-- end c_my_list -->

					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php } ?>

				<div class="c_user_guide">
					<div class="guide_box">
						<dl>
							<dt>적립금 지급 및 사용 안내사항</dt>
							<dd>적립금은 쇼핑몰 이용 시 상품 구매, 다양한 이벤트 참여를 통해 지급받을 수 있습니다.</dd>
							<dd>적립금은 <em><?php echo number_format( 1 * $siteInfo['s_pointusevalue']); ?>원</em>이상 누적 시 온라인에서 현금처럼 사용가능합니다.</dd>
							<dd>한 주문당 <em>최대 <?php echo number_format( 1 * $siteInfo['s_pointuselimit']); ?>원</em>까지 사용할 수 있습니다.</dd>
							<dd>주문 시 사용한 적립금은 주문취소를 할 경우 적립금으로 다시 반환됩니다.</dd>
						</dl>
					</div>
				</div><!-- end c_user_guide -->

			</div>
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->