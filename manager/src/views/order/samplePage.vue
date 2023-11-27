<template>
  <button @click="deselectRows">deselect rows</button> | 
  <button v-on:click="onBtnExport()">Download CSV export file</button> 
  <AgGridVue
    class="ag-theme-alpine"
    style="height: 1000px"
    :columnDefs="columnDefs.value"
    :rowData="rowData.value"
    :defaultColDef="defaultColDef"
    rowSelection="multiple"
    animateRows="true"
    copyHeadersToClipboard="true" 
    @cell-clicked="cellWasClicked"
    @grid-ready="onGridReady"
    suppressColumnMoveAnimation=true
  >
  </AgGridVue>
</template>

<script setup>    

import { reactive, onMounted, ref } from "vue";

import { AgGridVue } from "ag-grid-vue3";  // the AG Grid Vue Component
    const gridApi = ref(null); // Optional - for accessing Grid's API

    // Obtain API from grid's onGridReady event
    const onGridReady = (params) => { 
      gridApi.value = params.api;
    };

    const rowData = reactive({}); // Set rowData to Array of Objects, one Object per Row

    // Each Column Definition results in one Column.
    const columnDefs = reactive({
      value: [
           { 
             headerName: 'Athlete (locked as pinned)',
             field: "make",
             pinned: 'left',
             lockPinned: true,
             cellClass: 'lock-pinned',
             editable: false,
             width: 240,
          }, 
           { field: "model" },
           { field: "price" },
           { field: "model" },
           { field: "price" },
           { field: "model" },
           { field: "price" },
           { field: "model" },
           { field: "price" },
           { field: "model" },
           { field: "price" },
           { field: "model" },
           { field: "price" },
      ],
    });

    // DefaultColDef sets props common to all Columns
    const defaultColDef = {
      sortable: true,
      filter: true,
      editable: true,
      resizable:true,
      // flex: 1
    };

  // Example load data from server
  onMounted(() => {
    fetch("https://www.ag-grid.com/example-assets/row-data.json")
      .then((result) => result.json())
      .then((remoteRowData) => (rowData.value = remoteRowData));
  });

const cellWasClicked = (event) => { // Example of consuming Grid Event
  console.log("cell was clicked", event);
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
</script>

<style lang="scss">

.lock-pinned {
  background: #ddd;
}

</style>