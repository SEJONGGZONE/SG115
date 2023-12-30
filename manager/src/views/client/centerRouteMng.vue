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

/* (지도에서)주소로 지도이동 */
const checkAddress = () => {
  cvoGeocodeSel();
};


// 주소검색창에서 키보드 입력시..
const searchAddressEnter = (event) => {
  //검색 Btn
  if (event.keyCode != 13) return;
  cvoGeocodeSel();
};


/* 좌측영역 접기 */
let foldYn = false;
const onClickFoldButton = () => {
    var leftArea = document.getElementsByClassName("grid_clientmnglist")[0]; // 접기 버튼
    var arrowIco = document.getElementsByClassName("btn_fold_ilove")[0]; // 접기 화살표
    var mapArea = document.getElementsByClassName("area_map")[0]; // 지도영역
    
    
    if (foldYn) {
        leftArea.style.width = "30rem";
        arrowIco.style.transform = 'rotate(0deg)';
        foldYn = false;
        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
    } else {
        leftArea.style.width = "0rem";
        mapArea.style.width = "100%";
        arrowIco.style.transform = 'rotate(180deg)';
        foldYn = true;
        // 네이머지도가 일부영역이 이상하게 동작하여 리사이즈 이벤트를 강제호출..
        window.dispatchEvent(new Event('resize'));
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
 * 전역변수 관리
 */
const FROM_CENTER_OBJ = ref({}) // 출발센터 객체
const TO_CENTER_OBJ = ref({}) // 출발센터 객체
const ORG_POSITION_OBJ = ref({}) // 원래 위치정보 관련객체
const CUR_POSITION_OBJ = ref({}) // 위치정보 관련객체


const gridApi = ref(null);
const gridApi2 = ref(null);
const gridApi3 = ref(null);
const onGridReady2 = (params) => {
  gridApi2.value = params.columnApi;
};
const onGridReady3 = (params) => {
  gridApi3.value = params.columnApi;
};
const rowData = reactive([]);
const rowDataRoute = reactive([]);
const rowDataAlarm = reactive([]);
const rowDataRouteApi = reactive([]);
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

// 센터관리 그리드
const columnDefs = reactive({
  value: [
    {
      headerName: "NO",
      field: "RANK",
      pinned: "left",
      lockPinned: true,
      cellClass: "header-center",
      editable: false,
      valueGetter: "node.rowIndex + 1",
      cellStyle: { textAlign: "center" },
      width: 50,
    },
    // {
    //   headerName: "센터코드",
    //   field: "SPOT_CD",
    //   pinned: "left",
    //   lockPinned: true,
    //   cellClass: "lock-pinned",
    //   cellStyle: { textAlign: "left" },
    //   width: 150,
    // },
    {
      headerName: "출발센터",
      field: "SPOT_NAME",
      pinned: "left",
      lockPinned: true,
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 100,
    },
    {
      headerName: "주소",
      field: "ADDRESS",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
    {
      headerName: "전화번호",
      field: "PIC_TEL",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "담당자명",
      field: "PIC_NAME",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "위도",
      field: "LATITUDE",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "경도",
      field: "LONGITUDE",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "반경",
      field: "RADIUS",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 80,
    },
    {
      headerName: "수정일시",
      field: "LAST_UPDATE_DATE",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 250,
    },
  ],
});

// 도착센터 그리드
const columnDefs2 = reactive({
  value: [
    {
      headerName: "NO",
      field: "RANK",
      pinned: "left",
      lockPinned: true,
      cellClass: "header-center",
      editable: false,
      valueGetter: "node.rowIndex + 1",
      cellStyle: { textAlign: "center" },
      width: 50,
    },
    // {
    //   headerName: "센터코드",
    //   field: "SPOT_CD",
    //   pinned: "left",
    //   lockPinned: true,
    //   cellClass: "lock-pinned",
    //   cellStyle: { textAlign: "left" },
    //   width: 150,
    // },
    {
      headerName: "도착센터",
      field: "TO_SPOT_NAME",
      pinned: "left",
      lockPinned: true,
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "거리",
      field: "DISTANCE_INFO",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "center" },
      width: 80,
    },
    {
      headerName: "시간",
      field: "DURATION_INFO",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "center" },
      width: 80,
    },
    {
      headerName: "수정일시",
      field: "LAST_UPDATE_DATE",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
    {
      headerName: "주소",
      field: "ADDRESS",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 300,
    },
    ],
});   

// 알림대상 그리드
const columnDefs3 = reactive({
  value: [
    {
      headerName: "NO",
      field: "RANK",
      pinned: "left",
      lockPinned: true,
      cellClass: "header-center",
      editable: false,
      valueGetter: "node.rowIndex + 1",
      cellStyle: { textAlign: "center" },
      width: 50,
    },
    // {
    //   headerName: "센터코드",
    //   field: "SPOT_CD",
    //   pinned: "left",
    //   lockPinned: true,
    //   cellClass: "lock-pinned",
    //   cellStyle: { textAlign: "left" },
    //   width: 150,
    // },
    {
      headerName: "이름",
      field: "NAME",
      pinned: "left",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 100,
      editable: true,
    },
    {
      headerName: "연락처",
      field: "PHONE_NO",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "center" },
      width: 150,
      editable: true,
    },
    {
      headerName: "수정일시",
      field: "UPDATE_DATE",
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
      width: 150,
    },
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

// 출발센터 영역 노출여부
let startCenterTab = ref(false);
// 도착센터 영역 노출여부
let endCenterTab = ref(false);

//메인 그리드에서 선택한 row 정보
let rowTargetObj = "";
let selectStartRowData = ref({});
let selectEndRowData = ref({});
let selectAlarmRowData = ref({});

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

    FROM_CENTER_OBJ.value = null;
    
    setTimeout(() => {
        
        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
        
        // 팝업영역 숨기기(지도핸들링을 막아서 숨겨준다.)
        var onmap_startCenterTab = document.getElementsByClassName("onmap_startCenterTab")[0]; // 접기 버튼
        onmap_startCenterTab.style.display = "none";
        var onmap_endCenterTab = document.getElementsByClassName("onmap_endCenterTab")[0]; // 접기 버튼
        onmap_endCenterTab.style.display = "none";
        
        // 지도초기화
        initMap();

        // 지점조회..
        requestCvoSpotSel();
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
        
    });


    naver.maps.Event.addListener(mapInstance.value, "click", function (e) {

        var point = e.coord;

        console.log("맵 click:" + point.lat());
        console.log("맵 click:" + point.lng());
        
        // 기준점 위치 변경
        CUR_POSITION_OBJ.value.latitude = point.lat();
        CUR_POSITION_OBJ.value.longitude = point.lng();
        
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

  // 기준위치 마커지우기
  if (rootPointerMarker != null) rootPointerMarker.setMap(null);

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
  
  // 기준위치 마커지우기
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
                        '<span class="rootPositionTitle">' + FROM_CENTER_OBJ.value.SPOT_NAME + '</span>',
                        '<span class="rootPositionSubTitle">- 잡고 이동가능 -</span>',
                      '</div>'

                  ].join(''),
          size: new window.naver.maps.Size(38, 58),
          anchor: new window.naver.maps.Point(55, 72),
      },
      animation: naver.maps.Animation.BOUNCE,
      draggable: true
  });
  setTimeout(() => {
    rootPointerMarker.setAnimation(null)
  }, 1400); 
  
  // 기준점 마커를 드래그했을경우..
  naver.maps.Event.addListener(rootPointerMarker, "dragend", function (e) {
    var point = e.coord;
    console.log("마커이동 :" + point.lat());
    console.log("마커이동 :" + point.lng());
    
    // 기준위치 변경
    CUR_POSITION_OBJ.value.latitude = point.lat();
    CUR_POSITION_OBJ.value.longitude = point.lng();


  });

};



/**
 * 마커추가 - 거래처
 * @param {배차일보 객체} targetObj
 */
 const addCustMarker = (targetObj) => {
  try {
    if (targetObj.LATITUDE > 0 && targetObj.LONGITUDE > 0) {
      let latLng = new window.naver.maps.LatLng(targetObj.LATITUDE, targetObj.LONGITUDE);
      arrCustBounds.push(latLng);

      // 1.도착시간 확인
      var on_off_string, badge_on_off_string, imageInfoString, clgubun_css;
      on_off_string = "_on";

    //   if (targetObj.IMAGE_CNT_TODAY > 0) {
        badge_on_off_string = "_on";
        targetObj.inboundClassString = "";
    //   } else {
    //     badge_on_off_string = "_off";
    //     targetObj.inboundClassString = "_NOTIN";
    //   }
      
      
    //   imageInfoString = "<small><small>누적 : </small></small>" + targetObj.IMAGE_CNT_TOT;
      clgubun_css = "marker_clgubun_1";

      // 2.거래처마커 추가
      var custerMarker = new window.naver.maps.Marker({
        position: latLng,
        map: mapInstance.value,
        // index: targetObj.ROUTE_SEQ - 1,
        targetObj: targetObj,
        title: targetObj.SPOT_NAME,
        zIndex: 100,
        icon: {
          content: [
            '<div class="marker_center' + on_off_string + '">',
            // '<span class="marker_image_badge' + badge_on_off_string + '">' + targetObj.IMAGE_CNT_TODAY + '</span>',
            '<span class="' +
              clgubun_css +
              '">' +
              '센터' +
              '</span><span style="font-size:17px;">' +
              targetObj.SPOT_NAME +
              "</span<BR>",
            '<hr style="margin:0.2rem 0rem 0.1rem 0rem; border:0px; border-top:1px solid #FFFFFF;"/>',
            "" +
              targetObj.ADDRESS +
            "</div>",
            '<div class="marker_center_bottom_point"></div>',
            '<div class="marker_center_bottom_arrow' + on_off_string + '"></div>',
          ].join(""),
          size: new window.naver.maps.Size(38, 58),
          anchor: new window.naver.maps.Point(100, 85),
        },
        animation: naver.maps.Animation.DROP,
      });
      // 3.거래처반경(200미터 설정:자동진입 체크반경)
      var custCircle = new naver.maps.Circle({
      	map: mapInstance.value,
      	center: latLng, // 중심점
      	radius: 200, // 200 미터
      	fillColor: '#4952ff',
      	fillOpacity: 0.1,
      	strokeWeight : 1,
      	strokeColor: '#4952ff9b',
      });
      arrCustCircle.push(custCircle);

      // 4.거래처 마커 클릭시..이벤트..
      naver.maps.Event.addListener(custerMarker, "click", function () {
        console.log(custerMarker.targetObj);
        // 거래처 찾기..
        var infos = rowData;
        var targetObj = null;
        infos.forEach(function (item) {
          if (custerMarker.targetObj.SPOT_CD == item.SPOT_CD) {
            targetObj = item;
          }
        });
        // 거래처를 찾았다면..
        if (targetObj != null) {
            // // 1.그리드 클릭이벤트 효과
            // var event = {};
            // event.data = targetObj;
            // cellWasClicked(event);
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



/**
 * 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
 */
const resizeScreenSet = () => {
  
  setTimeout(() => {
    var grid_clientmnglist = document.getElementsByClassName("grid_clientmnglist")[0];
    var grid_searcharea = document.getElementsByClassName("grid_searcharea")[0];
    var s1 = grid_clientmnglist.getBoundingClientRect().top;
    var s2 = grid_searcharea.getBoundingClientRect().bottom;
    var innerHeight = window.innerHeight;
    var clientHeight = document.body.clientHeight;

    console.log("innerHeight=" + window.innerHeight);
    console.log("clientHeight=" + document.body.clientHeight);
    // console.log("s1=" + s1);
    // console.log("s2=" + s2);

    // 최종높이 지정
    var calcHeight = 0;
    if (clientHeight == 0) {
        calcHeight = innerHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    } else {
        calcHeight = clientHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    }

    calcHeight = calcHeight/3;
    // 출발센터 영역 높이지정
    var grid_clientmnglist1 = document.getElementsByClassName("ag-theme-alpine")[0];
    grid_clientmnglist1.style.height = calcHeight + "px";

    // 도착센터 영역 높이지정
    var to_center_list_title_div = document.getElementsByClassName("to_center_list_title_div")[0];
    var grid_clientmnglist2 = document.getElementsByClassName("ag-theme-alpine")[1];
    var s3 = to_center_list_title_div.getBoundingClientRect().top;
    var s4 = grid_clientmnglist2.getBoundingClientRect().top;
    console.log("s3=" + s3);
    console.log("s4=" + s4);
    console.log("calcHeight=" + calcHeight);
    calcHeight = calcHeight - (s4 - s3);
    grid_clientmnglist2.style.height = calcHeight + "px";

    // 알림대상 영역 높이지정
    var alarm_list_title_div = document.getElementsByClassName("alarm_list_title_div")[0];
    var grid_clientmnglist3 = document.getElementsByClassName("ag-theme-alpine")[2];
    // var s5 = alarm_list_title_div.getBoundingClientRect().top;
    // var s6 = grid_clientmnglist3.getBoundingClientRect().top;
    // console.log("s5=" + s5);
    // console.log("s6=" + s6);
    // console.log("calcHeight=" + calcHeight);
    // calcHeight = calcHeight - (s6 - s5);
    grid_clientmnglist3.style.height = calcHeight + "px";
  }, 500);
};

/******************************************************************************* api 호출 start */



/**
 * 센터위치로 지도이동/줌 - 그리드 클릭이벤트에서 호출
 * @param {*} targetObj 
 */
const moveClientPosition = async (targetObj) => {
  //console.log(targetObj);
  setTimeout(() => {
    // 지도이동 - 거래처위치
    var latLng = new window.naver.maps.LatLng(
      targetObj.LATITUDE,
      targetObj.LONGITUDE
    );
    mapInstance.value.setCenter(latLng);
    if (mapInstance.value.getZoom()<16) mapInstance.value.setZoom(16);
  }, 0);
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
  onRemoveRoute();
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

    }, 100);
  }
};

/**
 * CVO - 센터 조회..
 */
var curCircle;
const clientList = ref([]); // 거래처 목록
const requestCvoSpotSel = async () => {
  //조회

  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD': "00002",
      '@I_BOUND_DIST': "",
      '@I_KEYWORD': searchKeyword.value ?? "",
      '@I_CENTER_LAT': "",
      '@I_CENTER_LON': "",
  }
  
  // 거래처 목록 지우기
  clientList.value = [];
  // 그리드 데이타 지우기
  rowData.splice(0);
  rowDataRoute.splice(0);
  rowDataAlarm.splice(0);
  // 지도, 거래처마커 지우기
  onRemoveCust();
  // 주소객체(마커,인포윈도우) 지우기
  onRemoveAddress();
  // 기존경로 지우기
  onRemoveRoute();
  // 상세팝업(페이지)닫기
  startCenterTab.value = false;
  endCenterTab.value = false;
  
  // 7.API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      // 그리드용 데이타 푸쉬
      rowData.push(...data.RecordSet);
      // 센터 그리드에 세팅..
      gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
        rowData
      );
      // 거래처 마커추가작업
      var infos = data.RecordSet;
      // 검색기준위치
      var searchPosition;
      if (infos.length > 0) {
        
        // 거래처 목록 지우기
        clientList.value = [];
        
        var sHtmlString = "출발센터 - <small><small>" + infos.length + "건</small></small>";
        document.getElementById("mng_title").innerHTML = sHtmlString;
        
        infos.forEach(function (item) {
          // 거래처,마커추가
          addCustMarker(item);
        });
        
        // 지도영역 조정(마커를 다보이게..)
        mapInstance.value.fitBounds(arrCustBounds);

      } else {
        console.log("검색기준위치로이동...");
      }
      
    } else {
      console.log("데이타없으니..지움..");
      // 거래처 목록 지우기
      clientList.value = [];
      // 그리드에 세팅..
      gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
        rowData
      );
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
 * CVO - 센터도착 조회..
 */
const requestCvoSpotRouteSel = async (fromSpotCd, toSpotCd) => {
  //조회

  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD': "00002",
      '@I_FROM_SPOT_CD': fromSpotCd ?? "",
      '@I_TO_SPOT_CD': toSpotCd ?? "",
  }
  
  // 그리드 데이타 지우기
  rowDataRoute.splice(0);
  rowDataAlarm.splice(0);
  
  // API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_ROUTE_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      // 그리드용 데이타 푸쉬
      rowDataRoute.push(...data.RecordSet);
      // 새로고침 요청이었을경우..
      if (refreshRequestYn) {
        if (TO_CENTER_OBJ != null) {
          rowDataRoute.forEach(function (item) {
            if (item.TO_SPOT_CD == TO_CENTER_OBJ.value.TO_SPOT_CD) {
              // 상세데이타 세팅
              selectEndRowData.value = item;
            }
          });
        }
        refreshRequestYn = false;
      }
    } else {
      console.log("데이타없음..");
    }
    // 그리드에 세팅..
    gridApi2.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
      rowDataRoute
    );

    var infos = rowDataRoute;
    var sHtmlString = "도착센터 - <small><small>" + infos.length + "건</small></small>";
    document.getElementById("to_center_title").innerHTML = sHtmlString;

  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
  }
};

/**
 * 경로API 조회..
 */
const requestCvoSpotRouteApiSel = async (fromLat, fromLon, toLat, toLon) => {
  
  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅 - { "startPOS":"127.11015314141542,37.39472714688412", "endPOS":"127.10824367964793,37.401937080111644", "showRouteYn":"Y" }
  let reqData = {
      'startPOS': fromLon + ',' + fromLat,
      'endPOS': toLon + ',' + toLat,
      'showRouteYn': 'Y',
  }
  
  // 그리드 데이타 지우기
  rowDataRouteApi.splice(0);
  // 기존경로 지우기
  onRemoveRoute();
  
  // API요청..
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

      // 결과정보 저장용...
      selectEndRowData.value.DISTANCE = rowDataRouteApi[0].summary.distance;
      selectEndRowData.value.DURATION = rowDataRouteApi[0].summary.duration;

      // 화면 하단, 경로정보 표시용...
      var distance = rowDataRouteApi[0].summary.distance;
      distance = Math.round(distance/1000);
      var duration = rowDataRouteApi[0].summary.duration;
      duration = Math.round(duration/60);
      selectEndRowData.value.DISTANCE_INFO = distance + "km";
      selectEndRowData.value.DURATION_INFO = duration + "분";

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
      // 경로그리기
      drawRouteLine(arrPoints);
      // ------------------------------------------------------------
      // 영역설정
      // ------------------------------------------------------------
      var fitOption = { top: 10, right: 10, bottom: 10, left: 10, maxZoom: 50 };
      mapInstance.value.fitBounds(arrPoints, fitOption);

      // ------------------------------------------------------------
      // 경로정보 저장하기
      // ------------------------------------------------------------
      // 경로포인트 정리(x,y 좌표와 lng,lat가 함께있어서 정리..)
      var savePoints = [];
      arrPoints.forEach(function (item) {
        var saveItem = {};
        saveItem.x = item.x;
        saveItem.y = item.y;
        savePoints.push(saveItem);
      });
      // JSON 데이타 변환
      var json = { ...savePoints };
      var jsonString = JSON.stringify(json);
      // 서버요청하기..
      //console.log(jsonString);
      routeSave(jsonString, selectEndRowData.value.JSON_ALARM_INFO);

    } else {
      console.log("데이타없음..");
    }
    
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
  }
};

/**
 * 경로정보 저장하기
 */
const routeSave = async (routeString, alarmString) => {
  
  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD' : selectEndRowData.value.COMPANYCD,
      '@I_COMPANY_NAME' : selectEndRowData.value.COMPANY_NAME,
      '@I_FROM_SPOT_CD' : selectEndRowData.value.FROM_SPOT_CD,
      '@I_TO_SPOT_CD' : selectEndRowData.value.TO_SPOT_CD,
      '@I_DISTANCE' : selectEndRowData.value.DISTANCE,
      '@I_DURATION' : selectEndRowData.value.DURATION,
      '@I_ROUTE_INFO' : routeString ?? "",
      '@I_ALARM_INFO' : alarmString ?? "",
  }

  // API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_ROUTE_SAV `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      console.log(data);
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();

    // 도착센터 재조회하기..
    requestCvoSpotRouteSel(selectEndRowData.value.FROM_SPOT_CD, '');
  }

};

/**
 * 센터출발 저장하기
 */
const routeCenterStart = async () => {
  
  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD' : selectEndRowData.value.COMPANYCD,
      '@I_COMPANY_NAME' : selectEndRowData.value.COMPANY_NAME,
      '@I_DEVICE_NO' : '01094065736',
      '@I_FROM_SPOT_CD' : selectEndRowData.value.FROM_SPOT_CD,
      '@I_TO_SPOT_CD' : selectEndRowData.value.TO_SPOT_CD,
      '@I_LAT' : "",
      '@I_LON' : "",
  }

  // API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_START `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      console.log(data);
    }
    showAlertSuccess("정상처리 되었습니다.");
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
  }

};



/**
 * 경로 그리기..(실선-일ㄴ)
 */
 const drawRouteLine = async (arrPoints) => {
  try {
    //console.log(arrPoints);

    var prevPoint = null,
      nIndex = 0;
    var upperP1, upperP2, lowerP1, lowerP2;

    arrPoints.forEach(function (item) {
      //console.log(item);
      // 1.시작지점 세팅
      if (nIndex > 0) {
        upperP1 = new naver.maps.LatLng([prevPoint.x, prevPoint.y]);

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
          strokeColor: "#E51D1A",
          strokeWeight: 5,
          strokeStyle: "solid", // solid, shortdash, shortdot, shortdashdot, shortdashdotdot, dot, dash, longdash, dashdot, longdashdot, longdashdotdot
          strokeOpacity: 0.5,
        });
        // 3.1.마자막위치에 화살표 표기추가
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
 * 경로 그리기..(점선-굵게)
 */
 const drawRouteDotLine = async (arrPoints) => {
  try {
    //console.log(arrPoints);

    var prevPoint = null,
      nIndex = 0;
    var upperP1, upperP2, lowerP1, lowerP2;

    arrPoints.forEach(function (item) {
      //console.log(item);
      // 1.시작지점 세팅
      if (nIndex > 0) {
        upperP1 = new naver.maps.LatLng([prevPoint.x, prevPoint.y]);

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
          strokeColor: "#E51D1A",
          strokeWeight: 5,
          strokeStyle: "shortdash", // solid, shortdash, shortdot, shortdashdot, shortdashdotdot, dot, dash, longdash, dashdot, longdashdot, longdashdotdot
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
 * CVO 지오코드 요청(주소->좌표 변환)
 */
 const cvoGeocodeSel = async () => {
  var address = document.getElementById('inputAddress').value;
  startLoadingBar(); // 대기창 표시
  const param = {
    address: address,
  };

  let resultData;
  try {
    // 서비스 요청
    resultData = await operateApi.cvoGeocodeSel(param);
    // console.log(resultData.data);
    let data = resultData.data;

    // 데이타확인
    if (data.RecordCount > 0) {
      // 데이타 있음
      let LOCATION_INFO = data.RecordSet[0].LOCATION_INFO;
      let latitude = data.RecordSet[0].LAT;
      let longitude = data.RecordSet[0].LON;
      // console.log("가져온 좌표 :" + LOCATION_INFO);

      // 주소마커 추가
      const title = address;
      addAddressMarker(latitude, longitude, title);

      // 위치로 이동
      var latLng = new window.naver.maps.LatLng(latitude, longitude);
      mapInstance.value.setCenter(latLng);
    } else {
      // 데이타 없음
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar(); // 대기창 숨기기
  }
};

// 주소마커 추가하기
let cur_addressMarker = {};
let cur_addressMarkerInfo = {};
const onRemoveAddress = () => {
    if (cur_addressMarker.value != null) cur_addressMarker.value.setMap(null);
    if (cur_addressMarkerInfo.value != null) cur_addressMarkerInfo.value.setMap(null);
};

const addAddressMarker = (lat, lng, name) => {
  // mapInstance.value.setZoom(17);
  
  // 주소객체(마커,인포윈도우) 지우기
  onRemoveAddress();

  let latLng = new window.naver.maps.LatLng(lat, lng);
  // 지도이동 차단, 지도를 이동해버리면 클릭위치를 알수없다.
  // mapInstance.value.setCenter(latLng);
  cur_addressMarker.value = 
    new window.naver.maps.Marker({
      position: latLng,
      map: mapInstance.value,
      zIndex: 100,
      // icon: {
      //     content: [
      //                 // '<div class="cs_mapbridge">',
      //                 //     '<div class="map_group _map_group crs">',
      //                 //         '<div class="map_marker _marker num1 num1_big"> ',
      //                 //             '<span class="ico _icon"></span>',
      //                 //             '<span class="shd"></span>',
      //                 //         '</div>',
      //                 //     '</div>',
      //                 // '</div>'
      //                 '<div class="rootPosition">',
      //                   '<span class="rootPositionSubTitle">' + name + '</span>',
      //                 '</div>'

      //             ].join(''),
      //     size: new window.naver.maps.Size(38, 58),
      //     anchor: new window.naver.maps.Point(55, 72),
      // },
      animation: naver.maps.Animation.DROP,
    });

  cur_addressMarkerInfo.value =
    new window.naver.maps.InfoWindow({
      content:
        '<div style="padding:0px 10px; border-radius:50px;">' + name + "</div>",
    });
    cur_addressMarkerInfo.value.open(mapInstance.value, cur_addressMarker.value);
};



/**
 * 원래위치이동
 */
const rollbackPosition = async () => {
    // 원래위치로 복원
    FROM_CENTER_OBJ.value.LAT = ORG_POSITION_OBJ.value.LAT;
    FROM_CENTER_OBJ.value.LON = ORG_POSITION_OBJ.value.LON;
    // 센터위치로 지도이동/줌 
    moveClientPosition(FROM_CENTER_OBJ.value);  
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
        console.log('--- onGridReady');
		event.api.sizeColumnsToFit();
	},
	// 창 크기 변경 되었을 때 이벤트
	onGridSizeChanged: function(event) {
		console.log('--- onGridSizeChanged');
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

// 출발센터 선택 데이타 지정..
const setSelectStartRowData = (data) => {
  selectStartRowData.value = {};
  selectStartRowData.value.GEONUM = data.GEONUM;
  selectStartRowData.value.COMPANYCD = data.COMPANYCD;
  selectStartRowData.value.COMPANY_NAME = data.COMPANY_NAME;
  selectStartRowData.value.SPOT_CD = data.SPOT_CD;
  selectStartRowData.value.SPOT_NAME = data.SPOT_NAME;
  selectStartRowData.value.ADDRESS = data.ADDRESS;
  selectStartRowData.value.PIC_NAME = data.PIC_NAME;
  selectStartRowData.value.PIC_TEL = data.PIC_TEL;
  selectStartRowData.value.LATITUDE = data.LATITUDE;
  selectStartRowData.value.LONGITUDE = data.LONGITUDE;
  selectStartRowData.value.RADIUS = data.RADIUS;
  selectStartRowData.value.WS_NEWDATE = data.WS_NEWDATE;
  selectStartRowData.value.WS_NEWUSER = data.WS_NEWUSER;
  selectStartRowData.value.WS_EDTDATE = data.WS_EDTDATE;
  selectStartRowData.value.WS_EDTUSER = data.WS_EDTUSER;
  selectStartRowData.value.LAST_DATE = data.LAST_DATE;
  selectStartRowData.value.LAST_UPDATE_DATE = data.LAST_UPDATE_DATE;
};
// 도착센터 선택 데이타 지정..
const setSelectEndRowData = (data) => {
  selectEndRowData.value = {};
  selectEndRowData.value.COMPANYCD = data.COMPANYCD;
  selectEndRowData.value.COMPANY_NAME = data.COMPANY_NAME;
  selectEndRowData.value.FROM_SPOT_CD = data.FROM_SPOT_CD;
  selectEndRowData.value.FROM_SPOT_NAME = data.FROM_SPOT_NAME;
  selectEndRowData.value.FROM_SPOT_LAT = data.FROM_SPOT_LAT;
  selectEndRowData.value.FROM_SPOT_LON = data.FROM_SPOT_LON;
  selectEndRowData.value.TO_SPOT_CD = data.TO_SPOT_CD;
  selectEndRowData.value.TO_SPOT_NAME = data.TO_SPOT_NAME;
  selectEndRowData.value.TO_SPOT_LAT = data.TO_SPOT_LAT;
  selectEndRowData.value.TO_SPOT_LON = data.TO_SPOT_LON;
  selectEndRowData.value.PIC_NAME = data.PIC_NAME;
  selectEndRowData.value.PIC_TEL = data.PIC_TEL;
  selectEndRowData.value.ADDRESS = data.ADDRESS;
  selectEndRowData.value.DISTANCE = data.DISTANCE;
  selectEndRowData.value.DURATION = data.DURATION;
  selectEndRowData.value.LAST_DATE = data.LAST_DATE;
  selectEndRowData.value.LAST_UPDATE_DATE = data.LAST_UPDATE_DATE;
  selectEndRowData.value.DISTANCE_INFO = data.DISTANCE_INFO;
  selectEndRowData.value.DURATION_INFO = data.DURATION_INFO;
  selectEndRowData.value.DISTANCE_ROUTE_INFO = data.DISTANCE_INFO;
  selectEndRowData.value.DURATION_ROUTE_INFO = data.DURATION_INFO;
  selectEndRowData.value.JSON_ROUTE_INFO = data.JSON_ROUTE_INFO;
  selectEndRowData.value.JSON_ALARM_INFO = data.JSON_ALARM_INFO;
};
// 알림리스트 선택 데이타 지정..
const setSelectAlarmRowData = (data) => {
  selectAlarmRowData.value = {};
  selectAlarmRowData.value.NAME = data.NAME;
  selectAlarmRowData.value.PHONE_NO = data.PHONE_NO;
};

const cellValueChanged = (event) => {
  setSelectStartRowData(event.data);
};

/**
 * 그리드셀-더블 클릭시..
 * @param {*} event 
 */
const cellWasDblClicked = (event) => {

};
/**
 * 센터 그리드 클릭시..
 * @param {} event 
 */
const cellWasClicked = (event) => {
    // 팝업영역 보이기
    var onmap_startCenterTab = document.getElementsByClassName("onmap_startCenterTab")[0];
    onmap_startCenterTab.style.display = "flex";
    
    // 기존상세창을 닫아준다.
    startCenterTab.value = false;
    endCenterTab.value = false;
    // 주소객체(마커,인포윈도우) 지우기
    onRemoveAddress();
    // 전역변수저장
    FROM_CENTER_OBJ.value = event.data;
    ORG_POSITION_OBJ.value.LAT = FROM_CENTER_OBJ.value.LATITUDE;
    ORG_POSITION_OBJ.value.LON = FROM_CENTER_OBJ.value.LONGITUDE;

    setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
        // 상세데이타 세팅
        selectStartRowData.value = FROM_CENTER_OBJ.value;
        // 상세영역 열기
        startCenterTab.value = true;
        
    }, 100);  
    
    // 기존경로 지우기
    onRemoveRoute();
    // 도착지센터 팝업닫기
    endCenterTab.value = false;
    
    // 센터도착정보 조회..
    requestCvoSpotRouteSel(FROM_CENTER_OBJ.value.SPOT_CD, '');

    // 센터위치로 지도이동/줌 
    moveClientPosition(FROM_CENTER_OBJ.value);

};

/**
 * 경로API 새로고침
 */
var refreshRequestYn = false;
const refreshRoute = () => {

  refreshRequestYn = true;

  // 경로API 조회..저장까지...
  requestCvoSpotRouteApiSel(
    FROM_CENTER_OBJ.value.LATITUDE, FROM_CENTER_OBJ.value.LONGITUDE,
    TO_CENTER_OBJ.value.LATITUDE, TO_CENTER_OBJ.value.LONGITUDE
  );
};

/**
 * 도착 그리드 클릭시..
 * @param {} event 
 */
 const cellWasClicked2 = (event) => {
    
    var onmap_endCenterTab = document.getElementsByClassName("onmap_endCenterTab")[0];
    onmap_endCenterTab.style.display = "flex";
    // 기존상세창을 닫아준다.
    endCenterTab.value = false;
    // 주소객체(마커,인포윈도우) 지우기
    onRemoveAddress();
    // 기존경로 지우기
    onRemoveRoute();

    console.log('도착 그리드 클릭시..');
    // 전역변수저장
    TO_CENTER_OBJ.value = event.data;
    TO_CENTER_OBJ.value.LATITUDE = TO_CENTER_OBJ.value.TO_SPOT_LAT;
    TO_CENTER_OBJ.value.LONGITUDE = TO_CENTER_OBJ.value.TO_SPOT_LON;
    // ORG_POSITION_OBJ.value.LAT = FROM_CENTER_OBJ.value.LATITUDE;
    // ORG_POSITION_OBJ.value.LON = FROM_CENTER_OBJ.value.LONGITUDE;
    
    
    setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
        // 상세데이타 세팅
        selectEndRowData.value = TO_CENTER_OBJ.value;
        // 상세영역 열기
        endCenterTab.value = true;
        
        if (selectEndRowData.value.JSON_ROUTE_INFO == "") {
          //console.log('경로 요청하기...');
          if (!refreshRequestYn) {
            // 경로API 조회..저장까지...
            requestCvoSpotRouteApiSel(
              FROM_CENTER_OBJ.value.LATITUDE, FROM_CENTER_OBJ.value.LONGITUDE,
              TO_CENTER_OBJ.value.LATITUDE, TO_CENTER_OBJ.value.LONGITUDE
            );
          }
        } else {
          console.log('저장된 경로그리기...')          
          // 좌표데이타 정제하기..
          var jsonRoute = JSON.parse(selectEndRowData.value.JSON_ROUTE_INFO);
          var arrPoints = [];
          for(const key in jsonRoute) {
            var point = {};
            point.x = `${jsonRoute[key].x}`;
            point.y = `${jsonRoute[key].y}`;
            arrPoints.push(point);
          }
          // 경로그리기
          drawRouteLine(arrPoints);
          
          // 영역설정
          var fitOption = { top: 10, right: 10, bottom: 10, left: 10, maxZoom: 50 };
          mapInstance.value.fitBounds(arrPoints, fitOption);
        }
        
        // 알람리스트 세팅
        if (selectEndRowData.value.JSON_ALARM_INFO == "") {
          // 알림리스트 초기화
          rowDataAlarm.splice(0);
        } else {
          var arrData1 = selectEndRowData.value.JSON_ALARM_INFO.split('|');
          if (arrData1.length>0) {
            // 알림리스트 초기화
            rowDataAlarm.splice(0);
            // Loop...
            const newData = reactive([]);
            arrData1.forEach(element => {
                var alarm = {};
                alarm.NAME = element.split('_')[0];
                alarm.PHONE_NO = element.split('_')[1];
                // 데이타가 둘다 있는경우만 추가..
                if ((alarm.NAME != '') && (alarm.NAME != '')) {
                  newData.push(alarm);
                }
            });
            // 알람리스트 추가
            rowDataAlarm.push(...newData);
          }
        }
        // 그리드에 세팅..
        gridApi3.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
          rowDataAlarm
        );

    }, 100);  
    
    // 알람리스트 세팅하기..
};

/**
 * 알람리스트 행추가
 * @param {} event 
 */
const addAlarmList = (event) => {
  
  rowDataAlarm.push({});
  // 그리드에 세팅..
  gridApi3.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
    rowDataAlarm
  );
};

/**
 * 알람리스트 삭제하기 - 메소드 미확인으로 수동으로 데이타를 재구성해준다.(2023.10.30)
 * @param {} event 
 */
const deleteAlarmList = (event) => {
  // 삭제대상 객체
  var targetObj = selectAlarmRowData.value;
  // 원본데이타
  const orgData = rowDataAlarm;
  const newData = reactive([]);
  // 삭제데이타를 제외한 데이타셋을 구성... 
  orgData.forEach(element => {
    console.log(element.NAME + '/' + targetObj.NAME)
    if (
      (targetObj.NAME != element.NAME) && (targetObj.PHONE_NO != element.PHONE_NO)
    ) newData.push(element);
  });
  // 알림리스트 초기화
  rowDataAlarm.splice(0);
  rowDataAlarm.push(...newData);
  // 그리드에 세팅..
  gridApi3.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
    rowDataAlarm
  );
};

/**
 * 알람리스트 저장하기
 * @param {} event 
 */
const saveAlarmList = (event) => {

  var alarmListString = "";
  rowDataAlarm.forEach(element => {
    alarmListString += element.NAME + '_' + element.PHONE_NO + '|';
  });
  // 서버요청하기..
  routeSave(selectEndRowData.value.JSON_ROUTE_INFO, alarmListString);
};

/**
 * 알람리스트 테스트하기
 * @param {} event 
 */
const smsTestAlarmList = (event) => {

  var alarmListString = "";
  rowDataAlarm.forEach(element => {
    alarmListString += element.NAME + '_' + element.PHONE_NO + '|';
  });
  // 센터출발
  routeCenterStart();
};


/**
 * 알림리스트 그리드 클릭시..
 * @param {} event 
 */
 const cellWasClicked3 = (event) => {
    
  console.log('알림리스트 그리드 클릭시..');
    // 전역변수저장
    selectAlarmRowData.value = event.data;
    
};

const cellFocused = (event) => {
  console.log('--- cellFocused');
  setSelectStartRowData(rowData[event.rowIndex]);
};
const cellFocused2 = (event) => {
  console.log('--- cellFocused2');
  setSelectEndRowData(rowDataRoute[event.rowIndex]);
};
const cellFocused3 = (event) => {
  console.log('--- cellFocused3');
  setSelectAlarmRowData(rowDataAlarm[event.rowIndex]);
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

// 검색창에서 키보드 입력시..
const searchBtnEnter = (event) => {
  //검색 Btn
  if (event.keyCode != 13) return;
  // 1.거래처 목록 지우기
  clientList.value = [];
  // 2.그리드 데이타 지우기
  rowData.splice(0);
  // 3.지도, 거래처마커 지우기
  onRemoveCust();
  // 4.상세팝업(페이지)닫기
  startCenterTab.value = false;
  
  requestCvoSpotSel();
};
const searchBtn = (keyword) => {
  //검색 Btn
  rowData.splice(0);
  startCenterTab.value = false;
  pageNumber.value = 1;
  requestCvoSpotSel();
};
const cancleBtn = (rowData) => {
  //닫기 Btn
  startCenterTab.value = false;
  // 팝업영역 숨기기
  var onmap_startCenterTab = document.getElementsByClassName("onmap_startCenterTab")[0]; // 접기 버튼
  onmap_startCenterTab.style.display = "none";
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
      <div class="grid_clientmnglist">
        <!-- 타이틀 -->
        <div class="from_center_list_title_div" style="text-align: center;"><span id="mng_title">출발센터</span></div>
        <!-- 검색어 -->
        <div class="grid_searcharea" style="border-bottom: 1px solid #0b048678;">
          <input type="text" v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="거래처명,주소,전화번호,코드 조회가능.."/>
          <div class="item__buttons" style="width: 5rem;">
            <button class="btn_search" @click="requestCvoSpotSel">
              <i class="fa-regular fa-circle-check"></i><span>검 색</span>
            </button>
          </div>
        </div>
        <!-- 출발센터 -->
        <AgGridVue
          class="ag-theme-alpine"
          style="height:auto; width:auto; margin-top:0.1rem; background-color: #0b048653; padding:0.05rem;"
          :columnDefs="columnDefs.value"
          :rowData="rowData"
          :defaultColDef="defaultColDef"
          suppressColumnVirtualisation="true"
          animateRows="true"
          copyHeadersToClipboard="true"
          @cell-clicked="cellWasClicked"
          @cell-focused="cellFocused"
          @grid-ready="onGridReady"
          >
        </AgGridVue>
        <!-- 도착센터 -->
        <div class="to_center_list_title_div" style="text-align: center;"><span id="to_center_title">도착센터</span></div>
        <AgGridVue
          class="ag-theme-alpine"
          style="height:auto; width:auto; margin-top:0.1rem; background-color: #0b048653; padding:0.05rem;"
          :columnDefs="columnDefs2.value"
          :rowData="rowDataRoute"
          :defaultColDef="defaultColDef"
          suppressColumnVirtualisation="true"
          animateRows="true"
          copyHeadersToClipboard="true"
          @cell-clicked="cellWasClicked2"
          @cell-focused="cellFocused2"
          @grid-ready="onGridReady2"
          >
        </AgGridVue>
        <!-- 알림대상 -->
        <div class="alarm_list_title_div" style="text-align: center;">
          <span id="to_center_title">알림대상</span>
          <span class="alarm_list_add" @click="addAlarmList">추가</span>
          <span class="alarm_list_delete" @click="deleteAlarmList">삭제</span>
          <span class="alarm_list_save" style="right:3rem; width:3.8rem; font-size: 0.6rem;" @click="smsTestAlarmList">알림테스트</span>
          <span class="alarm_list_save" @click="saveAlarmList">저장</span>
        </div>
        <AgGridVue
          class="ag-theme-alpine"
          style="height:auto; width:auto; margin-top:0.1rem; background-color: #0b048653; padding:0.05rem;"
          :columnDefs="columnDefs3.value"
          :rowData="rowDataAlarm"
          :defaultColDef="defaultColDef"
          suppressColumnVirtualisation="true"
          animateRows="true"
          copyHeadersToClipboard="true"
          @cell-clicked="cellWasClicked3"
          @cell-focused="cellFocused3"
          @grid-ready="onGridReady3"
          >
        </AgGridVue>
      </div>

      <!-- 접기버튼 -->
      <button class="btn_fold_ilove" @click="onClickFoldButton()"><i class="fa-solid fa-arrow-left"></i></button>

      <!-- 지도영역 -->
      <div class="area_map" style="position: relative;">
            <NaverMap style="height: 100%; width: 100%;">
            </NaverMap>
            <div class="onmap_inputAddress_area">
                <div><input id="inputAddress" type="text" v-on:keyup.prevent="searchAddressEnter" placeholder="주소 전체 또는 일부 입력후 엔터.."/></div>
                <div class="item__buttons" style="width:5rem;">
                    <button class="btn_search" @click="checkAddress">
                        <i class="icon-rocket"></i><span>지도이동</span>
                    </button>
                </div>
            </div>
            <!-- 하단,출발센터 -->
            <div class="onmap_startCenterTab">
                <!--거래처상세영역 -->
                <div class="grid_data_detail" v-if="startCenterTab"
                    style="height:100%; width:10rem; overflow-x:hidden; padding-bottom:0rem; background-color:#ffffffac;">
                    <!-- 센터정보영역 -->
                    <div class="client_detail_area" data-aos="flip-left" data-aos-delay="0" 
                         style="margin-left:-1.0rem; margin-top:-0.4rem; margin-right:-1.0rem; box-shadow:"
                        >
                        <div class="start_center_top_title">
                          <span>'{{ selectStartRowData.SPOT_NAME }}'</span>
                          <span class="client_detail_tag_01"> 센터</span>
                        </div>
                        <!-- 거래처 상세정보 -->
                        <div class="item__contents" style="display:flex; flex-direction:column; flex-wrap: wrap; padding:0.5rem 0.0rem 0.5rem 0.5rem;">
                            <div data-aos="fade-up" data-aos-delay="150" style="display:flex; flex-direction: column;">
                                <label class="form-label">담당자명</label>
                                <span>{{ selectStartRowData.PIC_NAME }}&nbsp;</span>
                            </div>
                            <div data-aos="fade-up" data-aos-delay="200" style="display:flex; flex-direction: column;">
                                <label class="form-label">전화번호</label>
                                <span>{{ selectStartRowData.PIC_TEL }}&nbsp;</span>
                            </div>
                            <div data-aos="fade-up" data-aos-delay="250" style="display:flex; flex-direction: column;">
                                <label class="form-label">주소</label>
                                <span>{{ selectStartRowData.ADDRESS }}&nbsp;</span>
                            </div>
                        </div>
                        <div class="start_center_bottom_title">
                          <span>출발</span>
                          <span>- Start</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 새로고침 버튼 추가 -->
            <div v-if="endCenterTab" data-aos="fade-up" data-aos-delay="400"
                 style="position: absolute; left: auto; bottom: 9rem; border:0px solid red; cursor: pointer;">
              <img
                class="cvo_map_refresh"
                @click.prevent="refreshRoute"
                src="/assets/img/map/refresh_02.png"
                title="현재 시간으로 경로정보를 새로요청합니다."
              />
            </div>
            <div class="onmap_middle_area" v-if="endCenterTab" data-aos="fade-up" data-aos-delay="500">
              <small><small><small>▶︎</small></small></small>▶︎<small><small><small>▶︎</small></small></small>
              <BR/>
              <span>
                {{ selectEndRowData.DISTANCE_INFO }}
                &nbsp;&nbsp;&nbsp;
                {{ selectEndRowData.DURATION_INFO }}
              </span>
              <span style="font-size:0.8rem; color:#6c64cf;">
                &nbsp;- {{ selectEndRowData.LAST_UPDATE_DATE.substring(3) }} 기준
              </span>
            </div>
            <!-- 하단,도착센터 -->
            <div class="onmap_endCenterTab">
                <!--거래처상세영역 -->
                <div class="grid_data_detail" v-if="endCenterTab"
                    style="height:100%; width:10rem; overflow-x:hidden; padding-bottom:0rem; background-color:#ffffffac;">
                    <!-- 센터정보영역 -->
                    <div class="client_detail_area" data-aos="flip-left" data-aos-delay="0" 
                         style="margin-left:-1.0rem; margin-top:-0.4rem; margin-right:-1.0rem; box-shadow:"
                        >
                        <div class="end_center_top_title">
                          <span>'{{ selectEndRowData.TO_SPOT_NAME }}'</span>
                          <span class="client_detail_tag_01"> 센터</span>
                        </div>
                        <!-- 거래처 상세정보 -->
                        <div class="item__contents" style="display:flex; flex-direction:column; flex-wrap: wrap; padding:0.5rem 0.0rem 0.5rem 0.5rem;">
                            <div data-aos="fade-up" data-aos-delay="150" style="display:flex; flex-direction: column;">
                                <label class="form-label">담당자명</label>
                                <span>{{ selectEndRowData.TO_PIC_NAME }}&nbsp;</span>
                            </div>
                            <div data-aos="fade-up" data-aos-delay="200" style="display:flex; flex-direction: column;">
                                <label class="form-label">전화번호</label>
                                <span>{{ selectEndRowData.TO_PIC_TEL }}&nbsp;</span>
                            </div>
                            <div data-aos="fade-up" data-aos-delay="250" style="display:flex; flex-direction: column;">
                                <label class="form-label">주소</label>
                                <span>{{ selectEndRowData.TO_ADDRESS }}&nbsp;</span>
                            </div>
                        </div>
                        <div class="end_center_bottom_title">
                          <span>도착</span>
                          <span>- Start</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
  </div>
    <!-- 이미지 보기(모달창 띄우기) -->
    <div id="modalClientImagePopDiv" data-bs-toggle="modal" data-bs-target="#pdfPrintModal"  style="display: none"  data-backdrop="static"  data-keyboard="false">
    </div>
    <div class="modal fade"   data-bs-target="#pdfPrintModal"   id="pdfPrintModal"   style="z-index: 1051 !important"   data-backdrop="static"   data-keyboard="false"  >
      <div class="modal-dialog modal-xl setModalCenter" style="margin:0px auto"  >
        <ClientImageView :selectImageObj="selectImageObj"></ClientImageView>
      </div>
    </div>
  
</template>


<style lang="scss">

/* 알림리스트-추가,버튼 */
.alarm_list_add {
  position: absolute;
  width: 2.5rem;
  top: 0.4rem;
  left: 0.2rem;
  font-size: 0.65rem;
  background-color: #4b4bff;
  color: #ffffff;
  padding: 0.1rem 0.3rem;
  border-radius: 2rem;
  border: 0.05rem solid #ffffffba;
  cursor: pointer;
  transition: all 0.2s linear;
}
.alarm_list_add:hover {
  transform: scale(1.1);
}
/* 알림리스트-삭제,버튼 */
.alarm_list_delete {
  position: absolute;
  width: 2.5rem;
  top: 0.4rem;
  left: 3.2rem;
  font-size: 0.65rem;
  background-color: #ff4076;
  color: #ffffff;
  padding: 0.05rem 0.3rem;
  border-radius: 2rem;
  border: 0.05rem solid #ffffffba;
  cursor: pointer;
  transition: all 0.2s linear;
}
.alarm_list_delete:hover {
  transform: scale(1.1);
}
/* 알림리스트-저장,버튼 */
.alarm_list_save {
  position: absolute;
  width: 2.5rem;
  top: 0.4rem;
  right: 0.2rem;
  font-size: 0.65rem;
  background-color: #01010d;
  color: #ffffff;
  padding: 0.1rem 0.3rem;
  border-radius: 2rem;
  border: 0.05rem solid #ffffff;
  cursor: pointer;
  transition: all 0.2s linear;
}
.alarm_list_save:hover {
  transform: scale(1.1);
}


.sample_work {
  background-color: #0dcfe4;
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