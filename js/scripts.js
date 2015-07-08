//jQuery won't conflict with other libraries that might use $
jQuery.noConflict();

jQuery(function($){
	//jQuery Document Ready

	// make blurtbox hideable
	$('.closespitio').on('click', function(){
		$('.spitio_box').hide();
		});

	$('#spitio_badge').on('click', function(){
		$('#spitio_box').toggleClass('closed');
		});












});

