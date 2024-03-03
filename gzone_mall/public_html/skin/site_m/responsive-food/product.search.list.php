<?php
	defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
	/*
		$TotalCount -> 전체 검색 수 => (/program/product.search.list.php에서 지정)
		$res -> 검색결과 데이터 => (/program/product.search.list.php에서 지정)
		$arr_hashtag -> 검색결과에 해당되는 해시태그 => (/program/product.search.list.php에서 지정)
		$arr_brand -> 검색결과에 해당되는 브랜드 => (/program/product.search.list.php에서 지정)
		$arr_price -> 기본 가격 정렬 변수(직접 사용 금지: $skin_price에 필요한 항목만 추출하여 사용) => (/program/product.search.list.php에서 지정)
			EX>
			// 가격 검색 리스트 / 필요한 항목만 사용
			$skin_price = array(
				'1만원 이하'=>'10000_lower',
				'3만원 이하'=>'30000_lower',
				'5만원 이하'=>'50000_lower',
				'7만원 이하'=>'70000_lower',
				'7만원 이상'=>'70000_upper',
			);
		$search_word -> 검색키워드 => (/program/product.search.list.php에서 지정)
		$search_hashtag -> 해시키워드/검색키워드에서 해시 추출 외 다이렉트 해시 검색 시 => (/program/product.search.list.php에서 지정)
		$search_option -> 추가 검색 항목 배열 => (/program/product.search.list.php에서 지정)
		$HitProduct -> 다른 고객이 많이 찾은 상품 => (/program/product.search.list.php에서 지정)
	*/


	// 가격 검색 리스트 / 필요한 항목만 사용
	$skin_price = array(
		'1만원 이하'=>'10000_lower',
		'3만원 이하'=>'30000_lower',
		'5만원 이하'=>'50000_lower',
		'7만원 이하'=>'70000_lower',
		'7만원 이상'=>'70000_upper',
	);


	// 쉼표 기준으로 글자를 추가, 삭제 하는 스위치 함수 - 스킨마다 다를 수 있음으로 해당 페이지에서 함수 생성
	function switch_text($origin_text='', $s_text='') {

		$origin_text = urldecode($origin_text);
		$s_text = urldecode($s_text);

		$origin_text = array_filter(explode(',', $origin_text));

		if(isset($s_text) && $s_text != '') $s_text = trim($s_text);
		if(count($origin_text) > 0) {
			$origin_text = array_map('trim', $origin_text);
			ksort($origin_text);
		}
		if(isset($s_text) && $s_text != '') {
			if(in_array($s_text, $origin_text)) {
				$origin_text = array_flip($origin_text); // index와 value를 flip
				unset($origin_text[$s_text]);
				$origin_text = array_keys($origin_text); // index와 value를 flip 하면서 index초기화
			}
			else {
				$origin_text[] = $s_text;
			}

			// ㄱㄴㄷ순(인덱스) 으로 정렬
			$origin_text = array_flip($origin_text);
			ksort($origin_text);
			$origin_text = array_keys($origin_text); // index와 value를 flip
		}
		return urlencode(count($origin_text) > 0?implode(',', $origin_text):'');
	}
?>

<div class="c_page_tit">
    <div class="layout_fix">
        <div class="tit_box">
            <a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
            <div class="tit">통합검색</div>
        </div>
	</div>
</div><!-- end c_page_tit -->


<div class="c_section c_comb">

	<div class="search_form">
		<div class="layout_fix">
			<div class="inner">
				<form action="/" method="get" onsubmit="return searchPagesFunction(this);" class="form_box">
					<input type="hidden" name="pn" value="product.search.list">
					<input type="hidden" name="cuid" value="<?php echo $cuid; ?>">
					<input type="hidden" name="search_hashtag" value="<?php echo $search_hashtag; ?>">
					<input type="hidden" name="search_price" value="<?php echo $search_price; ?>">
					<input type="hidden" name="search_brand" value="<?php echo $search_brand; ?>">
					<input type="hidden" name="search_boon" value="<?php echo $search_boon; ?>">

					<input type="text" name="search_word" value="<?php echo $search_word; ?>" class="search_input js_search_word" autocomplete="off" placeholder="검색어 입력" />
					<button type="submit" class="btn_search">검색</button>
				</form>

				<?php if($TotalCount > 0) { // 검색결과가 있는경우 ?>
					<div class="result_word">
						<strong>"<?php echo ($search_word?$search_word:($search_hashtag?'#'.implode(', #', explode(',', $search_hashtag)).'':null)); ?>"</strong>에 대한 검색결과입니다.
					</div>
				<?php } else { ?>
				<?php } ?>
			</div>
		</div>
	</div><!-- end search_form -->


	<?php if($TotalCount > 0 || $TotalAllCount > 0) { // 검색결과가 있는경우 ?>
		<div class="layout_fix">
			<div class="result_item">

				<?php if(count($search_option) > 0) { // 추가검색(상세검색) ?>
					<div class="filter_box">
						<div class="scroll c_scroll_v">
							<div class="filter_tit">
								<div class="tit">상세조건</div>
								<?php 
									$hashtag_cnt = explode(',',$search_hashtag);
									if(($search_hashtag && count($hashtag_cnt)>1 ) || $search_price || $search_brand || $search_boon || $cuid) { 
								?>
										<?php // 검색조건 선택시에만 나타남 ?>
										<div class="reset">
											<?php
												// KAY :: 2022-08-25 :: 해시태그 검색 후 초기화시 #로 들어가서 초기화가 안됨 %23으로 수정
												if(preg_match('/^#/',$search_word)){
													$search_word = preg_replace('/^#/', '', $search_word);
													$search_word = '%23'.$search_word;
												}
											?>
											<a href="/?pn=<?php echo $pn; ?>&search_word=<?php echo $search_word.($detail_search?'&detail_search='.$detail_search:null).($search_word_detail?'&search_word_detail='.$search_word_detail:null); ?>" class="btn_reset">
												<span class="tx">필터 초기화</span>
											</a>
										</div>
								<?php } ?>
								<a href="#none" class="btn_close js_onoff_event" data-target=".c_comb" data-add="if_open_filter"></a>
							</div>

							<?php
								// 카테고리
								if(in_array('category', $search_option)){ 
									// 카테고리 전체 선택 여부(초기: false)
									$AllSelected = false;

									// 카테고리 전체 선택 여부
									if(empty($cuid) || $cuid == '') $AllSelected = true;

									// 카테고리 전체 선택 여부따른 전체 선택 URL 변경
									$AllCheckUrl = ProductOrderLinkBuild(array('cuid'=>'', 'listpg'=>1));
							?>
								<dl>
									<dt>카테고리</dt>
									<dd>
										<ul class="list">
											<?php // 1차를 선택해서 2차가 있으면 2차나옴 (3차안나옴) / 처음에는 1차만 ?>
											<li>
												<div class="c_select">
													<select onchange="location.href=this.value;">
														<option value="<?php echo $AllCheckUrl; ?>"<?php echo ($AllSelected === true?' checked':null); ?>>전체 카테고리</option>
														<?php
															foreach($arr_category as $ack=>$acv) {
																$cateUrl = ProductOrderLinkBuild(array('cuid'=>$ack,'listpg'=>1));
														?>
															<option value="<?php echo $cateUrl; ?>"<?php echo (isset($ActiveCate['cuid'][0]) && $ActiveCate['cuid'][0] == $ack?' selected':null); ?>><?php echo $acv['name']; ?></option>
														<?php } ?>
													</select>
													<span class="icon"></span>
												</div>
											</li>
											<?php if(isset($cuid) && $cuid != '' && count($arr_category[$ActiveCate['cuid'][0]]['item']) > 0) { ?>
												<li>
													<?php // 2차 ?>
													<div class="c_select">
														<select onchange="location.href=this.value;">
															<option value="<?php echo ProductOrderLinkBuild(array('cuid'=>$ActiveCate['cuid'][0],'listpg'=>1)); ?>">전체</option>
															<?php
																foreach($arr_category[$ActiveCate['cuid'][0]]['item'] as $ack2=>$acv2) {
																	$cateSubUrl = ProductOrderLinkBuild(array('cuid'=>$ack2,'listpg'=>1));
															?>
																<option value="<?php echo $cateSubUrl; ?>"<?php echo (isset($ActiveCate['cuid'][1]) && $ActiveCate['cuid'][1] == $ack2?' selected':null); ?>><?php echo $acv2['name']; ?></option>
															<?php } ?>
														</select>
														<span class="icon"></span>
													</div>
												</li>
											<?php } ?>
										</ul>
									</dd>
								</dl>
							<?php } ?>

							<?php
								// 브랜드
								if(in_array('brand', $search_option) && count($arr_brand) > 0) {
									// 브랜드 전체 선택 여부(초기: false)
									$AllChecked = false;

									// 브랜드 전체 선택 여부(URL의 브랜드 값과 전체 브랜드 값이 같다면 true)
									if(empty($search_brand) || $search_brand == '') $AllChecked = true;

									// 브랜드 전체 선택 여부따른 전체 선택 URL 변경
									$AllCheckUrl = ProductOrderLinkBuild(array('search_brand'=>'','listpg'=>1));
							?>
								<dl>
									<dt>브랜드</dt>
									<dd>
										<div class="list">
											<ul>
												<?php
												foreach($arr_brand as $bk=>$bv) {
													$brandUrl = ProductOrderLinkBuild(array('search_brand'=>switch_text($search_brand, $bk), 'listpg'=>1));
												?>
													<li>
														<label class="c_label">
															<input type="checkbox" onclick="location.href='<?php echo $brandUrl; ?>';"<?php echo (in_array($bk, explode(',',$search_brand))?' checked':null); ?>/>
															<span class="tx"><span class="icon"></span><?php echo $bk; ?></span>
														</label>
													</li>
												<?php } ?>
											</ul>
										</div>
									</dd>
								</dl>
							<?php } ?>

							<?php
								// 가격대
								if(in_array('price', $search_option) && count($arr_price) > 0 && count($skin_price) > 0) {
									// 가격 전체 선택 여부(초기: false)
									$AllChecked = true;

									// 가격 전체 선택 여부
									if(isset($search_price) && $search_price != '') $AllChecked = false;

									// 가격 전체 선택 여부따른 전체 선택 URL 변경
									$AllCheckUrl = ProductOrderLinkBuild(array('search_price'=>'', 'listpg'=>1));
							?>
								<dl>
									<dt>가격대</dt>
									<dd>
										<div class="list">
											<ul>
												<li>
													<label class="c_label">
														<input type="radio" onclick="location.href='<?php echo $AllCheckUrl; ?>';" <?php echo ($AllChecked === true?' checked':null); ?> />
														<span class="tx"><span class="icon"></span>전체</span>
													</label>
												</li>
												<?php
													foreach($skin_price as $pk=>$pv) {
														$priceUrl = ProductOrderLinkBuild(array('search_price'=>$pv, 'listpg'=>1));
												?>
													<li>
														<label class="c_label">
															<input type="radio" onclick="location.href='<?php echo $priceUrl; ?>';"<?php echo ($search_price == $pv?' checked':null); ?>/>
															<span class="tx"><span class="icon"></span><?php echo $pk; ?></span>
														</label>
													</li>
												<?php } ?>
											</ul>
										</div>
									</dd>
								</dl>
							<?php } ?>

							<?php
								 // 혜택구분
								if(in_array('boon', $search_option)) {

									// 혜택구분 전체 선택 여부(초기: false)
									$AllChecked = false;

									// 혜택구분 전체 선택 여부
									if($search_boon == '') $AllChecked = true;

									// 혜택구분 전체 선택 여부따른 전체 선택 URL 변경
									$AllCheckUrl = ProductOrderLinkBuild(array('search_boon'=>'', 'listpg'=>1));
							?>
								<dl>
									<dt>혜택구분</dt>
									<dd>
										<div class="list">
											<ul>
												<li>
													<label class="c_label">
														<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '무료배송'), 'listpg'=>1)); ?>'"<?php echo (in_array('무료배송', explode(',', $search_boon))?' checked':null); ?> /><span class="tx"><span class="icon"></span>무료배송</span>
													</label>
												</li>
												<li>
													<label class="c_label">
														<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '조건부 무료배송'), 'listpg'=>1)); ?>'"<?php echo (in_array('조건부 무료배송', explode(',', $search_boon))?' checked':null); ?> /><span class="tx"><span class="icon"></span>조건부 무료배송</span></label>
												</li>
												<li>
													<label class="c_label">
														<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '할인'), 'listpg'=>1)); ?>'"<?php echo (in_array('할인', explode(',', $search_boon))?' checked':null); ?> /><span class="tx"><span class="icon"></span>할인 상품</span>
													</label>
												</li>
												<li>
													<label class="c_label">
														<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '쿠폰'), 'listpg'=>1)); ?>'"<?php echo (in_array('쿠폰', explode(',', $search_boon))?' checked':null); ?> /><span class="tx"><span class="icon"></span>쿠폰 상품</span>
													</label>
												</li>
												<li>
													<label class="c_label">
														<input type="checkbox" onclick="location.href='<?php echo ProductOrderLinkBuild(array('search_boon'=>switch_text($search_boon, '적립금'), 'listpg'=>1)); ?>'"<?php echo (in_array('적립금', explode(',', $search_boon))?' checked':null); ?> /><span class="tx"><span class="icon"></span>적립금 지급</span>
													</label>
												</li>
											</ul>
										</div>
									</dd>
								</dl>
							<?php } ?>

							<?php
								// 해시태그
								if(in_array('hashtag', $search_option) && count($arr_hashtag) > 0) { // 해시태그

									// 해시태그 전체 선택 여부(초기: false)
									$AllChecked = false;

									// 해시태그 전체 선택 여부(URL의 해시태그 값과 전체 해시태그 값이 같다면 true)
									if(empty($search_hashtag) || $search_hashtag == '') $AllChecked = true; // 해시태그가 없는 경우도 있다.

									// 해시태그 전체 선택 여부따른 전체 선택 URL 변경
									$AllCheckUrl = ProductOrderLinkBuild(array('search_hashtag'=>'','search_word'=>urlencode($search_word), 'listpg'=>1)); // 해시태그가 없는 경우도 있다.
							?>
								<dl>
									<dt>관련태그</dt>
									<dd>
										<div class="list type_hash">
											<ul>
												<?php
													foreach($arr_hashtag as $hk=>$hv) {
														$HashTagUrl = ProductOrderLinkBuild(array('search_hashtag'=>switch_text($search_hashtag, $hk), 'listpg'=>1));
												?>
													<li>
														<label class="c_label">
															<input type="checkbox" onclick="location.href='<?php echo $HashTagUrl; ?>';"<?php echo (in_array($hk, explode(',',$search_hashtag))?' checked':null); ?>/>
															<span class="tx">	<span class="icon"></span>#<?php echo $hk; ?></span>
														</label>
														</li>
												<?php } ?>
											</ul>
										</div>
									</dd>
								</dl>
							<?php } ?>

						</div><!-- end scroll -->
					</div><!-- end filter_box -->
					<div class="bg_filter_close js_onoff_event" data-target=".c_comb" data-add="if_open_filter" onclick="return false;"></div>
				<?php } ?>

				<?php
					// 상품 정보 가져오기
					$list_type_pcclass = $list_type_moclass = $list_type_class='';

					if($list_type == 'list' ||($list_type=='' && $siteInfo['s_search_display_type']=='list') ){
						$list_type_class = 'if_list_type';
						$list_type='list';
					}else{
						$list_type='box';
					}

					if($siteInfo['s_search_display_type']==$list_type && $list_type!=''){
						if($list_type=='list'){
							$list_type_pcclass = 'pc_type_list'.$siteInfo['s_search_display'];
							$list_type_moclass = 'mobile_type_list'.$siteInfo['s_search_mobile_display'];
						}else{
						$list_type_pcclass = 'pc_type_box'.$siteInfo['s_search_display'];
							$list_type_moclass = 'mobile_type_box'.$siteInfo['s_search_mobile_display'];
						}
					}
				?>
				<div class="area_item" id="total_cnt">
					<?php
						include_once(OD_SITE_MSKIN_ROOT.'/product.ctrl.php');	 // 목록컨트롤러
						if(count($res)>0){
							include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');		// 상품리스트 호출
					?>
						<div class="c_pagi"><?php echo pagelisting_mobile($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?></div>

					<?php }else{ ?>
						<div class="c_none"><div class="gtxt">선택하신 필터와 일치하는 상품이 없습니다.<br/>다른 필터를 확인해 보세요.</div></div>
					<?php }?>
				</div><!-- end area_item -->

			</div><!-- end result_item -->
		</div><!-- end layout_fix -->

	<?php // 검색결과 없을때 ?>
	<?php }else{ ?>
		<div class="c_none"><div class="gtxt">입력하신 단어로 검색된 결과가 없습니다.<br/>다른 검색어를 입력해 주세요.</div></div>

		<?php if(count($HitProduct) > 0) { // 검색결과가 없는경우 ?>
			<div class="other_recommend">
				<div class="tit">다른 고객이 많이 찾은 상품</div>
				<?php
					$res = $HitProduct;
					include(OD_SITE_MSKIN_ROOT.'/ajax.product.list.php');
				?>
			</div>
		<?php } ?>
	<?php } ?>

</div><!-- end c_section -->









<script type="text/javascript">
	// 페이지 내부 검색 필수 여부 체크
	function searchPagesFunction(target) {
		var ck = $(target).find('.js_detail_search').is(':checked');
		if($(target).find('.js_search_word').val() == '') {
			alert('검색어를 입력해주세요');
			$(target).find('.js_search_word').focus();
			return false;
		}
	}

	$(document).on('click', '.js_detail_search', function(e) {
		e.preventDefault();
		var view_status = $(this).closest('.condition').hasClass('if_unfold');
		if(view_status === true) {
			$(this).closest('.condition').removeClass('if_unfold');
			$(this).prop('title', '상세검색 열기');
			$(this).find('span.tx').text('상세검색 열기');
		}
		else {
			$(this).closest('.condition').addClass('if_unfold');
			$(this).prop('title', '상세검색 닫기');
			$(this).find('span.tx').text('상세검색 닫기');
		}
	});
</script>