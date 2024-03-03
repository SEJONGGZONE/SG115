<?php

	// -- LCY -- 게시글목록 + 댓글
	include_once('wrap.header.php');

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	$boardSkinInfo = getBoardSkinInfo(); // 게시판정보를 불러온다
	$boardImagesUse = array();
	if(count($boardSkinInfo) > 0){
		foreach($boardSkinInfo as $k=>$v){
			if($v['skin']['images'] == 'true'){
				$boardImagesUse[] = $k;
			}
		}
	}

	// -- 게시판 정보를 불러온다.
	$getBoardList = get_board_list_array(false,false);

	// -- 게시판 필수 선택으로 지정
	$select_menu = in_array($select_menu,array_keys($getBoardList)) == false ? array_shift(array_keys($getBoardList)):$select_menu;
	if( $select_menu == '') $arr_param['select_menu'] = $select_menu;

	// 검색 체크
	$s_query = "
		from smart_bbs as b
		inner join smart_bbs_info as bi on (bi.bi_uid = b.b_menu)
		left join smart_individual as ind on (ind.in_id=b.b_inid)
		where 1 and b_menu = '".$select_menu."'
	";

	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {
		// -- 검색어
		if($pass_input_type == 'id'){ // 등록자 아이디
			$s_query .= " and (b_writer_type = 'member' or b_writer_type='admin') and b_inid like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'writer'){ // 등록자명
			$s_query .= " and b_writer like '%".$pass_input."%' ";
		}else if($pass_input_type == 'title'){ // 게시물 제목
			$s_query .= " and b_title like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and ( ((b_writer_type = 'member' or b_writer_type='admin') and b_inid like '%".$pass_input."%') or b_writer like '%".$pass_input."%' or b_title like '%".$pass_input."%'  ) ";
		}
		if( $pass_view_type!= '') {  $s_query .= " and bi_view_type= '".$pass_view_type."'  "; }// -- 노출구분

		// 등록일 검색
		if( $pass_sdate != '' && $pass_edate != ''){
			$s_query .= " and  ( left(b_rdate,10) BETWEEN '".$pass_sdate."' and '".$pass_edate."' ) ";
		}else{
			if($pass_sdate != ''){	$s_query .= " and left(b_rdate,10) >= '".$pass_sdate."' ";	}
			if($pass_edate != ''){	$s_query .= " and left(b_rdate,10) <= '".$pass_edate."' ";	}
		}
		//카테고리 검색
		if( $pass_category!= '') {  $s_query .= " and b_category= '".$pass_category."'  "; }
	}
	// -- 검색종료 -- }}}
	$boardInfo = get_board_info($select_menu); // 게시판정보 추출
	$replyMode = in_array($boardInfo['bi_list_type'],array('qna')); // reply 모드판별

	if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우
		$s_query .= " and b_depth = '1' ";
	}

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'p_rdate';
	if(!$so) $so = 'asc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);
	$que = " select b.* ,bi.*, ind.in_name, CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid ".$s_query." ORDER BY b_notice='Y' desc, b_orderuid desc , b_depth asc limit ".$count." , ".$listmaxcount;
	$res = _MQ_assoc($que);

?>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<form name="searchfrm" id="searchfrm" method="get" action='<?php echo $_SERVER["PHP_SELF"]?>' class="data_search">
	<input type=hidden name="searchMode" value="true">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="select_menu" value="<?php echo $select_menu; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
		<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

        <!-- 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php
                if($searchMode == 'true'){
                    $arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid,'select_menu'=>$select_menu),$arr_param));
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
                <?php } ?>
                <a href="_bbs.post_mng.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC,'select_menu'=>$select_menu)); ?>" class="c_btn h46 red" accesskey="a">글등록</a>
            </div>
        </div>

		<div class="c_tab type_col3">
			<ul>
				<?php
					foreach($getBoardList as $bk=>$bv){
						$hit_class = $select_menu!='' && $bk==$select_menu?'hit':'';
				?>
					<li class="<?php echo $hit_class;?>"><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('select_menu'=>$bk)); ?>" class="btn"><strong><?php echo $bv;?></strong></a></li>
				<?php }?>
			</ul>
		</div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
        <div class="search_form">
			<table class="table_form">
				<colgroup>
					<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload){?>
					<col width="130"/><col width="*"/>
					<?php }?>
					<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<?php
							// KAY :: 게시판 카테고리설	정
							$_categoryload = array_filter(explode(",",$boardInfo['bi_category']));
							$bbs_category_use_chk = 'N';
							if($boardInfo['bi_category_use']=='Y' && $_categoryload){
								$bbs_category_use_chk = 'Y';
						?>
							<th>카테고리</th>
							<td>
								<?php echo _InputSelect("pass_category", array_values($_categoryload) ,$pass_category,"", array_values($_categoryload) ,"전체 카테고리"); ?>
							</td>
						<?php }?>
						<th>검색어</th>
						<td>
                            <div class="lineup-row type_multi">
                                <select name="pass_input_type">
                                    <option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
                                    <option value="title" <?php echo $pass_input_type == 'title' ? 'selected' : ''?>>제목</option>
                                    <option value="id" <?php echo $pass_input_type == 'id' ? 'selected' : ''?>>작성자 ID</option>
                                    <option value="writer" <?php echo $pass_input_type == 'writer' ? 'selected' : ''?>>작성자 이름</option>
                                </select>
                                <input type="text" name="pass_input" class="design"  value="<?=$pass_input?>" placeholder="검색어" />
                            </div>
						</td>
						<th>작성일</th>
						<td>
                            <div class="lineup-row type_date">
                                <input type="text" name="pass_sdate" value="<?php echo rm_str($pass_sdate) < 1 ? '': $pass_sdate ?>" class="design js_pic_day" autocomplete="off" placeholder="날짜 선택" readonly style="width:85px">
                                <span class="fr_tx">-</span>
                                <input type="text" name="pass_edate" value="<?php echo rm_str($pass_edate) < 1?  '': $pass_edate ?>" class="design js_pic_day" autocomplete="off" placeholder="날짜 선택" readonly style="width:85px">
                            </div>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php
						if($searchMode == 'true'){
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</form><!-- end data_search -->

	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<form name="frmBbsInfo" id="frmBbsInfo" method="post" action="_bbs.post_mng.pro.php">
			<input type="hidden" name="_mode" value="">
			<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
			<input type="hidden" name="searchQue" value="<?php echo enc('e',$s_query)?>">
			<input type="hidden" name="searchCnt" value="<?php echo $TotalCount?>">
			<input type="hidden" name="ctrlMode" value="">
			<input type=hidden name="_PVSC" value="<?php echo $_PVSC?>">
			<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>

			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="return false;" class="c_btn h27 black line select-delete-item">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>
				</div>
			</div>


			<table class="table_list">
				<colgroup>
					<col width="40"><col width="80"/>
					<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload) { ?>
						<col width="100"/>
					<?php }?>
					<?php if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우 ?>
						<col width="100"/>
					<?php } ?>
					<col width="*"/><col width="120"/><col width="80"/><col width="100"><col width="175"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>

						<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload) { ?>
						<th scope="col">카테고리</th>
						<?php } ?>
						<?php if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우 ?>
						<th scope="col">답변상태</th>
						<?php } ?>
						<th scope="col">제목</th>
						<th scope="col">작성자</th>
						<th scope="col">조회수</th>
						<th scope="col">작성일</th>
						<th scope="col">관리 </th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$v) {

						// -- 게시글 권한
						$getBoardAuth = boardAuthChkAll($v['bi_uid']);

						$_num = $TotalCount - $count - $k ;
						$_num = number_format($_num);

						$printBtn  = '<div class="lineup-row type_center">';
						$printBtn .= '	<a href="_bbs.post_mng.form.php?_mode=modify&_uid='.$v['b_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22 gray">수정</a>';
						$printBtn .= '	<a href="#none" onclick="return false;" class="c_btn h22 dark line delete-item" data-uid="'.$v['b_uid'].'" data-apply = "true">삭제</a>';
						$printBtn .= '	<a href="'.$system['url'].'/?pn=board.view&_menu='.$v['b_menu'].'&_uid='.$v['b_uid'].'" target="_blank" class="c_btn sky line h22">바로가기</a>';
						$printBtn .= '</div>';

						// -- 댓글개수표시
						$commentCnt = $v['b_talkcnt'];
						$printCommentCnt = $commentCnt > 0?'<span class="t_orange" style="margin-left:5px">(댓글 '.$commentCnt.')</span>':'';

						// -- 게시물 제목
						$arrTitleIcon = array();
						$printTitle = strip_tags(stripslashes($v['b_title']));
						$printTitle = '<a href="_bbs.post_mng.view.php?_uid='.$v['b_uid'].'&_PVSC='.$_PVSC.'" class="board_title" >'.$printTitle.$printCommentCnt.'</a>';
						$tdReply =  $v['b_depth'] > 1 && $v['b_relation'] > 0?'if_reply':'';

						if( count($arrTitleIcon) > 0){ $printTitle = implode($arrTitleIcon).''.$printTitle; }
						else{ $printTitle = $printTitle; }

						// -- 작성자 정보
						$printWriterInfo = in_array($v['b_writer_type'], array('member','admin')) == true ? showUserInfo($v['b_inid'],$v['b_writer'],$v) : showUserInfo(false,$v['b_writer']);

						$printReplyStatus = '';
						if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우
							$replyCnt = _MQ_result("select count(*) as cnt from smart_bbs where b_relation = '".$v['b_uid']."' and b_depth = '2' ");
							$printReplyStatus = '<td class="this_state">'.($replyCnt > 0 ? '<span class="c_tag blue line h22">답변완료</span>' :  '<span class="c_tag light h22">답변대기</span>').'</td>';
						}

						// 게시판 카테고리설정 -- 카테고리 정보
						$printCategory='';
						if($boardInfo['bi_category_use']=='Y' && $_categoryload){
							$printCategory = $v['b_category']!=''?$v['b_category']:'<span class="t_light">미선택</span>';
						}

						$_image = '';
						if(in_array($v['bi_skin'], $boardImagesUse)){
							// 이미지 체크
							$_img = get_img_src($v['b_img1'], IMG_DIR_BOARD);
							if($_img <> '') $_image = '<div class="preview_thumb img80 img_full"><img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt=""></div>';
						}

						// 에디터 이미지 사용개수 체크
						$edit_img_cnt = edit_img_cnt($v['b_uid'],'board');
					?>
					<tr>
						<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck chk-buid" value="<?php echo $v['b_uid'];?>"></label></td>
						<td class="this_num"><?php echo $_num;?></td>
						<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload){?>
							<td class="t_blue">
								<?php echo $printCategory?>
							</td>
						<?php }?>
						<?php echo $printReplyStatus;?>
						<td class="t_left <?php echo $tdReply;?>">
						<?php echo $_image;?>
						<?php echo $printTitle;?>
							<?php if($v['b_notice']=='Y' ||  $v['b_secret']=='Y' || $edit_img_cnt['cnt']>0 || $getBoardAuth['reply']=='1'){?>
							<div class="in_something">
								<?php echo $v['b_notice']=='Y'?'<span class="c_tag yellow line">공지사항</span>':'';?>
								<?php echo $v['b_secret']=='Y'?'<span class="c_tag black line">비밀글</span>':'';?>
								<?php if($edit_img_cnt['cnt']>0){?><a href="#none" onclick="edit_img_pop(<?php echo $v['b_uid'];?>)" class="c_btn h22 gray line">이미지 관리</a><?php }?>
								<?php if($getBoardAuth['reply']=='1' ){?>
									<a href="_bbs.post_mng.form.php?_mode=reply&_uid=<?php echo $v['b_uid'];?>&_PVSC=<?php echo $_PVSC;?>" class="c_btn h22 blue">답변작성</a>
								<?php }?>
							</div>
							<?php }?>
						</td>
						<td><?php echo $printWriterInfo;?></td>
						<td class="this_state"><span class="hidden_tx">조회수</span><?php echo number_format($v['b_hit']);?></td>
						<td class="this_date"><?php echo printDateInfo($v['b_rdate']);?></td>
						<td class="this_ctrl"><?php echo $printBtn;?></td>
					</tr>
				<?php }	?>
				</tbody>

			</table>

			<?php if(	count($res) <  1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>
	</div>

	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>

	// -- 선택삭제
	$(document).on('click','.select-delete-item',function(){
		if( confirm("선택하신 게시판을 삭제하시겠습니까?\n게시물의 모든 데이터 및 댓글이 삭제가 됩니다.") == false){ return false; }
		var chkLen = $('.chk-buid:checked').length;
		if( chkLen < 1 ){ alert('한개이상 선택해 주세요.'); return false; }
		$('form#frmBbsInfo [name="_mode"]').val('selectDelete');
		$('form#frmBbsInfo').submit();
	});

	// -- 한개삭제
	$(document).on('click','.delete-item',function(){
		if( confirm("해당 게시물을 삭제하시겠습니까?\n게시물의 모든 데이터 및 댓글이 삭제가 됩니다.") == false){ return false; }

		var _uid = $(this).attr('data-uid');  // 고유번호
		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }

		$('form#frmBbsInfo [name="_uid"]').val(_uid);
		$('form#frmBbsInfo [name="_mode"]').val('delete');
		$('form#frmBbsInfo').submit();
	});

	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='board'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}

</script>
<?php
	include_once('wrap.footer.php');
?>
