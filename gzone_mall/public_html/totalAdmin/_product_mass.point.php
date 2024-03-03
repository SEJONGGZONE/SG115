<?PHP
	include_once("wrap.header.php");

	// KAY :: 2021-04-02 :: 상품쿠폰 배열화
	//		p_coupon : 상품의 쿠폰 DB 항목
	if(function_exists('product_ex_coupon') !== true){
		function product_ex_coupon($p_coupon) {

			$arr = array();
			$ex_coupon = explode("|" , $p_coupon);

			if(sizeof($ex_coupon) > 2) {// 2개 초과 신규 데이터
				$arr['title'] = $ex_coupon[0];// 상품쿠폰명
				$arr['type'] = $ex_coupon[1];// 상품쿠폰 타입
				$arr['price'] = $ex_coupon[2];// 상품쿠폰 할인액
				$arr['per'] = $ex_coupon[3];// 상품쿠폰 할인율
				$arr['max'] = $ex_coupon[4];// 상품쿠폰 최대 할인액 - 제외함.
			}
			else { // 2개 - 이전 데이터
				$arr['title'] = $ex_coupon[0];// 상품쿠폰명
				$arr['price'] = $ex_coupon[1];// 상품쿠폰 할인액
			}
			return $arr;
		}
	}

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

	// 상품 일괄 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_product as p where 1 ";

	$search_detail_name = 'mass_point';
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
		<input type="hidden" name="_submode" value="mass_point">
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
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_point_per">구매 적립율</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_coupon_title">상품 쿠폰명</a>
				<a href="#none" onclick="return false;" class="c_btn h22 blue line mass_coupon_price">상품쿠폰 할인방법</a>
			</div>
		</div>

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="70"/><col width="90"/>
				<col width="*"/>
				<col width="100"/><col width="250"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">노출여부</th>
					<th scope="col">상품정보</th>
					<th scope="col">적립율(%)</th>
					<th scope="col">상품쿠폰 정보</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>


<?PHP

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		//KAY :: 2021-04-20 // 상품쿠폰 개별 변경 추가
		$_mod = "<a href='#none' onclick='return false;' class='c_btn h22 blue product_change' data-pcode='".$v['p_code']."'>개별수정</a>";

		// KAY :: 2021-04-09 :: 상품쿠폰액 텍스트값 변경
		$ex_coupon = product_ex_coupon($v['p_coupon']);

		$ex_coupon['per']= number_format(floor($ex_coupon['per']*10)/10,1); // 퍼센트 첫째짜리까지

		$pname = addslashes(strip_tags($v['p_name']));

		if($v['p_type']=='ticket'){
			$plink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
		}else{
			$plink = '_product.form.php?_mode=modify&_code='.$v['p_code'].'';
		}

		echo "
			<tr>
				<td class='this_check'>
					<label class='design'><input type=checkbox name='chk_pcode[".$v['p_code']."]' value='Y' class='js_ck' data-pcode='".$v['p_code']."'></label>
				</td>
				<td class='this_num'>" . $_num . "</td>
				<td class='this_state'>" . $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')] . "</td>
				<td class='this_item'>
					<div class='order_item_thumb type_simple'>
						<div class='thumb'><a href='".$plink."' target='_blank' title='".$pname."'><img src='".$_p_img."' alt='".$pname."' ></a></div>
						<div class='order_item'>
		";
						 if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){
							echo "<div class='entershop'>". showCompanyInfo($v['p_cpid'])."</div>";
						 }
		echo"
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
					<div class='lineup-row'>
						<span class='hidden_tx'>적립율</span>
						<input type='text' name=_point_per[".$v['p_code']."] value='". $v['p_point_per'] ."' class='design _point_per t_right' style='width:50px;'>
						<span class='fr_tx term'>%</span>
					</div>
				</td>
				<td>
					<input type='text' name=_coupon_title[".$v['p_code']."] value='". $ex_coupon['title'] ."' class='design _coupon_title' placeholder='상품 쿠폰명' style='width:100%;'>
					<div class='dash_line'><!-- 점선라인 --></div>

					<div class='lineup-row type_side'>
						<div>
							<input type='text' name= _coupon_price[".$v['p_code']."]  value='". number_format($ex_coupon['price']) ."' 	class='design _coupon_price t_right' style='width:80px; display:". ($ex_coupon['type'] == 'price' ? '' : "none;") . "' placeholder='할인금액'>
							<input type='text' name= _coupon_per[".$v['p_code']."]  value='". $ex_coupon['per'] ."' class='design _coupon_per t_right' style='width:80px; display: ". ($ex_coupon['type'] == 'per' ? '' : "none;") . "' placeholder='할인율'>
						</div>
						<div class='lineup-row'>
							" . _InputRadio( "_coupon_type[".$v['p_code']."]" , array('price','per'),$ex_coupon['type'],"class='_coupon_type' data-pcode='".$v['p_code']."' ", array('원','%')) . "
						</div>
					</div>
					<div class='lineup-row type_side js_coupon_per_wrap' data-pcode=".$v['p_code']." style='display: ". ($ex_coupon['type'] == 'per' ? '' : "none;") . "'>
						<div class='dash_line'><!-- 점선라인 --></div>
						<span class='fr_tx'>최대 할인금액</span>
						<input type='text' name= _coupon_max[".$v['p_code']."]  value='". $ex_coupon['max'] ."' class='design _coupon_max number_style' style='width:80px; ' placeholder='0'>
					</div>
				</td>
				<td class='this_ctrl'>
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

</div>

</form>


<!-- KAY :: 2021-04-19 :: 적립율 팝업 -->
<div class="popup _point_per_pop" id="" style="display:none;">
	<div class="pop_title">구매 적립율 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>적립율</th>
						<td>
							<div class="lineup-row">
								<input type='text' name="_point_per[all]" value='' class='design t_right' style='width:70px;' placeholder="0">
								<span class="fr_tx term">%</span>
							</div>
							<?php echo _DescStr ('적립율은 소수점 이하 1자리까지 입력가능하고 2자리가 넘어가면 자동으로 반올림됩니다.'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_point_per"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_point_per');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<!-- KAY :: 2021-04-19 :: 상품쿠폰명 팝업 -->
<div class="popup _coupon_title_pop" id="" style="display:none;">
	<div class="pop_title">상품 쿠폰명 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>상품 쿠폰명</th>
						<td>
							<input type='text' name="_coupon_title[all]" value='' placeholder="상품 쿠폰명" class='design' >
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_coupon_title"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_coupon_title');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
</div>

<!-- KAY :: 2021-04-19 :: 상품쿠폰할인 팝업 -->
<div class="popup _coupon_price_pop" id="" style="display:none;">
	<div class="pop_title">상품쿠폰 할인 일괄변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
	<form name="" class="form_wrap">
		<div class="pop_conts data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>상품쿠폰 할인</th>
						<td>
							<div class="lineup-row">
								<input type='text' name="_coupon_price[all]" value='' class='design _coupon_price_all number_style' style='width:100px;' placeholder="0">
								<input type='text' name="_coupon_per[all]" value='' class='design _coupon_price_all t_right' style='width:100px; display:none;' placeholder="0">
								<?php echo _InputRadio( '_coupon_type[all]' , array('price','per') , 'price','', array('원','%') ); ?>
							</div>
							<?php echo _DescStr ('할인율 선택 후 최대할인액은 미정 또는 0원 설정 시 할인액의 제한이 없습니다.'); ?>
							<?php echo _DescStr ('쿠폰 할인율은 소수점 1 자리까지 허용합니다.'); ?>
							<?php echo _DescStr ('쿠폰 할인율 반영 후 실제 할인 적용 시 소수점은 반올림이 아니라 버림처리합니다.'); ?>
						</td>
					</tr>
					<tr class="coupon_price_max" style="display:none;">
						<th>최대 할인액</th>
						<td>
							<div class="lineup-row">
								<input type='text' name="_coupon_max[all]" value='' class='design number_style' style='width:100px;' placeholder="0">
								<span class="fr_tx term">원</span>
							</div>
							<script>
								// KAY :: 2021-04-02 :: 상품쿠폰 선택(전체)
								$(document).on("click" , "[name='_coupon_type[all]']", function(){
									var _type_all = $(this).val();
									// 전체숨김
									$("._coupon_price_all").hide(); $(".coupon_price_max").hide();
									// 개별열기
									if(_type_all == "price"){ $("input[name='_coupon_price[all]']").show();}
									if(_type_all == "per"){$("input[name='_coupon_per[all]']").show();$("input[name='_coupon_max[all]']").show();	$(".coupon_price_max").show(); }
								});
							</script>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="c_btnbox type_full">
			<ul>
				<li><a href="#none" onclick="return false;" class="c_btn h34 blue close selectMass_coupon_price"> 일괄변경</a></li>
				<li><a href="#none" onclick="selectMassClear('_coupon_price_all');" class="c_btn h34 blue line close">일괄비우기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
			</ul>
		</div>
	</form>
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

	//KAY :: 2021-04-19 :: -------------- 선택상품 일괄비우기 --------------
	function selectMassClear(class_point) {
		if(confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");

					if(class_point=='_point_per'){		$("input[name='_point_per["+_pcode+"]']").val(0);	}
					if(class_point=='_coupon_title'){	$("input[name='_coupon_title["+_pcode+"]']").val("");	}
					if(class_point=='_coupon_price_all'){
						$("input[name='_coupon_price["+_pcode+"]']").val("");
						$("input[name='_coupon_per["+_pcode+"]']").val("");
						$("input[name='_coupon_type["+_pcode+"]']").val("price");
						$("input[name='_coupon_max["+_pcode+"]']").val("");
					}
				});
				$("input[name=_mode]").val('mass_point');
				frm.submit();
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
	 }

	// KAY :: 2021-04-09 :: 개별수정(상품쿠폰 일괄관리 파란 변경버튼)
	$(document).ready(function(){
		$('.product_change').on('click',function(){

			var pcode = $(this).data("pcode");// 상품코드 추출
			var _point_per = $("input[name='_point_per[" + pcode +"]']").val();	//적립율
			var _coupon_title = $("input[name='_coupon_title[" + pcode +"]']").val();	//상품쿠폰명
			var _coupon_type = $("[name='_coupon_type[" + pcode +"]']:checked").val();	//상품쿠폰 할인원,할인액 타입
			var _coupon_price = $("input[name='_coupon_price[" + pcode +"]']").val();		// 상품쿠폰할인금액
			var _coupon_per = $("input[name='_coupon_per[" + pcode +"]']").val();		//상품쿠폰 할인율(퍼센트)
			var _coupon_max = $("input[name='_coupon_max[" + pcode +"]']").val();	//상품쿠폰 할인율일경우 최댓값

			$.ajax({
				data: {'_mode': 'point_direct_change' , 'pcode': pcode, '_point_per': _point_per,'_coupon_title': _coupon_title,'_coupon_type': _coupon_type,'_coupon_price': _coupon_price,'_coupon_per': _coupon_per,'_coupon_max': _coupon_max},
				type: 'POST', cache: false, dataType: 'JSON',
				url: '_product_mass.pro.php',
				success: function(data) {
					console.log(data);
					if(data.res == 'success') {
						alert("변경이 완료되었습니다.\n적립율은 소수점 1자리까지 저장됩니다.  \n상품쿠폰 할인율은 소수점 1자리까지 저장됩니다. ");
						location.reload();
					}
					else {alert("변경에 실패하였습니다.");}
				}
			}).fail(function(e){console.log(e.responseText);});
		});
	});

	// KAY :: 2021-04-19 :: 적립율 변경 팝업창 띄우기 + 일괄지정
	$('.mass_point_per').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._point_per_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_point_per').on('click',function(){
		if( confirm("적립율을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _point_per = $.trim($("input[name='_point_per[all]']").val()); // 지정 적립율

			if(_point_per >= 100){ alert("100미만값을 입력해주세요"); return false;}
			if( _point_per != ''){	$("input[name='_point_per[" + _pcode +"]']").val(_point_per);	}	// 적립율 적용
		});
			$("input[name=_mode]").val('mass_point');
			frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-19 :: 상품쿠폰명 변경 팝업창 띄우기 + 일괄지정
	$('.mass_coupon_title').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._coupon_title_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_coupon_title').on('click',function(){
		if( confirm("상품쿠폰명을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _coupon_title = $.trim($("input[name='_coupon_title[all]']").val()); // 지정 상품쿠폰명

			if( _coupon_title != ''){	$("input[name='_coupon_title[" + _pcode +"]']").val(_coupon_title);	}	// 상품쿠폰명 적용
		});
			$("input[name=_mode]").val('mass_point');
			frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-02 :: 개별 상품쿠폰 할인액,할인율 선택
	$(document).on("click" , "._coupon_type", function(){
		var _type = $(this).val();
		var _pcode = $(this).data("pcode");

		// 전체숨김
		$("input[name='_coupon_price["+_pcode+"]']").hide();
		$("input[name='_coupon_per["+_pcode+"]']").hide();
		$("input[name='_coupon_max["+_pcode+"]']").hide();
		$(".js_coupon_per_wrap[data-pcode="+_pcode+"]").hide();
		// 개별열기
		if(_type == "price"){
			$("input[name='_coupon_price["+_pcode+"]']").show();
		}
		if(_type == "per"){
			$("input[name='_coupon_per["+_pcode+"]']").show();
			$("input[name='_coupon_max["+_pcode+"]']").show();
			$(".js_coupon_per_wrap[data-pcode="+_pcode+"]").show();
		}
	});



	// KAY :: 2021-04-19 :: 상품쿠폰할인 변경 팝업창 띄우기 + 일괄지정
	$('.mass_coupon_price').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._coupon_price_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {
				$("[name='common_type']").change(function(){
					var _type = $(this).val();
					var _pcode = $(this).data("pcode");

					// 전체숨김
					$("input[name='_coupon_price["+_pcode+"]']").hide();
					$("input[name='_coupon_per["+_pcode+"]']").hide();
					$("input[name='_coupon_max["+_pcode+"]']").hide();
					// 개별열기
					if(_type == "price"){ $("input[name='_coupon_price["+_pcode+"]']").show();}
					if(_type == "per"){$("input[name='_coupon_per["+_pcode+"]']").show();$("input[name='_coupon_max["+_pcode+"]']").show();}
				});
			},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_coupon_price').on('click',function(){
		if( confirm("상품쿠폰할인을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _coupon_price = $.trim($("input[name='_coupon_price[all]']").val()); // 지정 상품쿠폰액
			var _coupon_type = $("[name='_coupon_type[all]']:checked").val();
			var _coupon_per = $.trim($("input[name='_coupon_per[all]']").val());
			var _coupon_max = $.trim($("input[name='_coupon_max[all]']").val());

			if(_coupon_type=='per'){
				$("._coupon_per").show(); $("._coupon_max").show(); $("._coupon_price").hide();
				if(_coupon_per >= 100){ alert("100미만값을 입력해주세요");return false;}
				if(_coupon_per <= 0){ alert("0 이상값을 입력해주세요"); return false;}
			}
			if(_coupon_type=='price'){
				$("._coupon_per").hide();$("._coupon_max").hide(); $("._coupon_price").show();
				if(_coupon_price <= 0){ alert("0 이상값을 입력해주세요");  return false;}
			}

			if( _coupon_type != ''){	$("[name='_coupon_type[" + _pcode +"]'][value="+_coupon_type+"]").attr('checked', true);	}// 상품쿠폰타입 적용
			if( _coupon_price !=''){	$("input[name='_coupon_price[" + _pcode +"]']").val(_coupon_price);	}// 상품쿠폰할인금액 적용
			if(_coupon_per != ''){$("input[name='_coupon_per[" + _pcode +"]']").val(_coupon_per);}// 상품쿠폰할인율(%) 적용
			if(_coupon_max != ''){$("input[name='_coupon_max[" + _pcode +"]']").val(_coupon_max);}// 상품쿠폰할인율(%) 적용
		});

		$("input[name=_mode]").val('mass_point');
		frm.submit();
		}
		else {		return false;	}
	});


</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>