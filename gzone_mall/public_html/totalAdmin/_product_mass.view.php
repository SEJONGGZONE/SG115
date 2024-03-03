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

	// 상품 일괄 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_product as p where 1 ";


	$search_detail_name = 'mass_view';
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

	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();
?>


<div class="data_list">

	<form name="frm" method="post" action="_product_mass.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_submode" value="mass_view">
		<input type="hidden" name="_select_category_cnt" value="0">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC?>">


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
			<div class="lineup-row mini_form">
				<?php echo _DescStr('상품을 선택하고 아래 일괄 변경항목을 클릭하면 한번에 수정할 수 있습니다.','blue'); ?>
				<?php if($AdminPath == 'totalAdmin'){?>
					<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_view">노출여부</a>
				<?php } ?>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_stock">재고량</a>
			</div>
		</div>

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="90"/><col width="*"/>
				<col width="270"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">노출여부</th>
					<th scope="col">상품코드/상품명</th>
					<th scope="col">노출/재고</th>
					<th scope="col">개별수정</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($res as $k=>$v){

					$_num = $TotalCount - $count - $k ;
					$pname = addslashes(strip_tags($v['p_name']));

					$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
					if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

					$_mod = "<a href='#none' onclick='return false;' class='c_btn h22 blue product_view_change' data-pcode='".$v['p_code']."'>개별수정</a>";

					if($v['p_type']=='ticket'){
						$plink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
					}else{
						$plink = '_product.form.php?_mode=modify&_code='.$v['p_code'].'';
					}
					
					$sub_disabled = $SubAdminMode === true && $AdminPath != 'totalAdmin' ?'disabled':'';
			?>
				<tr>
					<td class='this_check'>
						<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['p_code'];?>]" value="Y" class="js_ck" data-pcode="<?php echo $v['p_code'];?>"></label>
					</td>
					<td class='this_num'><?php echo $_num;?></td>
					<td class='this_state'><?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')];?></td>
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
					</td>
					<td>
						<div class='lineup-row type_end'>
							<label for="_view_<?php echo $v['p_code'];?>_Y" class="design">
								<input type="radio" id="_view_<?php echo $v['p_code'];?>_Y" name="_view[<?php echo $v['p_code'];?>]" value="Y" class="_view" <?php echo  $v['p_view'] == "Y" ? "checked" : "";?> <?php echo $sub_disabled;?>> 노출
							</label>
							<label for="_view_<?php echo $v['p_code'];?>_N" class="design">
								<input type="radio" id="_view_<?php echo $v['p_code'];?>_N" name="_view[<?php echo $v['p_code'];?>]" value="N" class="_view" <?php echo  $v['p_view'] == "N" ? "checked" : "";?> <?php echo $sub_disabled;?>> 숨김
							</label>
							<div class="divi"></div>
							<input type="text" name="_stock[<?php echo $v['p_code'];?>]" value="<?php echo $v['p_stock'];?>" class="design _stock number_style" style="width:70px;">
							<span class='fr_tx term'>개</span>
						</div>
					</td>
					<td class="this_ctrl">
						<div class='lineup-row type_center'>
							<?php if(in_array($v['p_option_type_chk'] , array('1depth','2depth','3depth')) ){?>
								<a href="#none" onclick="option_popup('<?php echo $v['p_code'];?>' , '<?php echo $v['p_option_type_chk'];?>')" class="c_btn orange line" >필수옵션</a>
								<a href="#none" onclick="addoption_popup('<?php echo $v['p_code'];?>')" class="c_btn sky line" >추가옵션</a>
							<?php }?>
							<?php echo $_mod;?>
						</div>
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

<!-- KAY :: 2021-04-19 :: 노출 팝업 -->
<div class="popup _view_pop" id="" style="display:none;">
	<div class="pop_title">노출여부 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>노출여부</th>
						<td>
							<?php echo _InputRadio( "_view[all]" , array('Y','N'),'','', array('노출','숨김'))?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_view "> 일괄변경</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close">닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<!-- KAY :: 2021-04-19 :: 재고 팝업 -->
<div class="popup _stock_pop" id="" style="display:none;">
	<div class="pop_title">재고 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>재고량</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="_stock[all]" value="" class="design number_style" style="width:100px;" placeholder="0">
								<span class="fr_tx term">개</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_stock"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear();" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<SCRIPT>
	// - 옵션열기 ---
	function option_popup(pass_code , pass_mode) {
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}

	// - 추가옵션열기 ---
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}


	// KAY :: 2021-04-19 :: 노출여부 변경 팝업창 띄우기 + 일괄지정
	$('.mass_view').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._view_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_view').on('click',function(){
		if( confirm("노출여부를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출

			if( _view != ''){	$("#_view_" + _pcode +"_" + _view ).prop('checked', true);	}
		});

		$("input[name=_mode]").val('mass_view');
		frm.submit();
		}
		else {	return false;	}
	});


	// KAY :: 2021-04-19 :: 재고변경 팝업창 띄우기 + 일괄지정
	$('.mass_stock').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._stock_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_stock').on('click',function(){
		if( confirm("재고를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _stock = $.trim($("input[name='_stock[all]']").val()); // 지정 재고

				if( _stock != ''){	$("._stock[name='_stock[" + _pcode +"]']").val(_stock);	}
			});

			$("input[name=_mode]").val('mass_view');
			frm.submit();
			}
			else{
				return false;
			}
	});

	//KAY :: 2021-04-19 :: 선택상품 일괄비우기  ----------
	function selectMassClear() {
		if( confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");
					$("input[name='_stock["+_pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_view');
			frm.submit();
		}else{
			return false;
		}
	}

	// KAY :: 2021-04-15 :: 개별수정( 개별변경 파란 변경버튼)
	$('.product_view_change').on('click',function(){
		var pcode = $(this).data("pcode");// 상품코드 추출
		var _view = $("input[name='_view["+pcode+"]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출
		var _stock = $("input[name='_stock[" + pcode +"]']").val(); // 재고

		$.ajax({
			data: {'_mode': 'view_direct_change' , 'pcode': pcode, '_view': _view,'_stock': _stock},
			type: 'POST', cache: false, dataType: 'JSON',
			url: '_product_mass.pro.php',
			success: function(data) {
				if(data.res == 'success') {
					alert("변경이 완료되었습니다.");
					location.reload();
				}
				else {alert("변경에 실패하였습니다.");}
			}
		}).fail(function(e){console.log(e.responseText);});
	});

</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>

