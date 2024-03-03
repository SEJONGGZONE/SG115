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
					echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.view.php 에서 호출 --

					// -- 목록,수정,삭제,글쓰기 사용
					echo '<div class="c_btnbox type_max"> <ul>';
					echo '		<li><a href="/?'.($_PVSC?enc('d',$_PVSC):'pn=board.list&_menu='.$_menu).'" class="c_btn h40 light line">목록</a></li>';
					if( $boardViewData['authModify'] === true || $boardViewData['authDelete'] === true) {
						echo '	';
						if( $boardViewData['authModify'] === true){ echo '<li><a href="'.$boardViewData['modifyLink'].'" data-uid="'.$boardViewData['uid'].'" data-mode="modify" class="c_btn h40 black line  evt-modify'.$boardViewData['authClass'].'">수정</a></li>'; }
						if( $boardViewData['authDelete'] === true){ echo '<li><a href="'.$boardViewData['deleteLink'].'" data-uid="'.$boardViewData['uid'].'" data-mode="delete" class="c_btn h40 black line evt-delete'.$boardViewData['authClass'].'">삭제</a></li>';}
						echo '	';
					}
					echo '	';

					if( $boardViewData['authWrite'] === true) { echo '<li style="display:none"><a href="'.$boardViewData['writeLink'].'" class="c_btn h40 black">글쓰기</a></li>'; }
					echo '	';

					// -- 답글쓰기
					if($boardViewData['authReply'] === true ) {
						echo '	';
						echo '		<li><a href="'.$boardViewData['replyLink'].'" class="c_btn h40 black evt-reply'.$boardViewData['replyClass'].'">'.$boardViewData['replyType'].'</a></li>';
						echo '	';
					}

					echo '</ul></div>';
				?>

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->


   </div><!-- end layout_grid -->
</div><!-- end c_section -->



<?php include_once(OD_PROGRAM_ROOT.'/board.auth_pop.php'); // -- 비밀번호 팝업 --    ?>
