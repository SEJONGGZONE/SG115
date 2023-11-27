import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

export default defineNuxtPlugin((nuxtApp) => {
  console.log(QuillEditor);
  nuxtApp.vueApp.component("QuillEditor", QuillEditor);
});
