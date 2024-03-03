<!-- ● 단락타이틀 -->
<div class="group_title"><strong>위치정보 설정</strong></div>


<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
<div class="data_form">
	<table class="table_form">
		<colgroup>
			<col width="180"><col width="*"><col width="180"><col width="*">
		</colgroup>
		<tbody>

			<tr>
				<th>주소설정</th>
				<td colspan="3">
                    <div class="lineup-row type_multi">
						<input type="text" name="_com_juso" id="_com_juso" value="<?php echo $row['p_com_juso'] ?>" class="design" style="width:350px;" placeholder="직접입력 또는 주소찾기" />
						<a href="#none" onclick="new_post_view('product_address'); return false;" class="c_btn h28 black">주소찾기</a>
					</div>
					 <div class="lineup-row">
					 	<div class="dash_line"><!-- 점선라인 --></div>
						<a href="#none" onclick="return false;" class="c_btn blue line js_get_com_juso">입점업체 정보로 적용하기</a>
						<input type="hidden" name="_com_juso_old" id="_com_juso_old" value="<?php echo $row['p_com_juso']; ?>">
						<label class="design">
							<?php /*
								<input type="checkbox" class="js_apply_mapxy" name="_apply_mapxy"<?php echo $row['p_apply_mapxy'] == '' ? ' checked':null ?> value="Y">
							*/ ?>
							<input type="checkbox" class="js_apply_mapxy" name="_apply_mapxy" <?php echo $row['p_apply_mapxy'] == 'Y' ? ' checked':null ?>  value="Y">좌표로 설정하기
						</label>
					</div>
					<div class="lineup-column type_auto js_view_mapxy" style="display: none;">
						<div class="dash_line"><!-- 점선라인 --></div>
                        <div class="lineup-row type_multi">
                            <span class="fr_tx">X좌표(경도)</span>
                            <input type="text" name="_com_mapx" value="<?php echo $row['p_com_mapx'] ?>" style="width:280px;" placeholder="X좌표(경도)" class="design" />
                            <input type="hidden" name="_com_mapx_old" value="<?php echo $row['p_com_mapx']; ?>">
						</div>
						<div class="lineup-row type_multi">
                            <span class="fr_tx">Y좌표(위도)</span>
                            <input type="text" name="_com_mapy" value="<?php echo $row['p_com_mapy'] ?>" style="width:280px;" placeholder="Y좌표(위도)" class="design" />
                            <input type="hidden" name="_com_mapy_old" value="<?php echo $row['p_com_mapy']; ?>">
                        </div>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div class="tip_box">
						<?php
							if( empty($siteInfo['kakao_api']) || empty($siteInfo['kakao_js_api'])){
								echo _DescStr("카카오 API 설정 후 이용 가능합니다.([환경설정 &gt; 운영 관리 설정 &gt; SNS 로그인/API 설정] 에서 설정가능)");
							}else{
								echo _DescStr("위치를 입력해두면 상세페이지에 사용처로 노출되고, 티켓 주문내역에서도 사용자가 사용장소를 확인할 수 있습니다.");
								echo _DescStr("좌표로 설정시에는 상품을 저장한 후에 지도를 확인할 수 있으며 좌표에 따른 주소는 수동으로 입력해 주셔야합니다.","red");
								echo _DescStr("주소 등록시 주변 경관을 설명하는 문구(OO주유소 근처, 교차로 부근 등)를 입력할 경우 검색이 되지 않을 수 있으니 주의하시기 바랍니다.");
								echo _DescStr("주소설정 시 자동으로 좌표가 설정됩니다.");
								echo _DescStr("좌표로 설정할 경우 해당 좌표 기준으로 지도상 표기되며 주소는 변경되지 않습니다.");
							}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<th>장소이름</th>
				<td>
					<input type="text" name="_com_locname" class="design" placeholder="장소이름" value="<?php echo $row['p_com_locname']?>" style="">
					<div class="c_tip">위치정보 하단에 노출 됩니다.(미 입력시 업체명이 노출됩니다.)</div>
				</td>
				<th>전화번호</th>
				<td>
					<input type="text" name="_com_tel" class="design" placeholder="전화번호" value="<?php echo $row['p_com_tel']?>" style="">
					<div class="c_tip">위치정보 하단에 노출 됩니다.</div>
				</td>
			</tr>
			<tr class="js_view_map" style="display: none;">
				<th>지도</th>
				<td colspan="3">
			        <div class="map_box" id="product_map_view" data-type="kakao" style="width:600px; height: 300px;">
					</div>
				</td>
			</tr>
			<tr>
				<th>운영시간</th>
				<td colspan="3">
					<textarea type="text" name="_com_time" class="design" placeholder="운영시간" rows="3"><?php echo $row['p_com_time']; ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
		include_once OD_ADDONS_ROOT."/newpost/newpost.search_m.php";
	?>

	<?php if( $siteInfo['kakao_js_api'] != ''){?>
	<script type = "text/javascript" id="sc" src = "//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $siteInfo['kakao_js_api']; ?>&libraries=services" ></script>
	<?php } ?>
	<script type="text/javascript">
		$(document).ready(function(){
			var _apply_mapxy = $('.js_apply_mapxy').prop('checked');
			if( _apply_mapxy == true){ // 체크할 경우
				$('.js_view_mapxy').show();
			}else{
				$('.js_view_mapxy').hide();
			}


		});
		function mapInit(id, addr) {
			try{
			   if( !id ){ id = "map"}
			   var map = {};
			   var marker_option = {level : 5};
				marker_option.lng = $('[name="_com_mapx"]').val();
				marker_option.lat = $('[name="_com_mapy"]').val();
				if( marker_option.lng != '' && marker_option.lat != ''){
					$('.js_view_map').show();
				}else{
					$('.js_view_map').hide();
					return false;
				}
			    var windowWidth = $(window).width();
			    $("#"+id).empty();
		        var mapContainer = document.getElementById(id), // 지도를 표시할 div
		            mapOption = {
		                center: new daum.maps.LatLng(marker_option.lat, marker_option.lng), // 지도의 중심좌표
		                level:marker_option.level // 지도의 확대 레벨
		            };
		        map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다
				var markerPosition  = new kakao.maps.LatLng(marker_option.lat, marker_option.lng);
				var marker = new kakao.maps.Marker({
				    position: markerPosition
				});
				marker.setMap(map);
			}catch(e){
				console.log(e);
				// alert("지도로드에 실패하였습니다.");
			}
	        // map.setZoomable(false); // :: option : 줌 사용 못하게 할 경우
		}

		function mapPositionUpdate(){
			var check = $('[name="_apply_mapxy"]').prop('checked');
			if( check === true){ mapInit('product_map_view'); return false; }
			var address = $('[name="_com_juso"]').val();
			$.ajax({
				url:'<?php echo OD_PROGRAM_URL; ?>/_pro.php',
				data:{_mode:'get_addr_position','address':address},
				dataType:'json',
			})
			.done(function(e){
				$('[name="_com_mapx"]').val(e.mapx);
				$('[name="_com_mapy"]').val(e.mapy);
				mapInit('product_map_view');
			});
		}
		$(document).ready(function(){
			mapPositionUpdate();
		});
		$(document).on('change','.js_apply_mapxy',function(){
			var chk = $(this).prop('checked');
			if( chk == true){ // 체크할 경우
				$('.js_view_mapxy').show();
			}else{
				$('.js_view_mapxy').hide();
			}
		});
		$(document).on('change','#_com_juso',function(){
			mapPositionUpdate();
		});

		$(document).on('click','.js_get_com_juso',function(){
			var comid = $('select[name="_cpid"]').val();
			$.ajax({
				url:'_product.inc_ajax.php',
				data:{_mode:'get_com_juso','comid':comid},
				dataType:'json',
			})
			.done(function(e){
				try{
					if( e.rst != 'success'){ alert(e.msg); return false; }
					$('input[name="_com_juso"]').val(e.address);
					$('input[name="_com_tel"]').val(e.tel);
					$('input[name="_com_locname"]').val(e.locname);
					mapPositionUpdate();

				}catch(e){
					console.log(e);
					alert('공급업체 정보호출에 실패하였습니다.');
				}
			})
			.fail(function(e,v){
				console.log(e);
				alert("공급업체 정보호출에 실패하였습니다.");
				return false;
			});
		});
	</script>

</div>
