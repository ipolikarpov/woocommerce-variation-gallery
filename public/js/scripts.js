jQuery(document).ready(function($) {
	if (
			($('.product > .summary > .variations_form').length > 0)
			&&
			($('.product > .summary > .variations_form').find('input.variation_id') != 'undefined')
		) {
		var variation_form = $('.product > .summary > .variations_form');
		variation_form.on( 'woocommerce_variation_has_changed', function( event, variation ){
	    	//variation changed
	    	//disable default main image change by wc
	    	variation_form.wc_variations_image_update(false);
	    	var variation = variation_form.find('input.variation_id').eq(0).val();
	    	//define ajax request for gallery full rebuild
	    	// var imagesHeight = $('.product').eq(0).height();
	    	// $('.product').eq(0).css('height', imagesHeight);
	    	var ajaxData = {
	    		action: 'wvg_change_images',
	    		variation: variation
	    	}
	    	var gallery = $('.product').find('.woocommerce-product-gallery').eq(0);
	    	gallery.animate({
	    		opacity : 0.1
	    	}, 100);
			$.post(wvg_ajax.ajax_url, ajaxData, function(result) {
	    		if (result != 'error') {
	    			variation_form.trigger('woocommerce_before_variation_has_changed');
    				gallery.replaceWith(result);
    				gallery = $('.product').find('.woocommerce-product-gallery').eq(0);
    				gallery.wc_product_gallery();
    				variation_form.trigger('woocommerce_after_variation_has_changed');
	    		} else {
	    			console.log('Error changin product variation gallery');
    				// variation_form.trigger('woocommerce_after_variation_has_changed');
	    		}
	    	});
	    	// $('.product').eq(0).css('height', 'auto');
		});

		variation_form.on('wc_variations_image_update');
	}
});