<?php 
	include_once('inc.header.php'); 
	$ticketCode = empty($ticketCode) ? '' : $ticketCode;
	$token_query = http_build_query(array('ticketCode'=>$ticketCode,'tokenType'=>'qrcheck'));
	$backUrl = "_order_ticket.list.php?".$token_query;

	// 티켓상품 정보를 가져온다.
	$pass_uid = enc('d',$ticketCode);

	$row = _MQ("select op.*, opt.opt_uid, opt.opt_status, opt.opt_ticketnum,  o.o_oname,o.o_uname, o.o_mid, o.o_memtype, o.o_ohp, o.o_uhp, p.p_img_list_square
		from smart_order_product_ticket as opt left join smart_order_product as op on(op.op_uid = opt.opt_opuid) left join smart_product as p on(p.p_code = op.op_pcode) left join smart_order as o on(o.o_ordernum = opt.opt_oordernum) where opt.opt_uid = '".$pass_uid."' ");
	if( count($row) < 1 ){ error_loc_msg($backUrl,"티켓정보를 확인할 수 없습니다."); }

	
	// 입점업체를 사용할 경우 티켓을 체크한다.
	if($SubAdminMode === true){
		if( $row['op_partnerCode'] != $_COOKIE['AuthCompany']){  error_loc_msg($backUrl,"티켓정보를 확인할 수 없습니다."); }
	}

    $p_thumb    = get_img_src('thumbs_s_'.$row['p_img_list_square']); // 상품 이미지
    if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/c_img/thumb.gif';

	// 상품명 
	$app_product_name_tmp = $row['op_pname'];
	
	// KAY :: 2022-12-07 :: 옵션 구분 수정
	$arr_option_name = array();
	if( $row['op_option1'] != ''){
    	$arr_option_name = array($row['op_option1'] ,$row['op_option2'],$row['op_option3'] );
    	$arr_option_name = array_filter($arr_option_name);	 
	}
    
    // 옵션명까지 합쳐진 이름
    $item_name = $app_product_name_tmp;
    if( count($arr_option_name) >0){
    	$item_name = $item_name."(".implode(" / ",$arr_option_name).")";
    }

    // 사용자 이름
    $item_user_name = $row['o_uname'] ? $row['o_uname'] : $row['o_oname'];
    $item_user_hp = $row['o_uhp'] ? $row['o_uhp'] : $row['o_ohp'];

	$userLink = '/?pn=product.view&code='.$row['op_pcode']; // 사용자 링크 사용시 
	$adminLink = '_product.form.php?_mode=modify&_code='.$row['op_pcode'];
	if( $row['op_ptype'] == 'ticket'){
		$adminLink = '_product_ticket.form.php?_mode=modify&_code='.$row['op_pcode'];
	}	

?>


<div class="ticket_confirm">
    <div class="white_box">
		 <div class="top_box">
			<span class="title">티켓을 확인해주세요</span>
			<a href="<?php echo $backUrl; ?>" class="btn_close" title="뒤로가기"></a>
		</div><!-- end top_box -->

        <div class="wrapping">
            <div class="ticket_num"><em>TICEKT NO</em><strong><?php echo $row['opt_ticketnum']; ?></strong></div><?php // 티켓번호 ?>
			<div class="coupon_item">
				<div class="thumb"><a href="<?php echo $adminLink; ?>" target="_blank"><img src="<?php echo $p_thumb; ?>" alt="<?php echo $app_product_name_tmp; ?>" /></a></div> <?php // 상품 썸네일 ?>
				<div class="item_name">
					<a href="<?php echo $adminLink; ?>" target="_blank"><?php echo $item_name; ?></a>
				</div> <?php // 상품명 ?>
				<div class="price"><strong><?php echo number_format($row['op_price']) ?></strong><em>원</em></div> <?php // 상품 가격 ?>
			</div>
        </div><!-- end info -->

		<div class="user">
			<div class="picto"></div>
			<dl>
				<dt><?php echo $item_user_name ?>님 (<?php echo $row['o_memtype'] == 'Y' ? $row['o_mid']:'비회원' ?>) <?php // 아이디없으면 "비회원" ?></dt>
				<dd><?php echo $item_user_hp ?></dd>
			</dl>
		</div>

        <div class="btn_box"> 
        	<?php 
        		// 티켓상태 여부에 따른 처리 
        		if( $row['opt_status'] == '대기'){
        			echo '<a href="#none" class="btn ok js_apply_ticket_status" data-type="use">티켓을 사용합니다</a>';
        		}
        		else if($row['opt_status'] == '사용'){
        			echo '<a href="#none" onclick="return false
        			;" class="btn cancel js_apply_ticket_status" data-type="unuse">티켓사용을 취소합니다</a>';
        		}
        		else if($row['opt_status'] == '취소'){
        			echo '<a href="#none" onclick="return false;" class="btn none">주문취소된 티켓입니다</a>';
        		}
        		else if($row['opt_status'] == '만료'){
        			echo '<a href="#none" onclick="return false;" class="btn none">사용기간이 만료된 티켓입니다</a>';
        		}        		
        	?>
        </div><!-- end btn_box -->
    </div><!-- end inner -->
</div><!-- end ticket_confirm -->

<script>
var auth_apply_ticket_status = true;
$(document).on('click','.js_apply_ticket_status',function(){
	if( auth_apply_ticket_status !== true){ alert("잠시만 기다려주세요."); return false; }
	var data = $(this).data();
	if( typeof data.type == 'undefined' || !data.type){ alert("잘못된 접근입니다."); return false; } 
	var msg = '티켓을 '+(data.type == 'use' ? '사용처리 하시겠습니까?':'사용취소처리 하시겠습니까?');
	if( confirm(msg) == false){ return false; } 

	data._mode = 'ticket_use';
	data.uid = '<?php echo $row['opt_uid'] ?>';

	auth_apply_ticket_status = false;
	$.ajax({url:'_ticket_confirm.pro.php', dataType:'json', type:'post' , data : data })
	.done(function(e){
		if( e.rst != 'success'){  alert(e.msg); return false; }
		location.reload();
	})
	.fail(function(e){
		alert("티켓처리에 실패하였습니다.("+e.responseText+")");
	})
	.always(function(e){
		auth_apply_ticket_status = true;
	})
});
</script>

<?php include_once('inc.footer.php'); ?>