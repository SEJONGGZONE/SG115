		<div class="js_dialog" style="display: none"><!-- 다이얼로그 기본폼:: 삭제금지 --></div>

		<div class="js_preview_image_popup popup" style="display: none;">
			<div class="pop_title">
				<strong>이미지 미리보기</strong>
				<a href="#none" class="btn_close close" title="닫기"></a>
			</div>
			<div class="data_list pop_conts preview_img"></div>
		</div>


	</div><!-- end wrap -->

	<iframe name="common_frame" width="0" height="0" frameborder="0" style="display:none;"></iframe>
	<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/common.footer.js"></script>
	<script>
	// 연관되지 않는 요소 열고닫기
	$(document).on('click','.js_onoff_event',function(e){
		var data = $(this).data();
		var targetClass = data.target;
		var addClass = data.add;
		var chk = $(targetClass).hasClass(addClass);
		if( chk == true){ // 이미 있다면
			$(targetClass).removeClass(addClass);
		}else{
			$(targetClass).addClass(addClass);
		}
	});
	// 연관되지 않는 요소 열고닫기 (라벨)
    $(document).on('change','.js_onoff_label',function(e){
        var data = $(this).data();
        var targetClass = data.target;
        var addClass = data.add;
        var chk = $(targetClass).hasClass(addClass);
        if( chk == true){ // 이미 있다면
            $(targetClass).removeClass(addClass);
        }else{
            $(targetClass).addClass(addClass);
        }
    });
</script>

	<?php //DeveModeFooter('#880015', '#ffffff', 'top:187px; font-weight:bold; left:250px'); ?>
	<?php DeveModeFooterDetail(); ?>
</body>
</html>