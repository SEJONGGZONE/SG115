<?PHP
	include_once("wrap.header.php");


	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		else $_PVS .= "&$key=$val";
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	$pass_status = $pass_status ? $pass_status : 'N';

	// DB가 존재하는지 체크
	$_db_chker = _MQ_result(" SELECT 1 FROM Information_schema.tables WHERE table_name = 'smart_orderbank_log' ");
	$res = array();

	if($_db_chker){
		// 검색 체크 [무통장건만]
		$s_query = " where 1 and ob_deleted = 'N' ";

		// 텝메뉴 기본 - 처리대기
		$pass_status = $pass_status ? $pass_status : "N";

		// -- 검색어
		if($pass_input_type == "pass_ordername"){ // 주문자명
			 $s_query .= " AND o.o_oname like '%". $pass_input ."%' ";
		}else if( $pass_input_type == "pass_ordernum"){ // 주문번호
			$s_query .= " AND o.o_ordernum like '%". $pass_input ."%' ";
		}else if($pass_input_type == "pass_payname"){ // 입금자명
			$s_query .= " AND (o.o_deposit like '%". $pass_input ."%' or ob.ob_ordername like '".$pass_input."') ";
		}else if($pass_input_type == "pass_tid"){ // 거래번호
			$s_query .= " AND ob.ob_tid like '%". $pass_input ."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and (  o.o_oname like '%".$pass_input."%' or o.o_ordernum like '%". $pass_input ."%' or (o.o_deposit like '%". $pass_input ."%' or ob.ob_ordername like '".$pass_input."') or ob.ob_tid like '%". $pass_input ."%'   ) ";
		}

		if( $pass_o_price_real ) { $s_query .= " AND  o.o_price_real = '".$pass_o_price_real."' "; }// 지블금액
		if( $pass_sdate ) { $s_query .= " AND ob_paydate >= '". $pass_sdate ." 00:00:00' "; }// 입금일시
		if( $pass_edate ) { $s_query .= " AND ob_paydate <= '". $pass_edate ." 23:59:59' "; }// 입금일시

		if( $pass_status ) {
			// 연동된 주문이 취소/삭제될경우 처리대기목록에 노출
			if( $pass_status == 'N' ) {
				$s_query .= " AND ob.ob_status ='N' ";
			}else{
				$s_query .= " AND ob.ob_status ='Y' ";
			}
		}// 입금상태
		if( $pass_status_type ) { $s_query .= " AND ob.ob_status_type ='".$pass_status_type."' "; }// 입금상태

		if(!$listmaxcount) $listmaxcount = 20;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'ob_date';
		if(!$so) $so = 'desc';
		$count = $listpg * $listmaxcount - $listmaxcount;

		$que = " select count(*) as cnt from smart_orderbank_log as ob
		left join smart_order as o on (o.o_ordernum = ob.ob_ordernum)
		$s_query ";

		$res = _MQ($que);
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$que = " select * from smart_orderbank_log as ob
		left join smart_order as o on (o.o_ordernum = ob.ob_ordernum)
		" . $s_query . "
		order by {$st} {$so} limit $count , $listmaxcount  ";
		$res = _MQ_assoc($que);

	}

?>

    <div class="c_tab">
        <ul>
            <li class="<?php echo ($pass_status<>"Y" ? "hit" : ""); ?>"><a href="?pass_status=N<?php echo ($menuUid?'&menuUid='.$menuUid:null); ?>" class="btn"><strong>처리대기 목록</strong></a></li>
            <li class="<?php echo ($pass_status=="Y" ? "hit" : ""); ?>"><a href="?pass_status=Y<?php echo ($menuUid?'&menuUid='.$menuUid:null); ?>" class="btn"><strong>처리완료 목록</strong></a></li>
        </ul>
    </div>

	<form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" class="data_search">
        <input type="hidden" name="mode" value="search">
        <input type="hidden" name="st" value="<?php echo $st; ?>">
        <input type="hidden" name="so" value="<?php echo $so; ?>">
        <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
        <input type="hidden" name="pass_status" value="<?php echo $pass_status;?>">

		<?php
			if(!$_db_chker){
				echo '<div class="page_warning">';
				echo '실시간입금 확인서비스 DB가 존재하지 않습니다.  DB생성하기 버튼을 눌러 DB를 생성해 주세요.';
				echo '<a href="'. OD_ADMIN_DIR .'/_orderbanklog.pro.php?_mode=create_table&pass_status='. $pass_status .'" class="c_btn h22 red">DB생성하기</a>';
				echo '</div>';
			}
			else if($siteInfo["s_bank_autocheck_use"]<>'Y'){
				echo '<div class="page_warning">';
				echo '실시간입금 확인서비스가 미사용으로 설정되어 있습니다. 사용으로 설정 후 확인가능합니다.';
				echo '<a href="'. OD_ADMIN_DIR .'/_config.orderbank.form.php" class="c_btn h22 red" target="_blank">설정 바로가기</a>';
				echo '</div>';
			}
		?>

        <div class="group_title">
            <strong>Search</strong>
            <div class="btn_box">
                <a href="#none" class="btn_search_ctrl js_onoff_event" data-target=".data_search" data-add="if_open_search"><i>검색</i><em>닫기</em></a>
                <?php if($mode == 'search'){ ?>
                    <a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'pass_status'=>$pass_status, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal btn_reset" accesskey="l">검색 초기화</a>
                <?php } ?>
				<a href="#none" class="c_btn sky line" onclick="$('.js_thisview').toggle(); return false;">도움말</a>
            </div>
        </div>

		<div class="js_thisview" style="display:none">
			 <table class="table_form" >
				<colgroup>
					<col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
				</colgroup>
				<tbody>
					<tr >
						<th>참고사항</th>
						<td colspan="5">
							<div class="tip_box">
								<?php echo _DescStr('주문연동, 관리자 연동시 주문내역에 따라 적립금 지급, 쿠폰사용, 재고증감, 문자 발송, 이메일 발송등이 처리됩니다.'); ?>
								<?php echo _DescStr('주문연동, 관리자연동시과 같이 주문과 연동된 입금내역은 삭제할 수 없습니다.'); ?>
								<?php echo _DescStr('입금내역중 주문과 연동시키지 못하는 주문들은 <em>주문외입금</em>처리하여 <em>처리대기</em>목록에서 <em>처리완료</em>목록으로 이동시킬수 있습니다.'); ?>
								<?php echo _DescStr('주문외입금 처리 버튼은 <em>주문연동</em>버튼 클릭시 나타나는 팝업창에 있습니다.'); ?>
								<div class="dash_line"></div>
								<?php echo _DescStr('<span class="c_tag blue line t4">주문연동</span> 입금자명과 주문자가 정확히 매칭되어 자동으로 연동처리된 입금내역입니다. 연동된 주문은 자동으로 입금완료 처리됩니다.', ''); ?>
								<?php echo _DescStr('<span class="c_tag green line t4">관리자연동</span> 처리대기 입금건중 <em>주문연동</em>버튼으로 관리자가 직접 주문과 연동시킨 입금내역입니다. 연동된주문은 입금완료 처리됩니다.', ''); ?>
								<?php echo _DescStr('<span class="c_tag yellow line t4">주문외입금</span> 처리대기 입금건중 주문과 관련없는 입금내역입니다.', ''); ?>
								<?php echo _DescStr('<span class="c_tag black line t4">처리대기</span> 입금내역과 주문내역이 정확히 매칭이 되지 않아 연동처리가 되지 않은 입금내역입니다. 관리자가 직접 확인 후 주문연동 버튼을 연동할 수 있습니다.', ''); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

        <div class="search_form">
            <table class="table_form">
                <colgroup>
                    <col width="130"><col width="*"><col width="130"><col width="*"><col width="130"><col width="*">
                </colgroup>
                <tbody>
                    <tr>
                        <th>검색어</th>
                        <td>
							<div class="lineup-row type_multi">
								<select name="pass_input_type">
									<option value="all" <?php echo $pass_input_type == 'all' ? 'selected' : ''?>>전체</option>
									<option value="pass_ordername" <?php echo $pass_input_type == 'pass_ordername' ? 'selected' : ''?>>주문자명</option>
									<option value="pass_ordernum" <?php echo $pass_input_type == 'pass_ordernum' ? 'selected' : ''?>>주문번호</option>
									<option value="pass_payname" <?php echo $pass_input_type == 'pass_payname' ? 'selected' : ''?>>입금자명</option>
									<option value="pass_tid" <?php echo $pass_input_type == 'pass_tid' ? 'selected' : ''?>>거래번호</option>
								</select>
								<input type="text" name="pass_input" class="design" style="width:150px" value="<?php echo $pass_input?>" placeholder="검색어" />
							</div>
						</td>
						<th>입금일</th>
                        <td>
                            <div class="lineup-row type_date">
                                <input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
                                <span class="fr_tx">-</span>
                                <input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day" style="width:85px" autocomplete="off" placeholder="날짜 선택">
                            </div>
                        </td>
                        <th>입금금액</th>
                        <td>
							<div class="lineup-row">
								<input type="text" name="pass_o_price_real" class="design number_style" value="<?php echo $pass_o_price_real; ?>" placeholder="0" style="width:100px">
								<span class="fr_xt">원</span>
							</div>
                        </td>
                    </tr>
					<?php if($pass_status=='Y'){?>
						<tr>
							<th>처리상태</th>
							 <td colspan="5">
								<?php   echo _InputRadio( "pass_status_type" , array('','order', 'adminO', 'adminC') , $pass_status_type , "" , array('전체','주문연동','관리자연동','주문외입금') , '-입금처리상태-');?>
							</td>
						</tr>
					<?php }?>
                </tbody>
            </table>


            <!-- 가운데정렬버튼 -->
            <div class="c_btnbox">
                <ul>
                    <li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span></li>
                    <?php if($mode == 'search'){ ?>
                        <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'pass_status'=>$pass_status, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

	</form><!-- end data_search -->



	<form name="frm" method="post" action="_orderbanklog.pro.php" target="">
	<input type="hidden" name="_mode" value=''>
	<input type="hidden" name="pass_status" value='<?php echo $pass_status; ?>'>
	<input type="hidden" name="_seachcnt" value='<?php echo $TotalCount; ?>'>
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e',$s_query); ?>">


		<!-- 리스트영역 -->
		<div class="data_list">

			<table class="table_list type_nocheck">
				<colgroup>
					<col width="70"><col width="150"><col width="135">
					<col width="*"><col width="100">

					<col width="100"><col width="145">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">처리상태 / 주문상태</div></th>
						<th scope="col">주문번호 (입금자명)</th>
						<th scope="col">APIBOX 거래번호 / 로그내용</th>
						<th scope="col">입금금액</th>
						<th scope="col">입금일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?PHP
				if(sizeof($res) > 0){
					foreach($res as $k=>$v) {

						$_mod = "<span class='shop_btn_pack'><input type=button value='상세보기' class='input_small blue' onclick='location.href=(\"_order.form.php?_mode=cancellist&ordernum=" . $v['o_ordernum'] . "&_PVSC=" . $_PVSC . "\");'></span>";
						$_num = $TotalCount - $count - $k ;

						 # 입금상태
						if($v['ob_status_type'] == 'order' && $v['o_ordernum']){
							$order_pay_status_html = ' <span class="c_tag blue line t4">주문연동</span> ';
						}else if($v['ob_status_type'] == 'adminO' && $v['o_ordernum']){
							$order_pay_status_html = ' <span class="c_tag green line t4">관리자연동</span> ';
						}else if($v['ob_status_type'] == 'adminC'){
							$order_pay_status_html = ' <span class="c_tag yellow line t4">주문외입금</span> ';
						}else{
							$order_pay_status_html = ' <span class="c_tag black line h22 t4">처리대기</span> ';
						}

						# 주문상태
						$order_status_html = " " . ($v['o_status'] ? $arr_o_status[$v['o_status']] : ' <span class="c_tag light h22 t4">미확인</span> ');

						# 액션 버튼
						$action_btn = "";
						if(!$v['o_ordernum']){
							$action_btn .= '
									<a href="#none" class="c_btn blue" onclick="open_orderpopup(\'' . $v['ob_uid'] . '\');">주문연동</a>
									<a href="#none" class="c_btn h22 dark line" onclick="del(\'_orderbanklog.pro.php?_mode=delete&pass_status='. $pass_status .'&_uid='. $v['ob_uid'] . '&_PVSC=' . $_PVSC . '\');">내역삭제</a>
							';
						}else if($v['o_ordernum']){
							$action_btn .= '
									<a href="#none" class="c_btn sky line" onclick="goto_order(\'_order.form.php?_mode=modify&_ordernum=' . $v['o_ordernum'] . '\');">주문보기</a>
									<a href="#none" class="c_btn h22 dark line" onclick="cancel(\'_orderbanklog.pro.php?_mode=cancel&pass_status='. $pass_status .'&_uid='. $v['ob_uid'] . '&_PVSC=' . $_PVSC . '\');">연동취소</a>
							';
						}else{
							$action_btn = "사용불가";
						}
					?>
						<tr>
							<td class="this_num"><?php echo $_num ; ?></td>
							<td class="this_state">
								<div class="lineup-row type_center">
									<?php echo $order_pay_status_html . $order_status_html; ?>
								</div>
							</td>
							<td>
								<div class="lineup-row type_center">
									<div class="t_blue"><?php echo ($v['o_ordernum'] ? $v['o_ordernum'] : ($v['ob_ordernum']>0 ? '<div class="t_red">주문내역삭제</div>' : '<div class="t_light">주문번호미확인</div>')); ?></div>
									<strong><?php echo($v['o_deposit'] ? $v['o_deposit'] : $v['ob_ordername']); ?></strong>
								</div>
							</td>
							<td class="t_left">
								<dl class="title_conts_box">
									<dt><span class="hidden_tx">APIBOX 거래번호</span><?php echo $v['ob_tid']; ?></dt>
									<dd>
										<?php
											$v['ob_content'] = str_replace(array("\n", "\r"), '§§', trim($v['ob_content']));
											$ex = explode('§§', $v['ob_content']);
										?>
										<div>
											<?php foreach($ex as $ek=>$ev){ ?>
												<?php echo $ev; ?>
											<?php } ?>
										</div>
									</dd>
								</dl>
								<div class="dash_line"><!-- 점선라인 --></div>
								<div class="lineup-row type_multi">
									<textarea name="_memo" id="js_memo_<?php echo $v['ob_uid']; ?>" class="design" style="height:55px;" placeholder="관리용 메모"><?php echo stripslashes($v["ob_memo"]) ; ?></textarea><!-- autoHeight 클래스 삭제 -->
									<span class="c_btn h22 gray" style="height:55px"><input type="button" class="js_memo_save" data-uid="<?php echo $v['ob_uid']; ?>" value="메모저장"></span>
								</div>
							</td>
							<td class="t_right t_red">
								<?php echo number_format($v['ob_orderprice']); ?>원
							</td>
							<td class="this_date">
								<?php echo printDateInfo($v['ob_paydate']);?>
							</td>
							<td class="this_ctrl">
								<div class="lineup-row type_center">
									<?php echo$action_btn; ?>
								</div>
							</td>
						</tr>
				<?php
					}
				}
				?>
				</tbody>
			</table>

			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</div>
	</form>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

<SCRIPT>
	// - 검색엑셀 ---
	// - 전체선택해제 ---
	$(document).ready(function() {
		$("input[name=allchk]").click(function (){
			if($(this).is(':checked')){
				$('.class_bankuid').attr('checked',true);
			}
			else {
				$('.class_bankuid').attr('checked',false);
			}
		});
	});
	// - 전체선택해제 ---

	// 주문연동
	function open_orderpopup(_uid){
		var url = "_orderbanklog.order_pop.php?_uid="+_uid;
		window.open(url,"order_popup","width=1050,height=650,scrollbars=yes");
	}

	// 주문페이지 이동
	function goto_order(_url){
		window.open(_url,"bank_order","");
	}

	$(document).ready(function() {
		$('body').on( 'keyup', '.autoHeight', function (e){
			$(this).css('height', 'auto' );
			$(this).height( this.scrollHeight - 20 );
		});
		$('body').find( '.autoHeight' ).keyup();
	});

	// 메모저장하기
	$(document).on('click', '.js_memo_save', function(){
		var uid = $(this).data('uid')*1;
		var text = encodeURIComponent($('#js_memo_' + uid).val());
		var _url = '_orderbanklog.pro.php';
		$.ajax({
			url: '_orderbanklog.pro.php'
			,data: {_mode:'memo' , _uid:uid , _memo:text}
			,type: 'POST'
			,dataType:'TEXT'
			,success:function(data){
				if(data == 'success'){
					alert('정상적으로 수정되었습니다.');
					return false;
				}else{
					alert('메모 저장 도중 에러가 발생하였습니다.');
					return false;
				}
			}
		});
	});

</SCRIPT>


<?PHP
	include_once("wrap.footer.php");  //o_price_real
?>