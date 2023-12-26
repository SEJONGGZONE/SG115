<template>
  <div class="example">
    <quill-editor
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
      :style="{ height: height }"
    />
    <div class="output code" style="display: none;">
      <code class="hljs" v-html="contentCode"></code>
    </div>
  </div>
</template>
<script>
import hljs from 'highlight.js'
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

import debounce from "lodash/debounce";
export default {
  name: "quill-example-snow",
  title: "Theme: snow",
  props: {
    height: String, // 부모 컴포넌트로부터 전달된 height prop을 선언합니다.
  },
  components: {
    QuillEditor,
  },
  data() {
    return {
      editorOption: {
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
            // ["clean"],
            ["link", "image", "video"],
          ],
          syntax: {
            highlight: (text) => hljs.highlightAuto(text).value,
          },
        },
      },
      content: "",
      contentCode: "",
    };
  },
  methods: {
    getContent(){ 
      return this.$refs.myTextEditor.getHTML() 
    },
    setContent(html){ 
      this.$refs.myTextEditor.setHTML(html);
      this.content = html;
    },
    onEditorChange: debounce(function(value) {
      this.content = value.target.innerHTML
    }, 466),
    onEditorBlur(editor) {
      console.log("editor blur!", editor);
    },
    onEditorFocus(editor) {
      
      console.log("editor focus!", editor);
    },
    onEditorReady(editor) {
      console.log("editor ready!", editor);
    },
    getEditor() {
      return this.$refs.myTextEditor.quill;
    },
  },
  computed: {
    editor() {
      return this.$refs.myTextEditor.quill;
    },
    contentCode() {
      return hljs.highlightAuto(this.content).value;
    },
  },
  mounted() {
    console.log("this is Quill instance:", this.editor);
  },
};
</script>

<style lang="scss" scoped>
  .example {
    display: flex;
    flex-direction: column;

    .editor {
      height: 40rem;
      overflow: hidden;
    }

    .output {
      width: 100%;
      height: 20rem;
      margin: 0;
      margin-top:0.3rem;
      border: 0.05rem solid #ffb3b3;
      overflow-y: auto;
      resize: vertical;
      background-color: #ffb3b337;

      &.code {
        padding: 1rem;
        height: 16rem;
      }

      &.ql-snow {
        border-top: none;
        height: 24rem;
      }
    }
  }
</style>