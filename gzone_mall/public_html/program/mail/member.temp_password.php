<?php

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

$mailling_content = "

		<table style='width:100%; border-spacing:0;font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>임시 비밀번호를 보내드립니다.</td>
				</tr>
				<tr>
					<td style='padding-top:10px;'>
						홈페이지를 통해 신청하신 임시 비밀번호를 안내해드립니다.<br/>
						고객님의 안전한 정보 관리를 위해 임시 비밀번호로 접속 후<br/>
						<a href='".$app_HTTP_URL."/?pn=mypage.modify.form' style='font-weight:600; color:#333; text-decoration:none;' target='_blank'>'마이페이지 &gt; 정보수정'</a>에서 비밀번호를 변경해주세요.
					</td>
				<tr>
				<tr>
					<td>
						<table style='width:100%;border-spacing:0; margin-top:30px; font-size:inherit; color:inherit'>
							<thead>
								<tr>
									<th colspan='2' style='text-align:left; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>임시 비밀번호</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style='padding:20px; color:#FF1434; font-weight:600; text-align:center; font-size:17px; border:2px solid #EBEBF2;'>
										${tmp_pw}
									</td>
								<tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
";