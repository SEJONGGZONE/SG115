<?php
/*
	accesskey {
		a: 배너추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');


# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
if(!$pass_site_skin) $pass_site_skin = $siteInfo['s_skin']; // 기본 스킨은 사용중인 스킨으로 고정
$_targetArr = array('_blank'=>'새창', '_self'=>'같은창');
$_targetViewArr = array('_blank'=>'(새창)', '_self'=>'(같은창)');


// 검색 조건
$s_query = '';
if($pass_site_skin) $s_query .= " and (find_in_set('{$pass_site_skin}', `b_loc`) > 0 or find_in_set('common', `b_loc`) > 0) ";
if($pass_loc) $s_query .= " and `b_loc` = '{$pass_loc}' ";
if($pass_view) $s_query .= " and `b_view` = '{$pass_view}' ";
if($pass_title) $s_query .= " and `b_title` like '%{$pass_title}%' ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'b_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_banner where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_banner where (1) {$s_query} order by {$st} {$so} limit {$count}, {$listmaxcount} ");


// 사이트 스킨 전체 배너리스트를 얻음
// KAY :: 2022-12-21 :: 반응형은 모바일 배너만 사용하므로 pc 배너 및 공통배너는 사용x
$merge_no = 'NO';
foreach($_skin_list as $k=>$v) {

	if(file_exists(OD_SKIN_ROOT.'/site_m/'.$k.'/_var.php')) {
		include(OD_SKIN_ROOT.'/site_m/'.$k.'/_var.php');
		if(count($arr_banner_loc) > 0) $AllBannerLoc[$k] = array_merge($arr_banner_loc);
	}
}
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
            <a href="_banner.form.php?_mode=add<?php echo ($pass_site_skin?'&s_skin='.$pass_site_skin:null); ?><?php echo ($pass_loc?'&s_loc='.$pass_loc:null); ?>" class="c_btn h46 red" accesskey="a">배너등록</a>
        </div>
    </div>

    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>스킨</th>
					<td>
						<?php echo _InputSelect('pass_site_skin', array_keys($_skin_list), $pass_site_skin, ' class="js_site_skin"', array_values($_skin_list), '스킨 선택'); ?>
					</td>
					<th>배너구분</th>
					<td>
						<?php echo _InputSelect('pass_loc', array_keys($arr_banner_loc_common), $pass_loc, ' class="js_skin_banner_loc"', array_values($arr_banner_loc_common), ''); ?>
						<script type="text/javascript">
							$(document).on('change', '.js_site_skin', SkinBannerLoc);
							$(document).ready(SkinBannerLoc);
							function SkinBannerLoc() {
								var _skin = $('.js_site_skin').find('option:selected').val();
								$.ajax({
									data: {
										_mode: 'skin_banner_loc',
										s_skin: _skin
									},
									type: 'POST',
									cache: false,
									async:false,
									dataType : 'json',
									url: '_banner.pro.php',
									success: function(e) {
										if(typeof e.data == 'undefined' || !e.data) data = null;
										else data = e.data;
										var result = data;
										var _option;
										_option = '<option value="">배너구분 선택</option>';
										$.each(result, function(k, v) {
											_option += '<option value="'+k+'"'+(k == '<?php echo $pass_loc; ?>'?' selected':'')+'>'+v+'</option>';
										});
										$('.js_skin_banner_loc').html('<option value="">-선택-</option>');
										$('.js_skin_banner_loc').html(_option);
										return result;
									}
								});
							}
						</script>
					</td>
				</tr>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio('pass_view', array('', 'Y', 'N'), $pass_view, '', array('전체', '노출', '숨김'), ''); ?>
					</td>
					<th>배너이름</th>
					<td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="width:185px" placeholder="배너이름">
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



<div class="data_list">
	<div class="list_ctrl">
		<div class="left_box"></div>
		<div class="right_box">
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'b_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'b_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'b_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'b_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_sdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'b_sdate' && $so == 'asc'?' selected':null); ?>>시작일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_sdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'b_sdate' && $so == 'desc'?' selected':null); ?>>시작일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_edate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'b_edate' && $so == 'asc'?' selected':null); ?>>종료일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'b_edate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'b_edate' && $so == 'desc'?' selected':null); ?>>종료일 ▼</option>
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
			<col width="70"><col width="80"><col width="100">
			<col width="*"><col width="*">
			<col width="110"><col width="100"><col width="110">
		</colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>노출</th>
				<th>이미지</th>
				<th>배너구분</th>
				<th>배너명/링크</th>
				<th>노출기간/순위</th>
				<th>등록일</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($r) > 0) { ?>
				<?php
				foreach($r as $k=>$v) {
					$_num = $TotalCount-$count-$k; // NO 표시
					$_title = strip_tags($v['b_title']);
					$_img = IMG_DIR_BANNER.$v['b_img'];
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) $_img = '<img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt="'.addslashes($_title).'">';
					else $_img = '';
				?>
					<tr>
						<td class="this_num"><?php echo number_format($_num); ?></td>
						<td class="this_state">
							<?php if($v['b_view'] == 'Y') { ?>
								<span class="c_tag blue h18 blue line t3">노출</span>
							<?php } else { ?>
								<span class="c_tag h18 light t3">숨김</span>
							<?php } ?>
						</td>
						<td class="img80 img_full"<?php echo ($v['b_color']?' style="background-color:'.$v['b_color'].'"':null); ?>>
							<?php echo $_img; ?>
						</td>
						<td class="t_left">
							<?php echo $AllBannerLoc[$pass_site_skin][$v['b_loc']]; ?>
						</td>
						<td class="t_left t_none">
							<div class="t_black t_bold"><?php echo $v['b_title']; ?></div>
							<div class="dash_line only_pc_view"><!-- 점선라인 --></div>
							<div class="lineup-column  type_left">
								<?php if($v['b_target'] != '_none' && isset($v['b_link'])) { ?>
									<a href="<?php echo $v['b_link']; ?>" target="_blank" class="t_sky"><?php echo $v['b_link']; ?></a>
								<?php } else { ?>
									<span class="t_none">링크없음</span>
								<?php } ?>
								<?php echo ($v['b_target'] != '_none' && isset($v['b_link']) ? $_targetViewArr[$v['b_target']] : ''); ?>
							</div>
						</td>
						<td class="t_orange">
							<div class="lineup-row type_center">
								<?php echo ($v['b_none_limit'] == 'Y'?'<span class="t_black">무기한</span>':$v['b_sdate'].' ~ '.$v['b_edate']); ?>
								<span class="t_light">(<?php echo number_format($v['b_idx']); ?>위)</span>
							</div>
						</td>
						<td class="this_date">
							<?php echo printDateInfo($v['b_rdate']) ?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_banner.form.php?_mode=modify&s_skin=<?php echo $pass_site_skin; ?>&_uid=<?php echo $v['b_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 gray ">수정</a>
								<a href="#none" onclick="del('_banner.pro.php?_mode=delete&_uid=<?php echo $v['b_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 dark line">삭제</a>
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
</div>
<!-- // 리스트 -->

<?php include_once('wrap.footer.php'); ?>