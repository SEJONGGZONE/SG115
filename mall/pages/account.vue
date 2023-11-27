<template>
  <layout :transparent="true">
    <breadcrumb-area title="Account" subtitle="Account"/>
    <profile-area/>
    <profile-menu-area/>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/Layout.vue";
import BreadcrumbArea from "@/components/common/breadcrumb/BreadcrumbArea.vue";
import ProfileArea from "@/components/profile/ProfileArea.vue";
import ProfileMenuArea from "@/components/profile/ProfileMenuArea.vue";

export default defineComponent({
  components: {
    Layout,
    BreadcrumbArea,
    ProfileArea,
    ProfileMenuArea,
  },
  setup() {
    useHead({
      title: "Account",
    });
  },
});
</script>
