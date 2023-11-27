<template>
  <layout :transparent="true">
    <delivery-info/>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import DeliveryInfo from "@/components/customer/DeliveryInfo.vue";  
  
import Layout from "@/layout/LayoutFour.vue";
export default defineComponent({ 
  components: { Layout,DeliveryInfo },
  setup() { 
    useHead({
      title: "DeliveryInfo",
    });
    return {  };
  }, 
});
</script>
