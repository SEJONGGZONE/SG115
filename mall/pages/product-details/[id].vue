<script setup>
import Layout from "@/layout/LayoutFour.vue";
import ShopDetailsArea from "@/components/shop-details/ShopDetailsArea.vue";
import * as categoryApi from "@/api";

import { useUserStore } from "@/store/useUser";
//로그인 정보
const store = useUserStore();
const clcodeS = store.getUserInfo?.CLCODE;
const resultData = ref({});
const category1 = ref("");
const category2 = ref("");
const saveHistoryItemSave = async () => {
  const userNoS = store.getUserInfo?.USER_NO;
  let localIp = localStorage.getItem("IP");
  let historyParam = {
    ip: localIp,
    itcode: useRoute().params.id,
    userNo: userNoS,
  };
  await categoryApi.memeber_webHistoryItemSave(historyParam);
};
const { $bus } = useNuxtApp();
const doSearchCategory = async () => {
  let param = {
    clcode: clcodeS ?? "",
    itscode: "",
    itscode2: "",
    keyword: "",
    itcode: useRoute().params.id,
    pageNum: "1",
    pageSize: "1",
    inputUser: "",

    orderType10: "",
    orderType20: "",
    orderType21: "",
    orderType30: "",
  };

  const responseObj = await categoryApi.list_categoryList(param);
  const response = responseObj.data;
  if (response.RecordCount > 0) {
    resultData.value = response.RecordSet[0];
    //최근 본 상품 저장
    setSeeProductList();
    category1.value = resultData.value.CATEGORY_CODE2.split("-")[0]?.replaceAll(
      " ",
      ""
    );
    category2.value = resultData.value.CATEGORY_CODE2.split("-")[1]?.replaceAll(
      " ",
      ""
    );
  }

  $bus.$emit("changeCategory", category1.value); //9월 7일 요청사항 : 2차 카테고리 선택 시, 헤더의 1차 카테고리 해당항목 표시
};

const setSeeProductList = () => {
  const productList = localStorage.getItem("seeProductList");
  let tempProductList = [];
  if (productList) {
    tempProductList = JSON.parse(productList);
    const findIndex = tempProductList.findIndex(
      (item) => item.ITCODE === resultData.value.ITCODE
    );
    if (findIndex === -1) {
      tempProductList.unshift(resultData.value);
    }
  } else {
    tempProductList.push(resultData.value);
  }
  tempProductList = tempProductList.slice(0, 10);

  localStorage.setItem("seeProductList", JSON.stringify(tempProductList));
};

onMounted(() => {
  doSearchCategory();
  saveHistoryItemSave();
});
</script>

<template>
  <layout>
    <!--<breadcrumb-area title="Product Details" subtitle="Product Details"/>-->
    <shop-details-area
      :item="resultData"
      :category1="category1"
      :category2="category2"
    />
  </layout>
</template> 

