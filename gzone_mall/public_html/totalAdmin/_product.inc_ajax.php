<?php 
	include "./inc.php";

	switch ($_mode) {
		// 입점업체 아이로부터 주소를 추출
		case 'get_com_juso':
			if( empty($comid)){  die(json_encode(array('rst'=>'fail','msg'=>'입점업체를 선택해주시기 바랍니다.'))); }

			// search address 
			$comInfo = _MQ("select cp_address , cp_tel, cp_name from smart_company where cp_id = '".$comid."' ");

			// 주소에 따른 좌표를 추출
			$com_mapx = '';
			$com_mapy = '';
			if( $comInfo['cp_address'] != '' && $siteInfo['kakao_api'] != ''){
				$getAddress = kakao_addr_search($comInfo['cp_address']); 
				if( !empty($getAddress['documents'][0]['x']) && !empty($getAddress['documents'][0]['y'])){
					$com_mapx = $getAddress['documents'][0]['x'];
					$com_mapy = $getAddress['documents'][0]['y'];
				}				
			}
			die(json_encode(array('rst'=>'success','tel'=>$comInfo['cp_tel'],'locname'=>$comInfo['cp_name'],'address'=>$comInfo['cp_address'],'com_mapx'=>$com_mapx, 'com_mapy'=>$com_mapy)));

		break;
		
	}