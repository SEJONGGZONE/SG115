<?php
unset($mailling_content,$mailling_app_content);
$arrMailItem = array();
$__first = mb_substr($join_name,0,1,'utf8'); // 보안처리
$__last = mb_substr($join_name,2,mb_strlen($join_name, 'utf8' ),'utf8'); // 보안처리
$arrMailItem['joinName'] = $__first."*".(mb_strlen($join_name,'utf8') > 2 ? $__last : null); // 이름 가운데 * 처리

$app_HTTP_URL = 'http://'.$system['host']; // 메일링 url

if( !is_array($mem_info) || count($mem_info) < 1){
	$mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
}

// ------------------------ 광고성 이메일/SMS 수신동의 상태 표기 --------------------------
$mailling_app_content = "
	<tr>
		<td>
			<table style='width:100%; border-spacing:0; margin-top:30px; font-size:inherit; color:inherit'>
				<thead>
					<tr>
						<th colspan='2' style='text-align:left; border-bottom:1px solid #EBEBF2; color:#000; font-size:16px; font-weight:700; padding-bottom:5px'>광고성 정보 수신동의 상태</th>
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
									<tr>
										<td style='padding-top:5px'>이메일</td>
										<td style='padding-top:5px'>".($mem_info['in_emailsend'] == "Y" ? "수신동의" : "수신거부")."</td>
									</tr>
									<tr>
										<td style='padding-top:5px'>문자</td>
										<td style='padding-top:5px'>".($mem_info['in_smssend'] == "Y" ? "수신동의" : "수신거부")."</td>
									</tr>
									<tr>
										<td style='padding-top:5px'>변경일</td>
										<td style='padding-top:5px'>".date("Y.m.d",strtotime( rm_str($mem_info['m_opt_date']) > 0 ? $mem_info['m_opt_date'] : date('Y-m-d') ))."</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
";
// ------------------------ 광고성 이메일/SMS 수신동의 상태 표기 끝 --------------------------

$mailling_content = "
		<table style='width:100%; border-spacing:0; font-size:inherit; color:inherit'>
			<tbody>
				<tr>
					<td style='text-align:left; border-bottom:2px solid #000; color:#000; font-weight:700; font-size:2em; padding:20px 0 10px'>회원가입을 환영합니다.</td>
				</tr>
				<tr>
					<td style='padding-top:15px;'>
						<strong style='font-weight:600; color:#000;'><!-- 이름 -->".$arrMailItem['joinName']."님!</strong><br/>
						<strong style='font-weight:600; color:#000; letter-spacing:-0.5px'>".$siteInfo[s_adshop]."</strong> 회원가입이 정상적으로 완료되었습니다.<br>
						가입하신 정보는 <a href='".$app_HTTP_URL."/?pn=mypage.modify.form' style='font-weight:600; color:#333; text-decoration:none' target='_blank'>마이페이지 &gt; 정보수정</a> 에서 수정이 가능합니다.
					</td>
				</tr>

				".$mailling_app_content."

			</tbody>
		</table>
";
