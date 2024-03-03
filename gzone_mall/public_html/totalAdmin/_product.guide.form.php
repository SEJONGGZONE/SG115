<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_product.guide.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_product_guide where g_uid = '{$_uid}' ");
	$pass_com = $r['g_user'];
}else{
	$_mode = 'add';
	$r['g_user'] = '_MASTER_'; // 등록시에는 통합관리자로 등록
}

// => 기본입점업체를 어떻게 지정해야할지 논의 필요
//// SSJ : 2017-12-03 입점업체는 기본입점업체로 지정
// $com_first = _MQ_assoc(" select cp_id from smart_company order by cp_name asc ");
// ViewArr($com_first);
// $arr_customer = arr_company();
// ViewArr($arr_customer);
// $p_user = $com_first['cp_id'];


?>
<form name="frm" action="_product.guide.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

	<?php if($SubAdminMode !== true && $AdminPath == 'totalAdmin') { ?> <input type="hidden" name="g_user" value="<?php echo ($r['g_user']?$r['g_user']:'_MASTER_'); ?>"> <?php } ?>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>입점업체</th>
					<td>
						<?php
						// ----- JJC : 입점관리 : 2020-09-17 -----
						if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer = array_merge(array('_MASTER_'=>'통합관리자'), $arr_customer);
							$arr_customer2 = arr_company2();
							$arr_customer2 = array_merge(array('_MASTER_'=>'통합관리자'), $arr_customer2);
						?>
							<?php if( $AdminPath == 'totalAdmin'){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
							<?php echo _InputSelect( 'g_user' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '입점업체 선택'); ?>
							<?php }else{ ?>
								<?php echo $arr_customer2[$pass_com]; ?>
							<?php } ?>
						<?php }else{?>
							<?php echo _DescStr('입점업체 미사용 <a href="https://www.onedaynet.co.kr/p/solution_plus.html#page_entershop" target="_blank"><em>신청하기</em></a>',''); ?>
						<?php }?>

						<?php if($AdminPath == 'totalAdmin'){ ?>
							<?php echo _DescStr('통합관리자로 선택 시 모든 입점업체가 사용 가능합니다.',''); ?>
						<?php } ?>
					</td>
					<th>분류</th>
					<td>
						<?php echo _InputSelect('g_type', array_keys($arrProGuideType), $r['g_type'], '', array_values($arrProGuideType), '구분선택'); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">타이틀</th>
					<td>
						<input type="text" name="g_title" class="design" value="<?php echo $r['g_title']; ?>" style="width:400px" placeholder="타이틀">
						<?php echo _DescStr('내용입력 선택 시 구분하기 위한 용도이며 사용자페이지에 노출되지 않습니다.',''); ?>
					</td>
					<th>기본설정</th>
					<td>
						<label class="design"><input type="checkbox" name="g_default" value="Y" <?php echo ($r['g_default']=='Y'?' checked ':null); ?>>기본으로 설정</label>
						<?php echo _DescStr('분류별로 1개만 설정가능하며, 상품 등록 시 자동으로 먼저 선택 및 입력됩니다. (상품별로 수정가능)',''); ?>
						<?php echo _DescStr('통합관리자의 이용안내가 기본 설정된 경우 업체의 기본 설정보다 우선으로 자동 선택됩니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">내용</th>
					<td colspan="3">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="g_content" class="input_text SEditor" ><?php echo stripslashes($r['g_content']); ?></textarea>
					</td>
				</tr>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>등록일</th>
					<td>
						<?php echo date('Y-m-d (H:i:s)', strtotime($r['g_rdate'])); ?>
					</td>
					<th>최종 수정일</th>
					<td>
						<?php echo date('Y-m-d (H:i:s)', strtotime($r['g_mdate'])); ?>
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
				g_type: { required: true}	// 등록구분
				,g_title: { required: true }	// 타이틀
				,g_content: { required: true }	// 상세내용
			},
			messages: {
				g_type: { required: '등록구분을 선택해주시기 바랍니다.'}	// 등록구분
				,g_title: { required: '타이틀을 입력해주시기 바랍니다.'}	// 타이틀
				,g_content: { required: '상세내용을 입력해주시기 바랍니다.'}	// 상세내용
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
		// - validate ---
	});


	$(document).on('change','[name="g_default"]',function(e){
		var chcked = $(this).prop('checked');
		if( chcked === true){  alert("기본으로 설정된 동일한 분류의 이용안내는 자동으로 해제됩니다"); return false; }
	});

</script>
<?php include_once('wrap.footer.php'); ?>