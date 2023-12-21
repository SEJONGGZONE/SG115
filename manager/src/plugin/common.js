
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


        // --------------------------------------------------------------------------------
        // 공통함수, 좌우 영역 드래그
        // --------------------------------------------------------------------------------
        // 대상 Element 선택
        let resizer = "";
        let leftSide = "";
        let rightSide = "";

        // 마우스의 위치값 저장을 위해 선언
        let x = 0;
        let y = 0;

        // 크기 조절시 왼쪽 Element를 기준으로 삼기 위해 선언
        let leftWidth = 0;

        // resizer에 마우스 이벤트가 발생하면 실행하는 Handler
        Vue.config.globalProperties.mouseDownHandlerForDrag = (e) => {
            // 대상 Element 선택
            resizer = document.getElementById("dragMe");
            leftSide = resizer.previousElementSibling;
            rightSide = resizer.nextElementSibling;

            // 마우스 위치값을 가져와 x, y에 할당
            x = e.clientX;
            y = e.clientY;
            // left Element에 Viewport 상 width 값을 가져와 넣음
            leftWidth = leftSide.getBoundingClientRect().width;

            // 마우스 이동과 해제 이벤트를 등록
            document.addEventListener("mousemove", mouseMoveHandler);
            document.addEventListener("mouseup", mouseUpHandler);
        };

        const mouseMoveHandler = (e) => {
        // 마우스가 움직이면 기존 초기 마우스 위치에서 현재 위치값과의 차이를 계산
        const dx = e.clientX - x;
        const dy = e.clientY - y;

        // 크기 조절 중 마우스 커서를 변경함
        // class="resizer"에 적용하면 위치가 변경되면서 커서가 해제되기 때문에 body에 적용
        document.body.style.cursor = "col-resize";

        // 이동 중 양쪽 영역(왼쪽, 오른쪽)에서 마우스 이벤트와 텍스트 선택을 방지하기 위해 추가
        leftSide.style.userSelect = "none";
        leftSide.style.pointerEvents = "none";

        rightSide.style.userSelect = "none";
        rightSide.style.pointerEvents = "none";

        // 초기 width 값과 마우스 드래그 거리를 더한 뒤 상위요소(container)의 너비를 이용해 퍼센티지를 구함
        // 계산된 퍼센티지는 새롭게 left의 width로 적용
        const newLeftWidth =
            ((leftWidth + dx) * 100) / resizer.parentNode.getBoundingClientRect().width;
        leftSide.style.width = `${newLeftWidth}%`;
        };

        const mouseUpHandler = () => {
        // 모든 커서 관련 사항은 마우스 이동이 끝나면 제거됨
        resizer.style.removeProperty("cursor");
        document.body.style.removeProperty("cursor");

        leftSide.style.removeProperty("user-select");
        leftSide.style.removeProperty("pointer-events");

        rightSide.style.removeProperty("user-select");
        rightSide.style.removeProperty("pointer-events");

        // 등록한 마우스 이벤트를 제거
        document.removeEventListener("mousemove", mouseMoveHandler);
        document.removeEventListener("mouseup", mouseUpHandler);
        };
    }
}