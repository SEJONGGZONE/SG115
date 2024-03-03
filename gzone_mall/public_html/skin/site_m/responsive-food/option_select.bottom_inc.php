<?php

	if( count($sres) > 0 ){
        foreach( $sres as $k=>$sr ){
			$is_addoption = ($sr['pto_is_addoption']=="Y" ? "add_" : "" );

			// {{{회원등급혜택}}}
			$printGroupSet = '';
			$groupSetPrice = getGroupSetPer($sr['pto_poptionprice'],'price',$sr['pto_pcode']);
			if( $groupSetPrice > 0){ // 할인금액이 있다면
				// $printGroupSet = ' <span class="group_price">(회원할인)</span>';
				$groupSetPrice *= $sr['pto_cnt'];
			}
			// {{{회원등급혜택}}}


			$cnt_class = $sr['pto_cnt']<='1'?'if_no':'';

/*					<!-- 날짜요일 추가(띄어쓰기 유지) -->
					<strong class='date'>".$this_day."</strong>*/
			// LCY : 2022-12-21 : 티켓기능
			$print_dateoption_view = '';
			if($sr['pto_dateoption_use'] == 'Y'){
				// 오늘 날짜
				$this_day = $sr['pto_dateoption_date'].' ('.$arr_day_week_short[date('w',strtotime($sr['pto_dateoption_date']))].')';
				$print_dateoption_view = '<strong class="date">'.$this_day.'</strong>';
			}

            echo "
				<dl>
					<dt>
					".$print_dateoption_view."
					" .
							trim(
								($sr['pto_is_addoption']=="Y" ? "<span class='add_tag'>추가</span>" : "" ) .
								$sr['pto_poptionname1'] . ($sr['pto_poptionname2']?" / ".$sr['pto_poptionname2']:null) . ($sr['pto_poptionname3']?" / ".$sr['pto_poptionname3']:null)
							)
					. "</dt>
					<dd>
						<div class='counter_box'>
							<a href='#none' onclick=\"".$is_addoption."option_select_update('down' , '" . $sr['pto_uid'] . "','" . $sr['pto_pcode'] . "')\" class='btn_down ".$cnt_class." js_option_cnt_chk' data-uid='".$sr['pto_uid']."' title=''><span class='shape'></span></a>
							<input type='text' name='' id='input_cnt_".$is_addoption.$sr[pto_uid]."' class='updown_input' value='".$sr['pto_cnt']."' readonly>
							<a href='#none' onclick=\"".$is_addoption."option_select_update('up' , '" . $sr['pto_uid'] . "','" . $sr['pto_pcode'] . "');\" class='btn_up' title=''><span class='shape'></span></a>
						</div>
						<span class='price'><strong>" . number_format( 1 * $sr['pto_poptionprice'] * $sr['pto_cnt'] - $groupSetPrice) . "</strong><em>원</em>".$printGroupSet."</span>
						<a href='#none' onclick=\"option_select_del('" . $sr['pto_uid'] . "','".$sr['pto_pcode']."')\" class='btn_delete' title='삭제'></a>
					</dd>
				</dl>
            ";
            $price_sum += $sr['pto_poptionprice'] * $sr['pto_cnt'] - $groupSetPrice;
            $cnt_sum += $sr['pto_cnt'];
        }
    }
    else {
        /*echo "
			<dl>
				<dt>구매하실 상품 옵션을 선택해 주시기 바랍니다.</dt>
			</dl>
        ";*/
    }


    echo "
		<input type=hidden name=option_select_expricesum ID='option_select_expricesum' value='{$price_sum}'>
		<input type=hidden name=option_select_cnt id='option_select_cnt' value='{$cnt_sum}'>
    ";

/*
<dl>
	<dt>[선택 01] 친환경 물로 붙이는 건식 풀바른 벽지 / 2차 옵션명 / 3차 옵션명</dt>
	<dd>
		<div class="view_counter">
			<a href="" class="btn_down" title="빼기"><span class="shape"></span></a>
			<input type="text" name="" class="updown_input" value="1">
			<a href="" class="btn_up" title="더하기"><span class="shape"></span></a>
		</div>
		<span class="price"><strong>9,127,800</strong>원</span>
		<a href="" class="btn_delete" title="옵션삭제"></a>
	</dd>
</dl>
<dl>
	<dt><span class="add_tag">추가</span>[선택 01] 친환경 물로 붙이는 건식 풀바른 벽지 / 2차 옵션명 / 3차 옵션명</dt>
	<dd>
		<div class="view_counter">
			<a href="" class="btn_down" title="빼기"><span class="shape"></span></a>
			<input type="text" name="" class="updown_input" value="1">
			<a href="" class="btn_up" title="더하기"><span class="shape"></span></a>
		</div>
		<span class="price"><strong>127,800</strong>원</span>
		<a href="" class="btn_delete" title="옵션삭제"></a>
	</dd>
</dl>
*/