<script setup>
	import {  reactive, onMounted, ref } from 'vue'; 
	import { useAppOptionStore } from '@/stores/app-option';
	import { onBeforeRouteLeave } from 'vue-router'
	import vueTable from '@/components/plugins/VueTable.vue';
	import navscrollto from '@/components/app/NavScrollTo.vue';  
	import { ScrollSpy } from 'bootstrap';
	import { memberApi } from '@/api';
	import { Toast } from 'bootstrap';
	import { startLoadingBar, removeLoadingBar, addHoverClassToTr, removeHoverClassFromTr, doSort, showToast } from "@/common/utils.ts";

	import { useAlert } from '@/composables/showAlert'
    const {showAlert,showAlertSuccess,showConfirm} = useAlert()

	//사용자 및 페이지 정보
	const userInfo = JSON.parse(sessionStorage.getItem("userInfo"));
	const userClcode = userInfo.CLCODE;
	const userId = userInfo.ID;
	const pageNumber = ref(1);
	const pageSize = ref(10);
	//검색어
	let searchKeyword = ''
	
	//메인 그리드에서 선택한 row 정보
	let selectRowData = ref([])

	//모달창 데이터
	let memo = ref('')//주문명
	let amount = ref('')//금액

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
				field: "CLNAME",
				width: "10%",
				sortable: true,
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
				sortable: true,
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
		},
		isShowMoreBtn: false,
	})
	/******************************************************************************* onMounted */
	onMounted(()=>{
		pageNumber.value = 1;
		doSearch();

		// 모달객체를 밖으로 추가해준다.
		setTimeout(()=>{
			const arrPrvModels = document.getElementById('modal_insert_area').getElementsByClassName("modal");
			for (var i = 0; i < arrPrvModels.length; i++) {
			// 기존요소 삭제
			document.getElementById('modal_insert_area').removeChild(arrPrvModels[i]);
			}
			const arrModels = document.getElementsByClassName('section')[0].getElementsByClassName("modal");
			for (var i = 0; i < arrModels.length; i++) {
			// 신규요소 추가
			document.getElementById('modal_insert_area').append(arrModels[i]);
			}
		},1000);
	})

	/******************************************************************************* api 호출 start */
	const doSearch = async (offset, limit, order, sort) => {	//조회

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
			data = await memberApi.accoutPayment(param);
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
	}

	const doSave = async (e) => {	//저장

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
				showAlertSuccess("정상 처리되었습니다.")
				setTimeout(() => {
					document.getElementById('closeButton').click();//저장 후 모달창 닫기
				}, 1000);
			}else{
				showAlert("관리자에게 문의바랍니다.")
			}
		} catch (error) {
			console.error(error);
		}
		}
	/******************************************************************************* api 호출 end */
	  
	
    

	/******************************************************************************* 버튼 및 액션 이벤트 start */
	const tableLoadingFinish = (elements) => {
		table.isLoading = false;
	}
	const searchBtn = (keyword) => { //검색 Btn
		table.rows = [];
		searchKeyword = keyword 
		pageNumber.value = 1;
		doSearch(); 
	}
	const handleRowClick = (rowData) => {//좌측 메인 그리드 row click event
		
		memo.value = ''
		amount.value = ''//주문명, 금액 초기화

		selectRowData.value = JSON.parse(JSON.stringify(rowData));
	}
	const modalSaveBtn = (e) =>{	//저장 Btn

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
		if(!selectRowData.value.CLPHONE){
			chk = true 
			chkId = !chkId? `전화번호` : `${chkId}, 전화번호`
		}

		if(chk){
			showAlert(`필수입력 항목을 확인해 주세요..<br>(${chkId})`)
			event.preventDefault();
			return;
		}
		doSave(e)
	}
	const searchMoteBtn = () => {	//더보기 Btn
		pageNumber.value = pageNumber.value + 1;
		doSearch();
	} 
	const checkKey = (obj) =>{		//모달 금액 포매팅
		if(obj === 'amount'){
			amount.value = amount.value.replace(/[^0-9]/g,'').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		}else if(obj === 'clphone'){
			selectRowData.value.CLPHONE = selectRowData.value.CLPHONE.replace(/\D/g, '');
    	}
		
	}
	/******************************************************************************* 버튼 및 액션 이벤트 end */

     
</script>
<template>

  <div class="section section__management">
		<!-- <div class="group__title">
			<h2>거래처수기결제</h2> 
		</div>
		<div class="group__search">
			<div class="part__search_box">
				<input type="text"  v-on:keyup.enter="searchBtn(keyword)" v-model="keyword" placeholder="검색어를 입력하세요" />
				<button @click="searchBtn(keyword)">
				<i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
				</button> 
			</div>
		</div> -->
		<div class="group__search">
			<div class="part__search_box">
				<div class="group__title">
					<h2>거래처수기결제</h2>
				</div>
				<input type="text"  v-on:keyup.enter="searchBtn(keyword)" v-model="keyword" placeholder="검색어를 입력하세요" />
				<button @click="searchBtn(keyword)">
					<i class="fa-solid fa-magnifying-glass"></i><span>검색</span>
				</button> 
          	</div>
          </div>
		<div class="group__contents"  > 
			<div class="part__data_list"> 
				<div class="item__scroll" id="productDiv">
				<div class="unit__scroll" >
					<table   > 
					<thead>
					<tr>
						<th v-for="(col, index) in table.columns"  :key="index">
						
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
					<tbody data-bs-toggle="modal" data-bs-target="#modalAccount" >
						<tr :class="selectRowData?.RANK === obj.RANK ? 'active' :  ''" class="pointer" style="text-decoration:none" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj, $event)">
						
						<td :class="col.class" v-for="(col, j) in table.columns" :key="j" >   
													<div>
														<span v-if="col.field === 'CLTEL'">{{ obj[col.field] }}<br v-if="obj[col.field]">{{ obj.CLPHONE }}</span>
														<span v-else>{{ obj[col.field] }}</span>
													</div>
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
				<!--modal-->
			<div class="modal fade" id="modalAccount"  style="z-index: 1051 !important;">
				<div class="modal-dialog setModalCenter">
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
								<label class="form-label">결제문자 수신 전화번호</label>
								<input type="text" pattern="[0-9]*" class="form-control" placeholder="수신 전화번호를 입력해주세요.." v-model="selectRowData.CLPHONE" @input="checkKey('clphone')"/>
							</div>
							<div class="mb-3">
								<label class="form-label">주문명</label>
								<input type="text" class="form-control" placeholder="주문명을 입력해주세요.." v-model = "memo"/>
							</div>
							<div class="mb-3">
								<label class="form-label">금액</label>
								<input type="text" pattern="[0-9]*" class="form-control" placeholder="금액을 입력해주세요.." v-model = "amount" @input="checkKey('amount')"/>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-white" data-bs-dismiss="modal" id = "closeButton"><i class="fa fa-circle-xmark fa-fw fa-lg"></i>  닫기</a>
							<button type="button" class="btn btn-primary" id="saveBtn" @click="modalSaveBtn(e)"><i class="fa fa-circle-check fa-fw fa-lg"></i>  결제문자 전송</button>
						</div>
					</div>
				</div>
			</div>
		</div>
</div>



<!-- 

	<body>
		<div>
			<panel data-bs-spy="scroll" data-bs-target="#navbar-example">
				<panel-header>
					<panel-title>거래처수기결제</panel-title>
					<panel-toolbar />
				</panel-header>
				<panel-body>
					<div class="input-group input-group-lg mb-3">
						<input type="text" class="form-control input-white" placeholder="검색어를 입력하세요" v-model="keyword" v-on:keyup.enter="searchBtn(keyword)"/>
						<button type="button" class="btn btn-primary" @click="searchBtn(keyword)"><i class="fa fa-search fa-fw"></i>검색</button>
					</div>
					<div class="vtl vtl-card">
						<div class="vtl-card-body" style="text-align: center">
							<div class="vtl-row">
								<div class="col-sm-12">
									<table class="vtl-table vtl-table-hover vtl-table-bordered vtl-table-responsive vtl-table-responsive-sm table-striped table tablelocal">
										<thead class="vtl-thead">
											<tr class="vtl-thead-tr">
												<th v-for="(col, index) in table.columns" class="vtl-thead-th" :class="col.headerClasses" :key="index" :style="Object.assign({width: col.width ? col.width : 'auto',}, col.headerStyles)">
													<div class="vtl-thead-column"
														:class="{'vtl-sortable': col.sortable, 'vtl-both': col.sortable, 
																'vtl-asc': table.sortable.order === col.field && table.sortable.sort === 'asc',
																'vtl-desc': table.sortable.order === col.field && table.sortable.sort === 'desc',}"
														@click.prevent="col.sortable ? doSort(col.field, table.sortable, table.rows) : false">
														{{ col.label }}
													</div>
												</th>
											</tr>
										</thead>
										<tbody data-bs-toggle="modal" data-bs-target="#modalAccount">
											<tr class="vtl-tbody-tr" v-if="table.rows.length > 0" v-for="(obj, index) in table.rows" :key="index" @mouseenter="addHoverClassToTr" @mouseleave="removeHoverClassFromTr" @click="handleRowClick(obj)">
												<td v-for="(col, j) in table.columns" :key="j" class="vtl-tbody-td">
													<div>
														<span v-if="col.field === 'CLTEL'">{{ obj[col.field] }}<br v-if="obj[col.field]">{{ obj.CLPHONE }}</span>
														<span v-else>{{ obj[col.field] }}</span>
													</div>
												</td>
											</tr>
											<tr class="vtl-tbody-tr" v-else>
												<td :colspan="table.columns.length">
												<span>no data</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<button v-if="table.isShowMoreBtn" type="button" class="btn btn-primary btn-lg" style="text-align: center" @click="searchMoteBtn">더보기</button>
						</div>
					</div>
				</panel-body> 
			</panel>

		

		</div> 
	</body> -->
</template>
