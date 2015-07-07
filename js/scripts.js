//jQuery won't conflict with other libraries that might use $
jQuery.noConflict();

jQuery(function($){
	//jQuery Document Ready

	// make blurtbox hideable
	$('.spitio_box').append('<div class="closespitio">&#8855;</div>');
	$('.closespitio').on('click', function(){
		$('.spitio_box').hide();
		});












});

