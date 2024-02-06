<template>
  <div > 
    <label style="margin-left:10px;width: 100%;">width : {{width}}px , height : {{height}}px</label>
    <input ref="input" type="file" name="image" accept="image/*" @change="setImage"/>

    <div class="contentImage">
      <section class="cropper-area">
        <div class="img-cropper">
          <vue-cropper ref="cropper"  class="vue-cropper" 
                       :aspect-ratio="1 / 1" 
                       :src="imgSrc" preview=".preview" @cropmove="cropMoving"   />
        </div>
        <div class="actions" style="display: flex; justify-content: space-between;">
          <div class="left-actions">
            <a href="#" role="button" @click.prevent="zoom(0.2)" >확대</a>
            <a href="#" role="button" @click.prevent="zoom(-0.2)">축소</a> 
            <a href="#" role="button" @click.prevent="rotate(-90)">회전</a>
          </div>
          <div class="right-actions">
            <a href="#" role="button" @click.prevent="reset">초기화</a> 
            <a href="#" role="button" @click.prevent="cropImage">적용</a>
          </div>
          <!--
          <a href="#" role="button" @click.prevent="showFileChooser">Upload Image</a>
          -->
        </div>
        <!--
        <textarea v-model="data" />
        -->
      </section>
      <!--
      <section class="preview-area">
        <p>Preview</p>
        <div class="preview" />
        <p>Cropped Image</p>
        <div class="cropped-image">
          <img
            v-if="cropImg"
            :src="cropImg"
            alt="Cropped Image"
          />
          <div v-else class="crop-placeholder" />
        </div>
      </section>
      -->
    </div>
  </div>
</template>

<script>
import VueCropper from 'vue-cropperjs';

export default {
  components: {
    VueCropper,
  }, 
  data() {
    return {
      imgSrc: '/assets/img/logo/noimg.png',
      defaultImgSrc: '/assets/img/logo/noimg.png',
      cropImg: '',
      data: null,
      width:0,
      height:0
    };
  },

  watch: {
    cropData: {
      handler(newData) {
        console.log('cropData has changed:', newData);
      },
      deep: true
    }
  },
  methods: {
    cropMoving(e){ 
      const data = this.$refs.cropper.getData(); 
      this.width = Math.floor(data.width); 
      this.height = Math.floor(data.height) 
    },
    resizeHeight(newValue) {
      console.log('--------------------- resizeHeight,0');
      console.log('--------------------- resizeHeight,newValue=' + newValue);
      this.$refs.cropper.minCanvasHeight = newValue;
      this.$refs.cropper.minCropBoxHeight = newValue;
      this.$refs.cropper.minContainerHeight = newValue;
      console.log('--------------------- resizeHeight,1');
    },
    inputClick(){
      console.log('--------------------- inputClick,0');
      this.$refs.input.click();
      document.getElementsByClassName('cropper-view-box')[0].style.position='relative';
      document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.position='absolute';
      document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.objectFit='scale-down';
      document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.height='640px';
      document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.top='0';
      document.getElementsByClassName('cropper-canvas')[0].childNodes[0].style.objectFit='scale-down';
      document.getElementsByClassName('cropper-canvas')[0].childNodes[0].style.height='640px';
      document.getElementsByClassName('cropper-container cropper-bg')[0].style.height='640px';
    },
    cropImage() {
      console.log('--------------------- cropImage,0');
      // get image data for post processing, e.g. upload or setting image src
      this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
      const image = new Image();
      image.src = this.cropImg; // 여기에 데이터 URL을 넣어주세요
      image.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;

        const context = canvas.getContext('2d');
        context.fillStyle = '#FFFFFF'; 
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(image, 0, 0);

        // 캔버스의 내용을 파일 오브젝트로 변환
        canvas.toBlob((blob) => {
          const file = new File([blob], 'cropped-image.png', { type: 'image/png' });

          // 이제 'file' 변수에 변환된 파일 오브젝트가 들어있습니다.
          this.$emit("editorDownImage",file) 
          this.imgSrc = this.defaultImgSrc
        }, 'image/png');
      };

    },
    flipX() {
      const dom = this.$refs.flipX;
      let scale = dom.getAttribute('data-scale');
      scale = scale ? -scale : -1;
      this.$refs.cropper.scaleX(scale);
      dom.setAttribute('data-scale', scale);
    },
    flipY() {
      const dom = this.$refs.flipY;
      let scale = dom.getAttribute('data-scale');
      scale = scale ? -scale : -1;
      this.$refs.cropper.scaleY(scale);
      dom.setAttribute('data-scale', scale);
    },
    getCropBoxData() {
      this.data = JSON.stringify(this.$refs.cropper.getCropBoxData(), null, 4);
    },
    getData() {
      this.data = JSON.stringify(this.$refs.cropper.getData(), null, 4);
    },
    move(offsetX, offsetY) {
      this.$refs.cropper.move(offsetX, offsetY);
    },
    reset() {
      this.$refs.cropper.reset();
    },
    rotate(deg) {
      this.$refs.cropper.rotate(deg);
    },
    setCropBoxData() {
      if (!this.data) return;

      this.$refs.cropper.setCropBoxData(JSON.parse(this.data));
    },
    setData() {
      if (!this.data) return;

      this.$refs.cropper.setData(JSON.parse(this.data));
    },
    setImage(e) { 
      console.log('************************* setImage');
      const file = e.target.files[0];

      if (file.type.indexOf('image/') === -1) {
        alert('Please select an image file');
        return;
      }

      if (typeof FileReader === 'function') {
        const reader = new FileReader();

        reader.onload = (event) => {
          this.imgSrc = event.target.result;
          // rebuild cropperjs with the updated source
          this.$refs.cropper.replace(event.target.result);
        };

        reader.readAsDataURL(file);
      } else {
        alert('Sorry, FileReader API not supported');
      }
      
      setTimeout(()=>{ 
        var curWidth = document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.width.replace('px', '');
        var curHeight = document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.height.replace('px', '');
        console.log('************************* setImage, curWidth=' + curWidth);
        console.log('************************* setImage, curHeight=' + curHeight);
        if (curHeight>640) {
          document.getElementsByClassName('cropper-view-box')[0].style.position='relative';
          document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.position='absolute';
          document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.objectFit='scale-down';
          document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.height='640px';
          document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.top='0';
          document.getElementsByClassName('cropper-canvas')[0].childNodes[0].style.objectFit='scale-down';
          document.getElementsByClassName('cropper-canvas')[0].childNodes[0].style.height='640px';
          document.getElementsByClassName('cropper-container cropper-bg')[0].style.height='640px';
          var rate = (curHeight/640-1)*-1;
          this.$refs.cropper.relativeZoom(rate);
          var newWidth = document.getElementsByClassName('cropper-view-box')[0].childNodes[0].style.width.replace('px', '');
          var newCnvWidth = document.getElementsByClassName('cropper-container cropper-bg')[0].style.width.replace('px', '');
          console.log('************************* setImage, newWidth=' + newWidth);
          console.log('************************* setImage, newCnvWidth=' + newCnvWidth);
          // 편집이미지 이동(중앙)
          this.$refs.cropper.moveTo((newCnvWidth-newWidth)/2,0);
          //
          document.getElementsByClassName('cropper-crop-box')[0].style.width = '50px';
          document.getElementsByClassName('cropper-crop-box')[0].style.height = '50px';
        }
      },100)
    }, 
    zoom(percent) {
      this.$refs.cropper.relativeZoom(percent);
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
body {
  font-family: Arial, Helvetica, sans-serif;
  width: 1024px;
  margin: 0 auto;
}

input[type="file"] {
  display: none;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0 5px 0;
}

.header h2 {
  margin: 0;
}

.header a {
  text-decoration: none;
  color: black;
}

.contentImage {
  display: flex;
  justify-content: center;
}

.cropper-area {
  width: 100%;
  height: 50%;
  margin: 0px 10px 0px 10px;
}
.img-cropper{
  display: flex;
  justify-content: center;
  height: 655px;
  align-items: center;
  border: 0px solid;
}
.img-cropper div div{           
  width: 500px;
}
.actions {
  margin-top: 0.5rem;
}

.actions a {
  display: inline-block;
  padding: 5px 15px;
  background: #0062CC;
  color: white;
  text-decoration: none;
  border-radius: 3px;
  margin-right: 5px;
  margin-bottom: 0;
}
.actions .left-actions a:last-child,
.actions .right-actions a:last-child {
    margin:0;
}

textarea {
  width: 100%;
  height: 100px;
}

.preview-area {
  width: 307px;
}
.cropper-crop-box{
  width: 300px;
  height: 300px;
}

.preview-area p {
  font-size: 1.25rem;
  margin: 0;
  margin-bottom: 1rem;
}

.preview-area p:last-of-type {
  margin-top: 1rem;
}

.preview {
  width: 100%;
  height: calc(372px * (9 / 16));
  overflow: hidden;
}

.crop-placeholder {
  width: 100%;
  height: 200px;
  background: #ccc;
}

.cropped-image img {
  max-width: 100%;
}

</style>