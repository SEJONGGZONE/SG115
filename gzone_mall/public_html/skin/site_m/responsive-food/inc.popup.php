<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
foreach($res as $pk=>$pv) {
    if($_COOKIE['AuthPopupClose_'.$pv['p_uid']] == 'Y') continue; // 오늘 하루 보이지 않음으로 체크된 팝업은 제외
    ?>
    <?php
    /*
        - 이미지형/에디터형 선택가능하게
        - 위치를 설정값 빼기
        - pc, 모바일 노출 구분없고 같이 설정
        - 팝업이 여러개 일때 팝업 닫으면 그다음 팝업이 뜨는 식(여러개가 한꺼번에 뜨는게 아니라 1개씩 뜸)
    */
    ?>
    <div class="c_popup js_popup_<?php echo $pv['p_uid']; ?> js_box_timeout">
        <div class="pop_wrap">
            <?php
            if($pv['p_mode'] == 'I') { // 이미지 모드
                $_img = IMG_DIR_POPUP_URL.$pv['p_img'];
                ?>
                <?php // 이미지형 (다른설정값 없음) ?>
                <div class="img_box">
                    <?php if($pv['p_link']) { ?><a href="<?php echo $pv['p_link']; ?>" target="<?php echo $pv['p_target']; ?>"><?php } ?>
                        <img src="<?php echo $_img; ?>" alt="<?php echo addslashes(htmlspecialchars($pv['p_title'])); ?>" />
                    <?php if($pv['p_link']) { ?></a><?php } ?>
                </div>
            <?php } else { ?>
                <?php // 에디터형 : 배경색상,팝업창 크기 관리자에서 지정; 기본값 350 * 250 으로 지정해서 0이 되지 않도록 ?>
                <div class="editor_box" style="background:<?php echo '#'.str_replace('#', '', ($pv['p_bgcolor']?$pv['p_bgcolor']:'#FFFFFF')); ?>;">
                    <div class="editor">
                        <?php echo $pv['p_content']; ?>
                    </div>
                </div>
            <?php } ?>

            <div class="close_box">
                <ul>
                    <li>
                        <label class="btn label">
                            <input type="checkbox" class="js_popup_today_close js_btn_timeout" data-uid="<?php echo $pv['p_uid']; ?>" value="Y" />
                            <span class="tt">오늘하루 그만보기</span>
                        </label>
                    </li>
                    <li>
                        <a href="#none" data-uid="<?php echo $pv['p_uid']; ?>" class="btn btn_close js_popup_close js_btn_timeout"><span class="tt">창닫기</span></a>
                    </li>
                </ul>
            </div>
        </div><!-- end pop_wrap -->
        <div class="c_popup_bg js_popup_close js_btn_timeout" data-uid="<?php echo $pv['p_uid']; ?>" onclick="return false;"></div>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).on('click', '.js_popup_today_close', function(e) {
        e.preventDefault();
        var _uid = $(this).data('uid');
        $.ajax({
            data: {
                _mode: 'popup_close',
                uid: _uid
            },
            type: 'POST',
            cache: false,
            url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
            success: function(data) {
                //if(data == 'error') return alert('알수없는 이유로 팝업을 닫지 못했습니다.');
                $('.js_popup_'+_uid).remove();
            }
        });
    });
    $(document).on('click', '.js_popup_close', function(e) {
        e.preventDefault();
        var _uid = $(this).data('uid');
        $('.js_popup_'+_uid).remove();
    });

</script>
