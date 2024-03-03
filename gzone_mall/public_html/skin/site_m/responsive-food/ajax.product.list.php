<?php
// $ActiveListColClass: 리스트의 단수를 설정 합니다.
/*
	리스트형 보기는 if_col1을 사용하면되며
	리스트 형태가 1단이라면 기본 리스트 형태는 리스트형으로 하고 썸네일형 버튼을 클릭 하면 4단이 나오면 된다.
*/
if(count($res) > 0) {
?>
    <?php // 마지막 2단형 : .if_box2_type / 마지막 3단형 : .if_box3_type / 리스트형 : .if_list_type  ?>
	<div class="item_list <?php echo $ActiveListColClass; ?> <?php echo $list_type_class;?> <?php echo $list_type_pcclass;?> <?php echo $list_type_moclass;?>">
        <div class="layout_fix">
            <ul>
                <?php
                foreach($res as $k=>$v) {
                ?>
                    <li>
                        <?php
                            // $incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
                            $locationFile = basename(__FILE__); // 파일설정
                            include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
                        ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
	</div><!-- end item_list -->
<?php } else { ?>
    <?php // 내용없을경우 ?>
    <div class="c_none">
        <div class="layout_fix">
            <div class="gtxt">등록된 상품이 없습니다.</div>
        </div>
    </div>
<?php } ?>