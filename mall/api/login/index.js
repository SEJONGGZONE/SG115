
import { getAxios, getAxiosErp } from "@/common/utils.ts"; 
/************************************************** 회원관리 */


/** 로그인 */
export function executeLogin(params){ 
    let data = {  
            '@I_ID': params.userId, 
            '@I_INPUT_USER': "9999",
            '@I_PASSWORD': params.userPw,
            '@I_USER_TYPE':params.userType
    }  

    return getAxiosErp().post(`/WEB_LOGIN_SEL`,data)
}
/** 회원관리 */
export function excuteRegiste(params){ 
    let data = {  
        '@I_GEONUM': params.geonum,
        '@I_TYPE': params.type,
        '@I_CLCODE': params.clcode,
        '@I_NAME': params.name,
        '@I_PHONE': params.phone,
        '@I_EMAIL': params.email,
        '@I_PASSWORD': params.password,
        '@I_COMPANY_NAME': params.companyName,
        '@I_COMPANY_CORPNO': params.companyCorpno,
        '@I_POST_NO': params.postNo,
        '@I_ADDRESS1': params.address1,
        '@I_ADDRESS2': params.address2,
        '@I_STATUS': params.status,
        '@I_JOIN_TYPE': params.joinType,
        '@I_INPUT_USER': params.userId,
        '@I_AGREE_01_YN':params.agree01,
        '@I_AGREE_02_YN':params.agree02,
        '@I_AGREE_03_YN':params.agree03,
        '@I_AGREE_EMAIL_YN':params.agreeEmail,
        '@I_AGREE_SMS_YN':params.agreeSms,
        '@I_CORP_FILE_NO':params.corpFileNo
    }  

    return getAxiosErp().post(`/WEB_USER_SAVE`,data)
}
/** 임시 비밀번호 요청/신규비밀번호 저장 */
export function resetPassWord(params){ 
    let data = {  
            '@I_EMAIL': params.email, 
            '@I_PHONENO': params.phoneNo,
            '@I_PASSWORD' : params.password
    }  

    return getAxiosErp().post(`/WEB_RESET_PASSWORD`,data)
}
 