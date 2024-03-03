<?PHP
	if(!$_GET['pt_type']) $_GET['pt_type'] = '상품리뷰';
	$app_current_link = '_product_talk.list.php?pt_type='.$_GET['pt_type'];
	include_once('wrap.header.php');


	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기


	// 검색 체크
	$s_query = "from smart_product_talk as pt inner join smart_product as p on (pt.pt_pcode = p.p_code) where pt_depth = '1' ";
	$s_query .= " and pt.pt_type  = '${pt_type}' ";

	// 입점업체 추가
	if($pass_com != ''){ $s_query .= "  and p.p_cpid = '".$pass_com."' "; }
	if( $pass_uid !="" ) { $s_query .= " and pt.pt_uid = '".$pass_uid."' "; }

	if( $_mode == "search" ) {
		// 작성자 검색
		if($pass_input_type == 'id'){ // 등록자 아이디
			$s_query .= " and pt.pt_inid like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'writer'){ // 등록자명
			$s_query .= " and pt_writer like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'pname'){ // 상품명
			$s_query .= " and p.p_name like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'pcode'){ // 상품코드
			$s_query .= " and pt.pt_pcode like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and (  pt.pt_inid like '%".$pass_input."%' or pt.pt_writer like '%".$pass_input."%'  or p.p_name like '%".$pass_input."%' or pt.pt_pcode like '%".$pass_input."%' ) ";
		}

		//if( $pass_pname !="" ) { $s_query_temp .= " and p.p_name like '%".$pass_pname."%' "; }			// 상품명
		//if( $pass_pcode !="" ) { $s_query_temp .= " and pt.pt_pcode like '%".$pass_pcode."%' "; }		// 상품코드
		if( $pass_content !="" ) { $s_query_temp .= " and pt.pt_content  like '%".$pass_content."%' "; }	// 작성내용

		if( $pass_star !="" ) { $s_query_temp .= " and pt.pt_eval_point = '".($pass_star*20)."' "; }	// 전체평점

		// KAY :: 2022-01-13 :: 상품리뷰 신고 관리자 노출 
		if($pass_report!=''){
			$report_query =" select count(*) from smart_product_talk_report as ptr	where ptr.ptr_ptuid = pt.pt_uid	";
			if($pass_report=='N'){
				$s_query .= "	and (".$report_query.")<=0 ";
			}else{
				$s_query .= "	and (".$report_query.")>0	";
			}
		}

		// 리뷰 및 문의 답변여부 검색
		if($pass_reply!=""){
			if($pass_reply=='Y'){
				$s_query .= " and ( select count(*) as cnt from smart_product_talk as pt2 where pt2.pt_relation = pt.pt_uid and pt2.pt_depth='2'  )>0 ";
			}else{
				$s_query .= " and ( select count(*) as cnt from smart_product_talk as pt2 where pt2.pt_relation = pt.pt_uid and pt2.pt_depth='2'  )<=0 ";
			}
		}
		$search_query = _MQ_assoc("select  * ".$s_query.$s_query_temp);

		if(count($search_query) > 0){
			$s_pt_uid = array(); // 정보 초기화
			foreach($search_query as $sk => $sv){
				$s_pt_uid[$sk] = $sv['pt_depth'] == 1 ?$sv['pt_uid'] :$sv['pt_relation'];
			}
			$s_query .= "and  find_in_set(pt.pt_uid,'".implode(',',$s_pt_uid)."') > 0";
		}else{
			$s_query  .= $s_query_temp;
		}
	}

	// 데이터 조회
	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'pt_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ("select count(*) as cnt ".$s_query);
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount/$listmaxcount);

	$que = " select pt.* , p.p_name, p.p_img_list, p.p_cpid {$s_query} order by {$st} {$so} limit $count , $listmaxcount ";
	$res = _MQ_assoc($que);

?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="data_search">
        <input type="hidden" name="_mode" value="search">
        <input type="hidden" name="st" value="<?php echo $st; ?>">
        <input type="hidden" name="so" value="<?php echo $so; ?>">
        <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
        <input type="hidden" name="pt_type" value=<?php echo $pt_type; ?>>

        <!-- ● 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($_mode == 'search') { ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>
            </div>
        </div>

        <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
        <div class="search_form">
            <table class="table_form">
                <colgroup>
                    <col width="130"><col width="*"><col width="130"><col width="*">
                </colgroup>
                <tbody>
                    <tr>
						<th>검색어</th>
                        <td>
							<div class="lineup-row type_multi">
                                <select name="pass_input_type">
                                    <option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
									<option value="pname" <?php echo $pass_input_type == 'pname' ? 'selected' : ''?>>상품명</option>
									<option value="pcode" <?php echo $pass_input_type == 'pcode' ? 'selected' : ''?>>상품코드</option>
                                    <option value="id" <?php echo $pass_input_type == 'id' ? 'selected' : ''?>>작성자 ID</option>
                                    <option value="writer" <?php echo $pass_input_type == 'writer' ? 'selected' : ''?>>작성자명</option>
                                </select>
                                <input type="text" name="pass_input" class="design"  value="<?php echo $pass_input?>" placeholder="검색어" />
                            </div>
						</td>
						<th><?php echo $pt_type =='상품리뷰'?'평점/내용':'내용';?></th>
						<td>
							<div class="lineup-row type_multi">
								<?php if($pt_type == "상품리뷰"){ ?>
									<?php echo _InputSelect( 'pass_star' , array('', '5', '4','3','2','1'), ($pass_star) , '' , array('전체평점', '5점', '4점','3점','2점','1점') , ''); ?>
								<?php } ?>
								<input type="text" name="pass_content" class="design" value="<?php echo $pass_content; ?>" placeholder="작성내용" />
							</div>
						</td>
                    </tr>
                    <tr>
						<th>답변여부</th>
						<?php if($pt_type == '상품리뷰'){ ?>
						<td>
						<?php } ?>
						<?php if($pt_type == "상품문의"){ ?>
						<td colspan="3">
						<?php } ?>
							<?php echo _InputRadio( 'pass_reply' , array('', 'Y', 'N'), ($pass_reply) , '' , array('전체', '답변완료', '답변대기') , ''); ?>
						</td>
						<?php if($pt_type == '상품리뷰'){ ?>
							<th>신고여부</th>
							<td>
								<?php echo _InputRadio( 'pass_report' , array('', 'Y', 'N'), ($pass_report) , '' , array('전체', '신고접수', '미신고') , ''); ?>
							</td>
						<?php } ?>
                    </tr>
                </tbody>
            </table>

            <div class="c_btnbox">
                <ul>
                    <li>
                        <span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
                    </li>
                    <?php if($_mode == 'search') { ?>
                        <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount,'pt_type'=>$pt_type)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </form><!-- end data_search -->

	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<form name="frmTalkInfo" id="frmTalkInfo" method="post" action="_product_talk.pro.php">
			<input type="hidden" name="_mode" value="">
			<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
			<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
			<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 black line">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pt_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pt_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pt_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pt_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
						<?php if($pt_type == "상품리뷰"){ ?>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pt_eval_point', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pt_eval_point' && $so == 'asc'?' selected':null); ?>>평점순 ▲</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pt_eval_point', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pt_eval_point' && $so == 'desc'?' selected':null); ?>>평점순 ▼</option>
						<?php } ?>
					</select>
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>
				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="40"/><col width="70"/><col width="90"/><col width="220"/>
					<col width="*"/>
					<col width="100"/><col width="100"/><col width="175"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">답변</th>
						<th scope="col">상품정보</th>
						<th scope="col"><?php echo $pt_type=='상품리뷰'?'평점/내용':'내용';?></th>
						<th scope="col">작성자</th>
						<th scope="col">작성일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>

				<?PHP
					foreach($res as $k=>$v){

						$_num = $TotalCount - $count - $k ;

						if($v['pt_intype'] == "normal"){
							$in_info = _MQ("select in_name as name from smart_individual where in_id = '".$v['pt_inid']."'");
						}else 	if($v['pt_intype'] == "admin"){
							$in_info = _MQ("select in_name as name from smart_individual where in_id = '".$v['pt_inid']."'");
						}else{
							$in_info = _MQ("select cp_name as name from smart_company where cp_id = '".$v['pt_inid']."'");
						}

						// 상품이미지
						$_p_img = get_img_src($v['p_img_list']);
						if($_p_img=='') $_p_img = 'images/thumb_no.jpg';

						// 관리자 답변 추출
						$reply_query = _MQ_assoc("select * from smart_product_talk where pt_depth = '2' and pt_relation = '".$v['pt_uid']."' ");

						// 평점 -> 별로 변환
						$eval_str = eval_point_change_star( $v['pt_eval_point'] );
						$_pt_img = get_img_src($v['pt_img']);
					?>
						<tr>
							<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck" value="<?php echo $v['pt_uid'];?>"></label></td>
							<td class="this_num"><?php echo $_num; ?></td>
							<td class="this_state">
								<?php if(count($reply_query) > 0){ ?>
									<span class="c_tag h18 blue line">답변완료</span>
								<?php }else{ ?>
									<span class="c_tag light">답변대기</span>
								<?php } ?>
							</td>
							<td>
								<div class="product_box">
									<img src="<?php echo $_p_img; ?>" alt="<?php echo $v['p_name']?>">
									<dl>
										<dt><?php echo $v['pt_pcode']; ?></dt>
										<dd><?php echo stripslashes($v['p_name']); ?></dd>
									</dl>
								</div>
							</td>
							<td class="t_left">
								<?php
									if($pt_type == '상품리뷰'){ 
										// 상품리뷰 신고 건수
										$report_cnt = _MQ_result("select count(*) as cnt from smart_product_talk_report where ptr_ptuid ='".$v['pt_uid']."' ");
								?>
									<div class="lineup-row type_side">
										<div class="t_star"><?php echo $eval_str; ?></div>
										<?php if($report_cnt>0){?>
											<span class="c_tag red line">신고 <?php echo $report_cnt;?>건</span>
										<?php }?>
									</div>
									<div class="dash_line"><!-- 점선라인 --></div>
								<?php } ?>
								<?php if($pt_type == "상품문의"){ ?>
									<div class="t_black t_bold"><?php echo stripslashes(strip_tags($v['pt_title'])); ?></div>
									<div class="dash_line"><!-- 점선라인 --></div>
								<?php }?>
								<div class="review_box">
									<div class="conts"><?php echo nl2br(stripslashes(strip_tags($v['pt_content']))); ?></div>
									<?php if($pt_type == '상품리뷰' && $_pt_img!=''){ ?>
										<div class="photo"><img src="<?php echo $_pt_img;?>" alt="<?php echo nl2br(stripslashes(strip_tags($v['pt_content']))); ?>" class="js_thumb_img"  data-img="<?php echo $_pt_img;?>" /></div>
									<?php } ?>
								</div>

								<?php
									//<!-- 관리자답변 -->
									if(count($reply_query) > 0){
										foreach ( $reply_query as $rpk=>$rpv) {
											$reply_date = date('Y.m.d (H:i:s)', strtotime($rpv['pt_rdate']));
									?>
										<div class="answer_box">
											<dl>
												<dt>
													<strong><?php echo showUserInfo($rpv['pt_inid'],$rpv['pt_writer'],$rpv) ?></strong>
													<em><?php echo $reply_date; ?></em>
												</dt>
												<dd><?php echo nl2br(stripslashes(strip_tags($rpv['pt_content']))); ?></dd>
											</dl>
											<div class="lineup-row">
												<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pt_type'=>$rpv['pt_type'], 'pt_uid'=>$rpv['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 gray">답변수정</a>
												<a href="#none" onclick="del('_product_talk.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pt_uid'=>$rpv['pt_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 dark line">답변삭제</a>
											</div>
										</div>
								<?php
										}
									}
								?>
							</td>
							<td>
								<div class="lineup-column type_reverse">
									<span class="hidden_tx">작성자</span><?php echo showUserInfo($v['pt_inid'],$v['pt_writer'],$v); ?>
								</div>
							</td>
							<td class="this_date"><?php echo printDateInfo($v['pt_rdate']); ?></td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pt_type'=>$v['pt_type'], 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn gray ">수정</a>
									<a href="#none" onclick="del('_product_talk.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 black line">삭제</a>
									<a href="_product_talk.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', 'pt_type'=>$v['pt_type'], 'pt_uid'=>$v['pt_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 blue">답변하기</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php if(count($res) <= 0) { ?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

			<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
			</div>
		</form>
	</div>

	<script>
		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(":checked")){
				if(confirm("정말 삭제하시겠습니까?")){
					$("form[name=frmTalkInfo]").children("input[name=_mode]").val("mass_delete");
					$("form[name=frmTalkInfo]").attr("action" , "_product_talk.pro.php");
					document.frmTalkInfo.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
	</script>

<?PHP
	include_once('wrap.footer.php');
?>