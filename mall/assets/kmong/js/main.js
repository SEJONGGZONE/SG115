// header navbox button 
function m_menu_ope() {
	$("body").addClass("hidden");
	$(".header").addClass("open");
	$(".nav_box").addClass("open");
}

function m_menu_clo() {
	$("body").removeClass("hidden");
	$(".header").removeClass("open");
	$(".nav_box").removeClass("open");
}


const bWidth = window.innerWidth;

$(document).ready(function () {

	if (bWidth > 1023) { // PC


	} else { // Tablet && Mobile  

	}

	$(document).mouseup(function (e) {

		if ($(".nav_box").has(e.target).length === 0) {
			$("body").removeClass("hidden");
			$(".header").removeClass("open");
			$(".nav_box").removeClass("open");
		}

	});

});



// accordion function
(function ($) {

	const lnbUI = {
		click: function (target, speed) {
			let _self = this,
				$target = $(target);
			_self.speed = speed || 400;

			$target.each(function () {
				if (findChildren($(this))) {
					return;
				}
				$(this).addClass('noDepth');
			});

			function findChildren(obj) {
				return obj.find('> ul').length > 0;
			}

			$target.on('click', '.acc_tit', function (e) {
				e.stopPropagation();
				let $this = $(this),
					$depthTarget = $this.next(),
					$siblings = $this.parent().siblings();

				$this.parent('li').find('ul li').removeClass('show');
				$siblings.removeClass('show');
				// $siblings.find('ul').slideUp(400);

				if ($depthTarget.css('display') == 'none') {
					_self.activeOn($this);
					// $depthTarget.slideDown(_self.speed);
				} else {
					// $depthTarget.slideUp(_self.speed);
					_self.activeOff($this);
				}

			})

		},
		activeOff: function ($target) {
			$target.parent().removeClass('show');
		},
		activeOn: function ($target) {
			$target.parent().addClass('show');
		},

	};

	// navbox
	$(function () {
		lnbUI.click('#nav li', 400)
	});

}(jQuery));



const slide_banner = new Swiper(".slide_banner", {

	loop: true,
	grabCursor: true,

	// autoplay: {
	// 	delay: 5000,
	// 	disableOnInteraction: false,
	// },

	navigation: {
		nextEl: ".slide_banner .next_btn",
		prevEl: ".slide_banner .prev_btn",
	},
});


const recent_slider = new Swiper(".recent_slider", {

	loop: true,
	grabCursor: true,
	effect: 'fade',

	// autoplay: {
	// 	delay: 5000,
	// 	disableOnInteraction: false,
	// },

	navigation: {
		nextEl: ".recent_slider .next_btn",
		prevEl: ".recent_slider .prev_btn",
	},
});


const mob_topmenu = new Swiper(".mob_topmenu", {

	slidesPerView: 'auto',
	freeMode: true,

});