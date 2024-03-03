<?php
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지

	*/

	// reload 일경우
	if( $_mode == 'reload'){
		$rowAdminMenu = _MQ("select *from smart_brand where c_uid = '".$_uid."'  ");
		$viewDepth = $rowAdminMenu['c_depth'];
	}

	switch($viewDepth)
	{
		case "1":
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 1 order by c_name");
		break;

		case "2":
			if($locUid1 != ''){ $viewUid = $locUid1; }
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 2 and c_parent = '".$viewUid."' order by c_name");
		break;

		case "3":
			if($locUid2 != ''){ $viewUid = $locUid2; }
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 3 and find_in_set('".$viewUid."',c_parent) order by c_name");
		break;
	}

?>
	<!-- 카테고리 목록박스 -->



<form name="brandlist" method="post" action="_brand.ajax_pro.php">
	<input type='hidden' name="_mode" value="mass_modify">

		<div class="js_thisform data_search" style="display:none">
			<table class="table_form">
				<colgroup>
					<col width="160"/><col width="*"/><col width="160"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>브랜드명</th>
						<td>
							<input type="text" name="ADD_name" value="" class="design" placeholder="브랜드명"  style="width:200px"/>
						</td>
						<th>노출여부</th>
						<td>
							<label for='ADD_view_Y' class='design'><input type='radio' id='ADD_view_Y' name='ADD_view' value='Y' class='_view' checked> 노출</label>
							<label for='ADD_view_N' class='design'><input type='radio' id='ADD_view_N' name='ADD_view' value='N' class='_view' > 숨김</label>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="saveAdminMenu(0); return false;" class="c_btn h23 blue" >추가하기</a></li>
				</ul>
			</div>
		</div>


		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="changeView('show'); return false;" class="c_btn gray line" >전체 노출</a>
				<a href="#none" onclick="changeView('hide'); return false;" class="c_btn gray line" >전체 숨김</a>
				<span class="c_btn h23 blue"><input type="submit" name="" value="일괄수정" /></span>
			</div>
		</div>


		<table class="table_list type_nocheck">
			<colgroup>
				<col width="160"/><col width="*"/><col width="160"/><col width="*"/>
			</colgroup>
			<thead>
				<th>노출여부</th>
				<th>브랜드명</th>
				<th>노출여부</th>
				<th>브랜드명</th>
			</thead>
			<tbody>
				<?php if(count($resAdminMenu) > 0) { ?>
					<tr>
						<?php
							foreach($resAdminMenu as $k=>$v){
								$amViewClass = $v['c_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
								$amViewName = $v['c_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명
								// 줄바꿈
								echo ( $k%2 == 0 && $k<> 0 ? "</tr><tr>" : "" );
						?>
						<!-- <td><span class="c_tag <?php echo $amViewClass?> h22 t2"><?=$amViewName?></span></td> -->
						<td>
							<div class='lineup-center'>
								<label for='_view_<?php echo $v['c_uid']?>_Y' class='design'><input type='radio' id='_view_<?php echo $v['c_uid']?>_Y' name='_view[<?php echo $v['c_uid']?>]' value='Y' class='_view' <?php echo ($v['c_view'] == "Y" ? "checked" : "")?>> 노출</label>
								<label for='_view_<?php echo $v['c_uid']?>_N' class='design'><input type='radio' id='_view_<?php echo $v['c_uid']?>_N' name='_view[<?php echo $v['c_uid']?>]' value='N' class='_view' <?php echo ($v['c_view'] == "N" ? "checked" : "")?>> 숨김</label>
							</div>
						</td>
						<td>
							<div class="lineup-row type_multi">
								<input type="text" name="_name[<?=$v['c_uid']?>]" value="<?=$v['c_name']?>" class="design" placeholder="브랜드 이름을 입력해주세요."  style="width:200px"/>
								<a href="#none" onclick="deleteAdminMenu('<?=$v['c_uid']?>'); return false;" class="c_btn h23 dark line scrollto" >삭제</a>
							</div>
						</td>
						<?php
							}
							$k++;
							if($k%2 <> 0 ) {
								echo '';
							}
						?>
					</tr>
				<?php }else{ ?>
					<tr>
						<td colspan="3">
							<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>


</form>