<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "미확인 입금자";
include_once($SkinData['skin_root'].'/service.header.php'); // 상단 헤더 출력
?>


<div class="c_section c_gridpage">
	<div class="layout_fix layout_grid">

		<div class="grid_aside">
			<?php
			include_once($SkinData['skin_root'].'/service.nav.php'); // 메뉴출력
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

                    <form role="search" name="gridsearch" method="get" action="/" class="area_box area_right">
                        <input type="hidden" name="pn" value="service.deposit.list">
                        <input type="text" name="search_date" class="input_design type_date js_pic_day js_type_date" value="<?php echo $search_date; ?>" autocomplete="off" placeholder="입금일 선택" readonly>
                        <div class="board_search">
                            <?php if($search_date || $search_name){ // 검색한 후 노출 / 검색 전 숨김 ?>
                                <a href="/?pn=service.deposit.list" class="btn_reset" title="초기화"></a>
                            <?php } ?>
                            <input type="search" name="search_name" value="<?php echo $search_name; ?>" class="input_design" autocomplete="off" placeholder="입금자명">
                            <input type="submit" name="" value="" class="btn_search" title="검색">
                        </div>
                    </form><!-- end area_right -->
                </div><!-- end c_board_ctrl -->

				<div class="c_board_list type_bank">
					<?php if(count($res)>0){ ?>
                        <?php // ul반복 ?>
                        <?php
                            foreach($res as $k=>$v) {
                                $_num = $TotalCount - $count - $k ;
                                // 입금자명 부분 노출
                                $on_name = $v['on_name'];
                                if($siteInfo['s_online_notice_privacy'] == 'Y'){
                                    $len = 2;
                                    $on_name = iconv_substr($v['on_name'], 0, $len, "utf-8");
                                    for($i=0; $i<(utf8_length($v['on_name'])-$len);$i++) $on_name .= '*';

                                }
                        ?>
                            <ul>
                                <li class="this_tit">
                                    <div class="number"><?php echo $_num; ?></div>
                                    <div class="posting">
                                        <div class="who"><em>입금자명 : </em><strong><?php echo $on_name; ?></strong></div>
                                        <?php if($siteInfo['s_online_notice_bank']=='Y'){ ?>
                                            <div class="who"><em>은행 : </em><strong><?php echo $v['on_bank']; ?></strong></div>
                                        <?php } ?>
                                        <div class="who"><em>금액 : </em><strong><?php echo number_format( 1 * $v['on_price']); ?></strong>원</div>
                                    </div>
                                </li>
                                <li class="this_info">
                                    <div class="date"><?php echo date('Y-m-d', strtotime($v['on_date'])); ?></div>
                                </li>
                            </ul>
                        <?php } ?>
					<?php }else{ // 내용 없을때 ?>
						<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
					<?php } ?>

				</div><!-- end c_board_list -->

				<?php if(count($res)>0){ ?>
					<div class="c_pagi">
						<?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
					</div><!-- end c_pagi -->
				<?php }?>

				<div class="c_user_guide">
					<div class="guide_box">
						<dl>
							<dt>미확인 입금자 목록 안내사항</dt>
							<dd>입금자/입금액이 정확하지 않은 입금자 목록 입니다.</dd>
							<dd>리스트에 성함이 있는 고객님은 1:1 온라인 문의 또는 고객센터로 문의해 주시기 바랍니다.</dd>
						</dl>
					</div>
				</div><!-- end c_user_guide -->

			</div><!-- end layout_fix -->
		</div><!-- end grid_section -->

	</div><!-- end layout_grid -->
</div><!-- end c_section -->


<script>
	// 미입금자 검색
	$(document).on('submit','form[name="gridsearch"]',function(){
		var sw = $(this).find('[name="search_name"]').val();
		if( sw.replace(/\s/gi,'') == ''){ alert("검색어를 입력해 주세요."); $(this).find('[name="search_name"]').focus(); return false; }
		return true;
	});

	$(document).on('click','.js_type_date',function(){
		var before_date = $('.js_type_date').val();
		console.log(before_date);
		$('input[name="search_date"]').val(before_date);

		$('.js_type_date').datepicker({
			onSelect: function(dateString) {

				$('.js_type_date').val(dateString);
				document.gridsearch.submit();
			}
		});
	});
</script>