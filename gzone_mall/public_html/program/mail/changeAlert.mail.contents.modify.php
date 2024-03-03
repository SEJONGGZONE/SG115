<?php
$__first = mb_substr($join_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($join_name,2,mb_strlen($join_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['joinName'] = $__first."*".(mb_strlen($join_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// ----------------------- 수신동의 상태 변경에 따른 처리 ---------------------------
$mailling_app_content = "";
if($mem_info['in_emailsend'] <> $_mailling && $_mailling ) {
	$mailling_app_content .= "
				<tr>
					<td style='padding-top:5px'>이메일</td>
					<td style='padding-top:5px'>".($_mailling == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
if($mem_info['in_smssend'] <> $_sms && $_sms ) {
	$mailling_app_content .= "
				<tr>
					<td style='padding-top:5px'>문자</td>
					<td style='padding-top:5px '>".($_sms == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
// ----------------------- 수신동의 상태 변경에 따른 처리 끝 ---------------------------

$mailling_content = "

		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>수신동의 변경안내</td>
				</tr>
				<tr>
					<td style='padding-top:10px;'>
						<strong style='color:#333;font-weight:600'>".$arrMailItem['joinName']."님</strong><br/>
						정보수정을 통하여 광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.<br/>
						변경된 수신정보상태는 아래와 같습니다.
					</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0; margin-top:30px;font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>광고성 정보 수신동의 상태</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding-top:10px;'>
										<table style='width:100%;border-spacing:0;font-size:inherit; color:inherit '>
											<colgroup>
												<col width='80'/><col width='*'/>
											</colgroup>
											<tbody>
												".$mailling_app_content."
												<tr>
													<td style='padding-top:5px;'>설정 변경일</td>
													<td style='padding-top:5px'>".date("Y.m.d")."</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>


";

?>