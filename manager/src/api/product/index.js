import { getAxios, getAxiosFile } from "@/common/utils.ts";
export const productApi = {
  /** 파일 업로드 */
  fileUpload(params, url) {
    params.enctype = "multipart/form-data";
    params.contentType = false;
    params.processData = false;
    // request.open("POST", "/api/File/upload" + url);
    // let data = await request.send(params);
    return getAxiosFile().post("File/upload" + url, params);
  },
  /************************** 상품조회/수정 */
  productManagement(params) {
    let data = {
      "@I_USEYN": params.useYn ?? "Y",
      "@I_ITSCODE": params.itsCode1,
      "@I_ITSCODE2": params.itsCode2,
      "@I_PAGE_SIZE": params.pageSize,
      "@I_KEYWORD": params.searchKeyword,
      "@I_CLCODE": params.clcode,
      "@I_PAGE_NUM": params.pageNumber,
      "@I_INPUT_USER": params.inputUser,
      "@I_ITCODE": params.itCode,
    };
    return getAxios().post(`/ADM_ITEMLIST_01`, data);
  },

  /** 상품 상세 대표 , 상세 조회 */
  selectImageList(params) {
    let data = {
      "@I_ITCODE": params.itCode,
    };
    return getAxios().post(`/ADM_POP_ITEMIMG_SEL`, data);
  },

  /** 상품 상세 대표 , 상세 삭제 */
  productImageDel(params) {
    let data = {
      "@I_INPUT_USER": params.inputUser,
      "@I_SEQ": params.seq,
      "@I_TYPE": params.type,
      "@I_ITCODE": params.itCode,
    };
    return getAxios().post(`/ADM_POP_ITEMIMG_DEL`, data);
  },

  /** 카테고리 조회 */
  category(params) {
    let data = {
      "@I_CODE": params.code,
      "@I_INPUT_USER": params.inputUser,
      "@I_NAME": params.name,
    };

    return getAxios().post(`/GET_CATEGORY`, data);
  },
  /** 관심수 list */
  getFavItemCntList(params) {
    let data = {
      "@I_ITCODE": params.itCode,
    };
    return getAxios().post(`/ADM_FAVITEM_CLIENT_SEL`, data);
  },
  /** 추가정보 list */
  getAddInfoList(params) {
    let data = {
      "@I_CODE": params.code,
      "@I_INPUT_USER": params.inputUser,
      "@I_NAME": params.name,
    };
    return getAxios().post(`/GET_CATEGORY`, data);
  },

  /************************** 기획관리 */

  eventManagementList(params) {
    //기획관리 조회
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_PAGE_SIZE": params.pageSize,
      "@I_PAGE_NUM": params.pageNumber,
      "@I_INPUT_USER": params.inputUser,
    };
    return getAxios().post(`/ADM_EVENT_MNG_SEL`, data);
  },

  eventManagementSave(params) {
    //기획관리 저장
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_TYPE": params.type,
      "@I_TITLE": params.title,
      "@I_IMG_FILE_NO": params.imgFileNo,
      "@I_DATE1": params.date1,
      "@I_DATE2": params.date2,
      "@I_SCHEDULE_USE_YN": params.scheduleUseYn,
      "@I_BIGO": params.bigo,
      "@I_INPUT_USER": params.inputUser,
      "@I_CONTENTS": params.content,
      "@I_SORT_NUM": params.sortNum,
    };
    return getAxios().post(`/ADM_EVENT_MNG_SAV`, data);
  },

  eventManagementDelete(params) {
    //기획관리 삭제
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_DEL_YN": params.delYn,
      "@I_INPUT_USER": params.inputUser,
    };
    return getAxios().post(`/ADM_EVENT_MNG_DEL`, data);
  },

  /************************** 기획상품관리 */

  eventProductSave(params) {
    //기획상품관리 상품 저장
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_TYPE": params.type,
      "@I_ITEM_LIST": params.itemList,
      "@I_INPUT_USER": params.inputUser,
    };
    return getAxios().post(`/ADM_EVENT_ITEM_SAV`, data);
  },
  eventProductDelete(params) {
    //기획상품관리 등록 데이터 삭제
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_DEL_YN": params.delYn,
      "@I_INPUT_USER": params.inputUser,
    };
    return getAxios().post(`/ADM_EVENT_ITEM_DEL`, data);
  },

  eventProductList(params) {
    //기획상품 등록 리스트 조회
    let data = {
      "@I_GEONUM": params.geonum,
      "@I_PAGE_SIZE": params.pageSize,
      "@I_PAGE_NUM": params.pageNumber,
      "@I_INPUT_USER": params.inputUser,
    };
    return getAxios().post(`/ADM_EVENT_ITEM_SEL`, data);
  },

  itemImageSave(params) {
    //기획상품 등록 리스트 조회
    let data = {
      "@I_ITCODE": params.itcode,
      "@I_TYPE": params.type,
      "@I_SEQ": params.seq,
      "@I_FILENO": params.fileno,
    };
    return getAxios().post(`/ADM_ITEMIMAGE_SAV`, data);
  },
};
