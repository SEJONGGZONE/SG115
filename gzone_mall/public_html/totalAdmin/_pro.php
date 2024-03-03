<?php // 실행프로그램 -기타 
include_once("./inc.php");

switch($_mode){

	// 검색상세 열고닫기 캐시용 쿠키를 생성
	case "set_search_detail_cache_cookie":
		if( empty($name)){ die(json_encode(array('rst'=>'fail'))); }
		$checked = $checked ? $checked : 'N';


		if( $checked == 'N'){
			unset($search_detail_cache[$name]);
		}
		else{
			$search_detail_cache[$name] = 'Y';
		}
		$search_detail_cache = array_filter($search_detail_cache);
		if( count($search_detail_cache) > 0){
			samesiteCookie('search_detail_cache', base64_serialize($search_detail_cache), time()+3600*24*365, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
		}
		else{
			samesiteCookie('search_detail_cache', '' , time()-3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
		}

		// if( $checked == 'N'){ samesiteCookie($name, $checked, time()-3600, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host'])))); }
		// else{  samesiteCookie($name, $checked, time()+3600*24*365, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));}
		
	break;
}
	
