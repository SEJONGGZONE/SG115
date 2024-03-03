
<?php // qr코드 , 지도보기 팝업이 노출 ?>
<div class="js_qrcode_view_popup"></div>
<div class="js_map_view_popup"></div>


<script type = "text/javascript" src = "//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $siteInfo['kakao_js_api']; ?>&libraries=services" ></script>
<script id="mypage_order_list">
<?php // qr보기 이벤트 ?>
var interval_mypage_qrcode_check = false;
$(document).on('click','.js_qrcode_view',function(e){
    var thisData = $(this).data();
    mypage_qrcode_popup(thisData);
});

var auth_mypage_qrcode_popup = true;
function mypage_qrcode_popup(thisData){
	if(interval_mypage_qrcode_check){
		clearInterval(interval_mypage_qrcode_check);
	}
	if( auth_mypage_qrcode_popup !== true){ return false; }
	auth_mypage_qrcode_popup = false;
	thisData._mode = 'qrcode_view';
    $.ajax({url:'<?php echo OD_PROGRAM_URL ?>/mypage.order.pro.php', dataType:'json',type:'post',data:thisData })
    .done(function(e){
    	if( e.rst != 'success'){ alert(e.msg); return false; }
		$('.js_map_view_popup').html(e.html);

		var $target = $('.c_layer.type_ticket_qr');
		$target.addClass('if_open_layer');

		interval_mypage_qrcode_check = setInterval(function(){
			mypage_qrcode_check();			
		},1000);
		
    })
    .fail(function(e){
    	console.log(e.responseText);
    })
    .always(function(e){
    	auth_mypage_qrcode_popup = true;
    });
};

var auth_mypage_qrcode_check = true;
function mypage_qrcode_check(){ // qr코드 변경체크
	var $parent = $('.c_layer.type_ticket_qr');
	if( $parent.hasClass('if_open_layer') !== true){ return false; } // 열리지 않았다면 처리하지 않는다. 

	if( auth_mypage_qrcode_check !== true){ return false; }
	auth_mypage_qrcode_check = false;

	var thisData = {};
	thisData._mode = 'qrcode_check';
	thisData.ticketCode = $('#check_ticket_code').val();
	thisData._status = $('#check_ticket_status').val();

    $.ajax({url:'<?php echo OD_PROGRAM_URL ?>/mypage.order.pro.php', dataType:'json',type:'post',data:thisData })
    .done(function(e){
    	if(e.rst == 'reload'){
    		var data = e.data;
			mypage_qrcode_popup(data);
    	}
    })
    .fail(function(e){
    	console.log(e.responseText);
    })
    .always(function(e){
    	auth_mypage_qrcode_check = true;
    });    
}

<?php // 지도보기 이벤트 ?>
$(document).on('click','.js_map_view',function(e){
	var thisData = $(this).data();
	mypage_map_popup(thisData);
});
function mypage_map_popup(thisData){
    thisData._mode = 'map_view';
    $.ajax({url:'<?php echo OD_PROGRAM_URL ?>/mypage.order.pro.php', dataType:'json',type:'post',data:thisData})
    .done(function(e){
    	console.log(e);
    	if( e.rst != 'success'){ alert(e.msg); return false; }
		$('.js_map_view_popup').html(e.html);
		$('.c_layer.type_ticket_map').addClass('if_open_layer');
		mypage_map_load('mapOrderView');
    })
    .fail(function(e){
    	console.log(e.responseText);
    });
}
function mypage_map_load(id)
{
	try{
		var mapData = $('#'+id).data();
		if( typeof mapData.lat == 'undefined') return false;

        // 모든 트리거 이벤트는 이곳에 한번에 정의
		$this = this;
	    if( !id ){ id = "map"}
	    var map = {};
	    var windowWidth = $(window).width();
	    $("#"+id).empty();
        var mapContainer = document.getElementById(id), // 지도를 표시할 div
            mapOption = {
                center: new daum.maps.LatLng(mapData.lat, mapData.lng), // 지도의 중심좌표
                level:mapData.level // 지도의 확대 레벨
                , scrollwheel : false,
            };
        map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

		var markerPosition  = new kakao.maps.LatLng(mapData.lat, mapData.lng);
		var marker = new kakao.maps.Marker({
		    position: markerPosition
		});
		marker.setMap(map);
		kakao.maps.event.addListener(marker, 'click', function() {}); // 마커클릭인벤트 필요시
		// 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
		var zoomControl = new kakao.maps.ZoomControl();
		map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);
		map.setDraggable(false);

	}
	catch(e){
		console.log(e);
	}
}



$('#cash_issue').on('click',function(e){ // 현금영수증 신청 버튼
	e.preventDefault();
	if (confirm('<?=$row[o_oname]?>님 <?=$row[o_ohp]?> 번호로 현금영수증 발행을 신청합니다.')) {
		$.ajax({
			data: {'ordernum':'<?=$ordernum?>'},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/totalCashReceipt.ajax.php',
			success: function(data) {
				if(data=='AUTH'){ // 작업에 성공했다면 진행 - AUTH = 현금영수증 발행, OK = 현금영수증 신청 완료
					$('#cash_status').text('현금영수증이 발행되었습니다');
				} else if(data=='OK') {
					$('#cash_status').text('현금영수증 발행이 신청되었습니다');
				} else { // 아니라면 오류 메세지
					alert('현금영수증 발행 신청에 실패했습니다.');
				}
			}
		});
	} else {
		return false;
	}
});

function order_complete(ordernum,pouid) {

	if(!confirm('수령확인 처리 하겠습니까?')) return;
	<? // 에스크로 처리 부분 -- 아직 사용하지 않음
/*		$c = _MQ("select ool_escrow from smart_order_onlinelog where ool_ordernum = '{$row[o_ordernum]}' order by ool_uid desc limit 1");
		if($c[ool_escrow]=='Y') { echo $complete_print; }
		else {
*/	?>
			common_frame.location.href='<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=complete&ordernum='+ordernum+'&pouid='+pouid;
	<? //} ?>

}

// -- LCY 카트 다시 담기 기능
// cart_stats : 카트상태 공백일 경우만 가능 , if_stats : 카트상태가 공백이고, 재고량이 있을 시
function _re_cart_pro(ordernum,opcode,cart_stats,if_stats)
{

	if(opcode == '' || opcode == undefined || ordernum == '' || ordernum == undefined ){
		alert('해당정보가 누락되었습니다.');
		return false;
	}

	if(cart_stats == ''){ // 카트에 담을 수 있다면
		if(if_stats == 'stock'){
			if(confirm('선택하신 상품의 재고량이 부족하여, 남은 재고량으로 장바구니에 담을 수 있습니다.\n장바구니에 담으시겠습니까?') === false){
				return false;
			}
		}else{
			if(confirm('선택하신 상품을 장바구니에 다시 담으시겠습니까?') === false){
				return false;
			}
		}
	}else{

		return false;
	}

		$.ajax({
			data: {'ordernum':ordernum,'opcode':opcode,'if_stats':if_stats},
			type: 'POST',
			cache: false,
			dataType:'json',
			url: '<?php echo OD_PROGRAM_URL; ?>/ajax.re_cart.pro.php',
			success: function(data) {
				//console.log(data['result']+'\n'+data['console']);
				if(data['result'] == 'success'){ // 장바구니에 다시 담았을 시
						if(confirm('해당 상품을 장바구니에 다시 담았습니다. 장바구니로 이동하시겠습니까?') === true){
							window.location.href='/?pn=shop.cart.list';
						}else{
							window.location.reload();
						}
				}else{ // 장바구니 다시 담기에 실패 하였을 시 상세 페이지 이동을 물어본다.
					if(confirm('해당 상품을 장바구니에 담지 못하였습니다. 상품 상세페이지로 이동하시겠습니까?') === true){
						window.location.href='/?pn=product.view&pcode='+opcode;
					}else{
						window.location.reload();
					}
				}
			}
		});

}


// 가상계좌/무통장 주문취소
function order_view_cancel_virtual(ordernum, price){

	var open_chk = $('.c_layer.type_order_cancel_view').hasClass('if_open_layer');
	// 콤마추가
	price = (price + '').comma();

	// 데이터 입력
	$('.cancel_virtual').find('input[name=ordernum]').val(ordernum);
	$('.cancel_virtual').find('.js_data_ordernum').text(ordernum);
	$('.cancel_virtual').find('.js_data_price').text(price);

	if(open_chk==false){
		$('.c_layer.type_order_cancel_virtual').addClass('if_open_layer');
	}else{
		$('.c_layer.type_order_cancel_virtual').removeClass('if_open_layer');
		// 데이터 삭제
		$('.cancel_virtual').find('input[name=ordernum]').val('');
		$('.cancel_virtual').find('.js_data_ordernum').text('');
		$('.cancel_virtual').find('.js_data_price').text('');
	}
}



// 주문취소
var cancel_trigger = true; // SSJ : 중복취소 방지 : 2021-12-31
function order_view_cancel(ordernum){
	if(ordernum == '' || ordernum == undefined){
		alert('잘못된 접근입니다.');
		return false;
	}

	// SSJ : 중복취소 방지 : 2021-12-31
	if(cancel_trigger === false){
		alert('전체 주문취소를 진행중입니다. 잠시만 기다려 주시기 바랍니다.');
		return false;
	}

	if( confirm('정말 주문을 취소하시겠습니까?') == true ) {
		cancel_trigger = false; // SSJ : 중복취소 방지 : 2021-12-31
		common_frame.location.href=("<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php?_mode=cancel&ordernum=" + ordernum + "&_PVSC=<?php echo $_PVSC; ?>");
	}

}
</script>