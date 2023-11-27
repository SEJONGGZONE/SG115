
export default{
    install(Vue){  
        Vue.config.globalProperties.showAlert = function(msg,option){
 
            let timger = option?.time ? option?.time : 1000
            let positionStr = option?.position ? option?.position : 'center'
            let iconStr = option?.icon ? option?.icon : 'info'
            let isNotuseTimer = option?.isNotuseTimer ?? true
            let isShowConfirmButton = !isNotuseTimer ? true : false

            let alertOption = {
                position:positionStr,// 'top', 'top-start', 'top-end', 'center', 'center-start', 'center-end', 'bottom', 'bottom-start', 'bottom-end'.
                icon: iconStr,//warning, error, success, info, question
                title: msg,
                showConfirmButton: isShowConfirmButton, // true or false
            }
            if(isNotuseTimer ){
                alertOption.timer = timger
            }
            Vue._context.provides.$swal.fire(alertOption).then(()=>{
                if(option?.callback){
                    option?.callback()
                }
            })

        } 
 
         Vue.config.globalProperties.showAlertSuccess = function(msg,option){
 
            let timger = option?.time ? option?.time : 1000
            let positionStr = option?.position ? option?.position : 'center'
            let iconStr = option?.icon ? option?.icon : 'info'
            let isNotuseTimer = option?.isNotuseTimer ?? true
            let isShowConfirmButton = !isNotuseTimer ? true : false

            let alertOption = {
                position:positionStr,// 'top', 'top-start', 'top-end', 'center', 'center-start', 'center-end', 'bottom', 'bottom-start', 'bottom-end'.
                icon: 'success',//warning, error, success, info, question
                title: msg,
                showConfirmButton: isShowConfirmButton, // true or false
            }
            if(isNotuseTimer ){
                alertOption.timer = timger
            }
            Vue._context.provides.$swal.fire(alertOption).then(()=>{
                if(option?.callback){
                    option?.callback()
                }
            })

        } 
 
         Vue.config.globalProperties.showConfirm = function(title,msg){

            return new Promise(function(resolve, reject) { 
                let alertOption = {
                    title: title,
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '확인' ,
                    cancelButtonText: '취소',
                } 
                Vue._context.provides.$swal.fire(alertOption).then((result) => {
                    resolve(result);
                })
            })
  

            

        } 
    }
}