<?php
//<!-- <p>GetCashBillEx - 문서 정보</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillEx(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $ID,
		'MgtKey'	=> $MgtKey
	))->GetCashBillExResult;

//	if ($Result->TradeType < 0){ //실패
//		echo $Result->TradeType;
//	}else{ //성공
//		echo '<pre>';
//		print_r($Result);
//		echo '</pre>';
//	}
?>