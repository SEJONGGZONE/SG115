<?PHP
	$app_mode = "popup";
	include_once("inc.header.php");


	// 자동입금로그 정보 추출
	$r = _MQ(" select * from smart_orderbank_log where ob_uid = '{$_uid}' ");
	if(!$r){
		error_msgPopup_s("잘못된 접근입니다.");
	}

	// 검색 체크
	$s_query = " from smart_order as o where o_paymethod = 'online' and o_paystatus = 'N' and o_canceled!='Y' ";
	$pass_type = $pass_type ? $pass_type : "match";
	if($pass_type == "match"){
		$s_query .= " and (replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."' or replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."') ";
		$s_query .= " and o_price_real = '". $r["ob_orderprice"] ."' ";
		//$s_query .= " and replace(o_bank , '-' , '' ) like '%". $r["ob_account"] ."%' ";
	}
	if($pass_type == "name"){
		$s_query .= " and (replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."' or replace(o_deposit,' ','') = '". trim($r["ob_ordername"]) ."') ";
	}
	if($pass_type == "price"){
		$s_query .= " and o_price_real = '". $r["ob_orderprice"] ."' ";
	}
	//$s_query .= " and `npay_order` = 'N' "; // 네이버페이 주문제외 2016-05-30 LDD

	$que = "
		select
			o.* ,
			(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
			(
				select
					concat(p.p_name , '§' , p.p_img_list_square)
				from smart_order_product as op
				left join smart_product  as p on ( p.p_code=op.op_pcode )
				where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
			) as p_info,
			(select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1) as ocs_cash
		$s_query
		ORDER BY o_rdate desc
	";
	$res = _MQ_assoc($que);

	// -- 은행명 추출 ---
	$arr_bank = array();
	$ex = _MQ_assoc("select bs_bank_name,bs_bank_num from smart_bank_set order by bs_uid asc");
	foreach( $ex as $k=>$v ){
		$arr_bank[rm_str($v["bs_bank_num"])] = $v["bs_bank_name"];
	}

?>



<div class="popup none_layer">

	<div class="pop_title">
		<strong>실시간입금 확인</strong>
		<a href="#none" class="btn_close" onclick="window.close();"></a>
	</div>

	<div class="pop_conts">

		<div class="data_form">
			<form name="frm" method="get" action="<?php echo $PHP_SELF?>" target="">
				<input type="hidden" name="form_name" value="<?php echo $formname?>">
				<input type="hidden" name="relation_prop_code" value="<?php echo $relation_prop_code?>">
				<input type="hidden" name="_uid" value="<?php echo $_uid?>">

				<table class="table_form">
					<colgroup>
						<col width="180"><col width="*">
					</colgroup>
					<tbody>
						<tr>
							<th>검색 입금정보</th>
							<td>
								<div class="title_conts_box">
									<dl>
										<dt class="t_blue">주문번호 : <?php echo ($arr_bank[$r["ob_account"]] ? $arr_bank[$r["ob_account"]] : "미확인") ?></dt>
										<dd>입금은행 : <?php echo $r["ob_account"] ?></dd>
										<dd>입금자명 : <?php echo $r["ob_ordername"]?></dd>
										<dd>입금액 : <?php echo number_format($r["ob_orderprice"]); ?>원</dd>
										<dd>주문일 : <?php echo date("Y-m-d H:i:s", strtotime($r["ob_date"])); ?></dd>
									</dl>
								</div>
							</td>
						</tr>
						<tr>
							<th>검색유형</th>
							<td>
								<?php echo _InputRadio("pass_type",array("match", "name", "price", "all"), $pass_type, " onchange='document.frm.submit()' ", array("입금정보(입금자명+입금금액)","입금자명","입금금액","무통장주문 전체(입금대기)")); ?>
								<?php echo _DescStr("검색유형을 클릭하면 자동으로 검색됩니다.",""); ?>
							</td>
						</tr>
						<tr>
							<th>참고사항</th>
							<td>
								<?php echo _DescStr("검색된 결과에서 일치하는 주문이 있다면 해당 주문의 [주문연동]을 클릭해주세요."); ?>
								<?php echo _DescStr("검색된 결과에서 일치하는 정보가 없다면 아래 [검색된 주문과는 별도로 입금처리하기]를 클릭하여 주문 외 입금으로 처리할 수도 있습니다."); ?>
								<?php echo _DescStr("주문외입금 처리한 후에도 다시 주문과 연동 시킬 수 있습니다."); ?>
								<?php echo _DescStr("입금자와 입금금액을 확인하신후 정확하게 선택해주시기 바랍니다.","red"); ?>
							</td>
						</tr>
					</tbody>
				</table>

			</form>

		</div>


		<div class="group_title"><strong>검색결과</strong></div>

		<!-- ● 데이터 리스트 -->
		<div class="data_list">
			<form name="relationForm2" method="post" action="_product.relation.pro.php">
			<input type="hidden" name="form_name" value="<?php echo $formname?>">
			<input type="hidden" name="relation_prop_code" value="<?php echo $relation_prop_code?>">
			<input type="hidden" name="o_ordernum" value="<?php echo $o_ordernum?>">
			<?php if(sizeof($res) > 0){ ?>

				<!-- 1차 옵션 -->
				<table class="table_list type_nocheck">
					<colgroup>
						<col width="250"><col width="*"><col width="100"><col width="80">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">주문번호/상품정보</th>
							<th scope="col">주문/입금정보</th>
							<th scope="col">주문일</th>
							<th scope="col">관리</th>
						</tr>
					</thead>
					<tbody>
						<?PHP
							foreach($res as $k=>$v){
								$ex = explode("§" , $v['p_info']);
								$app_pname = $ex[0];

								// 이미지 검사
								if($ex[1] && file_exists('..' . IMG_DIR_PRODUCT . $ex[1])){
									$_p_img = get_img_src($ex[1]);
								}else{
									$_p_img = 'images/thumb_no.jpg';
								}

								// 회원이름
								$app_user_info = _individual_info($v['o_mid']);
						?>
							<tr>
								<td class="t_left">
									<div class="product_box">
										<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($app_pname)); ?>">
										<dl>
											<dt class="t_blue"><?php echo $v['o_ordernum']; ?></dt>
											<dd><?php echo $app_pname . ($v['op_cnt'] > 1 ? " 외 " . ($v['op_cnt']-1) . "개" : ""); ?></dd>
										</dl>
									</div>


								</td>
								<td class="t_left">
									<div class="title_conts_box">
										<dl>
											<dt>주문자 : <?php echo ($v['o_memtype']=='Y' ? $app_user_info['in_name'].'<span class="normal">('.$v['o_mid'].')</span>' : $v['o_deposit'].'<span class="t_orange">(비회원)</span>'); ?></dt>
											<dd>휴대폰 : <?php echo $v['o_ohp']; ?></dd>
											<dd>이메일 : <?php echo $v['o_oemail']; ?></dd>
											<dd>입금자명 : <?php echo ($v['o_deposit']==$r['ob_ordername'] ? '<span class="t_red">(일치)</span>' : null);?> <?php echo $v['o_deposit']; ?></dd>
											<dd>
												결제금액 : <?php echo ($v['o_price_real']==$r['ob_orderprice'] ? '<span class="t_red">(일치)</span>' : null); ?>
												<?php echo number_format($v['o_price_real']); ?>원
											</dd>
										</dl>
									</div>
								</td>
								<td class="this_state">
									<?php echo date('Y-m-d', strtotime($v['o_rdate'])); ?>
								</td>
								<td class="this_ctrl">
									<div class="lineup-row type_center">
										<a href="#none" onclick="select_order('<?php echo $v['o_ordernum']; ?>')" class="c_btn h22 red">이 주문으로 연동하기</a>
									</div>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>

			<?php }else{ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>
			</form>

		</div>

	</div>

	<div class="c_btnbox type_full">
		<ul>
			<li><a href="#none" onclick="select_order('')" class="c_btn h22 red">주문 외 별도로 입금처리하기</a></li>
		</ul>
	</div>
</div>





<SCRIPT LANGUAGE="JavaScript">

	function select_order(_ordernum){

		if(_ordernum == ""){
			if(confirm("선택한 입금내역을 주문외입금처리 합니다.\n주문외입금이 맞습니까?")){
				location.href = '_orderbanklog.order_pop.pro.php?_uid=<?php echo $_uid?>&_type=adminC';
			}
		}else{
			if(confirm("선택한 주문을 입금완료처리 합니다.\n선택한 주문이 맞습니까?")){
				location.href = '_orderbanklog.order_pop.pro.php?_uid=<?php echo $_uid?>&_type=adminO&_ordernum=' + _ordernum;
			}
		}
	}

</script>



<?PHP
	include_once("inc.footer.php");
?>