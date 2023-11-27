<script setup>
	import {  reactive, onMounted,defineComponent } from 'vue';
	import highlightjs from '@/components/plugins/Highlightjs.vue';
	import navscrollto from '@/components/app/NavScrollTo.vue'; 
	import TeoGrid from '@/components/board/TeoGrid.vue'; 
	import { ScrollSpy } from 'bootstrap';
	import {memberMng, memberMngSave } from '@/api';
	import { ref, computed } from 'vue'; 
	import { Toast } from 'bootstrap';


	//table teogrid로 컴포넌트로 분리 한 화면(샘플)
	let searchKeyword = ''
	let totalCount = ''
	let detailShow = ref(false)
	let selectRowData = ref(null)
	let pageNumber = ref(1)
	let pageSize = ref(20)

	let msg = ref('')

	onMounted(()=>{
		new ScrollSpy(document.body, {
				target: '#sidebar-bootstrap',
				offset: 200
			})
	}) 
	
	const table = reactive({
		isLoading: false,
		isReSearch: false,
		columns: [
		{
				label: "NO",
				field: "GEONUM",
				width: "3%",
				sortable: true,
				},
				{
				label: "거래처명",
				field: "COMPANY_NAME",
				width: "10%",
				//sortable: true,
				isKey: true,
				},
				{
				label: "사용자",
				field: "USER_NAME",
				width: "10%",
				//sortable: true,
				},
				{
				label: "휴대폰번호",
				field: "USER_PHONE",
				width: "10%",
				//sortable: true,
				},
				{
				label: "이메일",
				field: "EMAIL",
				width: "10%",
				//sortable: true,
				},
				{
				label: "최종일시",
				field: "WS_EDTDATE",
				width: "10%",
				//sortable: true,
				},
		],
		rows: [],
		totalRecordCount: 0,
		sortable: {
		order: "RANK",
		sort: "asc",
		},
		rowClasses:(row) => {
    		return {
    				'table-success': JSON.stringify(row) === JSON.stringify(selectRowData.value)
  					};
		},
		isShowMoreBtn : true
	}); 
	/* Table search event*/
	const doSearch = async (offset, limit, order, sort) => { 
		table.isLoading = true;
		// table.isReSearch = !offset ? true : false;  
		let pageNumber = (Number(offset)/limit)+1 
		const param = {
				clcode : "",
				searchKeyword : searchKeyword ?? '',
				pageSize: limit,
				pageNumber :pageNumber
			}

		let data;
		try {
			data = await memberMng(param)
			if(data.RecordCount > 0){
				table.rows = data.RecordSet;
				table.totalRecordCount =71;
				table.sortable.order = order;
				table.sortable.sort = sort;
			}
		} catch (error) {
			console.error(error);
		}finally{
		table.isLoading = false; 
	}
	};
	  
	const searchBtn = (keyword) => { 
		searchKeyword = keyword 
		doSearch(0, 10, 'RANK', 'asc'); 
	}
  
    /*Table search finished event*/
    const tableLoadingFinish = (elements) => {
        table.isLoading = false;
     };

	/* row click event */
	const handleRowClick = (rowData,event) => {
		selectRowData.value = JSON.parse(JSON.stringify(rowData));
		detailShow.value = true;
	}

	const cancleBtn = (rowData) => {
		detailShow.value = false;
	}
	
	const saveBtn = (rowData) => {
		doSave()
	}

	const toastpop = () =>{
		var toast = new Toast(document.getElementById("toastMsg"));
		toast.show();
	}

	const doSave = async() =>{
		const userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
		let userId = userInfo.ID
		const param = {
			geonum : selectRowData.value.GEONUM,
			clcode : selectRowData.value.CLCODE,
			userName : selectRowData.value.USER_NAME,
			userPhone : selectRowData.value.USER_PHONE,
			companyName : selectRowData.value.COMPANY_NAME,
			companyCorpno : selectRowData.value.COMPANY_CORPNO,
			password : selectRowData.value.PASSWORD,
			email : selectRowData.value.EMAIL,
			status : selectRowData.value.STATUS,
			osType : selectRowData.value.OS_TYPE ?? '',
			joinType : "002",
			inputUser : userId
			}
			
		let data;
		try { 
			data = await memberMngSave(param)
			if(data.ResultCode === '00'){
				msg.value = "저장되었습니다."
				toastpop()
				doSearch(0, 10, 'RANK', 'asc')
			}
		} catch (error) {
			console.error(error);
		}
	}
	const addHoverClassToTr = (mouseEvent) => {
		mouseEvent.target.classList.add("hover");
	};
	const removeHoverClassFromTr = (mouseEvent) => {
		mouseEvent.target.classList.remove("hover");
	};

    // Get data first
    doSearch(0, 10, 'RANK', 'asc');

</script>
<template>
		<div class="row mb-3">
			
			<div class="col-xl-6" :style="detailShow ? '' : 'width:100%'">
				<panel data-bs-spy="scroll" data-bs-target="#navbar-example" :class="table.isLoading ? 'dimmed' : ''" >
					<panel-header>
						<panel-title>회원관리</panel-title>
						<panel-toolbar />
					</panel-header>
					<panel-body>
						<div class="input-group input-group-lg mb-3">
							<input type="text" class="form-control input-white" placeholder="검색어를 입력하세요" v-model="keyword" v-on:keyup.enter="searchBtn(keyword)" />
							<button type="button" class="btn btn-primary" @click="searchBtn(keyword)"><i class="fa fa-search fa-fw"></i>검색</button>
						</div>
						<TeoGrid :table="table" :detailShow="detailShow" @handleRowClick="handleRowClick" title="회원관리"></TeoGrid>
						<!-- <div class="vtl vtl-card">
							<div class="vtl-card-body">
								<div class="vtl-row">
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
						</div> -->
					</panel-body> 
				</panel>

			</div> 
			<div class="col-xl-6" v-if="detailShow"> 
				<div class="card border-0 mb-4" style="position: sticky; top: 70px;">
					<div class="card-header h6 mb-0 bg-none p-3">
						<i class="fa fa-dolly fa-lg fa-fw text-dark text-opacity-50 me-1"></i> 상세보기
					</div>
					<div class="card-body">
						<div class="mb-3">
							<label class="form-label">거래처명</label>
							<input type="text" class="form-control" v-model="selectRowData.COMPANY_NAME">
						</div>
						<div class="mb-3">
							<label class="form-label">사용자</label>
							<input type="text" class="form-control" v-model="selectRowData.USER_NAME">
						</div>
						<div class="mb-3">
							<label class="form-label">휴대폰번호</label>
							<input type="text" class="form-control" v-model="selectRowData.USER_PHONE">
						</div>
						<div class="mb-3">
							<label class="form-label">이메일</label>
							<input type="text" class="form-control" v-model="selectRowData.EMAIL">
						</div>

						<div class="col-md-9 pt-2">
							<div class="form-check form-check-inline" style="margin-bottom: 10px;">
								<input class="form-check-input" type="radio" id="radio1" value="20" v-model="selectRowData.STATUS"/>
								<label class="form-check-label" for="radio1">승인</label>
							</div>
							<div class="form-check form-check-inline" style="margin-bottom: 10px;">
								<input class="form-check-input" type="radio" id="radio2" value="99" v-model="selectRowData.STATUS" />
								<label class="form-check-label" for="radio2">거절</label>
							</div>
						</div>

						<div class="card-footer bg-none d-flex p-3">
							<button type="submit" class="btn btn-default ms-auto" @click="cancleBtn">취소</button>
							<button type="submit" class="btn btn-primary ms-2" @click="saveBtn">저장</button>
						</div>
						<!--
						<div class="">
							<label class="form-label">Description</label>
							<div class="form-control p-0 overflow-hidden h-250px">
								<quill-editor theme="snow" />
							</div>
						</div>
						-->
					</div>
				</div>
			</div>
		</div>

		<div class="position-fixed end-0 top-0 me-5 pt-5 mt-5 toasCenter" style="z-index: 1052 !important; ;">
			<div class="toast fade hide mb-3" data-autohide="false" id="toastMsg">
				<div class="toast-header">
					<i class="far fa-bell text-muted me-2"></i>
					<strong class="me-auto">알림</strong>
					<button type="button" class="btn-close" data-bs-dismiss="toast"></button>
				</div>
				<div class="toast-body" v-html="msg">
				</div>
			</div>
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
</style>
