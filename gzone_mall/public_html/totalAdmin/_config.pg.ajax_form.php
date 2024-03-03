<?php
/*
	// -- 공통으로 과거에 쓰였으나 유지보수를 위해 나누었다.
*/

include_once('inc.php');
# PG정보
if($_pg_type == '' || in_array($_pg_type, array_keys($arr_pg_type) ) == false){exit; }

$_pg_type_name = $arr_pg_type[$_pg_type];
$_pg_installment_peroid = $siteInfo['s_pg_installment_peroid']; // 할부기간
$arrNormalPeroid = array();

// -- PG사별 할부개월 처리 :: 2~12 개월
if( in_array($_pg_type,array('inicis','lgpay','daupay','kcp')) == true){   // 이니시스, LG페이, 다우페이 , kcp(일반할부의 경우 미사용)
	for($i=2;$i<=12; $i++){ $arrNormalPeroid[$i] = $i.'개월'; }
	if( $_pg_type == 'kcp'){
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? '0' :  $_pg_installment_peroid;  // 할부개월 KCP의 경우 입력형태
	}else if( $_pg_type == 'lgpay'){ // 토스페이먼츠 PG 모듈 교체 : 2021-02-22
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? '1' :  $_pg_installment_peroid;  // 할부개월
	}else{
		$_pg_installment_peroid = $_pg_installment_peroid == '' ? array_keys($arrNormalPeroid) :  explode(",",$_pg_installment_peroid);  // 할부개월
	}
}
?>

<?php if( $_pg_type == 'inicis') {  // ■■■■■■■■■■■ 이니시스 ■■■■■■■■■■ ?>

	<div class="group_title"><strong><?php echo $_pg_type_name; ?> 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">상점 ID</th>
					<td>
						<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" placeholder="상점 ID"/>
						<?php echo _DescStr("PG사에서 발급받은 상점 아이디 또는 사이트 코드를 입력하세요.")?>
						<?php echo _DescStr($_pg_type_name." 테스트 아이디는 <em>INIpayTest</em> 입니다. (테스트 결제 시에는 카드만 가능합니다.)")?>
					</td>
					<th class="">사인키</th>
					<td>
						<input type="text" name="_pg_skey" class="design" value="<?php echo $siteInfo['s_pg_skey']; ?>" style="width:340px" placeholder="사인키"/>
						<?php echo _DescStr("PG사에서 발급받으신 사인키를 입력해 주세요. ")?>
						<?php echo _DescStr($_pg_type_name." 테스트 사인키는  <em>SU5JTElURV9UUklQTEVERVNfS0VZU1RS</em> 입니다.")?>
					</td>
				</tr>
				<tr>
					<th>에스크로 ID</th>
					<td>
						<input type="text" name="_pg_code_escrow" class="design" value="<?php echo $siteInfo['s_pg_code_escrow']; ?>" placeholder="에스크로 코드"/>
						<?php echo _DescStr("에스크로 사용 시 PG사에서 발급받은 에스크로 아이디를 입력하세요.")?>
					</td>
					<th>에스크로 사인키</th>
					<td>
						<input type="text" name="_pg_escrow_skey" class="design" value="<?php echo $siteInfo['s_pg_escrow_skey']; ?>" style="width:340px" placeholder="에스크로 사인키"/>
						<?php echo _DescStr("PG사에서 발급받은 에스크로 사인키를 입력해 주세요.")?>
					</td>
				</tr>
				<tr>
					<th>에스크로 노출</th>
					<td colspan="3">
						<label class="design left20"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y" ? "checked" : NULL;?>>에스크로 가입정보 노출</label>
						<?php echo _DescStr("노출 설정 시 쇼핑몰 하단에 노출되며, PG사 정책에 따라 노출되지 않을 수 있습니다.")?>
					</td>
				</tr>

				<?php // LCY : 2022-12-28 : 이니시스TX모듈 교체 { ?>
				<tr>
					<th class="">INIAPI키</th>
					<td>
						<input type="text" name="_pg_apikey" class="design" value="<?php echo $siteInfo['s_pg_apikey']; ?>" style="width:340px" />
						<div class="tip_box">
							<?=_DescStr("PG사에서 발급받으신 INIAPI키(인증키)를 입력해 주세요. (이니시스 가맹점 관리자 > 상점정보 > 계약정보 > 부가정보 > INIAPI key 생성 갱신에서 확인 가능합니다.  )")?>
							<?=_DescStr($_pg_type_name." 테스트 INIAPI KEY  <em>ItEQKi3rY7uvDS8l</em> 입니다.")?>
						</div>
					</td>
					<th class="ess">에스크로 INIAPI키</th>
					<td>
						<input type="text" name="_pg_escrow_apikey" class="design" value="<?php echo $siteInfo['s_pg_escrow_apikey']; ?>" style="width:340px" />
						<div class="tip_box">
							<?=_DescStr("PG사에서 발급받으신 INIAPI키(인증키)를 입력해 주세요. (이니시스 가맹점 관리자 > 상점정보 > 계약정보 > 부가정보 > INIAPI key 생성 갱신에서 확인 가능합니다.  )")?>
							<?=_DescStr($_pg_type_name." 테스트 INIAPI KEY  <em>ItEQKi3rY7uvDS8l</em> 입니다.")?>
						</div>
					</td>
				</tr>
				<?php // LCY : 2022-12-28 : 이니시스TX모듈 교체 } ?>


				<?php // 신용카드 간편결제 추가  ?>
				<?php if( count($arr_available_easypay_pg[$siteInfo['s_pg_type']]) > 0) { ?>
				<tr>
					<th>간편결제</th>
					<td colspan="3">
						<?php echo _InputCheckbox('s_pg_paymethod_easypay', array_keys($arr_available_easypay_pg['inicis']), ($siteInfo['s_pg_paymethod_easypay'] != '' ? explode(",",$siteInfo['s_pg_paymethod_easypay']) : array()) , '', array_values($arr_available_easypay_pg['inicis']), ''); ?>
						<?php echo _DescStr('PG사 간편결제는 기본 카드결제로 진행되며, 이용가능 PG사에서 별도 계약 후 사용 가능하며, 주문/결제 페이지의 결제수단에 노출됩니다.'); ?>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<th class="">할부설정</th>
					<td colspan="3">
						<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','할부') , ''); ?>
						<div class="pg-installment-peroid" style="display:none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _InputCheckbox( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
							<?php echo _DescStr("할부 가능한 개월수를 1개 이상 선택해주세요.")?>
							<?php echo _DescStr("무이자 할부는 PG사와 별도로 협의하시기 바랍니다.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th>가상계좌 입금내역 통보 URL</th>
					<td colspan="3">
						<div class="lineup-row type_multi">
							<input type="text"  id="vacc" class="design" value="http://<?=$system['host'].OD_PROGRAM_DIR?>/shop.order.result_inicis_vacctinput.php" style="width:540px" readonly />
							<a href="#none"  data-clipboard-target="#vacc" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
						<?php echo _DescStr("위 URL을 복사하셔서 <em>가맹점관리자 > 거래내역 > 가상계좌 > 입금통보방식선택 메뉴의 입금내역 통보 URL</em> 항목에 넣어주세요.")?>
					</td>
				</tr>
				<tr>
					<th class="">참고사항</th>
					<td colspan="3">
						<?php echo _DescStr($_pg_type_name." 승인절차가 끝나면 반드시 고객님의 상점ID, 사인키를 입력하시고 키파일을 FTP에 등록하셔야만 정상 결제가 이루어집니다.")?>
						<?php echo _DescStr($_pg_type_name."에서 받으신 키파일은 압축을 풀고 생성된 폴더 전체를 FTP에 그대로 올려주세요.")?>
						<?php echo _DescStr("PC 업로드 경로 : public_html/pg/pc/inicis/key")?>
						<?php echo _DescStr("모바일 업로드 경로 : public_html/pg/m/inicis/key")?>
						<?php echo _DescStr("접속 기기에 따라 맞게 적용되어야 하므로 PC, 모바일 모두 업로드해주세요.")?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?php } else if( $_pg_type == 'kcp') {  // ■■■■■■■■■■■ KCP ■■■■■■■■■■ ?>

	<div class="group_title"><strong><?php echo $_pg_type_name; ?> 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">사이트 코드</th>
					<td>
						<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" placeholder="사이트 코드"/>
						<?php echo _DescStr("PG사에서 발급받은 상점 아이디 또는 사이트 코드를 입력하세요.")?>
						<?php echo _DescStr($_pg_type_name." 테스트 사이트 코드는 <em>T0000</em> 입니다.")?>
					</td>
					<th class="">사이트 키</th>
					<td>
						<input type="text" name="_pg_key" class="design" value="<?php echo $siteInfo['s_pg_key']; ?>" style="width: 340px;" placeholder="사이트 키"/>
						<?php echo _DescStr("PG사에서 발급받은 사이트 키를 입력하세요.")?>
						<?php echo _DescStr($_pg_type_name." 테스트 사이트 키는 <em>3grptw1.zW0GSo4PQdaGvsF__</em> 입니다.")?>
					</td>
				</tr>
				<tr>
					<th>에스크로 노출</th>
					<td colspan="3">
						<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y"  ? "checked" : NULL;?>>에스크로 가입정보 노출</label>
						<?php echo _DescStr("노출 설정 시 쇼핑몰 하단에 노출되며, PG사 정책에 따라 노출되지 않을 수 있습니다.")?>
					</td>
				</tr>
				<tr>
					<th class="">할부설정</th>
					<td colspan="3">
						<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','할부') , ''); ?>
						<div class="pg-installment-peroid" style="display:none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row">
								<span class="fr_tx">최대</span>
								<?php echo _InputSelect( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
								<span class="fr_tx">개월 까지 할부 선택가능</span>
							</div>
							<?php echo _DescStr("무이자 할부는 PG사와 별도로 협의하시기 바랍니다.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th>가상계좌 입금내역 통보 URL</th>
					<td colspan="3">
						<div class="lineup-row type_multi">
							<input type="text"  id="vacc" class="design" value="http://<?=$system['host'].OD_PROGRAM_DIR?>/shop.order.result_kcp_return.php" style="width:540px" readonly />
							<a href="#none"  data-clipboard-target="#vacc" class="c_btn h28 js-clipboard" onclick="return false;">복사</a>
						</div>
						<?php echo _DescStr("위 URL을 복사하셔서 <em>가맹점관리자 > 정보변경 > 공통 URL 정보</em> 항목에 넣어주세요.")?>
						<?php echo _DescStr("인코딩은 UTF-8로 설정해 주세요.")?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?php }else if($_pg_type == 'lgpay'){ // ■■■■■■■■■■■ 토스 ■■■■■■■■■■ ?>

	<div class="group_title"><strong><?php echo $_pg_type_name; ?> 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">클라이언트 키</th>
					<td>
						<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" style="width: 340px;" placeholder="클라이언트 키"/>
						<?php echo _DescStr("PG사에서 발급받은 clientKey를 입력하세요.")?>
						<?php echo _DescStr("테스트 clientKey 키는 토스페이먼츠 홈페이지에서 회원가입 후 발급받을 수 있습니다.")?>
					</td>
					<th class="">시크릿 키</th>
					<td>
						<input type="text" name="_pg_key" class="design" value="<?php echo $siteInfo['s_pg_key']; ?>" style="width: 340px;" placeholder="시크릿 키"/>
						<?php echo _DescStr("PG사에서 발급받은 secretKey 를 입력해 주세요.")?>
						<?php echo _DescStr("테스트 secretKey 키는 토스페이먼츠 홈페이지에서 회원가입 후 발급받을 수 있습니다.")?>
					</td>
				</tr>
				<tr>
					<th>에스크로 가입정보</th>
					<td colspan="3">
						<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y"  ? "checked" : NULL;?>>에스크로 가입정보 노출</label>
						<?php echo _DescStr("노출 설정 시 쇼핑몰 하단에 노출되며, PG사 정책에 따라 노출되지 않을 수 있습니다.")?>
					</td>
				</tr>
				<tr>
					<th class="">할부 설정</th>
					<td colspan="3">
						<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','할부') , ''); ?>
						<div class="pg-installment-peroid" style="display:none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row">
								<span class="fr_tx">최대</span>
									<?php echo _InputSelect( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
								<span class="fr_tx">개월 까지 할부 선택가능</span>
							</div>
							<?php echo _DescStr("무이자 할부는 PG사와 별도로 협의하시기 바랍니다.")?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?php } else if( $_pg_type == 'daupay') {  // ■■■■■■■■■■■ 키움페이 ■■■■■■■■■■ ?>

	<div class="group_title"><strong><?php echo $_pg_type_name; ?> 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">가맹점 ID</th>
					<td>
						<input type="text" name="_pg_code" class="design" value="<?php echo $siteInfo['s_pg_code']; ?>" placeholder="가맹점 ID"/>
						<?php echo _DescStr("PG사에서 발급받은 가맹점 ID를 입력해 주세요.")?>
						<?php echo _DescStr($_pg_type_name." 테스트 가맹점 ID는 PG사에 요청하여 발급받을 수 있습니다.")?>
					</td>
					<th class="">가맹점 암호화 키</th>
					<td>
						<input type="text" name="_pg_enc_key" class="design" value="<?php echo $siteInfo['s_pg_enc_key']; ?>" style="width: 340px;" placeholder="가맹점 암호화 키"/>
						<?php echo _DescStr("신용카드와 계좌이체 취소연동을 위해 ".$_pg_type_name." 관리자 페이지에서 암호화 키를 설정해야 합니다.")?>
					</td>
				</tr>
				<tr>
					<th class="">활성화 모드</th>
					<td colspan="3">
						<?php echo _InputRadio( '_pg_mode' , array('service','test'), ($siteInfo['s_pg_mode'] ? $siteInfo['s_pg_mode'] : 'Y') , '' , array('실결제 모드','테스트 모드') , ''); ?>
						<?php echo _DescStr("테스트 모드로 설정 시 실 결제가 이루어지지 않습니다.")?>
					</td>
				</tr>
				<tr>
					<th>에스크로 노출</th>
					<td colspan="3">
						<label class="design"><input type="checkbox" name="_view_escrow_join_info" value= "Y" <?=$siteInfo['s_view_escrow_join_info'] == "Y" ? "checked" : NULL;?>>에스크로 가입정보 노출</label>
						<?php echo _DescStr("노출 설정 시 쇼핑몰 하단에 노출되며, PG사 정책에 따라 노출되지 않을 수 있습니다.")?>
					</td>
				</tr>
				<tr>
					<th class="">할부 설정<!-- 구분자 : --></th>
					<td colspan="3">
						<?php echo _InputRadio( '_pg_installment' , array('N','Y'), ($siteInfo['s_pg_installment'] ? $siteInfo['s_pg_installment'] : 'Y') , '' , array('일시불','할부') , ''); ?>
						<div class="pg-installment-peroid" style="display:none;">
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _InputCheckbox( '_pg_installment_peroid' , array_keys($arrNormalPeroid), $_pg_installment_peroid , '' , array_values($arrNormalPeroid) , ''); ?>
						</div>
						<?php echo _DescStr("할부 가능한 개월수를 1개 이상 선택해주세요.")?>
						<?php echo _DescStr("무이자 할부는 PG사와 별도로 협의하시기 바랍니다.")?>
					</td>
				</tr>
				<tr>
					<th class="">참고사항</th>
					<td colspan="3">
						<?php echo _DescStr("가상계좌와 계좌이체는 에스크로 결제로 기본 적용됩니다.")?>
						<?php echo _DescStr("정상 서비스를 위해 서버 내 방화벽설정에서 IP 27.102.213.207, 27.102.213.205 에 대한 64001, 46001 포트를 열어주셔야 합니다. (서버업체에 문의)",'black')?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="#none" class="c_btn h22 sky line js_pg_popup if_with_tip" data-mode="mail" data-width="900" data-height="700" data-page="_config.pg_daupay.popup.php">안내메일 양식보기</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php } ?>

<?php // ■■■■■■■■■■■■■■■■ 공통 ■■■■■■■■■■■■■■■■■ ?>
<div class="group_title"><strong>기타 설정</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th class="ess">가상계좌 입금기한</th>
				<td>
					<div class="lineup-row">
						<input type="text" name="_pg_virtual_date" class="design" value="<?php echo $siteInfo['s_pg_virtual_date'] == '' ? 3 : $siteInfo['s_pg_virtual_date']; ?>" style="width:70px; text-align:right;" /><span class="fr_tx">일</span>
					</div>
					<?php echo _DescStr("위 설정한 날짜가 지난 후에는 가상계좌가 정보가 삭제됩니다.")?>
					<?php echo _DescStr("0을 입력하면 주문한 당일까지만 입금이 가능합니다.")?>
				</td>
			</tr>
			<tr>
				<th>앱 스키마</th>
				<td>
					<input type="text" name="_pg_app_scheme" class="design" value="<?php echo $siteInfo['s_pg_app_scheme']; ?>" style="width:340px;"  placeholder="앱 스키마"/>
					<?php echo _DescStr("별도의 모바일 APP(앱)이 있을경우에만 입력해주세요.")?>
					<?php echo _DescStr("앱 스키마 값을 설정하시면 iOS(아이폰) 기기에서 ISP 결제를 할 경우 결제 완료처리 후 정상적으로 설정된 앱 스키마 값을 통해 쇼핑몰 앱으로 돌아갈 수 있습니다.")?>
					<?php echo _DescStr("PG사에 따라 앱 스키마 옵션 지원이 안 될 수 있습니다.")?>
				</td>
			</tr>
			<tr>
				<th class="">참고사항</th>
				<td>
					<?php echo _DescStr("가상계좌의 부분취소는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해야 합니다." , 'black')?>
					<?php echo _DescStr("1) 주문관리 메뉴에서 부분취소 할 상품이 포함된 주문을 검색합니다.")?>
					<?php echo _DescStr("2) 검색된 주문의 '상세보기' 버튼을 눌러 주문 상세보기 페이지에 접속합니다.")?>
					<?php echo _DescStr("3) 부분취소 할 상품의 '부분취소' 버튼을 눌러 부분취소(직접환불)를 진행합니다.")?>
					<?php echo _DescStr("4) 부분취소요청관리 메뉴에서 부분취소 요청 내역을 확인합니다.")?>
					<?php echo _DescStr("3) 취소된 금액을 고객님의 환불계좌에 직접 이체 합니다.")?>
					<?php echo _DescStr("4) 부분취소요청관리 메뉴에서 '취소처리' 버튼을 눌러 해당 상품을 취소합니다.")?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<?php echo _DescStr("가상계좌 입금 후 현금영수증을 신청한 경우에는 PG사 가맹점관리자 페이지에서 직접 발급하셔야 합니다.")?>
					<?php echo _DescStr("카드결제 취소는 본 관리자에서 취소처리 하시면 PG사와 연동되어 카드사까지 한 번에 취소처리가 됩니다.")?>
					<?php echo _DescStr("실시간 계좌이체 취소는 본 관리자에서 취소처리 후 PG사 관리자모드에서 또 한번 취소처리를 직접 하셔야 합니다.")?>
					<?php echo _DescStr("복합과세의 경우 반드시 PG와 먼저 복합과세 계약을 신청하셔야 합니다.")?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
