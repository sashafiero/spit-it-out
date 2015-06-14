//jQuery won't conflict with other libraries that might use $
jQuery.noConflict();

jQuery(function($){
	//jQuery Document Ready

	// make blurtbox hideable
	$('.spitio_box').append('<div class="closespitio">( X )</div>');
	$('.closespitio').on('click', function(){
		$('.spitio_box').hide();
		});












});

