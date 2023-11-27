<script setup lang="ts">
import { onMounted, onUnmounted, ref, toRefs ,defineEmits } from "vue";
import { useLoad } from "@/naverMap/composables/useLoad";
import { addEventPolygon } from "@/naverMap/composables/useEvent";
import { UI_EVENT_POLYGON } from "@/naverMap/constants/events";

const emits = defineEmits([...UI_EVENT_POLYGON, "onLoad"]);
const props = defineProps<{
  paths:
    | naver.maps.ArrayOfCoords[]
    | naver.maps.KVOArrayOfCoords[]
    | naver.maps.ArrayOfCoordsLiteral[];
  options?: naver.maps.PolygonOptions;
}>();

const { paths, options } = toRefs(props);
const polygon = ref<naver.maps.Polygon>();

/** Setup polygon Instnace */
const useInitPolygon = (map: naver.maps.Map) => {
  polygon.value = new window.naver.maps.Polygon({
    map: map,
    paths: paths.value as any,
    ...options?.value,
  });

  addEventPolygon(emits, polygon.value);
  emits("onLoad", polygon.value);
};

onMounted(() => useLoad(useInitPolygon));
onUnmounted(() => polygon.value && polygon.value.setMap(null));
</script>

<template>
  <div></div>
</template>
