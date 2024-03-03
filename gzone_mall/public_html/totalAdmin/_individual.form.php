<?php // -- LCY :: 2017-09-20 -- 운영자관리 폼
		$app_current_link = '_individual.list.php';
		$app_current_name = "회원 등록";
		include_once('wrap.header.php');
		if( in_array($_mode,array('modify','add')) == false){
			error_loc_msg("_individual.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");
		}

		// -- 모드별 처리
		if( $_mode == 'modify'){ // 수정일 시
			$row = _MQ("select *from smart_individual where in_id = '".$_id."'  ");
			if( count($row) < 1){ error_loc_msg("_individual.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "회원 정보가 없습니다." ); }

			// -- 소셜연동 체크
			if( $row['sns_join'] != 'N'){
				if($row['fb_join'] == 'Y'){ $arrSnsType[] = 'F';  }
				if($row['ko_join'] == 'Y'){ $arrSnsType[] = 'K'; }
				if($row['nv_join'] == 'Y'){ $arrSnsType[] = 'N'; }
				if($row['ap_join'] == 'Y'){ $arrSnsType[] = 'A'; }
				if($row['go_join'] == 'Y'){ $arrSnsType[] = 'G'; }
			}

			// -- 로그인 횟수를 통해 방문 횟수를 구한다.
			$rowVisit = _MQ("select  count(distinct left(lc_rdate,10)) as cnt from smart_loginchk where lc_mid = '".$row['in_id']."' ");

			// 회원등급 처리
			if( $row['in_mgsuid'] == 0 ){
				$rowMgs = _MQ("select mgs_uid from smart_member_group_set where 1  order by mgs_idx asc, mgs_rank asc limit 0,1"); // 가장낮은등급을 가져온다.
				if( $rowMgs['mgs_uid'] != ''){ // 그룹이 최소 한개가 안될일은 없지만 혹시라도 있을경우를 대비
					_MQ_noreturn("update smart_individual set in_mgsuid = '".$rowMgs['mgs_uid']."', in_mgsdate = now()  where in_id = '".$row['in_id']."' ");
					$row['in_mgsuid'] = $rowMgs['mgs_uid'];
				}
			}
		}

		// -- 회원등급 선택을 위한 처리
		$resMgs = _MQ_assoc("select *from smart_member_group_set where 1  order by mgs_idx asc, mgs_rank asc ");
		$arrGroupInfo = array();
		foreach( $resMgs as $k=>$v){
			$arrGroupInfo[$v['mgs_uid']] = $v['mgs_name'];
		}

?>

<form action="_individual.pro.php" name="frm" id="frm"  method="post" >
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="_mode" value="<?php echo $_mode?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="tempID" value="<?php echo $row['in_id']?>"> <?php // -- ajax 모드 ?>
	<?php if($_mode == 'modify') { ?>
		<input type="hidden" name="_id" value="<?php echo $row['in_id']?>">
	<?php } ?>

	<div class="group_title"><strong>회원 필수정보</strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>승인여부</th>
					<td>
						<?php echo _InputRadio( '_auth' , array('Y','N'), (in_array($row['in_auth'], array('Y','N')) == false ? 'N':$row['in_auth']) , '' , array('승인', '미승인') , ''); ?>
					</td>
					<th class="">회원등급</th>
					<td>
						<?php echo _InputSelect( "_mgsuid" , array_keys($arrGroupInfo) , $row['in_mgsuid'] , " class='' " , array_values($arrGroupInfo), '' ); ?>
					</td>
				</tr>
				<tr>
					<th class="ess">아이디</th>
					<td>
						<?php if($_mode == 'add'){ ?>
						<input type="text" name="_id" class="design" style="" value="" placeholder="아이디"/>
						<?php echo _DescStr('아이디는 등록 후 수정이 불가합니다.','red'); ?>
						<?php }else{echo $row['in_id']; }?>
					</td>
					<th class="ess">이름</th>
					<td>
						<input type="text" name="_name" class="design" style="" value="<?php echo $row['in_name'] ?>" placeholder="이름">
					</td>
				</tr>
				<tr>
					<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호</th>
					<td>
						<input type="password" name="_pw" class="design" style="width:130px;" value="" placeholder="비밀번호(4자리 이상 권장)"/>
						<?php if($_mode == 'modify'){ ?><?php echo _DescStr('변경시에만 입력해주세요.', ''); ?><?php } ?>
					</td>
					<th <?php $_mode == 'add' ? 'class="ess"':''  ?>>비밀번호 확인</th>
					<td>
						<input type="password" name="_rpw" class="design" style="width:130px;" value="" placeholder="비밀번호 확인(동일하게 한번 더)">
					</td>
				</tr>
				<tr>
					<th class="ess">휴대폰</th>
					<td>
						<input type="text" name="_tel2" class="design" value="<?=$row['in_tel2']?>" placeholder="휴대폰"/>
						<div class="clear_both"></div>
						<?php echo _InputRadio( '_smssend' , array('Y','N'), (in_array($row['in_smssend'], array('Y','N')) == false ? 'N':$row['in_smssend']) , '' , array('수신허용', '수신거부') , ''); ?>
					</td>
					<th class="ess">이메일</th>
					<td>
						<input type="text" name="_email" class="design" value="<?=$row['in_email']?>" placeholder="이메일">
						<div class="clear_both"></div>
						<?php echo _InputRadio( '_emailsend' , array('Y','N'), (in_array($row['in_emailsend'], array('Y','N')) == false ? 'N':$row['in_emailsend']) , '' , array('수신허용', '수신거부') , ''); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>회원 부가정보</strong><!-- 메뉴얼로 링크 --> </div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>성별</th>
					<td>
						<?php echo _InputRadio( '_sex' , array('M','F'), $row['in_sex'] , '' , array('남성', '여성') , '');?>
					</td>
					<th>생년월일</th>
					<td>
						<div class="lineup-row type_date">
							<input type="text" name="_birth" class="design js_pic_day" style="width:90px;" value="<?=rm_str($row['in_birth']) < 1 ? '': $row['in_birth'] ?>" readonly placeholder="날짜선택">
						</div>
					</td>
				</tr>
				<tr>
					<th>주소</th>
					<td>
						<div class="lineup-row">
							<input type="text" name="_zonecode" value="<?=$row['in_zonecode']?>" id="_zonecode" class="design" style="width:90px" readonly onclick="new_post_view(); return false;" placeholder="우편번호">
							<a href="#none" onclick="new_post_view(); return false;" class="c_btn h28 black">주소찾기</a>
						</div>
						<div class="lineup-column type_auto">
							<input type="text" name="_address_doro" value="<?=$row['in_address_doro']?>" class="design" readonly id="_addr_doro" placeholder="주소검색" onclick="new_post_view(); return false;">
							<input type="text" name="_address2" id="_addr2" class="design" style="" value="<?=$row['in_address2']?>" placeholder="나머지 주소">

							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="lineup-row type_multi">
								<span class="fr_tx">지번주소</span>
								<input type="hidden" name="_zip1" id="_post1" value="<?=$row['in_zip1']?>" class="design t_center" style="width:50px">
								<input type="hidden" name="_zip2" id="_post2" value="<?=$row['in_zip2']?>" class="design t_center" style="width:50px">
								<input type="text" name="_address1" id="_addr1" class="design" style="" value="<?=$row['in_address1']?>" readonly placeholder="지번주소(자동입력)" onclick="new_post_view(); return false;"/>
							</div>
						</div>
					</td>
					<th>전화번호</th>
					<td>
						<input type="text" name="_tel" class="design" value="<?=$row['in_tel']?>" placeholder="전화번호"/>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>환불 계좌정보</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="">은행선택</th>
					<td>
						<div class="lineup-row type_multi">
							<?php echo _InputSelect( "_cancel_bank" , array_keys($ksnet_bank) , $row['in_cancel_bank'] , " class='' " , array_values($ksnet_bank), '-은행선택-' ); ?>
							<input type="text" name="_cancel_bank_account" class="design" value="<?=$row['in_cancel_bank_account']?>" placeholder="계좌번호"/>
						</div>
					</td>
					<th class="">예금주명</th>
					<td>
						<input type="text" name="_cancel_bank_name" class="design" value="<?=$row['in_cancel_bank_name']?>" placeholder="예금주명"/>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php if($_mode == 'modify') { ?>
		<div class="group_title"><strong>회원 활동정보</strong></div>
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>소셜연동</th>
						<td colspan="3">
							<?php echo _InputCheckbox( 'arrSnsType' , array('F','K','N','A','G'), (($arrSnsType)) , ' disabled ' , array('페이스북','카카오','네이버','애플','구글') , '') ?>
						</td>
					</tr>
					<tr>
						<th>가입일</th>
						<td>
							<?php echo $row['in_rdate'];?>
						</td>
						<th>가입경로</th>
						<td>
							<?php echo $row['in_join_ua']; ?>
						</td>
					</tr>
					<tr>
						<th>최종 로그인</th>
						<td>
							<?php echo $row['in_ldate'];?>
						</td>
						<th>수신동의/거부 변경일</th>
						<td>
							<?php echo (rm_str($row['m_opt_date']) < 1 ? '없음':$row['m_opt_date']);?>
						</td>
					</tr>
					<tr>
						<th>적립금</th>
						<td>
							<?php echo number_format($row['in_point']).'원';?>
						</td>
						<th>방문횟수</th>
						<td>
							<?php echo number_format($rowVisit['cnt']).'회';?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>


	<?php if( $_mode == 'modify') { ?>

		<!-- ●단락타이틀 -->
		<div class="group_title"><strong>주문정보</strong></div>
		<?php
			if(!$pass_limit) {$pass_limit = 99999;}
			$listmaxcount = $pass_limit ;
			if( !$listpg ) {$listpg = 1 ;}
			$count = $listpg * $listmaxcount - $listmaxcount;

			$resOrder = _MQ_assoc("select * from smart_order where 1 and  o_canceled='N' and o_paystatus='Y' and  o_mid ='".$row[in_id]."' order by o_rdate desc ");
			$TotalCount = count($resOrder);
			$Page = ceil($TotalCount / $listmaxcount);
		?>
		<div class="data_list">
			<table class="table_list type_nocheck">
				<colgroup>
					<col width="80"/><col width="120"/><col width="150"/><col width="*"/><col width="150"/><col width="100"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">접속기기</th>
						<th scope="col">주문번호</th>
						<th scope="col">주문상품</th>
						<th scope="col">구매일시</th>
						<th scope="col">주문상세</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($resOrder as $k=>$v) {
						$_num = $TotalCount - $count - $k ; // NO 표시


						// -- 모바일구매여부
						$printAgent = $v['mobile'] == 'Y' ? '<span class="c_tag h18 mo">MO</span>':'<span class="c_tag h18 t3 pc">PC</span>';

						// -- 주문상품 정보를 가져온다.
						$arrPrintProduct =  array();
						$printAddPrdocut = '';
						$resOrderProduct = _MQ_assoc("select op_pcode, op_pname, op_pouid from smart_order_product where op_oordernum = '".$v['o_ordernum']."' group by op_pcode ");
						if(count($resOrderProduct) > 0){
							if(count($resOrderProduct) > 1){ $printAddPrdocut = '이외 '.(count($resOrderProduct)-1).'개'; }
							$arrPrintProduct[] = ''.$resOrderProduct[0]['op_pname'].' '.$printAddPrdocut.'';

						}else{
							$arrPrintProduct[] = '상품정보가 없습니다.';
						}

						$printProduct = implode("",$arrPrintProduct);
						$printBtn = '<a href="_order.form.php?_mode=modify&_ordernum='.$v['o_ordernum'].'" class="c_btn h22" target="_blank">상세보기</a>';

				?>
					<tr>
						<td class="this_num"><?=$_num?></td>
						<td class="this_state">
							<?php echo $printAgent; ?>
						</td>
						<td class="t_blue t_bold">
							<?=$v['o_ordernum']?>
						</td>
						<td class="t_left">
							<?php echo $printProduct?>
						</td>
						<td class="this_date">
							<?php echo printDateInfo($v['o_rdate'])?>
						</td>
						<td class="this_ctrl">
							<div class="lineup-row type_center"><?php echo $printBtn; ?></div>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<?php	if(count($resOrder) < 1 ) {   ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">구매내역이 없습니다.</div></div>
			<?php } ?>

		</div>



		<div class="group_title" data-name="view-form"><strong>접속정보</strong></div>
		<div class="data_list">
			<table class="table_list type_nocheck">
				<colgroup>
					<col width="80"/><col width="130"/><col width="120"/><col width="120"/>
					<col width="*"/><col width="150"/>
					<col width="200"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col" class="colorset" >No</th>
						<th scope="col" class="colorset" >접속기기</th>
						<th scope="col" class="colorset" >IP</th>
						<th scope="col" class="colorset" >검색어</th>
						<th scope="col" class="colorset" >유입경로</th>
						<th scope="col" class="colorset" >유입일</th>
						<th scope="col" class="colorset" >브라우저</th>
					</tr>
				</thead>
				<tbody class="view-visit-list">

				</tbody>
			</table>
			<div class="common_none view-visit-list-none" style="display:none;"><div class="no_icon"></div><div class="gtxt">접속 정보가 없습니다.</div></div>
			<div class="paginate view-visit-paginate"></div>
		</div>


		<?php 
			// KAY : 2023-04-10 : 신고하기 기능 추가
			$row_block_total = _MQ_result("SELECT count(*) as cnt from smart_individual_block where ib_inid = '".$row['in_id']."' ");
			$res_block_list = _MQ_assoc("select *from smart_individual_block where ib_inid = '".$row['in_id']."' ");
			if($row_block_total>0){
		?>
			<!-- ●단락타이틀 -->
			<div class="group_title"><strong>리뷰 차단내역 (<?php echo $row_block_total;?>명)</strong></div>
				<div class="data_list">
					<table class="table_must">
						<colgroup>
                            <col width="60"/><col width="*"/><col width="95"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset" >No</th>
								<th scope="col" class="colorset" >차단자</th>
								<th scope="col" class="colorset" >차단일</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								foreach($res_block_list as $kk => $vv){
									$_num = $row_block_total-$kk;
							?>
							<tr>
								<td><?php echo $_num;?></td>
								<td><?php echo $vv['ib_block_inid'];?></td>
								<td><?php echo printDateInfo($vv['ib_rdate']);?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
			</div>
		<?php }?>

	<?php } ?>


	<?php echo _submitBTN('_individual.list.php'); ?>

</form>



	<div class="ajax-data-box" data-visit-ahref=""></div>
	<script>
	$(document).ready(function(){	viewVisitList();})
	$(document).on('click','.paginate .lineup a',function(){
		var ahref = $(this).attr('href');
		var hasHit = $(this).hasClass('hit');
		$(this).removeAttr("href");// 링크 클릭되지 않도록 href 엘리멘트 제거

		$('.ajax-data-box').attr('data-visit-ahref',ahref);
		if(hasHit == true){ return false; }
		else{
			viewVisitList();
		}
	});


	// 접속정보를 가져온다.
	function viewVisitList()
	{

		var _id = $('[name="tempID"]').val();
		var ajaxMode = 'viewVisitList';
		var ahref = $('.ajax-data-box').attr('data-visit-ahref');
		var result = $.parseJSON($.ajax({
			url: "_individual.ajax.php",
			type: "get",
			dataType : "json",
			data: {_id :_id , ajaxMode : ajaxMode , ahref : ahref},
			async: false
		}).responseText);

		if(result == undefined){ return false; }
		if( (result.cnt*1) > 0) {
			$('.view-visit-list').html(result.html);
		}else{
			$('.view-visit-list-none').show();
		}

		// -- 페이지네이트
		$('.view-visit-paginate').html(result.paginate);

	}

	// 폼 유효성 검사
	$(document).ready(function(){

		// - 이메일 검증
		jQuery.validator.addMethod("email_check", function(value, element) {
			var pattern = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/i;
			return this.optional(element) || pattern.test(value);
		}, "이메일 형식이 유효하지않습니다.");


		$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					_id: { required: true }
					,_name: { required: true }
					<?php if($mode == 'add'){ ?>
					,_pw: { required: true} }
					,_rpw: { required: true}
					<?php } ?>
					,_htel: { required: true }
					, _email: { required : true, email_check: true }
			},
			messages: {
					_id : { required: '아이디를 입력해 주세요.' }
					,_name : { required: '이름을 입력해 주세요.' }
					<?php if($mode == 'add'){ ?>
					,_pw : { required: '비밀번호를 입력해 주세요.' }
					,_rpw : { required: '비밀번혹 확인을 입력해 주세요.' }
					<?php } ?>
					,_htel : { required: '휴대폰번호를 입력해 주세요.' }
					, _email: { required : "이메일을 입력해 주세요.", email_check: "유효하지 않은 이메일 주소입니다" }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.

				if( $('input[name="_pw"]').val() != $('input[name="_rpw"]').val()){
					alert("입력하신 비밀번호와 확인 비밀번호가 일치하지 않습니다. ");
					return false;
				}

				form.submit();
			}
		});

	});

	</script>




<?php
	// 주소찾기 - 우편번호찾기 박스
	include_once OD_ADDONS_ROOT."/newpost/newpost.search_m.php";
	include_once('wrap.footer.php');
?>