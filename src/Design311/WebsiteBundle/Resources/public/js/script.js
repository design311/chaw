$(function(){

	var header = $('.detailheader');
	var headerTitle = $('.detailheader').children('.title');
	var headerHeight = header.outerHeight() - headerTitle.outerHeight();
	var body = $('body');

	$(document).scroll(function(){

		headerHeight = header.outerHeight() - headerTitle.outerHeight();
		var height = $(document).scrollTop();

		if (height>headerHeight) {
			body.addClass('fixed');
		} else {
			body.removeClass('fixed');
		};
	});

	$('.detailheader .title').click(function(){
		$('html,body').animate({
			scrollTop: headerHeight
		}, 1000);
	})

	if (typeof $('body').select2 == 'function') { 
		$("#searchRecipe_ingredients").select2();
	}

})

//images loaded
$(window).load(function() {
	if (typeof $.flexslider == 'function') { 
		$('.flexslider').flexslider({
			directionNav: false
		});
	}
});