<template>
<client-only>
  <header>
    <div id="header-sticky" class="header__area white-bg sticky">
        <div class="container cus-header">
            <div class="row align-items-center mobileMode custom-mobile" style="padding-bottom:8px;border-bottom: 1px dashed #595959;">
                <div @click.prevent="handleOffcanvas" class="mobile-menu-btn" style="width: 0px; padding-top: 12px; left: 5px; position: absolute;">
                    <a href="#" class="mobile-menu-toggle" ><i class="fas fa-bars" style="font-size:24px;"></i></a>
                </div> 
                <div class="header__action" style="width: 100px;position: absolute;left: 30px;padding-top: 16px;">
                    <div>
                        <ul>
                            <li>
                                <a @click.prevent="handleOpenSearchBar" href="#" class="search-toggle">
                                    <i class="fas fa-search"></i>
                                </a>
                            </li>
                        </ul>   
                    </div>                
                </div>
                <div style="width: 100px;position: absolute;left: calc(50% - 50px);padding-top: 20px;">
                    <div class="logo-mobile" style="cursor: pointer;height: 50px;">
                        <a @click="movePage('/')">
                            <img src="~/assets/img/icon_001.png" alt="logo" style="height:100%;">
                        </a>
                    </div>
                </div>
                <div class="header__action">
                    <div style="text-align: end;">
                        <ul>
                            
                            <li>
                                <a href="#" style="padding:20px 0px 10px 0;">
                                    <i class="fas fa-user"></i>
                                </a>
                                <extra-info/>
                            </li>
                            <li>
                                <a @click="goCart()" class="cart" style="padding:20px 0px 10px 0;">
                                    <i class="fas fa-cart-plus"></i><span class="cart-number-2" style="background:#ee5555;" v-if="isLogin && cartList.length > 0">{{cartListLen}}</span>
                                </a>
                                <div class="mini-cart" style="width:400px;">
                                    <div v-if="!isLogin || cartList.length === 0">
                                        <h5><a  @click="goCart()">장바구니가 비어있습니다.</a></h5>
                                    </div>
                                    <div v-else class="mini-cart-inner">
                                        <ul :class="`mini-cart-list ${cartList.length === 1 ? 'slider-height_1' :
                                                                    cartList.length === 2 ? 'slider-height_2' : 'slider-height'}`">
                                            <li v-for="(item,i) in cartList" :key="i">
                                                <div class="cart-img f-left">
                                                    <a :href="`/product-details/${item.ITCODE}`">
                                                        <CookImage :image="item.ITEM_MAIN_IMAGE" :size="'imgmS'"/>
                                                    </a>
                                                </div>
                                                <div class="cart-content f-left text-left">
                                                    <h4 style="font-size:20px;font-weight:500;margin-right:27px;">
                                                    <a :href="`/product-details/${item.ITCODE}`">
                                                        <span v-html="item.ITNAME"></span>
                                                    </a>
                                                    </h4>
                                                </div>
                                                <div class="cart-content f-left text-left">
                                                    <div class="cart-price">
                                                        <span class="price" style="color:#595959;font-size:15px;">{{common_utils.formattedPrice(item.AMOUNT)}}원  / 수량 : {{ common_utils.formattedPrice(item.QTY) }}</span>
                                                    </div>
                                                </div>
                                                <div class="del-icon f-right mt-5" @click="delCartList(item)">
                                                    <a href="#">
                                                        <i class="fal fa-times" style="border:1px solid #959595;padding:3px 6px;"></i>
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="total-price d-flex justify-content-between mb-30">
                                            <span style="font-size:18px;">총 합계({{ cartTotalCnt }}) : {{ common_utils.formattedPrice(cartTotalAmount) }}원</span>
                                        </div>
                                        <div class="checkout-link">
                                            <a class="os-btn" style="font-size:18px;font-weight: 500;cursor: pointer;" @click="moveToCheckout()">바로결제 하기</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>    
            </div>
            <!--모바일 메인 메뉴 슬라이더 영역-->
            <div class="scrollmenu mobile-menu-slider">
                <a href="/eventList?code=10&title=브랜드몰&eventType=20">브랜드몰</a>
                <a href="/eventList?code=11&title=캠핑/혼밥몰&eventType=20">캠핑/혼밥몰</a>
                <a href="/eventList?code=12&title=초특가할인몰&eventType=20">초특가할인몰</a>
                <a href="/eventList?code=13&title=핫브랜드&eventType=20">핫브랜드</a>
                <a href="/companyInfo">회사소개</a>
                <a href="/consult">입점/제휴문의</a>
            </div> 
            
            
            <!--모바일 메인 메뉴 슬라이더 영역2(1차카테고리표현)-->
            <!-- <div class="scrollmenu mobile-menu-slider cat_icon_area col-xl-12">
                <div class="cat_icon_style_01"><img src="~/assets/img/category/01.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/02.png"/><span>메뉴명#2</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/03.png"/><span>메뉴명#3</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/04.png"/><span>메뉴명#4</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/05.png"/><span>메뉴명#5</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/06.png"/><span>메뉴명#6</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/07.png"/><span>메뉴명#7</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/08.png"/><span>메뉴명#8</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/09.jpg"/><span>메뉴명#9</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/10.jpg"/><span>메뉴명#10</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/11.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/12.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/13.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/14.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/15.png"/><span>메뉴명#1</span></div>
                <div class="cat_icon_style_01"><img src="~/assets/img/category/16.png"/><span>메뉴명#1</span></div>
            </div> -->
            <div class="row align-items-center non_mobileMode" >
                <div class="col-xl-1 col-lg-1 col-md-2 col-sm-2 col-2 mt-2 logoArea">
                    <div class="logo" style="cursor: pointer;">
                        <a @click="movePage('/')">
                            <img src="~/assets/img/icon_001.png" alt="logo" style="height: 80%;margin-top: 20px;z-index: 1000000;position: relative;">
                        </a>
                    </div>
                </div>
                <!-- 모바일일때 적용
                -->
                <div class="col-xl-11 col-lg-11 col-md-10 col-sm-10 main-menu-col">
                    <div class="header__right p-relative d-flex justify-content-between align-items-center">
                        <div class="main-menu d-none d-lg-block">
                            <nav>
                                <ul>
                                   <li v-for="(item, index) in menuList" :key="index" class="active" :class="item?.hasDropdown ? 'has-dropdown mega-menu' : ''">
                                        <a :href="`${item.link}`" >{{ item.title }}</a>
                                        <ul v-if="item?.hasDropdown && !item.megamenu" class="submenu transition-3">
                                            <li v-for="(menu, index) in item.dropdownItems" :key="index">
                                                <a :href="`${menu.link}`" >{{ menu.title }}</a>
                                            </li>
                                        </ul>
                                        <ul v-if="item.hasDropdown && item.megamenu" class="submenu transition-3" :style="{ backgroundImage: `url(${bg})` }">
                                            <li v-for="(m_mnu, index) in item.dropdownItems" :key="index" class="has-dropdown" >
                                                <a :href="`${m_mnu.link}`" :style="`${selCategory_1 === m_mnu.code ? 'border: 2px solid #222222':''}`">{{m_mnu.title}}</a>
                                                <ul>
                                                    <li v-if="m_mnu.dropdownMenu" v-for="(m, index) in m_mnu.dropdownMenu" :key="index">
                                                        <a :href="`${m.link}`" >{{ m.title }}</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div @click.prevent="handleOffcanvas" class="mobile-menu-btn d-lg-none">
                            <a href="#" class="mobile-menu-toggle"><i class="fas fa-bars"></i></a>
                        </div>

                        <div class="header__action">
                            <div data-aos="fade-up" class="welcome-user">
                                <span v-if="isLogin"> {{ username }} 님 환영합니다.</span>
                            </div>
                            <div style="text-align: end;">
                                <ul>
                                    <li data-aos="fade-down" data-aos-delay="500" v-if="isLogin">
                                        <a @click="goBoard()" class="search-toggle cursor-pointer">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                    </li>
                                    <li data-aos="fade-up" data-aos-delay="600">
                                        <a @click.prevent="handleOpenSearchBar" href="#" class="search-toggle">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </li>
                                    <li data-aos="fade-right" data-aos-delay="700" style="z-index:50;">
                                        <a href="#">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <extra-info/>
                                    </li>
                                    <li data-aos="fade-up" data-aos-delay="800" style="z-index:50;">
                                        <a @click="goCart()" href="#" class="cart cursor-pointer">
                                            <i class="fas fa-cart-plus"></i><span class="cart-number-2" style="background:#ee5555;" v-if="isLogin && cartList.length > 0">{{cartListLen}}</span>
                                        </a>
                                        <div class="mini-cart" style="width:400px;">
                                            <div v-if="!isLogin || cartList.length === 0">
                                                <h5><a  @click="goCart()">장바구니가 비어있습니다.</a></h5>
                                            </div>
                                            <div v-else class="mini-cart-inner">
                                                <ul :class="`mini-cart-list ${cartList.length === 1 ? 'slider-height_1' :
                                                                            cartList.length === 2 ? 'slider-height_2' : 'slider-height'}`">
                                                    <li v-for="(item,i) in cartList" :key="i">
                                                        <div class="cart-img f-left">
                                                            <a :href="`/product-details/${item.ITCODE}`">
                                                                <CookImage :image="item.ITEM_MAIN_IMAGE" :size="'imgmS'"/>
                                                            </a>
                                                        </div>
                                                        <div class="cart-content f-left text-left">
                                                            <h4 style="font-size:20px;font-weight:500;margin-right:27px;">
                                                            <a :href="`/product-details/${item.ITCODE}`">
                                                                <span v-html="item.ITNAME"></span>
                                                            </a>
                                                            </h4>
                                                        </div>
                                                        <div class="cart-content f-left text-left">
                                                            <div class="cart-price">
                                                                <span class="price" style="color:#595959;font-size:15px;">{{common_utils.formattedPrice(item.AMOUNT)}}원  / 수량 : {{ common_utils.formattedPrice(item.QTY) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="del-icon f-right mt-5" @click="delCartList(item)">
                                                            <a href="#">
                                                                <i class="fal fa-times" style="border:1px solid #959595;padding:3px 6px;"></i>
                                                            </a>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="total-price d-flex justify-content-between mb-30">
                                                    <span style="font-size:18px;">총 합계({{ cartTotalCnt }}) : {{ common_utils.formattedPrice(cartTotalAmount) }}원</span>
                                                </div>
                                                <div class="checkout-link">
                                                    <a class="os-btn" style="font-size:18px;font-weight: 500;cursor: pointer;" @click="moveToCheckout()">바로결제 하기</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
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
        {
          link: `/eventList?code=${dataList.value[0]['GEONUM']}&title=${dataList.value[0]['TITLE']}&eventType=20`,
          code: dataList.value[0]['GEONUM'],
          title: dataList.value[0]['TITLE'],
        },
        {
          link: `/eventList?code=${dataList.value[1]['GEONUM']}&title=${dataList.value[1]['TITLE']}&eventType=20`,
          code: dataList.value[1]['GEONUM'],
          title: dataList.value[1]['TITLE'],
        },
        {
          link: `/eventList?code=${dataList.value[2]['GEONUM']}&title=${dataList.value[2]['TITLE']}&eventType=20`,
          code: dataList.value[2]['GEONUM'],
          title: dataList.value[2]['TITLE'],
        },
        {
          link: `/eventList?code=${dataList.value[3]['GEONUM']}&title=${dataList.value[3]['TITLE']}&eventType=20`,
          code: dataList.value[3]['GEONUM'],
          title: dataList.value[3]['TITLE'],
        },
        {
          link: '/companyInfo',
          title: '회사소개',
        },
        {
          link: '/consult',
          hasDropdown: false,
          title: '입점/제휴문의',
        },
      ]
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
        const data = await mainApi.main_excuteEventMng(param);
        if (data.RecordCount > 0) {
            dataList.value = data.RecordSet.filter(item => item.TYPE === '20');
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
      data = await cartApi.cart_delCartList(param)
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
      data = await cartApi.cart_getCartList(param)
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
        data = await productApi.product_category(param)
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
