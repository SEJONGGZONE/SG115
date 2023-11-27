<script setup>
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

function handleGetDate(minusDate) {
  var d = new Date();
      d = d.setDate(d.getDate() - minusDate);
  return d;
}
const getVisitorData = () => {
			return {
				chart: {
					series: [
						{ 
							name: 'Unique Visitors', 
							data: [
								[handleGetDate(77), 13], [handleGetDate(76), 13], [handleGetDate(75), 6 ], 
								[handleGetDate(73), 6 ], [handleGetDate(72), 6 ], [handleGetDate(71), 5 ], [handleGetDate(70), 5 ], 
								[handleGetDate(69), 5 ], [handleGetDate(68), 6 ], [handleGetDate(67), 7 ], [handleGetDate(66), 6 ], 
								[handleGetDate(65), 9 ], [handleGetDate(64), 9 ], [handleGetDate(63), 8 ], [handleGetDate(62), 10], 
								[handleGetDate(61), 10], [handleGetDate(60), 10], [handleGetDate(59), 10], [handleGetDate(58), 9 ], 
								[handleGetDate(57), 9 ], [handleGetDate(56), 10], [handleGetDate(55), 9 ], [handleGetDate(54), 9 ], 
								[handleGetDate(53), 8 ], [handleGetDate(52), 8 ], [handleGetDate(51), 8 ], [handleGetDate(50), 8 ], 
								[handleGetDate(49), 8 ], [handleGetDate(48), 7 ], [handleGetDate(47), 7 ], [handleGetDate(46), 6 ], 
								[handleGetDate(45), 6 ], [handleGetDate(44), 6 ], [handleGetDate(43), 6 ], [handleGetDate(42), 5 ], 
								[handleGetDate(41), 5 ], [handleGetDate(40), 4 ], [handleGetDate(39), 4 ], [handleGetDate(38), 5 ], 
								[handleGetDate(37), 5 ], [handleGetDate(36), 5 ], [handleGetDate(35), 7 ], [handleGetDate(34), 7 ], 
								[handleGetDate(33), 7 ], [handleGetDate(32), 10], [handleGetDate(31), 9 ], [handleGetDate(30), 9 ], 
								[handleGetDate(29), 10], [handleGetDate(28), 11], [handleGetDate(27), 11], [handleGetDate(26), 8 ], 
								[handleGetDate(25), 8 ], [handleGetDate(24), 7 ], [handleGetDate(23), 8 ], [handleGetDate(22), 9 ], 
								[handleGetDate(21), 8 ], [handleGetDate(20), 9 ], [handleGetDate(19), 10], [handleGetDate(18), 9 ], 
								[handleGetDate(17), 10], [handleGetDate(16), 16], [handleGetDate(15), 17], [handleGetDate(14), 16], 
								[handleGetDate(13), 17], [handleGetDate(12), 16], [handleGetDate(11), 15], [handleGetDate(10), 14], 
								[handleGetDate(9) , 24], [handleGetDate(8) , 18], [handleGetDate(7) , 15], [handleGetDate(6) , 14], 
								[handleGetDate(5) , 16], [handleGetDate(4) , 16], [handleGetDate(3) , 17], [handleGetDate(2) , 7 ], 
								[handleGetDate(1) , 7 ], [handleGetDate(0) , 7 ]
							]
						},
						{ 
							name: 'Page Views', 
							data: [
								[handleGetDate(77), 14], [handleGetDate(76), 13], [handleGetDate(75), 15], 
								[handleGetDate(73), 14], [handleGetDate(72), 13], [handleGetDate(71), 15], [handleGetDate(70), 16], 
								[handleGetDate(69), 16], [handleGetDate(68), 14], [handleGetDate(67), 14], [handleGetDate(66), 13], 
								[handleGetDate(65), 12], [handleGetDate(64), 13], [handleGetDate(63), 13], [handleGetDate(62), 15], 
								[handleGetDate(61), 16], [handleGetDate(60), 16], [handleGetDate(59), 17], [handleGetDate(58), 17], 
								[handleGetDate(57), 18], [handleGetDate(56), 15], [handleGetDate(55), 15], [handleGetDate(54), 15], 
								[handleGetDate(53), 19], [handleGetDate(52), 19], [handleGetDate(51), 18], [handleGetDate(50), 18], 
								[handleGetDate(49), 17], [handleGetDate(48), 16], [handleGetDate(47), 18], [handleGetDate(46), 18], 
								[handleGetDate(45), 18], [handleGetDate(44), 16], [handleGetDate(43), 14], [handleGetDate(42), 14], 
								[handleGetDate(41), 13], [handleGetDate(40), 14], [handleGetDate(39), 13], [handleGetDate(38), 10], 
								[handleGetDate(37), 9 ], [handleGetDate(36), 10], [handleGetDate(35), 11], [handleGetDate(34), 11], 
								[handleGetDate(33), 11], [handleGetDate(32), 10], [handleGetDate(31), 9 ], [handleGetDate(30), 10], 
								[handleGetDate(29), 13], [handleGetDate(28), 14], [handleGetDate(27), 14], [handleGetDate(26), 13], 
								[handleGetDate(25), 12], [handleGetDate(24), 11], [handleGetDate(23), 13], [handleGetDate(22), 13], 
								[handleGetDate(21), 13], [handleGetDate(20), 13], [handleGetDate(19), 14], [handleGetDate(18), 13], 
								[handleGetDate(17), 13], [handleGetDate(16), 19], [handleGetDate(15), 21], [handleGetDate(14), 22],
								[handleGetDate(13), 25], [handleGetDate(12), 24], [handleGetDate(11), 24], [handleGetDate(10), 22], 
								[handleGetDate(9) , 16], [handleGetDate(8) , 15], [handleGetDate(7) , 12], [handleGetDate(6) , 12], 
								[handleGetDate(5) , 15], [handleGetDate(4) , 15], [handleGetDate(3) , 15], [handleGetDate(2) , 18], 
								[handleGetDate(2) , 18], [handleGetDate(0) , 17]
							] 
						}
					],
					options: {
						colors: [appVariable.color.teal, appVariable.color.blue],
						fill: {
							opacity: .75,
							type: 'solid'
						},
						legend: {
							position: 'top',
							horizontalAlign: 'right',
							offsetY: 15,
							offsetX: 500,
							labels: {
								colors: appVariable.color.white
							}
						},
						xaxis: {
							type: 'datetime',
							tickAmount: 6,
							labels: {
								style: {
									colors: appVariable.color.white
								}
							}
						},
						yaxis: {
							labels: {
								style: {
									colors: appVariable.color.white
								}
							}
						},
						tooltip: { y: { formatter: function (val) { return "$ " + val + " thousands" } } },
						chart: { height: '100%', type: 'area', toolbar: { show: false }, stacked: true },
						plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
						dataLabels: { enabled: false },
						grid: { 
							show: true, borderColor: 'rgba('+ appVariable.color.whiteRgb +', .15)',
							xaxis: {
								lines: {
									show: true
								}
							},   
							yaxis: {
								lines: {
									show: true
								}
							},
							padding: {
								top: -40,
								right: 3,
								bottom: 0,
								left: 10
							},
						},
						stroke: { 
							show: false,
							curve: 'straight'
						}
					}
				}
			};
}
let visitor = ref(null);

const gridApi = ref(null);
const rowData = reactive([]);
const empList = ref([]); // 배송사원 목록
const dispatchSummaryList = ref([]); // 배송일지 목록
const isShowMoreBtn = ref(false);

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
    {
      headerName: "NO",
      field: "seq",
      pinned: "left",
      lockPinned: true,
      cellClass: "header-center",
      editable: false,
      valueGetter: "node.rowIndex + 1",
      cellStyle: { textAlign: "center" },
      width: 100,
    },
    {
      headerName: "거래처명",
      field: "COMPANY_NAME",
      pinned: "left",
      lockPinned: true,
      cellClass: "lock-pinned",
      cellStyle: { textAlign: "left" },
    },
    {
      headerName: "사업자번호",
      field: "COMPANY_CORPNO",
      // pinned: 'left',
      // lockPinned: true,
      cellClass: "lock-pinned",
      width: 190,
    },
    {
      headerName: "사용자명",
      field: "USER_NAME",
    },
    {
      headerName: "휴대폰번호",
      field: "USER_PHONE",
    },
    {
      headerName: "이메일",
      field: "EMAIL",
    },
    {
      headerName: "승인여부",
      field: "STATUS_NM",
      editable: false,
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
const userClcode = userInfo.CLCODE;
const userId = userInfo.ID;
const pageNumber = ref(1);
const pageSize = ref(20);

import { useAlert } from "@/composables/showAlert";
const { showAlert, showAlertSuccess, showConfirm } = useAlert();
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
onMounted(() => {
  pageNumber.value = 1;

  setTimeout(() => {
    // 지도초기화
    initMap();
    // 검색..
    doSearch();
    // 화면 초기화
    document.getElementsByClassName("info_summary")[0].style.display = "none";
    // 현위치조회 요청
    nowPositionSearch();

    visitor.value = getVisitorData();
  }, 500);

  animateNumber();
  setTimeout(() => {
    const arrPrvModels = document
      .getElementById("modal_insert_area")
      .getElementsByClassName("modal");
    for (var i = 0; i < arrPrvModels.length; i++) {
      // 기존요소 삭제
      document.getElementById("modal_insert_area").removeChild(arrPrvModels[i]);
    }
    const arrModels = document
      .getElementsByClassName("section")[0]
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
  mapInstance.value.setOptions("zoomControl", true); //줌 컨트롤의 표시 여부
  mapInstance.value.setOptions("zoomControlOptions", {
    //줌 컨트롤의 옵션
    position: naver.maps.Position.TOP_RIGHT,
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
  console.log("지도, 차량경로 지우기");
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
  console.log("지도, 차량경로 지우기");
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
    // 배송정보를 확인한다.
    dispatchSummarySearch(selectTruckObj.RANK - 1, selectTruckObj);
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
      var on_off_string, dtmString, route_seq_css;
      if (targetObj.INBOUND_DTM != null) {
        on_off_string = "_on";
        targetObj.inboundClassString = "";
        dtmString =
          targetObj.INBOUND_DTM.substring(11, 5) +
          " <small><small>도착</small></small>";
      } else {
        on_off_string = "_off";
        targetObj.inboundClassString = "_NOTIN";
        dtmString = "<small><small>- 미도착 -</small></small>";
      }
      if (targetObj.ROUTE_SEQ < 10) {
        route_seq_css = "marker_cust_route_seq_1";
      } else {
        route_seq_css = "marker_cust_route_seq_2";
      }
      // 2.거래처마커 추가
      var custerMarker = new window.naver.maps.Marker({
        position: latLng,
        map: mapInstance.value,
        index: targetObj.ROUTE_SEQ - 1,
        targetObj: targetObj,
        title: targetObj.CLNAME,
        zIndex: 100,
        icon: {
          content: [
            '<div class="marker_cust' + on_off_string + '">',
            '<span class="' +
              route_seq_css +
              '">' +
              targetObj.ROUTE_SEQ +
              '</span><span style="font-size:17px;">' +
              targetObj.CLNAME +
              "</span<BR>",
            '<hr style="margin:2px 0px; border:0px; border-top:1px solid #FFFFFF;"/>',
            "" +
              targetObj.ORDER_AMT +
              "<small><small><small>&nbsp;원</small></small></small> " +
              dtmString,
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
        //console.log(custerMarker.targetObj);

        // 우측 배송리르스트에서 클릭효과, 거래처 이동경로 정보 표시
        showCustInfo(custerMarker.index, custerMarker.targetObj);

        // openArrowLine.setOptions({
        // 	strokeColor: '#eb2b2b6b',
        // 	strokeStyle: 'dash',
        // 	strokeOpacity: 0.5
        // });
      });
      // 거래처 마커 마우스오버시..
      naver.maps.Event.addListener(custerMarker, "mouseover", function () {
        console.log(targetObj.ROUTE_SEQ);
        // openArrowLine.setOptions({
        // 	strokeColor: '#eb2b2b6b',
        // 	strokeStyle: 'dash',
        // 	strokeOpacity: 0.5
        // });
      });
      // 거래처 마커 저장
      custMarkerList.push(custerMarker);
      // // 인포윈도우 세팅
      // var htmlString = '';
      // htmlString += '<div style="padding:0px 10px; border-radius:50px;">';
      // htmlString += targetObj.ROUTE_SEQ + ' (' + targetObj.CLNAME + ')<BR>';
      // htmlString += targetObj.ADDRESS + '<BR>';
      // htmlString += targetObj.INBOUND_DTM + '';
      // htmlString += '</div>';
      // custInfowindow.push(
      // 	new window.naver.maps.InfoWindow({
      // 	content: htmlString
      // 	}),
      // );
      // custInfowindow[0].open(mapInstance.value, custMarkerList[0]);
      custkMarkerIndex++;
    }
  } catch (error) {
    console.error(error);
  }
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
        console.log(openArrowLine);
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
const drawTruckRouteSelectLine = (startDtm, endDtm) => {
  var targetRouteInfos = [];
  var targetRouteLines = [];

  console.log("startDtm=" + startDtm);
  console.log("endDtm=" + endDtm);

  var nLastOnMore = 0;
  routeInfoList.forEach((element) => {
    //console.log(element.TRACEDATE + element.TRACETIME);
    var curDtm = element.TRACEDATE + element.TRACETIME;

    if (startDtm <= curDtm && endDtm >= curDtm) {
      // 시간범위 이내이다..

      // 경로위치정보 객체 저장..(계산용)
      targetRouteInfos.push(element);
      // 경로위치정보 객체 저장..(그리기용)
      let latLng = new window.naver.maps.LatLng(
        element.LATITUDE,
        element.LONGITUDE
      );
      targetRouteLines.push(latLng);

      nLastOnMore = 1;
    } else {
      // 시간범위가 아니다..

      // 시간범위안에서 찾았다고해도, 추가로 더 넣어주기 위함...
      if (nLastOnMore > 0) {
        // 경로위치정보 객체 저장..(그리기용)
        let latLng = new window.naver.maps.LatLng(
          element.LATITUDE,
          element.LONGITUDE
        );
        targetRouteLines.push(latLng);

        nLastOnMore--;
      }
    }
  });

  // 선택노선 이동경로 그리기
  if (targetRouteLines.length > 0) {
    onRemoveTruckRoute();

    drawTruckRouteLine(targetRouteLines, true);
  }
};

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
 * 거래처 반경거리를 그려준다..
 * @param {*} roundMeter
 * @param {*} latLng
 */
const drawCustCircle = (roundMeter, latLng) => {
  // 3.거래처반경(200미터 설정:자동진입 체크반경)
  var custCircle = new naver.maps.Circle({
    map: mapInstance.value,
    center: latLng, // 중심점
    radius: roundMeter, // 반경거리
    fillColor: "#4952ff",
    fillOpacity: 0.1,
    strokeWeight: 1,
    strokeColor: "#4952ff9b",
  });
  arrCustCircle.push(custCircle); // 지우기위해 저장..
};

/**
	 * 배차목록 세팅
	 * @param {
            "ROUTE_CNT": 10,
            "TOT_ORDER_AMT": 1426430,
            "START_TIME": "05:25",
            "LAST_REP_TIME": "10:51",
            "TOT_DISTANCE": "87.29",
            "MAX_SPEED": 101,
            "DISPATCH_ID": "DIS20230905052554226",
            "COMPANYCD": "00001",
            "DDATE": "2023-09-05",
            "SHIFT_NO": 39,
            "ROUTE_SEQ": 1,
            "EPCODE": "0070",
            "EPNAME": "박준상",
            "EPPHONE": "010-4655-0048",
            "CLCODE": "0000001027",
            "CLNAME": "오송집(고바우)",
            "LAT": "36.6232536",
            "LON": "127.3253170",
            "ADDRESS": "충북 청주시 흥덕구 오송읍 오송생명5로 277번지 1층 오송",
            "PIC_NAME": "김동옥",
            "PIC_TEL": "010-9472-2470",
            "ITEM_INFO": "풀그린) 우동건더기스프500g 외 5건",
            "ITEM_CNT": 6,
            "ORDER_AMT": "80,330",
            "EXPECT_TIME": "2023-09-05 05:36:57",
            "RESULT_TIME": "",
            "ETC_DATA": "",
            "DISTANCE": "6028",
            "DURATION": "661",
            "INBOUND_CNT": 1,
            "INBOUND_DTM": "2023-09-05 08:23:15",
            "OUTBOUND_CNT": 0,
            "OUTBOUND_DTM": null
        } targetObj 
	 */
const addDispatchList = (targetObj) => {
  try {
    // 위치정보 확인
    targetObj.positionYn = false;

    if (targetObj.LAT > 0 && targetObj.LON > 0) {
      targetObj.positionYn = true;
    }
    // 도착시간 확인
    if (targetObj.INBOUND_DTM != null) {
      targetObj.inboundClassString = "";
      targetObj.inboundDtmString =
        targetObj.INBOUND_DTM.substring(11, 5) + " 도착";
    } else {
      targetObj.inboundClassString = "_NOTIN";
      targetObj.inboundDtmString = "- 미도착 -";
    }
    // 배송순번 자리수 초과시 css 변경
    var route_seq_class = "route_seq";
    if (targetObj.ROUTE_SEQ > 9) {
      route_seq_class = "route_seq_2";
    }
    targetObj.route_seq_class = route_seq_class + targetObj.inboundClassString;
    targetObj.distanceK = common_utils.distanceStringK(targetObj.DISTANCE);
    targetObj.cust_class = "cust" + targetObj.inboundClassString;
    targetObj.address_class = "address" + targetObj.inboundClassString;
    targetObj.amount_class = "amount" + targetObj.inboundClassString;
    targetObj.invoice_class = "invoice" + targetObj.inboundClassString;

    if (targetObj.positionYn) {
      // 위치정보가 있음..
      targetObj.arrive_class = "arrive" + targetObj.inboundClassString;
      targetObj.distance_class = "distance" + targetObj.inboundClassString;
    } else {
      // 위치정보가 없음..
      targetObj.arrive_class = "arrive_NOTIN";
      targetObj.distance_class = "distance_NOTIN";
    }

    targetObj.dashed_line_class = "dashed-line" + targetObj.inboundClassString;
    targetObj.triangle_triangle__left_class =
      "triangle triangle--left" + targetObj.inboundClassString;

    // 리스트에 푸쉬..
    dispatchSummaryList.value.push(targetObj);
  } catch (errpr) {
    console.log(error);
  }
};

/**
 * 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
 */
const resizeScreenSet = () => {
  setTimeout(() => {
    var info_detail = document.getElementsByClassName("info_detail")[0];
    var area_summary = document.getElementsByClassName("area_summary")[0];
    var s1 = info_detail.getBoundingClientRect().top;
    var s2 = area_summary.getBoundingClientRect().bottom;
    var calcHeight = s2 - s1 - 5;
    info_detail.style.height = calcHeight + "px";
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
    dispatchSummarySearch(curTruckIndex, curTruckObj);
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
    "<span>" +
    targetObj.NAME +
    "</span>&nbsp;" +
    "&nbsp;" +
    targetObj.SPEED +
    " <small><small>km/h</small></small>&nbsp;" +
    "<small><small>- " +
    targetObj.ADDRESS +
    "</small></small>";
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
/**
 * 배송일지 요약정보,세팅
 * @param {*} targetObj
 */
const setSummaryReport = (targetObj, deviceNo) => {
  // 배송정보-보이기
  document.getElementsByClassName("info_summary")[0].style.display = "flex";

  // 주문건수,금액
  var orderAmt = targetObj.TOT_ORDER_AMT;
  orderAmt = orderAmt.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  var htmlString = "";
  htmlString =
    "" +
    targetObj.ROUTE_CNT +
    "&nbsp;<small><small>건</small></small>&nbsp;/&nbsp;" +
    "" +
    orderAmt +
    "&nbsp;<small><small>원</small></small>";
  document.getElementsByClassName("item_info01")[0].innerHTML = htmlString;
  // 출발일시
  htmlString =
    targetObj.START_TIME +
    "&nbsp;<small><small><small><small>출발</small></small></small></small>";
  document.getElementsByClassName("item_info02")[0].innerHTML = htmlString;
  // 최종보고시간
  htmlString =
    "<small><small><small>최종 보고시간</small></small></small>&nbsp;" +
    targetObj.LAST_REP_TIME;
  document.getElementsByClassName("item_info03")[0].innerHTML = htmlString;
  // 이동거리,최고속도
  htmlString =
    "<small><small><small>이동거리</small></small></small>&nbsp;" +
    targetObj.TOT_DISTANCE +
    "<small><small><small>&nbsp;km</small></small></small>&nbsp;" +
    "<small><small><small>/ 최고</small></small></small>&nbsp;" +
    targetObj.MAX_SPEED +
    "<small><small><small>&nbsp;km/h</small></small></small>";
  document.getElementsByClassName("item_info04")[0].innerHTML = htmlString;

  // 경로정보 가져오기로직 호출
  setTimeout(() => {
    // 경로정보 조회
    var startDtm = targetObj.DISPATCH_ID.substring(3, 17);
    var endDtm =
      targetObj.DISPATCH_ID.substring(3, 11) +
      targetObj.LAST_REP_TIME.replaceAll(":", "") +
      "59";

    doRouteSearch(deviceNo, startDtm, endDtm);
  }, 1000);
};

/******************************************************************************* api 호출 start */

const nowPositionSearch = async () => {
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
  // 배송정보-감추기
  document.getElementsByClassName("info_summary")[0].style.display = "none";

  // 파라미터 구성
  const param = {
    companyCd: "0001",
    deviceNo: "",
    vehicleNo: "",
    keyword: "", // keyword.value,
    lat: "",
    lon: "",
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

        // 차량기준 - 지도영역 조정(마커를 다보이게..)
        mapInstance.value.fitBounds(arrTruckBounds);

        // 화면 갱신정보 업데이트
        var htmlString =
          "(주)목원푸드 배송현황<small><small><small><small>" +
          "&nbsp;-&nbsp;Update " +
          common_utils.formatTime_HH_MM_SS(new Date()) +
          "</small></small></small></small>";

        document.getElementsByClassName("info_title")[0].innerHTML = htmlString;

        // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
        resizeScreenSet();
      }
    }, 100);
  }
};

/**
 * 배송일지 조회
 *
 * 0070	박준상	01046550048
 * 0061	송신호	01071881379
 * 0004	임수호	01055013694
 * 0066	홍성훈	01021355562
 */
const dispatchSummarySearch = async (index, targetObj) => {
  // 다른 차량은 지우고 선택한 차량만 보여준다.
  onRemoveTruck();
  addTruckMarker(targetObj, naver.maps.Animation.BOUNCE);

  // 선택된 차량객체 지정..
  curTruckIndex = index;
  curTruckObj = targetObj;

  pdfFileName.value = `${targetObj?.VEHICLENO}_${targetObj.NAME}_${targetObj.TRACEDATE}`

  var elementLength = document.getElementsByClassName("item_emp").length;
  for (var i = 0; i < elementLength; i++) {
    if (curTruckIndex == i) {
      document.getElementsByClassName("item_emp")[i].style.transform =
        "scale(1.1)";
      document.getElementsByClassName("item_emp")[i].style.border =
        "0.05rem solid #FFFFFF";
      document.getElementsByClassName("item_emp")[i].style.boxShadow =
        "0px 5px 10px #00000078";
    } else {
      document.getElementsByClassName("item_emp")[i].style.transform =
        "scale(1.0)";
      document.getElementsByClassName("item_emp")[i].style.transition =
        "all 0.2s linear";
      document.getElementsByClassName("item_emp")[i].style.border =
        "0.05rem solid #989898E6";
      document.getElementsByClassName("item_emp")[i].style.boxShadow =
        "0px 0px 0px #00000078";
    }
  }

  // 배송일지 목록 비우기..
  dispatchSummaryList.value = [];
  // 배송정보-감추기
  document.getElementsByClassName("info_summary")[0].style.display = "none";
  // 지도, 거래처 마커/객체 지우기
  onRemoveCust();
  // 기존경로 지우기
  onRemoveTruckRoute();

  // 로딩바 표시..
  startLoadingBar();
  // 배송차량-타이틀,세팅
  setSummaryTitle(targetObj);

  // 파라미터 구성
  const param = {
    ddate: common_utils.setDateFormat(curDate),
    companyCd: "00002",
    deviceNo: targetObj.DEVICENO, // "01055013694",
    vehicleNo: "",
    keyword: "", // keyword.value,
    lat: "",
    lon: "",
  };
  // console.log("[배송일지 조회]-----------------------------------------------------");
  //console.log(param);

  let data, result;
  try {
    result = await operateApi.cvoDispatchSummarySel(param);
    data = result.data;
    // 데이타 세팅...
    if (data.RecordCount > 0) {
    }
  } catch (error) {
    console.error(error);
  } finally {
    // 로딩바숨기기..
    removeLoadingBar();

    setTimeout(() => {
      // 데이타 - LOOP
      var infos = data.RecordSet;
      infos.forEach(function (item) {
        //console.log(item.ROUTE_SEQ);
        // 배송일지 추가
        addDispatchList(item);
        // 배송거래처,마커추가
        addCustMarker(item);
      });

      // 지도영역 조정(마커를 다보이게..)
      drawDispatchCustLine(arrCustBounds, infos);
      mapInstance.value.fitBounds(arrCustBounds);

      // 지도이동
      var latLng = new window.naver.maps.LatLng(
        targetObj.LATITUDE,
        targetObj.LONGITUDE
      );
      mapInstance.value.setCenter(latLng);
      mapInstance.value.setZoom(14);

      // 배송일지 요약정보 세팅 + 안에서 이동경로정보도 가져온다.
      if (infos.length > 0) setSummaryReport(infos[0], targetObj.DEVICENO);

      // 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
      resizeScreenSet();
    }, 100);
  }
};

/**
 * 배송리스트에 - 거래처영역 마우스올렸을때
 * @param {*} index
 * @param {*} targetObj
 */
const showCustInfo = async (index, targetObj) => {
  // console.log(index);
  // console.log(targetObj);

  // 지도이동 - 도착지위치로..
  var latLng = new window.naver.maps.LatLng(targetObj.LAT, targetObj.LON);
  mapInstance.value.setCenter(latLng);
  mapInstance.value.setZoom(16);
  mapInstance.value.setZoom(17);

  // 지도에 차량이동경로 정보 표시하기..
  var startDtm = "",
    endDtm = "",
    arrBounds = [];
  // 거래처 반경원 지우기
  onRemoveCustCircle();
  if (index == 0) {
    // 첫번째 거래처는 배차번호기준 시간부터 측정한다.
    startDtm = targetObj.DISPATCH_ID;
    startDtm = startDtm.substring(3, 14); // 시간정보만 추출..
    arrBounds.push(CENTER_POSITION); // 센터위치 추가..
    endDtm = targetObj.INBOUND_DTM;
    if (endDtm != null) {
      endDtm = endDtm
        .replaceAll(" ", "")
        .replaceAll("-", "")
        .replaceAll(":", "");
    } else {
      console.log("아직 도착하지않았습니다.");
    }
    // 반경원 추가..
    var latLng = new window.naver.maps.LatLng(targetObj.LAT, targetObj.LON);
    drawCustCircle(CUST_ROUND_METER, latLng);
    arrBounds.push(latLng); // 도착지 위치 추가..
  } else {
    // 이전 거래처..
    var prvCust = dispatchSummaryList.value[index - 1];
    startDtm = prvCust.INBOUND_DTM;
    if (startDtm != null) {
      startDtm = startDtm
        .replaceAll(" ", "")
        .replaceAll("-", "")
        .replaceAll(":", "");
    } else {
      console.log("아직 도착하지않았습니다.");
    }
    // 반경원 추가..
    var latLng = new window.naver.maps.LatLng(prvCust.LAT, prvCust.LON);
    drawCustCircle(200, latLng);
    arrBounds.push(latLng); // 출발지 위치 추가..
    // 본 거래처..
    endDtm = targetObj.INBOUND_DTM;
    if (endDtm != null) {
      endDtm = endDtm
        .replaceAll(" ", "")
        .replaceAll("-", "")
        .replaceAll(":", "");
    } else {
      console.log("아직 도착하지않았습니다.");
    }
    // 반경원 추가..
    latLng = new window.naver.maps.LatLng(targetObj.LAT, targetObj.LON);
    drawCustCircle(CUST_ROUND_METER, latLng);
    arrBounds.push(latLng); // 도착지 위치 추가..
  }
  // 선택된 노선상의 실제 차량의 이동정보를 얻어온후 경로를 그려준다.
  drawTruckRouteSelectLine(startDtm, endDtm);

  // 지도영역 설정(이전거래처와의 반경으로 설정)
  mapInstance.value.fitBounds(arrBounds);
  //
  // // 노선 거래처 정보 얻기
  // var fromIdx = (openArrowLine.curItem.ROUTE_SEQ-2)*1;
  // var toIdx = (openArrowLine.curItem.ROUTE_SEQ-1)*1;
  // // console.log(fromIdx);
  // // console.log(toIdx);
  // var fromCust = custMarkerList[fromIdx].targetObj;
  // var toCust = custMarkerList[toIdx].targetObj;
  // console.log(fromCust.CLNAME + '>>' + toCust.CLNAME);
  // console.log(fromCust.INBOUND_DTM + '>>' + toCust.INBOUND_DTM);
  // 경로추적 사긴정보 얻기
  // var startDtm = fromCust.INBOUND_DTM;
  // startDtm = startDtm.replaceAll(' ','').replaceAll('-','').replaceAll(':','');

  // var endDtm = toCust.INBOUND_DTM;
  // endDtm = endDtm.replaceAll(' ','').replaceAll('-','').replaceAll(':','');
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
 * {"@I_DEVICENO":"01021355562","@I_TRACE_START_DTM":"20230907040000","@I_TRACE_END_DTM":"20230907235959"}
 */
const doRouteSearch = async (deviceNo, startDtm, endDtm) => {
  // console.log(deviceNo);
  // console.log(startDtm);
  // console.log(endDtm);

  // 기존경로 지우기
  onRemoveTruckRoute();
  // 대기창표시
  isShowMoreBtn.value = false;
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

const doSearch = async () => {
  //조회

  isShowMoreBtn.value = false;
  startLoadingBar();
  const param = {
    clcode: "",
    searchKeyword: searchKeyword.value ?? "",
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
  };

  let data;
  try {
    data = await memberApi.memberMng(param);
    if (data.RecordCount > 0) {
      let length = rowData.length;
      rowData.push(...data.RecordSet);
      gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
        rowData
      );
      if (data.RecordCount === pageSize.value) {
        isShowMoreBtn.value = true;
      }
    } else {
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    var productDiv = document.getElementsByClassName("ag-body-viewport")[0];
    setTimeout(() => {
      productDiv.scrollTop = productDiv.scrollHeight;
    }, 100);
  }
};

const doSave = async () => {
  //저장

  const param = {
    geonum: selectRowData.value.GEONUM,
    clcode: "", // selectRowData.value.CLCODE,
    userName: selectRowData.value.USER_NAME,
    userPhone: selectRowData.value.USER_PHONE,
    companyName: selectRowData.value.COMPANY_NAME,
    companyCorpno: selectRowData.value.COMPANY_CORPNO,
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
      doSearch();
    }
  } catch (error) {
    console.error(error);
  }
};
/******************************************************************************* api 호출 end */

/******************************************************************************* 버튼 및 액션 이벤트 start */
// var gridOptions = {
// 	columnDefs: columnDefs.value,
// 	rowData: rowData.value,
// 	defaultColDef:defaultColDef.value,
// 	rowSelection: 'multiple', /* 'single' or 'multiple',*/
// 	animateRows:true,
// 	copyHeadersToClipboard:true,
// 	enableColResize: true,
// 	enableSorting: true,
// 	enableFilter: true,
// 	enableRangeSelection: true,
// 	suppressColumnMoveAnimation:true,
// 	suppressRowClickSelection: false,
// 	suppressHorizontalScroll: true,
// 	localeText: {noRowsToShow: '조회 결과가 없습니다.'},
// 	getRowStyle: function (param) {
// 		if (param.node.rowPinned) {
// 			return {'font-weight': 'bold', background: '#dddddd'};
// 		}
// 		return {'text-align': 'center'};
// 	},
// 	getRowHeight: function(param) {
// 		if (param.node.rowPinned) {
// 			return 30;
// 		}
// 		return 24;
// 	},
// 	// GRID READY 이벤트, 사이즈 자동조정
// 	onGridReady: function (event) {
// 		event.api.sizeColumnsToFit();
// 	},
// 	// 창 크기 변경 되었을 때 이벤트
// 	onGridSizeChanged: function(event) {
// 		event.api.sizeColumnsToFit();
// 	},
// 	onRowEditingStarted: function (event) {
// 		console.log('never called - not doing row editing');
// 	},
// 	onRowEditingStopped: function (event) {
// 		console.log('never called - not doing row editing');
// 	},
// 	onCellEditingStarted: function (event) {
// 		console.log('cellEditingStarted');
// 	},
// 	onCellEditingStopped: function (event) {
// 		console.log('cellEditingStopped');
// 	},
// 	onRowClicked : function (event){
// 		console.log('onRowClicked');
// 	},
// 	onCellClicked : function (event){
// 		console.log('onCellClicked');
// 	},
// 	isRowSelectable : function(event){
// 		console.log('isRowSelectable');
// 		return true;
// 	},
// 	onSelectionChanged : function (event) {
// 		console.log('onSelectionChanged');
// 	},
// 	onSortChanged: function (event) {
// 		console.log('onSortChanged');
// 	},
// 	onCellValueChanged: function (event) {
// 		console.log('onCellValueChanged');
// 	},
// 	getRowNodeId : function(data) {
// 		return null;
// 	},
// 	// 리드 상단 고정
// 	setPinnedTopRowData: function(data) {
// 		return null;
// 	},
// 	// 그리드 하단 고정
// 	setPinnedBottomRowData: function(data) {
// 		return null;
// 	},
// 	// components:{
// 	//     numericCellEditor: NumericCellEditor,
// 	//     moodEditor: MoodEditor
// 	// },
// 	debug: false
// };

const setSelectRowData = (data) => {
  selectRowData.value = {};
  selectRowData.value.COMPANY_NAME = data.COMPANY_NAME;
  selectRowData.value.COMPANY_CORPNO = data.COMPANY_CORPNO;
  selectRowData.value.USER_NAME = data.USER_NAME;
  selectRowData.value.USER_PHONE = data.USER_PHONE;
  selectRowData.value.EMAIL = data.EMAIL;
  selectRowData.value.STATUS = data.STATUS;
};

const cellValueChanged = (event) => {
  setSelectRowData(event.data);
};
const cellWasClicked = (event) => {
  detailShow.value = true;
  selectRowData.value = event.data;
  //console.log("cell was clicked", event);
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
const searchBtnEnter = (event) => {
  //검색 Btn
  if (event.keyCode != 13) return;
  rowData.splice(0);
  detailShow.value = false;
  pageNumber.value = 1;
  doSearch();
};
const searchBtn = (keyword) => {
  //검색 Btn
  rowData.splice(0);
  detailShow.value = false;
  pageNumber.value = 1;
  doSearch();
};
const cancleBtn = (rowData) => {
  //닫기 Btn
  detailShow.value = false;
};
const saveBtn = (rowData) => {
  //저장 Btn
  doSave();
};
const searchMoteBtn = () => {
  //더보기 Btn
  pageNumber.value = pageNumber.value + 1;
  doSearch();
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

/*PDF 미리보기*/
import PdfLayout from "@/components/pdf/PdfLayout.vue";
import SaleLog from "@/components/pdf/order/SaleLog.vue";
const pdfFileName = ref(null) 
const openPdfView = () => {
  console.log('---------------- openPdfView');
  document.getElementById("modalPdfViewerDiv").click();
  console.log('---------------- openPdfView-----0');
}; 

</script>
<template>
  <div class="section section__management">
    <!-- <div class="group__search">
            <div class="part__search_box">
                <div class="group__title">
                    <h2>회원관리</h2>
                </div>
                <input type="text"  v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="검색어를 입력하세요" />
                <button @click.prevent="searchBtn">
                    <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
                </button>
            </div>
        </div> -->
    <div class="group__contents">
      <div
        class="dispatch_summary"
        style="gap: 0px 0px; padding: 0px; margin: 0px; overflow: hidden"
      >
        <div style="display: flex; width: 100%; height: 100%">
          <!-- 배차정보영역 -->
          <div class="area_summary" style="width: 30%; height: auto">
            <!-- 타이틀 -->
            <div class="info_title"></div>
            <!-- 배송사원목록 -->
            <div class="info_emplist">
              <div
                @mouseover="startMq_L()"
                @mouseout="stopMq()"
                class="mq_arrow"
              >
                ◀︎
              </div>
              <div id="empListDiv">
                <div
                  class="item_emp"
                  v-for="(targetObj, i) in empList"
                  v-bind:key="i"
                  @click="dispatchSummarySearch(i, targetObj)"
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
              <div
                @mouseover="startMq_R()"
                @mouseout="stopMq()"
                class="mq_arrow_r"
              >
                ►
              </div>
            </div>
            <!-- 배송정보,타이틀 -->
            <div class="item_title">- 배송차량을 선택해주세요 -</div>
            <!-- 날짜지정 -->
            <div class="info_date">
              <span class="day_backward" @click.prevent="changeDate(-2)"
                >-2<small
                  ><small><small>일</small></small></small
                ></span
              >
              <span class="day_backward" @click.prevent="changeDate(-1)"
                >-1<small
                  ><small><small>일</small></small></small
                ></span
              >
              <span class="day_today"></span>
              <span class="day_forward" @click.prevent="changeDate(1)"
                ><small><small>+</small></small
                >1<small
                  ><small><small>일</small></small></small
                ></span
              >
              <span class="day_forward" @click.prevent="changeDate(2)"
                ><small><small>+</small></small
                >2<small
                  ><small><small>일</small></small></small
                ></span
              >
            </div>
            <!-- 배송정보요약 -->
            <div class="info_summary">
              <div class="item_dispatchInfo">
                <!-- 배송정보,요약정보 -->
                <div class="item_info01"></div>
                <div class="item_info02"></div>
                <div class="item_info03"></div>
                <div class="item_info04"></div>
                <!-- 줄긋기 -->
                <hr
                  style="
                    border-top: 1px solid #daaeff;
                    width: 100px;
                    position: absolute;
                    right: 150px;
                    top: 20px;
                  "
                />
                <hr
                  style="
                    border-top: 1px solid #daaeff;
                    width: 50px;
                    position: absolute;
                    right: 10px;
                    top: 25px;
                  "
                />
                <hr
                  style="
                    border-top: 1px solid #dfbc9f;
                    width: 130px;
                    position: absolute;
                    right: 160px;
                    top: 55px;
                  "
                />
                <hr
                  style="
                    border-top: 1px solid #f4eafc;
                    width: 150px;
                    position: absolute;
                    right: 70px;
                    top: 115px;
                  "
                />
                <img
                  src="/assets/img/map/random_03.png"
                  style="
                    height: 120px;
                    position: absolute;
                    right: 20px;
                    top: 5px;
                  "
                />
              </div>
            </div>
            <div @click="openPdfView">일지 출력</div>
            <!-- 배송정보상세 -->
            <div class="info_detail">
              <div
                class="item_detail"
                v-for="(targetObj, i) in dispatchSummaryList"
                v-bind:key="i"
              >
                <div class="item_detail_list">
                  <div class="bg_bar"></div>
                  <div class="deliveryCustArea_Bg"></div>
                  <div :class="targetObj.route_seq_class">
                    {{ targetObj.ROUTE_SEQ }}
                  </div>
                  <div :class="targetObj.cust_class">
                    <p>{{ targetObj.CLNAME }}</p>
                  </div>
                  <div :class="targetObj.address_class">
                    <marquee direction="left" scrolldelay="150">
                      {{ targetObj.ITEM_INFO }} / {{ targetObj.ADDRESS }}
                    </marquee>
                  </div>
                  <div :class="targetObj.amount_class">
                    {{ targetObj.ORDER_AMT }}원
                  </div>
                  <div :class="targetObj.invoice_class">거래명세서</div>

                  <template v-if="targetObj.positionYn">
                    <div :class="targetObj.distance_class">
                      {{ targetObj.distanceK }} km
                    </div>
                    <div :class="targetObj.arrive_class">
                      {{ targetObj.inboundDtmString }}
                    </div>
                  </template>
                  <template v-else>
                    <div :class="targetObj.distance_class">0.00 km</div>
                    <div :class="targetObj.arrive_class">위치확인필요</div>
                  </template>

                  <hr :class="targetObj.dashed_line_class" />
                  <div :class="targetObj.triangle_triangle__left_class"></div>
                  <div
                    class="deliveryCustArea"
                    @click="showCustInfo(i, targetObj)"
                    @mouseover="resetDispatchSummaryList(i, targetObj)"
                    @mouseout="resetDispatchSummaryList(-1, targetObj)"
                  ></div>
                </div>
              </div>
            </div>
          </div>
          <!-- 리사이즈 기능은 임시로 숨긴다. 추후개발 -->
          <!-- <div class="resizer" id="dragMe" @mousedown="mouseDownHandler($event)"></div> -->
          <!-- 지도정보/리스트영역 -->
          <div style="width: 100%; border: 0px solid gray">
            <!-- 지도영역 -->
            <div class="area_map" style="position: relative">
              <NaverMap style="height: 100%; width: 100%" />
              <!-- 새로고침 버튼 추가 -->
              <div style="position: absolute; left: 10px; top: 10px">
                <img
                  class="cvo_map_refresh"
                  @click.prevent="nowPositionSearch"
                  src="/assets/img/map/refresh_blue_01.png"
                />
              </div>
              <!-- 경로정보요약 -->
              <div class="route_summ_area">
                <p>
                  <span><small><small>이동거리 : </small></small><span data-animation="number" data-value="129.68">0</span><small><small>km</small></small>&nbsp;&nbsp;/ </span>
                  <span><small><small>소요시간 : </small></small><span data-animation="number" data-value="54">0</span><small><small>분</small></small>&nbsp;&nbsp;/ </span>
                  <span><small><small>최고속도 : </small></small><span data-animation="number" data-value="141">0</span><small><small>km/h</small></small></span>
                </p>
                <div v-if="visitor">
                  <apexchart type="bar" width="100%" height="252" :options="visitor?.chart?.options" :series="visitor?.chart?.series"></apexchart>
                </div>
              </div>
            </div>
            <!-- 그리드영역 -->
            <div class="area_grid" id="productDiv">
              <!-- <panel style="">
                                    <panel-header>
                                        <panel-title>보기</panel-title>
                                        <panel-toolbar />
                                    </panel-header>
                                    <panel-body class="bg-gray-800 text-white">
                                    </panel-body>
                                </panel> -->
              <!-- 그리드 -->
              <AgGridVue
                class="ag-theme-alpine"
                style="height: 100%; width: 100%"
                :columnDefs="columnDefs.value"
                :rowData="rowData"
                :defaultColDef="defaultColDef"
                suppressColumnVirtualisation="true"
                animateRows="true"
                copyHeadersToClipboard="true"
                @cell-clicked="cellWasClicked"
                @cell-focused="cellFocused"
                @grid-ready="onGridReady"
                @cell-value-changed="cellValueChanged"
              >
              </AgGridVue>

              <!-- 더보기 버튼 -->
              <div
                class="custom-footer"
                v-if="isShowMoreBtn"
                style="border: 0px solid #ff0000"
              >
                <div class="item__buttons">
                  <button @click="searchMoteBtn">
                    <i class="fa-solid fa-plus"></i>더보기
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="part__data_detail" v-if="detailShow">
        <div class="item__contents">
          <div>
            <label class="form-label">거래처명</label><br />
            <input type="text" v-model="selectRowData.COMPANY_NAME" />
          </div>
          <div>
            <label class="form-label">사업자번호</label><br />
            <input type="text" v-model="selectRowData.COMPANY_CORPNO" />
          </div>
          <div>
            <label class="form-label">사용자</label><br />
            <input type="text" v-model="selectRowData.USER_NAME" />
          </div>
          <div>
            <label class="form-label">휴대폰번호</label><br />
            <input type="text" v-model="selectRowData.USER_PHONE" />
          </div>
          <div>
            <label class="form-label">이메일</label><br />
            <input type="text" v-model="selectRowData.EMAIL" />
          </div>
          <div>
            <label class="form-label">가입여부</label><br />
            <div class="radio_group">
              <label>
                <input
                  type="radio"
                  id="radio1-1"
                  value="10"
                  v-model="selectRowData.STATUS"
                /><span for="radio1-1">신규가입</span>
              </label>
              <label>
                <input
                  type="radio"
                  id="radio1-2"
                  value="20"
                  v-model="selectRowData.STATUS"
                /><span for="radio1-2">승인</span>
              </label>
              <label>
                <input
                  type="radio"
                  id="radio1-3"
                  value="99"
                  v-model="selectRowData.STATUS"
                /><span for="radio1-3"> 거절</span>
              </label>
            </div>
          </div>
        </div>
        <div class="item__buttons">
          <button class="btn_close" @click="cancleBtn">
            <i class="fa-regular fa-circle-xmark"></i><span>닫기</span>
          </button>
          <button class="btn_save" @click="saveBtn">
            <i class="fa-regular fa-circle-check"></i><span>저장</span>
          </button>
        </div>
      </div>
    </div>
     <!-- pdf 미리보기 임시 태그(모달창 띄우기) -->
    <div id="modalPdfViewerDiv" data-bs-toggle="modal" data-bs-target="#pdfPrintModal"  style="display: none"  data-backdrop="static"  data-keyboard="false"  />
    <div class="modal fade"   data-bs-target="#pdfPrintModal"   id="pdfPrintModal"   style="z-index: 1051 !important"   data-backdrop="static"   data-keyboard="false"  >
      <div class="modal-dialog modal-xl setModalCenter" style="margin: 0px auto"  >
        <PdfLayout :pdfFileName="pdfFileName">
          <SaleLog :pdfPrintData="dispatchSummaryList"></SaleLog>
        </PdfLayout>
      </div>
    </div>
  </div>
</template>


<style lang="scss">
.route_summ_area {
  position: absolute;
  left: 10px;
  bottom: 10px;
  background-color: #3328d09a;
  width: 100%;
  height: 350px;
  text-align: center;
  padding-right: 10px;
}
.route_summ_area p {
  // background-color: #4e44d5;
  border: 1px solid #ffffff;
  color: #ffffff;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 600;
  margin:2.5px 3px;
}
.route_summ_area p span {
  margin:0px 5px;
}
.sample_work {
  background-color: #a0ff52;
  background-color: #a0ff528f;
  background-color: #4952ff;
  background-color: #4952ff9b;
  background-color: #8bff3d;
  background-color: #8bff3d85;
}

// 고정열 배경색
// .lock-pinned {
//     background: #eef0ff;
// }
// 헤더 모서리 라운딩
.ag-header {
  border-radius: 3px;
}
// 테두리
.ag-root-wrapper {
  border: 1;
}
// 높이
.ag-theme-alpine {
  --ag-grid-size: 4px;
  --ag-list-item-height: 5px;
  --ag-header-foreground-color: rgb(255, 255, 255);
  --ag-header-background-color: #3a35be;
  --ag-font-size: 12px;
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