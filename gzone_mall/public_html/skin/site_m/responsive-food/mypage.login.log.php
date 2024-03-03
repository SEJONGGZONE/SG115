<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
$page_title = '로그인 기록'; // 페이지 타이틀
include_once($SkinData['skin_root'].'/mypage.header.php'); // 모바일 탑 네비
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

                <div class="c_board_ctrl">
                    <div class="area_box area_left">
                        <div class="board_tit">
                            <div class="name"><?php echo $page_title; ?></div>
                            <div class="total">Total <?php echo number_format( 1 * $TotalCount); ?></div>
                        </div>
                    </div><!-- end area_left -->
                </div><!-- end c_board_ctrl -->

				<?php if(count($row) <= 0) { ?>
					<div class="c_none"><span class="gtxt">접속 기록이 없습니다.</span></div>
				<?php } else { ?>
					<div class="c_my_list">
						<ul>
							<?php foreach($row as $k=>$v) { ?>
								<li>
									<div class="area_conts">
										<div class="conts_wrap">
											<div class="tit">접속IP : <?php echo $v['lc_ip']; ?></div>
											<div class="date">접속시간 : <?php echo $v['lc_rdate']; ?></div>
										</div>
									</div>

									<div class="area_state">
										<?php if($v['lc_type'] == 'individual') { ?>
											<span class="c_tag h30 light line">성공</span>
										<?php } else if($v['lc_type'] == 'deny') { ?>
											<span class="c_tag h30 light ">실패</span>
										<?php } ?>
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
							<dt>로그인 성공/실패 기록</dt>
							<dd>로그인 성공 여부와 실패 기록에대한 아이피를 확인할 수 있습니다.</dd>
							<dd>실패건이 많을 경우 비밀번호 등을 정기적으로 수정하길 바랍니다.</dd>
						</dl>
					</div>
				</div><!-- end c_user_guide -->


			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->


	</div><!-- end layout_grid -->
</div><!-- end c_section -->
