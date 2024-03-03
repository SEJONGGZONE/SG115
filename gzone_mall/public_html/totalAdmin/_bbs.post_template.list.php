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


	// 검색 체크
	$s_query = "
		from smart_bbs_template
		where 1
	";


	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {
		// -- 분류 선택할 시
		if( $pass_type != ''){ $s_query .= " and bt_type = '".$pass_type."'  "; }

		// -- 검색어
		if($pass_input_type == 'content'){ // 내용
			$s_query .= " and bt_content like '%".$pass_input."%' ";
		}else if($pass_input_type == 'title'){ // 제목
			$s_query .= " and bt_title like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and( bt_content like '%".$pass_input."%' or bt_title like '%".$pass_input."%'  )  ";
		}
	}
	// -- 검색종료 -- }}}


	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * ".$s_query." ORDER BY bt_rdate desc limit ".$count." , ".$listmaxcount);

?>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>' class="data_search">
	<input type=hidden name="searchMode" value="true">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

        <!-- 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('게시글양식검색')?>
            <!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php
                if($searchMode == 'true'){
                    $arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
                <?php } ?>
                <a href="_bbs.post_template.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">게시글 양식등록</a>
            </div>
        </div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
        <div class="search_form">
			<table class="table_form">
				<colgroup>
					<col width="130"/><col width="*"/><col width="130"/><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<th>검색어</th>
						<td>
                            <div class="lineup-row type_multi">
                                <select name="pass_input_type">
                                    <option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체-</option>
                                    <option value="title" <?=$pass_input_type == 'title' ? 'selected' : ''?>>제목</option>
                                    <option value="content" <?=$pass_input_type == 'content' ? 'selected' : ''?>> 내용</option>
                                </select>
                                <input type="text" name="pass_input" class="design"  value="<?=$pass_input?>" placeholder="검색어" />
                            </div>
						</td>
						<th>분류</th>
						<td>
							<?php echo _InputRadio( "pass_type" , array('','shop','admin') , $pass_type, "" , array('전체','사용자 양식','관리자 양식') , "")?>
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
	<div class="data_list data-bbs-post-template">
	<form name="frmBbsPostTemplate" id="frmBbsPostTemplate" method="post" action="_bbs.post_template.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="return false;" class="c_btn h27 black line select-delete">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>

				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->


		<table class="table_list">
			<colgroup>
				<col width="40"><col width="80"/><col width="110"/><col width="*"/><col width="100"/><col width="110"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">No</th>
					<th scope="col">분류</th>
					<th scope="col">관리용 제목</th>
					<th scope="col">등록일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);

					// 에디터 이미지 사용개수 체크
					$edit_img_cnt = edit_img_cnt($v['bt_uid'],'board_template');

					// KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성
					$printBtn = '
						<div class="lineup-row type_center">
							<a href="_bbs.post_template.form.php?_mode=modify&_uid='.$v['bt_uid'].'&_PVSC='.$_PVSC.'" class="c_btn gray h22">수정</a>
							<a href="#none" onclick="return false;" class="c_btn h22 dark line delete-item" data-uid="'.$v['bt_uid'].'">삭제</a>
					';
					if($edit_img_cnt['cnt']>0){
						$printBtn .='<a href="#none" onclick="edit_img_pop('.$v['bt_uid'].')" class="c_btn h22 gray line">이미지관리</a>';
					}
					$printBtn .='
						</div>
					'; // 관리버튼

					// -- 분류표시
					$printType = $v['bt_type'] == 'shop' ? '<span class="c_tag green">사용자 양식</span>':'<span class="c_tag yellow">관리자 양식</span>';


					// -- 출력
					echo '<tr>';
					echo '	<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck" value="'.$v['bt_uid'].'"></label></td>';
					echo '	<td class="this_num">'.$_num.'</td>';
					echo '	<td class="this_state">'.$printType.'</td>';
					echo '	<td class="t_left">'.stripslashes($v['bt_title'] ? $v['bt_title'] : '미입력').'</td>';
					echo '	<td class="this_date">'.printDateInfo($v['bt_rdate']).'</td>';
					echo '	<td class="this_ctrl">'.$printBtn.'</td>';
					echo '</tr>';
				}
			?>
			</tbody>

		</table>

			<?php if(count($res) <  1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

	</form>
	</div>

	<style>.post_replay .fr_bullet:before{ display:none; }</style>

	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>

	$(document).on('click','.delete-item',function(){
		if( confirm("해당 게시글 양식을 삭제하시겠습니까?") == false){ return false; }

		var _uid = $(this).attr('data-uid');  // 고유번호
		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }

		$('form#frmBbsPostTemplate [name="_uid"]').val(_uid);
		$('form#frmBbsPostTemplate [name="_mode"]').val('delete');
		$('form#frmBbsPostTemplate').submit();
	});

	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='board_template'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}


</script>
<?php
	include_once('wrap.footer.php');
?>