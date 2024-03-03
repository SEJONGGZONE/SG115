<?php
	include_once("inc.php");
?>

	<input type='hidden' name='smart_promotion_plan_product_setup_mode' value='' />

	<?php
		// 상품정보 추출
		$ppps_que = "
			select p.*, ppps.*
			from smart_promotion_plan_product_setup  as ppps
			inner join smart_product as p on (ppps.ppps_pcode = p.p_code)
			where
				ppps.ppps_ppuid = '". $uid ."'
			order by ppps.ppps_idx asc
		";  //=> SSJ:2017-12-13 상품이 삭제됬을때 선택삭제를 위해 p_code는 smart_promotion_plan_product_setup테이블에서 추출

		$ppps_res = _MQ_assoc($ppps_que);

		$TotalCount = count($ppps_res);
		if(sizeof($ppps_res) > 0){
	?>
	<table class="table_list">
		<colgroup>
			<col width="40"/><col width="70"/><col width="130"/><col width="90"/>
			<col width="*"/>
			<col width="120"/><col width="100"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
				<th scope="col">No</th>
				<th scope="col">순서</th>
				<th scope="col">노출</th>
				<th scope="col">상품정보</th>
				<th scope="col">상품가격</th>
				<th scope="col">재고량</th>
			</tr>
		</thead>
		<tbody>
			<?php
					foreach($ppps_res as $pppsk=>$pppsv){
						$_num = $TotalCount-$pppsk;
						$pname = htmlentities(strip_tags($pppsv['p_name']));
						$pcode = addslashes(strip_tags($pppsv['p_code']));

						// 이미지 검사
						$_p_img = get_img_src('thumbs_s_'.$pppsv['p_img_list_square']);
						if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

						if($pppsv['p_type']=='ticket'){
							$plink = '/totalAdmin/_product_ticket.form.php?_mode=modify&_code='.$pppsv['p_code'].'';
						}else{
							$plink = '/totalAdmin/_product.form.php?_mode=modify&_code='.$pppsv['p_code'].'';
						}
			?>
			<tr>
				<td class="this_check">
					<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $pppsv['p_code']; ?>]" class="js_ck class_pcode" value="Y"></label>
				</td>
				<td class="this_num"><?php echo number_format($_num); ?></td>
				<td class="this_ctrl">
					<div class="lineup-updown_ctrl">
						<div class="ctrl_form">
							<input type="text" name="sort_group[<?php echo $pppsv['p_code'];?>]" value="<?php echo $pppsv['ppps_sort_group']; ?>" class="design number_style sort_group_<?php echo $pppsv['p_code']; ?>" placeholder="0">
							<a href="#none" onclick="sort_group('<?php echo $pppsv['p_code'];?>','<?php echo $uid;?>')" class="c_btn h27 light ">적용</a>
						</div>
						<div class="ctrl_btn">
							<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','up','<?php echo $uid;?>')" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','down','<?php echo $uid;?>')" class="c_btn h22 icon_down" title="아래로"></a>
							<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','top','<?php echo $uid;?>')" class="c_btn h22 icon_top" title="맨위로"></a>
							<a href="#none" onclick="sort_up('<?php echo $pppsv['p_code'];?>','bottom','<?php echo $uid;?>')" class="c_btn h22 icon_bottom" title="맨아래로"></a>
						</div>
					</div>
				</td>
				<td class="this_state"><?php echo $arr_adm_button[($pppsv['p_view'] == 'Y' ? '노출' : '숨김')]; ?></td>
				<td class="ctg_name">
					<div class="order_item_thumb type_simple">
						<div class="thumb"><a href="<?php echo $plink;?>" target="_blank" title="<?php echo $pname;?>"><img src="<?php echo $_p_img;?>" alt="<?php echo $pname;?>" /></a></div>
						<div class="order_item">
							<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $pppsv['p_cpid'] ){?>
								<div class='entershop'><?php echo showCompanyInfo($pppsv['p_cpid']); ?></div>
							<?php }?>
							<dl>
								<dt><div class="item_name"><a href="<?php echo $plink;?>" target="_blank"><?php echo $pname;?></a></div></dt>
								<dt class="t_light"><?php echo $pcode;?></dt>
							</dl>
						</div>
						<div class="other_tag">
							<?php echo $arr_adm_button[$pppsv['p_type']];?>
							<?php
								if($pppsv['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
								else{
									if( $pppsv['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
									else if( $pppsv['p_sale_sdate'] <= date('Y-m-d') && $pppsv['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
									else{  echo '<span class="c_tag light t4">판매종료</span>'; }
								}
							?>
						</div>
					</div>
				</td>
				<td class="this_price t_red t_right t_bold"><?php echo number_format($pppsv['p_price']);?>원</td>
				<td>
					<?php if($pppsv['p_stock']>0){?>
						<span class="hidden_tx">재고량</span><?php echo number_format($pppsv['p_stock']);?>개
					<?php }else{?>
						<span class="c_tag light">품절</span>
					<?php }?>
				</td>
			</tr>
		<?php
				}
		?>
		</tbody>
	</table>
	<?php
		}// 상품정보 추출
	?>



	<?php if(sizeof($ppps_res) < 1){ ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>


<script>
	// 순위조정 up-down-top-bottom
	 function sort_up(pcode,mode,uid) {
		<?php if(pcode && mode){ ?>
			$.ajax({
				url: "_promotion_plan.product_sort.php",
				cache: false,dataType : 'json', type: "POST",
				data: {_mode:mode,_uid:uid,pcode:pcode },
				success: function(data){
					if(data.rst == 'fail'){
						alert(data.msg);
						return false;
					}
					promotion_plan_product_setup_view(<?php echo $uid;?>);
				},error:function(request,status,error){ console.log(request.responseText); }
			});
		<?php }else{ ?>
			//alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
		<?php } ?>
	}
	// 순위그룹 수정
	function sort_group(pcode,_uid){
		var group = $('.sort_group_'+ pcode).val()*1;
		if(group <= 0){
			alert('상품 순위를 입력해 주시기 바랍니다.');
			$('.sort_group_'+ pcode).focus();
			return false;
		}

		$.ajax({
			url: "_promotion_plan.product_sort.php",
			cache: false,dataType : 'json', type: "POST",
			data: {_mode : 'modify_group',_group:group,_uid:_uid,pcode:pcode },
			success: function(data){
				if(data.rst == 'fail'){
					alert(data.msg);
					return false;
				}
				promotion_plan_product_setup_view(<?php echo $uid;?>);
				if(data.msg !=''){
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText); }
		});
	}
</script>