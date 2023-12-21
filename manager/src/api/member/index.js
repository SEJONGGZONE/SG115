import { getAxiosErp } from "@/common/utils.ts";
/************************************************** 회원관리 */
export const memberApi = {
  /** 회원/거래처 관리>회원관리_리스트_조회 */
  memberMng(params) {
    let data = {
      "@I_CLCODE": params.clcode,
      "@I_KEYWORD": params.searchKeyword,
      "@I_PAGE_SIZE": params.pageSize,
      "@I_PAGE_NUM": params.pageNumber,
      "@I_INPUT_USER": "9999",
    };
    return getAxiosErp().post(`/ADM_USER_SEL`, data);
  },

  /** 회원/거래처 관리>회원관리_리스트_상세_저장 */
  memberMngSave(params) {
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_CLCODE": params.clcode,
      "@I_USER_NAME": params.userName,
      "@I_USER_PHONE": params.userPhone,
      "@I_COMPANY_NAME": params.companyName,
      "@I_COMPANY_CORPNO": params.companyCorpno,
      "@I_PASSWORD": params.password,
      "@I_EMAIL": params.email,
      "@I_STATUS": params.status,
      "@I_OS_TYPE": params.osType,
      "@I_JOIN_TYPE": params.joinType,
      "@I_INPUT_USER": params.inputUser,
      "@I_PAY_YN": params.payYn,
    };

    return getAxiosErp().post(`/ADM_USER_SAVE`, data);
  },

  /** 회원/거래처 관리>거래처 수기 결제_리스트_조회*/
  accoutPayment(params) {
    let data = {
      "@I_CLCODE": params.clcode,
      "@I_KEYWORD": params.searchKeyword,
      "@I_PAGE_SIZE": params.pageSize,
      "@I_PAGE_NUM": params.pageNumber,
      "@I_INPUT_USER": "9999",
    };

    return getAxiosErp().post(`/ADM_CLIENT_SEL`, data);
  },
  /** 회원/거래처 관리>거래처 수기 결제/관리자 수기결제 단독화면_상세_저장 */
  accoutPaymentSave(params) {
    let data = {
      "@I_CLCODE": params.clcode,
      "@I_MEMO": params.memo,
      "@I_AMOUNT": params.amount,
      "@I_ORDERID": params.key,
      "@I_PHONE_NO": params.phoneNo,
      "@I_INPUT_USER": "시스템",
    };

    return getAxiosErp().post(`/PAYMENT_KEYIN_ADMIN`, data);
  },
  /** [성창]사용자관리,사업자서류 업로드후 이미지정보 저장 */
  userImageSave(params) {
    // 첨부파일
    let data = {
      '@I_USERNO': params.userNo,
      '@I_FILENO': params.fileno,
    }
    return getAxiosErp().post(`/ADM_USERIMAGE_SAV`, data)
  },

  // ERP-거래처 정보 조회
  erpClientSel(params) {
    
    let data = {
      '@I_COMPANYCD': params.companyCd ?? '',
      '@I_DEVICENO': params.deviceNo ?? '',
      '@I_BOUND_DIST': params.boundDist ?? '',
      '@I_KEYWORD': params.keyword ?? '',
      '@I_CENTER_LAT': params.centerLat,
      '@I_CENTER_LON': params.centerLon
    }
    return getAxiosErp().post(`/ERP_CLIENT_SEL `, data)
  },

  // ERP-공통API조회
  erpCommonCall(procName, data) {
    return getAxiosErp().post(procName, data)
  },

  
};
