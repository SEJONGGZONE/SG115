<script setup>
import { reactive, onMounted, inject, ref, watch, computed } from "vue";
import "bootstrap-icons/font/bootstrap-icons.css";
import { useAppOptionStore } from "@/stores/app-option";
import { onBeforeRouteLeave } from "vue-router";
import vueTable from "@/components/plugins/VueTable.vue";
import navscrollto from "@/components/app/NavScrollTo.vue";
import ImageEdtior from "@/components/image/ImageEdtior.vue";
import { VueDraggableNext as draggable } from "vue-draggable-next";
import { ScrollSpy } from "bootstrap";
import { productApi } from "@/api";
import { Toast } from "bootstrap";
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
} from "@/common/utils.ts";
import { useAlert } from "@/composables/showAlert";

const { showAlert, showAlertSuccess, showConfirm } = useAlert();
//사용자 및 페이지 정보
const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
const userClcode = userInfo.CLCODE;
const userId = userInfo.ID;
const pageNumber = ref(1);
const pageSize = ref(15);
const isImageChange = ref(false);

const imageEditor = ref(null); //이미지 에디터 컴포넌트
const isEditor = ref(false); //이미지 편집 여부
const imageEditorType = ref("master"); //master , sub
const showDetailImage = ref("/assets/img/logo/noimg.png");

const isFileShowMoreBtn = computed(() => {
  return files.value.length < 9;
});
const isMasterFileShowMoreBtn = computed(() => {
  return masterFiles.value.length < 0;
});
//대표이미지
const selectQrUrl =
  "http://sjwas.gzonesoft.co.kr:27002/api/GzoneUtilController/getQrImage?dataString=http://sjwas.gzonesoft.co.kr:27004/qr_image/index.html?itcode=";
let selectImg = ref("");

//상품목록 검색조건 중 카데고리
const itsCode1 = ref("01");
const itsCode2 = ref("01");
const catergory1data = ref([]);
const catergory2data = ref([]);

// 상품 이미지 리스트 조회
const productTitleImg = ref([]); //대표이미지
const productSubImgList = ref([]); //추가이미지

//대표 이미지
//추가 이미지

let Testlist = [
  { name: "John", id: 1 },
  { name: "Joao", id: 2 },
  { name: "Jean", id: 3 },
  { name: "Gerard", id: 4 },
];
//검색어
let searchKeyword = ref("");

//메인 그리드에서 선택한 row 정보
const selectRowData = ref([]);

const tableSet = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: "NO",
      field: "RANK",
      width: "5%",
      sortable: true,
    },
    {
      label: "대표번호",
      field: "ITUSER",
      width: "5%",
      sortable: true,
    },
    {
      label: "이미지",
      width: "5%",
      isKey: true,
    },
    {
      label: "이미지 갯수",
      width: "5%",
      isKey: true,
    },
    {
      label: "상품명",
      width: "20%",
      sortable: true,
    },
    {
      label: "바코드",
      field: "ITBASE_EABARCODE",
      width: "10%",
      sortable: true,
    },
    {
      label: "제원",
      field: "ITSTAN",
      width: "10%",
      sortable: true,
    },
    // {
    //   label: "단가",
    //   field: "AMOUNT",
    //   width: "10%",
    //   sortable: true,
    // },
    {
      label: "매입가",
      field: "AMOUNT",
      width: "6%",
      sortable: true,
      isKey: true,
    },
    {
      label: "판매가",
      field: "ITEASDAN",
      width: "6%",
      sortable: true,
      isKey: true,
    },
    // {
    //   label: "관심수",
    //   field: "ITEM_COUNT",
    //   width: "10%",
    //   sortable: true,
    // },
    // {
    //   label: "추가정보",
    //   field: "LAST_DATE",
    //   width: "10%",
    // },
    {
      label: "Mall에서보기",
      field: "SHOW_IMAGE",
      width: "10%",
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "RANK",
    sort: "asc",
  },
  isShowMoreBtn: false,
});

/** 대표 이미지 */
let masterFiles = ref([]);
let masterImageFiles = ref(null); //업로드용 파일
let masterFilesPreview = ref([]);
/** 추가 이미지 */
let files = ref([]);
let imageFiles = ref(null); //업로드용 파일
let filesPreview = ref([]);
let uploadImageIndex = ref(0); // 이미지 업로드를 위한 변수

/******************************************************************************* onMounted */
onMounted(() => {
  pageNumber.value = 1;
  setCategory1();
  setCategory2();
  doSearch();
  // 모달객체를 밖으로 추가해준다.
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

/******************************************************************************* api 호출 start */
const setCategory1 = async () => {
  //카테고리 1
  const param = {
    code: "",
    inputUser: userId,
    name: "",
  };
  let data;
  try {
    const dataObj = await productApi.category(param);
    data = dataObj.data
    catergory1data.value = data.RecordSet;
    if (data.RecordCount > 0) {
      for (let i = 0; i < data.RecordCount; i++) {
        //코드 공백 제거(기존 패키지의설계가 VARCHAR를 쓰지않고 CHAR타입이라서 DB의 문제)
        catergory1data.value[i].CODE = data.RecordSet[i].CODE.trim();
      }
    }
  } catch (error) {
    console.error(error);
  }
};

const setCategory2 = async () => {
  //카테고리 2
  const param = {
    code: itsCode1.value,
    inputUser: userId,
    name: "",
  };
  let data;
  try {
    const dataObj = await productApi.category(param);
    data = dataObj.data
    catergory2data.value = data.RecordSet;
    if (data.RecordCount > 0) {
      itsCode2.value = catergory2data.value[0].CODE;
    }
  } catch (error) {
    console.error(error);
  }
};

/**
 * 상품조회
 * @param {} order 
 * @param {*} sort 
 */
const doSearch = async (order, sort) => {
  tableSet.isShowMoreBtn = false;

  if (tableSet.isLoading) return;
  tableSet.isLoading = true;

  startLoadingBar();

  const param = {
    itsCode1: itsCode1.value,
    itsCode2: itsCode2.value,
    pageSize: pageSize.value,
    searchKeyword: searchKeyword.value ?? "",
    clcode: userClcode,
    pageNumber: pageNumber.value,
    inputUser: userId,
    itCode: "",
  };

  let data;
  try {
    const dataObj = await productApi.productManagement(param);
    data = dataObj.data
    if (data.RecordCount > 0) {
      tableSet.rows.push(...data.RecordSet);
      if (data.RecordCount === pageSize.value) {
        tableSet.isShowMoreBtn = true;
      }
    } else {
      tableSet.isShowMoreBtn = false;
    }
  } catch (error) {
    console.error(error);
  } finally {
    removeLoadingBar();
    tableSet.isLoading = false;
    var productDiv = document.getElementById("productDiv");
    setTimeout(() => {
      productDiv.scrollTop = productDiv.scrollHeight;
    }, 100);
  }
};

const doSave = async () => {
  let files = document.querySelector("#selectFile").files;
  if (files.length === 0) return;

  const fileInfo = {
    //실제 파일
    file: files[0],
    //삭제및 관리를 위한 number
    SEQ: i + 0,

    TYPE: 30,
  };
  const params = makeFileInfo(fileInfo, "102");

  startProgressbar();
  saveImage(params).then(() => {
    const previewImage = document.getElementById("showImg");
    previewImage.src = "";
    document.getElementById("canclePop").click();
    searchBtn();
  });
};

const saveImage = async (params) => {
  return new Promise(async (resolve, reject) => {
    const file = params.file;
    const url = params.url;
    let formData = new FormData();
    formData.append("files", file);
    const data = await productApi.fileUpload(formData, url);

    if (data.data.ResultCode === "00") {
      isImageChange.value = true;
      setTimeout(() => {
        removeProgressbar();
      }, 5000);
    } else {
      showAlert("통신 이슈로 인해 저장되지 않았습니다. 재시도 바랍니다.");
      removeProgressbar();
      reject();
    }
    resolve(data);
  });
};

function callSaveImage(file, type) {
  // API 호출 로직
  // 여기에 실제 API 호출 코드를 작성합니다.
  // API 호출이 비동기적으로 처리되어야 하는 경우, Promise 또는 async/await를 사용하여 비동기 처리를 구현해야 합니다.
  // 아래의 예제에서는 간략하게 setTimeout 함수를 사용하여 비동기적인 동작을 흉내내었습니다.

  return new Promise(async (resolve, reject) => {
    const fileInfo = file;
    const params = makeFileInfo(fileInfo, type);
    const fileData = await saveImage(params);
    resolve(fileData); // API 호출 완료 후 resolve 호출
  });
}

let favItemCntList = ref([]);
const doFavItemCnt = async () => {
  //관심수 click 이벤트
  const param = {
    itCode: selectRowData.value.ITCODE,
  };
  let data;
  try {
    const dataObj = await productApi.getFavItemCntList(param);
    data = dataObj.data
    if (data.RecordCount > 0) {
      favItemCntList.value = data.RecordSet;
    }
  } catch (error) {
    console.error(error);
  }
};

const selectImageList = async () => {
  const itCode = selectRowData.value.ITCODE;
  const param = {
    itCode: itCode,
  };
  let data;
  try {
    const dataObj = await productApi.selectImageList(param);
    data = dataObj.data
    if (data.RecordCount > 0) {
      let imageList = data.RecordSet;
      masterFiles.value = imageList.filter((item) => item.TYPE === "30");
      if (masterFiles.value.length > 0) {
        showDetailImage.value = masterFiles.value[0].URL;
      }
      files.value = imageList.filter((item) => item.TYPE === "40");
    } else {
      masterFiles.value = [];
      files.value = [];
    }
    isImageChange.value = false;
  } catch (error) {
    console.error(error);
  }
};

const deleteImage = async (selectImge, type) => {
  try {
    isImageChange.value = true;

    if (type === "master") {
      if (selectImge.FILE_NO) {
        masterFiles.value = masterFiles.value.filter(
          (data) => !(data.FILE_NO === selectImge.FILE_NO)
        );
      } else {
        masterFiles.value = masterFiles.value.filter(
          (data) => !(data.SEQ === selectImge.SEQ)
        );
      }
    } else {
      if (selectImge.FILE_NO) {
        files.value = files.value.filter(
          (data) => !(data.FILE_NO === selectImge.FILE_NO)
        );
      } else {
        files.value = files.value.filter(
          (data) => !(data.SEQ === selectImge.SEQ)
        );
      }
    }
  } catch (error) {
    console.error(error);
  }
};

/******************************************************************************* api 호출 end */

/******************************************************************************* 버튼 및 액션 이벤트 start */
const closeImage = () => {
  console.log("--------------------- closeImage,0");
  isEditor.value = false;
  showDetailImage.value = "/assets/img/logo/noimg.png";
  if (isImageChange.value) {
    searchBtn();
  }
  document.getElementById("imageClose").click();
};
const handleRowClick = (rowData) => {
  //좌측 메인 그리드 row click event
  selectRowData.value = JSON.parse(JSON.stringify(rowData));
  console.log(selectRowData.value);
  selectImg.value = selectRowData.value.BACK_IMAGE;
  //doFavItemCnt();
};
const handleImageClick = async (rowData) => {
  //좌측 메인 그리드 row click event
  selectRowData.value = JSON.parse(JSON.stringify(rowData));
  await selectImageList(); //상세 이미지 불러오기
  //doFavItemCnt()
  document.getElementById("modalProductManagerDiv").click();
};
const searchBtn = () => {
  //검색 Btn
  tableSet.rows = [];
  pageNumber.value = 1;
  doSearch();
};
const optioCodeChg1 = () => {
  tableSet.rows = [];
  pageNumber.value = 1;
  catergory2data.value = [];
  itsCode2.value = "01";
  itsCode1.value = event.target.value;
  searchKeyword.value = "";
  setCategory2();
  doSearch();
};
const optioCodeChg2 = () => {
  tableSet.rows = [];
  pageNumber.value = 1;
  itsCode2.value = event.target.value;
  searchKeyword.value = "";
  doSearch();
};
const searchMoteBtn = () => {
  //더보기 Btn
  pageNumber.value = pageNumber.value + 1;
  doSearch();
};
const excelFileDownload = () => {
  //엑셀 Btn
  excelDownload(tableSet.rows, "productManagerMent");
};

const doAddInfo = (obj) => {
  //추가정보 click 이벤트 -- 작업 대기 및 disavle(23.04.27)
  handleRowClick(obj);
};

const openFile = (file) => {
  console.log("--------------------- openFile,0");
  var fileObject = document.getElementById("selectFile");
  fileObject.click();
};

const changeFile = (e) => {
  console.log("--------------------- changeFile,0");
  // 인풋 태그에 파일이 있는 경우
  if (e.target.files[0]) {
    // 이미지 파일인지 검사 (생략)
    // FileReader 인스턴스 생성
    const reader = new FileReader();
    // 이미지가 로드가 된 경우
    reader.onload = (e) => {
      const previewImage = document.getElementById("showImg");
      previewImage.src = e.target.result;
    };
    // reader가 이미지 읽도록 하기
    reader.readAsDataURL(e.target.files[0]);
  }
};
const makeFileInfo = (fileInfo, type) => {
  //103 : 대표이미지 , 104 : 추가이미지
  console.log("--------------------- makeFileInfo,0");

  const fileFormat = fileInfo.file.type?.split("/")?.[1];
  var d = new Date();

  const hours = d.getHours().toString().padStart(2, "0");
  const minutes = d.getMinutes().toString().padStart(2, "0");
  const seconds = d.getSeconds().toString().padStart(2, "0");

  let paddedNumber = type + String(fileInfo.SEQ).padStart(2, "0");
  if (type !== "104") {
    paddedNumber = type;
  }
  let fileName = `I_${selectRowData.value.ITCODE}_${paddedNumber}_${hours}${minutes}${seconds}`;
  let url = `/goods/${fileFormat}/${false}/${fileName}`;
  return {
    url,
    file: fileInfo.file,
  };
};
const masterImageChange = async () => {
  console.log("--------------------- masterImageChange,0");
};
const masterImageUpload = async () => {
  console.log("--------------------- masterImageUpload,0");

  const itCode = selectRowData.value.ITCODE;
  for (let i = 0; i < masterFiles.value.length; i++) {
    masterFiles.value[i].SEQ = i + 1;
  }
  const fileInfo = masterFiles.value[0];
  if (!fileInfo?.file) return false;
  await callSaveImage(fileInfo, "103").then((saveImage) => {
    console.log(saveImage);
    masterFiles.value[0].FILE_NO = saveImage.data.fileDetails[0].FileNo;
    //showAlertSuccess("대표 이미지가 저장되었습니다.")
  });
  console.log(masterFiles.value);
};
const fileDbSave = async (param) => {
  //파일 이미지 DB에 저장
  const params = {
    itcode: param.itcode,
    type: param.type,
    seq: param.seq,
    fileno: param.fileno,
  };
  return await productApi.itemImageSave(params);
};
const imageTotalSave = async () => {
  console.log("--------------------- imageTotalSave,0");

  await masterImageUpload();

  // if(files.value.length === 0){
  //   showAlert("추가 이미지가 없습니다.")
  //     return
  // }

  await imageAddUpload();

  const tempList = [];
  masterFiles.value.map((item) => tempList.push(item));
  files.value.map((item) => tempList.push(item));
  const params = await makeImageParams(tempList);

  const saveFileObj = await fileDbSave(params);
  const saveFile = saveFileObj.data
  if (saveFile.ResultMsg === "SUCCESS") {
    isImageChange.value = true;
    showAlertSuccess("이미지 저장이 완료되었습니다.");
    selectImageList();
    closeImage();
    //document.getElementById("imageClose").click()
  } else {
    showAlert("이미지 저장에 실패했습니다.");
  }
};

const makeImageParams = (dataList) => {
  console.log("--------------------- makeImageParams,0");
  let type = "";
  let seq = "";
  let fileno = "";
  dataList.map((item) => {
    fileno = fileno + item.FILE_NO + "/";
    seq = seq + item.SEQ + "/";
    type = type + item.TYPE + "/";
  });
  let params = {};
  params.itcode = selectRowData.value.ITCODE;
  params.type = type.replace(/\/$/, "");
  params.seq = seq.replace(/\/$/, "");
  params.fileno = fileno.replace(/\/$/, "");
  return params;
};

const fileDeleteButton = (file, type) => {
  console.log("--------------------- fileDeleteButton,0");
  showDetailImage.value = "/assets/img/logo/noimg.png";
  deleteImage(file, type);
};
let saveImages = ref([]);
const imageChange = async (edtiorType) => {
  console.log("--------------------- imageChange,0");
  imageEditorType.value = edtiorType;
  isEditor.value = true;
  setTimeout(() => {
    imageEditor.value.inputClick();
  }, 100);
};

const addEditorImage = (imageFile) => {
  console.log("--------------------- addEditorImage,0");

  let tempImages = [];
  const itCode = selectRowData.value.ITCODE;

  if (imageEditorType.value === "master") {
    masterFiles.value = [
      {
        //실제 파일
        file: imageFile,
        //이미지 프리뷰
        URL: URL.createObjectURL(imageFile),
        //삭제및 관리를 위한 number
        SEQ: 1,

        TYPE: 30,
        ITCODE: itCode,
      },
    ];
  } else {
    if (files.value.length > 3) {
      showAlert("파일 첨부는 4개까지만 가능합니다.");
      return;
    }

    let startIndex = files.value.length + 1;

    tempImages = {
      //실제 파일
      file: imageFile,
      //이미지 프리뷰
      URL: URL.createObjectURL(imageFile),
      SEQ: startIndex,
      TYPE: 40,
      ITCODE: itCode,
    };
    saveImages.value.push(tempImages);
    files.value = [
      ...files.value,
      //이미지 업로드
      tempImages,
    ];
  }
};

const imageAddUpload = async () => {
  console.log("--------------------- imageAddUpload,0");
  for (let i = 0; i < files.value.length; i++) {
    files.value[i].SEQ = i + 1;
  }
  for (let i = 0; i < files.value.length; i++) {
    const fileInfo = files.value[i];

    if (!fileInfo?.file) {
      continue;
    }
    await callSaveImage(fileInfo, "104").then((saveImage) => {
      console.log(saveImage);
      files.value[i].FILE_NO = saveImage.data.fileDetails[0].FileNo;
    });
  }
};
const setViewImage = (imageUrl) => {
  console.log("--------------------- setViewImage,0");
  isEditor.value = false;
  showDetailImage.value = imageUrl;
};

const previewImage = (itCode) => {
  console.log("--------------------- previewImage,0");
  const url = "https://www.cookzzang.com/product-details/" + itCode;
  window.open(url);
};


/******************************************************************************* 버튼 및 액션 이벤트 end */
</script>
<template>
  <div class="section section__management" style="">
    <!-- <div class="group__title">
            <h2>상품관리</h2>
            <div class="part__buttons" @click="setHIde()">
              <button>
                <i class="fa-solid fa-up-right-and-down-left-from-center" ></i>
              </button>
            </div>
          </div> -->
    
    <!-- 검색영역 -->
    <div class="group__search">
      <div class="part__search_box">
        <select
          class="selectBox"
          @change="optioCodeChg1"
          :v-model="catergory1data"
        >
          <option
            v-for="item in catergory1data"
            :value="item.CODE"
            :key="item.CODE"
          >
            {{ item.NAME }}
          </option>
        </select>

        <select
          class="selectBox"
          @change="optioCodeChg2"
          :v-model="catergory2data"
        >
          <option
            v-for="item in catergory2data"
            :value="item.CODE"
            :key="item.CODE"
          >
            {{ item.NAME }}
          </option>
        </select>
        <input
          type="text"
          v-on:keyup.enter="searchBtn()"
          v-model="searchKeyword"
          placeholder="검색어를 입력하세요"
        />
        <button @click="searchBtn()">
          <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
        </button>
        <button @click="excelFileDownload()">
          <i class="fa-solid fa-download"></i><span>엑셀</span>
        </button>
      </div>
    </div>
    
    <!-- 이미지 업로드 임시 태그(모달창 띄우기) -->
    <div
      id="modalProductManagerDiv"
      data-bs-toggle="modal"
      data-bs-target="#modalProductManager"
      style="display: none"
      data-backdrop="static"
      data-keyboard="false"
    />
    <!-- 이미지 편집 -->

    <div class="group__contents">
      <div class="part__data_list" style="height: 100%; padding-bottom: 0px">
        <!-- 메인리스트 -->
        <div class="item__scroll" id="productDiv">
          <div class="unit__scroll">
            <table>
              <thead>
                <tr>
                  <th
                    v-for="(col, index) in tableSet.columns"
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
                            tableSet.sortable.order === col.field &&
                            tableSet.sortable.sort === 'asc'
                              ? 'active'
                              : ''
                          "
                          @click.prevent="
                            col.sortable
                              ? doSort(col.field, tableSet.sortable, tableSet.rows)
                              : false
                          "
                        >
                          <i class="fa-solid fa-angle-up"></i>
                        </button>
                        <button
                          :class="
                            tableSet.sortable.order === col.field &&
                            tableSet.sortable.sort === 'desc'
                              ? 'active'
                              : ''
                          "
                          @click.prevent="
                            col.sortable
                              ? doSort(col.field, tableSet.sortable, tableSet.rows)
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
                  :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' : ''"
                  v-if="tableSet.rows.length > 0"
                  v-for="(obj, index) in tableSet.rows"
                  :key="index"
                  @mouseenter="addHoverClassToTr"
                  @mouseleave="removeHoverClassFromTr"
                  @click="handleRowClick(obj, $event)"
                >
                  <td class="vtl-tbody-td">
                    <div>
                      <span>{{ obj.RANK }}</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td">
                    <div>
                      <span>{{ obj.ITUSER }}</span>
                    </div>
                  </td>
                  <td
                    class="vtl-tbody-td"
                    @click="handleImageClick(obj, $event)"
                  >
                    <div class="h-50px clickPoint" style="padding: 5px 0px">
                      <img
                        alt=""
                        class="mw-100 mh-100"
                        :src="
                          obj.ITEM_MAIN_IMAGE
                            ? obj.ITEM_MAIN_IMAGE
                            : '/assets/img/logo/noimg.png'
                        "
                      />
                    </div>
                  </td>
                  <!--
                              <td class="vtl-tbody-td clickPoint" data-bs-toggle="modal" data-bs-target="#modalAccount1"
                                                    @mouseenter="addCursorClassToTr" @mouseleave="removeCursorClassFromTr"
                                                    >
                              <div class="h-50px clickPoint"><img alt="" class="mw-100 mh-100" :src="obj.BACK_IMAGE ? obj.BACK_IMAGE : '/assets/img/logo/noimg.png'"/></div>
                            </td>
                            -->
                  <td class="vtl-tbody-td">
                    <div>
                      <span>{{ obj.MORE_ITEMIMAGE_CNT }}</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td" style="text-align: left">
                    <div>
                      <span>{{ obj.ITNAME }}</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td">
                    <div>
                      <span>{{ obj.ITBASE_EABARCODE }}</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td" style="text-align: left">
                    <div>
                      <span>{{ obj.ITSTAN }}</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td" style="text-align: end">
                    <div>
                      <span>{{ obj.AMOUNT }}원</span>
                    </div>
                  </td>
                  <td class="vtl-tbody-td" style="text-align: end">
                    <div>
                      <span>{{ obj.ITEASDAN }}원</span>
                    </div>
                  </td>
                  <!-- <td class="vtl-tbody-td">
                    <div>
                      <span>{{ obj.ADD_INFO }}</span>
                    </div>
                  </td> -->
                  <td class="vtl-tbody-td">
                    <div>
                      <span
                        ><input
                          type="button"
                          value="미리보기"
                          @click="previewImage(obj.ITCODE)"
                      /></span>
                    </div>
                  </td>
                </tr>
                <tr v-else>
                  <td :colspan="tableSet.columns.length">
                    <span>no data</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- 더보기버튼 -->
        <div class="item__buttons" v-if="tableSet.isShowMoreBtn" style="gap: 0px">
          <button @click="searchMoteBtn" style="margin: 0px">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
        </div>
      </div>
      <!-- 상세정보 영역(기능 미구현) -->
      <div class="part__data_detail" v-if="detailShow">
        <div class="item__title">
          <i class="fa-solid fa-angle-right item__angle"></i>
          <span>상세보기</span>
        </div>
        <div class="item__contents">
          <div>
            <p>거래처명</p>
            <input type="text" v-model="selectRowData.COMPANY_NAME" />
          </div>
          <div>
            <p>사업자번호</p>
            <input type="text" v-model="selectRowData.COMPANY_CORPNO" />
          </div>
          <div>
            <p>사용자</p>
            <input type="text" v-model="selectRowData.USER_NAME" />
          </div>
          <div>
            <p>휴대폰번호</p>
            <input type="text" v-model="selectRowData.USER_PHONE" />
          </div>
          <div>
            <p>이메일</p>
            <input type="text" v-model="selectRowData.EMAIL" />
          </div>
          <div>
            <p>가입여부</p>
            <div class="radio_group">
              <label>
                <input
                  type="radio"
                  id="radio1-1"
                  value="20"
                  v-model="selectRowData.STATUS"
                /><span for="radio1-1">승인</span>
              </label>
              <label>
                <input
                  type="radio"
                  id="radio1-2"
                  value="99"
                  v-model="selectRowData.STATUS"
                /><span for="radio1-2"> 거절</span>
              </label>
            </div>
          </div>
          <div>
            <p>선결제필수 여부</p>
            <div class="radio_group">
              <label>
                <input
                  type="radio"
                  id="radio2-1"
                  value="Y"
                  v-model="selectRowData.PAY_YN"
                />
                <span for="radio2-1">예</span>
              </label>
              <label>
                <input
                  type="radio"
                  id="radio2-2"
                  value="N"
                  v-model="selectRowData.PAY_YN"
                  for="radio2-2"
                /><span>아니오</span>
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

    <!-- 이미지 편집 modal-->
    <div
      class="modal fade"
      id="modalProductManager"
      style="z-index: 1051 !important"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div
        class="modal-dialog modal-xl setModalCenter"
        style="margin: 0px auto"
      >
        <div class="modal-content">
          <!-- 모달헤더(타이틀/버튼) 영역 -->
          <div
            class="modal-header"
            style="
              padding: 0px 10px;
              border-bottom: 1px solid rgb(206, 206, 208);
              background-color: #8480fc;
              color: #ffffff;
            "
          >
            <div class="row" style="width: -webkit-fill-available">
              <div class="col-lg-12" style="margin: 0px 10px">
                <h5 style="border: 0px solid blue; margin: 0px auto">
                  {{ selectRowData.ITNAME }} / {{ selectRowData.ITMAKER }} /
                  {{ selectRowData.ITEASDAN }}원
                </h5>
              </div>
            </div>
            <div
              style="
                display: flex;
                justify-content: end;
                width: 300px;
                gap: 10px;
                margin: 10px;
              "
            >
              <a
                href="#"
                class="btn btn-white"
                @click="imageTotalSave"
                style="padding: 5px 20px"
                ><i class="fa fa-circle-check fa-fw"></i>저장</a
              >
              <a
                href="#"
                class="btn btn-white"
                @click="closeImage"
                style="padding: 5px 20px"
                ><i class="fa fa-circle-xmark fa-fw"></i>닫기</a
              >
              <a
                href="#"
                class="btn btn-white"
                v-show="false"
                id="imageClose"
                data-bs-dismiss="modal"
                ><i class="fa fa-circle-xmark fa-fw"></i>닫기</a
              >
            </div>
          </div>
          <!-- 모달바디(내용) 영역 -->
          <div class="modal-body" style="padding: 10px 0px 0px 0px">
            <div class="row">
              <!--왼쪽 -- 대표이미지, 추가이미지 영역-->
              <div class="col-lg-4">
                <div class="" style="">
                  <!--대표이미지 영역-->
                  <h6
                    class="modal-title"
                    style="
                      border-width: 1px 1px 1px 15px;
                      border-style: solid;
                      border-color: #605aff;
                      color: #605aff;
                      padding: 0 0 0 5px;
                      margin: 0 0 0 10px;
                    "
                  >
                    대표이미지
                  </h6>
                  <div
                    class="room-file-upload-wrapper-m"
                    style="
                      display: flex;
                      flex-direction: column;
                      border: 0px solid #605aff;
                      width: auto;
                    "
                  >
                    <!-- 이미지가 없을때 -->
                    <div
                      v-if="!masterFiles.length"
                      class="room-file-upload-example-container"
                      style="justify-content: left"
                    >
                      <div class="room-file-upload-example">
                        <div class="file-preview-wrapper-upload">
                          <div class="image-box" @click="imageChange('master')">
                            <label
                              for="masterFiles"
                              style="background-color: #aa42ff"
                              >+</label
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- 이미지가 있을때 -->
                    <div
                      v-else
                      class="file-preview-content-container"
                      style="display: flex; justify-content: left"
                    >
                      <div
                        class="file-preview-container"
                        style="justify-content: left"
                      >
                        <div
                          v-for="(file, index) in masterFiles"
                          :key="index"
                          class="file-preview-wrapper"
                        >
                          <div
                            class="file-close-button"
                            @click="fileDeleteButton(file, 'master')"
                            :name="file.SEQ"
                          >
                            X
                          </div>
                          <img
                            :src="file.URL"
                            id="masterImage"
                            @click="setViewImage(file?.URL)"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  style="
                    margin-top: 20px;
                    height: 510px;
                    border: 0px solid red;
                    position: relative;
                  "
                >
                  <!--추가이미지 영역-->
                  <h6
                    class="modal-title"
                    style="
                      border-width: 1px 1px 1px 15px;
                      border-style: solid;
                      border-color: #ff5a5a;
                      color: #ff5a5a;
                      padding: 0 0 0 5px;
                      margin: 0 0 10px 10px;
                    "
                  >
                    추가이미지
                  </h6>
                  <div
                    class="room-file-upload-wrapper"
                    style="
                      display: flex;
                      flex-direction: column;
                      border: 0px solid blue;
                      height: auto;
                    "
                  >
                    <!-- 이미지가 없을때 -->
                    <div
                      v-if="!files.length"
                      class="room-file-upload-example-container"
                      style="display: flex; justify-content: left"
                    >
                      <div class="room-file-upload-example">
                        <div class="room-file-notice-item-green"></div>
                        <div
                          class="file-preview-wrapper-upload"
                          v-if="isFileShowMoreBtn"
                        >
                          <div class="image-box" @click="imageChange('sub')">
                            <label for="file">+</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- 이미지가 있을때 -->
                    <div
                      v-else
                      class="file-preview-content-container"
                      style="display: flex; justify-content: left"
                    >
                      <draggable
                        class="file-preview-container"
                        :list="files"
                        style="justify-content: left"
                      >
                        <div
                          v-for="(file, index) in files"
                          :key="index"
                          class="file-preview-wrapper"
                        >
                          <div
                            class="file-close-button"
                            @click="fileDeleteButton(file, 'sub')"
                            :name="file?.SEQ"
                          >
                            X
                          </div>
                          <img
                            :src="file?.URL"
                            @click="setViewImage(file?.URL)"
                          />
                        </div>
                      </draggable>
                    </div>
                    <!-- 이미지가 최대갯수 이하 일때 -->
                    <div
                      v-if="files.length > 0 && files.length < 3"
                      class="file-preview-content-container"
                      style="display: flex; justify-content: left"
                    >
                      <div
                        class="file-preview-wrapper-upload"
                        v-if="isFileShowMoreBtn"
                      >
                        <div class="image-box" @click="imageChange">
                          <label for="file">+</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    style="
                      background-color: #fc8080;
                      color: #ffffff;
                      padding: 5px;
                      position: absolute;
                      bottom: 0px;
                      margin-left: 5px;
                      width: 100%;
                      text-align: center;
                    "
                  >
                    드래그하여 순서를 변경하실 수 있습니다.
                  </div>
                </div>
              </div>
              <!--오른쪽 -- 미리보기/편집 영역-->
              <div class="col-lg-8">
                <!-- This division element needs the css height -->
                <div
                  style="
                    border: 1px solid rgb(133, 129, 129);
                    height: 730px;
                    padding: 0px;
                    margin-right: 10px;
                    margin-bottom: 10px;
                  "
                >
                  <ImageEdtior
                    v-if="isEditor"
                    ref="imageEditor"
                    @editorDownImage="addEditorImage"
                  />
                  <img
                    v-else
                    :src="showDetailImage"
                    style="width: 100%; height: 100%; object-fit: scale-down"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}

.code-description {
  padding: 22px 52px;
  background-color: rgba(81, 92, 230, 0.1);
  line-height: 1.4em;
}

.code-description,
.code-description a {
  font-family: Arial;
  font-size: 14px;
  color: #515ce6;
}

.code-html {
  padding: 20px 52px;
}


/* https://loy124.tistory.com/203        */
.main-container {
  width: 1200px;
  height: 400px;
  margin: 0 auto;
}

.room-deal-information-container {
  margin-top: 50px;
  color: #222222;
  border: 1px solid #dddddd;
}

.room-deal-information-title {
  text-align: center;
  line-height: 60px;
  border-bottom: 1px solid #dddddd;
}

.room-deal-information-content-wrapper {
  min-height: 50px;
  display: flex;
}

.room-deal-informtaion-content-title {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 150px;
  background-color: #f9f9f9;
}

.room-deal-information-content {
  width: 100%;
}

.room-deal-option-selector {
  display: flex;
  align-items: center;
  padding: 15px;
}

.room-deal-option-item {
  width: 100px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #cccccc;
  border-radius: 5px;
  cursor: pointer;
}

.room-deal-option-item:last-child {
  margin-left: 10px;
}

.room-deal-option-notice {
  margin-left: auto;
  color: #888888;
}

.room-deal-option-item-deposit {
  margin-left: 10px;
}

.room-deal-information-wrapper {
  display: flex;
  flex-direction: column;
}

.room-deal-information-option {
  padding: 10px;
  display: flex;
  align-items: center;
}

.room-deal-information-option:last-child {
  border-bottom: 1px solid #dddddd;
}

.room-deal-information-item-type {
  color: #fff;
  background-color: #61b6e5;
  min-width: 50px;
  height: 26px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 3px;
}

.room-deal-information-item-wrapper {
  display: flex;
  align-items: center;
  margin-left: 10px;
  height: 46px;
  width: 100%;
}

.room-deal-information-item-wrapper > input {
  border: 1px solid #dddddd;
  width: 140px;
  height: 100%;
  padding: 0 15px;
}

.room-deal-inforamtion-won {
  margin: 0 10px;
}

.room-deal-information-example {
  color: #888888;
}

.room-deal-information-option:not(:first-child) {
  margin-top: 10px;
}

.room-deal-inforamtion-divide {
  margin: 0 8px;
  color: #222222;
  font-weight: 100;
}

.room-deal-close-button-wrapper {
  margin-left: auto;
  cursor: pointer;
}

.room-deal-close-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  background-color: #666666;
  color: rgb(220, 220, 220);
}

.room-deal-cliked {
  background-color: rgb(235, 235, 235);
  color: rgb(170, 170, 170);
}

.room-file-upload-example {
  height: 100%;
}

.room-write-content-container {
  border-top: 1px solid #dddddd;
  min-height: 260px;
}

.room-picture-notice {
  margin: 20px;
  padding: 20px 40px;
  border: 1px solid #dddddd;
}

.file-preview-content-container {
  width: auto;
  height: 100%;
}

.room-file-upload-wrapper {
  border: 1px solid #dddddd;
  /* background-color: #f4f4f4; */
  /* min-height: 350px; */
  color: #888888;
  display: flex;
  align-items: left;
  justify-content: left;
  height: auto;
}

.room-file-upload-wrapper-m {
  border: 1px solid #dddddd;
  /* background-color: #f4f4f4; */
  color: #888888;
  display: flex;
  align-items: left;
  justify-content: left;
  height: auto;
  width: auto;
}

.room-file-upload-example-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: auto;
}

.room-file-image-example-wrapper {
  text-align: center;
}

.room-file-notice-item {
  margin-top: 5px;
  text-align: left;
}

.room-file-notice-item-green {
  color: green;
}

.image-box-m {
  margin-top: 30px;
  padding-bottom: 20px;
  text-align: center;
}

.image-box-m input[type="file"] {
  position: absolute;
  width: 0;
  height: 0;
  padding: 0;
  overflow: hidden;
  border: 0;
}

.image-box-m label {
  display: inline-block;
  padding: 10px 20px;
  background-color: #232d4a;
  color: #fff;
  vertical-align: middle;
  cursor: pointer;
  border-radius: 5px;
}

.image-box {
  margin: 20px auto;
  text-align: center;
}

.image-box input[type="file"] {
  position: absolute;
  width: 0;
  height: 0;
  padding: 0;
  overflow: hidden;
  border: 0;
}

.image-box label {
  display: inline-block;
  padding: 2px 20px;
  background-color: #ff4f42;
  font-size: 1.5rem;
  font-weight: 600;
  color: #fff;
  vertical-align: middle;
  cursor: pointer;
  border-radius: 30px;
  box-shadow: 5px 5px 5px #c3c3c3;
}

.file-preview-wrapper {
  padding: 10px;
  position: relative;
}

.file-preview-wrapper > img {
  position: relative;
  width: 150px;
  height: 150px;
  border: 1px solid lightgrey;
  z-index: 10;
  object-fit: scale-down;
  box-shadow: 5px 5px 5px #d7d7d7;
  padding: 2px;
  cursor: pointer;
}

.file-preview-wrapper-m > img {
  position: relative;
  width: 400px;
  height: 300px;
  border: 1px solid lightgrey;
  z-index: 10;
  padding: 10px;
  object-fit: scale-down;
  box-shadow: 5px 5px 5px #d7d7d7;
  padding: 2px;
  cursor: pointer;
}

.file-close-button {
  position: absolute;
  /* align-items: center; */
  line-height: 18px;
  z-index: 99;
  right: 0px;
  top: 2px;
  color: #fff;
  font-weight: 600;
  background-color: #ff0000;
  border-radius: 15px;
  border: 1.5px solid #ffffff;
  width: 25px;
  height: 25px;
  text-align: center;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 5px 5px 5px #00000052;
}

.file-preview-container {
  height: 100%;
  display: flex;
  flex-wrap: wrap;
}

.file-preview-wrapper-upload {
  margin: 10px;
  padding-top: 20px;
  /* background-color: #FFFFFF; */
  width: 150px;
  height: 150px;
}

.room-write-button-wrapper {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #222222;
}

.room-write-button-wrapper > div {
  width: 160px;
  height: 50px;
  border: 1px solid #dddddd;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.room-write-button {
  margin-left: 15px;
  color: #fff;
  background-color: #1564f9;
}

.room-write-button:hover {
  opacity: 0.8;
}
.cropper-area {
  width: 1000px;
  margin: 10px 10px 10px 10px;
}
</style>