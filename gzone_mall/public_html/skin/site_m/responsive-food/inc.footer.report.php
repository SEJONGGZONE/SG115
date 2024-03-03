<?php // 리뷰 신고하기 레이어 ?>
<div class="c_layer type_report js_report_wrap">
</div><!-- end c_layer / type_report -->


<script type="text/javascript">

// 리뷰 신고하기 열고 닫기 : KHY
var authReportPop = true;
 $(document).on('click','.js_btn_report',function(e){ // 버튼에게

    <?php if( is_login() !== true){?>

        if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
        location.href = '/?pn=member.login.form&_rurl=<?php echo urlencode('/?'.$_SERVER['QUERY_STRING']); ?>';
        return;

    <?php }else{ ?>

     var targetClass = '.c_layer.type_report'; // 열리는 요소를 포함한 부모에게
     var addClassName = 'if_open_layer'; // 열리게 될 클래스값
     var chk = $(targetClass).hasClass(addClassName);

     // 열기
     if( chk == false){ 
        var data = $(this).data();
        if( !data.type || !data.uid ){ alert("신고하기 데이터 조회에 실패하였습니다."); return false;  }
        $.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/_pro.php',data:{_mode : 'get_report' , _type : data.type, _uid : data.uid  }, dataType:'json',type:'get'})
        .done(function(e){
            if( e.rst != 'success'){ alert(e.msg); } 
            else{
                $('.js_report_wrap').html(e.content);
                $(targetClass).addClass(addClassName);
            }
        })
        .fail(function(e){
            console.log(e);
            alert("신고하기에 실패하였습니다.");
            return false;
        })
    }
     else {  
        $(targetClass).removeClass(addClassName);  
        $('.js_report_wrap').html('');
    }
    <?php } ?>
 });    


<?php if( is_login() === true){?>
	$(document).on('click','.js_report_wrap .js_submit_ok',function(){
		var $form = $('#formReviewReport');
		var _reason = $form.find('input[name="_reason"]:checked').val();
		var _reason_ect = $form.find('textarea[name="_reason_etc"]').val();
		 var targetClass = '.c_layer.type_report'; // 열리는 요소를 포함한 부모에게
		 var addClassName = 'if_open_layer'; // 열리게 될 클래스값

		if( _reason==undefined){
			alert("신고 이유를 선택해주세요."); return false; 
		}

		if( parseInt(_reason) == 4){
			if( _reason_ect == ''){  alert("신고사유의 자세한 내용을 입력해 주세요!"); return false; }
		}

		$.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/_pro.php',data:$form.serialize(), dataType:'json',type:'post'})
		.done(function(e){
			if(e.msg){  alert(e.msg); }
			$(targetClass).removeClass(addClassName);  
			setTimeout(function(e){
				$('.js_report_wrap').html('');  
			},100);                  
			
		})
		.fail(function(e){
			console.log(e);
			alert("신고하기에 실패하였습니다.");
			return false;
		})    
	});


	// 기타 선택했을경우 내용입력 li 노출
	$(document).on('change','.js_report_wrap input[name="_reason"]',function(){
		var $form = $('#formReviewReport');
		var _reason = $form.find('input[name="_reason"]:checked').val();
		var _reason_ect = $form.find('textarea[name="_reason_etc"]').val();
		if( parseInt(_reason) == 4){
			$form.find('textarea[name="_reason_etc"]').closest('li').show();
		}else{
			$form.find('textarea[name="_reason_etc"]').closest('li').hide();
		}
	});


	// {LCY} : 하이앱 -- 상품후기 회원 차단하기 기능
	$(document).on('click','.js_btn_review_block_user',function(){
		if( confirm("이 회원의 리뷰를 모두 차단하시겠습니까?\n차단 후에는 다시 취소할 수 없습니다.") == false){return false;}
		 var targetClass = '.c_layer.type_report'; // 열리는 요소를 포함한 부모에게
		 var addClassName = 'if_open_layer'; // 열리게 될 클래스값

		var data = $(this).data();
		if( !data.uid){ alert("해당 리뷰 데이터 조회에 실패하였습니다."); return false; }
		$.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/_pro.php', data:{_mode:'block_user', _uid : data.uid } , dataType: 'json', type:'post'})
		.done(function(e){
			if( e.rst == 'success'){ 
				alert("해당 회원의 모든 리뷰는 앞으로 노출 되지 않습니다."); 
			}
			else{ alert(e.msg); }

			$(targetClass).removeClass(addClassName);  
			setTimeout(function(e){
				$('.js_report_wrap').html('');  
			},100);        
			
			try{
				<?php if(in_array($pn, array('product.view')) > 0){?>
				eval_view();
				<?php }else{ ?>
				location.reload();
				<?php } ?>
			}catch(e){}
		})
	});

<?php } ?>

</script>