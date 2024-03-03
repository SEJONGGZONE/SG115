<?php
$page_title = $boardInfo['bi_name']; // 게시판명
include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.header.php'); // 상단 헤더 출력
	
?>


<div class="c_section c_gridpage">
	<div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.nav.php'); // 메뉴출력
			?>
		</div><!-- end grid_aside -->

		<div class="grid_section">
			<div class="layout_fix">
				<?php
					echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.list.php 에서 호출 --
				?>
				
				<?php if($BoardSkinData!=''){?>
					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php }?>

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

    </div><!-- end layout_grid -->
</div><!-- end c_section -->

<?php include_once(OD_PROGRAM_ROOT.'/board.auth_pop.php'); // -- 비밀번호 팝업 --   ?>

