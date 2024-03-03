<?php // 티켓 큐알코드 레이어 ?>
<div class="c_layer type_ticket_qr ">
    <input type="hidden" id="check_ticket_code" value="<?php echo enc('e',$this_optinfo['opt_ticketnum']) ?>">
    <input type="hidden" id="check_ticket_status" value="<?php echo $this_optinfo['opt_status'] ?>">
    <div class="wrapping">
        <div class="tit_box">
            <div class="tit">티켓 QR코드</div>
            <a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".c_layer.type_ticket_qr" data-add="if_open_layer" title="닫기"></a>
        </div><!-- end tit_box -->

        <div class="conts_box c_scroll_v">
            <div class="qr_box<?php echo $all_complete_check === true ? ' if_all_use': null ?>"> <?php // 모든 쿠폰 사용완료되면 .if_all_use ?>
                <div class="qrcode">

                    <div class="code_img">
                        <img src="<?php echo $this_optinfo['create_qrcode_url'] ?>" data-src="<?=$this_optinfo['create_qrcode_url']?>" alt="QR코드" />
                        <span class="all_complete">사용<br/>완료</span>
                    </div>
					<div class="code_num">
						<div class="number"><?php echo $this_optinfo['opt_ticketnum'] ?></div>

                        <?php if( count($coupon_assoc) > 0) { ?>
						<?php // 티켓이 2개 이상일때만 노출 ?>
						<div class="state">
							<strong class="possible">사용가능 (<?php echo number_format($coupon_status_check['대기']) ?>개)</strong>
							<em>/</em>
							<strong class="complete">사용완료 (<?php echo number_format($coupon_status_check['사용']) ?>개)</strong>
						</div>
                        <?php } ?>
					</div>
                </div>
				<div class="tip">티켓팅 담당자에게 QR코드를 보여주세요.</div>
            </div><!-- end qr_box -->
        </div><!-- end conts_box -->
    </div>
    <div onclick="return false;" class="bg_close js_onoff_event" data-target=".c_layer.type_ticket_qr" data-add="if_open_layer"></div>
</div><!-- end c_layer / type_ticket_qr -->
<script>
//
</script>
