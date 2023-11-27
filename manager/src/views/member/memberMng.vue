<script setup>
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
const pageSize = ref(15)

import { useAlert } from '@/composables/showAlert'
const { showAlert, showAlertSuccess, showConfirm } = useAlert()
//검색어
let searchKeyword = ''

//우측 상세보기 영역 노출여부
let detailShow = ref(false)

//메인 그리드에서 선택한 row 정보
let rowTargetObj = ''
let selectRowData = ref(null)

const table = reactive({
  isLoading: false,
  isReSearch: false,
  columns: [
    {
      label: 'NO',
      field: 'RANK',
      width: '5%',
      sortable: true,
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

/******************************************************************************* onMounted */
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
  }, 1000)
})

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
    data = await memberApi.memberMng(param)
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
    var productDiv = document.getElementById('productDiv')
    setTimeout(() => {
      productDiv.scrollTop = productDiv.scrollHeight
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
    data = await memberApi.memberMngSave(param)
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
  const memberType = selectRowData.value.TYPE

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

  const tempList = []
  files.value.map((item) => tempList.push(item))
  const params = await makeImageParams(tempList)

  const saveFile = await fileDbSave(params)

  if (saveFile.ResultMsg === 'SUCCESS') {
    isImageChange.value = true
    showAlertSuccess('이미지 저장이 완료되었습니다.')
    closeImage()
    searchBtn()
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
const searchBtn = () => {
  //검색 Btn
  table.rows = []
  detailShow.value = false
  pageNumber.value = 1
  doSearch()
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

const handleImageClick = async () => {
  document.getElementById('modalImageManagerDiv').click()
}

/******************************************************************************* 버튼 및 액션 이벤트 end */
</script>
<template>
  <div class="section section__management">
    <div class="group__search">
      <div class="part__search_box">
        <div class="group__title">
          <h2>회원관리</h2>
        </div>
        <input type="text" v-on:keyup.enter="searchBtn()" v-model="searchKeyword" placeholder="검색어를 입력하세요" />
        <button @click="searchBtn()"><i class="fa-solid fa-magnifying-glass"></i><span>검색</span></button>
      </div>
    </div>
    <div class="group__contents">
      <div class="part__data_list">
        <div class="item__scroll" id="productDiv">
          <div class="unit__scroll">
            <table>
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
              <tbody>
                <!-- <tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' :  ''" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="utils.addHoverClassToTr" @mouseleave="utils.removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
							<td :class="col.class" v-for="(col, j) in table.columns" :key="j" v-html="obj[col.field]" style="overflow: hidden; text-overflow: ellipsis;" ></td> 
							</tr> -->
                <tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' : ''" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="utils.addHoverClassToTr" @mouseleave="utils.removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
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

        <div class="item__buttons" v-if="table.isShowMoreBtn">
          <button @click="searchMoteBtn"><i class="fa-solid fa-plus"></i>더보기</button>
        </div>
      </div>
      <div class="part__data_detail" v-if="detailShow">
        <!-- <div class="item__title" >
				<i class="fa-solid fa-angle-right item__angle"></i>
				<span>상세보기</span>
				</div> -->
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
              <label> <input type="radio" id="radio1-1" value="10" v-model="selectRowData.STATUS" /><span for="radio1-1">신규가입</span> </label>
              <label> <input type="radio" id="radio1-2" value="20" v-model="selectRowData.STATUS" /><span for="radio1-2">승인</span> </label>
              <label> <input type="radio" id="radio1-3" value="99" v-model="selectRowData.STATUS" /><span for="radio1-3"> 거절</span> </label>
            </div>
          </div>

          <div v-if="selectRowData.TYPE === '20'">
            <label class="form-label">첨부서류</label><br />
            <button type="button" class="btn btn-outline-primary me-1 mb-1 icon-paper-clip" style="background-color: #348fe2; color: #ffffff" @click="handleImageClick">사업자 등록증</button>
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

<style scoped>
.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
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

.image-box-m input[type='file'] {
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

.image-box input[type='file'] {
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