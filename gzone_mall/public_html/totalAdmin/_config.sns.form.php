<?php
	include_once('wrap.header.php');
	$HTTPS_Check = ($system['ssl_use'] == 'Y'?true:false);
?>
<form action="_config.sns.pro.php" method="post">
	<div class="group_title"><strong>SNS로그인 및 공유하기 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>카카오 API</th>
					<td>
						<label class="design">
							<input type="checkbox" name="kakao_login_use" value="Y"<?php echo ($siteInfo['kakao_login_use'] == 'Y'?' checked':null); ?>>
							카카오 로그인 사용
						</label>
						<label class="design">
							<input type="checkbox" name="kakao_share_use" value="Y"<?php echo ($siteInfo['kakao_share_use'] == 'Y'?' checked':null); ?>>
							카카오 공유하기 사용
						</label>
						<div class="tip_box">
							<?php echo _DescStr('공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다.'); ?>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">REST </span>
							<input type="text" name="kakao_api" class="design" value="<?php echo $siteInfo['kakao_api']; ?>" style="width:250px" placeholder="REST">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Javascript </span>
							<input type="text" name="kakao_js_api" class="design" value="<?php echo $siteInfo['kakao_js_api']; ?>" style="width:250px" placeholder="Javascript">
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>

						<?php // 복사URL ?>
						<div class="copy_box">
							<dl>
								<dt>Redirect URI</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.$system['host'].'/addons/sns_login/kakao/callback.php';?>" onclick="return false;"><strong><?php echo 'http://'.$system['host'].'/addons/sns_login/kakao/callback.php';?></strong></a>
									<?php // 보안서버를 사용하는 경우 ?>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/kakao/callback.php';?>" onclick="return false;"><strong><?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/kakao/callback.php';?></strong></a>
									<?php }?>
								</dd>
							</dl>
						</div><!-- end copy_box -->
					</td>
				</tr>
				<tr>
					<th>네이버 API</th>
					<td>
						<label class="design">
							<input type="checkbox" name="nv_login_use" value="Y"<?php echo ($siteInfo['nv_login_use'] == 'Y'?' checked':null); ?>>
							네이버 로그인 사용
						</label>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Client ID </span>
							<input type="text" name="nv_login_key" class="design" value="<?php echo $siteInfo['nv_login_key']; ?>" style="width:200px" placeholder="Client ID">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Client Secret </span>
							<input type="text" name="nv_login_secret" class="design" value="<?php echo $siteInfo['nv_login_secret']; ?>" style="width:200px" placeholder="Client Secret">
						</div>

						<div class="dash_line"></div>

						<?php // 복사URL ?>
						<div class="copy_box">
							<dl>
								<dt>서비스 URL</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.$system['host'];?>" onclick="return false;"><strong><?php echo 'http://'.$system['host'];?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['host'];?>" onclick="return false;"><strong><?php echo 'https://'.$system['host'];?></strong></a>
									<?php }?>
								</dd>
							</dl>
							<dl>
								<dt>Callback URL</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/naver/callback.php';?>" onclick="return false;"><strong><?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/naver/callback.php';?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/naver/callback.php';?>" onclick="return false;"><strong><?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/naver/callback.php';?></strong></a>
									<?php }?>
								</dd>
							</dl>
						</div><!-- end copy_box -->
					</td>
				</tr>
				<tr>
					<th>페이스북 API</th>
					<td>
						<?php if($siteInfo['s_ssl_status'] == '대기' || $siteInfo['s_ssl_check'] == 'N') { ?>
							<span class="c_tag gray">보안서버 미적용</span>
						<?php } else if($siteInfo['s_ssl_status'] == '진행' &&  $siteInfo['s_ssl_check'] == 'Y') { ?>
							<span class="c_tag blue">보안서버 적용중</span>
						<?php } else if($siteInfo['s_ssl_status'] == '만료') { ?>
							<span class="c_tag gray">보안서버 만료</span>
						<?php } else { ?>
							<span class="c_tag gray">보안서버 미적용</span>
						<?php } ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<label class="design">
							<input type="checkbox" name="s_facebook_login_use" value="Y"<?php echo ($siteInfo['s_facebook_login_use'] == 'Y' && $siteInfo['s_ssl_status'] == '진행' && $siteInfo['s_ssl_check'] == 'Y'?' checked':null); ?><?php echo ($siteInfo['s_ssl_status'] != '진행' || $siteInfo['s_ssl_check'] != 'Y'?' onclick="if(confirm(\'페이스북 로그인 기능을 사용하기 위해서는 보안인증서(SSL)가 서버에 설치되어 있어야 합니다.\\n\\n보안서버 설정 페이지로 이동하시겠습니까?\')) location.href=\'_config.ssl.default_form.php\'; return false;"':null); ?>>
							페이스북 로그인 사용
						</label>
						<label class="design">
							<input type="checkbox" name="facebook_share_use" value="Y"<?php echo ($siteInfo['facebook_share_use'] == 'Y'?' checked':null); ?>>
							페이스북 공유하기 사용
						</label>
						<div class="tip_box">
							<?php echo _DescStr('페이스북 로그인을 정상적으로 이용하기 위해서는 보안서버가 설치되어 있어야 합니다. '); ?>
							<?php echo _DescStr('공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다.'); ?>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">앱 아이디 </span>
							<input type="text" name="s_facebook_key" class="design" value="<?php echo $siteInfo['s_facebook_key']; ?>" style="width:250px" placeholder="앱 아이디">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">앱 시크릿 코드 </span>
							<input type="text" name="s_facebook_secret" class="design" value="<?php echo $siteInfo['s_facebook_secret']; ?>" style="width:250px" placeholder="앱 시크릿 코드">
						</div>

						<div class="dash_line"><!-- 점선라인 --></div>

						<?php // 복사URL ?>
						<div class="copy_box">
							<dl>
								<dt>앱도메인</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo $system['host'];?>" onclick="return false;"><strong><?php echo $system['host'];?></strong></a>
								</dd>
							</dl>
							<dl>
								<dt>로그인 리디렉션 URI</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.$system['host'].'/addons/sns_login/facebook/callback.php';?>" onclick="return false;"><strong><?php echo 'http://'.$system['host'].'/addons/sns_login/facebook/callback.php';?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/facebook/callback.php';?>" onclick="return false;"><strong><?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/facebook/callback.php';?></strong></a>
									<?php }?>
								</dd>
							</dl>
						</div><!-- end copy_box -->
					</td>
				</tr>
				<tr>
					<th>트위터</th>
					<td>
						<label class="design">
							<input type="checkbox" name="twitter_share_use" value="Y"<?php echo ($siteInfo['twitter_share_use'] == 'Y'?' checked':null); ?>>
							트위터 공유하기 사용
						</label>
						<div class="clear_both"></div>
						<div class="tip_box">
							<?php echo _DescStr("공유 기능을 사용할 경우 상품 상세페이지에 해당 SNS로 상품을 공유할 수 있는 버튼이 노출됩니다."); ?>
						</div>
					</td>
				</tr>
				<?php // ?>
				<tr>
					<th>구글 API</th>
					<td>
						<label class="design">
							<input type="checkbox" name="s_google_login_use" value="Y"<?php echo ($siteInfo['s_google_login_use'] == 'Y'?' checked':null); ?>>
							구글 로그인 사용
						</label>
						<div class="tip_box">
							<?php echo _DescStr('구글 로그인을 정상적으로 이용하기 위해서는 보안서버가 설치되어 있어야 합니다. '); ?>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Client 아이디</span>
							<input type="text" name="s_google_client_id" class="design" value="<?php echo $siteInfo['s_google_client_id']; ?>" style="width:200px" placeholder="Client 아이디">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Client 비밀번호</span>
							<input type="text" name="s_google_client_pw" class="design" value="<?php echo $siteInfo['s_google_client_pw']; ?>" style="width:200px" placeholder="Client 비밀번호">
						</div>

						<div class="dash_line"></div>

						<?php // 복사URL ?>
						<div class="copy_box">
							<dl>
								<dt>서비스 URL</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.$system['host'];?>" onclick="return false;"><strong><?php echo 'http://'.$system['host'];?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['host'];?>" onclick="return false;"><strong><?php echo 'https://'.$system['host'];?></strong></a>
									<?php }?>
								</dd>
							</dl>
							<dl>
								<dt>Callback URL</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/google/callback.php';?>" onclick="return false;"><strong><?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/google/callback.php';?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/google/callback.php';?>" onclick="return false;"><strong><?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/google/callback.php';?></strong></a>
									<?php }?>
								</dd>
							</dl>
						</div><!-- end copy_box -->
					</td>
				</tr>


				<tr>
					<th>애플 API</th>
					<td>
						<label class="design">
							<input type="checkbox" name="apple_login_use" value="Y"<?php echo ($siteInfo['apple_login_use'] == 'Y'?' checked':null); ?>>
							애플 로그인 사용
						</label>
						<div class="tip_box">
							<?php echo _DescStr('하이APP서비스 이용 시 가능한 설정이며, 자체개발 시 서비스지원이 제한될 수 있습니다.'); ?>
							<?php echo _DescStr('애플 로그인을 정상적으로 이용하기 위해서는 보안서버가 설치되어 있어야 합니다. '); ?>
						</div>						
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">Client ID </span>
							<input type="text" name="apple_clientid" class="design" value="<?php echo $siteInfo['apple_clientid']; ?>" style="width:200px" placeholder="Client ID">
						</div>

						<div class="dash_line"></div>

						<?php // 복사URL ?>
						<div class="copy_box">
							<dl>
								<dt>도메인</dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.str_replace('www.', '', $system['host']);?>" onclick="return false;"><strong><?php echo 'http://'.str_replace('www.', '', $system['host']);?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.str_replace('www.', '', $system['host']);?>" onclick="return false;"><strong><?php echo 'https://'.str_replace('www.', '', $system['host']);?></strong></a>
									<?php }?>
								</dd>
							</dl>
							<dl>
								<dt>Callback URL </dt>
								<dd>
									<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/apple/callback.php';?>" onclick="return false;"><strong><?php echo 'http://'.str_replace('www.', '', $system['host']).'/addons/sns_login/apple/callback.php';?></strong></a>
									<?php if($HTTPS_Check===true){?>
										<a href="#none" class="btn_copy js-clipboard" data-clipboard-text="<?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/apple/callback.php';?>" onclick="return false;"><strong><?php echo 'https://'.$system['ssl_domain'].'/addons/sns_login/apple/callback.php';?></strong></a>
									<?php }?>
								</dd>
							</dl>
						</div><!-- end copy_box -->
					</td>
				</tr>
				<tr>
					<th>API 발급가이드</th>
					<td>
						<a href="https://www.onedaynet.co.kr/p/service.faq_list.html?searchMode=both&searchIndex=api" class="c_btn h27 sky" target="_blank">API 발급가이드 바로가기</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>스팸방지(리캡챠) 기능 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>구글 API</th>
					<td>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">사이트 키 </span>
							<input type="text" name="recaptcha_api" class="design" value="<?php echo $siteInfo['recaptcha_api']; ?>" style="width:315px" placeholder="사이트 키">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">시크릿 키 </span>
							<input type="text" name="recaptcha_secret" class="design" value="<?php echo $siteInfo['recaptcha_secret']; ?>" style="width:315px" placeholder="시크릿 키">
						</div>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td>
						<a href="https://www.onedaynet.co.kr/p/service.faq_list.html?searchMode=both&searchIndex=api" class="c_btn h27 sky" target="_blank">API 발급가이드 바로가기</a>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('위 입력값이 저장되면 회원가입,제휴문의 페이지 하단에 무조건 스팸방지가 적용됩니다.'); ?>
						<?php echo _DescStr('게시판별 글쓰기는 회원 또는 비회원에게 스팸방지를 적용할 것인지를 설정할 수 있습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>소셜채널 링크</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>채널별 URL</th>
					<td>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">인스타그램 </span>
							<input type="text" name="sns_link_instagram" class="design" value="<?php echo $siteInfo['sns_link_instagram']; ?>" style="width:100%" placeholder="인스타그램">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">페이스북 </span>
							<input type="text" name="sns_link_facebook" class="design" value="<?php echo $siteInfo['sns_link_facebook']; ?>" style="width:100%" placeholder="페이스북">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">트위터 </span>
							<input type="text" name="sns_link_twitter" class="design" value="<?php echo $siteInfo['sns_link_twitter']; ?>" style="width:100%" placeholder="트위터">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">유튜브 </span>
							<input type="text" name="sns_link_youtube" class="design" value="<?php echo $siteInfo['sns_link_youtube']; ?>" style="width:100%" placeholder="유튜브">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">블로그 </span>
							<input type="text" name="sns_link_blog" class="design" value="<?php echo $siteInfo['sns_link_blog']; ?>" style="width:100%" placeholder="블로그">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">카카오채널 </span>
							<input type="text" name="sns_link_kkp" class="design" value="<?php echo $siteInfo['sns_link_kkp']; ?>" style="width:100%" placeholder="카카오채널">
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('스킨에 따라 노출여부와 위치는 상이할 수 있습니다.', ''); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>
</form>

<?php include_once('wrap.footer.php'); ?>