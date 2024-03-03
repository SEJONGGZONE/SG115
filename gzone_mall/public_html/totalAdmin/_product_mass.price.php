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

	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();


		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where 1 ";

		$search_detail_name = 'mass_price';
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
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_submode" value="mass_price">
	<input type="hidden" name="_select_category_cnt" value="0">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC;?>">


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
				<?php if( $AdminPath == 'totalAdmin' ){?>
					<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_commission_type">정산방식</a>
					<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_change_sPersent">수수료</a>
					<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_change_sPrice">공급가</a>
				<?php } ?>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_change_screenPrice">정상가</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_change_price">판매가</a>
			</div>
		</div>

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="90"/>
				<col width="*"/>

				<?php if( $AdminPath == 'totalAdmin' ){?>
				<col width="350"/>
				<?php } ?>

				<col width="270"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col" >No</th>
					<th scope="col" >노출여부</th>
					<th scope="col" >상품정보</th>
					<th scope="col" >가격정보</th>
					<?php if( $AdminPath == 'totalAdmin' ){?>
					<th scope="col" >정산방식/수수료</th>
					<?php } ?>
					<th scope="col" >개별수정</th>
				</tr>
			</thead>
			<tbody>

<?PHP

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$pname = addslashes(strip_tags($v['p_name']));

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		// KAY :: 2021-04-15 :: 일괄 가격 개별수정 버튼 변경 추가
		$_mod = "<a href='#none' onclick='return false;' class='c_btn h22 blue product_price_change' data-pcode='".$v['p_code']."'>개별수정</a>";

		if($v['p_type']=='ticket'){
			$plink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
		}else{
			$plink = '_product.form.php?_mode=modify&_code='.$v['p_code'].'';
		}


		$sub_disabled = $SubAdminMode === true && $AdminPath != 'totalAdmin' ?'disabled':'';


		echo "
			<tr>
				<td class='this_check'>
					<label class='design'><input type='checkbox' name='chk_pcode[".$v['p_code']."]' class='js_ck' value='Y' data-pcode='".$v['p_code']."'></label>
				</td>
				<td class='this_num'>" . $_num . "</td>
				<td class='this_state'>" . $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')] . "</td>
				<td>
					<div class='order_item_thumb type_simple'>
						<div class='thumb'><a href='".$plink."' target='_blank' title='".$pname."'><img src='".$_p_img."' alt='".$pname."' ></a></div>
						<div class='order_item'>
		";
						 if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){
							echo "<div class='entershop'>". showCompanyInfo($v['p_cpid'])."</div>";
						 }
		echo "
							<dl>
								<dt><div class='item_name'><a href='".$plink."' target='_blank'>". strip_tags($v['p_name']) . "</a></div></dt>
								<dt class='t_light'>" . $v['p_code'] ."</dt>
							</dl>
						</div>
						<div class='other_tag'>
							".$arr_adm_button[$v['p_type']]."
	";
								if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
								else{
									if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
									else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
									else{  echo '<span class="c_tag light t4">판매종료</span>'; }
								}
		echo "
						</div>
					</div>
				</td>
				<td>
					<div class='in_price type_input'>
						<ul class='in_thead'>
							".($AdminPath === 'totalAdmin' ? "<li>공급가(원)</li>":null)."
							<li>정상가(원)</li>
							<li>판매가(원)</li>
						</ul>
						<ul>
							".($AdminPath === 'totalAdmin' ? "
							<li><input type='text' name='_sPrice[".$v['p_code']."]' class='design number_style _sPrice' value='". $v['p_sPrice'] ."' style='width:100%' placeholder='공급가' ".$sub_disabled."></li>

							":null)."

							<li><input type='text' name='_screenPrice[".$v['p_code']."]' class='design number_style _screenPrice' value='". $v['p_screenPrice'] ."' style='width:100%' placeholder='정상가'></li>
							<li><input type='text' name='_price[".$v['p_code']."]' class='design number_style _price' value='". $v['p_price'] ."' style='width:100%' placeholder='판매가' ></li>
						</ul>
					</div>
				</td>


				".($AdminPath === 'totalAdmin' ? "
				<td>
					<div class='lineup-row type_end'>
						<label class='design'>
							<input type='radio' name='_commission_type[".$v['p_code']."]'  data-pcode='".$v['p_code']."' value='공급가' id='_commission_type_".$v['p_code']."_공급가' class='_commission_type' ". ($v['p_commission_type'] == "공급가" ? "checked" : "") ." ".$sub_disabled.">공급가
						</label>
						<label class='design'>
							<input type='radio' name='_commission_type[".$v['p_code']."]' data-pcode='".$v['p_code']."' value='수수료' id='_commission_type_".$v['p_code']."_수수료' class='_commission_type' ". ($v['p_commission_type'] == "수수료" ? "checked" : "") ." ".$sub_disabled.">수수료
						</label>
						<span class='divi'></span>
						<input type='text' name='_sPersent[".$v['p_code']."]' class='design _sPersent' value='". $v['p_sPersent'] ."' style='width:55px' ". ( $SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_commission_type'] == "수수료" ? "" : "disabled") .">
						<span class='fr_tx term'>%</span>
					</div>
				</td>

				":null)."
				<td  class='this_ctrl'>
					<div class='lineup-row type_center'>
						". (
							in_array($v['p_option_type_chk'] , array('1depth','2depth','3depth')) ?
								"
								<a href='#none' onclick=\"option_popup('". $v['p_code'] ."' , '". $v['p_option_type_chk'] ."')\" class='c_btn orange line' > 필수옵션</a>
								<a href='#none' onclick=\"addoption_popup('". $v['p_code'] ."')\" class='c_btn sky line' >추가옵션</a>
								" :
								""
						) ."
						". $_mod ."
					</div>
				</td>
			</tr>
		";
	}
?>
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

</form>









<?// KAY :: 2021-04-15 ::  정산방식 팝업 ?>
<div class="popup _commission_type_pop" style="display:none;">
	<div class="pop_title">정산방식 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="change_commission_type" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>정산방식</th>
						<td>
							<label class="design"><input type="radio" id="_commission_type_all_공급가" name="_commission_type[all]" value="공급가" >공급가</label>
							<label class="design"><input type="radio" id="_commission_type_all_수수료" name="_commission_type[all]" value="수수료" >수수료</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_commission_type ">일괄변경</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<?// KAY :: 2021-04-15 ::  공급가 팝업 ?>
<div class="popup _sPrice_pop" style="display:none;">
	<div class="pop_title">공급가 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="change_sPrice" class="form_wrap">
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
								<input type="text" name="common_price" style="width:100px;" class="design _sPrice_cal" placeholder="0">
								<?php echo _InputRadio( 'common_type' , array('price','per'),'price',' data-class="_sPrice_pop" ', array('원','%')); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<?php echo _InputRadio( 'common_ud' , array('no','up','down'),'down',' data-class="_sPrice_pop" ' , array('교체','인상','인하') ); ?>
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
							<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_sPrice_pop"' , array('선택안함(1원)','10원','100원','1,000원') ); ?>
							<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_sPrice "> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_sPrice')" class="c_btn blue line close"> 일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<?// KAY :: 2021-04-15 ::  수수료 팝업 ?>
<div class="popup _sPersent_pop" id="" style="display:none;">
	<div class="pop_title">수수료 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="change_sPersent" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>수수료</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="_sPersent[all]" style="width:70px;" class="design" placeholder="0">
								<span class='fr_tx term'>%</span>
							</div>
							<?php echo _DescStr ('수수료는 소수점 이하 2자리까지 입력가능하고 3자리가 넘어가면 자동으로 반올림됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_sPersent"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_sPersent')" class="c_btn blue line close"> 일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<?// KAY :: 2021-04-15 ::  기존가 팝업 ?>
<div class="popup _screenPrice_pop" id="" style="display:none;">
	<div class="pop_title">정상가 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

	<form name="change_screenPrice" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>정상가</th>
						<td>
							<div class="lineup-row">
								<input type="text" name="common_price" style="width:100px;" class="design _screenPrice_cal" placeholder="0">
								<?php echo _InputRadio( 'common_type' , array('price','per'),'price',' data-class="_screenPrice_pop" ', array('원','%')); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<?php echo _InputRadio( 'common_ud' , array('no','up','down'),'down',' data-class="_screenPrice_pop" ' , array('교체','인상','인하') ); ?>
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
							<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class=_screenPrice_pop' , array('선택안함(1원)','10원','100원','1,000원') ); ?>
							<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_screenPrice"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_screenPrice')" class="c_btn blue line close "> 일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<?// KAY :: 2021-04-15 ::  판매가 팝업 ?>
<div class="popup _price_pop" id="" style="display:none;">
	<div class="pop_title">판매가 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="change_price" class="form_wrap">
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
								<input type="text" name="common_price" style="width:100px;" class="design _price_cal" placeholder="0">
								<?php echo _InputRadio( 'common_type' , array('price','per'),'price',' data-class="_price_pop" ', array('원','%')); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<?php echo _InputRadio( 'common_ud' , array('no','up','down'),'down',' data-class="_price_pop" ' , array('교체','인상','인하') ); ?>
							</div>

							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _DescStr ('교체 : 계산없이 입력한 값으로 교체됩니다.'); ?>
							<?php echo _DescStr ('인상 : 새로 입력된 값을 더해서 계산됩니다. (기존값 + 새로 입력한 값)'); ?>
							<?php echo _DescStr ('인하 : 새로 입력된 값을 빼서 계산됩니다. (기존값 - 새로 입력한 값)'); ?>
						</td>
					</tr>

					<tr class="common_perdel">
						<th>절사 단위</span></th>
						<td>
							<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_price_pop"' , array('선택안함(1원)','10원','100원','1,000원') ); ?>
							<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_Price"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_price')" class="c_btn blue line close"> 일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>
</div>

<script>
	// - 옵션열기 ---
	function option_popup(pass_code , pass_mode) {
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}

	// - 추가옵션열기 ---
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}

	// -------------- 선택상품 일괄비우기 --------------
	// KAY :: 2021-04-20 :: class_val (공급가,수수료,기존가,판매가)에 대한 저장값
	function selectMassClear(class_val) {
		if(confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");
					$("input[name='"+class_val+"["+_pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}else{
			return false;
		}
	}


	//---------- KAY :: 2021-04-16 각 팝업 화면에 노출을 위한 공통 처리 ---------
	// 팝업띄우기 - 초기화
	function lightbox_me_reset(wrap_class){
		$("."+wrap_class+" input[name='common_price']").val(""); // 금액 reset
		$("."+wrap_class+" .common_perdel").hide(); // 절삭닫기
	}

	 // 절삭함수	_perdel_type : 절삭타입 (per_te , per_h , per_th)
	 function price_cut(_perdel_type , price){
		 var _screenPer_m = price;
		 switch(_perdel_type){
			case "per_te": _screenPer_m = Math.floor(price/10)*10; break;
			case "per_h": _screenPer_m = Math.floor(price/100)*100; break;
			case "per_th": _screenPer_m = Math.floor(price/1000)*1000; break;
		 }
		return _screenPer_m;
	 }

	// KAY :: 2021-04-20 :: 팝업 계산함수
	//cal_class : 입력값(텍스트), cal_class_val(입력받은값-저장된것),cla_type(인상,인하,할인적용,금액 data-class값)
	function lightbox_me_Calculation(cal_class,cal_class_val,cal_type){
		$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _common_price = $.trim($("."+cal_class).val()); // 지정

			var common_ud = $("[name='common_ud'][data-class='" + cal_type + "']:checked").val(); // 인상인하 타입선택
			var common_type = $("[name='common_type'][data-class='" + cal_type  + "']:checked").val(); // 할인적용금액,할인적용율(%) 타입선택

			// KAY :: 2021-04-13 :: 계산(절삭단위,인상인하)
			// 변수정의
			var common_perdel = $("input[name='common_perdel'][data-class='"	+	cal_type	+	"']").val(); // 절사단위 변수
			var _p_Per =parseFloat(_common_price)/100; // 퍼센트 입력시 퍼센트 값

			var _price_cal = $("input[name='"+cal_class_val+"["+_pcode+"]']");
			var _price_before = _price_cal.val().replace(/,/g, '');
			var _price_plus = parseInt(_price_before)+parseInt(_common_price); //이전 가격 + 입력 가격
			var _price_minus = parseInt(_price_before)-parseInt(_common_price); //이전 가격 - 입력 가격
			var _per_plus= parseFloat(_price_before)*(1+(_p_Per));// 원래가격 + 퍼센트 계산후 가격
			var _per_minus = parseFloat(_price_before)*(1-(_p_Per));// 원래가격 - 퍼센트 계산후 가격

			// 사전체크
			if(cal_class=='_price_cal' && _common_price<=0){alert ("값을 입력해주세요");return false;} // 판매가의 경우에만 0이상 입력되도록


			if(common_type == 'per' && _common_price>=100){	alert ("100보다 작은값을 입력해주세요.");return false;}
			if(common_ud =='down' && common_type == 'price' && _price_minus<=0 ){alert("0 이하 값으로 인해 인하할 수 없는 상품이 있습니다.");return false;}
			if(common_ud =='down' && common_type == 'per' && _per_minus<=0 ){alert("0 이하 값으로 인해 인하할 수 없는 상품이 있습니다.");return false;}

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
				if(common_type == 'per' ){	_price_cal.val(price_cut(common_perdel , _per_minus));	} // 절삭적용
			}
		});

		$("input[name=_mode]").val('mass_price');
		frm.submit();
	}


	// KAY :: 2021-04-20 :: common_type 선택 시 변경사항
	$("[name='common_type']").click(function(){
		var _type = $(this).val();
		var _class = $(this).data("class");
		var _type_ud = $("[name='common_ud'][data-class='" + _class + "']:checked").val();

		$("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",false);
		if(_type == "price" ){	$("."+_class+" .common_perdel").hide();$("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",false);}
		if(_type == "per" ){
			$("."+_class+" .common_perdel").show();
			$("[name='common_ud'][data-class='" + _class + "']#common_udno").attr("disabled",true);
			$("[name='common_ud'][data-class='" + _class + "']#common_udno").prop("checked", false);
		}
	});

	$("[name='common_ud']").click(function(){
		var _type_ud = $(this).val();
		var _class = $(this).data("class");
		var _type = $("[name='common_type'][data-class='" + _class + "']:checked").val();

		if(_type == "price" &&_type_ud=="no" ){$("."+_class+" .common_perdel").hide();}
	});



	// KAY :: 2021-04-02 :: 개별 공급가,수수로 선택시 수수료입력창 disabled
	$(document).on("click" , "._commission_type", function(){
		var _type = $(this).val();
		var _pcode = $(this).data("pcode");

		$("input[name='_sPersent["+_pcode+"]']").attr("disabled",false);
		if(_type=='공급가'){
			$("input[name='_sPersent["+_pcode+"]']").attr("disabled",true);
		}else{
			$("input[name='_sPersent["+_pcode+"]']").attr("disabled",false);
		}
	});



	//---------- KAY :: 2021-04-16 각 팝업 화면에 노출--------------------------------------
	// KAY :: 2021-04-15 :: 지정 정산방식 팝업 띄우기 + 일괄적용
	$('.mass_commission_type').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._commission_type_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_commission_type').on('click',function(){
		if( confirm("정산방식을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _commission_type = $("input[name='_commission_type[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 정산방식
				if( _commission_type != ''){	$("#_commission_type_" + _pcode +"_" + _commission_type ).prop('checked', true) ;	}// 정산방식 적용
			});
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}
		else{	return false;	}
	});



	// KAY :: 2021-04-15 :: 지정 공급가 팝업 띄우기 + 일괄적용
	$('.mass_change_sPrice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_sPrice_pop");
			$("._sPrice_pop").lightbox_me({centered: true, closeEsc: false, onClose: function(){} ,onLoad: function() {}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_sPrice').on('click',function(){
		if( confirm("공급가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_sPrice_cal","_sPrice","_sPrice_pop");
		}
		else{
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 지정 수수료 팝업 띄우기	+ 일괄적용
	$('.mass_change_sPersent').on('click',function(){
		$("._perdel_sPersent").hide();
		if($('.js_ck').is(":checked")){
			$("._sPersent_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_sPersent').on('click',function(){

		var _sPersent = $.trim($("input[name='_sPersent[all]']").val()); // 지정 수수료
		if(_sPersent>=100){	alert ("수수료는 100보다 작은값을 입력해주세요.");return false;}

		if( confirm("수수료를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				if( _sPersent != ''){	$("._sPersent[name='_sPersent[" + _pcode +"]']").val(_sPersent);	}// 수수료 적용
			});
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}
		else{
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 지정 기존가 팝업 띄우기	+ 일괄적용
	$('.mass_change_screenPrice').on('click',function(){
		$(".common_perdel").hide();
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_screenPrice_pop");
			$("._screenPrice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});


	$('.selectMass_screenPrice').on('click',function(){
		if( confirm("기존가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_screenPrice_cal","_screenPrice","_screenPrice_pop");
		}
		else { return false;}
	});


	// KAY :: 2021-04-15 :: 지정 판매가 팝업 띄우기	+ 일괄적용
	$('.mass_change_price').on('click',function(){
		$(".common_perdel").hide();
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_price_pop");
			$("._price_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_Price').on('click',function(){
		if( confirm("판매가를 일괄변경하시겠습니까?") ){ lightbox_me_Calculation("_price_cal","_price","_price_pop"); }
		else{ 	return false;}
	});


	// KAY :: 2021-04-15 :: 개별수정(기존가격 일괄관리 파란 변경버튼)
	// 정산방식, 공급가, 수수료,판매가,기존가 개별변경
	$('.product_price_change').on('click',function(){
		var pcode = $(this).data("pcode");// 상품코드 추출
		var _commission_type = $("input[name='_commission_type[" + pcode +"]']:checked").val(); //정산방식
		var _sPrice = $("input[name='_sPrice[" + pcode +"]']").val(); //공급가
		var _sPersent = $("input[name='_sPersent[" + pcode +"]']").val(); //수수료
		var _screenPrice = $("input[name='_screenPrice[" + pcode +"]']").val(); //기존가
		var _price = $("input[name='_price[" + pcode +"]']").val(); //판매가

		if(_sPersent >= 100){ alert("수수료는 100미만값을 입력해주시기 바랍니다.");return false;}

		$.ajax({
			data: {'_mode': 'price_direct_change' , 'pcode': pcode, '_commission_type': _commission_type,'_sPrice': _sPrice,'_sPersent': _sPersent,'_screenPrice': _screenPrice,'_price':_price},
			type: 'POST', cache: false, dataType: 'JSON',
			url: '_product_mass.pro.php',
			success: function(data) {
				if(data.res == 'success') {
					<?php if($SubAdminMode === true && $AdminPath != 'totalAdmin'){?>
						alert("변경이 완료되었습니다.");
					<?php }else{?>
						alert("변경이 완료되었습니다. \n수수료는 소수점 이하 3자리에서 자동으로 반올림되어 저장됩니다. ");
					<?php }?>
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
