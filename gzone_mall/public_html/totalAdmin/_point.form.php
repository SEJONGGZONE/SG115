<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_point.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$row = _MQ(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where pl_uid = '{$_uid}' ");
	// 지급상태
	$status_icon = $arr_adm_button['적립예정'];
	if($row['pl_status']=='Y') $status_icon = $arr_adm_button['적립완료'];
	else if($row['pl_status']=='C') $status_icon = $arr_adm_button['적립취소'];

	// 수정불가 - 적립완료, 적립취소, 삭제
	$trigger_mod = true;
	if($row['pl_status'] == 'Y' || $row['pl_status'] == 'C' || $row['pl_delete'] == 'Y') $trigger_mod = false;


}else{
	$_mode = 'add';
	$trigger_mod = true;
}

?>
<form name="frm" action="_point.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">지급내용</th>
					<td colspan="3">
						<input type="text" name="_title" class="design" value="<?php echo $row['pl_title']; ?>" style="width:400px" placeholder="지급내용">
					</td>
				</tr>
				<tr>
					<th class="ess">지급/차감 금액</th>
					<td>
						<div class="lineup-row">
							<?php if($trigger_mod){ ?>
								<input type="text" name="_point" class="design number_style" value="<?php echo number_format($row['pl_point']); ?>" style="width:100px" placeholder="0">
								<?php echo _DescStr('지급할 금액을 입력하고, 차감할 경우는 마이너스(-)를 붙여서 입력해주세요.',''); ?>
							<?php }else{  ?>
								<div class="fr_tx t_blue t_bold t_big"><?php echo number_format($row['pl_point']); ?></div>
							<?php } ?>
						</div>
						<?php if($row['pl_status']=='Y'){ ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="fr_tx t_black t_bold">
								<?php if($row['pl_point'] <> $row['pl_point_apply']){ ?>
									<div class="t_red t_col_to_row">보정 <?php echo number_format($row['pl_point_apply']-$row['pl_point']);?></div>
								<?php 
									}else{
										echo ('적립내역 : ' . number_format($row['pl_point_before']) . ($row['pl_point_apply'] < 0 ? ' - ' : ' + ') . number_format(abs($row['pl_point_apply'])) . ' = ' . number_format($row['pl_point_after']));
									}
								?>
							</div>
						<?php } ?>
					</td>
					<th class="ess">지급(예정)일</th>
					<td>
						<?php if($_mode == 'modify'){ ?>
							<?php echo $status_icon; ?>
							<div class="dash_line"><!-- 점선라인 --></div>
						<?php } ?>
						<?php if($trigger_mod){ ?>
							<div class="lineup-row type_date">
								<input type="text" name="_appdate" class="design js_pic_day_min_today" value="<?php echo $row['pl_appdate']; ?>" style="width:85px" placeholder="날짜 선택" readonly>
							</div>
							<?php echo _DescStr('오늘을 선택하면 바로 적용되어 취소가 불가하고, 미래를 선택할 경우 적립예정이 되어 해당날짜 전까지는 수정 및 취소가 가능합니다.',''); ?>
						<?php }else{  ?>
							<?php echo $row['pl_appdate']; ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th class="ess">적용 회원</th>
					<td colspan="3">
						<?php if($_mode == 'add'){ ?>
							<a href="#none" onclick="window.open('_point.search.php?','','width=900px,height=800px,left=100px,scrollbars=yes'); return false;" class="c_btn h27 blue">+ 회원추가</a>
							<div class="dash_line"><!-- 점선라인 --></div>
							<textarea name="_inid" rows="4" class="design" placeholder="적용 회원"><?php echo $row['pl_inid']; ?></textarea>
							<div class="tip_box">
								<?php echo _DescStr('회원검색버튼을 눌러 적립금을 지급할 회원을 추가하거나 직접 아이디를 콤마(,)로 구분하여 입력할 수 있습니다. '); ?>
								<?php echo _DescStr('같은 회원이 여러번 추가되어도 한번만 적용되며 중복으로 지급되지 않습니다.'); ?>
							</div>
						<?php }else{ ?>
							<?php echo showUserInfo($row['pl_inid'],$row['in_name']); ?>
						<?php } ?>
					</td>
				</tr>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<div class="tip_box">
							<?php echo _DescStr('등록시간 : ' . $row['pl_rdate']); ?>
							<?php if($row['pl_status']=='Y'){ ?>
								<?php echo _DescStr('지급시간 : ' . $row['pl_adate']); ?>
							<?php } ?>
						</div>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN($app_current_link); ?>
</form>



<script type="text/javascript">

	$(document).ready(function() {
		// -  validate ---
		$('form[name=frm]').validate({
			ignore: 'input[type=text]:hidden,input[type=button]',
			rules: {
				<?php if($_mode == 'add'){ ?>
					_title : { required: true }
					,_point : { required: true }
					,_appdate : { required: true }
					,_inid : { required: true }
				<?php }else if($row['pl_status'] == 'N' && $row['pl_delete']=='N'){ ?>
					_title : { required: true }
					,_point : { required: true }
					,_appdate : { required: true }
				<?php }else{ ?>
					_title : { required: true }
				<?php } ?>
			},
			messages: {
				<?php if($_mode == 'add'){ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
					,_point : { required: '지급적립금를 입력해주시기 바랍니다.' }
					,_appdate : { required: '지급예정일을 입력해주시기 바랍니다.' }
					,_inid : { required: '적용유저를 입력해주시기 바랍니다.' }
				<?php }else if($row['pl_status'] == 'N' && $row['pl_delete']=='N'){ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
					,_point : { required: '지급적립금를 입력해주시기 바랍니다.' }
					,_appdate : { required: '지급예정일을 입력해주시기 바랍니다.' }
				<?php }else{ ?>
					_title : { required: '제목을 입력해주시기 바랍니다.' }
				<?php } ?>
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

				if($('[name=_point]').val() ==''){ 
					alert('지급적립금를 입력해주시기 바랍니다.'); return false;
				}

				form.submit();
			}
		});
		// - validate ---
	});



</script>
<?php include_once('wrap.footer.php'); ?>