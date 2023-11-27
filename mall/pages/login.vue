<template>
  <layout :transparent="true">
    <login-area></login-area>
  </layout>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import LoginArea from "@/components/login-register/LoginArea.vue";  
  
import Layout from "@/layout/LayoutFour.vue";
export default defineComponent({ 
  components: { Layout,LoginArea },
  setup() { 
    useHead({
      title: "Login",
    });
    return {  };
  }, 
});
</script>
