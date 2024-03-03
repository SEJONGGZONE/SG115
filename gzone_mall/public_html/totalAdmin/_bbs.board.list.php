<?php

	// -- LCY -- 기세판 목록
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


	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_bbs_info  where 1  ";


	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {
		// -- 검색어
		if($pass_input_type == 'uid'){
			$s_query .= " and bi_uid like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'name'){
			$s_query .= " and bi_name like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){
			$s_query .= " and ( bi_uid like '%".$pass_input."%' or bi_name like '%".$pass_input."%') ";
		}

		if( $pass_skin!= '') {  $s_query .= " and bi_skin= '".$pass_skin."'  "; }// -- 게시판스킨
		if( $pass_view!= '') {  $s_query .= " and bi_view= '".$pass_view."'  "; }// -- 노출여부
		if( $pass_view_type!= '') {  $s_query .= " and bi_view_type= '".$pass_view_type."'  "; }// -- 노출구분
	}
	// -- 검색종료 -- }}}

	// -- 게시판 정보를 불러온다. {{{
	$getBoardSkinInfo = getBoardSkinInfo(); // 게시판 스킨 정보 배열로 호출
	$arrBoardKink = array();
	foreach($getBoardSkinInfo as $k=>$v){
		$arrBoardKink[$k] = $v['skin']['title'];
	}
	// -- 게시판 정보를 불러온다. }}}

	// -- 노출구분이 있을 시 :: 순위변경 가능할 시
	if($st=='' && $so==''){
		if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true) {
			$st = 'bi_view_idx';
			$so = 'asc';
		}else{
			$st = 'bi_rdate';
			$so = 'desc';
		}
	}

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");


?>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>' class="data_search">
	<input type=hidden name="searchMode" value="true">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
		<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

        <div class="group_title">
            <strong>Search</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('게시판검색')?>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php
					if($searchMode == 'true'){
						$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
                    ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal btn_reset">검색 초기화</a>
                <?php } ?>
                <a href="_bbs.board.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">게시판 등록</a>
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
                                    <option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
                                    <option value="uid" <?php echo $pass_input_type == 'uid' ? 'selected' : ''?>>게시판ID</option>
                                    <option value="name" <?php echo $pass_input_type == 'name' ? 'selected' : ''?>>게시판명</option>
                                </select>
                                <input type="text" name="pass_input" class="design" style="" value="<?=$pass_input?>" placeholder="검색어" />
                            </div>
						</td>
						<th>적용 스킨</th>
						<td>
							<?php echo _InputSelect( "pass_skin" , array_keys($arrBoardKink) , $pass_skin, "" , array_values($arrBoardKink) , "게시판 스킨선택")?>
						</td>
					</tr>

					<tr>
						<th>노출여부</th>
						<td>
							<?php echo _InputRadio( 'pass_view' , array('', 'Y', 'N'), ($pass_view) , '' , array('전체', '노출', '숨김') , ''); ?>
						</td>
						<th>메뉴구분</th>
						<td>
							<?php echo _InputRadio( 'pass_view_type' , array_merge(array(''), array_keys($arrBoardViewType)), ($pass_view_type) , '' , array_merge(array('전체'), array_values($arrBoardViewType)) , ''); ?>
						</td>
					</tr>
				</tbody>
			</table>

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

		<div class="list_ctrl">
			<div class="left_box"></div>
			<div class="right_box">
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'bi_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'bi_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
					<?php if($pass_view_type!=''){?>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_view_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'bi_view_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_view_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'bi_view_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
					<?php }?>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_post_cnt', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'bi_post_cnt' && $so == 'asc'?' selected':null); ?>>게시글수 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'bi_post_cnt', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'bi_post_cnt' && $so == 'desc'?' selected':null); ?>>게시글수 ▼</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>


	<form name="frmBbsInfo" id="frmBbsInfo" method="post" action="_bbs.board.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?php echo enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?php echo $TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?php echo $_PVSC?>">
		<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>
		<input type=hidden name="_sort" value=""> <?php // 개별실행 ::  정렬방식 up,down,first,last?>

		<table class="table_list type_nocheck">
			<colgroup>
				<col width="60"/>
				<?php if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){  ?> <col width="130"/> <?php } ?>
				<col width="140"/><col width="120"/><col width="*"/><col width="110"/><col width="110"><col width="175"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">No</th>
					<?php if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){  ?>
						<th scope="col">순위</th>
					<?php } ?>
					<th scope="col">노출여부/구분</th>
					<th scope="col">스킨</th>
					<th scope="col">게시판명(아이디)</th>
					<th scope="col">게시글</th>
					<th scope="col">등록일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);
					
					$printPostCnt = _MQ_result("select count(*) as cnt FROM smart_bbs WHERE b_menu='".$v['bi_uid']."' AND b_depth ='1'"); // -- 게시글 수

					$printBtn = '
					<div class="lineup-row type_center">
						<a href="_bbs.board.form.php?_mode=modify&_uid='.$v['bi_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22 gray">수정</a>
						<a href="#none" onclick="return false;" class="c_btn h22 dark line delete-board" data-uid="'.$v['bi_uid'].'" data-apply="true" data-mainview="'.$v['bi_main_view'].'" data-postcnt="'.$printPostCnt.'">삭제</a>
						<a href="'.$system['url'].'/?pn=board.list&_menu='.$v['bi_uid'].'" target="_blank" class="c_btn h22 sky line">바로가기</a>
					</div>
					'; // 관리버튼

					// 순위변경 :: 노출구분이 있을 시에만가능
					$printIdx = '
						<div class="lineup-row type_center">
							<div class="lineup-updown">
								<a href="#none"  class="c_btn h22 icon_up evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="up"  title="위로"></a>
								<a href="#none"  class="c_btn h22 icon_down evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="down"  title="아래로"></a>
							</div>
							<div class="lineup-updown">
								<a href="#none"  class="c_btn h22 icon_top evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="first"  title="맨위로"></a>
								<a href="#none"  class="c_btn h22 icon_bottom evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="last"  title="맨아래로"></a>
							</div>
						</div>
					';

					$printView =  $arr_adm_button[($v['bi_view'] == 'Y' ? '노출' : '숨김')]; // -- 노출여부
					$printSkinName = $arrBoardKink[$v['bi_skin']]; // // -- 스킨명

					$printMainView = $v['bi_main_view']=='Y'?'<span class="c_tag h18 purple line">고객센터 메인 노출</span>':'';

					// -- 출력
					echo '<tr>';
					echo '	<td class="this_num">'.$_num.'</td>';
					if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){
						echo '	<td class="this_ctrl">'.$printIdx.'</td>';
					}
					echo '	<td class="this_state"><div class="lineup-row type_center">'.$printView.' '.$arrBoardViewType[$v['bi_view_type']].'</div></td>';
					echo '	<td class="t_blue">'.$printSkinName.'</td>';
					echo '	<td class="t_left"><div class="lineup-row type_side">'.$v['bi_name'].' ('.$v['bi_uid'].') '.$printMainView.'</div></td>';
					echo '	<td><a href="_bbs.post_mng.list.php?select_menu='.$v['bi_uid'].'" class="c_btn sky" style="width:90px" target="_blank">게시글 '.$printPostCnt.'개</a></td>';
					echo '	<td class="this_date">'.printDateInfo($v['bi_rdate']).'</td>';
					echo '	<td class="this_ctrl">'.$printBtn.'</td>';
					echo '</tr>';
				}
			?>
			</tbody>

		</table>

			<?php if(count($res) <  1) {  ?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

	</form>
	</div>


	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>

	$(document).on('click','.delete-board',function(){
	
		var _uid = $(this).attr('data-uid');  // 고유번호
		var _main_view = $(this).attr('data-mainview');  // 메인노출
		var _postcnt = $(this).attr('data-postcnt');  // 게시글 개수

		if(_main_view=='Y' && _postcnt<=0){
			if( confirm("해당 게시판을 삭제하시겠습니까?\n고객센터 메인노출로 설정된 게시판은 삭제가 불가능합니다.") == false){ return false; }
		}else{
			if( confirm("해당 게시판을 삭제하시겠습니까?\n등록된 게시물이 있는경우 삭제가 불가능합니다.") == false){ return false; }
		}

		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }

		$('form#frmBbsInfo [name="_uid"]').val(_uid);
		$('form#frmBbsInfo [name="_mode"]').val('delete');
		$('form#frmBbsInfo').submit();
	});

	$(document).on('click','.evt-sort',function(){
		var _sort = $(this).attr('data-sort'); // 정렬방식, 위로,아래롤,맨위로,맨아래로
		var _uid = $(this).attr('data-uid'); // 고유번호
		if( _uid == '' || _uid == undefined || _sort == '' || _sort == undefined){ alert("잘못된 접근입니다."); return false; }
		<?php if($st  == 'bi_view_idx' && $so == 'asc' ){ ?>
			$('form#frmBbsInfo [name="_uid"]').val(_uid);
			$('form#frmBbsInfo [name="_sort"]').val(_sort);
			$('form#frmBbsInfo [name="_mode"]').val('sort');
			$('form#frmBbsInfo').submit();
		<?php }else{ ?>
			alert('순위조정은 정렬상태가 "순위순 ▲"인 상태에서만 조정할 수 있습니다,');
		<?php } ?>
	});

</script>
<?php
	include_once('wrap.footer.php');
?>
