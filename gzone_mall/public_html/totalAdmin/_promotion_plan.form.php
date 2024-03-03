<?php

	$app_current_link = '_promotion_plan.list.php';
	$app_current_name = '기획전 등록';

	include_once('wrap.header.php');
	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " select * from smart_promotion_plan where pp_uid='${uid}'  ";
		$row = _MQ($que);

		// smart_table_text를 통해 pp_content(PC내용), pp_content_m(모바일 내용) 정보 가져옴
		$row = array_merge($row , _text_info_extraction( "smart_promotion_plan" , $row['pp_uid'] ));

	}
	// - 수정 ---
	else {
		// 추가일 경우
		$uid = '0';

		// 기획전 추가 시 - 적용한 기획전 상품 삭제 --> uid가 0인 경우 삭제
		_MQ_noreturn(" delete from smart_promotion_plan_product_setup where ppps_ppuid = '". $uid ."' ");
	}
?>



<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_promotion_plan.pro.php" >
	<input type="hidden" name="_mode" value="<?php echo $_mode?>">
	<input type="hidden" name="uid" value="<?php echo $uid?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC?>">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="160"><col width="*"><col width="160"><col width="*">
			</colgroup>
			<tbody>
				<tr>

					<th>노출여부</th>
					<td >
						<?php echo _InputRadio( '_view' , array('Y','N'), ($row['pp_view'] ? $row['pp_view'] : 'Y') , '' , array('노출','숨김') , ''); ?>
					</td>
					<th>진행기간</th>
					<td >
                        <div class="lineup-row type_date">
                            <input type="text" name="_sdate" value="<?=$row['pp_sdate']?>" class="design js_pic_day" placeholder="날짜 선택" style="width:85px" readonly>
                            <span class="fr_tx">-</span>
                            <input type="text" name="_edate" value="<?=$row['pp_edate']?>" class="design js_pic_day right" placeholder="날짜 선택" style="width:85px" readonly>
                        </div>
					</td>
				</tr>
				<tr>
					<th>기획전명</th>
					<td colspan="3">
						<input type="text" name="_title" class="design" value="<?php echo stripslashes(strip_tags($row['pp_title'])); ?>" placeholder="기획전명" style="width:100%" >
					</td>
				</tr>
				<tr>
					<th>목록이미지</th>
					<td colspan="3">
                        <div class="lineup-row">
						    <?php echo _PhotoForm('..'.IMG_DIR_BANNER, 'pp_img', $row['pp_img'], 'style="width:250px"'); ?>
                        </div>
                        <div class="tip_box">
						    <?php echo _DescStr('스킨 디자인에 따라 자동 조절될 수 있습니다. (권장 사이즈 625px * free)'); ?>
                        </div>
					</td>
				</tr>
				<tr>
					<th>상단 내용</th>
					<td colspan="3">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="_content" class="input_text SEditor" ><?php echo stripslashes($row['pp_content']); ?></textarea>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


	<?php if($_mode == 'modify') { ?>
		<div class="group_title"><strong>기획전 상품관리</strong></div>
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick="promotion_plan_product_setup_del(); return false;" class="c_btn h27 black line">선택삭제</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="promotion_plan_product_setup_add(); return false;" class="c_btn h27 blue">상품추가</a>
			</div>
		</div>
		<div ID="product_table_id">
			<?php
				//$uid = $uid
				include_once("_promotion_plan.product_ajax.php");
			?>
		</div>
		<?php echo _DescStr('숨김상품은 기획전에서도 노출되지 않습니다. ',''); ?>

	<?php } ?>
	<?php if($_mode == 'add'){ ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">기획전 저장 후에 상품등록이 가능합니다.</div></div>
	<?php } ?>



	<?php echo _submitBTN('_promotion_plan.list.php'); ?>

</form>





	<!-- 기획전 상품 관련 함수 -->
	<script type="text/javascript">

		//기획전 상품 등록
		function promotion_plan_product_setup_add() {
			var uid = $('[name="frm"] [name="uid"]').val();
			if( uid == undefined || uid == '' || uid == '0' || uid == 0){ alert('기획전 추가 후 선택 가능합니다.'); return false; }

			window.open('_promotion_plan.product_pop.php?uid='+uid , 'relation', 'width=1350, height=800,scrollbars=yes');
		}

		//기획전 상품 삭제
		function promotion_plan_product_setup_del() {
			if($('.class_pcode').is(":checked")){
				if(confirm("정말 삭제하시겠습니까?")){
					$("input[name='smart_promotion_plan_product_setup_mode']").val("mass_delete"); // 일괄삭제
					$("form[name='frm']").attr("action" , "_promotion_plan.product_pro.php");
					$("form[name='frm']").attr("target" , "common_frame");
					document.frm.submit();
					$("form[name='frm']").attr("action" , "_promotion_plan.pro.php");
					$("form[name='frm']").attr("target" , "");
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}


		// 기획전 상품 목록 보기
		function promotion_plan_product_setup_view(uid) {
			$.ajax({
				url: "_promotion_plan.product_ajax.php", cache: false, type: "POST",
				data: "uid=" + uid ,
				success: function(data){
					$("#product_table_id").html(data);
				}
			});
		}

	</script>
	<!-- 기획전 상품 관련 함수 -->




<?php include_once('wrap.footer.php'); ?>