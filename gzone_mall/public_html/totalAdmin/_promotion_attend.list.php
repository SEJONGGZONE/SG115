<?php
	include_once('wrap.header.php');


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

	######## 검색 체크
	$s_query = " from smart_promotion_attend_config where 1 ";

	// 검색 쿼리 준비
	if( $pass_status !="" ) { $s_query .= " and atc_use = '${pass_status}' "; }
	if( $pass_use !="" ) { $s_query .= " and atc_use = '${pass_use}' "; }
	if( $pass_type !="" ) { $s_query .= " and atc_type = '${pass_type}' "; }
	if( $pass_title !="" ) { $s_query .= " and atc_title like '%${pass_title}%' "; }
	if( $pass_uselimit =="N" ) { $s_query .= " and atc_limit = 'N' "; }
	if( $pass_sdate !="" ) { $s_query .= " and atc_edate >= '${pass_sdate}' "; }
	if( $pass_edate !="" ) { $s_query .= " and atc_sdate <= '${pass_edate}' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'atc_uid';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");

?>

    <form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>" class="data_search tab_conts" data-idx="search">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

        <!-- ● 단락타이틀 -->
        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($mode == 'search') { ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>

				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">설정하기</a>
                <a href="_promotion_attend.form.php?<?php echo URI_Rebuild('?', array('?_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">출석체크 등록</a>
            </div>
        </div>

        <!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
        <div class="search_form">
            <!-- 폼테이블 2단 -->
            <table class="table_form">
                <colgroup>
                    <col width="130"><col width="*"><col width="130"><col width="*">
                </colgroup>
                <tbody>
                    <tr>
						<th>출석체크명</th>
                        <td><input type="text" name="pass_title" class="design" style="" value="<?php echo trim(stripslashes($pass_title))?>" placeholder="출석체크명"></td>
						<!--
                        <th>진행상태</th>
                        <td>
                            <?php echo _InputRadio( "pass_status" , array('', 'Y','N'), $pass_status , "" , array('전체', '진행','종료') ); ?>
                        </td>
						-->
                        <th>상태</th>
                        <td>
                            <?php echo _InputRadio( "pass_use" , array('', 'Y','N'), $pass_use , "" , array('전체', '사용','중지') ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>기간</th>
                        <td>
                            <div class="lineup-row">
                                <div class="lineup-row type_date">
                                    <input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day js_passdate" autocomplete="off" placeholder="날짜 선택" style="width:85px" <?php echo ($pass_uselimit == 'N' ? ' disabled ' : null); ?>>
                                    <span class="fr_tx">-</span>
                                    <input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day js_passdate" autocomplete="off" placeholder="날짜 선택" style="width:85px" <?php echo ($pass_uselimit == 'N' ? ' disabled ' : null); ?>>
                                </div>
                                <label class="design"><input type="checkbox" id="pass_uselimit" name="pass_uselimit" value="N" <?php echo ($pass_uselimit == 'N' ? ' checked ' : null); ?>>제한없음</label>
                            </div>
                        </td>
						<th>참여 방식</th>
                        <td>
                            <?php echo _InputRadio( "pass_type" , array('', 'T','C'), $pass_type , "" , array('전체', '누적 참여형','연속 참여형') ); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- 가운데정렬버튼 -->
            <div class="c_btnbox">
                <ul>
                    <li>
                        <span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
                    </li>
                    <?php if($mode == 'search') { ?>
                        <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </form><!-- end data_search -->


	<!-- 환경 설정  -->
	<form action="_promotion_attend.pro.php" method="post" class="data_search js_thisview" style="display:none;">
		<input type="hidden" name="_mode" value="config" />
		<table class="table_form">
			<colgroup>
				<col width="130"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>메뉴노출</th>
					<td>
						<?php echo _InputRadio( "_promotion_attend_view" , array('Y','N'), $siteInfo['s_promotion_attend_view']?$siteInfo['s_promotion_attend_view']:'N' , '' , array('노출','숨김') ); ?>
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

	<!-- ● 데이터 리스트 -->
	<div class="data_list">


		<table class="table_list type_nocheck">
			<colgroup>
				<col width="80"><col width="160">
				<col width="*"><col width="*">
				<col width="80"><col width="80">
				<col width="110"><col width="175">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">No</th>
					<th scope="col">상태/참여방식</th>
					<th scope="col">출석체크명</th>
					<th scope="col">혜택지급/기간</th>
					<th scope="col">참여수</th>
					<th scope="col">달성건</th>
					<th scope="col">등록일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php

				if(sizeof($res) > 0){
					foreach($res as $k=>$v){

						// 총 참여수,  총 달성수 추출
						$arr_cnt = _MQ(" select count(*) as total , sum(if(atl_success = 'Y' , 1 , 0)) as success from smart_promotion_attend_log where atl_event = '". $v['atc_uid'] ."' ");

						$arr_cnt['success'] = $arr_cnt['success']==''?'0':$arr_cnt['success'];

						$_mod = "<a href='_promotion_attend.form.php?_mode=modify&_uid=" . $v['atc_uid'] . "&_PVSC=" . $_PVSC . "' class='c_btn h22 gray'>수정</a>";
						// 출석체크 내역이 있으면 삭제 불가
						if($arr_cnt['total']>0){
							$_del = "<a href='#none' onclick='alert(\"출석 내역이 있는 이벤트는 삭제할 수 없습니다.\");return false;' class='c_btn h22 dark line'>삭제</a>";
						}else{
							$_del = "<a href='#none' onclick='del(\"_promotion_attend.pro.php?_mode=delete&_uid=" . $v['atc_uid'] . "&_PVSC=" . $_PVSC . "\");return false;' class='c_btn h22 dark line'>삭제</a>";
						}
						$_log = "<a href='_promotion_attend.log.php?_uid=" . $v['atc_uid'] . "&_PVSC=" . $_PVSC . "' class='c_btn h22 sky line'>내역보기</a>";

						$_num = $TotalCount - $count - $k ;
				?>
						<tr>
							<td class="this_num"><?php echo $_num; ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php echo $arr_adm_button[($v['atc_use'] == 'Y' ? '사용' : '중지')]; ?>
									<?php echo ($v['atc_type'] == 'T' ? '<span class="c_tag yellow">누적 참여형</span>' : '<span class="c_tag green">연속 참여형</span>'); ?>
								</div>
							</td>
							<td class="t_left">
								<?php echo stripslashes($v['atc_title']); ?>
							</td>
							<td class="t_left">
								<div class="t_blue t_full"><?php echo ($v['atc_duplicate'] == 'Y' ? '지급조건 달성시마다' : '한번만 지급'); ?></div>
								<div class="dash_line only_pc_view"><!-- 점선라인 --></div>
								<?php
									if($v['atc_limit'] == 'Y'){
										echo date('Y.m.d' , strtotime($v['atc_sdate'])) . ' ~ ' . date('Y.m.d' , strtotime($v['atc_edate']));
									}else{
										echo '기간제한 없음';
									}
								?>
							</td>
							<td class="t_blue t_right"><span class="hidden_tx">참여수</span><?php echo number_format($arr_cnt['total']); ?>회</td>
							<td class="t_red t_right"><span class="hidden_tx">달성건</span><?php echo number_format($arr_cnt['success']); ?>건</td>
							<td class="this_date"><?php echo printDateInfo($v['atc_rdate']); ?></td>
							<td class="this_ctrl">
                                <div class="lineup-row type_center">
									<?php echo $_mod; ?>
									<?php echo $_del; ?>
									<?php echo $_log; ?>
								</div>
							</td>
						</tr>
				<?php
					}
				}
				?>

			</tbody>
		</table>

		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>



	<script type="text/javascript">

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

		// 기간제한없음 선택시 기간 검색 불가
		$('#pass_uselimit').on('click', function(){
			var _uselimit = $(this).is(':checked');
			// 기간제한없을때 기간수정불가
			if(_uselimit == true){
				$('.js_passdate').attr('disabled',true);
			}else{
				$('.js_passdate').removeAttr('disabled');
			}
		});

	</script>

<?php include_once('wrap.footer.php'); ?>