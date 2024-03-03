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
	$s_query = " from smart_product as p where p_option_type_chk IN ('1depth','2depth','3depth') ";

	$search_detail_name = 'mass_option';
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


<div class="data_list">
	<form name="frm" method="post" action="_product_mass.pro.php" >
		<input type="hidden" name="_mode" value=''>
		<input type="hidden" name="_submode" value="mass_option">
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
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_poption_supplyprice">공급가</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_poptionprice">판매가</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_cnt">재고량</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_view">옵션 노출여부</a>
			</div>
		</div>

				<table class="table_list type_nocheck">
					<colgroup>
						<col width="70"/><col width="*"/><col width="*"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" >No</th>
							<th scope="col" >상품</th>
							<th scope="col" >옵션</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($res as $k=>$v){

								$_num = $TotalCount - $count - $k ;
								$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
								if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

								$pname = addslashes(strip_tags($v['p_name']));

								switch($v['p_option_type_chk']){
									case "1depth":
										$po_que = "
											select
												po.* , po.po_poptionname as app_poptionname
											from smart_product_option as po
											where
												po.po_pcode='".$v['p_code']."' and
												po.po_depth='1'
												order by po_sort asc , po_uid asc
										";
										break;
									case "2depth":
										$po_que = "
											select
												po2.* , CONCAT(po1.po_poptionname , ' &gt; ' , po2.po_poptionname) as app_poptionname
											from smart_product_option as po2
											INNER JOIN smart_product_option as po1 ON (po1.po_uid = po2.po_parent and po1.po_depth='1' )
											where
												po2.po_pcode='".$v['p_code']."' and
												po2.po_depth='2'
												order by po2.po_sort asc , po2.po_uid asc
										";
										break;
									case "3depth":
										$po_que = "
											select
												po3.* , CONCAT(po1.po_poptionname , ' &gt; ' , po2.po_poptionname , ' &gt; ' , po3.po_poptionname) as app_poptionname
											from smart_product_option as po3
											INNER JOIN smart_product_option as po1 ON (po1.po_uid = SUBSTRING_INDEX(po3.po_parent, ',', 1) and po1.po_depth='1')
											INNER JOIN smart_product_option as po2 ON (po2.po_uid = SUBSTRING_INDEX(po3.po_parent, ',', -1) and po2.po_depth='2')
											where
												po3.po_pcode='".$v['p_code']."' and
												po3.po_depth='3'
												order by po3.po_sort asc , po3.po_uid asc
										";
										break;
								}
								$po_res = _MQ_assoc($po_que);
						?>
						<tr>
							<td class="this_num"><?php echo $_num;?></td>
							<td>
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
							<td class="this_item">
								<div class="mass_option_list">
									<ul class="thead">
										<li class="li_check"><label class="design"><input type="checkbox" class="js_option_AllCK" value="Y" data-pcode="<?php echo $v['p_code'];?>"></label></li>
										<li class="li_name">옵션명</li>
										<li class="li_price">공급가(원)</li>
										<li class="li_price">판매가(원)</li>
										<li class="li_mount">재고(개)</li>
										<li class="li_ctrl"></li>
									</ul>
									
									<?php 
										if(count($po_res) > 0){
											foreach($po_res as $po_k=>$po_v) {
											$_mod = "<a href='#none' onclick='return false;' class='c_btn h22 blue option_change' data-pouid='".$po_v['po_uid']."'>수정</a>";
									?>
											<ul class="tbody">
												<li class="li_check">
													<label class="design">
														<input type="checkbox" name="chk_pcode[<?php echo $po_v['po_uid'];?>]" value="Y" class="js_ck js_ck_<?php echo $v['p_code'];?>" data-pcode="<?php echo $po_v['po_uid'];?>">
													</label>
												</li>
												<li class="li_name"><?php echo strip_tags($po_v['app_poptionname']);?></li>
												<li class="li_price"><input type="text" name="_poption_supplyprice[<?php echo $po_v['po_uid'];?>]" value="<?php echo $po_v['po_poption_supplyprice'];?>" class="design _poption_supplyprice number_style" style="width:90px;"></li>
												<li class="li_price"><input type="text" name="_poptionprice[<?php echo $po_v['po_uid'];?>]" value="<?php echo $po_v['po_poptionprice'];?>" class="design _poptionprice number_style" style="width:90px;"></li>
												<li class="li_mount"><input type="text" name="_cnt[<?php echo $po_v['po_uid'];?>]" value="<?php echo $po_v['po_cnt'];?>" class="design _cnt number_style" style="width:60px;"></li>
												<li class="li_ctrl">
													<div class="lineup-row type_end onoff">
														<label for="_view<?php echo $po_v['po_uid'];?>_Y" class="design">
															<input type="radio" id="_view_<?php echo $po_v['po_uid'];?>_Y" name="_view[<?php echo $po_v['po_uid'];?>]" value="Y" class="_view" <?php echo  ($po_v['po_view'] == "Y" ? "checked" : "");?>> 노출
														</label>
														<label for="_view<?php echo $po_v['po_uid'];?>_N" class="design">
															<input type="radio" id="_view_<?php echo $po_v['po_uid'];?>_N" name="_view[<?php echo $po_v['po_uid'];?>]" value="N" class="_view" <?php echo ($po_v['po_view'] == "N" ? "checked" : "");?>> 숨김
														</label>
													</div>
													<?php echo $_mod;?>
												</li>
											</ul>
									<?php } ?>
								<?php } ?>
									</div>
								</td>
							</tr>
						<?php } ?>
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



<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 공급가 팝업 -->
<div class="popup _poption_supplyprice_pop" style="display:none;">
	<div class="pop_title">공급가 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>공급가</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="common_price" value="" class="design _poption_supplyprice_cal" placeholder="0" style="width:100px;">
								<?php echo _InputRadio( 'common_type' , array('price','per'),'price',' data-class="_poption_supplyprice_pop" ', array('원','%')); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<?php echo _InputRadio( 'common_ud' , array('no','up','down'),'down',' data-class="_poption_supplyprice_pop" ' , array('금액반영','인상','인하') ); ?>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _DescStr ('교체 : 계산없이 입력한 값으로 교체됩니다.'); ?>
							<?php echo _DescStr ('인상 : 새로 입력된 값을 더해서 계산됩니다. (기존값 + 새로 입력한 값)'); ?>
							<?php echo _DescStr ('인하 : 새로 입력된 값을 빼서 계산됩니다. (기존값 - 새로 입력한 값)'); ?>
						</td>
					</tr>
					<tr class="common_perdel">
						<th>절사 단위</th>
						<td>
							<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_poption_supplyprice_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
							<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_poption_supplyprice"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_poption_supplyprice');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 판매가 팝업 -->
<div class="popup _poptionprice_pop" id="" style="display:none;">
	<div class="pop_title">판매가 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>판매가</th>
						<td>
							<div class="lineup-row">
								<input type='text' name="common_price" value='' class="design _poptionprice_cal" placeholder="0" style='width:100px;'>
								<?php echo _InputRadio( 'common_type' , array('price','per'),'price',' data-class="_poptionprice_pop" ', array('원','%')); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<?php echo _InputRadio( 'common_ud' , array('no','up','down'),'down',' data-class="_poptionprice_pop" ' , array('금액반영','인상','인하') ); ?>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _DescStr ('교체 : 계산없이 입력한 값으로 교체됩니다.'); ?>
							<?php echo _DescStr ('인상 : 새로 입력된 값을 더해서 계산됩니다. (기존값 + 새로 입력한 값)'); ?>
							<?php echo _DescStr ('인하 : 새로 입력된 값을 빼서 계산됩니다. (기존값 - 새로 입력한 값)'); ?>
						</td>
					</tr>
					<tr class="common_perdel">
						<th>절사 단위</th>
						<td>
							<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_poptionprice_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
							<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_poptionprice"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_poptionprice');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 재고변경 팝업 -->
<div class="popup _cnt_pop" id="" style="display:none;">
	<div class="pop_title">재고량 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form class="form_wrap">
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
								<input type='text' name="_cnt[all]" value='' class='design number_style' style='width:70px;' placeholder="0">
								<span class="fr_tx term">개</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_cnt"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_cnt');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>


<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 노출여부 팝업 -->
<div class="popup _view_pop" id="" style="display:none;">
	<div class="pop_title">노출여부 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>노출여부</th>
						<td>
							<?php echo _InputRadio( "_view[all]" , array('Y','N'), "미지정" , "" , array('노출','숨김') , '')?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_view"> 일괄변경</a></li>
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

	 // 선택상품 일괄수정
	 function selectMassModify() {
		 if($('.js_ck').is(":checked")){
				$("form[name=frm]").attr("action" , "_product_mass.pro.php");
				$("input[name=_mode]").val('mass_option');
				document.frm.submit();
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

	// -------------- 선택상품 일괄비우기 --------------
	//	KAY :: 2021-04-20 ::option_val 옵션(공급가,판매가,재고,노출여부)에 대한 저장값
	function selectMassClear(option_val) {
		if(confirm("일괄비우기 하시겠습니까?")){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var pcode = $(this).data("pcode");
					$("."+option_val+"[name='"+option_val+"["+pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_option');
			frm.submit();
		}else{
			return false;
		}
	}

	//	KAY :: 2021-04-20 :: 절삭함수	_perdel_type : 절삭타입 (per_te , per_h , per_th)
	function price_cut(_perdel_type , price){
		var _screenPer_m = price;
		switch(_perdel_type){
			case "per_te": _screenPer_m = Math.floor(price/10)*10; break;
			case "per_h": _screenPer_m = Math.floor(price/100)*100; break;
			case "per_th": _screenPer_m = Math.floor(price/1000)*1000; break;
		}
		return _screenPer_m;
	}

	// KAY :: 2021-04-20 ::		팝업띄우기 - 초기화
	function lightbox_me_reset(wrap_class){
		$("."+wrap_class+" input[name='common_price']").val(""); // 금액 reset
		$("."+wrap_class+" .common_perdel").hide(); // 절삭닫기
	}

	// KAY :: 2021-04-20 :: common_type 선택 시 변경사항
	$("[name='common_type']").change(function(){
		var _type = $(this).val();
		var _class = $(this).data("class");
		var _type_ud = $("[name='common_ud'][data-class='" + _class + "']:checked").val();

		$("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",false);
		if(_type == "price" ){	$("."+_class+" .common_perdel").hide(); $("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",false);}
		if(_type == "per" ){
			$("."+_class+" .common_perdel").show();
			$("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",true);
			$("[name='common_ud'][data-class='" + _class + "']#common_udno").prop("checked", false);
		}
	});

	$("[name='common_ud']").change(function(){
		var _type_ud = $(this).val();
		var _class = $(this).data("class");
		var _type = $("[name='common_type'][data-class='" + _class + "']:checked").val();
		if(_type == "price" &&_type_ud=="no" ){$("."+_class+" .common_perdel").hide();}
	});

	// KAY :: 2021-04-20 :: 절삭,인상,인하 계산
	function lightbox_me_Calculation(cal_class,cal_class_val,cal_type){
		var _common_price = $("."+cal_class).val();
		var common_ud = $("[name='common_ud'][data-class='" + cal_type + "']:checked").val(); // 인상인하 타입선택
		var common_type = $("[name='common_type'][data-class='" + cal_type + "']:checked").val(); // 할인적용금액,할인적용율(%) 타입선택
		var exit = false;
		$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			// KAY :: 2021-04-13 :: 판매가 계산(절삭단위,인상인하)
			// 변수정의
			var common_perdel = $("input[name='common_perdel'][data-class='" + cal_type + "']:checked").val(); // 절사단위 변수
			var _p_Per =parseFloat(_common_price)/100; //판매가 퍼센트 입력시 퍼센트 값
			var _price_cal = $("."+cal_class_val+"[name='"+cal_class_val+"[" + _pcode +"]']");// 이전가격 변수
			var _price_before = _price_cal.val().replace(/,/g, '');
			var _price_plus = parseInt(_price_before)+parseInt(_common_price); //이전 가격 + 입력 가격
			var _price_minus = parseInt(_price_before)-parseInt(_common_price); //이전 가격 - 입력 가격
			var _per_plus= parseFloat(_price_before)*(1+(_p_Per));// 원래가격 + 퍼센트 계산후 가격
			var _per_minus = parseFloat(_price_before)*(1-(_p_Per));// 원래가격 - 퍼센트 계산후 가격

			// 사전체크
			if(_common_price<=0){alert ("값을 입력해주세요"); return false;}
			if(common_type == 'per' && _common_price>=100){	alert ("100보다 작은값을 입력해주세요.");return false;}
			if(common_ud =='down' && common_type == 'price' && _price_minus < 0 ){ exit = true; return exit;  }
			if(common_ud =='down' && common_type == 'per' && _per_minus < 0 ){exit = true; return exit; }

			// 금액반영
			if(common_ud =='no'&&common_type == 'price' ){	_price_cal.val(_common_price);	 }

			// 인상
			if(common_ud =='up'){

				if(common_type == 'price' ){	_price_cal.val(_price_plus);	}
				if(common_type == 'per' ){	_price_cal.val(price_cut(common_perdel , _per_plus));	} // 절삭적용
			}

			// 인하
			if(common_ud =='down'){
				if(common_type == 'price' ){	_price_cal.val(_price_minus);	}
				if(common_type == 'per' ){_price_cal.val(price_cut(common_perdel , _per_minus));	}//절삭적용
			}
		});
		if (exit == true ){alert("인하할 수 없는 상품이 있으며, 그 상품을 제외하고 인하적용 되었습니다.");}
	}




	// KAY :: 2021-04-20 :: 상품옵션관리 공급가 변경 팝업창 띄우기 + 일괄지정
	$('.mass_poption_supplyprice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_poption_supplyprice_pop");
			$("._poption_supplyprice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_poption_supplyprice').on('click',function(){
		if( confirm("옵션의 공급가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_poption_supplyprice_cal","_poption_supplyprice","_poption_supplyprice_pop");
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-20 :: 상품옵션관리 판매가 변경 팝업창 띄우기 + 일괄지정
	$('.mass_poptionprice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_poptionprice_pop");
			$("._poptionprice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_poptionprice').on('click',function(){
		if( confirm("옵션의 판매가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_poptionprice_cal","_poptionprice","_poptionprice_pop");
			$("input[name=_mode]").val('mass_option');
			frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-20 :: 상품옵션관리 재고 변경 팝업창 띄우기 + 일괄지정
	$('.mass_cnt').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._cnt_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_cnt').on('click',function(){
		if( confirm("옵션의 재고를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _cnt = $.trim($("input[name='_cnt[all]']").val()); // 지정 재고
				if( _cnt != ''){	$("._cnt[name='_cnt[" + _pcode +"]']").val(_cnt);	}// 재고 적용
			});
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-20 :: 상품옵션관리 노출여부 변경 팝업창 띄우기 + 일괄지정
	$('.mass_view').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._view_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_view').on('click',function(){
		if( confirm("옵션의 노출여부를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출여부
				if( _view != ''){	$("#_view_" + _pcode +"_" + _view ).prop('checked', true);	}	// 노출여부 적용
			});
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-20 :: 개별수정(상품옵션 일괄관리 파란 변경버튼)
	$(document).ready(function(){
		$('.option_change').on('click',function(){
			var _po_uid= $(this).data('pouid');// 상품코드 추출
			var _poption_supplyprice = $("._poption_supplyprice[name='_poption_supplyprice[" + _po_uid +"]']").val(); //상품옵션 공급가
			var _poptionprice = $("._poptionprice[name='_poptionprice[" + _po_uid +"]']").val(); //상품옵션 판매가
			var _cnt = $("._cnt[name='_cnt[" + _po_uid +"]']").val();	//상품옵션 재고
			var _view = $("._view[name='_view["+_po_uid +"]']").filter(function() {if (this.checked) return this;}).val(); // 상품옵션 노출
			// encodeURIComponent
			console.log( $(this).data());
			$.ajax({
				data: {'_mode': 'option_direct_change' , '_po_uid': _po_uid, '_poption_supplyprice': _poption_supplyprice, '_poptionprice': _poptionprice, '_cnt': _cnt, '_view': _view},
				type: 'POST', cache: false, dataType: 'JSON',
				url: '_product_mass.pro.php',
				success: function(data) {
					console.log(data);
					if(data.res == 'success') { alert("변경하였습니다.");}
					else {alert("변경에 실패하였습니다.");}
				}
			}).fail(function(e){console.log(e.responseText);});
		});
	});
</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>