<script setup lang="ts">
import { useAppSidebarMenuStore } from '@/stores/app-sidebar-menu';
import { useAppOptionStore } from '@/stores/app-option';
import { onMounted } from 'vue';
import { slideToggle } from '@/composables/slideToggle.js';
import { slideUp } from '@/composables/slideUp.js';
import { slideDown } from '@/composables/slideDown.js';
import TopMenuNav from '@/components/app/TopMenuNav.vue';

const appTopMenu = useAppSidebarMenuStore();
const appOption = useAppOptionStore();

function handlePageLoadMenuFocus() {
	var targetMenu = document.querySelector('.app-top-menu .menu');
	if (!targetMenu) {
		return;
	}
	var targetMenuStyle = window.getComputedStyle(targetMenu);
	var bodyStyle = window.getComputedStyle(document.body);
	var targetCss = (bodyStyle.getPropertyValue('direction') == 'rtl') ? 'margin-right' : 'margin-left';
	var marginLeft = parseInt(targetMenuStyle.getPropertyValue(targetCss));
	var viewWidth = document.querySelector('.app-top-menu').clientWidth - 128;
	var prevWidth = 0;
	var speed = 0;
	var fullWidth = 0;

	var elms = [].slice.call(document.querySelectorAll('.app-top-menu .menu > .menu-item'));
	if (elms) {
		var found = false;
		elms.map(function(elm) {
			if (!elm.classList.contains('menu-control')) {
				fullWidth += elm.clientWidth;
				if (!found) {
					prevWidth += elm.clientWidth;
				}
				if (elm.classList.contains('active')) {
					found = true;
				}
			}
		});
	}
	
	if (prevWidth >= viewWidth) {
		var finalScrollWidth = prevWidth - viewWidth + 128;
		if (bodyStyle.getPropertyValue('direction') != 'rtl') {
			targetMenu.style.marginLeft = '-' + finalScrollWidth + 'px';
		} else {
			targetMenu.style.marginRight = '-' + finalScrollWidth + 'px';
		}
	}
	
	var elm = targetMenu.querySelector('.menu-control.menu-control-end');
	if (prevWidth != fullWidth && fullWidth >= viewWidth) {
		elm.classList.add('show');
	} else {
		elm.classList.remove('show');
	}

	var elm = targetMenu.querySelector('.menu-control.menu-control-start');
	if (prevWidth >= viewWidth && fullWidth >= viewWidth) {
		elm.classList.add('show');
	} else {
		elm.classList.remove('show');
	}
};

function handleMenuButtonAction(element, direction) {
	var element = element.target;
	var obj = element.closest('.menu');
	var objStyle = window.getComputedStyle(obj);
	var bodyStyle = window.getComputedStyle(document.querySelector('body'));
	var targetCss = (bodyStyle.getPropertyValue('direction') == 'rtl') ? 'margin-right' : 'margin-left';
	var marginLeft = parseInt(objStyle.getPropertyValue(targetCss));  
	var containerWidth = document.querySelector('.app-top-menu').clientWidth - 80;
	var totalWidth = 0;
	var finalScrollWidth = 0;
	
	var elms = [].slice.call(obj.querySelectorAll('.menu-item'));
	if (elms) {
		elms.map(function(elm) {
			if (!elm.classList.contains('menu-control')) {
				totalWidth += elm.clientWidth;
			}
		});
	}

	switch (direction) {
		case 'next':
			var widthLeft = totalWidth + marginLeft - containerWidth;
			if (widthLeft <= containerWidth) {
				finalScrollWidth = widthLeft - marginLeft - 88;
				setTimeout(function() {
					obj.querySelector('.menu-control.menu-control-end').classList.remove('show');
				}, 300);
			} else {
				finalScrollWidth = containerWidth - marginLeft - 88;
			}

			if (finalScrollWidth !== 0) {
				obj.style.transitionProperty = 'height, margin, padding';
				obj.style.transitionDuration = '300ms';
				if (bodyStyle.getPropertyValue('direction') != 'rtl') {
					obj.style.marginLeft = '-' + finalScrollWidth + 'px';
				} else {
					obj.style.marginRight = '-' + finalScrollWidth + 'px';
				}
				setTimeout(function() {
					obj.style.transitionProperty = '';
					obj.style.transitionDuration = '';
					obj.querySelector('.menu-control.menu-control-start').classList.add('show');
				}, 300);
			}
			break;
		case 'prev':
			var widthLeft = -marginLeft;

			if (widthLeft <= containerWidth) {
				obj.querySelector('.menu-control.menu-control-start').classList.remove('show');
				finalScrollWidth = 0;
			} else {
				finalScrollWidth = widthLeft - containerWidth + 88;
			}
			
			obj.style.transitionProperty = 'height, margin, padding';
			obj.style.transitionDuration = '300ms';
			
			if (bodyStyle.getPropertyValue('direction') != 'rtl') {
				obj.style.marginLeft = '-' + finalScrollWidth + 'px';
			} else {
				obj.style.marginRight = '-' + finalScrollWidth + 'px';
			}
			
			setTimeout(function() {
				obj.style.transitionProperty = '';
				obj.style.transitionDuration = '';
				obj.querySelector('.menu-control.menu-control-end').classList.add('show');
			}, 300);
			break;
			break;
		}
};

function handlePageResize() {
	window.addEventListener('resize', function() {
		var targetElm = document.querySelector('.app-top-menu .menu');
		
		if (targetElm) {
			targetElm.removeAttribute('style');
		}
		if (document.body.clientWidth > 767) {
			handlePageLoadMenuFocus();
		}
	});
};

function handleTopMenuToggle(menus, forMobile = false) {
	menus.map(function(menu) {
		menu.onclick = function(e) {
			e.preventDefault();
			
			if (!forMobile || (forMobile && document.body.clientWidth < 768)) {
				var target = this.nextElementSibling;
				menus.map(function(m) {
					var otherTarget = m.nextElementSibling;
					if (otherTarget !== target) {
						slideUp(otherTarget);
						otherTarget.closest('.menu-item').classList.remove('expand');
						otherTarget.closest('.menu-item').classList.add('closed');
					}
				});
			
				slideToggle(target);
			}
		}
	});
};

function handleTopMenuSubMenu() {
	"use strict";
	
	var menuBaseSelector = '.app-top-menu .menu > .menu-item.has-sub';
	var submenuBaseSelector = ' > .menu-submenu > .menu-item.has-sub';
	
	var menuLinkSelector =  menuBaseSelector + ' > .menu-link';
	var menus = [].slice.call(document.querySelectorAll(menuLinkSelector));
	handleTopMenuToggle(menus, true);
	
	var submenuLvl1Selector = menuBaseSelector + submenuBaseSelector;
	var submenusLvl1 = [].slice.call(document.querySelectorAll(submenuLvl1Selector + ' > .menu-link'));
	handleTopMenuToggle(submenusLvl1);
	
	var submenuLvl2Selector = menuBaseSelector + submenuBaseSelector + submenuBaseSelector;
	var submenusLvl2 = [].slice.call(document.querySelectorAll(submenuLvl2Selector + ' > .menu-link'));
	handleTopMenuToggle(submenusLvl2);
	
	window.addEventListener('resize', function() {
		var elms = [].slice.call(document.querySelectorAll('.app-top-menu .menu-submenu'));
		elms.map(function(elm) {
			elm.removeAttribute('style');
		});
	});
};

onMounted(() => {
	handlePageResize();
	handlePageLoadMenuFocus();
	handleTopMenuSubMenu();
});
</script>
<template>
	<div id="top-menu" class="app-top-menu">
		<div class="menu">
			<template v-for="menu in appTopMenu">
				<top-menu-nav v-if="menu.title" v-bind:menu="menu"></top-menu-nav>
			</template>
			
			<div class="menu-item menu-control menu-control-start">
				<a href="javascript:;" class="menu-link" v-on:click="handleMenuButtonAction($event, 'prev')"><i class="fa fa-angle-left"></i></a>
			</div>
			<div class="menu-item menu-control menu-control-end">
				<a href="javascript:;" class="menu-link" v-on:click="handleMenuButtonAction($event, 'next')"><i class="fa fa-angle-right"></i></a>
			</div>
		</div>
	</div>
</template>