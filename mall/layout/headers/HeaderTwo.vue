<template>
  <client-only>
  <header>
    <div id="header-sticky" :class="`header__area header__transparent box-25 ${isSticky ? 'sticky' : ''}`">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-6 col-lg-6">
                    <div class="main-menu d-none d-lg-block p-relative">
                      <nav>
                          <menus/>
                      </nav>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-2 col-md-4 col-sm-4">
                    <div class="logo">
                        <nuxt-link href="/">
                          <img src="~/assets/img/logo/logo.png" alt="logo">
                        </nuxt-link>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-8 col-sm-8">
                    <div class="header__right p-relative d-flex justify-content-between justify-content-sm-end align-items-center">
                        
                        <div @click.prevent="handleOffcanvas" class="mobile-menu-btn d-lg-none">
                            <a href="#" class="mobile-menu-toggle"><i class="fas fa-bars"></i></a>
                        </div>
                        <div class="header__action">
                            <ul>
                                <li>
                                  <a @click.prevent="handleOpenSearchBar" href="#" class="search-toggle">
                                    <i class="ion-ios-search-strong"></i> Search
                                  </a>
                                </li>
                                <li><a href="#" class="cart"><i class="ion-bag"></i> Cart 
                                  <span>({{state.totalPriceQuantity.quantity}})</span>
                                </a>
                              <!-- cart mini start -->
                              <cart-mini/>
                              <!-- cart mini end -->
                                </li>
                                <li><a href="#"><i class="far fa-bars"></i></a>
                                  <!-- extra info start -->
                                  <extra-info/>
                                  <!-- extra info end -->
                                </li>
                            </ul>
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

<script lang="ts">
// external
import {defineComponent} from 'vue';
import { useCartStore } from '@/store/useCart';
// internal
import Menus from './Menus.vue';
import CartMini from './header-com/CartMini.vue';
import ExtraInfo from './header-com/ExtraInfo.vue';
import SearchPopup from '@/components/common/modals/SearchPopup.vue';
import OffCanvas from '@/components/common/sidebar/OffCanvas.vue';
// interface
interface SearchPopupComponentRef {
  openSearchPopup(): void
}
interface OffCanvasComponentRef {
    OpenOffcanvas(): void
}

  export default defineComponent ({
  components: { Menus, CartMini, ExtraInfo, SearchPopup, OffCanvas },
    data() {
        return {
          isSticky: false,
          showSearch:false,
       }
    },
    methods: {
        handleSticky() {
        if (window.scrollY > 80) {
            this.isSticky = true;
        } else {
            this.isSticky = false;
        }
        },
        handleOpenSearchBar(){
          const searchPopupRef = this.$refs.search_popup as SearchPopupComponentRef
          searchPopupRef.openSearchPopup()
        },
        handleOffcanvas(){
          const offCanvas = this.$refs.offcanvas as OffCanvasComponentRef
          offCanvas.OpenOffcanvas()
        }
    },
    setup() {
        const state = useCartStore();
        return {state}   
    },
    mounted() {
        window.addEventListener("scroll", this.handleSticky);
    },
  })
</script>
