
import { getAxios, getAxiosErp } from "@/common/utils.ts"; 

/************************************************** 회원관리 */

/** 기획/이벤트 영역 */
export function excuteEventMng(params){ 
    let data = {  
        '@I_GEONUM': params.geonum,
        '@I_PAGE_SIZE': params.pageSize,
        '@I_PAGE_NUM': params.pageNum,
        '@I_INPUT_USER': params.inputUser
    }  

    return getAxiosErp().post(`/WEB_EVENTMNG_SEL`,data)
    
}

/** 관심상품 영역 */
export function excuteFaveItem(params){ 
    let data = {  
        '@I_CLCODE': params.clcode,
        '@I_KEYWORD': params.keyword,
        '@I_PAGE_SIZE': params.pageSize,
        '@I_PAGE_NUM': params.pageNum,
        '@I_INPUT_USER': params.inputUser
    }  

    return getAxiosErp().post(`/WEB_FAVITEM_SEL`,data)
    
}

/** 푸터_입점/제휴 상담신청 */
export function addConsult(params){ 
    let data = {  
        '@I_TYPE': params.type,
        '@I_NAME': params.name,
        '@I_PHONE_NO': params.phoneNo,
        '@I_MEMO': params.memo,
        '@I_WS_NEWDATE': params.wsNewdate,
        '@I_WS_NEWUSER': params.wsNewuser,
        '@I_INPUT_USER': params.inputUser
    }  

    return getAxiosErp().post(`/WEB_CONSULT_SAV`,data)
    
}
