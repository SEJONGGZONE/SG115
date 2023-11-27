
import { getAxios } from "@/common/utils.ts"; 

/** 사용자 수기결제 단독화면_조회 */
export function userPaymentSearch(params){ 
    let data = {  
        '@I_PAYMENT_NO': params.paymentNo,
    }  
    return getAxios().post(`/PAYMENT_INFO_SEL`,data)
}
/** 사용자 수기결제 단독화면_저장 */
export function userPaymentSave(params){ 
    let data = {  
        '@I_PAYMENT_NO': params.paymentNo,
        '@I_CARDNUMBER': params.cardNum,
        '@I_CARDEXPIRATIONYEAR': params.cardPeriodY,
        '@I_CARDEXPIRATIONMONTH': params.cardPeriodM,
        '@I_CARDPASSWORD': params.cardPassNum,
        '@I_CUSTOMERIDENTITYNUMBER': params.juminNum,      
        '@I_INPUT_USER': params.inputUser,      
    }  
    return getAxios().post(`/PAYMENT_KEYIN_USER`,data)
}