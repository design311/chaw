$(function(){
})

//images loaded
$(window).load(function() {
	if (typeof $.flexslider == 'function') { 
		$('.flexslider').flexslider({
			directionNav: false
		});
	}
});