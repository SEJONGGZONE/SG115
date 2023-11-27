<script setup>
import VueTableLite from 'vue3-table-lite'
	import {   onMounted } from 'vue'; 


	onMounted(()=>{
		const interId = setInterval(() => {
			const vtlTable = document.getElementsByClassName("vtl-table")
			if(vtlTable){
				document.getElementsByClassName("vtl-table")[0].classList.add("table-striped")
				document.getElementsByClassName("vtl-table")[0].classList.add("table")	
				clearInterval(vtlTable)
			}
		}, 1000);


		
	}) 

</script>

<template>
  <vue-table-lite />
</template>