<?php
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');

# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
$_targetArr = array('_blank'=>'새창', '_self'=>'같은창');

// 검색 조건
$s_query = " from smart_popup where 1 ";
if($pass_view) $s_query .= " and `p_view` = '{$pass_view}' ";
if($pass_title) $s_query .= " and `p_title` like '%{$pass_title}%' ";
if($pass_mode) $s_query .= " and `p_mode` = '{$pass_mode}' ";

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'p_rdate';
if(!$so) $so = 'asc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
    <input type="hidden" name="_mode" value="search">
    <input type="hidden" name="st" value="<?php echo $st; ?>">
    <input type="hidden" name="so" value="<?php echo $so; ?>">
    <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

    <div class="group_title">
        <strong>Search</strong>
        <div class="btn_box">
            <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
            <?php if($_mode == 'search') { ?>
                <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
            <?php } ?>
            <a href="_popup.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">팝업등록</a>
        </div>
    </div>

    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio('pass_view', array('', 'Y', 'N'), $pass_view, '', array('전체', '노출', '숨김'), ''); ?>
					</td>
					<th>팝업타입</th>
					<td>
						<?php echo _InputRadio('pass_mode', array('', 'I', 'E'), $pass_mode, '', array('전체', '이미지', '에디터'), ''); ?>
					</td>
					<th>팝업이름</th>
					<td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="width:500px" placeholder="팝업이름">
					</td>
				</tr>
			</tbody>
		</table>

		<div class="c_btnbox">
			<ul>
				<li>
					<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
				</li>
				<?php if($_mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
    </div>
</form><!-- end data_search -->


<!-- 리스트 -->
<div class="data_list">
	<form name="frm" method="post" action="" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

		<div class="list_ctrl">
			<div class="left_box"></div>
			<div class="right_box">
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_sdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_sdate' && $so == 'asc'?' selected':null); ?>>시작일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_sdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_sdate' && $so == 'desc'?' selected':null); ?>>시작일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_edate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_edate' && $so == 'asc'?' selected':null); ?>>종료일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_edate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_edate' && $so == 'desc'?' selected':null); ?>>종료일 ▼</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>

		<table class="table_list type_nocheck">
			<colgroup>
				<col width="70"><col width="80"><col width="130"><col width="100">
				<col width="*">
				<col width="100"><col width="100"><col width="110">
			</colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>노출</th>
					<th>순위</th>
					<th>타입</th>
					<th>타이틀/링크</th>
					<th>노출기간</th>
					<th>등록일</th>
					<th>관리</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($r) > 0) { ?>
					<?php
					foreach($r as $k=>$v) {
						$_num = $TotalCount-$count-$k; // NO 표시
						$_title = strip_tags($v['p_title']);
						$_img = IMG_DIR_POPUP.$v['p_img'];
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) $_img = '<img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt="'.addslashes($_title).'">';
						else $_img = '';

						// 에디터 이미지 사용개수 체크
						$edit_img_cnt = edit_img_cnt($v['p_uid'],'popup');
					?>
						<tr>
							<td class="this_num"><?php echo number_format($_num); ?></td>
							<td class="this_state2">
								<?php if($v['p_view'] == 'Y') { ?>
									<span class="c_tag blue h18 blue line t3">노출</span>
								<?php } else { ?>
									<span class="c_tag h18 light t3">숨김</span>
								<?php } ?>
							</td>
							<td class="this_state">
								<div class="lineup-updown_ctrl">
									<div class="ctrl_form">
										<input type="text" name="sort_group[<?php echo $v['p_uid'];?>]" value="<?php echo number_format($v['p_sort_group']); ?>" class="design number_style sort_group_<?php echo $v['p_uid']; ?>" placeholder="0">
										<a href="#none" onclick="sort_group('<?php echo $v['p_uid'];?>'); return false;" class="c_btn h27 light">적용</a>
									</div>
									<div class="ctrl_btn">
										<a href="#none" onclick="sort_up('<?php echo $v['p_uid'];?>','up','<?php echo enc('e' ,$s_query); ?>'); return false;" class="c_btn h22 icon_up" title="위로"></a>
										<a href="#none" onclick="sort_up('<?php echo $v['p_uid'];?>','down','<?php echo enc('e' ,$s_query); ?>'); return false;" class="c_btn h22 icon_down" title="아래로"></a>
										<a href="#none" onclick="sort_up('<?php echo $v['p_uid'];?>','top','<?php echo enc('e' ,$s_query); ?>'); return false;" class="c_btn h22 icon_top" title="맨위로"></a>
										<a href="#none" onclick="sort_up('<?php echo $v['p_uid'];?>','bottom','<?php echo enc('e' ,$s_query); ?>'); return false;" class="c_btn h22 icon_bottom" title="맨아래로"></a>
									</div>
								</div>
							</td>
							<td class="img80 img_full">
								<?php echo ($v['p_mode'] == 'I'?$_img:'<span class="c_tag green">에디터형</span>'); ?>
							</td>
							<td class="t_left t_none">
								<div class="t_black t_bold"><?php echo $v['p_title']; ?></div>
								<div class="dash_line only_pc_view"><!-- 점선라인 --></div>
								<div class="lineup-column  type_left">
									<?php if($v['p_mode'] == 'I' && $v['p_link'] != '') { ?>
										<a href="<?php echo $v['p_link']; ?>" target="_blank" class="t_sky"><?php echo $v['p_link']; ?></a>
									<?php } else { ?>
										<span class="t_none">링크없음</span>
									<?php } ?>
									<?php echo ($v['p_mode'] == 'I' && $v['p_link'] != '' ? $_targetArr[$v['p_target']] : ''); ?>
									<?php if($edit_img_cnt['cnt']>0){?><a href="#none" onclick="edit_img_pop('<?php echo $v['p_uid'] ?>')" class="c_btn h22 gray line">이미지 관리</a><?php }?>
								</div>
							</td>
							<td class="t_orange">
								<?php echo ($v['p_none_limit'] == 'Y'?'<span class="t_black">무기한</span>':$v['p_sdate'].' ~ '.$v['p_edate']); ?>
							</td>
							<td class="this_date">
								<?php echo printDateInfo($v['p_rdate']);?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_popup.form.php?_mode=modify&_uid=<?php echo $v['p_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 gray ">수정</a>
									<a href="#none" onclick="del('_popup.pro.php?_mode=delete&_uid=<?php echo $v['p_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 dark line">삭제</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<?php if(count($r) <= 0) { ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</form>
</div>
<!-- // 리스트 -->

<script>
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='popup'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}

	// 순위조정 up-down-top-bottom
	function sort_up(uid,mode,query) {
		<?php if($st  == 'p_idx' && $so == 'asc' ){ ?>
			common_frame.location.href='_popup.sort.php?uid='+uid+'&_mode='+mode+'&query='+query;
		<?php }else{ ?>
			alert('순위조정은 정렬상태가 "순위순 ▲"인 상태에서만 조정할 수 있습니다,');
		<?php } ?>
	}

	// 순위그룹 수정
	function sort_group(uid){
		var group = $('.sort_group_'+ uid).val()*1;
		if(group <= 0){
			alert('상품 순위를 입력해 주시기 바랍니다.');
			$('.sort_group_'+ uid).focus();
			return false;
		}
		common_frame.location.href='_popup.sort.php?uid='+uid+'&_mode=modify_group&_group='+group;
	}

</script>
<?php include_once('wrap.footer.php'); ?>