<?php
	// 선택된 쿠폰을 뿌려준다.
	$couponDiscountTotalPrice = $couponSaveTotalPrice = 0;
	$printSeleteCoupon = '';
	$arrSelectCoupon = array();
	foreach($resCouponForm as $k=>$v){

		$couponPrice = $v['coup_price'];

		// 적립일경우 와 할인,배송일 경우 처리
		if( $v['coup_type'] == 'save'){
			$couponSaveTotalPrice += $v['coup_price'];
		}else{
			$couponDiscountTotalPrice += $v['coup_price'];
		}

		// 각 할인데 따른 표기
		if( $v['ocs_boon_type'] == 'discount'){
			$couponBonnType = ''.number_format( 1 * $couponPrice).'원 할인';
		}else if( $v['ocs_boon_type'] == 'save'){
			$couponBonnType = ''.number_format( 1 * $couponPrice).'원 적립';

		}else if( $v['ocs_boon_type'] == 'delivery'){
			$couponBonnType = ''.number_format( 1 * $couponPrice).'원 할인';
		}

		$couponTypeName = $arrCouponSet['ocs_type_view'][$v['ocs_type']]; // 쿠폰유형
		$couponName = stripslashes($v['ocs_name']); // 쿠폰명
		$arrSelectCoupon[] = $v['coup_uid']; // 이미 적용된 쿠폰 확인

		$printSeleteCoupon .='
			<div class="coupon_result li_select_coupon_item">
				<input type="hidden" name="use_coupon_member[]" value="'.$v['coup_uid'].'" />
				<span class="name">'.$couponTypeName.'<em>'.$couponName.'</em></span>
				<span class="discount">'.$couponBonnType.'</span>
				<a href="#none" onclick="return false;" class="btn_cancle js_select_coupon_delete"  data-uid="'.$v['coup_uid'].'"></a>
			</div>
		';
	}
?>
<dt class="form_dt"><span class="tit">쿠폰</span></dt>
<dd class="form_dd">
    <div class="mine">
        <span class="txt">보유 쿠폰 <em><?php echo number_format( 1 * count($coupon_individual)); ?>장</em></span>
        <span class="txt">사용 가능 <span class="num js_available_coupon_totalcnt"><?php echo count($arrAbailableInfo); ?>장</span></span>
    </div>

    <div class="with_btn li_available_coupon_item">
        <div class="c_select">
            <?php if( count($resCouponForm) < count($arrAbailableInfo)) {  ?>
                <select class="js_select_coupon_box">
                        <option value="">쿠폰을 선택해주세요</option>
                    <?php
                        // $priceSum +  $priceDelivery + $priceAddDelivery
                        foreach($arrAbailableInfo as $k=>$rowCouponInfo){
                            if( in_array($rowCouponInfo['coup_uid'], $arrSelectCoupon) == true){ continue; } // 이미 적용된 쿠폰이라면
                            $couponTypeName = $arrCouponSet['ocs_type_view'][$rowCouponInfo['ocs_type']];
                            $couponName = stripslashes($rowCouponInfo['ocs_name']);
                    ?>
                        <option value="<?php echo $rowCouponInfo['coup_uid']; ?>">(<?php echo $couponTypeName; ?>) <?php echo $couponName; ?></option>
                    <?php } ?>
                </select>
                <span class="icon"></span>
            <?php }else{ ?>
                <select class="js_select_coupon_box_none" disabled="">
                    <option value="">사용 가능한 쿠폰이 없습니다.</option>
                </select>
                <span class="icon"></span>
            <?php } ?>
        </div>

        <a href="#none" onclick="return false;" class="c_btn h45 black line js_select_coupon_apply">할인적용</a>
    </div><!-- end with_btn -->

    <?php echo $printSeleteCoupon; // 선택한 쿠폰노출 ?>

    <?php // 데이터박스 ?>
    <div class="coupon_form_ajax_data" style="display: none;">
        <input type="hidden" name="arrAbailableInfoCnt" value="<?php echo count($arrAbailableInfo) ?>"> <?php // 사용 가능한 쿠폰 개수 ?>
        <input type="hidden" name="arrDisableCouponUidCnt" value="<?php echo count($arrDisableCouponUid) ?>"> <?php // 사용 불가능한 쿠폰을 삭제처리한 개수  ?>
        <input type="hidden" name="couponDiscountTotalPrice" value="<?php echo $couponDiscountTotalPrice; ?>"> <?php // 할인금액 총액(주문할인+배송비할인)  ?>
        <input type="hidden" name="couponSaveTotalPrice" value="<?php echo $couponSaveTotalPrice; ?>"> <?php // 적립총액(주문적립)  ?>
        <input type="hidden" name="selectCouponAllChk" value="<?php echo count($resCouponForm) == count($arrAbailableInfo) ? 'true':'false'; ?>"> <?php // 전부 선택이 되었는지  ?>
    </div>
    <?php // ?>
</dd>
