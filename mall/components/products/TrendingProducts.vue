<template>
  <section class="product__area pt-60 pb-100">
    <div class="custom-container">
      <div class="row">
        <div class="col-xl-12">
          <div class="section__title-wrapper text-center mb-55 p-relative">
            <div class="section__title mb-10">
              <h2 style="font-size:30px;">관심상품</h2>
            </div>
          </div>
        </div>
      </div>
      <div class="product__slider product__slider-4">
        <div class="row">
          <div
            v-for="item in store.products.slice(0,12)"
            :key="item.id"
            class="col-lg-3 col-md-4 product__item"
          >
            <product-item :item="item" />
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-12">
          <div class="product__load-btn text-center mt-25">
            <a href="/shop" class="os-btn os-btn-3">더 보기</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useProductsStore } from "@/store/useProducts";
import ProductItem from "./ProductItem.vue";

export default defineComponent({
  components: { ProductItem },
  data() {
    
  },
  props:{
   
  },
  methods: {
   
  },
  setup() {
    const store = useProductsStore();
    const trending_prd = store.products.filter((p) => p.trending)
    return {
      store,
    }
  }
});
</script>
