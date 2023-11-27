<template>
  <layout :transparent="true">
    <breadcrumb-area title="Shop" subtitle="Shop" />
    <!-- error area start -->
    <section class="error__area pt-60 pb-100">
      <div class="container">
        <div class="col-xl-8 offset-xl-2 col-lg-8 offset-lg-2">
          <div class="error__content text-center">
            <div class="error__number">
              <h1>{{error.statusCode}}</h1>
            </div>
            <span>not found {{error.message}}</span>
            <h2>Nothing To See Here!</h2>
            <p>
              The page are looking for has been moved or doesn’t exist anymore,
              if you like you can return to our homepage. If the problem
              persists, please send us an email to
              <span class="highlight comment">basictheme400@gmail.com</span>
            </p>

            <div class="error__search">
              <button @click="handleClearError" class="os-btn os-btn-3 os-btn-black">
                Back To Home
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- error area end -->
  </layout>
</template>

<script lang="ts" setup>
import Layout from "./layout/Layout.vue";
import BreadcrumbArea from "./components/common/breadcrumb/BreadcrumbArea.vue";

defineProps(["error"]);
const handleClearError = () => clearError({redirect:'/'})
</script>
