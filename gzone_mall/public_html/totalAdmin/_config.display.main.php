<?php include_once('wrap.header.php');


	// AJAX데이터 서포트
	if($viewUid != ''){
		$row = _MQ("select *from smart_display_main_set where dms_uid = '".$viewUid."'  ");

		$app_uid = $viewUid;
		$app_depth = $row['dms_depth'];

		switch($row['dms_depth'])
		{
			case "1":
				$locUid1 = $row['dms_uid'];
			break;
			case "2":
				$locUid1 = $row['dms_parent'];
				$locUid2 = $row['dms_uid'];
			break;
		}
	}
?>

<form name="formDisplayMainData">
	<input type="hidden" name="locUid1" value="<?php echo $locUid1?>"> <!-- 선택된 1차카테고리 -->
	<input type="hidden" name="locUid2" value="<?php echo $locUid2?>"> <!-- 선택된 1차카테고리 -->
</form>

<div class="category" id="config-display-main">
	<ul class="table">
		<li class="td">
			<div class="depth_tt"><strong>메인 노출영역 (추가불가)</strong></div>
			<div class="view-item-list inner_box" data-depth="1">
				<?php
					$viewDepth = 1;
					include dirname(__FILE__)."/_config.display.main.ajax_list.php";
				?>
			</div>
		</li>
		<li class="td">
			<div class="depth_tt">
				<strong>세부 분류</strong>
				<a href="#none" onclick="configDisplayMain.viewItemForm('add','2',''); return false;" style="<?php echo $locUid1 == '' ? 'display:none;':''?>" class="c_btn h23 blue scrollto add-item" data-scrollto="view-form" data-depth="2">추가</a>
				</div>
			<div class="view-item-list inner_box" data-depth="2">
				<?php
					if( $locUid2 != '' || $locUid1 != ''){
						$viewDepth = 2;
						include dirname(__FILE__)."/_config.display.main.ajax_list.php";
					 }else{
				?>
					<div class="category_before">메인 노출영역을 먼저 선택해주세요.</div>
				<?php } ?>
			</div>
		</li>
	</ul>
</div>

<?php // -- 메뉴 클릭 시 폼 ajax -- ?>
<div id="config-display-main-form" class="view-form" data-name="view-form">
<?php
	// AJAX데이터 서포트
	if( $app_uid > 0 ) {

		$_mode = 'modify';
		$_uid = $app_uid;
		$_depth = $app_depth;
		include dirname(__FILE__)."/_config.display.main.ajax_form.php";
	}
?>
</div>


		<?php if($reload == 'true' && $viewUid != ''){?>
<!-- 		<script>
			$(function(){
				configDisplayMain.viewItemForm('modify','1','<?php  echo $viewUid; ?>');
			});
		</script> -->
		<?php }?>

<script>



	// -- 공통적인 함수 사용을 위해 객체사용
	var configDisplayMain = {};

	// -- 순서변경
	configDisplayMain.idx = function(_type,_uid,_depth)
	{
		var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
		var locUid2 = $('form[name="formDisplayMainData"] [name="locUid2"]').val();
		var url = '_config.display.main.ajax_pro.php';
		_depth = _depth*1;
		_uid = _uid * 1;

		if( _depth == 1){ alert('1차 분류는 순서변경이 불가능합니다.'); return false; }

		$.ajax({
			url: url, cache: false,dataType : 'json', type: "post", data: {_mode:'idx', _type : _type, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth },
			success: function(data){
				if(data.rst == 'success'){
					configDisplayMain.viewListReload(_uid,_depth);
					return false;
				}else{
					alert(data.msg);
					return false;
				}
			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}

	// -- 단독으로 새로고침 -
	configDisplayMain.viewListReload = function(_uid, _depth)
	{
		var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
		_depth = _depth*1;
		_uid = _uid * 1;
		if(_depth == 1){
			$('#config-display-main .add-item').hide();
			$('form[name="formDisplayMainData"] [name="locUid1"]').val(_uid);
			$('#config-display-main .add-item[data-depth="2"]').show();
			$('#config-display-main .view-item-list[data-depth="2"]').html('<div class="category_before">상위 분류를 먼저 선택해주세요.</div>');
		}else if(_depth == 2){
			$('#config-display-main .add-item[data-depth="2"]').show();
		}

		var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
		var url = '_config.display.main.ajax_list.php';
		$.ajax({
			url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid , locUid1 : locUid1 },
			success: function(html){
				$('#config-display-main .view-item-list[data-depth="'+_depth+'"]').html(html);
				$('#config-display-main .item-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
				$('#config-display-main .item-list-tr[data-depth="'+_depth+'"][data-uid="'+_uid+'"]').addClass('hit');
			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}

	// --  클릭 시 :: 3차는 제외
	configDisplayMain.viewList = function(_depth,_uid, cache)
	{
		$('#config-display-main-form').html('');

		if(_depth == '' || _depth == undefined){ return false; }
		if(_uid == '' || _uid == undefined){ _uid = '0'; }

		_depth = _depth*1;
		_uid = _uid * 1;

		$('#config-display-main .add-item').hide();
		if(_depth == 1){
			$('form[name="formDisplayMainData"] [name="locUid1"]').val(_uid);
			$('form[name="formDisplayMainData"] [name="locUid2"]').val('');
			$('#config-display-main .add-item[data-depth="2"]').show();
		}else if(_depth == 2){
			$('form[name="formDisplayMainData"] [name="locUid2"]').val(_uid);
			$('#config-display-main .add-item[data-depth="2"]').show();
		}

		var viewDepth = _depth + 1;
		var viewUid = _uid* 1;
		var url = '_config.display.main.ajax_list.php';

		$('#config-display-main .item-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
		$('#config-display-main .item-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

		// AJAX데이터 서포트 {
		if( typeof cache == 'undefined'){
			var locUid1 = $('form[name="formDisplayMainData"]').find('[name="locUid1"]').val();
			var locUid2 = $('form[name="formDisplayMainData"]').find('[name="locUid2"]').val();
			history.replaceState(null,null,"<?php echo $CURR_FILENAME ?>?locUid1="+locUid1+"&locUid2="+locUid2+"&reload=true");
		}
		// AJAX데이터 서포트 }

		$.ajax({
			url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid },
			success: function(html){
				$('#config-display-main .view-item-list[data-depth="'+viewDepth+'"]').html(html);



			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}


	// -- 수정 또는 추가를 클릭할 시
	configDisplayMain.viewItemForm=function(_mode,_depth,_uid)
	{
		if( _uid == '' || _uid == undefined){ _uid = 0; }


		// -- 현재 위치를 반환
		var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
		var locUid2 = $('form[name="formDisplayMainData"] [name="locUid2"]').val();

		_depth = _depth *1;
		_uid = _uid *1;

		// -- 메뉴추가일 시 상위 카테고리 확인
		if(_mode == 'add'){
			if(_depth == 2){
				if( locUid1 == '' || locUid1 == undefined){ alert("상위분류를 선택해 주세요."); return false; }
			}else if(_depth == 3){
				if( locUid2 == '' || locUid2 == undefined){ alert("상위분류를 선택해 주세요."); return false; }
			}
		}


		// AJAX데이터 서포트 {
		history.replaceState(null,null,"<?php echo $CURR_FILENAME ?>?viewUid="+_uid+'&locUid1='+locUid1+'&locUid2='+locUid2+'&reload=true');
		$('.item-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
		$('.item-list-tr[data-uid="'+_uid+'"]').addClass('hit');
		if( _depth != 2){ configDisplayMain.viewList(_depth,_uid,true); }
		// AJAX데이터 서포트 }

		var url = '_config.display.main.ajax_form.php';
		$.ajax({
			url: url, cache: false,dataType : 'html', type: "get", data: {_mode:_mode,  _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth , reload:'<?php echo $reload == '' ? '': $reload; ?>' },
			success: function(html){

				$('#config-display-main-form').html(html);

			},error:function(request,status,error){ console.log(request.responseText);}
		});
	}

	// -- 메뉴 추가/수정
	configDisplayMain.saveItem=function()
	{
		var formData = $('form[name="formDisplayMain"]').serialize();
		if(formData == '' || formData == undefined){ return false; }

		var chkName = $('form[name="formDisplayMain"] [name="_name"]').val();
		var chkView = $('form[name="formDisplayMain"] [name="_view"]:checked').val();
		if(chkName == '' || chkName == undefined ){ alert('분류명을 입력해 주세요.'); $('form[name="formDisplayMain"] [name="_name"]').focus(); return false; }
		if(chkView == '' || chkView == undefined){ alert('노출여부를 선택해 주세요.'); $('form[name="formDisplayMain"] [name="_view"]').focus(); return false; }

		var url = '_config.display.main.ajax_pro.php';
	  $.ajax({
		  url: url, cache: false,dataType : 'json', type: "post", data: formData,
		  success: function(data){



			if( data.rst == 'error'){
				alert(data.msg); window.location.reload();
			}else if(data.rst == 'blank'){
				alert(data.msg); $('form[name="formDisplayMain"] [name="'+data.key+'"]').focus();
			}else if( data.rst == 'fail-link'){
				alert(data.msg); $('form[name="formDisplayMain"] [name="'+data.key+'"]').focus();
			}else if(data.rst == 'fail-modify' ){
				alert(data.msg); return false;
			}else if( data.rst == 'success'){
				alert(data.msg);

				// AJAX데이터 서포트 {
				var _uid = $('form[name="formDisplayMain"]').find('[name="_uid"]').val();
				var _depth = $('form[name="formDisplayMain"]').find('[name="_depth"]').val();
				var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
				var locUid2 = $('form[name="formDisplayMainData"] [name="locUid2"]').val();
				// window.location.reload();
				switch(data._depth){
					case "1":
						// configDisplayMain.viewItemForm('modify',_depth,_uid);
					break;
					case "2":
					case "3":
						// configDisplayMain.viewItemForm('modify',_depth,_uid);
					break;
				}
				// AJAX데이터 서포트 }
			}else{
				window.location.reload();
			}

		  },error:function(request,status,error){ console.log(request.responseText);}
	  });
	}

	// -- 메뉴삭제
	configDisplayMain.deleteItem=function()
	{

		var _uid = $('form[name="formDisplayMain"] [name="_uid"]').val();
		var _depth = $('form[name="formDisplayMain"] [name="_depth"]').val();
		var _mode = $('form[name="formDisplayMain"] [name="_mode"]').val();

		if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

		if( confirm("해당 분류를 삭제하시겠습니까?") == false ){ return false; }

		var url = '_config.display.main.ajax_pro.php';
	  $.ajax({
		  url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid, _depth : _depth},
		  success: function(data){

			if( data.rst == 'error'){
				alert(data.msg); window.location.reload();
			}else if(data.rst == 'blank'){
				alert(data.msg); $('form[name="formDisplayMain"] [name="'+_data.key+'"]').focus();
			}else if( data.rst == 'fail-link'){
				alert(data.msg); $('form[name="formDisplayMain"] [name="'+_data.key+'"]').focus();
			}else if( data.rst == 'fail-delete'){
				alert(data.msg); $('form[name="formDisplayMain"] [name="'+_data.key+'"]').focus();
			}else if( data.rst == 'success'){
				alert(data.msg);

				var locUid1 = $('form[name="formDisplayMainData"] [name="locUid1"]').val();
				var locUid2 = $('form[name="formDisplayMainData"] [name="locUid2"]').val();

				if(locUid1 == _uid ){ locUid1 = ''; }
				if(locUid2 == _uid ){ locUid2 = ''; }

				switch(data._depth){
					case "1":
						window.location.href="_config.display.main.php?viewUid="+locUid1;
					break;
					case "2":
					case "3":
						window.location.href="_config.display.main.php?locUid1="+locUid1+"&locUid2="+locUid2;
					break;
				}
			}

		  },error:function(request,status,error){ console.log(request.responseText);}
	  });

	}


	/*
		// --- 선택된  상품 이벤트
	*/

	// --- 페이지 네이트 ---
	$(document).on('click','#config-display-main-form .view-paginate span a',function(){
		var ahref = $(this).attr('href');
		var hasHit = $(this).hasClass('hit');
		$(this).removeAttr("href");// 링크 클릭되지 않도록 href 엘리멘트 제거

		$('.ajax-data-box').attr('data-ahref',ahref);
		if(hasHit == true){ return false; }
		else{
			selectMainProductList();
		}
	});

	// ---  아이템 삭제
	$(document).on('click','#config-display-main-form .select-main-product-delete',function(){
		var _uid = $('[name="formDisplayMain"] [name="_uid"]').val();
		var chkLen = $('#config-display-main-form .main-pcode:checked').length;
		if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }
		var selectVar = $('#config-display-main-form .main-pcode:checked').serialize();

		var url = '_config.display.main.ajax_pro.php';
    $.ajax({
        url: url, cache: false,dataType : 'json', type: "POST",
        data: {_mode : 'selectMainProductDelete' , selectVar : selectVar , _uid : _uid },
        success: function(data){
        	if(data.rst == 'fail'){
        		alert(data.msg);
        		return false;
        	}

        	selectMainProductList();

			var $root = $('html, body');
			$root.animate({
				scrollTop: $('[data-name="view-main"]').offset().top - 10
			}, 500, 'easeInOutCubic');
			return false;

        },error:function(request,status,error){ console.log(request.responseText); }
    });
	});

	//  상품을 가져온다.
	function selectMainProductList()
	{
		var _uid = $('[name="formDisplayMain"] [name="_uid"]').val();
		var _mode = 'selectMainProductList';
		var ahref = $('#config-display-main-form .ajax-data-box').attr('data-ahref');

		var result = $.parseJSON($.ajax({
			url: "_config.display.main.ajax_pro.php",
			type: "get",
			dataType : "json",
			data: {_mode : _mode , ahref : ahref, _uid : _uid},
			async: false
		}).responseText);

		if(result == undefined){ return false; }
		$('#config-display-main-form .js_AllCK').prop('checked',false);
		$('#config-display-main-form .select-main-product-list').html(result.printList);

		// -- 페이지네이트
  		$('#config-display-main-form .view-paginate').html(result.printPaginate);
	}

	// --  상품선택
	function selectMainProductAddpop()
	{
		var _uid = $('[name="formDisplayMain"] [name="_uid"]').val();
		if( _uid == undefined || _uid == '' || _uid == '0' || _uid == 0){ alert('분류를 추가 후 선택 가능합니다.'); return false; }

		window.open('_config.display.main.pop.php?pass_uid='+_uid,'selectMainProductAddpop', 'width=1350, height=800, scrollbars=yes');
	}

	// KAY :: 2021-12-13 :: 상품진열관리 상품순서설정
	// 순위조정 up-down-top-bottom
	function sort_up(pcode,mode,uid) {
		<?php if(pcode && mode){ ?>

			$.ajax({
				url: "_config.display.main.ajax.sort.php",
				cache: false,dataType : 'json', type: "POST",
				data: {_mode:mode,_uid:uid,pcode:pcode },
				success: function(data){
					if(data.rst == 'fail'){
						alert(data.msg);
						return false;
					}
					selectMainProductList();
				},error:function(request,status,error){ console.log(request.responseText); }
			});
		<?php }else{ ?>
			alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
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
			url: "_config.display.main.ajax.sort.php",
			cache: false,dataType : 'json', type: "POST",
			data: {_mode : 'modify_group',_group:group,_uid:_uid,pcode:pcode },
			success: function(data){
				if(data.rst == 'fail'){
					alert(data.msg);
					return false;
				}
				selectMainProductList();
				if(data.msg !=''){
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText); }
		});
	}

</script>





<?php include_once('wrap.footer.php'); ?>