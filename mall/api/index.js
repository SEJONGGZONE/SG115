/****************************************************************************************************** 쇼핑몰*/
import * as mainApi from "@/api/main";
import * as listApi from "@/api/list";
import * as cartApi from "@/api/cart";
import * as paymentApi from "@/api/payment";
/************************************************** 로그인/회원가입(loginApi) */
export function login_executeLogin(params) {
  return loginApi.executeLogin(params);
}
export function login_excuteRegiste(params) {
  return loginApi.excuteRegiste(params);
}
export function login_resetPassWord(params) {
  return loginApi.resetPassWord(params);
}

/************************************************** 메인(mainApi) */
/** 기획/이벤트 영역 */
export function main_excuteEventMng(params) {
  return mainApi.excuteEventMng(params);
}
/** 관심상품 영역 */
export function main_excuteFaveItem(params) {
  return mainApi.excuteFaveItem(params);
}
/** 푸터_입점/제휴 상담신청 */
export function main_addConsult(params) {
  return mainApi.addConsult(params);
}
/******************************************************************  리스트 성 화면(listApi)*/
/************************************************** 카테고리 목록 */
/** 카테고리_목록화면   -- 리스트 조회 */
export function list_categoryList(params) {
  return listApi.categoryList(params);
}
/************************************************** 기획상품 */
/** 기획상품_목록화면   -- 리스트 조회 */
export function list_eventList(params) {
  return listApi.eventList(params);
}
/************************************************** 관심상품 */
/** 관심상품_목록화면   -- 리스트 조회 */
export function list_favList(params) {
  return listApi.favList(params);
}
/** 관심상품_목록화면   -- (하트버튼)관심상품 추가 */
export function list_addFavList(params) {
  return listApi.addFavList(params);
}
/** 관심상품_목록화면   -- (하트버튼)관심상품 삭제 */
export function list_delFavList(params) {
  return listApi.delFavList(params);
}
/************************************************** 주문내역조회 */
/** 주문내역 조회 */
export function list_orderHistoryList(params) {
  return listApi.orderHistoryList(params);
}
/**************************************************  장바구니(cartApi)*/
/** 장바구니 추가 */
export function cart_addCartList(params) {
  return cartApi.addCartList(params);
}
/** 바로 구매 추가 */
export function cart_addBuyCart(params) {
  return cartApi.addBuyCart(params);
}
/** 장바구니 조회 */
export function cart_getCartList(params) {
  return cartApi.getCartList(params);
}
/** 장바구니 삭제 */
export function cart_delCartList(params) {
  return cartApi.delCartList(params);
}
/**************************************************  결제(paymentApi)*/
/** 최근 배송지목록 조회 */
export function payment_recentShipList(params) {
  return paymentApi.recentShipList(params);
}
/** 배송지목록 조회 */
export function payment_shipList(params) {
  return paymentApi.shipList(params);
}
/** 배송지정보 등록 */
export function payment_addShipList(params) {
  return paymentApi.addShipList(params);
}
/** 배송지정보 삭제 */
export function payment_delShipList(params) {
  return paymentApi.delShipList(params);
}
/** 주문저장(결제연동) */
export function payment_saveOrder(params) {
  return paymentApi.saveOrder(params);
}
/** 결제 상태 변경 */
export function payment_paymentStatusUpdate(params) {
  return paymentApi.paymentStatusUpdate(params);
}
/** 토스 결제 승인 */
export function payment_tossPaymentConfirm(params) {
  return paymentApi.tossPaymentConfirm(params);
}
/**  결제정보 저장 */
export function payment_paymentSave(params) {
  return paymentApi.paymentSave(params);
}
/****************************************************************************************************** 관리자(삭제하지말것 재활용 함)*/
import * as memberApi from "@/api/member";
import * as loginApi from "@/api/login";
import * as productApi from "@/api/product";
import * as orderApi from "@/api/order";
import * as userApi from "@/api/user";
import * as operateApi from "@/api/operate";

/************************************************** 회원관리 */

export function member_chkEmailAndBuisinessNumber(params) {
  return memberApi.chkEmailAndBuisinessNumber(params);
}
export function memeber_getIp() {
  return memberApi.getMyIp();
}
/** 유저 로그인 히스토리  */
export function memeber_webHistoryUserInfo(params) {
  return memberApi.webHistoryUserInfo(params);
}
/** 상품 히스토리  */
export function memeber_webHistoryItemSave(params) {
  return memberApi.webHistoryItemSave(params);
}
/** 회원/거래처 관리>회원관리_리스트_조회 */
export function member_memberMng(params) {
  return memberApi.memberMng(params);
}
/** 회원/거래처 관리>회원관리_리스트_상세_저장 */
export function member_memberMngSave(params) {
  return memberApi.memberMngSave(params);
}
/** 회원/거래처 관리>거래처 수기 결제_리스트_조회*/
export function member_accoutPayment(params) {
  return memberApi.accoutPayment(params);
}
/** 회원/거래처 관리>거래처 수기 결제/관리자 수기결제 단독화면_상세_저장 */
export function member_accoutPaymentSave(params) {
  return memberApi.accoutPaymentSave(params);
}
/** *****************************상품관리 */
/** 파일 업로드 */
export async function product_fileUpload(params, url) {
  return productApi.fileUpload(params, url);
}
/** 사용자 수기결제 단독화면_저장 */
export function product_productManagement(params) {
  return productApi.productManagement(params);
}
/** 카테고리 조회 */
export function product_category(params) {
  return productApi.category(params);
}
/** 관심수 list */
export function product_getFavItemCntList(params) {
  return productApi.getFavItemCntList(params);
}
/** 추가정보 list */
export function product_getAddInfoList(params) {
  return productApi.getAddInfoList(params);
}

/** 기획상품관리 */
export function product_eventProductManagement(params) {
  return productApi.eventProductManagement(params);
}
export function product_eventProductManagementSave(params) {
  return productApi.eventProductManagementSave(params);
}
export function product_eventProductManagementDelete(params) {
  return productApi.eventProductManagementDelete(params);
}
export function product_eventProductManagementRU(params) {
  return productApi.eventProductManagementRU(params);
}
/************************************************** 주문관리 */
export function order_orderManagement(params) {
  return orderApi.orderManagement(params);
}
export function order_orderManagementConfirm(params) {
  return orderApi.orderManagementConfirm(params);
}
/************************************************** 운영관리 */
/** 공지사항/팝업 */
export function operate_operateNoticeSel(params) {
  return operateApi.operateNoticeSel(params);
}
/** QnA */
//조회
export function operate_operateQnaSel(params) {
  return operateApi.operateQnaSel(params);
}
//저장
export function operate_operateQnaSav(params) {
  return operateApi.operateQnaSav(params);
}
//삭제
export function operate_operateQnaDel(params) {
  return operateApi.operateQnaDel(params);
}
//답변 저장
export function operate_operateQnaRefSav(params) {
  return operateApi.operateQnaRefSav(params);
}
//딥변 삭제
export function operate_operateQnaRefDel(params) {
  return operateApi.operateQnaRefDel(params);
}

/************************************************** 사용자 수기결제 */
/** 사용자 수기결제 단독화면_조회 */
export function user_userPaymentSearch(params) {
  return userApi.userPaymentSearch(params);
}
/** 사용자 수기결제 단독화면_저장 */
export function user_userPaymentSave(params) {
  return userApi.userPaymentSave(params);
}

/** 지도 데이터 갱신 */
export function CvoGpsServiceSave(params) {
  return operateApi.CvoGpsServiceSave(params);
}
