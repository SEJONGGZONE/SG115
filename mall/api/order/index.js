import { getAxios } from "@/common/utils.ts"; 

export function orderManagement(params){ 
    let data = {  
        "@I_SEARCH_TYPE": params.searchType,
        "@I_START_DATE": params.startDate,
        "@I_END_DATE": params.endDate,
        "@I_KEYWORD": params.keyword ?? '',
        "@I_CLCODE": params.clcode,
        "@I_ITCODE": params.itcode,
        "@I_PAGE_SIZE": params.pageSize,
        "@I_PAGE_NUM": params.pageNumber,
        "@I_INPUT_USER": params.inputUser
      
    }  

    return getAxios().post(`/ADM_ORDER_SEL `,data)
}
export function orderManagementConfirm(params){ 
    let data = {  
    "@I_ORDER_NO_LIST": params.orderNoList,
    "@I_INPUT_USER": params.inputUser
  } 

    return getAxios().post(`/ADM_ORDER_CONFIRM `,data)
}
 