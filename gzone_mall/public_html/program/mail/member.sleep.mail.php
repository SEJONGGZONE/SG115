<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();

$__first = mb_substr($r['in_name'],0,1,'utf8'); // 보안처리
$__last = mb_substr($r['in_name'],2,mb_strlen($r['in_name'], 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($r['in_name'],'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "


		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>휴면계정 해지를 위한 인증</td>
				</tr>
				<tr>
					<td style='padding-top:10px'>
						<strong style='color:#000;font-weight:600'>".$arrMailItem['name']."님</strong><br/>
						<strong style='color:#000;font-weight:600'>".$siteInfo['s_adshop']."</strong> 휴면상태를 해지하기 위한 인증을 요청해주셨습니다.<br/>
						인증완료를 위해 아래 인증버튼을 클릭하시기 바랍니다. <br/>
						클릭 후 정상적으로 인증을 마치신 후 로그인하시면 정상적으로 서비스를 이용하실 수 있습니다.
					</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0; margin-top:30px; font-size:inherit; color:inherit'>
							<tbody>
								<tr>
									<td style='text-align:center;'>
										<a href='".$_AUTH_URL."' style='border-radius:5px; font-size:14px; font-weight:600; color:#fff; background:#FF1434; padding:15px 20px; text-decoration:none; display:block' target='_blank'>휴면계정 해지 인증하기</a>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

";