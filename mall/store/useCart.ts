import { defineStore } from 'pinia';
import ProductType from '@/types/productType';
import { number } from 'yup';

export const useCartStore = defineStore('cart', {
  state: () => ({
    cart_products: [] as any[],
    orderQuantity: 1 as number,
    quantityCount: 0 as number,
    total: 0 as number,
    checkedCartList: [],//장바구니에서 체크한 항목 결제화면으로 이동 예정
    cartTotalCnt: 0 as number,
    cartTotalAmount: 0 as number,
    orderProductNm : ''
  }),
  
  actions: {
    // add_cart_product
    add_cart_product(payload: any) {
      const isExist = this.cart_products.some((i) => i.GEONUM === payload.GEONUM);
      if (!isExist) {
        const newItem = {
          ...payload,
          orderQuantity: 1,
        };
        this.cart_products.push(newItem);
        useNuxtApp().$toast.success(`${payload.ITNAME} added to cart`);
      } else {
        this.cart_products.map((item) => {
          if (item.GEONUM === payload.GEONUM) {
            if (typeof item.QTY !== 'undefined') {
              if (Number(item.ITBOX_IPQTY) >= item.QTY + this.orderQuantity) {
                item.orderQuantity =
                  this.orderQuantity !== 1
                    ? this.orderQuantity + item.QTY
                    : item.QTY + 1;
                useNuxtApp().$toast.success(`${this.orderQuantity} ${item.ITNAME} added to cart`);
              } else {
                useNuxtApp().$toast.error(`No more quantity available for this product!`);
                this.orderQuantity = 1;
              }
            }
          }
          return { ...item };
        });
      }
      localStorage.setItem('cart_products', JSON.stringify(this.cart_products));
    },
    // quantityDecrementㅁ
    quantityDecrement(payload: any) {
      this.cart_products.map((item) => {
        if (item.GEONUM === payload.GEONUM) {
          if(typeof item.QTY !== 'undefined'){
            if (item.QTY > 1) {
              item.QTY = item.QTY - 1;
            }
          }
        }
        return { ...item };
      });
      localStorage.setItem('cart_products', JSON.stringify(this.cart_products));
    },
    // remover_cart_products
    remover_cart_products (payload:any){
      this.cart_products = this.cart_products.filter(p => p.GEONUM !== payload.GEONUM)
      useNuxtApp().$toast.error(`${payload.ITNAME} remove to cart`);
      localStorage.setItem('cart_products', JSON.stringify(this.cart_products));
    },
    clear_cart () {
      const confirmMsg = window.confirm('Are you sure deleted your all cart items ?');
      if(confirmMsg){
        this.cart_products = [];
      }
      localStorage.setItem('cart_products', JSON.stringify(this.cart_products));
    },
    initialOrderQuantity(){
      this.orderQuantity = 1
    },
    updateCheckedCartList(cartList) {
      localStorage.setItem('checkedCartList', JSON.stringify(cartList));

      this.cartTotalCnt = 0;
      this.cartTotalAmount = 0; 
      for (let i = 0; i < cartList.length; i++) {
        this.cartTotalCnt += Number(cartList[i].QTY);

        if (typeof cartList[i].AMOUNT === 'string') { 
           this.cartTotalAmount += Number(cartList[i].AMOUNT.replaceAll(',','')) * Number(cartList[i].QTY);
        }else{ 
           this.cartTotalAmount += Number(cartList[i].AMOUNT) * Number(cartList[i].QTY);
        }
       }
       localStorage.setItem('cartTotalCnt', this.cartTotalCnt);
       localStorage.setItem('cartTotalAmount', this.cartTotalAmount);
    },
    getCheckedCartList() {
      
      if (process.client) {
          const data = localStorage.getItem('checkedCartList');
          if (data) {
            return this.checkedCartList = JSON.parse(data);
          } else {
            localStorage.setItem('checkedCartList', JSON.stringify([]));
            return this.checkedCartList = [];
          }
        }
    },
    setOrderProductNm(orderName){
      localStorage.setItem('orderProductNm', orderName);
      this.orderProductNm = orderName
    }
  },
  getters: {
    totalPriceQuantity: (state) => {
      return state.cart_products.reduce((cartTotal, cartItem) => {
        const { price, orderQuantity } = cartItem;
        if(typeof orderQuantity !== 'undefined'){
          const itemTotal = price * orderQuantity;
          cartTotal.QTY += orderQuantity;
          cartTotal.total += itemTotal;
        }
        return cartTotal;
      }, {
        total: 0,
        quantity: 0,
      })
    },
    get_cart_products:(state) => {
      if (process.client) {
        const data = localStorage.getItem('cart_products');
        if (data) {
          return state.cart_products = JSON.parse(data);
        } else {
          localStorage.setItem('cart_products', JSON.stringify([]));
          return state.cart_products = [];
        }
      } else {
        return state.cart_products;
      }
    },
    getCartTotalCnt: (state) => {
      const value = localStorage.getItem('cartTotalCnt');
      return state.cartTotalCnt !== null ? Number(value) : 0;
    },
    getCartTotalAmount: (state) => {
      const value = localStorage.getItem('cartTotalAmount');
      return state.cartTotalAmount !== null ? Number(value) : 0;
    },
    getOrderProductNm:(state)=>{
      
      localStorage.getItem('orderProductNm');
      let orderName = state.orderProductNm
      if(!orderName){
        orderName = localStorage.getItem('orderProductNm');
      }
      return orderName
    } 
  }

})