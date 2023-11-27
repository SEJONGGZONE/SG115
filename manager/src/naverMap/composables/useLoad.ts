import { mapIsLoaded, mapCallbackList, mapInstance } from "@/naverMap/stores";

export const useLoad = (action: (map: naver.maps.Map) => void) => {
  mapIsLoaded.value
    ? action(mapInstance.value!)
    : mapCallbackList.value.push(action);
};
