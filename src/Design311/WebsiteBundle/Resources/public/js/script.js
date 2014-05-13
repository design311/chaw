$(function(){

	var header = $('.detailheader');
	var body = $('body');
	var headerHeight = header.outerHeight() - header.children('.title').outerHeight();

	$(document).scroll(function(){

		var height = $(document).scrollTop();

		if (height>headerHeight) {
			body.addClass('fixed');
		} else {
			body.removeClass('fixed');
		};
	});

	$('.detailheader .title').one('click', function(){
		$(this).addClass('clicked');
		$('html,body').animate({
			scrollTop: headerHeight
		}, 1000);
	})

})

//images loaded
$(window).load(function() {
	if (typeof $.flexslider == 'function') { 
		$('.flexslider').flexslider({
			directionNav: false
		});
	}
});