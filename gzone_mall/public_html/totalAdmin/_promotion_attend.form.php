<?php

	$app_current_link = "_promotion_attend.list.php";
	$app_current_name = "출석체크 등록";

	// 메뉴 고정
	include_once('wrap.header.php');

	// 기본값 설정
	$_mode = $_mode ? $_mode : 'add';

	if($_mode == 'modify'){
		// 출석체크 이벤트 정보 추출
		$r = _MQ(" select * from smart_promotion_attend_config where atc_uid = '". $_uid ."' ");
		if(!$r) error_msg('잘못된 접근입니다. ');

		// 출석체크 이벤트 보상 지급 조건 정보 추출
		$addinfo = _MQ_assoc(" select * from smart_promotion_attend_addinfo where ata_event = '". $_uid ."' order by ata_days asc ");

		// 출석체크 이벤트 출석현황 추출
		$arr_attend_log = array();
		$arr_attend_member = array();
		// 총 참여수 ,  참여회원수 추출
		$_temp = _MQ_assoc(" select * from smart_promotion_attend_log where atl_event = '". $_uid ."' ");
		if(sizeof($_temp) > 0){
			foreach($_temp as $k=>$v){
				// 총참여수
				$arr_attend_log['total']++;
				// 총 참여회원
				$arr_attend_member[$v['atl_member']] = 1;
				// 보상 지급 횟수 / 총 쿠폰지급수 / 총 적립금 지급액 추출
				if($v['atl_success'] == 'Y'){
					// 보상 지급 횟수
					$arr_attend_log['success'][$v['atl_addinfo_days']]++;
					// 총 쿠폰지급수
					if($v['atl_coupon'] > 0){
						$arr_attend_log['coupon'][$v['atl_coupon']]['cnt']++;
						$arr_attend_log['coupon'][$v['atl_coupon']]['name'] = $v['atl_coupon_name'];
					}
					// 총 적립금 지급액
					if($v['atl_point'] > 0){
						$arr_attend_log['point'] += $v['atl_point'];
					}
				}
			}
			// 총 참여회원 수
			$arr_attend_log['member'] = sizeof($arr_attend_member);
		}
	}

	// 발급 가능한 쿠폰 목록 추출
	$arr_coupon_list = array();
	$coupon_res = _MQ_assoc(" select * from smart_individual_coupon_set where ocs_view = 'Y' and ocs_issued_type = 'auto' and ocs_issued_type_auto = '5' order by ocs_uid desc ");
	if(sizeof($coupon_res) > 0){
		foreach($coupon_res as $k=>$v){
			$str_dprice = printCouponSetBoon($v);
			$arr_coupon_list[$v['ocs_uid']] = '['. strip_tags($arrCouponSet['ocs_type_view'][$v['ocs_type']]) .'] ' . trim(stripslashes($v['ocs_name'])) . ' - ' . $str_dprice;
		}
	}

	// 수정가능여부 - 1회이상 출석체크가 된 경우 수정불가
	$isEditable = ($arr_attend_log['total']*1 > 0 ? false : true);

	// 사용상태를 "중지"에서 "사용"으로 변경시 "중지"되는 다른 이벤트가 있는지 체크
	$isUsed = false;
	if($r['atc_use'] <> 'Y'){
		$usedCnt = _MQ(" select count(*) as cnt from smart_promotion_attend_config where atc_use = 'Y' ");
		if($usedCnt['cnt']>0) $isUsed = true;
	}

	/* 보상 지급 조건 관련 변수/함수 */
	// 내용없을경우 html
	if($isEditable){
		$common_none = '<table class="table_list js_common_none"><tr><td><div class="common_none"><div class="no_icon"></div><div class="gtxt">지급 조건을 추가해주세요.</div></div></td></tr></table>';
	}else{
		$common_none = '<table class="table_list"><tr><td><div class="common_none"><div class="no_icon"></div><div class="gtxt">지급 조건이 추가되지 않았습니다.</div></div></td></tr></table>';
	}

	// 보상 지급 조건 한줄 html
	function get_addinfo_html($arr_coupon_list=array(), $arrInfo, $arr_coupon_error=array(), $isEditable=true ){

		// 에러 배열까지 합치기
		if(sizeof($arr_coupon_error) > 0){
			foreach($arr_coupon_error as $k=>$v){
				$arr_coupon_list[$k] = $v;
			}
		}

		// 쿠폰선택 셀렉트 박스 html 생성
		$app_coupon_select = '<select name="addinfo[_coupon][]" class="js_select_check'. (in_array($arrInfo['ata_coupon'], array_keys($arr_coupon_error)) ? ' select_error' : null) .'" style="'. ($isEditable === false ? '' : null) .'" '. ($isEditable === false ? ' disabled ' : null) .'><option value="0">선택</option>';
		foreach($arr_coupon_list as $k=>$v){
			$app_coupon_select .= '<option value="'. $k .'" class="'. (in_array($k, array_keys($arr_coupon_error)) ? 'option_error' : null) .'" '. ($k == $arrInfo['ata_coupon'] ? ' selected ' : null) .'>'. $v .'</option>';
		}
		$app_coupon_select .= '</select>';

		

		$str = '
			<div class="data_graph_wrap type_benefit js_attend_wrap">
				<div class="data_graph">
					<table class="table_must">
						<colgroup>
							<col width="60"><col width="*"><col width="105">
						</colgroup>
						<tbody>
							<tr>
								<th>출석일</th>
								<td>
									<div class="lineup-row">
										<input type="text" name="addinfo[_days][]" class="design number_style js_addinfo_days" value="'. $arrInfo['ata_days'] .'" placeholder="0" style="width:50px;" '. ($isEditable === false ? ' disabled ' : null) .'>
										<span class="fr_tx">일 이상</span>
									</div>
								</td>
								<td>
									<a href="#none" class="c_btn h22 dark line '. ($isEditable ? ' js_attend_delete_btn ' : null) .'" style="width:75px" >삭제'. ($isEditable == false ? '불가 ' : null) .'</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="data_graph">
					<table class="table_must">
						<colgroup>
							<col width="60"><col width="*"><col width="105">
						</colgroup>
						<tbody>
							<tr>
								<th>쿠폰</th>
								<td class="t_left">
									'. $app_coupon_select .'
								</td>
								<td>
									<div class="lineup-row type_center">
										<input type="text" name="addinfo[_coupon_delay][]" class="design number_style" value="'. $arrInfo['ata_coupon_delay'] .'" placeholder="0" style="width:50px;" '. ($isEditable === false ? ' disabled ' : null) .'>
										<span class="fr_tx">일 후</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="data_graph">
					<table class="table_must">
						<colgroup>
							<col width="60"><col width="*"><col width="105">
						</colgroup>
						<tbody>
							<tr>
								<th>적립금</th>
								<td>
									<div class="lineup-row">
										<input type="text" name="addinfo[_point][]" value="'. $arrInfo['ata_point'] .'" class="design number_style" placeholder="0" style="width:85px" '. ($isEditable === false ? ' disabled ' : null) .'>
										<span class="fr_tx">원</span>
									</div>
								</td>
								<td>
									<div class="lineup-row type_center">
										<input type="text" name="addinfo[_point_delay][]" class="design number_style" value="'. $arrInfo['ata_point_delay'] .'" placeholder="0" style="width:50px;" '. ($isEditable === false ? ' disabled ' : null) .'>
										<span class="fr_tx">일 후</span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="dash_line js_attend_wrap_line"><!-- 점선라인 --></div>
		';

		/* 00일후 지급 기능 미사용

		*/
	
		return preg_replace('/\r\n|\r|\n/', '', trim($str));

	}
	/* 보상 지급 조건 관련 변수/함수 */

?>

<form id="frm" name="frm" method="post" ENCTYPE="multipart/form-data" action="_promotion_attend.pro.php" >
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="isEditable" value="<?php echo ($isEditable ? 'Y' : 'N'); ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">


	<div class="group_title"><strong>참여방법 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용상태</th>
					<td>
						<?php echo _InputRadio( '_use' , array('Y','N'), ($r['atc_use'] ? $r['atc_use'] : 'N') , ' class="'. ($isUsed ? 'js_used_chk' : null) .'" ' , array('사용','중지') , ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('사용으로 변경 시 다른 출석체크 이벤트는 자동으로 <em>중지</em>로 변경됩니다.'); ?>
						<?php echo _DescStr('사용으로 된 경우라도 이벤트 기간이 종료되면 사용자 페이지에서도 종료됩니다.'); ?>
						<?php echo _DescStr('1회 이상 참여가 있는 경우 <em>사용상태</em>를 제외한 아래 모든 항목은 수정할 수 없습니다.' , 'red'); ?>
					</td>
					<th>이벤트 기간</th>
                    <td>
                        <?php echo _InputRadio( '_limit' , array('Y','N'), ($r['atc_limit'] ? $r['atc_limit'] : 'Y') , ' class="js_use_limit" ' . ($isEditable === false ? ' disabled ' : null) , array('기간설정','기간제한 없음') , ''); ?>
                        <div class="dash_line"><!-- 점선라인 --></div>
                        <div class="lineup-row type_date">
                            <input type="text" name="_sdate" value="<?php echo ($r['atc_limit']=='Y'?$r['atc_sdate']:''); ?>" class="design js_pic_day js_limit_date" style="width:85px" placeholder="날짜 선택" readonly <?php echo ($isEditable === false || $r['atc_limit'] == 'N' ? ' disabled ' : null); ?>>
                            <span class="fr_tx">-</span>
                            <input type="text" name="_edate" value="<?php echo  ($r['atc_limit']=='Y'?$r['atc_edate']:''); ?>" class="design js_pic_day js_limit_date" style="width:85px" placeholder="날짜 선택" readonly <?php echo ($isEditable === false || $r['atc_limit'] == 'N' ? ' disabled ' : null); ?>>
                        </div>
                    </td>
				</tr>
				<tr>
					<th>참여방식</th>
					<td>
						<?php echo _InputRadio( '_type' , array('T','C'), ($r['atc_type'] ? $r['atc_type'] : 'T') , ($isEditable === false ? ' disabled ' : null) , array('누적 참여형','연속 참여형') , ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('누적 참여형 : 이벤트 기간의 출석 합산일이 일정 기간 이상인 회원에게 혜택 지급'); ?>
						<?php echo _DescStr('연속 참여형 : 이벤트 기간에 연속으로 일정 기간 출석한 회원에게 혜택 지급'); ?>
					</td>
                    <th>혜택지급 중복</th>
                    <td>

						<?php echo _InputRadio( '_duplicate' , array('N','Y'), ($r['atc_duplicate'] ? $r['atc_duplicate'] : 'N') , ($isEditable === false ? ' disabled ' : null) , array('한번만 지급','조건 달성 시 마다 지급') , ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('한번만 지급 : 이벤트 기간동안 출석체크 보상 지급 만족 시 최초 한번만 지급합니다.'); ?>
						<?php echo _DescStr('조건 달성 시 마다 지급 : 이벤트 기간동안 출석체크 보상 지급 만족 시 계속 지급합니다.'); ?>
                    </td>
				</tr>
				<tr>
					<th>출석체크명</th>
					<td colspan="3">
						<input type="text" name="_title" class="design" value="<?php echo htmlspecialchars(stripslashes($r['atc_title']))?>" placeholder="출석체크명" style="width:500px" <?php echo ($isEditable === false ? ' disabled ' : null); ?>>
						<?php echo _DescStr('출석체크명은 관리용으로 사용자에서는 노출되지 않습니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>타이틀 이미지</th>
					<td colspan="3">
                        <div class="lineup-row">
						    <?php echo _PhotoForm('..'.IMG_DIR_BANNER, '_img_pc', $r['atc_img_pc'], 'style="width:250px"'); ?>
                        </div>
						<?php echo _DescStr('진행중일때 출석체크 상단에 노출되는 이미지 입니다. (권장사이즈 : 1280 x free)'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title">
		<strong>보상지급 설정</strong>
		<div class="btn_box">
			<?php if($isEditable){ ?><a href="#none" class="c_btn h27 blue js_attend_add_btn">+ 추가하기</a><?php }else{ ?><a href="#none" class="c_btn h27 gray line">참여내역이 존재하여 추가불가</a><?php } ?>
		</div>
	</div>
	<div id="js_attend_addinfo">
		<?php
			if(sizeof($addinfo) < 1){
				// 내용없을경우
				echo $common_none;
			}else{
				foreach($addinfo as $k=>$v){
					$arr_coupon_list_tmp = $arr_coupon_list;
					// 등록된 쿠폰이 지급가능한 쿠폰배열에 없을때 별도로 추출
					$arr_coupon_error = array();
					if($v['ata_coupon'] > 0 && !in_array($v['ata_coupon'], array_keys($arr_coupon_list))){
						$_error = _MQ(" select * from smart_individual_coupon_set where ocs_uid = '". $v['ata_coupon'] ."' ");
						if($_error['ocs_uid'] > 0){
							$_error_str = ' - 사용불가';
							if($_error['ocs_view'] == 'N') $_error_str = ' - 발급중지';
							$arr_coupon_error[$_error['ocs_uid']] = '['. $arrCouponSet['ocs_type_view'][$_error['ocs_type']] .'] ' . trim(stripslashes($_error['ocs_name'])) . $_error_str;
							$arr_coupon_list_tmp[$_error['ocs_uid']] = '['. $arrCouponSet['ocs_type_view'][$_error['ocs_type']] .'] ' . trim(stripslashes($_error['ocs_name'])) . $_error_str;
						}else{
							$arr_coupon_error[$v['ata_coupon']] = '쿠폰이 삭제되었습니다.';
							$arr_coupon_list_tmp[$v['ata_coupon']] = '쿠폰이 삭제되었습니다.';
						}
					}
					echo get_addinfo_html($arr_coupon_list_tmp, $v, $arr_coupon_error, $isEditable);
				}
			}
		?>
	</div>
	<div class="tip_box">
		<?php echo _DescStr('보상 지급 조건은 쿠폰과 적립금을 동시에 지급할 수 있으며 사용은 각 설정을 따릅니다.'); ?>
		<?php echo _DescStr('지급기한이 "0일"일 경우 지급조건 달성 즉시 혜택을 지급하며 내역이 있을 경우 조건 추가및 수정이 불가합니다. '); ?>
		<?php echo _DescStr('쿠폰을 지급하기 위해서는 쿠폰관리에서 자동발급 쿠폰(출석체크)을 먼저 생성해야합니다. <a href="/totalAdmin/_coupon_set.list.php"><em>쿠폰관리 바로가기</em></a>'); ?>
	</div>


	<div class="group_title"><strong>참여현황 통계</strong></div>
	<div class="data_graph_wrap">
		<div class="data_graph">
			<table class="table_must">
				<thead>
					<tr>
						<th scope="col">참여 건수</th>
						<th scope="col">참여 회원수</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="t_right t_blue"><?php echo number_format($arr_attend_log['total']); ?>회</td>
						<td class="t_right t_red"><?php echo number_format($arr_attend_log['member']); ?>명</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="data_graph">
			<table class="table_must">
				<thead>
					<tr>
						<th scope="col">쿠폰 지급 횟수</th>
						<th scope="col">적립금 지급액</th>
						<th scope="col">보상 지급 횟수</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="t_right t_blue">
							<?php
								if(sizeof($arr_attend_log['coupon']) > 0){
									foreach($arr_attend_log['coupon'] as $k=>$v){
										$_ex = explode('-', $v['name']);
										echo '+ ' . trim($_ex[0]) . ' : ' . $v['cnt'] . '회<br>';
									}
								}else{
									echo '0회';
								}
							?>
						</td>
						<td class="t_right t_blue"><?php echo number_format($arr_attend_log['point']); ?>원</td>
						<td class="t_right t_red">
							<?php
								if(sizeof($arr_attend_log['success']) > 0){
									foreach($arr_attend_log['success'] as $k=>$v){
										echo ($r['atc_type'] == 'T' ? '+ 누적참여' : '+ 연속참여 ') . $k . '일 : ' . $v . '회<br>';
									}
								}else{
									echo '0회';
								}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<?php echo _submitBTN('_promotion_attend.list.php'); ?>

</form>



	<script type="text/javascript">

	// 보상지급 index 초기화
	function attend_index_init(){
		if( $('.js_attend_wrap').length < 1){ return false; }
		$('.js_attend_wrap').each(function(i,v){$(v).attr('data-index',i);});
		$('.js_attend_wrap_line').each(function(i,v){$(v).attr('data-index',i);});		
	}


	$(function() {

		// 이벤트 기간설정에 따른 시작일/종료일 설정 여부 체크
		$('.js_use_limit').on('click', function(){
			<?php echo ($isEditable === false ? ' return false; // 출석체크 진행 이후에는 수정불가 ' : null); ?>
			var _limit = $(this).val();
			// 기간제한없을때 기간수정불가
			if(_limit == 'N'){
				$('.js_limit_date').attr('disabled',true);
			}else{
				$('.js_limit_date').removeAttr('disabled');
			}
		});


		// - validate ---
		$('#frm').validate({
			ignore: '.ignore',
			rules: {
				_title: {required: true , minlength: 4 },
				_sdate:{ required : function() { return ($('input[name=_limit]:checked').val()=='Y'?true:false); } },
				_edate:{ required : function() { return ($('input[name=_limit]:checked').val()=='Y'?true:false); }}
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
			},
			messages: {
				_title: {required: '이벤트명을 입력해주시기 바랍니다.' , minlength: '이벤트명은 최소 4글자 이상 입력해주시기 바랍니다.' },
				_sdate:{ required : '이벤트 시작일을 입력해주시기 바랍니다.'},
				_edate:{ required : '이벤트 종료일을 입력해주시기 바랍니다.'}
			},
			submitHandler : function(form) {

				// 보상지급설정이 없을 경우 체크 
				if( $('.js_addinfo_days').length < 1){
					alert('보상지급 설정은 최소 1개이상 등록해 주시기 바랍니다.');
					return false;
				}

				// 보상 지급 조건은 일수가 중복될 수 없고 빈값(0포함)을 입력할 수 없음
				var trigger_days = true;
				var str_days = '';
				$('.js_addinfo_days').each(function(){
					var _day = $(this).val();

					// 빈값(0포함) 체크
					if(_day*1 == 0){
						alert('보상 지급 조건은 1일이상 입력해주시기 바랍니다.');
						$(this).focus();
						trigger_days = false;
						return false;
					}

					// 중복 체크
					if(str_days.indexOf(_day) > -1){
						alert('보상 지급 조건이 중복되었습니다. 확인 후 다시 시도해주시기 바랍니다.');
						$(this).focus();
						trigger_days = false;
						return false;
					}

					// 중복 체크를위해 문자열에 저장
					str_days += ('|' + _day);
				});

				// 보상지급 1개이상 체크 
				var trigger_check = true;
				$('.js_attend_wrap').each(function(i,v){
					var cp = $(v).find('[name="addinfo[_coupon][]"]').val()*1;
					var pi = $(v).find('[name="addinfo[_point][]"]').val()*1;
					if( cp < 1  && pi < 1){ trigger_check = false;return false; }
				});
				if( trigger_check !== true){  alert('보상지급 설정을 모두 입력해 주세요.'); return false; }

				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				if(trigger_days){
					form.submit();
				}
			}
		});
		// - validate ---


		// 보상지급 index 초기화
		attend_index_init();

	});

	// 보상 지급 조건 추가
	$(document).delegate('.js_attend_add_btn', 'click', function(){
		// common_none 삭제
		$('#js_attend_addinfo .js_common_none').remove();

		// 보상 지급 조건 한줄 추가
		$('#js_attend_addinfo').append('<?php echo get_addinfo_html($arr_coupon_list, array(), array(), $isEditable); ?>');

		attend_index_init();

	});

	// 보상 지급 조건 삭제
	$(document).delegate('.js_attend_delete_btn', 'click', function(){

		// 한줄 삭제
		var $parent = $(this).closest('.js_attend_wrap');
		var index = $parent.data('index');
		$parent.remove();
		$('.js_attend_wrap_line[data-index="'+index+'"]').remove();

		// js_attend_wrap_line

		// common_none 추가
		if( $('.js_attend_wrap').length < 1 ){
			$('#js_attend_addinfo').html('<?php echo $common_none; ?>');
		}
	});

	// 사용불가 쿠폰 선택시 에러클래스 추가
	$(document).delegate('.js_select_check', 'change', function(){
		var is_error = $(this).find('option:selected').hasClass('option_error');
		if(is_error){
			$(this).addClass('select_error');
		}else{
			$(this).removeClass('select_error');
		}
	});

	// 사용상태 변경시 사용중인 다른 이벤트가 있다면 알림
	var used_chk_trigger = true; // 경고창 한번만 노출
	$(document).on('change', '.js_used_chk', function(){
		if(used_chk_trigger && $('.js_used_chk:checked').val() == 'Y'){
			alert('현재 이벤트의 사용상태를 "사용"으로 변경하면\n사용상태가 "사용"으로 설정된 다른 이벤트는 \n사용상태가 "중지"로 변경됩니다.');
			used_chk_trigger = false;
		}
	});
	</script>


<?php include_once('wrap.footer.php'); ?>