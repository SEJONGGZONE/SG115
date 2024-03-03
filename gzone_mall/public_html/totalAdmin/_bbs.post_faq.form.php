<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
if($_REQUEST['_mode'] == 'modify') {
	$app_current_name = 'FAQ 수정';
}else{
	$app_current_name = 'FAQ 등록';
}
$app_current_link = '_bbs.post_faq.list.php';
include_once('wrap.header.php');
if( in_array($_mode, array('add','modify')) == false){  error_msg("잘못된 접근입니다."); }

$arrFaq = unserialize(stripslashes($siteInfo['s_bbs_faq_type']));
$faqType = explode(',',$arrFaq['type']);

// FAQ 분류 키값 1씩 더해주기
foreach($faqType as $ftk => $ftv){
	$faqTypeVal[$ftk+1] = $ftv;
}


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ("select *from smart_bbs_faq  where bf_uid = '".$_uid."' ");
	if( count($r) < 1 ){ error_msg("게시물이 존재하지 않습니다."); }
	$_title = htmlspecialchars(stripslashes($r['bf_title'])); // 제목
	$_content = stripslashes($r['bf_content']); // 질문내용

}else{ // 추가

	$_title = '';
	$_content = '';
}

?>
<form name="frmBbsFaq" id="frmBbsFaq" action="_bbs.post_faq.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid ?>">

	<!-- ●단락타이틀 -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">제목</th>
					<td colspan="3">
						<input type="text" name="_title" maxlength="300" class="design bold t_black" placeholder="제목" value="<?php echo $_title ?>" style="width:100%;">
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _InputCheckbox( '_best' , array('Y'), ($r['bf_best']) , '' , array('베스트로 지정') , '');?>
						<?php
							echo _DescStr("베스트글로 지정되면 고객센터 메인에 최대 ".number_format($arrFaqBoardConfig['bestCnt'])."개만 최근 등록순으로 노출됩니다.");
						?>
					</td>
				</tr>
				<tr>
					<th class="ess">분류</th>
					<td colspan="3">
						<?php echo _InputRadio( '_type' , array_keys($faqTypeVal)  , ($r['bf_type']) , '' , array_values($faqTypeVal) , '-분류선택-'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="/totalAdmin/_bbs.post_faq_type.form.php" class="c_btn sky line">분류설정 바로가기</a>
					</td>
				</tr>
				<tr>
					<th class="ess">내용</th>
					<td colspan="3">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px;"><?php echo $_content; ?></textarea>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<?php echo _submitBTN('_bbs.post_faq.list.php'); ?>
</form>

<script type="text/javascript">
	$(document).ready(function(){
		// -  validate ---
		$('form[name=frmBbsFaq]').validate({
			ignore: '.ignore',
			rules: {
				_title : {required : true  }
				,	_type : {required : true  }
				,	_content : {required : true  }
			},
			messages: {
				_title : {required : '제목을 입력해 주세요.'  }
				,	_type : {required : '분류를 선택해 주세요.' }
				,	_content : {required : '내용을 입력해 주세요.' }
			},
			submitHandler : function(form) {
				form.submit();
			}
		});
		// - validate ---
	});
</script>
<?php
	// -- 게시판 정보를 불러온다. {{{
	include_once('wrap.footer.php');
?>