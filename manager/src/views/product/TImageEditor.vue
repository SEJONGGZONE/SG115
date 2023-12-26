  <template>
    <div class="section section__management" style="">
      <div class="group__contents">
        <div class="part__data_list" style="height: 100%; padding-bottom: 0px">
          <div ref="tuiImageEditor" class="editArea"></div>
        </div>
      </div>
    </div>
  </template>
  <script>
  import ImageEditor from 'tui-image-editor';
  
  const includeUIOptions = {
    includeUI: {
      initMenu: 'filter',
    },
  };

  // const instance = new TuiImageEditor(document.querySelector('#my-image-editor'), {
  //   cssMaxWidth: 700,
  //   cssMaxHeight: 500,
  //   includeUI: {
  //     theme: {
  //       'menu.normalIcon.path': '/assets/svg/icon-d.svg',
  //       'menu.normalIcon.name': 'icon-d',
  //       'menu.activeIcon.path': '/assets/svg/icon-b.svg',
  //       'menu.activeIcon.name': 'icon-b',
  //       'menu.disabledIcon.path': '/assets/svg/icon-a.svg',
  //       'menu.disabledIcon.name': 'icon-a',
  //       'menu.hoverIcon.path': '/assets/svg/icon-c.svg',
  //       'menu.hoverIcon.name': 'icon-c',
  //       'submenu.normalIcon.path': '/assets/svg/icon-a.svg',
  //       'submenu.normalIcon.name': 'icon-a',
  //       'submenu.activeIcon.path': '/assets/svg/icon-c.svg',
  //       'submenu.activeIcon.name': 'icon-c'
  //     },
  //   }
  // });
  // instance.loadImageFromURL('/assets/img/product/img_edt_sample.jpg', 'My sample image');


  const editorDefaultOptions = {
    cssMaxWidth: 700,
    cssMaxHeight: 500,
  };
  export default {
    name: 'TuiImageEditor',
    props: {
      includeUi: {
        type: Boolean,
        default: true,
      },
      options: {
        type: Object,
        default() {
          return editorDefaultOptions;
        },
      },
    },
    mounted() {
      let options = this.options;
      if (this.includeUi) {
        options = Object.assign(includeUIOptions, this.options);
      }
      this.editorInstance = new ImageEditor(this.$refs.tuiImageEditor, options);
      //this.editorInstance.loadImageFromURL('/assets/img/product/img_edt_sample.jpg', 'My sample image');
      setTimeout(() => {
        // 로고제거
        document.getElementsByClassName("tui-image-editor-header-logo")[0].style.display = "none";
        // 배경설정1,메인
        document.getElementsByClassName("tui-image-editor-main-container")[0].style.backgroundColor = "#ffffff";
        // 배경설정1,하단
        //document.getElementsByClassName("tui-image-editor-menu")[0].style.backgroundColor = "#ffffff";
        //this.editorInstance.loadImageFromURL('/assets/img/product/img_edt_sample.jpg', 'My sample image');
        //this.editorInstance.resizeEditor();
        
      }, 100);

      //this.addEventListener();
    },
    destroyed() {
      Object.keys(this.$listeners).forEach((eventName) => {
        this.editorInstance.off(eventName);
      });
      this.editorInstance.destroy();
      this.editorInstance = null;
    },
    methods: {
      addEventListener() {
        Object.keys(this.$listeners).forEach((eventName) => {
          this.editorInstance.on(eventName, (...args) => this.$emit(eventName, ...args));
        });
      },
      getRootElement() {
        return this.$refs.tuiImageEditor;
      },
      invoke(methodName, ...args) {
        let result = null;
        if (this.editorInstance[methodName]) {
          result = this.editorInstance[methodName](...args);
        } else if (methodName.indexOf('.') > -1) {
          const func = this.getMethod(this.editorInstance, methodName);
  
          if (typeof func === 'function') {
            result = func(...args);
          }
        }
  
        return result;
      },
      getMethod(instance, methodName) {
        const { first, rest } = this.parseDotMethodName(methodName);
        const isInstance = instance.constructor.name !== 'Object';
        const type = typeof instance[first];
        let obj;
  
        if (isInstance && type === 'function') {
          obj = instance[first].bind(instance);
        } else {
          obj = instance[first];
        }
  
        if (rest.length > 0) {
          return this.getMethod(obj, rest);
        }
  
        return obj;
      },
      parseDotMethodName(methodName) {
        const firstDotIdx = methodName.indexOf('.');
        let firstMethodName = methodName;
        let restMethodName = '';
  
        if (firstDotIdx > -1) {
          firstMethodName = methodName.substring(0, firstDotIdx);
          restMethodName = methodName.substring(firstDotIdx + 1, methodName.length);
        }
  
        return {
          first: firstMethodName,
          rest: restMethodName,
        };
      },
    },
  };
  </script>
<style>
.editArea {
  margin:0rem;
  position: absolute;
  left: 0;
  top:0;
}
.tui-image-editor-load-btn {
  cursor: pointer;
}
</style>