<?php

unset($mailling_content);
$arrMailItem = array();
$__first = mb_substr($_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($_name,2,mb_strlen($_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['name'] = $__first."*".(mb_strlen($_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['content'] = str_replace("{회원아이디}" , $_id , str_replace("{회원명}" , $_name , stripslashes($siteInfo['s_2year_opt_content_top'])));

$arrMailItem['_URL'] = $app_HTTP_URL . "/addons/2yearOpt/2yearOpt.php";

$arrMailItem['_URL_mail_Y'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|Y" )) ; // 메일수신동의
$arrMailItem['_URL_mail_N'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|mail§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|mail§pass|N" )) ; // 메일수신거부

$arrMailItem['_URL_sms_Y'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|Y" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|Y" )) ; // 문자수신동의
$arrMailItem['_URL_sms_N'] = $arrMailItem['_URL'] . "?p=" . (function_exists(onedaynet_encode) ? onedaynet_encode( "id|" . $_id . "§mode|sms§pass|N" ) : enc( 'e' , "id|" . $_id . "§mode|sms§pass|N" )) ; // 문자수신거부

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "


		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>수신동의 변경안내</td>
				</tr>
				<tr>
					<td style='padding-top:10px;'>
						<strong style='color:#333;font-weight:600'>".$arrMailItem['name']."님</strong><br/>
						".$arrMailItem['content']."
						본 메일의 수신동의여부 체크 버튼을 통해 수신여부 설정이 가능합니다. <br/>
						수신동의여부 설정은 한번만 가능하며 이후에는 <a href='".$app_HTTP_URL."/?pn=mypage.modify.form' target = '_blank' style='color:#333; font-weight:600; text-decoration:none'>'마이페이지 > 나의 정보수정'</a>에서 설정 가능합니다.
					</td>
				</tr>

				<tr>
					<td>
						<table style='width:100%;border-spacing:0;margin-top:30px;font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>수신동의여부 체크</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding-top:10px ;'>
										<table style='width:100%;border-spacing:0; font-size:inherit; color:inherit'>
											<colgroup>
												<col width='80'/><col width='*'/>
											</colgroup>
											<tbody>
												<tr>
													<td style=' padding-top:5px; '>이메일</td>
													<td style=' padding-top:5px;'><a href='".$arrMailItem['_URL_mail_Y']."' style='color:#cd3726;text-decoration:none; background:#0351FF; padding:6px 10px; font-weight:400; display:inline-block; color:#fff; margin:0 2px' target = '_blank' >수신 동의하기</a>
													<a href='".$arrMailItem['_URL_mail_N']."' style='color:#cd3726;text-decoration:none; background:#999; padding:6px 10px; font-weight:400; display:inline-block; color:#fff; margin:0 2px' target = '_blank'>수신 거부하기</a></td>
												</tr>
												<tr>
													<td style=' padding-top:5px; '>문자</td>
													<td style=' padding-top:5px;'><a href='".$arrMailItem['_URL_sms_Y']."' style='color:#cd3726;text-decoration:none; background:#0351FF; padding:6px 10px; font-weight:400; display:inline-block; color:#fff; margin:0 2px' target = '_blank'>수신 동의하기</a>
													<a href='".$arrMailItem['_URL_sms_N']."' style='color:#cd3726;text-decoration:none; background:#999; padding:6px 10px; font-weight:400; display:inline-block; color:#fff; margin:0 2px' target = '_blank'>수신 거부하기</a></td>
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