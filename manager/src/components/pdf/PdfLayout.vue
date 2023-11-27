<template>
  <div
    class="modal-dialog modal-xl setModalCenter popLayout"
    style="margin: 0px auto"
  >
    <div class="modal-content popContent">
      <!-- 모달헤더(타이틀/버튼) 영역 -->
      <div class="popContentHeader">
        <div class="row" style="width: -webkit-fill-available">
          <div class="col-lg-12" style="margin: 0px 10px">
            <h5 style="border: 0px solid blue; margin: 0px auto">미리보기</h5>
          </div>
        </div>
        <div
          style="
            display: flex;
            justify-content: end;
            width: 300px;
            gap: 10px;
            margin: 10px;
          "
        >
          <a
            href="#"
            class="btn btn-white"
            @click="pdfPrint"
            style="padding: 5px 20px"
            ><i class="fa fa-print fa-fw"></i>출력</a
          >
          <a
            href="#"
            class="btn btn-white"
            style="padding: 5px 20px"
            data-bs-dismiss="modal"
            ><i class="fa fa-circle-xmark fa-fw"></i>닫기</a
          >
        </div>
      </div>
      <div class="modal-body" id="pdfPrint" style="height:500px;">
        <div class="report_body" id="pdfMain">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { createPdfFromHtml } from "@/common/pdfMake.js";
const props = defineProps({ 
    pdfFileName: {
		type : String
		,default:""
	}
})
const pdfPrint = () => {
  const productTable = document.getElementById("pdfMain");
  createPdfFromHtml(productTable,props.pdfFileName);
};
</script>
<style scoped>

#pdfPrint {
  border:0px solid #c6a1ed; overflow-x: hidden; overflow-y: auto; flex-direction: column;
  padding:10px 10px;
}

#pdfPrint .report_body {
  display: block;
  width: 100%;
  height:auto;
  position: relative;
  border: 0px solid #230b36;
  padding:50px 0px 50px 0px;
  background-image: url( "/assets/img/map/bg_tile_logo_200_001_fade_10.png" );
  background-repeat: repeat;
}
.popLayout {
  width: 1000px;
  height: 750px;
}

.popContent {
  height:100%;
}
.popContentHeader {
  display:flex;
  padding: 0px 10px;
  border-bottom: 1px solid rgb(206, 206, 208);
  background-color: #8480fc;
  color: #ffffff;
  height:60px;
  align-items: baseline;
}
</style>