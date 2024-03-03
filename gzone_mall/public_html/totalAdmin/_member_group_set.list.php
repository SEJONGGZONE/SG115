<?php
	// -- LCY -- 회원등급관리
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

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt from smart_member_group_set where 1 ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	// 넘길 변수 설정하기
	$res = _MQ_assoc(" select mgs.*, (select count(*) as cnt from smart_individual where in_mgsuid = mgs_uid ) as cnt from smart_member_group_set as mgs  where 1 order by mgs_idx desc, mgs_rank desc");

?>
	<div class="data_search">
		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>회원 등급</strong><!-- 메뉴얼로 링크 -->
			<div class="btn_box">
				<a href="/totalAdmin/_config.group.php" class="c_btn h46 sky line" target="_blank">등급정책 설정</a>
				<a href="_member_group_set.form.php?_mode=add" class="c_btn h46 red" >등급등록</a>
			</div>
		</div>
	</div>



	<div class="data_list">
		<form name="frm" id="frm" method="post" target="common_frame" action="_member_group_set.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_uid" value="">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 gray line">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27 gray line">선택해제</a>
				<a href="#none" onclick=" return false;" class="c_btn h27 black line on-select-delete">선택삭제</a>
				<a href="#none" onclick=" return false;" class="c_btn h27 blue on-select-idx">순서 일괄적용</a>
			</div>
			<div class="right_box">

			</div>
		</div>

		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="100"/><col width="150"/><col width="120"/><col width="*"/><col width="*"/><col width="100"/><col width="110"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">등급순서</th>
					<th scope="col">등급명</th>
					<th scope="col">회원 수</th>
					<th scope="col">평가조건</th>
					<th scope="col">혜택</th>
					<th scope="col">등록일</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
			<?php
				if(count($res) >  0) {
					foreach($res as $k=>$v) {

						$printRank = $v['mgs_rank'];
						$printName = $v['mgs_name'];

						// -- 등록된 회원수를 가져온다.
						$rowRcnt = $v['cnt'];
						$printRcnt = number_format($rowRcnt)."명"; //  등록된 회원 수

						// -- 등급조건을 가져온다.
						$arrCondition = array(); $printCondition = '';
						if($v['mgs_condition_totprice'] > 0){ $arrCondition[] = number_format($v['mgs_condition_totprice']).'원 이상 구매'; }
						if($v['mgs_condition_totcnt'] > 0){ $arrCondition[] = number_format($v['mgs_condition_totcnt']).'회 이상 구매'; }
						if(count($arrCondition) > 0){ $printCondition = implode(" + ",$arrCondition); }
						else{ $printCondition = '<span class="t_none">제한없음</span>'; }

						// -- 등급혜택을 가져온다.
						$arrBoon = array(); $printBoon = '';
						if($v['mgs_give_point_per'] > 0){ $arrBoon[] = number_format($v['mgs_give_point_per'],1).'% 적립'; }
						if($v['mgs_sale_price_per'] > 0){ $arrBoon[] = number_format($v['mgs_sale_price_per'],1).'% 할인'; }
						if(count($arrBoon) > 0){ $printBoon = implode(" + ",$arrBoon); }
						else{ $printBoon = '<span class="t_none">없음</span>'; }

						// -- 등록일
						$printRdate = printDateInfo($v['mgs_rdate']);

						// {{{회원등급추가}}}
						$disabledDeleteClass = $readonlyAttr = '';
						if($v['mgs_rank'] == 1 ){ $disabledDeleteClass = 'disabled'; $readonlyAttr = "readonly";   }
						else{
							$readonlyAttr .=" tabindex='".($k+1)."' ";
						}

						$printBtn = '
							<div class="lineup-row type_center">
								<a href="_member_group_set.form.php?_mode=modify&_uid='.$v['mgs_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22 gray">수정</a>
								<a href="#none" onclick="return false;" class="c_btn h22 dark line on-get-delete '.$readonlyClass.'" data-rcnt="'.$rowRcnt.'" data-uid="'.$v['mgs_uid'].'">삭제</a>
							</div>
						'; // 관리버튼

						$printIdx = '<input type="text" name="_idx['.$v['mgs_uid'].']" value="'.$v['mgs_idx'].'" data-value="'.$v['mgs_idx'].'" class="design js_input_idx number_style" '.$readonlyAttr.' placeholder="0" style="width:50px;">';
				?>
					<tr>
						<td class="this_check"><label class="design"><input type="checkbox" class="js_ck mgs-uid <?php echo $disabledDeleteClass;?>" name="arrUid[]" value="<?php echo $v['mgs_uid'];?>" <?php echo $disabledDeleteClass;?>></label></td>
						<td class="this_state"><?php echo $printIdx;?></td>
						<td class="t_blue t_bold"><?php echo $printName;?></td>
						<td class="t_sky"><?php echo $printRcnt;?></td>
						<td class="t_left "><?php echo $printCondition;?></td>
						<td class="t_left t_orange"><?php echo $printBoon;?></td>
						<td class="this_date" ><?php echo $printRdate;?></td>
						<td class="this_ctrl"><?php echo $printBtn;?></td>
					</tr>
				<?php }?>
			<?php }?>
			</tbody>
		</table>
	</form>
	</div>

	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>

	<?php if(count($res) <0){?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 등급이 없습니다.</div></div>
	<?php } ?>



	<script>

		$(document).on('click','.on-get-delete',function(){
			if( confirm("해당 등급을 삭제 하시겠습니까?") == false){ return false; }
			var chk = $(this).hasClass('disabled');
			var _uid = $(this).attr('data-uid');
			var _rcnt = $(this).attr('data-rcnt');
			if( _uid == undefined || _uid == ''){ alert("등급 정보가 없습니다."); return false; }
			if( chk == true){ alert("기본등급은 삭제가 불가능합니다."); return false; }
			if( _rcnt > 0){ alert("등록된 회원이 있는 등급은 삭제가 불가능합니다."); return false; }

			$('form#frm [name="_mode"]').val('delete');
			$('form#frm [name="_uid"]').val(_uid);
			$('form#frm').submit();

			$('form#frm [name="_mode"]').val('');
			$('form#frm [name="_uid"]').val('');
			return true;
		})

		$(document).on('click','.on-select-delete',function(){
			if( confirm("선택하신 등급을 삭제 하시겠습니까?") == false){ return false; }
			var chkLen = $('.js_ck:checked').length *1;
			if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }

			$('form#frm [name="_mode"]').val('selectDelete');
			$('form#frm').submit();
			$('form#frm [name="_mode"]').val('');
			return true;
		})


		// {{{회원등급추가}}}
		$(document).on('focusout','.js_input_idx',function(){
			var dval = $(this).attr('data-value')*1
			if( $(this).val()*1 == 0){
				$(this).val( (dval == 0 ? 1 : dval) )
			}
			$(this).attr('data-value', $(this).val() );
		})

		$(document).on('click','.on-select-idx',function(){

			if( confirm("입력된 등급순서를 일괄적용 하시겠습니까?") == false){ return false; }
			var rstChkCnt = 0;
			$('.js_input_idx').each(function(i,v){
				if( $(v).val()*1 == 0 ){ rstChkCnt ++; }
			})

			if( rstChkCnt > 0) { alert("등급순서는 1이상 입력해 주세요."); return false; }

			$('form#frm [name="_mode"]').val('execIdx');
			$('form#frm').submit();
			$('form#frm [name="_mode"]').val('');
			return true;
		});

	</script>
<?php	include_once('wrap.footer.php'); ?>
