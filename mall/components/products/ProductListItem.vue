<template>
  <div class="product__wrapper mb-40">
      <div class="row">
          <div class="col-xl-4 col-lg-4">
              <div class="product__thumb">
                  <nuxt-link :href="`/product-details/${col.ITCODE}`" class="w-img">
                      <img :src="col.ITEM_MAIN_IMAGE ? col.ITEM_MAIN_IMAGE : noImg" alt="product-img">
                  </nuxt-link>
              </div>
          </div>
          <div class="col-xl-8 col-lg-8">
              <div class="product__content p-relative">
                  <div class="product__content-inner list">
                      <h4>
                        <nuxt-link :href="`/product-details/${col.ITCODE}`">
                          <span v-html="col.ITNAME"></span>
                        </nuxt-link>
                      </h4>
                      <div class="product__price-2 mb-10">
                        <span>{{common_utils.formattedPrice(col.AMOUNT)}} 원  / 관심수 : {{ common_utils.formattedPrice(col.FAVITEM_CNT) }}</span>
                      </div>
                  </div>
                  <div class="add-cart-list d-sm-flex align-items-center">
                      <a href="#" class="add-cart-btn mr-10">+ 장바구니</a>
                      <div class="product__action-2 transition-3 mr-20">
                        <a @click.prevent="wishlistState.add_wishlist_product(col)" href="#" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to Wishlist">
                          <i class="fal fa-heart"></i>
                        </a>
                        <!-- Button trigger modal -->
                        <a @click.prevent="store.initialOrderQuantity" href="#" data-bs-toggle="modal" :data-bs-target="`#productModalListId-${col.ITCODE}`">
                            <i class="fal fa-search"></i>
                        </a>
                      </div>
                  </div>
                  <!-- shop modal start -->
              </div>
          </div>
      </div>
  </div>

  <!-- product modal start -->
  <!--<product-modal :item="col" :list="true"/>-->
  <!-- product modal end -->
</template>

<script lang="ts">
import { defineComponent,PropType } from 'vue'
import ProductType from '@/types/productType';
import ProductModal from '../common/modals/ProductModal.vue';
import { useCartStore } from '@/store/useCart';
import { useCompareStore } from '@/store/useCompare';
import { useWishlistStore } from '@/store/useWishlist';
import noImg from "~/assets/img/no_img.png";
import * as common_utils from "@/common/utils.ts";
export default defineComponent({
  components: { ProductModal },
  props:{
    col:{
      type:Object as PropType<ProductType>,
      default:{},
      required:true
    }
  },
  setup () {
    const store = useCartStore();
    const compareState = useCompareStore();
    const wishlistState = useWishlistStore();
    return {store,compareState,wishlistState}
  }
})
</script>

<style scoped>

</style>