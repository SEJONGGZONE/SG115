<script setup>
import Datepicker from "@vuepic/vue-datepicker";
import { string } from "yup";
import { ko } from 'date-fns/locale'

const local = ref(ko)
const koreanLocale = {
		lang: 'ko',
		daysOfWeek: ['일', '월', '화', '수', '목', '금', '토'],
		monthNames: [
		'1월',
		'2월',
		'3월',
		'4월',
		'5월',
		'6월',
		'7월',
		'8월',
		'9월',
		'10월',
		'11월',
		'12월'
		],
		today: '오늘',
		clear: '초기화',
		cancel: '취소',
		ok: '확인',
		select: '날짜 선택',
		prevMonth: '이전달',
		nextMonth: '다음달',
		datePickerTitle: '날짜 선택',
		year: '년',
		month: '월',
		placeholder: '날짜를 선택하세요',
		rangeSeparator: ' - ',
		weekAbbreviation: '주',
		rangeHeaderText: '날짜 범위 선택'
	}
</script>
<template>
  <Datepicker class="dp" placeholder="선택하세요" format='yyyy-MM-dd' :locale="local" :noMinutesOverlay="true" :enable-time-picker="false" auto-apply :close-on-auto-apply="true" />
</template>
<style>
.dp input{
	padding: 0 40px;
	height: 48px;
}
</style>