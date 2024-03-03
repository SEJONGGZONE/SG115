<?PHP
# LDD010
$app_mode = "popup";
include_once("inc.php");

############### 옵션 SORTing ############### 
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3 
// pass_uid  -옵션 고유번호
if( in_array($pass_type , array("insert")) && $pass_depth && $pass_uid ) {

    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from smart_product_addoption where pao_uid='" . $pass_uid ."' ");

    // 타켓 하위 순위 1 순위씩 밀림
    _MQ_noreturn(" update smart_product_addoption set pao_sort = pao_sort + 1 where pao_pcode='" . $pass_code . "' and pao_depth='". $pass_depth ."' ". ($target_r['pao_parent'] ? " and pao_parent = '". $target_r['pao_parent'] ."' " : "") . " and pao_sort > '". $target_r['pao_sort'] ."' ");


    switch ($pass_depth) {
        case '1':

            $max_sort = ($target_r['pao_sort'] + 1);

            // 항목추가 - 1차
            _MQ_noreturn("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='1',
                    pao_sort='". $max_sort ."'
            ");
            $uid_1depth = _MQ_insert_id();

            // 순번추출 - 2차
            $r2 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='2' and pao_parent='" . $uid_1depth . "' ");
            $max_sort2 = $r2['max_sort'] + 1;

            // 항목추가 - 2차
            _MQ_noreturn("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='2',
                    pao_parent='{$uid_1depth}',
                    pao_sort='". $max_sort2 ."'
            ");
            $uid_2depth = _MQ_insert_id();
			/*
            // 순번추출 - 3차
            $r3 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='3' and find_in_set('" . $uid_2depth . "' , pao_parent) > 0 ");
            $max_sort3 = $r3['max_sort'] + 1;

            // 항목추가 - 3차
            _MQ_noreturn("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='3',
                    pao_parent='{$uid_1depth},{$uid_2depth}',
                    pao_sort='". $max_sort3 ."'
            ");
			*/
        break;
        case '2':

            $max_sort = ($target_r['pao_sort'] + 1);

            // 항목추가 - 2차
            _MQ_query("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='2',
                    pao_parent='{$target_r['pao_parent']}',
                    pao_sort='". $max_sort ."'
            ");
            $uid_2depth = _MQ_insert_id($connect);
			/*
            // 순번추출 - 3차
            $r3 = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='3' and find_in_set('" . $uid_2depth . "' , pao_parent) > 0 ");
            $max_sort3 = $r3['max_sort'] + 1;

            // 항목추가 - 3차
            _MQ_query("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='3',
                    pao_parent='{$target_r['pao_parent']},{$uid_2depth}',
                    pao_sort='". $max_sort3 ."'
            ");
			*/
        break;
		/*
        case '3':

            $max_sort = ($target_r['pao_sort'] + 1);

            // 항목추가 - 3차
            _MQ_query("
                insert smart_product_addoption set
                    pao_pcode='{$pass_code}',
                    pao_poptionname='',
                    pao_depth='3',
                    pao_parent='{$target_r['pao_parent']}',
                    pao_sort='". $max_sort ."'
            ");
        break;
		*/
    }

    // 항목추가 - 2차
    //_MQ_noreturn(" insert smart_product_addoption set pao_pcode='" . $pass_code . "', pao_poptionname='', pao_depth='". $pass_depth ."', pao_parent='" . $target_r['pao_parent'] . "', pao_sort='". ($target_r['pao_sort'] + 1) ."' ");
}




############### 옵션 SORTing ############### 
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3 
// pass_uid  -옵션 고유번호
else if( in_array($pass_type , array("U" , "D")) && $pass_depth && $pass_uid ) {

    // LCY : 2023-03-10 : 순서조정 보정 -- 데이터를 보정하기 전에 현재 순서 기준으로 정렬 {
    $resOption1 = _MQ_assoc("select *from smart_product_addoption where pao_pcode = '".$pass_code."' and pao_depth = '1' order by pao_sort asc ");
    $option_sort1 = 0;
    foreach($resOption1 as $k=>$v){
        _MQ_noreturn("update smart_product_addoption set pao_sort = '".$option_sort1."' where pao_uid = '".$v['pao_uid']."' and pao_pcode = '".$pass_code."' and pao_depth = '1' ");
        $option_sort1 ++;

        $option_sort2 = 0;
        $resOption2 = _MQ_assoc("select *from smart_product_addoption where pao_pcode = '".$pass_code."' and pao_parent = '".$v['pao_uid']."' and pao_depth = '2' order by pao_sort asc ");
        foreach($resOption2 as $sk=>$sv){
            _MQ_noreturn("update smart_product_addoption set pao_sort = '".$option_sort2."' where pao_uid = '".$sv['pao_uid']."' and pao_pcode = '".$pass_code."' and pao_depth = '2' ");
            $option_sort2 ++;
        }
    }
    // LCY : 2023-03-10 : 순서조정 보정 -- 데이터를 보정하기 전에 현재 순서 기준으로 정렬 }

    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from smart_product_addoption where pao_uid='" . $pass_uid ."' ");

    // max sort 추출
    $maxr = _MQ(" select ifnull(max(pao_sort),0) as max_sort from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='". $pass_depth ."' ". ($target_r['pao_parent'] ? " and pao_parent = '". $target_r['pao_parent'] ."' " : "") );

    // 0보다 크고 max 보다 적어야 함.
    if( $target_r[pao_sort] >= 0 && $target_r['pao_sort'] <= $maxr['max_sort'] ) { // LCY : 2023-03-10 : 순서조정 보정 -- >= 0 으로 변경 

        // 타켓 데이터 정보 변경
        _MQ_noreturn(" update smart_product_addoption set ". ( $pass_type == "U" ? "pao_sort=pao_sort-1" : "pao_sort=pao_sort+1") ." where pao_uid='" . $pass_uid ."' ");


        // 순위 바꿀 데이터 정보 추출 및 변경
        if( $pass_type == "U" ) {
            $change_r = _MQ(" select * from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='". $pass_depth ."' ". ($target_r['pao_parent'] ? " and pao_parent = '". $target_r['pao_parent'] ."' " : "") . " and pao_uid != '" . $pass_uid ."' and pao_sort < '". $target_r['pao_sort'] ."' order by pao_sort desc , pao_uid desc limit 1 ");
            _MQ_noreturn(" update smart_product_addoption set pao_sort = pao_sort + 1 where pao_uid='" . $change_r[pao_uid] ."' ");
        }
        else {
            $change_r = _MQ(" select * from smart_product_addoption where pao_pcode='" . $pass_code . "' and pao_depth='". $pass_depth ."' ". ($target_r['pao_parent'] ? " and pao_parent = '". $target_r['pao_parent'] ."' " : "") . " and pao_uid != '" . $pass_uid ."' and pao_sort > '". $target_r['pao_sort'] ."' order by pao_sort asc , pao_uid asc limit 1 ");
            _MQ_noreturn(" update smart_product_addoption set pao_sort = pao_sort - 1 where pao_uid='" . $change_r[pao_uid] ."' ");
        }
    }

}


# 2015-11-18 실시간 저장을 위하여 정보를 한번씩 더 저장 한다.
if( sizeof($pao_info) > 0 ) {
    foreach( $pao_info as $k=>$v ){
        if(!$v['pao_poptionname']) {
            //echo "<script>alert('옵션명을 입력하세요.');</script>";
            exit;
        }            
        $que = "
            update smart_product_addoption set 
                pao_poptionname='".addslashes(trim($v['pao_poptionname']))."',
                pao_poptionprice='".trim(rm_str($v['pao_poptionprice']))."',
                pao_poptionpurprice='".trim(rm_str($v['pao_poptionpurprice']))."',
                pao_cnt='".trim(rm_str($v['pao_cnt']))."',
                pao_view='".$v['pao_view']."'
            where pao_uid='{$k}'
        ";
        _MQ_noreturn($que);
    }
}

exit;