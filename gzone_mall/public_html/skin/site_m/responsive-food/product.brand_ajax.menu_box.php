
<div class="swipe_box" ID="js_swipe_menu_brand">
	<ul>
		<?php
			// 브랜드 초성 정보 합체
			$arr_prefix = array_merge($arr_prefix_kor , $arr_prefix_eng , array('기타'));
			$cnt = 0; // 줄바꿈을 위한 체크변수
			$arr_prev_prefix = array(); // 바로 앞 prefix 배열 저장

			$all_hit_class = $uid==''?'class="hit"':'';
			
			echo '<li '.$all_hit_class.'><a href="/?pn=product.brand_list&brand='.$brand.'" class="btn"><strong>ALL</strong></a></li>';
			foreach($arr_prefix as $k=>$v){
				if(sizeof($arr_brand_prefix[$v]) > 0){

					// 스펠링타이틀의 경우
					if(!($arr_prev_prefix[$v] > 0)) {
						$cnt ++;
						$arr_prev_prefix[$v]++;
					}

					// 일반 브랜드의 경우
					if(sizeof($arr_brand_prefix[$v]) > 0 ) {
						foreach($arr_brand_prefix[$v] as $sk=>$sv){
							$hit_class = ($sk==$uid)?'class="hit"':'';
							echo ($cnt%6 == 0 && $cnt <> 0 ? '' : ''); // 줄바꿈
							echo '<li '.$hit_class.'><a href="/?pn=product.brand_list&brand='.$brand.'&uid='. $sk .'" class="btn"><strong>'. $sv .'</strong></a></li>';
							$cnt ++;
						}
					}

				}
			}
		?>
	</ul>
</div>