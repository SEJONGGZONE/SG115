<template>
  <layout :transparent="true">
    <customer-update/>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Layout from "@/layout/LayoutFour.vue"; 
import CustomerUpdate from "@/components/customer/CustomerUpdate.vue";

export default defineComponent({
  components: {
    Layout, 
    CustomerUpdate,
  },
  setup() {
    useHead({
      title: "CustomerUpdate",
    });
  },
});
</script>
