<?php
# LDD010
include_once("inc.php");

// string 만들기
switch( $pass_mode ){
	// 1차옵션 삭제
	case "1depth_del":

		// 순번재조정
		$r = _MQ(" select * from smart_product_option where po_uid='" . $pass_uid . "' ");
		_MQ_noreturn(" update smart_product_option set po_sort=po_sort-1  where po_pcode='" . $r['po_pcode'] . "' and po_depth='". $r['po_depth'] ."' and po_parent='" . $r['po_uid'] . "' and po_sort > '". $r['po_sort'] ."' ");


		//옵션 삭제 전 옵션 이미지 삭제
		option_img_del($pass_uid);


		_MQ_noreturn("delete from smart_product_option where po_uid='{$pass_uid}'"); // 삭제
        _MQ_noreturn("delete from smart_product_option where find_in_set('{$pass_uid}',po_parent)>0 ");// 하위카테고리 삭제

	break;

	// 1차 옵션 추가
	case "1depth_add":

		// 순번추출
		$r = _MQ(" select ifnull(max(po_sort),0) as max_sort from smart_product_option where po_pcode='" . $pass_code . "' and po_depth='1' ");
		$max_sort = $r['max_sort'] + 1;
		_MQ_noreturn("
			insert smart_product_option set
				po_pcode='{$pass_code}',
				po_poptionname='',
				po_depth='1',
				po_sort='". $max_sort ."'
		");// 항목추가 - 1차

	break;
}

	// 상품정보 추출
	$p_info = get_product_info($pass_code);
	// 부모창이 바뀌어 옵션형태를 가져오지 못할 경우 - 기존 상품정보에서 반영함
	if( !in_array( $app_option1_type , array('normal' , 'color', 'size'))) {$app_option1_type = $p_info['p_option1_type'];}
	if( !in_array( $app_option2_type , array('normal' , 'color', 'size'))) {$app_option2_type = $p_info['p_option2_type'];}
	if( !in_array( $app_option3_type , array('normal' , 'color', 'size'))) {$app_option3_type = $p_info['p_option3_type'];}

?>


<form action="_product_option.pro.php" name="frm_option" id="frm_option" target="common_frame"  method="post" ENCTYPE="multipart/form-data">
	<input type="hidden" name="pass_code" value="<?php echo $pass_code; ?>">
	<input type="hidden" name="pass_type" value="">
	<input type="hidden" name="pass_depth" value="">
	<input type="hidden" name="pass_uid" value="">

		<?php
			//1차 추출
			$save_chk = 0;
			$que = " select * from smart_product_option where po_pcode='{$pass_code}' and po_depth='1' order by po_sort asc , po_uid asc ";
			$res = _MQ_assoc($que);

			if(sizeof($res) <= 0){ echo '<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 1차 옵션이 없습니다.</div></div>'; }
			else{
		?>

		<div class="table_option">
			<?php
				foreach($res as $k=>$r) {
					if($r['po_poptionname'] == "" || !$r['po_poptionname'])  $save_chk ++;
			?>
			<div class="first_step_box">
				<dl class="depth_2<?php echo ($r['po_view'] == 'Y'?null:' if_option_hide'); ?>">
					<dt>
						<div class="first_tit">1차 옵션</div>
					</dt>
					<dd>
						<div class="option_name">
							<?php // KAY :: 일괄업로드 :: 2021-07-02 -- 옵션명에서 특수문자 제거 ?>
							<?php $op_str = array(">","|","§"); //변경할 특수문자 종류 배열?>
							<div class="name"><input type="text" name="po_info[<?php echo $r['po_uid']; ?>][po_poptionname]" class="design" placeholder="1차 옵션명" value="<?php echo str_replace($op_str,"",$r['po_poptionname']); ?>" /></div>
							<?php
								// ----- 컬러형일 경우 -----
								if($app_option1_type == 'color') { fn_option_color( $r['po_uid'] , $r['po_color_type'] , $r['po_color_name']); }
							?>
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
									<input type="text" name="po_info[<?php echo $r['po_uid']; ?>][po_poption_supplyprice]" class="design number_style" placeholder="0" value="<?php echo number_format($r['po_poption_supplyprice']); ?>" />
								</li>
								<li>
									<input type="text" name="po_info[<?php echo $r['po_uid']; ?>][po_poptionprice]" class="design number_style" placeholder="" value="<?php echo number_format($r['po_poptionprice']); ?>" />
								</li>
								<li class="li_mount">
									<input type="text" name="po_info[<?php echo $r['po_uid']; ?>][po_cnt]" class="design number_style" placeholder="" value="<?php echo number_format($r['po_cnt']); ?>" />
								</li>
								<li class="li_mount">
									<input type="text" name="" class="design number_style" placeholder="" value="<?php echo number_format($r['po_salecnt']); ?>" style="width:100%" disabled />
								</li>
							</ul>
						</div>
					</dd>
				</dl>
				<div class="step_ctrl">
					<?php echo _InputRadio('po_info['. $r['po_uid'] .'][po_view]', array('Y', 'N'), ($r['po_view']?$r['po_view']:'Y'), ' class="btn_hide_input" ', array('노출', '숨김')); ?>
					<div class="lineup-row type_end">
						<div class="lineup-updown">
							<a href="#none" onclick="f_sort('U' , '1', '<?php echo $r['po_uid']; ?>' ); return false;" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="f_sort('D' , '1', '<?php echo $r['po_uid']; ?>' ); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						</div>
						<a href="#none" onclick="f_insert('1', '<?php echo $r['po_uid']; ?>'); return false;" class="c_btn blue">1차 삽입</a><!-- 끼워넣기 추가버튼 -->
						<a href="#none" onclick="category_apply_save('1depth_del', '<?php echo $r['po_uid']; ?>'); return false;" class="c_btn h22 black">1차 삭제</a>
					</div>
				</div>
			</div><!-- end first_step -->
			<?php
				}
			?>
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
//		var checked = $(this).is(':checked');
//		if(checked === true) {
//
//			$(this).closest('li').removeClass('if_option_hide');
//			$(this).closest('label').attr('title', '옵션 숨기기');
//			$('.ui-tooltip-content').html('옵션 숨기기');
//		}
//		else {
//
//			$(this).closest('li').removeClass('if_option_hide').addClass('if_option_hide');
//			$(this).closest('label').attr('title', '옵션 보이기');
//			$('.ui-tooltip-content').html('옵션 보이기');
//		}
//
//        category_apply();
//    });
//});
// } 옵션 숨기기 효과




	// 색상 - 이미지에 따른 파일찾기 / color picker 적용
	$(document).ready(function(){
		// color_type 중 선택된 타입 열기
		$(".color_type:checked").each(function(){
			var check_val = $(this).val();
			var pouid = $(this).data("pouid");
			$(".right_box[data-pouid='"+ pouid +"']").hide(); // 동일 pouid 모두 닫기
			$(".right_box." + check_val + "[data-pouid='"+ pouid +"']").show(); // 동일 pouid 중 선택 열기
		});
	});

	// 색상 이미지 선택 처리
	$(document).on("click" , ".color_type" , function(){
		var check_val = $(this).val();
		var pouid = $(this).data("pouid");
		$(".right_box[data-pouid='"+ pouid +"']").hide(); // 동일 pouid 모두 닫기
		$(".right_box." + check_val + "[data-pouid='"+ pouid +"']").show(); // 동일 pouid 중 선택 열기
	});


</script>