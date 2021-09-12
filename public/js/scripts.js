jQuery(document).ready(function($) {
	if (
			($('.product > .summary > .variations_form').length > 0)
			&&
			($('.product > .summary > .variations_form').find('input.variation_id') != 'undefined')
		) {
		var variation_form = $('.product > .summary > .variations_form');
		variation_form.on( 'woocommerce_variation_has_changed', function( event ){
	    	variation_form.wc_variations_image_update(false);
	    	variation_form.trigger('woocommerce_before_variation_has_changed');

	    	var variation_id = variation_form.find('input.variation_id').eq(0).val();
	    	var variations_data = variation_form.data('product_variations');
	    	var variation_data = variations_data.filter(variation => variation.variation_id == variation_id);

	    	

	    	if (variation_data.length == 1) {
	    		variation_data = variation_data[0];
	    		var gallery_data = variation_data.gallery; // each element of gallery_data stores html
	    		var gallery = $('.product').find('.woocommerce-product-gallery').eq(0);
	    		gallery.animate({
		    		opacity : 0.1
		    	}, 100);
	    		gallery.replaceWith(gallery_data);
				gallery = $('.product').find('.woocommerce-product-gallery').eq(0);
				gallery.wc_product_gallery();

	    	}

	    	variation_form.trigger('woocommerce_after_variation_has_changed');

		});

		variation_form.on('wc_variations_image_update');
	}
});