<?php
	// -- 카테고리 정보를 가져온다.
	$rowCate = _MQ("select *from smart_category where c_uid = '".$cuid."' ");

	$viewBest = $rowCate['c_best_product_view'] == 'Y' ? '노출':'숨김';
	$viewBestMobile = $rowCate['c_best_product_mobile_view'] == 'Y' ? '노출':'숨김';

	$sque = "smart_product_category_best as pctb inner join smart_product as p on(p.p_code = pctb.pctb_pcode ) where pctb_cuid = '".$cuid."'";
	$arr_customer = arr_company2(); // 공급업체정보를 가져온다.
	$listmaxcount = 100;
	$listpg = rm_str($ahref);
	if(!rm_str($listpg)) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;
	$row = _MQ("select count(*) as cnt from ".$sque);
	$TotalCount = $row['cnt'];
	$Page = ceil($TotalCount/$listmaxcount);
	$res = _MQ_assoc("select * from  ".$sque." order by pctb_idx asc limit ".$count.", ".$listmaxcount." ");
	$printList = '';
?>
<?php if( count($res) > 0) {?>

<div class="list_ctrl ">
	<div class="left_box">
		<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn gray line ">전체선택</a>
		<a href="#none" onclick="selectAll('N'); return false;"  class="c_btn gray line ">전체해제</a>
		<a href="#none" onclick="return false;" class="c_btn h27 black line select-best-product-delete">선택삭제</a>
	</div>
	<div class="right">
		<a href="#none" onclick="selectBestProductAddpop(); return false;" class="c_btn h27 blue">상품추가</a>
	</div>
</div>


<table class="table_list">
	<colgroup>
		<col width="40"/><col width="70"/><col width="130"/><col width="90"/>
		<col width="*"/>
		<col width="120"/><col width="100"/>
	</colgroup>
	<thead>
		<tr>
			<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y" ></label></th>
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
		foreach($res as $k=>$v){
			$_num = $TotalCount-$count-$k;
			$_num = number_format($_num);
			$pname = addslashes(strip_tags($v['p_name']));
			$pcode = addslashes(strip_tags($v['p_code']));

			// 이미지 체크
			$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
			if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

			if($v['p_type']=='ticket'){
				$plink = '/totalAdmin/_product_ticket.form.php?_mode=modify&_code='.$v['p_code'].'';
			}else{
				$plink = '/totalAdmin/_product.form.php?_mode=modify&_code='.$v['p_code'].'';
			}

			// -- 리스트
	?>
			<tr>
				<td class="this_check"><label class="design"><input type="checkbox" class="js_ck best-pcode" value="<?php echo $v['p_code'];?>" name="chk_pcode[]"></label></td>
				<td class="this_num"><?php echo $_num;?></td>
				<td class="this_ctrl">
					<div class="lineup-updown_ctrl">
						<div class="ctrl_form">
							<input type="text" name="sort_group[<?php echo $v['p_code'];?>]" value="<?php echo $v['pctb_sort_group'];?>" class="design number_style sort_group_<?php echo $v['p_code'];?>" placeholder="0">
							<a href="#none" onclick="sort_group('<?php echo $v['p_code'];?>','<?php echo $cuid;?>')" class="c_btn gray ">수정</a>
						</div>
						<div class="ctrl_btn">
							<a href="#none" onclick="sort_up('<?php echo $v['p_code'];?>' ,'up','<?php echo $cuid;?>')" class="c_btn h22 icon_up" title="위로"></a>
							<a href="#none" onclick="sort_up('<?php echo $v['p_code'];?>' ,'down','<?php echo $cuid;?>')" class="c_btn h22 icon_down" title="아래로"></a>
							<a href="#none" onclick="sort_up('<?php echo $v['p_code'];?>' ,'top','<?php echo $cuid;?>')" class="c_btn h22 icon_top" title="맨위로"></a>
							<a href="#none" onclick="sort_up('<?php echo $v['p_code'];?>' ,'bottom','<?php echo $cuid;?>')" class="c_btn h22 icon_bottom" title="맨아래로"></a>
						</div>
					</div>
				</td>
				<td class="this_state"><?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')];?></td>
				<td class="ctg_name">
					<div class="order_item_thumb type_simple">
						<div class="thumb"><a href="<?php echo $plink;?>" target="_blank" title="<?php echo $pname;?>"><img src="<?php echo $_p_img;?>" alt="<?php echo $pname;?>" /></a></div>
						<div class="order_item">
							<?php if($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['p_cpid'] ){?>
								<div class='entershop'><?php echo showCompanyInfo($v['p_cpid']); ?></div>
							<?php }?>
							<dl>
								<dt><div class="item_name"><a href="<?php echo $plink;?>" target="_blank"><?php echo $pname;?></a></div></dt>
								<dt class="t_light"><?php echo $pcode;?></dt>
							</dl>
						</div>
						<div class="other_tag">
							<?php echo $arr_adm_button[$v['p_type']];?>
							<?php
								if($v['p_sale_type'] == 'A'){ echo '<span class="c_tag yellow line t4">상시판매</span>';}
								else{
									if( $v['p_sale_sdate'] > date('Y-m-d')){ echo '<span class="c_tag black line t4">판매전</span>'; }
									else if( $v['p_sale_sdate'] <= date('Y-m-d') && $v['p_sale_edate'] >= date('Y-m-d')){ echo '<span class="c_tag yellow t4">판매중</span>'; }
									else{  echo '<span class="c_tag light t4">판매종료</span>'; }
								}
							?>
						</div>
					</div>
				</td>
				<td class="this_price t_red t_right t_bold"><?php echo number_format($v['p_price']);?>원</td>
				<td>
					<?php if($v['p_stock']>0){?>
						<span class="hidden_tx">재고량</span><?php echo number_format($v['p_stock']);?>개
					<?php }else{?>
						<span class="t_none">품절</span>
					<?php }?>
				</td>
			</tr>
	<?php	} ?>
	</tbody>
</table>


<div class="paginate view-paginate">
<?php echo pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y'); ?>
</div>

<?php }else{ ?>
	<div class="common_none">
		<div class="no_icon"></div><div class="gtxt">등록된 상품이 없습니다.</div>
		<a href="#none" onclick="selectBestProductAddpop(); return false;" class="c_btn h27 blue">베스트 상품 추가하기</a>
	</div>
<?php } ?>


<div class="ajax-data-box" data-ahref=""></div>