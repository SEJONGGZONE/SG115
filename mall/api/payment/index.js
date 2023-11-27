
import { getAxios,getTossAxios } from '@/common/utils.ts'

/************************************************** 결제관리 */

/** 최근 배송지목록 조회 */
export function recentShipList(params){ 
    let data = {  
        '@I_CLCODE' : params.clcode,
        '@I_USER_NO' : params.userNo,
        '@I_INPUT_USER' : params.inputUser
    }  

    return getAxios().post(`/WEB_LAST_PLACE_SEL`,data)
    
}

/** 배송지목록 조회 */
export function shipList(params){ 
    let data = {  
        '@I_CLCODE' : params.clcode,
        '@I_USER_NO' : params.userNo,
        '@I_INPUT_USER' : params.inputUser
    }  

    return getAxios().post(`/WEB_DELIVERY_PLACE_SEL`,data)
    
}

/** 배송지정보 등록 */
export function addShipList(params){ 
    let data = {  
            '@I_GEONUM': params.geonum,
            '@I_CLCODE': params.clcode,
            '@I_USER_NO': params.userNo,
            '@I_NICK_NAME': params.nickName,
            '@I_NAME': params.name,
            '@I_ADDRESS1': params.address1,
            '@I_ADDRESS2': params.address2,
            '@I_POST_NO': params.postNo,
            '@I_PHONE': params.phone,
            '@I_INPUT_USER': params.inputUser
    }  

    return getAxios().post(`/WEB_DELIVERY_PLACE_SAVE`,data)
    
}

/** 배송지목록 삭제 */
export function delShipList(params){ 
    let data = {  
        '@I_GEONUM' : params.geonum,
        '@I_INPUT_USER' : params.inputUser
    }  

    return getAxios().post(`/WEB_DELIVERY_PLACE_DEL`,data)
    
}

/** 주문저장(결제연동) */
export function saveOrder(params){  
    let data = {  
            '@I_GEONUM': params.geonum,
            '@I_ORDERID': params.orderId,
            '@I_CART_NO': params.cartNo,
            '@I_ITEMLIST': params.itemList,// VARCHAR(MAX), -- 상품리스트(구분자 '/')
            '@I_CLCODE': params.clcode,
            '@I_USER_NO': params.userNo,
            '@I_STATUS': params.status,
            '@I_ORDER_NAME': params.orderName,
            '@I_ORDER_PHONE': params.orderPhone,
            '@I_RECV_NAME': params.recvName,
            '@I_RECV_PHONE': params.recvPhone,
            '@I_ADDRESS1': params.address1,
            '@I_ADDRESS2': params.address2,
            '@I_POST_NO': params.postNo,
            '@I_PAYMENT_TYPE': params.paymentType,
            '@I_PAYMENT_NO': params.paymentNo,
            '@I_PAYMENT_DATE': params.paymentDate,
            '@I_ORDER_AMOUNT': params.orderAmount,
            '@I_DELI_AMOUNT': params.deliAmount,
            '@I_DISCOUNT_AMOUNT': params.discountAmount,
            '@I_PAYMENT_AMOUNT': params.paymentAmount,
             "@I_MEMO": params.memo,
            '@I_INPUT_USER': params.inputUser
    }   

    return getAxios().post(`/WEB_ORDER_SAVE`,data)
    
}

/** 주문저장(결제연동) */
export function paymentStatusUpdate(params){  
    let data = {  
            '@I_PAYMENTKEY': params.paymentKey,
            '@I_STATUS': params.status,
            '@I_INPUT_USER': params.inputUser
    }   

    return getAxios().post(`/WEB_PAYMENTSTATUS_UPDATE`,data)
    
}
/** 결제저장 */
export function paymentSave(params){  
    let data = {
        '@I_STATUS' : params.status,  
        '@I_PAYMENTKEY' : params.paymentkey,  
        '@I_ORDERID' : params.orderid,  
        '@I_ORDERNAME' : params.ordername,  
        '@I_BANKCODE' : params.bankcode,  
        '@I_ACCONTNO' : params.accountno,  
        '@I_CARD' : params.card,  
        '@I_SECRET' : params.secret,  
        '@I_RECEIPT' : params.receipt,  
        '@I_TOTALAMOUNT' : params.totalamount,  
        '@I_SUPPLIEDAMOUNT' : params.suppliedamount,  
        '@I_VAT' : params.vat,  
        '@I_TAXFREEAMOUNT' : params.taxfreeamount,  
        '@I_METHOD' : params.method,  
        '@I_VERSION' : params.version,  
        '@I_ORGDATA' : params.orgdata
    }  

    return getAxios().post(`/WEB_PAYMENT_SAVE`,data)
    
}




export function tossPaymentConfirm(params){   
     let data = {  
            'amount': params.amount,
            'orderId': params.orderId,
            'paymentKey': params.paymentKey
    }   

    return getTossAxios().post(`/`,data)
}
