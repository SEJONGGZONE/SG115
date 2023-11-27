<template>
    <div style="margin:10px;"> 
        <div style="border:0px solid #FF0000; position: relative;">
        <div style="position:absolute; left:10px; bottom:10px; font-size:12px;">출력일시 : {{ firstData?.nowDtmString }}</div>
        <div style="position:absolute; right:10px; bottom:10px; font-size:12px;">배차번호 : {{ firstData?.DISPATCH_ID }}</div>
        </div>
        <table>
        <!-- 제목 행 -->
        <thead>
            <tr>
            <th colspan="9"><h4>배 차 일 지</h4></th>
            </tr>
        </thead>
        <!-- 내용 행 -->
        <tbody style="overflow: scroll">
            <tr style="background-color: #f2f2f292">
            <td colspan="3">일자</td>
            <td colspan="3">성명</td>
            <td colspan="3">담당자</td>
            </tr>
            <!-- 추가 행 (예시) -->
            <tr>
            <td colspan="3">{{ firstData?.DDATE }}</td>
            <td colspan="3">{{ firstData?.VEHICLENO }} / {{ firstData?.EPNAME }}</td>
            <td colspan="3"></td>
            </tr>

            <tr style="background-color: #f2f2f292">
            <td colspan="3">거래처</td>
            <td colspan="1">도착일시</td>
            <td colspan="1">매출액</td>
            <td colspan="2">입금액</td>
            <td colspan="2">업무목적</td>
            </tr>
            <tr v-for="data in pdfPrintData" v-bind:key="data" :class="data.inboundChkClass">
            <td colspan="3">{{ data.CLNAME }}</td>
            <td colspan="1">{{ data.inboundDtmString }}</td>
            <td colspan="1" class="rightAmt">{{ data.ORDER_AMT }}</td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            </tr>
            <tr style="background-color: #f2f2f292">
            <td colspan="3">합계금액</td>
            <td colspan="2" class="rightAmt">
                {{ utils.numberWithCommas(firstData?.TOT_ORDER_AMT) }}
            </td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            </tr>
            <tr style="background-color: #f2f2f292">
            <td colspan="3">주유날짜 / 주유금액</td>
            <td colspan="2">출발시 누적거리</td>
            <td colspan="2">도착시 누적거리</td>
            <td colspan="2">주행거리</td>
            </tr>
            <tr class="rowHeight">
            <td colspan="3"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2">{{ firstData?.TOT_DISTANCE }}km</td>
            </tr>
            <tr style="background-color: #f2f2f292">
            <td rowspan="3">경비</td>
            <td colspan="2">항목</td>
            <td colspan="2">내용</td>
            <td colspan="2">금액</td>
            <td colspan="2">적요</td>
            </tr>
            <tr class="rowHeight">
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2" rowspan="3"></td>
            </tr>
            <tr class="rowHeight">
            <td colspan="2" style="background-color: #f2f2f292">합계금액</td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            </tr>
        </tbody>
        </table>
        <div style="border:0px solid #FF0000; position: relative; margin-top:35px;">
        <div style="position:absolute; left:10px; bottom:10px; font-size:12px;">출력일시 : {{ firstData?.nowDtmString }}</div>
        <div style="position:absolute; right:10px; bottom:10px; font-size:12px;">배차번호 : {{ firstData?.DISPATCH_ID }}</div>
        </div>
</div>
</template>
<script setup>
import { ref, computed, reactive, onMounted, defineComponent } from "vue";
const props = defineProps({
  pdfPrintData: {
    type: Array,
    default: [],
  },
});
const firstData = computed(() => {
  if (props.pdfPrintData.length > 0) {
    return (firstData.value = props.pdfPrintData[0]);
  }
});

import { utils } from "@/common/utils.ts";
</script>
 <style scoped>

 .inboundChk {
  color:#000000;
 }
 .inboundChk_NOTIN {
  color:#a8a8a8;
}
/* 기본 테이블 스타일 */
table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
  border-spacing: 0 5px; /* 이 값은 선 사이의 간격을 조절합니다. */
}

th,
td {
  border: 1px solid #d4d4d4ad;
  padding: 8px 12px;
  text-align: center;
}

/* 나머지 행은 흰색 배경 */
tbody tr:not(.header) {
  background-color: #ffffff8e;
}

/* 마우스가 셀 위에 있을 때 테두리 검은색으로 */
tbody tr:hover {
  background-color: #f5f5f5;
}
.rightAmt {
  text-align: right;
}
.rowHeight td {
  height: 40px;
}
tr.double-border-bottom {
  border-bottom: 1px solid black;
}

tr.double-border-bottom + tr {
  border-top: 1px solid black;
}
</style>