<script setup lang="ts">
import { getCurrentInstance, onMounted,watch } from "vue";
import { RouterLink, RouterView, useRouter } from "vue-router";
import { useAppOptionStore } from "@/stores/app-option";
import { useAppVariableStore } from "@/stores/app-variable";
import { ProgressFinisher, useProgress } from "@marcoschulte/vue3-progress";
import AppSidebar from "@/components/app/Sidebar.vue";
import AppSidebarRight from "@/components/app/SidebarRight.vue";
import AppHeader from "@/components/app/Header.vue";
import AppTopMenu from "@/components/app/TopMenu.vue";
import AppFooter from "@/components/app/Footer.vue";
import AppThemePanel from "@/components/app/ThemePanel.vue";
import { Toast } from "bootstrap";
import routers from "./router";

const appOption = useAppOptionStore();
const appVariable = useAppVariableStore()
const internalInstance = getCurrentInstance();
const router = useRouter();
watch(() => appVariable.toastMsg, (newVal, oldVal) => {
  toastpop()
})
const toastpop = () => {
  const toast = new Toast(document.getElementById("toastMsg"));
  if(appVariable.toastMsg){
  	let data = toast.show();
	console.log(data)
  }
  setTimeout(()=>{appVariable.toastMsg = undefined},3000)
};

const progresses = [] as ProgressFinisher[];

const permitUrl = [
  //로그인 없이 허용 가능한 페이지
  { url: "/user/login" }, //로그인
  { url: "/user/userPayment" }, // 수기결제(사용자)
  { url: "/user/managerPayment" }, // 수기결제(관리자)
];
const validationUrl = (nowUrl: string) => {
  //허용 가능한 페이지 체크
  const permitPage = permitUrl.filter((item) => nowUrl.indexOf(item.url) > -1);
  let returnData = false
  if (permitPage.length > 0) {
    returnData = true;
  }
  setPageOption(returnData)
  return returnData;
};

const setPageOption = (isSet:boolean)=>{
	console.log(isSet)
	if(isSet){
		appOption.appContentClass = 'p-0';
	}else{
		appOption.appContentClass = '';
	}
	appOption.appSidebarHide = isSet;
	appOption.appHeaderHide = isSet;
}

const initData = () => {
  progresses.push(useProgress().start());
  appOption.appSidebarMobileToggled = false;
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
};

routers.beforeEach(async (to, from) => {
  const userInfo = sessionStorage.getItem("userInfo");
  validationUrl(to.path)
  if (!userInfo) {
    if (!validationUrl(to.path)) {
      //허용 불가 페이지인 경우 로그인 페이지로 이동
      router.push("/");
    } else {
      initData();
    }
  } else {
    initData();
  }
});
routers.afterEach(async (to, from) => {
  progresses.pop()?.finish();
});
</script>

<template>
  
    <div class="wrap">
      <div class="header">
        <h1>
          <img src="../asset/img/img/logo.png" alt="쿡짱" />
        </h1>
        <div class="part__buttons">
          <button class="btn_logout">로그아웃</button>
        </div>
      </div>
      <div class="layout_bundle">
        <div class="lnb">
          <button class="btn_fold" onclick="onClickFoldButton()">
            <i class="fa-solid fa-arrow-left"></i>
          </button>
          <div class="part__lnb">
            <div class="part__login_info">
              <div class="item__profile">
                <img src="../asset/img/img/logo.png" alt="쿡짱" />
              </div>
              <b class="item__name">관리자</b>a
            </div>
            <div class="part__menu_list">
              <ul class="menu_list">
                <li>
                  <button class="active">
                    <i class="fa-solid fa-users"></i>
                    <span>회원/거래처관리</span>
                    <i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                  <ul class="active">
                    <li>
                      <button class="active"><span>회원관리</span></button>
                    </li>
                    <li>
                      <button><span>거래처수기결제</span></button>
                    </li>
                  </ul>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-layer-group"></i><span>상품관리</span>
                    <i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-cart-shopping"></i
                    ><span>주문관리</span>
                    <i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-calculator"></i><span>정산관리</span>
                    <i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-comments"></i><span>운영관리</span
                    ><i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                </li>
                <li>
                  <button>
                    <i class="fa-solid fa-map"></i><span>지도관리</span>
                    <i class="fa-solid fa-angle-right item__angle"></i>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="section section__management">
          <div class="group__title">
            <h2>회원관리</h2>
            <div class="part__buttons">
              <button>
                <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
              </button>
            </div>
          </div>
          <div class="group__search">
            <div class="part__search_box">
              <input type="text" placeholder="검색어를 입력하세요" />
              <button>
                <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
              </button>
            </div>
          </div>
          <div class="group__contents">
            <div class="part__data_list">
              <div class="item__scroll">
                <div class="unit__scroll">
                  <table>
                    <thead>
                      <th>
                        <div class="unit_bundle">
                          NO
                          <div class="unit__buttons">
                            <button>
                              <i class="fa-solid fa-angle-up"></i>
                            </button>
                            <button class="active">
                              <i class="fa-solid fa-angle-down"></i>
                            </button>
                          </div>
                        </div>
                      </th>
                      <th>
                        <div class="unit_bundle">
                          거래처명
                          <div class="unit__buttons">
                            <button>
                              <i class="fa-solid fa-angle-up"></i>
                            </button>
                            <button class="active">
                              <i class="fa-solid fa-angle-down"></i>
                            </button>
                          </div>
                        </div>
                      </th>
                      <th>
                        <div class="unit_bundle">
                          사업자번호
                          <div class="unit__buttons">
                            <button>
                              <i class="fa-solid fa-angle-up"></i>
                            </button>
                            <button class="active">
                              <i class="fa-solid fa-angle-down"></i>
                            </button>
                          </div>
                        </div>
                      </th>
                      <th>
                        <div class="unit_bundle">
                          사용자
                          <div class="unit__buttons">
                            <button>
                              <i class="fa-solid fa-angle-up"></i>
                            </button>
                            <button class="active">
                              <i class="fa-solid fa-angle-down"></i>
                            </button>
                          </div>
                        </div>
                      </th>
                      <th>휴대폰번호</th>
                      <th>이메일</th>
                      <th>최종일시</th>
                    </thead>
                    <tbody>
                      <tr class="active">
                        <td>1</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>5</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>6</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>7</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>8</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>9</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>10</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>

                      <tr>
                        <td>9</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>10</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>11</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                      <tr>
                        <td>12</td>
                        <td>
                          <span>
                            북문로 떡볶이 (봉명) 북문로 떡볶이 (봉명)</span
                          >
                        </td>
                        <td><span>301929393</span></td>
                        <td><span>오영오</span></td>
                        <td><span>01020268712</span></td>
                        <td><span>rkskek@daum.net</span></td>
                        <td><span>2022-05-25 15:31:24</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="item__buttons">
                <button><i class="fa-solid fa-plus"></i>더보기</button>
              </div>
            </div>
            <div class="part__data_detail">
              <div class="item__title">
                <i class="fa-solid fa-angle-right item__angle"></i>
                <span>상세보기</span>
              </div>
              <div class="item__contents">
                <div>
                  <p>거래처명</p>
                  <input type="text" value="북문로떡볶이" />
                </div>
                <div>
                  <p>사업자번호</p>
                  <input type="text" value="북문로떡볶이" />
                </div>
                <div>
                  <p>사용자</p>
                  <input type="text" value="북문로떡볶이" />
                </div>
                <div>
                  <p>휴대폰번호</p>
                  <input type="text" value="북문로떡볶이" />
                </div>
                <div>
                  <p>이메일</p>
                  <input type="text" value="dhwltn78@daum.net" />
                </div>
                <div>
                  <p>가입여부</p>
                  <div class="radio_group">
                    <label>
                      <input type="radio" value="Y" name="a" checked /><span
                        >승인</span
                      >
                    </label>
                    <label>
                      <input type="radio" value="N" name="a" /><span>거절</span>
                    </label>
                  </div>
                </div>
                <div>
                  <p>선결제필수 여부</p>
                  <div class="radio_group">
                    <label>
                      <input type="radio" value="Y" name="b" /><span>예</span>
                    </label>
                    <label>
                      <input type="radio" value="N" name="b" checked /><span
                        >아니오</span
                      >
                    </label>
                  </div>
                </div>
              </div>
              <div class="item__buttons">
                <button class="btn_close">
                  <i class="fa-regular fa-circle-xmark"></i><span>닫기</span>
                </button>
                <button class="btn_save">
                  <i class="fa-regular fa-circle-check"></i><span>저장</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<style>
.progressbar {
  position: fixed;
  bottom: 50%;
  left: 55%;
}

.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}


.toasCenter {
  width: 350px;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>