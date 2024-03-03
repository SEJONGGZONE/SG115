<?php
	include_once('wrap.header.php');
?>


<form name="frm" method='post' action='_config.ssl.pro.php' ENCTYPE='multipart/form-data'>
<input type='hidden' name='pass_menu' value='_config.ssl.default_form'>

	<!--보안서버 설정 -->
	<div class="group_title"><strong>기본정보 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">보안서버 사용여부</th>
					<td colspan='3'>
						<?php
							echo _InputRadio( "_ssl_check" , array("Y","N") ,  (!$siteInfo['s_ssl_check'] ? "N" : $siteInfo['s_ssl_check'] ) , "" , array("사용","미사용") , "");
							echo '
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="tip_box">
									' . _DescStr("
										민원이 발생할 경우 정보통신망법에 따라 보안서버(SSL) 구축 의무사항 위반 시 사전경고 없이 과태료가 부과될 수 있습니다.
									") . '
									' . _DescStr("
										로그인, 회원가입, 게시판, 주문, 결제 시 이름, 전화번호 등을 취급하는 사이트가 이에 해당합니다.
									") . '
								</div>
							';
						?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="http://www.onedaynet.co.kr/p/add_05.html" target='_blank' class="c_btn h27 sky">SSL 보안서버 안내 및 신청</a>
					</td>
				</tr>
				<tr>
					<th>보안서버 진행상태</th>
					<td>
						<?=_InputSelect( "_ssl_status" , array('대기' , '진행' , '만료') , $siteInfo['s_ssl_status'] , "" , "" , "-진행상태-")?>
						<div class="tip_box">
							<?=_DescStr("진행일 경우에만 보안서버를 사용할 수 있습니다.")?>
						</div>
					</td>
					<th>보안서버 사용기간</th>
					<td>
						<div class="lineup-row type_date">
							<input type="text" name="_ssl_sdate" class="design js_pic_day" value="<?php echo $siteInfo['s_ssl_sdate']!='0000-00-00'?$siteInfo['s_ssl_sdate']:''; ?>" style="width:90px; cursor:pointer;" readonly placeholder="날짜 선택">
							<span class="fr_tx">~</span>
							<input type="text" name="_ssl_edate" class="design js_pic_day" value="<?php echo $siteInfo['s_ssl_edate']!='0000-00-00'?$siteInfo['s_ssl_edate']:''; ?>" style="width:90px; cursor:pointer;" readonly placeholder="날짜 선택">
						</div>
						<div class="tip_box">
							<?=_DescStr("사용기간 이전일 경우 대기로, 이후일 경우 만료로변경됩니다.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th>도메인</th>
					<td>
						<div class="lineup-row type_multi">
							<span class="fr_tx">https://</span>
							<input type="text" name="" class="design" value="<?php echo $siteInfo['s_ssl_domain']; ?>" style="width:300px" readonly placeholder="도메인" disabled>
						</div>
						<div class="tip_box">
							<?php
								if($siteInfo['s_ssl_domain'] == ""){
									echo _DescStr("<em>대표도메인</em>이 등록되지 않았습니다.", "red");
									echo '<div class="dash_line"></div><a href="_config.default.form.php" target="_blank" class="c_btn h27 black">대표도메인 설정 바로가기</a>';
								}else{
									echo _DescStr("<em>환경설정 > 기본설정 > 쇼핑몰 기본정보</em>의 대표도메인과 동일합니다.");
								}
							?>
						</div>
					</td>
					<th>포트번호</th>
					<td>
						<input type="text" name="_ssl_port" class="design" value="<?php echo $siteInfo['s_ssl_port']; ?>" style="width:50px" placeholder="포트번호">
						<?=_DescStr("기본포트(443)외에는 도메인 뒤에 포트번호가 노출될 수 있습니다.")?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 보안서버 설정 -->


	<!--보안서버 인증정보 설정 -->
	<div class="group_title"><strong>보안서버 인증정보 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">Seal Number</th>
					<td >
						<input type="text" name="_ssl_pc_sealnum" class="design" value="<?=$siteInfo['s_ssl_pc_sealnum']?>" style="width:400px" placeholder="Seal Number">
						<div class="tip_box">
							<?=_DescStr("원데이넷을 통해 신청하신 경우 입력해주세요.")?>
						</div>
					</td>
					<th class="">Seal ID</th>
					<td >
						<input type="text" name="_ssl_pc_sealid" class="design" value="<?=$siteInfo['s_ssl_pc_sealid']?>" style="width:600px" placeholder="Seal ID">
						<div class="tip_box">
							<?=_DescStr("원데이넷을 통해 신청하신 경우 입력해주세요.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="">인증서 선택</th>
					<td colspan="3">
						<div class="label_box">
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgN" value="N" <?=( !$siteInfo['s_ssl_pc_img'] || $siteInfo['s_ssl_pc_img'] == 'N' ? 'checked' : '' )?>>미사용</label>
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgC" value="C" <?=( $siteInfo['s_ssl_pc_img'] == 'C' ? 'checked' : '' )?>>SECTIGO<img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/ssl_positive.png" style="height:50px;" alt="SECTIGO"></label>
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgU" value="U" <?=( $siteInfo['s_ssl_pc_img'] == 'U' ? 'checked' : '' )?>>UCERT SSL <img src="https://www.ucert.co.kr/images/maincenterContent/trustlogo/ucert_black.gif" style="height:50px;" alt="UCERT SSL"></label>
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgK" value="K" <?=( $siteInfo['s_ssl_pc_img'] == 'K' ? 'checked' : '' )?>>KISA SSL<img src="https://www.ucert.co.kr/image/trustlogo/s_kisa.gif" style="height:50px;" alt="KISA SSL"></label>
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgA" value="A" <?=( $siteInfo['s_ssl_pc_img'] == 'A' ? 'checked' : '' )?>>Alpha SSL<img src="https://www.ucert.co.kr/image/trustlogo/alphassl_seal.gif" style="height:50px;" alt="Alpha SSL"></label>
							<label class="design"><input type="radio" name="_ssl_pc_img" ID="_ssl_pc_imgE" value="E" <?=( $siteInfo['s_ssl_pc_img'] == 'E' ? 'checked' : '' )?>>기타</label>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box"><?=_DescStr("설정에 따라 지정된 사용자페이지 하단에 노출됩니다.")?></div>
					</td>
				</tr>

				<tr class="auth_view_etc" style="<?=( $siteInfo['s_ssl_pc_img'] <> 'E' ? 'display:none !important;' : '' )?>">
					<th class="">기타 인증이미지 소스</th>
					<td colspan="3">
						<textarea name="_ssl_pc_img_etc" rows="3" cols="" class="design" placeholder="보안서버 기타 인증이미지 소스"><?=$siteInfo['s_ssl_pc_img_etc']?></textarea>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?=_DescStr("보안서버 인증이미지를 기타로 선택할 경우 클릭시 링크를 포함한 인증이미지 소스를 입력하시기 바랍니다.");?>
							<?=_DescStr("소스에 대한 예는 다음과 같습니다.<br><br>&lt;!--KISA Certificate Mark--&gt;<br>&lt;img src=\"https://www.ucert.co.kr/image/trustlogo/s_kisa.gif\" width=\"65\" height=\"63\" align=\"absmiddle\" border=\"0\" style=\"cursor:pointer\" <br>Onclick=javascript:window.open(\"https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum={실넘버}&sealid={실아이디}\",\"mark\",\"scrollbars=no,resizable=no,width=565,height=780\");&gt;<br>&lt;!--KISA Certificate Mark--&gt;");?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
	<!-- 보안서버 인증정보 설정 -->

	<?php echo _submitBTNsub(); ?>

</form>



<script>
	/*  ON/OFF ---------- */
	var onoff = function() {
		if($("input[name='_ssl_pc_img']").filter(function() {if (this.checked) return this;}).val() == "E") {
			$(".auth_view_etc").show();
			$(".auth_view_seal").hide();
		}
		else {
			$(".auth_view_etc").hide();
			$(".auth_view_seal").show();
		}
	}
	$("input[name='_ssl_pc_img']").click(function() {onoff();});
	/*  ON/OFF ---------- */
</script>




<?php
	include_once('wrap.footer.php');
?>