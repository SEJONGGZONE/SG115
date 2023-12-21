
import { getAxios,getAxiosErp } from "@/common/utils.ts"; 

/************************************************** 회원관리 */

/** 장바구니 추가 */
export function addCartList(params){ 
    let cartNO = localStorage.getItem("CART_NO") ?? ''
    let data = {  
       '@I_CART_NO': cartNO,
       '@I_SEQ': params.seq,
       '@I_CLCODE': params.clcode,
       '@I_USER_NO': params.userNo,
       '@I_ITCODE': params.itcode,
       '@I_QTY': params.qty,
       '@I_AMOUNT': params.amount,
       '@I_TYPE':'10',
       '@I_INPUT_USER': params.inputUser
    }  
 

    return getAxiosErp().post(`/WEB_CART_SAVE`,data)
}
/** 장바구니 바로 구매 추가 */
export function addBuyCart(params){  
    let data = {  
       '@I_CART_NO': '',
       '@I_SEQ': params.seq,
       '@I_CLCODE': params.clcode,
       '@I_USER_NO': params.userNo,
       '@I_ITCODE': params.itcode,
       '@I_QTY': params.qty,
       '@I_AMOUNT': params.amount,
       '@I_TYPE':'20',
       '@I_INPUT_USER': params.inputUser
    }  
 

    return getAxiosErp().post(`/WEB_CART_SAVE`,data)
}

/** 장바구니 리스트 조회 */
export function getCartList(params){ 
    let data = {  
        '@I_TYPE' : params.type ?? '10',
        '@I_CLCODE': params.clcode,
        '@I_USER_NO': params.userNo
    }  

    return getAxiosErp().post(`/WEB_CART_SEL`,data)
    
}

/** 장바구니 삭제 */
export function delCartList(params){ 
    localStorage.removeItem("CART_NO")
    let data = {  
        '@I_CART_NO': params.cartNo,
        '@I_SEQ': params.seq
    }  

    return getAxiosErp().post(`/WEB_CART_DEL`,data)
    
}
