<template>
 <Form :validation-schema="schema" @submit="onSubmit">
      <div class="mb-20">
        <label for="name">Username <span>**</span></label>
        <Field name="name" id="name" type="text" placeholder="Enter Username" />
        <ErrorMessage name="name" class="text-danger" />
      </div>

      <div class="mb-20">
        <label for="email-id">Email Address <span>**</span></label>
        <Field name="email" id="email-id" type="text" placeholder="Email address..." />
        <ErrorMessage name="email" class="text-danger" />
      </div>

      <div class="mb-20">
        <label for="pass">Password <span>**</span></label>
        <Field name="password" id="pass" type="password" placeholder="Enter password..." />
        <ErrorMessage name="password" class="text-danger" />
      </div>

      <div class="mt-10"></div>
      <button type="submit" class="os-btn w-100">Register Now</button>
      <div class="or-divide"><span>or</span></div>
      <nuxt-link href="/login" class="os-btn os-btn-black w-100">login Now</nuxt-link>
  </Form>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { Field, Form, ErrorMessage } from "vee-validate";
import * as yup from "yup";

import { fxAlert,fxConfirm } from "@/common/utils.ts"; 
export default defineComponent({
  components: { Field, Form, ErrorMessage },
  setup() {
    const schema = yup.object({
      name: yup.string().required().label("Name"),
      email: yup.string().required().email().label("Email"),
      password: yup.string().required().min(6).label("Password"),
    });

    function onSubmit(values: object,{resetForm}: {resetForm: () => void}) {
      alert(JSON.stringify(values, null, 2));
      resetForm()
    }
    return { schema, onSubmit };
  },
});
</script>

