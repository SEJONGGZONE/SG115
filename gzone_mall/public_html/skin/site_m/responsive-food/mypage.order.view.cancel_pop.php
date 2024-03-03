
<div class="c_layer type_order_cancel" id="product_cancel_pop">
	<form name="product_cancel" class="wrapping">
	    <input type="hidden" name="mode" value="cancel"/><input type="hidden" name="ordernum" value=""/><input type="hidden" name="op_uid" value=""/><input type="hidden" name="cancel_mem_type" value="member"/>

		<div class="tit_box">
			<div class="tit">부분취소 신청하기</div>
			<a href="#none" class="btn_close close js_onoff_event" data-target=".c_layer.type_order_cancel" data-add="if_open_layer" title="닫기"></a>
		</div><!-- end tit_box -->

		<div class="conts_box c_scroll_v">

			<div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">환불금액</span></dt>
                    <dd class="form_dd">
                        <div class="sum_price">
                            <dl>
                                <dt>상품가격</dt>
                                <dd><span class="price_num product_price"></span></dd>
                            </dl>
                            <dl>
                                <dt>배송비</dt>
                                <dd>+ <span class="price_num delivery_price"></span></dd>
                            </dl>
                            <dl class="this_discount discount_price_wrap">
                                <dt>할인</dt>
                                <dd>- <span class="price_num discount_price"></span></dd>
                            </dl>
                            <dl class="this_last">
                                <dt>최종 환불금액</dt>
                                <dd><strong class="return_price">0</strong>원</dd>
                            </dl>
                        </div>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">상품정보</span></dt>
                    <dd class="form_dd">
                        <div class="tx_item">
                            <strong class="product_name"><?php // 상품명 ?></strong>
                        </div>
                        <div class="tx_option js_product_info">
                            <strong class="option product_option"><?php // 옵션명 ?></strong><em class="product_option_cnt"></em>
                        </div>
						<?php // 추가옵션 별도 노출 없으면 숨김 ?>
						<!--<div class="tx_option js_add_product_info">
                            <strong class="option add_product_option"><?php // 옵션명 ?></strong><em class="add_product_option_cnt"></em>
                        </div>-->
                    </dd>
                </dl>
                <dl class="form_dl">
					<dt class="form_dt"><span class="tit">환불수단</span></dt>
					<dd class="form_dd">
						<div class="c_labelbox">
							<?php if(in_array($siteInfo['s_pg_type'],array('inicis','allthegate','kcp','lgpay','billgate'))) { ?>
								<?php if( !in_array($row['o_paymethod'],array('card','iche','point','hpp')) ) { ?>
									<label class="c_label"><input type="radio" name="cancel_type" class="cancel_type_pg" value="pg"><span class="tx"><span class="icon"></span>계좌 환불</span></label>
								<?php }else{ ?>
									<label class="c_label"><input type="radio" name="cancel_type" class="cancel_type_pg" value="pg"><span class="tx"><span class="icon"></span>PG사 결제 취소</span></label>
								<?php } ?>
							<?php } ?>
							<label class="c_label"><input type="radio" name="cancel_type" class="cancel_type_point" value="point"><span class="tx"><span class="icon"></span>적립금 환불</span></label>
						</div>

						<?php if($row['o_paycancel_method'] =='D' ){ // 환불 방식이 분배이면 설명 (부분취소 kms 2019-03-20)
							echo '
							<div class="tip_txt">할인금액을 구매 상품 개수에 따라 분배하여 환불됩니다.</div>
							';
								}else {
									echo '
							<div class="tip_txt">할인금액을 제외하고 환불됩니다.</div>
							';
								}
						?>

					</dd>
				</dl>
				<?php if( !in_array($row['o_paymethod'],array('card','iche','point','hpp')) ) { ?>
				<dl class="form_dl view_pg" style="display:none;">
					<dt class="form_dt"><span class="tit">환불계좌</span></dt>
					<dd class="form_dd">
                        <div class="bank_box">
                            <div class="c_select">
                                <select name="cancel_bank">
                                    <option value="">은행 선택</option>
                                    <?php foreach($ksnet_bank as $kk => $vv) { ?><option value="<?php echo $kk; ?>" <?php echo ($mem_info['in_cancel_bank']!='' && ($mem_info['in_cancel_bank']==$kk)?' selected ':''); ?>><?php echo $vv; ?></option><?php } ?>
                                </select>
                                <span class="icon"></span>
                            </div>
                            <input type="text" name="cancel_bank_name" class="input_design" value="<?php echo $mem_info['in_cancel_bank_name']; ?>"  autocomplete="off" placeholder="예금주 명" />
                            <input type="text" name="cancel_bank_account" class="input_design" value="<?php echo $mem_info['in_cancel_bank_account']; ?>" autocomplete="off" placeholder="계좌번호" >
                        </div>
						<label class="c_label"><input type="checkbox" name="save_myinfo" value="Y"><span class="tx"><span class="icon"></span>다음에도 사용</span></label>
					</dd>
				</dl>
				<?php } ?>
				<dl class="form_dl">
					<dt class="form_dt"><span class="tit">전달내용</span></dt>
					<dd class="form_dd">
						<textarea name="cancel_msg" rows="3" style="" class="text_design" placeholder="관리자에게 전달하실 내용이 있다면 입력해주세요."></textarea>
					</dd>
				</dl>
			</div><!-- end c_form -->

		</div><!-- end conts_box -->

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" class="c_btn h40 black line js_onoff_event" data-target=".c_layer.type_order_cancel" data-add="if_open_layer" >닫기</a></li>
				<li><a href="#none" class="c_btn h40 black js_cancel_submit" >부분취소 신청하기</a></li>
			</ul>
		</div>

	</form><!-- end wrapping -->
    <div class="bg_close js_onoff_event" data-target=".c_layer.type_order_cancel" data-add="if_open_layer" ></div>
</div><!-- end c_layer -->




<div class="c_layer height_normal type_order_cancel_view " id="product_cancel_view_pop">
    <div class="wrapping">

        <div class="tit_box">
            <div class="tit">부분취소 신청확인</div>
            <a href="#none" class="btn_close close js_onoff_event" data-target=".c_layer.type_order_cancel_view" data-add="if_open_layer" title="닫기"></a>
        </div><!-- end tit_box -->

        <div class="conts_box c_scroll_v">
            <div class="c_form">
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">환불금액</span></dt>
                    <dd class="form_dd">
                        <div class="sum_price">
                            <dl>
                                <dt>상품가격</dt>
                                <dd><span class="price_num product_price"></span></dd>
                            </dl>
                            <dl>
                                <dt>배송비</dt>
                                <dd>+ <span class="price_num delivery_price"></span></dd>
                            </dl>
                            <dl class="this_discount discount_price_wrap">
                                <dt>할인</dt>
                                <dd>- <span class="price_num discount_price"></span></dd>
                            </dl>
                            <dl class="this_last">
                                <dt>최종 환불금액</dt>
                                <dd><strong class="return_price">0</strong>원</dd>
                            </dl>
                        </div>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">상품정보</span></dt>
                    <dd class="form_dd">
                        <div class="tx_item">
                            <strong class="product_name" ><?php // 상품명 ?></strong>
                        </div>
                        <div class="tx_option js_product_info">
                            <strong class="option product_option"><?php // 옵션명 ?></strong><em class="product_option_cnt"></em>
                        </div>
						<?php // 추가옵션 별도 노출 없으면 숨김 ?>
						<!--<div class="tx_option">
                            <strong class="option add_product_option"><?php // 옵션명 ?></strong><em class="add_product_option_cnt"></em>
                        </div>-->
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">환불수단</span></dt>
                    <dd class="form_dd">
                        <?php if(in_array($siteInfo['s_pg_type'],array('inicis','allthegate','kcp','lgpay','billgate'))) { ?>
                        <?php if( !in_array($row['o_paymethod'],array('card','iche','point','hpp')) ) { ?>
                                <div class="cancel_type_val cancel_type_pg" style="display:none;">계좌 환불</div>
                            <?php }else{ ?>
                                <div class="cancel_type_val cancel_type_pg" style="display:none;">PG사 결제취소</div>
                            <?php } ?>
                        <?php } ?>
                        <div class="cancel_type_val cancel_type_point">적립금 환불</div>

                        <?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
                        <div class="tx_bank view_pg js_bank_info" style="display:none;">
                            <span class="cancel_bank"></span>
                            <span class="cancel_bank_account"></span>
                            (예금주 : <span class="cancel_bank_name"></span>)
                        </div>
                        <?php } ?>
                    </dd>
                </dl>
                <dl class="form_dl">
                    <dt class="form_dt"><span class="tit">전달내용</span></dt>
                    <dd class="form_dd">
                        <div class="cancel_msg"></div>
                    </dd>
                </dl>
            </div>
        </div><!-- end conts_box -->

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" class="c_btn h40 black line close js_onoff_event" data-target=".c_layer.type_order_cancel_view" data-add="if_open_layer" >확인</a></li>
			</ul>
		</div>

    </div><!-- end wrapping -->
   <div class="bg_close js_onoff_event" data-target=".c_layer.type_order_cancel_view" data-add="if_open_layer" ></div>
</div><!-- end c_layer -->










<script>
	$(document).ready(function(){

		$('input[name=cancel_type]').on('change',function(){
			var type = $(this).val();
			if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
		});

		// 부분취소 값 저장
		$('.js_cancel_submit').on('click',function(){
			$('form[name=product_cancel]').submit();
		});

		// 부분취소 값 저장시
		$('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();
			<?php
				// ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치  --
				if( $row['npay_order'] == 'Y'){
					echo 'alert("네이버페이 주문취소는 고객센터에 문의해 주세요."); return false;';
				}
			?>

			<?// 2016-11-30 ::: 사전체크 ::: JJC ?>
			var app_cancel_type = $("form[name=product_cancel] input[name=cancel_type]").filter(function() {if (this.checked) return this;}).val(); // 선택한 환불수단
			app_cancel_type = app_cancel_type == undefined ? '' : app_cancel_type;// - undefined 초기화
			if( app_cancel_type == '' ){ alert('환불수단을 선택해주시기 바랍니다.'); return false; }

			<? if( !in_array($row['o_paymethod'],array('card','point')) ) { ?>
				if( $('form[name=product_cancel] input[name=cancel_bank_name]').val() == '' && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' )){ alert('예금주를 입력해주시기 바랍니다.'); return false; }
				if( $('form[name=product_cancel]  select[name=cancel_bank]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('은행을 선택해주시기 바랍니다.'); return false; }
				if( $('form[name=product_cancel]  input[name=cancel_bank_account]').val() == ''  && ( $('input[name=cancel_type]:checked').val() != 'card' && $('input[name=cancel_type]:checked').val() != 'point' ) ){ alert('계좌번호를 입력해주시기 바랍니다.'); return false; }
			<? } ?>
			<?// 2016-11-30 ::: 사전체크 ::: JJC ?>

			if(confirm("정말 주문을 취소하시겠습니까?")===true) {
				var data = $(this).serialize();
				$.ajax({
					data: data,
					type: 'POST', dataType: 'JSON', cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
					success: function(data) {
						if(data['result']=='OK'){alert('부분취소/환불을 신청하셨습니다.\n\n신청내용을 확인한 후 빠르게 처리하도록 하겠습니다.'); location.reload(); return false;}
						else {alert(data['result_text']);}
					},
					error:function(request,status,error){
						alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});
			}
		});
	});


	// 부분취소 신청 버튼 클릭시 레이어팝업 노출
	$('.product_cancel').on('click',function(){
        <?php
            // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치  --
            if( $row['npay_order'] == 'Y'){
                echo 'alert("네이버페이 주문취소는 고객센터에 문의해 주세요."); return false;';
            }
        ?>

		var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');
		arr_addoption_info= new Array();

		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_thumb').attr('alt',data['data']['name']);
					$product_pop.find('.product_name').html(data['data']['name']);
					$product_pop.find('.product_link').attr('href', '/?pn=product.view&pcode='+ data['data']['pcode']);
					$product_pop.find('.product_price').html('<strong>' + data['data']['price'] + '</strong>원');//상품금액
					$product_pop.find('.delivery_price').html('<strong>' + data['data']['delivery'] + '</strong>원');//배송비용
					//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					if(parseInt(data['data']['discount']) > 0){
						$product_pop.find('.discount_price').html('<strong>' + data['data']['discount'] + '</strong>원');
						$product_pop.find('.discount_price_wrap').show();
					}else{
						$product_pop.find('.discount_price').html('');
						$product_pop.find('.discount_price_wrap').hide();
					}
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액

					if(data['data']['option']) {
						$product_pop.find('.product_option_cnt').html('('+ data['data']['pro_cnt'] +'개)');
						$product_pop.find('.product_option').html(''+ data['data']['option'] +'');
						if(data['data']['addoption']) {
							$product_pop.find('.js_add_product_info').remove();
							arr_addoption_name = data['data']['addoption'].split(',');
							arr_addoption_cnt = data['data']['addoptioncnt'].split(',');
							
							// 추가옵션 상품정보 노출
							$.each(arr_addoption_name, function (index, item) {
								$.each(arr_addoption_cnt, function (cnt_index, cnt_item) {
									arr_addoption_info['cnt'] = cnt_item.trim();
									arr_addoption_info['name'] = item.trim();
								});
							
								str = '<div class="tx_option js_add_product_info">';
								str +='<strong class="option add_product_option">(추가)'+arr_addoption_info.name+'</strong><em class="add_product_option_cnt">('+arr_addoption_info.cnt+'개)</em><div>';
								$product_pop.find('.js_product_info').after(str);
							});

							//$product_pop.find('.add_product_option').html('(추가)'+data['data']['addoption'] +'');
							//$product_pop.find('.add_product_option_cnt').html('('+ data['data']['cnt'] +'개)');
						}else{
							$product_pop.find('.js_add_product_info').hide();
							$product_pop.find('.add_product_option').html('');
							$product_pop.find('.add_product_option_cnt').html('');
						}
					} else {
						$product_pop.find('.product_option_cnt').html('('+ data['data']['cnt'] +'개)');
						$product_pop.find('.product_option').html('옵션없음');
					}
					$product_form.find('input[name=ordernum]').val(ordernum);
					$product_form.find('input[name=op_uid]').val(op_uid);
					if(data['data']['pg_check']=='N') {
						$('input[name=cancel_type].cancel_type_pg').parent().hide();
						$('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
						$('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
					}

					// KAY :: 2022-12-26 :: 레이어팝업 열고 닫기
					var layer_open_chk = $('.c_layer.type_order_cancel').hasClass('if_open_layer');
					if(layer_open_chk==false){
						 $('.c_layer.type_order_cancel').addClass('if_open_layer');
					}else{
						 $('.c_layer.type_order_cancel').removeClass('if_open_layer');
						 $product_form.find('input[name=ordernum]').val('');
						 $product_form.find('input[name=op_uid]').val('');
					}

				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});


	// 취소 신청확인 클릭시
	$('.product_cancel_view').on('click',function(){
		var ordernum = $(this).data('ordernum');
		var op_uid = $(this).data('opuid');
		$product_pop = $('#product_cancel_view_pop');
		arr_addoption_info= new Array();

		$.ajax({
			data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'view'},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
			dataType: 'JSON',
			success: function(data) {
				if(data['result']=='OK'){
					$product_pop.find('.product_thumb').attr('src',data['data']['image']);
					$product_pop.find('.product_thumb').attr('alt',data['data']['name']);
					$product_pop.find('.product_name').html(data['data']['name']);
					$product_pop.find('.product_price').html('<strong>' + data['data']['price'] + '</strong>원');//상품금액
					$product_pop.find('.delivery_price').html('<strong>' + data['data']['delivery'] + '</strong>원');//배송비용

					//할인비용 // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					if(parseInt(data['data']['discount']) > 0){
						$product_pop.find('.discount_price').html('<strong>' + data['data']['discount'] + '</strong>원');
						$product_pop.find('.discount_price_wrap').show();
					}else{
						$product_pop.find('.discount_price').html('');
						$product_pop.find('.discount_price_wrap').hide();
					}
					$product_pop.find('.return_price').text(data['data']['return']);//환불금액
					if(data['data']['option']) {
						$product_pop.find('.product_option_cnt').html('('+ data['data']['pro_cnt'] +'개)');
						$product_pop.find('.product_option').html(''+ data['data']['option'] +'');
						if(data['data']['addoption']) {

							$product_pop.find('.js_add_product_info').remove();
							arr_addoption_name = data['data']['addoption'].split(',');
							arr_addoption_cnt = data['data']['addoptioncnt'].split(',');


							console.log(arr_addoption_name);

							// 추가옵션 상품정보 노출
							$.each(arr_addoption_name, function (index, item) {
								$.each(arr_addoption_cnt, function (cnt_index, cnt_item) {
									arr_addoption_info['cnt'] = cnt_item.trim();
									arr_addoption_info['name'] = item.trim();
								});
							
								str = '<div class="tx_option js_add_product_info">';
								str +='<strong class="option add_product_option">(추가)'+arr_addoption_info.name+'</strong><em class="add_product_option_cnt">('+arr_addoption_info.cnt+'개)</em><div>';
								$product_pop.find('.js_product_info').after(str);
							});

							//$product_pop.find('.product_option').append('(추가)'+data['data']['addoption'] +'');
						}
					} else {
						$product_pop.find('.product_option_cnt').html('('+ data['data']['cnt'] +'개)');
						$product_pop.find('.product_option').html('옵션없음');
					}

					$product_pop.find('.cancel_date').text(data['data']['date']);
					$product_pop.find('.cancel_bank').text('[' + data['data']['bank'] + ']');
					$product_pop.find('.cancel_bank_account').text(data['data']['bank_account']);
					$product_pop.find('.cancel_bank_name').text(data['data']['bank_name']);
					$product_pop.find('.cancel_msg').text(data['data']['msg']);
					if(data['data']['msg'] == '') $product_pop.find('.cancel_msg').closest('dl').hide();
					else $product_pop.find('.cancel_msg').closest('dl').show();
					$product_pop.find('.cancel_type_val').hide();
					$product_pop.find('.cancel_type_val.cancel_type_'+data['data']['cancel_type']).show();

					if(data['data']['cancel_type']!='pg') {
						$product_pop.find('.js_bank_info').hide();
					}else{
						$product_pop.find('.js_bank_info').show();
					}

				} else {
					alert(data['result_text']);
				}
			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	});
</script>