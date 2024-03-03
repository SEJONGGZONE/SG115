<?php 
	if( $p_info['p_dateoption_use'] != 'Y'){  return false; } // 달력옵션 아닐경우 막기

	$calendar = array();

	// 상품에서 예약 가능한 달력의 날짜를 가져온다.
	$calendar = get_product_dateoption_enable_date($pcode);

	$calendar['startYear'] = date('Y',strtotime($calendar['startDate']));
	$calendar['startMonth'] = date('m',strtotime($calendar['startDate']));

	$option_type_default_title = array('noraml'=>'옵션을 선택해주세요.','color'=>'Color 옵션','size'=>'Size 옵션');

	$p_option1_title = $p_option2_title = $p_option3_title = '';
	if( in_array(rm_str($p_info['p_option_type_chk']), array('1','2','3'))){
		$p_option1_title = $p_info['p_option1_title']!=''?$p_info['p_option1_title']:$option_type_default_title[$p_info['p_option1_type']];
	}
	if( in_array(rm_str($p_info['p_option_type_chk']), array('2','3'))){
		$p_option2_title = $p_info['p_option2_title']!=''?$p_info['p_option2_title']:$option_type_default_title[$p_info['p_option2_type']];
	}
	if( in_array(rm_str($p_info['p_option_type_chk']), array('3'))){
		$p_option3_title = $p_info['p_option3_title']!=''?$p_info['p_option3_title']:$option_type_default_title[$p_info['p_option3_type']];
	}


?>
<div class="p_Vcalendar js_product_view_calendar">
	<div class="month_box">
		<a href="#none" onclick="calendar.action('prev'); return false;" class="btn prev if_no" title="이전달"></a>
		<strong class="view_date"><?php echo $calendar['startYear'] ?>.<?php echo $calendar['startMonth']; ?></strong>
		<a href="#none" onclick="calendar.action('next'); return false;" class="btn next" title="다음달"></a>
	</div>
	<div class="calendar">
		<ul class="week">
			<li>SUN</li>
			<li>MON</li>
			<li>TUE</li>
			<li>WED</li>
			<li>THU</li>
			<li>FRI</li>
			<li>SAT</li>
		</ul>
	</div><!-- end calendar -->
</div><!-- end p_Vcal -->
<script type="text/javascript">
	var calendar = {

		pcode : '<?php echo $pcode ?>',
		selDate : '', // 선택된 날짜

		// 현재 날짜
		toDate : {
			'year': '<?php echo date('Y'); ?>',
			'month': '<?php echo date('m'); ?>',
		},

		// 업데이트 실시간 날짜
		nowDate : {
			'year': '<?php echo $calendar['startYear']; ?>',
			'month': '<?php echo $calendar['startMonth']; ?>',
		},

		// 옵션타이틀 정보 
		option : {
			'check' : <?php echo rm_str($p_info['p_option_type_chk']);  ?>,
			'depth1': '<?php echo $p_option1_title ?>',
			'depth2': '<?php echo $p_option2_title ?>',
			'depth3': '<?php echo $p_option3_title ?>',
		},

		// auth
		auth : {
			loadCalendar : false,
		},

		// 이전/다음
		action : function(mode){
			var thisObj = this;
			var	curYear = parseInt(thisObj.nowDate.year);
			var curMonth = parseInt(thisObj.nowDate.month);

			if( typeof mode == 'undefined' || mode == ''){ return false; }
			if( mode == 'next'){
				curMonth++;
				if(curMonth >= 13) {
					curMonth = 1;
					curYear += 1;
				}
			}
			else if(mode == 'prev'){
				curMonth--;
				if(curMonth <= 0) {
					curMonth = 12;
					curYear -= 1;
				}
			}
			else{ return false; }

			thisObj.auth.loadCalendar = true;
			thisObj.loadCalendar(curYear,curMonth);
		},

		// 달력 불러오기
		loadCalendar : function(year, month){
			var thisObj = this;
			if( thisObj.auth.loadCalendar !== true){ return false; }
			thisObj.auth.loadCalendar = false;

			var $productViewCalendar = $('.js_product_view_calendar');
			var data = {pcode : thisObj.pcode , year : year , month : month , selDate : thisObj.selDate };
			$.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/product.view.inc_calendar.ajax.php', type:'get', data:data, dataType:'json'})
			.done(function(e){
				if( e == null || !e.rst){  alert("잘못된 접근입니다."); return false; }
				if( e.rst != 'success'){ alert(e.msg);return false;}
				$productViewCalendar.find('.view_calendar_item').remove();
				$productViewCalendar.find('.calendar').append(e.view);
				$productViewCalendar.find('.view_date').text(e.viewDate);

				thisObj.nowDate.year = year;
				thisObj.nowDate.month = month;
				month = month<10?'0'+month:month;
				var checkToDate = thisObj.toDate.year+''+thisObj.toDate.month;
				var checkNowDate = year+''+month;
				if( checkToDate != checkNowDate){ $productViewCalendar.find('.btn.prev').removeClass('if_no');  }
				else{  $productViewCalendar.find('.btn.prev').addClass('if_no');}
			});
		},

		// 달력선택 
		selectDate : function(selDate){
			var thisObj = this;
			if( typeof selDate == 'undefined' || !selDate){ return false; }
			var $productViewCalendar = $('.js_product_view_calendar');
			var $selDate = $productViewCalendar.find('.view_calendar_item .day[data-date="'+selDate+'"]');
			 var $form = $('#frmProductView');
			 $form.find('[name="selDate"]').val('');

			if( $selDate.length < 1){ return false; }

			// 날짜 체크
			var selectDateChk = $selDate.closest('li').hasClass('hit');

			// 기본 선택 비우기
			$productViewCalendar.find('.view_calendar_item li').removeClass('hit');

			// 옵션 선택 초기화 
			thisObj.optionInit();

			// 선택에 따른 처리
			if( selectDateChk == true){
				thisObj.selDate = '';
			}
			else{
				thisObj.selDate = selDate;
				$selDate.closest('li').addClass('hit');
				$('#span_option1').removeClass('before');
			}
			$form.find('[name="selDate"]').val(thisObj.selDate);
		},

		// 옵션선택 초기화 (날짜선택에 따른 처리)
		optionInit : function(){
			var thisObj = this;

			// 기본 옵션선택 비우기
			if(thisObj.option.depth1 != ''){
				$('#option_select1_poname').text(thisObj.option.depth1);
				$('#option_select1_id').val('');
				$('#span_option1').addClass('before');
			}
			if(thisObj.option.depth2 != ''){
				$('#option_select2_poname').text(thisObj.option.depth2);
				$('#option_select2_id').val('');
				$('#span_option2').addClass('before');
			}
			if(thisObj.option.depth3 != ''){
				$('#option_select3_poname').text(thisObj.option.depth3);
				$('#option_select3_id').val('');
				$('#span_option3').addClass('before');
			}						
		}
	}
	$(document).ready(function(){
		calendar.auth.loadCalendar = true;
		calendar.loadCalendar('<?php echo $calendar['startYear']; ?>',<?php echo $calendar['startMonth']; ?>);
	});
</script>