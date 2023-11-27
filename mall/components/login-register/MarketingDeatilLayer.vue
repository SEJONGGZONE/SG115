
<script setup lang="ts">
import * as loginApi from '@/api'
import { Field, Form, ErrorMessage } from "vee-validate";
import * as common_utils from "@/common/utils.ts";

const emit = defineEmits(["closeModal"]);
const hideCenterLayer=()=>{
  emit("closeModal", 'closeButton_marketingDeatilLayer');
}

</script>

<template>
  
      <h4 class="title">마케팅 및 광고 활용 동의</h4>
      <div class="y_scroll_auto2" style="max-height: 1160px;">
        <div class="layer_pop_contents v5">
          &lt;본 양식은 퍼스트몰에서 제공하는 샘플입니다. 운영 정책에 맞게 수정하시어 이용하시기 바랍니다.&gt; <br>
          <br>
          - 목적: 이벤트, 기획전, 새로운 상품 안내 등의 마케팅<br>
          - 항목: 휴대폰번호, 이메일 <br>
          - 보유기간: 회원 탈퇴 시 즉시 삭제<br>
          <br>
          ※ 개인정보 수집 및 이용에 동의하지 않을 권리가 있으며, 동의 거부 시 일부 서비스 이용이 제한될 수 있습니다. 수신 여부는 마이페이지&gt;회원정보 관리에서 변경할 수 있습니다.<br>

        </div>
      </div>
      <div class="layer_bottom_btn_area2">
        <button type="button" class="btn_resp size_c color2 Wmax" @click="hideCenterLayer()">확인</button>
      </div>
</template>

<style>
</style>

