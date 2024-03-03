<?php // 디자인추가 2019-09-17 ARA ?>
<style type="text/css">
    .post_popup_section .post_close_btn {position:fixed; left:0; bottom:0; width:100%; text-align:center;  box-sizing:border-box; padding:10px; z-index:50; background:#fff; box-shadow:0 -3px 3px rgba(0,0,0,0.1);}
    .post_popup_section .post_close_btn strong {display:block; color:#fff; height:45px; line-height:45px; font-size:14px; background:#333; }
    .post_popup_section iframe {margin-bottom:65px !important;}
</style>
<div id="find_postcode" class="post_popup_section" style="display:none;border:0;width:100%;">
<a href= "#none" id="btnFoldWrap" onclick="foldDaumPostcode(); return false;" class="post_close_btn" style="" ><strong>닫기</strong></a>
</div>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
// 우편번호 찾기 찾기 화면을 넣을 element
var element_wrap = document.getElementById('find_postcode');

function foldDaumPostcode() {
    // iframe을 넣은 element를 안보이게 한다.
    element_wrap.style.display = 'none';
    // $(".post_hide_section").show();
}

// 도로명주소 우편번호 열기
function post_popup_show() {
    // 현재 scroll 위치를 저장해놓는다.
    var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

    element_wrap.style.height = getCurrentInnerHeight();

    // iframe을 넣은 element를 보이게 한다.
    document.body.appendChild(element_wrap);

    // KAY :: 2022-12-26 :: block 에서 flex 로 변경
    element_wrap.style.display = 'flex';    

    // $(".post_hide_section").hide();
    //document.getElementById("region_name").focus();
    document.body.scrollTop = 0;




    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
            // 시도
            var sido = data.sido;
            // 시군구
            var sigungu = data.sigungu;
            // 읍면
            var bname1 = data.bname1;
            // 동리
            var bname2 = data.bname2;
            // 도로명
            var roadname = data.roadname;
            // 지번주소전체
            var jibunAddress = data.jibunAddress;
            // 도로명주소전체
            var roadAddress = data.roadAddress;

            // 추가배송비 - 기본값설정
            var addprice = $("#default_addprice").val()*1;

            // 추출된데이터전송
            $.ajax({
                url: '_config.delivery_addprice.pro.php',
                data: {'_mode':'ajax_form', 'sido':sido, 'sigungu':sigungu, 'bname1':bname1, 'bname2':bname2, 'roadname':roadname, 'jibunAddress':jibunAddress, 'roadAddress':roadAddress, 'addprice':addprice},
                type: 'post',
                dataType: 'html',
                success: function(data){
                    $('#js_address_data').html(data);
                    $('.js_reset_btn').show();//초기화버튼
                    $('#_addprice').focus();
                }
            });

            // iframe을 넣은 element를 안보이게 한다.
            foldDaumPostcode();
            document.getElementById("_addr2").focus();

            // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
            document.body.scrollTop = currentScroll;

        },

        // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
        onresize : function(size) {

            element_wrap.style.height = getCurrentInnerHeight();
        },
        width : '100%',
        height : '100%'
    }).embed(element_wrap);


    // 아이폰이 아닐경우 스크롤이 두개여서 하나를 지우기 위함
    if( navigator.userAgent.indexOf("iPhone") < 1 ){
        var parent_el = document.getElementById("find_postcode");
        parent_el.lastElementChild.style.overflowY = "hidden";
    }
}

window.addEventListener('resize', function() {
  element_wrap.style.height = getCurrentInnerHeight();
}, true);

// 현재 높이값 구하기.
function getCurrentInnerHeight(){
    var close_btn_el = document.getElementById("btnFoldWrap");

    var cur_height = 0;
    // 아이폰이 아닐경우 닫기 버튼 빼기
    if( navigator.userAgent.indexOf("iPhone") < 1 ){
        cur_height = window.innerHeight - close_btn_el.scrollHeight;
    }else{
        cur_height = window.innerHeight ;
    }

    return cur_height + "px";
}

function new_post_view() {
    post_popup_show();
}
</script>
