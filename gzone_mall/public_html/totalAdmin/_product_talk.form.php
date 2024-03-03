<?PHP

	if(!isset($_GET['pt_type'])) $_GET['pt_type'] = '상품리뷰';

	$app_current_name = $_GET['pt_type']=="상품리뷰"?"리뷰 수정":"문의 수정";
	$app_current_link = '_product_talk.list.php?pt_type='.$_GET['pt_type'];

	include_once('wrap.header.php');
	$app_path = '..'.IMG_DIR_PRODUCT;

	// 메인에서 클릭후 넘어온경우 목록 링크에 pt_type을 추가해줘야함
	if($main_chk=='Y'){
		$app_list_link = $app_current_link;
	}else{
		$app_list_link = "_product_talk.list.php";
	}

	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " select *  from smart_product_talk where pt_uid='".$pt_uid."' ";
		$row = _MQ($que);
		if(!$row['pt_uid']) error_msg('잘못된접근입니다.');
		$_str = "수정";
		$in_id = $row['pt_inid'];
		$pt_type = $row['pt_type'];
		$pt_depth = $row['pt_depth'];
		$pt_eval_point = $row['pt_eval_point'];
		$pt_writer = $row['pt_writer'];
	}
	else {

		// - 등록 ---
		$_str = "등록";

		if($AdminPath != 'totalAdmin'){
			$in_id = $com_id;
			$pt_writer = $com['cp_name'] != '' ? $com['cp_name'] : $seller_name;
		}
		else{
			$in_id = $siteInfo['s_adid'];
			$pt_writer = get_reply_writer();
		}

	}

?>
<script language='javascript' src='../../include/js/lib.validate.js'></script>

<form name="frm" method="post" action="_product_talk.pro.php" enctype="multipart/form-data">
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="pt_uid" value="<?php echo $pt_uid; ?>">
<?PHP
	if( $pt_uid && $_mode == "add") {
		$ique = "
			select
				pt.* , p.p_name, p.p_img_list_square
			from smart_product_talk  as pt
			inner join smart_product as p on (pt.pt_pcode = p.p_code)
			where pt.pt_uid='".$pt_uid."'
		";
		$ir = _MQ($ique);

		// 이미지 체크
		$_p_img = get_img_src($ir['p_img_list_square']);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		// 부모글 이미지 체크
		$_pt_img = get_img_src($ir['pt_img']);

		// 평점 -> 별로 변환
		$eval_str = eval_point_change_star( $ir['pt_eval_point'] );
?>
	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>본문 내용</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>상품정보</th>
					<td>
						<div class="product_box">
							<img src="<?php echo $_p_img; ?>" class="" data-img="<?php echo $_p_img; ?>" alt="">
							<dl>
								<dt><?php echo $ir['pt_pcode']; ?></dt>
								<dd><?php echo $ir['p_name']; ?></dd>
							</dl>
						</div>
					</td>
				</tr>

				<?php if($ir['pt_type'] == "상품리뷰" && $ir['pt_depth'] == 1) { ?>
					<tr>
						<th>평점</th>
						<td class="t_star"><?php echo $eval_str; ?></td>
					</tr>
				<?php } ?>
			</tbody>
				<?php if($ir['pt_type'] == "상품문의") { ?>
					<tr>
						<th>본문 제목</th>
						<td><?php echo stripcslashes(htmlspecialchars($ir['pt_title'])); ?></td>
					</tr>
				<?php }?>
				<tr>
					<th>본문 내용</th>
					<td>
						<?php if($_pt_img) { ?><div style="margin-bottom:5px;"><img src="<?=$_pt_img?>" alt="" style="max-width:300px;"/></div><?php } ?>
						<?php echo nl2br(stripcslashes(htmlspecialchars($ir['pt_content']))); ?>
					</td>
				</tr>
				<tr>
					<th>작성자</th>
					<td><?php echo $ir['pt_writer']; ?> (<?php echo $ir['pt_inid']; ?>)</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php }?>

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong><?php echo ($_mode=='modify'?'본문':'답변'); ?> 내용</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">작성자</th>
					<td>
						<input type="text" name="pt_writer" class="design" placeholder="작성자" value="<?php echo $pt_writer; ?>"/>
						<input type="hidden" name="pt_inid" value="<?php echo $in_id; ?>"/>
					</td>
				</tr>
				<?php if($_mode=='modify' && $row['pt_type'] == '상품리뷰' && $row['pt_depth'] == 1) { ?>
				<tr>
					<th>평점</th>
					<td><?php echo _InputRadio( 'pt_eval_point' , array('5','4','3','2','1'), ($row['pt_eval_point']/20) , '' , array('5점', '4점','3점','2점','1점') , ''); ?></td>
				</tr>
				<tr>
					<th>이미지</th>
					<td>
						<div class="lineup-row">
							<?php
								if($row['pt_depth'] == 1) echo _PhotoForm($app_path, '_img', $row['pt_img'], 'style="width:300px"');
								else echo '<img src="'.$app_path.$row['pt_img'].'" alt="" width="80">';
							?>
						</div>
					</td>
				</tr>
				<? } ?>
				<?php if($row['pt_type'] == '상품문의' && $row['pt_depth'] == 1) { ?>
				<tr>
					<th class="ess"><?php echo ($_mode=='modify'?'본문':'답변'); ?> 제목</th>
					<td>
						<input type="text" name="pt_title" class="design" placeholder="<?php echo ($_mode=='modify'?'본문':'답변'); ?>제목" value="<?php echo $row['pt_title']; ?>" style="width:98%;"/>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess"><?php echo ($_mode=='modify'?'본문':'답변'); ?> 내용</th>
					<td><textarea name="pt_content" class="design" placeholder="<?php echo ($_mode=='modify'?'본문':'답변'); ?>내용" style="width:98%;height:200px;"><?php echo $row['pt_content']; ?></textarea></td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- KAY :: 2022-01-13 :: 상품리뷰 신고 관리자 노출 -->
	<?php
		if($row['pt_type'] == '상품리뷰'){
			$report_info = _MQ_assoc("select * from smart_product_talk_report where ptr_ptuid = '".$row['pt_uid']."' ");
			$report_total = count($report_info);
	?>
		<?php if($report_total>0){?>
			<!-- ●단락타이틀 -->
			<div class="group_title" data-name="view-form"><strong>신고내역 (<?php echo $report_total;?>건)</strong></div>
				<div class="data_list">
					<table class="table_must">
						<colgroup>
							<col width="40"/><col width="*"/><col width="*"/><col width="95"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset" >No</th>
								<th scope="col" class="colorset" >사유</th>
								<th scope="col" class="colorset" >신고자</th>
								<th scope="col" class="colorset" >신고일</th>
							</tr>
						</thead>
						<tbody class="view-visit-list">
							<?php
								foreach($report_info as $rk => $rv){
									$_num = $report_total-$rk;

									// 신고 사유
									$report_rea = $rv['ptr_reason']=='4'?'기타 ['.$rv['ptr_reason_etc'].']':$arr_reposrt_reason['review'][$rv['ptr_reason']];
							?>
							<tr>
								<td><?php echo $_num;?></td>
								<td><?php echo $report_rea;?></td>
								<td><?php echo $rv['ptr_inid'];?></td>
								<td><?php echo printDateInfo($rv['ptr_rdate']);?></td>
							</tr>
							<?php }?>
						</tbody>
				</table>
				<div class="common_none view-visit-list-none" style="display:none;"><div class="no_icon"></div><div class="gtxt">신고 정보가 없습니다.</div></div>
				<div class="paginate view-visit-paginate"></div>
			</div>
		<?php }?>
	<?php }?>

	<?php echo _submitBTN($app_list_link); ?>	
</form>


<script>
	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						pt_writer: { required: true }
						<?php if($row['pt_type'] == '상품문의' && $row['pt_depth'] == 1) { ?>
						,pt_title: { required: true }
						<?php } ?>
						,pt_content: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						pt_writer: { required: '작성자를 입력해주시기 바랍니다.' }
						<?php if($row['pt_type'] == '상품문의' && $row['pt_depth'] == 1) { ?>
						,pt_title: { required: '<?php echo ($_mode=='modify'?'본문':'답변'); ?>제목을 입력해주시기 바랍니다.' }
						<?php } ?>
						,pt_content: { required: '<?php echo ($_mode=='modify'?'본문':'답변'); ?>내용을 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});
</script>


<?PHP
	include_once('wrap.footer.php');
?>