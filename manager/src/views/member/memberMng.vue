<script setup>
	import { ref, computed, reactive, onMounted,defineComponent } from 'vue';
	import highlightjs from '@/components/plugins/Highlightjs.vue';
	import vueTable from '@/components/plugins/VueTable.vue';
	import navscrollto from '@/components/app/NavScrollTo.vue';  
	import { memberApi } from '@/api';
	import { startLoadingBar, removeLoadingBar, addHoverClassToTr, removeHoverClassFromTr, doSort, showToast } from "@/common/utils.ts";

	import { useAppOptionStore } from '@/stores/app-option'; 
	const appOption = useAppOptionStore();
	//사용자 및 페이지 정보
	const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
	const userClcode = userInfo.CLCODE;
	const userId = userInfo.ID;
	const pageNumber = ref(1);
	const pageSize = ref(15);

	import { useAlert } from '@/composables/showAlert'
	const {showAlert,showAlertSuccess,showConfirm} = useAlert()
	//검색어
	let searchKeyword = ''
	
	//우측 상세보기 영역 노출여부
	let detailShow = ref(false)
	
	//메인 그리드에서 선택한 row 정보
	let rowTargetObj = '';
	let selectRowData = ref(null)
	
	const table = reactive({
		isLoading: false,
		isReSearch: false,
		columns: [
		{
				label: "NO",
				field: "RANK",
				width: "5%",
				sortable: true,
				},
				{
				label: "거래처명",
				field: "COMPANY_NAME",
				width: "10%",
				sortable: true,
				isKey: true,
				class:'pointer',
				},
				{
				label: "사업자번호",
				field: "COMPANY_CORPNO",
				width: "10%",
				sortable: true,
				isKey: true,
				},
				{
				label: "사용자명",
				width: "10%",
				//sortable: true,
				},
				{
				label: "휴대폰번호",
				width: "10%",
				//sortable: true,
				},
				{
				label: "이메일",
				field: "EMAIL",
				width: "20%",
				//sortable: true,
				},
				{
				label: "승인여부",
				field: "STATUS_NM",
				width: "10%",
				//sortable: true,
				},
		],
		rows: [],
		totalRecordCount: 0,
		sortable: {
		order: "GEONUM",
		sort: "asc",
		},
  		isShowMoreBtn: false,
	}); 
	
	/******************************************************************************* onMounted */
	onMounted(()=>{
		pageNumber.value = 1;
		doSearch();
	}) 

	/******************************************************************************* api 호출 start */
	const doSearch = async () => { //조회

  		if (table.isLoading) return;
		
		table.isLoading = true;
		table.isShowMoreBtn = false;

		startLoadingBar();
		const param = {
				clcode : "",
				searchKeyword : searchKeyword ?? '',
				pageSize: pageSize.value,
				pageNumber :pageNumber.value
			}

		let data;
		try {
			data = await memberApi.memberMng(param)
			table.isLoading = false;
			if(data.RecordCount > 0){
				table.rows.push(...data.RecordSet);
				if(data.RecordCount === pageSize.value){
					table.isShowMoreBtn = true;
				}
			}else{
				table.isShowMoreBtn = false;
			}
		} catch (error) {
			console.error(error);
		}finally {
			removeLoadingBar();
			table.isLoading = false;
			var productDiv = document.getElementById('productDiv');   
			setTimeout(()=>{
				productDiv.scrollTop = productDiv.scrollHeight;
				
			},100) 
		}
	};
	
	const doSave = async() =>{	//저장

		const param = {
			geonum : selectRowData.value.GEONUM,
			clcode : '', // selectRowData.value.CLCODE,
			userName : selectRowData.value.USER_NAME ?? '',
			userPhone : selectRowData.value.USER_PHONE ?? '',
			companyName : selectRowData.value.COMPANY_NAME ?? '',
			companyCorpno : selectRowData.value.COMPANY_CORPNO ?? '',
			password : selectRowData.value.PASSWORD ?? '',
			email : selectRowData.value.EMAIL ?? '',
			status : selectRowData.value.STATUS ?? '',
			osType : selectRowData.value.OS_TYPE ?? '',
			joinType : "002",
			inputUser : userId,
			payYn : selectRowData.value.PAY_YN ?? ''
			}
			
		let data;
		try { 
			data = await memberApi.memberMngSave(param)
			if(data.ResultCode === '00'){
				showAlertSuccess("저장되었습니다.")
				table.rows = [];
				doSearch();
			}
		} catch (error) {
			console.error(error);
		}
	}
	/******************************************************************************* api 호출 end */
	  
	
    

	/******************************************************************************* 버튼 및 액션 이벤트 start */
	const tableLoadingFinish = (elements) => {
        table.isLoading = false;
     };
	const searchBtn = (keyword) => { //검색 Btn
		table.rows = [];
		detailShow.value = false;
		searchKeyword = keyword;
		pageNumber.value = 1;
		doSearch();
	}
	const handleRowClick = (rowData,event) => { //좌측 메인 그리드 row click event

		if(rowTargetObj){
			rowTargetObj.classList.remove("table-good");
		}
		selectRowData.value = JSON.parse(JSON.stringify(rowData));
		detailShow.value = true;

		rowTargetObj = (event.target).closest('tr')
		rowTargetObj.classList.add("table-good");
	}
	const cancleBtn = (rowData) => {	//닫기 Btn
		if(rowTargetObj){
			rowTargetObj.classList.remove("table-good");
		}
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
/******************************************************************************* 버튼 및 액션 이벤트 end */


</script>
<template>
	<div class="section section__management">
		<div class="group__search">
			<div class="part__search_box">
				<div class="group__title">
					<h2>회원관리</h2>
				</div>
				<input type="text"  v-on:keyup.enter="searchBtn(keyword)" v-model="keyword" placeholder="검색어를 입력하세요" />
				<button @click="searchBtn(keyword)">
					<i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
				</button> 
			</div>
		</div>
		<div class="group__contents"> 
			<div class="part__data_list"> 
				<div class="item__scroll" id="productDiv">
					<div class="unit__scroll">
						<table> 
						<thead>
						<tr>
							<th v-for="(col, index) in table.columns"  :key="index" :style="'width :'+ col.width">
							
							<div class="unit_bundle">
								{{ col.label }}
								<div class="unit__buttons" v-if="col.sortable">
								<button :class="table.sortable.order === col.field && table.sortable.sort === 'asc' ? 'active' : ''" @click.prevent="col.sortable ? doSort(col.field, table.sortable, table.rows) : false"><i class="fa-solid fa-angle-up"></i></button>
								<button :class="table.sortable.order === col.field && table.sortable.sort === 'desc' ? 'active' : ''" @click.prevent="col.sortable ? doSort(col.field, table.sortable, table.rows) : false"><i class="fa-solid fa-angle-down"></i></button>
								</div>
							</div>
							</th> 
							</tr>
						</thead>
						<tbody >
							<!-- <tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' :  ''" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
							<td :class="col.class" v-for="(col, j) in table.columns" :key="j" v-html="obj[col.field]" style="overflow: hidden; text-overflow: ellipsis;" ></td> 
							</tr> -->
							<tr :class="selectRowData?.GEONUM === obj.GEONUM ? 'active' :  ''" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
									<td>
										<div><span>{{ obj.RANK }}</span></div>
									</td>
									<td style="text-align: left;">
										<div><span>{{ obj.COMPANY_NAME }}</span></div>
									</td>
									<td style="text-align: left;">
										<div><span>{{ obj.COMPANY_CORPNO }}</span></div>
									</td>
									<td style="text-align: left;">
										<div><span>{{ obj.USER_NAME }}</span></div>
									</td>
									<td style="text-align: left;">
										<div><span>{{ obj.USER_PHONE }}</span></div>
									</td>
									<td>
										<div><span>{{ obj.EMAIL }}</span></div>
									</td>
									<td>
										<div><span>{{ obj.STATUS_NM }}</span></div>
									</td>
							</tr>
							<tr  v-else>
							<td :colspan="table.columns.length">
							<span>no data</span>
							</td>
						</tr>
						</tbody>
						</table>
					</div>
				</div>

				<div class="item__buttons"  v-if="table.isShowMoreBtn">
					<button @click="searchMoteBtn"><i class="fa-solid fa-plus"></i>더보기</button>
				</div>
			</div>
			<div class="part__data_detail" v-if="detailShow"> 
				<!-- <div class="item__title" >
				<i class="fa-solid fa-angle-right item__angle"></i>
				<span>상세보기</span>
				</div> -->
				<div class="item__contents">
				<div>
					<label class="form-label">거래처명</label><BR/>
					<input type="text" v-model="selectRowData.COMPANY_NAME">
				</div>
				<div>
					<label class="form-label">사업자번호</label><BR/>
					<input type="text" v-model="selectRowData.COMPANY_CORPNO">
				</div>
				<div>
					<label class="form-label">사용자</label><BR/>
					<input type="text" v-model="selectRowData.USER_NAME">
				</div>
				<div>
					<label class="form-label">휴대폰번호</label><BR/>
					<input type="text" v-model="selectRowData.USER_PHONE">
				</div>
				<div>
					<label class="form-label">이메일</label><BR/>
					<input type="text"  v-model="selectRowData.EMAIL">
				</div>
				<div>
					<label class="form-label">가입여부</label><BR/>
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

<style>

</style>