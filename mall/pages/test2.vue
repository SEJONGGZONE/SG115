<script setup>
import { onMounted, ref } from "vue";

import * as operateApi from "@/api";
const watchId = ref("");

const accuracy = ref("");
const latitude = ref("");
const longitude = ref("");
const altitude = ref("");
const heading = ref("");
const speed = ref("");
const reqcount = ref(0);
const callGeoLocation = () => {
  const successCallback = (position) => {
    console.log("position:::", position);
    reqcount.value = reqcount.value + 1;
    latitude.value = position.coords.latitude;
    longitude.value = position.coords.longitude;
    accuracy.value = position.coords.accuracy;
    altitude.value = position.coords.altitude;
    heading.value = position.coords.heading;
    speed.value = position.coords.speed;
    const currentDate = new Date();

    // 날짜를 문자열로 변환합니다.
    const year = currentDate.getFullYear().toString();
    const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
    const day = currentDate.getDate().toString().padStart(2, "0");

    const dateDay = `${year}${month}${day}`;

    const hours = currentDate.getHours().toString().padStart(2, "0");
    const minutes = currentDate.getMinutes().toString().padStart(2, "0");
    const seconds = currentDate.getSeconds().toString().padStart(2, "0");

    const dateTime = `${hours}${minutes}${seconds}`;
    // 년, 월, 일을 조합하여 날짜 문자열을 생성합니다.
    const params = {
      traceDate: dateDay,
      traceTime: dateTime,
      latitude: latitude.value,
      longitude: longitude.value,
      speed: speed.value,
      direction: heading.value,
    };
    operateApi.CvoGpsServiceSave(params);
  };
  const errorCallback = () => {};
  const options = {
    enableHighAccuracy: true,
    timeout: 500,
  };
  watchId.value = navigator.geolocation.watchPosition(
    successCallback,
    errorCallback,
    options
  );
};

const clearWatch = () => {
  navigator.geolocation.clearWatch(watchId.value);
};

onMounted(() => {
  callGeoLocation();
});
</script>

<template>
  <div>
    Accuracy: {{ accuracy }}<br />
    Latitude: {{ latitude }} | Longitude : {{ longitude }}<br />
    Altitude: {{ altitude }}<br />
    Heading: {{ heading }} <br />
    Speed: {{ speed }}<br />
    reqcount: {{ reqcount }}<br />
  </div>
  <div>
    <input type="button" value="워치 취소" @click="clearWatch" />
  </div>
  <!-- <div>
    <pre lang="json">{{
      JSON.stringify(
        {
          coords: {
            accuracy: coords.accuracy,
            latitude: coords.latitude,
            longitude: coords.longitude,
            altitude: coords.altitude,
            altitudeAccuracy: coords.altitudeAccuracy,
            heading: coords.heading,
            speed: coords.speed,
          },
          locatedAt,
          error: error ? error.message : error,
        },
        null,
        2,
      )
    }}</pre>
    <button @click="pause">Pause watch</button>
    <button @click="resume">Start watch</button>
  </div> -->
</template>
