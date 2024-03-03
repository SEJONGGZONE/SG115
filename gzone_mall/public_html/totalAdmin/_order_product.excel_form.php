<?php
$app_current_link = '_order_product.list.php';
include_once('wrap.header.php');
include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
$Excel = ExcelLoader($_FILES['excel_file']['tmp_name']);
$ExcelCnt = count($Excel)-2;


// 엑셀키값
$excel_keys = array(
'고유번호',
'주문번호',
'주문일시',
'주문자 이름',
'주문자 휴대폰',
'받는분 이름',
'받는분 휴대폰',
'받는분 우편번호',
'받는분 주소',
'받는분 지번주소',
'상품코드',
'대표상품명',
'옵션1',
'옵션2',
'옵션3',
'판매단가',
'수량',
'금액',
'배송상태',
'택배사',
'송장번호',
'배송시 유의사항',
'관리자메모',
);


$excel_values = array();
$idx = 0;
foreach($Excel as $k=>$v) {
	if($k < 2) continue; // 파일정보와 헤더는 제외
	foreach($excel_keys as $kk=>$vv) $excel_values[$idx][$vv] = $v[$kk];
	$idx ++;
}


// 엑셀 간략 검증
$OPCheck = _MQ(" select op_uid from smart_order_product where op_uid = '{$excel_values[0]['고유번호']}' ");
if(!$OPCheck['op_uid']) error_msg('엑셀 파일이 잘못되었습니다.\\n\\n배송주문상품관리에서 받은 엑셀 파일이 맞는지 확인바랍니다.');
?>
<form action="_order_product.pro.php" method="post" onsubmit="return wFun();">
	<input type="hidden" name="_mode" value="ins_excel">
	<div class="group_title">
		<strong>배송주문상품관리 일괄업로드</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box js_submit_box" style="display: none;">
			<span class="c_btn h46 red"><input type="submit" value="등록처리 (<?php echo number_format($ExcelCnt); ?>)" /></span>
			<a href="<?php echo $app_current_link; ?>" class="c_btn h46 black line">돌아가기</a>
		</div>
	</div>

	<div class="data_form">
		<table class="table_form">
			<tbody>
				<tr>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('처리 수에 따라 다소시간이 걸릴 수 있습니다.'); ?>
							<?php echo _DescStr('해당 페이지에서 <em>등록처리</em>버튼을 눌러 저장 하지 않으면 등록되지 않습니다.'); ?>
							<?php echo _DescStr('해당 페이지에서 <em>새로고침</em>을 할 경우 문제가 생길 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="data_list_excel js_data_table">
		<table class="table_list">
			<colgroup>
				<col width="70">
				<col width="70">
				<col width="100">
				<col width="165">
				<col width="165">
				<col width="100">
				<col width="135">
				<col width="90">
				<col width="80">
				<col width="145">
				<col width="80">
				<col width="145">
				<col width="120">
				<col width="*">
				<col width="*">
				<col width="155">
				<col width="*">
				<col width="*">
				<col width="*">
				<col width="*">
				<col width="100">
				<col width="70">
				<col width="100">
				<col width="200">
				<col width="200">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">NO</th>
					<th scope="col">처리</th>
					<th scope="col">배송상태</th>
					<th scope="col">택배사</th>
					<th scope="col">송장번호</th>
					<th scope="col">고유번호</th>
					<th scope="col">주문번호</th>
					<th scope="col">주문일시</th>
					<th scope="col">주문자 이름</th>
					<th scope="col">주문자 휴대폰</th>
					<th scope="col">받는분 이름</th>
					<th scope="col">받는분 휴대폰</th>
					<th scope="col">받는분 우편번호</th>
					<th scope="col">받는분 주소</th>
					<th scope="col">받는분 지번주소</th>
					<th scope="col">상품코드</th>
					<th scope="col">대표상품명</th>
					<th scope="col">옵션1</th>
					<th scope="col">옵션2</th>
					<th scope="col">옵션3</th>
					<th scope="col">판매단가</th>
					<th scope="col">수량</th>
					<th scope="col">금액</th>
					<th scope="col">배송시 유의사항</th>
					<th scope="col">관리자메모</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$num = 0;
				foreach($excel_values as $k=>$v) {
					$num++;

					$_opuid = $v['고유번호']; // 주문번호
				?>
					<tr>
						<td><?php echo number_format($num); ?></td>
						<td>
							<?php echo _InputSelect('w_check['.$_opuid.']', array('Y', 'N'), 'Y', '', array('등록', '제외'), ''); ?>
						</td>
						<td>
							<?php
							$DClass = ''; // 배송상태 셀렉트박스 클래스
							if($v['배송상태'] == '배송완료') $DClass = 'diliver_ok';
							else if($v['배송상태'] == '배송중') $DClass = 'diliver_ing';
							else if($v['배송상태'] == '배송준비') $DClass = 'diliver_ready';
							else if($v['배송상태'] == '배송대기') $DClass = 'pay_ready';
							else $DClass = 'pay_ready'; // LCY : 2023-06-13 : 송장예외처리 추가 :: 상태없을 경우 기본 배송대기
							echo _InputSelect('_sendstatus['.$_opuid.']', $arr_order_product_sendstatus, ($v['배송상태']?$v['배송상태']:'배송대기'), ' class="js_sendstatus '.($DClass?$DClass:null).'"', '', '');
							?>
						</td>
						<td>
							<?php echo _InputSelect('_sendcompany['.$_opuid.']', array_keys($arr_delivery_company), $v['택배사'], '', '', ''); ?>
						</td>
						<td>
							<input type="text" name="_sendnum[<?php echo $_opuid; ?>]" class="design" placeholder="송장번호" value="<?php echo $v['송장번호']; ?>">
						</td>
						<td><?php echo $_opuid; ?></td>
						<td><?php echo $v['주문번호']; ?></td>
						<td><?php echo $v['주문일시']; ?></td>
						<td><?php echo $v['주문자 이름']; ?></td>
						<td><?php echo $v['주문자 휴대폰']; ?></td>
						<td><?php echo $v['받는분 이름']; ?></td>
						<td><?php echo $v['받는분 휴대폰']; ?></td>
						<td><?php echo $v['받는분 우편번호']; ?></td>
						<td class="t_left"><?php echo $v['받는분 주소']; ?></td>
						<td class="t_left"><?php echo $v['받는분 지번주소']; ?></td>
						<td><?php echo $v['상품코드']; ?></td>
						<td class="t_left"><?php echo $v['대표상품명']; ?></td>
						<td class="t_left"><?php echo $v['옵션1']; ?></td>
						<td class="t_left"><?php echo $v['옵션2']; ?></td>
						<td class="t_left"><?php echo $v['옵션3']; ?></td>
						<td><?php echo number_format((int)$v['판매단가']); ?></td>
						<td><?php echo number_format((int)$v['수량']); ?></td>
						<td><?php echo number_format((int)$v['금액']); ?></td>
						<td><?php echo htmlspecialchars($v['배송시 유의사항']); ?></td>
						<td><?php echo htmlspecialchars($v['관리자메모']); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</form>


<script type="text/javascript">
	//페이지가 준비 되면 노출 :: 페이지 준비전 submit가 발생시 데이터 잘림 현상 발생
	$(document).ready(function() {
		$('.js_submit_box').show();
	});

	function wFun() {
		if(!confirm("입력하시겠습니까?")) return false;
		return true;
	}


	$('.js_sendstatus').on('change', function(e) {
		var _status = $(this).val();
		var _class = new Array();
		_class['배송완료'] = 'diliver_ok';
		_class['배송중'] = 'diliver_ing';
		_class['배송준비'] = 'diliver_ready';
		_class['배송대기'] = 'pay_ready';

		$(this).removeClass('diliver_ok');
		$(this).removeClass('diliver_ing');
		$(this).removeClass('diliver_ready');
		$(this).removeClass('pay_ready');
		if(_class[_status]) $(this).addClass(_class[_status]);
		else $(this).addClass(_class['배송대기']);
	});

	// 휠 스크롤을 가로에서 세로로 변경
	$('.js_data_table').bind('mousewheel', function(e) {
		e.preventDefault();
		var wheelDelta = e.originalEvent.wheelDelta;
		if(wheelDelta > 0) $(this).scrollLeft(-wheelDelta + $(this).scrollLeft());
		else $(this).scrollLeft(-wheelDelta + $(this).scrollLeft());
	});
</script>
<?php include_once('wrap.footer.php'); ?>