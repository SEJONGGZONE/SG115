import { getAxios, getCvoAxios } from "@/common/utils.ts";

/***************************************************공지사항관리 */
export function operateNoticeSel(params) {
  let data = {
    "@I_GNO": params.gno ?? "",
    "@I_CLCODE": params.clCode ?? "",
    "@I_INPUT_USER": params.inputUser ?? "",
  };
  return getAxios().post(`/WEB_NOTICE_SEL`, data);
}
export function CvoGpsServiceSave(params) {
  let data = {
    companyCd: "00002",
    deviceNo: "01047321808",
    vehicleCd: "88-1563",
    vehicleNo: "88아1563",
    traceDate: params.traceDate,
    traceTime: params.traceTime,
    eventCode: "05",
    gpsYn: "Y",
    chargeYn: "Y",
    latitude: params.latitude,
    longitude: params.longitude,
    direction: params.direction,
    speed: params.speed,
    remark: "/DIS20230923095758031",
    intervalDistance: "0",
    batteryLevel: "82",
  };

  return getCvoAxios().post(`/CVO_415_GPSRECV_SAV `, data);
}

/*************************************************** Q&A */
/** 조회 */
export function operateQnaSel(params) {
  let data = {
    "@I_GEONUM": params.geonum,
    "@I_BOARD_TYPE": params.boardType,
    "@I_KEYWORD": params.keyword,
    "@I_PAGE_SIZE": params.pageSize,
    "@I_PAGE_NUM": params.pageNum,
    "@I_REF_NO": params.refNo, //WEB_BOAR.GEONUM
    "@I_INPUT_USER": params.inputUser,
  };
  return getAxios().post(`/WEB_BOARD_SEL`, data);
}
/** 저장 */
export function operateQnaSav(params) {
  let data = {
    "@I_TYPE": params.type,
    "@I_TITLE": params.title, //제목
    "@I_MEMO": params.memo, //내용
    "@I_REF_NO": params.refNo, //WEB_BOAR.GEONUM
    "@I_REF_SEQ": params.refSeq, //답글 순번
    "@I_WS_NEWDATE": params.wsNewdate, //최초 작성일
    "@I_WS_NEWUSER": params.wsNewuser, //최초 작성자
    "@I_INPUT_USER": params.inputUser, //등록사용자번호
  };
  return getAxios().post(`/WEB_BOARD_SAV`, data);
}
/** 삭제 */
export function operateQnaDel(params) {
  let data = {
    "@I_GEONUM": params.geonum,
    "@I_INPUT_USER": params.inputUser, //등록사용자번호
  };
  return getAxios().post(`/WEB_BOARD_DEL`, data);
}
/** 댓글_저징 */
export function operateQnaRefSav(params) {
  let data = {
    "@I_GEONUM": params.geonum,
    "@I_TYPE": params.type,
    "@I_TITLE": params.title, //제목
    "@I_MEMO": params.memo, //내용
    "@I_REF_NO": params.refNo, //WEB_BOAR.GEONUM
    "@I_REF_SEQ": params.refSeq, //답글 순번
    "@I_DATE": params.date, //등록일
    "@I_USER": params.user, //등록자
    "@I_INPUT_USER": params.inputUser,
  };
  return getAxios().post(`/WEB_BOARD_REPLAY_SAV`, data);
}
/** 댓글_삭제 */
export function operateQnaRefDel(params) {
  let data = {
    "@I_GEONUM": params.geonum,
    "@I_INPUT_USER": params.inputUser, //등록사용자번호
  };
  return getAxios().post(`/WEB_BOARD_REPLAY_DEL`, data);
}
