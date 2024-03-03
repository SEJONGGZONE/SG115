<?php

	// 이미지 체크
	$_p_img = get_img_src('thumbs_s_'.$row['p_img_list_square']);
	if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

	// 상품명
	$_p_name = stripslashes($row['op_pname']);

	// 옵션정보
	$_option = "";

	// KAY :: 2022-12-07 :: 옵션 구분 수정
	$arr_option_name = array($row['op_option1'] ,$row['op_option2'],$row['op_option3'] );
	$arr_option_name = array_filter($arr_option_name);

	$print_dateoption_date ='';
	if($row['op_dateoption_use'] == 'Y' && rm_str($row['op_dateoption_date']) > 0  ){
		$print_dateoption_day = date('w',strtotime($row['op_dateoption_date']));
		$print_dateoption_date = "<span style='color:#369' class='if_cal_date'>[".$row['op_dateoption_date']." (".$arr_day_week_short[$print_dateoption_day].")]</span>";
	}

	if( count($arr_option_name) > 0){
		$_option = implode(" / ",$arr_option_name);
	}


	$ticketStatusCntInfo = array(); // 티켓의 상태값에 대한 개수를 저장
	ob_start();

	foreach($res as $tk=>$tv){


		$search_btn = ''; // 발급조회 버튼
		$ticketStatusCntInfo[$tv['opt_status']]++;
		$ticketStatusDateInfo = '';
		if( $tv['opt_status'] == '취소'){
			$ticketStatusDateInfo = '취소일:'. date('Y-m-d',strtotime($tv['opt_cdatetime']));
			$search_btn = '<a href="#none" onclick="alert(\'취소된 티켓은 발급조회가 불가능합니다.\'); return false;" class="c_btn sky line ">발급조회</a>';
		}
		else if( $tv['opt_status'] == '사용'){
			$ticketStatusDateInfo = '사용일:'. date('Y-m-d',strtotime($tv['opt_udatetime']));
			$search_btn = '<a href="_order_ticket.list.php?pass_ticketnum='.$tv['opt_ticketnum'].'&mode=search" class="c_btn sky line " target="_blank">발급조회</a>';
		}
		else if($tv['opt_status'] == '만료'){
			$search_btn = '<a href="_order_ticket.list.php?pass_ticketnum='.$tv['opt_ticketnum'].'&mode=search" class="c_btn sky line " target="_blank">발급조회</a>';
		}
		// 미사용
		else{
			$search_btn = '<a href="_order_ticket.list.php?pass_ticketnum='.$tv['opt_ticketnum'].'&mode=search" class="c_btn sky line " target="_blank">발급조회</a>';
		}

		// 티켓의 유효기간 표기
		if( $tv['opt_expire_type'] != ''){
			$_expire_date = $tv['opt_expire_date'];
		}
		else{
			$_expire_date = '없음';
		}
?>
<tr>
	<td>
		<?php echo $arr_ticket_status_icon[$tv['opt_status']] ?>
		<?php if( $ticketStatusDateInfo != ''){?>
		<span style="margin-top:2px;">(<?php echo $ticketStatusDateInfo ?>)</span>
		<?php } ?>
	</td>
	<td class="t_blue">
		<div class="product_name"><?php echo $tv['opt_ticketnum'] ?></div>
	</td>
	<td>
		<?php echo date('Y-m-d',strtotime($tv['opt_rdatetime'])) ?>
	</td>
	<td><?php echo $_expire_date ?></td>
	<td>
		<?php echo $search_btn; ?>
	</td>
</tr>

<?php
	}
	$ticket_view_list = ob_get_clean();
?>


<div class="pop_title">
	티켓정보
	<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a>
</div>

<div class="pop_conts">

	<?php
	/* 노출할 필요없음
	<div class="tip_box page_tip">
		<?php
			echo _DescStr('상품명: <strong>'.$_p_name.'</strong>','black');
			if( !empty($_option)){ echo _DescStr('옵션정보: <strong>'.$print_dateoption_date.' '.$_option.'</strong>','black');}
			echo _DescStr('티켓 사용처리는 <u>주문/배송 > 배송/티켓/정산관리 > 티켓발급관리</u> 에서 가능합니다.(또는 티켓검색 클릭)','black');
			if( $ticketStatusCntInfo['사용'] > 0){
				echo _DescStr('<font color="red">발급된 티켓중 사용완료된 티켓이 있습니다.</font>');
			}
			$_p_name = $_option = '';

		?>
	</div>
	*/
	?>

	<div class="data_list">
		<?php if( count($res) <1 ){ ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">입금확인  후 티켓이 발급됩니다.</div></div>
		<?php }else{  ?>
		<table class="table_list">
			<colgroup>
				<col width="90"><col width="*"><col width="100"><col width="100"><col width="100">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">티켓상태</th>
					<th scope="col">티켓번호</th>
					<th scope="col">발급일</th>
					<th scope="col">유효기간</th>
					<th scope="col">발급조회</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $ticket_view_list;
				?>
			</tbody>
		</table>
		<?php } ?>

	</div>
</div>


<div class="c_btnbox type_full">
	<ul>
		<li><a href="#none" onclick="return false;" class="c_btn h34 black line close" >닫기</a></li>
	</ul>
</div>