<?php


	// ------------------------- 옵션 설정 -------------------------
	// 옵션이 normal 형일 경우 처리
	//			옵션형태 : normal , color , size

	// 다음 차수의 옵션 형태 추출
	$app_option_depth_type = "p_option" . $depth_next . "_type";

	if($r[$app_option_depth_type] == 'normal') {

		// 재고없이 옵션만 추출
		if( ( str_replace("depth","",$r['p_option_type_chk']) - $depth ) > 1 ) {

			foreach( $sres as $k=>$sr ){
				$str_option .= "
					<a href='#none' class='opt_tx' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\">
						<strong>".$sr['po_poptionname']."</strong>
					</a>
				";
		   }
		}

		// 재고 추출
		else {
			foreach( $sres as $k=>$sr ){
				$str_option .= "
					<a href='#none' class='opt_tx ".($sr['po_cnt'] <=0?'soldout':'')." ' onclick=\"option_select_tmp('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."','".$sr['po_poptionname']."','".$r['p_code']."')\">
						<strong>".$sr['po_poptionname']."</strong>
						<em class='opt_remain'>
							".($sr['po_cnt'] > 0 ? ($isOptionStock ? " (".number_format( 1 * $sr['po_cnt'])."개 남음)" : null) . "</em><em class='opt_price'>" . number_format( 1 * $sr['po_poptionprice']) . "원"  : "일시품절")."
						</em>
					</a>
				";
			}
		}

		// KAY :: 2022-12-16 :: 옵션타이틀 축가
		$p_option_title= $depth_next=='2'?$r['p_option2_title']:$r['p_option3_title'];

		echo "
			<div class='opt_dropbox js_box_optdropbox' data-idx='".$depth_next."'>
				<div class='opt_tx this_selected js_btn_optdropbox'>
					<strong id='option_select" . $depth_next . "_poname'>".($p_option_title!=''?$p_option_title:'옵션을 선택해주세요.')."</strong>
				</div>
				<div class='opt_list c_scroll_v' id='option_select_" . $depth_next . "_box'>
					<div class='opt_tx js_btn_optdropbox'><strong>".($p_option_title!=''?$p_option_title:'옵션을 선택해주세요.')."</strong></div>
					<div class='c_scroll_v'>" . $str_option . "</div>
				</div>
				<span class='arrow'></span>
			</div>

			<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>
		";
	}
	// 옵션이 normal 형일 경우 처리




	// 1차 옵션이 color 형일 경우 처리
	else if($r[$app_option_depth_type] == 'color') {

		$p_option_title= $depth_next=='2'?$r['p_option2_title']:$r['p_option3_title'];
		echo "<div class='this_opt_tit'>".($p_option_title!=''?$p_option_title:'Color 옵션')."</div>";
		echo "<ul>";

		// 컬러는 #컬러값을 입력하거나 이미지를 등록할 수 있도록 / 이미지: [모바일]150 * 150, [PC]35 * 35  / 품절일 때 label에 none 추가 / 선택안되게
		foreach( $sres as $k=>$sr ){

			// 품절여부
			$app_soldout_class ='';
			$app_soldout_chk ='N';
			$app_price_view ='N';
			if(( str_replace("depth","",$r['p_option_type_chk']) - $depth_next ) == 0 && $sr['po_cnt'] <= 0){
				$app_soldout_class = 'soldout';
				$app_soldout_chk ='Y';
			}

			if(str_replace("depth","",$r['p_option_type_chk'])==$depth_next){
				$app_price_view = 'Y';
			}

			//색상 or 이미지
			$app_color_name = (
				$sr['po_color_type'] == 'img' ?
					'background-image:url(\'/upfiles/option/'.$sr['po_color_name'].'\');' :
					'background:' . $sr['po_color_name']
			);

			echo "
				<li>
					<label class='" . $app_soldout_class . "'>
						<input type='radio' name='_option_select". $depth ."' onclick=\"option_select_tmp2('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."' ,'".$r['p_code']."')\" />
						<span class='tx'>
						    <span class='shape'  style='" . $app_color_name . "'></span>
						    <strong>" . $sr['po_poptionname'] . "</strong>
			";

			if($app_price_view=='Y'){
				echo "<em class='opt_remain'>
							".($app_soldout_chk=='N' ? ($isOptionStock ? " (".number_format( 1 * $sr['po_cnt'])."개 남음)" : null) . "</em><em class='opt_price'>" . number_format( 1 * $sr['po_poptionprice']) . "원"  : "일시품절")."
						</em>";
			}

			echo "
                        </span>
					</label>
				</li>
			";
		}

		echo "</ul>";

		echo "<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>";
	} // 1차 옵션이 color 형일 경우 처리



	// 1차 옵션이 size 형일 경우 처리
	else if($r[$app_option_depth_type] == 'size') {

		$p_option_title= $depth_next=='2'?$r['p_option2_title']:$r['p_option3_title'];
		echo "<div class='this_opt_tit'>".($p_option_title!=''?$p_option_title:'Size 옵션')."</div>";
		echo "<ul>";
		// 품절일 때 label에 none 추가 / 선택안되게
		foreach( $sres as $k=>$sr ){


			// 품절여부
			$app_soldout_class ='';
			$app_soldout_chk ='N';
			$app_price_view ='N';
			if(( str_replace("depth","",$r['p_option_type_chk']) - $depth_next ) == 0 && $sr['po_cnt'] <= 0){
				$app_soldout_class = 'soldout';
				$app_soldout_chk ='Y';
			}

			if(str_replace("depth","",$r['p_option_type_chk'])==$depth_next){
				$app_price_view = 'Y';
			}

			echo "
				<li>
					<label class='" . $app_soldout_class . "'>
						<input type='radio' name='_option_select". $depth ."' onclick=\"option_select_tmp2('" . $depth_next . "','".$r['p_option_type_chk']."','".$sr['po_uid']."' ,'".$r['p_code']."')\" />
						<span class='tx'>
						    <strong>". $sr['po_poptionname'] ."</strong>
			";

			if($app_price_view=='Y'){
				echo "<em class='opt_remain'>
							".($app_soldout_chk=='N' ? ($isOptionStock ? " (".number_format( 1 * $sr['po_cnt'])."개 남음)" : null) . "</em><em class='opt_price'>" . number_format( 1 * $sr['po_poptionprice']) . "원"  : "일시품절")."
						</em>";
			}

			echo "
						</span>
					</label>
				</li>
			";
		}

		echo "</ul>";
		echo "<input type='hidden' name='_option_select" . $depth_next . "' ID='option_select" . $depth_next . "_id' value=''>";
	} // 1차 옵션이 size 형일 경우 처리

	// ------------------------- 옵션 설정 -------------------------