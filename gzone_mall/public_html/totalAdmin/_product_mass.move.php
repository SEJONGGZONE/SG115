<?PHP
	include_once("wrap.header.php");

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기


	// 전체 카테고리 정보 추출
	$arr_category_name = array();
	$c_que = "select c_uid , c_name from smart_category ";
	$c_res = _MQ_assoc($c_que);
	foreach( $c_res as $k=>$v ){
		$arr_category_name[$v['c_uid']] = $v['c_name'];
	}
	// 전체 카테고리 정보 추출

	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();

	// 상품 일괄 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_product as p where 1 ";

	$search_detail_name = 'mass_move';
	include_once("_product.inc_search.php");
	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'p_idx';
	if(!$so) $so = 'asc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select p.* $s_query order by {$st} {$so} limit $count , $listmaxcount ");

?>


	<div class="list_ctrl">
		<div class="left_box">
			<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
			<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
			<a href="#none" class="c_btn blue" onclick="$('.js_thisform').toggle(); return false;">선택상품 일괄변경</a>
		</div>
		<div class="right_box">
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
			</select>
		</div>
	</div>



	<div class="data_search js_thisform" style="display:none">
		<table class="table_form">
			<colgroup>
				<col width="130px"/><col width="*"/><col width="130px"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>적용방법</th>
					<td colspan="3">
						<?php echo _DescStr("① 원하는 상품을 전체 또는 개별 선택합니다. ","black"); ?>
						<?php echo _DescStr("② 추가, 삭제, 복사를 원하는 카테고리를 선택하고 [선택추가] 버튼을 클릭합니다. (여러개 추가가능) ","black"); ?>
						<?php echo _DescStr("③ 진행하고자 하는 관리버튼을 골라서 클릭합니다. ","black"); ?>
						<?php echo _DescStr("※ 선택 상품 복사 + 카테고리 추가는 페이지 로딩 속도를 고려하여 되도록 5개 이하 단위로 실행해 주시기 바랍니다.")?>
					</td>
				</tr>
				<tr>
					<th>카테고리 선택</th>
					<td colspan="3">
						<?php include_once("_product_mass.inc_category.form.php"); ?>
					</td>
				</tr>
				<tr>
					<th>추가</th>
					<td>
						<div class="lineup-row type_side">
							<span class="fr_tx t_blue">선택한 상품에 위 카테고리를 추가할까요?</span><a href="#none" onclick="selectCategoryAdd();" class="c_btn blue line" style="width:160px">카테고리 추가</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_side">
							<span class="fr_tx t_blue">선택한 상품에 위 카테고리를 추가하여 상품을 복사할까요?</span><a href="#none" onclick="selectCategoryCopy();" class="c_btn line blue" style="width:160px">상품복사 + 카테고리 추가</a>
						</div>
					</td>
					<th>삭제</th>
					<td>
						<div class="lineup-row type_side">
							<span class="fr_tx t_red">선택한 상품에 위 카테고리를 삭제할까요?</span><a href="#none" onclick="selectCategorySelDel();" class="c_btn line red" style="width:160px">카테고리 삭제</a>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_side">
							<span class="fr_tx t_red">선택한 상품에 카테고리를 전부 초기화(삭제)할까요?</span><a href='#none' onclick="selectCategoryDelete();" class='c_btn line red' style="width:160px">카테고리 초기화</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<script type="text/javascript">
		$(document).delegate('.detail_sc', 'click', function(e) {
			e.preventDefault();
			var mode2 = $('.sc_detail_mode').val();
			if(mode2 == 1) {
				$('.sc_detail_mode').val(2);
				$('.sc_detail').show();
				$(this).attr('title' , '상품 이동/복사/삭제 닫기').html('상품 이동/복사/삭제 닫기');
			}
			else {
				$('.sc_detail_mode').val(1);
				$('.sc_detail').hide();
				$(this).attr('title' , '상품 이동/복사/삭제 열기').html('상품 이동/복사/삭제 열기');
			}
		});
	</script>
	<!-- // 검색영역 -->







<div class="data_list" >
	<form name="frm" method="post" action="_product_mass.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_submode" value="mass_move">
		<input type="hidden" name="_select_category_cnt" value="0">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC?>">

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="90"/>
				<col width="*"/>
				<col width="*"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">노출</th>
					<th scope="col" >상품정보</th>
					<th scope="col">카테고리</th>
				</tr>
			</thead>
			<tbody>


			<?php
				foreach($res as $k=>$v){

					$_num = $TotalCount - $count - $k ;

					$pname = addslashes(strip_tags($v['p_name']));

					$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
					if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

					$ex_coupon = explode("|" , $v['p_coupon']);

					// 상품 카테고리 정보 추출
					$arr_product_category_string = array();
					$app_product_category_string = "";
					$pct_que = "
						SELECT c.*
						FROM smart_product_category as pct
						INNER JOIN smart_category as c ON ( c.c_uid = pct.pct_cuid )
						WHERE
							pct.pct_pcode = '". $v['p_code'] ."'
					";
					$pct_res = _MQ_assoc($pct_que);
					foreach($pct_res as $pct_sk=>$pct_sv){
						$arr_tmp_string = array();
						if( $pct_sv['c_parent'] > 0 ){
							$ex = explode("," , $pct_sv['c_parent']);
							if($ex[0] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[0]];}
							if($ex[1] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[1]];}
						}
						$arr_tmp_string[] = $arr_category_name[$pct_sv['c_uid']];
						$arr_product_category_string[] = implode(" &gt; " , $arr_tmp_string);
					}
					if(sizeof($arr_product_category_string) > 0 ){
						$app_product_category_string = "<div class='mass_category'>". implode("</div><div class='mass_category'>" , $arr_product_category_string) ."</div>";
					}

					// 상품 카테고리 정보 추출

					if($v['p_type']=='ticket'){
						$plink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
					}else{
						$plink = '_product.form.php?_mode=modify&_code='.$v['p_code'].'';
					}
			?>

				<tr>
					<td class='this_check'><label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['p_code'];?>]" value="Y" class="js_ck" data-pcode="<?php echo $v['p_code'];?>"></label></td>
					<td class='this_num'><?php echo $_num;?></td>
					<td class='this_ctrl'><?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')];?></td>
					<td class="this_item">
						<div class="order_item_thumb type_simple">
							<div class="thumb"><a href="<?php echo $plink;?>" target="_blank" title="<?php echo $pname;?>"><img src="<?php echo $_p_img;?>" alt="<?php echo $pname;?>" /></a></div>
							<div class="order_item">
								<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){?>
									<div class="entershop"><?php echo showCompanyInfo($v['p_cpid']);?></div>
								<?php }?>
								<dl>
									<dt><div class="item_name"><a href="<?php echo $plink;?>" target="_blank"><?php echo strip_tags($v['p_name']);?></a></div></dt>
									<dt class="t_light"><?php echo $v['p_code'];?></dt>
								</dl>
							</div>
							<div class="other_tag">
								<?php echo $arr_adm_button[$v['p_type']];?>
								<?php
									if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
									else{
										if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
										else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
										else{  echo '<span class="c_tag light t4">판매종료</span>'; }
									}
								?>
							</div>
						</div>

						<?php if(in_array($v['p_option_type_chk'] , array('1depth','2depth','3depth')) ){?>
							<div class="lineup-row">
								<div class="dash_line"><!-- 점선라인 --></div>
								<a href="#none" onclick="option_popup('<?php echo $v['p_code'];?>' , '<?php echo $v['p_option_type_chk'];?>')" class="c_btn orange line" >필수옵션</a>
								<a href="#none" onclick="addoption_popup('<?php echo $v['p_code'];?>')" class="c_btn sky line" >추가옵션</a>
							</div>
						<?php }?>
					</td>
					<td class="t_left">
						<?php echo $app_product_category_string;?>
					</td>
				</tr>
			<?php }?>

				</tbody>
			</table>


			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>


			<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
			</div>
	</div>
</form>





<script src="/include/js/jquery/jquery.ui.datepicker-ko.js"></script>
<SCRIPT>

	// - 옵션열기 ---
	function option_popup(pass_code , pass_mode) {
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}


	// - 추가옵션열기 ---
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}


	// 선택상품 삭제
	 function selectDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("정말 선택한 상품을 삭제 하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				// 상품관리의 상품처리 파일 이용하여 처리함.
				$("form[name=frm]").attr("action" , "_product.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 상품의 전체 카테고리 제외
	 function selectCategoryDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("정말 선택한 상품의 전체 카테고리를 삭제 하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_modify_category_delete");
				$("form[name=frm]").attr("action" , "_product_mass.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 카테고리 갯수 체크
	 function selectCategoryCnt() {
		return $.ajax({
			url: "_product_mass.inc_category.pro.php",
			type: "POST",
			data: "_mode=cnt&_code=<?=$_tmpcode?>",
			async: false
		}).responseText;
	 }


	// 선택 상품에 선택 카테고리 추가
	 function selectCategoryAdd() {
		 if($('.js_ck').is(":checked")){
			var category_cnt = selectCategoryCnt(); // 갯수추출
			if(category_cnt > 0 )	{
				 if(confirm("정말로 선택 상품 카테고리 추가를 실행 하시겠습니까?")){
					$("form[name='frm']").children("input[name=_mode]").val("mass_modify_category_add");
					$("form[name='frm']").attr("action" , "_product_mass.pro.php");
					document.frm.submit();
				 }
			}
			else {
				alert('카테고리를 1개 이상 선택해 주시기 바랍니다.');
			}
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 상품에 선택 카테고리 삭제
	 function selectCategorySelDel() {
		 if($('.js_ck').is(":checked")){
			var category_cnt = selectCategoryCnt(); // 갯수추출
			if(category_cnt > 0 )	{
				 if(confirm("정말로 선택 상품 카테고리 삭제를 실행 하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_modify_category_seldel");
					$("form[name=frm]").attr("action" , "_product_mass.pro.php");
					document.frm.submit();
				 }
			}
			else {
				alert('카테고리를 1개 이상 선택해 주시기 바랍니다.');
			}
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 상품 복사 + 카테고리 추가
	 function selectCategoryCopy() {
		 if($('.js_ck').is(":checked")){
			var category_cnt = selectCategoryCnt(); // 갯수추출
			if(category_cnt > 0 )	{
				 if(confirm("적용을 위하여 수초에서 수십초가 소요됩니다.\n\n정말로 선택 상품 복사 + 카테고리 추가를 실행 하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_modify_category_copy");
					$("form[name=frm]").attr("action" , "_product_mass.pro.php");
					document.frm.submit();
				 }
			}
			else {
				alert('카테고리를 1개 이상 선택해 주시기 바랍니다.');
			}
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>