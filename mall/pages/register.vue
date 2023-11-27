<template>
  <layout :transparent="true">
    <register-area/>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/LayoutFour.vue";
import BreadcrumbArea from "@/components/common/breadcrumb/BreadcrumbArea.vue";
import RegisterArea from "@/components/login-register/RegisterArea.vue";

export default defineComponent({
  components: {
    Layout,
    BreadcrumbArea,
    RegisterArea,
  },
  setup() {
    useHead({
      title: "Register",
    });
  },
});
</script>
