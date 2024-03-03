
<?php // 타이틀 ?>
<div class="layer_tit js_latest_cnt" data-cnt=<?php echo count($LatestList);?>>
	<span class="tit">최근 본 상품(<em><?php echo count($LatestList);?></em>)</span>
	<a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".p_Recent" data-add="if_open_recent" title="닫기"></a>
</div><!-- end layer_tit -->

<?php // 상품리스트 ?>
<div class="layer_cont c_scroll_v">
	<div class="recent_li">
		<ul>
			<?php
			foreach($LatestList as $k =>$v){
				$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);
				?>
				<li>
					<?php // 상품 박스 ?>
					<div class="recent_item<?php echo ($v['p_stock'] < 1 ? ' item_soldout ' : ''); ?>">
						<div class="thumb">
							<a href="/?pn=product.view&cuid=<?php echo $v['p_cuid'];?>&pcode=<?php echo $v['p_code'];?>" class="upper_link" title=""></a>
							<?php
							// 솔드아웃일 때 노출(일단 안보이게 함)
							// 솔드아웃일 때 item_box옆에 item_soldout 클래스 추가
							?>
							<?php if($v['p_stock'] <= 0){ ?>
								<div class="soldout">SOLD OUT</div>
							<?php }?>
							<div class="img">
								<img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" style="background-image: url('<?php echo $_img;?>')" alt="<?php echo addslashes($v['p_name'])  ?>" />
								<input type="hidden" name="item_pro_img" data-index="<?php echo $k;?>" value="<?php echo $_img;?>">
							</div>
						</div>
						<div class="info">
							<a href="/?pn=product.view&cuid=<?php echo $v['p_cuid'];?>&pcode=<?php echo $v['p_code'];?>" class="item_name" title=""><?php echo addslashes($v['p_name'])  ?></a>
							<span class="price"><span class="won"><?php echo number_format($v['p_price']); ?></span><span class="won_t">원</span></span>
						</div>
						<div class="quick_btn">
							<?php
								// 장바구니 담기 기능
								$is_option = is_option($v['p_code']); // 옵션상품인지 체크
								$cart_link = "javascript:app_submit_from_list('{$v['p_code']}', 'cart', {$is_option},'latest');";
								$cart_chk = is_cart($v['p_code'])?'hit':null;
							?>
							<a href="<?php echo $cart_link; ?>" class="btn btn_cart js_cart <?php echo $cart_chk;?>"data-pcode="<?php echo $v['p_code']; ?>" title="장바구니 담기"></a>
							<a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="<?php echo (is_wish($v['p_code'])?'찜삭제':'찜하기'); ?>"></a>
							<a href="#none" onclick="late_delete('<?php echo $v['pl_uid']; ?>'); return false;" class="btn btn_del" title="삭제"></a>
						</div>
					</div><!-- end recent_item -->
				</li>
			<?php }?>
		</ul>
	</div>
</div><!-- end layer_cont -->
