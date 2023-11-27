<template>
  <com-swiper></com-swiper>
</template>
 
<script setup>
import quillEditor from "@/components/common/others/QuillEditor.vue";
import ComSwiper from "@/components/common/others/ComSwiper.vue";
const editData = ref(null);
const onEditorReady = (editor) => {
  editData.value = editor;
};
const log = () => {
  console.log(editData.value.getHTML());
};
</script>