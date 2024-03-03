<?php
include_once('wrap.header.php');

$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>


<form class="defaut_form" method="post" action="_config.default.pro.php" onsubmit="return validate_check();">
	<!-- 관리자 정보설정 -->
	<div class="group_title"><strong>관리자 정보설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">관리자 아이디</th>
					<td>
						<input type="text" name="_adid" class="design" value="<?php echo $r['s_adid']; ?>" style="width:185px" required placeholder="관리자 아이디">
					</td>
					<th>관리자 비밀번호</th>
					<td>
						<label class="design"><input type="checkbox" name="_change_apw" class="js_change_apw" value="Y"> 변경</label>
						<div class="js_change_apw_box lineup-column" style="display: none;">
							<div class="lineup-row type_multi">
								<span class="fr_tx" style="width:90px">비밀번호 변경 </span> <input type="password" name="_adpwd" class="design js_pw_input" value="" style="width:150px" placeholder="비밀번호 변경">
							</div>
							<div class="lineup-row type_multi">
								<span class="fr_tx" style="width:90px">비밀번호 확인 </span> <input type="password" name="_adpwd_ck" class="design js_pw_ckinput" value="" style="width:150px" placeholder="비밀번호 확인">
							</div>
							<div class="dash_line"><!-- 점선 --></div>
							<?php echo _DescStr('6자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.', ''); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="" rowspan="2">발신/수신 정보</th>
					<td rowspan="2">
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">대표전화</span><input type="text" name="_glbtel" class="design" value="<?php echo $r['s_glbtel']; ?>" style="width:185px" placeholder="대표 번호" required>
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">대표메일</span><input type="text" name="_ademail" value="<?php echo $r['s_ademail']; ?>" class="design" style="width:185px" placeholder="대표 메일" required>
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">수신 휴대폰</span>
							<input type="text" name="_glbmanagerhp" class="design" value="<?php echo $r['s_glbmanagerhp']; ?>" style="width:185px" placeholder="관리자 휴대폰" required>
						</div>
						<div class="dash_line"><!-- 점선 --></div>
						<?php echo _DescStr('대표전화 : 문자 서비스 이용 시 발신번호로 사용되며, 인증절차가 꼭 필요합니다.'); ?>
						<?php echo _DescStr('대표메일 : 발신메일로 사용되며 사이트 도메인과 같은 이메일 사용을 권장합니다.'); ?>
						<?php echo _DescStr('수신 휴대폰 : 주문/문의 등의 주요 문자를 받을 관리자(담당자)의 휴대폰입니다.'); ?>
					</td>
					<th class="">관리자 로그인 노출 정보</th>
					<td>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">전화번호</span>
							<input type="text" name="_login_page_phone" class="design" value="<?php echo $r['s_login_page_phone']; ?>" style="width:185px" placeholder="전화번호">
						</div>
						<div class="lineup-row type_multi">
							<span class="fr_tx" style="width:80px">이메일</span>
							<input type="text" name="_login_page_email" class="design" value="<?php echo $r['s_login_page_email']; ?>" style="width:185px" placeholder="이메일">
						</div>
						<div class="dash_line"><!-- 점선 --></div>
						<?php echo _DescStr('관리자 페이지 로그인 화면에 노출됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>관리자 명칭</th>
					<td>
						<input type="text" name="_reply_writer" class="design" value="<?php echo $r['s_reply_writer']; ?>" style="width:100px" placeholder="관리자 명칭">
						<?php echo _DescStr('상품 리뷰/문의, 게시판 답변에 기본적으로 기재될 명칭입니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<div class="group_title"><strong>사이트 기본설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>사이트명</th>
					<td>
						<input type="text" name="_adshop" class="design" value="<?php echo $r['s_adshop']; ?>" style="width:185px" placeholder="사이트명">
						<?php echo _DescStr("사이트 하단에 노출되며, 페이지별 브라우저 타이틀에 함께 표기 되도록 설정할 수 있습니다."); ?>
					</td>
					<th>대표 도메인</th>
					<td>
						<input type="text" name="_ssl_domain" class="design" value="<?php echo $r['s_ssl_domain']; ?>" style="width:185px" placeholder="대표도메인">
						<div class="tip_box">
							<?php echo _DescStr("http(s)://를 제외한 도메인만 입력해 주시기 바랍니다."); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>통신판매신고번호</th>
					<td>
						<input type="text" name="_company_snum" class="design" value="<?php echo $r['s_company_snum']; ?>" style="width:185px" placeholder="통신판매신고번호">
					</td>

					<th>팩스번호</th>
					<td>
						<input type="text" name="_fax" class="design" value="<?php echo $r['s_fax']; ?>" style="width:185px" placeholder="팩스번호">
					</td>
				</tr>
				<tr>
					<th>개인정보관리책임자</th>
					<td>
						<input type="text" name="_privacy_name" class="design" value="<?php echo $r['s_privacy_name']; ?>" style="width:185px" placeholder="개인정보관리책임자">
						<?php echo _DescStr('사업자등록번호 노출시에만 함께 노출됩니다.',''); ?>
					</td>
					<?php // LCY : 2022-09-22 : 한국진흥원 보안패치 적용 -- 인증 정책 ?>
					<th>호스팅사</th>
					<td>
						<input type="text" name="_hosting_by" class="design" value="<?php echo $r['s_hosting_by']; ?>" style="width:185px" placeholder="호스팅사">
						<?php echo _DescStr('전자상거래법 제10조 1항에 따라 쇼핑몰 하단에 사업자정보와 함께 호스팅 제공자도 함께 표시되어야 합니다.','red'); ?>
						<?php echo _DescStr('이를 위반할 경우 과태료 등 행정처분이 내려질 수 있으니 주의바랍니다.',''); ?>
					</td>
				</tr>
				<tr>
					<th>고객센터 운영시간</th>
					<td>
						<textarea name="_cs_info" rows="4" class="design" placeholder="고객센터 운영시간"><?php echo stripslashes($r['s_cs_info']); ?></textarea>
					</td>
					<th>브라우저 상단색상</th>
					<td>
						<input type="text" name="_mobile_header_color" class="design js_colorpic" value="<?php echo $r['s_mobile_header_color']; ?>" style="width:70px" placeholder="색상 선택" autocomplete="off">
						<?php echo _DescStr('모바일 브라우저 상단 및 바탕에 표시되는 색상을 설정할 수 있습니다.',''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>회사 정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>회사명</th>
					<td>
						<input type="text" name="_company_name" class="design" value="<?php echo $r['s_company_name']; ?>" style="width:185px" placeholder="회사명">
					</td>
					<th>대표자명</th>
					<td>
						<input type="text" name="_ceo_name" class="design" value="<?php echo $r['s_ceo_name']; ?>" style="width:185px" placeholder="대표자명">
					</td>
				</tr>
				<tr>
					<th>사업자등록번호</th>
					<td>
						<div class="lineup-row type_multi">
							<input type="text" name="_company_num" class="design" value="<?php echo $r['s_company_num']; ?>" style="width:185px" placeholder="사업자등록번호">
							<label class="design"><input type="checkbox" name="_view_network_company_info" value="Y"<?php echo ($r['s_view_network_company_info'] == 'Y'?' checked="checked"':null); ?>> 사이트 하단 노출</label>
						</div>
					</td>
					<th>주소</th>
					<td>
						<input type="text" name="_company_addr" class="design" value="<?php echo $r['s_company_addr']; ?>" placeholder="주소">
					</td>
				</tr>
				<tr>
					<th>업태</th>
					<td>
						<input type="text" name="_item1" class="design" value="<?php echo $r['s_item1']; ?>" style="width:185px" placeholder="업태">
					</td>
					<th>종목</th>
					<td>
						<input type="text" name="_item2" class="design" value="<?php echo $r['s_item2']; ?>" style="width:185px" placeholder="종목">
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td colspan="3">
						<?php echo _DescStr('바로빌 서비스 이용 시 세금계산서 발행 정보로 사용되니 정확한 정보로 입력해주세요.',''); ?>
						<?php echo _DescStr('사이트 하단에 회사 정보로 노출됩니다. ',''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>







	<div class="group_title"><strong>사이트 메타태그 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>Title</th>
					<td>
						<input type="text" name="_glbtlt" class="design" value="<?php echo $r['s_glbtlt']; ?>" placeholder="Title">
					</td>
				</tr>
				<tr>
					<th>Description</th>
					<td>
						<textarea name="_glbdsc" rows="4" class="design" placeholder="Description"><?php echo $r['s_glbdsc']; ?></textarea>
						<?php echo _DescStr('사이트를 설명하는 문구를 입력해주시고, 해당 문구는 검색엔진 등록 시 타이틀 밑에 노출됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>Keywords</th>
					<td>
						<textarea name="_glbkwd" rows="4" class="design" placeholder="Keywords"><?php echo $r['s_glbkwd']; ?></textarea>
						<?php echo _DescStr('검색엔진에 도움을 주는 단어나 문구를 콤마(,) 구분으로 입력해주세요.'); ?>
						<?php echo _DescStr('플랫폼마다 검색에 관한 정책이 다를 수 있고, 위 단어가 검색을 보장하는 것은 아닙니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>Meta Tag</th>
					<td>
						<textarea name="_gmeta" rows="4" class="design" placeholder="Meta Tag"><?php echo $r['s_gmeta']; ?></textarea>
						<?php echo _DescStr('네이버 서치어드바이저, 구글마스터 도구 등 메타태그를 사용하실 경우 이용하시기 바랍니다.'); ?>
						<?php echo _DescStr('메타태그 및 자바스크립트 이외 삽입 시 오류를 발생 시킬 수 있습니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 사이트 메타테그 설정 -->


	<?php echo _submitBTNsub(); ?>
</form>



<script type="text/javascript">
	// 비밀번호 변경 체크 동작
	$(document).delegate('.js_change_apw', 'change click', function(e) {
		var is_checked = $(this).is(':checked');
		if(is_checked === true) {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').show();
			$('.js_change_apw_box').find('input').eq(0).focus();
		}
		else {
			$('.js_change_apw_box').find('input').val('');
			$('.js_change_apw_box').hide();
		}
	});

	// 등록 검증
	function validate_check() {

		// 비밀번호 변경 체크
		var pw_change = $('.js_change_apw').is(':checked');
		var pw_length = 6; // 최소 비밀번호 글자수
		if(pw_change === true) {
			var pw = $('.js_pw_input').val();
			var pw_ck = $('.js_pw_ckinput').val();
			if(!pw || !pw_ck) { // 변경 비밀번호 입력 체크
				alert('비밀번호를 입력해 주세요.');
				if(!pw) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw.length < pw_length || pw_ck.length < pw_length) { // 6자리 비밀번호 확인
				alert(pw_length+'자리 이상 영문(대소문자구분)과 숫자를 조합하여 설정할 수 있습니다.');
				if(pw.length < pw_length) $('.js_pw_input').focus();
				else $('.js_pw_ckinput').focus();
				return false;
			}
			if(pw != pw_ck) { // 비밀번호 일치성 확인
				alert('비밀번호와 비밀번호확인이 일치하지 않습니다.');
				$('.js_pw_ckinput').focus();
				return false;
			}
		}
	}
</script>
<?php include_once('wrap.footer.php'); ?>