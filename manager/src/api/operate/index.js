import { getAxiosErp, getAxiosCVO } from '@/common/utils.ts'

export const operateApi = {
  /***************************************************공지사항관리 */
  operateManagement(params) {
    let data = {
      '@I_GNO': params.gno,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_NOTICE_SEL `, data)
  },
  CvoGpsServiceSave(params) {
    let data = { companyCd: '00002', deviceNo: '01047321808', vehicleCd: '88-1563', vehicleNo: '88아1563', traceDate: '20230923', traceTime: '101321', eventCode: '05', gpsYn: 'Y', chargeYn: 'Y', latitude: '36.5980613', longitude: '127.3018132', direction: '192', speed: '0', remark: '/DIS20230923095758031', intervalDistance: '0', batteryLevel: '82' }

    return getAxiosCVO().post(`/CVO_415_GPSRECV_SAV `, data)
  },
  CvoGpsServiceCall(params) {
    let data = {
      '@I_DEVICENO': '01047321808',
      '@I_TRACE_START_DTM': '20230923000000',
      '@I_TRACE_END_DTM': '20230924000000',
    }
    return getAxiosCVO().post(`/CVO_TRACE_SEL `, data)
  },
  operateManagementSave(params) {
    let data = {
      '@I_GNO': params.gno,
      '@I_TITLE': params.title,
      '@I_MEMO': params.memo,
      '@I_DATE1': params.date1,
      '@I_DATE2': params.date2,
      '@I_DAY_USE_YN': params.dayUseYn,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_NOTICE_SAV `, data)
  },
  operateManagementDelete(params) {
    let data = {
      '@I_GNO': params.gno,
      '@I_DEL_YN': params.delYn,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_NOTICE_DEL `, data)
  },
  /***************************************************이벤트 알림관리 */
  eventManagement(params) {
    let data = {
      '@I_GEONUM': params.geonum,
      '@I_PAGE_SIZE': params.pageSize,
      '@I_PAGE_NUM': params.pageNum,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_ALARM_SEL `, data)
  },
  eventManagementSave(params) {
    let data = {
      '@I_GEONUM': params.geonum,
      '@I_TYPE': params.type,
      '@I_TITLE': params.title,
      '@I_CONTENTS': params.contents,
      '@I_SCHEDULE_USE_YN': params.scheduleUseYn,
      '@I_DATE1': params.date1,
      '@I_DATE2': params.date2,
      '@I_TIME1': params.time1,
      '@I_TIME2': params.time2,
      '@I_DAY_01_YN': params.day01Yn,
      '@I_DAY_02_YN': params.day02Yn,
      '@I_DAY_03_YN': params.day03Yn,
      '@I_DAY_04_YN': params.day04Yn,
      '@I_DAY_05_YN': params.day05Yn,
      '@I_DAY_06_YN': params.day06Yn,
      '@I_DAY_07_YN': params.day07Yn,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_ALARM_SAV `, data)
  },
  eventManagementDelete(params) {
    let data = {
      '@I_GEONUM': params.geonum,
      '@I_DEL_YN': params.delYn,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/ADM_ALARM_DEL `, data)
  },

  /***************************************************공통 코드 관리 */

  commCodeSave(params) {
    let data = {
      '@I_CODE_NO': params.codeNo,
      '@I_CODE_CLASS': params.codeClass,
      '@I_CODE_CD': params.codeCd,
      '@I_CODE_NAME': params.codeName,
      '@I_DESC_01': params.desc01 ?? '',
      '@I_DESC_02': params.desc02 ?? '',
      '@I_SORT_NUM': params.sortNum,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/COMM_CODE_SAV `, data)
  },

  commCodeDel(params) {
    let data = {
      '@I_CODE_CLASS': params.codeClass,
      '@I_CODE_CD': params.codeCd,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/COMM_CODE_DEL `, data)
  },

  commCodeSel(params) {
    let data = {
      '@I_CODE_CLASS': params.codeClass,
      '@I_CODE_CD': params.codeCd,
      '@I_INPUT_USER': params.inputUser,
    }

    return getAxiosErp().post(`/COMM_CODE_SEL `, data)
  },

  /***************************************************거래처관리 */

  //거래처 조회
  admClientSel(params) {
    let data = {
      '@I_CLCODE': params.clCode ?? '',
      '@I_KEYWORD': params.keyword ?? '',
      '@I_PAGE_SIZE': params.pageSize,
      '@I_PAGE_NUM': params.pageNumber,
      '@I_INPUT_USER': params.inputUser,
    }
    return getAxiosErp().post(`/ADM_CLIENT_SEL `, data)
  },

  //거래처 위치 저장
  admClientPosSave(params) {
    let data = {
      '@I_CLCODE': params.clCode,
      '@I_INPUT_USER': params.inputUser,
      '@I_LAT': params.lat,
      '@I_LON': params.lon,
      '@I_ADDRESS': params.address,
    }
    return getAxiosErp().post(`/ADM_CLIENT_POS_SAV `, data)
  },

  //거래처 위치 조회
  admClientPosSel(params) {
    let data = {
      '@I_CLCODE': params.clCode ?? '',
      '@I_POS_YN': params.posYn ?? '',
      '@I_KEYWORD': params.keyword ?? '',
      '@I_PAGE_SIZE': params.pageSize,
      '@I_PAGE_NUM': params.pageNumber,
      '@I_INPUT_USER': params.inputUser,
    }
    return getAxiosErp().post(`/ADM_CLIENT_POS_SEL `, data)
  },

  // CVO-차량현위치 조회
  cvoNowPositionSel(params) {
    console.log('[opreate-index.js] CVO-차량현위치 조회')
    let data = {
      '@I_COMPANYCD': params.companyCd ?? '',
      '@I_DEVICENO': params.deviceNo ?? '',
      '@I_VEHICLENO': params.vehicleNo ?? '',
      '@I_KEYWORD': params.keyword ?? '',
      '@I_LAT': params.lon,
      '@I_LON': params.lat,
      '@I_INPUT_USER': params.inputUser,
    }
    return getAxiosCVO().post(`/CVO_415_NOWPOSITION_SEL `, data)
  },

  // CVO-지오코드(주소-좌표변환) 조회
  cvoGeocodeSel(params) {
    console.log('[opreate-index.js] CVO-지오코드(주소-좌표변환) 조회')
    let data = {
      '@I_ADDRESS': params.address ?? '',
    }
    return getAxiosCVO().post(`/CVO_415_GEOCODE_SEL `, data)
  },

  // CVO-배송일지 조회
  cvoDispatchSummarySel(params) {
    console.log('[opreate-index.js] CVO-배송일지 조회')
    let data = {
      '@I_DDATE': params.ddate ?? '',
      '@I_COMPANYCD': params.companyCd ?? '',
      '@I_DEVICENO': params.deviceNo ?? '',
      '@I_VEHICLENO': params.vehicleNo ?? '',
      '@I_KEYWORD': params.keyword ?? '',
      '@I_LAT': params.lon,
      '@I_LON': params.lat,
    }
    return getAxiosCVO().post(`/CVO_415_DISPATCH_SUMMARYT_SEL `, data)
  },

  // CVO-경로조회 조회
  cvoTraceSel(params) {
    console.log('[opreate-index.js] CVO-경로조회 조회')
    let data = {
      '@I_COMPANYCD': params.companyCd ?? '',
      '@I_DEVICENO': params.deviceNo ?? '',
      '@I_TRACE_START_DTM': params.traceStartDtm ?? '',
      '@I_TRACE_END_DTM': params.traceEndDtm ?? '',
    }
    return getAxiosCVO().post(`/CVO_415_TRACE_SEL `, data)
  },
}
