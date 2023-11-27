<template>
  <div>
    <select class="sort-wrapper mr-30 pr-25 p-relative" v-model="selectVal" ref="selectBox" @change="handleSelectFiltering" style="width: 200px; height: 35px;font-size: 16px;">
      <option value="0">&nbsp;&nbsp;&nbsp;----정렬----</option>
      <option value="1">&nbsp;&nbsp;&nbsp;인기상품 순</option>
      <option value="2">&nbsp;&nbsp;&nbsp;신규상품 순</option>
      <option value="3">&nbsp;&nbsp;&nbsp;상품명 : 오름차순</option>
      <option value="4">&nbsp;&nbsp;&nbsp;상품명 : 내림차순</option>
      <option value="5">&nbsp;&nbsp;&nbsp;가격 : 최고가 순</option>
      <option value="6">&nbsp;&nbsp;&nbsp;가격 : 최저가 순</option>
    </select>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useProductsStore } from '@/store/useProducts';

export default defineComponent({
  props:{
    item: {
      type: Array,
      default: [],
    }
  },
  data() {
    return {
      selectVal:'0'
    }
  },
  
  methods: {
    handleSelectFiltering() {
      /*let processedItem = this.item.slice();

      switch (this.selectVal) {
        case '1':
          processedItem.sort((a, b) => b.FAVITEM_CNT.localeCompare(a.FAVITEM_CNT));
          break;
        case '2':
          processedItem.sort((a, b) => b.ITEM_UPDATE_DATE.localeCompare(a.ITEM_UPDATE_DATE));
          break;
        case '3':
          processedItem.sort((a, b) => a.ITNAME.localeCompare(b.ITNAME));
          break;
        case '4':
          processedItem.sort((a, b) => b.ITNAME.localeCompare(a.ITNAME));
          break;
        case '5':
          processedItem.sort((a, b) => Number(b.AMOUNT.replace(/[,]/g, '')) - Number(a.AMOUNT.replace(/[,]/g, '')));
          break;
        case '6':
          processedItem.sort((a, b) => Number(a.AMOUNT.replace(/[,]/g, '')) - Number(b.AMOUNT.replace(/[,]/g, '')));
          break;
        default:
          break;
      }

      this.$emit('update:item', processedItem);*/
      let sortType = ''
      switch (this.selectVal) {
        case '1':
          sortType = 'DESC'
          break;
        case '2':
          sortType = 'ASC'
          break;
        case '3':
          sortType = 'ASC'
          break;
        case '4':
          sortType = 'DESC'
          break;
        case '5':
          sortType = 'DESC'
          break;
        case '6':
          sortType = 'ASC'
          break;
        default:
          break;
      }
      const obj = {
        orderType10 : this.selectVal === '5' || this.selectVal === '6' ? sortType : '', //가격 높은순(DESC) , 낮은순(ASC)
        orderType20 : this.selectVal === '2' ? sortType : '',                           //상품 최신순(ASC)
        orderType21 : this.selectVal === '1' ? sortType : '',                           //상품 인기순(DESC)
        orderType30 : this.selectVal === '3' || this.selectVal === '4' ? sortType : '', //상품명 오름차순(ASC), 내림차순(DESC)
      }
      this.$emit('update:item', obj);
    },
  },
  setup () {
    const state = useProductsStore();
    return {state}
  }
})
</script>
<style>
.grey-bg{
  background-color: #f5f5f5;
}
</style>
