<?php // -- LCY :: 2017-09-20 -- 회원등급관리 폼
		$app_current_link = '_member_group_set.list.php';
		include_once('wrap.header.php');

		if( in_array($_mode,array('modify','add')) == false){ error_loc_msg("_member_group_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다."); }

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_member_group_set where mgs_uid = '".$_uid."'  ");
			if( count($row) < 1){ error_loc_msg("_member_group_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "회원등급 정보가 없습니다." ); }
			$printRank = $row['mgs_rank'];
		}else{ // 추가일시
			// -- 등록된 등급중 가장 큰 순서를 가져온다.
			$rowRank = _MQ_result("select mgs_rank from smart_member_group_set order by mgs_rank desc limit 0, 1");
			$printRank = $rowRank == '' ? 1 : ($rowRank+1);
		}
?>

<form action="_member_group_set.pro.php" name="frm" id="frm" target="common_frame" method="post" ENCTYPE="multipart/form-data"> <?php // {{{회원등급추가}}} ?>
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="_mode" value="<?=$_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="ajaxMode" value="<?=$_mode == 'add' ? 'add':'modify'?>"> <?php // -- ajax 모드 ?>
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">


	<div class="group_title"><strong>기본정보</strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">등급명</th>
					<td>
						<input type="text" name="_name" class="design" style="" value="<?php echo $row['mgs_name'] ?>" placeholder="등급명">
					</td>
					<th class="ess">등급 순서</th>
					<td>
						<div class="lineup-row">
							<input type="hidden" name="_rank" value="<?php echo  $printRank?>">
							<input type="text" name="_sort_group" class="design t_right" value="<?php echo $row['mgs_sort_group']; ?>" placeholder="0" <?php echo $printRank == 1 ? 'readonly':null ?>>
							<input type="hidden" name="_sort_idx" class="design" value="<?php echo $row['mgs_sort_idx']; ?>" >
							<input type="hidden" name="_idx" class="design" value="<?php echo $row['mgs_idx']; ?>" >
						</div>
						<div class="tip_box">
							<?php echo _DescStr('숫자가 낮을수록 먼저 노출되며 기본등급(1번)은 변경이 불가능합니다.', ''); ?>
							<?php echo _DescStr('숫자가 높을수록 등급평가 시 최종 적용되니 숫자가 겹치지 않도록 입력합니다.', ''); ?>
						</div>
					</td>
				</tr>
				<tr style="display:none">
					<th>(PC) 회원등급 아이콘</th>
					<td>
						<div class="tip_box">
							<?php echo _PhotoForm('../upfiles/icon', '_icon', $row['mgs_icon'], 'style="width:280px"'); ?>
							<?php echo _DescStr('이미지 사이즈 : 75 × 75 (pixel)'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>등급 아이콘</th>
					<td colspan="3">
						<div class="lineup-row">
							<?php echo _PhotoForm('../upfiles/icon', '_mobile_icon', $row['mgs_mobile_icon'], 'style="width:280px"'); ?>
						</div>
						<?php echo _DescStr('권장 사이즈 : 200 × 200 (pixel)','black'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>평가 기준</strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">구매금액</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx"><strong class="t_sky t_bold">평가기간</strong> 동안 총 구매 금액이 </span>
							<input type="text" name="_condition_totprice" <?=rm_str($printRank) <= 1 ? 'readonly':''?> class="design number_style" style="width:100px;" value="<?php echo $row['mgs_condition_totprice'] ?>" placeholder="0">
							<span class="fr_tx">원 이상</span>
						</div>
					</td>
					<th class="ess">구매횟수</th>
					<td>
						<div class="lineup-row">
							<span class="fr_tx"><strong class="t_sky t_bold">평가기간</strong> 동안 총 구매횟수가 </span>
							<input type="text" name="_condition_totcnt" <?=rm_str($printRank) <= 1 ? 'readonly':''?> class="design number_style" style="width:70px;" value="<?php echo $row['mgs_condition_totcnt'] ?>" placeholder="0">
							<span class="fr_tx">회 이상</span>
						</div>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<a href="/totalAdmin/_config.group.php" class="c_btn sky line" >평가기간 설정하기</a>
						<?php echo _DescStr('등급평가 시 평가기간동안의 구매금액과 구매횟수에 모두 만족해야만 등급에 대한 적용이 됩니다.', ''); ?>
						<?php echo _DescStr('평가기간은 회원등급 기본 설정에서 별도로 설정할 수 있습니다.', ''); ?>
						<?php echo _DescStr('등급순서가 1인 기본등급은 회원가입 후 바로 적용되는 등급으로서 평가기준을 설정할 수 없습니다.', ''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>혜택 설정</strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">할인율</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_sale_price_per" class="design t_right" style="width:70px;" value="<?php echo str_replace("0.0","0",$row['mgs_sale_price_per']) ?>" placeholder="0.0">
							<span class="fr_tx">%</span>
						</div>
					</td>
					<th>적립률</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_give_point_per" class="design t_right" style="width:70px;" value="<?php echo str_replace("0.0","0",$row['mgs_give_point_per']) ?>" placeholder="0.0">
							<span class="fr_tx">%</span>
						</div>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('할인율과 적립률은 소수점 1자리까지 입력가능합니다. ', ''); ?>
						<?php echo _DescStr('해당 등급의 회원은 상품상세 페이지에 할인율과 적립률이 노출되며 할인율과 적립률이 적용되어 장바구니에 담기게 됩니다.', ''); ?>
						<?php echo _DescStr('상품상세에서 별도로 회원등급 추가혜택을 미적용 할 경우 등급 혜택은 적용되지 않습니다.', ''); ?>
						<?php echo _DescStr('총 결제금액이 아닌 상품별 금액 기준으로 혜택이 적용됩니다.', ''); ?>
						<?php echo _DescStr('위 혜택은 마이페이지 주문내역에는 별도로 표시되지 않으며 관리자페이지 주문(할인혜택)에서 확인이가능합니다. ', 'black'); ?>
						<?php echo _DescStr('기본 상품 판매가 및 옵션 판매가에 적용되며 추가옵션에는 적용되지 않습니다.', 'black'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN('_member_group_set.list.php'); ?>


</form>


<script>

$(document).ready(mgsValidate);

function mgsValidate()
{
	$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					_name: { required: true }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

			},
			messages: {
					_name : { required: '회원등급 이름을 입력해 주세요.' }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
	});
}

</script>




<?php
	include_once('wrap.footer.php');

?>