<template>  
     <span class="text-warning">
      <i class="ion-ios-star" style="font-size: 24px;"></i>
    </span>  

    <span class="text-warning">
      <i v-if="startNumber >= 2" class="ion-ios-star" style="font-size: 24px;"></i>
      <i v-else class="ion-ios-star-outline" style="font-size: 24px;"></i>
    </span> 
    
    <span class="text-warning">
      <i v-if="startNumber >= 3" class="ion-ios-star" style="font-size: 24px;"></i>
      <i v-else class="ion-ios-star-outline" style="font-size: 24px;"></i>
    </span> 
    
    <span class="text-warning">
      <i v-if="startNumber >= 4" class="ion-ios-star" style="font-size: 24px;"></i>
      <i v-else class="ion-ios-star-outline" style="font-size: 24px;"></i>
    </span> 
    
    <span class="text-warning">
      <i v-if="startNumber >= 5" class="ion-ios-star" style="font-size: 24px;"></i>
      <i v-else class="ion-ios-star-outline" style="font-size: 24px;"></i>
    </span>  
</template>

<script setup> 
 
  const props = defineProps({
    startNumber: Number
  });

</script> 