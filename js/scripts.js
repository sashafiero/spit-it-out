//jQuery won't conflict with other libraries that might use $
jQuery.noConflict();

jQuery(function($){
	//jQuery Document Ready

	// make blurtbox hideable
	$('.blurtbox').append('<div class="closeblurtbox">( X )</div>');
	$('.closeblurtbox').on('click', function(){
		$('.blurtbox').hide();
		});












});

