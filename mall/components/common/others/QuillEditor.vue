<template>
  <ClientOnly>
    <QuillEditor
      class="editor"
      ref="myTextEditor"
      :disabled="false"
      v-model="content"
      :options="editorOption"
      @input="onEditorChange"
      @change="onEditorChange"
      @blur="onEditorBlur($event)"
      @focus="onEditorFocus($event)"
      @ready="onEditorReady($event)"
      :style="{ height: params.height }"
    />
  </ClientOnly>
</template>
 
<script setup>
const myTextEditor = ref(null);
const params = defineProps({
  height: {
    type: String,
    default: "400px",
  },
});
const editorOption = {
  placeholder: "내용을 입력해주세요",
  modules: {
    toolbar: [
      ["bold", "italic", "underline", "strike"], // <strong>, <em>, <u>, <s>
      ["blockquote", "code-block"], // <blockquote>, <pre class="ql-syntax" spellcheck="false">
      [{ header: 1 }, { header: 2 }], // <h1>, <h2>
      [{ list: "ordered" }, { list: "bullet" }],
      [{ script: "sub" }, { script: "super" }], // <sub>, <sup>
      [{ indent: "-1" }, { indent: "+1" }], // class제어
      [{ direction: "rtl" }], //class 제어
      [{ size: ["small", false, "large", "huge"] }], //class 제어 - html로 되도록 확인
      [{ header: [1, 2, 3, 4, 5, 6, false] }], // <h1>, <h2>, <h3>, <h4>, <h5>, <h6>, normal
      [{ font: [] }], // 글꼴 class로 제어
      [{ color: [] }, { background: [] }], //style="color: rgb(230, 0, 0);", style="background-color: rgb(230, 0, 0);"
      [{ align: [] }], // class
      ["link", "image", "video"],
    ],
    syntax: {
      highlight: (text) => hljs.highlightAuto(text).value,
    },
  },
};
const content = ref("");
const emit = defineEmits(["onEditorReady"]);

const getContent = () => {
  const value = content.value;
  return value;
};
const setContent = (html) => {
  this.$refs.myTextEditor.setHTML(html);
};
const onEditorChange = (value) => {
  return value.target.innerHTML;
};
const onEditorBlur = (editor) => {
  console.log("editor blur!", editor);
};
const onEditorFocus = (editor) => {
  console.log("editor focus!", editor);
};
const onEditorReady = (editor) => {
  emit("onEditorReady", myTextEditor.value);
};
const getEditor = () => {
  return this.$refs.myTextEditor.quill;
};

const editor = () => {
  return this.$refs.myTextEditor.quill;
};
const contentCode = () => {
  return hljs.highlightAuto(this.content).value;
};
</script>