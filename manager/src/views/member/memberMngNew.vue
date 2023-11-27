<script setup>
	import { ref, computed, reactive, onMounted,defineComponent } from 'vue';
	import { AgGridVue } from "ag-grid-vue3";  // the AG Grid Vue Component
	import highlightjs from '@/components/plugins/Highlightjs.vue';
	import vueTable from '@/components/plugins/VueTable.vue';
	import navscrollto from '@/components/app/NavScrollTo.vue';
	import { memberApi } from '@/api';
	import { startLoadingBar, removeLoadingBar, addHoverClassToTr, removeHoverClassFromTr, doSort, showToast } from "@/common/utils.ts";
	 
 
	const gridApi = ref(null);
	const rowData = reactive([]);
	const isShowMoreBtn = ref(false)

	const onCellValueChanged = (params)=>{
		const colId = params.column.getId();  
		if (colId === 'STATUS') {
			// const selectedCountry = params.data.country;
			// const selectedCity = params.data.city;
			// const allowedCities = countyToCityMap(selectedCountry);
			// const cityMismatch = allowedCities.indexOf(selectedCity) < 0;

			// if (cityMismatch) {
			// 	params.node.setDataValue('city', null);
			// }
		}
	}

	const defaultColDef = {
		sortable: true,
		filter: true,
		editable: true,
		resizable:true,
		cellStyle: { textAlign: 'center' },
		onCellValueChanged:onCellValueChanged
		// flex: 1
	};
	const cellCellEditorParams = (params) => { 
		const allowedData =   ['신규가입', '승인','거절']  

		return {
			values: allowedData,
			formatValue: (value) => `${value}`,
		};
	};
	const columnDefs = reactive({
	value: [
		{
			headerName: 'NO',
			field: "seq",
			pinned: 'left',
			lockPinned: true,
			cellClass: 'header-center',
			editable: false, 
			valueGetter: 'node.rowIndex + 1',
			cellStyle: {textAlign: 'center'},

			width: 100
		},
		{
			headerName: '거래처명',
			field: "COMPANY_NAME",
			pinned: 'left',
			lockPinned: true,
			cellClass: 'lock-pinned', 
			cellStyle: {textAlign: 'left'},
		},
		{
			headerName: '사업자번호',
			field: "COMPANY_CORPNO",
			// pinned: 'left',
			lockPinned: true,
			cellClass: 'lock-pinned',
			width: 190
		},
		{
			headerName: '사용자명',
			field: "USER_NAME",
		},
		{
			headerName: '휴대폰번호',
			field: "USER_PHONE",
		},
		{
			headerName: '이메일',
			field: "EMAIL",
		},
		{
			headerName: '승인여부',
			field: "STATUS_NM",   
			editable: false, 
		},
	],
	});

	const dataForBottomGrid = [
	{
		athlete: 'Total',
		age: '15 - 61',
		country: 'Ireland',
		year: '2020',
		date: '26/11/1970',
		sport: 'Synchronised Riding',
		gold: 55,
		silver: 65,
		bronze: 12,
	},
	];






	import { useAppOptionStore } from '@/stores/app-option';
	const appOption = useAppOptionStore();
	//사용자 및 페이지 정보
	const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
	const userClcode = userInfo.CLCODE;
	const userId = userInfo.ID;
	const pageNumber = ref(1);
	const pageSize = ref(20);

	import { useAlert } from '@/composables/showAlert'
	const {showAlert,showAlertSuccess,showConfirm} = useAlert()
	//검색어
	let searchKeyword = ref('')

	//우측 상세보기 영역 노출여부
	let detailShow = ref(false)

	//메인 그리드에서 선택한 row 정보
	let rowTargetObj = '';
	let selectRowData = ref({})
 

	/******************************************************************************* onMounted */
	onMounted(()=>{ 
		pageNumber.value = 1;
		doSearch();
		
		 
	})

	/******************************************************************************* api 호출 start */
	const doSearch = async () => { //조회
 
		isShowMoreBtn.value = false
		startLoadingBar();
		const param = {
				clcode : "",
				searchKeyword : searchKeyword.value ?? '',
				pageSize: pageSize.value,
				pageNumber :pageNumber.value
			}

		let data;
		try {
			data = await memberApi.memberMng(param) 
			if(data.RecordCount > 0){ 
				let length = rowData.length 
				rowData.push(...data.RecordSet) 
					gridApi.value.columnModel.gridOptionsService.gridOptions.api.setRowData(rowData)  
				if(data.RecordCount === pageSize.value){
					isShowMoreBtn.value = true;
				}
			}else{ 
			}
		} catch (error) {
			console.error(error);
		}finally {
			removeLoadingBar(); 
			var productDiv = document.getElementsByClassName('ag-body-viewport')[0]
			setTimeout(()=>{
				productDiv.scrollTop = productDiv.scrollHeight;

			},100)
		}
	};

	const doSave = async() =>{	//저장

		const param = {
			geonum : selectRowData.value.GEONUM,
			clcode : '', // selectRowData.value.CLCODE,
			userName : selectRowData.value.USER_NAME,
			userPhone : selectRowData.value.USER_PHONE,
			companyName : selectRowData.value.COMPANY_NAME,
			companyCorpno : selectRowData.value.COMPANY_CORPNO,
			password : selectRowData.value.PASSWORD,
			email : selectRowData.value.EMAIL,
			status : selectRowData.value.STATUS,
			osType : selectRowData.value.OS_TYPE ?? '',
			joinType : "002",
			inputUser : userId,
			payYn : selectRowData.value.PAY_YN
			}

		let data;
		try {
			data = await memberApi.memberMngSave(param)
			if(data.ResultCode === '00'){
				showAlertSuccess("저장되었습니다.") 
				doSearch();
			}
		} catch (error) {
			console.error(error);
		}
	}
	/******************************************************************************* api 호출 end */




	/******************************************************************************* 버튼 및 액션 이벤트 start */ 
		// var gridOptions = {
		// 	columnDefs: columnDefs.value,
		// 	rowData: rowData.value,
		// 	defaultColDef:defaultColDef.value,
		// 	rowSelection: 'multiple', /* 'single' or 'multiple',*/
		// 	animateRows:true,
		// 	copyHeadersToClipboard:true,
		// 	enableColResize: true,
		// 	enableSorting: true,
		// 	enableFilter: true,
		// 	enableRangeSelection: true,
		// 	suppressColumnMoveAnimation:true,
		// 	suppressRowClickSelection: false, 
		// 	suppressHorizontalScroll: true,
		// 	localeText: {noRowsToShow: '조회 결과가 없습니다.'},
		// 	getRowStyle: function (param) {
		// 		if (param.node.rowPinned) {
		// 			return {'font-weight': 'bold', background: '#dddddd'};
		// 		}
		// 		return {'text-align': 'center'};
		// 	},
		// 	getRowHeight: function(param) {
		// 		if (param.node.rowPinned) {
		// 			return 30;
		// 		}
		// 		return 24;
		// 	},
		// 	// GRID READY 이벤트, 사이즈 자동조정 
		// 	onGridReady: function (event) {
		// 		event.api.sizeColumnsToFit();
		// 	},
		// 	// 창 크기 변경 되었을 때 이벤트 
		// 	onGridSizeChanged: function(event) {
		// 		event.api.sizeColumnsToFit();
		// 	},
		// 	onRowEditingStarted: function (event) {
		// 		console.log('never called - not doing row editing');
		// 	},
		// 	onRowEditingStopped: function (event) {
		// 		console.log('never called - not doing row editing');
		// 	},
		// 	onCellEditingStarted: function (event) {
		// 		console.log('cellEditingStarted');
		// 	},
		// 	onCellEditingStopped: function (event) {
		// 		console.log('cellEditingStopped');
		// 	},    
		// 	onRowClicked : function (event){
		// 		console.log('onRowClicked');
		// 	},
		// 	onCellClicked : function (event){
		// 		console.log('onCellClicked');
		// 	},
		// 	isRowSelectable : function(event){
		// 		console.log('isRowSelectable');
		// 		return true;
		// 	},
		// 	onSelectionChanged : function (event) {
		// 		console.log('onSelectionChanged');
		// 	},
		// 	onSortChanged: function (event) {
		// 		console.log('onSortChanged');
		// 	},
		// 	onCellValueChanged: function (event) {
		// 		console.log('onCellValueChanged');
		// 	},
		// 	getRowNodeId : function(data) {
		// 		return null; 
		// 	},
		// 	// 리드 상단 고정 
		// 	setPinnedTopRowData: function(data) {
		// 		return null; 
		// 	},
		// 	// 그리드 하단 고정 
		// 	setPinnedBottomRowData: function(data) {
		// 		return null; 
		// 	},
		// 	// components:{
		// 	//     numericCellEditor: NumericCellEditor,
		// 	//     moodEditor: MoodEditor
		// 	// },
		// 	debug: false
		// };


 
		const setSelectRowData = (data)=>{ 
			selectRowData.value = {}
			selectRowData.value.COMPANY_NAME = data.COMPANY_NAME
			selectRowData.value.COMPANY_CORPNO = data.COMPANY_CORPNO
			selectRowData.value.USER_NAME = data.USER_NAME
			selectRowData.value.USER_PHONE = data.USER_PHONE
			selectRowData.value.EMAIL = data.EMAIL
			selectRowData.value.STATUS = data.STATUS
		}

		const cellValueChanged = (event) => {   
			setSelectRowData(event.data) 
		}
		const cellWasClicked = (event) => {
			detailShow.value  = true
			selectRowData.value  = event.data
			//console.log("cell was clicked", event);
		}
		const cellFocused = (event) => { 
			setSelectRowData(rowData[event.rowIndex])
		}
		const deselectRows = () =>{
			gridApi.value.deselectAll()
		}
		const onBtnExport = () => {
			gridApi.value.exportDataAsCsv();
		}
		const getState = () =>{
			console.log(gridApi.value.getAllColumns())
		}

		const onGridReady = (params) => {
			gridApi.value = params.columnApi;
		};



	const tableLoadingFinish = (elements) => { 
     };
	 const searchBtnEnter = (event) => { //검색 Btn 
		if(event.keyCode != 13) return
		rowData.splice(0)
		detailShow.value = false; 
		pageNumber.value = 1;
		doSearch();
	} 
	const searchBtn = (keyword) => { //검색 Btn 
		rowData.splice(0)
		detailShow.value = false; 
		pageNumber.value = 1;
		doSearch();
	} 
	const cancleBtn = (rowData) => {	//닫기 Btn 
		detailShow.value = false;
	}
	const saveBtn = (rowData) => {	//저장 Btn
		doSave()
	}
	const searchMoteBtn = () => {	//더보기 Btn
		pageNumber.value = pageNumber.value + 1;
		doSearch();
	};
	const setHIde = () =>{
		appOption.appHeaderHide = !appOption.appHeaderHide
		appOption.appContentFullHeight = !appOption.appContentFullHeight
	}

	const getHeaderData = ()=> {
		const headerData = []; 

		for (const columnDef of columnDefs.value) {
			headerData.push({ [columnDef.field]: columnDef.headerName });
		}

		return headerData;
		}

		
/******************************************************************************* 버튼 및 액션 이벤트 end */


</script>
<template>

        <div class="section section__management">
          <!-- <div class="group__title">
            <h2>회원관리</h2>
          </div> -->
          <!-- <div class="group__search">
            <div class="part__search_box">
              <input type="text"  v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="검색어를 입력하세요" />
              <button @click.prevent="searchBtn">
                <i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
              </button>
            </div>
          </div> -->
		  <div class="group__search">
			<div class="part__search_box">
				<div class="group__title">
					<h2>회원관리</h2>
				</div>
				<input type="text"  v-on:keyup.prevent="searchBtnEnter" v-model="searchKeyword" placeholder="검색어를 입력하세요" />
				<button @click.prevent="searchBtn">
					<i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
				</button>
          	</div>
          </div>
          <div class="group__contents">
            <div class="part__data_list" style="height:100%">
              <div class="item__scroll" id="productDiv"> 
					<!-- :pinnedTopRowData = getHeaderData() -->
					<AgGridVue 
						class="ag-theme-alpine"
						style="height: 950px"
						:columnDefs="columnDefs.value"
						:rowData="rowData"
						:defaultColDef="defaultColDef"  
  						suppressColumnVirtualisation="true"
						animateRows="true"
						copyHeadersToClipboard="true"
						@cell-clicked="cellWasClicked"
						@cell-focused="cellFocused"
						@grid-ready="onGridReady" 
						@cell-value-changed="cellValueChanged"
						 
					>
					</AgGridVue>  
					<div class="custom-footer"  v-if="isShowMoreBtn"> 
						<div class="item__buttons" >
							<button @click="searchMoteBtn"><i class="fa-solid fa-plus"></i>더보기</button>
						</div>
						</div>
					</div> 

            </div>

             
            <div class="part__data_detail" v-if="detailShow">
              <!-- <div class="item__title" >
                <i class="fa-solid fa-angle-right item__angle"></i>
                <span>상세보기</span>
              </div> -->
              <div class="item__contents">
                <div>
				  <label class="form-label">거래처명</label><br/>
				  <input type="text" v-model="selectRowData.COMPANY_NAME">
                </div>
                <div>
                  <label class="form-label">사업자번호</label><br/>
                  <input type="text" v-model="selectRowData.COMPANY_CORPNO">
                </div>
                <div>
					<label class="form-label">사용자</label><br/>
                  <input type="text" v-model="selectRowData.USER_NAME">
                </div>
                <div>
					<label class="form-label">휴대폰번호</label><br/>
                  <input type="text" v-model="selectRowData.USER_PHONE">
                </div>
                <div>
					<label class="form-label">이메일</label><br/>
                  <input type="text"  v-model="selectRowData.EMAIL">
                </div>
                <div>
				  <label class="form-label">가입여부</label><br/>
                  <div class="radio_group">
                    <label>
                      <input type="radio"  id="radio1-1" value="10" v-model="selectRowData.STATUS"/><span for="radio1-1">신규가입</span>
                    </label>
                    <label>
                      <input type="radio"  id="radio1-2" value="20" v-model="selectRowData.STATUS"/><span for="radio1-2">승인</span>
                    </label>
                    <label>
                      <input type="radio"  id="radio1-3" value="99" v-model="selectRowData.STATUS" /><span for="radio1-3"> 거절</span>
                    </label>
                  </div>
                </div>
                <!-- <div>
                  <p>선결제필수 여부</p>
                  <div class="radio_group">
                    <label>
                      <input type="radio"   id="radio2-1" value="Y" v-model="selectRowData.PAY_YN"/> <span for="radio2-1">예</span>
                    </label>
                    <label>
                      <input type="radio" id="radio2-2" value="N" v-model="selectRowData.PAY_YN"  for="radio2-2"/><span
                        >아니오</span
                      >
                    </label>
                  </div>
                </div> -->
              </div>
              <div class="item__buttons">
                <button class="btn_close" @click="cancleBtn">
                  <i class="fa-regular fa-circle-xmark"></i><span>닫기</span>
                </button>
                <button class="btn_save" @click="saveBtn">
                  <i class="fa-regular fa-circle-check"></i><span>저장</span>
                </button>
              </div>
            </div>
          </div>
        </div>
</template>


<style lang="scss">

// .lock-pinned {
//   background: #ddd;
// }
.header-center {
  text-align: center;
}
.ag-header-cell-text{
	width:240px;
  text-align: center;
    color: #fff;
}

.sticky {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background-color: #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}
.ag-header{
	background-color: #565656;
	border-radius: 10px;
}
.ag-root-wrapper{
	border: 0;
}

.custom-footer {
  display: flex;
  justify-content: center;
  margin-top: 10px;
  border:1px solid #565656;
}
</style>