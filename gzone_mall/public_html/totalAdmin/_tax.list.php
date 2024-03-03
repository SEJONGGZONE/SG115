<?PHP

	include_once("wrap.header.php");

	// 바로빌변수
	include_once(OD_ADDONS_ROOT . '/barobill/include/var.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기


	// 검색 체크
	$s_query = " where 1 and bt_is_delete = 'N' ";


	// -- 검색어
	if($pass_input_type == "corpname"){ // 업체명
		$s_query .= " and CorpName like '%". trim($pass_input) ."%' ";
	}else if( $pass_input_type == "ceo"){ // 대표자명
		$s_query .= " and CEOName like '%". trim($pass_input) ."%' ";
	}else if($pass_input_type == "corpnum"){ // 사업자등록번호
		$s_query .= " and replace(CorpNum, '-','') like '%". trim(str_replace('-','',$pass_input)) ."%' ";
	}else if($pass_input_type == "mgtkey"){ // 문서번호
		 $s_query .= " and replace(MgtKey, '-','') like '%". trim(str_replace('-','',$pass_input)) ."%' ";
	}else if($pass_input_type == "name"){ // 품목명
		$s_query .= " and Name like '%". trim($pass_input) ."%' ";
	}else if( $pass_input_type == 'all'){ // 전체검색
		$s_query .= " and ( CorpName like '%". trim($pass_input) ."%' or CEOName like '%". trim($pass_input) ."%' or replace(CorpNum, '-','') like '%". trim(str_replace('-','',$pass_input)) ."%' or replace(MgtKey, '-','') like '%". trim(str_replace('-','',$pass_input)) ."%' or Name like '%". trim($pass_input) ."%') ";
	}


	// 기본 쿼리 + 검색 조건
	if( $pass_sdate ) { $s_query .= " and if(bt_idate = '0000-00-00 00:00:00', bt_rdate, bt_idate) >= '". $pass_sdate ." 00:00:00' "; }
	if( $pass_edate ) { $s_query .= " and if(bt_idate = '0000-00-00 00:00:00', bt_rdate, bt_idate) <= '". $pass_edate ." 23:59:59' "; }
	if( $pass_tax ) { $s_query .= " and TaxInvoiceType = '". $pass_tax ."' "; }// 과세구분



	// 페이지설정
	$listmaxcount = $listmaxcount ? $listmaxcount : 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from smart_baro_tax $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);


	// 현금영수증 리스트 불러오기
	$que = "
		select
			*
		from smart_baro_tax
		{$s_query}
		ORDER BY bt_uid desc limit $count , $listmaxcount
	";

	$res = _MQ_assoc($que);

?>



<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="st" value="<?php echo $st; ?>">
<input type="hidden" name="so" value="<?php echo $so; ?>">
<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
<input type=hidden name="_state" value="<?=$_state?>">

    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php	if($mode == 'search'){ ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('_state'=>$_state)); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
            <?php } ?>
			<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">포인트확인</a>
            <a href="_tax.form.php<?php echo URI_Rebuild('?', array('_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red">개별발급</a>
        </div>
    </div>

	<div class="js_thisview" style="display:none">
		 <table class="table_form" summary="">
            <colgroup>
                <col width="130"><col width="*">
            </colgroup>
			<tbody>
				<tr class="" >
					<th>참고사항</th>
					<td>
						 <?PHP
							// 상태값 추출
							if($siteInfo[TAX_BAROBILL_ID] && $siteInfo['TAX_CERTKEY']) {
								// 세금계산서 잔여포인트 추출 - return_balance
								//include_once( dirname(__FILE__)."/../addons/barobill/api_ti/_tax.GetBalanceCostAmount.php");

								echo _DescStr('바로빌 잔여포인트: <em class="js_return_balance">조회중입니다.</em>', 'blue');
								echo _DescStr('세금계산서 발행 시 포인트가 소모되며, 바로빌 포인트가 없으면 세금계산서 발행이 되지 않습니다.');
								echo '<script>
											(function(){
												// 바로빌 잔여 포인트 추출
												$.get("/totalAdmin/ajax.simple.php?_mode=getBalanceCostAmount", function( data ) {
													$(".js_return_balance").html("<font class=\"t_blue t_bold\">" + data + "</font>P");
												}, "text");
											})();
										</script>';
							}
						?>
						<?PHP
							// 2018-08-27 SSJ :: 바로빌-현금영수증 문서키 중복 체크를 통해 아이디 유효성 체크
							if($siteInfo['TAX_BAROBILL_ID'] && $siteInfo['TAX_CERTKEY']) {
								echo '<script>
											(function(){
												// check_key 를 이용하여 계정정보 유효성 체크 - 오류 발생시에만 메세지 추가
												$.get("/totalAdmin/ajax.simple.php?_mode=check_key", function( data ) {
													console.log(data);
													if(data != ""){
														$(".js_return_error").html("<div class=\"c_tip red\">※ " + data + "</div>").show();
													}
												}, "text");
											})();
										</script>';
								echo '<div class=" js_return_error" style="display:none;"></div>';
							}
						?>

						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="/totalAdmin/_config.tax.form.php" class="c_btn sky" target="_blank">바로빌 설정 바로가기</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

    <div class="search_form">
        <table class="table_form" summary="">
            <colgroup>
                <col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
            </colgroup>
            <tbody>
                <tr>
					<th>검색어</th>
                    <td>
						<div class="lineup-row type_multi">
							<select name="pass_input_type">
								<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
								<option value="corpname" <?php echo $pass_input_type == 'corpname' ? 'selected' : ''?>>업체명</option>
								<option value="ceo" <?php echo $pass_input_type == 'ceo' ? 'selected' : ''?>>대표자명</option>
								<option value="corpnum" <?php echo $pass_input_type == 'corpnum' ? 'selected' : ''?>>사업자등록번호</option>
								<option value="mgtkey" <?php echo $pass_input_type == 'mgtkey' ? 'selected' : ''?>>문서번호</option>
								<option value="name" <?php echo $pass_input_type == 'name' ? 'selected' : ''?>>품목명</option>
							</select>
							<input type="text" name="pass_input" class="design" style="width:150px" value="<?php echo $pass_input?>" placeholder="검색어" />
						</div>
					</td>
                    <th>발행일</th>
                    <td>
                        <div class="lineup-row type_date">
                            <input type=text name="pass_sdate" class="design js_pic_day" value="<?php echo $pass_sdate?>" style="width:85px;" autocomplete="off" placeholder="날짜 선택">
                            <span class="fr_tx">-</span>
                            <input type=text name="pass_edate" class="design js_pic_day" value="<?php echo $pass_edate?>" style="width:85px;" autocomplete="off" placeholder="날짜 선택">
                        </div>
                    </td>
					<th>과세구분</th>
                    <td>
						<?php echo _InputRadio("pass_tax", array('','1','2'), $pass_tax, "", array('전체','과세','면세'), "")?>
					</td>
                </tr>
            </tbody>
        </table>

        <!-- 가운데정렬버튼 -->
        <div class="c_btnbox">
            <ul>
                <li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
                <?php	if($mode == 'search'){?>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('_state'=>$_state)); ?>" class="c_btn h34 black line normal">전체목록</a></li>
                <?php	} ?>
            </ul>
        </div>

    </div>
</form><!-- end data_search -->






<form name="frm" method="post" action="_cashbill.pro.php" target="common_frame">
<input type="hidden" name="_mode" value="">

		<!-- 리스트영역 -->
		<div class="data_list">

			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="mass_delete_tax(); return false;" class="c_btn h27 black line">선택삭제</a>
					<a href="#none" onclick="mass_issue_tax(); return false;" class="c_btn h27 gray blue">선택발행</a>
				</div>
				<div class="right_box">
					<a href="#none" onclick="mass_print_tax(); return false;" class="c_btn icon icon_print only_pc_view">선택인쇄</a>
				</div>
			</div>


			<table class="table_list" summary="리스트기본">
				<colgroup>
					<col width="40"><col width="70"><col width="130"><col width="*"><col width="*"><col width="280"><col width="100"><col width="150">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">발행상태 / 과세구분</th>
						<th scope="col">공급받는자</th>
						<th scope="col">문서번호</th>
						<th scope="col">품목/발행금액</th>
						<th scope="col">발행일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?PHP
					foreach($res as $k=>$v) {

						// 수정,조회,인쇄 버튼
						if($v['Status']=='0000'){
							$_mod = '<a href="_tax.form.php'. URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['bt_uid'], '_PVSC'=>$_PVSC)) .'" class="c_btn h22 gray">수정</a>';
							$_info = '';
							$_print = '';
						}else{
							$_mod = '';
							$_info = '<a href="#none" onclick="window.open(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'info', '_uid'=>$v['bt_uid'])) .'\', \'tax_print\', \'width=900, height=700\')" class="c_btn h22 blue line">조회</a>';
							$_print = '<a href="#none" onclick="window.open(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'print', '_uid'=>$v['bt_uid'])) .'\', \'tax_print\', \'width=900, height=700\')" class="c_btn h22 gray line only_pc_view">인쇄</a>';
						}

						// 삭제, 취소 버튼
						if(in_array($v['Status'], array('0000','4012','5013','5031'))){// 발행전, 발행거부, 발행취소상태일때 삭제 가능
							$_del = '<a href="#none" onclick="del(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['bt_uid'], '_PVSC'=>$_PVSC)) .'\')" class="c_btn h22 dark line">삭제</a>';
						}else{
							$_del = '<a href="#none" onclick="cancel(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'cancel', '_uid'=>$v['bt_uid'], '_PVSC'=>$_PVSC)) .'\')" class="c_btn h22 dark line">취소</a>';
						}

						// 발행 버튼
						if($v['Status']=='0000'){
							$_issue = '<a href="#none" onclick="issue(\'_tax.pro.php'. URI_Rebuild('?', array('_mode'=>'quick', '_uid'=>$v['bt_uid'], '_PVSC'=>$_PVSC)) .'\')" class="c_btn h22 blue">발행</a>';
						}else{
							$_issue = '';
						}

						$_num = $TotalCount - $count - $k ;
				?>
						<tr>
							<td class="this_check">
								<label class="design"><input type="checkbox" name="_uids[]" value="<?php echo $v['bt_uid'] ?>" class="js_ck"></label>
							</td>
							<td class="this_num"><?php echo $_num; ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php echo $arr_barobill_button[$arr_inner_state_table[$v['Status']]]; ?>
									<?php echo  ($v['TaxInvoiceType'] == 1 ? '<span class="c_tag green line">과세</span>' : '<span class="c_tag green">면세</span>'); ?>
								</div>
							</td>
							<td><?php echo $v['CorpNum']; ?><br/>
								<?php echo $v['CorpName']; ?> (<?php echo $v['CEOName']; ?>)
							</td>
							<td class="t_blue"><span class="hidden_tx">문서번호</span><?php echo ($v['MgtKey']?$v['MgtKey']:'<span class="t_none">발행전</span>'); ?></td>
							<td class="t_left">
								<span class="hidden_tx">품목</span><?php echo ($v['Name']?$v['Name']:'<span class="t_none">미입력</span>'); ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="in_price">
									<ul class="in_thead">
										<li>공급가</li>
										<li>세액</li>
										<li>금액</li>
									</ul>
									<ul>
										<li><?php echo number_format($v['Amount']); ?></li>
										<li><?php echo number_format($v['Tax']); ?></li>
										<li class="t_blue"><?php echo number_format($v['bt_total_price']); ?></li>
									</ul>
								</div>
							</td>
							<td class="this_date"><?php echo ($v['bt_idate']<>'0000-00-00 00:00:00'?printDateInfo($v['bt_idate']):''.printDateInfo($v['bt_rdate']).''); ?></td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<?php echo $_mod; ?>
									<?php echo $_issue; ?>
									<?php echo $_info; ?>
									<?php echo $_print; ?>
									<?php echo $_del; ?>
								</div>
							</td>
						</tr>
				<?php } ?>
				</tbody>
			</table>

			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

	</div>
</form>


<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
</div>



<script>
	// 발행버튼
	function issue($href){
		if(confirm("세금계산서를 발행합니다.\n\n계속 진행하시겠습니까?")) {
			document.location.href = $href;
		}
	}

	// 선택출력하기
	function mass_print_tax(){
		if($('.js_ck:checked').length<1){
			alert('출력할 세금계산서를 선택해주세요.');
			return false;
		}

		document.frm.action= "_tax.pro.php";
		document.frm.target= "tax_print";
		document.frm._mode.value= "mass_print";
		window.open("","tax_print","width=900,height=620");
		document.frm.submit();

	}

	// 선택발행
	function mass_issue_tax(){
		if($('.js_ck:checked').length<1){
			alert('발급할 세금계산서를 선택해주세요.');
			return false;
		}

		document.frm.action= "_tax.pro.php";
		document.frm.target= "";
		document.frm._mode.value= "mass_issue";
		document.frm.submit();
	}

	// 선택삭제
	function mass_delete_tax(){
		document.frm.action= "_tax.pro.php";
		document.frm.target= "";
		document.frm._mode.value= "mass_delete";
		document.frm.submit();
	}

</script>



<?PHP
	include_once("wrap.footer.php");
?>