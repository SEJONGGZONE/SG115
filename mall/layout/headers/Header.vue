<template>
<client-only>
  <!-- header -->
  <header id="header" class="header">
    <div class="h_inner">
        <div class="h_topnav">
            <div class="linkbox">
                <a href="" class="link">로그인</a>
            </div>
            <div class="linkbox">
                <a href="" class="link">회원가입</a>
            </div>
        </div>
        <div class="h_contbox">
            <div class="h_left">
                <div class="h_logo">
                    <a href="" class="link">
                        <img src="~/assets/img/icon_001.png" alt="" class="img-full-kmong">
                    </a>
                </div>
                <div class="h_schbox">
                    <input type="text" class="h_schinput">
                    <button class="btn sch_btn_kmong">
                        <img src="~/assets/img/kmong/img/ic_search.png" alt="" class="img-full-kmong">
                    </button>
                </div>
            </div>
            <div class="h_right">
                <div class="linkbox">
                    <a href="" class="link">
                        <img src="~/assets/img/kmong/img/ic_truck.png" alt="" class="img-full-kmong">
                    </a>
                </div>
                <div class="linkbox">
                    <a href="" class="link">
                        <img src="~/assets/img/kmong/img/ic_locate.png" alt="" class="img-full-kmong">
                    </a>
                </div>
                <div class="linkbox">
                    <a href="" class="link">
                        <img src="~/assets/img/kmong/img/ic_profile.png" alt="" class="img-full-kmong">
                    </a>
                </div>
                <div class="linkbox">
                    <a href="" class="link">
                        <img src="~/assets/img/kmong/img/ic_heart.png" alt="" class="img-full-kmong">
                    </a>
                </div>
                <div class="linkbox cart">
                    <a href="" class="link">
                        <img src="~/assets/img/kmong/img/ic_cart.png" alt="" class="img-full-kmong">
                    </a>
                    <!-- 
                        [확인] 
                        장바구니 팝업 노출 : cartbox의 class에 "show" 추가
                    -->
                    <div class="cartbox">
                        <div class="cart_list">
                            <div class="prd_item">
                                <div class="thumbox">
                                    <a href="" class="link">
                                        <img src="~/assets/img/kmong/img/prd_thum1.jpg" alt="" class="img-full-kmong">
                                    </a>
                                </div>
                                <div class="txtbox">
                                    <p class="name">
                                        [움트리] 실장님 저염 회간장 대...
                                    </p>
                                    <div class="infobox">
                                        <p class="price">999,010원</p>
                                        <div class="bar"></div>
                                        <p class="count">수량 : 999</p>
                                    </div>
                                </div>
                                <div class="del_btn">
                                    <img src="~/assets/img/kmong/img/ic_close.png" alt="" class="img-full-kmong">
                                </div>
                            </div>
                            <div class="prd_item">
                                <div class="thumbox">
                                    <a href="" class="link">
                                        <img src="~/assets/img/kmong/img/prd_thum11.jpg" alt="" class="img-full-kmong">
                                    </a>
                                </div>
                                <div class="txtbox">
                                    <p class="name">
                                        [움트리] 실장님 저염 회간장 대...
                                    </p>
                                    <div class="infobox">
                                        <p class="price">999,010원</p>
                                        <div class="bar"></div>
                                        <p class="count">수량 : 999</p>
                                    </div>
                                </div>
                                <div class="del_btn">
                                    <img src="~/assets/img/kmong/img/ic_close.png" alt="" class="img-full-kmong">
                                </div>
                            </div>
                            <div class="prd_item">
                                <div class="thumbox">
                                    <a href="" class="link">
                                        <img src="~/assets/img/kmong/img/prd_thum14.jpg" alt="" class="img-full-kmong">
                                    </a>
                                </div>
                                <div class="txtbox">
                                    <p class="name">
                                        [움트리] 실장님 저염 회간장 대...
                                    </p>
                                    <div class="infobox">
                                        <p class="price">999,010원</p>
                                        <div class="bar"></div>
                                        <p class="count">수량 : 999</p>
                                    </div>
                                </div>
                                <div class="del_btn">
                                    <img src="~/assets/img/kmong/img/ic_close.png" alt="" class="img-full-kmong">
                                </div>
                            </div>
                            <div class="prd_item">
                                <div class="thumbox">
                                    <a href="" class="link">
                                        <img src="~/assets/img/kmong/img/prd_thum15.jpg" alt="" class="img-full-kmong">
                                    </a>
                                </div>
                                <div class="txtbox">
                                    <p class="name">
                                        [움트리] 실장님 저염 회간장 대...
                                    </p>
                                    <div class="infobox">
                                        <p class="price">999,010원</p>
                                        <div class="bar"></div>
                                        <p class="count">수량 : 999</p>
                                    </div>
                                </div>
                                <div class="del_btn">
                                    <img src="~/assets/img/kmong/img/ic_close.png" alt="" class="img-full-kmong">
                                </div>
                            </div>
                        </div>
                        <div class="cart_bottom">
                            <div class="total">
                                <div class="txt">총 합계 (999)</div>
                                <div class="price">9,999,999원</div>
                            </div>
                            <a href="" class="pay_btn">
                                바로 결제하기
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h_nav">
            <div class="navitem">
                <div class="category_btn link">
                    <img src="~/assets/img/kmong/img/ic_hamburger.png" alt="" class="img-full-kmong">
                    <div class="txt">카테고리</div>
                </div>
                <div class="h_nav_ul">
                    <div class="depth1">
                        <a href="" class="link">냉동식품</a>
                        <div class="h_nav_ul_ul">
                            <div class="depth2">
                                <a href="" class="link">만두/돈까스</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">튀김/탕류/조림</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">냉동어묵/유부/소세지</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">냉동떡류/햄/볶음밥</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">면류/우동/냉면/쫄면</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">냉동소스류/육수류</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">피자치즈/빵가루</a>
                            </div>
                            <div class="slide_banbox">
                                <div class="swiper slide_banner banner1">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum4.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    움트리) 실장님 저염 회간...
                                                </div>
                                            </a>
                                        </div>
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum5.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    움트리) 실장님 저염 회간2...
                                                </div>
                                            </a>
                                        </div>
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum6.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    움트리) 실장님 저염 회간3...
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-button-prev prev_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_left.png" alt="" class="img-full-kmong">
                                    </div>
                                    <div class="swiper-button-next next_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_right.png" alt="" class="img-full-kmong">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">가공식품</a>
                        <div class="h_nav_ul_ul">
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="slide_banbox">
                                <div class="swiper slide_banner banner1">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum1.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    슬라이드 2개
                                                </div>
                                            </a>
                                        </div>
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum2.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    슬라이드 2개 슬라이드 2개 슬라이드 2개슬라이드 2개 슬라이드 2개
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-button-prev prev_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_left.png" alt="" class="img-full-kmong">
                                    </div>
                                    <div class="swiper-button-next next_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_right.png" alt="" class="img-full-kmong">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">장류/소스류</a>
                        <div class="h_nav_ul_ul">
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="depth2">
                                <a href="" class="link">서브 카테고리</a>
                            </div>
                            <div class="slide_banbox">
                                <div class="swiper slide_banner banner1">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <a href="">
                                                <div class="imgbox">
                                                    <img src="~/assets/img/kmong/img/prd_thum3.jpg" alt="" class="img-full-kmong">
                                                </div>
                                                <div class="txt">
                                                    슬라이드 1개
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="swiper-button-prev prev_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_left.png" alt="" class="img-full-kmong">
                                    </div>
                                    <div class="swiper-button-next next_btn">
                                        <img src="~/assets/img/kmong/img/ic_arrow_right.png" alt="" class="img-full-kmong">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">일회용기/잡화</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">주방/생활용품</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">곡류/김치/반찬류</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">고춧가루/건어물/견과</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">커피/음료/유제품</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">냉장, 냉동수산물</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">냉장/냉동축산/난류</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">D2 선주문 야채등</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">캠핑/혼밥몰</a>
                    </div>
                    <div class="depth1">
                        <a href="" class="link">배달용기</a>
                    </div>
                </div>
            </div>
            <div class="navitem">
                <a href="" class="link">오늘의 쿡짱</a>
            </div>
            <div class="navitem">
                <a href="" class="link">베스트</a>
            </div>
            <div class="navitem">
                <a href="" class="link">신상품</a>
            </div>
            <div class="navitem">
                <a href="" class="link">회사소개</a>
            </div>
            <div class="navitem">
                <a href="" class="link">입점/제휴문의</a>
            </div>
        </div>
        <div class="h_mobile tablet">
            <div class="h_schbox">
                <input type="text" class="h_schinput" placeholder="검색어를 입력하세요">
                <button class="btn sch_btn_kmong">
                    <img src="~/assets/img/kmong/img/ic_search.png" alt="" class="img-full-kmong">
                </button>
            </div>
            <div class="menu_icon tablet" onclick="m_menu_ope();">
                <img src="~/assets/img/kmong/img/ic_hamburger.png" alt="" class="img-full-kmong">
            </div>
        </div>
    </div>
</header>

  <!-- search popup start -->
  <search-popup ref="search_popup"/>
  <!-- search popup end -->

  <!-- off canvas start -->
  <off-canvas ref="offcanvas"/>
  <!-- off canvas end -->
</client-only>
</template>

<style>

</style>

<script setup   lang="ts">
// external
import { useCartStore } from '@/store/useCart';
import { useUserStore } from '@/store/useUser';
 
// internal
import * as cartApi from '@/api';
import * as mainApi from '@/api';
import * as productApi from '@/api';
import CartMini from './header-com/CartMini.vue';
import * as common_utils from "@/common/utils.ts";
import SearchPopup from '@/components/common/modals/SearchPopup.vue';
import ExtraInfo from './header-com/ExtraInfo.vue';
import OffCanvas from '@/components/common/sidebar/OffCanvas.vue';
import bg from '~/assets/img/bg/mega-menu-bg.jpg';
//import menuData from "@/mixins/menuData";
import CookImage from '@/components/common/others/CookImage.vue'
  
// interface
interface SearchPopupComponentRef {
    openSearchPopup(): void
}
interface OffCanvasComponentRef {
    OpenOffcanvas(): void
}

const state = useCartStore();
const isSticky = ref(false)
const search_popup = ref<SearchPopupComponentRef | null>(null);
const offcanvas = ref<OffCanvasComponentRef | null>(null);

 //로그인 정보
const store = useUserStore();
const nameS = computed(()=>store.getUserInfo?.NAME);
const clcodeS = computed(()=>store.getUserInfo?.CLCODE);
const userNo = computed(()=>store.getUserInfo?.USER_NO);
const username = computed(()=>{ 
    return store.getUserName
})
const isLogin = computed(()=>{
    return store.isLogin
})

//카트 정보
const cartList = reactive([])
const cartListLen = ref('')
const searchText = ref('')
let cartTotalCnt = 0
let cartTotalAmount = 0;

const menuList = ref()//메뉴 목록
const selCategory_1 = ref('')//9월 7일 요청사항 : 2차 카테고리 선택 시, 헤더의 1차 카테고리 해당항목 표시


/****************************************************************************************** mounted */

const {$bus} = useNuxtApp();
onMounted(async()=>{

        window.addEventListener("scroll", handleSticky);
        useHead({title: "(주)성창FOOD 에 오신걸 환영합니다."});
        $bus.$off("chageCart")
        $bus.$on("chageCart", () => {
            if(nameS){
                if(isLogin.value){
                    doSearchCatrList()
                }
            }else{
                cartList.splice(0, cartList.length);
                cartListLen.value = '0'
            }
        }) 
        if(isLogin.value){//로그인 한 경우만 장바구니 조회
            doSearchCatrList()
        }
        //9월 7일 요청사항 : 2차 카테고리 선택 시, 헤더의 1차 카테고리 해당항목 표시
        $bus.$off("changeCategory")
        $bus.$on("changeCategory", (receivedCategory) => {
            selCategory_1.value = receivedCategory
        });

        await doSearch()
        await doSearchCategory()
        await setMenuData()
        
})
/****************************************************************************************** method */
const goCart = () =>{
    router.push('/cart');
}
const goBoard = () =>{
    router.push('/qnaList');
}

const moveToCheckout=() =>{//결제하기 버튼


    if(!isLogin.value){
        common_utils.fxAlert("로그인 후 이용해 주세요");
        //router.push(`/login`);//로그인화면으로 이동
        return
    }else{
        state.updateCheckedCartList(cartList); 
        let checkItCode = ''
        
        let orderNm = ''
        if(cartList.length > 0){
            if(cartList.length === 1){
                orderNm = `${cartList[0].ITNAME}`
            }else{
                orderNm = `${cartList[0].ITNAME} 외 ${cartList.length-1}`
            }
        } 
        state.setOrderProductNm(orderNm)
        cartList.map(item=>checkItCode += item.ITCODE+"/") 
        location.href= `/checkout?checkItCodes=${checkItCode.replace(/\/$/, '')}`
    }

}
const moveToSearchKeyDown = (e) =>{
   if(e.key === 'enter'){
        moveToSearch()
   }
}
const moveToSearch=() =>{
    if(!searchText.value){
        return
    }
window.location.href = `/searchResultList?keyWord=${searchText.value}`;//categoryList
}


const router = useRouter()
const movePage = (path) =>{
    router.push(path);
}
const handleSticky=() => {
    if (window.scrollY > 80) {
        isSticky.value = true;
    } else {
        isSticky.value = false;
    }
}
const handleOpenSearchBar=() =>{
    const searchPopupRef = search_popup.value;
    if (searchPopupRef) {
      searchPopupRef.openSearchPopup();
    }
}
const handleOffcanvas = () => {
    const offCanvas = offcanvas.value;
    if (offCanvas) {
        offCanvas.OpenOffcanvas(menuList.value);
    }
} 
const delCartList=(col) =>{//장바구니 삭제
      doDelCartList(col)
      checkCartTotalAmount()
}
const checkCartTotalAmount=() =>{//총 합계
    cartTotalCnt = 0
    cartTotalAmount = 0

    for (let i = 0; i < cartList.length; i++) {
        cartTotalCnt += Number(cartList[i].QTY);
        cartTotalAmount += Number(cartList[i].AMOUNT * cartList[i].QTY);
    }
}
const setMenuData=()=> {
    menuList.value = [{
          link: '/#',
          title: '카테고리',
          megamenu: true,
          hasDropdown: true,       
          dropdownItems: dropdownMenu.value
        },              
        // {
        //   link: `/eventList?code=${dataList.value[0]['GEONUM']}&title=${dataList.value[0]['TITLE']}&eventType=20`,
        //   code: dataList.value[0]['GEONUM'],
        //   title: dataList.value[0]['TITLE'],
        // },
        // {
        //   link: `/eventList?code=${dataList.value[1]['GEONUM']}&title=${dataList.value[1]['TITLE']}&eventType=20`,
        //   code: dataList.value[1]['GEONUM'],
        //   title: dataList.value[1]['TITLE'],
        // },
        // {
        //   link: `/eventList?code=${dataList.value[2]['GEONUM']}&title=${dataList.value[2]['TITLE']}&eventType=20`,
        //   code: dataList.value[2]['GEONUM'],
        //   title: dataList.value[2]['TITLE'],
        // },
        // {
        //   link: `/eventList?code=${dataList.value[3]['GEONUM']}&title=${dataList.value[3]['TITLE']}&eventType=20`,
        //   code: dataList.value[3]['GEONUM'],
        //   title: dataList.value[3]['TITLE'],
        // },

        // {
        //   link: '/companyInfo',
        //   title: '회사소개',
        // },
        // {
        //   link: '/consult',
        //   hasDropdown: false,
        //   title: '입점/제휴문의',
        // },
    ]
    
    // 관리자화면에서 관리되는 메뉴 추가..
    dataList.value.forEach(item => {
        menuList.value.push( {
            link: `/eventList?code=${item['GEONUM']}&title=${item['TITLE']}&eventType=20`,
            code: item['GEONUM'],
            title: item['TITLE'],
            }
        )
    }); 
}
    
/****************************************************************************************** api호출 */

const dataList = ref([]);
const doSearch=async() =>{
            
      let param = {
        geonum: '',
        pageSize: '100',
        pageNum: '1',
        inputUser: ''
      };

      try {
        const dataObj = await mainApi.main_getEventList(param);
        const data = dataObj.data;
        if (data.RecordCount > 0) {
            dataList.value = data.RecordSet.filter(item => item.TYPE === '10'); // '메뉴에표시' 기획만 필터링...
            // console.log("-------------------------- 확인중 >")
            // console.log(dataList.value);
        }
      } catch (error) {
        console.error(error);
      }
    }

const doDelCartList = async (col)=>{//장바구니 상품 삭제

    let data;
    let param = {
            cartNo : col.CART_NO ?? "",
            seq : col.SEQ ?? ""
    }
    try {
        const dataObj = await cartApi.cart_delCartList(param)
        const data = dataObj.data;
        if(data.ResultCode === '00'){
            cartList.splice(0);
            doSearchCatrList()
        }
    } catch (error) {
      console.error(error);
    }
}

const doSearchCatrList = async ()=>{//장바구니 상품 조회
    cartList.splice(0);

    let data;
    let param = {
            clcode : clcodeS.value ?? "",
            userNo : userNo.value?? ""
    }
    try {
      const dataObj = await cartApi.cart_getCartList(param)
      const data = dataObj.data;
      if(data.RecordCount > 0){
        cartList.push(...data.RecordSet);
        cartListLen.value = data.RecordCount
        checkCartTotalAmount()
        let cartNo = cartList.length > 0 ? cartList[0].CART_NO : ''
        localStorage.setItem("CART_NO",cartNo)
      }else{
        localStorage.removeItem("CART_NO")
      }
    } catch (error) {
      console.error(error);
    }
}
const categoryList = ref([])
const dropdownMenu = ref([])
const doSearchCategory = async ()=>{//카테고리1 영역

    let data;
    let param = {
        code : '',
        inputUser : '',
        name : ''
    }
    try {
        const dataObj = await productApi.product_category(param)
        const data = dataObj.data;
        if(data.RecordCount > 0){
            categoryList.value.push(...data.RecordSet);
        }
    } catch (error) {
        console.error(error);
    }finally{

        for (let i = 0; i < categoryList.value.length; i++) {
            let category = categoryList.value[i];
            let formattedItem = {
                link: `/categoryList?code=${category.CODE}&title=${category.NAME}`,
                title: category.NAME,
                code: category.CODE
            }

            dropdownMenu.value.push(formattedItem);
        }
        localStorage.setItem("category1", JSON.stringify(categoryList.value))
    }
}
</script>
