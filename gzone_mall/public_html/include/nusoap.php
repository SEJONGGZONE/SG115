<?php 
// PHP 버전분기 처리
$exp_phpVersion = explode(".",phpversion());
$rst_phpVersion = $exp_phpVersion[0];
$path_soaplib = $_SERVER['DOCUMENT_ROOT'].'/include/soapLib';

// LCY : 2023-11-21 : SOAP PHP7고정
//if( $rst_phpVersion == 7){
	//$path_soaplib = $_SERVER['DOCUMENT_ROOT'].'/include/soapLib7';
//}
include_once $path_soaplib."/nusoap.php";
