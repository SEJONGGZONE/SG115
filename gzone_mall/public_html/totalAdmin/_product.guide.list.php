<?php
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');


/*

CREATE TABLE  hy30_db.smart_product_guide (
g_uid INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  '고유번호',
g_user VARCHAR( 50 ) NOT NULL COMMENT  '적용입점업체아이디',
g_type TINYINT NOT NULL COMMENT  '안내구분',
g_default ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  '기본적용여부',
g_title VARCHAR( 100 ) NOT NULL COMMENT  '타이틀',
g_content TEXT NOT NULL COMMENT  '이용안내내용',
g_rdate DATETIME NOT NULL COMMENT  '등록일',
INDEX (  g_user ,  g_type ,  g_default )
) ENGINE = MYISAM


*/



# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);


// 검색 조건
$s_query = "";
if($pass_type) $s_query .= " and g_type = '{$pass_type}' ";
if($pass_title) $s_query .= " and g_title like '%{$pass_title}%' ";
if($pass_com) $s_query .= " and g_user = '{$pass_com}' ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'g_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_product_guide where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_product_guide where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

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

			<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">도움말</a>
            <a href="_product.guide.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">안내등록</a>
        </div>
    </div>

	<div class="js_thisview" style="display:none">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>참고사항</th>
					<td>
						<?php echo _DescStr('입점업체별 정책을 미리 등록해두고 상품관리에서 선택해 사용할 수 있습니다.',''); ?>
						<?php echo _DescStr('특정 입점업체로 등록된 경우 해당 업체의 상품에서만 사용 가능하고, "통합관리자"로 등록된 경우 모든 입점업체에서 사용 가능합니다.',''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('이용안내는 사용자페이지 상품 상세 하단에 노출되며 상품별로 다르게 적용하거나, 사용하지 않을수도 있습니다.',''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

    <div class="search_form">
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>타이틀</th>
					<td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="width:185px" placeholder="타이틀">
					</td>
					<th>분류</th>
					<td>
						<?php echo _InputSelect('pass_type', array_keys($arrProGuideType), $pass_type, '', array_values($arrProGuideType), '구분선택'); ?>
					</td>
					<th>입점업체</th>
					<td>
						<?php
						// ----- JJC : 입점관리 : 2020-09-17 -----
						if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer = array_merge(array('_MASTER_'=>'통합관리자'), $arr_customer);
							$arr_customer2 = arr_company2();
							$arr_customer2 = array_merge(array('_MASTER_'=>'통합관리자'), $arr_customer2);
						?>
							<?php if( $AdminPath == 'totalAdmin'){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
							<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '입점업체 선택'); ?>
							<?php }else{ ?>
								<?php echo $arr_customer2[$pass_com]; ?>
							<?php } ?>
						<?php }else{?>
							<?php echo _DescStr('입점업체 미사용 <a href="https://www.onedaynet.co.kr/p/solution_plus.html#page_entershop" target="_blank"><em>신청하기</em></a>',''); ?>
						<?php }?>
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

	<div class="list_ctrl">
		<div class="left_box">
			<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
			<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
			<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
		</div>
		<div class="right_box"></div>
	</div>

	<table class="table_list">
		<colgroup>
			<col width="40"><col width="70"><col width="90">
			<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin') { ?>
			<col width="200">
			<?php } ?>
			<col width="*"><col width="100"><col width="100"><col width="110">
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
				<th>No</th>
				<th>기본노출</th>
				<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin') { ?>
				<th>입점업체</th>
				<?php } ?>
				<th>타이틀</th>
				<th>최종수정일</th>
				<th>등록일</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($r) > 0) { ?>
				<?php
				foreach($r as $k=>$v) {

					$_num = $TotalCount-$count-$k; // NO 표시

					// 에디터 이미지 사용개수 체크
					$edit_img_cnt = edit_img_cnt($v['g_uid'],'setting');
				?>
					<tr>
						<td class="this_check"><label class="design"><input type="checkbox" name="chk_uids[]" class="js_ck" value="<?php echo $v['pl_uid']; ?>"></label></td>
						<td class="this_num"><?php echo number_format($_num); ?></td>
						<td class="this_state">
							<?php if($v['g_default'] == 'Y') { ?>
								<span class="c_tag h18 blue">기본</span>
							<?php }else{ ?>
								<span class="c_tag h18 blue line">선택</span>
							<?php } ?>
						</td>
						<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin') { ?>
						<td>
							<div class='entershop'><?php echo ($arr_customer[$v['g_user']]?($v['g_user'] != '_MASTER_' ? showCompanyInfo($v['g_user']) : '통합관리자'):'확인불가'); ?></div>
						</td>
						<?php } ?>
						<td class="t_left">
							<div class="list_data_box">
								<div class="text_info">
									<dl>
										<dt class="t_blue"><?php echo $arrProGuideType[$v['g_type']]; ?></dt>
										<dd><?php echo strip_tags($v['g_title']); ?></dd>
									</dl>
								</div>
								<?php if( $AdminPath == 'totalAdmin' && $edit_img_cnt['cnt']>0) {?>
									<div class="side"><a href="#none" onclick="edit_img_pop('<?php echo $v['g_uid'] ?>')" class="c_btn gray line">이미지 관리</a></div>
								<?php } ?>
							</div>
						</td>
						<td>
							<span class="hidden_tx">최종 수정일</span><?php echo printDateInfo($v['g_mdate']); ?>
						</td>
						<td class="this_date">
							<?php echo printDateInfo($v['g_rdate']); ?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center">
								<a href="_product.guide.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['g_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray ">수정</a>
								<a href="#none" onclick="del('_product.guide.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['g_uid'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 dark line">삭제</a>
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

<script>
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='setting'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
</script>

<?php include_once('wrap.footer.php'); ?>