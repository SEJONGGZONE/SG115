<?php include_once('inc.header.php'); ?>

<?php // 헤더 ================================= ?>
<div class="header">

	<div class="site_name">
		<a href="#none" onclick="return false;" class="btn_menu type_ctrl js-menu-ctl" title="메뉴닫기"></a>
		<a href="<?php echo OD_ADMIN_URL; ?>" class="link"><!-- <?php echo $siteInfo['s_adshop']; ?> -->Total Admin</a>
	</div><!-- end site_name -->

	<div class="nav">
		<div class="wrapping">
			<div class="swipe_box" ID="js_swipe_admin_depth1">
				<ul>
					<?php
					$res_depth1 = _MQ_assoc("select *
												from smart_admin_menu as depth1 where am_view = 'Y' and am_depth = '1' order by am_idx asc");

					foreach($res_depth1 as  $depth1_key=>$depth1_value) {

						// -- 권한체크
					//	if($siteAdminMenuSet[$depth1_value['am_uid']] !== true ){ continue; }

						// -- 1뎁스에서 볼수 있는 페이지를 가져온다.
						$resChkDepth2 = _MQ_assoc("select am_uid from smart_admin_menu where find_in_set(am_uid,'".implode(",",array_keys($siteAdminMenuSet))."') > 0 and am_view = 'Y' and am_depth = '2' and am_parent = '".$depth1_value['am_uid']."' order by am_idx asc");
					//	if( count($resChkDepth2) < 1){ continue; }
						foreach($resChkDepth2 as $k2=>$v2){
							$rowChkDepth3 = _MQ("select am_uid, am_link from smart_admin_menu where find_in_set(am_uid,'".implode(",",array_keys($siteAdminMenuSet))."') > 0 and find_in_set('".$v2['am_uid']."',am_parent) > 0 and am_view = 'Y' and am_depth = '3'   order by am_idx asc ");
							//if( count($rowChkDepth3) < 1){ continue; }
							//else{ $depth1_value['am3_link'] = $rowChkDepth3['am_link']; $depth1_value['am3_uid'] = $rowChkDepth3['am_uid']; break; }
							$depth1_value['am3_link'] = $rowChkDepth3['am_link']; $depth1_value['am3_uid'] = $rowChkDepth3['am_uid']; break;
						}

						$depth_hit = false;
						if( $depth1_value['am_uid'] == $current_page_info['am1_uid']  ){	$depth_hit = true;}

						if( $depth1_value['am3_link'] != '') $chkLink = explode("?",$depth1_value['am3_link']);
						// -- 파일체크
						if( $depth1_value['am3_link'] == '' || is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false){ $depth1_value['am3_link'] = '_blank.php';  }
					?>
						<li class="<?=$depth_hit === true ? 'hit':''?>">
							<a href="<?php echo OD_ADMIN_URL.'/'.$depth1_value['am3_link'].(strpos($depth1_value['am3_link'],'?')!==false?'&':'?').'menuUid='.$depth1_value['am3_uid']; ?>" class="menu"><strong><?php echo $depth1_value['am_name']; ?></strong></a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div><!-- end wrapping -->
		<script>
			// 아이스크롤 스크립트(스와이프)
			// js_swipe_블라블라 / #js_swipe_블라블라 => 의미에 맞게 동일하게변경 (아래 ID 동일하게 변경)
			var scrollWidth = 0, scrollIndex = 1, wrapper = document.getElementById('js_swipe_admin_depth1'), testScroll = '';

			// 스와이프 적용
			$(document).ready(function(){
				swipe_Menu1/*숫자만변경*/();
			});

			function swipe_Menu1/*숫자만변경*/(){
				$.each($('#js_swipe_admin_depth1 li'), function(k, v){  scrollWidth += $('#js_swipe_admin_depth1 li').eq(k).outerWidth()*1; });
				var len = $('#js_swipe_admin_depth1 li').length;
				swipe_Scroll1/*숫자만변경*/ = new IScroll('#js_swipe_admin_depth1', {
					'click':true, 'probeType': 3, 'bindToWrapper':true, 'scrollX': true, 'scrollY': false
					, mouseWheel: true
				});

				if(scrollIndex > 0 && $('#js_swipe_admin_depth1 li.hit').length > 0) {
					var scrollOffset = ( $(window).width()*1/2 - $('#js_swipe_admin_depth1 li.hit').outerWidth()*1/2 ) * -1;
					swipe_Scroll1/*숫자만변경*/.scrollToElement(document.querySelector('#js_swipe_admin_depth1 li.hit'), 500, scrollOffset);
				}
			}
		</script>
	</div><!-- end nav -->

	<div class="right_btn">
		<ul>
			<li class="this_id">
				<?php if( $siteAdmin['a_type'] == 'master') {  // -- 전체관리자일경우 -- ?>
					<div class="myid" ><?=$siteAdmin['a_id']?></div>
				<?php }else{ // -- 일반관리자일 경우 -- ?>
					<div class="myid"><?=$siteAdmin['a_id']?></div>
				<?php } ?>
				<a href="logout.php" class="btn_logout">Logout</a>
			</li>
			<li class="this_hide this_home"><a href="/" class="btn btn_home" target="_blank" title="내쇼핑몰"></a></li>
			<li class="this_hide"><a href="http://www.onedaynet.co.kr/manual/hyssence_plus/pages/set_1.html" class="btn btn_menual" target="_blank" title="매뉴얼 보기"></a></li>
			<li class="this_hide"><a href="#none" class="btn btn_more js_onoff_event" data-target=".header" data-add="if_open_quick" onclick="return false;" title="자주찾는메뉴"></a></li>
		</ul>
	</div><!-- end right_btn -->

	<div class="quick_menu">
		<?php
			$favMenu = _MQ_assoc(" select fm_uid, fm_menuName from smart_favmenu where fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_depth = '1' and fm_view = 'Y' order by fm_menuIdx asc ");
			if(sizeof($favMenu) > 0){
				foreach($favMenu as $_favk=>$_favv){
					// 세부 메뉴 추출
					$_favq = "
						select fm.fm_menuName, am.am_link , am.am_uid
						from smart_favmenu as fm
						left join smart_admin_menu as am on (fm.fm_menu = am.am_uid)
						left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
						where 1
							and fm.fm_appId = 'totaladmin'
							and fm.fm_admin = '". $siteAdmin['a_uid'] ."'
							and fm.fm_depth = '2'
							and fm.fm_parent = '".$_favv['fm_uid']."'
							and am.am_uid > '0'
							and am.am_view = 'Y'
							and am.am_link != ''
							". ($siteAdmin['a_uid']<>1 ? " and ams.ams_uid > '0' "  : null) ."
						group by fm.fm_uid
						order by fm.fm_menuIdx asc
					";
					$_favr = _MQ_assoc($_favq);
			?>
			<div class="menu_list">
				<div class="ctg1"><?php echo stripslashes($_favv['fm_menuName']); ?></div>
				<?php
					if(sizeof($_favr)>0){
						echo '<ul>';
						foreach($_favr as $_favk2=>$_favv2){
							echo '<li><a href="'. OD_ADMIN_DIR . '/' . $_favv2['am_link'] .(strpos($_favv2['am_link'],'?')!==false?'&':'?').'menuUid='. $_favv2['am_uid'] .'" class="ctg2">'. $_favv2['fm_menuName'] .'</a></li>';
						}
						echo '</ul>';
					}else{
						echo '<div class="none_menu">메뉴 미설정</div>';
					}
				?>

			</div>
		<?php
			}
		}
		?>
		<a href="<?php echo OD_ADMIN_DIR; ?>/_config.favmenu.list.php" class="btn_set">메뉴 설정하기</a>
	</div><!-- end quick_menu -->

</div><!-- end header -->




<?php // 컨텐츠 ================================= ?>
<?php if( $CURR_FILENAME != '_main.php' && count($current_page_info) > 0  ) {  ?>
<div class="container">

	<div class="aside">
		<div class="page_name">
			<strong><?=$current_page_info['am1_name']?></strong>
			<a href="#none" onclick="return false;" class="btn_close js-menu-ctl" title="메뉴닫기"></a>
		</div>
		<div class="ctg_box">
			<ul class="ul">
			<?php
				$res_depth2 = _MQ_assoc("select *from smart_admin_menu where am_view = 'Y'  and am_depth = '2' and am_parent = '".$current_page_info['am1_uid']."' order by am_idx asc  ");

				foreach($res_depth2 as $depth2_key=>$depth2_value) {

					if($siteAdminMenuSet[$depth2_value['am_uid']] !== true ){ continue; }

					$res_depth3 = _MQ_assoc("select *from smart_admin_menu where am_view = 'Y'  and am_depth = '3' and find_in_set('".$depth2_value['am_uid']."',am_parent) > 0 order by am_idx asc  ");

					$curr_depth2_chk = true;
					$curr_depth2_chk = count($res_depth3) > 0 && $depth2_value['am_uid'] ==  $current_page_info['am2_uid'] ? true : null; // 다 열지 않을 시 주석해제
			?>
				<li class="li js-sub-menu-container <?echo $curr_depth2_chk === true ? 'if_open':null ?> <?php echo count($res_depth3) < 1 ? 'no-depth3': null ?>" data-amuid="<?php echo $depth2_value['am_uid'] ?>">
					<a href="#none" onclick="return false;" class="ctg2 js-sub-menu-ctl" data-amuid="<?php echo $depth2_value['am_uid']?>"><?php echo $depth2_value['am_name']?></a>
					<?php if( count($res_depth3)  > 0 ) { ?>
					<div class="depth3_box">
						<ul>
						<?php
							foreach($res_depth3 as $depth3_key => $depth3_value) {
								unset($chkLink);
								if($siteAdminMenuSet[$depth3_value['am_uid']] !== true ){ continue; }
								$curr_depth3_chk = $depth3_value['am_uid'] ==  $current_page_info['am3_uid'] ? true : null;
								$chk_nonepage = false;

								if( $depth3_value['am_link'] != '') $chkLink = explode("?",$depth3_value['am_link']);

								if( $depth3_value['am_link'] == '' || is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false){ $depth3_value['am_link'] = '_blank.php'; $chk_nonepage = true; }
						?>
							<li class="<?php echo $curr_depth3_chk === true ? 'hit' : '' ?>"><a href="<?php echo OD_ADMIN_URL.'/'.$depth3_value['am_link'].(strpos($depth3_value['am_link'],'?')!==false?'&':'?').'menuUid='.$depth3_value['am_uid']; ?>" class="ctg3"><?php echo $depth3_value['am_name'] ?><?php echo $chk_nonepage === true  ? '<font color="red">(X)</font>':null?></a></li>
						<?php } ?>
						</ul>
					</div><!-- end depth3_box -->
					<?php } ?>
				</li>
			<?php } ?>
			</ul>
		</div><!-- end ctg_box -->
	</div><!-- end aside -->
	<div class="aside_bg_close js-menu-ctl" onclick="return false;"></div>

	<div class="section<?php echo in_array($CURR_FILENAME, array('_config.sms.out_send_list.php','_config.sms.out_list.php')) == true ? ' if_hidden':'' ?> <?php echo preg_match("/(^_cntlog).|(^_static)|(^_addons)/",$CURR_FILENAME)>0?'if_hidden':'';?>">

		<div class="page_top">
			<div class="tit"><?php echo $app_current_name != '' ? $app_current_name : $current_page_info['am3_name']; ?></strong></div>
			<?php if($ManualLink[$MLink]) { ?>
				<a href="<?php echo $ManualLink[$MLink]; ?>" class="m_btn" target="_blank" title="매뉴얼보기"></a>
			<?php } ?>
			<a href="#none" onclick="return false;" class="btn_open_sub js-menu-ctl">MENU</a>
			<span class="location"><?php echo implode(" > ", array($current_page_info['am1_name'], $current_page_info['am2_name'], $current_page_info['am3_name']) ) ?></span>
		</div><!-- end page_top -->

<?php } ?>