
    var time_sale_timmer_var = {};
    function time_sale_timmer_set(pcode)
    {

        var el = '.js_time_sale_timmer[data-pcode='+pcode+']';
        var data = $(el).data();
        if( typeof data != 'object'){ return true;}
        time_sale_timmer_view(data);
    }

    function time_sale_timmer_view(data)
    {
        var setData = {};
        setData.h = data.h*1;
        setData.i = data.i*1;
        setData.s = data.s*1;
		setData.d = data.d*1;
        setData.next = data.next*1;
        setData.chk =  data.chk*1;
		setData.pcode = data.pcode;

        time_sale_timmer_var[data.pcode] = setInterval(function(){
            setData.s--;
            setData.chk--;
            if( setData.s < 0){
                setData.s = 59;
                setData.i--;
                if( setData.i < 0){
                    setData.h --;
                    if( setData.h < 0){ setData.h =0; setData.i =0; }
                    else{
                        setData.i = 59;
                    }
                }
            }

            var setDataView = {};

            if( setData.d <= 0){
                setDataView.d  = '';
				setData.d = 0;
            }else{
                setDataView.d = String(setData.d);
				setDataView.h  = (setDataView.d*12);
			}

            if( setData.h <= 0){
                setDataView.h = '00';
                setData.h = 0;
            }else{
                setDataView.h = String(setData.h);
				setDataView.h = setDataView.h<10?'0'+setDataView.h:setDataView.h;
            }
            if( setData.i <= 0){
                setDataView.i = '00';
                setData.i = 0;
            }else{
                setDataView.i = String(setData.i);
				setDataView.i = setDataView.i<10?'0'+setDataView.i:setDataView.i;
            }

            if( setData.s <= 0){
                setDataView.s = '00';
                setData.s = 0;
            }else{
                setDataView.s = String(setData.s);
				setDataView.s = setDataView.s<10?'0'+setDataView.s:setDataView.s;
            }




			var time_text = setDataView.h+':'+setDataView.i+':'+setDataView.s;
			var result = time_text.replace(/(\n|\r\n)/g, '<br/>');



			if(result == '00:00:00'){

				clearInterval(time_sale_timmer_var[data.pcode]) ;// 시간 종료
				var time_end_text = '<em class="last">종료된 타임세일</em>';

				// 상태변경
				$.ajax({
					data: {_mode:'time_sale_view_modify' , pcode:data.pcode}, type: 'POST', dataType: 'JSON', cache: false, url: '/program/_pro.php',
					success: function(data) {
						if(data.rst == 'success') {
							$('.js_time_sale_timmer[data-pcode="'+setData.pcode+'"] .js_timer').remove();
                            $('.js_time_sale_timmer[data-pcode="'+setData.pcode+'"]').each(function(si,sv){
                                $(sv).find('.last').remove();
                                $(sv).find('.js_time_sale_icon').after(time_end_text);
                            });
							setDataView.s = setDataView.i = setDataView.h = 0;
						}else{
							alert(data.msg); return false;
						}
					},
					error:function(request,status,error){ alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error); }
				});

			}
            else{

                $('.js_time_sale_timmer[data-pcode="'+setData.pcode+'"] .js_time_sale_timer_h').text(setDataView.h);
                $('.js_time_sale_timmer[data-pcode="'+setData.pcode+'"] .js_time_sale_timer_m').text(setDataView.i);
                $('.js_time_sale_timmer[data-pcode="'+setData.pcode+'"] .js_time_sale_timer_s').text(setDataView.s);

            }

        },1000);

    }