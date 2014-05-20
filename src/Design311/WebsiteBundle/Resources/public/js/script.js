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

	$('.ajax-button').click(function(){
		if (body.hasClass('loggedin')) {

			var link = $(this);

			//TODO add loader gif for slow json
			$.getJSON( $(this).data('ajax'), function( data ) {
				if (data.status === 1) {
					link.children('i').addClass('active');
				}
				else{
					link.children('i').removeClass('active');
				}

				if (link.hasClass('shop')) {
					$('.shoppinglistcount').text(data.shoppinglistcount);
				};
			});
		}
		else{
			window.location = '/login/auth/';
		}
		
		return false;
	})

	if (typeof $('body').select2 == 'function') { 
		$("#searchRecipe_ingredients").select2();
	}

	$( "#slider-range" ).slider({
      range: "min",
      min: 0,
      max: 50,
      value: 50,
      slide: function( event, ui ) {
        $( ".amount" ).val( ui.value );
        $( ".amount" ).text( ui.value );
      }
    });
    $( ".amount" ).val( $( "#slider-range" ).slider( "value" ) );
    $( ".amount" ).text( $( "#slider-range" ).slider( "value" ) );

})

//images loaded
$(window).load(function() {
	if (typeof $.flexslider == 'function') { 
		$('.flexslider').flexslider({
			directionNav: false
		});
	}
});