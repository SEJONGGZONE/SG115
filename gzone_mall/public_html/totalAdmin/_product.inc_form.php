<?php
	/*
		- 개별실행불가파일, 상품공통화 파일
		- _product.form.php, _product_ticket.form.php 에서 사용
		- $app_product_type 변수를 통해 분리 (delivery, ticket)
	*/
?>


<div class="group_title"><strong class="<?php echo $app_product_type == 'delivery' ? 't_green':'t_purple' ?>"><?php echo $arr_product_type_name[$app_product_type]; ?> 기본설정</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<?php if($AdminPath == 'totalAdmin' || ($AdminPath == 'subAdmin' && $_mode == "modify") ) {?>
			<tr>
				<th class="">상품 노출</th>
				<td colspan="3">
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<?php echo _InputRadio( '_view' , array('Y','N'), ($row['p_view'] ? $row['p_view'] : 'N') , '' , array('노출','숨김') , ''); ?>
					<?php }else{ ?>
						<?php echo str_replace(array("", ''), '', $arr_adm_button[($row['p_view'] == "Y" ? "노출" : "숨김")]); ?>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th class="">상품코드</th>
				<td>
					<div class="lineup-row type_multi">
						<input type="text" name="" class="design" value="<?php echo $_code; ?>" placeholder="상품코드" style="width:200px" readonly="readonly">
						<?php if($_mode != 'add') { ?>
							<a href="#none" onclick="window.open('/?pn=product.view&pcode=<?php echo $_code; ?>'); return false;" class="c_btn sky">미리보기</a>
						<?php } ?>
					</div>
					<?php echo _DescStr("최초 상품 등록 시 자동으로 지정되며, 수정이 불가합니다."); ?>
					<?php echo _DescStr("상품 고유번호로 사용되며, 주문/배송 등 검색 시 정확하게 검색이 가능합니다."); ?>
				</td>
				<th>순위</th>
				<td>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
					<div class="lineup-row">
						<input type="text" name="_sort_group" class="design number_style" value="<?php echo $row['p_sort_group']; ?>" placeholder="0" style="width:70px">
						<input type="hidden" name="_sort_idx" class="design" value="<?php echo $row['p_sort_idx']; ?>" >
						<input type="hidden" name="_idx" class="design" value="<?php echo $row['p_idx']; ?>" >
						<span class="fr_tx">위</span>
					</div>
					<?php }else{ ?>
						<div class="lineup-row">
							<input type="text" name="_sort_group" class="design number_style" value="<?php echo $row['p_sort_group']; ?>" placeholder="0" style="width:70px" disabled>
							<input type="hidden" name="_sort_idx" class="design" value="<?php echo $row['p_sort_idx']; ?>" >
							<input type="hidden" name="_idx" class="design" value="<?php echo $row['p_idx']; ?>" >
							<span class="fr_tx">위</span>
						</div>
					<?php } ?>
					<?php echo _DescStr("상품목록에서 추천순이며, 낮은 숫자가 먼저 노출됩니다. (숫자 1이상 입력)"); ?>
					<?php echo _DescStr("숫자가 같은 상품이 있을 경우 최신 등록한 상품이 먼저 노출됩니다."); ?>
				</td>
			</tr>
			<?php } ?>

			<tr>
				<th class="ess">상품명</th>
				<td colspan="3">
					<input type="text" name="_name" class="design" value="<?php echo $row['p_name']; ?>" placeholder="상품명" style="width:500px">
				</td>
			</tr>
			<tr>
				<th class="">부가 상품명</th>
				<td colspan="3">
					<textarea type="text" name="_subname" class="design" placeholder="상품설명" rows="3"><?php echo $row['p_subname']; ?></textarea>
					<?php echo _DescStr("상품상세페이지 설정에서 설명문구를 숨김처리 할 수 있습니다. <a href='/totalAdmin/_config.display.pinfo.php' target='_blank'><em>노출설정 바로가기</em></a>"); ?>
				</td>
			</tr>

			<?php  if( $AdminPath == 'totalAdmin'){ ?>
			<?php }else{ ?>
			<tr>
				<th class="t_red">참고사항</th>
				<td colspan="3">
					<?php echo _DescStr('노출설정, 순위, 적립율, 판매량은 통합관리자만 수정이 가능하니, 관리자에게 문의바랍니다.','red'); ?>
				</td>
			</tr>
			<?php } ?>
			<?php if($_mode == 'modify'){ ?>
			<tr>
				<th>등록일</th>
				<td><?php echo date('Y-m-d', strtotime($row['p_rdate'])); ?> <span class="t_light"><?php echo date('(H:i:s)', strtotime($row['p_rdate'])); ?></span></td>
				<th>최종 수정일</th>
				<td><?php echo date('Y-m-d', strtotime($row['p_mdate'])); ?> <span class="t_light"><?php echo date('(H:i:s)', strtotime($row['p_mdate'])); ?></span></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>


<div class="group_title"><strong>가격 및 옵션</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th class="ess">상품가격</th>
				<td>
					<div class="lineup-row">
						<span class="fr_tx t_blue t_bold">정상가</span>
						<input type="text" name="_screenPrice" class="design number_style t_right" value="<?php echo $row["p_screenPrice"]; ?>" placeholder="0" style="width:100px">
						<span class="fr_tx">원</span>
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx t_red t_bold">판매가</span>
						<input type="text" name="_price" class="design number_style t_right" value="<?php echo $row["p_price"]; ?>" placeholder="0" style="width:100px">
						<span class="fr_tx">원</span>
						<input type="text" class="design number_style t_right js_percent_view" value="<?php echo DCPer($row['p_screenPrice'], $row['p_price']); ?>" placeholder="0" style="width:70px" disabled>
						<span class="fr_tx">% 할인</span>
					</div>
					<div class="tip_box">
						<?php echo _DescStr('할인이 없을 경우 정상가에 0을 입력하거나 판매가와 동일하게 입력해주세요.'); ?>
						<?php echo _DescStr('정상가가 판매가와 동일하여 할인율이 0이거나, 정상가가 판매가보다 낮으면 할인율은 노출되지 않습니다.'); ?>
						<?php echo _DescStr('할인율은 소수점 반올림 계산되며 소수점 이하인 경우에도 노출되지 않습니다.'); ?>
					</div>
				</td>
				<th>적립율</th>
				<td>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
					<div class="lineup-row">
						<input type="text" name="_point_per" class="design number_style" data-decimals="1" value="<?php echo $row['p_point_per']; ?>" placeholder="0" style="width:70px">
						<span class="fr_tx">%</span>
						<input type="text" class="design number_style js_point_per_ext" value="<?php echo $point_per_ext  ?>" placeholder="0" style="width:100px" disabled>
						<span class="fr_tx">원 적립예상</span>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="lineup-row">
						<span class="fr_tx t_sky t_bold">결제완료 <?php echo $siteInfo['s_orderpointprodate']; ?>일 후 적립</span>
						<a href="/totalAdmin/_config.point.form.php" class="c_btn sky line" target="_blank">적립일 설정 바로가기</a>
					</div>
					<?php }else{ ?>
						<div class="lineup-row">
							<input type="text" name="_point_per" class="design number_style" data-decimals="1" value="<?php echo $row['p_point_per']; ?>" placeholder="0" style="width:70px" disabled>
							<span class="fr_tx">%</span>
							<input type="text" class="design number_style js_point_per_ext" value="<?php echo $point_per_ext  ?>" placeholder="0" style="width:100px" disabled>
							<span class="fr_tx">원 적립예상</span>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>
					<?php } ?>
					<?php echo _DescStr('소수점 1자리까지 설정 가능하며, 소수점 2자리까지 입력 시 자동으로 반올림 됩니다.'); ?>
					<?php echo _DescStr('쿠폰 및 적립금등의 할인적용과는 상관없이 판매가를 기준으로 계산되며 1원 미만은 버림처리됩니다.'); ?>
					<?php echo _DescStr('옵션 사용시에는 옵션 판매가로 적립되니 예상적립금은 참고용으로만 확인해주세요.','blue'); ?>
				</td>
			</tr>
			<tr>
				<th>재고량</th>
				<td>
                    <div class="lineup-row">
					    <input type="text" name="_stock" class="design number_style" value="<?php echo $row['p_stock']; ?>" placeholder="0" style="width:70px">
                        <span class="fr_tx">개</span>
                    </div>
					<?php echo _DescStr('재고량이 0인 경우 품절로 표시되며, 옵션 사용시 옵션 재고량을 우선합니다.'); ?>
					<?php echo _DescStr("상품상세페이지 설정에서 재고량을 숨김처리 할 수 있습니다. <a href='/totalAdmin/_config.display.pinfo.php' target='_blank'><em>노출설정 바로가기</em></a>"); ?>
				</td>
				<th>판매량</th>
				<td>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
                    <div class="lineup-row">
					    <input type="text" name="_salecnt" class="design number_style" value="<?php echo $row['p_salecnt']; ?>" placeholder="0" style="width:70px">
                        <span class="fr_tx">개</span>
                    </div>
					<?php }else{ ?>
						 <div class="lineup-row">
							<input type="text" name="_salecnt" class="design number_style" value="<?php echo $row['p_salecnt']; ?>" placeholder="0" style="width:70px" disabled>
							<span class="fr_tx">개</span>
						</div>
					<?php } ?>
					<?php echo _DescStr("주문이 완료된 건수로서 상품목록에서 인기순으로 정렬할때 사용되며, 높은수록 먼저 노출됩니다."); ?>
				</td>
			</tr>
			<tr>
				<th>옵션사용</th>
				<td colspan="3">
					<?php echo _InputRadio( "_option_type_chk" , array('nooption','1depth','2depth','3depth'), ($row['p_option_type_chk'] ? $row['p_option_type_chk'] : "nooption") , " class='_option_type_chk' " , array('미사용','1차 옵션','2차 옵션','3차 옵션') , '');?>

					<div class="in_option_list option_type" style="<?=(in_array($row['p_option_type_chk'] , array('1depth','2depth','3depth')) ? '' : 'display:none;')?>">
						<dl class="init_depth1_type init_depth_type">
							<dt>1차 옵션</dt>
							<dd>
								<?php echo _InputRadio('p_option1_type', array('normal' , 'color', 'size'), ($row['p_option1_type']?$row['p_option1_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
								<input type="text" name="p_option1_title" class="design" value="<?php echo $row['p_option1_title']; ?>" placeholder="1차 옵션 타이틀" style="width:500px">
							</dd>
						</dl>

						<dl class="init_depth2_type init_depth_type">
							<dt>2차 옵션</dt>
							<dd>
								<?php echo _InputRadio('p_option2_type', array('normal' , 'color', 'size'), ($row['p_option2_type']?$row['p_option2_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
								<input type="text" name="p_option2_title" class="design" value="<?php echo $row['p_option2_title']; ?>" placeholder="2차 옵션 타이틀" style="width:500px">
							</dd>
						</dl>

						<dl class="init_depth3_type init_depth_type">
							<dt>3차 옵션</dt>
							<dd>
								<?php echo _InputRadio('p_option3_type', array('normal' , 'color', 'size'), ($row['p_option3_type']?$row['p_option3_type']:'normal'), ' class="btn_hide_input " ', array('일반' , '컬러', '사이즈')); ?>
								<input type="text" name="p_option3_title" class="design" value="<?php echo $row['p_option3_title']; ?>" placeholder="3차 옵션 타이틀" style="width:500px">
							</dd>
						</dl>
					</div>

				</td>
			</tr>
			<tr>
				<th>옵션등록</th>
				<td>
					<div class="lineup-row">
						<a href="#none" onclick="option_popup('<?php echo $_code; ?>'); return false;" class="c_btn h27 blue">필수옵션 설정하기</a>

						<?php if( $app_product_type == 'delivery'){?>
						<a href="#none" onclick="addoption_popup('<?php echo $_code; ?>');" class="c_btn h27 blue line">추가옵션 설정하기</a>
						<?php } ?>
					</div>
					<?php if( $app_product_type == 'delivery'){?>
						<?php echo _DescStr('필수옵션을 사용할 경우에만 추가옵션을 사용할 수 있습니다.',''); ?>
					<?php }else{ ?>
						<?php echo _DescStr('티켓은 상품의 특성상 추가옵션을 사용할 수 없습니다.',''); ?>
					<?php } ?>
				</td>
				<th>자주쓰는 옵션</th>
				<td>
					<div class="lineup-row">
						<a href="#none" onclick="common_option_popup('<?php echo $_code; ?>'); return false;" class="c_btn sky">자주쓰는 옵션 선택</a>
						<a href="/totalAdmin/_product.common_option_set.list.php" class="c_btn sky line" target="_blank">자주쓰는 옵션관리 바로가기</a>
					</div>
					<?php echo _DescStr('미리 등록해둔 옵션을 선택하면 편리하게 옵션추가가 가능합니다.',''); ?>
				</td>
			</tr>


			<?php if( $app_product_type == 'ticket'){?>
			<tr>
				<th>달력옵션</th>
				<td colspan="3">
					<div class="lineup-row"><label class="design"><input type="checkbox" name="_dateoption_use" value="Y" <?php echo $row['p_dateoption_use'] == 'Y' ? 'checked' :NULL ?>>달력옵션 사용</label></div>
					<div class="lineup-row js_dateoption_useY"<?php echo $row['p_dateoption_use'] != 'Y' ? ' style="display:none;"':null ?>>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _InputRadio( "_dateoption_date_type" , array('day','date'), !$row['p_dateoption_date_type'] ? 'day':$row['p_dateoption_date_type'] , "" , array('기준일자 지정','특정날짜 지정') , '');?>
						<?php echo _DescStr('기준일자 지정 : 오늘을 기준으로 일수를 지정하여 설정합니다.'); ?>
						<?php echo _DescStr('특정날짜 지정 : 특정한 날짜를 직접 입력하여 설정합니다.'); ?>
					</div>

					<div class="in_option_list js_dateoption_useY">
						<dl class="js_dateoption_date_type if_day">
							<dt>선택기간</dt>
							<dd>
								<div class="lineup-row">
									<span class="fr_tx">오늘기준</span>
									<input type="text" name="_dateoption_sday" class="design number_style" value="<?php echo $row["p_dateoption_sday"]; ?>" placeholder="0" style="width:60px">
									<span class="fr_tx">일 ~</span>
									<input type="text" name="_dateoption_eday" class="design number_style" value="<?php echo $row["p_dateoption_eday"]; ?>" placeholder="0" style="width:60px">
									<span class="fr_tx">일까지</span>
								</div>
								<?php echo _DescStr('시작일에 0을 입력 시 당일부터 가능하고, 선택가능 종료일에 0을 입력 시 종료일에 제한이 없습니다.'); ?>
							</dd>
						</dl>
						<dl class="js_dateoption_date_type if_date">
							<dt>선택날짜</dt>
							<dd>
								<div class="lineup-row type_date">
									<input type="text" name="_dateoption_sdate" class="design js_pic_day" value="<?php echo rm_str($row["p_dateoption_sdate"]) < 1 ? date('Y-m-d') : $row["p_dateoption_sdate"]; ?>" placeholder="날짜 선택" style="width:85px">
									<span class="fr_tx">-</span>
									<input type="text" name="_dateoption_edate" class="design js_pic_day" value="<?php echo rm_str($row["p_dateoption_edate"]) < 1 ? date('Y-m-d',strtotime("+1 years")) : $row["p_dateoption_edate"]; ?>" placeholder="날짜 선택" style="width:85px">
								</div>
								<?php echo _DescStr('설정된 범위내에서만 달력에서 선택가능합니다.'); ?>
							</dd>
						</dl>
						<dl>
							<dt>당일구매 가능시간</dt>
							<dd>
								<div class="lineup-row type_date">
									<input type="time" name="_dateoption_stime" class="design type_time"  value="<?php echo date('H:i',strtotime($row['p_dateoption_stime']));?>" placeholder="시간 선택" style="width:100px" autocomplete=off>
									<span class="fr_tx">~</span>
									<input type="time" name="_dateoption_etime" class="design type_time"  value="<?php echo date('H:i',strtotime($row['p_dateoption_etime']));?>" placeholder="시간 선택" style="width:100px" autocomplete=off>
								</div>
								<?php echo _DescStr('당일 구매가 가능할 경우 구매가능 시간을 설정합니다.'); ?>
							</dd>
						</dl>
						<dl>
							<dt>구매<br/>불가요일</dt>
							<dd>
								<?php
									$arr_set_date_week = $row['p_dateoption_ex_week'] ?  explode(",",$row['p_dateoption_ex_week']) : array();
									foreach($arr_date_var['date_week2'] as $k=>$v){

										$yoil_code = $k+1;
										echo '
										<label class="design">
											<input type="checkbox" name="_dateoption_ex_week[]" value="'.$yoil_code.'" '.(in_array($yoil_code, $arr_set_date_week) ==  true ? 'checked':null).'>'.$v.'
										</label>';
									}
								?>
								<?php echo _DescStr('불가 요일을 설정하면 해당 요일에는 모두 구매가 불가합니다.'); ?>
							</dd>
						</dl>
						<dl>
							<dt>구매<br/>불가날짜</dt>
							<dd>
								<textarea name="_dateoption_ex_date" class="design selector " placeholder="날짜별 선택불가 설정" rows="4" readonly ><?=stripslashes($row['p_dateoption_ex_date'])?></textarea>
								<?php // 1개라도 입력했을때만 노출 ?>
								<div class="dash_line"><!-- 점선라인 --></div>
								<a href="#none" class="c_btn black line js_dateoption_ex_date_reset" onclick="return false;">설정날짜 초기화</a>

								<?php echo _DescStr('상품의 날짜별 선택 불가일을 설정가능합니다.'); ?>
								<?php echo _DescStr('설정화면 클릭 시 노출되는 달력에서 날짜를 선택하시면 자동으로 추가되며, 선택된 날짜를 다시한번 클릭하면 해제됩니다.'); ?>
							</dd>
						</dl>
					</div>
				</td>
			</tr>




		<tr class="js_expire_type_wrap">
			<th>티켓 유효기간</th>
			<td colspan="3">
				<?php echo _InputRadio( "_expire_type" , array('','day','date'), $row['p_expire_type'] , " " , array('없음(무제한)','만료일 지정','만료날짜 지정') , '');?>

				<div class="lineup-row js_expire_type if_day"  style="display: none;">
					<div class="dash_line"></div>
					<span class="fr_tx">발급일로부터 </span>
					<input type="text" name="_expire_day" class="design number_style" value="<?php echo $row['p_expire_day'] ? $row['p_expire_day'] : 0; ?>" style="width:70px;" placeholder="">
					<span class="fr_tx">일 까지</span>
					<div class="dash_line"></div>
				</div>
				<div class="lineup-row type_date js_expire_type if_date" style="display: none;">
					<div class="dash_line"></div>
					<input type="text" name="_expire_date" class="design js_datepic" value="<?php echo $row['p_expire_date'] ? $row['p_expire_date'] : date('Y-m-d',strtotime("+7days")); ?>" style="width:85px;" placeholder="날짜 선택">
					<span class="fr_tx">까지</span>
					<div class="dash_line"></div>
				</div>
				<?php
					echo _DescStr("달력옵션 사용시에는 본 설정과 무관하게  달력에서 선택된 날짜로 티켓유효기간이 자동 고정됩니다.","");
					echo _DescStr("티켓유효기간을 `없음`으로 설정 시 별도 표시 되지 않습니다.","");
					echo _DescStr("티켓유효기간 설정을 변경하더라도 이미 주문된 티켓의 유효기간은 변경되지 않습니다.(주문당시 설정을 기준으로 유효기간이 설정됩니다.)","");
				?>
			</td>
		</tr>
			<?php } ?>
			<tr>
				<th>참고사항</th>
				<td colspan="3">
					<?php echo _DescStr('옵션이 등록되지 않거나 옵션 판매가의 0원이 포함되어 있는 경우 옵션 오류 상품으로 저장되니 주의해주세요.','red');?>
					<?php echo _DescStr('옵션유형을 변경하시면 기존에 등록하셨던 옵션의 형태가 변경되므로 주의해주세요.','red'); ?>
					<?php echo _DescStr('주문 내역이 있는 상품의 옵션은 기존 주문건에 대한 정보의 혼선이 있을 수 있으니 수정에 주의해주세요.'); ?>
					<?php
						if( $app_product_type == 'delivery'){
							echo _DescStr('추가옵션은 차수나 유형선택이 불가하고 무조건 2차 옵션으로 일반유형으로만 등록가능합니다.','');
						}
						else{
							echo _DescStr('티켓상품은 추가옵션 등록이 불가능합니다.','');
						}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>








<div class="group_title"><strong>카테고리 및 부가정보</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th class="ess">카테고리</th>
				<td colspan="3">
					<?php include_once("_product.inc_category_form.php"); ?>

                    <div id="_product_cateogry_list" class="category_result_wrap">
                        <!-- 상품카테고리 목록 노출 -->
                        <?PHP
                        $_cmode = "list";
                        include_once("_product.inc_category_pro.php");
                        ?>
                    </div>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<a href="/totalAdmin/_category.list.php" class="c_btn sky line" target="_blank">카테고리 설정 바로가기</a>
					<?php }else{ ?>
					<?php } ?>
				</td>
			</tr>
			<?php
				// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
				//		basic : 기본정보
				//		all : 브랜드 전체 정보
				$arr_brand = brand_info('basic');
			?>
			<tr>
				<th>브랜드</th>
				<td>
					<div class="lineup-row">
						<?php echo _InputSelect( "_brand" , array_keys($arr_brand) , $row['p_brand'] , "" , array_values($arr_brand) , "브랜드 선택"); ?>
						<a href="#none" class="c_btn sky line js_brand_preview" <?php echo $row['p_brand']!=''?'':'style="display:none"';?>  target="_blank">미리보기</a>
					</div>
					<?php echo _DescStr("브랜드를 선택하면 상세페이지에 노출되며, 스킨에 따라 위치는 상이할 수 있습니다."); ?>
					<?php echo _DescStr("해당 브랜드를 클릭하거나, 별도 브랜드 목록페이지에서도 상품이 노출됩니다."); ?>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="/totalAdmin/_brand.list.php" class="c_btn sky line" target="_blank">브랜드 설정 바로가기</a>
					<?php }?>
				</td>
				<? // JJC ::: 브랜드 정보 추출  ::: 2017-11-03?>
				<?php  if( $AdminPath == 'totalAdmin'){ ?>
				<th>상품 아이콘</th>
				<td>
					<?php
						$r2 = $product_icon;
						$pi_uid_array = explode(",",$row['p_icon']);
						if(sizeof($r2) > 0){
							foreach($r2 as $k2 => $v2) {
								$checked = @array_search($v2['pi_uid'],$pi_uid_array) === false ? NULL : " checked ";

								if($v2['pi_view_type']=='img'){
									$arr_view_icon = "<img src='".IMG_DIR_ICON.$v2['pi_img_m']."' title = '".$v2['pi_title']."'>";
								}else{
									$arr_view_icon = '<span class="item_icon" style="background-color:'.$v2['pi_bg_color'].'; color:'.$v2['pi_text_color'].'; border-color:'.$v2['pi_line_color'].';">'.addslashes(strip_tags($v2['pi_title'])).'</span>';
								}
								echo "<label class='design'><input type='checkbox' name='_icon[]' value='". $v2['pi_uid'] ."' ".$checked.">".$arr_view_icon."</label>";
							}
						}else{
							echo _DescStr('등록된 상품 아이콘이 없습니다.');
						}
					?>
					<?php echo _DescStr("상품목록과 상세페이지에 노출되며, 스킨에 따라 위치는 상이할 수 있습니다."); ?>
					<?php echo _DescStr("자동적용 아이콘은 상품 설정값에 따라 자동으로 노출됩니다."); ?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<a href="/totalAdmin/_product_icon.list.php" class="c_btn sky line" target="_blank">아이콘 설정 바로가기</a>
				</td>
				<?php }else{ ?>
				<?php
					$r2 = $product_icon;
					$pi_uid_array = explode(",",$row['p_icon']);
					if( count($pi_uid_array) > 0 && count($r2) > 0){
				?>
				<th>상품 아이콘</th>
				<td>
					<?php
						foreach($r2 as $k2 => $v2) {
							$checked = @array_search($v2['pi_uid'],$pi_uid_array) === false ? NULL : " checked ";

							if($v2['pi_view_type']=='img'){
								$arr_view_icon = "<img src='".IMG_DIR_ICON.$v2['pi_img_m']."' title = '".$v2['pi_title']."'>";
							}else{
								$arr_view_icon = '<span class="item_icon" style="background-color:'.$v2['pi_bg_color'].'; color:'.$v2['pi_text_color'].'; border-color:'.$v2['pi_line_color'].';">'.addslashes(strip_tags($v2['pi_title'])).'</span>';
							}
							echo "<label class='design'><input type='checkbox' class='disabled' disabled value='". $v2['pi_uid'] ."' ".$checked.">".$arr_view_icon."</label>";
						}
					?>

					<?php
						echo _DescStr("상품 아이콘은 통합 관리자만 수정할 수 있습니다.",'red');
					?>
					<?php echo _DescStr("상품목록과 상세페이지에 노출되며, 스킨에 따라 위치는 상이할 수 있습니다."); ?>
					<?php echo _DescStr("자동적용 아이콘은 상품 설정값에 따라 자동으로 노출됩니다."); ?>
				</td>
				<?php } }  ?>
			</tr>
			<tr>
				<th class="ess">정보제공고시</th>
				<td colspan="3">
					<div class="lineup-row">
						<a href="#none" onclick="reqinfo_popup();" class="c_btn h27 blue">개별 설정하기</a>
						<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<a href="#none" onclick="reqinfo_default_setting_popup();" class="c_btn h27 blue line"><?php echo $arr_product_type_name[$app_product_type]; ?> 샘플 설정하기</a>
						<?php }else{ ?>
						<?php } ?>
					</div>
					<?php echo _DescStr('전자상거래 등에서의 소비자 보호에 관한 법률에 의해 필수적으로 입력해야 합니다.'); ?>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
					<?php echo _DescStr('샘플항목으로 설정해두면 상품등록시마다 동일하게 입력되며, 개별로 수정도 가능합니다.'); ?>
					<?php }else{ ?>
					<?php } ?>
				</td>
			</tr>

			<?php if( $app_product_type == 'delivery'){ ?>
			<tr>
				<th>제조사</th>
				<td>
					<input type="text" name="_maker" class="design" value="<?php echo $row['p_maker']; ?>" placeholder="제조사" style="">
				</td>
				<th>원산지</th>
				<td>
					<input type="text" name="_orgin" class="design" value="<?php echo $row['p_orgin']; ?>" placeholder="원산지" style="">
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>



<div class="group_title"><strong>판매 이벤트 설정</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th class="">기간판매</th>
				<td colspan="3">
					<?php echo _InputRadio('_sale_type' , array('A','T') , ($row['p_sale_type'] ? $row['p_sale_type'] : 'A'), '' , array('상시판매','기간판매'));  ?>
					<?php echo _DescStr("기간판매 사용시에는 타임세일을 사용할 수 없습니다.",'blue'); ?>

					<div class="js_sale_type_wrap" id="_sale_typeT_date_box"<?php echo $row['p_sale_type'] == 'T' ? null : ' style="display:none;"';  ?>>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="lineup-row type_date">
							<input type="text" name="_sale_sdate" readonly value="<?php echo (!$row['p_sale_sdate'] || $row['p_sale_sdate'] == '0000-00-00') ? date('Y-m-d') : $row['p_sale_sdate'] ?>" class="design js_pic_day" style="width:85px">
							<input type="hidden" name="_sale_sdateh" ID="sale_dateh" value='<?=sprintf("%02d" , $row['p_sale_sdateh'])?>' class="input_text"/><!-- 시 -->
							<input type="hidden" name="_sale_sdatem" ID="sale_datem" value='<?=sprintf("%02d" , $row['p_sale_sdatem'])?>' class="input_text"/><!-- 분 -->
							<span class="fr_tx">~</span>
							<input type="text" name="_sale_edate" readonly value="<?php echo (!$row['p_sale_edate'] || $row['p_sale_edate'] == '0000-00-00') ? date('Y-m-d',strtotime("+1 years")) : $row['p_sale_edate'] ?>" class="design js_pic_day" style="width:85px">
							<input type="hidden" name="_sale_edateh" ID="sale_edateh" value='<?=($row['p_sale_edateh'] ? sprintf("%02d" , $row['p_sale_edateh']) : "23")?>'  /><!-- 시 -->
							<input type="hidden" name="_sale_edatem" ID="sale_edatem" value='<?=($row['p_sale_edatem'] ? sprintf("%02d" , $row['p_sale_edatem']) : "59")?>' /><!-- 분 -->
						</div>
						<?php echo _DescStr("설정한 기간이 지나면 사용자페이지에 노출은 되지만 구매가 불가합니다.",''); ?>
					</div>

				</td>
			</tr>
			<tr>
				<th class="">타임세일</th>
				<td colspan="3">
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<?php echo _InputCheckbox( '_time_sale' , array('Y'), ($row['p_time_sale']) , '' , array('타임세일 사용') , '');?>
						<?php echo _DescStr("상시판매일 경우에만 타임세일을 사용할 수 있습니다.",'blue'); ?>

						<div id="_time_saleY_date_box"<?php echo $row['p_time_sale'] == 'Y' ? null : ' style="display:none;"';  ?>>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row type_date">
								<input type="text" name="_time_sale_sdate" readonly value="<?php echo (!$row['p_time_sale_sdate'] || $row['p_time_sale_sdate'] == '0000-00-00') ? date('Y-m-d') : $row['p_time_sale_sdate'] ?>" class="design js_pic_day" style="width:85px">
								<input type="time" name="_time_sale_sclock" value="<?php echo date('H:i',strtotime($row['p_time_sale_sclock']));?>" class="design type_time input_text"/>
								<span class="fr_tx">~</span>
								<input type="text" name="_time_sale_edate" readonly value="<?php echo (!$row['p_time_sale_edate'] || $row['p_time_sale_edate'] == '0000-00-00') ? date('Y-m-d',strtotime("+1 years")) : $row['p_time_sale_edate'] ?>" class="design js_pic_day" style="width:85px">
								<input type="time" name="_time_sale_eclock" value= "<?php echo date('H:i',strtotime($row['p_time_sale_eclock']));?>" class="design type_time"/>
								<a href="/?pn=product.time_list" class="c_btn sky" target="_blank">미리보기</a>
							</div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="/totalAdmin/_config.display.timesale.php" class="c_btn sky line" target="_blank">타임세일 상품설정 바로가기</a>
						</div>
					<?php }else{ ?>
						<?php echo _InputCheckbox( '_time_sale' , array('Y'), ($row['p_time_sale']) , '' , array('타임세일 사용') , '');?>

						<div id="_time_saleY_date_box"<?php echo $row['p_time_sale'] == 'Y' ? null : ' style="display:none;"';  ?>>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row type_date">
								<input type="text" name="_time_sale_sdate" readonly value="<?php echo (!$row['p_time_sale_sdate'] || $row['p_time_sale_sdate'] == '0000-00-00') ? date('Y-m-d') : $row['p_time_sale_sdate'] ?>" class="design js_pic_day" style="width:85px" disabled>
								<input type="time" name="_time_sale_sclock" value="<?php echo date('H:i',strtotime($row['p_time_sale_sclock']));?>" class="design type_time input_text" disabled/>
								<span class="fr_tx">~</span>
								<input type="text" name="_time_sale_edate" readonly value="<?php echo (!$row['p_time_sale_edate'] || $row['p_time_sale_edate'] == '0000-00-00') ? date('Y-m-d',strtotime("+1 years")) : $row['p_time_sale_edate'] ?>" class="design js_pic_day" style="width:85px" disabled>
								<input type="time" name="_time_sale_eclock" value= "<?php echo date('H:i',strtotime($row['p_time_sale_eclock']));?>" class="design type_time" disabled/>
								<a href="/?pn=product.time_list" class="c_btn sky" target="_blank">미리보기</a>
							</div>
						</div>
					<?php } ?>
				</td>
			</tr>

			<?php if( $app_product_type == 'delivery'){?>
			<tr>
				<th>네이버페이</th>
				<td colspan="3">
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
					<?php
						echo _InputRadio( "npay_use" , array('Y','N'), ($row['npay_use'] ? $row['npay_use'] : "N") , "" , array('사용','미사용') , '');

						//echo '<div class="tip_box">';
						echo _DescStr('정상적으로 계약이 된 경우 장바구니/구매하기 버튼 하단에 네이버페이 구매 버튼이 노출됩니다.');
						echo _DescStr('네이버페이 사용 시 상품별 설정이 가능하며 미사용 설정 시 네이버정책 위반이 될 수 있으므로 주의하시기 바랍니다.');
						echo _DescStr('네이버페이 결제 또는 재고 문제가 있을 시 해당 설정을 통해 네이버페이를 일시적으로 미사용 처리 할 수 있습니다.');
						//echo '</div>';
					?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<a href="/totalAdmin/_config.pg_naver.form.php" class="c_btn sky line" target="_blank">네이버페이 설정 바로가기</a>
					<?php }else{ ?>
						<?php
							echo _InputRadio( "" , array('Y','N'), ($row['npay_use'] ? $row['npay_use'] : "N") , " disabled " , array('사용','미사용') , '');

							//echo '<div class="tip_box">';
							echo _DescStr('정상적으로 계약이 된 경우 장바구니/구매하기 버튼 하단에 네이버페이 구매 버튼이 노출됩니다.');
							echo _DescStr('네이버페이 사용 시 상품별 설정이 가능하며 미사용 설정 시 네이버정책 위반이 될 수 있으므로 주의하시기 바랍니다.');
							echo _DescStr('네이버페이 결제 또는 재고 문제가 있을 시 해당 설정을 통해 네이버페이를 일시적으로 미사용 처리 할 수 있습니다.');
							//echo '</div>';
						?>
					<?php } ?>
				</td>
			</tr>
			<?php }?>
			<tr>
				<th>1회 최대 구매개수</th>
				<td>
                    <div class="lineup-row">
					    <input type="text" name="_buy_limit" class="design number_style" value="<?php echo $row['p_buy_limit']; ?>" placeholder="0" style="width:70px">
                        <span class="fr_tx">개</span>
                    </div>
					<div class="tip_box">
						<?php
							echo _DescStr("주문 시 한번에 구매할 수 있는 상품의 수량을 설정할 수 있습니다.(모든 옵션 동일)");
							echo _DescStr("0으로 입력 시 제한없이 구매가 가능합니다.","");
						?>
					</div>
				</td>
				<th>중복구매 가능여부</th>
				<td>
					<?php
						echo _InputRadio('_duplicate_use' , array('Y','N') , ($row['p_duplicate_use'] ? $row['p_duplicate_use'] : 'Y'), '' , array('가능','불가능'));
						//echo '<div class="tip_box">';
						echo _DescStr("불가능으로 설정할 경우 본상품은 1회만 구매 가능합니다.","blue");
						echo _DescStr("중복구매는 <strong>회원</strong> 주문시에 한해서 판별되며 주문취소, 비회원주문건은 제외됩니다. ");
						echo _DescStr("무통장/가상계좌는 결제대기 부터, 그외는 결제완료를 기준으로 체크됩니다.");
						//echo '</div>';
					?>
				</td>
			</tr>
			<?php  if( $AdminPath == 'totalAdmin'){ ?>
			<?php }else{ ?>
			<tr>
				<th class="t_red">참고사항</th>
				<td colspan="3">
					<?php
						if( $app_product_type == 'delivery'){
							echo _DescStr("타임세일/네이버페이는 통합관리자만 설정이 가능하니, 관리자에게 문의바랍니다.",'red');
						}else{
							echo _DescStr("타임세일은 통합관리자만 설정이 가능하니, 관리자에게 문의바랍니다.",'red');
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>



<div class="group_title"><strong>할인/혜택 설정</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>상품쿠폰</th>
				<td>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<?php
							$ex_coupon = explode("|" , $row['p_coupon']);
							if(count($ex_coupon) < 4){  $_tmp = $ex_coupon; $ex_coupon[0] = $_tmp[0]; $ex_coupon[2] = $_tmp[1]; $ex_coupon[1] = ''; }// 이전 데이터 예외 처리
						?>
						<?php echo _InputRadio( "_coupon_type" , array('', 'price' ,'per') , $ex_coupon[1] , '', array('미사용', '할인액(원)' ,'할인율(%)'));?>

						<div class="lineup-column type_auto cls_coupon_wrap"><?php // :[0328]?>
							<div class="cls_coupon_type price per">
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<span class="fr_tx t_black" style="width:40px">쿠폰명</span>
									<input type="text" name="_coupon_title" class="design " value="<?php echo $ex_coupon[0]; ?>" placeholder="쿠폰명 (최대 15자)" style="width:150px" maxlength="15">
								</div>
							</div>

							<div class="cls_coupon_type price">
								<div class="lineup-row">
									<span class="fr_tx t_orange" style="width:40px">할인액</span>
									<input type="text" name="_coupon_price" class="design number_style" value="<?php echo $ex_coupon[2]; ?>" placeholder="0" style="width:100px">
									<span class="fr_tx">원</span>
								</div>
							</div>

							<div class="cls_coupon_type per">
								<div class="lineup-row">
									<span class="fr_tx t_orange" style="width:40px">할인율</span>
									<input type="text" name="_coupon_per" class="design number_style" data-decimals="1" value="<?php echo number_format(floor($ex_coupon[3]*10)/10,1); ?>" placeholder="0" style="width:70px">
									<span class="fr_tx">%</span>
									<span class="fr_tx">최대</span>
									<input type="text" name="_coupon_max" class="design number_style" value="<?php echo $ex_coupon[4]; ?>" placeholder="0" style="width:100px">
									<span class="fr_tx">원</span>
								</div>
							</div>
						</div>
					<?php }else{ ?>
						<?php
							$ex_coupon = explode("|" , $row['p_coupon']);
							if(count($ex_coupon) < 4){  $_tmp = $ex_coupon; $ex_coupon[0] = $_tmp[0]; $ex_coupon[2] = $_tmp[1]; $ex_coupon[1] = ''; }// 이전 데이터 예외 처리
						?>
						<?php echo _InputRadio( "_coupon_type" , array('', 'price' ,'per') , $ex_coupon[1] , " disabled='disabled' " , array('미사용', '할인액(원)' ,'할인율(%)'));?>

						<div class="lineup-column type_auto cls_coupon_wrap"> <?php // :[0328]?>
							<div class="cls_coupon_type price per">
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<span class="fr_tx t_black" style="width:40px">쿠폰명</span>
									<input type="text" name="_coupon_title" class="design " value="<?php echo $ex_coupon[0]; ?>" placeholder="쿠폰명 (최대 15자)" style="width:150px" maxlength="15" disabled>
								</div>
							</div>
							<div class="cls_coupon_type price">
								<div class="lineup-row">
									<span class="fr_tx t_orange" style="width:40px">할인액</span>
									<input type="text" name="_coupon_price" class="design number_style" value="<?php echo $ex_coupon[2]; ?>" placeholder="0" style="width:100px" disabled>
									<span class="fr_tx">원</span>
								</div>
							</div>
							<div class="cls_coupon_type per">
								<div class="lineup-row">
									<span class="fr_tx t_orange" style="width:40px">할인율</span>
									<input type="text" name="_coupon_per" class="design number_style" data-decimals="1" value="<?php echo number_format(floor($ex_coupon[3]*10)/10,1); ?>" placeholder="0" style="width:70px" disabled>
									<span class="fr_tx">%</span>
									<span class="fr_tx">최대</span>
									<input type="text" name="_coupon_max" class="design number_style" value="<?php echo $ex_coupon[4]; ?>" placeholder="0" style="width:100px" disabled>
									<span class="fr_tx">원</span>
								</div>
							</div>
						</div>
					<?php } ?>

					<div class="dash_line"><!-- 점선라인 --></div>
					<?php echo _DescStr('주문 시 별도로 선택해서 사용하는 쿠폰과는 별개이며 상품에 자동적용되는 혜택입니다.'); ?>
					<?php echo _DescStr('사용할 경우 자동아이콘(상품쿠폰 아이콘)이 상품목록과 상세에 노출되며, 스킨에 따라 위치는 상이할 수 있습니다.'); ?>
				</td>
				<th>회원등급 추가혜택</th>
				<td>
					<?php  if( $AdminPath == 'totalAdmin'){ ?>
						<?php
							echo _InputRadio( "_groupset_use" , array('Y','N'), ($row['p_groupset_use'] ? $row['p_groupset_use'] : "N") , " class='_groupset_use' " , array('적용','미적용') , '');
							echo _DescStr('적용할 경우 등급관리에 설정해둔 혜택 (할인율/적립율)이 적용됩니다.');
						?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="/totalAdmin/_member_group_set.list.php" class="c_btn sky line" target="_blank">회원등급 설정 바로가기</a>
					<?php }else{ ?>
						<?php
							echo _InputRadio( "_groupset_use" , array('Y','N'), ($row['p_groupset_use'] ? $row['p_groupset_use'] : "N") , " class='_groupset_use' disabled " , array('적용','미적용') , '');
							echo _DescStr('적용할 경우 등급관리에 설정해둔 혜택 (할인율/적립율)이 적용됩니다.');
						?>
					<?php } ?>
				</td>
			</tr>
			<?php  if( $AdminPath == 'totalAdmin'){ ?>
			<?php }else{ ?>
			<tr>
				<th class="t_red">참고사항</th>
				<td>
					<?php echo _DescStr('할인 혜택은 통합관리자만 수정이 가능하니, 관리자에게 문의바랍니다.','red'); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>



<?php if( $app_product_type == 'delivery') {?>
<div class="group_title"><strong>정산 및 배송정보</strong></div>
<?php }else{ ?>
<div class="group_title"><strong>정산 정보</strong></div>
<?php } ?>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>

			<tr>
				<th class="ess">입점업체</th>
				<td>
					<?php
					// ----- JJC : 입점관리 : 2020-09-17 -----
					if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
						$arr_customer = arr_company();
						$arr_customer2 = arr_company2();
					?>
						<?php if( $AdminPath == 'totalAdmin'){ ?>
						<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
						<script src="/include/js/select2/js/select2.min.js"></script>
						<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php echo _InputSelect( '_cpid' , array_keys($arr_customer) , $row['p_cpid'] , ' class="select2" ' , array_values($arr_customer) , '입점업체 선택'); ?>
						<?php echo _DescStr('업체를 선택하면 상세페이지 하단에 판매자 정보로 노출됩니다.'); ?>
						<?php }else{ ?>
							<?php echo $arr_customer2[$row['p_cpid']]; ?>
						<?php } ?>
					<?php }else{?>
						<?php echo _DescStr('입점업체 미사용 <a href="https://www.onedaynet.co.kr/p/solution_plus.html#page_entershop" target="_blank"><em>신청하기</em></a>',''); ?>
					<?php }?>
				</td>
				<th>업체 정산형태</th>
				<td>
					<div class="lineup-row">
						<label class="design" style="width:80px"><input type="radio" name="_commission_type" value="공급가" <?php echo ($row['p_commission_type'] == '공급가' ? ' checked' : null); ?>>공급가</label>
						<input type="text" name="_sPrice" id="comSaleTypeTr1" class="design number_style t_right" value="<?php echo $row['p_sPrice']; ?>" placeholder="0" style="width:100px" <?php echo ($row['p_commission_type'] <> '공급가' ? ' disabled' : null); ?>>
						<span class="fr_tx">원 (매입가)</span>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="lineup-row">
						<label class="design" style="width:80px"><input type="radio" name="_commission_type" value="수수료" <?php echo ($row['p_commission_type'] <> '공급가' ? ' checked' : null); ?>>수수료</label>
						<input type="text" name="_sPersent" id="comSaleTypeTr2" class="design number_style t_right" value="<?php echo ($row['p_sPersent'] ? $row['p_sPersent'] : $siteInfo['s_account_commission']); ?>" placeholder="0" style="width:70px" <?php echo ($row['p_commission_type'] == '공급가' ? ' disabled' : null); ?>>
						<span class="fr_tx">%</span>
					</div>
				</td>
			</tr>
			<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
			<tr>
				<th>상품 과세여부</th>
				<td colspan="3">
					<label class="design"><input type="radio" name="p_vat" value="Y" <?php echo ($row['p_vat'] == "Y" || !$row['p_vat'] ? "checked" : NULL); ?>>과세</label>
					<label class="design"><input type="radio" name="p_vat" value="N" <?php echo ($row['p_vat'] == "N" ? "checked" : NULL); ?>>면세</label>
					<?php echo _DescStr('기본설정에서 복합과세를 선택한 경우 과세와 면세를 상품별로 선택할 수 있습니다.'); ?>
					<?php echo _DescStr('과세 : 부가세가 포함되어 세금계산서 또는 현금영수증(현금결제 시)이 발행됩니다.'); ?>
					<?php echo _DescStr('면세 : 부가세 없이 세금계산서 또는 현금영수증(현금결제 시)이 발행됩니다.'); ?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<a href="/totalAdmin/_config.vat.form.php" class="c_btn sky line" target="_blank">과세설정 바로가기</a>
				</td>
			</tr>
			<?php } ?>
			<?php if( $app_product_type == 'delivery') {?>
			<tr>
				<th>배송비 설정</th>
				<td colspan="3">
					<div class="lineup-row">
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="N" <?php echo ($row['p_shoppingPay_use'] == 'N' || $row['p_shoppingPay_use'] == '' ? ' checked ' : NULL); ?>><?php echo ($SubAdminMode ? '업체별' : '쇼핑몰'); ?> 정책</label>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="P" <?php echo ($row['p_shoppingPay_use'] == 'P' ? ' checked ' : NULL); ?>>상품별 배송비</label>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="Y" <?php echo ($row['p_shoppingPay_use'] == 'Y' ? ' checked ' : NULL); ?>>개별 배송비</label>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="F" <?php echo ($row['p_shoppingPay_use'] == 'F' ? ' checked ' : NULL); ?>>무료 배송</label>
						<!-- 무료배송비 일때 -->
					</div>

					<?php
						// ----- JJC : 상품별 배송비 : 2018-08-16 -----
					?>
					<div class="shoppingPayP" style="width:100%">
						<div class="lineup-row">
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="_shoppingPayPdPrice" class="design number_style" placeholder="0" value="<?php echo $row['p_shoppingPayPdPrice']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'P' ? ' disabled ' : NULL); ?>>
							<span class="fr_tx">원</span>
							<input type="text" name="_shoppingPayPfPrice" class="design number_style" placeholder="0" value="<?php echo $row['p_shoppingPayPfPrice']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'P' ? ' disabled ' : NULL); ?>>
							<span class="fr_tx">원 이상 무료배송</span>
						</div>
					</div>
					<?php
						// ----- JJC : 상품별 배송비 : 2018-08-16 -----
					?>

					<div class="shoppingPayY" style="width:100%">
						<div class="lineup-row">
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="_shoppingPay" class="design number_style t_right" id="_shoppingPay_use_Y" placeholder="0" value="<?php echo $row['p_shoppingPay']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'Y' ? ' disabled ' : NULL); ?>>
							<span class="fr_tx">원</span>
						</div>
					</div>

					<div class="shoppingPayN" style="width:100%">
						<div class="dash_line"><!-- 점선라인 --></div>
						<a href="#none" onclick="entershop_view(); return false;" class="c_btn h27 sky line">배송비 정책설정 바로가기</a>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<?php echo _DescStr('쇼핑몰 정책 : 쇼핑몰 통합관리자가 설정한 정책으로 자동적용됩니다.'); ?>
					<?php echo _DescStr('입점업체 정책 : 입점업체 기능을 사용 할 경우 상품에 선택된 업체의 정책으로 적용하며, 해당 업체에서 배송정책을 미사용한 경우 쇼핑몰 기본정책이 적용됩니다.'); ?>
					<?php echo _DescStr('상품별 배송비 : 이 상품에만 별도의 배송비와 무료배송비를 설정할 수 있습니다.'); ?>
					<?php echo _DescStr('개별 배송비 : 이 상품에만 별도의 배송비를 무조건 부과할 수 있습니다.'); ?>
					<?php echo _DescStr('무료배송 : 이 상품에 대해서만 무료배송을 적용하며, 무료배송 아이콘이 노출됩니다.'); ?>
				</td>
			</tr>
			<tr>
				<th>추가 배송정보</th>
				<td colspan="3">
					<input type="text" name="_delivery_info" class="design" value="<?php echo $row['p_delivery_info']?>" placeholder="배송정보 (예 : 로젠택배 (2~3일 소요))" maxlength="20">
					<?php echo _DescStr('상세페이지 기본 택배사 등의 배송정보에 추가적으로 노출되'); ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>


<div class="group_title"><strong>상세설명 및 부가정보</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>상세 설명</th>
				<td>
					<div class="mobile_tip">에디터 기능은 모바일에서 제한적일 수 있습니다.</div>
					<textarea name="_content" class="input_text SEditor"><?php echo stripslashes($row['p_content']); ?></textarea>
				</td>
			</tr>
			<tr>
				<th>해시태그</th>
				<td>
					<?php include '_product.hashtag.php'; ?>
				</td>
			</tr>
			<tr>
				<th>관련상품</th>
				<td>
					<?php include '_product.relation.php'; ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>



<!-- 이용안내 -->
<?php
	if( $app_product_type == 'delivery'){
		include_once(dirname(__file__)."/_product.inc_guide.php");
	}
	else{ // 티켓일 경우
		// 주소설정 : 위치정보
		include_once dirname(__FILE__)."/_product.inc_address.php";
	}
?>



<div class="group_title"><strong>이미지 등록</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>등록방법</th>
				<td>
					<label class="design"><input type="radio" name="_img_auto_resize_use" <?php echo ($_mode<>'modify'?' checked ':''); ?> class="img_auto_resize_use" value="auto">자동등록</label>
					<label class="design"><input type="radio" name="_img_auto_resize_use" <?php echo ($_mode=='modify'?' checked ':''); ?> class="img_auto_resize_use" value="direct">직접등록</label>
					<?php echo _DescStr('자동등록 : 대표 이미지 1개만 등록하고 다른 노출영역의 이미지를 자동으로 생성합니다.'); ?>
					<?php echo _DescStr('직접등록 : 각 노출 항목에 이미지를 모두 개별적으로 등록합니다.'); ?>
				</td>
			</tr>
			<tr class="auto_area">
				<th>대표 이미지</th>
				<td>
					<?php echo _PhotoForm( '../upfiles/product', '_img_main_tmp', '', 'style="width:300px"')?>
					<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['main_image_width']} x {$SkinInfo['product']['main_image_height']} (pixel)"); ?>
				</td>
			</tr>
			<tr class="direct_area">
				<th>목록 이미지</th>
				<td>
					<div class="in_option_list ">
						<dl>
							<dt>기본이미지</dt>
							<dd>
								<div class="lineup-row">
									<?php
										// 파일삭제 체크를위한 기존이미지 저장
										echo '<input type="hidden" name="backup_img_org" value="'. implode('||', array_filter(array($row['p_img_list_square'],$row['p_img_list_over'],$row['p_img_b1'],$row['p_img_b2'],$row['p_img_b3'],$row['p_img_b4'],$row['p_img_b5']))) .'">';
									?>
									<div class="input_file hyperLinkDiv" style="display:none;">
										<input type="text" name="_img_list_square" class="design  hyperLink" style="width:100%;" value="<?php echo (strpos($row['p_img_list_square'], '//') !== false?$row['p_img_list_square']:null); ?>" placeholder="http(s)://를 포함하여 입력" disabled>
									</div>
									<?php echo _PhotoForm( '../upfiles/product', '_img_list_square', $row['p_img_list_square'], 'style="width:250px"')?>
								</div>
								<label class="design"><input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="_img_list_square">외부 이미지</label>
								<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['list_image_width']} x {$SkinInfo['product']['list_image_height']} (pixel)"); ?>

								<?php if(strpos($row['p_img_list_square'], '//') !== false){ ?>
								<script>
									// 외부 이미지 사용시 처리
									(function(){
										$parent = $('.js_use_hyperlink[value=_img_list_square]').attr({'checked':'checked'}).closest('td');
										$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
										$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
									})();
								</script>
								<?php } ?>

							</dd>
						</dl>
						<dl>
							<dt>오버이미지</dt>
							<dd>
								<div class="lineup-row">
									<div class="input_file hyperLinkDiv" style="display:none;">
											<input type="text" name="_img_list_over" class="design  hyperLink" style="width:100%;" value="<?php echo (strpos($row['p_img_list_over'], '//') !== false?$row['p_img_list_over']:null); ?>" placeholder="http(s)://를 포함하여 입력" disabled>

									</div>
									<?php echo _PhotoForm( '../upfiles/product', '_img_list_over', $row['p_img_list_over'], 'style="width:250px"')?>
								</div>
								<label class="design"><input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="_img_list_over">외부 이미지</label>
								<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['list_image_width']} x {$SkinInfo['product']['list_image_height']} (pixel)"); ?>

								<?php if(strpos($row['p_img_list_over'], '//') !== false){ ?>
								<script>
									// 외부 이미지 사용시 처리
									(function(){
										$parent = $('.js_use_hyperlink[value=_img_list_over]').attr({'checked':'checked'}).closest('td');
										$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
										$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
									})();
								</script>
								<?php } ?>

							</dd>
						</dl>
					</div>
					<?php echo _DescStr('기본 이미지 : 메인과 상품목록에 노출되는 이미지입니다. (필수)'); ?>
					<?php echo _DescStr('오버 이미지 : 마우스를 오버하면 애니메이션 효과와 함께 나타나는 이미지입니다.(선택사항)'); ?>
				</td>
			</tr>
			<tr class="direct_area">
				<th>상세 이미지</th>
				<td>
					<div class="in_option_list ">
					<?php
						$arr_imgname = array('_img_b1', '_img_b2', '_img_b3', '_img_b4', '_img_b5');
						$_img_idx = 0;
						foreach($arr_imgname as $k=>$v){
							if(!$row['p'.$v] && $k>0) continue;
							$_img_idx++;
					?>
						<dl>
							<dt>
								<font class="js_img_idx"><?php echo $_img_idx;?>번</font>
							</dt>
							<dd>
								<div class="lineup-row">
									<div class="input_file hyperLinkDiv" style="display:none;width:250px">
										<input type="text" name="_img_b<?php echo $_img_idx; ?>" class="hyperLink design" style="width:100%;" value="<?php echo (strpos($row['p'.$v], '//') !== false?$row['p'.$v]:null); ?>" placeholder="http(s)://를 포함하여 입력해주세요." disabled>
									</div>
									<?php echo _PhotoForm( '../upfiles/product', '_img_b'.$_img_idx, $row['p'.$v], 'style="width:250px"')?>
									<?php if($k==0){ ?>
									<a href="#none" class="c_btn h27 blue js_addimg_btn">추가</a>
									<?php }else{ ?>
									<a href="#none" class="c_btn h27 black line js_delimg_btn">삭제</a>
									<?php } ?>
								</div>
								<label class="design"><input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="_img_b<?php echo $_img_idx; ?>">외부 이미지</label>


								<?php if(strpos($row['p'.$v], '//') !== false){ ?>
								<script>
									// 외부 이미지 사용시 처리
									(function(){
										$parent = $('.js_use_hyperlink[value=_img_b<?php echo $_img_idx; ?>]').attr({'checked':'checked'}).closest('td');
										$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
										$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
									})();
								</script>
								<?php } ?>


								<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['detail_image_width']} x {$SkinInfo['product']['detail_image_height']} (pixel)"); ?>
							</dd>
						</dl>
					<?php } ?>
					</div>
					<?php echo _DescStr('상세이미지는 최대 5개까지 등록가능하며, 상품상세페이지에서 스와이프(롤링)형태로 노출됩니다.'); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>


<?php if($AdminPath == 'totalAdmin') {?>
<div class="group_title"><strong>네이버/다음 EP연동</strong></div>
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>
			<tr>
				<th>네이버 EP</th>
				<td>
					<?php echo _InputRadio( 'p_naver_switch' , array('Y','N') , $row['p_naver_switch'] ? $row['p_naver_switch'] : "N" , "" , array('적용','미적용') , ""); ?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="tip_box">
						<?php echo _DescStr("전체상품 DB URL : <em>http://". $system['host'] ."/addons/ep/naver/allep.php</em>")?>
					</div>
				</td>
				<th>다음 EP</th>
				<td>
					<?php echo _InputRadio( 'p_daum_switch' , array('Y','N') , $row['p_daum_switch'] ? $row['p_daum_switch'] : "N" , "" , array('적용','미적용') , ""); ?>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="tip_box">
						<?php echo _DescStr('전체상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/allep.php</em>'); ?>
						<?php echo _DescStr('요약상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/briefep.php</em>'); ?>
					</div>
				</td>
			</tr>
			<tr>
				<th>참고사항</th>
				<td colspan="3">
					<a href="/totalAdmin/_config.product.form.php?menuUid=303" class="c_btn h46 sky line" target="_blank">EP연동 설정 바로가기</a>
					<?php echo _DescStr("환경설정에서 전체 적용이 되어야 정상적으로 노출되니 설정을 확인해주세요.")?>
					<?php echo _DescStr("전체 미적용인 경우에는 상품마다 개별 설정을 해도 무조건 미적용됩니다.")?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>