<?php // 상품박스(공통) ?>
<div class="item_box<?php echo ($v['p_stock'] <= 0 || $p_sale_type_chk=='Y' || $p_sale_before_chk=='Y' || $p_timesale_type_chk=='N' || $p_timesale_before_chk=='Y'? ' if_soldout ' : ''); ?> js_item_box" ID="js_item_box<?php echo $v['p_code'];?>" data-pcode="<?php echo $v['p_code'];?>" data-timesale="<?php echo $v['p_time_sale'];?>" data-timesaletype="<?php echo $p_timesale_type_chk;?>" data-beforetimesale="<?php echo $p_timesale_before_chk;?>" data-timeday="<?php echo $arr_current_time['day'];?> ">
    <?php // 썸네일 ?>
	<div class="thumb">
        <a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title=""></a>

        <?php // 상품 아이콘 :: 수동아이콘 ?>
        <?php echo $manual_pro_icon; ?>

		<?php if($v['p_img_list_over']!=''){ ?>
			<div class="ov_img"><img src="<?php echo $_ov_img;?>" alt="<?php echo addslashes(($v['p_name'])); ?>" /></div>
		<?php }?>
        <?php if($_img) { ?>
            <div class="img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes(($v['p_name'])); ?>"  /></div>
        <?php }?>

		<?php if($v['p_stock'] <= 0) { ?>
            <div class="soldout">SOLD OUT</div>
        <?php }else if($p_sale_type_chk=='Y'){?>
            <div class="soldout">판매종료</div>
        <?php }else if($p_sale_before_chk=='Y'){ ?>
			<div class="soldout">
				<strong><?php echo date('Y년 n월 j일',strtotime($v['p_sale_sdate']));?></strong>
				오픈예정
			</div>
		<?php }else if($p_timesale_type_chk=='N'){?>
			<div class="soldout">타임세일 종료</div>
        <?php }else if($p_timesale_before_chk=='Y'){ ?>
			<div class="soldout">
				<strong><?php echo date('n월 j일',strtotime($v['p_time_sale_sdate'])).' '.date('H:i',strtotime($v['p_time_sale_sclock']));?></strong>
				OPEN
			</div>
		<?php }?>

		<?php if($best_use=='Y'){ ?>
			<?php $rank = $k+1;?>
			<div class="ranking"><?php echo $rank;?></div>
		<?php }?>

        <?php // 리뷰, 장바구니, 찜하기 버튼 ?>
        <div class="btn_multi">

            <?php // 리뷰(상세 리뷰탭으로 이동 링크) ?>
            <?php if($eval_cnt>0){?><?php }?>
			<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>&review_mv=Y" class="review">
				<span class="mark">
					<span class="star this_value" style="width: <?php echo $star_persent;?>%;"></span><?php // 별점 width값 조절 ?>
					<span class="star this_base"></span>
				</span>
				<?php if($eval_cnt>0){?>
					<span class="num">(<?php echo $eval_cnt;?>)</span>
				<?php }?>
			</a>

            <div class="icbtn">
                <?php
                /*
                    장바구니 담기면 .hit 클래스 추가
                    옵션이 없는 상품은 장바구니페이지로 이동안하고 상세에서 뜨는 창 노출
                */
                ?>
                <a href="<?php echo $cart_link; ?>" class="btn btn_cart js_cart<?php echo (is_cart($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="장바구니 담기"></a>
                <?php // 찜하기 버튼 ?>
                <a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="<?php echo (is_wish($v['p_code'])?'찜삭제':'찜하기'); ?>"></a>
            </div>
        </div><!-- end btn_multi -->
	</div><!-- end thumb -->

    <?php // 정보 ?>
	<div class="info">
		<?php if($v['p_time_sale']=='Y'){?>
			<?php // 타이머 ?>
			<div class="item_timer js_time_sale_timmer <?php echo $p_timesale_type_chk=='N'?'if_timeout':''?>" data-pcode="<?php echo $v['p_code'];?>" data-chk="<?php echo $chkSecond; ?>" data-d="<?php echo $arr_current_time['day'] ?>" data-h="<?php echo $arr_current_time['hour'] ?>" data-i="<?php echo $arr_current_time['minut'] ?>" data-s="<?php echo $arr_current_time['second'] ?>">
				<span class="clock js_time_sale_icon"><span class="bar hour"></span><span class="bar minute"></span></span>
				
				<?php if($p_timesale_before_chk=='N'){?>
					<?php if($p_timesale_type_chk=='Y'){?>
						<?php if($arr_current_time['day']<=1){?>
							<strong class="js_timer js_time_sale_timer_h"><?php echo $arr_current_time['hour'];?></strong>
							<em class="js_timer">:</em>
							<strong class="js_timer js_time_sale_timer_m"><?php echo $arr_current_time['minut'];?></strong>
							<em class="js_timer">:</em>
							<strong class="js_timer js_time_sale_timer_s"><?php echo $arr_current_time['second'];?></strong>
						<?php }else{ ?>
							<em class="last"><?php echo $arr_current_time['day'].'일';?> 남음</em>
						<?php }?>
					<?php }else{?>
						<em class="last">Timeout</em>
					<?php }?>
				<?php }else{?>
					<em class="last">Coming</em>
				<?php }?>
			</div>
		<?php }?>

		<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>" class="item_name"><?php echo stripslashes($v['p_name']); ?></a>
		<?php if($v['p_subname']!=''){ ?>
			<div class="sub_name"><?php echo stripslashes($v['p_subname']); ?></div>
		<?php }?>

		<div class="price">
			<?php if($v['p_screenPrice'] > 0) { ?>
                <div class="before"><span class="won"><?php echo number_format( 1 * $v['p_screenPrice']); ?></span></div>
            <?php } ?>
            <div class="after"><span class="won"><?php echo number_format( 1 * $v['p_price']); ?></span><span class="won_t">원</span></div>
			<?php if($v['p_screenPrice'] > 0 && $v['p_screenPrice']>$v['p_price']  && DCPer($v['p_screenPrice'], $v['p_price'])>0 ) { ?>
				<div class="percent"><?php echo DCPer($v['p_screenPrice'], $v['p_price']); ?>%</div>
			<?php }?>
		</div>

        <?php // 리뷰, 장바구니, 찜하기 버튼 ?>
        <div class="btn_multi">

			<?php // 리뷰(상세 리뷰탭으로 이동 링크) ?>
            <?php if(rm_str($eval_cnt)>0){?><?php }?>
			<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>&review_mv=Y" class="review">
				<span class="mark">
					<?php // 별점 width값 조절 ?>
					<span class="star this_value" style="width: <?php echo $star_persent;?>%;"></span>
					<span class="star this_base"></span>
				</span>
				<?php if(rm_str($eval_cnt)>0){?>
					<span class="num">(<?php echo $eval_cnt;?>)</span>
				<?php }?>
			</a>

            <div class="icbtn">
                <?php
                /*
                    장바구니 담기면 .hit 클래스 추가
                    옵션이 없는 상품은 장바구니페이지로 이동안하고 상세에서 뜨는 창 노출
                */
                ?>
                <a href="<?php echo $cart_link; ?>" class="btn btn_cart js_cart<?php echo (is_cart($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="장바구니 담기"></a>
                <?php // 찜하기 버튼 ?>
                <a href="#none" onclick="return false;" class="btn btn_wish js_wish<?php echo (is_wish($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="<?php echo (is_wish($v['p_code'])?'찜삭제':'찜하기'); ?>"></a>
            </div>
        </div><!-- end btn_multi -->

        <?php // 상품 아이콘 :: 자동아이콘 ?>
        <?php echo $auto_pro_icon; ?>
	</div><!-- end info -->
</div><!-- end item_box -->
