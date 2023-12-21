import { getAxios, getAxiosErp } from "@/common/utils.ts";

import axios from "axios";
/************************************************** 회원관리 */
/** 회원/거래처 관리>회원관리_리스트_조회 */
export function memberMng(params) {
  let data = {
    "@I_CLCODE": params.clcode,
    "@I_KEYWORD": params.searchKeyword,
    "@I_PAGE_SIZE": params.pageSize,
    "@I_PAGE_NUM": params.pageNumber,
    "@I_INPUT_USER": "9999",
  };

  return getAxios().post(`/ADM_USER_SEL`, data);
}

/** 회원/거래처 관리>회원관리_리스트_상세_저장 */
export function memberMngSave(params) {
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

  return getAxios().post(`/ADM_USER_SAVE`, data);
}

/** 회원/거래처 관리>거래처 수기 결제_리스트_조회*/
export function accoutPayment(params) {
  let data = {
    "@I_CLCODE": params.clcode,
    "@I_KEYWORD": params.searchKeyword,
    "@I_PAGE_SIZE": params.pageSize,
    "@I_PAGE_NUM": params.pageNumber,
    "@I_INPUT_USER": "9999",
  };

  return getAxios().post(`/ADM_CLIENT_SEL`, data);
}
/** 회원/거래처 관리>거래처 수기 결제/관리자 수기결제 단독화면_상세_저장 */
export function accoutPaymentSave(params) {
  let data = {
    "@I_CLCODE": params.clcode,
    "@I_MEMO": params.memo,
    "@I_AMOUNT": params.amount,
    "@I_ORDERID": params.key,
    "@I_PHONE_NO": params.phoneNo,
    "@I_INPUT_USER": "시스템",
  };

  return getAxios().post(`/PAYMENT_KEYIN_ADMIN`, data);
}

/** IP 조회 */
export function getMyIp() {
  return axios.post(`https://ipinfo.io?token=8e0fd96d1413ed`);
}

/** 이메일 , 사업자 중복 체크 */
export function chkEmailAndBuisinessNumber(params) {
  let data = {
    "@I_TYPE": params.type,
    "@I_DATA": params.data,
  };
  return getAxios().post(`/WEB_CHK_USERINFO`, data);
}

/** 유저 로그인 히스토리 정보 */

export function webHistoryUserInfo(params) {
  var userAgent = navigator.userAgent.toLowerCase();
  let data = {
    "@I_IP": params.ip,
    "@I_USER_AGENT": userAgent,
  };

  return getAxiosErp().post(`/WEB_HISTORY_USER_SAVE`, data);
}

/** 상품 조회 히스토리 저장*/

export function webHistoryItemSave(params) {
  var userAgent = navigator.userAgent.toLowerCase();
  let data = {
    "@I_ITCODE": params.itcode,
    "@I_USER_NO": params.userNo ?? "",
    "@I_CLIENT_NO": "",
    "@I_IP": params.ip,
    "@I_USER_AGENT": userAgent,
  };

  return getAxiosErp().post(`/WEB_HISTORY_ITEM_SAVE`, data);
}
