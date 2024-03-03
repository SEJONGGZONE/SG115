<div class="c_layer type_order_complain">
	<form ID="frm_complain_page" class="wrapping" name="frm_complain_page" action="<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php" target="common_frame" method="post" onsubmit="return complain_func(this)">
	<input type="hidden" name="opuid" id="opuid" value="" />
	<input type="hidden" name="_mode" value="complain" />	

        <div class="tit_box">
            <div class="tit">교환/반품 신청</div>
            <a href="#none" class="btn_close js_onoff_event" onclick="return false;" title="닫기" data-target=".c_layer.type_order_complain" data-add="if_open_layer"></a>
        </div><!-- end tit_box -->

        <div class="conts_box c_scroll_v">
            <div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">상품정보</span></dt>
                    <dd class="form_dd">
                        <div class="tx_item">
                            <strong class="product_name js_complan_pname" value=""></strong><?php // 상품명 ?>
                        </div>
                        <div class="tx_option">
                            <strong class="option product_option js_product_option"></strong><em class="product_option_cnt js_product_option_cnt"></em><?php // 옵션명 ?>
                        </div>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">신청내용</span></dt>
                    <dd class="form_dd">
                        <textarea name="complain_content" rows="3" style="" class="text_design js_complain_content" placeholder="관리자에게 전달하실 내용이 있다면 입력해주세요."></textarea>
                    </dd>
                </dl>
            </div>
        </div><!-- end conts_box -->

        <div class="c_btnbox">
            <ul>
                <li><a href="#none" onclick="return false;" data-target=".c_layer.type_order_complain" data-add="if_open_layer" class="c_btn h40 black line close js_onoff_event">닫기</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h40 black close js_complain_submit" >교환/반품 신청</a></li>
            </ul>
        </div>

	</form><!-- end wrapping -->
    <div class="bg_close js_onoff_event" data-target=".c_layer.type_order_complain" data-add="if_open_layer" ></div>
</div><!-- end c_layer -->



<script>

	// 교환/반품 신청 값 저장
	$('.js_complain_submit').on('click',function(){
		$('#frm_complain_page').submit();
	});

	// 교환반품 폼 유효성 체크
	function complain_func(frm) {

		var complain_content = $('.js_complain_content').val();
		if(complain_content=='') {alert('내용을 입력해 주세요.');return false;}

        <?php 
            // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치  --
            if( $row['npay_order'] == 'Y'){ 
                echo 'alert("네이버페이의 경우 교환/반품 신청은 고객센터에 문의해 주세요."); return false;';
            }
        ?>
		if(!frm.opuid.value) {alert('오류가 발생하였습니다. 새로고침 후 다시 시도해주세요.');return false;}
		if(!confirm('교환/반품 신청을 하시겠습니까?')) return false;

		return true;
	}
		
	// - 교환반품 박스 open ---
	function complain_view(pname,opuid,opname,opcnt){
		$("#opuid").val(opuid);
		$(".js_complan_pname").val(pname);
		$(".js_complan_pname").text(pname);

		$(".js_product_option").text(opname);
		$(".js_product_option_cnt").text(' ('+opcnt+'개)');

		open_complain_chk = $('.c_layer.type_order_complain').hasClass('if_open_layer');
		if(open_complain_chk==false){
			$('.c_layer.type_order_complain').addClass('if_open_layer');
			$('.js_complain_content').val('');
		}else{
			$('.c_layer.type_order_complain').removeClass('if_open_layer');
		}
	}

</script>
