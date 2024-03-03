<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();
$__first = mb_substr($_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($_name,2,mb_strlen($_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['id'] = LastCut($_id,2);

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "


		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>휴면계정 전환안내</td>
				</tr>
				<tr>
					<td style='padding-top:15px;'>
						<strong style='color:#000;font-weight:600'>".$arrMailItem['name']."님</strong><br/>
						<strong style='color:#000;font-weight:600'>".$siteInfo['s_adshop']."</strong> 의 계정이 휴면상태로 변경되었습니다.<br/>
						계정 정보를 확인하시고, 홈페이지를 방문 및 로그인하시면 휴면상태를 해지할 수 있습니다.
					</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0; margin-top:30px;font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>회원정보</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding-top:10px;'>
										<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
											<colgroup>
												<col width='80'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style='padding-top:5px;'>이름</td>
													<td style='padding-top:5px'>".$arrMailItem['name']."</td>
												</tr>
												<tr>
													<td style='padding-top:5px'>아이디 </td>
													<td style='padding-top:5px'>".$arrMailItem['id']."</td>
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