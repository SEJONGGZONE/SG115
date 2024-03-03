<?PHP
	include_once("wrap.header.php");
?>


<form name="frm" method="post" action="_config.tax.pro.php"  target="common_frame">

<!-- ● 단락타이틀 -->
<div class="group_title"><strong>바로빌 서비스 연동</strong></div>
<div class="data_form">
	<table class="table_form" summary="검색항목">
		<colgroup>
			<col width="180"><col width="*"/><col width="180"><col width="*"/>
		</colgroup>
		<tbody>
			<tr >
				<th>사용여부<br></th>
				<td>
					<?php echo _InputRadio( 'TAX_CHK' , array('Y','N') ,  (!$siteInfo['TAX_CHK'] ? 'N' : $siteInfo['TAX_CHK'] ) , '' , array('사용','미사용') , ''); ?>
					<?php echo _DescStr('세금계산서와 현금영수증 발행 시 공통으로 적용됩니다.'); ?>
				</td>
				<th>서비스 모드</th>
				<td class="auth_view">
					<?php echo _InputRadio( 'TAX_MODE' , array('service','test') , (!$siteInfo['TAX_MODE'] ? 'test' : $siteInfo['TAX_MODE'] ) , '' , array('서비스 모드','테스트 모드') , ''); ?>
					<?php echo _DescStr('테스트 모드 경우 실제 연동이 이루어지지 않습니다.'); ?>
				</td>
			</tr>
			<tr class="auth_view">
				<th class="">가입정보</th>
				<td>
					<div class="lineup-row type_multi">
						<span class="fr_tx" style="width:70px">성명</span>
						<input type="text" name="TAX_BAROBILL_NAME" class="design" style="width:200px" value="<?php echo $siteInfo['TAX_BAROBILL_NAME']; ?>" placeholder="성명"/>
					</div>
					<div class="dash_line "><!-- 점선라인 --></div>
					<div class="lineup-row type_multi">
						<span class="fr_tx" style="width:70px">아이디</span>
						<input type="text" name="TAX_BAROBILL_ID" class="design" style="width:200px" value="<?php echo  $siteInfo['TAX_BAROBILL_ID']; ?>" placeholder="바로빌 아이디"/>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="lineup-row type_multi">
						<span class="fr_tx" style="width:70px">비밀번호</span>
						<input type="password" name="TAX_BAROBILL_PW" class="design" style="width:200px" value="<?php echo $siteInfo['TAX_BAROBILL_PW']; ?>" placeholder="바로빌 비밀번호"/>
					</div>
				</td>
				<th class="">잔여 포인트</th>
				<td>
					<?PHP
						// 상태값 추출
						if($siteInfo[TAX_BAROBILL_ID] && $siteInfo['TAX_CERTKEY']) {
							// 세금계산서 잔여포인트 추출 - return_balance
							//include_once( dirname(__FILE__)."/../addons/barobill/api_ti/_tax.GetBalanceCostAmount.php");
							echo '<div class="lineup-column type_auto">';
							echo '<script>
										(function(){
											// 바로빌 잔여 포인트 추출
											$.get("/totalAdmin/ajax.simple.php?_mode=getBalanceCostAmount", function( data ) {
												if(data.indexOf("오류") > -1){
													$(".js_return_balance").html("" + data + "");
												}else{
													$(".js_return_balance").html("" + data + "P");
													$.get("/totalAdmin/ajax.simple.php?_mode=check_key", function( data ) {
														console.log(data);
														if(data != ""){
															$(".js_return_error").html("※ " + data + "").show();
														}
													}, "text");
												}
											}, "text");
										})();
									</script>';
							echo '<span class="fr_tx t_red t_big js_return_balance ">조회중입니다.</span>';
							echo '<span class="fr_tx js_return_error t_red " style="display:none;"></span>';
							echo '</div>';
							echo '<div class="dash_line"><!-- 점선라인 --></div>';
							echo '<a href="/addons/barobill/api_barobill/GetCashChargeURL.php" target="_blank" class="c_btn h27 sky left10">바로빌 포인트 충전하기</a>';
							echo '<div class="tip_box">';
							echo _DescStr('세금계산서 발행 시 포인트가 소모되며, 포인트가 없으면 발행되지 않습니다.');
							echo _DescStr('현금영수증 발행은 포인트가 소모되지 않지만 이경우에도 가입이 꼭 필요합니다.');
							echo '</div>';
						}
					?>
				</td>
			</tr>
			<tr>
				<th>가입안내</th>
				<td colspan="3">
					<div class="c_tip">1. 바로빌 회원가입</div>
					<div class="c_tip">테스트 회원가입 : <A HREF="http://testbed.barobill.co.kr" target='_blank'><u>http://testbed.barobill.co.kr</u></A></div>
					<div class="c_tip">실연동 회원가입 : <A HREF="http://www.barobill.co.kr" target='_blank'><u>http://www.barobill.co.kr</u></A></div>
					<div class="c_tip">회원가입 시 연동회원으로 가입하시고, 연동코드에 아래값을 꼭 입력해주세요.</div>
					<div class="c_tip t_red">연동코드 : GOBEYOND</div>
					<div class="dash_line"><!-- 점선라인 --></div>

					<div class="c_tip">2. 바로빌 공인인증서 등록</div>
					<div class="c_tip">전자문서 &gt; 환경설정 &gt; 공인인증서관리</div>
					<div class="c_tip">위 메뉴를 통해 세금계산서에 연동할 업체 공인인증서을 등록하시기 바랍니다.</div>
					<div class="dash_line"><!-- 점선라인 --></div>

					<div class="c_tip">3. 바로빌 충전</div>
					<div class="c_tip">마이페이지&gt; 포인트관리 &gt; 충전하기에서 포인트를 충전하시기 바랍니다.</div>
					<div class="c_tip">단, 테스트의 경우 일정 포인트를 제공해드리고 있습니다.</div>

				</td>
			</tr>

			<input type="hidden" name="TAX_CERTKEY" value="<?php echo $tax_barobill_certkery; ?>" />

		</tbody>
	</table>
</div>



<!-- ● 단락타이틀 -->
<div class="group_title"><strong>세금계산서 사업자 정보</strong></div>
<div class="data_form">
	<input type="hidden" name="name" value="<?php echo $siteInfo['s_company_name']; ?>" >
	<input type="hidden" name="ceoname" value="<?php echo $siteInfo['s_ceo_name']; ?>" >
	<input type="hidden" name="number1" value="<?php echo $siteInfo['s_company_num']; ?>" >
	<input type="hidden" name="taxaddress" value="<?php echo $siteInfo['s_company_addr']; ?>" >
	<input type="hidden" name="taxstatus" value="<?php echo $siteInfo['s_item1']; ?>" >
	<input type="hidden" name="taxitem" value="<?php echo $siteInfo['s_item2']; ?>" >

	<table class="table_form" summary="검색항목">
		<colgroup>
			<col width="180"><col width="*"/><col width="180"><col width="*"/>
		</colgroup>
		<tbody>
			<tr class="auth_view">
				<th >상호(법인)명</th>
				<td>
					<input type="text" name="name" class="design" style="width:200px" value="<?php echo $siteInfo['s_company_name']; ?>" placeholder="상호(법인)명" disabled/>
				</td>
				<th>대표자명</th>
				<td><input type="text" name="ceoname" class="design" style="width:100px" value="<?php echo $siteInfo['s_ceo_name']; ?>" placeholder="대표자명" disabled/></td>
			</tr>
			<tr class="auth_view">
				<th class="">사업자등록번호</th>
				<td><input type="text" name="number1" class="design" style="width:200px" value="<?php echo $siteInfo['s_company_num']; ?>" placeholder="사업자등록번호" disabled/></td>
				<th>주소</th>
				<td><input type="text" name="taxaddress" class="design" style="width:500px" value="<?php echo $siteInfo['s_company_addr']; ?>" placeholder="사업장소재지" disabled/></td>
			</tr>
			<tr class="auth_view">
				<th class="">업태</th>
				<td><input type="text" name="taxstatus" class="design" style="width:200px" value="<?php echo $siteInfo['s_item1']; ?>" placeholder="업태" disabled/></td>
				<th>종목</th>
				<td><input type="text" name="taxitem" class="design" style="width:200px" value="<?php echo $siteInfo['s_item2']; ?>" placeholder="종목" disabled/></td>
			</tr>
			<tr class="">
				<th>참고사항</th>
				<td colspan="3">
					<?php echo _DescStr('위 정보는 모두 [환경설정 > 기본설정 > 쇼핑몰 기본정보]에서 설정하신 내용과 동일하며 변경을 위해서는 해당페이지에서 수정해주세요.'); ?>
					<?php echo _DescStr('정확한 세금계산서 발행을 위해 사업자등록증에 기재된 정보로 입력해주세요.'); ?>
				</td>
			</tr>

		</tbody>
	</table>
</div>


<!-- ● 단락타이틀 -->
<div class="group_title"><strong>현금영수증 설정</strong></div>
<div class="data_form">
	<table class="table_form" summary="검색항목">
		<colgroup>
			<col width="180"><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th>필수발행 사용여부</th>
				<td>
					<?php echo _InputRadio( 'force_cashbill_use' , array('Y','N') ,  (!$siteInfo['s_force_cashbill_use'] ? 'N' : $siteInfo['s_force_cashbill_use'] ) , '' , array('사용','미사용') , ''); ?>
					<?php echo _DescStr('"전자상거래 현금영수증 의무발행" 관련법령 시행에 따라 건당 10만원 이상의 현금거래 시 반드시 현금영수증을 의무로 발행해야합니다.','red'); ?>
					<?php echo _DescStr('이를 위반할 경우 업종에 따라 과태료가 발생될 수 있으니 주의바랍니다.',''); ?>
				</td>
			</tr>
			<tr class="auth_cashbill">
				<th>필수발행 금액 설정</th>
				<td>
					<div class="lineup-row">
						<input type="text" name="force_cashbill_price" class="design number_style t_right" style="width:100px" value="<?php echo $siteInfo['s_force_cashbill_price']; ?>" /><span class="fr_tx">원 이상 현금 결제 시</span>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="tip_box">
						<?php echo _DescStr('위 설정된 금액 이상 현금 결제 시 현금영수증을 필수로 발행신청해야만 주문이 가능해집니다.'); ?>
						<?php echo _DescStr('현금 결제액이란 주문금액 중 적립금 사용액, 쿠폰 사용액을 제외한 실 결제 금액을 의미 합니다. '); ?>
						<?php echo _DescStr('배송비는 현금 결제액에 포함되며 현금영수증 발행 금액에도 포함됩니다. '); ?>
					</div>
				</td>
			</tr>

		</tbody>
	</table>
</div>


<?php echo _submitBTNsub(); ?>

</form>

<script>
	/*  메인스타일 ---------- */
	var onoff = function() {
		if($("input[name='TAX_CHK']").filter(function() {if (this.checked) return this;}).val() == "Y") {
			$(".auth_view").find("input").removeAttr("readonly").removeAttr("onclick").removeClass("disabled");
		}
		else {
			$(".auth_view").find("input").attr("readonly","readonly").attr("onclick","return false").addClass("disabled");
		}
	}
	onoff();
	$("input[name='TAX_CHK']").click(function() {onoff();});
	/*  // 메인스타일 ---------- */
</script>

<?PHP
	include_once("wrap.footer.php");
?>