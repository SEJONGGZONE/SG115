<?php
/*
	https://www.onedaynet.co.kr/od_run.php
*/
echo '<hr><h1 style=;">#필수패키지 적용여부</h1></hr>';
echo '<span style="color:#999">※ 솔루션 구동에 필요한 PHP 필수/선택 패키지(모듈) 설치에 대한 여부</span>';

// 적합테스트 이후 필히 이 파일을 지우세요
$Extension = array(
	array('name'=>'ionCube Loader', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'date', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'libxml', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'openssl', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'pcre', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'zlib', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'bcmath', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'ctype', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'curl', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'dba', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'dom', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'hash', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'filter', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'gd', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'gettext', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'session', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'iconv', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'json', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'mbstring', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'mysql', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'SimpleXML', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'SPL', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'PDO', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'posix', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'Reflection', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'standard', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'soap', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'sockets', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'exif', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'tokenizer', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'xml', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'xmlreader', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'xmlwriter', 'required'=>'Y', 'description'=>'', 'url'=>''),
	array('name'=>'apache2handler', 'required'=>'Y', 'description'=>'서버에따라 없을 수 있음(체크)', 'url'=>''),

	array('name'=>'ftp', 'required'=>'N', 'description'=>'', 'url'=>''),
	array('name'=>'pdo_sqlite', 'required'=>'N', 'description'=>'', 'url'=>''),
	array('name'=>'SQLite', 'required'=>'N', 'description'=>'', 'url'=>''),
	array('name'=>'Zend Optimizer', 'required'=>'N', 'description'=>'', 'url'=>''),
);
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/totalAdmin/_attach') || file_exists($_SERVER['DOCUMENT_ROOT'].'/addons')) { // 하이센스 >= 2.0 제품군
	$Extension = array_merge($Extension, array(array('name'=>'mcrypt', 'required'=>'Y', 'description'=>'php 암호화 라이브러리', 'url'=>''), array('name'=>'mhash', 'required'=>'Y', 'description'=>'php 암호화 라이브러리', 'url'=>''),));
}

$RequiredArr = array('Y'=>'<span style="color:#FF7F27; font-weight:bold;">필수</span>', 'N'=>'선택');
$Installed = array(0=>'<span style="color:#FF7F27; font-weight:bold;">미설치</span>', 1=>'<span style="color:#3F48CC; font-weight:bold;">설치</span>');
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




	<table>
		<colgroup>
			<col width="200" />
			<col width="100" />
			<col width="100" />
			<col width="*" />
			<col width="100" />
		</colgroup>
		<thead>
			<tr>
				<th>패키지</th>
				<th>필수여부</th>
				<th>설치여부</th>
				<th>설명</th>
				<th>관련자료</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($Extension as $k=>$v) {
				if(!$v['name']) continue;
			?>
				<tr>
					<td>
						<?php echo $v['name']; ?>
					</td>
					<td>
						<?php echo ($RequiredArr[$v['required']]?$RequiredArr[$v['required']]:$v['required']); ?>
					</td>
					<td>
						<?php
						echo $Installed[extension_loaded($v['name'])];
						?>
					</td>
					<td>
						<?php echo $v['description']; ?>
					</td>
					<td>
						<?php if($v['url']) { ?>
							<a href="<?php echo $v['url']; ?>" target="_blank">링크</a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<xmp><?php //print_r(get_loaded_extensions()); ?></xmp>
</body>
</html>