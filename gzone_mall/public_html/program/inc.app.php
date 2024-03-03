<?
include_once(dirname(__FILE__).'/inc.php');
if(is_app() !== true) {  die(json_encode(array('rst'=>'fail','msg'=>'Access Deny!'))); }
switch ($_mode) {
	case 'push_status':
				/*
				// 실행방법
				$(document).on('change', '.js_app_alram_set',function(){
				    var chk = $(this).prop('checked');
				    var pushState = '';
				    if( chk == true){
				        pushState  = 'Y';
				    }else{
				        pushState  = 'N';
				    }
				    location.href="<?php echo $VARAPP['action/url/pushState']; ?>"+pushState;
				});

				function updatePushState(pushState){
				    $.ajax({url:'<?php echo OD_PROGRAM_URL; ?>/inc.app.php',type:'post',dataType:'json',data:{_mode:'push_status',pushState:pushState}})
				    .done(function(e){
				        try{
				            if(e.rst == 'success'){

				            }
				        }catch(e){

				        }
				    }).fail(function(){
				        alert('fail');
				    });
				}
			*/
		if( in_array($pushState, array('Y','N')) < 1){  die(json_encode(array('rst'=>'fail','msg'=>'Access Deny!'))); }
		_MQ_noreturn("update smart_app_user set au_push_send  = '".$pushState."' ,au_push_udate = now() where au_uid = '".$appUserInfo['au_uid']."'  ");	

		 die(json_encode(array('rst'=>'success'))); 
	break;
	
	default:
		// code...
	break;
}
