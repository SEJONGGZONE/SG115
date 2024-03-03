<?php if( $CURR_FILENAME != '_main.php') { ?>


	</div><!-- end section -->
</div><!-- end container -->

<div class="footer">
	<div class="layout_fix">
		© ONEDAYNET.CO.KR ALL RIGHTS RESERVED.
	</div>
    <a href="#none" onclick="return false;" class="fly_gotop js_scroll_stage" title="맨위로"></a>
</div>

<?php } ?>

<script>
    // 맨위로 가는 버튼
    $( document ).ready( function() {
        $( '.fly_gotop' ).click( function() {
            $( 'html, body' ).animate( { scrollTop : 0 }, 400 );
            return false;
        } );
    } );

    // 스크롤 내리면 나타나는 스크립트
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 30) {
            $(".js_scroll_stage").addClass("if_scroll");
        } else {
            $(".js_scroll_stage").removeClass("if_scroll");
        }
    });
</script>

<?php include_once('inc.footer.php'); ?>