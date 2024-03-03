<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$p_info = get_product_info($pcode);
$calendar = array();

try{

	// 상품체크
	if(!$pcode || count($p_info) < 1){ throw new Exception("상품조회에 실패하였습니다.", __LINE__); }
	if( $p_info['p_dateoption_use'] != 'Y'){ throw new Exception("이용이 불가능합니다.", __LINE__);}
	if( empty($year) || empty($month)){
		$year = date('Y');
		$month = date('m');
	}

	// 현재 날짜
	$day = date('d');

	// 월보정
	$month = (rm_str($month) < 10 ? '0': null).$month;

	// 일자정보를 지정
	$calendar['dayInfo'] = array();

	// 현재날짜 미리 선언 
	$calendar['nowInfo']['date'] = date('Y-m-d',strtotime($year.$month.'01'));
	$calendar['nowInfo']['exDate'] = explode("-",$calendar['nowInfo']['date']);
	$calendar['nowInfo']['year'] = $calendar['nowInfo']['exDate'][0];
	$calendar['nowInfo']['month'] = $calendar['nowInfo']['exDate'][1];	

	// 이전 -- 순서변경되면 안됨
	$calendar['dayInfo']['prev']['date'] = date('Y-m-d',strtotime("-1 month",strtotime($calendar['nowInfo']['date'])));
	$calendar['dayInfo']['prev']['exDate'] = explode("-",$calendar['dayInfo']['prev']['date']);
	$calendar['dayInfo']['prev']['year'] = $calendar['dayInfo']['prev']['exDate'][0];
	$calendar['dayInfo']['prev']['month'] = $calendar['dayInfo']['prev']['exDate'][1];

	// 현재 -- 순서변경되면 안됨
	$calendar['dayInfo']['now']['date'] = $calendar['nowInfo']['date'];
	$calendar['dayInfo']['now']['exDate'] = $calendar['nowInfo']['exDate'];
	$calendar['dayInfo']['now']['year'] = $calendar['nowInfo']['year'];
	$calendar['dayInfo']['now']['month'] = $calendar['nowInfo']['month'];

	// 다음 -- 순서변경되면 안됨
	$calendar['dayInfo']['next']['date'] = date('Y-m-d',strtotime("+1 month",strtotime($calendar['nowInfo']['date'])));
	$calendar['dayInfo']['next']['exDate'] = explode("-",$calendar['dayInfo']['next']['date']);
	$calendar['dayInfo']['next']['year'] = $calendar['dayInfo']['next']['exDate'][0];
	$calendar['dayInfo']['next']['month'] = $calendar['dayInfo']['next']['exDate'][1];

	// 날자 체크
	if( ($calendar['dayInfo']['now']['year'].$calendar['dayInfo']['now']['month']) < date('Ym')){ throw new Exception("달력날짜가 잘못되었습니다.", __LINE__); }

	// 부가정보를 다시 채운다.
	foreach($calendar['dayInfo'] as $k=>$v){
		$calendar['dayInfo'][$k]['totalDay'] = Month_Day($v['month'],$v['year']); // 기본 달을 가져온다. 
		$calendar['dayInfo'][$k]['monthSolarStart'] = date("Y-m-d" , strtotime($v['year']."-".$v['month']."-01")); // 이번달 양력 첫날
		$calendar['dayInfo'][$k]['monthSolarEnd'] = date("Y-m-d" , strtotime($v['year']."-".$v['month']."-".$calendar['dayInfo'][$k]['totalDay']."")); // 이번달 양력 마지막날
		$calendar['dayInfo'][$k]['firstDay']=date("w", strtotime($calendar['dayInfo'][$k]['monthSolarStart']) ); //선택한 월 1일의 요일을 구함. 일요일은 0.		
	}


	// 상품에서 예약 가능한 달력의 날짜를 가져온다.
	$calendar['startDate'] = $calendar['endDate'] = '';
	if( $p_info['p_dateoption_date_type'] == 'day'){
		$calendar['startDate'] = date('Y-m-d',strtotime("+ ".$p_info['p_dateoption_sday']."days"));
		if( $p_info['p_dateoption_eday'] > 0){
			$calendar['endDate'] = date('Y-m-d',strtotime("+ ".$p_info['p_dateoption_eday']."days",strtotime($calendar['startDate'])));
		}

	}
	else{
		$calendar['startDate'] = $p_info['p_dateoption_sdate'];
		$calendar['endDate'] = $p_info['p_dateoption_edate'];
	}	

	// 만약 시작날짜가 오늘보다 작다면 오늘로 고정
	$calendar['startDate'] = $calendar['startDate'] < date('Y-m-d') ? date('Y-m-d') : $calendar['startDate'];

	// 요일별/날짜별 불가설정
	$calendar['exDateoptionExWeek'] = $p_info['p_dateoption_ex_week'] != '' ? explode(",",$p_info['p_dateoption_ex_week']) : array();
	$calendar['exDateoptionExDate'] = $p_info['p_dateoption_ex_date'] != '' ? explode(",",$p_info['p_dateoption_ex_date']) : array();

	$calendar['exStartDate'] = explode("-",$calendar['startDate']);
	$calendar['exEndDate'] = explode("-",$calendar['endDate']);

	$calendar['endYear'] = empty($calendar['exEndDate'][0]) ? 0 : $calendar['exEndDate'][0];
	$calendar['endMonth'] = empty($calendar['exEndDate'][1]) ? 0 : $calendar['exEndDate'][1];

	$calendar['startYear'] = date('Y');
	$calendar['startMonth'] = date('n');


	// 상품별 당일 예약시간체크
	$p_info['p_dateoption_stime'] = rm_str($p_info['p_dateoption_stime']) < 1 ? 0 : $p_info['p_dateoption_stime'].':00';
	$p_info['p_dateoption_etime'] = rm_str($p_info['p_dateoption_etime']) < 1 ? 0 : $p_info['p_dateoption_etime'].':00';  // ADD :: 시간 추가 -- LCY 2017-07-24 	


	// 예약불가 요일을 배열로 가져온다.
	$arr_dateoption_ex_week = $p_info['p_dateoption_ex_week'] != '' ? explode(",",$p_info['p_dateoption_ex_week']) : array();	
	$arr_dateoption_ex_week = array_filter($arr_dateoption_ex_week);

	// 예약불가 날짜별 설정을 배열로 가져온다.
	$arr_ex_date = array();
	$arr_dateoption_ex_date = $p_info['p_dateoption_ex_date'] != '' ? explode(",",$p_info['p_dateoption_ex_date']) : array(); 
	$arr_dateoption_ex_date = array_filter($arr_dateoption_ex_date);


}catch(Exception $e){
	$code = $e->getCode();
	$msg = $e->getMessage();
	die(json_encode(array('rst'=>'fail','msg'=>'('.$code.') '.$msg)));
}


ob_start();
@include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
$_view = ob_get_clean();

die(json_encode(array('rst'=>'success','view'=>$_view,'calendar'=>$calendar,'viewDate'=>date('Y.m',strtotime($year.rm_str($month)."01")) )));


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행