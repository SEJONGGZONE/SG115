<?php include_once('wrap.header.php');?>

	<?php
		$tempSkinInfo = SkinInfo('category');

		// -- 메인 :: 스킨에 따른 단수처리
		$arrProDisplay['arrDepth']['box'] = explode(",",$tempSkinInfo['pc_pro_box_depth']);	// pc 박스형
		$arrProDisplay['arrDepth']['list'] = explode(",",$tempSkinInfo['pc_pro_list_depth']);	// pc 리스트형
		$arrProDisplay['arrDepthMo']['box'] = explode(",",$tempSkinInfo['mo_pro_box_depth']);	//모바일 박스형
		$arrProDisplay['arrDepthMo']['list'] = explode(",",$tempSkinInfo['mo_pro_list_depth']);	// 모바일 리스트형

		foreach($arrProDisplay['arrDepth'] as $apdk => $apdv){
			foreach($apdv as $apdkk=>$apdvv){
				$arrProDisplay['arrDepthName'][$apdk][$apdvv] = $apdk=='box' ? $tempSkinInfo['pro_depth_all_title']['pc_box_'.$apdvv]:$tempSkinInfo['pro_depth_all_title']['pc_list_'.$apdvv];
			}
		}

		foreach($arrProDisplay['arrDepthMo'] as $admk => $admv){
			foreach($admv as $admkk=>$admvv){
				$arrProDisplay['arrDepthNameMo'][$admk][$admvv] =  $admk=='box' ? $tempSkinInfo['pro_depth_all_title']['mo_box_'.$admvv]:$tempSkinInfo['pro_depth_all_title']['mo_list_'.$admvv];
			}
		}

		$arrProDisplay['arrDepth']['box'][]='list_none';
		$arrProDisplay['arrDepthName']['box'][] = '===========';

		$arrProDisplay['arrListName'] = array_merge($arrProDisplay['arrDepthName']['box'],$arrProDisplay['arrDepthName']['list']);	

		$list_display_type = $siteInfo['s_brand_display_type']!=''?$siteInfo['s_brand_display_type']:'box';

		// 기본 값
		$list_default = $siteInfo['s_brand_display']>0?$siteInfo['s_brand_display']:$tempSkinInfo['pc_pro_depth_default'];								// 목록 상품진열 pc
		$list_mo_default = $siteInfo['s_brand_mobile_display']>0?$siteInfo['s_brand_mobile_display']:$tempSkinInfo['mo_pro_depth_default'];		// 목록 상품진열 모바일
		$list_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_default.'단형';
		$list_mo_default = ($list_display_type=='box'?'박스 ':'리스트 ').$list_mo_default.'단형';

		$arrProDisplay['arrListNameMo'] = $arrProDisplay['arrDepthNameMo'][$list_display_type];

	?>
	<form name="formBrand" method="post" action="_brand.pro.php" target="common_frame" class="data_search">
		<input type="hidden" name="_mode" value="modify">
		<input type="hidden" name="list_view_type" value="<?php echo $list_display_type;?>">

		<div class="group_title">
			<strong>Setting</strong>
			<div class="btn_box">
				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">설정하기</a>
				<a href="#none" class="c_btn red" onclick="$('.js_thisform').toggle(); return false;">브랜드 등록</a>
			</div>
		</div>

		<div class="js_thisview" style="display:none">
			<table class="table_form">
				<colgroup>
					<col width="160"/><col width="*"/><col width="160"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>메뉴노출</th>
						<td>
							<?php echo _InputRadio( '_brand_view' , array('Y','N'), ($siteInfo['s_brand_view'] ? $siteInfo['s_brand_view'] : 'N') , '' , array('노출','숨김') , ''); ?>
							<?php echo _DescStr('상단메뉴에 노출여부만 설정하며 숨김되더라도 설정은 계속 사용가능합니다.',''); ?>
							<?php echo _DescStr('브랜드는 사용자페이지에서 <strong>가나다순</strong> 및 <strong>ABC순</strong>으로 자동정렬됩니다.'); ?>
						</td>
						<th>상품진열</th>
						<td>
							<div class="lineup-row type_responsive">
								<?php
									echo _InputSelect( '_brand_display' , array_values($arrProDisplay['arrListName']) , $list_default , ' class="js_brand_view_pc" ' , array_values($arrProDisplay['arrListName']) , 'PC 상품진열');
									echo _InputSelect( '_brand_mobile_display' , array_values($arrProDisplay['arrListNameMo']) , $list_mo_default , ' class="js_brand_view_mo" ' , array_values($arrProDisplay['arrListNameMo']) , '반응형 상품진열');
								?>
								<a href="#none" class="c_btn sky line js_onoff_event" data-target=".help_item_setting" data-add="if_open_help">도움말</a>
							</div>
							<?php echo _DescStr('첫 화면부터 반응형(모바일)의 상품진열 개수를 설정합니다.',''); ?>
							<? include_once("help.item_setting.php"); //단수도움말 ?>
						</td>
					</tr>
					<tr>
						<th>목록 페이지당 상품 개수</th>
						<td colspan="3">
							<?php echo _InputRadio('_brand_limit', array(20,30,40,50,60,80,100), ($siteInfo['s_brand_limit'] ?$siteInfo['s_brand_limit']:20) , "" ,array('20개','30개','40개','50개','60개','80개','100개'), "");  ?>
							<?php echo _DescStr('선택한 상품진열 단수에 따라 알맞게 설정해주세요.',''); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</form>



	<form name="formAdminMenuData">
		<input type="hidden" name="locUid1" value="<?php echo $locUid1?>"> <!-- 선택된 1차카테고리 -->
		<input type="hidden" name="locUid2" value="<?php echo $locUid2?>"> <!-- 선택된 2차카테고리 -->
	</form>

	<div class="view-admin-menu-list inner_box" data-depth="1">
		<?php
			$viewDepth = 1;
			include dirname(__FILE__)."/_brand.ajax_list.php";
		?>
	</div>


	<?php // -- 브랜드 클릭 시 폼 ajax -- ?>
	<div class="view-admin-menu-form" data-name="view-form"></div>


	<div class="c_btnbox type_full">
		<ul>
			<li><a href="#none" onclick="brand_submit(); return false;" class="c_btn h46 red">저장</a></li>
		</ul>
	</div>

	<div class="fixed_save js_fixed_save" style="display:none;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox type_full">
				<ul>
					<li><a href="#none" onclick="brand_submit(); return false;" accesskey="s" class="c_btn h34 red">저장</a></li>
				</ul>
			</div>
		</div>
	</div>

<script>

// -- 브랜드순서변경
function idxAdminMenu(_type,_uid,_depth)
{

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();
	var url = '_brand.ajax_pro.php';

	_depth = _depth*1;
	_uid = _uid * 1;

  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode:'idx', _type : _type, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth },
      success: function(data){
      	if(data.rst == 'success'){
      		viewAdminMenuListReload(_uid,_depth);
      		return false;
      	}else{
      		alert(data.msg);
      		return false;
      	}

      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 단독으로 브랜드 새로고침 -
function viewAdminMenuListReload(_uid, _depth)
{

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();

	_depth = _depth*1;
	_uid = _uid * 1;


	if(_depth == 1){
		$('.add-admin-menu').hide();
		$('form[name="formAdminMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formAdminMenuData"] [name="locUid2"]').val('');
		$('.add-admin-menu[data-depth="2"]').show();

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 브랜드를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu').hide();
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var url = '_brand.ajax_list.php';

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2},
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+_depth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 브랜드를 클릭 시 :: 3차브랜드는 제외
function viewAdminMenuList(_depth,_uid)
{
	$('.view-admin-menu-form').html('');

	if(_depth == '' || _depth == undefined){ return false; }
	if(_uid == '' || _uid == undefined){ _uid = '0'; }

	_depth = _depth*1;
	_uid = _uid * 1;

	$('.add-admin-menu').hide();
	if(_depth == 1){
		$('form[name="formAdminMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formAdminMenuData"] [name="locUid2"]').val('');
		$('.add-admin-menu[data-depth="2"]').show();

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 브랜드를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var viewDepth = _depth + 1;
	var viewUid = _uid* 1;
	var url = '_brand.ajax_list.php';

	$('.admin-menu-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
	$('.admin-menu-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid },
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+viewDepth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });
}


// -- 브랜드 추가/수정
function saveAdminMenu(uid)
{
	// 수정
	if(uid > 0 ) {
		var mode = 'modify';
		var _name = $("input[name='_name["+ uid +"]']").val();
		var _view = $("input[name='_view["+ uid +"]']").filter(function() {if (this.checked) return this;}).val();

		if(_name == '' || _name == undefined ){ alert('브랜드명을 입력해 주세요.'); $("input[name='_name["+ uid +"]']").focus(); return false; }
		if(_view == '' || _view == undefined){ alert('노출여부를 선택해 주세요.'); $("input[name='_view["+ uid +"]']").focus(); return false; }

	}
	// 추가
	else {
		var mode = 'add';
		var _name = $("input[name='ADD_name']").val();
		var _view = $("input[name='ADD_view']").filter(function() {if (this.checked) return this;}).val();
		uid = 0;

		if(_name == '' || _name == undefined ){ alert('브랜드명을 입력해 주세요.'); $("input[name='ADD_name']").focus(); return false; }
		if(_view == '' || _view == undefined){ alert('노출여부를 선택해 주세요.'); $("input[name='ADD_view']").focus(); return false; }
	}


	var formData = '_mode='+ mode +'&';
	formData += '_uid=' + uid + '&';
	formData += '_name=' + _name + '&';
	formData += '_view=' + _view;


	var url = '_brand.ajax_pro.php';
	$.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: formData,
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('input[name="'+data.key+'"]').focus();
      	}else if(data.rst == 'fail-modify' ){
      		alert(data.msg); return false;
      	}else if( data.rst == 'success'){
      		alert(data.msg);
			window.location.href="_brand.list.php";
      	}else{
      		window.location.reload();
      	}

      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 브랜드삭제
function deleteAdminMenu(uid)
{

	var _uid = uid;
	if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

	if( confirm("해당 브랜드를 삭제하시겠습니까?") == false ){ return false; }

	var url = '_brand.ajax_pro.php';
	$.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid},
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}
		else if( data.rst == 'success'){
      		alert(data.msg);
			window.location.href="_brand.list.php";
      	}

      },error:function(request,status,error){ console.log(error);}
  });

}


	 // -------------- 일괄노출변경 / 일괄숨김변경 --------------
	 function changeView(_type) {
		 $('._view').each(function(){

			// 노출 적용
			if( _type == 'show'){
				if($(this).val() == 'Y') {$(this).prop('checked', true) ;}
				else if($(this).val() == 'N') {$(this).prop('checked', false) ;}
			}
			// 숨김 적용
			else if( _type == 'hide'){
				if($(this).val() == 'Y') {$(this).prop('checked', false) ;}
				else if($(this).val() == 'N') {$(this).prop('checked', true) ;}
			}

		 });
	 }
	 // -------------- 선택상품 일괄지정 --------------


	// -- 브랜드 노출여부 저장
	function brand_submit()
	{
		formBrand.submit();
	}

	$(document).ready(function(){
		BrandListSelect('reload');
	});


	// 상품 진열 설정 클릭시
	$(document).on('change', '.js_brand_view_pc', BrandListSelect);
	function BrandListSelect(chk_mode) {
		var _brand_view_pc = $('.js_brand_view_pc').find('option:selected').val();
		var _brand_view_pcval = '<?php echo $list_default;?>';
		var _brand_view_moval = '<?php echo $list_mo_default;?>';

		chk_mode = chk_mode=='reload'?'reload':'change';

		if(_brand_view_pc!=''){
			if(chk_mode=='change'){
				_brand_view_moval='';
			}
			$('.js_brand_view_pc').find('option[value=""]').prop('disabled',true);
		}

		$.ajax({
			data: {
				_mode: 'pcSelect',
				_brand_view_pc: _brand_view_pc
			},
			type: 'POST',
			cache: false,
			url: '_brand.ajax_pro.php',
			success: function(data) {
				if(data == '') data = null;
				var result = $.parseJSON(data);
				var _option;
				if(_brand_view_moval!='' && chk_mode=='reload'){
					_option = '<option value="" disabled>반응형 상품진열</option>';
				}else{
					_option = '<option value="">반응형 상품진열</option>';
				}
				if(result && result!=null) {
					$.each(result.name, function(k, v) {
						if(result.type=='box'){
							var option_title = '박스 '+v+'단형';
						}else{
							var option_title = '리스트 1단형';
						}
						_option += '<option value="'+option_title+'" '+( (_brand_view_moval!='' && _brand_view_moval===option_title) ?' selected':'')+'>'+option_title+'</option>';
					});
					$('.js_brand_view_mo').show();
				}
				$('.js_brand_view_mo').html(_option);
				$('input[name="list_view_type"]').val(result.type);
				return result;
			}
		});
	}

	// 상품 진열설정 모바일 클릭시
	$(document).on('change', '.js_brand_view_mo', BrandListSelectMo);
	function BrandListSelectMo() {
		var _brand_view_pc = $('.js_brand_view_pc').find('option:selected').val();
		var _brand_view_mo = $('.js_brand_view_mo').find('option:selected').val();

		if(_brand_view_mo!='' && _brand_view_pc!=''){
			$('.js_brand_view_mo').find('option[value=""]').prop('disabled',true);
		}
	}

</script>





<?php include_once('wrap.footer.php'); ?>