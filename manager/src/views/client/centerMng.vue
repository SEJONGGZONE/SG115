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

/* (상세팝업에서)주소로 지도이동 */
const checkAddressOnDetail = () => {
    document.getElementById('inputAddress').value = FROM_CENTER_OBJ.value.ADDRESS;
    cvoGeocodeSel();
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
      width: 150,
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

// 구간관리 그리드
const columnDefs2 = reactive({
  value: [
    // {
    //   headerName: "NO",
    //   field: "RANK",
    //   pinned: "left",
    //   lockPinned: true,
    //   cellClass: "header-center",
    //   editable: false,
    //   valueGetter: "node.rowIndex + 1",
    //   cellStyle: { textAlign: "center" },
    //   width: 50,
    // },
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

    FROM_CENTER_OBJ.value = null;
    
    setTimeout(() => {
        
        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
        
        // 팝업영역 숨기기(지도핸들링을 막아서 숨겨준다.)
        var onmap_clientdetail_area = document.getElementsByClassName("onmap_clientdetail_area")[0]; // 접기 버튼
        onmap_clientdetail_area.style.display = "none";
        
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

        if (FROM_CENTER_OBJ.value != null) {
            // 기준점 마커 표시 세팅..
            rootPositionMarkerSet(
                new naver.maps.LatLng(CUR_POSITION_OBJ.value.latitude, CUR_POSITION_OBJ.value.longitude)
            );
            // 상세데이타 반영
            selectRowData.value.LATITUDE = CUR_POSITION_OBJ.value.latitude;
            selectRowData.value.LONGITUDE = CUR_POSITION_OBJ.value.longitude;
        }
        
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

    if (FROM_CENTER_OBJ.value != null) {
        // 상세데이타 반영
        selectRowData.value.LATITUDE = CUR_POSITION_OBJ.value.latitude;
        selectRowData.value.LONGITUDE = CUR_POSITION_OBJ.value.longitude;
    }

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
            // 1.그리드 클릭이벤트 효과
            var event = {};
            event.data = targetObj;
            cellWasClicked(event);
        //   // 상세데이타 세팅
        //   selectRowData.value = targetObj;
        //   // 상세영역 열기
        //   detailShow.value = false;
        //   setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
        //     // 상세영역 열기
        //     detailShow.value = true;
        //   }, 100);  
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
    // console.log("calcHeight=" + calcHeight);

    var calcHeight = 0;
    if (clientHeight == 0) {
        calcHeight = innerHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    } else {
        calcHeight = clientHeight - s2 - 10; // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    }
    // AG그리드 높이 지정
    var grid_clientmnglist1 = document.getElementsByClassName("ag-theme-alpine")[0];
    grid_clientmnglist1.style.height = calcHeight + "px";

  }, 500);
};

/******************************************************************************* api 호출 start */



/**
 * 거래처위치로 지도이동/줌 - 그리드 클릭이벤트에서 호출
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
  
  // 1.거래처 목록 지우기
  clientList.value = [];
  // 2.그리드 데이타 지우기
  rowData.splice(0);
  // 3.지도, 거래처마커 지우기
  onRemoveCust();
  // 주소객체(마커,인포윈도우) 지우기
  onRemoveAddress();
  // 4.상세팝업(페이지)닫기
  detailShow.value = false;
  
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
 * 저장하기
 */
const doSave = async () => {
  //저장
  // 대기바 표시
  startLoadingBar();
  // 파라미터세팅
  let reqData = {
      '@I_COMPANYCD': "00002",
      '@I_SPOT_CD': selectRowData.value.SPOT_CD,
      '@I_SPOT_NAME': selectRowData.value.SPOT_NAME,
      '@I_PIC_NAME': selectRowData.value.PIC_NAME,
      '@I_PIC_TEL': selectRowData.value.PIC_TEL,
      '@I_ADDRESS': selectRowData.value.ADDRESS,
      '@I_LATITUDE': selectRowData.value.LATITUDE,
      '@I_LONGITUDE': selectRowData.value.LONGITUDE,
      '@I_RADIUS': selectRowData.value.RADIUS,
  }

  // 7.API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosCVO().post(`/CVO_623_SPOT_SAV `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {
      console.log(data);
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();

    // 거래처조회 - 호출..
    requestCvoSpotSel();
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
      let LAT = data.RecordSet[0].LAT;
      let LON = data.RecordSet[0].LON;
      console.log("가져온 좌표 :" + LOCATION_INFO);

      selectRowData.value.LAT = LAT;
      selectRowData.value.LON = LON;

      // onRemove();
      const title = address;
      const latitude = selectRowData.value.LAT;
      const longitude = selectRowData.value.LON;
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
    // 거래처위치로 지도이동/줌 
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
 * 센터 그리드셀-클릭시..
 * @param {} event 
 */
const cellWasClicked = (event) => {
    // 팝업영역 보이기
    var onmap_clientdetail_area = document.getElementsByClassName("onmap_clientdetail_area")[0]; // 접기 버튼
    onmap_clientdetail_area.style.display = "flex";
    // 기존상세창을 닫아준다.
    detailShow.value = false;
    // 주소객체(마커,인포윈도우) 지우기
    onRemoveAddress();
    // 전역변수저장
    FROM_CENTER_OBJ.value = event.data;
    ORG_POSITION_OBJ.value.LAT = FROM_CENTER_OBJ.value.LATITUDE;
    ORG_POSITION_OBJ.value.LON = FROM_CENTER_OBJ.value.LONGITUDE;

    setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
        // 상세데이타 세팅
        selectRowData.value = FROM_CENTER_OBJ.value;
        // 상세영역 열기
        detailShow.value = true;
        
    }, 100);  
    

    // 거래처위치로 지도이동/줌 
    moveClientPosition(FROM_CENTER_OBJ.value);
};


/**
 * 구간 그리드셀-클릭시..
 * @param {} event 
 */
 const cellWasClicked2 = (event) => {
    // // 팝업영역 보이기
    // var onmap_clientdetail_area = document.getElementsByClassName("onmap_clientdetail_area")[0]; // 접기 버튼
    // onmap_clientdetail_area.style.display = "flex";
    // 기존상세창을 닫아준다.
    detailShow.value = false;
    // // 주소객체(마커,인포윈도우) 지우기
    // onRemoveAddress();

    console.log('구간 그리드셀-클릭시..');
    // 전역변수저장
    TO_CENTER_OBJ.value = event.data;
    TO_CENTER_OBJ.value.LATITUDE = TO_CENTER_OBJ.value.TO_SPOT_LAT;
    TO_CENTER_OBJ.value.LONGITUDE = TO_CENTER_OBJ.value.TO_SPOT_LON;
    // ORG_POSITION_OBJ.value.LAT = FROM_CENTER_OBJ.value.LATITUDE;
    // ORG_POSITION_OBJ.value.LON = FROM_CENTER_OBJ.value.LONGITUDE;

    // setTimeout(() => { // 애니메이션효과 추가로 지연시간이 필요함..(2023.10.16)
    //     // 상세데이타 세팅
    //     selectRowData.value = FROM_CENTER_OBJ.value;
    //     // 상세영역 열기
    //     detailShow.value = true;
        
    // }, 100);  
    
    // 거래처위치로 지도이동/줌 
    moveClientPosition(TO_CENTER_OBJ.value);
};

const cellFocused = (event) => {
  console.log('--- cellFocused');
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
  detailShow.value = false;
  
  requestCvoSpotSel();
};
const searchBtn = (keyword) => {
  //검색 Btn
  rowData.splice(0);
  detailShow.value = false;
  pageNumber.value = 1;
  requestCvoSpotSel();
};
const cancleBtn = (rowData) => {
  //닫기 Btn
  detailShow.value = false;
  // 팝업영역 숨기기
  var onmap_clientdetail_area = document.getElementsByClassName("onmap_clientdetail_area")[0]; // 접기 버튼
  onmap_clientdetail_area.style.display = "none";
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
        <div class="info_title" style="text-align: center;"><span id="mng_title">출발센터</span></div>
        <!-- 검색어 -->
        <div class="grid_searcharea" style="border-bottom: 1px solid #0b048678;">
          <input type="text" v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="거래처명,주소,전화번호,코드 조회가능.."/>
          <div class="item__buttons" style="width: 5rem;">
            <button class="btn_search" @click="requestCvoSpotSel">
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
            <!-- 하단 -->
            <div class="onmap_clientdetail_area" data-aos="fade-up" data-aos-delay="300">
                <!--거래처상세영역 -->
                <div class="grid_data_detail" v-if="detailShow"
                    style="height:100%; width:19rem; overflow-x:hidden; padding-bottom:0rem; background-color:#ffffffac;">
                    <!-- 거래처정보영역 -->
                    <div class="client_detail_area" data-aos="flip-left" data-aos-delay="50" 
                         style="margin-left:-1.0rem; margin-top:-0.4rem; margin-right:-1.0rem;"
                        >
                        <div class="client_detail_title">
                            <span>{{ selectRowData.SPOT_NAME }} - </span>
                            <span class="client_detail_tag_01">Update<br/>{{ selectRowData.LAST_UPDATE_DATE }}</span>
                        </div>
                        <!-- 거래처 상세정보 -->
                        <div class="item__contents" style="display:flex; flex-direction:row; flex-wrap: wrap; padding:0.5rem 0.0rem 0.0rem 0.5rem;">
                            <div data-aos="fade-up" :data-aos-delay="150" style="display:flex; flex-direction: column;">
                                <label class="form-label">담당자명</label>
                                <input type="text" v-model="selectRowData.PIC_NAME"/>
                            </div>
                            <div data-aos="fade-up" :data-aos-delay="200" style="display:flex; flex-direction: column;">
                                <label class="form-label">전화번호</label>
                                <input type="text" v-model="selectRowData.PIC_TEL"/>
                            </div>
                            <div data-aos="fade-up" :data-aos-delay="250" style="display:flex; flex-direction: column; position:relative;">
                                <label class="form-label">주소</label>
                                <input type="text" v-model="selectRowData.ADDRESS" style="width:15.3rem;"/>
                                <span class="client_detail_tag_02" style="width: 2.0rem;" @click="checkAddressOnDetail">이동</span>
                            </div>
                            <div data-aos="fade-up" :data-aos-delay="300" style="display:flex; flex-direction: column;">
                                <label class="form-label">위도</label>
                                <input type="text" v-model="selectRowData.LATITUDE" readonly/>
                            </div>
                            <div data-aos="fade-up" :data-aos-delay="350" style="display:flex; flex-direction: column;">
                                <label class="form-label">경도</label>
                                <input type="text" v-model="selectRowData.LONGITUDE" readonly/>
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
                        <hr style="margin:0.1rem 0.2rem;"/>
                    </div>
                    <!-- 버튼영역 -->
                    <div class="item__buttons" style="margin:0.5rem 0rem 0.5rem 0rem; border:0px solid red;">
                        <button class="btn_close" @click="cancleBtn">
                            <i class="fa-regular fa-circle-check"></i><span>닫 기</span>
                        </button>
                        <button class="btn_save" @click="saveBtn">
                            <i class="fa-regular fa-circle-check"></i><span>저 장</span>
                        </button>
                        <button class="btn_save" @click="rollbackPosition" style="background-color:#6244c6; border-width:0px;">
                            <i class="fa-regular fa-circle-check"></i><span>원래위치</span>
                        </button>
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

.route_info_title {
  align-items: center;
  justify-content: center;
  background-color: #ad8dff;
  color: #ffffff;
  font-size: 1.3rem;
  font-weight: 600;
  // text-shadow: 2px 2px 2px #FFFFFF;
  border-width: 0px;
  padding: 0rem 0.1rem;
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