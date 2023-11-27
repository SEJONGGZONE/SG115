// import { useAppOptionStore } from "@/stores/app-option";
// import { useAppVariableStore } from "@/stores/app-variable";
// import * as XLSX from 'xlsx'
// const appVariable = useAppVariableStore()
// const appOption = useAppOptionStore();

import axios from "axios";

import Swal from "sweetalert2";

import { useModalStore } from "@/store/useModal";
const instance = axios.create({
  //http://sjwas.gzonesoft.co.kr:27002/swagger/ui/index
  //24484
  baseURL:
    "https://sjwas.gzonesoft.co.kr:24485/api/DatabaseController/runProcedure",
  timeout: 500000,
  headers: {
    "Content-Type": "application/json",
    CodyApiKey: "1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=",
  },
});
const CVOinstance = axios.create({
  //http://sjwas.gzonesoft.co.kr:27002/swagger/ui/index
  //24484
  baseURL:
    "https://sjwas.gzonesoft.co.kr:27002/api/DatabaseController/runProcedure",
  timeout: 500000,
  headers: {
    "Content-Type": "application/json",
    CodyApiKey: "1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=",
  },
});

let fileBaseUrl = "http://sjwas.gzonesoft.co.kr:27002/api/";
if (process.client && location.hostname === "www.cookzzang.com") {
  fileBaseUrl = "https://sjwas.gzonesoft.co.kr:24485/api/";
}
const instanceFile = axios.create({
  //http://sjwas.gzonesoft.co.kr:27002/swagger/ui/index
  baseURL: fileBaseUrl,
  timeout: 50000,
  headers: {
    "Content-Type": "multipart/form-data",
    CodyApiKey: "1Bb6wI6DnVL8S8LQAbvAO+pSnKXCf7HwQ7GEn+FnrJk=",
  },
});

const username = "live_sk_lpP2YxJ4K874pbQyJop3RGZwXLOb";
const password = "";
const credentials = `${username}:${password}`;

// Base64로 인코딩
const encodedCredentials = btoa(credentials);

const tossInstance = axios.create({
  baseURL: "https://api.tosspayments.com/v1/payments/confirm",
  timeout: 50000,
  headers: {
    "Content-Type": "application/json",
    Authorization: "Basic " + encodedCredentials,
  },
});

const cartDimmed = () => {
  let divElement = document.getElementById("dimmedDiv");
  divElement.classList.add("modal-backdrop");
  divElement.classList.add("fade");
  divElement.style.pointerEvents = "all";
  divElement.classList.add("show");
  let modalOverlay = document.getElementById("modal-overlay-cart");
  modalOverlay.style.display = "";
};

const dimmed = () => {
  let divElement = document.getElementById("dimmedDiv");
  divElement.classList.add("modal-backdrop");
  divElement.classList.add("fade");
  divElement.style.pointerEvents = "all";
  divElement.classList.add("show");
};

const removeDimmed = () => {
  let divElement = document.getElementById("dimmedDiv");

  divElement.classList.remove("modal-backdrop");
  divElement.style.pointerEvents = "none";
  divElement.classList.remove("fade");
  divElement.classList.remove("show");
  let modalOverlay = document.getElementsByClassName("modal-overlay");
  for (let i = 0; i < modalOverlay.length; i++) {
    let element = modalOverlay[i];
    if (element) {
      element.style.display = "none";
    }
  }
};

const setCookie = (name, value, days) => {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + value + expires + "; path=/";
};

// 쿠키 가져오기 함수
const getCookie = (name) => {
  var cookieName = name + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var cookieArray = decodedCookie.split(";");

  for (var i = 0; i < cookieArray.length; i++) {
    var cookie = cookieArray[i];
    while (cookie.charAt(0) === " ") {
      cookie = cookie.substring(1);
    }
    if (cookie.indexOf(cookieName) === 0) {
      return cookie.substring(cookieName.length, cookie.length);
    }
  }
  return "";
};

function fxAlert(msg: any, option) {
  let typeStr = option?.type ? option?.type : "success";
  let timerStr = option?.timer ? option?.timer : 1500;
  Swal.fire({
    title: msg, //warning, error, success, info, and question.
    text: "",
    icon: typeStr,
    confirmButtonText: "확인",
    showConfirmButton: false,
    timer: timerStr,
  });
}
function fxAlertOk(msg: any, option) {
  let typeStr = option?.type ? option?.type : "success";
  return new Promise(function (resolve, reject) {
    Swal.fire({
      title: msg, //warning, error, success, info, and question.
      text: "",
      icon: typeStr,
      confirmButtonText: "확인",
      showConfirmButton: true,
    }).then((result) => {
      resolve(result);
    });
  });
}
function fxConfirm(title, msg: any) {
  return new Promise(function (resolve, reject) {
    Swal.fire({
      title: title,
      text: msg,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "확인",
      cancelButtonText: "취소",
    }).then((result) => {
      resolve(result);
    });
  });
}

/**
 * request 인터셉터
 */
instance.interceptors.request.use(
  function (config) {
    console.log("interceptos111");
    return config;
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error);
  }
);

/**
 * response 인터셉터
 */
instance.interceptors.response.use(
  function (data) {
    console.log("interceptos2222");
    return data.data;
  },
  function (error) {
    // Do something with request error
    return Promise.reject(error);
  }
);

function getAxios() {
  return instance;
}
function getAxiosFile() {
  return instanceFile;
}
function getCvoAxios() {
  return CVOinstance;
}
function getTossAxios() {
  return tossInstance;
}

const formattedPrice = (price) => {
  if (!price) return;

  if (typeof price === "string") {
    if (price.indexOf(",") > -1) return price;
    return Number(price).toLocaleString("ko-KR");
  } else {
    return price.toLocaleString("ko-KR");
  }
  return price;
};

// function startLodingBar() {
//   appOption.isDimmed = true;
//   appOption.isProgressbar = true;
// }
// function removeLodingBar() {
//   appOption.isDimmed = false;
//   appOption.isProgressbar = false;
// }

// function startProgressbar() {
//   appOption.isProgressbar = true;
// }
// function removeProgressbar() {
//   appOption.isProgressbar = false;
// }

// function startDimmed() {
//   appOption.isDimmed = true;
// }
// function removeDimmed() {
//   appOption.isDimmed = false;
// }

// function showToast(msg){
//   appVariable.toastMsg = msg
// }
// /**hover */
// function addHoverClassToTr(mouseEvent){
//   //cursor를 포함한 hover
//   mouseEvent.target.classList.add("hover");
//   addCursorClassToTr(mouseEvent);
// };
// function removeHoverClassFromTr(mouseEvent){
//   //cursor를 포함한 hover
//   mouseEvent.target.classList.remove("hover");
//   removeCursorClassFromTr(mouseEvent);
// };
// function addHoverClassToTr1(mouseEvent){
//   mouseEvent.target.classList.add("hover");
// };
// function removeHoverClassFromTr1(mouseEvent){
//   mouseEvent.target.classList.remove("hover");
// };

// function addCursorClassToTr(mouseEvent){
//   mouseEvent.target.classList.add("cursor");
// };
// function removeCursorClassFromTr(mouseEvent){
//   mouseEvent.target.classList.remove("cursor");
// };

// function doSort(field, setting, arry){

// 	let sort = "asc";
// 	if (field == setting.order) {
// 		if (setting.sort == "asc") {
// 			sort = "desc";
// 		}
// 	}
// 	setting.order = field;
//   setting.sort = sort;

// 	arry.sort(function(a, b){
// 		var collator = new Intl.Collator(undefined, {
// 			numeric: true,
// 			sensitivity: "base",
// 		});
// 		let sortOrder = sort === "desc" ? -1 : 1;
// 		return collator.compare(a[field], b[field]) * sortOrder;
// 	});
// }

function excelDownload(data: any, fileName: any) {
  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Data");
  XLSX.writeFile(workbook, `${fileName}.xlsx`);
}

function numberWithCommas(num: { toString: () => string }) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function setDateFormat(date: {
  length: number;
  getFullYear: () => string;
  getMonth: () => number;
  getDate: () => any;
}) {
  let returnDate;

  if (date.length === 10) {
    return date;
  }

  returnDate = date
    ? date.getFullYear() +
      "-" +
      String(date.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(date.getDate()).padStart(2, "0")
    : "";

  return returnDate;
}

function searchAdress() {
  return new Promise(function (resolve, reject) {
    new window.daum.Postcode({
      oncomplete: (data) => {
        resolve(data);
      },
    }).open();
  });
}

export {
  // startLodingBar,
  //       removeLodingBar,
  //       startProgressbar,
  //       removeProgressbar,
  //       startDimmed,
  //       removeDimmed,
  //       showToast,
  //       addHoverClassToTr,
  //       removeHoverClassFromTr,
  //       addHoverClassToTr1,
  //       removeHoverClassFromTr1,
  //       addCursorClassToTr,
  //       removeCursorClassFromTr,
  //       doSort,
  cartDimmed,
  searchAdress,
  getCookie,
  setCookie,
  removeDimmed,
  dimmed,
  fxConfirm,
  fxAlert,
  excelDownload,
  numberWithCommas,
  getAxios,
  getAxiosFile,
  setDateFormat,
  fxAlertOk,
  getTossAxios,
  formattedPrice,
  getCvoAxios,
};
