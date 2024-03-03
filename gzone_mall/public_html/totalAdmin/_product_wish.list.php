<?php
	include_once('wrap.header.php');

	$_PVS = ''; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

	// 입점업체 보안
	if($pass_com != ''){ $_cpid = $pass_com;  }

	######## 검색 체크
	$s_query = " from smart_product_wish as pw
	inner join smart_product as p on (pw.pw_pcode = p.p_code)
	inner join smart_individual as ind on (ind.in_id = pw.pw_inid)
	where 1 ";

	// 신규 검색 기능
	if( $pass_input_value != ''){
		$arr_input_que = array();
		if( in_array($pass_input_type, array('all','pass_code')) > 0 ){ $arr_input_que[] = " p.p_code like '%".$pass_input_value."%'  "; } // 상품코드
		if( in_array($pass_input_type, array('all','pass_name')) > 0 ){ $arr_input_que[] = " p.p_name like '%".$pass_input_value."%'  "; } // 상품명
		if( in_array($pass_input_type, array('all','pass_mname')) > 0 ){ $arr_input_que[] = " ind.in_name like '%".$pass_input_value."%'  "; } // 회원명
		if( in_array($pass_input_type, array('all','pass_inid')) > 0 ){ $arr_input_que[] = " ind.in_id like '%".$pass_input_value."%'  "; } // 회원아이디
		if( count($arr_input_que) > 0){  $s_query .= "  and (".implode(" or ",$arr_input_que).")  "; }
	}

	if( $_cpid !="" ) { $s_query .= " and p.p_cpid='${_cpid}' "; }

	// JJC ::: 브랜드관리 ::: 2017-11-03
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

	if( $_cuid !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$_cuid."') > 0 "; }
	else if( $pass_parent03_real !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$pass_parent03_real."') > 0 "; }
	else if( $pass_parent02_real !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , -1) = '" . $pass_parent02_real . "' or
						pct.pct_cuid = '" . $pass_parent02_real . "'
					)
			) > 0
		";
	}
	else if( $pass_parent01 !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , 1) = '" . $pass_parent01 . "' or
						pct.pct_cuid = '" . $pass_parent01 . "'
					)
			) > 0
		";
	}



	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'pw_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);
	$res = _MQ_assoc(" select p.* ,pw.*, ind.in_name,ind.in_id  $s_query order by {$st} {$so} limit $count , $listmaxcount ");



?>

<form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" autocomplete="off" class="data_search">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>Search</strong>
		<div class="btn_box">
			<a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
			<?php if($mode == 'search'){ ?>
				<a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
			<?php } ?>
		</div>
	</div>

        <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
        <div class="search_form">
			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
				</colgroup>
				<tbody>

					<tr>
						<th>검색어</th>
						<td>
							<div class="lineup-row type_multi">
								<select name="pass_input_type">
									<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
									<option value="pass_code" <?php echo $pass_input_type == 'pass_code' ? 'selected' : ''?>>상품코드</option>
									<option value="pass_name" <?php echo $pass_input_type == 'pass_name' ? 'selected' : ''?>>상품명</option>
									<option value="pass_mname" <?php echo $pass_input_type == 'pass_mname' ? 'selected' : ''?>>회원명</option>
									<option value="pass_inid" <?php echo $pass_input_type == 'pass_inid' ? 'selected' : ''?>>회원ID</option>
								</select>
								<input type="text" name="pass_input_value" class="design" style="width:150px" value="<?=$pass_input_value?>" placeholder="검색어" />
							</div>
						</td>
						<th>브랜드</th>
						<td>
							<?php
								// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
								//		basic : 기본정보
								//		all : 브랜드 전체 정보
								$arr_brand = brand_info('basic');
								echo _InputSelect( 'pass_brand' , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "브랜드 선택");
							?>
						</td>
						<th>입점업체</th>
						<td>
							<?php
							// ----- JJC : 입점관리 : 2020-09-17 -----
							if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
								$arr_customer = arr_company();
								$arr_customer2 = arr_company2();
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
					<tr>
						<th>카테고리</th>
						<td colspan="5">
                            <div class="lineup-row type_process">
                                <?PHP
                                    // 상품 카테고리 분류 (list , form 을 공통으로 쓰기 위한 조치)
                                    // 1차 - pass_parent01 -> app_depth1
                                    // 2차 - pass_parent02_real -> app_depth2
                                    // 3차 - pass_parent03_real -> $row[p_cuid]
                                    if( $pass_parent01 ) {
                                        $app_depth1 =  $pass_parent01 ;
                                    }
                                    if( $pass_parent02_real ) {
                                        $app_depth2 =  $pass_parent02_real ;
                                    }
                                    if( $pass_parent03_real ) {
                                        $app_depth3 =  $pass_parent03_real ;
                                    }

                                    $pass_parent03_no_required = "Y";

                                    include_once(OD_PROGRAM_ROOT."/category.inc.php");
                                ?>
                            </div>
						</td>

					</tr>
				</tbody>
			</table>

			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
	    </div>
    </form><!-- end data_search -->



	<div class="data_list">

		<form name="frm" method="post" action="_product_wish.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn gray line">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn black line">선택 찜해제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pw_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pw_rdate' && $so == 'desc'?' selected':null); ?>>찜등록일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pw_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pw_rdate' && $so == 'asc'?' selected':null); ?>>찜등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'desc'?' selected':null); ?>>상품금액 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'asc'?' selected':null); ?>>상품금액 ▲</option>
					</select>
				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="70"><col width="*">
					<col width="120">
					<col width="120"><col width="100"><col width="90">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">상품정보</th>
						<th scope="col">상품금액</th>
						<th scope="col">찜한 회원</th>
						<th scope="col">찜등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?PHP
					if(sizeof($res) > 0){
						foreach($res as $k=>$v) {

							$_del = '<a href="#none" onclick="del(\'_product_wish.pro.php?_mode=delete&pw_uid='. $v['pw_uid'] .'&_PVSC='. $_PVSC .'\');" class="c_btn h22 dark line">찜 해제</a>';

							$_num = $TotalCount - $count - $k ;

							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
							if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

							$userLink = '/?pn=product.view&code='.$v['p_code']; // 사용자 링크 사용시 
							$adminLink = '_product.form.php?_mode=modify&_code='.$v['p_code'];
							if( $v['p_type'] == 'ticket'){
								$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$v['p_code'];
							}

					?>
							<tr>
								<td class="this_check"><label class="design"><input type="checkbox" name="chk_uid[]" class="js_ck" value="<?php echo $v['pw_uid']; ?>"></label></td>
								<td class="this_num"><?php echo $_num; ?></td>
								<td>
									<div class="order_item_thumb type_simple">
										<div class="thumb"><a href="<?php  echo $adminLink; ?>" target="_blank"><img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>"></a></div>
										<div class="order_item">
											<dl>
												<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){?>
													<div class='entershop'><?php echo showCompanyInfo($v['p_cpid']); ?></div>
												<?php }?>
												<dt><div class="item_name"><a href="<?php  echo $adminLink; ?>" target="_blank"><?php echo strip_tags($v['p_name']); ?></a></div></dt>
												<dt class="t_light"><?php echo $v['p_code']; ?></dd>
												<dt>
													<?php
														// JJC ::: 브랜드관리 ::: 2017-11-03
														echo ($arr_brand[$v['p_brand']] ? "<span class='item_brand'>Brand : ".$arr_brand[$v['p_brand']] . "</span>" : "") ;
													?>
												</dt>
											</dl>
										</div>
										<div class="other_tag">
											<?php echo $arr_adm_button[$v['p_type']];?>
											<?php
												if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
												else{
													if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
													else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
													else{  echo '<span class="c_tag light t4">판매종료</span>'; }
												}
											?>
										</div>
									</div>
								</td>
								<td class="t_red t_right t_bold this_price">
									<?php echo number_format($v['p_price']); ?>원
								</td>
								<td>
									<span class="hidden_tx">찜한 회원</span><?php echo showUserInfo($v['in_id'],$v['in_name'],$v); ?>
								</td>
								<td>
									<span class="hidden_tx">찜 등록일</span><?php echo printDateInfo($v['pw_rdate']); ?>
								</td>

								<td class="this_ctrl">
									<div class="lineup-row type_center"><?php echo $_del; ?></div>
								</td>
								<!--

								소스삭제
								<?php
									# 상품별 통계를 추출한다 -- 상품코드별로 찜한 횟수 노출
									if($pass_mode == 'group'){
								?>

								<?php }else{ ?><?php } ?>
								-->
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>


			<?php if(count($res) < 1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>
		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(':checked')){
				if(confirm('정말 삭제하시겠습니까?')){
					$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
					$('form[name=frm]').attr('action' , '_product_wish.pro.php');
					document.frm.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
	</script>


<?php include_once('wrap.footer.php'); ?>