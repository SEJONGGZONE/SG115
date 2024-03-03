	<?php
    	if($p_info['p_com_juso'] != '' && $p_info['p_type'] == 'ticket'){
    		// js_product_view_map_view
    ?>
    <?php // 티켓 사용처 위치정보 ?>
    <div class="place_info js_product_view_map_position">
        <div class="sub_tit"><span class="tit">사용처 위치정보</span></div>
        <div class="wrapping">

            <div class="map_box" style="width:100%;" id="mapProductView" data-level="<?php echo $execLoc == 'mypage' ? '4':'5' ?>" data-lat="<?php echo $p_info['p_com_mapy'] ?>" data-lng="<?php echo $p_info['p_com_mapx'] ?>" data-address="<?php echo $p_info['p_com_juso'] ?>">여기 지도 들어감</div>

            <div class="info_box">

                <?php // 장소이름 ?>
                <div class="place_name"><?php echo $_com_locname ?></div>

                <?php if( $p_info['p_com_tel'] != '') { ?>
                <dl>
                    <dt>전화번호</dt>
                    <dd><a href="tel:<?php echo $p_info['p_com_tel'] ?>" class="tel"><?php echo $p_info['p_com_tel'] ?></a></dd>
                </dl>
                <?php } ?>
                <dl>
                    <dt>주소</dt>
                    <dd><?php echo $p_info['p_com_juso']; ?></dd>
                </dl>

				<?php if($p_info['p_com_time']!=''){?>
					<dl>
						<dt>운영시간</dt>
						<dd><?php echo nl2br($p_info['p_com_time']); ?></dd>
					</dl>
				<?php }?>
            </div>
        </div>
    </div><!-- end place_info -->


	<script>

		function pages_map_view(id)
		{
			try{

				var mapData = $('#'+id).data();
				if( typeof mapData.lat == 'undefined') return false;

	            // 모든 트리거 이벤트는 이곳에 한번에 정의
				$this = this;
			    if( !id ){ id = "map"}
			    var map = {};
			    var windowWidth = $(window).width();
			    $("#"+id).empty();
		        var mapContainer = document.getElementById(id), // 지도를 표시할 div
		            mapOption = {
		                center: new daum.maps.LatLng(mapData.lat, mapData.lng), // 지도의 중심좌표
		                level:mapData.level // 지도의 확대 레벨
		                , scrollwheel : false,
		            };
		        map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다


				var markerPosition  = new kakao.maps.LatLng(mapData.lat, mapData.lng);
				var marker = new kakao.maps.Marker({
				    position: markerPosition
				});
				marker.setMap(map);


				// 지도 확대 축소를 제어할 수 있는  줌 컨트롤을 생성합니다
				var zoomControl = new kakao.maps.ZoomControl();
				map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);

				// map.setDraggable(false);

			}
			catch(e){
				console.log(e);
			}
		}
		$(function(){pages_map_view('mapProductView');});
	</script>



<?php } ?>