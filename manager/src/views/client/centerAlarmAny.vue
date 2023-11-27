<script setup>
import { onBeforeRouteLeave, useRoute } from "vue-router";
const route = useRoute();

import { mapInstance } from '@/naverMap/stores'
import { NaverMap } from '@/naverMap'
import { ref, computed, reactive, onMounted, defineComponent } from "vue";

import * as common_utils from "@/common/utils.ts";

// AOS 플러그인 적용
import AOS from "aos";
import  "aos/dist/aos.css" 

// 파라미터받기
let REQ_FROM_SPOT = ref("");
let REQ_TO_SPOT = ref("");
let REQ_DEVICE_NO = ref("");
const getUrlParam = () => {
  var d = route.query.d;
  REQ_FROM_SPOT.value = d.split('_')[0];
  REQ_TO_SPOT.value = d.split('_')[1];
  REQ_DEVICE_NO.value = d.split('_')[2];
};

/**
 * 전역변수 관리
 */
const ALARM_INFO_OBJ = ref({}) // 배차정보 객체

onMounted(() => {
  
  getUrlParam(); // 파라미터받기

  AOS.init({
    duration: 1500,
  });
  
  setTimeout(() => {
    // mapInstance.value.setOptions('mapTypeControl', true);
    // 파라미터 체크-1
    if (REQ_FROM_SPOT.value != "") {
      console.log("REQ_FROM_SPOT.value=" + REQ_FROM_SPOT.value);
    } else {
      console.log("[REQ_FROM_SPOT] 넘어온파라미터 없음...")
    }
    // 파라미터 체크-2
    if (REQ_TO_SPOT.value != "") {
      console.log("REQ_TO_SPOT.value=" + REQ_TO_SPOT.value);
    } else {
      console.log("[REQ_TO_SPOT] 넘어온파라미터 없음...")
    }
    // 파라미터 체크-3
    if (REQ_DEVICE_NO.value != "") {
      console.log("REQ_DEVICE_NO.value=" + REQ_DEVICE_NO.value);
    } else {
      console.log("[REQ_DEVICE_NO] 넘어온파라미터 없음...")
    }
    
    // 센터출발정보 조회
    searchSpotStartInfo();

  }, 500);
})

/**
 * 지도이동 - 차량
 */
const mapMoveTruck = () => {
  // 지도이동 - 차량의 현위치
  var latLng = new window.naver.maps.LatLng(
    ALARM_INFO_OBJ.value.TRUCK_LAT,
    ALARM_INFO_OBJ.value.TRUCK_LON
  );
  mapInstance.value.setCenter(latLng);
  mapInstance.value.setZoom(14);
}
/**
 * 지도이동 - 거래처
 */
 const mapMoveCust = () => {
  // 지도이동 - 차량의 현위치
  var latLng = new window.naver.maps.LatLng(
    ALARM_INFO_OBJ.value.TO_SPOT_LAT,
    ALARM_INFO_OBJ.value.TO_SPOT_LON
  );
  mapInstance.value.setCenter(latLng);
  mapInstance.value.setZoom(14);
}

/**
 * 센터출발정보 조회
 */
const searchSpotStartInfo = async () => {
  
  
  // 지도 마커 지우기
  onRemoveCust();
  onRemoveTruck();
  // 경로 지우기
  // onRemoveTruckRoute();

  // 대기창표시
  common_utils.startLoadingBar();

  let reqData = {
    "@I_COMPANYCD": "00002",
    "@I_FROM_SPOT_CD": REQ_FROM_SPOT.value,
    "@I_TO_SPOT_CD": REQ_TO_SPOT.value,
    "@I_DEVICE_NO": REQ_DEVICE_NO.value,
    "@I_LAT": "",
    "@I_LON": ""
  }


  // 7.API요청..
   let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_START_INFO `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      ALARM_INFO_OBJ.value = data.RecordSet[0];
      
      // 거래처도착예정 시간
      let num10 = ALARM_INFO_OBJ.value.EXPECT_TIME.substr(9,2); // 분수 저장..
      num10 = Math.round(num10/10)*10; // 10분단위 설정
      ALARM_INFO_OBJ.value.EXPECT_TIME_TXT = 
        ALARM_INFO_OBJ.value.EXPECT_TIME.substr(6,5)
      ;
      // 기사전화걸기 링크내용구성
      ALARM_INFO_OBJ.value.CALL_PHONE = 
        "tel:" + REQ_DEVICE_NO.value
      ;

      // -----------------------------------------------------------------------
      // 마커추가
      // -----------------------------------------------------------------------
      const custObject = ref({}) // 거래처 객체
      var arrRouteBounds = []; // 경로 영역(위치)정보

      // 출발센터
      arrRouteBounds.push(new window.naver.maps.LatLng(
        ALARM_INFO_OBJ.value.FROM_SPOT_LAT,
        ALARM_INFO_OBJ.value.FROM_SPOT_LON
        )
      );
      // 출발센터-마커정보 매핑
      custObject.LATITUDE = ALARM_INFO_OBJ.value.FROM_SPOT_LAT;
      custObject.LONGITUDE = ALARM_INFO_OBJ.value.FROM_SPOT_LON;
      custObject.SPOT_CD = REQ_FROM_SPOT.value;
      custObject.SPOT_NAME = ALARM_INFO_OBJ.value.FROM_SPOT_NAME;
      custObject.ADDRESS = ALARM_INFO_OBJ.value.FROM_SPOT_ADDRESS;
      custObject.TYPE = "출발센터";
      addCustMarker(custObject);

      // 도착센터
      arrRouteBounds.push(new window.naver.maps.LatLng(
        ALARM_INFO_OBJ.value.TO_SPOT_LAT,
        ALARM_INFO_OBJ.value.TO_SPOT_LON
        )
      );
      // 도착센터-경로정보 생성(마스터에서 가져온..)
      var distance = ALARM_INFO_OBJ.value.DISTANCE;
      distance = Math.round(distance/1000);
      var duration = ALARM_INFO_OBJ.value.DURATION;
      duration = Math.round(duration/60);
      ALARM_INFO_OBJ.value.DISTANCE_INFO = distance; // km
      ALARM_INFO_OBJ.value.DURATION_INFO = duration; // 분
      // 도착센터-마커정보 매핑
      custObject.LATITUDE = ALARM_INFO_OBJ.value.TO_SPOT_LAT;
      custObject.LONGITUDE = ALARM_INFO_OBJ.value.TO_SPOT_LON;
      custObject.SPOT_CD = REQ_TO_SPOT.value;
      custObject.SPOT_NAME = ALARM_INFO_OBJ.value.TO_SPOT_NAME;
      custObject.ADDRESS = ALARM_INFO_OBJ.value.TO_SPOT_ADDRESS;
      custObject.TYPE = "도착센터";
      setTimeout(() => {
        addCustMarker(custObject);
      }, 100);
      // 차량
      arrRouteBounds.push(new window.naver.maps.LatLng(
        ALARM_INFO_OBJ.value.DRV_LAT,
        ALARM_INFO_OBJ.value.DRV_LON
        )
      );
      // 차량-마커정보 매핑
      ALARM_INFO_OBJ.value.TRUCK_LAT = ALARM_INFO_OBJ.value.DRV_LAT;
      ALARM_INFO_OBJ.value.TRUCK_LON = ALARM_INFO_OBJ.value.DRV_LON;
      ALARM_INFO_OBJ.value.SPEED = ALARM_INFO_OBJ.value.DRV_SPEED;
      ALARM_INFO_OBJ.value.EPNAME = ALARM_INFO_OBJ.value.DRV_NAME;
      ALARM_INFO_OBJ.value.VEHICLENO = ALARM_INFO_OBJ.value.VEHICLENO;
      ALARM_INFO_OBJ.value.TRUCK_ADDRESS = ALARM_INFO_OBJ.value.DRV_ADDRESS;
      
      custObject.TYPE = "차량";
      setTimeout(() => {
        if (ALARM_INFO_OBJ.value.DRV_SPEED > 0) {
          // 운행중
          document.getElementsByClassName('cvo_map_dispinfo')[0].style.backgroundColor = '#2879ffe6';
        } else {
          // 정차중
          document.getElementsByClassName('cvo_map_dispinfo')[0].style.backgroundColor = '#989898e6';
        }
        addTruckMarker(ALARM_INFO_OBJ.value);
      }, 500);
      

      var fitOption = { top: 10, right: 20, bottom: 10, left: 20, maxZoom: 50 };
      mapInstance.value.fitBounds(arrRouteBounds, fitOption);

      // 현재 차량위치에서 도착센터까지 KAKAO경로조회 요청하기..
      requestCvoSpotRouteApiSel(
        ALARM_INFO_OBJ.value.DRV_LAT,
        ALARM_INFO_OBJ.value.DRV_LON,
        ALARM_INFO_OBJ.value.TO_SPOT_LAT,
        ALARM_INFO_OBJ.value.TO_SPOT_LON
      );

      // -----------------------------------------------------------------------
      // 경로이력정보 조회 -- 잠시 주석처리..
      // -----------------------------------------------------------------------
      // var traceStartDtm, traceEndDtm;
      // traceStartDtm = dispatchId.substr(3,12);
      // var nowDtmString = common_utils.nowYYYY_MM_DD_HH_MM_SS();
      // nowDtmString = nowDtmString.replace(/\D/g, '') // 숫자를 제외한 모든 문자 제거
      // //console.log("nowDtmString=" + nowDtmString);
      // traceEndDtm = nowDtmString;
      // searchRouteInfo(device_No, traceStartDtm, traceEndDtm);

/*
-- 배차정보 Set
        {
            "DISPATCH_ID": "DIS20231103093528061",
            "COMPANYCD": "00001",
            "COMPANY_NAME": "(주)목원푸드",
            "CENTER_LAT": "36.587024",
            "CENTER_LON": "127.2408339",
            "VEHICLENO": "819고7667",
            "EPCODE": "0004",
            "EPNAME": "임수호",
            "EPPHONE": "010-5501-3694",
            "CLCODE": "0000000115",
            "CLNAME": "북문로떡볶이(롯데마트점)",
            "CLIENT_LAT": "36.6202981",
            "CLIENT_LON": "127.5160616",
            "CLIENT_ADDRESS": "청주시 상당구 용암북로 1번길 60 롯데마트 상당점 2F 푸드코트 북문로 떡볶이",
            "PIC_NAME": "김성식",
            "PIC_TEL": "010-3456-0074",
            "ITEM_INFO": "D-2토담) 밀떡볶이11cm 1.8kg 선주문 외 7건",
            "ORDER_AMT": "98,200",
            "TOT_ORDER_AMT": 98200
        }
*/      
    } else {
      console.log("거래처별, 배차경로 조회-3");
    }
  } catch (error) {
    console.error(error);
  } finally {
    common_utils.removeLoadingBar();
    console.log("거래처별, 배차경로 조회-4," + ALARM_INFO_OBJ.value.ITEM_INFO);
  }
};



// 경로 객체
var routeInfoList = []; // 경로데이타 정보
var arrRouteBounds = []; // 경로 영역(위치)정보
var arrRouteLineList = []; // 경로 폴리라인
/**
 * 지도, 차량경로 지우기
 */
const onRemoveRoute = () => {
  // console.log("지도, 차량경로 지우기");
  // 지우기
  arrRouteLineList.forEach((el) => {
    el.setMap(null);
  });
  // 초기화
  arrRouteLineList = [];
};

/**
 * 경로API 조회..
 */
 const requestCvoSpotRouteApiSel = async (fromLat, fromLon, toLat, toLon) => {
  
  // 대기바 표시
  common_utils.startLoadingBar();

  // 파라미터세팅 - { "startPOS":"127.11015314141542,37.39472714688412", "endPOS":"127.10824367964793,37.401937080111644", "showRouteYn":"Y" }
  let reqData = {
      'startPOS': fromLon + ',' + fromLat,
      'endPOS': toLon + ',' + toLat,
      'showRouteYn': 'Y',
  }
  
  // 기존경로 지우기
  onRemoveRoute();
  
  // API요청..
  const rowDataRouteApi = reactive([]);
  let data, result;
  try {
    result = await common_utils.getAxiosCVOUtil().post(`/getCVORoute `, reqData)
    data = result.data;

    if (data.ResultData.routes.length > 0) {
      // 경로API 데이타 푸쉬
      rowDataRouteApi.push(...data.ResultData.routes);

      // console.log(rowDataRouteApi);
      // console.log(rowDataRouteApi[0]);
      // console.log("distance=" + rowDataRouteApi[0].summary.distance);
      // console.log("duration=" + rowDataRouteApi[0].summary.duration);


      // 도착센터-경로정보 생성(지금시점, 차량위치를 가지고 kakao 경로요청한..)
      var distance = rowDataRouteApi[0].summary.distance;
      distance = Math.round(distance/1000);
      var duration = rowDataRouteApi[0].summary.duration;
      duration = Math.round(duration/60);
      ALARM_INFO_OBJ.value.DISTANCE_INFO = distance; // km
      ALARM_INFO_OBJ.value.DURATION_INFO = duration; // 분


      // // 화면 하단, 경로정보 표시용...
      // var distance = rowDataRouteApi[0].summary.distance;
      // distance = Math.round(distance/1000);
      // var duration = rowDataRouteApi[0].summary.duration;
      // duration = Math.round(duration/60);
      // selectEndRowData.value.DISTANCE_INFO = distance + "km";
      // selectEndRowData.value.DURATION_INFO = duration + "분";

      // console.log(rowDataRouteApi[0].sections[0].roads);
      var arrPoints = [];
      // ------------------------------------------------------------
      // 경로포인트 요약 추출...
      // ------------------------------------------------------------
      rowDataRouteApi[0].sections[0].guides.forEach(function (item) {
        arrPoints.push(
          new naver.maps.LatLng([item.x, item.y])
        );
      });

      // ------------------------------------------------------------
      // 경로그리기
      // ------------------------------------------------------------
      drawRouteLine(arrPoints, "예상");
      
      //console.log(ALARM_INFO_OBJ.value.LAST_ROUTE_INFO);
      var arrRouteHistory = JSON.parse(ALARM_INFO_OBJ.value.LAST_ROUTE_INFO);
      drawRouteLine(arrRouteHistory, "이력");
      

    } else {
      console.log("데이타없음..");
    }
    
  } catch (error) {
    console.error(error);
  } finally {
    common_utils.removeLoadingBar();
  }
};
/**
 * 경로 그리기..(실선-일반)
 */
 const drawRouteLine = async (arrPoints, routeType) => {
  try {
    //console.log(arrPoints);
    
    var prevPoint = null,
      nIndex = 0;
    var upperP1, upperP2, lowerP1, lowerP2;

    var lineColor = "";
    if (routeType == "예상") {
      lineColor = "#2879ffe6";
    } else {
      lineColor = "#E51D1Ae6";
    }
    
    arrPoints.forEach(function (item) {
      
      // 1.시작지점 세팅
      if (nIndex > 0) {
        
        // 2.종료지점 세팅
        if (routeType == "예상") {
          upperP1 = new naver.maps.LatLng([prevPoint.x, prevPoint.y]);
          upperP2 = new naver.maps.LatLng([item.x, item.y]);
        } else {
          console.log(item);
          upperP1 = new naver.maps.LatLng([prevPoint.LONGITUDE, prevPoint.LATITUDE]);
          upperP2 = new naver.maps.LatLng([item.LONGITUDE, item.LATITUDE]);
        }
        // 3.경로객체 생성
        var openArrowLine = new naver.maps.Polyline({
          path: [upperP1, upperP2],
          map: mapInstance.value,
          // startIcon: naver.maps.PointingIcon.CIRCLE,
          // startIconSize: 10,
          // endIcon: naver.maps.PointingIcon.BLOCK_ARROW,
          // endIconSize: 10,
          clickable: true, // 사용자 인터랙션을 받기 위해 clickable을 true로 설정합니다.
          strokeColor: lineColor,
          strokeWeight: 5,
          strokeStyle: "solid", // solid, shortdash, shortdot, shortdashdot, shortdashdotdot, dot, dash, longdash, dashdot, longdashdot, longdashdotdot
          strokeOpacity: 0.5,
        });
        // 마자막위치에 화살표 표기추가
        if (nIndex == arrPoints.length - 1) {
          openArrowLine.setOptions(
            "endIcon",
            naver.maps.PointingIcon.BLOCK_ARROW
          );
          openArrowLine.setOptions("endIconSize", 20);
        }
        
        arrRouteLineList.push(openArrowLine);

      }
      
      prevPoint = item;
      nIndex++;
    });
    
  } catch (error) {
    console.log(error);
  }
};


/**
 * 경로이력정보 조회
 */
 const searchRouteInfo = async (device_No, traceStartDtm, traceEndDtm) => {
  
  // // 지도 마커 지우기
  // onRemoveTruck();
  // // 기존경로 지우기
  // onRemoveTruckRoute();

  // 대기창표시
  common_utils.startLoadingBar();

  let reqData = {
    '@I_COMPANYCD' : "00001",
    '@I_DEVICE_NO' : device_No ?? '',
    '@I_TRACE_START_DTM': traceStartDtm ?? '',
    '@I_TRACE_END_DTM': traceEndDtm ?? '',
  }

  // API요청..
   let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_415_TRACE_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      console.log("경로정보 조회," + data.RecordCount);
/*
        {
            "RANK": 1,
            "GEONUM": 62104,
            "DEVICE_NO": "01055013694",
            "VEHICLECD": "819-7667",
            "VEHICLENO": "819고7667",
            "TRACEDATE": "20231103",
            "TRACETIME": "093548",
            "EVENTCODE": "05",
            "GPSYN": "Y",
            "CHARGEYN": "N",
            "LATITUDE": "36.6103501",
            "LONGITUDE": "127.3260339",
            "DIRECTION": "68",
            "SPEED": 50,
            "REMARK": "",
            "INTERVALDISTANCE": "0",
            "BATTERYLEVEL": 81,
            "TIMESTAMP": null,
            "ADDRESS": "",
            "DISPATCH_KEY": "DIS20231103093528061",
            "ROUTE_TITLE": "50<small>km/h</small>",
            "MARKER_SUBTITLE": "819고7667",
            "MARKER_CAPTION": "",
            "MARKER_SUBCAPTION": "11/03 09:35",
            "STATUS_NM": "운행중",
            "MAX_SPEED": 140,
            "MAX_DT": "20231106173013",
            "MIN_DT": "20231103093548",
            "DURATION_INFO": "3<small><small>일</small></small>7<small><small>시간</small></small>54<small><small>분</small></small>"
        }

*/      
    } else {
      console.log("경로정보 조회");
    }
  } catch (error) {
    console.error(error);
  } finally {
    common_utils.removeLoadingBar();
    console.log("경로정보 조회,");
  }
};

// 거래처 객체..
var custMarkerList = [],
  custkMarkerIndex = 0;
/**
 * 지도, 거래처마커 지우기
 */
 const onRemoveCust = () => {
  //마커 및 타이틀 객체 지우기
  // 1.마커 지우기
  custMarkerList.forEach((el) => {
    el.setMap(null);
  });
  custMarkerList = [];
};

/**
 * 마커추가 - 거래처
 * @param {배차일보 객체} targetObj
 */
const addCustMarker = (targetObj) => {
  try {
    if (targetObj.LATITUDE > 0 && targetObj.LONGITUDE > 0) {
      let latLng = new window.naver.maps.LatLng(targetObj.LATITUDE, targetObj.LONGITUDE);
      
      var markerType, clgubun_css;
      if (targetObj.TYPE == "출발센터") {
        markerType = "center";
      } else {
        markerType = "client";
      }
      clgubun_css = "marker_clgubun_1";

      // 거래처마커 추가
      var custerMarker = new window.naver.maps.Marker({
        position: latLng,
        map: mapInstance.value,
        // index: targetObj.ROUTE_SEQ - 1,
        targetObj: targetObj,
        title: targetObj.SPOT_NAME,
        zIndex: 100,
        icon: {
          content: [
            '<div class="marker_' + markerType + '">',
            '<span class="' +
              clgubun_css +
              '">' +
              '센터' +
              '</span><span style="font-size:17px;">' +
              targetObj.SPOT_NAME +
              "</span<BR>",
            '<hr style="margin:0.2rem 0rem 0.1rem 0rem; border:0px; border-top:1px solid #FFFFFF;"/>',
            "<marquee scrollamount=\"4\">" +
              targetObj.ADDRESS +
            "</marquee></div>",
            '<div class="marker_' + markerType + '_bottom_point"></div>',
            '<div class="marker_' + markerType + '_bottom_arrow"></div>',
          ].join(""),
          size: new window.naver.maps.Size(38, 58),
          anchor: new window.naver.maps.Point(100, 85),
        },
        animation: naver.maps.Animation.DROP,
      });

      // 거래처 마커 저장
      custMarkerList.push(custerMarker);
      
    }
  } catch (error) {
    console.error(error);
  }
};


// -------------------------------------------------------
// 차량마커관련..
// -------------------------------------------------------
var addTruckBounds = [];
var truckMarkerList = [], truckMarkerIndex = 0;
const onRemoveTruck = () => {
  //마커 및 타이틀 객체 지우기
  truckMarkerList.forEach((el) => {
    el.setMap(null);
  });
  truckMarkerList = [];
};
/**
 * 차량마커 올리기..
 * @param targetObj 
 */
const addTruckMarker = (targetObj) => {
  let latLng = new window.naver.maps.LatLng(
    targetObj.TRUCK_LAT,
    targetObj.TRUCK_LON
  );
  addTruckBounds.push(latLng);

  var on_off_string, status_text;
  if (targetObj.SPEED > 0) {
    on_off_string = "_on";
    status_text = "운행중";
  } else {
    on_off_string = "_off";
    status_text = "정차중";
  }

  var truckMarker = new window.naver.maps.Marker({
      position: latLng,
      map: mapInstance.value,
      targetObj: targetObj, // 차량객체를 전달한다..
      title: targetObj.EPNAME,
      zIndex: 100,
      icon: {
        content: [
          '<div class="marker_truck' + on_off_string + '">',
          '<span style="font-size:17px;">' +
            targetObj.VEHICLENO +
            " (" +
            targetObj.EPNAME +
            ")</span><BR>",
          '<hr style="margin:2px 0px; border:0px; border-top:1px solid #FFFFFF;"/>',
          '<img src="/assets/img/map/truck_white_002.png" style="height:20px; width:auto; margin:5px 5px 5px 5px;"/>',
          "| " + targetObj.SPEED + ' <span style="font-size:7px;">km/h</span>',
          '| <span style="font-size:15px;">' + status_text + "</span>",
          "</div>",
          '<div class="marker_truck_bottom_point"></div>',
          '<div class="marker_truck_bottom_arrow' + on_off_string + '"></div>',
        ].join(""),
        size: new window.naver.maps.Size(38, 58),
        anchor: new window.naver.maps.Point(90, 95),
      },
      animation: naver.maps.Animation.BOUNCE,
  });
  truckMarkerList.push(truckMarker);
  
  setTimeout(() => {
    truckMarker.setAnimation(null);
  }, 1500);
  // // 차량 마커클릭시..
  // naver.maps.Event.addListener(truckMarker, "click", function () {
  //   // 차량객체얻기..
  //   var selectTruckObj = truckMarker.targetObj;
  //   // 최근 차량의 이동경로 조회
  //   var lastDtm = selectTruckObj.TRACEDATE + selectTruckObj.TRACETIME;
  //   recentRouteTruck(selectTruckObj.DEVICE_NO, lastDtm);
  // });

  truckMarkerIndex++;
};





</script>


<template>
  <div class="section section__management" style="margin:0rem; padding:0rem;">
    <!-- 네이버지도 -->
    <div style="flex: 2; border-style: solid; border-width: 1px; border-color: #a8a7b0; position: relative">
        <NaverMap style="height: 100%; width: 100%" />
        <!-- 새로고침 버튼 -->
        <div style="position: absolute; left: 10px; top: 10px" data-aos="fade-down-right" data-aos-delay="50">
          <img class="cvo_map_refresh" @click.prevent="searchSpotStartInfo" src="/assets/img/map/refresh_blue_01.png" />
        </div>
        <!-- 전화걸기 버튼 -->
        <div style="position: absolute; right: 10px; top: 10px" data-aos="fade-down-left" data-aos-delay="150">
          <a :href=ALARM_INFO_OBJ.CALL_PHONE>
            <img class="cvo_map_refresh" src="/assets/img/map/icon_tel.png" />
          </a>
        </div>
        <!-- 지도하단 정보영역 -->
        <div class="cvo_map_bottom_area">
          <!-- 차량정보 -->
          <div class="cvo_map_dispinfo" @click.prevent="mapMoveTruck" data-aos="fade-up-right" data-aos-delay="250">
            <div class="cvo_info1">
              {{ ALARM_INFO_OBJ.VEHICLENO }} ({{ ALARM_INFO_OBJ.DRV_NAME }})
            </div>
            <hr style="margin:0.1rem 0rem 0.3rem 0rem; border:0px; border-top:0.05rem solid #ffffff;"/>
            <div class="cvo_info2">
              <div class="area1">
                <div>{{ ALARM_INFO_OBJ.DRV_SPEED }}</div>
                <div><span style="font-size:0.6rem;">&nbsp;km/h</span></div>
              </div>
              <div class="area2">
                <div><marquee scrollamount="4"><span>{{ ALARM_INFO_OBJ.DRV_ADDRESS }}&nbsp;</span></marquee></div>
                <div style="display:block;">
                  <span style="font-size:0.7rem;">{{ ALARM_INFO_OBJ.DRV_TRACE_DTM_STRING }}</span>
                  <span style="font-size:0.45rem;"> - update</span>
                </div>
              </div>
            </div>
          </div>
          <!-- 도착센터 정보 -->
          <div class="cvo_map_custinfo" @click.prevent="mapMoveCust" data-aos="fade-up-left" data-aos-delay="350">
            <div class="cust_name"><span>{{ ALARM_INFO_OBJ.TO_SPOT_NAME }}</span></div>
            <hr style="margin:0.1rem 0rem 0.0rem -0.2rem; border:0px; border-top:0.05rem solid #ffffff;"/>
            <div class="order_info">
              <div>
                <span style="font-size:0.9rem;">{{ ALARM_INFO_OBJ.DISTANCE_INFO }}</span><span style="font-size:0.45rem;"> km</span>
                &nbsp;/&nbsp;<span style="font-size:0.9rem;">{{ ALARM_INFO_OBJ.DURATION_INFO }}</span><span style="font-size:0.45rem;"> 분</span>
              </div>
              <div style="display:block;">
                <span style="font-size:0.7rem;">{{ ALARM_INFO_OBJ.EXPECT_TIME_TXT }}</span>
                <span style="font-size:0.45rem;"> - 도착예정</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<style>

/* ---------------------------------------------------------- */
/* 고객사, 제공웹지도 - 하단 정보영역 */
/* ---------------------------------------------------------- */
.cvo_map_bottom_area {
  position: absolute; bottom: 0.8rem;
  border: 0px solid red;
  width: 100%;
  padding: 0.2rem;
  display: inline-flex;
}

/** 배차(차량)정보 */
.cvo_map_dispinfo {
  display: block;
  color: #FFFFFF;
  padding: 0.2rem 0.4rem;
  border-radius: 0.2rem;
  font-size: 0.8rem;
  background-color: #2879ffe6;
  filter: drop-shadow(5px 5px 5px #00000078);
  transition: all 0.2s linear;
  border: 0rem solid red;
  width: 49%;
}
/** 속도 */
.cvo_map_dispinfo .area1 {
  padding:0.2rem 0.2rem 0rem 0rem;
  margin-left: -0.2rem;
  border: 0px solid red;
  font-size: 1rem;
  line-height: 0.7rem;
  border-right: 0.05rem solid #ffffff8b;
  text-align: center;
}
/** 주소,시간 */
.cvo_map_dispinfo .area2 {
  padding:0rem 0.2rem 0rem 0.2rem;
  border: 0px solid red;
  font-size: 0.9rem;
  line-height: 0.9rem;
  text-align: center;
}
/** 차량번호,기사명 */
.cvo_info1 {
  display: block;
  border: 0px solid red;
  margin-top:-0.1rem;
  margin-bottom:-0.1rem;
  text-align: center;
  font-size: 0.7rem;
}
/** 속도,주소,시간 */
.cvo_info2 {
  display: inline-flex;
}

/** 배차(거래처) 정보 */
.cvo_map_custinfo {
  color: #FFFFFF;
  margin-left: 0.2rem;
  padding: 0.2rem 0.1rem 0.2rem 0.4rem;
  border-radius: 0.2rem;
  font-size: 1rem;
  background-color: #7329afd1;
  filter: drop-shadow(5px 5px 5px #00000078);
  transition: all 0.2s linear;
  width: 49%;
  text-align: center;
}
.cust_name {
  margin-top:-0.1rem;
  margin-bottom:-0.1rem;
  font-size: 0.7rem;
  /* 말줄임처리 */
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  word-break: break-all;
}
.order_info {
  padding:0.3rem 0.2rem 0rem 0rem;
  border: 0px solid red;
  font-size: 0.9rem;
  line-height: 0.9rem;
  text-align: center;
}
</style>
