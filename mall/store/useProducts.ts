import { defineStore } from "pinia";
import productData from "@/data/productData";
import ProductType from "@/types/productType";

export const useProductsStore = defineStore("products", {
  state: () => ({
    products: productData as ProductType[],
    filterProducts: productData as ProductType[],
    priceRange: [0, 500] as any,
    activeCls : '' as string,
  }),
  actions: {
    handleParentCategory(value: string) {
      this.filterProducts = this.products.filter(
        (p) => p.parentCategory.toLowerCase() === value.toLowerCase()
      );
      this.activeCls = value;
    },
    handleCategory(value: string) {
      this.filterProducts = this.products.filter(
        (p) => p.category.toLowerCase() === value.toLowerCase()
      );
      this.activeCls = value;
    },
    onChangeRange(value: number) {
      this.priceRange = value;
    },
    filterPrice() {
      if (this.priceRange.length) {
        this.filterProducts = this.products.filter(
          (p) =>
            p.price >= this.priceRange[0] && p.price <= this.priceRange[1]
        );
      }
    },
    handleSize(size: string) {
      this.filterProducts = this.products.filter(p => p.sizes?.includes(size))
      this.activeCls = size;
    },
    handleColor(color: string) {
      this.filterProducts = this.products.filter(p => p.colors?.includes(color))
      this.activeCls = color;
    },
    handleBrand(brand: string) {
      this.filterProducts = this.products.filter(p => p.brand.toLowerCase() === brand.toLowerCase())
      this.activeCls = brand;
    },
    handleSelectFiltering(value: string, item) {
      console.log("1 this.filterProducts:::", item);
      
      let processedItem = item.slice(); // 프록시 객체 내부의 배열을 복사하여 수정 결과를 저장할 변수 선언
      
      switch (value) {
        case '1':
          processedItem.sort((a, b) => b.ITNAME.localeCompare(a.ITNAME));
          break;
        case '2':
          processedItem.sort((a, b) => a.ITNAME.localeCompare(b.ITNAME));
          break;
        case '3':
          processedItem.sort((a, b) => Number(b.AMOUNT.replace(/[,]/g, '')) - Number(a.AMOUNT.replace(/[,]/g, '')));
          break;
        case '4':
          processedItem.sort((a, b) => Number(a.AMOUNT.replace(/[,]/g, '')) - Number(b.AMOUNT.replace(/[,]/g, '')));
          break;
        
        default:
          break;
      }
      
      console.log("2 this.filterProducts:::", processedItem);
      
      this.$emit("update:item", processedItem);
      return processedItem; // 수정된 배열 반환

    },
    handleResetFilter() {
      this.filterProducts = this.products;
      this.activeCls = '';
      this.priceRange = [0,500]
    }
  },
  getters:{
    getRelatedProducts(state) {
      return (category:string,id:number) => (
        state.products.filter(
          (p) => p.category.toLowerCase() === category.toLowerCase() && p.id !== id
        ).slice(0,4)
      )
    },
  }
});
