<script setup>

// 속도차트관련...
import { useAppVariableStore } from '@/stores/app-variable';
import { animateNumber } from '@/components/app/AnimateNumber.vue';
import { Popover } from 'bootstrap';
import apexchart from '@/components/plugins/Apexcharts.vue';
import jsVectorMap from 'jsvectormap';
import 'jsvectormap/dist/maps/world.js';
import 'jsvectormap/dist/css/jsvectormap.min.css';
import 'simple-line-icons/css/simple-line-icons.css';
const appVariable = useAppVariableStore();


// 이하 공통..
import { onMounted, reactive, ref } from "vue";
import { mapInstance } from "@/naverMap/stores";
import { NaverMap } from "@/naverMap";

import { operateApi } from "@/api";
import { startLoadingBar, removeLoadingBar, doSort } from "@/common/utils.ts";

import { useAlert } from "@/composables/showAlert";
const { showAlert, showAlertSuccess } = useAlert();
import * as common_utils from "@/common/utils.ts";

let selectRowData = ref([]);

const keyword = ref("");
const pageNumber = ref(1);
const pageSize = ref(10);
const table = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    { label: "NO", field: "RANK", width: "10%" },
    { label: "차량번호", field: "VEHICLENO", width: "22%" },
    { label: "기사명", field: "NAME", width: "20%" },
    { label: "최종일시", field: "TIMESTAMP", width: "20%" },
    //,{ label:"현위치",field:"ADDRESS",width:"10%" }
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "RANK",
    sort: "asc",
  },
  isShowMoreBtn: true,
});

const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userId = userInfo.ID;


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
  // 시간 마커지우기..
  onRemovetime();
  // 시간 마커 스타일초기화
  document.documentElement.style.setProperty('--time-marker-bg-30', '#a3a3a37f');
  document.documentElement.style.setProperty('--time-marker-bg-60', '#382efe7f');
  document.documentElement.style.setProperty('--time-marker-bg-90', '#fe2e517f');
  // 경로요약정보 영역 감추기
  document.getElementsByClassName("route_summ_area")[0].style.display = "none";
};


const mouseChartMove = (event, chartContext, config) =>{
      // console.log(event);
      // console.log(chartContext);
      
      //console.log(routeInfoList[config.dataPointIndex]);

      // var curObj = config.config.series[config.seriesIndex];
      // console.log('idx=' + config.seriesIndex);
      // console.log(curObj);
      // console.log(curObj.data);
      // console.log(curObj.name);

      //console.log('------------------------------------');

      addSpeedChartMarker(routeInfoList[config.dataPointIndex]);
}
let getSpeedTimeData = (newData) => {
			return {
        chart: {
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
                var dtmString = String(dtm.getHours()).padStart(2, "0") + ':' +
                                String(dtm.getMinutes()).padStart(2, "0");
                return dtmString;
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
                      var dtmString = String(dtm.getHours()).padStart(2, "0") + ':' +
                                      String(dtm.getMinutes()).padStart(2, "0") + ':' +
                                      String(dtm.getSeconds()).padStart(2, "0");
                      var curTraceInfo = null;
                      // 경로데이타에서 동일시간의 자료를 찾아서 '주소'를 얻어온다. 그리고 세팅..
                      routeInfoList.forEach((element) => {
                        // 경로시간값
                        let traceDtm = common_utils.getDateFromString(element.TRACEDATE + element.TRACETIME);
                        // 경로시간 스트링
                        var traceDtmString = String(traceDtm.getHours()).padStart(2, "0") + ':' +
                                      String(traceDtm.getMinutes()).padStart(2, "0") + ':' +
                                      String(traceDtm.getSeconds()).padStart(2, "0");
                        //console.log('[비교]' + dtmString + ' / ' + traceDtmString);
                        if (dtmString == traceDtmString) {
                          //console.log(element.ADDRESS);
                          curTraceInfo = element;
                        }
                      });
                      // 동일시간값의 정보를 찾았다면 추가해준다.
                      if (curTraceInfo != null) {
                        dtmString += " | " + curTraceInfo.ADDRESS;
                      }
                      return dtmString;
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

//////////////api 통신
onMounted(() => {
  searchBtn();
  setTimeout(() => {
    const setRowData = (lat, lot) => {
      selectRowData.value.LATITUDE = lat;
      selectRowData.value.LONGITUDE = lot;

      onRemoveTruck();
      const title = selectRowData.value.VEHICLENO;
      const latitude = selectRowData.value.LATITUDE;
      const longitude = selectRowData.value.LONGITUDE;
      addTruckMarker(latitude, longitude, title);
    };
    // naver.maps.Event.addListener(mapInstance.value, 'click', (e)=>{
    //   setRowData(e.coord.lat(), e.coord.lng())
    // });

    mapInstance.value.setOptions("mapTypeControl", true); //지도 유형 컨트롤의 표시 여부
    CENTER_POSITION = new naver.maps.LatLng([127.2408339, 36.587024]);

    // 차트기본세팅
    visitor.value = getSpeedTimeData();
  }, 500);
});

// 센터위치 - 목원푸드
var CENTER_POSITION;

/**
 * 경로조회
 */
 const doRouteSearch = async (deviceNo, startDtm, endDtm) => {
  //console.log('경로조회 요청- 진입-1');
  
  // 기존경로 지우기
  onRemoveTruckRoute();
  //console.log('경로조회 요청- 진입-2');

  // 대기창표시
  startLoadingBar();

  //console.log('경로조회 요청- 진입-3');
  // 파라미터 매핑
  const param = {
    companyCd: "00002",
    deviceNo: deviceNo,
    traceStartDtm: startDtm,
    traceEndDtm: endDtm,
  };

  //console.log('경로조회 요청- 진입-4');
  // 데이타 요청.
  let data, result;
  try {
    //console.log('경로조회 요청- 진입-5');
    result = await operateApi.cvoTraceSel(param);
    //console.log('경로조회 요청- 진입-6');
    data = result.data;
    //console.log('경로조회 요청- 진입-7');
    // 데이타 세팅...
    if (data.RecordCount > 0) {
    }
  } catch (error) {
    console.error(error);
  } finally {
    //console.log('경로조회 요청- 진입-8');
    // 로딩바숨기기..
    removeLoadingBar();
    // 경로데이타 초기화
    routeInfoList = []; // 경로데이타(부가정보포함)
    arrRouteBounds = []; // 위치정보

    // ----------------------------
    // 타이머..실행
    // ----------------------------
    setTimeout(() => {
      //console.log('경로조회 요청- 진입-10');
      // 데이타 - LOOP
      var infos = data.RecordSet;
      var distanceSum = 0, maxSpeed = 0;
      infos.forEach(function (item) {
        routeInfoList.push(item);
        // 경로위치정보 객체 저장..(그리기용)
        let latLng = new window.naver.maps.LatLng(
          item.LATITUDE,
          item.LONGITUDE
        );
        // 거리저장
        distanceSum += (item.INTERVALDISTANCE*1);
        // 최고속도확인
        if (item.SPEED > maxSpeed) maxSpeed = item.SPEED;
        // 위치저장
        arrRouteBounds.push(latLng);

        addSpeedTimeMarker(item);
      });

      // 전체 경로 그리기
      drawTruckRouteLine(arrRouteBounds, true);

      // 경로정보기준 - 지도영역 조정(마커를 다보이게..)
      mapInstance.value.fitBounds(arrRouteBounds);
      
      if (routeInfoList.length>0) { // 경로데이타가 있을때..
        // 경과시간 구하기..
        var lastIndex = routeInfoList.length-1;
        var sDtm = routeInfoList[0].TRACEDATE + routeInfoList[0].TRACETIME;
        var eDtm = routeInfoList[lastIndex].TRACEDATE + routeInfoList[lastIndex].TRACETIME;
        var sDtmString = sDtm.replace(/^(\d{4})(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)$/, '$1-$2-$3 $4:$5:$6');
        var eDtmString = eDtm.replace(/^(\d{4})(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)$/, '$1-$2-$3 $4:$5:$6');
        var sDate = new Date(eDtmString);
        var eDate = new Date(sDtmString);
        const diffMSec = sDate.getTime() - eDate.getTime();
        const diffDate = diffMSec / (24 * 60 * 60 * 1000);
        const diffHour = diffMSec / (60 * 60 * 1000);
        const diffMin = diffMSec / (60 * 1000);
        
        //console.log('distanceSum='+distanceSum);

        // 경로 요약정보 세팅
        setRouteInfo (
          common_utils.distanceStringK(distanceSum), Math.round(diffMin), maxSpeed,
        )

        // ------------------------------------------------------------------------------------
        // 차트 데이타 세팅 (시간, 속도)
        // ------------------------------------------------------------------------------------
        var chartDatas = [];
        routeInfoList.forEach((element) => {
          let traceDtm = common_utils.getDateFromString(element.TRACEDATE + element.TRACETIME);
          // GMT+9시간을 더해준다.
          traceDtm.setHours(traceDtm.getHours() + 9); 

          // console.log('[시간값]=' + element.TRACEDATE + element.TRACETIME);
          // console.log(traceDtm);
          chartDatas.push([traceDtm, element.SPEED]);
        });
        speedChartObject = { 
          name : routeInfoList[0].VEHICLENO,
          data : chartDatas
        };

        // 속도차트데이타 리프레쉬
        speedChartSet();
      }

      // 로딩바숨기기..
      removeLoadingBar();

    }, 100);

    // 대기창표시 - 경로가 많을경우 기다린다..다시표시해준다..
    startLoadingBar();
    //console.log('경로조회 요청- 진입-9');
  }
};

//


// 속도차트 데이터 객체
var speedChartObject = {};
function speedChartSet() {
  //console.log('speedChartSetspeedChartSetspeedChartSetspeedChartSetspeedChartSetspeedChartSetspeedChartSetspeedChartSet');
  visitor.value = getSpeedTimeData(speedChartObject);
   console.log(visitor.value.chart.series[0]);
}

/**
 * 차트 보이기/숨기기
 * @param {}} showYn 
 */
let showYn = false;
function showSpeedChart() {
  console.log('차트 보이기/숨기기');
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
 * 현재위치 조회..
 */
const nowPositionSearch = async () => {
  table.isShowMoreBtn = false; // 더보기 버튼 숨기기
  if (table.isLoading) return;
  table.isLoading = true; // 대기창표시하기
  startLoadingBar();

  // 지도 마커 지우기
  onRemoveTruck();
  // 기존경로 지우기
  onRemoveTruckRoute();

  // 파라미터 구성
  const param = {
    companyCd: "00002",
    deviceNo: "",
    vehicleNo: "",
    keyword: keyword.value,
    lat: "",
    lon: "",
    inputUser: userId,
  };

  let data, result;
  try {
    result = await operateApi.cvoNowPositionSel(param);
    data = result.data;

    table.isLoading = false;
    if (data.RecordCount > 0) {
      table.rows.push(...data.RecordSet);

      if (data.RecordCount === pageSize.value) {
        table.isShowMoreBtn = true;
      }
    } else {
      table.isShowMoreBtn = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    table.isLoading = false;
    var areaDataList = document.getElementById("areaDataList");
    setTimeout(() => {
      areaDataList.scrollTop = areaDataList.scrollHeight;

      // 스크롤바 체크
      if (areaDataList.scrollHeight > areaDataList.clientHeight) {
        document.getElementById("btnToTop").style.display = "block";
      }

      // 데이타확인 - LOOP
      var infos = data.RecordSet;
      if (infos.length > 0) {
        addTruckBounds.clear;

        infos.forEach(function (item) {
          console.log(item.NAME);
          addTruckMarker(item);
        });
        
        // 차량이 1대이상일경우 영역을 맞추고, 그외 이동시킨다.
        if (addTruckBounds.length>1) {
          mapInstance.value.fitBounds(addTruckBounds);
        } else {
          const latitude = addTruckBounds[0].LATITUDE;
          const longitude = addTruckBounds[0].LONGITUDE;
          var latLng = new window.naver.maps.LatLng(latitude, longitude);
          mapInstance.value.setCenter(latLng);
        }
      }
    }, 100);
  }
};

/**
 * 검색 Btn
 */
const searchBtn = () => {
  pageNumber.value = 1;
  table.rows = [];
  nowPositionSearch();
};

/**
 * 더보기 Btn
 */
const searchMoreBtn = () => {
  pageNumber.value = pageNumber.value + 1;
  nowPositionSearch();
};


/**
 * 최상위 스크롤..
 */
const scrollToTop = () => {
  //위로 Btn
  document.getElementById("areaDataList").scrollTop = 0;
};

/**
 * 리스트 클릭시..
 * @param rowData 
 */
const handleRowClick = (rowData) => {
  selectRowData.value = JSON.parse(JSON.stringify(rowData));

  const title = selectRowData.value.VEHICLENO;
  const latitude = selectRowData.value.LATITUDE;
  const longitude = selectRowData.value.LONGITUDE;
  // 마커추가
  addTruckMarker(selectRowData.value); // latitude,longitude,title)

  // 리스트차량의 위치로 이동
  var latLng = new window.naver.maps.LatLng(latitude, longitude);
  mapInstance.value.setCenter(latLng);

  // 최근 차량의 이동경로 조회
  var lastDtm = selectRowData.value.TRACEDATE + selectRowData.value.TRACETIME;
  recentRouteTruck(selectRowData.value.DEVICENO, lastDtm);
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
    targetObj.LATITUDE,
    targetObj.LONGITUDE
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
  truckMarkerList.push(truckMarker);
  
  // 차량 마커클릭시..
  naver.maps.Event.addListener(truckMarker, "click", function () {
    // 차량객체얻기..
    var selectTruckObj = truckMarker.targetObj;
    // 최근 차량의 이동경로 조회
    var lastDtm = selectTruckObj.TRACEDATE + selectTruckObj.TRACETIME;
    recentRouteTruck(selectTruckObj.DEVICENO, lastDtm);
  });

  truckMarkerIndex++;
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
 const addSpeedChartMarker = (targetObj) => {
  
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
          // '<div class="marker_speed' + on_off_string + ' ' + speed_css + '">',
          // '<span style="font-size:15px;">' +
          //   targetObj.SPEED + ' <span style="font-size:7px;">km/h</span>',
          //   "</span>",
          // "</div>",
          // '<div class="marker_time_bottom_arrow' + on_off_string + ' ' + bottom_arrow_css + '"></div>',
          '<div class="marker_time_bottom_point_big' + ' ' + speed_css + '"></div>',
          // '<div class="marker_time_bottom_info">',
          // //'<span style="font-size:15px;">' + targetObj.ADDRESS + "</span><BR/>",
          // '<span style="font-size:15px;">' + timeString + "</span>",
          // "</div>",
        ].join(""),
        size: new window.naver.maps.Size(25, 25),
        anchor: new window.naver.maps.Point(37.5, 0),
      },
  });
  speedMarkerList.push(speedMarker);
  // 향후 다른 시점의 속도와 비교를 위해서 배열로 담아서 저장한다..향후을 위하야~
  speedMarkerIndex++;
};

// -------------------------------------------------------
// 시간 마커관련..
// -------------------------------------------------------
var addSpeedTimeBounds = [];
var timeMarkerList = [], timeMarkerIndex = 0;
const onRemovetime = () => {
  //마커 및 타이틀 객체 지우기
  timeMarkerList.forEach((el) => {
    el.setMap(null);
  });
  timeMarkerList = [];
};
/**
 * 속도/시간 마커 올리기..
 * @param targetObj 
 */
const addSpeedTimeMarker = (targetObj) => {
  let latLng = new window.naver.maps.LatLng(
    targetObj.LATITUDE,
    targetObj.LONGITUDE
  );
  addSpeedTimeBounds.push(latLng);

  var on_off_string, status_text, speed_css, bottom_arrow_css;
  if (targetObj.SPEED > 0) {
    on_off_string = "_on";
    status_text = "운행중";
    
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

  } else {
    on_off_string = "_off";
    status_text = "정차중";
    speed_css = "marker_speed_speed_30";
    bottom_arrow_css = "marker_time_bottom_arrow_speed_30";
  }
  //console.log('marker_time' + on_off_string);

  var timeString = targetObj.TRACETIME
  timeString = timeString.substring(0,2) + ':' + timeString.substring(2,4);

  var timeMarker = new window.naver.maps.Marker({
      position: latLng,
      map: mapInstance.value,
      targetObj: targetObj, // 차량객체를 전달한다..
      title: targetObj.NAME,
      zIndex: 10,
      icon: {
        content: [
          '<div class="marker_speed' + on_off_string + ' ' + speed_css + '">',
          '<span style="font-size:15px;">' +
            targetObj.SPEED + ' <span style="font-size:7px;">km/h</span>',
            "</span>",
          "</div>",
          '<div class="marker_time_bottom_arrow' + on_off_string + ' ' + bottom_arrow_css + '"></div>',
          '<div class="marker_time_bottom_info">',
          //'<span style="font-size:15px;">' + targetObj.ADDRESS + "</span><BR/>",
          '<span style="font-size:15px;">' + timeString + "</span>",
          "</div>",
        ].join(""),
        size: new window.naver.maps.Size(38, 58),
        anchor: new window.naver.maps.Point(35, 45),
      },
  });
  timeMarkerList.push(timeMarker);
  
  // 마커클릭시..
  naver.maps.Event.addListener(timeMarker, "click", function () {
    // 차량 객체얻기..
    var selectTimeObj = timeMarker.targetObj;
    
    if (selectTimeObj.SPEED <=30) { 
      document.documentElement.style.setProperty('--time-marker-bg-30', '#a3a3a37f');
      document.documentElement.style.setProperty('--time-marker-bg-60', '#382efe00');
      document.documentElement.style.setProperty('--time-marker-bg-90', '#fe2e5100');
    } else if (selectTimeObj.SPEED <90) { 
      document.documentElement.style.setProperty('--time-marker-bg-30', '#a3a3a300');
      document.documentElement.style.setProperty('--time-marker-bg-60', '#382efe7f');
      document.documentElement.style.setProperty('--time-marker-bg-90', '#fe2e5100');
    } else if (selectTimeObj.SPEED >=90) { 
      document.documentElement.style.setProperty('--time-marker-bg-30', '#a3a3a300');
      document.documentElement.style.setProperty('--time-marker-bg-60', '#382efe00');
      document.documentElement.style.setProperty('--time-marker-bg-90', '#fe2e517f');
    }

  });

  timeMarkerIndex++;
};

/**
 * 시간정보 보이기/감추기
 */
let showTimeYn = false;
function showTimeInfo() {
  if (showTimeYn) {
    document.documentElement.style.setProperty('--marker-time-display', 'visible');
    document.getElementsByClassName("recent_route_time_onoff")[0].innerHTML = "시간표시 ON";
    document.getElementsByClassName("recent_route_time_onoff")[0].style.backgroundColor = "#382efe7f";
    showTimeYn = false;
  } else {
    document.documentElement.style.setProperty('--marker-time-display', 'hidden');
    document.getElementsByClassName("recent_route_time_onoff")[0].innerHTML = "시간표시 OFF";
    document.getElementsByClassName("recent_route_time_onoff")[0].style.backgroundColor = "#7a797971";
    showTimeYn = true;
  }
}
/**
 * 속도정보 보이기/감추기
 */
 let showSpeedYn = false;
function showSpeedInfo() {
  if (showSpeedYn) {
    document.documentElement.style.setProperty('--marker-speed-display', 'visible');
    document.getElementsByClassName("recent_route_speed_onoff")[0].innerHTML = "속도표시 ON";
    document.getElementsByClassName("recent_route_speed_onoff")[0].style.backgroundColor = "#382efe7f";
    showSpeedYn = false;
  } else {
    document.documentElement.style.setProperty('--marker-speed-display', 'hidden');
    document.getElementsByClassName("recent_route_speed_onoff")[0].innerHTML = "속도표시 OFF";
    document.getElementsByClassName("recent_route_speed_onoff")[0].style.backgroundColor = "#7a797971";
    showSpeedYn = true;
  }
}

/**
 * 경로요약정보 설정..
 */
 const setRouteInfo = (d1, d2, d3) => {

  console.log('d1='+d1);
  console.log('d2='+d2);
  console.log('d3='+d3);

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
  document.getElementsByClassName("recent_route_speedChart_onoff")[0].style.backgroundColor = speedForColor; // 배경색
  
  

  // 데이타 세팅..
  var htmlString = "";
  htmlString = "" +
  "<p>" +
  "<span><small><small>이동거리 : </small></small><span data-animation=\"number\" data-value=\"" + d1 + "\">0</span><small><small>km</small></small>&nbsp;&nbsp;/ </span>" +
  "<span><small><small>소요시간 : </small></small><span data-animation=\"number\" data-value=\"" + d2 + "\">0</span><small><small>분</small></small>&nbsp;&nbsp;/ </span>" +
  "<span><small><small>최고속도 : </small></small><span data-animation=\"number\" data-value=\"" + d3 + "\">0</span><small><small>km/h</small></small></span>" +
  "</p>"
  ;
  document.getElementById('route_statics').innerHTML = htmlString;

  // popover
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new Popover(popoverTriggerEl)
  });

  animateNumber();
};



/**
 * 최근 차량의 이동경로 조회(6시간이전)
 * @param deviceNo 
 */
const recntHour = 3;
const recentRouteTruck = (deviceNo, lastDtm) => {
  // 경로정보 조회
  let date = common_utils.getDateFromString(lastDtm);
  date.setHours(date.getHours() - recntHour); 
  var startDtm = common_utils.getDtmFormatOnlyDigits(date);
  // var endDtm = common_utils.getDtmFormatOnlyDigits(new Date()); // 지금까지..
  var endDtm = lastDtm;

  doRouteSearch(deviceNo, startDtm, endDtm);
}

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



</script>

<template>
  <div class="section section__management" style="gap: 2px">
    <div class="group__title">
      <h2>차량현위치조회</h2>
    </div>
    <div></div>
    <div class="group__contents">
      <!-- 메인데이타 -->
      <div class="part__data_list" style="flex: 0.5" id="areaDataList">
        <!-- 검색 -->
        <div class="group__search">
          <div class="part__search_box">
            <input
              type="text"
              v-on:keyup.enter="searchBtn()"
              v-model="keyword"
              placeholder="검색어를 입력하세요"
            />
            <button @click="searchBtn()" style="width: 100px">
              <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
            </button>
          </div>
        </div>
        <!-- 리스트 -->
        <div>
          <div class="item__scroll" id="productDiv">
            <div class="unit__scroll">
              <table>
                <thead>
                  <tr>
                    <th
                      v-for="(col, index) in table.columns"
                      :key="index"
                      :style="
                        Object.assign(
                          { width: col.width ? col.width : 'auto' },
                          col.headerStyles
                        )
                      "
                    >
                      <div class="unit_bundle">
                        {{ col.label }}
                        <div class="unit__buttons" v-if="col.sortable">
                          <button
                            :class="
                              table.sortable.order === col.field &&
                              table.sortable.sort === 'asc'
                                ? 'active'
                                : ''
                            "
                            @click.prevent="
                              col.sortable
                                ? doSort(col.field, table.sortable, table.rows)
                                : false
                            "
                          >
                            <i class="fa-solid fa-angle-up"></i>
                          </button>
                          <button
                            :class="
                              table.sortable.order === col.field &&
                              table.sortable.sort === 'desc'
                                ? 'active'
                                : ''
                            "
                            @click.prevent="
                              col.sortable
                                ? doSort(col.field, table.sortable, table.rows)
                                : false
                            "
                          >
                            <i class="fa-solid fa-angle-down"></i>
                          </button>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    :class="selectRowData?.RANK === obj.RANK ? 'active' : ''"
                    v-if="table.rows.length > 0"
                    v-for="(obj, index) in table.rows"
                    :key="index"
                    @click="handleRowClick(obj, $event)"
                  >
                    <td>
                      <div>{{ obj.RANK }}</div>
                    </td>
                    <td>
                      <div>
                        <span>{{ obj.VEHICLENO }}</span>
                      </div>
                    </td>
                    <td>
                      <div>
                        <span>{{ obj.NAME }}</span>
                      </div>
                    </td>
                    <td>
                      <div>
                        <span>{{ obj.TIMESTAMP }}</span>
                      </div>
                    </td>
                    <!-- <td>
                            <div style="text-align: left;"><span>{{ obj.ADDRESS }}</span></div>
                          </td> -->
                  </tr>
                  <tr v-else>
                    <td :colspan="table.columns.length">
                      <span>no data</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- 더보기버튼 -->
        <div class="item__buttons" v-if="table.isShowMoreBtn">
          <button @click="searchMoreBtn">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
          <button id="btnToTop" @click="scrollToTop" style="display: none">
            <i class="fa-solid fa-arrow-up"></i>위로
          </button>
        </div>
      </div>
      <!-- 네이버지도 -->
      <div
        style="
          flex: 2;
          border-style: solid;
          border-width: 1px;
          border-color: #a8a7b0;
          position: relative
        "
      >
        <NaverMap style="height: 100%; width: 100%" />
        <!-- 새로고침 버튼 추가 -->
        <div style="position: absolute; left: 10px; top: 10px">
          <img
            class="cvo_map_refresh"
            @click.prevent="searchBtn"
            src="/assets/img/map/refresh_blue_01.png"
          />
        </div>
        <!-- 경로정보요약 -->
        <div class="route_summ_area">
          <span class="recent_route_time_onoff" @click="showTimeInfo()">시간표시 ON</span>
          <span class="recent_route_speed_onoff" @click="showSpeedInfo()">속도표시 ON</span>
          <span class="recent_route_speedChart_onoff" @click="showSpeedChart()"><i class="icon-chart"></i></span>
          <div id="route_statics">
          </div>
        </div>
        <!-- 속도차트 -->
        <div class="route_summ_area_chart" v-if="visitor">
            <apexchart id="chart123" type="bar" width="100%" height="100px" :options="visitor?.chart?.options" :series="visitor?.chart?.series" @mouseMove="mouseChartMove"></apexchart>
        </div>
      </div>
    </div>
    <div></div>
  </div>
</template>

<style lang="scss">

.sample_work {
  background-color: #5da3f7c9;
  background-color: #a0ff528f;
  background-color: #4952ff;
  background-color: #4952ff9b;
  background-color: #8bff3d;
  background-color: #7d13e6bc;
}

tr {
  cursor: pointer;
}
</style>
