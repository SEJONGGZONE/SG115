import { defineStore } from 'pinia'; 
export const useModalStore = defineStore('modal', {
  state: () => ({
    isShowCartModal: false, 
    isEventModal: false,
  })
})