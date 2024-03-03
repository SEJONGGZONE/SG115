<?php


/*
	https://www.onedaynet.co.kr/od_run.php
*/
function od_run_curl_http_code($url = false,$return = true)
{
	if( $key === false){ return false; }

	$curl_url = $url;
	$ch = curl_init($curl_url);
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if( $return === false){ 
		if( in_array($http_code,array(200,301,302,403))){ return true; }
		else{ return false; }
	}else{
		return $http_code;
	}
	return $http_code;
}


$__code = od_run_curl_http_code('http://'.$_SERVER['HTTP_HOST'].'/od_run_secure/index.php');
echo '<hr><h1>#보안체크1</h1></hr>';
echo '<span style="color:#999">※ 현재 서버에 .htaccess 에 대한 보안설정이 가능한지 체크</span>';
if( $__code != '403'){  
	echo '<h3 style="color:red"> >> 아파치 보안 취약 - 서버업체 문의하여 해당 메세지가 발생되지 않도록 처리 필요.</h3>';

	echo '<div style="background-color:#999;"><a href="https://www.google.com/search?q=.htaccess+%EC%95%84%ED%8C%8C%EC%B9%98+%EC%84%A4%EC%A0%95&oq=.ht&aqs=chrome.1.69i60j69i59l3j69i57j69i60l3.2860j0j1&sourceid=chrome&ie=UTF-8" target="_blank"></a></div>';
}else{
	echo '<h3 style="color:blue"> >> 정상</h3>';
}

$__code = od_run_curl_http_code('http://'.$_SERVER['HTTP_HOST'].'/.htaccess');
echo '<hr><h1>#보안체크2</h1></hr>';
echo '<span style="color:#999">※  http://'.$_SERVER['HTTP_HOST'].'/.htaccess 접속 시 정상적으로 외부로 노출안되는지 체크</span>';
if( $__code != '403'){  
	echo '<h3 style="color:red"> >>아파치 보안 취약 - 하단 소스 아파치 httpd.conf 에 추가 또는 서버업체 문의</h3>';

	echo '
		<pre style="background-color:#999;">'.htmlspecialchars('

	<Files ".ht*"> 
		Require all denied 
	</Files>				
		').'</pre>
	';
}else{
	echo '<h3 style="color:blue"> >> 정상</h3>';
}

?>
<!DOCTYPE html>
<html lang="ko-KR">
<html>
<head>
	<title>원데이넷 구동 전 테스트</title>
	<meta charset="utf-8">
	<style type="text/css">
		table {
			width: 100%;
			border-collapse: collapse;
			text-align: left;
			line-height: 1.5;

		}
		table thead th {
			padding: 10px;
			font-weight: bold;
			vertical-align: top;
			color: #369;
			border-bottom: 3px solid #036;
		}
		table tbody th {
			padding: 10px;
			font-weight: bold;
			vertical-align: top;
			border-bottom: 1px solid #ccc;
			background: #f3f6f7;
		}
		table td {
			padding: 10px;
			vertical-align: top;
			border-bottom: 1px solid #ccc;
		}
	</style>
</head>
<body>
</body>
</html>