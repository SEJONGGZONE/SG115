<?PHP
	if(!$_GET['pass_menu']) $_GET['pass_menu'] = 'inquiry';
	$app_current_link = '_request.list.php?pass_menu='.$_GET['pass_menu'];
	$pass_menu = $_GET['pass_menu'];
	include_once('wrap.header.php');


	// 검색 체크
	$s_query = " from smart_request where r_menu='{$pass_menu}' ";
	if( $_mode == "search" ) {


		// -- 검색어
		if($pass_input_type == "title"){
			$s_query .= " and r_title like '%".$pass_input."%' ";
		}else if( $pass_input_type == "content"){
			$s_query .= " and r_content like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){
			$s_query .= " and ( r_title like '%".$pass_input."%' or r_content like '%".$pass_input."%') ";
		}

		// -- 작성자
		if($pass_menu=='inquiry'){
			if($pass_winput_type == "id"){
				$s_query .= " and r_title like '%".$pass_input."%' ";
			}else if( $pass_input_type == "writer"){
				$s_query .= " and r_content like '%".$pass_input."%' ";
			}else if( $pass_input_type == 'all'){
				$s_query .= " and ( r_title like '%".$pass_input."%' or r_content like '%".$pass_input."%') ";
			}
		}else{
			if($pass_winput_type == "name"){				// 이름/상호명
				$s_query .= " and r_comname like '%".$pass_winput."%' ";
			}else if( $pass_winput_type == "htel"){			// 연락처
				$s_query .= " and REPLACE(r_hp, '-','') like '%".rm_str($pass_winput)."%' ";
			}else if( $pass_winput_type == "email"){			// 이메일
				$s_query .= " and r_email like '%".$pass_winput."%' ";
			}else if( $pass_winput_type == "all"){
				$s_query .= " and ( r_comname like '%".$pass_winput."%' or REPLACE(r_hp, '-','') like '%".$pass_winput."%'  or r_email like '%".$pass_winput."%') ";
			}
		}



		if( $pass_status !="" ) { $s_query .= " and r_status='{$pass_status}' "; }
	}


	$listmaxcount = 30 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * {$s_query} ORDER BY r_rdate desc limit $count , $listmaxcount ");
?>


    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="data_search tab_conts" data-idx="search">
        <input type="hidden" name="_mode" value="search">
        <input type="hidden" name="st" value="<?php echo $st; ?>">
        <input type="hidden" name="so" value="<?php echo $so; ?>">
        <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
        <input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">

        <!-- ● 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($_mode == 'search') { ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>

				<?php if($pass_menu=='partner'){?>
					<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">설정하기</a>
				<?php }?>
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
									<option value="title" <?php echo $pass_input_type == 'title' ? 'selected' : ''?>>제목</option>
									<option value="content" <?php echo $pass_input_type == 'content' ? 'selected' : ''?>>내용</option>
								</select>
								<input type="text" name="pass_input" class="design"  value="<?php echo $pass_input; ?>" style="width:500px" placeholder="검색어" />
							</div>
						</td>
						<th>작성자</th>
						<td>
							<div class="lineup-row type_multi">
								<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
									 <select name="pass_winput_type">
										<option value="all" <?php echo $pass_winput_type == 'all' ? 'selected' : ''?>>전체</option>
										<option value="id" <?php echo $pass_winput_type == 'id' ? 'selected' : ''?>>작성자 ID</option>
										<option value="writer" <?php echo $pass_winput_type == 'writer' ? 'selected' : ''?>>작성자명</option>
									</select>
								<?php } ?>
								<?php if( in_array($pass_menu , array("partner")) ){ ?>
									 <select name="pass_winput_type">
										<option value="all" <?php echo $pass_winput_type == 'all' ? 'selected' : ''?>>전체</option>
										<option value="name" <?php echo $pass_winput_type == 'name' ? 'selected' : ''?>>이름/상호명</option>
										<option value="htel" <?php echo $pass_winput_type == 'htel' ? 'selected' : ''?>>연락처</option>
										<option value="email" <?php echo $pass_winput_type == 'email' ? 'selected' : ''?>>이메일</option>
									</select>
								<?php } ?>
								<input type="text" name="pass_winput" class="design"  value="<?php echo $pass_winput; ?>" placeholder="작성자" />
							</div>
						</td>
					</tr>
					<tr>
						<th>답변상태</th>
						<td colspan="3">
							<?php echo _InputRadio( 'pass_status' , array('', '답변완료', '답변대기'), ($pass_status) , '' , array('전체', '답변완료', '답변대기') , ''); ?>
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
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount,'pass_menu'=>$pass_menu)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
	    </div>
    </form><!-- end data_search -->


	<?php // 제휴문의 메뉴 노출여부 환경설정 ?>
	<?php if($pass_menu=='partner'){?>
		<!-- 환경 설정  -->
		<form action="_request.pro.php" method="post" class="data_search js_thisview" style="display:none;">
			<input type="hidden" name="_mode" value="config" />
			<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">

			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>메뉴 노출여부</th>
						<td>
							<?php echo _InputRadio( "_request_partner_view" , array('Y','N'), $siteInfo['s_request_partner_view']?$siteInfo['s_request_partner_view']:'N' , '' , array('노출','숨김') ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 blue "><input type="submit" name="" value="설정저장"></span></li>
				</ul>
			</div>
		</form>
	<?php }?>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<form name="frm" id="frm" method="post" action="_request.pro.php">
			<input type="hidden" name="_mode" value="" />
			<input type="hidden" name="_PVSC" value="<?php echo $_PVSC?>">
			<input type="hidden" name="_uid" value="">
			<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">

			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
					<a href="#none" onclick="return false;" class="c_btn h27 black line select-delete-item">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'r_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'r_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'r_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'r_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
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
					<col width="40"/><col width="70"/><col width="90"/><col width="*"/>
					<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
						<col width="110"/>
					<?php } ?>
					<?php if( in_array($pass_menu , array("partner")) ){ ?>
						<col width="200"/>
					<?php } ?>
					<col width="100"/><col width="130"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">No</th>
						<th scope="col">답변상태</th>
						<th scope="col">제목/내용</th>
						<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
							<th scope="col">작성자</th>
						<?php } ?>
						<?php if( in_array($pass_menu , array("partner")) ){ ?>
							<th scope="col">작성자</th>
						<?php } ?>

						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$row){
						$_mod = "<input type=button value='수정' class=btn onclick='location.href=(\"\");'>";
						$_del = "<input type=button value='삭제' class=btn onclick='del(\"_request.pro.php?pass_menu={$pass_menu}&_mode=delete&_uid=$row[r_uid]&_PVSC=${_PVSC}\");'>";

						$_num = $TotalCount - $count - $k ;
				?>
						<tr>
							<td class="this_check"><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck chk-ruid" value="<?php echo $row['r_uid'];?>"></label></td>
							<td class="this_num"><?php echo $_num; ?></td>
							<td class="this_state">
								<?php if($row['r_status'] == "답변대기") { ?>
									<span class="c_tag light h18">답변대기</span>
								<?php }else{ ?>
									<span class="c_tag h18 blue line">답변완료</span>
								<?php } ?>
							</td>
							<td class="t_left t_black">
								<dl class="title_conts_box">
									<dt><?php echo strip_tags($row['r_title']); ?></dt>
									<dd><?php echo $row['r_content'];?></dd>
								</dl>
							</td>
							<?php if( in_array($pass_menu , array("inquiry")) ){ ?>
							<td><span class="hidden_tx">작성자</span><?php echo showUserInfo($row['r_inid']); ?></td>
							<?php } ?>
							<?php if( in_array($pass_menu , array("partner")) ){ ?>
							<td class="t_left">
								<div class="t_black t_full"><?php echo $row['r_comname']; ?></div>
								<div class="t_light t_full"><?php echo $row['r_hp']; ?></div>
								<div class="t_light t_full"><?php echo $row['r_email']; ?></div>
							</td>
							<?php } ?>
							<td class="this_date"><?php echo printDateInfo($row['r_rdate']); ?></td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<a href="_request.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', 'pass_menu'=>$pass_menu, '_uid'=>$row['r_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn blue ">답변관리</a>
									<a href="#none" onclick="del('_request.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', 'pass_menu'=>$pass_menu, '_uid'=>$row['r_uid'], '_PVSC'=>$_PVSC)); ?>');" class="c_btn h22 black line">삭제</a>
								</div>
							</td>
						</tr>
				<?php } ?>
				</tbody>
			</table>

			<?php if(count($res) <= 0) { ?>
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>
		</form>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>

	</div>

<script>
	// -- 선택삭제
	$(document).on('click','.select-delete-item',function(){
		if( confirm("선택하신 문의를 삭제하시겠습니까?\n모든 데이터가 삭제 됩니다.") == false){ return false; }
		var chkLen = $('.chk-ruid:checked').length;
		if( chkLen < 1 ){ alert('한개이상 선택해 주세요.'); return false; }
		$('form#frm [name="_mode"]').val('selectDelete');
		$('form#frm').submit();
	});

	<?php if($pass_menu=='partner'){?>
		// 검색 / 환경설정 텝메뉴 클릭 이벤트
		var trigger_cont_editor = true;
		$(document).on('click', '.tab_menu', function() {
			var idx = $(this).data('idx');
			// 탭변경
			$('.tab_menu').closest('li').removeClass('hit');
			$('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
			// 입력항목변경
			$('.tab_conts').hide();
			$('.tab_conts[data-idx='+ idx +']').show();
		});
	<?php }?>
</script>

<?PHP
	include_once('wrap.footer.php');
?>