
interface User {

   USER_NO:number,
   TYPE:number,
   CLCODE?:string,
   SUCODE?:string,
   ID?:string,
   PASSWORD?:number,
   EMAIL?:string,
   NAME?:string,
   PHONE?:number,
   COMPANY_CORPNO?:string,
   COMPANY_NAME?:string,
   ADDRESS1?:string,
   ADDRESS2?:string,
   STATUS?:number,
   FILE_NO1?:string,
   FILE_NO2?:string,
   FILE_NO3?:string,
   LOGIN_OS_TYPE?:string,
   LOGIN_BROWSER?:string,
   LOGIN_DATE?:string,
   RECV_NAME?:string,
   RECV_ADDRESS1?:string,
   RECV_ADDRESS2?:string,
   RECV_POST_NO?:string,
   RECV_PHONE?:string,
   WS_NEWDATE?:string,
   WS_NEWUSER?:string,
   WS_EDTDATE?:string,
   WS_EDTUSER?:string,
   START_URL?:string,
   RET_CODE?:number,
   RET_MSG?:string
}

export default User;