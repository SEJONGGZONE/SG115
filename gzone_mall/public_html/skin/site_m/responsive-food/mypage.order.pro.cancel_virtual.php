
<div class="c_layer cancel_virtual type_order_cancel_virtual">

	<form name="cancel_frm" class="wrapping" method="post" action="<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php" autocomplete="off" target="">
	<input type="hidden" name="_mode" value="refund"/>
	<input type="hidden" name="ordernum" value=""/>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>"/>

        <div class="tit_box">
            <div class="tit">주문취소 신청</div>
			<a href="#none" class="btn_close close js_onoff_event" data-target=".c_layer.type_order_cancel_virtual" data-add="if_open_layer" title="닫기"></a>
        </div>

        <div class="conts_box c_scroll_v">
            <div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">주문번호</span></dt>
                    <dd class="form_dd">
                        <div class="tx_ordernum"><span class="js_data_ordernum"></span></div>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">환불받을 금액</span></dt>
                    <dd class="form_dd">
                        <div class="tx_price"><span class="js_data_price"></span>원</div>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">환불계좌</span></dt>
                    <dd class="form_dd">
                        <div class="bank_box">
                            <div class="c_select">
                                <select name="bank_code">
                                    <option value="">은행 선택</option>
                                    <?php foreach($ksnet_bank as $k => $v) { ?><option value="<?php echo $v; ?>" <?php echo ($mem_info['in_cancel_bank']!='' && ($mem_info['in_cancel_bank']==$k)?' selected ':''); ?>><?php echo $v; ?></option><?php } ?>
                                </select>
                                <span class="icon"></span>
                            </div>
                            <input type="text" name="refund_nm" class="input_design" value=""  placeholder="예금주 명" />
                            <input type="text" name="refund_account" class="input_design" value="" placeholder="계좌번호" >
                        </div>
                    </dd>
                </dl>
            </div><!-- end c_form -->
        </div><!-- end conts_box -->

        <div class="c_btnbox">
            <ul>
                <li><a href="#none" onclick="return false;" class="c_btn h40 black line js_onoff_event" data-target=".c_layer.type_order_cancel_virtual" data-add="if_open_layer">닫기</a></li>
				<li><a href="#none" class="c_btn h40 black js_allcancel_submit">전체 주문취소</a></li>
            </ul>
        </div>
	</form><!-- end wrapping -->

    <div class="bg_close js_onoff_event" data-target=".c_layer.type_order_cancel_virtual" data-add="if_open_layer" ></div>
</div><!-- end c_layer -->



<script>

// 전체 주문취소 버튼클릭시 해당 값 저장
$('.js_allcancel_submit').on('click',function(){

	var ordernum = $('.type_order_cancel_virtual [name="ordernum"]').val();
	var refund_nm = $('.type_order_cancel_virtual [name="refund_nm"]').val();
	var bank_code = $('.type_order_cancel_virtual [name="bank_code"]').val();
	var refund_account = $('.type_order_cancel_virtual [name="refund_account"]').val();

	if(ordernum =='' ){ alert('취소할 주문이 선택되지 않았습니다.\n\n새로고침(F5) 후 다시 시도해 주시기 바랍니다.'); return false; }
	if(bank_code =='' || bank_code==undefined){ alert('입금은행을 선택해주시기 바랍니다.'); return false; }
	if(refund_nm =='' || refund_nm==undefined){ alert('예금주를 입력해주시기 바랍니다.'); return false; }
	if(refund_account =='' || refund_account==undefined){ alert('환불받을 계좌번호를 입력해주시기 바랍니다.'); return false; }


	if(!confirm('정말 주문을 취소하시겠습니까?')){
		return false;
	}else{
		$('form[name=cancel_frm]').submit();
	}
});
</script>