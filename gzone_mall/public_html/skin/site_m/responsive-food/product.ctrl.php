<?php 
	$_event = $_event!=''?$_event:'';
	$dmsuid = $_event == 'main_md'?$dmsuid:'';
?>
<div class="p_List_ctrl <?php echo in_array($pn, array('product.search.list')) == true ? ' if_search':'' ?> js_list_ctrl" id="total_cnt" data-event="<?php echo $_event;?>" data-dmsuid="<?php echo $dmsuid;?>">
    <div class="layout_fix">
        <div class="inner">
            <div class="left_box">
                <div class="total">전체 <em><?php echo ($category_info['c_list_product_view'] == 'N'?'0':number_format( 1 * $TotalCount)); ?></em>개</div>

                <div class="filter_benefit">
                    <label class="c_label">
                        <input type="checkbox" class="js_free_delivery_chk" <?php echo $free_delivery_chk=='Y'?'checked':'';?>>
                        <span class="tx"><span class="icon"></span>무료배송</span>
                    </label>
                    <label class="c_label">
                        <input type="checkbox" class="js_coupon_chk" <?php echo $coupon_chk=='Y'?'checked':'';?>>
                        <span class="tx"><span class="icon"></span>할인쿠폰</span>
                    </label>
                    <label class="c_label this_soldout">
                        <input type="checkbox" class="js_pstock_chk" <?php echo $pstock_chk=='Y'?'checked':'';?>>
                        <span class="tx"><span class="icon"></span>품절제외</span>
                    </label>
                </div><!-- end filter_benefit -->
            </div><!-- end left_box -->

            <div class="right_box">
                <div class="range_box">
                    <a href="#none" onclick="return false;" class="btn_ctrl js_onoff_event" data-target=".range_box" data-add="if_open_range">
                        <span class="tx"><?php echo $arrOrderFilter[$_order]; ?></span><span class="ic"></span>
                    </a>
                    <div class="range_open">
                        <ul>
							<?php foreach($arrOrderFilter as $k=>$v){?>
								<?php if($_event=='main_md'){?>
									<li class="js_order_filter <?php echo ($k == $_order ?' hit ':null); ?>" data-order="<?php echo $k;?>"><a href="#none" onclick="return false;" class="opt"><?php echo $v;?></a></li>
								<?php }else{ ?>
									<li <?php echo ($_order == $k ?' class="hit"':null); ?>><a href="<?php echo ProductOrderLinkBuild(array('search_mode'=>'Y','uid'=>$uid,'_event'=>$_event, 'typeuid'=>$typeuid, '_order'=> $k , 'listpg'=>1, 'scroll_mode'=>'on','pstock_chk'=>$pstock_chk,'free_delivery_chk'=>$free_delivery_chk,'coupon_chk'=>$coupon_chk, 'brand'=>$brand )); ?>" class="opt"><?php echo $v;?></a></li>
								<?php }?>
							<?php }?>
                        </ul>
                    </div>
                </div><!-- end range_box -->

				<div class="type_box">
					<a href="#none" onclick="return false;" class="btn this_box js_onoff_event js_list_type<?php echo ($list_type =='box' || $list_type=='' ?' hit':null); ?>" data-type="box" title="박스형">
						<span class="ic"></span>
					</a>
					<a href="#none" onclick="return false;" class="btn this_list js_onoff_event js_list_type<?php echo ($list_type == 'list'?' hit':null); ?>" data-type="list" title="리스트형">
						<span class="ic"></span>
					</a>
				</div><!-- end type_box -->

				<?php // 검색에서만 사용 ?>
                <a href="#none" class="btn_filter js_onoff_event" data-target=".c_comb" data-add="if_open_filter"><strong>필터</strong></a>

            </div><!-- end right_box -->
        </div>
    </div>


	<script>

		// 브랜드 변수 -> product.brand_list
		var brand = '<?php echo $brand; ?>';

        $(document).on('click', '.js_list_type', function(e) {
            e.preventDefault();
            var list_type = $(this).data('type');
            if(typeof list_type == 'undefined'){ return false; }
            $('.js_list_type').removeClass('hit');
            $(this).addClass('hit');
            pro_list_search_chk();
        });


		// 품절상품 제외 체크
		$(document).on('click', '.js_pstock_chk', function() {
			pro_list_search_chk();
		});

		// 무료배송 상품 체크
		$(document).on('click', '.js_free_delivery_chk', function() {
			pro_list_search_chk();
		});

		// 할인쿠폰 체크
		$(document).on('click', '.js_coupon_chk', function() {
			pro_list_search_chk();
		});

		// MD's픽 정렬클릭시 (md's 픽 더보기에서만 적용
		$(document).on('click', '.js_order_filter', function() {
            var list_order = $(this).data('order');
            if(typeof list_order == 'undefined'){ return false; }
            $('.js_order_filter').removeClass('hit');
            $('.js_order_filter[data-order="'+list_order+'"]').addClass('hit');

			pro_list_search_chk();
		});


		var product_list_auth=true;
		function pro_list_search_chk(){

			if(product_list_auth !=true){return false}
			product_list_auth = false;

			var coupon_chk = $('.js_coupon_chk').prop('checked');					// 할인쿠폰 체크
			var pstock_chk = $('.js_pstock_chk').prop('checked');						// 품절제외 체크
			var free_delivery_chk = $('.js_free_delivery_chk').prop('checked');	// 무료배송 체크
			var list_type = $('.js_list_type.hit').data('type');
			var list_event = $('.js_list_ctrl').data('event');							// 페이지 이벤트

			pstock_chk = pstock_chk==true?'Y':'N';
			free_delivery_chk = free_delivery_chk==true?'Y':'N';
			coupon_chk = coupon_chk==true?'Y':'N';

			// md's픽에서만 값 지정
			console.log(list_event);
			if(list_event=='main_md'){

				var list_dmsuid = $('.js_list_ctrl').data('dmsuid');							// md 더보기 버튼 클릭후 넘어왔을경우
				var _order = $('.js_order_filter.hit').data('order');
				var _event = list_event;


                $.ajax({
                    data: {_event: 'main_md',_list_type: 'ajax.product_md',view_type:'list',search_mode:'Y',_order : _order,listpg:'1',pstock_chk:pstock_chk,free_delivery_chk:free_delivery_chk,coupon_chk:coupon_chk,list_type:list_type,dmsuid:list_dmsuid , brand : brand },
                    type: 'post',
                    cache: true,
                    url: '<?php echo OD_PROGRAM_URL; ?>/product.list.php',
                    success: function(data) {
                        $('.js_list_md').html(data);
						product_list_auth = true;
                    }
                });
			}else{
				// 기본 데이터
				var cuid = '<?php  echo $cuid; ?>';
				var uid = '<?php  echo $uid; ?>';
				var _event = '<?php  echo $_event; ?>';
				var typeuid = '<?php  echo $typeuid; ?>';
				var _order = '<?php  echo $_order; ?>';
				var url = '<?php echo $pn;?>';

				location.href="/?pn="+url+"&search_mode=Y&uid="+uid+"&cuid="+cuid+"&_event="+_event+"&typeuid="+typeuid+"&_order="+_order+"&listpg=1&scroll_mode=on&pstock_chk="+pstock_chk+"&free_delivery_chk="+free_delivery_chk+"&coupon_chk="+coupon_chk+"&list_type="+list_type+"&brand="+brand;
			}

		}

	</script>
</div><!-- end p_List_ctrl -->