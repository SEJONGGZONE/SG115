<?php
	include_once("wrap.header.php");

	$is_valide = _MQ_assoc(" SHOW TABLES LIKE 'smart_delivery_addprice'; ");
	if(count($is_valide)<1){
		echo '
			<style>
				.new_deny_guide {background:#2793a0; color:#fff; margin:20px; margin-bottom:0px; font-size:14px; padding:15px 20px; letter-spacing:-0.5px;}
				.new_deny_guide strong {text-decoration:underline; font-weight:400;}
			</style>

			<div class="new_deny_guide">	※ 추가배송비를 사용하기 위해 <strong><a href="_config.delivery_addprice.pro.php?_mode=create_table" style="color:#fff;">“추가배송비 DB를 생성”</a></strong>해 주시기 바랍니다.  </div>
		';

		return;
	}

	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $k => $v) { if( is_array($v) ){foreach($v as $sk => $sv) { $_PVS .= "&" . $k . "[".$sk."]=$sv"; }}else {$_PVS .= "&$k=$v"; }}
	$_PVSC = enc('e' , $_PVS);

	//  smart_delivery_addprice
	// 검색 체크
	$s_query = " where 1 ";
	if($pass_addr) $s_query .= " and da_addr like '%". $pass_addr ."%' ";

	// 데이터 조회
	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'da_uid';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_delivery_addprice $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_delivery_addprice {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>

<div class="data_search">

	<div class="group_title">
		<strong>Search</strong>
		<div class="btn_box">
			<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
			<?php if ($mode == 'search') { ?>
				<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
			<?php } ?>
			<a href="#none" class="c_btn h46 red js_open_add_box">도서산간 등록</a>
			<a href="#none" class="c_btn h46 red line js_open_excel_box only_pc_view">일괄업로드</a>
		</div>
	</div>

	<!-- 도서산간지역 추가폼 열림 -->
	<div class="open_excel js_add_box" style="display:none;">
		<input type="hidden" name="" id="default_addprice" value="<?php echo rm_str($addprice); ?>" />
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>주소등록</th>
					<td>
						<div class="lineup-row">
							<a href="#none" onclick="new_post_view();return false;"  class="c_btn h27 black">주소검색</a>
							<a href="#none" onclick="init_address();return false;"  class="c_btn h27 js_reset_btn black line" style="display:none;">검색초기화</a>
						</div>
					</td>
				</tr>
			</tbody>
			<tbody id="js_address_data" class="hidden_tbody">
			</tbody>
			<tbody>
				<tr>
					<th>참고사항</th>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('<em>주소검색</em>버튼을 눌러 검색된 주소를 선택하면 선택한 주소가 포함된 선택가능한 주소의 목록이표시됩니다.'); ?>
							<?php echo _DescStr('검색된 주소의 목록중 추가할 지역을 <em>선택추가</em>버튼을 눌러 추가해주세요.'); ?>
							<?php echo _DescStr('<em>수정추가</em>로 도서산간 지역을 추가시 추가배송비가 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.'); ?>
							<?php echo _DescStr('추가배송비는 반드시 숫자로만 공백없이 입력하고 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.'); ?>
							<?php echo _DescStr('"제주특별자치도 제주시"와 "제주특별자치도 제주시 가령골길" 두 주소가 모두 등록되어 있다면 "제주특별자치도 제주시 가령골길"이 먼저 적용됩니다. '); ?>
							<?php echo _DescStr('지번주소와 도로명주소가 모두 등록되어있다면 도로명주소가 먼저 적용되며, 되도록 도로명으로 입력해주세요.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="mobile_tip">엑셀업로드는 PC에서 가능합니다.</div>
	</div>

	<!-- 엑셀일괄등록 열림 -->
	<div class="open_excel js_excel_box only_pc_view" style="display:none;">
		<form action="_config.delivery_addprice.pro.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_mode" value="ins_excel" />
			<table class="table_form">
				<colgroup>
					<col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>일괄업로드</th>
						<td>
							<div class="lineup-row type_multi">
								<div class="input_file" style="width:300px">
									<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
									<div class="fileDiv">
										<input type="button" class="buttonImg" value="파일찾기">
										<input type="file" name="excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
									</div>
								</div>
								<a href="<?php echo IMG_DIR_NORMAL; ?>/delivery_addprice_sample.xls" class="c_btn h27">샘플파일 다운</a>
								<span class="c_btn h27 blue"><input type="submit" name="" value="업로드 저장" /></span><!-- <a href="" class="c_btn h27 black">업로드 저장</a> -->
							</div>
						</td>
					</tr>
					<tr>
						<th>참고사항</th>
						<td>
							<div class="tip_box">
								<?php echo _DescStr('도서산간 지역은 적용될 행정구역단위까지 입력해주세요. 배송주소와 지역명이 일치해야 추가배송비가 적용됩니다.'); ?>
								<?php echo _DescStr('이미 등록된 도서산간 지역은 추가배송비만 업데이트 됩니다. '); ?>
								<?php echo _DescStr('추가배송비는 반드시 숫자로만 공백없이 입력하셔야 합니다.'); ?>
								<?php echo _DescStr('추가배송비가 0원이면 도서산간지역은 추가되지만 추가배송비가 적용되지는 않습니다.'); ?>
								<?php echo _DescStr('"제주특별자치도 제주시"와 "제주특별자치도 제주시 가령골길" 두 주소가 모두 등록되어 있다면 "제주특별자치도 제주시 가령골길"이 먼저 적용됩니다. '); ?>
								<?php echo _DescStr('지번주소와 도로명주소가 모두 등록되어있다면 도로명주소가 먼저 적용됩니다. '); ?>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="tip_box">
								<?php echo _DescStr('업로드 용량에 따라 다소 시간이 걸릴 수 있으며, <em>최대 '.ini_get('upload_max_filesize').'</em>까지 업로드 가능 합니다.'); ?>
								<?php echo _DescStr('<em>엑셀97~2003 버전</em> 파일만 업로드가 가능합니다. 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하세요.'); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>


	<form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" autocomplete="off" class="search_form">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>주소검색</th>
					<td><input type="text" name="pass_addr" class="design" style="" value="<?php echo $pass_addr; ?>" placeholder="주소"></td>
				</tr>
			</tbody>
		</table>
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
				<?php if ($mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</form><!-- end search_form -->

</div><!-- end data_search -->



<div class="data_list">
	<form name="frm" method="post" action="" onsubmit="return false;">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?=$_PVSC?>">
		<input type="hidden" name="_search_que" value="<?=enc('e',$s_query)?>">

		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
				<a href="#none" onclick="$('.js_thisform').toggle(); return false;" class="c_btn h22 blue">선택 일괄변경</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="selectExcel(); return false;" class="c_btn icon icon_excel only_pc_view">선택 엑셀다운</a>
				<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel only_pc_view" >검색 엑셀다운 (<?php echo number_format(sizeof($res)); ?>)</a>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_uid', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'da_uid' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_uid', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'da_uid' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_addr', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'da_addr' && $so == 'asc'?' selected':null); ?>>주소순 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_addr', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'da_addr' && $so == 'desc'?' selected':null); ?>>주소순 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'da_price' && $so == 'asc'?' selected':null); ?>>배송비 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'da_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'da_price' && $so == 'desc'?' selected':null); ?>>배송비 ▼</option>
				</select>
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
					<col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>추가배송비</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="modify_addprice" id="js_modify_addprice" value="" class="design number_style t_right" placeholder="0" style="width:90px">
								<span class="fr_tx t_blue">원</span>
								<a href="#none" onclick="selectModify(); return false;" class="c_btn h22 blue">일괄 변경하기</a>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<div class="mobile_tip">엑셀 다운로드는 PC에서 가능합니다.</div>

		<table class="table_list">
			<colgroup>
				<col width="40"><col width="70"><col width="*"><col width="130"><col width="110"><col width="110">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">주소</th>
					<th scope="col">추가 배송비</th>
					<th scope="col">등록일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(sizeof($res)>0){
					foreach($res as $k=>$v) {
						$_mod = '<a href="#none" onclick="window.open(\'_config.delivery_addprice.form.php?_uid='. $v['da_uid'] .'\',\'add_delivery_price\',\'width=800,height=335,scrollbars=no\');" class="c_btn h22 gray">수정</a>';
						$_del = '<a href="#none" onclick="del(\'_config.delivery_addprice.pro.php?_mode=delete&_uid='. $v['da_uid'] .'&_PVSC='. $_PVSC .'\');" class="c_btn h22 dark line">삭제</a>';
						$_num = $TotalCount - $count - $k ;
				?>
						<tr>
							<td class="this_check"><label class="design"><input type="checkbox" name="chk_uid[]" class="js_ck" value="<?php echo $v['da_uid']; ?>"></label></td>
							<td class="this_num"><?php echo $_num; ?></td>
							<td class="t_left"><?php echo $v['da_addr']; ?></td>
							<td class="this_state t_right t_green"><?php echo number_format($v['da_price'],0); ?>원</td>
							<td class="this_date"><?php echo printDateInfo($v['da_rdate']); ?></td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<?php echo $_mod; ?>
									<?php echo $_del; ?>
								</div>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>

		<?php if(sizeof($res)<1){ ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</form>
</div>


<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg=",'Y'); ?>
</div>


<!-- 주소검색 다음 API 호출 -->

<?php include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_addprice.php'); // 다음주소찾기 ?>

<script>
	// 엑셀 일괄업로드 폼 열기/닫기
	$(document).delegate('.js_open_excel_box', 'click', function(){
		$('.js_add_box').hide();
		$('.js_excel_box').toggle();
	});
	// 도서산간지역 추가 폼 열기/닫기
	$(document).delegate('.js_open_add_box', 'click', function(){
		$('.js_excel_box').hide();
		$('.js_add_box').toggle();
	});

	 // 선택삭제
	 function selectDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("정말 삭제하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				$("form[name=frm]").attr("action" , "_product.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

	// 상세정보영역 초기화
	function init_address(){
		$('#js_address_data').html($('#js_address_data').data('text'));
		$('.js_reset_btn').hide();
	}
	init_address();

	// 주소추가
	function insert_addprice(_idx){
		var key = $('#key_' + _idx).val();
		var addr = $('#addr_' + _idx).val();
		var addprice = $('#_addprice').val().replace(/[^0-9]/g,'')*1;

		document.location.href = "_config.delivery_addprice.pro.php?_mode=add&key=" + encodeURI(key) + "&addr=" + encodeURI(addr) + "&addprice=" + addprice;
	}

	// 추가배송비 입력창 숫자만 입력
	$(document).delegate("#_addprice", "focusin", function(){
		var _val = $(this).val().replace(/[^0-9]/g,'')*1;
		if(_val==0) _val = '';
		$(this).val(_val);
	}).delegate("#_addprice", "focusout", function(){
		var _val = $(this).val().replace(/[^0-9]/g,'')*1+'';
		$(this).val(_val.comma());
	});


	// - 전체선택 / 해제
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.js_ck').attr('checked',true);
			}
			else {
				$('.js_ck').attr('checked',false);
			}
		});
	});


	// 선택순위수정
	 function selectDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("선택된 "+$('.js_ck:checked').length+"개의 도서산간지역을 삭제하시겠습니까?")){
				$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
				$("input[name=_mode]").val('mass_delete');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }


	// 선택 추가배송비 일괄변경
	 function selectModify() {
		 if($('.js_ck').is(":checked")){
			var _price = $('#js_modify_addprice').val().replace(/[^0-9]/g,'')*1;
			$('#js_modify_addprice').val(_price);

			_price = _price + '';
			 if(confirm("선택된 "+$('.js_ck:checked').length+"개의 도서산간지역의 추가배송비를 "+_price.comma()+"원으로 일괄 변경하시겠습니까?")){
				$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
				$("input[name=_mode]").val('mass_modify');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }


	// 선택엑셀다운로드
	 function selectExcel() {
		 if($('.js_ck').is(":checked")){
			$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
			$("input[name=_mode]").val('select_excel');
			document.frm.submit();
			$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
			$("input[name=_mode]").val('');
		 }
		 else {
			 alert('1개 이상 선택하시기 바랍니다.');
		 }
	 }


	// 검색엑셀다운로드
	 function searchExcel() {
		$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
		$("input[name=_mode]").val('search_excel');
		document.frm.submit();
		$("form[name=frm]").attr("action" , "_config.delivery_addprice.pro.php");
		$("input[name=_mode]").val('');
	 }

</script>


<?php include_once("wrap.footer.php"); ?>