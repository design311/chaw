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

	$('.detailheader .title').click(function(e){
		if (e.target.tagName != 'A') {		
			$('html,body').animate({
				scrollTop: headerHeight
			}, 1000);
		};
	})

	$('.ajax-button').click(function(){
		if (body.hasClass('loggedin')) {

			var link = $(this);
			
			$.getJSON( $(this).data('ajax'), function( data ) {
				if (data.status === 1) {
					link.find('.sprite').addClass('orange');
				}
				else{
					link.find('.sprite').removeClass('orange');
				}

				if (link.hasClass('shop')) {
					$('.shoppinglistcount').text(data.shoppinglistcount);
				};
			});
		}
		else{
			window.location = loginpage;
		}
		
		return false;
	})

	if (typeof $('body').select2 == 'function') { 
		$("#searchRecipe_ingredients").select2();
	}

	if (typeof $('body').slider == 'function') {
		$( "#slider-range" ).slider({
	      range: "min",
	      min: 0,
	      max: 50,
	      value: 50,
	      slide: function( event, ui ) {
	        $( ".amount" ).text( ui.value );
	      },
	      stop: function( event, ui ) {
	        $( ".amount" ).val( ui.value ).change(); //call change event for input
	      }
	    });
	    $( ".amount" ).val( $( "#slider-range" ).slider( "value" ) );
	    $( ".amount" ).text( $( "#slider-range" ).slider( "value" ) );
	}

	$('.tooltip').tipsy({gravity: $.fn.tipsy.autoNS, live: true, html: true});

	$('.flash').addClass('visible');
	$('.flash-close').click(function(){
		$('.flash').removeClass('visible');
		return false;
	})

	$('#markdown-toggle').click(function(){
		$('.markdown').slideToggle();
		return false;
	})

	$('.showmore-toggle').click(function(){
		var toggle = $(this);
		$(this).parent().siblings('.showmore').slideToggle(function(){
			if ($(this).is(':visible')) {
				toggle.children('i').removeClass('sort-desc').addClass('sort-asc');
				toggle.children('span').text('Minder recepten weergeven');
			}
			else{
				toggle.children('i').removeClass('sort-asc').addClass('sort-desc');
				toggle.children('span').text('Meer recepten weergeven');
			}
		});
		return false;
	})

	var mapHeight = $('.mapcontainer').css('height');
	$('.resize').click(function(){
		
		$('.mapcontainer').toggleClass('fullscreen');
		if ($('.mapcontainer').hasClass('fullscreen')) {
			$(this).text('Kaart verkleinen');
			$('.mapcontainer').animate({
				height:'100%'
			}, 1000);
		}
		else{
			$(this).text('Kaart vergroten');
			$('.mapcontainer').animate({
				height:mapHeight
			}, 1000);
		}
		setTimeout(function(){
			google.maps.event.trigger(map, "resize");
		},1000)
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