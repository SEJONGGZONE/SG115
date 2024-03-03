<?php
unset($mailling_app_content,$mailling_content);
$arrMailItem = array();

$__first = mb_substr($mem_info['in_name'],0,1,'utf8'); // 보안처리
$__last = mb_substr($mem_info['in_name'],2,mb_strlen($mem_info['in_name'], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($mem_info['in_name'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

// ----------------------- 수신동의 상태 변경에 따른 처리 ---------------------------
$mailling_app_content = "";
if( $arr_var['mode'] == "mail" ) { // 메일
	$mailling_app_content .= "
				<tr>
					<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>이메일</td>
					<td style='vertical-align:top;font-weight:600; padding:4px 0;'>".($arr_var['pass'] == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
if( $arr_var['mode'] == "sms" ) { // SMS
	$mailling_app_content .= "
				<tr>
					<td style='vertical-align:top; padding:4px 0; letter-spacing:0px;'>문자</td>
					<td style='vertical-align:top;font-weight:600; padding:4px 0; '>".($arr_var['pass'] == "Y" ? "수신동의" : "수신거부")."</td>
				</tr>
	";
}
// ----------------------- 수신동의 상태 변경에 따른 처리 끝 ---------------------------

$mailling_content = "

		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>수신동의 정보 변경안내</td>
				</tr>
				<tr>
					<td style='padding-top:10px;'>
						<strong style='color:#333;font-weight:600'>".$arrMailItem['name']."님</strong><br/>
						매 2년마다 이루어지는 재수신동의로 발송되는 이메일을 통해 <br/>
						광고성 정보 수신동의 상태가 변경되었음을 알려드립니다.
					</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0; margin-top:30px;font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>광고성 정보 수신동의 변경일</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding-top:10px'>
										<table style='width:100%;border-spacing:0;font-size:inherit; color:inherit '>
											<colgroup>
												<col width='80'/><col width='*'/>
											</colgroup>
											<tbody>
												".$mailling_app_content."
												<tr>
													<td style='padding-top:5px;'>설정 변경일</td>
													<td style='padding-top:5px'>".date("Y.m.d",strtotime($mem_info['m_opt_date']))."</td>
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