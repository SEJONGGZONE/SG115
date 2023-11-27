<script setup>
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

function handleGetDate(minusDate) {
  var d = new Date();
      d = d.setDate(d.getDate() - minusDate);
  return d;
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
const geolocationError = () => {
  console.log('위치 정보를 찾을 수 없습니다.2')
}
const setAlert = (text) => {
  alert(text)
}


/**
 * 차트에 마우스를 올렸을때..
 */
const mouseChartMove = (event, chartContext, config) =>{
      // console.log(event);
      // console.log(chartContext);
      
      //console.log(routeInfoList[config.dataPointIndex]);

      // var curObj = config.config.series[config.seriesIndex];
      // console.log('idx=' + config.seriesIndex);
      // console.log(curObj);
      // console.log(curObj.data);
      // console.log(curObj.name);

      // console.log('------------------------------------');

      addspeedMarker(targetRouteInfos[config.dataPointIndex]);
}

/**
 * 차트 기본데이타셋(속성등..)
 */
let getSpeedTimeData = (newData) => {
			return {
        chart: {
          methods:{
            
            // mouseMove(e){
            // 	console.log(e)
            // }
            },
          series: [newData],
					options: {
						colors: ['#ffffffc6', '#ffffffc6'],
						fill: {
							opacity: .75,
							type: 'solid'
						},
						legend: { // 
							position: 'top',
							horizontalAlign: 'right',
							offsetY: 20,
							offsetX: 500,
							labels: {
								colors: appVariable.color.white
							}
						},
						xaxis: {
							type: 'datetime',
							tickAmount: 6,
              formatter: function (val) {
                var dtm = new Date(val);
                dtm.setHours(dtm.getHours() - 9); 
                // 마우스올린 위치의 시간값을 찾음.
                var imageInfoString = String(dtm.getHours()).padStart(2, "0") + ':' +
                                String(dtm.getMinutes()).padStart(2, "0");
                return imageInfoString;
              },
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
						tooltip: { 
              x: { formatter: function (val) { 
                      var dtm = new Date(val);
                      dtm.setHours(dtm.getHours() - 9); 
                      // 마우스올린 위치의 시간값을 찾음.
                      var imageInfoString = String(dtm.getHours()).padStart(2, "0") + ':' +
                                      String(dtm.getMinutes()).padStart(2, "0") + ':' +
                                      String(dtm.getSeconds()).padStart(2, "0");
                      var curTraceInfo = null;
                      // 경로데이타에서 동일시간의 자료를 찾아서 '주소'를 얻어온다. 그리고 세팅..
                      targetRouteInfos.forEach((element) => {
                        // 경로시간값
                        let traceDtm = common_utils.getDateFromString(element.TRACEDATE + element.TRACETIME);
                        // 경로시간 스트링
                        var traceimageInfoString = String(traceDtm.getHours()).padStart(2, "0") + ':' +
                                      String(traceDtm.getMinutes()).padStart(2, "0") + ':' +
                                      String(traceDtm.getSeconds()).padStart(2, "0");
                        //console.log('[비교]' + imageInfoString + ' / ' + traceimageInfoString);
                        if (imageInfoString == traceimageInfoString) {
                          //console.log(element.ADDRESS);
                          curTraceInfo = element;
                        }
                      });
                      // 동일시간값의 정보를 찾았다면 추가해준다.
                      if (curTraceInfo != null) {
                        imageInfoString += " | " + curTraceInfo.ADDRESS;
                      }
                      return imageInfoString;
                    } 
              },
              y: { formatter: function (val) { return val + " km/h" } }
            },
						chart: { height: '95%', type: 'area', toolbar: { show: false }, stacked: true },
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
								top: -20,
								right: 3,
								bottom: 0,
								left: 10
							},
						},
						stroke: { 
							show: false,
							curve: 'smooth'
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
const userClcode = userInfo.CLCODE;
const userId = userInfo.ID;
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
onMounted(() => {
  pageNumber.value = 1;

  setTimeout(() => {
    // 지도초기화
    initMap();
    
    // 화면 초기화
    document.getElementsByClassName("route_summ_area")[0].style.display = "none"; // 경로요약정보 영역 감추기
    
    // 현위치조회 요청 - 운영용 - 나중에 적용시 쿠키로 저장하자..
    // curPositionObj.boundDist = boundDist.value;
    // getCurrentPosition();
    
    // 차량최종위치 얻기 - 개발용
    curPositionObj.boundDist = boundDist.value;
    curPositionObj.value.latitude = 36.5980585;
    curPositionObj.value.longitude = 127.3018034;
    requestCvoNowPositionSel();

    // 차트 기본세팅..
    visitor.value = getSpeedTimeData();
  }, 500);
  
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
          detailShow.value = true;
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
 * 속도차트 마커 올리기..
 * 
 * ADDRESS : "세종특별자치시 부강면 부강리 401-111"
BATTERYLEVEL : 86
CHARGEYN : "N"
DEVICENO : "01055013694"
DIRECTION : "14"
DISPATCH_KEY : "DIS20230918082843005"
DURATION_INFO : "2<small><small>시간</small></small>20<small><small>분</small></small>"
EVENTCODE : "05"
GEONUM : 3807
GPSYN : "Y"
INTERVALDISTANCE : "0"
LATITUDE : "36.5314873"
LONGITUDE : "127.3652222"
MARKER_CAPTION : "세종특별자치시 부강면 부강리 401-111"
MARKER_SUBCAPTION : "09/18 09:17"
MARKER_SUBTITLE : "819고7667"
MAX_DT : "20230918104932"
MAX_SPEED : 137
MIN_DT : "20230918082856"
RANK : 90
REMARK : ""
ROUTE_TITLE : "0<small>km/h</small>"
SPEED : 0
STATUS_NM : "정차중"
TIMESTAMP : null
TRACEDATE : "20230918"
TRACETIME : "091738"
VEHICLECD : "819-7667"
VEHICLENO : "819고7667"
 * @param targetObj 
 */
const addspeedMarker = (targetObj) => {
  
  //console.log(targetObj);

  // 기존 마커 지우기
  onRemoveSpeedMarker();
  if (targetObj == null) return;
  if ((targetObj.LATITUDE <= 0) || (targetObj.LONGITUDE <= 0)) {
    return;
  }

  // 위치세팅
  let latLng = new window.naver.maps.LatLng(
    targetObj.LATITUDE,
    targetObj.LONGITUDE
  );
  // 상태체크
  var on_off_string, status_text, speed_css, bottom_arrow_css;
  if (targetObj.SPEED > 0) { // 가동중 -----------
    on_off_string = "_on";
    status_text = "운행중";
    // 속도별 색상표현..
    if (targetObj.SPEED <=30) { 
      speed_css = "marker_speed_speed_30";
      bottom_arrow_css = "marker_time_bottom_arrow_speed_30";
    } else if (targetObj.SPEED <90) { 
      speed_css = "marker_speed_speed_60";
      bottom_arrow_css = "marker_time_bottom_arrow_speed_60";
    } else if (targetObj.SPEED >=90) { 
      speed_css = "marker_speed_speed_90";
      bottom_arrow_css = "marker_time_bottom_arrow_speed_90";
    }
  } else { // 비가동중 -----------
    on_off_string = "_off";
    status_text = "정차중";
    speed_css = "marker_speed_speed_30";
    bottom_arrow_css = "marker_time_bottom_arrow_speed_30";
  }
  var timeString = targetObj.TRACETIME
  timeString = timeString.substring(0,2) + ':' + timeString.substring(2,4);

  // 마커추가..
  var speedMarker = new window.naver.maps.Marker({
      position: latLng,
      map: mapInstance.value,
      targetObj: targetObj, // 차량객체를 전달한다..
      title: targetObj.ADDRESS,
      zIndex: 100,
      icon: {
        content: [
          '<div class="marker_speed' + on_off_string + ' ' + speed_css + '">',
          '<span style="font-size:15px;">' +
            targetObj.SPEED + ' <span style="font-size:7px;">km/h</span>',
            "</span>",
          "</div>",
          '<div class="marker_time_bottom_arrow' + on_off_string + ' ' + bottom_arrow_css + '"></div>',
          '<div class="marker_time_bottom_point' + ' ' + speed_css + '"></div>',
          '<div class="marker_time_bottom_info">',
          //'<span style="font-size:15px;">' + targetObj.ADDRESS + "</span><BR/>",
          '<span style="font-size:15px;">' + timeString + "</span>",
          "</div>",
        ].join(""),
        size: new window.naver.maps.Size(38, 58),
        anchor: new window.naver.maps.Point(35, 45),
      },
  });
  speedMarkerList.push(speedMarker);
  // 향후 다른 시점의 속도와 비교를 위해서 배열로 담아서 저장한다..향후을 위하야~
  speedMarkerIndex++;
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
const drawTruckRouteSelectLine = (startDtm, endDtm, startCust, endCust) => {
  
  targetRouteInfos = [];
  targetRouteLines = [];

  // console.log("startDtm=" + startDtm);
  // console.log("endDtm=" + endDtm);

  var nLastOnMore = 0, distanceSum = 0, maxSpeed = 0;
  routeInfoList.forEach((element) => {
    //console.log(element.TRACEDATE + element.TRACETIME);
    var curDtm = element.TRACEDATE + element.TRACETIME;

    if (startDtm <= curDtm && endDtm >= curDtm) {
      // 경로위치정보 객체 저장..(계산용)
      targetRouteInfos.push(element);
      // 경로위치정보 객체 저장..(그리기용)
      let latLng = new window.naver.maps.LatLng(
        element.LATITUDE,
        element.LONGITUDE
      );
      // 거리저장
      distanceSum += (element.INTERVALDISTANCE*1);
      // 최고속도확인
      if (element.SPEED > maxSpeed) maxSpeed = element.SPEED;
      // 위치저장
      targetRouteLines.push(latLng);

      nLastOnMore = 1;
    } else {
      // 시간범위안에서 찾았다고해도, 추가로 더 넣어주기 위함...
      if (nLastOnMore > 0) {
        // 경로위치정보 객체 저장..(그리기용)
        let latLng = new window.naver.maps.LatLng(
          element.LATITUDE,
          element.LONGITUDE
        );
        // 거리저장
        distanceSum += (element.INTERVALDISTANCE*1);
        // 최고속도확인
        if (element.SPEED > maxSpeed) maxSpeed = element.SPEED;
        // 위치저장
        targetRouteLines.push(latLng);

        nLastOnMore--;
      }
    }
  });

  // 선택노선 이동경로 그리기
  if (targetRouteLines.length > 0) {
    onRemoveTruckRoute();

    drawTruckRouteLine(targetRouteLines, true);

    // 경과시간 구하기..
    var lastIndex = targetRouteInfos.length-1;
    var sDtm = targetRouteInfos[0].TRACEDATE + targetRouteInfos[0].TRACETIME;
    var eDtm = targetRouteInfos[lastIndex].TRACEDATE + targetRouteInfos[lastIndex].TRACETIME;
    var simageInfoString = sDtm.replace(/^(\d{4})(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)$/, '$1-$2-$3 $4:$5:$6');
    var eimageInfoString = eDtm.replace(/^(\d{4})(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)$/, '$1-$2-$3 $4:$5:$6');
    var sDate = new Date(eimageInfoString);
    var eDate = new Date(simageInfoString);
    const diffMSec = sDate.getTime() - eDate.getTime();
    const diffDate = diffMSec / (24 * 60 * 60 * 1000);
    const diffHour = diffMSec / (60 * 60 * 1000);
    const diffMin = diffMSec / (60 * 1000);
    // ------------------------------------------------------------------------------------
    // 경로 요악정보 세팅
    // ------------------------------------------------------------------------------------
    setRouteInfo(
      common_utils.distanceStringK(distanceSum), Math.round(diffMin), maxSpeed,
      startCust, endCust
    );

    // ------------------------------------------------------------------------------------
    // 차트 데이타 세팅 (시간, 속도)
    // ------------------------------------------------------------------------------------
    var chartDatas = [];
    targetRouteInfos.forEach((element) => {
      let traceDtm = common_utils.getDateFromString(element.TRACEDATE + element.TRACETIME);
      // GMT+9시간을 더해준다.
      traceDtm.setHours(traceDtm.getHours() + 9); 

      // console.log('[시간값]=' + element.TRACEDATE + element.TRACETIME);
      // console.log(traceDtm);
      chartDatas.push([traceDtm, element.SPEED]);
    });
    speedChartObject = { 
      name : targetRouteInfos[0].VEHICLENO,
      data : chartDatas
    };
    // console.log(speedChartObject);
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
 * 경로요약정보 설정..
 */
const setRouteInfo = (d1, d2, d3, startCust, endCust) => {
  // 배송정보-보이기
  document.getElementsByClassName("route_summ_area")[0].style.display = "block";

  // 속도에 따른 배경색 변경 표시..
  var speedForColor = "#9e9997d7", titleShadow = "9e9997";
  if (d3 < 30) { 
    speedForColor = "#9e9997d7"; // 회색
    titleShadow = "#000000";
  } else if (d3 < 60) { 
    speedForColor = "#8a75ffc9"; // 연한파란
    titleShadow = "#000000";
  } else if (d3 < 90) {
    speedForColor = "#364bee9a"; // 파란
    titleShadow = "#0000FF";
  } else if (d3 < 200) {
    speedForColor = "#ee7336bb"; // 빨간
    titleShadow = "#FF0000";
  }
  document.getElementsByClassName("route_summ_area")[0].style.backgroundColor = speedForColor; // 배경색
  document.getElementsByClassName("route_summ_area_chart")[0].style.backgroundColor = speedForColor; // 배경색
  

  // 데이타 세팅..
  var htmlString = "";
  htmlString = "" +
  "<div class=\"route_summ_area_title\">" +
    startCust + "&nbsp;&nbsp;<small><small><small>▶︎</small></small></small>▶︎<small><small><small>▶︎</small></small></small>&nbsp;&nbsp;" + endCust +
  "</div>" +
  "<p>" +
  "<span><small><small>이동거리 : </small></small><span data-animation=\"number\" data-value=\"" + d1 + "\">0</span><small><small>km</small></small>&nbsp;&nbsp;/ </span>" +
  "<span><small><small>소요시간 : </small></small><span data-animation=\"number\" data-value=\"" + d2 + "\">0</span><small><small>분</small></small>&nbsp;&nbsp;/ </span>" +
  "<span><small><small>최고속도 : </small></small><span data-animation=\"number\" data-value=\"" + d3 + "\">0</span><small><small>km/h</small></small></span>" +
  "</p>"
  ;
  document.getElementsByClassName("route_summ_area")[0].innerHTML = htmlString;
  
  // 타이틀 그림자색 세팅...
  document.getElementsByClassName("route_summ_area_title")[0].style.textShadow = "1px 1px 15px " + titleShadow;

  // popover
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new Popover(popoverTriggerEl)
  });
  
  animateNumber();
};

// 속도차트 데이터 객체
var speedChartObject = {};
function speedChartSet() {
  visitor.value = getSpeedTimeData(speedChartObject);
  // console.log(visitor.value.chart.series[0]);
}

/**
 * 차트 보이기/숨기기
 * @param {}} showYn 
 */
let showYn = false;
function showChart() {
  if (showYn) {
    showYn = false;
    document.getElementsByClassName("route_summ_area")[0].style.bottom = "20px";
    document.getElementsByClassName("route_summ_area_chart")[0].style.display = "none";
  } else {
    showYn = true;
    document.getElementsByClassName("route_summ_area")[0].style.bottom = "185px";
    document.getElementsByClassName("route_summ_area_chart")[0].style.display = "block";
  }
  
}

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
 * 화면크기변경에 따른, 배송상세내역(좌측하단) 영역 리사이즈..
 */
const resizeScreenSet = () => {
  
  setTimeout(() => {
    var grid_clientlist = document.getElementsByClassName("grid_clientlist")[0];
    var grid_searcharea = document.getElementsByClassName("grid_searcharea")[0];
    var s1 = grid_clientlist.getBoundingClientRect().top;
    var s2 = grid_searcharea.getBoundingClientRect().bottom;
    
    // 현재 창의 높이에서 바로위영역의 하단값을 빼고, 여백을 더빼준다.
    var calcHeight = document.body.clientHeight - s2 - 10;
    
    
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
  // 배송정보-감추기
  document.getElementsByClassName("route_summ_area")[0].style.display = "none"; // 경로요약정보 영역 감추기

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

/**
 * ERP - 거래처 조회..
 */
var curCircle;
const requestErpClientSel = async () => {
  //조회

  // 더보기버튼 처리
  isShowMoreBtn.value = false;
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
  
  // 그리드 데이타 지우기
  rowData.splice(0);
  // 지도, 거래처마커 지우기
  onRemoveCust();
  // 지도, 반영원 지우기
  if (curCircle != null) curCircle.setMap(null);
  // 지도, 반영원 그리기
  curCircle = new window.naver.maps.Circle({
    map: mapInstance.value,
    center: new window.naver.maps.LatLng(curPositionObj.value.latitude, curPositionObj.value.longitude),
    radius: curPositionObj.boundDist*1000,    
    fillColor: '#4952ff',
    fillOpacity: 0.1,
    strokeWeight : 1,
    strokeColor: '#4952ff9b',
  });
  
  // API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosERP().post(`/ERP_CLIENT_SEL `, reqData)
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
        });
        
        // 지도영역 조정(마커를 다보이게..)
        mapInstance.value.fitBounds(arrCustBounds);
        // 현재맵 기준위치
        //searchPosition = mapInstance.value.getCenter();
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
    var productDiv = document.getElementsByClassName("ag-body-viewport")[0];
    setTimeout(() => {
      productDiv.scrollTop = 0;
    }, 100);
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
    result = await common_utils.getAxiosERP().post(`/CVO_623_CLIENTIMAGE_SEL `, reqData)
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
  // detailShow.value = true;
  // selectRowData.value = event.data;
  //console.log("cell was clicked", event);
  
};
/**
 * 그리드셀-클릭시..
 * @param {} event 
 */
const cellWasClicked = (event) => {
  var targetObj = event.data;
  // 상세데이타 세팅
  selectRowData.value = targetObj;
  // 상세영역 열기
  detailShow.value = true;
  // 거래처 이미지 조회
  requestErpClientImage("", targetObj.CLCODE);
  // 거래처위치로 지도이동/줌 
  moveClientPosition(targetObj);
  
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
const searchMoteBtn = () => {
  //더보기 Btn
  pageNumber.value = pageNumber.value + 1;
  requestErpClientSel();
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
      <!-- 1.그리드, 거래처리스트 -->
      <div class="grid_clientlist">
        <!-- <panel style="height:100%; width:100%; background-color: #FFFFFF;">
            <panel-header>
                <panel-title>보기</panel-title>
                <panel-toolbar />
            </panel-header>
            <panel-body class="bg-gray-800 text-white" style="height:100%; padding:0px;">
              
            </panel-body>
        </panel> -->
        <!-- 타이틀 -->
        <div class="info_title" style="text-align: center;"></div>
        <!-- 날짜지정 -->
        <div style="display: flex; flex-direction: column; border-bottom: 1px solid #0b048678;">
          <div class="info_date" style="display:flex; padding-left: 0.2rem;">
            <span class="day_backward" style="display:flex;" @click.prevent="changeDate(-2)">-2<small><small><small>일</small></small></small></span>
            <span class="day_backward" style="display:flex;" @click.prevent="changeDate(-1)">-1<small><small><small>일</small></small></small></span>
            <span class="day_today" style="text-align: center; margin-top:0.5rem;"></span>
            <span class="day_forward" style="display:flex;" @click.prevent="changeDate(1)"><small><small>+</small></small>1<small><small><small>일</small></small></small></span>
            <span class="day_forward" style="display:flex;" @click.prevent="changeDate(2)"><small><small>+</small></small>2<small><small><small>일</small></small></small></span>
          </div>
        </div>
        <!-- 배송사원목록 -->
        <div class="info_emplist" style="display:flex; border-bottom: 1px solid #0b048678;">
            <div @mouseover="startMq_L()" @mouseout="stopMq()" class="mq_arrow" style="align-items: center; display: flex; margin-right: 0.1rem;">◀︎</div>
            <div id="empListDiv">
              <div
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
          <div>
            <select
              v-model="boundDist"
              @change="optionBoundChange"
            >
              <option value="1">반경 1km</option>
              <option value="3">반경 3km</option>
              <option value="5">반경 5km</option>
              <option value="10">반경 10km</option>
              <option value="30">반경 30km</option>
            </select>
          </div>
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
          style="height:auto; width: auto; margin-top:0.1rem; background-color: #0b048653; padding:0.05rem;"
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
              <div style="position: absolute; left: 10px; top: 10px">
                <img
                  class="cvo_map_refresh"
                  @click.prevent="requestCvoNowPositionSel"
                  src="/assets/img/map/refresh_blue_01.png"
                />
              </div>
              <!-- 경로정보요약 -->
              <div class="route_summ_area" @click="showChart()">
              </div>
              <!-- 속도차트 -->
              <div class="route_summ_area_chart" v-if="visitor">
                  <apexchart id="chart123" type="bar" width="100%" height="100px" :options="visitor?.chart?.options" :series="visitor?.chart?.series" @mouseMove="mouseChartMove"></apexchart>
              </div>
            </div>

            <!-- 그리드영역 -->
            <div class="area_grid" id="productDiv">
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
      <!--3.그리드, 거래처상세영역 -->
      <div class="grid_data_detail" v-if="detailShow" style="height:auto; margin-bottom:0.5rem; background-image: url('/assets/img/map/bg_tile_logo_200_001_fade_10.png');">
        <div class="client_detail_area">
          <!-- 거래처 상세정보 -->
          <div class="item__contents">
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">거래처명</label>
              <input type="text" v-model="selectRowData.CLNAME" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">사업자번호</label>
              <input type="text" v-model="selectRowData.CLSAUPNO" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">전화번호</label>
              <input type="text" v-model="selectRowData.CLTEL" />
            </div>
            <div style="display:flex; flex-direction: column;">
              <label class="form-label">주소1</label>
              <input type="text" v-model="selectRowData.CLJUSO1" />
            </div>
            <div style="display:flex; flex-direction: column;">
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
            </div>
          </div>
          <!-- 분리선 -->
          <hr style="margin: 0.2rem;"/>
          <!-- 거래처이미지 리스트 -->
          <div class="client_image_area">
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
        <div class="item__buttons">
          <button class="btn_close" @click="cancleBtn">
            <i class="fa-regular fa-circle-xmark"></i><span>닫기</span>
          </button>
          <!-- <button class="btn_save" @click="saveBtn">
            <i class="fa-regular fa-circle-check"></i><span>저장</span>
          </button> -->
        </div>
      </div>


    </div>
     <!-- 거래처납품 이미지 보기(모달창 띄우기) -->
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