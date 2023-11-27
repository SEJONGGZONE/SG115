import { getCurrentInstance } from 'vue'

export function useAlert () {
    const vm = getCurrentInstance()  
    return {
		showAlert : vm.appContext.config.globalProperties.showAlert,
		showAlertSuccess : vm.appContext.config.globalProperties.showAlertSuccess,
		showConfirm : vm.appContext.config.globalProperties.showConfirm
	}
}