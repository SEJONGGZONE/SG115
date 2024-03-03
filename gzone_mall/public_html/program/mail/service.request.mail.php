<?php
$__first = mb_substr($_comname,0,1,'utf8'); // 보안처리
$__last = mb_substr($_comname,2,mb_strlen($_comname, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['comname'] = $__first."*".(mb_strlen($_comname,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리
$arrMailItem['content'] = nl2br($_content); // 내용
$arrMailItem['admcontent'] = nl2br($_admcontent); // 답변내용

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "

		<table style='width:100%;border-spacing:0; font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>제휴문의에 답변드립니다.</td>
				</tr>
				<tr>
					<td style='padding-top:10px; font-size:12px'>
						<strong style='color:#333;font-weight:600'>".$arrMailItem['comname']."님</strong><br/>
						<strong style='color:#333;font-weight:600'>".$siteInfo[s_adshop]."</strong> 에 문의해주셔서 감사합니다.<br/>
						요청해주신 문의에 대한 관리자의 답변내용을 다음과 같이 전달해드립니다.
					</td>
				</tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0;margin-top:30px; font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>문의내용</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:15px; border:2px solid #EBEBF2; font-weight:400;'>".$arrMailItem['content']."</td>
								<tr>
							</tbody>
						</table>

						<table style='width:100%;border-spacing:0;margin-top:30px; font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>답변내용</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:15px; border:1px solid #000; font-weight:400;'>".$arrMailItem['admcontent']."</td>
								<tr>
							</tbody>
						</table>
					</td>
				</tr>

			</tbody>
		</table>

";
?>