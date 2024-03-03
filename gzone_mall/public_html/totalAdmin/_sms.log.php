<?php
include_once('wrap.header.php');

// 자동 인스톨 처리
$InstallCK = _MQ_query(' desc smart_sms_log ');
if(!@_MQ_num_rows($InstallCK)) {

	_MQ_noreturn("
		CREATE TABLE  `smart_sms_log` (
			`idx` INT( 11 ) NOT NULL AUTO_INCREMENT COMMENT  '고유키',
			`code` VARCHAR( 5 ) NOT NULL COMMENT  '에러코드',
			`msg` VARCHAR( 255 ) NOT NULL COMMENT  '에러메시지',
			`send_num` VARCHAR( 20 ) NOT NULL COMMENT  '보내는 번호',
			`receive_num` VARCHAR( 20 ) NOT NULL COMMENT  '받는번호',
			`rdate` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  '기록일',
			PRIMARY KEY (  `idx` )
		) ENGINE = MYISAM COMMENT =  'SMS 발송 에러로그'
	");
}

// 링크 넘김 변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);

// 검색쿼리
$s_query = '';
if($pass_sdate && $pass_edate) $s_query .= " AND date(rdate) between '{$pass_sdate}' and '{$pass_edate}' "; // 범위 기간검색
else if($pass_sdate) $s_query .= " AND date(rdate) >= '{$pass_sdate }' "; // 시작일 기간검색
else if($pass_edate) $s_query .= " AND date(rdate) <= '{$pass_edate}' "; // 종료일 기간검색
if($pass_send) $s_query .= " and replace(send_num, '-', '') like '%".str_replace('-', '', $pass_send)."%' "; // 발신번호 검색
if($pass_receive) $s_query .= " and replace(receive_num, '-', '') like '%".str_replace('-', '', $pass_receive)."%' "; // 수신번호 검색
if($pass_code) $s_query .= " and code = '{$pass_code}' "; // 에러코드 검색

$listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_sms_log where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc("select * from smart_sms_log where (1) {$s_query} order by idx desc limit {$count}, {$listmaxcount} ");


//$SMSUser = onedaynet_sms_user();
$ErrCodeIcon = array(
	'S01'=>'black',
	'S02'=>'red',
	'S03'=>'yellow',
	'S04'=>'purple',
	'S05'=>'violet',
	'S06'=>'brown',
	'S07'=>'ygreen',
	'S08'=>'darkgreen',
	'S09'=>'black',
	'S70'=>''
);
?>

<!-- 검색 -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
    <input type="hidden" name="_mode" value="search">

    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php if($_mode == 'search'){ ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
            <?php } ?>
			<div class="btn_box"><a href="#none" onclick="$('.js_tip_table').toggle(); return false;" class="c_btn h24 sky line">에러코드 안내</a></div>
        </div>
    </div>

	<div class="open_excel js_tip_table" style="display:none">
		<table class="table_list type_inside">
			<colgroup>
				<col width="100"/><col width="350"/><col width="*"/><col width="120"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">코드</th>
					<th scope="col">메시지</th>
					<th scope="col">상세설명</th>
					<th scope="col">설정변경</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S01']?' '.$ErrCodeIcon['S01']:' light'); ?>" >S01</span></td>
					<td class="t_left">아이디 또는 비밀번호가 누락되었습니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('SMS 설정에 모비톡 아이디 또는 비밀번호가 빠진 상태'); ?>
						</div>
					</td>
					<td><a href="_config.sms.form.php" class="c_btn gray line" >수정하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S02']?' '.$ErrCodeIcon['S02']:' light'); ?>" >S02</span></td>
					<td class="t_left">잘못된 계정정보입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('모비톡 계정이 존재하지 않거나 잘못된 정보로 설정된 상태'); ?>
						</div>
					</td>
					<td><a href="_config.sms.form.php" class="c_btn gray line" >수정하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S03']?' '.$ErrCodeIcon['S03']:' light'); ?>" >S03</span></td>
					<td class="t_left">등록되지 않은 아이피 입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('홈페이지 아이피가 모비톡에 설정되지 않은 상태'); ?>
							<?php echo _DescStr('현재 아이피 : '.$_SERVER['SERVER_ADDR'].''); ?>
						</div>
					</td>
					<td class="only_pc_view"><a href="http://mobitalk.gobeyond.co.kr" class="c_btn sky line" target="_blank">추가하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S04']?' '.$ErrCodeIcon['S04']:' light'); ?>" >S04</span></td>
					<td class="t_left">유효하지 않은 발신번호 입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('발신번호 형식이 올바르지 않은 상태'); ?>
						</div>
					</td>
					<td><a href="_config.default.form.php" class="c_btn gray line" >수정하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S05']?' '.$ErrCodeIcon['S05']:' light'); ?>" >S05</span></td>
					<td class="t_left">발신번호 등록 후 이용가능 합니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('모비톡에 발신번호 등록이 되어있지 않은 상태'); ?>
						</div>
					</td>
					<td class="only_pc_view"><a href="http://mobitalk.gobeyond.co.kr/pages/customer_modify.form.php" class="c_btn sky line" target="_blank">서류인증</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S06']?' '.$ErrCodeIcon['S06']:' light'); ?>" >S06</span></td>
					<td class="t_left">발신번호 상태가 (대기/반려/만료) 입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('모비톡에 등록된 발신번호 인증 상태가 대기, 반려, 만료 상태'); ?>
						</div>
					</td>
					<td><a href="https://www.onedaynet.co.kr/p/service.inqury_list.html" class="c_btn sky" target="_blank">문의하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S07']?' '.$ErrCodeIcon['S07']:' light'); ?>" >S07</span></td>
					<td class="t_left">유효하지 않은 수신번호 입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('수신자번호 형식이 올바르지 않은 상태'); ?>
						</div>
					</td>
					<td><a href="_individual.list.php" class="c_btn gray line">회원검색</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S08']?' '.$ErrCodeIcon['S08']:' light'); ?>" >S08</span></td>
					<td class="t_left">잔여 건수가 부족합니다. (잔여: X건)</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('모비톡의 문자발송 건수가 부족하여 발송하지 못하는 상태'); ?>
						</div>
					</td>
					<td><a href="_config.sms.out_list.php" class="c_btn gray line" >충전하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S09']?' '.$ErrCodeIcon['S09']:' light'); ?>" >S09</span></td>
					<td class="t_left">모비톡의 문자내용에 스팸성문구가 포함되어 있는 경우입니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('발송하려는 문자에 스팸성 문구가 포함되어 발송되지 않은 상태'); ?>
						</div>
					</td>
					<td><a href="https://www.onedaynet.co.kr/p/service.notice_view.html?board=service.notice_list&_pass_var_code=JnNlYXJjaF9jb250ZW50PWNvbnRlbnQmc2VhcmNoSW5kZXg966y47J6Q&serialnum=60827" class="c_btn sky" target="_blank">확인하기</a></td>
				</tr>
				<tr>
					<td><span class="c_tag h22<?php echo ($ErrCodeIcon['S70']?' '.$ErrCodeIcon['S70']:' light'); ?>" >S70</span></td>
					<td class="t_left">발신프로필 또는 템플릿 등록상에 오류가 있습니다.</td>
					<td class="t_left">
						<div class="tip_box">
							<?php echo _DescStr('모비톡 발신프로필 오류 또는 템플릿 연동 고유번호 등의 오류'); ?>
						</div>
					</td>
					<td><a href="http://mobitalk.gobeyond.co.kr" class="c_btn sky line" target="_blank">확인하기</a></td>
				</tr>
			</tbody>
		</table>
	</div>

    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>에러코드</th>
					<td>
						<?php echo _InputSelect('pass_code', array_keys($ErrCodeIcon), $pass_code, '', array_keys($ErrCodeIcon), '전체'); ?>
					</td>
					<th>검색기간</th>
					<td>
                        <div class="lineup-row type_date">
                            <input type="text" name="pass_sdate" class="design js_datepic_max_today" value="" autocomplete="off" placeholder="날짜 선택" style="width:85px" readonly>
                            <span class="fr_tx">-</span>
                            <input type="text" name="pass_edate" class="design js_datepic_max_today" value="" autocomplete="off" placeholder="날짜 선택" style="width:85px" readonly>
                        </div>
					</td>
				</tr>
				<tr>
					<th>발신번호</th>
					<td>
						<input type="text" name="pass_send" class="design" value="<?php echo $pass_send; ?>" style="width:110px" placeholder="발신번호">
					</td>
					<th>수신번호</th>
					<td>
						<input type="text" name="pass_receive" class="design" value="<?php echo $pass_receive; ?>" style="width:110px" placeholder="수신번호">
					</td>
				</tr>
			</tbody>
		</table>

		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색"></span></li>
				<?php if($_mode == 'search'){ ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form><!-- end data_search -->


<div class="data_list">
	<table class="table_list type_nocheck">
		<colgroup>
			<col width="70"/><col width="100"/><col width="*"/><col width="150"/><col width="150"/><col width="150"/>
		</colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>코드</th>
				<th>메시지</th>
				<th>발신번호</th>
				<th>수신번호</th>
				<th>발생일</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($r as $k=>$v) { $_num = $TotalCount-$count-$k; ?>
				<tr>
					<td class="this_num"><?php echo $_num; ?></td>
					<td class="this_ctrl"><span class="c_tag h22<?php echo ($ErrCodeIcon[$v['code']]?' '.$ErrCodeIcon[$v['code']]:' light'); ?>" style="float:none"><?php echo $v['code']; ?></span></td>
					<td style="text-align:left"><?php echo $v['msg']; ?></td>
					<td><span class="hidden_tx">발신번호</span><?php echo $v['send_num']; ?></td>
					<td><span class="hidden_tx">수신번호</span><?php echo $v['receive_num']; ?></td>
					<td class="this_date"><?php echo printDateInfo($v['rdate']); ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if(count($r) <= 0) { ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>

	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, "?&{$_PVS}&listpg=", 'Y'); ?>
	</div>
</div>
<?php include_once('wrap.footer.php'); ?>