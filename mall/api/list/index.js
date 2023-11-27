
import { getAxios } from "@/common/utils.ts"; 

/************************************************** 카테고리 목록 */

/** 카테고리_목록화면   -- 리스트 조회 */
export function categoryList(params){ 
    let data = {  
        '@I_CLCODE': params.clcode,
        '@I_ITSCODE': params.itscode,
        '@I_ITSCODE2': params.itscode2,
        '@I_KEYWORD': params.keyword,
        '@I_ITCODE': params.itcode,
        '@I_PAGE_SIZE': params.pageSize,
        '@I_PAGE_NUM': params.pageNum,
        '@I_ORDER_TYPE_10': params.orderType10,//가격
        '@I_ORDER_TYPE_20': params.orderType20,//최신수정일시
        '@I_ORDER_TYPE_30': params.orderType21,//관심수
        '@I_ORDER_TYPE_40': params.orderType30,//상품명
        '@I_INPUT_USER': params.inputUser,
    }   
    return getAxios().post(`/WEB_ITEMLIST_SEL`,data) 
    
}

/************************************************** 기획상품 */
/** 기획상품_목록화면   -- 리스트 조회 */
export function eventList(params){ 
    let data = {  
        '@I_TYPE': params.type ,
        '@I_PAGE_SIZE': params.pageSize,
        '@I_PAGE_NUM': params.pageNum,
        '@I_INPUT_USER': params.inputUser,
        '@I_EVENTNO' : params.eventNo,
        '@I_CLCODE' : params.clcode, 
        '@I_ORDER_TYPE_10': params.orderType10,//가격
        '@I_ORDER_TYPE_20': params.orderType20,//최신수정일시
        '@I_ORDER_TYPE_30': params.orderType21,//관심수
        '@I_ORDER_TYPE_40': params.orderType30,//상품명
    }  

    return getAxios().post(`/WEB_EVENTITEM_SEL`,data)
    
}

/************************************************** 관심상품 */
/** 관심상품_목록화면   -- 리스트 조회 */
export function favList(params){ 
    let data = {  
        '@I_CLCODE': params.clcode,
        '@I_KEYWORD': params.keyword,
        '@I_PAGE_SIZE': params.pageSize,
        '@I_PAGE_NUM': params.pageNum,
        '@I_INPUT_USER': params.inputUser,
        '@I_ORDER_TYPE_10': params.orderType10,//가격
        '@I_ORDER_TYPE_20': params.orderType20,//최신수정일시
        '@I_ORDER_TYPE_30': params.orderType21,//관심수
        '@I_ORDER_TYPE_40': params.orderType30,//상품명
    }  

    return getAxios().post(`/WEB_FAVITEM_SEL`,data)
    
}
/** 관심상품_목록화면   -- (하트버튼)관심상품 추가 */
export function addFavList(params){ 
    let data = {  
        '@I_CLCODE': params.clcode,
        '@I_ITCODE': params.itcode,
        '@I_ITNAME' : params.itname
    }  

    return getAxios().post(`/WEB_FAVITEM_SAVE`,data)
    
}
/** 관심상품_목록화면   -- (하트버튼)관심상품 삭제 */
export function delFavList(params){ 
    let data = {  
        '@I_CLCODE': params.clcode,
        '@I_ITCODE': params.itcode
    }  

    return getAxios().post(`/WEB_FAVITEM_DEL`,data)
    
}
/************************************************** 주문내역조회 */
/** 주문내역 조회 */
export function orderHistoryList(params){ 
    let data = {  
        "@I_USER_NO": params.userNo,
        "@I_ORDER_START_DATE": params.startDate,
        "@I_ORDER_END_DATE": params.endDate,
        "@I_KEYWORD": params.keyword,
        "@I_INPUT_USER": params.inputUser
  } 

    return getAxios().post(`/WEB_ORDER_SEL `,data)
}
