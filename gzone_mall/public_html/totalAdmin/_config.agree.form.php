<?php
	include_once('wrap.header.php');
	$arr_policy = arr_policy('all');
?>

<form name="frm" method="post" action="_config.agree.pro.php" enctype="multipart/form-data">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">

		<div class="c_tab type_col3">
			<ul>
				<li class="hit"><a href="#none" class="btn tab_menu" data-idx="guideinfo" data-trigger="N"><strong>이용약관</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="join" data-trigger="N"><strong>가입약관</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="privacy" data-trigger="Y"><strong>개인정보처리방침</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="join_add" data-trigger="N"><strong>가입 추가약관</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="agree" data-trigger="N"><strong>개인정보수집 및 이용</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="deny" data-trigger="Y"><strong>이메일무단수집거부</strong></a></li>
			</ul>
		</div>

		<table class="table_form tab_conts" data-idx="guideinfo">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>이용약관</th>
					<td>
						<?php $_appname = 'agree_html_m'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design SEditor" style="width:100%;height:300px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
				<tr>
					<th>이용약관(회원가입)</th>
					<td>
						<?php $_appname = 'agree'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design" style="width:100%;height:300px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>

			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="privacy" style="display:none;">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>개인정보처리방침</th>
					<td>
						<?php $_appname = 'privacy_html_m'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design SEditor" style="width:100%;height:300px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="join" style="display:none;">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>회원가입 약관</th>
					<td>
						<?php $_appname = 'join_privacy'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design" style="width:100%;height:300px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="join_add" style="display:none;">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>[선택]개인정보수집 및 이용 동의</th>
					<td>
						<?php $_appname = 'join_optional'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<?php echo _InputRadio( 'arr_policy['. $_appname .'][po_use]' , array('Y', 'N'), ($arr_policy[$_appname]['po_use']?$arr_policy[$_appname]['po_use']:'N') , '' , array('사용', '미사용')); ?>
						<a href="#none" class="c_btn h27 blue line js_policy_add_btn" data-name="<?php echo $_appname; ?>">+ 추가하기</a>
						<?php if(sizeof($arr_policy[$_appname]['data']) > 0){ foreach($arr_policy[$_appname]['data'] as $k=>$v){ ?>
							<div class="js_policy_line ">
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_uid]" value="<?php echo $v['po_uid']; ?>">
									<input type="text" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_title]" class="design" value="<?php echo $v['po_title']; ?>" style="width:600px">
									<a href="#none" class="c_btn h27 gray js_policy_del_btn">- 삭제</a>
								</div>
								<div class="dash_line"><!-- 점선라인 --></div>
								<textarea name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_content]" class="design" style="width:100%;height:200px;"><?php echo $v['po_content']?></textarea>
								<?php echo _DescStr('치환자:
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
								'); ?>
							</div>
						<?php }} ?>
					</td>
				</tr>
				<tr>
					<th>[선택] 개인정보 처리ㆍ위탁 동의</th>
					<td>
						<?php $_appname = 'join_csinfo'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<?php echo _InputRadio( 'arr_policy['. $_appname .'][po_use]' , array('Y', 'N'), ($arr_policy[$_appname]['po_use']?$arr_policy[$_appname]['po_use']:'N') , '' , array('사용', '미사용')); ?>
						<a href="#none" class="c_btn h27 blue line js_policy_add_btn" data-name="<?php echo $_appname; ?>">+ 추가하기</a>
						<?php if(sizeof($arr_policy[$_appname]['data']) > 0){ foreach($arr_policy[$_appname]['data'] as $k=>$v){ ?>
							<div class="js_policy_line">
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_uid]" value="<?php echo $v['po_uid']; ?>">
									<input type="text" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_title]" class="design" value="<?php echo $v['po_title']; ?>" style="width:600px">
									<a href="#none" class="c_btn h27 gray js_policy_del_btn">- 삭제</a>
								</div>
								<div class="dash_line"><!-- 점선라인 --></div>
								<textarea name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_content]" class="design" style="width:100%;height:200px;"><?php echo $v['po_content']?></textarea>
								<?php echo _DescStr('치환자:
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
								'); ?>
							</div>
						<?php }} ?>
					</td>
				</tr>
				<tr>
					<th>[선택] 개인정보 제3자 제공 동의</th>
					<td>
						<?php $_appname = 'join_thirdinfo'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<?php echo _InputRadio( 'arr_policy['. $_appname .'][po_use]' , array('Y', 'N'), ($arr_policy[$_appname]['po_use']?$arr_policy[$_appname]['po_use']:'N') , '' , array('사용', '미사용')); ?>
						<a href="#none" class="c_btn h27 blue line js_policy_add_btn" data-name="<?php echo $_appname; ?>">+ 추가하기</a>
						<?php if(sizeof($arr_policy[$_appname]['data']) > 0){ foreach($arr_policy[$_appname]['data'] as $k=>$v){ ?>
							<div class="js_policy_line">
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_uid]" value="<?php echo $v['po_uid']; ?>">
									<input type="text" name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_title]" class="design" value="<?php echo $v['po_title']; ?>" style="width:600px">
									<a href="#none" class="c_btn h27 gray js_policy_del_btn">- 삭제</a>
								</div>
								<div class="dash_line"><!-- 점선라인 --></div>
								<textarea name="arr_policy[<?php echo $_appname; ?>][data][<?php echo $k; ?>][po_content]" class="design" style="width:100%;height:200px;"><?php echo $v['po_content']?></textarea>
								<?php echo _DescStr('치환자:
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
									<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
									<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
								'); ?>
							</div>
						<?php }} ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="agree" style="display:none;">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>[필수] 개인정보수집 및 이용 동의(비회원 주문)</th>
					<td>
						<?php $_appname = 'guest_order'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design" style="width:100%;height:200px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
				<tr>
					<th>[필수] 개인정보수집 및 이용 동의(비회원 글쓰기)</th>
					<td>
						<?php $_appname = 'guest_board'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design" style="width:100%;height:200px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
				<tr>
					<th>[필수] 개인정보수집 및 이용 동의(제휴문의)</th>
					<td>
						<?php $_appname = 'partner_agree'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design" style="width:100%;height:200px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="table_form tab_conts" data-idx="deny" style="display:none;">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>이메일무단수집거부</th>
					<td>
						<?php $_appname = 'deny_html_m'; ?>
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_uid]" value="<?php echo $arr_policy[$_appname]['po_uid']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][type]" value="<?php echo $arr_policy[$_appname]['type']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_use]" value="<?php echo $arr_policy[$_appname]['po_use']; ?>">
						<input type="hidden" name="arr_policy[<?php echo $_appname; ?>][po_title]" value="<?php echo $arr_policy[$_appname]['po_title']; ?>">
						<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
						<textarea name="arr_policy[<?php echo $_appname; ?>][po_content]" class="design SEditor" style="width:100%;height:200px;"><?=stripslashes($arr_policy[$_appname]['po_content'])?></textarea>
						<?php echo _DescStr('치환자:
							<a href="#none" class="js_insert_text"><u>[게시일::이메일무단수집거부]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용 최소금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::사용한도]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::회원가입 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::구매 적립일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급금액]</u></a>,
							<a href="#none" class="js_insert_text"><u>[적림급 설정::포토후기 지급일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[상품/배송::택배사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[통합 전자결제(PG) 관리::PG사]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::고객센터 대표번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표 이메일]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::사이트명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::회사명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::대표자명]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::통신판매신고번호]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::업태]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::종목]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::개인정보관리책임자]</u></a>,
							<a href="#none" class="js_insert_text"><u>[쇼핑몰 기본정보::로그인 시도횟수]</u></a>
						'); ?>
					</td>
				</tr>
			</tbody>
		</table>

	</div>


	<?php echo _submitBTNsub(); ?>


</form>

<script>
	// 텝메뉴
	$(document).on('click', '.tab_menu', function() {
		$parent = $(this).closest('.data_form');
		var idx = $(this).data('idx');
		// 탭변경
		$parent.find('.tab_menu').closest('li').removeClass('hit');
		$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$parent.find('.tab_conts').hide();
		$parent.find('.tab_conts[data-idx='+ idx +']').show();

		// 부모창이 display:none; 일때 높이 오류 수정
		var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
		if(trigger_cont_editor){
			$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
				var id = $(this).attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
				}
			});
			$(this).data('trigger','N');
		}
	});

	// 약관설정 항목 추가 2017-09-13 SSJ
	var idx = 9999; // 중복되지 않도록 충분히 큰수로 지정
	$(document).on('click', '.js_policy_add_btn', function(){
		idx++;

		$wrap = $(this).closest('td');
		var name = $(this).data('name');
		var _html = '';
			_html += '<div class="js_policy_line lineup-column">';
			_html += '	<div class="dash_line"><!-- 점선라인 --></div>';
			_html += '	<div class="lineup-row type_multi"><input type="hidden" name="arr_policy['+name+'][data]['+idx+'][po_uid]" value="">';
			_html += '	<input type="text" name="arr_policy['+name+'][data]['+idx+'][po_title]" class="design" value="" style="width:600px">';
			_html += '	<a href="#none" class="c_btn h27 gray js_policy_del_btn">- 삭제</a></div>';
			_html += '	<div class="dash_line"><!-- 점선라인 --></div>';
			_html += '	<textarea name="arr_policy['+name+'][data]['+idx+'][po_content]" class="design" style="width:100%;height:200px;"></textarea>';
			_html += '</div>';
		$wrap.append(_html);
	});



	// 약관설정 항목 삭제 2017-09-13 SSJ
	$(document).on('click', '.js_policy_del_btn', function(){
		if(confirm('약관을 삭제하면 회원가입시 해당 약관에 동의한 내역도 모두 삭제됩니다.\n\n정말 삭제하시겠습니까?')){
			$this = $(this).closest('.js_policy_line');
			$this.remove();
		}
	});
	function policy_delete(obj){
	}
</script>

<?php include_once('wrap.footer.php'); ?>