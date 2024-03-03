<?php
# LDD010
include_once("inc.php");

// string 만들기
switch( $pass_mode ){

    // 1차 , 2차 옵션 삭제
    case "2depth_del":
    case "1depth_del":

        // 삭제전 하위정보 확인
        $ique = " select count(*) as cnt from smart_product_addoption where pao_pcode='{$pass_code}' and find_in_set('{$pass_uid}' , pao_parent) > 0 "; // 1차 정보
		$ires = _MQ($ique);
		if($ires['cnt'] > 0) {
            echo "is_subcategory"; // 하위 카테고리가 있음 표시
            exit;
        }

        // 순번재조정
        $r = _MQ(" select * from smart_product_addoption where pao_uid='" . $pass_uid . "' ");
        _MQ_noreturn(" update smart_product_addoption set pao_sort=pao_sort-1  where pao_pcode='" . $r['pao_pcode'] . "' and pao_depth='". $r['pao_depth'] ."' and pao_parent='" . $r['pao_uid'] . "' and pao_sort > '". $r['pao_sort'] ."' ");

        // 삭제
        _MQ_noreturn("delete from smart_product_addoption where pao_uid='{$pass_uid}'");

        break;




    // 2차 옵션 추가
    case "2depth_add":

        $ique = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_uid='{$pass_uid}' "; // 1차 정보
        $ir = _MQ($ique);

        // 순번추출
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $ir['pao_uid'] . "' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$ir['pao_uid']}',
                pao_sort='". $max_sort ."'
        ");

        break;



    // 1차 옵션 추가
    case "1depth_add":

        // 순번추출 - 1차
        $r = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='1' ");
        $max_sort = $r['max_sort'] + 1;

        // 항목추가 - 1차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='1',
                pao_sort='". $max_sort ."'
        ");
        $uid_1depth = _MQ_insert_id();

        // 순번추출 - 2차
        $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $uid_1depth . "' ");
        $max_sort2 = $r2['max_sort'] + 1;

        // 항목추가 - 2차
        _MQ_noreturn("
            insert smart_product_addoption set
                pao_pcode='{$pass_code}',
                pao_poptionname='',
                pao_depth='2',
                pao_parent='{$uid_1depth}',
                pao_sort='". $max_sort2 ."'
        ");

        break;

}


?>



<form action="_product_addoption.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post">
	<input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
	<input type="hidden" name="pass_type" value="">
	<input type="hidden" name="pass_depth" value="">
	<input type="hidden" name="pass_uid" value="">


	<?php
		//1차 추출 , // LCY : 2023-03-10 : 순서조정 보정 -- 데이터를 보정하기 전에 현재 순서 기준으로 정렬
		$que = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_depth='1' order by pao_sort asc , pao_uid asc ";
		$res = _MQ_assoc($que);
		if(sizeof($res) <= 0){ echo '<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 추가옵션이 없습니다.</div></div>'; }
		else{
	?>

	<div class="table_option">
		<?php
			// ------------ 추가 옵션 loop ------------
			foreach($res as $k=>$r) {
		?>
			<div class="first_step_box">
				<dl>
					<dt>
						<div class="first_tit if_have_next">타이틀</div>
					</dt>
					<dd>
						<div class="option_name">
							<div class="name"><input type="text" name="pao_info[<?=$r['pao_uid']?>][pao_poptionname]" class="design" placeholder="옵션 타이틀" value="<?=$r['pao_poptionname']?>" /></div>
						</div>
					</dd>
				</dl>

				<div class="last_step_box">
					<?php
						// ------------ 2차 옵션 loop ------------
						$que2 = " select * from smart_product_addoption where pao_pcode='{$pass_code}' and pao_depth='2' and pao_parent='{$r['pao_uid']}' order by pao_sort asc , pao_uid asc ";
						$res2 = _MQ_assoc($que2);
						if(sizeof($res2) <= 0){
							echo '<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 추가옵션이 없습니다.</div></div>';
						}
						else {
					?>

					   <?php
						foreach($res2 as $k2=>$r2) {
							if($r2['pao_poptionname'] == "" || !$r2['pao_poptionname']) $save_chk ++;
						?>
							<dl>
								<dt>
									<div class="first_tit">옵션명</div>
								</dt>
								<dd>

									<div class="duplicate_box depth_2<?php echo ($r2['pao_view'] == 'Y'?null:' if_option_hide'); ?>">
										<div class="option_name">
											<div class="name"><input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionname]" class="design" placeholder="옵션명" value="<?php echo $r2['pao_poptionname']; ?>"></div>
										</div>
										<div class="option_price_box">
											<ul class="thead">
												<li>공급가(원)</li>
												<li>판매가(원)</li>
												<li class="li_mount">재고량</li>
												<li class="li_mount">판매량</li>
											</ul>
											<ul>
												<li>
													<input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionpurprice]" class="design number_style" placeholder="0" value="<?php echo number_format($r2['pao_poptionpurprice']); ?>">
												</li>
												<li>
													<input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_poptionprice]" class="design number_style" placeholder="0" value="<?php echo number_format($r2['pao_poptionprice']); ?>">
												</li>
												<li class="li_mount">
													<input type="text" name="pao_info[<?php echo $r2['pao_uid']; ?>][pao_cnt]" class="design number_style" placeholder="0" value="<?php echo number_format($r2['pao_cnt']); ?>">
												</li>
												<li class="li_mount">
													<input type="text" name="" class="design number_style" placeholder="0" value="<?php echo number_format($r2['pao_salecnt']); ?>" disabled="">
												</li>
											</ul>
										</div>

										<div class="step_ctrl">
											<?php echo _InputRadio('pao_info['. $r2['pao_uid'] .'][pao_view]', array('Y', 'N'), ($r2['pao_view']?$r2['pao_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
											<div class="lineup-row type_end">
												<div class="lineup-updown">
													<a href="#none" onclick="f_sort('U' , '2', '<?php echo $r2['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
														<a href="#none" onclick="f_sort('D' , '2', '<?php echo $r2['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
												</div>
												<a href="#none" onclick="f_insert('2', '<?php echo $r2['pao_uid']; ?>'); return false;" class="c_btn blue line">추가</a><!-- 끼워넣기 추가버튼 -->
												<a href="#none" onclick="category_apply_save('2depth_del', '<?php echo $r2['pao_uid']; ?>'); return false;" class="c_btn h22 black line">삭제</a>
											</div>
										</div>
									</div><!-- end duplicate_box -->

								</dd>
							</dl>
						<?php } ?>

					<?php } ?>
				</div><!-- end last_step_box -->


				<div class="step_ctrl if_have_next">
					<?php echo _InputRadio('pao_info['. $r['pao_uid'] .'][pao_view]', array('Y', 'N'), ($r['pao_view']?$r['pao_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
					<div class="lineup-row type_end">
						<div class="lineup-updown">
							<a href="#none" onclick="f_sort('U' , '1', '<?php echo $r['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="f_sort('D' , '1', '<?php echo $r['pao_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						</div>
						<a href="#none" onclick="category_apply_save('2depth_add', '<?php echo $r['pao_uid']; ?>'); return false;" class="c_btn blue">추가</a><!-- 끼워넣기 추가버튼 -->
						<a href="#none" onclick="category_apply_save('1depth_del', '<?php echo $r['pao_uid']; ?>'); return false;" class="c_btn h22 black">삭제</a>
					</div>
				</div>
			</div>
		<?php } // ------------ 1차 옵션 loop ------------ ?>

	</div>
	<?php } ?>
	<input type="hidden" name="no_save_num" value="<?php echo $save_chk; ?>">

</form>


<script>
// 옵션 숨기기 효과 {
//$(function() {
//
//    $('.btn_hide_input').on('click', function() {
//
//        var checked = $(this).is(':checked');
//        if(checked === true) {
//
//            $(this).closest('li').removeClass('if_option_hide');
//            $(this).closest('label').attr('title', '옵션 숨기기');
//            $('.ui-tooltip-content').html('옵션 숨기기');
//        }
//        else {
//
//            $(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
//            $(this).closest('label').attr('title', '옵션 보이기');
//            $('.ui-tooltip-content').html('옵션 보이기');
//        }
//
//        category_apply();
//    });
//});
// } 옵션 숨기기 효과
</script>