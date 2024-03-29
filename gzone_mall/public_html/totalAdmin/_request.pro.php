<?PHP
	include "./inc.php";


	if( in_array( $_mode , array('add' , 'modify') ) ){

		// --사전 체크 ---
		$_menu = nullchk($_menu , "메뉴를 선택해주시기 바랍니다.");
		// --사전 체크 ---

		// -- 등록한 파일명 ---
		//$_file_name = _FilePro( "../upfiles/normal" , "_file" ) ;
		// -- 등록한 이미지명 ---

		// --query 사전 준비 ---
		$sque = "
			 r_menu = '". $_menu ."'
			,r_comname = '". $_comname ."'
			,r_email = '". $_email ."'
			,r_tel = '". $_tel ."'
			,r_hp = '". $_hp ."'
			,r_status = '". $_status ."'
			,r_title = '". $_title ."'
			,r_content = '". $_content ."'
			,r_admcontent = '". $_admcontent ."'
			,r_file = '". $_file_name ."'
		";
		if($_menu == "normal_pt" && $_title ) {
			$sque .= " ,r_title = '". $_title ."' ";
		}
		// --query 사전 준비 ---

	}

	// 문의하기 정보 추출
	$r = _MQ(" select * from smart_request where r_uid='{$_uid}' ");


	// - 모드별 처리 ---
	switch( $_mode ){

		// 설정저장
		case "config":
			$que = "
				update smart_setup set
					s_request_partner_view = '". $_request_partner_view ."'
			";
			_MQ_noreturn($que);
			error_loc_msg("_request.list.php?pass_menu={$pass_menu}&".enc('d' , $_PVSC ),"설정이 저장되었습니다.");
			break;


		case "add":
			$que = " insert smart_request set $sque , r_rdate = now() ";
			_MQ_noreturn($que);
			$_uid = _MQ_insert_id();

			// 첨부파일 저장
			odtFileUpload("addFile","smart_request",$_uid);

			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "modify":
			$r = _MQ(" select * from smart_request where r_uid='{$_uid}' ");

			$que = " update smart_request set $sque ". ($_status == "답변완료" ? " , r_admdate = now()" : "")." where r_uid='{$_uid}' ";
			_MQ_noreturn($que);

			// 첨부파일 저장
			odtFileUpload("addFile","smart_request",$_uid);
			odtFileUpload("modifyFile","smart_request",$_uid);

			// 제휴문의일 경우 답변을 메일로 발송
			if($_status=='답변완료' && $_sendmail == 'Y' && in_array($_menu,array('partner'))) { // 2019-04-09 SSJ :: 답변완료체크, 메일발송 체크 시 항상 메일발송(이전에는 답변대기->답변완료 변경시에 발송됐음)
				// - 메일발송 ---
				$_oemail = $_email;
				if( mailCheck($_oemail) ){
		            include_once(OD_MAIL_ROOT."/service.request.mail.php"); // 메일 내용 불러오기 ($mailing_content)
					$_title = "[".$siteInfo[s_adshop]."] 제휴문의에 관해 답변드립니다.";
					$_content = get_mail_content($mailling_content);
					// -- 메일 발송
					mailer( $_oemail, $_title , $_content );
				}
				// - 메일발송 ---
			}

			error_loc("_request.form.php?pass_menu={$pass_menu}&_mode=${_mode}&_uid=${_uid}&_PVSC=${_PVSC}");
			break;


		case "delete":
			//// 이미지 삭제
			//_FileDel( "../upfiles/normal" , $r[r_file]);

			// -- 파일삭제(데이터,첨부파일) - 관리자
			$resBoardFiles = _MQ_assoc("select f_realname from smart_files where f_table_uid = {$_uid} and f_table = 'smart_request'   ");
			foreach($resBoardFiles as $k=>$v){
				deleteFiles($v['f_realname']);
			}
			_MQ_noreturn("delete  from smart_files where  f_table_uid = {$_uid} and f_table = 'smart_request'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제

			// -- 파일삭제(데이터,첨부파일) - 사용자
			$resBoardFiles = _MQ_assoc("select f_realname from smart_files where f_table_uid = '{$_uid}_user' and f_table = 'smart_request'   ");
			foreach($resBoardFiles as $k=>$v){
				deleteFiles($v['f_realname']);
			}
			_MQ_noreturn("delete  from smart_files where  f_table_uid = '{$_uid}_user' and f_table = 'smart_request'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제

			_MQ_noreturn("delete from smart_request where r_uid='{$_uid}' ");
			error_loc("_request.list.php?pass_menu={$pass_menu}&".enc('d' , $_PVSC ));
			break;


		// -- 선택삭제와 일반삭제를 같이 처리한다.
		case "selectDelete": // 선택삭제

			if( count($chkVar)  < 1 ){ error_msg("삭제할 문의글이 존재하지 않습니다."); }

			// -- 파일삭제(데이터,첨부파일)
			$resBoardFiles = _MQ_assoc("select f_realname from smart_files where find_in_set(f_table_uid, '".implode(",",$chkVar)."' ) > 0 and f_table = 'smart_request'   ");
			foreach($resBoardFiles as $k=>$v){
				deleteFiles($v['f_realname']);
			}

			// -- 게시글,댓글,파일 db 데이터 삭제
			_MQ_noreturn("delete  from smart_request where  find_in_set(r_uid, '".implode(",",$chkVar)."' ) > 0 "); // 문의글
			_MQ_noreturn("delete  from smart_files where  find_in_set(f_table_uid, '".implode(",",$chkVar)."' ) > 0 and f_table = 'smart_request'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제

			error_loc("_request.list.php?pass_menu={$pass_menu}&".enc('d' , $_PVSC ));

		break;

	}
	// - 모드별 처리 ---

	exit;
?>