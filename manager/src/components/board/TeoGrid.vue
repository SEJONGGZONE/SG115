
<script setup>
 

   const emits = defineEmits(['handleRowClick']);
   const props = defineProps({
		table: {
			type: Array,
			default : []
		},
		detailShow : {
			type : Boolean,
			default : false
		},
		title : {
			type : String,
			default : ""
		}
		
	})
	
	let selectRowData = ref(null)

	const addHoverClassToTr = (mouseEvent) => {
		mouseEvent.target.classList.add("hover");
	};
	const removeHoverClassFromTr = (mouseEvent) => {
		mouseEvent.target.classList.remove("hover");
	};

	/* row click event */
	const handleRowClick = (rowData,event) => {
		emits('handleRowClick', rowData);
	}

</script>
<template>  
	<div class="col-xl-6" :style="detailShow ? '' : 'width:100%'">
		<div class="vtl vtl-card">
			<div class="vtl-card-body">
				<div class="vtl-row" style="text-align:center">
					<div class="col-sm-12 mx-auto">
						<table class="vtl-table vtl-table-hover vtl-table-bordered vtl-table-responsive vtl-table-responsive-sm table-striped table" style="background-image: url(../assets/img/logo/mokwen_logo.png);">
							<thead class="vtl-thead">
								<tr class="vtl-thead-tr">
									<th v-for="(col, index) in table.columns" class="vtl-thead-th" :class="col.headerClasses" :key="index" :style="Object.assign({width: col.width ? col.width : 'auto',}, col.headerStyles)">
										<div class="vtl-thead-column">
											{{ col.label }}
										</div>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr class="vtl-tbody-tr" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj, $event)" 
								>
									<td v-for="(col, j) in table.columns" :key="j" class="vtl-tbody-td">
										<div>
											<span>{{ obj[col.field] }}</span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<button type="button" class="btn btn-primary btn-lg" style="text-align:center">더보기</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template> 