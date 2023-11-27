<template>
  <layout :transparent="true">
    <order-complete></order-complete>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import OrderComplete from "@/components/checkout/OrderComplete.vue";  
  
import Layout from "@/layout/LayoutFour.vue";
export default defineComponent({ 
  components: { Layout,OrderComplete },
  setup() { 
    useHead({
      title: "OrderComplete",
    });
    return {  };
  }, 
});
</script>
