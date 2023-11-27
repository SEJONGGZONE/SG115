import { fileURLToPath, URL } from "url";

import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import vueJsx from "@vitejs/plugin-vue-jsx";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
  	vue({
      template: {
        compilerOptions: {
          //isCustomElement: (tag) => tag.includes('ion-icon')
        }
      }
    }), 
    vueJsx()
  ],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
  server: {
    proxy: {
      '/cvoapi': 'http://sjwas.gzonesoft.co.kr:27002',
      '/api': 'http://sjwas.gzonesoft.co.kr:32206',
      '/DatabaseController': 'http://sjwas.gzonesoft.co.kr:32206/api',
      '/file': 'http://sjwas.gzonesoft.co.kr:32206/api'
    }
  }
});
