<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import { mapInstance } from "@/naverMap/stores";
import { NaverMap } from "@/naverMap";

import { operateApi } from "@/api";
import { startLoadingBar, removeLoadingBar, doSort } from "@/common/utils.ts";
import * as common_utils from "@/common/utils.ts";

import { useAlert } from "@/composables/showAlert";
import { nextTick } from "process";
import { left } from "@popperjs/core";
const { showAlert, showAlertSuccess } = useAlert();
let selectRowData = ref([]);
const isShowTable = ref(true);

const posYn = ref("");
const keyword = ref("");
const pageNumber = ref(1);
const pageSize = ref(15);
const table = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "NO",
      field: "RANK",
      width: "5%",
    },
    {
      label: "거래처명",
      field: "CLNAME",
      width: "10%",
      sortable: true,
      isKey: true,
    },
    {
      label: "사업자 등록번호",
      field: "CLSAUPNO",
      width: "10%",
    },
    // {
    // label: "전화번호",
    // field: "CLTEL",
    // width: "10%",
    // },
    {
      label: "대표자",
      field: "CLCEO",
      width: "5%",
    },
    {
      label: "전화번호",
      field: "CLPHONE",
      width: "10%",
    },
    // {
    // label: "주소",
    // field: "CLJUSO1",
    // width: "30%",
    // },
    // {
    // label: "주소2",
    // field: "CLJUSO2",
    // width: "10%",
    // },
    // {
    // label: "위도",
    // field: "LAT",
    // width: "10%",
    // },
    // {
    // label: "경도",
    // field: "LON",
    // width: "10%",
    // },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "CLNAME",
    sort: "asc",
  },
  isShowMoreBtn: true,
});

const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userId = userInfo.ID;

//////////////api 통신
onMounted(() => {
  searchBtn();
  setTimeout(() => {
    const setRowData = (lat, lon) => {
      selectRowData.value.LAT = lat;
      selectRowData.value.LON = lon;

      onRemove();
      const title = selectRowData.value.CLNAME;
      const latitude = selectRowData.value.LAT;
      const longitude = selectRowData.value.LON;
      addMarker(latitude, longitude, title);
    };

    naver.maps.Event.addListener(mapInstance.value, "click", (e) => {
      setRowData(e.coord.lat(), e.coord.lng());
    });

    mapInstance.value.setOptions("mapTypeControl", true); //지도 유형 컨트롤의 표시 여부
  }, 500);
});

const doSearch = async () => {
  table.isShowMoreBtn = false;

  if (table.isLoading) return;
  table.isLoading = true;
  startLoadingBar();

  console.log("posYn.value =" + posYn.value);

  const param = {
    clcode: "",
    posYn: posYn.value ? "Y" : "N",
    keyword: keyword.value,
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
    inputUser: userId,
  };

  let data;
  try {
    data = await operateApi.admClientPosSel(param);
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
    }, 100);

    // 선택데이타 초기화
    selectRowData.value = {};
    // 지도마커 초기화
    onRemove();
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
      // '@I_BOUND_DIST': curPositionObj.boundDist,
      // '@I_KEYWORD': searchKeyword.value ?? "",
      // '@I_CENTER_LAT': curPositionObj.value.latitude,
      // '@I_CENTER_LON': curPositionObj.value.longitude,
  }
  
  // // 1.거래처 목록 지우기
  // clientList.value = [];
  // // 2.그리드 데이타 지우기
  // rowData.splice(0);
  
  
  // 7.API요청..
  let data, result;
  try {
    result = await common_utils.getAxiosERP().post(`/ERP_CLIENT_SEL `, reqData)
    data = result.data;
    if (data.RecordCount > 0) {

      // // 그리드용 데이타 푸쉬
      // rowData.push(...data.RecordSet);
      // // 그리드에 세팅..
      // gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(
      //   rowData
      // );
      
      } else {
        
      }

    
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
  }
};


const saveData = async () => {
  const address1 = selectRowData.value.CLJUSO1;
  const address2 = selectRowData.value.CLJUSO2;
  startLoadingBar();
  const param = {
    clCode: selectRowData.value.CLCODE,
    lat: selectRowData.value.LAT,
    lon: selectRowData.value.LON,
    address: address1 + " " + address2,
    inputUser: userId,
  };

  let data;
  try {
    data = await operateApi.admClientPosSave(param);
    table.isLoading = false;
    showAlertSuccess("저장이 완료되었습니다.");
    table.rows = [];
    doSearch();
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    table.isLoading = false;
  }
};

/**
 * CVO 지오코드 요청(주소->좌표 변환)
 */
const cvoGeocodeSel = async () => {
  const address1 = selectRowData.value.CLJUSO1;
  const address2 = selectRowData.value.CLJUSO2;
  console.log(address1 + " " + address2);

  startLoadingBar(); // 대기창 표시
  const param = {
    address: address1 + " " + address2,
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

      // 마커추가
      // const title = selectRowData.value.CLNAME
      // addMarker(LAT,LON,title)

      // setRowData(LAT,LON)

      selectRowData.value.LAT = LAT;
      selectRowData.value.LON = LON;

      onRemove();
      const title = selectRowData.value.CLNAME;
      const latitude = selectRowData.value.LAT;
      const longitude = selectRowData.value.LON;
      addMarker(latitude, longitude, title);

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

//////////////함수

const checkAddress = () => {
  cvoGeocodeSel();
};
const saveBtn = () => {
  saveData();
};

const searchBtn = () => {
  //검색 Btn
  pageNumber.value = 1;
  table.rows = [];
  doSearch();
};
const searchMoteBtn = () => {
  //더보기 Btn
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};

const scrollToTop = () => {
  //위로 Btn
  document.getElementById("areaDataList").scrollTop = 0;
};

const handleRowClick = (rowData) => {
  selectRowData.value = JSON.parse(JSON.stringify(rowData));

  onRemove();
  const title = selectRowData.value.CLNAME;
  const latitude = selectRowData.value.LAT;
  const longitude = selectRowData.value.LON;
  // 마커추가
  addMarker(latitude, longitude, title);

  // 위치로 이동
  var latLng = new window.naver.maps.LatLng(latitude, longitude);
  mapInstance.value.setCenter(latLng);
};

var markerList = []; // 마커 객체
var infowindow = []; // 마커 위 타이틀 객체
const onRemove = () => {
  //마커 및 타이틀 객체 지우기
  markerList.forEach((el) => {
    el.setMap(null);
  });
  infowindow.forEach((el) => {
    el.setMap(null);
  });
  markerList = [];
  infowindow = [];
};

const addMarker = (lat, lng, name) => {
  // mapInstance.value.setZoom(17);
  onRemove();
  let latLng = new window.naver.maps.LatLng(lat, lng);
  // 지도이동 차단, 지도를 이동해버리면 클릭위치를 알수없다.
  // mapInstance.value.setCenter(latLng);
  markerList.push(
    new window.naver.maps.Marker({
      position: latLng,
      map: mapInstance.value,
      zIndex: 100,
    })
  );
  infowindow.push(
    new window.naver.maps.InfoWindow({
      content:
        '<div style="padding:0px 10px; border-radius:50px;">' + name + "</div>",
    })
  );
  infowindow[0].open(mapInstance.value, markerList[0]);
};

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
</script>

<template>
  <div class="section section__management" style="gap: 2px">
    <div class="group__title">
      <h2>거래처 위치관리</h2>
    </div>
    <div></div>
    <div class="group__contents container1">
      <!-- 메인데이타 -->
      <div
        class="part__data_list left"
        style="flex: unset; height: auto"
        id="areaDataList"
      >
        <!-- 검색 -->
        <div style="margin-top: -15px">
          <input type="checkbox" v-model="posYn" style="height: auto" /><span>
            위치있음</span
          >
        </div>
        <div class="group__search">
          <div class="part__search_box">
            <input
              type="text"
              v-on:keyup.enter="searchBtn()"
              v-model="keyword"
              placeholder="검색어를 입력하세요"
            />
            <button @click="searchBtn()" style="width: 100px">
              <i class="fa-solid fa-magnifying-glass"></i><span>통합 검색</span>
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
                        <span>{{ obj.CLNAME }}</span>
                      </div>
                    </td>
                    <td>
                      <div>
                        <span>{{ obj.CLSAUPNO }}</span>
                      </div>
                    </td>
                    <!-- <td>
                            <div><span>{{ obj.CLTEL}}</span></div>
                          </td>  -->
                    <td>
                      <div>
                        <span>{{ obj.CLCEO }}</span>
                      </div>
                    </td>
                    <td>
                      <div>
                        <span>{{ obj.CLPHONE }}</span>
                      </div>
                    </td>
                    <!-- <td>
                            <div><span>{{ obj.CLJUSO1}}</span></div>
                          </td> 
                          <td>
                            <div><span>{{ obj.CLJUSO2}}</span></div>
                          </td>  -->
                    <!-- <td>
                            <div><span>{{ obj.LAT}}</span></div>
                          </td> 
                          <td>
                            <div><span>{{ obj.LON}}</span></div>
                          </td>  -->
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
          <button @click="searchMoteBtn">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
          <button id="btnToTop" @click="scrollToTop" style="display: none">
            <i class="fa-solid fa-arrow-up"></i>위로
          </button>
        </div>
      </div>
      <div
        class="resizer"
        id="dragMe"
        @mousedown="mouseDownHandler($event)"
      ></div>
      <!-- 상세 -->
      <div class="part__data_detail right" style="flex: 1; height: auto">
        <!-- <div class="item__title" >
          <i class="fa-solid fa-angle-right item__angle"></i>
          <span>선택한 거래처</span>
        </div> -->
        <div class="item__contents" style="display: flex; flex-direction: row">
          <!-- 거래처명 -->
          <div style="flex: 0.25">
            <p>거래처명</p>
            <input type="text" disabled v-model="selectRowData.CLNAME" />
          </div>
          <!-- 주소 -->
          <div style="flex: 0.5; margin-top: 0px">
            <p>주소</p>
            <div style="display: flex; justify-content: space-between">
              <input
                type="text"
                v-model="selectRowData.CLJUSO1"
                style="flex: 3"
              />
              <input
                type="text"
                v-model="selectRowData.CLJUSO2"
                style="flex: 1; margin-left: 10px"
              />
            </div>
          </div>
          <div
            class="item__buttons"
            style="
              flex: 0.1;
              border: 0px solid red;
              margin-left: 20px;
              margin-top: 0px;
            "
          >
            <button class="btn_check" @click="checkAddress">
              <i class="fa-regular fa-circle-check"></i><span>위치확인</span>
            </button>
          </div>
          <!-- 좌표정보(숨김처리:사용자가 값을 바꿀이유는 없다) -->
          <div style="display: none; justify-content: space-between">
            <div style="width: 49%">
              <p>위도</p>
              <input type="text" v-model="selectRowData.LAT" />
            </div>
            <div style="width: 49%">
              <p>경도</p>
              <input type="text" v-model="selectRowData.LON" />
            </div>
          </div>
        </div>
        <!-- 네이버지도 -->
        <div style="flex: 9; background-color: blanchedalmond">
          <NaverMap style="height: 100%" @click="clickOk" />
        </div>
        <!-- 저장버튼 -->
        <div
          class="item__buttons"
          style="
            margin-top: 10px;
            display: flex; /* Flex 컨테이너로 설정 */
            justify-content: center; /* 내부 아이템을 가로로 중앙 정렬 */
            align-items: center; /* 내부 아이템을 세로로 중앙 정렬 */
          "
        >
          <button class="btn_save" @click="saveBtn">
            <i class="fa-regular fa-circle-check"></i><span>저장</span>
          </button>
        </div>
      </div>
    </div>
    <div></div>
  </div>
</template>

<style scoped>
tr {
  cursor: pointer;
}
.container1 {
  display: flex;
  height: 16rem;
  width: 100%;
}
.resizer {
  background-color: #eb2b2b;
  cursor: ew-resize;
  width: 3px;
  margin: 0px -10px;
}
.left {
  width: 35%;
  /* 중앙 정렬 */
  display: flex;
  /*justify-content: center;*/
  min-width: 25%;
}
.right {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 25%;
}
</style>
