import { defineStore } from 'pinia'; 
export const useUserStore = defineStore('user',()=> {
    const notEnterPath=[
      '/favList'
    ]
    const userInfo = ref({ 
        USER_NO:0,
        TYPE:"",
        CLCODE :"",
        SUCODE :"",
        ID :"", 
        EMAIL :"",
        NAME :"",
        PHONE : "",
        COMPANY_CORPNO :"",
        COMPANY_NAME :"",
        ADDRESS1 :"",
        ADDRESS2 :"", 
        CORP_IMG_URL :"", 
        AGREE_01_YN: "",
        AGREE_02_YN: "",
        AGREE_03_YN: "",
        AGREE_EMAIL_YN: "",
        AGREE_SMS_YN: "",
        POST_NO: "",
        CORP_FILE_NO: "",
    })
    // const doubleCount = computed(() => count.value * 2)  
    const getUserInfo = computed(()=>{ 
      if (process.client) {
        const data = sessionStorage.getItem('userInfo');
        if (data) {
          userInfo.value = JSON.parse(data);
          return userInfo.value
        } else {
          sessionStorage.setItem('userInfo', JSON.stringify({}));
          userInfo.value ={ 
              USER_NO:0,
              TYPE:"",
              CLCODE :"",
              SUCODE :"",
              ID :"", 
              EMAIL :"",
              NAME :"",
              PHONE : "",
              COMPANY_CORPNO :"",
              COMPANY_NAME :"",
              ADDRESS1 :"",
              ADDRESS2 :"",
              CORP_IMG_URL :"" ,
              AGREE_01_YN: "",
              AGREE_02_YN: "",
              AGREE_03_YN: "",
              AGREE_EMAIL_YN: "",
              AGREE_SMS_YN: "",
              POST_NO: "",
              CORP_FILE_NO: "",
          }
          return userInfo.value;
        }
      } else {
        return userInfo.value;
      }
    })
    function setUserInfo(loginUser:any){
      
      userInfo.value = loginUser
      sessionStorage.setItem('userInfo', JSON.stringify(userInfo.value));
    }
    function setIsLogin(isLogin : boolean){
      if(isLogin){
        useNuxtApp().$toast.success("로그인이 완료 되었습니다.");
      }else{ 
        useNuxtApp().$toast.success("로그아웃 되었습니다.");
      }
    }

    const isLogin = computed(()=>!!userInfo.value?.ID)  
    const userClcode = computed(()=>userInfo.value?.CLCODE)  
    const userNo = computed(()=>userInfo.value?.USER_NO)  
    const getUserName = computed(()=>{
      if(userInfo.value?.TYPE === '20'){
        return`${userInfo.value?.COMPANY_NAME}(${userInfo.value?.NAME})`
      }else{
        return userInfo.value?.NAME
      } 
    })

    function logout(){
      setIsLogin(false);
      setUserInfo({}) 
      localStorage.removeItem('checkedCartList');
      localStorage.removeItem('BUY_CART_NO');
      localStorage.removeItem('CART_NO');
      localStorage.removeItem('cart_products');
      if(notEnterPath.includes(location.pathname)){ // 로그인 허용 페이지인 경우 메인화면으로 전환
        location.href = '/'
       // useNuxtApp().$router.push('/')
      }
    }

    return { setUserInfo, getUserInfo,setIsLogin ,isLogin,logout,getUserName,userClcode,userNo}

  // state: () => ({ 
  //   isLogin: false as boolean,
  //   userInfo : useStorage('userInfo', [] ) as any
  // }),
  // actions: {
  //   setIsLogin(isLogin : boolean){
  //     this.isLogin = isLogin
  //     if(this.isLogin){
  //       useNuxtApp().$toast.success("로그인이 완료 되었습니다.");
  //     }else{
  //       this.logOut()
  //       useNuxtApp().$toast.success("로그아웃 되었습니다.");
  //     }
  //   },
  //   setUserInfo(userInfo : userType){
  //     this.userInfo = userInfo 
  //   },  
  //   logOut(){       
  //     this.userInfo = {} as userType 
  //   }
  // },
  // getters: {
  //   getUserInfo:(state)=>{
  //       console.log("여기만 호출하나33?")
  //     if (process.client) {
  //       const data = localStorage.getItem('userInfo');
  //        console.log("여기만 호출하나?22")
  //       if (data) {
  //         return state.userInfo = JSON.parse(data);
  //       } else {
  //         console.log("여기만 호출하나111?")
  //         localStorage.setItem('userInfo', JSON.stringify([]));
  //         return state.userInfo = {} as userType;
  //       }
  //     } else {
  //       return state.userInfo;
  //     } 
  //   },
  // }
  
})