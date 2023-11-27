<script setup>
import { ref, computed, reactive, onMounted, defineComponent } from "vue";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import { productApi } from "@/api";
import {
  startLoadingBar,
  removeLoadingBar,
  addHoverClassToTr1,
  removeHoverClassFromTr1,
  doSort,
  showToast,
  addCursorClassToTr,
  removeCursorClassFromTr,
  excelDownload,
  startProgressbar,
  removeProgressbar,
  setDateFormat,
} from "@/common/utils.ts";
import { ko } from "date-fns/locale";
import Datepicker from "vue3-datepicker";
import quillEditor from "@/components/plugins/QuillEditor.vue";
import { useAlert } from "@/composables/showAlert";

const { showAlert, showAlertSuccess } = useAlert();
//사용자 및 페이지 정보
const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userClcode = userInfo.CLCODE;
const userId = userInfo.ID;
const pageNumber = ref(1);
const pageSize = ref(10);
const pageNumberRU = ref(1);
const pageSizeRU = ref(8);
const pageNumberRD = ref(1);
const pageSizeRD = ref(8);

const rType = ref(); //기획유형 10 - 테마, 20 - 일반
const sortNum = ref(); //보기순서
const quillEditorRef = ref(null); //내용

//대표이미지
const selectQrUrl =
  "http://sjwas.gzonesoft.co.kr:27002/api/GzoneUtilController/getQrImage?dataString=http://sjwas.gzonesoft.co.kr:27004/qr_image/index.html?itcode=";
let selectImg = ref("");

//검색어
let searchKeyword = "";

//죄측 메인 그리드에서 선택한 row 정보
let rowTargetObj = "";
let selectRowData = ref([]);

//전체선택
const isAll = ref({});

//게시기간
let koreanLocale = {
  lang: "ko",
  daysOfWeek: ["일", "월", "화", "수", "목", "금", "토"],
  monthNames: [
    "1월",
    "2월",
    "3월",
    "4월",
    "5월",
    "6월",
    "7월",
    "8월",
    "9월",
    "10월",
    "11월",
    "12월",
  ],
  today: "오늘",
  clear: "초기화",
  cancel: "취소",
  ok: "확인",
  select: "날짜 선택",
  prevMonth: "이전달",
  nextMonth: "다음달",
  datePickerTitle: "날짜 선택",
  year: "년",
  month: "월",
  placeholder: "날짜를 선택하세요",
  rangeSeparator: " - ",
  weekAbbreviation: "주",
  rangeHeaderText: "날짜 범위 선택",
};

const table = reactive({
  //좌측 메인 테이블 정보
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "",
      field: "CHECK",
      width: "3%",
      isKey: true,
    },
    {
      label: "NO",
      field: "RANK",
      width: "2%",
      sortable: false,
    },
    {
      label: "기획명",
      field: "TITLE",
      width: "10%",
      sortable: true,
      isKey: true,
    },
    {
      label: "기획유형",
      field: "TYPE",
      width: "7%",
      sortable: true,
      isKey: true,
    },
    // {
    // label: "게시기간",
    // field: "",
    // width: "10%",
    // sortable: false,
    // isKey: true,
    // },
    {
      label: "보기순서",
      field: "",
      width: "7%",
      sortable: false,
      isKey: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "TYPE",
    sort: "asc",
  },
  isShowMoreBtn: false,
});

/******************************************************************************* onMounted */
onMounted(() => {
  pageNumber.value = 1;
  doSearch();
});
/******************************************************************************* 버튼 및 액션 이벤트 start */
const chnageAllChk = (e) => {
  //헤더부 전체선택
  table.rows.map((item) => (item.CHECK = isAll.value));
};
const tableLoadingFinish = (elements) => {
  //Table search finished event
  table.isLoading = false;
};
const handleRowClick = (rowData, event) => {
  //좌측 메인 그리드 row click event

  if (rowTargetObj) {
    rowTargetObj.classList.remove("table-good");
  }
  selectRowData.value = JSON.parse(JSON.stringify(rowData));
  // selectRowData.value.DATE1 = selectRowData.value.DATE1 ? new Date(selectRowData.value.DATE1) : ''
  // selectRowData.value.DATE2 = selectRowData.value.DATE2 ? new Date(selectRowData.value.DATE2) : ''
  sortNum.value = selectRowData.value.SORT_NUM;
  rType.value = selectRowData.value.TYPE;
  quillEditorRef.value.setContent(selectRowData.value.CONTENTS);
  selectImg.value = selectRowData.value.IMG_URL;
  document.getElementById("showImg").src = selectRowData.value.IMG_URL
    ? selectRowData.value.IMG_URL
    : "/assets/img/logo/noimg.png";
  document.getElementById("selectFile").value = "";

  rowTargetObj = event.target.closest("tr");
  rowTargetObj.classList.add("table-good");
};

const chkBtn = () => {
  //사용안함
  if (selectRowData.value.DAY_USE_YN === "N") {
    //체크->체크해제
    selectRowData.value.DAY_USE_YN = "Y";
  } else {
    selectRowData.value.DAY_USE_YN = "N";
    // selectRowData.value.DATE1 = ''
    // selectRowData.value.DATE2 = ''
  }
};
const newBtn = () => {
  //신규
  selectRowData.value = [];
  selectImg.src = "";
  sortNum.value = "";
  rType.value = "";
  quillEditorRef.value.setContent("");
  document.getElementById("showImg").src = "/assets/img/logo/noimg.png";
  document.getElementById("selectFile").value = "";

  isAll.value = false;
  table.rows.forEach((row) => {
    row.CHECK = false;
  });
};
const saveBtn = () => {
  //저장

  selectRowData.value.CONTENTS = quillEditorRef.value.getContent();

  let chk = false;
  let chkId = "";

  if (!selectRowData.value.TITLE) {
    chk = true;
    chkId = `${chkId}기획명`;
  }
  // if(selectRowData.value.SCHEDULE_USE_YN !== 'N'){//체크해제된 상태->입력항목vali...
  // 	if(!selectRowData.value.DATE1){
  // 		chk = true
  // 		chkId = !chkId? `시작일` : `${chkId}, 시작일`
  // 	}
  // 	if(!selectRowData.value.DATE2){
  // 		chk = true
  // 		chkId = !chkId? `종료일` : `${chkId}, 종료일`
  // 	}
  // }
  if (!rType.value) {
    chk = true;
    chkId = !chkId ? `기획유형` : `${chkId}, 기획유형`;
  }
  if (!sortNum.value) {
    chk = true;
    chkId = !chkId ? `보기순서` : `${chkId}, 보기순서`;
  }

  if (chk) {
    showAlert(`입력 항목을 확인해 주세요..<br>(${chkId})`);
    event.preventDefault();
    return;
  }

  if (document.getElementById("selectFile").files.length > 0) {
    //이미지 수정여부 1 - 수정
    doImgSave();
  } else {
    doSave();
  }
};
const deleteBtn = () => {
  //삭제

  const selectedRows = table.rows.filter((row) => row.CHECK === true);

  if (selectedRows.length === 0) {
    showAlert("삭제할 항목을 선택해주세요.");
    return;
  }

  selectedRows.forEach((row) => {
    doDelete(row);
  });
};
const searchMoteBtn = () => {
  //좌측 메인 테이블 더보기 버튼
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};
const openFile = (file) => {
  //파일 정보
  if (!selectRowData?.value?.GEONUM) {
    showAlert("저장 후<br> 이미지를 등록해주세요.");
    return;
  }

  var fileObject = document.getElementById("selectFile");
  fileObject.click();
};
const changeFile = (e) => {
  if (e.target.files[0]) {
    // 인풋 태그에 파일이 있는 경우
    // 이미지 파일인지 검사 (생략)
    // FileReader 인스턴스 생성
    const reader = new FileReader();

    reader.onload = (e) => {
      // 이미지가 로드가 된 경우
      const previewImage = document.getElementById("showImg");
      previewImage.src = e.target.result;
    };

    reader.readAsDataURL(e.target.files[0]); // reader가 이미지 읽도록 하기
  }
};
const inputData = () => {
  //입력항목 입력값 제한
  sortNum.value = sortNum.value.replace(/[^0-9]/g, "");
};

//드래그

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

/******************************************************************************* api 호출 start */

const doSearch = async () => {
  //좌측 메인 테이블 조회
  if (table.isLoading) return;
  table.isLoading = true;
  startLoadingBar();
  const param = {
    geonum: "",
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
    inputUser: userId,
  };
  let data;
  try {
    data = await productApi.eventManagementList(param);
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
    /*var productDiv = document.getElementById('productDiv');
			setTimeout(()=>{
			productDiv.scrollTop = productDiv.scrollHeight;
			},100)*/
  }
};

const doDelete = async (row) => {
  //삭제
  const param = {
    geonum: row?.GEONUM,
    delYn: "Y",
    inputUser: userId,
  };
  let data;
  try {
    data = await productApi.eventManagementDelete(param);
    if (data.ResultCode === "00") {
      showAlertSuccess("삭제되었습니다.");
      table.rows = [];
      doSearch();
    }
  } catch (error) {
    console.error(error);
  }
  newBtn();
};
const doSave = async () => {
  //저장

  const param = {
    geonum: selectRowData?.value?.GEONUM ?? "",
    type: rType.value ?? "",
    title: selectRowData?.value?.TITLE ?? "",
    imgFileNo: "", // imgFileNo.value ?? selectRowData?.value?.IMG_FILE_NO,
    date1: "", // setDateFormat(selectRowData?.value?.DATE1)?? '',
    date2: "", // setDateFormat(selectRowData?.value?.DATE2)?? '',
    scheduleUseYn: "Y", // selectRowData?.value?.SCHEDULE_USE_YN ?? 'Y',
    bigo: selectRowData?.value?.BIGO ?? "",
    inputUser: userId,
    content: selectRowData.value.CONTENTS ?? "",
    sortNum: sortNum.value ?? "",
  };

  let data;
  try {
    data = await productApi.eventManagementSave(param);
    if (data.ResultCode === "00") {
      showAlertSuccess("저장되었습니다.");
      table.rows = [];

      doSearch();
    }
  } catch (error) {
    console.error(error);
  }
  newBtn();
};
let imgFileNo = ref(null);
const doImgSave = async () => {
  //대표이미지 저장

  let files = document.querySelector("#selectFile").files;
  if (files.length === 0) return;

  let formData = new FormData();
  formData.append("files", files[0]);
  const fileFormat = files[0].type?.split("/")?.[1];

  document.getElementById("selectFile");
  var d = new Date();
  const hours = d.getHours().toString().padStart(2, "0");
  const minutes = d.getMinutes().toString().padStart(2, "0");
  const seconds = d.getSeconds().toString().padStart(2, "0");

  let fileName = `I_${selectRowData.value.GEONUM}_202_${hours}${minutes}${seconds}`;
  let url = `/event_mng/${fileFormat}/${false}/${fileName}`;

  startLoadingBar();

  const data = await productApi.fileUpload(formData, url);
  console.log("///data:::", data);
  if (data.data.ResultCode === "00") {
    //showAlertSuccess("저장되었습니다.")
    removeProgressbar();

    imgFileNo.value = data.data.fileDetails[0].FileNo;
    await doSave();
  } else {
    showAlert("통신 이슈로 인해 저장되지 않았습니다. 재시도 바랍니다.");
    removeLoadingBar();
  }
};
</script>
<template>
  <div class="section section__management">
    <!-- <div class="group__title">
            <h2>기획관리</h2>
        </div>   -->
    <div class="group__contents container1">
      <!-- 좌측리스트영역 -->
      <div class="part__data_list left" style="flex: unset">
        <div class="group__search">
          <div
            class="part__search_box"
            style="
              display: flex;
              align-items: center;
              justify-content: space-between;
            "
          >
            <div class="group__title">
              <h2>기획관리</h2>
            </div>
            <div style="display: flex">
              <button type="button" @click="newBtn()">
                <i class="fa fa-pen-to-square fa-fw"></i>신규
              </button>
              <button type="button" style="width: 100px" @click="deleteBtn()">
                <i class="fa fa-trash-can fa-fw"></i>선택 삭제
              </button>
            </div>
          </div>
        </div>
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
                      <template v-if="col.field === 'CHECK'">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          @change="chnageAllChk"
                          v-model="isAll"
                        />
                      </template>
                      <template v-else>
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
                      </template>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' : ''"
                  v-if="table.rows.length > 0"
                  v-for="(obj, index) in table.rows"
                  :key="index"
                  @mouseenter="addHoverClassToTr"
                  @mouseleave="removeHoverClassFromTr"
                  @click="handleRowClick(obj, $event)"
                >
                  <td>
                    <div>
                      <input
                        class="form-check-input"
                        type="checkbox"
                        v-model="obj['CHECK']"
                      />
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.RANK }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span>{{ obj.TITLE }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.TYPE === "10" ? "테마" : "일반" }}</span>
                    </div>
                  </td>
                  <!-- <td>
									<div><span>{{ obj.DATE1 }} <br/>~<br/> {{ obj.DATE2 }}</span></div>
								</td>
								<td>
									<div><span>{{ obj.SCHEDULE_USE_YN==='Y' ? '사용함' : '사용안함' }}</span></div>
								</td> -->
                  <td>
                    <div>
                      <span>{{ obj.SORT_NUM }}</span>
                    </div>
                  </td>
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
        <div class="item__buttons" v-if="table.isShowMoreBtn">
          <button @click="searchMoteBtn">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
        </div>
      </div>
      <!-- 좌우조절 -->
      <div
        class="resizer"
        id="dragMe"
        @mousedown="mouseDownHandler($event)"
      ></div>
      <!-- 우측상세영역 -->
      <div class="part__data_detail right" style="flex: 1">
        <div style="overflow: auto; height: 100%">
          <!-- <div class="item__title" >
						<i class="fa-solid fa-angle-right item__angle"></i>
						<span>상세보기</span>
					</div> -->
          <div class="group__search" style="">
            <div
              class="part__search_box"
              style="float: right; border-bottom: 1px solid #eaeaea"
            >
              <button type="button" @click="saveBtn()">
                <i class="fa fa-circle-check fa-fw"></i>저장
              </button>
            </div>
          </div>
          <div class="item__contents">
            <div>
              <div
                class="row"
                style="border-top: 0px solid #eaeaea; width: 100%"
              >
                <div>
                  <div class="row">
                    <div class="col-xl-6 col-lg-6">
                      <label class="form-label">기획명</label>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="기획명.."
                        v-model="selectRowData.TITLE"
                      />
                    </div>
                    <div class="col-xl-6 col-lg-6">
                      <label class="form-label">보기순서</label>
                      <input
                        type="text"
                        class="form-control"
                        placeholder="예시) 1.."
                        v-model="sortNum"
                        @input="inputData()"
                      />
                    </div>
                    <div class="col-xl-6 col-lg-6">
                      <label class="form-label">대표 이미지</label>
                      <div class="row" style="align-items: end">
                        <div class="h-55px col-auto" style="padding-right: 0px">
                          <img
                            id="showImg"
                            :src="
                              selectImg
                                ? selectImg
                                : '/assets/img/logo/noimg.png'
                            "
                            class="mw-100 mh-70 rounded"
                          />
                        </div>
                        <div
                          class="text-dark text-opacity-10 small fw-bold col-auto"
                        >
                          <input
                            type="file"
                            v-show="false"
                            id="selectFile"
                            multiple
                            accept=".jpg, .jpeg, .png"
                            @change="changeFile"
                          />
                          <a
                            href="javascript:void(0);"
                            style="z-index: 1052 !important"
                            class="btn btn-primary btn"
                            @click.prevent="openFile"
                            >찾아보기</a
                          >
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                      <label class="form-label">기획유형</label>
                      <div class="radio_group" style="gap: 10px">
                        <label>
                          <input
                            type="radio"
                            id="radio1-1"
                            value="10"
                            v-model="rType"
                          />
                          <span for="radio1-1"> 테마</span>
                        </label>
                        <label>
                          <input
                            type="radio"
                            id="radio1-2"
                            value="20"
                            v-model="rType"
                          />
                          <span for="radio1-2"> 일반</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--2차 끝-->

              <div class="mb-3" style="margin-top: 10px">
                <quill-editor theme="snow" ref="quillEditorRef" />
                <div style="text-align: end; font-size: small">
                  <label v-if="selectRowData.WS_EDTDATE"
                    >Update {{ selectRowData.WS_EDTDATE }}/{{
                      selectRowData.WS_EDTUSER
                    }}</label
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.table-container {
  height: 500px; /* 원하는 높이 값으로 설정 */
  overflow-y: scroll;
  border-collapse: collapse; /* 테두리 선이 겹치지 않도록 설정합니다. */
  border: 1px solid gainsboro; /* 테두리 선의 스타일과 색상을 지정합니다. */
}
.ellipsis {
  width: 200px;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
  display: block;
}
.margin10 {
  margin-bottom: 10px;
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
  justify-content: center;
  min-width: 25%;
}
.right {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 35%;
}
</style>