<?PHP
	$app_current_name = "문의수정 및 답변";

	if(!$_GET['pass_menu']) $_GET['pass_menu'] = 'inquiry';
	$app_current_link = '_request.list.php?pass_menu='.$_GET['pass_menu'];
	include_once('wrap.header.php');

	if( $_mode == "modify" ) {
		$row = _MQ(" select * from smart_request where r_uid='{$_uid}' ");

		// -- 게시물 첨부파일을 불러온다.
		$getBoardFile = getFilesRes('smart_request',$_uid);
		$getBoardFileUser = getFilesRes('smart_request', $_uid.'_user'); 
	}

	if( !$pass_menu ) {
		error_msg("메뉴를 선택해주시기 바랍니다.");
	}


?>

<script language='javascript' src='../../include/js/lib.validate.js'></script>


<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_request.pro.php">
<input type=hidden name="_mode" value="<?php echo $_mode; ?>">
<input type=hidden name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type=hidden name="_uid" value="<?php echo $_uid; ?>">
<input type=hidden name="pass_menu" value="<?php echo $pass_menu; ?>">
<input type=hidden name="_menu" value="<?php echo $pass_menu; ?>">

	<div class="data_form">

		<div class="group_title"><strong>문의내용</strong></div>
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<?php if( in_array($row['r_menu'] , array("inquiry")) ){?>
				<tr>
					<th>작성자</th>
					<td colspan="3"> <?php echo showUserInfo($row['r_inid']); ?></td>
				</tr>
				<?php } ?>
				<?php if( in_array($row['r_menu'] , array("partner")) ) {?>
				<tr>
					<th class="ess">이름/상호명</th>
					<td colspan="3">
						<input type="text" name="_comname" value="<?php echo stripslashes($row['r_comname']); ?>" placeholder="이름/상호명" class="design" required>
					</td>
				</tr>
				<tr>
					<th class="ess">연락처</th>
					<td>
						<input type="text" name="_hp" value="<?php echo stripslashes($row['r_hp']); ?>" placeholder="연락처" class="design">
					</td>
					<th class="ess">이메일</th>
					<td>
						<input type="text" name="_email" value="<?php echo stripslashes($row['r_email']); ?>" placeholder="이메일" class="design">
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess">문의제목</th>
					<td colspan="3"><input type="text" name="_title" class="design" style="width:400px" placeholder="문의제목" value="<?php echo stripslashes(strip_tags($row['r_title'])); ?>" /></td>
				</tr>

				<tr>
					<th class="ess">문의내용</th>
					<td colspan="3">
						<textarea name="_content" class="design" placeholder="문의내용" style="width:90%;height:180px;"><?php echo stripslashes($row['r_content']); ?></textarea>
					</td>
				</tr>

				<?php if(count($getBoardFileUser) > 0) {?>
					<tr>
						<th>첨부파일</th>
						<td colspan="3">
							<?php foreach($getBoardFileUser as $k=>$v) { $file_num = $k+1; ?>
								<div class="lineup-row">
									<?php 	if(is_image_file($v['f_oldname']) == true) {?>
										<div class="preview_thumb">
											<img src="<?php echo IMG_DIR_FILE.$v['f_realname']; ?>" class="js_thumb_img js_thumb_popup" data-img="<?php echo IMG_DIR_FILE.$v['f_realname']; ?>" alt="">
											<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid']; ?>" class="c_btn h27" >다운로드</a>
										</div>
									<?php } else { ?>
										<div class="preview_thumb">
											<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid']; ?>" class="btn_file" title="<?php echo addslashes($v['f_oldname']); ?>"><?php echo $v['f_oldname']; ?></a>
										</div>
									<?php } ?>
								</div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>작성일</th>
					<td colspan="3"><?php echo $row['r_rdate']; ?></td>
				</tr>
			</tbody>
		</table>


		<div class="group_title"><strong>답변내용</strong></div>
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
				<tr>
					<th>답변상태</th>
					<td>
						<?php echo _InputRadio( "_status" , array('답변대기','답변완료') , ($row['r_status'] ? $row['r_status'] : "답변대기") , "" , array('답변대기','답변완료') , ""); ?>
					</td>
				</tr>

				<tr>
					<th>답변내용</th>
					<td>
						<textarea name="_admcontent" class="design" placeholder="관리자답변(메모)" style="width:90%;height:180px;" ><?php echo stripslashes($row['r_admcontent']); ?></textarea>
					</td>
				</tr>
				<?php if( in_array($row['r_menu'] , array("partner")) ) {?>
					<tr class="js_status_email" style="<?php echo $row['r_status']=='답변대기'?'display:none':'';?>" >
						<th>이메일 발송</th>
						<td>
							<label class="design"><input type="checkbox" name="_sendmail" value="Y" <?php echo ($row['r_status']=='답변대기'?'checked':''); ?>/> 답변내용 이메일 발송</label>
							<div class="tip_box">
							<?php echo _DescStr("답변내용을 이메일로 발송하려면 체크된 상태로 저장하세요."); ?>
							<?php echo _DescStr("답변상태가 완료일 경우에만 이메일이 발송되며 체크하고 저장하면 계속해서 발송할 수 있습니다."); ?>
							</div>
						</td>
					</tr>
				<?php } ?>

				<tr>
					<th>파일첨부</th>
					<td>
						<?php if( count($getBoardFile) > 0) { ?>
							<?php foreach( $getBoardFile as $k=>$v){  $idx = ($k+1);?>
								<?php if($k == 0){ ?>
									<a href="#none" onclick="return false;" data-idx="<?php echo $idx; ?>" class="c_btn h27 blue exec-addfile">+ 파일추가</a>
									<div class="dash_line"><!-- 점선라인 --></div>
								<?php } ?>
								<div class="tr-files lineup-row" data-idx="<?php echo $idx ?>" data-mode="modify">
									<div class="input_file" style="width:250px">
										<input type="text" id="fakeFileTxt<?php echo $idx;?>" class="fakeFileTxt" readonly="readonly" value="<?php echo $v['f_oldname']; ?>" disabled>
										<div class="fileDiv">
											<input type="button" class="buttonImg" value="파일찾기" />
											<input type="file" name="modifyFile[<?php echo $v['f_uid'] ?>]" value="<?php echo $v['f_oldname']; ?>" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo $idx;?>').value = this.value" />
										</div>
									</div>
									<div class="lineup-row">
										<a href="<?php echo ''.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].''; ?>" class="c_btn dark line" title="<?php echo addslashes($v['f_oldname']); ?>"><?php echo $v['f_oldname']; ?></a>
										<label class="design"><input type="checkbox" name="modifyFile_DEL[]" value="<?php echo $v['f_uid']; ?>" >삭제</label>
									</div>
									<input type="hidden" name="modifyFile_OLD[]" value="<?php echo $v['f_uid'] ?>">
								</div>
							<?php }?>
						<?php }else{ ?>
							<a href="#none" onclick="return false;" class="c_btn h27 blue exec-addfile">+ 파일추가</a>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="tr-files lineup-row" data-mode="add" data-idx="1">
								<div class="input_file" style="width:250px">
									<input type="text" id="fakeFileTxt<?php echo 1;?>" class="fakeFileTxt" readonly="readonly" disabled>
									<div class="fileDiv">
										<input type="button" class="buttonImg" value="파일찾기" />
										<input type="file" name="addFile[]" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo 1;?>').value = this.value" />
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="c_tip"><?php echo implode(",",$arrUpfileConfig['ext']['all']) ?> 파일만 등록 가능합니다.(최대 <em><?php echo number_format($arrUpfileConfig['cnt']).'</em>개' ?>)</div>
					</td>
				</tr>
			</tbody>
		</table>

	</div>


<?php echo _submitBTN("_request.list.php" , "pass_menu={$pass_menu}")?>
</form>


<script>
	// -- 파일 추가 --
	var addfile_auth=true;
	$(document).on('click','.exec-addfile',function(){
		var idx = $('.tr-files').length*1;
		var buid = $('#frmBbs [name="_uid"]').val();
		var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;


		if( idx >= upfileCnt){ alert('파일첨부는 최대 '+number_format(upfileCnt)+'개 까지 첨부가능합니다.'); addfile_auth= true; return false;}

		if(addfile_auth !=true){return false}
		addfile_auth = false;

		var url = '_bbs.post_mng.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
	      	if( data.rst == 'success') {
				addfile_auth = true;

		      	$('div.tr-files[data-idx='+idx+']').after(data.html);
		      	return true;
		      }else{
		      	return false;
		      }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	// -- 파일 삭제
	$(document).on('click','.exec-delfile',function(){
		$(this).closest('div.tr-files[data-mode="add"]').remove();
	});


	// -- 답변완료 이메일 체크
	$(document).on('click','input[name="_status"]',function(){
		var _status = $(this).val();

		$('.js_status_email').hide();
		if(_status=='답변완료'){
			$('.js_status_email').show();
		}

	});



	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						<?php if( in_array($row[r_menu] , array('partner')) ) {?>
						_comname : { required: true },
						_hp: { required: true },
						_email: { required: true },
						<?php } ?>
						_title: { required: true }
						,_content: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						<?php if( in_array($row[r_menu] , array('partner')) ) {?>
						_comname : { required: '이름/상호명을 입력해주시기 바랍니다.' },
						_hp: { required: '연락처를 입력해주시기 바랍니다.' },
						_email: { required: '이메일을 입력해주시기 바랍니다.' },
						<?php } ?>
						_title: { required: '문의제목을 입력해주시기 바랍니다.' }
						,_content: { required: '문의내용을 입력해주시기 바랍니다.' }
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