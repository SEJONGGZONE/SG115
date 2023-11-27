<template> 
    
        <nav> 
            <div class="nav nav-tabs set-nav" :id="`product-details${itCode}`" role="tablist">
                <template v-for="(image,index ) in imageList" v-bind:key="index">  
                  <button class="nav-item nav-link mb-5" :class="index===0 ? 'active': ''" @mouseover="handleActiveImg(image?.URL, $event)">
                    <div class="product__nav-img">
                      <cook-image :image="image?.URL" :size="'detailimg1'"></cook-image> 
                    </div>
                  </button> 
                </template> 
            </div>
        </nav>
    
</template>

<script   setup> 

import CookImage from '@/components/common/others/CookImage.vue'
const jsonImgList = ref([])//전체
const json_img = ref([]) // 4개만 담기

const props = defineProps({
    jsonData: {
        type : String,
        default : ""
    },
    itCode:{
      type:String,
      default:''
    }
}); 
const itCode = computed(()=>{
  return props?.itCode
})
const imageList = computed(()=>{
  if(props?.jsonData){
          jsonImgList.value = JSON.parse(props?.jsonData)
          jsonImgList.value.sort(function(a, b) {
            return a.seq - b.seq;
          });
          json_img.value = jsonImgList.value.slice(0,4)
        } 
    return json_img.value
})
const emit = defineEmits(["changeListImage"]);


let targetObj = '';
const handleActiveImg = (image,event) =>{
  const elementId = `product-details${itCode.value}`
  const divElement = document.getElementById(elementId);
  const buttons = divElement.querySelectorAll('button');

  buttons.forEach(button => {
    button.classList.remove('active');
  }); 

  targetObj = (event.target).closest('button')
  targetObj.classList.add("active");
  emit("changeListImage",props.itCode,image);
}

</script>

<style scoped>
.radius{
  border-radius: 0.6rem!important
}
</style>
