<script setup lang="ts">
import {  onMounted, reactive, ref } from 'vue'; 
import { mapInstance } from "@/naverMap/stores";
import  {
  NaverMap} from "@/naverMap";
 
	import { useAlert } from '@/composables/showAlert'
	const {showAlert} = useAlert()


let selectRowData = ref([])
const isShowTable = ref(true)
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
			sortable: true,
			isKey: true,
			},
			{
			label: "위도",
			field: "latitude",
			width: "10%",
			},
			{
			label: "경도",
			field: "longitude",
			width: "10%",
			},  
			{
			label: "테스트",
			field: "longitude",
			width: "10%",
			}, 
	],
	rows: [
{

  RANK : '1',
  CLNAME : '오징어나라',
  latitude : '37.5632666',
  longitude : '126.9804949'
},
{

  RANK : '2',
  CLNAME : '석촌김밥',
  latitude : '37.5619065',
  longitude : '126.9827689'
},
{

  RANK : '3',
  CLNAME : '이촌유통',
  latitude : '37.5639213',
  longitude : '126.9818039'
},
{

  RANK : '4',
  CLNAME : '동촌삼겹살',
  latitude : '37.5653719',
  longitude : '126.9837414'
},

  ],
	totalRecordCount: 0,
	sortable: {
	order: "RANK",
	sort: "asc",
	},
	isShowMoreBtn: true,
})

const handleRowClick = (rowData) => { 

	selectRowData.value = JSON.parse(JSON.stringify(rowData));

  onRemove()
  const title = selectRowData.value.CLNAME
  const latitude = selectRowData.value.latitude
  const longitude = selectRowData.value.longitude
  goMap(latitude,longitude,title)
}

var markerList = []  // 마커 객체
var infowindow = []; // 마커 위 타이틀 객체
var circleList = []
const onRemove = () =>{ //마커 및 타이틀 객체 지우기
  markerList.forEach(el => {
    el.setMap(null);
  });
  infowindow.forEach(el => {
        el.setMap(null);
      });
  circleList.forEach(el => {
        el.setMap(null);
      });
  markerList = []
  infowindow = []
  circleList = []
      
}

const clickOk = (e) =>{
  onRemove()
   var marker = new naver.maps.Marker({
        position: e.coord,
        map: mapInstance.value
    });
    markerList.push(marker); 
}

const goMap = (lat, lng, name) => {
      mapInstance.value.setZoom(17);
      onRemove();
      let latLng = new window.naver.maps.LatLng(lat, lng)
      mapInstance.value.setCenter(latLng);
      markerList.push(
        new window.naver.maps.Marker({
          position: latLng,
          map: mapInstance.value,
          zIndex: 100,
        }),
      );
      infowindow.push(
        new window.naver.maps.InfoWindow({
          content: '<div style="width:150px;text-align:center;padding:10px;">' + name + '</b></div>',
        }),
      );
      infowindow[0].open(mapInstance.value, markerList[0]);
      initMap(latLng)
}
const initMap = (latLng) => {
  // 오버레이 추가 å
 
    var circle = new naver.maps.Circle({
        map: mapInstance.value, 
        center:  latLng,
        radius: 100,
        strokeColor: '#ff0000',
        fillColor: '#ff0000',
        fillOpacity: 0.3,
        clickable: true,
        zIndex: 1
    }); 
    circleList.push(circle)
 

}
const test = (e) => {
  e.stopPropagation() 
  const circle = circleList[0]
  circle.setRadius(200)
}
const setIsShowhide = () => {
  isShowTable.value = !isShowTable.value
    setTimeout( function() { 
		window.dispatchEvent(new Event('resize'));
	}, 1);
}

onMounted(()=>{
    showAlert("메뉴 준비중입니다.")
})
</script>

<template>

		<div>
    
    <panel data-bs-spy="scroll" data-bs-target="#navbar-example">
        <panel-header>
          <panel-title>지도 데이터 </panel-title>
          <panel-toolbar />
        </panel-header>
        <panel-body> 
         <div class="row mb-3">
							<div class="col-xl-4">
								<div class="vtl vtl-card">
									<div class="vtl-card-body" style="text-align: center">
										<div class="vtl-row">
<!-- brightness-high
brightness-high-fill -->
                         <div style="text-align: start;"> 
                              <button type="button" class="btn btn-lg btn-primary me-1 mb-1" @click="setIsShowhide()"><i class="fas fa-lg fa-fw me-10px" :class="isShowTable ? 'fa-toggle-on' : 'fa-toggle-off'"></i>{{isShowTable ? '리스트 감추기' : '리스트 보이기'}}</button> 
                              <button type="button" class="btn btn-lg btn-primary me-1 mb-1"  @click="saveBtn()"><i class="fa fa-circle-check fa-fw"></i>저장</button>  
                            </div> 
											<div class="col-sm-12" v-if="isShowTable" >
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
													<tbody>
														<tr class="vtl-tbody-tr " v-if="table.isShowMoreBtn" v-for="(obj, index) in table.rows" :key="index"  @click="handleRowClick(obj, $event)">
															<td class="vtl-tbody-td ">
																<div>
																	<span>{{ index+1 }}</span>
																</div>
															</td>
															<td class="vtl-tbody-td">
																<div>
																	<span>{{ obj.CLNAME }}</span>
																</div>
															</td>
															<td class="vtl-tbody-td">
																<div>
																	<span>{{ obj.latitude }} </span>
																</div>
															</td>
															<td class="vtl-tbody-td">
																<div>
																	<span>{{ obj.longitude }}</span>
																</div>
															</td>
															<td class="vtl-tbody-td">
																<div>
																	<span> <button @click="test">클릭</button></span>
																</div>
															</td>
														</tr> 
													</tbody>
												</table>
											</div>
										</div> 
									</div>
								</div>
							</div>
							<!--modal-->
							<div :class="!isShowTable ? 'col-xl-10' : 'col-xl-8'" :style="!isShowTable ? 'width:100%' :  ''"> 
								<div class="card border-0 mb-4" style="position: sticky; top: 100px;">
									<div class="card-header h6 mb-0 bg-none p-3"> 
									</div>
									<div class="card-body">
									 <NaverMap  style="height: 80vh">   @click="clickOk" >
                        <!-- <NaverMarker
                          :latitude="targetMap.latitude"
                          :longitude="targetMap.longitude"
                          @on-load="marker = $event"
                        />
                        <NaverInfoWindow :marker="marker" :open="true">
                          {{targetMap.title}}
                        </NaverInfoWindow> -->

                        <!-- <NaverCircle
                          :latitude="37.566616443521745"
                          :longitude="126.97837068565364"
                        /> -->
                    <!-- 
                        <NaverEllipse :bounds="ellipseBounds" /> -->
                    <!-- 
                        <NaverGroundOverlay :url="groundOverlayImg" :bounds="groundOverlayBound" /> -->

                        <!-- <NaverPolygon :paths="polygonPaths" /> -->

                        <!-- <NaverPolyline :path="polylinePaths" />

                        <NaverRectangle :bounds="rectangleBounds" /> -->
                      </NaverMap>
									</div>
								</div>
							</div>
						</div>
        </panel-body> 
      </panel>
    </div>

 
</template>

<style scoped>
tr {
cursor: pointer;
}
</style>
