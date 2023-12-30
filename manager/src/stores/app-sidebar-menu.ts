import { defineStore } from "pinia";

export const useAppSidebarMenuStore = defineStore({
  id: "appSidebarMenu",
  state: () => {
		return [
			// ----------------------------------------------------------------------------------------
			// 신규적용(2023.11.27) - 성창푸드
			// ----------------------------------------------------------------------------------------
			// {
			// 	url: '/member/memberMng', icon: 'fa fa-user-group', title: '사용자관리',
			// 	children: [
			// 		// { url: '/client/clientMng',title: '거래처관리',  },
			// 		// { url: '/client/centerMng',title: '센터관리',  },
			// 		// { url: '/client/centerRouteMng',title: '센터알림',  },
			// 		// { url: '/client/centerAlarmAny',title: '센터도착(외부)',  },
			// 		// { url: '/map/maplist',title: '거래처 위치관리',  },
			// 		 { url: '/member/memberMng', title: '사용자관리' },
			// 		//{ url: '/member/memberMngNew', title: '회원관리(GRID)' },
			// 		// { url: '/member/account', title: '거래처수기결제' },
			// 		//{ url: '/member/accountV2', title: '사용 안함(window.open VER)' },
			// 	]
			// },
			// {
			// 	url: '/product/productManagement', icon: 'fa fa-layer-group', title: '상품관리',
			// 	children: [
			// 		{ url: '/product/productManagement', title: '상품조회/수정' },
			// 		//{ url: '/product/imageView', title: '이미지 편집' },
			// 	]
			// },
			// {
			// 	url: '/product/productManagement', icon: 'fa fa-layer-group', title: '기획 관리',
			// 	children: [
			// 		{ url: '/product/eventProductList', title: '기획관리' },
			// 		{ url: '/product/eventProductItem', title: '기획상품관리' },
			// 		// { url: '/product/eventProductManagement', title: '(X)기획상품관리' },
			// 	]
			// },
			
			// ----------------------------------------------------------------------------------------
			// 신규적용(2023.11.27) - 성창푸드
			// ----------------------------------------------------------------------------------------
			{
				url: '/member/memberMngSungChang', icon: 'fa fa-user-group', title: '사용자관리',
				children: [
					{ url: '/member/memberMngSungChang', title: '사용자관리' },
				]
			},
			{
				url: '/product/productManagementSungChang', icon: 'fa fa-cart-shopping', title: '상품관리',
				children: [
					{ url: '/product/productManagementSungChang', title: '상품조회/수정'},
					//{ url: '/product/imageView', title: '이미지 편집' },
					{ url: '/product/TImageEditor', title: '이미지 편집기' },
				]
			},
			{
				url: '/product/eventProductList', icon: 'fa fa-calendar-check', title: '기획관리',
				children: [
					{ url: '/product/eventProductList', title: '기획관리' },
					{ url: '/product/eventProductItem', title: '기획상품관리' },
				]
			},
			{
				url: '/operate/operateManagement', icon: 'fa fa-sliders', title: '운영관리',
				children: [
					{ url: '/operate/operateManagement', title: '공지사항/팝업' },
					// { url: '/operate/eventManagement', title: '이벤트/알림' },
					{ url: '/operate/commonCodeManagement', title: '공통코드관리' },
				]
			},

			// ----------------------------------------------------------------------------------------
			// 신규적용(2023.10.17) - 아이사랑
			// ----------------------------------------------------------------------------------------
			
			// {
			// 	url: '/client/clientMng', icon: 'fa fa-user-group', title: '거래처관리',
			// 	children: [
			// 		{ url: '/client/clientMng',title: '거래처관리',  },
			// 		{ url: '/client/centerMng',title: '센터관리',  },
			// 		{ url: '/client/centerRouteMng',title: '센터알림',  },
			// 		{ url: '/client/centerAlarmAny',title: '센터도착(외부)',  },
			// 		// { url: '/map/maplist',title: '거래처 위치관리',  },
			// 		// { url: '/member/memberMng', title: '회원관리' },
			// 		//{ url: '/member/memberMngNew', title: '회원관리(GRID)' },
			// 		// { url: '/member/account', title: '거래처수기결제' },
			// 		//{ url: '/member/accountV2', title: '사용 안함(window.open VER)' },
			// 	]
			// },
			// {
			// 	url: '/product/productManagement', icon: 'fa fa-layer-group', title: '상품관리',
			// 	children: [
			// 		{ url: '/product/productManagement', title: '상품조회/수정' },
			// 		//{ url: '/product/imageView', title: '이미지 편집' },
			// 		{ url: '/product/eventProductList', title: '기획관리' },
			// 		{ url: '/product/eventProductItem', title: '기획상품관리' },
			// 		// { url: '/product/eventProductManagement', title: '(X)기획상품관리' },
			// 	]
			// },
			// {
			// 	url: '/order/orderManagement', icon: 'fa fa-basket-shopping', title: '주문관리',
			// 	children: [
			// 		{ url: '/order/orderManagement', title: '주문조회/확정' },
			// 		{ url: '/order/samplePage', title: '그리드 샘플' },
			// 	]
			// },
			// {
			// 	url:  '/ui/modal-notification', icon: 'fa fa-calculator', title: '정산관리',
			// 	children: [
			// 		{ url: '/ui/modal-notification', title: '정산현황(GEO)' },
			// 	]
			// },
			// {
			// 	url: '/operate/operateManagement', icon: 'fa fa-comments', title: '운영관리',
			// 	children: [
			// 		{ url: '/operate/operateManagement', title: '공지사항/팝업' },
			// 		{ url: '/operate/eventManagement', title: '이벤트/알림' },
			// 		{ url: '/operate/commonCodeManagement', title: '공통코드관리' },
			// 	]
			// },
			// {
			// 	url: '/map/nowposition', icon: 'fa fa-map', title: '위치관제',
			// 	children: [
			// 		{ url: '/map/nowposition', title: '차량현위치',  },
			// 		{ url: '/map/deliveryImage', title: '배송사진',  },
			// 		{ url: '/map/deliveryImageAny', title: '배송사진(외부)',  },
			// 	]
			// }, 
			// ,
			// ----------------------------------------------------------------------------------------
			// 템플릿 예제 
			// ----------------------------------------------------------------------------------------
			// 	,
			// 	{
			// 		url: '/dashboard', icon: 'fa fa-sitemap', title: 'Dashboard',
			// 		children: [
			// 			{ url: '/dashboard/v1', title: 'Dashboard v1' },
			// 			{ url: '/dashboard/v2', title: 'Dashboard v2' },
			// 			{ url: '/dashboard/v3', title: 'Dashboard v3' }
			// 		]
			// 	},
			// 	{ url: '/email', icon: 'fa fa-hdd', title: 'Email', badge: '10',
			// 		children: [
			// 			{ url: '/email/inbox', title: 'Inbox' },
			// 			{ url: '/email/compose', title: 'Compose' },
			// 			{ url: '/email/detail', title: 'Detail' }
			// 		]
			// 	},
			// 	{ url: '/widgets', icon: 'fab fa-simplybuilt', title: 'Widgets', label: 'NEW' },
			// 	{ url: '/ui', icon: 'fa fa-gem', title: 'UI Elements', label: 'NEW',
			// 		children: [
			// 			{ url: '/ui/general', title: 'General', highlight: true },
			// 			{ url: '/ui/typography', title: 'Typograhy' },
			// 			{ url: '/ui/tabs-accordions', title: 'Tabs & Accordions' },
			// 			{ url: '/ui/modal-notification', title: 'Modal & Notification' },
			// 			{ url: '/ui/widget-boxes', title: 'Widget Boxes' },
			// 			{ url: '/ui/media-object', title: 'Media Object' },
			// 			{ url: '/ui/buttons', title: 'Buttons', highlight: true },
			// 			{ url: '/ui/icon-fontawesome', title: 'FontAwesome' },
			// 			{ url: '/ui/icon-bootstrap-icons', title: 'Bootstrap Icons' },
			// 			{ url: '/ui/icon-simple-line-icons', title: 'Simple Line Icons' },
			// 			{ url: '/ui/language-bar-icon', title: 'Language Bar & Icon' },
			// 			{ url: '/ui/social-buttons', title: 'Social Buttons' }
			// 		]
			// 	},
			// 	{ url: '/bootstrap-5', img: '/assets/img/logo/logo-bs5.png', title: 'Bootstrap 5', label: 'NEW' },
			// 	{ url: '/form', icon: 'fa fa-list-ol', title: 'Form Stuff', label: 'NEW',
			// 		children: [
			// 			{ url: '/form/elements', title: 'Form Elements', highlight: true },
			// 			{ url: '/form/plugins', title: 'Form Plugins', highlight: true },
			// 			{ url: '/form/wizards', title: 'Form Wizards', highlight: true }
			// 		]
			// 	},
			// 	{ url: '/table', icon: 'fa fa-table', title: 'Tables',
			// 		children: [
			// 			{ url: '/table/elements', title: 'Table Elements' },
			// 			{ url: '/table/plugins', title: 'Table Plugins' }
			// 		]
			// 	},
			// 	{ url: '/pos', icon: 'fa fa-cash-register', title: 'POS System', label: 'NEW',
			// 		children: [
			// 			{ url: '/pos/customer-order', title: 'Customer Order' },
			// 			{ url: '/pos/counter-checkout', title: 'Counter Checkout' },
			// 			{ url: '/pos/kitchen-order', title: 'Kitchen Order' },
			// 			{ url: '/pos/table-booking', title: 'Table Booking' },
			// 			{ url: '/pos/menu-stock', title: 'Menu Stock' }
			// 		]
			// 	},
			// 	{ url: '/chart', icon: 'fa fa-chart-pie', title: 'Chart', label: 'NEW',
			// 		children: [
			// 			{ url: '/chart/js', title: 'Chart JS' },
			// 			{ url: '/chart/apex', title: 'Apex Chart' }
			// 		]
			// 	},
			// 	{ url: '/calendar', icon: 'fa fa-calendar', title: 'Calendar' },
			// 	{ url: '/map', icon: 'fa fa-map', title: 'Map' },
			// 	{ url: '/gallery', icon: 'fa fa-image', title: 'Gallery' },
			// 	{ url: '/page-option', icon: 'fa fa-cogs', title: 'Page Options', label: 'NEW',
			// 		children: [
			// 			{ url: '/page-option/blank', title: 'Blank Page' },
			// 			{ url: '/page-option/with-footer', title: 'Page with Footer' },
			// 			{ url: '/page-option/with-fixed-footer', title: 'Page with Fixed Footer', highlight: true },
			// 			{ url: '/page-option/without-sidebar', title: 'Page without Sidebar' },
			// 			{ url: '/page-option/with-right-sidebar', title: 'Page with Right Sidebar' },
			// 			{ url: '/page-option/with-minified-sidebar', title: 'Page with Minified Sidebar' },
			// 			{ url: '/page-option/with-two-sidebar', title: 'Page with Two Sidebar' },
			// 			{ url: '/page-option/full-height', title: 'Full Height Content' },
			// 			{ url: '/page-option/with-wide-sidebar', title: 'Page with Wide Sidebar' },
			// 			{ url: '/page-option/with-light-sidebar', title: 'Page with Light Sidebar' },
			// 			{ url: '/page-option/with-mega-menu', title: 'Page with Mega Menu' },
			// 			{ url: '/page-option/with-top-menu', title: 'Page with Top Menu' },
			// 			{ url: '/page-option/with-boxed-layout', title: 'Page with Boxed Layout' },
			// 			{ url: '/page-option/with-mixed-menu', title: 'Page with Mixed Menu' },
			// 			{ url: '/page-option/boxed-layout-with-mixed-menu', title: 'Boxed Layout with Mixed Menu' },
			// 			{ url: '/page-option/with-transparent-sidebar', title: 'Page with Transparent Sidebar' },
			// 			{ url: '/page-option/with-search-sidebar', title: 'Page with Search Sidebar', highlight: true },
			// 			{ url: '/page-option/with-hover-sidebar', title: 'Page with Hover Sidebar', highlight: true },
			// 		]
			// 	},
			// 	{ url: '/extra', icon: 'fa fa-gift', title: 'Extra', label: 'NEW',
			// 		children: [
			// 			{ url: '/extra/timeline', title: 'Timeline' },
			// 			{ url: '/extra/coming-soon', title: 'Coming Soon Page' },
			// 			{ url: '/extra/search', title: 'Search Results' },
			// 			{ url: '/extra/invoice', title: 'Invoice' },
			// 			{ url: '/extra/error', title: '404 Error Page' },
			// 			{ url: '/extra/profile', title: 'Profile Page' },
			// 			{ url: '/extra/scrum-board', title: 'Scrum Board', highlight: true },
			// 			{ url: '/extra/cookie-acceptance-banner', title: 'Cookie Acceptance Banner', highlight: true },
			// 			{ url: '/extra/orders', title: 'Orders', highlight: true },
			// 			{ url: '/extra/order-details', title: 'Order Details', highlight: true },
			// 			{ url: '/extra/products', title: 'Products', highlight: true },
			// 			{ url: '/extra/product-details', title: 'Product Details', highlight: true },
			// 		]
			// 	},
			// 	{ url: '/user', icon: 'fa fa-key', title: 'Login & Register',
			// 		children: [
			// 			{ url: '/user/login-v1', title: 'Login' },
			// 			{ url: '/user/login-v2', title: 'Login v2' },
			// 			{ url: '/user/login-v3', title: 'Login v3' },
			// 			{ url: '/user/register-v3', title: 'Register v3' }
			// 		]
			// 	},
			// 	{ url: '/helper', icon: 'fa fa-medkit', title: 'Helper',
			// 		children: [
			// 			{ url: '/helper/css', title: 'Predefined CSS Classes' }
			// 		]
			// 	},
			// 	{ url: '/menu', icon: 'fa fa-align-left', title: 'Menu Level',
			// 		children: [
			// 			{ url: '/menu/menu-1-1', title: 'Menu 1.1',
			// 				children: [
			// 					{ url: '/menu/menu-1-1/menu-2-1', title: 'Menu 2.1',
			// 						children: [
			// 							{ url: '/menu/menu-1-1/menu-2-1/menu-3-1', title: 'Menu 3.1' },
			// 							{ url: '/menu/menu-1-1/menu-2-1/menu-3-2', title: 'Menu 3.2' }
			// 						]
			// 					},
			// 					{ url: '/menu/menu-1-1/menu-2-2', title: 'Menu 2.2' },
			// 					{ url: '/menu/menu-1-1/menu-2-3', title: 'Menu 2.3' },
			// 				]
			// 			},
			// 			{ url: '/menu/menu-1-2', title: 'Menu 1.2' },
			// 			{ url: '/menu/menu-1-3', title: 'Menu 1.3' },
			// 		]
			// 	}
			//
]
  }
});