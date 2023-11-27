<script setup lang="ts">
import { onMounted, onUnmounted, ref, toRefs ,defineEmits } from "vue";
import { useLoad } from "@/naverMap/composables/useLoad";
import { addEventRectangle } from "@/naverMap/composables/useEvent";
import { UI_EVENT_RECTANGLE } from "@/naverMap/constants/events";

const emits = defineEmits([...UI_EVENT_RECTANGLE, "onLoad"]);
const props = defineProps<{
  bounds: naver.maps.Bounds | naver.maps.BoundsLiteral;
  options?: naver.maps.RectangleOptions;
}>();

const { bounds, options } = toRefs(props);
const rectangle = ref<naver.maps.Rectangle>();

/** Setup rectangle Instnace */
const useInitRectangle = (map: naver.maps.Map) => {
  rectangle.value = new window.naver.maps.Rectangle({
    map: map,
    bounds: bounds.value,
    ...options?.value,
  });

  addEventRectangle(emits, rectangle.value);
  emits("onLoad", rectangle.value);
};

onMounted(() => useLoad(useInitRectangle));
onUnmounted(() => rectangle.value && rectangle.value.setMap(null));
</script>

<template>
  <div></div>
</template>
