<?php
	if($_REQUEST['_mode'] == 'modify') {
		$app_current_name = '티켓상품 수정';
		$app_current_link = '_product_ticket.list.php';
	}
	else{
		$app_current_name = '티켓상품 등록';
		$app_current_link = '_product_ticket.list.php';
	}

	// 상품타입을 고정한다.
	$app_product_type = 'ticket';

	include_once('wrap.header.php');

	$point_per_ext = 0; // 적립률에 따른 예상 적립금

	// - 수정 ---
	if( $_mode == "modify" ) {
		$que = " select * from smart_product where p_code='${_code}'  ";
		$row = _MQ($que);

		if( $row['p_code'] == ''){ error_loc_msg("_product_ticket.list.php","삭제된 상품입니다."); }

		// KAY :: 2024-02-20 :: 입점업체 상품만 수정가능하도록
		if($AdminPath=='subAdmin'){
			if($row['p_cpid']!=$com_id){
				error_loc_msg("_product.list.php","수정 권한이 없는 상품입니다."); 
			}
		}

		$_str = "수정";
		$app_cpname = $row['p_cpid'] . ":" . $row['cp_name'];

		// - 텍스트 정보 추출 ---
		$_text_info_extraction = _text_info_extraction( "smart_product" , $row['p_code'] );
		$_text_info_extraction = is_array($_text_info_extraction) ? $_text_info_extraction : array();
		if($row) $row = array_merge($row , $_text_info_extraction);

		// 티켓상품이 아닐경우
		if( $row['p_type'] != 'ticket'){ error_loc("_product.form.php?".$_SERVER['QUERY_STRING']); }

		// 포인트 % 가 있을 경우 처리
		if( $row['p_point_per'] > 0){ $point_per_ext = floor($row['p_price']*($row['p_point_per']/100)); }

		if( rm_str($row['p_dateoption_stime']) < 1){ $row['p_dateoption_stime'] = '09:00'; }
		if( rm_str($row['p_dateoption_etime']) < 1){ $row['p_dateoption_etime'] = '23:30'; }

	}
	// - 수정 ---

	// - 등록 ---
	else {
		$_mode = "add";
		$_str = "등록";
		$app_cpname = "";
		$row['c_parent'] = $pass_parent01;
		$_code = shop_productcode_create();// 예 : A1234-B1234-C1234

		// 등록상품이 가장위에 노출되도록
		$row['p_sort_group'] = 100;
		$row['p_sort_idx'] = 0.5;
		$row['p_idx'] = 0.5;

		// 기본 숨김처리
		$row['p_view'] = 'N';		

		if( rm_str($row['p_dateoption_stime']) < 1){ $row['p_dateoption_stime'] = '09:00'; }
		if( rm_str($row['p_dateoption_etime']) < 1){ $row['p_dateoption_etime'] = '23:30'; }		
	}
	// - 등록 ---


	// 아이콘 정보 배열로 추출
	$product_icon = get_product_icon_info("product_name_small_icon");

	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();

	// 스킨정보 추출
	$SkinInfo = SkinInfo();

	// 판매기간에 따른 처리
	if(!$row['p_sale_sdateh']) $row['p_sale_sdateh'] = '00';
	if(!$row['p_sale_sdatem']) $row['p_sale_sdatem'] = '00';
	if(!$row['p_sale_edateh']) $row['p_sale_edateh'] = "";
	if(!$row['p_sale_edatem']) $row['p_sale_edatem'] = "";

	if( $SubAdminMode !== true){
		$tmp_cpid = array_keys($arr_customer);
		$row['p_cpid'] = $tmp_cpid[0];
	}
	else{
		// 입점업체 보안
		if( $AdminPath == 'subAdmin'){ $row['p_cpid'] = $com_id; }
	}

?>


<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_product_ticket.pro.php" >
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="_code" value="<?php echo $_code; ?>">
<input type="hidden" id="_time_sale_bak" value="<?php echo $row['p_time_sale']; ?>">
<?php  if( $AdminPath == 'subAdmin' || $SubAdminMode !== true){ ?>
	<input type=hidden name="_cpid" value="<?php echo $row['p_cpid']; ?>">
<?php } ?>



	<?php 
		// 상품내용 파일공통화 처리 -- 스크립트는 공통화 하지 마세요!
		// $app_product_type = 'delivery'; // 상단에서 선언
		include_once dirname(__FILE__)."/_product.inc_form.php";

		echo _submitBTN('_product_ticket.list.php', '', ($_mode == 'modify' ? '<li><a href="#none" class="c_btn h46 blue line" id="product_copy">복사하기</a></li>' : null)); 
	?>

</form>



<script>

	// 판매기간 설정에 따른 이벤트
	$(function(){
		js_sale_type();
	});

	$(document).on('change','input[name="_sale_type"]',function(){
		js_sale_type();
	});
	function js_sale_type(){
		var $_sale_typeT_date_box = $('#_sale_typeT_date_box');
		var _sale_type = $('input[name="_sale_type"]:checked').val();
		if( _sale_type == 'T'){ // 기간판매 일 경우
			$_sale_typeT_date_box.show();
		}else{
			$_sale_typeT_date_box.hide();
		}

		js_time_sale_type();
	}

	function js_time_sale_type(){
		var _time_sale = $('input[name="_time_sale"]').is(':checked');
		var _sale_type = $('input[name="_sale_type"]:checked').val();
		
		$('input[name="_time_sale"]').prop('disabled',false);
		if( _sale_type == 'T'){
			$('input[name="_time_sale"]').prop('checked',false).prop('disabled',true);
			_time_sale = false;
		}else{
			
			var _time_sale_bak = $('#_time_sale_bak').val();
			if( _time_sale_bak == 'Y'){
				_time_sale = true;
				$('input[name="_time_sale"]').prop('checked',true).prop('disabled',false);
			}			

			<?php if($AdminPath == 'subAdmin'){ ?>
				$('input[name="_time_sale"]').prop('disabled',true);	
			<?php }?>
		}
		if( _time_sale == true){
			$('#_time_saleY_date_box').show();
			
		}else{
			$('#_time_saleY_date_box').hide();
		}
	}


	// 타임세일 설정에 따른 이벤트
	$(document).on('click','input[name="_time_sale"]',function(){
		var _time_sale = $('input[name="_time_sale"]').is(':checked');
		if( _time_sale == true){
			$('#_time_saleY_date_box').show();
		}else{
			$('#_time_saleY_date_box').hide();
		}
	});
// 일 피커


	if(!window.datepicker_ymd || window.datepicker_ymd === undefined) window.datepicker_ymd = 'yyyy-mm-dd';
	$(function() {
	
		var ex_dateoption_ex_date = new Array();
		var selectedDates = new Array();
		var startDate = new Date('<?php echo date('Y-m-d') ?>');
		var _dateoption_ex_date = $('[name=_dateoption_ex_date]').val();		
		if( _dateoption_ex_date != ''){ ex_dateoption_ex_date = _dateoption_ex_date.split(','); }
		if( ex_dateoption_ex_date.length > 0){
			$.each(ex_dateoption_ex_date, function(i,v){
				selectedDates.push(new Date(v));
			});
			startDate = new Date(ex_dateoption_ex_date[ex_dateoption_ex_date.length-1]);
		}
	
	    $('[name=_dateoption_ex_date]').datepicker({
			timepicker: false,
			multipleDates: true,
			selectedDates: selectedDates,
			autoClose: true,
			language: 'ko',
			startDate: startDate,
			dateFormat: window.datepicker_ymd,	
	    });
	});
    $(function(){ /*$('.timepicker').timepicker({timeFormat:"H:i"}); */js_dateoption_use();  })
	$(document).on('change','input[name="_dateoption_use"]',function(){
		js_dateoption_use();
	});
	// 달력옵션 사용여부에 따른 처리
	function js_dateoption_use()
	{
		var chk = $('input[name="_dateoption_use"]').prop('checked');
		var _option_type_chk = $('input[name="_option_type_chk"]:checked').val();
		$('.js_dateoption_useY').hide();
		$('.init_product_option_type_dateoption').show();
		$('.js_expire_type_wrap').show();

		if( chk === true &&  _option_type_chk == 'nooption'){
			$('input[name="_dateoption_use"]').prop('checked',false);
			alert('달력옵션의 경우 1~3차 옵션일 경우에만 사용 가능합니다.');
			return false;
		}


		if( chk == true){
			$('.js_dateoption_useY').show();
			$('.init_product_option_type_dateoption').hide();

			$('.js_expire_type_wrap').hide();

			// 달력옵션 사용 시
			$('input[name="p_option1_type"][value="normal"]').trigger('click');
			if( _option_type_chk == '2depth' || _option_type_chk == '3depth'){
				$('input[name="p_option2_type"][value="normal"]').trigger('click');
			}

			if( _option_type_chk == '3depth'){
				$('input[name="p_option3_type"][value="normal"]').trigger('click');
			}

		}

		func_dateoption_date_type();
	}

	// 달력옵션의 선택기간지정
	function func_dateoption_date_type(){

		var _dateoption_use = $('input[name="_dateoption_use"]').prop('checked');
		var _dateoption_date_type = $('[name="_dateoption_date_type"]:checked').val();
		$('.js_dateoption_date_type').hide();

		if( _dateoption_use !== true){ return false; }

		if( _dateoption_date_type != ''){
			$('.js_dateoption_date_type.if_'+_dateoption_date_type).show();
		}
	}
	$(document).ready(function(e){ func_dateoption_date_type();});
	$(document).on('change','[name="_dateoption_date_type"]',function(){func_dateoption_date_type();});

	// 달력옵션에서 구매 불가날짜를 클릭했을 시 
	$(document).on('click','.js_dateoption_ex_date_reset',function(){
		$('[name=_dateoption_ex_date]').val('');
	});

	// 티켓 유효기간 타입선택
	function func_expire_type(){
		var _expire_type = $('[name="_expire_type"]:checked').val();
		$('.js_expire_type').hide();
		if( _expire_type != ''){
			$('.js_expire_type.if_'+_expire_type).show();
		}
	}
	$(document).ready(function(e){ func_expire_type();});
	$(document).on('change','[name="_expire_type"]',function(){func_expire_type();});

</script>

<script language="javascript">

	// 상품복사
	//$('#product_copy').on('click',function(e){
	$(document).on('click', '#product_copy', function(e){
		e.preventDefault();
		var c = confirm('상품을 복사하시겠습니까?');
		if(c){ location.href='_product_ticket.copy.php?pcode=<?=$_code?>&_PVSC=<?php echo $_PVSC; ?>'; }
	});


	// 정산 형태 선택
	var comm_type_check = function() {

		if($('input[name=_commission_type]:checked').val() == '공급가') {
			$('#comSaleTypeTr1').prop('disabled', false);
			$('#comSaleTypeTr2').prop('disabled', true);
		} else {
			$('#comSaleTypeTr1').prop('disabled', true);
			$('#comSaleTypeTr2').prop('disabled', false);
		}


		<?php if( $AdminPath != 'totalAdmin'){?>
			$('[name="_commission_type"]').prop('disabled',true);
			$('#comSaleTypeTr1, #comSaleTypeTr2').prop('disabled',true);
		<?php }?>		

	}
	$(document).on('click', 'input[name=_commission_type]', comm_type_check);
	$(document).ready(comm_type_check);


	// 배송정보 설정
	var delivery_setting = function() {
		if($('._shoppingPay_use:checked').val() == 'Y') {
			$('#_shoppingPay_use_Y').removeAttr('disabled');
		} else {
			$('#_shoppingPay_use_Y').attr('disabled','disabled');
		}
		 // ----- JJC : 상품별 배송비 : 2018-08-16 -----
		if($('._shoppingPay_use:checked').val() == 'P') {
			$("input[name='_shoppingPayPdPrice']").removeAttr('disabled');
			$("input[name='_shoppingPayPfPrice']").removeAttr('disabled');
			$(".shoppingPayP").show();
		} else {
			$("input[name='_shoppingPayPdPrice']").attr('disabled','disabled');
			$("input[name='_shoppingPayPfPrice']").attr('disabled','disabled');
			$(".shoppingPayP").hide();
		}
		// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	}
	$(document).on('click', '._shoppingPay_use', delivery_setting);
	$(document).ready(delivery_setting);


	// 쇼핑몰/입점업체 배송비 확인하기
	function entershop_view() {
		var cpid = $('select[name=_cpid]').val();
		if(!cpid) {
			alert('입점업체를 먼저 선택하세요');
		} else {
			<?php
				if($SubAdminMode)	{
					echo "window.open('_entershop.form.php?_mode=modify&menu_idx=16&_id='+cpid);";
				}
				else {
					echo "window.open('_config.delivery.form.php?menu_idx=5');";
				}
			?>
		}
	}

	// 텝메뉴
	$(document).on('click', '.tab_menu', function() {
		$parent = $(this).closest('.data_form');
		var idx = $(this).data('idx');
		// 탭변경
		$parent.find('.tab_menu').closest('li').removeClass('hit');
		$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$parent.find('.tab_conts').hide();
		$parent.find('.tab_conts[data-idx='+ idx +']').show();

		// 부모창이 display:none; 일때 높이 오류 수정
		var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
		if(trigger_cont_editor){
			$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
				var id = $(this).attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
				}
			});
			$(this).data('trigger','N');
		}
	});


	// 옵션설정에 따른 노출
	function onoff_option() {
		// 옵션사용하지 않을 경우 옵션유형 모두 닫기
		if($('._option_type_chk:checked').val() == 'nooption') {
			$(".option_type").hide();
		}
		else {
			$(".option_type").show(); // 옵션유형 div 열기
			$(".init_depth_type").hide(); // 옵션유형 항목 일단 모두 닫기
			if($('._option_type_chk:checked').val() == '1depth') {
				$(".init_depth1_type").show(); // 1차만 열기
			}
			else if($('._option_type_chk:checked').val() == '2depth') {
				$(".init_depth1_type").show(); $(".init_depth2_type").show(); // 1차,2차 열기
			}
			else if($('._option_type_chk:checked').val() == '3depth') {
				$(".init_depth_type").show(); // 모두 열기
			}
		}
	}
	$(document).ready(onoff_option);
	$(document).on('click', '._option_type_chk', onoff_option);


	// 이미지 자동등록/직접등록
	function onoff() {
		if($('.img_auto_resize_use:checked').val() == 'auto') {
			$('.auto_area').show();
			$('.direct_area').hide();
		} else {
			$('.auto_area').hide();
			$('.direct_area').show();
		}
	}
	$(document).ready(onoff);
	$(document).on('click', '.img_auto_resize_use', onoff);


	// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
	function switch_coupon(){
		var cp_type = $("[name='_coupon_type']:checked").val();
		var cp_per = $("input[name='_coupon_per']").val();
		var cp_price = $("input[name='_coupon_price']").val();

		$(".cls_coupon_type").hide();
		if(cp_type == "price"){ $(".cls_coupon_type.price").show();}
		if(cp_type == "per"){ $(".cls_coupon_type.per").show(); }
	}
	$(document).ready(switch_coupon);
	$(document).on("change" , "[name='_coupon_type']", switch_coupon );



	// 상세이미지 등록폼 추가 버튼
	$(document).on('click', '.js_addimg_btn', function(){
		if($('.js_img_idx').length>=5){
			alert('상세 이미지는 최대 5개까지만 등록가능합니다.');
			return false;
		}

		var html = '';
			html += '<dl>';
			html += '	<dt><font class="js_img_idx">0번</font></dt>';
			html += '	<dd>';
			html += '		<div class="lineup-row">';
			html += '			<div class="input_file hyperLinkDiv" style="display:none;width:250px">';
			html += '				<input type="text" name="" class=" hyperLink design" value="" style="width:100%;" placeholder="http(s)://를 포함하여 입력해주세요." disabled>';
			html += '			</div>';
			html += '			<?php echo str_replace(array("\n","\r"), "", addslashes(_PhotoForm( "../upfiles/product", "", "", "style=\"width:250px\""))); ?>';
			html += '			<a href="#none" class="c_btn h27 js_delimg_btn">삭제</a>';
			html += '		</div>';

			html += '			<label class="design"><input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="">외부 이미지</label>';
			html += '		<?php echo addslashes(_DescStr("권장사이즈 : {$SkinInfo['product']['detail_image_width']} x {$SkinInfo['product']['detail_image_height']} (pixel)")); ?>';
			html += '	</dd>';
			html += '</dl>';

		$(this).closest('.in_option_list').append(html);

		rename_img();

	});
	// 상세이미지 등록폼 삭제 버튼
	$(document).on('click', '.js_delimg_btn', function(){
		$(this).closest('dl').remove();

		rename_img();

	});
	//
	function rename_img(){
		var _img_idx = 0;
		$('.js_img_idx').each(function(){
			_img_idx++;
			// 항목명 변경
			$(this).text(_img_idx+'번');
			// input[name]변경 -- realFile
			$(this).closest('dl').find('input[type=file].realFile').attr('name','_img_b'+_img_idx);
			// input[name]변경 -- oldFile
			$(this).closest('dl').find('input[type=hidden].oldFile').attr('name','_img_b'+_img_idx+'_OLD');
			// input[name]변경 -- js_del
			$(this).closest('dl').find('input[type=hidden].js_del').attr('name','_img_b'+_img_idx+'_DEL');
			// value변경 -- js_use_hyperlink
			$(this).closest('dl').find('input[type=checkbox].js_use_hyperlink').val('_img_b' + _img_idx);
			// input[name]변경 -- hyperLink
			$(this).closest('dl').find('input[type=text].hyperLink').attr('name','_img_b'+_img_idx);
		});
	}



	// 옵션창 열기
	function option_popup(pass_code) {
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_option.form.php?pass_mode='+pass_mode+'&pass_code=' + pass_code ,'option','width=1120,height=638,scrollbars=yes');
	}

	// -- 자주쓰는 옵션 열기
	function common_option_popup(pass_code)
	{
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_common_option.pop.php?pass_mode='+pass_mode+'&pass_code=' + pass_code ,'option','width=1120,height=638,scrollbars=yes');
	}

	// 추가옵션창 열기
	function addoption_popup(code) {
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_addoption.popup.php?pass_code=' + code,'addoption','width=1120,height=638,scrollbars=yes');
	}

	// 정보제공고시창 열기
	function reqinfo_popup() {
		window.open('_product_ticket_reqinfo.popup.php?pass_code=<?php echo $_code; ?>','reqinfo','width=1350,height=700,scrollbars=yes');
	}

	// 정보제공고시 기본항목 설정창 열기
	function reqinfo_default_setting_popup() {
		window.open('_product_ticket_reqinfo.popup.php?pass_code=_DEFAULT_T_SETTING_','default_setting','width=1350,height=700,scrollbars=yes,top=50,');
	}


	// 외부 이미지 사용 체크 이벤트
	$(document).on('click', '.js_use_hyperlink', function(){
		$parent = $(this).closest('dd');
		var trigger = $(this).is(':checked');
		if(trigger){
			$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
			$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
		}else{
			$parent.find('.input_file:not(.hyperLinkDiv)').show().find('input').removeAttr('disabled');
			$parent.find('.input_file.hyperLinkDiv').hide().find('input').attr({'disabled':'disabled'});
		}
	});


	// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 - Max 검증 시 콤마제거
	jQuery.validator.addMethod("max", function( value, element, param ) {
		value = value.replace(/,/,'')
		return this.optional( element ) || value <= param;
	}, "Please enter a value less than or equal to {0}.");

	// 상품가격설정에 따른 할인율 노출
	function change_print_persent() {
		var _screenPrice =  $('input[name="_screenPrice"]').val().replace(/,/g,'')*1;
		var _price =  $('input[name="_price"]').val().replace(/,/g,'')*1;

		if( (_screenPrice >_price) && (_screenPrice!='' && _price!='' ) ){
			persent = Math.round((_screenPrice-_price)*100/_screenPrice);
		}else{
			persent = 0;
		}

		$('.js_percent_view').val(persent);
	}
	$(document).ready(change_print_persent);
	$(document).on('keyup', 'input[name="_screenPrice"]', change_print_persent);
	$(document).on('keyup', 'input[name="_price"]', change_print_persent);


	// 상품가격설정에 적립률 체크
	function change_sale_print_persent() {
		var _point_per =  $('input[name="_point_per"]').val().replace(/,/g,'')*1;
		var _price =  $('input[name="_price"]').val().replace(/,/g,'')*1;

		var point_per_ext = 0;
		if( _price > 0 && _point_per > 0){

			if( _point_per > 100){
				alert("적립률은 100 보다 클 수 없습니다.");
				$('input[name="_point_per"]').val(100);
				_point_per = 100;
			}
			point_per_ext = Math.round( _price * (_point_per/100) );
		}

		$('.js_point_per_ext').val(point_per_ext.toString().comma());
	}
	$(document).ready(change_sale_print_persent);
	$(document).on('keyup', 'input[name="_point_per"]', change_sale_print_persent);
	$(document).on('keyup', 'input[name="_price"]', change_sale_print_persent);




	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_code: { required: true }
						,_cpid: { required: true }
						,_name: { required: true }
						,_type: { required: true }
						,_price: { required: true , min: { param: 1, depends: function(element) { return ($('input[name=_price]').val().replace(/,/g,'')*1 == 0 ? 1 : 0); } } }
						,_content: { required: true }
						//,_content_m: { required: function(){ return ($('input[name=_use_content]').is(':checked') ? false : true);} }
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
						,_coupon_title: { required: function(){ return ($('[name=_coupon_type]:checked').val() != '' ? true : false);} }
						,_coupon_price: {
							required: function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? true : false); }
							,min:function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? 1 : false); }
							,max:function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? ($('input[name="_price"]').val().replace(/,/g,'')*1 - 1) : false); }
						}
						,_coupon_per: {
							required: function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? true : false); }
							,min:function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? 0.1 : false); }
							,max:function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? 99.9 : false); }
						}
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----

						,_sPersent: {
							required: function(){ return ($('[name=_commission_type]:checked').val() == '수수료' ? true : false); }
							,max:function(){ return ($('[name=_commission_type]:checked').val() == '수수료' ? 99.9 : false); }
						}
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
				},
				messages: {
						_code : { required: '상품코드를 입력해주시기 바랍니다.' }
						,_cpid : { required: '입점업체를 선택 해주시기 바랍니다.' }
						,_name : { required: '대표상품명을 입력해주시기 바랍니다.' }
						,_price : { required: '판매가를 입력해주시기 바랍니다.' , min: '판매가를 입력해주시기 바랍니다.'}
						,_content : { required: '상품상세설명을 입력해주시기 바랍니다.' }
						//,_content_m : { required: '상품상세설명(MOBILE)을 입력해주시기 바랍니다.' }
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
						,_coupon_title: { required: '쿠폰명을 입력해주시기 바랍니다.' }
						,_coupon_price: {
							required: '쿠폰 할인금액을 입력해주시기 바랍니다.'
							,min:'쿠폰 할인금액은 0보다 큰 금액으로 입력해주시기 바랍니다.'
							,max: '쿠폰 할인금액은 판매가보다 적은 금액으로 입력해주시기 바랍니다.'
						}
						,_coupon_per: {
							required: '쿠폰 할인율을 입력해주시기 바랍니다.'
							,min:'쿠폰 할인율은 0보다 큰 수를 입력해주시기 바랍니다.'
							,max: '쿠폰 할인율은 100보다 작은 수를 입력해주시기 바랍니다.'
						}
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----

						,_sPersent: {
							required: '수수료를 입력해주시기 바랍니다.'
							,max: '수수료를 100보다 작은 수를 입력해주시기 바랍니다.'
						}
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});

</script>

<?php include_once('wrap.footer.php'); ?>