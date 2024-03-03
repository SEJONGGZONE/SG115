<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
if($_REQUEST['_mode'] == 'modify') {
	$app_current_name = '게시글 양식 수정';
}else{
	$app_current_name = '게시글 양식 등록';
}
$app_current_link = '_bbs.post_template.list.php';
include_once('wrap.header.php');

// 변수 설정
if($_mode == 'modify'){
	$r = _MQ("select *from smart_bbs_template where bt_uid = '".$_uid."' ");
	if( count($r) < 1 ){ error_msg("게시글 양식이 존재하지 않습니다."); }
}else{
	$_mode = 'add';
}

$_type = $_type!=''?$_type:'admin';
$_type = $r['bt_type']!=''?$r['bt_type']:$_type;
$_type_disabled = $_mode=='modify'?'disabled':'';
?>
<form name="frmBbsPostTemplate" id="frmBbsPostTemplate" action="_bbs.post_template.pro.php" method="post"  autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_type" value="<?php echo $_type; ?>">

	<!-- ●단락타이틀 -->
	<div class="group_title"><strong><?php echo $app_current_name;?></strong><!-- 메뉴얼로 링크 --> </div>

	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>분류</th>
					<td>
						<?php echo _InputRadio( '_type' , array('shop','admin') , $_type  ,$_type_disabled, array('사용자 양식','관리자 양식') , '');?>
						<?php echo _DescStr("사용자 양식에서는 에디터 기능을 사용할 수 없습니다.","red"); ?>
					</td>
					<th >관리용 제목</th>
					<td>
						<input type="text" name="_title" class="design bold t_black" placeholder="제목" value="<?php echo $r['bt_title'] ?>" style="width:500px;">
					</td>
				</tr>

				<tr>
					<th class="ess">양식</th>
					<td colspan="3">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<?php if($_type=='shop'){?>
							<textarea name="_content" class="design" rows="30"><?php echo stripslashes($r['bt_content']); ?></textarea>
						<?php }else{?>
							<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px; display: none;"><?php echo stripslashes($r['bt_content']); ?></textarea>
						<?php }?>
					</td>
				</tr>
				</tr>

			</tbody>
		</table>
	</div>

	<?php echo _submitBTN('_bbs.post_template.list.php'); ?>
</form>

<script type="text/javascript">

	// -- 게시글 양식 타입 선택 (추가일경우)
	$(document).on('change','input[name="_type"]',function(){
		var _type = $(this).val();
		if( _type == ''){ return false; }
		if( confirm("게시글 양식 분류를 변경 시 작성중인 내용들이 초기화 됩니다.\n변경하시겠습니까?") == false){ 
			$(this).val(_type); return false; 
		}
		location.href="_bbs.post_template.form.php?_type="+_type+"&_mode=<?php echo $_mode ?>&_PVSC=<?php echo $_PVSC; ?>";
	});

	$(document).ready(function() {

		// -  validate ---
		$('form[name=frmBbsPostTemplate]').validate({
			ignore: '.ignore',
			rules: {
				_type : {required : true  }
				, _title : {required : false  }
				, _content: { required: true }
			},
			messages: {
				_type : {required : '분류를 선택해 주세요.'  }
				, _title : {required : '제목을 입력해 주세요.'  }
				, _content: { required: '내용을 입력해 주세요.' }
			},
			submitHandler : function(form) {
				form.submit();
			}
		});
		// - validate ---
	});



</script>
<?php
		include_once('wrap.footer.php');
?>