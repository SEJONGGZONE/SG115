<?PHP

	// LMH005

	// 메뉴 지정 변수
	$app_current_link = "_promotion.list.php";
	$app_current_name = "프로모션코드 등록";

	include_once("wrap.header.php");

	$_mode = $_mode ? $_mode : 'add';
	if($_mode == "modify") {
        $row = _MQ(" SELECT * FROM smart_promotion_code WHERE pr_uid='" . $pr_uid . "' ");
	}



?>


	<form name="frm" method="post" action="_promotion.pro.php" enctype="multipart/form-data" >
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>"/>
	<input type="hidden" name="pr_uid" value="<?php echo $pr_uid; ?>"/>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>"/>

		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>사용여부</td>
						<td>
							<?php echo _InputRadio("pr_use", array('N', "Y"), ($row['pr_use']?$row['pr_use']:"Y"), "", array('미사용', "사용") )?>
						</td>
						<th>만료일</td>
						<td>
							<div class="lineup-row type_date">
								<input type="text" name="pr_expire_date" size="15" readonly class="design js_pic_day" placeholder="날짜 선택" value="<?php echo ($row['pr_expire_date'] ? $row['pr_expire_date'] : date("Y-m-d" , strtotime("+ 30 day"))); ?>" style="width:85px">
							</div>
						</td>
					</tr>
					<tr>
						<th>프로모션 코드명</td>
						<td>
							<input type="text" name="pr_name" size="30" class="design" placeholder="프로모션 코드명" value="<?php echo $row['pr_name']; ?>">
							<?php echo _DescStr("코드명은 관리자 참고용으로 사용자에게 노출되지 않습니다."); ?>
						</td>
                        <th>프로모션 코드</td>
                        <td>

                        	<?php if( $_mode == 'add'){?>
                                <div class="lineup-row type_multi">
                                    <input type="text" name="pr_code" size="30" class="design js_pr_code"placeholder="프로모션 코드" value="">
                                    <a href="#none" onclick="return false;" class="c_btn h27 blue line js_pr_code_overlap_check">중복체크</a>
                                    <input type="hidden" name="pr_code_check" class="js_pr_code_check" value="">
                                </div>
                        	<?php }else{?>
                        		<input type="text" name="" size="30" class="design" readonly disabled placeholder="프로모션 코드" value="<?php echo $row['pr_code']; ?>">
                        		<input type="hidden" name="pr_code" value="<?php echo $row['pr_code']; ?>"/>
                        	<?php } ?>
                            

							<?php echo _DescStr("한번 생성한 프로모션코드는 변경할 수 없습니다.","red"); ?>
							<?php echo _DescStr("한글,영문(대소문자 구분없음),숫자,특수문자 등 자유롭게 등록가능합니다.",""); ?>
                        </td>
					</tr>

					<tr>
						<th>최소주문금액</td>
						<td>
                            <div class="lineup-row">
                                <input type="text" name="pr_min_order_price" size="20" class="design number_style" style="width:100px" placeholder="0" value="<?php echo ($row['pr_min_order_price']?$row['pr_min_order_price']:0); ?>"/>
                                <span class="fr_tx">원</span>
                            </div>							
							<?php echo _DescStr("주문시 사용가능한 최소 주문금액을 설정하실 수 있습니다.(총 결제금액 기준)"); ?>

						</td>
						<th>중복사용 설정</td>
						<td>
							<?php echo _InputRadio("pr_due_use", array('N', "Y"), ($row['pr_due_use']?$row['pr_due_use']:"N"), "", array('중복사용 불가능', "중복사용 가능") )?>
							<?php echo _DescStr("중복사용이 불가능할 경우 주문취소되지 않은 주문건을 기준으로 중복사용 여부가 체크됩니다."); ?>
							<?php echo _DescStr("무통장/가상계좌와 같이 입금대기인 주문의 경우 결제완료와 상관없이 체크됩니다."); ?>
						</td>						
					</tr>


					<tr>
                       <th>할인혜택</td>
                       <td colspan="3">
                            <?php echo _InputRadio("pr_type", array('A', "P"), ($row['pr_type']?$row['pr_type']:"A"), "", array('할인액', "할인율") )?>
                            <div class="dash_line"><!-- 점선라인 --></div>
                            <div class="lineup-row">
                                <input type="text" name="pr_amount" size="20" class="design number_style" style="width:100px" placeholder="0" value="<?php echo ($row['pr_amount']?$row['pr_amount']:0); ?>"/>
                                <span class="fr_tx">
                                    <span class="type_print type_P" style="display:none;">%</span>
                                    <span class="type_print type_A">원</span>
                                </span>
                            </div>

							<div class="lineup-row js_price_type_per" style="display: none;">
								<div class="dash_line"><!-- 점선라인 --></div>
								<label class="design"><input type="checkbox" value="Y" name="pr_price_max_use" <?php echo $row['pr_price_max_use'] == 'Y' ? 'checked':''  ?>>최대</label>
								<input type="text" name="pr_price_max" class="design number_style" style="width:100px;" placeholder="0" value="<?php echo $row['pr_price_max']?>" <?php echo $row['pr_price_max_use'] != 'Y' ? 'readonly="readonly"':'' ?> />
								<span class="fr_tx">원까지</span><span class="fr_tx t_blue t_bold">할인</span>
							</div>
                                        
                            <div class="tip_box">
								<?php echo _DescStr("할인액 : 배송비를 포함한 총 결제금액에 적용되며, 결제금액이 할인금액 미만일때는 사용할 수 없습니다."); ?>
								<?php echo _DescStr("할인율 : 배송비를 제외한 상품 총액에만 적용되며, 100%가 초과되면 혜택금액이 높아져 사용할 수 없으니 100이하로 입력해주세요."); ?>
                            </div>
                            <script>
                                var this_type = '';
                                $(document).ready(function(){
                                    this_type = $('input[name=pr_type]:checked').val(); trigger_type();
                                    $('input[name=pr_type]').on('click',function(){ this_type = $('input[name=pr_type]:checked').val(); trigger_type(); });
                                    trigger_price_max_use();
                                });
                                function trigger_type(){
                                    $('.type_print').hide(); $('.type_print.type_'+this_type).show();

                                    $('.js_price_type_per').hide();
                                    if(this_type == 'P'){ $('.js_price_type_per').show(); }
                                }

                                // 최대값 클릭 시
                                $(document).on('click','[name="pr_price_max_use"]',function(){
                                	trigger_price_max_use();
                                });
                                function trigger_price_max_use(){
	                                var pr_price_max_use = $('[name="pr_price_max_use"]').prop('checked');
	                                if( pr_price_max_use == true){ $('[name="pr_price_max"]').removeAttr('readonly'); }
	                                else{ $('[name="pr_price_max"]').attr('readonly','readonly'); }                                	
                                }
                            </script>
                        </td>
					</tr>
					<? if($_mode=='modify') { ?>
						<tr>
							<th>생성일</td>
							<td>
								<?php echo date('Y-m-d (H:i:s)',strtotime($row['pr_rdate'])); ?>
							</td>
							<th>수정일</td>
							<td>
								<? if(rm_str($row[pr_edate])>0) { ?>
								<?php echo date('Y-m-d (H:i:s)',strtotime($row['pr_edate'])); ?>
								<? } ?>
							</td>
						</tr>
					<? } ?>
				</tbody>
			</table>

		</div>

		<?php echo _submitBTN($app_current_link)?>

	</form>

	<script language="javascript">

	// 아이디 중복체크
	$(document).on('click', '.js_pr_code_overlap_check', function(e) {
		e.preventDefault();
		var _pr_code = $('.js_pr_code').val();
		_pr_code = _pr_code.trim();
		$('.js_pr_code').val(_pr_code);
		if(!_pr_code) {
			alert('프로모션 코드를 입력해주세요.');
			$('.js_pr_code').focus();
			return false;
		}
		$.ajax({
			data: {
				_mode: 'pr_code_check',
				_pr_code: _pr_code
			},
			type: 'POST',
			cache: false,
			url: '_promotion.pro.php',
			success: function(data) {

				// 전달된 데이터를 array로 변환
				try { var result = $.parseJSON(data); }
				catch(e) { alert('통신중 에러가 발생하였습니다.'); if(typeof console === 'object') console.log(data); return; }

				if(result['msg']) {
					var msg = result['msg'];
					msg = msg.replace(/\\n/gi, '\n');
				}


				if(result['result'] == 'success') {
					$('.js_pr_code_check').val(1);
					alert(msg);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
				else {
					$('.js_pr_code_check').val('');
					alert(msg);
					$('.js_pr_code').focus();

					// 다시 들어간 포커스를 글자 맨뒤로 밀기 위한 처리
					$('.js_pr_code').val('');
					$('.js_pr_code').val(_pr_code);

					// alert 안내가 있다면
					if(re_alert && re_alert != '') alert(re_alert);
				}
			}
		});
	});

	$(document).ready(function(){
		// -  validate ---
		$("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
			rules: {
				pr_code: { required: true },
				pr_name: { required: false },
				pr_amount: { required: true },
				pr_expire_date: { required: true },

				pr_code_check: { required : true }
			},
			messages: {
				pr_code: { required: "프로모션 코드를 입력하시기 바랍니다." },
				pr_name: { required: "코드명을 입력하시기 바랍니다." },
				pr_amount: { required: "할인율 또는 할인금액을 입력하시기 바랍니다." },
				pr_expire_date: { required: "만료일을 선택하시기 바랍니다." },

				pr_code_check: { required: "프로모션 코드를 중복검사 해주시기 바랍니다." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				var pr_type = $('[name="pr_type"]:checked').val();
				if( pr_type == 'P'){
					var pr_price_max_use_check = $('[name="pr_price_max_use"]').prop('checked');
					var pr_price_max  = $('[name="pr_price_max"]').val()*1;
					if( pr_price_max_use_check == true && pr_price_max < 1){
						$('[name="pr_price_max"]').focus();
						alert("최대 할인 금액을 입력해 주시기 바랍니다.");
						return false;
					}
					
				}

				form.submit();
			}			
		});
		// - validate ---
	});


	</SCRIPT>



<?PHP
	include_once("wrap.footer.php");
?>