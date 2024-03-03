    
    // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 
    function dateoption_check(){
        var $form = $('#frmProductView');
        var product_dateoption_use = $form.find('[name="product_dateoption_use"]').val();
        var selDate = $form.find('[name="selDate"]').val();
        if( product_dateoption_use == 'Y'){
            if( !selDate ){
                alert("달력에서 날짜를 먼저 선택해 주세요.");
                return false;
            }
        }
        return true;
    }


    // - 옵션 선택 체크 ---
    function option_select(depth,code) {


        var depth_next = depth * 1 + 1;
        var select_var = $("#option_select"+depth+"_id").val();


        // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 
        if( dateoption_check() !== true){ return false; }
        var selDate = $('#frmProductView').find('[name="selDate"]').val();
        if(typeof selDate == 'undefined' || !selDate){ selDate = ''; }

        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            code : code,
            depth : depth,
            uid : select_var,
            selDate : selDate,
        }
        var app_var = "";
        if( $("input, select").is("#option_select1_id") == true ){
            // app_var = "&uid1=" + $("#option_select1_id").val() ;
            ajaxData.uid1 = $("#option_select1_id").val();
        }

        // 마지막 옵션인 3차는 초기화 한다. 
        if( $("input, select").is("#option_select3_id") == true ){
            var $opt3_title = $('#span_option3 .this_opt_tit').clone();
            $('#span_option3').html($opt3_title);
        }

        if(select_var) {
            $.ajax({
                url: "/program/option_select.php",
                cache: false,
                type: "POST",
                data: ajaxData,
                success: function(data){

                    // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 
                    if( data == 'error_date'){
                        alert("달력에서 날짜를 먼저 선택해 주세요.");
                        return false;
                    }

                    $("#span_option" + depth_next ).html(data).removeClass('before');
                    //select_box_reload();    // hover를 동작하게 하기 위해 다시한번 호출
                }
            });
        }
    }
    // - 옵션 선택 체크 ---


    // - 옵션 추가 ---
    function option_select_add(code) {
        var _trigger = true;
        if( $("input, select").is("#option_select1_id") == true && !($("#option_select1_id").val() != "") ){
            _trigger = false;
        }
        if( $("input, select").is("#option_select2_id") == true && !($("#option_select2_id").val() != "") ){
            _trigger = false;
        }
        if( $("input, select").is("#option_select3_id") == true && !($("#option_select3_id").val() != "") ){
            _trigger = false;
        }

        // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 
        if( dateoption_check() !== true){ return false; }
        var selDate = $('#frmProductView').find('[name="selDate"]').val();
        if(typeof selDate == 'undefined' || !selDate){ selDate = ''; }        


        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            code : code,
            uid1 : $("#option_select1_id").val(),
            uid2 : $("#option_select2_id").val(),
            uid3 : $("#option_select3_id").val(),
            add_uid1 : $("#add_option_select_1_id").val(),
            add_uid2 : $("#add_option_select_2_id").val(),
            add_uid3 : $("#add_option_select_3_id").val(),
            add_uid4 : $("#add_option_select_4_id").val(),
            add_uid5 : $("#add_option_select_5_id").val(),
            add_uid6 : $("#add_option_select_6_id").val(),
            add_uid7 : $("#add_option_select_7_id").val(),
            add_uid8 : $("#add_option_select_8_id").val(),
            add_uid9 : $("#add_option_select_9_id").val(),
            add_uid10 : $("#add_option_select_10_id").val(),
            add_uid10 : $("#add_option_select_10_id").val(),
            selDate : selDate,
        }


        if( _trigger ) {
            $.ajax({
                url: "/program/option_select_add.php",
                cache: false,
                type: "POST",
                data: ajaxData,
                success: function(data){
                    if(data == "error1") {
                        alert('잘못된 접근입니다.');
                    }
                    else if(data == "error2") {
                        alert('이미 선택한 옵션입니다.');
                    }
                    else if(data == "error3") {
                        alert('선택 옵션의 재고량이 부족합니다.');
                    }
                    else if(data == "error4") {
                        alert('재고량이 부족합니다.');
                    }

                    // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 {
                    else if( data == 'error_date'){
                        alert("달력에서 날짜를 먼저 선택해 주세요.");
                    }  
                    // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 }

                    else {
						$("#span_seleced_list").show();
                        $("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                    }
                }
            });
        }
        else {
            alert('옵션을 선택해 주세요');
        }
    }
    // - 옵션 추가 ---

    // - 옵션 삭제 ---
    function option_select_del(uid,code) {

        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            code : code,
            uid1 : uid,
        }

        $.ajax({
            url: "/program/option_select_del.php",
            cache: false,
            type: "POST",
            data: ajaxData,
            success: function(data){
                if(data == "error1") {
                    alert('잘못된 접근입니다.');
                }
                else if(data == "error4") {
                    alert('재고량이 부족합니다.');
                }
                else {
					$("#span_seleced_list").show();
					$("#span_seleced_list").html(data);
					if($("#option_select_expricesum").val()<='0'){
						$("#span_seleced_list").hide()
					}

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                }
            }
        });
    }
    // - 옵션 삭제 ---

    // - 옵션 수량수정 ::: _type : up/down ---
    function option_select_update(_type , uid,code) {



        // LCY : 2022-12-21 : 티켓기능 -- 달력옵션 체크 
        if( dateoption_check() !== true){ return false; }
        var selDate = $('#frmProductView').find('[name="selDate"]').val();
        if(typeof selDate == 'undefined' || !selDate){ selDate = ''; }        


        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            _type : _type,
            code : code,
            uid : uid,
            cnt : $("#input_cnt_" + uid).val(),
            selDate : selDate,
        }

        $.ajax({
            url: "/program/option_select_update.php",
            cache: false,
            type: "POST",
            data: ajaxData,
            success: function(data){
                if(data == "error1") {
                    alert('잘못된 접근입니다.');
                }
                else if(data == "error3") {
                    alert('선택 옵션의 재고량이 부족합니다.');
                }
                else if(data == "error4") {
                    alert('재고량이 부족합니다.');
                }   

                // 최대구매개수제한 {
                else if(data == "error5") {
                    var max_cnt = $('#frmProductView [name="product_buy_limit"]').val();
                    max_cnt = parseInt(max_cnt);
                    alert('최대 '+max_cnt+'개까지 구매 가능합니다.');
                }                  
                // 최대구매개수제한 }     

                else {
					$("#span_seleced_list").show();
                    $("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                }
            }
        });
    }
    // - 옵션 수량수정 ---




	// - 추가옵션 추가 ---
	function add_option_select_add(code , pao_uid, uid,depth) {


        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            code : code,
            uid : pao_uid,
        }

		if( pao_uid ) {
			$.ajax({
				url: "/program/add_option_select_add.php",
				cache: false,
				type: "POST",
				data: ajaxData,
				success: function(data){
					if(data == "error1") {
						alert('잘못된 접근입니다.');
					}
					else if(data == "error2") {
						alert('이미 선택한 옵션입니다.');
					}
					else if(data == "error3") {
						alert('선택 옵션의 재고량이 부족합니다.');
					}
					else if(data == "error4") {
						alert('재고량이 부족합니다.');
					}
					else if(data == "error6") {
						alert('필수옵션을 먼저 선택해 주시기 바랍니다.');
					}
					else {
						$("#span_seleced_list").show();
						$("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());

						// 기본형태 맞춤
						if(uid) $("#add_option_select_"+uid+"_id").val(pao_uid);
						if(uid) $("#add_option_select_"+uid+"_box").hide();
						setTimeout(function(){ $('#add_option_select_'+uid+'_box').attr({'style':''}); }, 100);
						$('.js_box_optdropbox_add[data-idx='+depth+']').removeClass('if_open_opt');
					}
				}
			});
		}
	}
	// - 추가옵션 추가 ---

	// - 추가옵션 수량수정 ::: _type : up/down ---
	function add_option_select_update(_type , uid, code) {

        // LCY : 2022-12-21 : 티켓기능 -- ajax 데이터 기능개선
        var ajaxData = {
            code : code,
            _type : _type,
            uid : uid,
            cnt : $("#input_cnt_add_" + uid).val(),
        }

		$.ajax({
			url: "/program/add_option_select_update.php",
			cache: false,
			type: "POST",
			data: ajaxData,
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error3") {
					alert('선택 옵션의 재고량이 부족합니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else if(data == "error5") {
					alert('구매제한 개수가 초과 되었습니다.');
				}
				else {
					$("#span_seleced_list").show();
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 추가옵션 수량수정 ---