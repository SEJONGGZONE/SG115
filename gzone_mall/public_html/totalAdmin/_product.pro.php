<?PHP
	ini_set('memory_limit','512M'); // 2020-02-20 SSJ :: 파일용량이 클경우 썸네일 생성 시 메모리 오류 방지
	include "./inc.php";


	// 상품타입 고정 (delivery)
	$auth_ptype = 'delivery';

	// 입점관리자 일경우 업체 고정
	if($AdminPath == 'subAdmin'){ $_cpid = $com_id; }	

	// 콤마제거
	$_sPrice					= delComma($_sPrice);
	$_screenPrice			= delComma($_screenPrice);
	$_price						= delComma($_price);
	$_stock						= delComma($_stock);
	$_idx						= delComma($_idx);
	$_salecnt					= delComma($_salecnt);
	$_shoppingPayFree	= delComma($_shoppingPayFree);
	$_shoppingPay		= delComma($_shoppingPay);

	// --이미지 경로 ---
	$app_path = "..".IMG_DIR_PRODUCT;

	// - 입력수정 사전처리 ---
	if( in_array( $_mode , array("add" , "modify") ) ) {

		// --사전 체크 ---
		$_code = nullchk($_code , '상품코드를 입력해주시기 바랍니다.');
		$_cpid = nullchk($_cpid , '입점업체를 선택 해주시기 바랍니다.');
		/*$_cuid = nullchk($_cuid , '상품분류를 선택해주시기 바랍니다.');*/
		$_name = nullchk($_name , '상품명을 입력해주시기 바랍니다.');
		$_price = nullchk($_price , '판매가를 입력해주시기 바랍니다.');
		$_content	= nullchk($_content , '상품상세설명을 입력해주시기 바랍니다.');
		// --사전 체크 ---

		// LCY : 2023-01-19 : 추가체크 
		if( $_sale_type == 'T'){
			if( $_sale_sdate > $_sale_edate){  error_msg("올바른 판매기간을 설정해주세요");  }

			// 기간판매일 경우 타임세일 미사용 처리 
			$_time_sale = 'N';
		}

		// KAY :: 2023-02-24 :: 타임세일 체크
		$_time_sale = $_time_sale=='Y'?'Y':'N';
		if( $_time_sale == 'Y'){
			//if( date('Y-m-d H:i:s') > $_time_sale_edate.' '.$_time_sale_eclock){$_view='N'; }		// 타임세일이 종료된 상품은 숨김처리
			if( $_time_sale_sdate > $_time_sale_edate){  error_msg("올바른 타임세일 기간을 설정해주세요");  }
		}

		// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
		$_content = wrap_tag_iframe($_content);
		// 2019-12-04 SSJ :: 이미지 alt 속성 자동추가
		$_content = set_img_alter($_content, $_name);

		$_content			= addslashes($_content);
		$_name			= addslashes($_name);
		$_subname		= addslashes($_subname);
		$_orgin				= addslashes($_orgin);
		$_maker			= addslashes($_maker);

		if( $AdminPath == 'totalAdmin'){
			// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
			$_coupon			= addslashes($_coupon_title) . '|' . $_coupon_type .'|'. rm_comma($_coupon_price) .'|' . rm_comma($_coupon_per) . '|' . rm_comma($_coupon_max);
		}

		// SSJ : 2017-12-12 외부이미지 등록시 기존 이미지 삭제
		if(sizeof($_use_hyperlink)>0){
			foreach($_use_hyperlink as $k=>$v){
				$_tmp = ${$v .'_OLD'};
				if(strpos(${$v}, '//') === false) ${$v} = ''; // 외부이미지 등록시 http(s):// 는 필수로 입력하여야한다
				if(${$v} <> '' && $_tmp <> '' && strpos($_tmp, '//') === false){
					_PhotoDel( $app_path , $_tmp );
					_PhotoDel( $app_path , 'thumbs_s_'.$_tmp );
					_PhotoDel( $app_path , 'thumbs_b_'.$_tmp );
				}
			}
		}
		/** SSJ : 2017-11-09  삭제버튼에의한 이미지 삭제 처리 - 다른 이미지 처리보다 먼저 와야 한다 ****/
		// 상세 이미지 2~10 배열로 정리 //=> 스킨디자인상 실제로는 5번까지만 사용함
		$arr_imgname = array('_img_list_square', '_img_b1', '_img_b2', '_img_b3', '_img_b4', '_img_b5' ,'_img_list_over');
		foreach($arr_imgname as $k=>$v){
			$_tmp = ${$v .'_OLD'};
			if($_tmp) $backup_img_org = str_replace($_tmp, '', $backup_img_org);
		}
		$arr_img_big = array_filter(explode('||', $backup_img_org));

		if(sizeof($arr_img_big) > 0){
			foreach($arr_img_big as $k=>$v){
				if($v <> '' && strpos($v, '//') === false){
					_PhotoDel( $app_path , $v );
					_PhotoDel( $app_path , 'thumbs_s_'.$v );
					_PhotoDel( $app_path , 'thumbs_b_'.$v );
				}
			}
		}
		/** SSJ : 2017-11-09  삭제버튼에의한 이미지 삭제 처리 - 다른 이미지 처리보다 먼저 와야 한다 ****/

		if($_img_auto_resize_use == 'auto' && $_FILES['_img_main_tmp']['name']) {	// 이미지 자동 등록

			// 이미지 등록
			$_img_main_tmp_name = _PhotoPro( $app_path , '_img_main_tmp' ) ;

			// --이미지 썸네일 처리 ---

			if( $_FILES['_img_main_tmp']['size'] >0 ) app_product_auto_thumbnail($app_path , $_img_main_tmp_name);

			$_img_list_square_name	= 'auto_s_'.$_img_main_tmp_name;
			$_img_b1_name			= 'auto_main_'.$_img_main_tmp_name;

			if($_mode == 'modify') {	// 이전에 등록된 이미지를 삭제한다.
				$tmp_r = _MQ("select * from smart_product where p_code = '".$_code."'");
				$arr_imgname = array('p_img_list_square', 'p_img_b1');
				foreach($arr_imgname as $k=>$v){
					if($tmp_r[$v] <> '' && strpos($tmp_r[$v], '//') === false){
						_PhotoDel( $app_path , $tmp_r[$v] );						// 정사각형
						_PhotoDel( $app_path , 'thumbs_b_'.	$tmp_r[$v] );// 정사각형(썸네일삭제)
						_PhotoDel( $app_path , 'thumbs_s_'.	$tmp_r[$v] );// 정사각형(썸네일삭제)
					}
				}
			}


		} else {	// 이미지 직접 등록

			$arr_imgname = array('_img_list_square', '_img_b1', '_img_b2', '_img_b3', '_img_b4', '_img_b5','_img_list_over');
			foreach($arr_imgname as $k=>$v){
				// 이미지 직접 등록
				if(strpos(${$v}, '//') !== false){
					${$v.'_name'} = ${$v};
					if(${$v.'_DEL'} == 'Y'){
						${$v.'_name'} = '';
						_PhotoDel( $app_path , ${$v.'_OLD'} );						// 정사각형
						_PhotoDel( $app_path , 'thumbs_b_'. ${$v.'_OLD'} );// 정사각형(썸네일삭제)
						_PhotoDel( $app_path , 'thumbs_s_'. ${$v.'_OLD'} );// 정사각형(썸네일삭제)
					}
				}else{
					${$v.'_name'} = _PhotoPro($app_path, $v);

					// ![LCY] 썸네일 삭제 안되는 오류 수정
					if(${$v.'_DEL'} == 'Y'){
						_PhotoDel( $app_path , 'thumbs_b_'. ${$v.'_OLD'} );// 정사각형(썸네일삭제)
						_PhotoDel( $app_path , 'thumbs_s_'. ${$v.'_OLD'} );// 정사각형(썸네일삭제)
					}

				}

				// -- 이미지 썸네일 오류 수정 -- 2019-06-18 LCY
				if($v == '_img_list_square' || $v=='_img_list_over'){ $thumbMode = 'list'; }
				else{ $thumbMode = 'main'; }
				// -- 이미지 썸네일 오류 수정 -- 2019-06-18 LCY


				// --이미지 썸네일 처리 ---
				if( $_FILES[$v]["size"] >0 ) app_product_thumbnail($app_path , ${$v.'_name'} , ${$v.'_OLD'} , $thumbMode); // -- 이미지 썸네일 오류 수정 -- 2019-06-18 LCY
			}

		}
		// --이미지 처리 ---

		// 해시태그 정리
		if( trim($_hashtag) != "" ) {
			$_hashtag_tmp = explode(",",trim($_hashtag));
			$_hashtag_tmp = array_filter(array_unique($_hashtag_tmp));
			foreach($_hashtag_tmp as $k=>$v){
				$_hashtag_tmp[$k] = trim($v);
			}

			$_hashtag = implode(",",$_hashtag_tmp);
		}

		// 정상가가 판매가와 같을 경우 정상가 0 적용 kms 2019-09-19
		if ( rm_str($_screenPrice) == rm_str( $_price ) ) {
			$_screenPrice = 0;
		}

		$npay_use = $npay_use == 'N' ? 'N':'Y'; // 네이버페이는 기본 사용이므로,,,
		$_use_content ='Y';



		// --query 사전 준비 ---
		$sque = "
			p_cpid				= '" . $_cpid . "'
			, p_stock				= '" . rm_str($_stock)  . "'
			, p_screenPrice			= '" . rm_str($_screenPrice)  . "'
			, p_price				= '" . rm_str($_price)  . "'
			, p_name				= '" . $_name . "'
			, p_subname				= '" . $_subname . "'
			, p_content				= '" . $_content  . "'
			, p_shoppingPay_use		= '" . $_shoppingPay_use . "'
			, p_shoppingPay			= '" . $_shoppingPay . "'
			, p_shoppingPayFree		= '" . $_shoppingPayFree . "'
			, p_delivery_info		= '" . $_delivery_info . "'
			, p_orgin				= '" . $_orgin  . "'
			, p_maker				= '" . $_maker  . "'
			, p_img_list			= '" . $_img_list_square_name  . "'
			, p_img_list2			= '" . $_img_list_square_name  . "'
			, p_img_list_square		= '" . $_img_list_square_name  . "'
			, p_img_list_over		= '" . $_img_list_over_name  . "'
			, p_img_b1				= '" . $_img_b1_name  . "'
			, p_img_b2				= '" . $_img_b2_name  . "'
			, p_img_b3				= '" . $_img_b3_name  . "'
			, p_img_b4				= '" . $_img_b4_name  . "'
			, p_img_b5				= '" . $_img_b5_name  . "'
			, p_option_type_chk		= '" . $_option_type_chk  . "'
			, p_option1_type		= '" . $p_option1_type  . "'
			, p_option2_type		= '" . $p_option2_type  . "'
			, p_option3_type		= '" . $p_option3_type  . "'
			, p_relation			= '" . $_relation  . "'
			, p_relation_type			= '" . $_relation_type  . "'
			, p_hashtag				= '".$_hashtag."'
			, p_hashtag_shuffle		= '".$_hashtag_shuffle."'
			, p_use_content		= '".($_use_content=='Y'?'Y':'N')."'
			, p_shoppingPayPdPrice = '" . rm_str($_shoppingPayPdPrice) . "'
			, p_shoppingPayPfPrice = '" . rm_str($_shoppingPayPfPrice) . "'
			, p_brand = '" . $_brand . "'
			, p_option1_title = '".addslashes($p_option1_title)."'
			, p_option2_title = '".addslashes($p_option2_title)."'
			, p_option3_title = '".addslashes($p_option3_title)."'
			, p_type = '".$auth_ptype."'
			, p_buy_limit = '".rm_str($_buy_limit)."' 
			, p_duplicate_use = '".($_duplicate_use == 'N' ? 'N' : 'Y' )."' 
			, p_sale_sdateh = '" . $_sale_sdateh . "'
			, p_sale_sdatem = '" . $_sale_sdatem . "'
			, p_sale_edateh = '" . $_sale_edateh . "'
			, p_sale_edatem = '" . $_sale_edatem . "'
			, p_apply_mapxy = '".$_apply_mapxy."'
			, p_sale_type = '" . $_sale_type."'
			, p_sale_sdate = '" . $_sale_sdate."'
			, p_sale_edate = '" . $_sale_edate."'			

		";
		// --query 사전 준비 ---


		if( $AdminPath == 'totalAdmin'){
			$sque .= "
				, p_commission_type		= '" . $_commission_type  . "'
				, p_salecnt				= '" . rm_str($_salecnt)  . "'
				, p_sPrice				= '" . rm_str($_sPrice)  . "'
				, p_sPersent			= '" . $_sPersent  . "'
				, p_icon				= '". @implode(",",$_icon)."'
				, p_point_per			= '" . preg_replace("/[^0-9.]/","",$_point_per)  . "'
				, p_view				= '" . $_view  . "'
				, p_coupon				= '" . $_coupon  . "'
				, p_sort_group					= '" . delComma($_sort_group) . "'
				, p_sort_idx					= '" . delComma($_sort_idx) . "'
				, p_idx					= '" . delComma($_idx) . "'
				, p_naver_switch		= '".$p_naver_switch."'
				, p_daum_switch			= '".$p_daum_switch."'
				, p_groupset_use		= '".($_groupset_use=='Y'?'Y':'N')."'			
				, npay_use = '" . $npay_use . "'	

				, p_time_sale = '" . $_time_sale."'
				, p_time_sale_sdate = '" . $_time_sale_sdate."'
				, p_time_sale_sclock = '" . $_time_sale_sclock."'
				, p_time_sale_edate = '" . $_time_sale_edate."'
				, p_time_sale_eclock = '" . $_time_sale_eclock."'

			";
		}

		// 2017-06-16 ::: 부가세율설정 ::: JJC -- SSJ : 2018-02-08 복합과세일때만 변경 되도록
		if($p_vat <> '') $sque .= " , p_vat = '" . $p_vat . "' ";

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			// -- 코드 중복 체크 ---
			$r = _MQ("select count(*) as cnt from smart_product where p_code='${_code}' ");
			if( $r[cnt] > 0 ) {
				error_msg("코드가 중복 됩니다.");
			}
			// -- 코드 중복 체크 ---

			// 짧은 URL 적용
			$_shorten_url = get_shortURL("http://".$_SERVER["HTTP_HOST"] . "/?pn=product.view&pcode={$_code}" );

			if($AdminPath == 'subAdmin'){
				// 입점업체가 상품등록시 기본 수수료 형태로 설정되며 관리자가 입력한 수수료율이 설정된다.
				$_commission_type = '수수료';
				$_sPersent = $siteInfo['s_account_commission'];
				$sque .= "
					, p_commission_type		= '" . $_commission_type  . "'
					, p_sPersent			= '" . $_sPersent  . "'
				";
			}

			$que = " insert smart_product set $sque , p_code='{$_code}' , p_rdate = now() , p_mdate = now() , p_shorten_url='{$_shorten_url}' ";
			_MQ_noreturn($que);

			// 이용정보 저장
			foreach($arrProGuideType as $k=>$v){
				_text_info_insert( 'smart_product' , $_code , 'p_guide_'.$k , ${'p_guide_'.$k});
				_text_info_insert( 'smart_product' , $_code , 'p_guide_type_'.$k , ${'p_guide_type_'.$k});
				_text_info_insert( 'smart_product' , $_code , 'p_guide_uid_'.$k , ${'p_guide_uid_'.$k});
			}

			// 카테고리 상품 갯수 업데이트
            update_catagory_product_count();

            // SSJ : 2017-09-18 p_idx 재정렬
            product_resort();

            // JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
            product_option_validate_check($_code);

            // SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
            product_soldout_check($_code);

            // KAY :: 에디터 이미지 관리 :: 에디터에 등록되는 이미지명 추출 :: 2021-06-02 -------------
            editor_img_ex($_content.$_content_m , 'product' , $_code);

            // 타임세일: 판매기간이 T일경우 타임세일 무력화 
            if( $_sale_type == 'T' ){
            	_MQ_noreturn("update smart_product set p_time_sale ='N' where p_code = '".$_code."'  ");
            }

			error_loc("_product.form.php?_mode=modify&_code=${_code}&_PVSC=${_PVSC}");
			break;



		case "modify":
			
			// KAY :: 2024-02-20 :: 입점업체 상품만 수정가능하도록
			if($AdminPath=='subAdmin'){
				$r = _MQ("select * from smart_product where p_code='".$_code."' ");
				if($r['p_cpid']!=$com_id){
					error_loc_msg("_product.list.php","수정 권한이 없는 상품입니다."); 
				}
			}

			$que = " update smart_product set $sque ,  p_mdate = now() where p_code='{$_code}' ";
			_MQ_noreturn($que);

			// 이용정보 저장
			foreach($arrProGuideType as $k=>$v){
				_text_info_insert( 'smart_product' , $_code , 'p_guide_'.$k , ${'p_guide_'.$k});
				_text_info_insert( 'smart_product' , $_code , 'p_guide_type_'.$k , ${'p_guide_type_'.$k});
				_text_info_insert( 'smart_product' , $_code , 'p_guide_uid_'.$k , ${'p_guide_uid_'.$k});
			}

			// 카테고리 상품 갯수 업데이트
            update_catagory_product_count();

            // SSJ : 2017-09-18 p_idx 재정렬
            product_resort();

            // JJC : 옵션 적합성 검사 - p_option_valid_chk 정보 업데이트 : 2018-04-16
            product_option_validate_check($_code);

            // SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
            product_soldout_check($_code);

            // KAY :: 에디터 이미지 관리 :: 2021-06-02 -------------
            editor_img_ex($_content.$_content_m , 'product' , $_code);

            // 타임세일: 판매기간이 T일경우 타임세일 무력화 
            if( $_sale_type == 'T' ){
            	_MQ_noreturn("update smart_product set p_time_sale ='N' where p_code = '".$_code."'  ");
            }


			error_loc("_product.form.php?_mode=${_mode}&_code=${_code}&_PVSC=${_PVSC}");
			break;



		case "delete":
			// -- 상품정보 추출 ---
			$r = _MQ("select * from smart_product where p_code='${_code}' ");

			$arr_imgname = array('p_img_list_square', 'p_img_b1', 'p_img_b2', 'p_img_b3', 'p_img_b4', 'p_img_b5', 'p_img_list', 'p_img_list2','p_img_list_over');
			foreach($arr_imgname as $k=>$v){
				if($r[$v] <> '' && strpos($r[$v], '//') === false){
					// -- 이미지 삭제 ---
					_PhotoDel( $app_path , $r[$v] );
					_PhotoDel( $app_path , "thumbs_s_".$r[$v] );
					_PhotoDel( $app_path , "thumbs_b_".$r[$v] );
				}
			}
			// -- 이미지 삭제 ---

			// -- 옵션 삭제 ---
			_MQ_noreturn("delete from smart_product_option where po_pcode='{$_code}' ");

			// -- 추가옵션 삭제 ---
			_MQ_noreturn("delete from smart_product_addoption where pao_pcode='{$_code}' ");

			// -- 상품 적용 카테고리 삭제 ---
			_MQ_noreturn("delete from smart_product_category where pct_pcode='{$_code}' ");

			// -- 상품 정보제공고시 삭제 ---
			_MQ_noreturn("delete from smart_product_req_info where pri_pcode='{$_code}' ");

			// 이용정보 삭제
			_MQ_noreturn("delete from smart_table_text where ttt_tablename = 'smart_product' and ttt_datauid = '{$_code}' ");

			// 기획전 상품 삭제
			_MQ_noreturn("delete from smart_promotion_plan_product_setup where ppps_pcode = '{$_code}' ");

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($_code,'product');

			// 상품정보 삭제
			_MQ_noreturn("delete from smart_product where p_code='{$_code}' ");

			// 카테고리 상품 갯수 업데이트
			update_catagory_product_count();

			// SSJ : 2017-09-18 p_idx 재정렬
			product_resort();

			error_loc("_product.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			break;



		// 일괄삭제
		case "mass_delete":

			$s_query = " where p_code in ('".implode("','" , array_keys($chk_pcode))."') ";

			// -- 이미지 삭제 ---
			$res = _MQ_assoc("select * from smart_product {$s_query} ");
			$arr_imgname = array('p_img_list_square', 'p_img_b1', 'p_img_b2', 'p_img_b3', 'p_img_b4', 'p_img_b5', 'p_img_list', 'p_img_list2','p_img_list_over');
			foreach($res as $key=>$r){
				foreach($arr_imgname as $k=>$v){
					if($r[$v] <> '' && strpos($r[$v], '//') === false){
						// -- 이미지 삭제 ---
						_PhotoDel( $app_path , $r[$v] );
						_PhotoDel( $app_path , "thumbs_s_".$r[$v] );
						_PhotoDel( $app_path , "thumbs_b_".$r[$v] );
					}
				}
			}
			// -- 이미지 삭제 ---

			// -- 옵션 삭제 ---
			_MQ_noreturn("delete from smart_product_option where po_pcode in ('".implode("','" , array_keys($chk_pcode))."') ");
			// -- 옵션 삭제 ---

			// -- 추가옵션 삭제 ---
			_MQ_noreturn("delete from smart_product_addoption where pao_pcode in ('".implode("','" , array_keys($chk_pcode))."') ");

			// -- 상품 적용 카테고리 삭제 ---
			_MQ_noreturn("delete from smart_product_category where pct_pcode in ('".implode("','" , array_keys($chk_pcode))."') ");

			// -- 상품 정보제공고시 삭제 ---
			_MQ_noreturn("delete from smart_product_req_info where pri_pcode in ('".implode("','" , array_keys($chk_pcode))."') ");

			// 이용정보 삭제
			_MQ_noreturn("delete from smart_table_text where ttt_tablename = 'smart_product' and ttt_datauid in ('".implode("','" , array_keys($chk_pcode))."') ");

			// 기획전 상품 삭제
			_MQ_noreturn("delete from smart_promotion_plan_product_setup where ppps_pcode in ('".implode("','" , array_keys($chk_pcode))."') ");

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del(array_keys($chk_pcode),'product');

			// 상품삭제
			_MQ_noreturn("delete from smart_product {$s_query} ");

			// 카테고리 상품 갯수 업데이트
			update_catagory_product_count();

			// SSJ : 2017-09-18 p_idx 재정렬
			product_resort();

			// 2017-07-20 ::: 상품일괄이동/복사/삭제관리 - 선택상품 일괄 삭제 ::: JJC
			if($_submode == 'mass_move'){
				error_loc("_product_mass.move.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			}
			// 2017-07-20 ::: 상품일괄이동/복사/삭제관리 - 선택상품 일괄 삭제 ::: JJC
			//  상품관리 - 선택상품 일괄 삭제
			else {
				error_loc("_product.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			}
			//  상품관리 - 선택상품 일괄 삭제

			break;


		// --- KAY : 상품일괄업로드 개선 : 2021-07-02 ---
		// 업로드 체크
		case "upload_chk":

			$upload_cnt = _MQ_result(" SELECT puc_cnt FROM smart_product_upload_count where puc_uid = '". $uid ."' ");
			$cnt = _MQ_result(" SELECT count(*) FROM smart_product_option_tmp where pot_pucuid = '". $uid ."' ");

			// 총 개수
			//	상품업로드 개수(total) , 옵션 개수(cnt)
			echo json_encode(array('total' => $upload_cnt , 'cnt' => $cnt)); exit;

			break;
		// --- KAY : 상품일괄업로드 개선 : 2021-07-02 ---



	}
	// - 모드별 처리 ---

	exit;
?>