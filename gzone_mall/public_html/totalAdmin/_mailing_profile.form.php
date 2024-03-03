<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_mailing_data.list.php';
include_once('wrap.header.php');


	if( !$_mduid ) {
		error_msg("잘못된 접근입니다.");
	}

	$row = _MQ("select * from smart_mailing_data where md_uid='${_mduid}' ");

	// 저장한 정보 불러오기 --> $app_profile 로 저장됨
	include_once("..".IMG_DIR_NORMAL."/mailing.profile.php");
	$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));
	$_cnt = sizeof($ex_app_profile);

?>


<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_mailing_profile.pro.php" class="list_ctrl">
	<input type=hidden name="_mode" value="<?php echo $_mode; ?>">
	<input type=hidden name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type=hidden name="_cnt" value="<?php echo $_cnt; ?>" hname="메일링 적용된 회원" required>
	<input type=hidden name="_mduid" value="<?php echo $_mduid; ?>">
	<div class="left_box">
		<a href="#none" onclick="window.open('_mailing_profile.individual_list.php', 'individual_popup', 'width=900,height=800,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');" class="c_btn h27 blue">+ 회원추가</a>
		<?php echo _submitBTN($app_current_link,null,'',false); ?>
	</div>
	<div class="right_box">
		<a href="#none" onclick="del('_mailing_profile.pro.php?_mduid=<?php echo $_mduid; ?>&_mode=profile_delete&_PVSC=<?php echo $_PVSC; ?>');" class="c_btn h27 black line">검색 회원(<font id="app_cnt"><?php echo number_format($_cnt); ?></font>명) 삭제</a>
	</div>
	<?php echo _DescStr('회원을 추가한 후 확인을 눌러주세요! ','red'); ?>
</form><!-- end list_ctrl -->

<div class="data_list">
	<table class="table_list type_nocheck">
		<colgroup>
			<col width="80"><col width="*"><col width="120"><col width="150"><col width="110"><col width="90">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">No</th>
				<th scope="col">수신자</th>
				<th scope="col">발송상태</th>
				<th scope="col">발송일자</th>
				<th scope="col">등록일</th>
				<th scope="col">삭제</th>
			</tr>
		</thead>
		<tbody>
		<?PHP
			$mpres = _MQ_assoc(" select  * from smart_mailing_profile where mp_mduid='{$_mduid}' ORDER BY mp_uid desc ");
			if(sizeof($mpres) < 1) echo "<tr><td colspan='6'><div class='common_none'><div class='no_icon'></div><div class='gtxt'>발송 내역이 없습니다.</div></div></tr></td>";

			foreach($mpres as $k=>$mpr){
				$_num = sizeof($mpres) - $k ;
				echo "
					<tr>
						<td class='this_num'>". $_num ."</td>
						<td class='t_left'>" . str_replace(","," / ",cutstr_new($mpr['mp_email'],1000 , "...")). "</td>
						<td>
							" . ($mpr['mp_status']=="Y" ?
									"<span class='c_tag blue h18 line'>발송완료</span>"
									:
									"<a href='#none' onclick='send_mailing(".$mpr['mp_uid'].")' class='c_btn h22 blue'>지금 발송하기</a>"
							) . "
						</td>
						<td class='this_state'>
							" . ($mpr['mp_status']=="Y" ? printDateInfo($mpr['mp_sdate']) : '<span class="c_tag h18 gray line">미발송</span>') . "
						</td>
						<td class='this_date'>" . printDateInfo($mpr['mp_rdate']) . "</td>
						<td class='this_ctrl'>
							" . ($mpr['mp_status']<>"Y" ?
									'<a href="#none" onclick="del(\'_mailing_profile.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_mduid'=>$_mduid, '_mpuid'=>$mpr['mp_uid'], '_PVSC'=>$_PVSC)) .'\')" class="c_btn h22 dark line">삭제하기</a>'
									:
									'<span class="c_tag h18 light">삭제불가</span>'
							) . "
						</td>
					</tr>
				";
			}
		?>
		</tbody>
	</table>

</div>



<div class="group_title"><strong>메일링 미리보기</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>제목</th>
				<td>
					<?php echo $row['md_title']; ?>
				</td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<div class="editor">
						<?php echo stripslashes($row['md_content']); ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>



<script>
	// 메일발송하기
	function send_mailing(uid) {

		if(!confirm("발송하시겠습니까?")) return false;

		common_frame.location.href="_mailing_profile.pro.php?_mode=sendpro&_mduid=<?php echo $_mduid; ?>&_uid="+uid;

	}
</script>

<?php include_once('wrap.footer.php'); ?>