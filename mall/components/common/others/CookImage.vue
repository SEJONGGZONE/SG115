<template>
     <img :class="size" :style="style" :src="image ? imageUrl : noImg" alt="banner" @error="handleImageError"/>
</template>

<script setup>

import noImg from "~/assets/img/no_img.png";
  const props = defineProps({
    image: {
        type : String,
        default :noImg
    },
    size: {
      type : String,
      default : 'imgmXS'
    },
    style:{
        type : Object,
        default : {}
    }
  });

  const retryCount = ref(0)
  const imageUrl = computed(()=>{
    return `${props.image}?retry=${retryCount.value}`
  })

  const handleImageError =()=> {
      if (retryCount.value < 3) { // 최대 3번 재시도
        retryCount.value++; 
      }
    }

</script>
<style>
.size-30 {
  width: 30%;
  height: 30%;
}
.size-50 {
  width: 50%;
  height: 50%;
}
.imgmXS {
  width: 70px;
  height: 70px;
  object-fit: scale-down;
}
.imgmS {
  width: 50px;
  height: 50px;
  object-fit: scale-down;
}
.imgmM {
  width: 100px;
  height: 100px;
  object-fit: scale-down;
  border: 1px solid #eaedff;
}
.imgmL {
  width: 300px;
  height: auto;
  object-fit: scale-down;
}
.imgmXL {
  height: auto;
  object-fit: scale-down;
}
.detailimg {
  height: auto;
  width: 100%;
  object-fit: cover;
}
.detailimg1{
  width: 95px;
  height: 95px;
  object-fit: scale-down;
}
.detailimg2 {
  height: 480px;
  width: 100%;
  object-fit: scale-down;
}
@media only screen and (max-width:450px){
  .imgmL{
    width: 300px;
    height: 150px;
    object-fit: scale-down;
  }
}

</style>
