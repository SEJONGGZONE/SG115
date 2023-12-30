<script setup lang="ts">
import { ref, computed, reactive, onMounted, defineComponent } from 'vue'
import highlightjs from '@/components/plugins/Highlightjs.vue'
import vueTable from '@/components/plugins/VueTable.vue'
import navscrollto from '@/components/app/NavScrollTo.vue'
import ImageEdtior from '@/components/image/ImageEdtior.vue'
import { VueDraggableNext as draggable } from 'vue-draggable-next'
import { memberApi, productApi } from '@/api'
import { utils } from '@/common/utils.ts'

import { useAppOptionStore } from '@/stores/app-option'
const appOption = useAppOptionStore()
//사용자 및 페이지 정보
const userInfo = JSON.parse(sessionStorage.getItem('userInfo'))
const userClcode = userInfo.CLCODE
const userId = userInfo.ID
const pageNumber = ref(1)
const pageSize = ref(20)

import { useAlert } from '@/composables/showAlert'
const { showAlert, showAlertSuccess, showConfirm } = useAlert()
//검색어
let searchKeyword = ''

//우측 상세보기 영역 노출여부
let detailShow = ref(true)

//메인 그리드에서 선택한 row 정보
let rowTargetObj = ''
let selectRowData = ref({})

const table = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: 'NO',
      field: 'RANK',
      width: '5%',
      sortable: false,
    },
    {
      label: '거래처명',
      field: 'COMPANY_NAME',
      width: '15%',
      sortable: true,
      isKey: true,
      class: 'pointer',
    },
    {
      label: '사업자번호',
      field: 'COMPANY_CORPNO',
      width: '10%',
      sortable: true,
      isKey: true,
    },
    {
      label: '사용자명',
      width: '10%',
      //sortable: true,
    },
    {
      label: '휴대폰번호',
      width: '10%',
      //sortable: true,
    },
    {
      label: '이메일',
      field: 'EMAIL',
      width: '20%',
      //sortable: true,
    },
    {
      label: '개인/사업자',
      field: 'TYPE',
      width: '10%',
      //sortable: true,
    },
    {
      label: '첨부파일',
      field: 'FILE_URL',
      width: '10%',
      //sortable: true,
    },
    {
      label: '승인여부',
      field: 'STATUS_NM',
      width: '10%',
      //sortable: true,
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: 'GEONUM',
    sort: 'asc',
  },
  isShowMoreBtn: false,
})

/* 첨부파일 이미지 */
let files = ref([])
let imageFiles = ref(null) //업로드용 파일

const imageEditor = ref(null) //이미지 에디터 컴포넌트
const isEditor = ref(false) //이미지 편집 여부
const isImageChange = ref(false)
const imageEditorType = ref('master') //master , sub
const showDetailImage = ref('/assets/img/logo/noimg.png')

const isFileShowMoreBtn = computed(() => {
  return files.value.length < 1
})


//////////////api 통신
onMounted(() => {
  pageNumber.value = 1
  doSearch()
  setTimeout(() => {
    const arrPrvModels = document.getElementById('modal_insert_area').getElementsByClassName('modal')
    for (var i = 0; i < arrPrvModels.length; i++) {
      // 기존요소 삭제
      document.getElementById('modal_insert_area').removeChild(arrPrvModels[i])
    }
    const arrModels = document.getElementsByClassName('section')[0].getElementsByClassName('modal')
    for (var i = 0; i < arrModels.length; i++) {
      // 신규요소 추가
      document.getElementById('modal_insert_area').append(arrModels[i])
    }
  }, 500)
});


/******************************************************************************* api 호출 start */
const doSearch = async () => {
  //조회

  if (table.isLoading) return

  table.isLoading = true
  table.isShowMoreBtn = false
  
  utils.startLodingBar()
  const param = {
    clcode: '',
    searchKeyword: searchKeyword ?? '',
    pageSize: pageSize.value,
    pageNumber: pageNumber.value,
  }

  let data
  try {
    const dataObj = await memberApi.memberMng(param)
    data = dataObj.data
    table.isLoading = false
    if (data.RecordCount > 0) {
      table.rows.push(...data.RecordSet)
      if (data.RecordCount === pageSize.value) {
        table.isShowMoreBtn = true
      }
    } else {
      table.isShowMoreBtn = false
    }
  } catch (error) {
    console.error(error)
  } finally {
    utils.removeLodingBar()
    table.isLoading = false
    var memberListDiv = document.getElementById('memberListDiv')
    setTimeout(() => {
      memberListDiv.scrollTop = memberListDiv.scrollHeight
    }, 100)
  }
}


const doSave = async () => {
  //저장

  const param = {
    geonum: selectRowData.value.GEONUM,
    clcode: '', // selectRowData.value.CLCODE,
    userName: selectRowData.value.USER_NAME ?? '',
    userPhone: selectRowData.value.USER_PHONE ?? '',
    companyName: selectRowData.value.COMPANY_NAME ?? '',
    companyCorpno: selectRowData.value.COMPANY_CORPNO ?? '',
    password: selectRowData.value.PASSWORD ?? '',
    email: selectRowData.value.EMAIL ?? '',
    status: selectRowData.value.STATUS ?? '',
    osType: selectRowData.value.OS_TYPE ?? '',
    joinType: '002',
    inputUser: userId,
    payYn: selectRowData.value.PAY_YN ?? '',
  }

  let data
  try {
    const dataObj = await memberApi.memberMngSave(param)
    data = dataObj.data
    if (data.ResultCode === '00') {
      showAlertSuccess('저장되었습니다.')
      table.rows = []
      doSearch()
    }
  } catch (error) {
    console.error(error)
  }
}

const fileDbSave = async (param) => {
  //파일 이미지 DB에 저장
  const params = {
    userNo: param.userNo,
    fileno: param.fileno,
  }
  return await memberApi.userImageSave(params)
}
const saveImage = async (params) => {
  return new Promise(async (resolve, reject) => {
    const file = params.file
    const url = params.url
    let formData = new FormData()
    formData.append('files', file)
    const data = await productApi.fileUpload(formData, url)

    if (data.data.ResultCode === '00') {
      isImageChange.value = true
      setTimeout(() => {
        utils.removeProgressbar()
      }, 5000)
    } else {
      showAlert('통신 이슈로 인해 저장되지 않았습니다. 재시도 바랍니다.')
      utils.removeProgressbar()
      reject()
    }
    resolve(data)
  })
}

const selectImageList = async () => {
  isEditor.value = false
  if (selectRowData.value.FILE_URL) {
    files.value = [{ SEQ: 1, URL: selectRowData.value.FILE_URL }]
    showDetailImage.value = selectRowData.value.FILE_URL
  } else {
    files.value = []
    showDetailImage.value = '/assets/img/logo/noimg.png'
  }
  isImageChange.value = false
}

/******************************************************************************* api 호출 end */

/******************************************************************************* 버튼 및 액션 이벤트 start */
const setViewImage = (imageUrl) => {
  console.log('--------------------- setViewImage,0')
  isEditor.value = false
  showDetailImage.value = imageUrl
}
const callSaveImage = (file, type) => {
  // API 호출 로직
  // 여기에 실제 API 호출 코드를 작성합니다.
  // API 호출이 비동기적으로 처리되어야 하는 경우, Promise 또는 async/await를 사용하여 비동기 처리를 구현해야 합니다.
  // 아래의 예제에서는 간략하게 setTimeout 함수를 사용하여 비동기적인 동작을 흉내내었습니다.

  return new Promise(async (resolve, reject) => {
    const fileInfo = file
    const params = makeFileInfo(fileInfo, type)
    const fileData = await saveImage(params)
    resolve(fileData) // API 호출 완료 후 resolve 호출
  })
}
const makeFileInfo = (fileInfo, type) => {
  //103 : 대표이미지 , 104 : 추가이미지
  console.log('--------------------- makeFileInfo,0')

  const fileFormat = fileInfo.file.type?.split('/')?.[1]
  var d = new Date()
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  const hours = String(d.getHours()).padStart(2, '0') // 시
  const minutes = String(d.getMinutes()).padStart(2, '0') // 분
  const seconds = String(d.getSeconds()).padStart(2, '0') // 초

  const randomNumber = Math.floor(Math.random() * 1000)
  const randomString = randomNumber.toString().padStart(3, '0')
  const memberType = selectRowData.value.GEONUM

  let fileName = `I_${memberType}_${year}${month}${day}${hours}${minutes}${seconds}_${randomString}` //사업자 이미지 번호 (I_3202200508_20230901)
  let url = `/join_web/${fileFormat}/${false}/${fileName}`
  return {
    url,
    file: fileInfo.file,
  }
}
const imageAddUpload = async () => {
  console.log('--------------------- imageAddUpload,0')
  for (let i = 0; i < files.value.length; i++) {
    files.value[i].SEQ = i + 1
  }
  for (let i = 0; i < files.value.length; i++) {
    const fileInfo = files.value[i]

    if (!fileInfo?.file) {
      continue
    }
    await callSaveImage(fileInfo, '104').then((saveImage) => {
      console.log(saveImage)
      files.value[i].FILE_NO = saveImage.data.fileDetails[0].FileNo
    })
  }
}
const closeImage = () => {
  document.getElementById('imageClose').click()
}
const imageTotalSave = async () => {
  console.log('--------------------- imageTotalSave,0')

  // if(files.value.length === 0){
  //   showAlert("추가 이미지가 없습니다.")
  //     return
  // }

  await imageAddUpload()
  console.log('--------------------- imageTotalSave,1')

  const tempList = []
  files.value.map((item) => tempList.push(item))
  const params = await makeImageParams(tempList)
  console.log('--------------------- imageTotalSave,2')

  const saveFileObj = await fileDbSave(params)
  const saveFile = saveFileObj.data

  if (saveFile.ResultMsg === 'SUCCESS') {
    isImageChange.value = true
    showAlertSuccess('이미지 저장이 완료되었습니다.')
    closeImage() // 이미지 팝업닫기..
    clickSearch() // 통합검색 요청..
    //document.getElementById("imageClose").click()
  } else {
    showAlert('이미지 저장에 실패했습니다.')
  }
}

const makeImageParams = (dataList) => {
  console.log('--------------------- makeImageParams,0')
  let type = ''
  let seq = ''
  let fileno = ''
  dataList.map((item) => {
    fileno = fileno + item.FILE_NO + '/'
    // seq = seq + item.SEQ + '/'
    // type = type + item.TYPE + '/'
  })
  let params = {}
  params.userNo = selectRowData.value.GEONUM
  // params.type = type.replace(/\/$/, '')
  // params.seq = seq.replace(/\/$/, '')
  params.fileno = fileno.replace(/\/$/, '')
  return params
}

let saveImages = ref([])
const imageChange = async (edtiorType) => {
  console.log('--------------------- imageChange,0')
  imageEditorType.value = edtiorType
  isEditor.value = true
  setTimeout(() => {
    imageEditor.value.inputClick()
  }, 100)
}

const fileDeleteButton = (file, type) => {
  console.log('--------------------- fileDeleteButton,0')
  showDetailImage.value = '/assets/img/logo/noimg.png'
  deleteImage(file, type)
}
const deleteImage = async (selectImge, type) => {
  try {
    isImageChange.value = true
    files.value = []
    // if (selectImge.FILE_NO) {
    //   files.value = files.value.filter((data) => !(data.FILE_NO === selectImge.FILE_NO))
    // } else {
    //   files.value = files.value.filter((data) => !(data.SEQ === selectImge.SEQ))
    // }
  } catch (error) {
    console.error(error)
  }
}
const addEditorImage = (imageFile) => {
  console.log('--------------------- addEditorImage,0')

  let tempImages = []
  const itCode = selectRowData.value.COMPANY_CORPNO

  if (files.value.length > 0) {
    showAlert('파일 첨부는 1개까지만 가능합니다.')
    return
  }

  let startIndex = files.value.length + 1

  tempImages = {
    //실제 파일
    file: imageFile,
    //이미지 프리뷰
    URL: URL.createObjectURL(imageFile),
    SEQ: startIndex,
    TYPE: selectRowData.value.TYPE,
    ITCODE: itCode,
  }
  saveImages.value.push(tempImages)
  files.value = [
    ...files.value,
    //이미지 업로드
    tempImages,
  ]
}
const tableLoadingFinish = (elements) => {
  table.isLoading = false
}

const handleRowClick = (rowData, event) => {
  //좌측 메인 그리드 row click event

  if (rowTargetObj) {
    rowTargetObj.classList.remove('table-good')
  }
  selectRowData.value = JSON.parse(JSON.stringify(rowData))
  detailShow.value = true
  selectImageList()
  rowTargetObj = event.target.closest('tr')
  rowTargetObj.classList.add('table-good')
}
const cancleBtn = (rowData) => {
  //닫기 Btn
  if (rowTargetObj) {
    rowTargetObj.classList.remove('table-good')
  }
  detailShow.value = false
}
const saveBtn = (rowData) => {
  //저장 Btn
  doSave()
}
const searchMoteBtn = () => {
  //더보기 Btn
  pageNumber.value = pageNumber.value + 1
  doSearch()
}
const setHIde = () => {
  appOption.appHeaderHide = !appOption.appHeaderHide
  appOption.appContentFullHeight = !appOption.appContentFullHeight
}
// 이미지 클릭시..
const handleImageClick = async () => {
  document.getElementById('modalImageManagerDiv').click()
}

// 검색창에서 키보드 입력시..
const searchBtnEnter = (event) => {
  //검색 Btn
  if (event.keyCode != 13) return;

  // 1. 목록 지우기
  table.rows = []
  // 2. 목록 요청하기
  doSearch();
};

// 통합검색 버튼 클릭시..
const clickSearch = async () => {
  // 1. 목록 지우기
  table.rows = []
  // 2. 상세영역 초기화
  selectRowData = ref({})
  // 3. 페이지 번호 초기화
  pageNumber.value = 1
  // 4. 목록 요청하기
  doSearch();
};

//////////////함수


</script>

<template>
  <div class="section section__management" style="gap: 2px">
    <!-- <div class="group__title">
      <h2>사용자 관리</h2>
    </div> -->
    <div class="group__contents_sungchang">
      <!-- 메인데이타 -->
      <div class="part__data_list left_side" style="flex: unset; height: auto;">
        <div class="left_side_detail_title">
          <i class="icon-list"></i>&nbsp;&nbsp;사용자관리 - 사용자(거래처) 관리
        </div>
        <!-- 검색어 -->
        <div class="grid_searcharea" style="border-bottom: 1px solid #0b048678;">
          <input type="text" v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="거래처명,주소,전화번호,코드 조회가능.."/>
          <div class="item__buttons" style="width: 5rem;">
            <button class="btn_search" @click="clickSearch">
              <i class="fa-regular fa-circle-check"></i><span>통합검색</span>
            </button>
          </div>
        </div>
        <div class="item__scroll" id="memberListDiv" style="margin-bottom: 1rem;">
          <div class="unit__scroll">
            <table>
              <!-- 리스트헤더(타이틀) -->
              <thead>
                <tr>
                  <th v-for="(col, index) in table.columns" :key="index" :style="'width :' + col.width">
                    <div class="unit_bundle">
                      {{ col.label }}
                      <div class="unit__buttons" v-if="col.sortable">
                        <button :class="table.sortable.order === col.field && table.sortable.sort === 'asc' ? 'active' : ''" @click.prevent="col.sortable ? utils.doSort(col.field, table.sortable, table.rows) : false"><i class="fa-solid fa-angle-up"></i></button>
                        <button :class="table.sortable.order === col.field && table.sortable.sort === 'desc' ? 'active' : ''" @click.prevent="col.sortable ? utils.doSort(col.field, table.sortable, table.rows) : false"><i class="fa-solid fa-angle-down"></i></button>
                      </div>
                    </div>
                  </th>
                </tr>
              </thead>
              <!-- 리스트본문 -->
              <tbody>
                <!-- <tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' :  ''" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="utils.addHoverClassToTr" @mouseleave="utils.removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
							<td :class="col.class" v-for="(col, j) in table.columns" :key="j" v-html="obj[col.field]" style="overflow: hidden; text-overflow: ellipsis;" ></td> 
							</tr> -->
                <tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' : obj.GEONUM%2 === 0?'link':''" 
                    v-if="table.rows.length > 0" 
                    v-for="(obj, index) in table.rows" 
                    :key="index" 
                    @mouseenter="utils.addHoverClassToTr" 
                    @mouseleave="utils.removeHoverClassFromTr" 
                    @click="handleRowClick(obj, $event)">
                  <td>
                    <div>
                      <span>{{ obj.RANK }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span>{{ obj.COMPANY_NAME }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span>{{ obj.COMPANY_CORPNO }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span>{{ obj.USER_NAME }}</span>
                    </div>
                  </td>
                  <td style="text-align: left">
                    <div>
                      <span>{{ obj.USER_PHONE }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.EMAIL }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.TYPE === '10' ? '개인' : '사업자' }}</span>
                    </div>
                  </td>
                  <td>
                    <div>
                      {{ obj.TYPE === '10' ? '' : obj.FILE_URL ? 'Y' : '' }}
                    </div>
                  </td>
                  <td>
                    <div>
                      <span>{{ obj.STATUS_NM }}</span>
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

        
        <!-- 더보기버튼 -->
        <div class="item__buttons" v-if="table.isShowMoreBtn" style="gap: 0px; margin-bottom: -0.2rem;">
          <button @click="searchMoteBtn" style="margin: 0px">
            <i class="fa-solid fa-plus"></i>더보기
          </button>
        </div>

      </div>
      <div id="dragMe" class="resizer_h" @mousedown="mouseDownHandlerForDrag($event)"></div>
      <!-- 상세 -->
      <div class="part__data_detail right_side" style="height: auto" v-if="detailShow">
          <div class="right_side_detail_title">
            <i class="icon-note"></i>&nbsp;&nbsp;사용자(거래처) 상세정보
          </div>
          <div class="item__contents_sungchang">
          <div>
            <label class="form-label">거래처명</label><br />
            <input readonly type="text" v-model="selectRowData.COMPANY_NAME"/>
          </div>
          <div>
            <label class="form-label">사업자번호</label><br />
            <input readonly type="text" v-model="selectRowData.COMPANY_CORPNO" />
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
            <label class="form-label">아이디</label><br />
            <input type="text" v-model="selectRowData.ID" />
          </div>
          <div>
            <label class="form-label">비밀번호</label><br />
            <input type="text" v-model="selectRowData.PASSWORD" />
          </div>
          <div>
            <label class="form-label">가입여부</label><br />
            <div class="radio_group">
              <label> <input type="radio" id="radio1-1" value="10" v-model="selectRowData.STATUS" /><span for="radio1-1">신규</span> </label>
              <label> <input type="radio" id="radio1-2" value="20" v-model="selectRowData.STATUS" /><span for="radio1-2">승인</span> </label>
              <label> <input type="radio" id="radio1-3" value="99" v-model="selectRowData.STATUS" /><span for="radio1-3">차단</span> </label>
            </div>
          </div>
          <div>
            <label class="form-label">사업자등록증</label><br />
            <img :src="showDetailImage" @click="handleImageClick" />
            <button @click="handleImageClick" class="btn btn-outline-primary me-1 mb-1 btn_file" style="background-color: #ffffff9b;">
              <i class="fa-solid fa-plus"></i> 파일첨부
            </button>
          </div>
          <!-- <div>
					<p>선결제필수 여부</p>
					<div class="radio_group">
					<label>
						<input type="radio"   id="radio2-1" value="Y" v-model="selectRowData.PAY_YN"/> <span for="radio2-1">예</span>
					</label>
					<label>
						<input type="radio" id="radio2-2" value="N" v-model="selectRowData.PAY_YN"  for="radio2-2"/><span
						>아니오</span
						>
					</label>
					</div>
				</div> -->
        </div>
        <div class="item__buttons">
          <button class="btn_close" @click="cancleBtn"><i class="fa-regular fa-circle-xmark"></i><span>닫기</span></button>
          <button class="btn_save" @click="saveBtn"><i class="fa-regular fa-circle-check"></i><span>저장</span></button>
        </div>
      </div>
    </div>
    <div id="modalImageManagerDiv" data-bs-toggle="modal" data-bs-target="#modalImageManager" style="display: none" data-backdrop="static" data-keyboard="false" />
    <!-- 추가정보 modal-->
    <div class="modal fade" id="modalImageManager" style="z-index: 1051 !important" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-xl setModalCenter" style="margin: 0px auto">
        <div class="modal-content">
          <!-- 모달헤더(타이틀/버튼) 영역 -->
          <div class="modal-header" style="padding: 0px 10px; border-bottom: 1px solid rgb(206, 206, 208); background-color: #8480fc; color: #ffffff">
            <div class="row" style="width: -webkit-fill-available">
              <div class="col-lg-12" style="margin: 0px 10px">
                <h5 style="border: 0px solid blue; margin: 0px auto">사업자 서류 첨부</h5>
              </div>
            </div>
            <div style="display: flex; justify-content: end; width: 300px; gap: 10px; margin: 10px">
              <a href="#" class="btn btn-white" @click="imageTotalSave" style="padding: 5px 20px"><i class="fa fa-circle-check fa-fw"></i>저장</a>
              <a href="#" class="btn btn-white" @click="closeImage" style="padding: 5px 20px"><i class="fa fa-circle-xmark fa-fw"></i>닫기</a>
              <a href="#" class="btn btn-white" v-show="false" id="imageClose" data-bs-dismiss="modal"><i class="fa fa-circle-xmark fa-fw"></i>닫기</a>
            </div>
          </div>
          <!-- 모달바디(내용) 영역 -->
          <div class="modal-body" style="padding: 10px 0px 0px 0px">
            <div class="row">
              <div class="col-lg-4">
                <div style="margin-top: 20px; height: 510px; border: 0px solid red; position: relative">
                  <h6 class="modal-title" style="border-width: 1px 1px 1px 15px; border-style: solid; border-color: #ff5a5a; color: #ff5a5a; padding: 0 0 0 5px; margin: 0 0 10px 10px">이미지 등록</h6>
                  <div class="room-file-upload-wrapper" style="display: flex; flex-direction: column; border: 0px solid blue; height: auto">
                    <!-- 이미지가 없을때 -->
                    <div v-if="!files.length" class="room-file-upload-example-container" style="display: flex; justify-content: left">
                      <div class="room-file-upload-example">
                        <div class="room-file-notice-item-green"></div>
                        <div class="file-preview-wrapper-upload" v-if="isFileShowMoreBtn">
                          <div class="image-box" @click="imageChange('sub')">
                            <label for="file">+</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- 이미지가 있을때 -->
                    <div v-else class="file-preview-content-container" style="display: flex; justify-content: left">
                      <draggable class="file-preview-container" :list="files" style="justify-content: left">
                        <div v-for="(file, index) in files" :key="index" class="file-preview-wrapper">
                          <div class="file-close-button" @click="fileDeleteButton(file, 'sub')" :name="file?.SEQ">X</div>
                          <img :src="file?.URL" @click="setViewImage(file?.URL)" />
                        </div>
                      </draggable>
                    </div>
                    <!-- 이미지가 최대갯수 이하 일때 -->
                    <div v-if="files.length > 0 && files.length < 1" class="file-preview-content-container" style="display: flex; justify-content: left">
                      <div class="file-preview-wrapper-upload" v-if="isFileShowMoreBtn">
                        <div class="image-box" @click="imageChange">
                          <label for="file">+</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <div style="background-color: #fc8080; color: #ffffff; padding: 5px; position: absolute; bottom: 0px; margin-left: 5px; width: 100%; text-align: center">드래그하여 순서를 변경하실 수 있습니다.</div> -->
                </div>
              </div>
              <!--오른쪽 -- 미리보기/편집 영역-->
              <div class="col-lg-8">
                <div style="border: 1px solid rgb(133, 129, 129); height: 750px; padding: 0px; margin-right: 10px; margin-bottom: 10px">
                  <ImageEdtior v-if="isEditor" ref="imageEditor" @editorDownImage="addEditorImage" />
                  <img v-else :src="showDetailImage" style="width: 100%; height: 100%; object-fit: scale-down" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- CSS 전역 -->
<style>
tr {
  cursor: pointer;
}


.sample_color {
  color: #8542d1;
}
</style>

<!-- CSS 영역한정(좌우조절용) -->
<style scoped>
.left_side {
  width: 75%;
  /* 중앙 정렬 */
  display: flex;
  /*justify-content: center;*/
  min-width: 25%;
}
.right_side {
  flex: 1;
  /* 중앙 정렬 */
  display: flex;
  justify-content: center;
  min-width: 25%;
}
</style>
