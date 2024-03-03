<?php
$page_title = "장바구니";
include_once($SkinData['skin_root'].'/shop.header.php'); // 상단 헤더 출력
?>

<div class="c_section c_order">
    <div class="layout_fix">

		<div id="ID_cart_display">
            <?php
                // 장바구니 최초 접속 시 checkbox 모두 선택
                $app_cart_init = true;
                include OD_PROGRAM_ROOT."/shop.cart.list.ajax.php";
            ?>
        </div>

    </div><!-- end layout_fix -->
</div><!-- end c_section -->



<?php // 장바구니 스크립트 ?>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function(){
		set_cart_cnt();
		get_cart_price();
	});


	// 카트 -> 주문서작성
	function cart_submit() {
		$('input[name=mode]').val('select_buy');
		document.frm.action = '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php';
		document.frm.submit();
	}

	// === 비회원 구매 설정 kms 2019-06-25 ====
	// 카트 -> 컨펌 -> 주문서작성
	function cart_confirm_submit() {
		$('input[name=mode]').val('select_buy');
		if(confirm("구매하기는 로그인 후 이용하실 수 있습니다.\n\n로그인 페이지로 이동하시겠습니까?")){
			document.frm.action = '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php';
			document.frm.submit();
		}

	}
	// === 비회원 구매 설정 kms 2019-06-25 ====

	// - 개별상품 삭제 (클릭 시) ---
	function cart_delete(cuid) {
		if(confirm('정말 삭제하시겠습니까?')){
			$('input[name=mode]').val('select_onlydelete');
			$('input[name=cuid]').val(cuid);
			var param = $('form[name=frm]').serialize();

			$.ajax({
				url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
				data : param,
				type : 'POST',
				dataType : 'TEXT',
				success : function(data){
					switch(data){
						case 'ok' :
							// 성공 - 카트 reload
							cart_view();
							window.location.reload();
							break;
						default :
							alert('서버와의 통신중 오류가 발생하였습니다.');
							break;
					}
					return false;
				}
			});
		}
	}
	// - 선택상품 삭제 ---


	// - 선택상품 수량변경 --- type => up, down
	function cart_modify(cuid,type,buy_limit) {

		var cnt_org = $('#cart_cnt_'+cuid).val()*1; // 2019-07-24 SSJ :: 변경전 수량 저장
		var cnt = 0;

		if(type == 'up'){ // 수량 증가
		    cnt = cnt_org + 1;
		}else if(type == 'down'){ // 수량 감소
		    cnt = cnt_org - 1 ;
		}

		if(cnt <= 0){
		    return false;
		}

		$('#cart_cnt_'+cuid).val(cnt);

		$('input[name=mode]').val('select_modify');
		$('input[name=cuid]').val(cuid);
		var param = $('form[name=frm]').serialize();

		$.ajax({
		    url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
		    data : param,
		    type : 'POST',
		    dataType : 'TEXT',
		    success : function(data){
			// 2019-07-24 SSJ :: 에러 발생 시 수량 이전으로 회귀
			if(data != 'ok') $('#cart_cnt_'+cuid).val(cnt_org);

			switch(data){
			    case 'error1' :
				alert('수정하실 수량은 0보다 커야 합니다.');
				break;
			    case 'soldout' :
				alert('장바구니 담긴 상품중 품절 된 상품이 있습니다.');
				break;
			    case 'notenough' :
				alert('해당 상품의 재고량이 부족합니다.');
				break;

				// LCY : 2023-01-19 : 최대구매수량 체크 추가 p_buy_limit
			    case 'error_buy_limit' :
			    	if( type == 'up'){
				    	if( typeof buy_limit == 'undefined' || buy_limit == '' ){
				    		alert('서버와의 통신중 오류가 발생하였습니다.');
				    		return false;
				    	}
				    	buy_limit = parseInt(buy_limit);
				    	alert('본 상품은 한번 구매시 최대 '+buy_limit+'까지 구매가 가능합니다.');

				    	$('#cart_cnt_'+cuid).val(buy_limit-1);
				    	cart_modify(cuid,type,buy_limit);
			    	}
				break;


			    case 'ok' :
				// 성공 - 카트 reload
				cart_view();
				break;
			    default :
				alert('서버와의 통신중 오류가 발생하였습니다.');
				break;
			}
			return false;
		    }
		});

		//document.frm.action = "<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php";
		//document.frm.submit();
	}
	// - 선택상품 수량변경 ---


	// 장바구니 상품 비우기
	function cart_remove_all()
	{
		var chk_confirm = confirm('장바구니에 든 상품을 전부 비우시겠습니까?');

		if(chk_confirm == true){
			$(".cls_code:checkbox").prop('checked',true);
			cart_delete_submit();
		}
	}

	// - 선택상품 삭제 (옵션또한 모두 삭제) ---
	function cart_select_delete() {

		var chk_confirm = confirm('선택하신 상품을 장바구니에서 삭제 하시겠습니까?');
		if(!chk_confirm) return;

		cart_delete_submit();

	}
	// - 선택상품 삭제 --

	// - 선택상품 삭제 , 장바구니 비우기 공통 ----
	function cart_delete_submit(){
		if($('.cls_code:checkbox:checked').length == 0 ) {

			alert('1개 이상 선택해주시기 바랍니다.');
		}
		else {

			$('input[name=mode]').val('select_delete');
			var param = $('form[name=frm]').serialize();

			$.ajax({
				url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.pro.php',
				data : param,
				type : 'POST',
				dataType : 'TEXT',
				success : function(data){
					switch(data){
						case 'error1' :
							alert('1개이상 선택해주시기 바랍니다.');
							break;
						case 'ok' :
							// 성공 - 카트 reload
							cart_view();
							window.location.reload();
							break;
						default :
							alert('서버와의 통신중 오류가 발생하였습니다.');
							break;
					}
					return false;
				}
			});
		}
	}
	// - 선택상품 삭제 , 장바구니 비우기 공통 ----


	// -- 전체선택 / 반전 ----
	$(document).on('change', '.js_allcheck', function(){
		$parent = $(this).closest('.cart_list');
		if( $parent.find('.js_allcheck').val() == 'Y' ) {
			$parent.find(".cls_code").attr("checked",false);
			$parent.find('.js_allcheck').val('N').prop('checked',false);
		} else {
			$parent.find(".cls_code").attr("checked",true);
			$parent.find('.js_allcheck').val('Y').prop('checked',true);
		}

		set_cart_cnt();
		get_cart_price();
	});

	// -- 개별선택 ----
	$(document).on('change', '.cls_code', function(){
		$parent = $(this).closest('.cart_list');

		// 하나라도 선택안되어있으면 전체선택 값 수정
		if($parent.find('.cls_code:not(:checked)').length > 0){
			$parent.find('.js_allcheck').val('N').prop('checked',false);
		}else{
			$parent.find('.js_allcheck').val('Y').prop('checked',true);
		}

		set_cart_cnt();
		get_cart_price();
	});

	// -- 전체선택 / 반전 ----
	function cart_all_select(){
		$parent = $('.cart_list');
		if($parent.find('.cls_code:not(:checked)').length > 0){
			$parent.find(".cls_code").attr("checked",true);
		}else{
			$parent.find(".cls_code").attr("checked",false);
		}

		set_cart_cnt();
		get_cart_price();
	}


	// 카트 선택된 상품 수
	function set_cart_cnt(){
		//var total = $('.cls_code').length;
		//$('.glb_cart_cnt, .js_cart_cnt').text(total);
		var selected = $('.cls_code:checked').length;
		$('.js_cart_selected').text(selected);
	}


	// 카트 총 결제금액 계산
	function get_cart_price(){
		var cart_price = 0, cart_delivery = 0, cart_point = 0, cart_total = 0;
		$('.cls_code:checked').each(function(){
			cart_price += $('input[name=cart_price_'+$(this).val()+']').val()*1;
			cart_delivery += $('input[name=cart_delivery_'+$(this).val()+']').val()*1;
			cart_point += $('input[name=cart_point_'+$(this).val()+']').val()*1;
		});

		cart_total = cart_price + cart_delivery;

		$('#cart_price').text(String(cart_price).comma());
		$('#cart_delivery').text(String(cart_delivery).comma());
		$('#cart_point').text(String(cart_point).comma());
		$('#cart_total').text(String(cart_total).comma());
	}


	// 카트 불러오기 - ajax
	function cart_view(){

		var param = $('form[name=frm]').serialize();
		var open_pcode = '';
		$('form[name=frm]').find('.if_open').each(function(){
			if(open_pcode!='') open_pcode += '|';
			open_pcode = $(this).data('pcode');
		});

		param += '&open_pcode='+open_pcode+'&app_cart_init=true';
		$.ajax({
			url : '<?php echo OD_PROGRAM_URL; ?>/shop.cart.list.ajax.php',
			data : param,
			type : 'POST',
			dataType : 'HTML',
			success : function(data){
				$('#ID_cart_display').html(data);

				// 체크박스 초기화
				$('.cls_code').trigger('change');
				return false;
			}
		});
	}




</script>
<?php // 장바구니 스크립트 ?>



<script>

function NPayBuy() {

	var cart_ck = 0;
	var pcode = '';
	$('.cls_code:checked').each(function(){

		cart_ck += 1;
		pcode += ','+$(this).val();
	});
	if(cart_ck <= 0) return alert('네이버페이로 구매하실 상품을 선택 하세요.');
	if(!confirm('상품중 네이버페이로 구매 가능 한 상품만 진행됩니다.\n계속하시겠습니까?')) return false;

	location.href = ('/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=cart');
	//var LocationUrl = '/addons/npay/shop.order.result_npay.pro.php?mode=add&pcode='+pcode+'&pass_type=cart';
	//window.open(LocationUrl, '', "scrollbars=yes, width=1200, height=500");
}

<?php if(is_login()) { // 로그인일 시 만   ?>
	function _cart_product_wish(sel,_pcode){

		if(_pcode == '' || _pcode == undefined){
			alert('상품 코드를 찾을 수 없습니다.');
			return false;
		}
		var _mode = 'all';
		var ajax_data = 'mode='+_mode+'&pcode='+_pcode+'&_datatype=json';

		$.ajax({
			url:"<?php echo OD_PROGRAM_URL; ?>/product.wish.pro.php",
			async:false,
			type:'POST',
			data:ajax_data,
			success: function(data){

				if($(sel).hasClass('if_wish') == true){
					 $(sel).removeClass('if_wish');
				}else{
					$(sel).addClass('if_wish');
				}

			},error: function(xhr, status, error){
					var error_confirm=confirm('데이터 전송 오류입니다. 확인을 누르시면 페이지가 새로고침됩니다.');
					if(error_confirm==true){
						document.location.reload();
					}
				}


			});

		return false;

	}
<?php }else{ ?>

	function _cart_product_wish()
	{
		alert('로그인 사용자만 이용할 수 있습니다.');

		location.href = "/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER[QUERY_STRING]); ?>";

		return false;
	}

<?php }?>

// 선택한 상품 옵션 모두 보기
$(document).on('click', '.js_pinfo_ctl', function(){
	$parent = $(this).closest('.js_pinfo');
	var is_open = $parent.hasClass('if_open');
	if(is_open){
		$parent.removeClass('if_open');
		var _ocnt = ($(this).data('ocnt')*1).toString().comma();
		$(this).html('<span class="tx">선택한 옵션<strong>('+ _ocnt +'개)</strong> 모두보기</span>');
	}else{
		$parent.addClass('if_open');
		$(this).html('<span class="tx">옵션정보 닫기</span>');
	}
});
</script>
