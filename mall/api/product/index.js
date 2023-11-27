
import { getAxios,getAxiosFile } from "@/common/utils.ts"; 
/** 파일 업로드 */
export async function fileUpload(params,url) {
    params.enctype = 'multipart/form-data';
    params.contentType = false
    params.processData = false
    // request.open("POST", "/api/File/upload" + url);
    // let data = await request.send(params);
    return getAxiosFile().post("File/upload" + url,params)
}
/************************** 상품조회/수정 */
export function productManagement(params){ 
    let data = {  
        '@I_ITSCODE': params.itsCode1, 
        '@I_ITSCODE2': params.itsCode2, 
        '@I_PAGE_SIZE': params.pageSize, 
        '@I_KEYWORD': params.searchKeyword, 
        '@I_CLCODE': params.clcode, 
        '@I_PAGE_NUM': params.pageNumber,
        '@I_INPUT_USER': params.inputUser,
        '@I_ITCODE': params.itCode 
    }  
    return getAxios().post(`/GET_ITEMLIST_01`,data)
}

/** 카테고리 조회 */
export function category(params){ 
    let data = {  

        "@I_CODE": params.code,
        "@I_INPUT_USER": params.inputUser,
        "@I_NAME": params.name
    }  

    return getAxios().post(`/WEB_CATEGORY_SEL`,data)
}
/** 관심수 list */
export function getFavItemCntList(params){ 
    let data = {  
        "@I_ITSCODE": params.itCode
    }  
    return getAxios().post(`/ADM_FAVITEM_CLIENT_SEL`,data)
}
/** 추가정보 list */
export function getAddInfoList(params){ 
    let data = {  

        "@I_CODE": params.code,
        "@I_INPUT_USER": params.inputUser,
        "@I_NAME": params.name
    }  
    return getAxios().post(`/WEB_CATEGORY_SEL`,data)
}

/************************** 기획상품관리 */
export function eventProductManagement(params){ //기획상품관리 좌측 메인 테이블 조회
    let data = {  
        '@I_GEONUM': params.geonum, 
        '@I_PAGE_SIZE': params.pageSize, 
        '@I_PAGE_NUM': params.pageNumber,
        '@I_INPUT_USER': params.inputUser
    }  
    return getAxios().post(`/ADM_EVENITEM_SEL`,data)
}
export function eventProductManagementSave(params){ //기획상품관리 좌측 메인 테이블 저장
    let data = {  
        '@I_GEONUM': params.geonum, 
        '@I_TYPE': params.type, 
        '@I_TITLE': params.title, 
        '@I_IMG_FILE_NO': params.imgFileNo, 
        '@I_DATE1': params.date1, 
        '@I_DATE2': params.date2,
        '@I_SCHEDULE_USE_YN': params.scheduleUseYn,
        '@I_ITEM_LIST': params.itemList,
        '@I_INPUT_USER': params.inputUser
    }  
    return getAxios().post(`/ADM_EVENITEM_SAV`,data)
}
export function eventProductManagementDelete(params){ //기획상품관리 죄측 메인 테이블 삭제
    let data = {  
        '@I_GEONUM': params.geonum, 
        '@I_DEL_YN': params.delYn, 
        '@I_INPUT_USER': params.inputUser
    }  
    return getAxios().post(`/ADM_EVENITEM_DEL`,data)
}
export function eventProductManagementRU(params){ //기획상품관리 우측(R) 상단(U) 상품목록 테이블 조회
    let data = {  
        '@I_GEONUM': params.geonum, 
        '@I_PAGE_SIZE': params.pageSize, 
        '@I_PAGE_NUM': params.pageNumber,
        '@I_INPUT_USER': params.inputUser
    }  
    return getAxios().post(`/ADM_EVENITEMLIST_SEL`,data)
}