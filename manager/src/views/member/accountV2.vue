<script setup>
import {  reactive, onMounted } from 'vue'; 
import { useAppOptionStore } from '@/stores/app-option';
import { onBeforeRouteLeave } from 'vue-router'
import vueTable from '@/components/plugins/VueTable.vue';
import navscrollto from '@/components/app/NavScrollTo.vue';  
import { ScrollSpy } from 'bootstrap';
import { memberApi } from '@/api';
import { ref } from 'vue'; 
import { Toast } from 'bootstrap';

//account ver2
//row 선택시, window.open으로 화면의 띄움

let searchKeyword = ''
let totalCount = ''
let selectRowData = ref([])
const isMobile = /Mobile|Android/.test(window.navigator.userAgent)
const appOption = useAppOptionStore();

let memo = ref('')//주문명
let amount = ref('')//금액
let msg = ref('')//toast 안내문구
let pageNumber = ref(1)
let pageSize = ref(20)
onMounted(()=>{
	new ScrollSpy(document.body, {
		target: '#sidebar-bootstrap',
		offset: 200
	})
	console.log("isMobile:::",isMobile)
	if(isMobile){
		appOption.appSidebarHide = true;
		appOption.appHeaderHide = true;
		appOption.appContentClass = 'p-0';
	}
	
}),  
onBeforeRouteLeave((to, from, next)=>{
	appOption.appSidebarHide = false;
	appOption.appHeaderHide = false;
	appOption.appContentClass = '';
	next();
})

const table = reactive({
	isLoading: false,
	isReSearch: false,
	columns: [
	{
			label: "NO",
			field: "RANK",
			width: "3%",
			sortable: true,
			},
			{
			label: "거래처명",
			field: "CLNAME",
			width: "10%",
			//sortable: true,
			isKey: true,
			},
			{
			label: "전화번호",
			field: "CLTEL",
			width: "10%",
			//sortable: true,
			},
			{
			label: "대표자",
			field: "CLCEO",
			width: "10%",
			//sortable: true,
			},
			{
			label: "주소",
			field: "CLJUSO1",
			width: "10%",
			//sortable: true,
			},
			{
			label: "사업자번호",
			field: "CLSAUPNO",
			width: "10%",
			//sortable: true,
			},
			{
			label: "최종일시",
			field: "LAST_DATE",
			width: "10%",
			//sortable: true,
			},
	],
	rows: [],
	totalRecordCount: 0,
	sortable: {
	order: "RANK",
	sort: "asc",
	isShowMoreBtn : true
	},
})

/**
 * Table search event
 */
const doSearch = async (  order, sort) => { 
	if(table.isLoading) return
	table.isLoading = true; 
	const param = {
			clcode : "",
			searchKeyword : searchKeyword ?? '',
			pageSize: pageSize.value,
			pageNumber : pageNumber.value
		}

	let data;
	try {
		data = await memberApi.accoutPayment(param);
		if(data.RecordCount > 0){
			table.rows.push(...data.RecordSet);
			table.sortable.order = order;
			table.sortable.sort = sort;
			table.isShowMoreBtn = true
		}else{
			table.rows
			table.isShowMoreBtn = false
		}
	} catch (error) {
		console.error(error);
	} finally{
		table.isLoading = false; 
	}
}

const searchBtn = (keyword) => { 
	table.rows = []
	searchKeyword = keyword 
	pageNumber.value = 1
	doSearch(); 
}

const searchMoteBtn = () => { 
	pageNumber.value = pageNumber.value + 1
	doSearch(); 
}
/* Table search finished event */
const tableLoadingFinish = (elements) => {
	table.isLoading = false;
}

/* row click event */
const handleRowClick = (rowData) => {
	//주문명, 금액 초기화
	memo.value = ''
	amount.value = ''

	selectRowData.value = JSON.parse(JSON.stringify(rowData));
	console.log("rowData",rowData);
	console.log("handleRowClick",selectRowData);
}

const modalSaveBtn = () =>{

	let chk = false
	let chkId = ''

	if(!memo.value){
		chk = true 
		chkId = `${chkId}주문명`
	}
	if(!amount.value){
		chk = true 
		chkId = !chkId? `금액` : `${chkId}, 금액`
	}
	
	if(chk){
		msg.value = `필수입력 항목을 확인해 주세요..<br>(${chkId})`
		event.preventDefault();
		toastpop();
		return;
	}
	doSave()
}

const doSave = async () => { 

	let data;
	let date = new Date();
	date = date.getFullYear().toString() + date.getMonth().toString() + date.getDate().toString() + date.getHours().toString() + date.getMinutes().toString() + date.getSeconds().toString()

	const param = {
		clcode : selectRowData.value.CLCODE,
		memo : memo.value,
		amount: amount.value.replace(/,/gi, ''),
		key :"KEYIN_" + selectRowData.value.CLCODE +"_"+ date,
		phoneNo : selectRowData.value.CLPHONE
	}

	try {
		data = await memberApi.accoutPaymentSave(param);
		if(data.RecordSet?.[0]?.PAYMENT_NO){
			msg.value = '저장되었습니다.'
			toastpop()
			document.getElementById("saveBtn").disabled = true
		}else{
			msg.value = '관리자에게 문의바랍니다.'
			toastpop()
		}
	} catch (error) {
		console.error(error);
	}
}
const chnageSearchText = () =>{
	table.rows = []
	table.isShowMoreBtn = false
}

const checkKey = () =>{
	amount.value = amount.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

const toastpop = () =>{
	const toast = new Toast(document.getElementById("toastMsg"));
	toast.show();
}
const addHoverClassToTr = (mouseEvent) => {
    mouseEvent.target.classList.add("hover");
};
const removeHoverClassFromTr = (mouseEvent) => {
    mouseEvent.target.classList.remove("hover");
};

pageNumber.value = 1
doSearch(); 
     
</script>
<template>
	<body >
		<div>
			<panel data-bs-spy="scroll" data-bs-target="#navbar-example " :class="table.isLoading ? 'dimmed' : ''" >
				<panel-header>
					<panel-title>거래처수기결제</panel-title>
					<panel-toolbar />
				</panel-header>
				<panel-body>
					<div class="input-group input-group-lg mb-3">
						<input type="text" class="form-control input-white" placeholder="검색어를 입력하세요" v-model="keyword" @input="chnageSearchText"  v-on:keyup.enter="searchBtn(keyword)"/>
						<button type="button" class="btn btn-primary" @click="searchBtn(keyword)"><i class="fa fa-search fa-fw"></i>검색</button>
					</div>
					<!--<vue-table 
						class="vue-table me-2" 
						:is-slot-mode="true"
						:is-loading="table.isLoading"
						:is-re-search="table.isReSearch"
						:columns="table.columns"
						:rows="table.rows"
						:total="table.totalRecordCount"
						:sortable="table.sortable"
						@do-search="doSearch"
						@is-finished="table.isLoading = false"
						@row-clicked="handleRowClick"
						>
					</vue-table>-->

					<div class="vtl vtl-card">
						<div class="vtl-card-body">
							<div class="vtl-row">
								<div class="col-sm-12" style="text-align: center;">
									<table class="vtl-table vtl-table-hover vtl-table-bordered vtl-table-responsive vtl-table-responsive-sm table-striped table" >
										<thead class="vtl-thead">
											<tr class="vtl-thead-tr">
												<th v-for="(col, index) in table.columns" class="vtl-thead-th" :class="col.headerClasses" :key="index" :style="Object.assign({width: col.width ? col.width : 'auto',}, col.headerStyles)">
													<div class="vtl-thead-column">
														{{ col.label }}
													</div>
												</th>
											</tr>
										</thead>
										<tbody data-bs-toggle="modal" data-bs-target="#modalAccount">
											<tr class="vtl-tbody-tr" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj)">
												<td v-for="(col, j) in table.columns" :key="j" class="vtl-tbody-td">
													<div>
														<span>{{ obj[col.field] }}</span>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
									<button v-if="table.isShowMoreBtn" type="button" class="btn btn-primary btn-lg" style="text-align:center" @click="searchMoteBtn">더보기</button>
								</div>
							</div>
						</div>
					</div>
				</panel-body> 
			</panel>

			<div class="modal fade" id="modalAccount" style="z-index: 1051 !important">
				<div class="modal-dialog ">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">거래처 수기 결제 상세</h5>
							<a href="#" class="btn-close" data-bs-dismiss="modal"></a>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label class="form-label">상호명</label>
								<input type="text" class="form-control" v-model="selectRowData.CLNAME" disabled/>
							</div>
							<div class="mb-3">
								<label class="form-label">전화번호</label>
								<input type="text" class="form-control" v-model="selectRowData.CLPHONE" disabled/>
							</div>
							<div class="mb-3">
								<label class="form-label">주문명</label>
								<input type="text" class="form-control" placeholder="주문명" v-model = "memo"/>
							</div>
							<div class="mb-3">
								<label class="form-label">금액</label>
								<input type="text" class="form-control" placeholder="금액" v-model = "amount" @input="checkKey()"/>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-white" data-bs-dismiss="modal">취소</a>
							<button type="button" class="btn btn-primary" id="saveBtn" @click="modalSaveBtn">저장</button>
						</div>
					</div>
				</div>
			</div>

		</div>
		
	</body>

	<div class="position-fixed end-0 top-0 me-5 pt-5 mt-5 toasCenter" style="z-index: 1052 !important; ;">
		<div class="toast fade hide mb-3"  data-autohide="false" id="toastMsg">
			<div class="toast-header">
				<i class="far fa-bell text-muted me-2"></i>
				<strong class="me-auto">알림</strong>
				<button type="button" class="btn-close" data-bs-dismiss="toast"></button>
			</div>
			<div class="toast-body" v-html="msg">
			</div>
		</div>
	</div>

	<div class="fa-3x progressbar" v-if="table.isLoading">
		<i class="fas fa-spinner fa-pulse"></i>
	</div>
</template>

<style>
.toasCenter {
  width: 350px;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -10%);
}
.vtl-table-hover tbody tr:hover {
  color: #212529;
  background-color: #ececec;
}

.dimmed {
  opacity: 0.5; /* 투명도를 50%로 설정 */
  pointer-events: none; /* 이벤트를 비활성화하여 마우스 클릭 등을 막음 */
}
.progressbar {
  position: fixed;
  bottom: 50%;
  left: 55%; 
}
</style>