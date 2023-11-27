<template>
  <layout :transparent="true">
    <breadcrumb-area title="Coming Soon" subtitle=""/>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/LayoutFour.vue";
import BreadcrumbArea from "@/components/common/breadcrumb/BreadcrumbArea.vue";


export default defineComponent({
  components: {
    Layout,
    BreadcrumbArea
  },
  setup() {
    useHead({
      title: "Contact",
    });
  },
});
</script>
