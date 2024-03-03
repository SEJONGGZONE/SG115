<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>주문내역 인쇄하기</title>
	<!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
	<link rel="apple-touch-icon-precomposed" href="images/homeicon.png" />
	<meta name="format-detection" content="telephone=no" />
	<SCRIPT src="/include/js/jquery-1.11.2.min.js"></SCRIPT>
	<style type="text/css">
		html {width:100%; height:100%;}
		body {margin:0; background:#fff; height:100%; box-sizing:border-box;}
		body,p,pre,form,span,div,table,td,ul,ol,li,dl,dt,dd,input,textarea,label,button {color:#000; word-wrap:break-word; word-break:keep-all; font-family:"맑은고딕", sans-serif ; font-size:12px; font-weight:400;}
		b,strong {word-wrap:break-word; word-break:break-all; font-family:inherit; font-size:inherit; font-weight:600; letter-spacing:0px;}

		p,form,span,h1,h2,h3,h4,h5,h6 {margin:0; padding:0; font-weight:normal}
		div,table {margin:0; padding:0; border-spacing:0; border-collapse:collapse; border:0px none; }
		ul,ol,li,td,dl,dt,dd {margin:0; padding:0; list-style:none; font-size:inherit}
		em,i {font-style:normal}
		a,span {display:inline-block;}
		img {border:0;}
		span,div,a,b,strong,label {color:inherit; font-size:inherit; font-weight:inherit}
		table caption {width:0px; height:0px; font-size:0; visibility:hidden; }
		table {width:100%;}

		.wrap {max-width:1000px; margin:0 auto; height:100%; box-sizing:border-box; padding:10px;}

		.print_title {text-align:center; font-size:21px; font-weight: 600; letter-spacing:-1px; border:2px solid #000; padding:20px 0;}

		.info_box {overflow:hidden; margin-top:20px}
		.info_title {font-weight:600; font-size:14px; padding-bottom:5px;}
		.info_table {display:table; width:100%; box-sizing:border-box; table-layout:fixed; border-spacing:0px;}
		.info_table th {background:#f0f0f0; border:1px solid #ccc; padding:10px}
		.info_table td {background:#fff; border:1px solid #ccc; padding:10px;}

		.item_list {margin-top:30px;}
		.item_title {font-weight:600; font-size:14px; overflow:hidden; padding-bottom: 5px; box-sizing: border-box;}
		.item_title strong {float:left;}
		.item_title .time_box {float:right; overflow:hidden; margin-top:3px; font-size:11px;}
		.item_title .time_box li {float:left; margin-left:10px; position:relative; padding-left:10px}

		.item_table {display:table; width:100%; box-sizing:border-box; table-layout:fixed;}
		.item_table th {border:1px solid #ccc; background:#f0f0f0; padding:10px 5px; border-bottom:0;}
		.item_table th+ th {border-left:0;}
		.item_table td {border:1px solid #ccc; padding:10px 5px; text-align:center;}
		.item_table td+ td {border-left:0;}
		.item_table .number {text-align:center;}
		.item_table .name {font-weight:300;}
		.item_table .name strong {font-weight:600;}
		.item_table .price {text-align:right; font-size:12px;}
		.item_table .count {text-align:center;}
		.item_table .count .box {background:#fff;}
		.item_table .code {text-align:center;}
		.item_list .add_txt {padding:20px 20px 25px 20px; font-weight:600; font-size:18px; background:#fff; text-align:center;}
		.item_list .left {text-align:left}
		.item_list .right {text-align:right}

		.total_sum {margin-top:30px; box-sizing: border-box; overflow: hidden; font-size:13px; display:flex; justify-content:center; align-items:flex-end; flex-direction:column; border:2px solid #ccc; padding:20px;}
		.total_sum .sum_table {display:table; box-sizing:border-box; border-collapse:separate; border-spacing:0px; float:right; text-align:right;}
		.total_sum dl {display:table-row;}
		.total_sum dt {display:table-cell; padding-right:20px;}
		.total_sum dd {display:table-cell; font-weight:600; padding:2px 0;}

		.total_sum dl.this_total {font-size:18px;}
		.total_sum dl.this_total * {font-weight:600; border-top:1px dashed #ddd;}

		tr.cancel td {position:relative; color:#999;}
		tr.cancel td:before {content: "";position: absolute;left: 0;top: 48%;width: 100%;height: 1px;border-top: 1px solid #000;}
	</style>
</head>
<body>
<div class="wrap">



<?php
	foreach($row_array as $k=>$v) {

		$ordernum = $v['o_ordernum'];
		$row = $v;

		// 페이지 인쇄 구분
		if($k <> 0 ) {
			echo "<div style='page-break-before: always;'/></div>";
		}

		$printMember = $row['o_memtype']=='N'?$row['o_oname']:$row['o_oname'].' ('.$row['o_mid'].') ';

?>

	<div class="print_title"><strong><?php echo $siteInfo['s_adshop']; ?></strong> 주문 내역서</div>


	<div class="info_box">
		<div class="info_title">주문자(수령자) 정보</div>

		<table class="info_table">
			<colgroup>
				<col width="100"/><col width="*"/><col width="100"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>성명</th>
					<td><?php echo $printMember;?></td>
					<th>주문번호</th>
					<td><?php echo $ordernum; ?></td>
				</tr>
				<tr>
					<th>연락처</th>
					<td <?php echo in_array($row['o_order_type'], array('delivery','both')) > 0 ? '': ' colspan="3"' ?>>
						<?php
							echo tel_format($row['o_ohp']);
						?>
					</td>

					<?php if( in_array($row['o_order_type'], array('delivery','both')) > 0) { ?>
					<th>주소</th>
					<td>
						<?php

							echo $row['o_raddr_doro'] .' '. $row['o_raddr2'];
							if($row['o_raddr1']){
								echo '<br>(지번주소: '.$row['o_raddr1'] .' '. $row['o_raddr2'].')';
							}
						?>
					</td>
					<?php } ?>
				</tr>
				<?php if(trim($row['o_content'])) { ?>
				<tr>
					<th>배송 메모</th>
					<td colspan="3"><?php echo nl2br(stripslashes($row['o_content'])); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<div class="item_list">

		<div class="item_title">
			<strong>주문상품 리스트</strong>
			<div class="time_box">
				<ul>
					<li>주문시간 : <?php echo printDateInfo($row['o_rdate'],1); ?></li>
					<li>출력시간 : <?php echo printDateInfo(date('Y-m-d H:i:s'),1); ?></li>
				</ul>
			</div>
		</div>

		<table class="item_table">
			<colgroup>
				<col width="60"/><col width="100"/><col width="130"/><col width="*"/><col width="60"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<th scope="col">분류</th>
					<th scope="col">상품코드</th>
					<th scope="col">상품명</th>
					<th scope="col">수량</th>
					<th scope="col">가격</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$NpayDcPrice = 0; // LDD: 2018-07-21 네이버 페이 할인 (N포인트+N적립금)
				$arr_pcode = array();
				$arr_pcode_idx = array();
				$arr_op = array();
				$sres = _MQ_assoc("
					select
						op.* , p.*
					from smart_order_product as op
					left join smart_product as p on ( p.p_code=op.op_pcode )
					where op_oordernum='" . $ordernum . "'
				");
				foreach( $sres as $k=>$v ){

					/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */
					$NpayDcPrice += ($v['npay_point']+$v['npay_point2']);
					/* LDD: 2018-07-21 네이버페이 할인 포함 (N포인트+N적립금) */

					// 옵션 정보
					$op_option_name = sizeof(array_filter(array($v['op_option1'],$v['op_option2'],$v['op_option3']))) > 0 ? implode('/', array_filter(array($v['op_option1'],$v['op_option2'],$v['op_option3']))) : NULL;

					// 분류정보
					$pct_r = _MQ(" select c.c_name from smart_product_category as pct inner join smart_category as c on (c.c_uid = pct.pct_cuid) where pct.pct_pcode = '". $v['op_pcode'] ."' limit 1 ");

					// 티켓상품 예약일
					$app_dateoption_date = '';
					if($v['op_ptype'] =='ticket' && $v['op_dateoption_use']=='Y'){
						$dateoption_date = date('Y-m-d',strtotime($v['op_dateoption_date']));
						$dateoption_day = date('w',strtotime($v['op_dateoption_date']));
						$app_dateoption_date = '['.$dateoption_date.'('.$arr_day_week_short[$dateoption_day].')]';

						// 상품명
						$app_pname = stripslashes($v['op_pname']).'<br/>'.$app_dateoption_date.' '.$op_option_name;
					}else{

						// 상품명
						$app_pname = stripslashes($v['op_pname']).'<br/>'.$op_option_name;
					}


					echo "
						<tr class='". ($v['op_cancel'] == 'Y' ? 'cancel' : null) /* SSJ : 부분취소 표시 : 2020-09-04 */ ."'>
							<td class=''>". ($k+1)  ."</td>
							<td class=''>". $pct_r['c_name'] ."</td>
							<td class=''>". $v['op_pcode'] ."</td>
							<td class='left'>". $app_pname ."</td>
							<td class=''>".$v['op_cnt']."</td>
							<td class='right'>". number_format($v['op_price'] * $v['op_cnt']) ."원</td>
						</tr>
					";

				}
			?>
			</tbody>
		</table>

		<?php echo (trim($row['o_printcontent']) ? '<div class="add_txt">'.stripslashes($row['o_printcontent']).'</div>' : ''); ?>

	</div>


	<div class="total_sum">
		<div class="sum_table">
			<dl>
				<dt>상품 </dt>
				<dd><?php echo number_format($row['o_price_total']); ?>원</dd>
			</dl>
			<dl>
				<dt>배송료 </dt>
				<dd>+ <?php echo number_format($row['o_price_delivery']); ?>원</dd>
			</dl>
			<dl>
				<dt>할인 </dt>
				<dd>- <?php echo number_format( 1 * $row['o_price_total'] + $row['o_price_delivery'] - $row['o_price_real'] + $NpayDcPrice); ?>원</dd>
			</dl>
			<?php if(($row['o_price_refund'] + $row['o_price_usepoint_refund']) > 0){ // 부분취소 표시 ?>
				<dl>
					<dt>- 취소 </dt>
					<dd><?php echo number_format( 1 * ($row['o_price_refund'] + $row['o_price_usepoint_refund'])); ?>원</dd>
				</dl>
			<?php } ?>
			<dl class="this_total">
				<dt>총 결제금액</dt>
				<dd><?php echo number_format($row['o_price_real']-($row['o_price_refund'] + $row['o_price_usepoint_refund'])); // 부분취소 표시 ?>원</dd>
			</dl>
		</div>
	</div>
	<!-- / 최종 주문가격 -->

<? } ?>


</div>

<script language="javascript">
	$(document).ready(function() {
		print();
	});
</script>

</body>
</html>