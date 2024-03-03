<?php // 티켓 지도보기 레이어 ?>
<div class="c_layer type_ticket_map">
    <div class="wrapping">
        <?php // 레이어 타이틀 ?>
        <div class="tit_box">
            <?php if( $row['p_com_locname'] != ''){?>
            <div class="tit"><?php echo $_com_locname; ?></div>
            <?php } ?>
            <a href="#none" onclick="return false;" class="btn_close js_onoff_event" data-target=".c_layer.type_ticket_map" data-add="if_open_layer" title="닫기"></a>
        </div><!-- end tit_box -->

        <div class="conts_box c_scroll_v">
            <div class="place_info">
                <div class="map_box" style="width:100%;" id="mapOrderView" data-level="<?php echo $execLoc == 'mypage' ? '4':'5' ?>" data-lat="<?php echo $row['p_com_mapy'] ?>" data-lng="<?php echo $row['p_com_mapx'] ?>" data-address="<?php echo $row['p_com_juso'] ?>">
                    
                </div>
                <div class="info_box">

                    <?php if( $row['p_com_tel'] != '') { ?>
                    <dl>
                        <dt>전화번호</dt>
                        <dd><a href="tel:<?php echo $row['p_com_tel']; ?>" class="tel"><?php echo $row['p_com_tel']; ?></a></dd>
                    </dl>
                    <?php } ?>
                    <dl>
                        <dt>주소</dt>
                        <dd><?php echo $row['p_com_juso']; ?></dd>
                    </dl>
                </div>
            </div>
        </div><!-- end conts_box -->
    </div>
    <div onclick="return false;" class="bg_close js_onoff_event" data-target=".c_layer.type_ticket_map" data-add="if_open_layer"></div>
</div><!-- end c_layer / type_ticket_map -->