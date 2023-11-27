<script setup>
import { watch } from 'vue'; 
import { ref, computed, reactive, onMounted, defineComponent } from "vue";

const props = defineProps({
    selectImageObj: {
        type: Array,
        default: [],
    }
});
// 현재 데이타객체
const curDataObj = computed(() => {
    if (props.selectImageObj.length > 0) {
        console.log("왔네...");
        zoomInit(); // 이미지 확대/축소 초기화
        rotateInit(); // 이미지회전 초기화
        calcIndex = 0;
        return (curDataObj.value = props.selectImageObj[0]);
    }
});

/**
 * 이미지회전
 */
var curDeg = 0;
const rotateInit = () => {
    curDeg = 0;
    document.getElementById("curImage").style.transform = 'rotate(' + curDeg + 'deg)';
}
const rotateAction = () => {
    // if (curDeg<360) {
        curDeg = curDeg + 90;
    // } else {
    //     curDeg = 90;
    // }
    document.getElementById("curImage").style.transform = 'rotate(' + curDeg + 'deg) scale(' + curZoom + ', ' + curZoom + ')';
};

/**
 * 이미지 확대/축소
 */
var curZoom = 1;
const zoomInit = () => {
    curZoom = 1;
    document.getElementById("curImage").style.transform = 'scale(1.0, 1.0)';
}
const zoomOut = () => {
    curZoom -= 0.1;
    document.getElementById("curImage").style.transform = 'rotate(' + curDeg + 'deg) scale(' + curZoom + ', ' + curZoom + ')';
};
const zoomIn = () => {
    curZoom += 0.1;
    document.getElementById("curImage").style.transform = 'rotate(' + curDeg + 'deg) scale(' + curZoom + ', ' + curZoom + ')';
};

/**
 * 다음 이미지
 */
 var calcIndex = 0;
const nextImage = (diff) => {
    
    calcIndex += diff;
    var targetIndex = curDataObj.value.CUR_INDEX + calcIndex;
    var maxIndex = curDataObj.value.ORG_DATALIST.length - 1;
    if (targetIndex<0) {
        targetIndex = 0;
        calcIndex = 0;
    } else if (targetIndex > maxIndex) {
        targetIndex = maxIndex;
        calcIndex = maxIndex;
    }
    // console.log(calcIndex);
    // console.log(curDataObj.value.ORG_DATALIST[targetIndex].URL);
    
    zoomInit(); // 이미지 확대/축소 초기화
    rotateInit(); // 이미지회전 초기화

    // QR코드 생성
    var newURL = curDataObj.value.ORG_DATALIST[targetIndex].URL;
    var QR_CODE_RES = "http://sjwas.gzonesoft.co.kr:27002/api/GzoneUtilController/getQrImage?dataString=" + newURL;
    
    // 상단 설명세팅
    document.getElementById("curInfo").innerHTML = "촬영일시 : " + curDataObj.value.ORG_DATALIST[targetIndex].WS_NEWDATE;
    // 이미지세팅
    document.getElementById("curImage").src = newURL;
    // 하단 QR코드세팅
    document.getElementsByClassName("img_clientimage_qr_code")[0].src = QR_CODE_RES;
};

</script>
<template>  
		<div class="modal-dialog ">
			<div class="modal-content" style="background-image: url('/assets/img/map/bg_tile_logo_200_001_fade_10.png');">
				<div class="modal-header">
					<h5 id="curInfo" class="modal-title">
                        촬영일시 : {{ curDataObj?.WS_NEWDATE }}
                    </h5>
					<a href="#" class="btn-close" data-bs-dismiss="modal"></a>
				</div>
				<div class="modal-body">
                    <div class="showImageArea">
                        <div class="imageMainPart">
                            <span @click="nextImage(-1)">◀︎</span>
                            <div style="z-index: 100;">
                                <img id="curImage" class="main_image" draggable="true" :src="curDataObj?.URL"/>
                            </div>
                            
                            <span @click="nextImage(1)">▶︎</span>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
                    <div style="z-index: 110; display:flex; justify-content: space-between; width: 100%;">
                        <div style="display: flex;">
                            <div>
                                <img class="rotateBtn" @click="rotateAction" src="/assets/img/map/refresh_blue_01.png"/>
                            </div>
                            <div style="display: flex; gap: 0.1rem 0.5rem; margin-left:0.5rem;">
                                <span class="zoomOutBtn" style="display:flex;" @click="zoomOut">-</span>
                                <span class="zoomInBtn" style="display:flex;" @click="zoomIn">+</span>
                            </div>
                        </div>
                        <img class="img_clientimage_qr_code" :src="curDataObj?.QR_CODE_RES"/>
                        <div style="">
                            <a href="#" class="btn btn-white" data-bs-dismiss="modal" style="margin:0 0rem;">닫기</a>
                            <!-- <button type="button" class="btn btn-primary">공유하기</button> -->
                        </div>
                    </div>
				</div>
			</div> 
		</div>  
</template>
<style>
/* 메인이미지 */
.main_image {
    transition: all 0.2s linear;
    pointer-events: none;
}
/* QR코드 이미지 */
.img_clientimage_qr_code {
    width: auto;
   height: 4rem;
   margin: -0.8rem 0rem -0.5rem 0rem;
}
/* 회전버튼 */
.rotateBtn {
  height: 2.2rem;
  width: auto;
  cursor: pointer;
  filter: drop-shadow(2px 2px 2px #6f6f6f);
  transition: all 0.2s linear;
}
.rotateBtn:hover {
  transform: scale(1.2);
}
/* 확대/축소 버튼 */
.zoomOutBtn {
  border: 0.15rem solid #ffffff;
  border-radius: 3rem;
  cursor: pointer;
  font-size: 2rem;
  color: #ffffff;
  align-items: center;
  justify-content: center;
  width:2.2rem;
  height:2.2rem;
  padding-bottom: 0.4rem;
  text-shadow: 2px 2px 2px #6f6f6f;
  background-color: #fd5a5a;
  transition: all 0.2s linear;
  box-shadow: 3px 3px 3px #00000078;
  user-select: none;
  transition: all 0.2s linear;
}
.zoomOutBtn:hover{
  transform: scale(1.2);
}
.zoomInBtn {
  border: 0.15rem solid #ffffff;
  border-radius: 3rem;
  cursor: pointer;
  font-size: 2rem;
  color: #ffffff;
  align-items: center;
  justify-content: center;
  width:2.2rem;
  height:2.2rem;
  padding-bottom: 0.2rem;
  text-shadow: 2px 2px 2px #6f6f6f;
  background-color: #fd5a5a;
  transition: all 0.2s linear;
  box-shadow: 3px 3px 3px #00000078;
  user-select: none;
  transition: all 0.2s linear;
}
.zoomInBtn:hover {
  transform: scale(1.2);
}


/* 화살표+이미지 영역 */
.imageMainPart {
  display:flex;
  border: 0px solid red;
  align-items: center;
  gap: 0 0.5rem;
}
/* 좌우화살표 */
.imageMainPart span {
  font-size:5rem;
  color: #8c8c8c;
  cursor: pointer;
  filter: drop-shadow(2px 2px 5px #00000078);
  transition: all 0.2s linear;
  user-select: none;
  z-index: 110;
}
.imageMainPart span:hover {
  transform: scale(1.2);
}
/* 이미지 */
.imageMainPart img {
  width:23rem;
  height:23rem;
  object-fit: scale-down;
  filter: drop-shadow(5px 5px 5px #00000054);
  user-select: none;
}

</style>