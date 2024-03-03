<?php
	include_once './inc.php';

	switch ($ajaxMode) {

		case 'chkUid': //고유번호 체크

			if( $chkUid == ''){ echo json_encode(array('rst'=>'fail','msg'=>'게시판 아이디를 입력해 주세요.','key'=>'_uid')); exit; } // 비었을 경우

			if( preg_match("/^[a-zA-Z0-9_]*$/",$chkUid) == false ){ echo json_encode(array('rst'=>'fail','msg'=>'게시판 아이디는 영문(대문자, 소문자), 숫자, 언더바(_)만 사용 가능합니다.','key'=>'_uid')); exit; }
			$rowChk = _MQ("select count(*) as cnt from smart_bbs_info where bi_uid = '".$chkUid."' ");
			if( $rowChk['cnt'] < 1){ echo json_encode(array('rst'=>'success')); exit; } // 중복이 아닐경우
			else{ echo json_encode(array('rst'=>'fail','msg'=>'이미 사용중인 게시판 아이디 입니다. 다른아이디를 입력해 주세요.','key'=>'_uid')); exit; }    // 중복일경우
		break;

		case 'selectSkin': // 스킨선택 시

			$htmlSkin = "";
			$htmlSkin .='<div class="tip_table">';

			$skinInfo = getBoardSkinInfo($_skinName,$agent);

			if( count($skinInfo) > 0 && $_skinName != '' ){
				$skinOption = $skinInfo[$_skinName]['skin']; // 변수를 짧게 줄인다
				$htmlSkin .='			<ul>';
				$htmlSkin .='				<li class="th" style="width:100px">게시판 설명</li>';
				$htmlSkin .='				<li class="t_left">'.$skinOption['info'].'</li>';
				$htmlSkin .='			</ul>';

				// -- 이미지첨부가 가능일 시 권장크기를 노출
				if($skinOption['images'] == 'true') {
					//$temp_images_view = array('list'=>'게시물 리스트','view'=>'게시물 본문');
					//if( $skinOption['images_view'] != ''){
					//	$htmlSkin .='			<ul>';
					//	$htmlSkin .='				<li class="th">이미지 노출위치</li>';
					//	$htmlSkin .='				<li class="t_left">'.($temp_images_view[$skinOption['images_view']]).'</li>';
					//	$htmlSkin .='			</ul>';
					//}
					if( $skinOption['images_width'] != ''){
						$htmlSkin .='			<ul>';
						$htmlSkin .='				<li class="th">이미지 권장크기</li>';
						$htmlSkin .='				<li class="t_left">'.$skinOption['images_width'].' x '.$skinOption['images_height'].' (pixel, 사이즈에 따라 자동조정)</li>';
						$htmlSkin .='			</ul>';
					}
				}
				$htmlSkin .='</div>';

				$htmlSkin .='<div class="tip_table">';
				$htmlSkin .='<ul><li class="th">파일 첨부</li><li class="th">목록 이미지</li><li class="th">기간 설정</li><li class="th">답글</li></ul><ul>';

				$htmlSkin .='<li class="">'.($skinOption['file'] == 'true' ? '<span class="c_tag blue">사용가능</span>':'<span class="c_tag light">사용불가</span>').'</li>';
				$htmlSkin .='<li class="">'.($skinOption['images'] == 'true' ? '<span class="c_tag blue">사용가능</span>':'<span class="c_tag light">사용불가</span>').'</li>';

				// -- 기간이벤트 사용여부
				if($skinOption['date'] != '') {
					$htmlSkin .='<li class="">'.($skinOption['date'] == 'true' ? '<span class="c_tag blue">사용가능</span>':'<span class="c_tag light">사용불가</span>').'</li>';
				}
				// -- 답글 사용여부
				if($skinOption['reply'] != '') {
					$htmlSkin .='<li class="">'.($skinOption['reply'] == 'true' ? '<span class="c_tag blue">사용가능</span>':'<span class="c_tag light">사용불가</span>').'</li>';
				}
				$htmlSkin .='</ul></div>';
			}else{
				$htmlSkin .= '<div class="common_none"><div class="no_icon"></div><div class="gtxt">스킨 정보가 없습니다.</div></div>';
			}



			// -- 스킨특성 기능 사용여부 추가
			$arrSkinOption = array();
			$skinInfo = getBoardSkinInfo($_skinName,$agent);
			$skinOptionDefault = $skinInfo[$_skinName]['skin']; // PC 기준으로 가져온다.

			if( $skinOptionDefault['file'] == 'true'){ $arrSkinOption[] = 'upload-file'; }
			if( $skinOptionDefault['images'] == 'true'){ $arrSkinOption[] = 'upload-images'; }
			if( $skinOptionDefault['date'] == 'true'){ $arrSkinOption[] = 'option-date'; }
			if( $skinOptionDefault['reply'] == 'true'){ $arrSkinOption[] = 'option-reply'; }
			if ( in_array($skinOptionDefault['type'],array('qna')) == false){ $arrSkinOption[] = 'option-comment'; }

			// -- 스킨타입
			$skinType =$skinOptionDefault['type'];

			echo json_encode(array('rst'=>'success','htmlSkin'=>$htmlSkin,'skinOption'=>$arrSkinOption,'skinType'=>$skinType)); exit;



		break;

	}

?>