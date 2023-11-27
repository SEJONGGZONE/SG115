<script setup>


import { watch } from 'vue'; 
   const props = defineProps({
		rowData222: {
			type: Array,
			default : []
		}
	})
	const editRowData = ref(props.rowData)
	console.log(editRowData)
	
</script>
<template>  
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">거래처 수기 결제 상세</h5>
					<a href="#" class="btn-close" data-bs-dismiss="modal"></a>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">상호명11</label>
						<input type="text" class="form-control" :value="rowData"/>
					</div>
					<div class="mb-3">
						<label class="form-label">금액</label>
						<input type="text" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">주문명</label>
						<input type="text" class="form-control" />
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-white" data-bs-dismiss="modal">취소</a>
					<button type="button" class="btn btn-primary">저장</button>
				</div>
			</div> 
		</div>  
</template>