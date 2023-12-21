<script setup>
import { onBeforeRouteLeave, useRoute } from "vue-router";
const route = useRoute();

import VueGeolocation from 'vue-geolocation-api'
import { useAppVariableStore } from '@/stores/app-variable';
import { animateNumber } from '@/components/app/AnimateNumber.vue';
import { Popover } from 'bootstrap';
import apexchart from '@/components/plugins/Apexcharts.vue';
import jsVectorMap from 'jsvectormap';
import 'jsvectormap/dist/maps/world.js';
import 'jsvectormap/dist/css/jsvectormap.min.css';
import 'simple-line-icons/css/simple-line-icons.css';
const appVariable = useAppVariableStore();

import { mapInstance } from "@/naverMap/stores";
import { NaverMap } from "@/naverMap";
import { operateApi, memberApi } from "@/api";

import { ref, computed, reactive, onMounted, defineComponent } from "vue";
import { AgGridVue } from "ag-grid-vue3"; // the AG Grid Vue Component
import highlightjs from "@/components/plugins/Highlightjs.vue";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import {
  startLoadingBar,
  removeLoadingBar,
  addHoverClassToTr,
  removeHoverClassFromTr,
  doSort,
  showToast,
} from "@/common/utils.ts";
import * as common_utils from "@/common/utils.ts";

// AOS 플러그인 적용
import AOS from "aos";
import  "aos/dist/aos.css" 

// 파라미터받기
let REQ_CLCODE = ref("");
const getUrlParam = () => {
    REQ_CLCODE.value = route.query.clcode;
    searchKeyword.value = REQ_CLCODE.value;
};

/* 좌측영역 접기 */
let foldYn = false;
const onClickFoldButton = () => {
    var leftArea = document.getElementsByClassName("grid_clientlist")[0]; // 접기 버튼
    var arrowIco = document.getElementsByClassName("fa-arrow-left")[0]; // 접기 화살표
    var mapArea = document.getElementsByClassName("area_map")[0]; // 지도영역
    
    
    if (foldYn) {
        leftArea.style.width = "19rem";
        arrowIco.style.transform = 'rotate(0deg)';
        foldYn = false;
        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
    } else {
        leftArea.style.width = "0rem";
        mapArea.style.width = "100%";
        arrowIco.style.transform = 'rotate(180deg)';
        foldYn = true;
    }
    
};

/* 하단 이미지 카드 클릭시 */
const onClickImageCard = (targetObj) => {
  //console.log(targetObj)

  // 1.그리드 클릭이벤트 효과
  var event = {};
  event.data = targetObj;
  cellWasClicked(event);
}
/**
 * 위치정보 관련..
 */
const curPositionObj = ref({})
const isPositionReady = ref(false)
const getCurrentPosition = () => {

  showAlertSuccess("현재 접속위치를 확인중입니다.");

  // 대기바 표시
  startLoadingBar();

  if (!navigator.geolocation) {
    removeLoadingBar();
  } else {
    navigator.geolocation.getCurrentPosition(getPositionValue, geolocationError)
  }
}
const getPositionValue = (val) => {
  curPositionObj.value.latitude = val.coords.latitude
  curPositionObj.value.longitude = val.coords.longitude
  isPositionReady.value = true
  console.log('주소 확인 완료', val);

  removeLoadingBar();

  // 차량최종위치 얻기..
  requestCvoNowPositionSel();
}
const geolocationError = (e) => {
  console.log(e);
  console.log(CENTER_POSITION);
  console.log('위치 정보를 찾을 수 없습니다.');

  curPositionObj.value.latitude = CENTER_POSITION.y;
  curPositionObj.value.longitude = CENTER_POSITION.x;

  removeLoadingBar();

  // 차량최종위치 얻기..
  requestCvoNowPositionSel();
}
const setAlert = (text) => {
  alert(text)
}


const gridApi = ref(null);
const rowData = reactive([]);
const empList = ref([]); // 배송사원 목록
const dispatchSummaryList = ref([]); // 배송일지 목록

const onCellValueChanged = (params) => {
  const colId = params.column.getId();
  if (colId === "STATUS") {
    // const selectedCountry = params.data.country;
    // const selectedCity = params.data.city;
    // const allowedCities = countyToCityMap(selectedCountry);
    // const cityMismatch = allowedCities.indexOf(selectedCity) < 0;
    // if (cityMismatch) {
    // 	params.node.setDataValue('city', null);
    // }
  }
};

const defaultColDef = {
  sortable: true,
  filter: true,
  editable: true,
  resizable: true,
  cellStyle: { textAlign: "center" },
  onCellValueChanged: onCellValueChanged,
  // flex: 1
};
const cellCellEditorParams = (params) => {
  const allowedData = ["신규가입", "승인", "거절"];

  return {
    values: allowedData,
    formatValue: (value) => `${value}`,
  };
};
const columnDefs = reactive({
  value: [
    // {
    //   headerName: "NO",
    //   field: "seq",
    //   pinned: "left",
    //   lockPinned: true,
    //   cellClass: "header-center",
    //   editable: false,
    //   valueGetter: "node.rowIndex + 1",
    //   cellStyle: { textAlign: "center" },
    //   width: 100,
    // },
    {
      headerName: "거래처명",
      field: "CLNAME",
      pinned: "left",
      lockPinned: true,
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "거리",
      field: "DISTANCE_ME_TXT",
      cellStyle: { textAlign: "left" },
      width: 80,
    },
    {
      headerName: "오늘",
      field: "IMAGE_CNT_TODAY",
      cellStyle: { textAlign: "left" },
      width: 70,
    },
    {
      headerName: "누적",
      field: "IMAGE_CNT_TOT",
      cellStyle: { textAlign: "left" },
      width: 70,
    },
    {
      headerName: "사업자번호",
      field: "CLSAUPNO",
      // pinned: 'left',
      // lockPinned: true,
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 145,
    },
    {
      headerName: "전화번호",
      field: "CLTEL",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "주소1",
      field: "CLJUSO1",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
    {
      headerName: "주소2",
      field: "CLJUSO2",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
    {
      headerName: "주소3",
      field: "CLJUSO2",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
    {
      headerName: "주소4",
      field: "CLJUSO2",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
    {
      headerName: "위도",
      field: "LAT",
      cellStyle: { textAlign: "center" },
      width: 120,
    },
    {
      headerName: "경도",
      field: "LON",
      cellStyle: { textAlign: "center" },
      width: 120,
    },
    // {
    //   headerName: "사용자명",
    //   field: "USER_NAME",
    // },
    // {
    //   headerName: "휴대폰번호",
    //   field: "USER_PHONE",
    // },
    // {
    //   headerName: "이메일",
    //   field: "EMAIL",
    // },
    // {
    //   headerName: "승인여부",
    //   field: "STATUS_NM",
    //   editable: false,
    // },
  ],
});

const dataForBottomGrid = [
  {
    athlete: "Total",
    age: "15 - 61",
    country: "Ireland",
    year: "2020",
    date: "26/11/1970",
    sport: "Synchronised Riding",
    gold: 55,
    silver: 65,
    bronze: 12,
  },
];

// 현재 날짜 저장..
let curDate = new Date();
// 현재 차량객체 저장..
let curTruckIndex = null;
let curTruckObj = null;

import { useAppOptionStore } from "@/stores/app-option";
const appOption = useAppOptionStore();
//사용자 및 페이지 정보
const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
// const userClcode = userInfo.CLCODE;
// const userId = userInfo.ID;
const userClcode = "9999999999";
const userId = "guest";
const pageNumber = ref(1);
const pageSize = ref(20);

import { useAlert } from "@/composables/showAlert";
const { showAlert, showAlertSuccess, showConfirm } = useAlert();

const catergory1data = ref([
  { CODE:"1", NAME:"1km"},{ CODE:"3", NAME:"3km"},{ CODE:"5", NAME:"5km"},
  { CODE:"10", NAME:"10km"},{ CODE:"30", NAME:"30km"},{ CODE:"50", NAME:"50km"},
]);

// 반경거리 콤보
const boundDist = ref("10");
//검색어
let searchKeyword = ref("");

//우측 상세보기 영역 노출여부
let detailShow = ref(false);

//메인 그리드에서 선택한 row 정보
let rowTargetObj = "";
let selectRowData = ref({});

/******************************************************************************* onMounted */
/**
 * 뷰 마운트-초기구동 시점
 */
let bFistYn = true;
onMounted(() => {
    
    getUrlParam(); // 파라미터받기

    pageNumber.value = 1;

    AOS.init({
      duration: 1200,
    });

    // 초기 왼쪽을 접어준다..
    onClickFoldButton();
    
    setTimeout(() => {
      // 지도초기화
      initMap();

      
      // 현위치조회 요청 - 운영용 - 나중에 적용시 쿠키로 저장하자..
    //   curPositionObj.boundDist = boundDist.value;
    //   getCurrentPosition();

      // 차량최종위치 얻기 - 개발용
      curPositionObj.boundDist = boundDist.value;
      curPositionObj.value.latitude = 36.5980585;
      curPositionObj.value.longitude = 127.3018034;
      requestCvoNowPositionSel();

      // 파라미터받기
      if (REQ_CLCODE.value != null) {
        console.log("REQ_CLCODE.value=" + REQ_CLCODE.value);
      } else {
        console.log("넘어온파라미터 없음...")
      }
    }, 500);

    // 모달창처리를 위한코드 삽입..
    setTimeout(() => {
      const arrPrvModels = document
        .getElementById("modal_insert_area")
        .getElementsByClassName("modal");
      for (var i = 0; i < arrPrvModels.length; i++) {
        // 기존요소 삭제
        document.getElementById("modal_insert_area").removeChild(arrPrvModels[i]);
      }
      const arrModels = document
        .getElementsByClassName("section__management_ilove")[0]
        .getElementsByClassName("modal");
      for (var i = 0; i < arrModels.length; i++) {
        // 신규요소 추가
        document.getElementById("modal_insert_area").append(arrModels[i]);
      }
    }, 1000);

    
});

/**
 * 맵 초기화
 */
function initMap() {
    mapInstance.value.setOptions("mapTypeControl", true); //지도 유형 컨트롤의 표시 여부
    mapInstance.value.setOptions("zoomControl", false); //줌 컨트롤의 표시 여부
    mapInstance.value.setOptions("zoomControlOptions", {
        //줌 컨트롤의 옵션
        position: naver.maps.Position.RIGHT_BOTTOM,
    });
  
    mapInstance.value.defaultCameraAnimationDuration = 1000;
    naver.maps.Event.addListener(mapInstance.value, "zoom_changed", function () {
        console.log("맵 줌/인아웃시:" + mapInstance.value.getZoom());
        // openArrowLine.setOptions({
        // 	strokeColor: '#eb2b2b6b',
        // 	strokeStyle: 'dash',
        // 	strokeOpacity: 0.5
        // });
    });

    naver.maps.Event.addListener(mapInstance.value, "dragend", function () {
        //  console.log("맵 dragend:" + mapInstance.value.getCenter().lat());
        //  console.log("맵 dragend:" + mapInstance.value.getCenter().lng());
        
        // // 기준위치 변경
        // curPositionObj.value.latitude = mapInstance.value.getCenter().lat();
        // curPositionObj.value.longitude = mapInstance.value.getCenter().lng();
        // // ERP거래처 조회요청
        // requestErpClientSel();

    });


    naver.maps.Event.addListener(mapInstance.value, "click", function (e) {

        var point = e.coord;

        console.log("맵 click:" + point.lat());
        console.log("맵 click:" + point.lng());
        
        // 기준점 위치 변경
        curPositionObj.value.latitude = point.lat();
        curPositionObj.value.longitude = point.lng();
        // 기준점 마커 표시 세팅..
        rootPositionMarkerSet(
          new naver.maps.LatLng(curPositionObj.value.latitude, curPositionObj.value.longitude)
        );
        
    });

    CENTER_POSITION = new naver.maps.LatLng([127.2408339, 36.587024]);
}
// 센터위치 - 목원푸드
var CENTER_POSITION;
// 차량마커 객체..
var truckMarkerList = [],
  truckMarkerIndex = 0;
var truckInfowindow = []; // 인포윈도우 - 차량
var arrTruckBounds = [];
/**
 * 지도, 차량마커 지우기
 */
const onRemoveTruck = () => {
  //마커 및 타이틀 객체 지우기
  // 1.마커 지우기
  truckMarkerList.forEach((el) => {
    el.setMap(null);
  });
  truckMarkerList = [];
  // 2.인포윈도우 지우기;
  truckInfowindow.forEach((el) => {
    el.setMap(null);
  });
  truckInfowindow = [];
  // 3.영역객체 지우기
  arrTruckBounds = [];
};
// 거래처 객체..
var custMarkerList = [],
  custkMarkerIndex = 0;
var custInfowindow = []; // 인포윈도우 - 거래처
var arrCustBounds = [];
var arrCustLineList = []; // 거래처 폴리라인
var arrCustCircle = []; // 거래처 반경원
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
  // 2.인포윈도우 지우기
  custInfowindow.forEach((el) => {
    el.setMap(null);
  });
  custInfowindow = [];
  // 3.영역객체, 지우기
  arrCustBounds = [];
  // 4.라인 지우기
  arrCustLineList.forEach((el) => {
    el.setMap(null);
  });
  arrCustLineList = [];

  // 추가삭제 - 거래처 반경원 지우기
  onRemoveCustCircle();
};
// 경로 객체
var routeInfoList = []; // 경로데이타 정보
var arrRouteBounds = []; // 경로 영역(위치)정보
var arrRouteLineList = []; // 경로 폴리라인
/**
 * 지도, 차량경로 지우기
 */
const onRemoveTruckRoute = () => {
  // console.log("지도, 차량경로 지우기");
  // 지우기
  arrRouteLineList.forEach((el) => {
    el.setMap(null);
  });
  // 초기화
  arrRouteLineList = [];
};
/**
 * 거래처 반경원 지우기
 */
var CUST_ROUND_METER = 200;
const onRemoveCustCircle = () => {
  // console.log("지도, 차량경로 지우기");
  // 지우기
  arrCustCircle.forEach((el) => {
    el.setMap(null);
  });
  // 초기화
  arrCustCircle = [];
};

/**
 * 배송사원리스트 추가하기
 * @param {*} targetObj
 */
const addEmpList = (targetObj) => {
  // 운행여부에 따라서 테마를 변경하기위한..
  var on_off_string;
  if (targetObj.SPEED > 0) {
    on_off_string = "_on";
  } else {
    on_off_string = "_off";
  }
  targetObj.on_off_string = on_off_string;

  try {
    if (targetObj.LAST_DISPWORK_INFO != null) {
      var 최종배차작업정보 = targetObj.LAST_DISPWORK_INFO.split("#");
      targetObj.배차시작일시 = 최종배차작업정보[0];
      targetObj.최종보고시간 = 최종배차작업정보[1];
      targetObj.배송건수 = 최종배차작업정보[2];
      targetObj.진행건수 = 최종배차작업정보[3];
      targetObj.배송현황 = targetObj.배송건수 + " / " + targetObj.진행건수;
      targetObj.status_text = "배송중";
    } else {
      targetObj.배송현황 = "배송대기";
      targetObj.status_text = "배송대기";
    }
  } catch (error) {
    console.error(error);
  } finally {
  }

  empList.value.push(targetObj);
};

/**
 * 기준위치 마커표시 하기
 * @param {*} position 
 */
var rootPointerMarker;
const rootPositionMarkerSet = (targetPosition) => {
  // 기존 마커지우기
  if (rootPointerMarker != null) rootPointerMarker.setMap(null);
  // 마커 표시
  rootPointerMarker = new naver.maps.Marker({
      position: targetPosition,
      map: mapInstance.value,
      title: 'Green',
      icon: {
          content: [
                      // '<div class="cs_mapbridge">',
                      //     '<div class="map_group _map_group crs">',
                      //         '<div class="map_marker _marker num1 num1_big"> ',
                      //             '<span class="ico _icon"></span>',
                      //             '<span class="shd"></span>',
                      //         '</div>',
                      //     '</div>',
                      // '</div>'
                      '<div class="rootPosition">',
                        '<span class="rootPositionTitle">반경 ' + curPositionObj.boundDist + 'km</span>',
                        '<span class="rootPositionSubTitle">- 잡고 이동가능 -</span>',
                      '</div>'

                  ].join(''),
          size: new window.naver.maps.Size(38, 58),
          anchor: new window.naver.maps.Point(55, 72),
      },
      draggable: true
  });
  
  // 기준점 마커를 드래그했을경우, 기준점을 바꿔주고 재검색해준다.
  naver.maps.Event.addListener(rootPointerMarker, "dragend", function (e) {
    var point = e.coord;
    // console.log("마커이동 :" + point.lat());
    // console.log("마커이동 :" + point.lng());
    
    // 기준위치 변경
    curPositionObj.value.latitude = point.lat();
    curPositionObj.value.longitude = point.lng();
    // 검색어를 비우고..
    searchKeyword.value = "";
    // ERP거래처 조회요청
    requestErpClientSel();

  });

};
/**
 * 마커추가 - 차량 현위치
 * @param {차량현위치 객체} targetObj
 */
const addTruckMarker = (targetObj, animteInfo) => {
  let latLng = new window.naver.maps.LatLng(
    targetObj.LATITUDE,
    targetObj.LONGITUDE
  );
  arrTruckBounds.push(latLng);
  // 운행여부에 따라서 테마를 변경하기위한..
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
    title: targetObj.NAME,
    zIndex: 100,
    icon: {
      content: [
        '<div class="marker_truck' + on_off_string + '">',
        '<span style="font-size:17px;">' +
          targetObj.VEHICLENO +
          " (" +
          targetObj.NAME +
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
  });
  if (animteInfo != null) {
    setTimeout(() => {
      truckMarker.setAnimation(animteInfo);
      setTimeout(() => {
        truckMarker.setAnimation(null);
      }, 5000);
    }, 2000);
  }
  // 차량 마커클릭시..
  naver.maps.Event.addListener(truckMarker, "click", function () {
    // 차량객체얻기..
    var selectTruckObj = truckMarker.targetObj;
    if (selectTruckObj.DISPATCH_KEY == null) {
      showAlertSuccess("운행중인 배송내역이 없습니다.");
      return;
    }
  });
  // 차량마커 저장.
  truckMarkerList.push(truckMarker);
  // // 인포윈도우..
  // var htmlString = '';
  // htmlString += '<div style="padding:0px 10px; border-radius:50px;">';
  // htmlString += targetObj.VEHICLENO + ' (' + targetObj.NAME + ')<BR>';
  // htmlString += targetObj.ADDRESS + '<BR>';
  // htmlString += targetObj.TIMESTAMP + '';
  // htmlString += '</div>';
  // truckInfowindow.push(
  //     new window.naver.maps.InfoWindow({
  //     content: htmlString
  //     }),
  // );
  // truckInfowindow[0].open(mapInstance.value, truckMarkerList[0]);
  truckMarkerIndex++;
};
/**
 * 마커추가 - 거래처
 * @param {배차일보 객체} targetObj
 */
const addCustMarker = (targetObj) => {
  try {
    if (targetObj.LAT > 0 && targetObj.LON > 0) {
      let latLng = new window.naver.maps.LatLng(targetObj.LAT, targetObj.LON);
      arrCustBounds.push(latLng);

      // 1.도착시간 확인
      var on_off_string, badge_on_off_string, imageInfoString, clgubun_css;
      on_off_string = "_on";

      if (targetObj.IMAGE_CNT_TODAY > 0) {
        badge_on_off_string = "_on";
        targetObj.inboundClassString = "";
      } else {
        badge_on_off_string = "_off";
        targetObj.inboundClassString = "_NOTIN";
      }
      
      
      imageInfoString = "<small><small>누적 : </small></small>" + targetObj.IMAGE_CNT_TOT;
      clgubun_css = "marker_clgubun_1";

      // 2.거래처마커 추가
      var custerMarker = new window.naver.maps.Marker({
        position: latLng,
        map: mapInstance.value,
        // index: targetObj.ROUTE_SEQ - 1,
        targetObj: targetObj,
        title: targetObj.CLNAME,
        zIndex: 100,
        icon: {
          content: [
            '<div class="marker_cust' + on_off_string + '">',
            '<span class="marker_image_badge' + badge_on_off_string + '">' + targetObj.IMAGE_CNT_TODAY + '</span>',
            '<span class="' +
              clgubun_css +
              '">' +
              targetObj.CLGUBUN +
              '</span><span style="font-size:17px;">' +
              targetObj.CLNAME +
              "</span<BR>",
            '<hr style="margin:0.2rem 0rem 0.1rem 0rem; border:0px; border-top:1px solid #FFFFFF;"/>',
            "" +
              targetObj.DISTANCE_ME_TXT +
              " / " +
              imageInfoString,
            "</div>",
            '<div class="marker_cust_bottom_point"></div>',
            '<div class="marker_cust_bottom_arrow' + on_off_string + '"></div>',
          ].join(""),
          size: new window.naver.maps.Size(38, 58),
          anchor: new window.naver.maps.Point(100, 85),
        },
      });
      // // 3.거래처반경(200미터 설정:자동진입 체크반경)
      // var custCircle = new naver.maps.Circle({
      // 	map: mapInstance.value,
      // 	center: latLng, // 중심점
      // 	radius: 200, // 200 미터
      // 	fillColor: '#4952ff',
      // 	fillOpacity: 0.1,
      // 	strokeWeight : 1,
      // 	strokeColor: '#4952ff9b',
      // });
      // arrCustCircle.push(circle);

      // 4.거래처 마커 클릭시..이벤트..
      naver.maps.Event.addListener(custerMarker, "click", function () {
        console.log(custerMarker.targetObj);
        // 거래처 찾기..
        var infos = rowData;
        var targetObj = null;
        infos.forEach(function (item) {
          if (custerMarker.targetObj.CLCODE == item.CLCODE) {
            targetObj = item;
          }
        });
        // 거래처를 찾았다면..
        if (targetObj != null) {
          // 상세데이타 세팅
          selectRowData.value = targetObj;
          // 상세영역 열기
          detailShow.value = false;
          setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
            // 상세영역 열기
            detailShow.value = true;
          }, 100);  
          
          // 거래처 이미지 조회
          requestErpClientImage("", targetObj.CLCODE);
        }
      });
      // 거래처 마커 마우스오버시..
      naver.maps.Event.addListener(custerMarker, "mouseover", function () {
        // console.log(targetObj.ROUTE_SEQ);
      });
      // 거래처 마커 저장
      custMarkerList.push(custerMarker);
      custkMarkerIndex++;
    }
  } catch (error) {
    console.error(error);
  }
};

// -------------------------------------------------------
// 속도차트 마커관련..
// -------------------------------------------------------
var speedMarkerList = [], speedMarkerIndex = 0;
const onRemoveSpeedMarker = () => {
  //마커 및 타이틀 객체 지우기
  speedMarkerList.forEach((el) => {
    el.setMap(null);
  });
  speedMarkerList = [];

  // 시간 마커 스타일초기화
  document.documentElement.style.setProperty('--time-marker-bg-30', '#a3a3a37f');
  document.documentElement.style.setProperty('--time-marker-bg-60', '#382efe7f');
  document.documentElement.style.setProperty('--time-marker-bg-90', '#fe2e517f');
};






/**
 * 거래처(배송) 경로그리기..
 */
const drawDispatchCustLine = (arrPoints, infos) => {
  try {
    var prevPoint = null,
      nIndex = 0;
    var upperP1, upperP2, lowerP1, lowerP2;

    arrPoints.forEach(function (item) {
      if (nIndex == 0) {
        // 시작은 목원푸드에서부터..
        upperP1 = CENTER_POSITION;
      } else {
        upperP1 = new naver.maps.LatLng([prevPoint.x, prevPoint.y]);
      }
      upperP2 = new naver.maps.LatLng([item.x, item.y]);

      var openArrowLine = new naver.maps.Polyline({
        path: [upperP1, upperP2],
        map: mapInstance.value,
        startIcon: naver.maps.PointingIcon.CIRCLE,
        startIconSize: 10,
        endIcon: naver.maps.PointingIcon.BLOCK_ARROW,
        endIconSize: 20,
        // strokeColor: '#eb2b2b6b',
        // strokeWeight: 6
        clickable: true, // 사용자 인터랙션을 받기 위해 clickable을 true로 설정합니다.
        strokeColor: "#E51D1A",
        strokeStyle: "shortdash", // solid, shortdash, shortdot, shortdashdot, shortdashdotdot, dot, dash, longdash, dashdot, longdashdot, longdashdotdot
        strokeOpacity: 0.5,
        strokeWeight: 5,
      });
      openArrowLine.curItem = infos[nIndex];

      // 노선에 마우스 오버시
      naver.maps.Event.addListener(openArrowLine, "mouseover", function () {
        // 노선-색상변경
        openArrowLine.setOptions({
          strokeColor: "#E51D1A",
          strokeStyle: "solid",
          strokeOpacity: 0.5,
        });
      });

      naver.maps.Event.addListener(openArrowLine, "click", function () {
        // console.log(openArrowLine);
        openArrowLine.setOptions({
          strokeColor: "#755afd",
          strokeStyle: "solid",
          strokeOpacity: 1,
        });
      });

      naver.maps.Event.addListener(openArrowLine, "mouseout", function () {
        openArrowLine.setOptions({
          strokeColor: "#E51D1A",
          strokeStyle: "shortdash",
          strokeOpacity: 0.5,
        });
      });

      arrCustLineList.push(openArrowLine);

      prevPoint = item;
      nIndex++;
    });
  } catch (error) {
    console.log(error);
  }
};

/**
 * 선택된 노선상의 실제 차량의 이동정보를 얻어온후 경로를 그려준다.
 * @param {*} startDtm
 * @param {*} endDtm
 */
var targetRouteInfos = [];
var targetRouteLines = [];


/**
 * 차량(이동) 경로그리기..
 */
const drawTruckRouteLine = (arrPoints, selectRouteYn) => {
  try {
    var prevPoint = null,
      nIndex = 0;
    var upperP1, upperP2, lowerP1, lowerP2;

    arrPoints.forEach(function (item) {
      // console.log(item);
      // 1.시작지점 세팅
      if (nIndex == 0) {
        upperP1 = new naver.maps.LatLng([item.x, item.y]);
        if (!selectRouteYn) {
          // 선택된 경로가 아니라면, 시작은 목원푸드에서부터..
          upperP1 = CENTER_POSITION;
        }
      } else {
        upperP1 = new naver.maps.LatLng([prevPoint.x, prevPoint.y]);
      }
      // 2.종료지점 세팅
      upperP2 = new naver.maps.LatLng([item.x, item.y]);
      // 3.경로객체 생성
      var openArrowLine = new naver.maps.Polyline({
        path: [upperP1, upperP2],
        map: mapInstance.value,
        // startIcon: naver.maps.PointingIcon.CIRCLE,
        // startIconSize: 10,
        // endIcon: naver.maps.PointingIcon.BLOCK_ARROW,
        // endIconSize: 10,
        clickable: true, // 사용자 인터랙션을 받기 위해 clickable을 true로 설정합니다.
        strokeColor: "#755afd",
        strokeWeight: 5,
        strokeStyle: "solid", // solid, shortdash, shortdot, shortdashdot, shortdashdotdot, dot, dash, longdash, dashdot, longdashdot, longdashdotdot
        strokeOpacity: 0.7,
      });
      // 3.1.마자막위치에 화살표 표기추가
      if (nIndex == arrPoints.length - 1) {
        openArrowLine.setOptions(
          "endIcon",
          naver.maps.PointingIcon.BLOCK_ARROW
        );
        openArrowLine.setOptions("endIconSize", 20);
      }

      // 4.경로이벤트 생성
      naver.maps.Event.addListener(openArrowLine, "mouseover", function () {
        // openArrowLine.setOptions({
        // 	strokeColor: '#755afd',
        // 	strokeWeight: 10,
        // 	strokeStyle: 'dot', //
        // 	strokeOpacity: 1
        // });
      });

      // naver.maps.Event.addListener(openArrowLine, 'click', function() {

      // 	console.log(openArrowLine);
      // 	// openArrowLine.setOptions({
      // 	// 	strokeColor: '#eb2b2b6b',
      // 	// 	strokeStyle: 'dash',
      // 	// 	strokeOpacity: 0.5
      // 	// });
      // });

      naver.maps.Event.addListener(openArrowLine, "mouseout", function () {
        // openArrowLine.setOptions({
        // 	strokeColor: '#755afd',
        // 	strokeWeight: 5,
        // 	strokeStyle: 'solid',
        // 	strokeOpacity: 0.7
        // });
      });

      arrRouteLineList.push(openArrowLine);

      prevPoint = item;
      nIndex++;
    });
  } catch (error) {
    console.log(error);
  }
};


/**
 * 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
 */
const resizeScreenSet = () => {
  
  setTimeout(() => {
    var grid_clientlist = document.getElementsByClassName("grid_clientlist")[0];
    var grid_searcharea = document.getElementsByClassName("grid_searcharea")[0];
    var s1 = grid_clientlist.getBoundingClientRect().top;
    var s2 = grid_searcharea.getBoundingClientRect().bottom;
    var innerHeight = window.innerHeight;
    var clientHeight = document.body.clientHeight;

    // console.log("innerHeight=" + window.innerHeight);
    // console.log("clientHeight=" + document.body.clientHeight);
    // console.log("s1=" + s1);
    // console.log("s2=" + s2);
    // console.log("calcHeight=" + calcHeight);

    var calcHeight = 0;
    if (clientHeight == 0) {
        calcHeight = innerHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    } else {
        calcHeight = clientHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    }
    
    var grid_clientlist = document.getElementsByClassName("ag-theme-alpine")[0];
    grid_clientlist.style.height = calcHeight + "px";
    // info_detail.style.display = "flex";
  }, 500);
};

/**
 * 날짜차이만큼 계산해서 화면에 표시..
 * @param {날짜차이} diffDay
 */
const changeDate = (diffDay) => {
  // 현재날짜로 초기화
  if (diffDay == 0) curDate = new Date();

  //console.log(curDate);
  const calcDay = new Date(curDate);
  calcDay.setDate(curDate.getDate() + diffDay * 1);
  // console.log(calcDay);
  //console.log(common_utils.formatTime_HH_MM_SS(calcDay));
  //console.log(common_utils.formatDate_MM_DD(calcDay));
  // 현재날짜 저장..
  curDate = calcDay;
  // 요일구하기
  var dayString = common_utils.getNameofWeek(curDate, 2);
  // 날짜 표시..
  document.getElementsByClassName("day_today")[0].innerHTML =
    '<div style="margin:-40px 0px -20px 0px;">' +
    common_utils.formatDate_MM_DD(curDate) +
    "</div>" +
    '<div style="justify-content:center; font-size:0.8rem; color:#510fc5; border:0px solid red; margin:-20px 0px -20px 0px;">-' +
    dayString +
    "-</div>";
  // 선택된 차량이 있고, 날짜가 변경되었을경우...
  if (curTruckObj != null) {
    
  }
};

/**
 * 배송차량-타이틀,세팅
 * @param {*} targetObj
 */
const setSummaryTitle = (targetObj) => {
  // 배송차량 타이틀 변경
  var htmlString = "";
  htmlString =
    "<p><span>" +
    targetObj.NAME +
    "</span>&nbsp;" +
    "&nbsp;" +
    targetObj.SPEED +
    " <small><small>km/h</small></small>&nbsp;" +
    "<small><small>- " +
    targetObj.ADDRESS +
    "</small></small></p>";
  document.getElementsByClassName("item_title")[0].innerHTML = htmlString;
  // 속도체크-배경색 변경
  if (targetObj.SPEED > 0) {
    // 운행중
    document.getElementsByClassName("item_title")[0].style.backgroundColor =
      "#2879ffE6";
  } else {
    // 정차중
    document.getElementsByClassName("item_title")[0].style.backgroundColor =
      "#989898E6";
  }
  // document.getElementsByClassName('item_title')[0].style.backgroundColor = '#0f089a';
};


/******************************************************************************* api 호출 start */

/**
 * CVO-차량최종위치조회 요청
 */
const requestCvoNowPositionSel = async () => {
  changeDate(0); // 현재날짜 표시/설정
  // 대기창표시하기
  startLoadingBar();
  // 지도, 차량마커 지우기
  onRemoveTruck();
  // 지도, 거래처마커 지우기
  onRemoveCust();
  // 배송사원(차량) 리스트 지우기
  empList.value = [];

  // 배송일지 목록 비우기..
  dispatchSummaryList.value = [];
  

  // 파라미터 구성
  const param = {
    companyCd: "00002",
    deviceNo: "",
    vehicleNo: "",
    keyword: "", // keyword.value,
    lat: curPositionObj.value.latitude,
    lon: curPositionObj.value.longitude,
    inputUser: userId,
  };

  let data, result;
  try {
    result = await operateApi.cvoNowPositionSel(param);
    data = result.data;
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    // table.isLoading = false;
    var areaDataList = document.getElementById("areaDataList");
    setTimeout(() => {
      // 데이타확인 - LOOP
      var infos = data.RecordSet;
      if (infos.length > 0) {
        infos.forEach(function (item) {
          // 차량마커 추가..
          addTruckMarker(item, null);
          // 배송사원리스트 추가..
          addEmpList(item);
        });
        
        // // 차량이 1대이상일경우 영역을 맞추고, 그외 이동시킨다.
        // if (arrTruckBounds.length>1) {
        //   // 차량기준 - 지도영역 조정(마커를 다보이게..)
        //   mapInstance.value.fitBounds(arrTruckBounds);
        // } else {
        //   const latitude = arrTruckBounds[0].y;
        //   const longitude = arrTruckBounds[0].x;
        //   var latLng = new window.naver.maps.LatLng(latitude, longitude);
        //   mapInstance.value.setCenter(latLng);
        // }


        // 화면 갱신정보 업데이트
        var htmlString =
          "<small><small>'아이사랑' 배송사진</small></small><small><small><small><small>" +
          "&nbsp;-&nbsp;Update " +
          common_utils.formatTime_HH_MM_SS(new Date()) +
          "</small></small></small></small>";

        document.getElementsByClassName("info_title")[0].innerHTML = htmlString;

        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
      }
    }, 100);

    // 거래처 검색..그리드..
    requestErpClientSel();
  }
};


/**
 * 거래처위치로 지도이동/줌 - 그리드 클릭이벤트에서 호출
 * @param {*} targetObj 
 */
const moveClientPosition = async (targetObj) => {
  setTimeout(() => {
    // 지도이동 - 거래처위치
    var latLng = new window.naver.maps.LatLng(
      targetObj.LAT,
      targetObj.LON
    );
    mapInstance.value.setCenter(latLng);
    mapInstance.value.setZoom(16);
  }, 500);
};

/**
 * 배송리스트에 - 납품사진 마우스 클릭시..
 * @param {*} index 
 * @param {*} targetObj 
 */
const showDeliveryImage = async (index, targetObj) => {
  console.log(index);
  console.log(targetObj);
};

/**
 * 배송리스트 - 배경색 초기화(선택안함 처리)
 * @param {*} index
 * @param {*} targetObj
 */
const resetDispatchSummaryList = async (index, targetObj) => {
  // // 선택한 거래처의 뒷 배경색만 변경해주고 나머지는 초기화(없이) 해준다.
  // var elementLength = document.getElementsByClassName('deliveryCustArea_Bg').length;
  // for(var i=0; i<elementLength; i++) {
  // 	document.getElementsByClassName('deliveryCustArea_Bg')[i].style.backgroundColor='#fffec400';
  // }

  // 선택한 거래처의 뒷 배경색만 변경해주고 나머지는 초기화(없이) 해준다.
  var elementLength = document.getElementsByClassName(
    "deliveryCustArea_Bg"
  ).length;
  for (var i = 0; i < elementLength; i++) {
    if (index == i) {
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.borderTop = "1px solid #c6a1ed";
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.borderBottom = "1px solid #c6a1ed";
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.backgroundColor = "#deffb877";
    } else {
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.borderTop = "0px solid #c6a1ed";
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.borderBottom = "0px solid #c6a1ed";
      document.getElementsByClassName("deliveryCustArea_Bg")[i].style.backgroundColor = "#fffec400";
    }
  }
};

/**
 * 경로조회
 */
const doRouteSearch = async (deviceNo, startDtm, endDtm) => {
  // console.log(deviceNo);
  // console.log(startDtm);
  // console.log(endDtm);

  // 기존경로 지우기
  onRemoveTruckRoute();
  // 대기창표시
  startLoadingBar();
  // 파라미터 매핑
  const param = {
    deviceNo: deviceNo, // "01055013694",
    traceStartDtm: startDtm, // "20230907040000",
    traceEndDtm: endDtm, // "20230907235959"
  };
  // 데이타 요청.
  let data, result;
  try {
    result = await operateApi.cvoTraceSel(param);
    data = result.data;
    // 데이타 세팅...
    if (data.RecordCount > 0) {
    }
  } catch (error) {
    console.error(error);
  } finally {
    // 로딩바숨기기..
    removeLoadingBar();
    // 경로데이타 초기화
    routeInfoList = []; // 경로데이타(부가정보포함)
    arrRouteBounds = []; // 위치정보

    // 타이머..실행
    setTimeout(() => {
      // 데이타 - LOOP
      var infos = data.RecordSet;
      infos.forEach(function (item) {
        // console.log(item);
        routeInfoList.push(item);
        // 경로위치정보 객체 저장..(그리기용)
        let latLng = new window.naver.maps.LatLng(
          item.LATITUDE,
          item.LONGITUDE
        );
        arrRouteBounds.push(latLng);
      });

      // 전체 경로 그리기
      //drawTruckRouteLine(arrRouteBounds, false);
    }, 100);
  }
};

/**
 * ERP - 거래처 조회..
 */
var curCircle;
const clientList = ref([]); // 거래처 목록
const requestErpClientSel = async () => {
  //조회

  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD': "00002",
      '@I_DEVICENO': "",
      '@I_BOUND_DIST': curPositionObj.boundDist,
      '@I_KEYWORD': searchKeyword.value ?? "",
      '@I_CENTER_LAT': curPositionObj.value.latitude,
      '@I_CENTER_LON': curPositionObj.value.longitude,
  }
  
  const arrPrvModels = document.getElementsByClassName("image_summ_card");
  for (var i = 0; i < arrPrvModels.length; i++) {
    // 기존요소 삭제
    document.getElementsByClassName("image_summ_area")[0].removeChild(arrPrvModels[i]);
  }
  

  // 1.거래처 목록 지우기
  clientList.value = [];
  // 2.그리드 데이타 지우기
  rowData.splice(0);
  // 3.지도, 거래처마커 지우기
  onRemoveCust();
  // 4.지도, 반영원 지우기
  if (curCircle != null) curCircle.setMap(null);
  // 5.지도, 반영원 그리기
  var centerLatLng = new window.naver.maps.LatLng(curPositionObj.value.latitude, curPositionObj.value.longitude);
  curCircle = new window.naver.maps.Circle({
    map: mapInstance.value,
    center: centerLatLng,
    radius: curPositionObj.boundDist*1000,    
    fillColor: '#4952ff',
    fillOpacity: 0.1,
    strokeWeight : 1,
    strokeColor: '#4952ff9b',
  });
  // 6.기준점 마커 표시 세팅..
  rootPositionMarkerSet(centerLatLng);
  
  // 7.API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosErp().post(`/ERP_CLIENT_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      // 그리드용 데이타 푸쉬
      rowData.push(...data.RecordSet);
      // 그리드에 세팅..
      gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
        rowData
      );
      // 거래처 마커추가작업
      var infos = data.RecordSet;
      // 검색기준위치
      var searchPosition;
      if (infos.length > 0) {
        infos.forEach(function (item) {
          // 거래처,마커추가
          addCustMarker(item);

          // 당일이미지가 있는경우..
          if (item.IMAGE_CNT_TODAY > 0) {
            // 거래처 목록에 추가..
            clientList.value.push(item);
          }
        });
        
        // 지도영역 조정(마커를 다보이게..)
        mapInstance.value.fitBounds(arrCustBounds);
        // 현재맵 기준위치
        //searchPosition = mapInstance.value.getCenter();

        // 하단 이미지요약정보 보기/감추기
        var image_summ_area = document.getElementsByClassName("image_summ_area")[0];
        if (clientList.value.length>0) {
          image_summ_area.style.display = "flex";
        } else {
          image_summ_area.style.display = "none";
        }
      } else {
        console.log("검색기준위치로이동...");
        searchPosition = new window.naver.maps.LatLng(curPositionObj.value.latitude, curPositionObj.value.longitude)
      }
      
      // // 지도중심이동
      // mapInstance.value.setCenter(searchPosition);
      
      // 줌레벨 판단
      var distance = curPositionObj.boundDist*1;
      switch (distance) {
        case 1: mapInstance.value.setZoom(16); break;
        case 3: mapInstance.value.setZoom(14); break;
        case 5: mapInstance.value.setZoom(13); break;
        case 10: mapInstance.value.setZoom(12); break;
        case 30: mapInstance.value.setZoom(11); break;
        default:
          mapInstance.value.setZoom(16); 
          break;
      }
      


    } else {
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    
    // 거래처 코드가 넘어왔다면..
    if (bFistYn && REQ_CLCODE.value != null) {
        bFistYn = false;
        // 1.검색어를 비우고..
        searchKeyword.value = "";
        
        // 2.거래처 찾기..
        var infos = rowData;
        var targetObj = null;
        infos.forEach(function (item) {
          if (item.CLCODE == REQ_CLCODE.value) {
            targetObj = item;
          }
        });
        // 3.거래처를 찾았다면..
        if (targetObj != null) {
            // 1.그리드 클릭이벤트 효과
            var event = {};
            event.data = targetObj;
            cellWasClicked(event);
            // 2.왼쪽을 접어준다.
            //onClickFoldButton();
        }

    }
  }
};


/**
 * ERP - 거래처 이미지 조회..
 */
const clientImageList = ref([]); // 거래처 이미지 목록
const requestErpClientImage = async (ddate, clcode) => {
  
  // 대기팝업 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD': "00002",
      '@I_DDATE': ddate,
      '@I_CLCODE': clcode,
      '@I_CLNAME': "",
  }
  // 거래처 이미지 목록 비우기..
  clientImageList.value = [];

  // API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosErp().post(`/CVO_623_CLIENTIMAGE_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      
      // 
      var infos = data.RecordSet;
      if (infos.length > 0) {
        var showDateYn = true, prevDate = "";
        infos.forEach(function (item) {
          var ddate = item.DDATE;
          item.DDATE_HTML = "" 
            + ddate.substring(4,6) 
            + "/" 
            + ddate.substring(6,8);
          // 날짜 체크
          if (prevDate == "") {
            prevDate = item.DDATE; // 날짜저장
            showDateYn = true; // 날짜표시
          } else {
            if (prevDate != item.DDATE) { // 이전날짜와 다를경우..
              prevDate = item.DDATE; // 날짜저장
              showDateYn = true; // 날짜표시
            } else {
              showDateYn = false; // 날짜숨김
            }
          }
          // 날짜표시여부 추가세팅
          item.showDateYn = showDateYn;
          // 거래처 이미지 목록 푸쉬..
          clientImageList.value.push(item);
          // console.log(item);
/**
 * 
            "GEONUM": 46,
            "COMPANYCD": "00002               ",
            "DDATE": "20231011  ",
            "DISPATCH_ID": null,
            "CLCODE": "0000000399          ",
            "TYPE": "300       ",
            "URL": "http://sjwas.gzonesoft.co.kr:27004/file/delivery/2023/10/11/00002_20231011_0000000399_0004_300_012325.jpg",
            "SEQ": 3,
            "FILE_NO": 294,
            "WS_NEWDATE": "2023-10-11 01:23:25",
            "WS_NEWUSER": "0004                ",
            "WS_EDTDATE": null,
            "WS_EDTUSER": null,
            "D_IMG_CNT": 3
 */
        });
      }
    }
  } catch (error) {
    console.error(error);
  } finally {
    // 대기팝업 제거
    removeLoadingBar();
  }
};


const doSave = async () => {
  //저장

  const param = {
    geonum: selectRowData.value.GEONUM,
    clcode: "", // selectRowData.value.CLCODE,
    userName: selectRowData.value.USER_NAME,
    userPhone: selectRowData.value.USER_PHONE,
    companyName: selectRowData.value.CLNAME,
    companyCorpno: selectRowData.value.CLSAUPNO,
    password: selectRowData.value.PASSWORD,
    email: selectRowData.value.EMAIL,
    status: selectRowData.value.STATUS,
    osType: selectRowData.value.OS_TYPE ?? "",
    joinType: "002",
    inputUser: userId,
    payYn: selectRowData.value.PAY_YN,
  };

  let data;
  try {
    data = await memberApi.memberMngSave(param);
    if (data.ResultCode === "00") {
      showAlertSuccess("저장되었습니다.");
      requestErpClientSel();
    }
  } catch (error) {
    console.error(error);
  }
};
/******************************************************************************* api 호출 end */

/******************************************************************************* 버튼 및 액션 이벤트 start */
var gridOptions = {
	columnDefs: columnDefs.value,
	rowData: rowData.value,
	defaultColDef:defaultColDef.value,
	rowSelection: 'multiple', /* 'single' or 'multiple',*/
	animateRows:true,
	copyHeadersToClipboard:true,
	enableColResize: true,
	enableSorting: true,
	enableFilter: true,
	enableRangeSelection: true,
	suppressColumnMoveAnimation:true,
	suppressRowClickSelection: false,
	suppressHorizontalScroll: true,
	localeText: {noRowsToShow: '조회 결과가 없습니다.'},
	getRowStyle: function (param) {
		if (param.node.rowPinned) {
			return {'font-weight': 'bold', background: '#dddddd'};
		}
		return {'text-align': 'center'};
	},
	getRowHeight: function(param) {
		if (param.node.rowPinned) {
			return 30;
		}
		return 24;
	},
	// GRID READY 이벤트, 사이즈 자동조정
	onGridReady: function (event) {
		event.api.sizeColumnsToFit();
	},
	// 창 크기 변경 되었을 때 이벤트
	onGridSizeChanged: function(event) {
		event.api.sizeColumnsToFit();
	},
	onRowEditingStarted: function (event) {
		console.log('never called - not doing row editing');
	},
	onRowEditingStopped: function (event) {
		console.log('never called - not doing row editing');
	},
	onCellEditingStarted: function (event) {
		console.log('cellEditingStarted');
	},
	onCellEditingStopped: function (event) {
		console.log('cellEditingStopped');
	},
	onRowClicked : function (event){
		console.log('onRowClicked');
	},
	onCellClicked : function (event){
		console.log('onCellClicked');
	},
	isRowSelectable : function(event){
		console.log('isRowSelectable');
		return true;
	},
	onSelectionChanged : function (event) {
		console.log('onSelectionChanged');
	},
	onSortChanged: function (event) {
		console.log('onSortChanged');
	},
	onCellValueChanged: function (event) {
		console.log('onCellValueChanged');
	},
	getRowNodeId : function(data) {
		return null;
	},
	// 리드 상단 고정
	setPinnedTopRowData: function(data) {
		return null;
	},
	// 그리드 하단 고정
	setPinnedBottomRowData: function(data) {
		return null;
	},
	// components:{
	//     numericCellEditor: NumericCellEditor,
	//     moodEditor: MoodEditor
	// },
	debug: false
};

const setSelectRowData = (data) => {
  selectRowData.value = {};
  selectRowData.value.CLNAME = data.CLNAME;
  selectRowData.value.CLSAUPNO = data.CLSAUPNO;
  selectRowData.value.USER_NAME = data.USER_NAME;
  selectRowData.value.USER_PHONE = data.USER_PHONE;
  selectRowData.value.EMAIL = data.EMAIL;
  selectRowData.value.STATUS = data.STATUS;
};

const cellValueChanged = (event) => {
  setSelectRowData(event.data);
};

/**
 * 그리드셀-더블 클릭시..
 * @param {*} event 
 */
const cellWasDblClicked = (event) => {

};
/**
 * 그리드셀-클릭시..
 * @param {} event 
 */
const cellWasClicked = (event) => {
  // 기존상세창을 닫아준다.
  detailShow.value = false;

  setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
    var targetObj = event.data;
    // 상세데이타 세팅
    selectRowData.value = targetObj;
    // 상세영역 열기
    detailShow.value = true;
    // 거래처 이미지 조회
    requestErpClientImage("", targetObj.CLCODE);
    // 거래처위치로 지도이동/줌 
    moveClientPosition(targetObj);
  }, 100);  
};
const cellFocused = (event) => {
  setSelectRowData(rowData[event.rowIndex]);
};
const deselectRows = () => {
  gridApi.value.deselectAll();
};
const onBtnExport = () => {
  gridApi.value.exportDataAsCsv();
};
const getState = () => {
  console.log(gridApi.value.getAllColumns());
};

const onGridReady = (params) => {
  gridApi.value = params.columnApi;
};

const tableLoadingFinish = (elements) => {};

// 반경거리콤보 선택시..
const optionBoundChange = () => {
  
  detailShow.value = false; // 우측상세 여역 닫기
  curPositionObj.boundDist = event.target.value; // 반경거리 세팅하기
  //console.log(curPositionObj.boundDist);
  
  searchKeyword.value = ""; // 키워드검색어 지우기..
  // 기준점, 마커지우기
  if (rootPointerMarker != null) rootPointerMarker.setMap(null);

  // ERP거래처 요청하기..
  requestErpClientSel();
};
// 검색창에서 키보드 입력시..
const searchBtnEnter = (event) => {
  //검색 Btn
  if (event.keyCode != 13) return;
  rowData.splice(0);
  detailShow.value = false;
  
  requestErpClientSel();
};
const searchBtn = (keyword) => {
  //검색 Btn
  rowData.splice(0);
  detailShow.value = false;
  pageNumber.value = 1;
  requestErpClientSel();
};
const cancleBtn = (rowData) => {
  //닫기 Btn
  detailShow.value = false;
};
const saveBtn = (rowData) => {
  //저장 Btn
  doSave();
};

const setHIde = () => {
  appOption.appHeaderHide = !appOption.appHeaderHide;
  appOption.appContentFullHeight = !appOption.appContentFullHeight;
};

const getHeaderData = () => {
  const headerData = [];

  for (const columnDef of columnDefs.value) {
    headerData.push({ [columnDef.field]: columnDef.headerName });
  }

  return headerData;
};

/******************************************************************************* 버튼 및 액션 이벤트 end */
// 대상 Element 선택
let resizer = "";
let leftSide = "";
let rightSide = "";

// 마우스의 위치값 저장을 위해 선언
let x = 0;
let y = 0;

// 크기 조절시 왼쪽 Element를 기준으로 삼기 위해 선언
let leftWidth = 0;

// resizer에 마우스 이벤트가 발생하면 실행하는 Handler
const mouseDownHandler = (e) => {
  // 대상 Element 선택
  resizer = document.getElementById("dragMe");
  leftSide = resizer.previousElementSibling;
  rightSide = resizer.nextElementSibling;

  // 마우스 위치값을 가져와 x, y에 할당
  x = e.clientX;
  y = e.clientY;
  // left Element에 Viewport 상 width 값을 가져와 넣음
  leftWidth = leftSide.getBoundingClientRect().width;

  // 마우스 이동과 해제 이벤트를 등록
  document.addEventListener("mousemove", mouseMoveHandler);
  document.addEventListener("mouseup", mouseUpHandler);
};

const mouseMoveHandler = (e) => {
  // 마우스가 움직이면 기존 초기 마우스 위치에서 현재 위치값과의 차이를 계산
  const dx = e.clientX - x;
  const dy = e.clientY - y;

  // 크기 조절 중 마우스 커서를 변경함
  // class="resizer"에 적용하면 위치가 변경되면서 커서가 해제되기 때문에 body에 적용
  document.body.style.cursor = "col-resize";

  // 이동 중 양쪽 영역(왼쪽, 오른쪽)에서 마우스 이벤트와 텍스트 선택을 방지하기 위해 추가
  leftSide.style.userSelect = "none";
  leftSide.style.pointerEvents = "none";

  rightSide.style.userSelect = "none";
  rightSide.style.pointerEvents = "none";

  // 초기 width 값과 마우스 드래그 거리를 더한 뒤 상위요소(container)의 너비를 이용해 퍼센티지를 구함
  // 계산된 퍼센티지는 새롭게 left의 width로 적용
  const newLeftWidth =
    ((leftWidth + dx) * 100) / resizer.parentNode.getBoundingClientRect().width;
  leftSide.style.width = `${newLeftWidth}%`;
};

const mouseUpHandler = () => {
  // 모든 커서 관련 사항은 마우스 이동이 끝나면 제거됨
  resizer.style.removeProperty("cursor");
  document.body.style.removeProperty("cursor");

  leftSide.style.removeProperty("user-select");
  leftSide.style.removeProperty("pointer-events");

  rightSide.style.removeProperty("user-select");
  rightSide.style.removeProperty("pointer-events");

  // 등록한 마우스 이벤트를 제거
  document.removeEventListener("mousemove", mouseMoveHandler);
  document.removeEventListener("mouseup", mouseUpHandler);
};

var scrollYN = false;
var direction = 1;
const startMq_L = () => {
  direction = -5;
  scrollYN = true;
  startScroll();
};
const startMq_R = () => {
  direction = 5;
  scrollYN = true;
  startScroll();
};
const stopMq = () => {
  scrollYN = false;
};
function startScroll() {
  if (scrollYN) {
    var tarDOM = document.getElementById("empListDiv");
    var maxscroll = tarDOM.scrollWidth;
    tarDOM.scrollLeft = tarDOM.scrollLeft + direction;
    setTimeout(() => {
      startScroll();
    }, 10);
  }
}


const disabled = ref(false)

function warnDisabled() {
  disabled.value = true
  setTimeout(() => {
    disabled.value = false
  }, 1500)
}

/**
 * 이미지 상세보기
 */
import ClientImageView from "@/components/popup/ClientImageView.vue";
const selectImageObj = ref([]);
const selectImageObjIndex = ref(0); // 선택한 인덱스
const openClientImageView = (targetObj, idx) => {
  
  console.log("선택한인덱서 = " + idx);
  
  targetObj.CUR_INDEX = idx; // // 현재 인덱스
  targetObj.ORG_DATALIST = clientImageList; // 전체 데이타리스트
  targetObj.QR_CODE_RES = "http://sjwas.gzonesoft.co.kr:27002/api/GzoneUtilController/getQrImage?dataString=" + targetObj.URL;

  selectImageObj.value = [];
  selectImageObj.value.push(targetObj);
  
  // document.getElementById("curImage").src = selectImageObj.URL;
  // document.getElementById("curDateInfo").innerHTML = selectImageObj.WS_NEWDATE;
  document.getElementById("modalClientImagePopDiv").click();
  
}; 
</script>
<template>
  <div class="section__management_ilove">
    <div class="group__contents_ilove">
      <!-- 1.그리드, 거래처리스트 -->
      <div class="grid_clientlist">
        <!-- 타이틀 -->
        <div class="info_title" style="text-align: center;"></div>
        <!-- 날짜지정 -->
        <div style="display: flex; flex-direction: column; border-bottom: 1px solid #0b048678;">
          <div data-aos="fade-down" class="info_date" style="display:flex; padding-left: 0.2rem;">
            <span data-aos="fade-right" data-aos-delay="600" class="day_backward" style="display:flex;" @click.prevent="changeDate(-2)">-2<small><small><small>일</small></small></small></span>
            <span data-aos="fade-right" data-aos-delay="300" class="day_backward" style="display:flex;" @click.prevent="changeDate(-1)">-1<small><small><small>일</small></small></small></span>
            <span data-aos="fade-down" data-aos-delay="100" class="day_today" style="text-align: center; margin-top:0.5rem;"></span>
            <span data-aos="fade-left" data-aos-delay="300" class="day_forward" style="display:flex;" @click.prevent="changeDate(1)"><small><small>+</small></small>1<small><small><small>일</small></small></small></span>
            <span data-aos="fade-left" data-aos-delay="600" class="day_forward" style="display:flex;" @click.prevent="changeDate(2)"><small><small>+</small></small>2<small><small><small>일</small></small></small></span>
          </div>
        </div>
        <!-- 배송사원목록 -->
        <div class="info_emplist" style="display:flex; border-bottom: 1px solid #0b048678;">
            <div @mouseover="startMq_L()" @mouseout="stopMq()" class="mq_arrow" style="align-items: center; display: flex; margin-right: 0.1rem;">◀︎</div>
            <div id="empListDiv">
              <div
                data-aos="fade-up" :data-aos-delay="i*200"
                class="item_emp"
                v-for="(targetObj, i) in empList"
                v-bind:key="i"
              >
                <div :class="'item_emp_header' + targetObj.on_off_string">
                  <small
                    ><small> {{ targetObj.VEHICLENO }} - </small></small
                  >
                  <small> {{ targetObj.NAME }} </small>
                </div>
                <hr
                  style="
                    margin: 0px;
                    border: 0px;
                    border-top: 1px solid #ffffff;
                  "
                />
                <div :class="'item_emp_body' + targetObj.on_off_string">
                  <img
                    src="/assets/img/map/truck_white_002.png"
                    style="
                      height: 22px;
                      width: auto;
                      margin: 9px 15px 8px 0px;
                    "
                  />
                  | {{ targetObj.SPEED }}
                  <small
                    ><small><small>&nbsp;km/h</small></small></small
                  >
                </div>
                <template v-if="targetObj.배송현황 == '배송대기'">
                  <div class="item_emp_footer_off">
                    {{ targetObj.status_text }}
                  </div>
                  <hr
                    style="
                      margin: 0px 2px;
                      border: 0px;
                      border-top: 1.5px solid #989898e6;
                    "
                  />
                  <div
                    style="
                      margin-top: -2px;
                      justify-content: center;
                      font-weight: 600;
                      color: #989898e6;
                    "
                  >
                    <small
                      ><small>최종 : {{ targetObj.TIMESTAMP }} </small></small
                    >
                  </div>
                </template>
                <template v-else>
                  <div class="item_emp_footer">
                    {{ targetObj.배송현황
                    }}<small
                      ><small
                        >&nbsp;-&nbsp;{{ targetObj.status_text }}</small
                      ></small
                    >
                  </div>
                  <hr
                    style="
                      margin: 0px 0px;
                      border: 0px;
                      border-top: 1.5px solid #0f089a;
                    "
                  />
                  <div
                    style="
                      margin-top: -2px;
                      justify-content: center;
                      font-weight: 600;
                    "
                  >
                    <small
                      ><small>최종 : {{ targetObj.TIMESTAMP }} </small></small
                    >
                  </div>
                </template>
              </div>
            </div>
            <div @mouseover="startMq_R()" @mouseout="stopMq()" class="mq_arrow_r" style="align-items: center; display: flex; margin-left: 0.1rem;">▶︎</div>
        </div>
        <div class="grid_searcharea" style="border-bottom: 1px solid #0b048678;">
          <input type="text" v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="검색어를 입력하세요"/>
          <div class="item__buttons" style="width: 5rem;">
            <button class="btn_search" @click="requestErpClientSel">
              <i class="fa-regular fa-circle-check"></i><span>검 색</span>
            </button>
          </div>
        </div>
        <!-- 그리드 -->
        <AgGridVue
          class="ag-theme-alpine"
          style="height:auto; width:auto; margin-top:0.1rem; background-color: #0b048653; padding:0.05rem;"
          :columnDefs="columnDefs.value"
          :rowData="rowData"
          :defaultColDef="defaultColDef"
          suppressColumnVirtualisation="true"
          animateRows="true"
          copyHeadersToClipboard="true"
          @cell-dblclicked="cellWasDblClicked"
          @cell-clicked="cellWasClicked"
          @cell-focused="cellFocused"
          @grid-ready="onGridReady"
          @cell-value-changed="cellValueChanged"
          >
          </AgGridVue>
      </div>
      <!-- 접기버튼 -->
      <button class="btn_fold_ilove" @click="onClickFoldButton()"><i class="fa-solid fa-arrow-left"></i></button>
      <!-- 2.지도, 거래처/사진 위치 -->
      <div class="dispatch_summary" style="gap: 0px 0px; padding: 0px; margin: 0px; overflow: hidden;">
        <div style="display: flex; width: 100%; height: 100%">       
          <!-- 1.배차정보영역 -->
          <div class="area_summary" style="width: 30%; height: auto; display: none;">
            
            
            <!-- 배송정보,타이틀 -->
            <div class="item_title">- 배송차량을 선택해주세요 -</div>
            
            
          </div>
          <!-- 2.지도정보/리스트영역 -->
          <div style="width: 100%; border: 0px solid gray">
            <!-- 지도영역 -->
            <div class="area_map" style="position: relative;">
              <NaverMap style="height: 100%; width: 100%" />
              <!-- 새로고침 버튼 추가 -->
              <div style="position: absolute; left: 1rem; top: 0.5rem;">
                <img class="cvo_map_refresh" @click.prevent="requestCvoNowPositionSel" src="/assets/img/map/refresh_blue_01.png"/>
              </div>
              <!-- 반경콤보 -->
              <div style="position: absolute; left:4.5rem; top: 0.9rem;">
                <select class="bound_select" v-model="boundDist" @change="optionBoundChange">
                    <option value="1">반경 1km</option>
                    <option value="3">반경 3km</option>
                    <option value="5">반경 5km</option>
                    <option value="10">반경 10km</option>
                    <option value="30">반경 30km</option>
                </select>
              </div>
              <!-- 하단,사진정보 -->
              <div class="image_summ_area" data-aos="fade-up" data-aos-delay="300">
                <div class="image_summ_card" v-for="(targetObj, i) in clientList" v-bind:key="i" @click="onClickImageCard(targetObj)" data-aos="flip-left">
                  <span v-show="targetObj.IMAGE_CNT_TODAY>0">{{ targetObj.CLNAME }}</span>
                  <img v-show="targetObj.IMAGE_CNT_TODAY>0" :src="targetObj?.LAST_IMAGE_URL" />
                  <div class="image_summ_badge" v-show="targetObj.IMAGE_CNT_TODAY>1">{{ targetObj.IMAGE_CNT_TODAY }}</div>
                </div>
              </div>
            </div>
            

          </div>
        </div>
      </div>
      <!--3.그리드, 거래처상세영역 -->
      <div class="grid_data_detail" v-if="detailShow" style="height:auto; margin-bottom:0.5rem; background-image: url('/assets/img/map/bg_tile_logo_200_001_fade_10.png');">
        <div class="client_detail_area" data-aos="flip-left" data-aos-delay="50">
          <!-- 거래처 상세정보 -->
          <div class="item__contents">
            <div data-aos="fade-up" :data-aos-delay="100" style="display:flex; flex-direction: column;" v-if="selectRowData.CLNAME != ''">
              <label class="form-label">거래처명 - {{ selectRowData.CLCODE }}</label>
              <input type="text" v-model="selectRowData.CLNAME" readonly/>
            </div>
            <div data-aos="fade-up" :data-aos-delay="200" style="display:flex; flex-direction: column;">
              <label class="form-label">사업자번호</label>
              <input type="text" v-model="selectRowData.CLSAUPNO" readonly/>
            </div>
            <div data-aos="fade-up" :data-aos-delay="300" style="display:flex; flex-direction: column;">
              <label class="form-label">전화번호</label>
              <input type="text" v-model="selectRowData.CLTEL" readonly/>
            </div>
            <div data-aos="fade-up" :data-aos-delay="400" style="display:flex; flex-direction: column;">
              <label class="form-label">주소</label>
              <input type="text" v-model="selectRowData.CLJUSO1" readonly/>
            </div>
            <!-- <div style="display:flex; flex-direction: column;">
              <label class="form-label">주소2</label>
              <input type="text" v-model="selectRowData.CLJUSO2" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">주소3</label>
              <input type="text" v-model="selectRowData.CLJUSO3" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">주소4</label>
              <input type="text" v-model="selectRowData.CLJUSO4" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">위도</label>
              <input type="text" v-model="selectRowData.LAT" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">경도</label>
              <input type="text" v-model="selectRowData.LON" />
            </div> -->
          </div>
          <!-- 분리선 -->
          <hr style="margin: 0.2rem;"/>
          <!-- 거래처이미지 리스트 -->
          <div class="client_image_area" data-aos="flip-right" data-aos-delay="100">
            <div class="client_image_set" v-for="(targetObj, i) in clientImageList" v-bind:key="i">
              <template v-if="targetObj.showDateYn">
                <div class="item_ddate" @click="openClientImageView(targetObj, i)">
                  <span>{{ targetObj.DDATE_HTML }}<BR/>({{ targetObj.D_IMG_CNT }})</span>
                </div>
              </template>
              <div @click="openClientImageView(targetObj, i)">
                <img class="item_image" :src="targetObj.URL">
              </div>
            </div>
          </div>
        </div>
        <div class="item__buttons" style="margin-top:0.5rem;">
          <button class="btn_close" @click="cancleBtn">
            <i class="fa-regular fa-circle-check"></i><span>닫기</span>
          </button>
          <!-- <button class="btn_save" @click="saveBtn">
            <i class="fa-regular fa-circle-check"></i><span>저장</span>
          </button> -->
        </div>
      </div>


    </div>
     <!-- 이미지 보기(모달창 띄우기) -->
    <div id="modalClientImagePopDiv" data-bs-toggle="modal" data-bs-target="#pdfPrintModal"  style="display: none"  data-backdrop="static"  data-keyboard="false"  />
    <div class="modal fade"   data-bs-target="#pdfPrintModal"   id="pdfPrintModal"   style="z-index: 1051 !important"   data-backdrop="static"   data-keyboard="false"  >
      <div class="modal-dialog modal-xl setModalCenter" style="margin:0px auto"  >
        <ClientImageView :selectImageObj="selectImageObj"></ClientImageView>
      </div>
    </div>
    
  </div>
  
</template>


<style lang="scss">



.sample_work {
  background-color: #5da3f7c9;
  background-color: #a0ff528f;
  background-color: #4952ff;
  background-color: #4952ff9b;
  background-color: #8bff3d;
  background-color: #220d7ddd;
}





/* 입장 애니메이션 */
/* 시작 스타일 */
.fade-enter-from {
  opacity: 0;
}
/* transition */
.fade-enter-active {
  transition: all 0.3s;
}
/* 끝 스타일 */
.fade-enter-to {
  opacity: 1;
}

/* 퇴장 애니메이션 */
/* 시작 스타일 */
.fade-leave-from {
  opacity: 1;
}
/* transition */
.fade-leave-active {
  transition: all 0.3s;
}
/* 끝 스타일 */
.fade-leave-to {
  opacity: 0;
}

.shake {
  animation: shake 0.82s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
  transform: translate3d(0, 0, 0);
}
@keyframes shake {
  10%,
  90% {
    transform: translate3d(-1px, 0, 0);
  }

  20%,
  80% {
    transform: translate3d(2px, 0, 0);
  }

  30%,
  50%,
  70% {
    transform: translate3d(-4px, 0, 0);
  }

  40%,
  60% {
    transform: translate3d(4px, 0, 0);
  }
}



// 고정열 배경색
// .lock-pinned {
//     background: #eef0ff;
// }
// 헤더 모서리 라운딩
.ag-header {
  border-radius: 0px;
}
// 테두리
.ag-root-wrapper {
  border: 0;
}
// 타이틀
.ag-theme-alpine {
  --ag-grid-size: 4px;
  --ag-list-item-height: 5px;
  --ag-header-foreground-color: rgb(255, 255, 255);
  --ag-header-background-color: #0998ea;
  --ag-font-size: 0.6rem;
}
// 헤더 텍스트 정렬
.ag-header-cell-label {
  justify-content: center;
}
.ag-theme-balham .ag-icon-menu:before,
.ag-theme-balham .ag-icon-asc:before,
.ag-theme-balham .ag-icon-desc:before,
.ag-theme-balham .ag-icon-filter:before {
  color: #ffffff;
}
.resizer {
  background-color: #eb2b2b6b;
  cursor: ew-resize;
  width: 3px;
  margin: 0px 10px;
}
</style>