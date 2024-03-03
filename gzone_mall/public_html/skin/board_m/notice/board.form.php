<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

    <dl class="form_dl">
        <dt class="form_dt ess"><span class="tit ">제목</span></dt>
        <dd class="form_dd">
            <input type="text" name="_title" class="input_design" autocomplete="off" placeholder="제목을 입력해주세요." value="<?php echo $boardFormData['title'] ?>" />
        </dd>
    </dl>
    <dl class="form_dl">
        <dt class="form_dt ess"><span class="tit ">작성자</span></dt>
        <dd class="form_dd">
            <input type="text" name="_writer" class="input_design" value="<?php echo $boardFormData['writer'];?>" autocomplete="off" placeholder="이름을 입력해주세요."/>
        </dd>
    </dl>

    <?php if($boardFormData['categoryUse']==true && $boardFormData['category']){ // 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정 ?>
    <dl class="form_dl">
        <dt class="form_dt"><span class="tit ">카테고리</span></dt>
        <dd class="form_dd">
            <div class="c_select">
                <?php echo _InputSelect( "_category" , array_values($boardFormData['category']) ,$postInfo['b_category'], '', array_values($boardFormData['category']) , '카테고리 선택'); ?>
                <span class="icon"></span>
            </div>
        </dd>
    </dl>
    <?php }?>

    <?php if( $boardFormData['passwdUse'] === true) {   ?>
    <dl class="form_dl">
        <dt class="form_dt ess"><span class="tit ">비밀글</span></dt>
        <dd class="form_dd">
            <div class="with_btn">
                <input type="password" name="_passwd" class="input_design" placeholder="비밀번호를 입력해주세요." autocomplete="new-password" />
                <?php if( $boardFormData['secretUse'] === true ) { // 비밀글 사용 시 ?>
                    <label class="c_label">
                        <input type="checkbox" name="_secret" value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?>   />
                        <span class="tx"><span class="icon"></span>비밀글</span>
                    </label>
                <?php } ?>
            </div>
            <div class="tip_txt"><?php echo $boardFormData['pwTxt']; ?></div>
        </dd>
    </dl>
    <?php }else{ ?>

    <?php if( $boardFormData['secretUse'] === true ) { // 비밀글 사용 시 ?>
    <dl class="form_dl">
        <dt class="form_dt"><span class="tit ">비밀글</span></dt>
        <dd class="form_dd">
            <label class="c_label">
                <input type="checkbox" name="_secret" value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?> />
                <span class="tx"><span class="icon"></span>비밀글</span>
            </label>
        </dd>
    </dl>
    <?php } ?>
    <?php } ?>

    <?php if( $boardFormData['noticeUse'] === true ) { //  공지글 사용시 :: 관리자 일경우  ?>
    <dl class="form_dl">
        <dt class="form_dt"><span class="tit ">공지글</span></dt>
        <dd class="form_dd">
            <label class="c_label">
                <input type="checkbox" name="_notice" value="Y" <?php echo $boardFormData['noticeChk'] === true ? 'checked':'' ; ?> />
                <span class="tx"><span class="icon"></span>공지사항으로 등록</span>
            </label>
        </dd>
    </dl>
    <?php } ?>

    <?php if( $boardFormData['optionDateUse'] === true) { ?>
    <dl class="form_dl">
        <dt class="form_dt ess"><span class="tit ">기간</span></dt>
        <dd class="form_dd">
            <div class="with_btn">
                <input type="text" name="_sdate" class="input_design type_date js_datepic_min_today" value="<?php echo $boardFormData['sdate']; ?>" readonly placeholder="시작일">
                <span class="unit">~</span>
                <input type="text" name="_edate" class="input_design type_date js_datepic_min_today" value="<?php echo $boardFormData['edate'];?>" readonly placeholder="종료일">
            </div>
        </dd>
    </dl>
    <?php } ?>

    <?php if( $boardFormData['imagesUploadUse'] === true) {  ?>
    <dl class="form_dl">
        <dt class="form_dt ess"><span class="tit ">목록 이미지</span></dt>
        <dd class="form_dd">

            <div class="input_file_box type_photo">
                <input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled=""  placeholder="사진 등록 (<?php echo implode(",",$arrUpfileConfig['ext']['images']); ?>)">
                <div class="fileDiv">
                    <input type="button" class="buttonImg" value="파일찾기">
                    <input type="file" class="realFile" accept="<?php echo 'image/'.implode(", image/",$arrUpfileConfig['ext']['images']); ?>" name="_img1" id="thumb_file" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
                </div>
            </div>

            <div class="photo_preview js_photo_view">
                <img src="<?php echo  $boardFormData['listImage'] ; ?>" style="<?php echo $boardFormData['listImage'] == '' ? 'display: none' : null; ?> " id="img_preview">
            </div>
            <?php if( $boardFormData['listImage'] != '') { ?>
                <?php
                /*
                    <label class="c_label"><input type="checkbox" name="_img1_DEL" value="Y" /><span class="tx"><span class="icon"></span>첨부 이미지삭제</span></label>
                */
                ?>
                <input type="hidden" name="_img1_OLD" value="<?php echo $postInfo['b_img1']; ?>">
            <?php } ?>

        </dd>
    </dl>
    <?php } ?>



	<dl class="form_dl">
		<?php // <dt class="form_dt"><span class="tit ">내용</span></dt> ?>
        <dd class="form_dd">
            <textarea name="_content" rows="10" class="text_design" placeholder=""><?php echo $boardFormData['content']; ?></textarea>
            <div class="tip_txt">글 등록 시 개인정보 입력은 삼가해 주시기 바랍니다.</div>
			<?php if( is_admin() === true){?>
				<div class="tip_txt"><strong>사용자페이지에서 수정할 경우 html이 자동으로 삭제되니 관리자페이지에서 수정해주세요.</strong></div>
			<?php }?>
        </dd>
    </dl>

	 <?php if( $boardFormData['fileUploadUse'] === true) {  ?>
    <dl class="form_dl">
        <dt class="form_dt"><span class="tit ">첨부파일</span></dt>
        <dd class="form_dd">
            <div class="attach_box">
                <?php if( $boardFormData['addFileUse'] === true ) {  ?>
                    <div class="duplicate list-files" data-mode="add">
                        <div class="input_file_box">
                            <input type="text" id="fakeFileTxt1" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="파일 등록">
                            <div class="fileDiv">
                                <input type="button" class="buttonImg" value="파일찾기">
                                <input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById('fakeFileTxt1').value = this.value">
                            </div>
                        </div>
                        <span class="add_btn_box"><a href="#none" onclick="return false;" class="file_btn exec-addfile">+ 추가</a></span>
                    </div>
                <?php } ?>
            </div>

            <?php
            // -- 등록된 파일이 있을경우 처리
            if(count($boardFormData['resFile']) > 0) {
                ?>
                <ul class="upload_list">
                    <?php foreach($boardFormData['resFile'] as $k=>$v){ ?>
                        <li class="upload-file">
                            <a href="<?php echo ''.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].''; ?>" class="txt"><?php echo $v['f_oldname']; ?></a>
                            <label class="c_label"><input type="checkbox" name="modifyFile_DEL[]" value="<?php echo $v['f_uid']; ?>"><span class="tx"><span class="icon"></span>삭제</span></label>
                            <input type="hidden" name="modifyFile_OLD[]" value="<?php echo $v['f_uid'] ?>">
                            <input type="file" name="modifyFile[<?php echo $v['f_uid'] ?>]" style="display: none;">
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <div class="tip_txt">최대 5개까지만 등록 가능합니다.</div>
        </dd>
    </dl>
    <?php } ?>


    <?php if( $boardFormData['recaptchaUse'] === true) { ?>
    <dl class="form_dl tr-recaptcha">
        <dt class="form_dt ess"><span class="tit ">스팸방지</span></dt>
        <dd class="form_dd">
            <?php // 스팸방지 들어감 ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="g-recaptcha"  data-sitekey="<?php echo $boardFormData['recaptchaApi']; ?>"></div>
            <div class="tip_txt">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
        </dd>
    </dl>
    <?php } ?>

