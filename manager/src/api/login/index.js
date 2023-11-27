import { getAxios } from "@/common/utils.ts";
/************************************************** 회원관리 */
export const loginApi = {
  /** 로그인 */
  executeLogin(params) {
    let data = {
      "@I_ID": params.userId,
      "@I_INPUT_USER": "9999",
      "@I_PASSWORD": params.userPw,
      "@I_USER_TYPE": 99,
    };

    return getAxios().post(`/WEB_LOGIN_SEL`, data);
  },
};
