<template>
  <div class="banner__area pt-20">
    <div class="container custom-container">
      <div>
        <div class="row">
          <div v-for="item in categoryData.slice(0, 3)" :key="item.id" class="col-xl-4 col-lg-4 col-md-6">
            <div class="banner__item mb-30 p-relative">
              <div class="banner__thumb fix">
                <nuxt-link href="/shop" class="w-img">
                  <img :src="item.img" alt="banner" />
                </nuxt-link>
              </div>
              <div class="banner__content p-absolute transition-3">
                <h5>
                  <nuxt-link href="/shop" >
                    <span v-html="item.parentTitle"></span>
                  </nuxt-link>
                </h5>
                <nuxt-link href="/shop" class="link-btn"></nuxt-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import categoryData from "~/mixins/categoryData";

export default defineComponent({
  mixins: [categoryData],
  props: {
   
  },
});
</script>
