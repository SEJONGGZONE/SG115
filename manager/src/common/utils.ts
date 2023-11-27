import { useAppOptionStore } from '@/stores/app-option'
import { useAppVariableStore } from '@/stores/app-variable'
import * as XLSX from 'xlsx'
const appVariable = useAppVariableStore()
const appOption = useAppOptionStore()

import axios from 'axios'
const instance = axios.create({
  baseURL: '/cvoapi/DatabaseController/runProcedure',
  timeout: 50000,
  headers: {
    'Content-Type': 'application/json',
    CodyApiKey: '1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=',
  },
})

const instanceFile = axios.create({
  baseURL: '/cvoapi',
  timeout: 50000,
  headers: {
    'Content-Type': 'application/json',
    CodyApiKey: '1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=',
  },
})
// let apiType = '/api'
// if (location.port === '33790' || ) {
//   apiType = '/cvoapi'
// }
const instanceCVO = axios.create({
  baseURL: '/cvoapi/DatabaseController/runProcedure',
  timeout: 50000,
  headers: {
    'Content-Type': 'application/json',
    CodyApiKey: '1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=',
  },
})
const instanceCVOUtil = axios.create({
  baseURL: '/api/GzoneUtilController',
  timeout: 50000,
  headers: {
    'Content-Type': 'application/json',
    CodyApiKey: '1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=',
  },
})
const instanceERP = axios.create({
  baseURL: '/DatabaseController/runProcedure',
  timeout: 50000,
  headers: {
    'Content-Type': 'application/json',
    CodyApiKey: '1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=',
  },
})

/**
 * request 인터셉터
 */
instance.interceptors.request.use(
  function (config) {
    // console.log("interceptos111")
    return config
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error)
  },
)

/**
 * response 인터셉터
 */
instance.interceptors.response.use(
  function (data) {
    // console.log("interceptos2222")
    return data.data
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error)
  },
)

function getAxios() {
  return instance
}
function getAxiosFile() {
  return instanceFile
}
function getAxiosCVO() {
  return instanceCVO
}
function getAxiosCVOUtil() {
  return instanceCVOUtil
}
function getAxiosERP() {
  return instanceERP
}

function startLoadingBar() {
  //진행중(로딩바)+딤드 추가
  appOption.isDimmed = true
  appOption.isProgressbar = true
}
function removeLoadingBar() {
  //진행중(로딩바)+딤드 제거
  appOption.isDimmed = false
  appOption.isProgressbar = false
}

function startProgressbar() {
  //진행중(로딩바) 추가
  appOption.isProgressbar = true
}
function removeProgressbar() {
  //진행중(로딩바) 제거
  appOption.isProgressbar = false
}

function startDimmed() {
  //모달창 뒷 딤드 추가
  appOption.isDimmed = true
}
function removeDimmed() {
  //모달창 뒷 딤드 제거
  appOption.isDimmed = false
}

function showToast(msg) {
  //토스트 메시지 띄우기
  appVariable.toastMsg = msg
}
/**hover */
function addHoverClassToTr(mouseEvent) {
  //grid 마우스 호버 액션 추가
  //cursor를 포함한 hover
  mouseEvent.target.classList.add('hover')
  addCursorClassToTr(mouseEvent)
}
function removeHoverClassFromTr(mouseEvent) {
  //grid 마우스 호버 액션 제거
  //cursor를 포함한 hover
  mouseEvent.target.classList.remove('hover')
  removeCursorClassFromTr(mouseEvent)
}
function addHoverClassToTr1(mouseEvent) {
  //grid 마우스 호버 액션 추가(커서액션 제외)
  mouseEvent.target.classList.add('hover')
}
function removeHoverClassFromTr1(mouseEvent) {
  //grid 마우스 호버 액션 추가(커서액션 제외)
  mouseEvent.target.classList.remove('hover')
}

function addCursorClassToTr(mouseEvent) {
  //grid 마우스 커서 액션 추가
  mouseEvent.target.classList.add('cursor')
}
function removeCursorClassFromTr(mouseEvent) {
  //grid 마우스 커서 액션 제거
  mouseEvent.target.classList.remove('cursor')
}

function doSort(field, setting, arry) {
  //grid 헤더 선택 시 정렬

  let sort = 'asc'
  if (field == setting.order) {
    if (setting.sort == 'asc') {
      sort = 'desc'
    }
  }
  setting.order = field
  setting.sort = sort

  arry.sort(function (a, b) {
    var collator = new Intl.Collator(undefined, {
      numeric: true,
      sensitivity: 'base',
    })
    let sortOrder = sort === 'desc' ? -1 : 1
    return collator.compare(a[field], b[field]) * sortOrder
  })
}

function excelDownload(data, fileName) {
  //엑셀파일 다운로드

  const worksheet = XLSX.utils.json_to_sheet(data)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Data')
  XLSX.writeFile(workbook, `${fileName}.xlsx`)
}

function numberWithCommas(num) {
  //000,000 숫자 세자리 콤마 포맷
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

function setDateFormat(date) {
  //YYYY-MM-DD로 데이터 포맷

  let returnDate

  if (date.length === 10) {
    return date
  }

  returnDate = date ? date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0') : ''

  return returnDate
}

/**
 * 년월일시분초, 문자없이 숫자만 리턴한다.
 * @param date 날짜값
 * @returns
 */
function getDtmFormatOnlyDigits(date: Date) {
  let returnDate

  if (date.length === 10) {
    return date
  }

  returnDate = date ? date.getFullYear() + String(date.getMonth() + 1).padStart(2, '0') + String(date.getDate()).padStart(2, '0') + String(date.getHours()).padStart(2, '0') + String(date.getMinutes()).padStart(2, '0') + String(date.getSeconds()).padStart(2, '0') : ''

  return returnDate
}

/**
 * 날짜 포맷 리턴함수 : MM/DD
 * @param date
 * @returns
 */
function formatDate_MM_DD(date: Date) {
  let returnDate
  returnDate = date ? String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0') : ''
  return returnDate
}

/**
 * 시간포맷 리턴함수 : HH_MM_SS
 * @param targetDate
 * @returns
 */
function formatTime_HH_MM_SS(targetDate: Date) {
  var today = targetDate
  var hours = ('0' + today.getHours()).slice(-2)
  var minutes = ('0' + today.getMinutes()).slice(-2)
  var seconds = ('0' + today.getSeconds()).slice(-2)
  var timeString = hours + ':' + minutes + ':' + seconds
  // console.log(timeString);
  return timeString
}

/**
 * 시간포맷 리턴함수 : HH_MM
 * @param targetDate
 * @returns
 */
function formatTime_HH_MM(targetDate: Date) {
  var today = targetDate
  var hours = ('0' + today.getHours()).slice(-2)
  var minutes = ('0' + today.getMinutes()).slice(-2)
  var seconds = ('0' + today.getSeconds()).slice(-2)
  var timeString = hours + ':' + minutes
  // console.log(timeString);
  return timeString
}

/**
 * 거리표시(미터를 킬로미터로, 소수점2자리까지)
 * @param distance
 * @returns
 */
function distanceStringK(distance) {
  var calcDistance = distance / 1000

  // console.log('distance=' + distance);
  // console.log('calcDistance=' + calcDistance);

  // 소수점 2자리 표시
  return (Math.round(calcDistance * 100) / 100).toFixed(2)
}

/**
 * 날짜, 요일명 얻기
 * @param targetDate
 * @param type (1:짧은, 2:긴)
 * @returns
 */
function getNameofWeek(targetDate: Date, type: Number) {
  const dayOfWeek = targetDate.getDay()

  //0:일, 1:월, 2:화, 3:수, 4:목, 5:금, 6:토
  let dayStringShort = '',
    dayStringLong = ''
  switch (dayOfWeek) {
    case 0:
      dayStringShort = '일'
      dayStringLong = '일요일'
      break
    case 1:
      dayStringShort = '월'
      dayStringLong = '월요일'
      break
    case 2:
      dayStringShort = '화'
      dayStringLong = '화요일'
      break
    case 3:
      dayStringShort = '수'
      dayStringLong = '수요일'
      break
    case 4:
      dayStringShort = '목'
      dayStringLong = '목요일'
      break
    case 5:
      dayStringShort = '금'
      dayStringLong = '금요일'
      break
    case 6:
      dayStringShort = '토'
      dayStringLong = '토요일'
      break
  }
  if (type == 1) return dayStringShort
  else return dayStringLong
}

/**
 * 현재일시 리턴하기(YYYY_MM_DD_HH_MM_SS)
 */
function nowYYYY_MM_DD_HH_MM_SS() {
  const now = new Date()

  return setDateFormat(now) + ' ' + formatTime_HH_MM_SS(now)
}

/**
 * 날짜 스트링을 날짜값으로..
 * @param timeString
 * @returns
 */
function getDateFromString(timeString) {
  var year = timeString.substring(0, 4)
  var month = timeString.substring(4, 6) - 1
  var day = timeString.substring(6, 8)
  var hour = timeString.substring(8, 10)
  var minute = timeString.substring(10, 12)
  var second = timeString.substring(12, 14)
  return new Date(year, month, day, hour, minute, second)
}

const utils = {
  getAxios() {
    return instance
  },
  getAxiosFile() {
    return instanceFile
  },
  getAxiosCVO() {
    return instanceCVO
  },
  getAxiosERP() {
    return instanceERP
  },

  startLoadingBar() {
    //진행중(로딩바)+딤드 추가
    appOption.isDimmed = true
    appOption.isProgressbar = true
  },
  removeLoadingBar() {
    //진행중(로딩바)+딤드 제거
    appOption.isDimmed = false
    appOption.isProgressbar = false
  },

  startProgressbar() {
    //진행중(로딩바) 추가
    appOption.isProgressbar = true
  },
  removeProgressbar() {
    //진행중(로딩바) 제거
    appOption.isProgressbar = false
  },

  startDimmed() {
    //모달창 뒷 딤드 추가
    appOption.isDimmed = true
  },
  removeDimmed() {
    //모달창 뒷 딤드 제거
    appOption.isDimmed = false
  },
  showToast(msg) {
    //토스트 메시지 띄우기
    appVariable.toastMsg = msg
  },
  /**hover */
  addHoverClassToTr(mouseEvent) {
    //grid 마우스 호버 액션 추가
    //cursor를 포함한 hover
    mouseEvent.target.classList.add('hover')
    addCursorClassToTr(mouseEvent)
  },
  removeHoverClassFromTr(mouseEvent) {
    //grid 마우스 호버 액션 제거
    //cursor를 포함한 hover
    mouseEvent.target.classList.remove('hover')
    removeCursorClassFromTr(mouseEvent)
  },
  addHoverClassToTr1(mouseEvent) {
    //grid 마우스 호버 액션 추가(커서액션 제외)
    mouseEvent.target.classList.add('hover')
  },
  removeHoverClassFromTr1(mouseEvent) {
    //grid 마우스 호버 액션 추가(커서액션 제외)
    mouseEvent.target.classList.remove('hover')
  },

  addCursorClassToTr(mouseEvent) {
    //grid 마우스 커서 액션 추가
    mouseEvent.target.classList.add('cursor')
  },
  removeCursorClassFromTr(mouseEvent) {
    //grid 마우스 커서 액션 제거
    mouseEvent.target.classList.remove('cursor')
  },
  excelDownload(data, fileName) {
    //엑셀파일 다운로드

    const worksheet = XLSX.utils.json_to_sheet(data)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Data')
    XLSX.writeFile(workbook, `${fileName}.xlsx`)
  },

  numberWithCommas(num) {
    if (!num) return
    //000,000 숫자 세자리 콤마 포맷
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
  },

  setDateFormat(date) {
    //YYYY-MM-DD로 데이터 포맷

    let returnDate

    if (date.length === 10) {
      return date
    }

    returnDate = date ? date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0') : ''

    return returnDate
  },
  /**
   * 날짜 포맷 리턴함수 : MM/DD
   * @param date
   * @returns
   */
  formatDate_MM_DD(date: Date) {
    let returnDate
    returnDate = date ? String(date.getMonth() + 1).padStart(2, '0') + '/' + String(date.getDate()).padStart(2, '0') : ''
    return returnDate
  },

  /**
   * 시간포맷 리턴함수 : HH_MM_SS
   * @param targetDate
   * @returns
   */
  formatTime_HH_MM_SS(targetDate: Date) {
    var today = targetDate
    var hours = ('0' + today.getHours()).slice(-2)
    var minutes = ('0' + today.getMinutes()).slice(-2)
    var seconds = ('0' + today.getSeconds()).slice(-2)
    var timeString = hours + ':' + minutes + ':' + seconds
    // console.log(timeString);
    return timeString
  },

  /**
   * 시간포맷 리턴함수 : HH_MM
   * @param targetDate
   * @returns
   */
  formatTime_HH_MM(targetDate: Date) {
    var today = targetDate
    var hours = ('0' + today.getHours()).slice(-2)
    var minutes = ('0' + today.getMinutes()).slice(-2)
    var seconds = ('0' + today.getSeconds()).slice(-2)
    var timeString = hours + ':' + minutes
    // console.log(timeString);
    return timeString
  },

  /**
   * 거리표시(미터를 킬로미터로, 소수점2자리까지)
   * @param distance
   * @returns
   */
  distanceStringK(distance) {
    var calcDistance = distance / 1000

    // console.log('distance=' + distance);
    // console.log('calcDistance=' + calcDistance);

    // 소수점 2자리 표시
    return (Math.round(calcDistance * 100) / 100).toFixed(2)
  },

  /**
   * 날짜, 요일명 얻기
   * @param targetDate
   * @param type (1:짧은, 2:긴)
   * @returns
   */
  getNameofWeek(targetDate: Date, type: Number) {
    const dayOfWeek = targetDate.getDay()

    //0:일, 1:월, 2:화, 3:수, 4:목, 5:금, 6:토
    let dayStringShort = '',
      dayStringLong = ''
    switch (dayOfWeek) {
      case 0:
        dayStringShort = '일'
        dayStringLong = '일요일'
        break
      case 1:
        dayStringShort = '월'
        dayStringLong = '월요일'
        break
      case 2:
        dayStringShort = '화'
        dayStringLong = '화요일'
        break
      case 3:
        dayStringShort = '수'
        dayStringLong = '수요일'
        break
      case 4:
        dayStringShort = '목'
        dayStringLong = '목요일'
        break
      case 5:
        dayStringShort = '금'
        dayStringLong = '금요일'
        break
      case 6:
        dayStringShort = '토'
        dayStringLong = '토요일'
        break
    }
    if (type == 1) return dayStringShort
    else return dayStringLong
  },

  doSort(field, setting, arry) {
    //grid 헤더 선택 시 정렬

    let sort = 'asc'
    if (field == setting.order) {
      if (setting.sort == 'asc') {
        sort = 'desc'
      }
    }
    setting.order = field
    setting.sort = sort

    arry.sort((a, b) => {
      var collator = new Intl.Collator(undefined, {
        numeric: true,
        sensitivity: 'base',
      })
      let sortOrder = sort === 'desc' ? -1 : 1
      return collator.compare(a[field], b[field]) * sortOrder
    })
  },
  initSettingOutPop() {
    // 모달객체를 밖으로 추가해준다.
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
  },
}

export { 
  startLoadingBar, removeLoadingBar, startProgressbar, removeProgressbar, startDimmed, removeDimmed, showToast, 
  addHoverClassToTr, removeHoverClassFromTr, addHoverClassToTr1, removeHoverClassFromTr1, addCursorClassToTr, 
  removeCursorClassFromTr, doSort, excelDownload, numberWithCommas, 
  getAxios, getAxiosFile, getAxiosCVO, getAxiosCVOUtil, getAxiosERP, 
  setDateFormat, getDtmFormatOnlyDigits, formatDate_MM_DD, formatTime_HH_MM_SS, formatTime_HH_MM, distanceStringK, 
  getNameofWeek, nowYYYY_MM_DD_HH_MM_SS, getDateFromString, utils 
}
