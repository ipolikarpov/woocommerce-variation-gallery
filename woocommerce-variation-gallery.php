<?php
/*
Plugin Name: Woocommerce Varition Gallery
Author: Ivan Polikarpov
Text Domain: woocommerce-variation-gallery
Domain Path: /languages
*/


add_action( 'init', 'wvg_add_custom_fields' );
register_activation_hook( __FILE__, 'wvg_add_custom_fields' );

if ( !function_exists( 'wvg_add_custom_fields' ) ) {
	function wvg_add_custom_fields() {
		if (is_plugin_active( 'woocommerce/woocommerce.php' )) {
			add_action( 'woocommerce_product_after_variable_attributes', 'wvg_variation_settings_fields', 10, 3 );
			if (is_admin()) {
				wp_register_script('wvg_admin_scripts', plugins_url('/admin/js/scripts.js', __FILE__), array('jquery'), NULL);
				wp_enqueue_script('wvg_admin_scripts');
				wp_register_style('wvg_admin_css', plugins_url('/admin/css/style.css', __FILE__), NULL, NULL);
				wp_enqueue_style('wvg_admin_css');
			} else {
				wp_register_script('wvg_public_scripts', plugins_url('/public/js/scripts.js', __FILE__), array('jquery'), NULL);
				wp_enqueue_script('wvg_public_scripts');
				wp_localize_script( 'wvg_public_scripts', 'wvg_ajax',
            		array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
			}
		} else {
			add_action( 'admin_notices', 'wvg_wc_needed_notice' );
			return false;
		}
	}
}

if ( !function_exists('wvg_wc_needed_notice') ) {
	function wvg_wc_needed_notice() {
		?>
    	<div class="notice notice-error is-dismissible">
        	<p><?php _e( 'It looks like WooCommerce is not installed or activated!', 'woocommerce-variation-gallery' ); ?></p>
    	</div>
    	<?php
	}
}

if ( !function_exists('wvg_variation_settings_fields') ) {
	function wvg_variation_settings_fields($loop, $variation_data, $variation) {
		echo '<div class="options_group wvg-gallery">';
		woocommerce_wp_hidden_input( 
			array( 
				'id'          => '_wvg_gallery[' . $variation->ID . ']',
				'name' => '_wvg_gallery[' . $variation->ID . ']',
				'value'       => get_post_meta( $variation->ID, '_wvg_gallery', true )
			)
		);
		echo '<ul class="wvg-gallery-images">';
		$images = get_post_meta( $variation->ID, '_wvg_gallery', true );
		if ($images) {
			$images = explode(';', $images);
			foreach ($images as $image) {
				if (!empty($image)) {
					$src = wp_get_attachment_image_src($image, 'thumbnail');
					echo '<li data-id="'.$image.'"><img src="'.$src[0].'"></li>';
				}
			}
		}
		echo '</ul>';
		echo '<p><a href="#" class="button-primary wvg-gallery-add-button">'.__('Add Variation Images', 'woocommerce-variation-gallery').'</a></p></div>';
	}
}

add_action( 'woocommerce_save_product_variation', 'wvg_save_variation_fields', 10, 2 );

if ( !function_exists('wvg_save_variation_fields') ) {
	function wvg_save_variation_fields($variation_id, $i) {
		$text_field = $_POST['_wvg_gallery'][ $variation_id ];
		update_post_meta( $variation_id, '_wvg_gallery', esc_attr( $text_field ) );
	}
}

//Frontend Part
add_action( 'wp_ajax_wvg_change_images', 'wvg_change_images' );
add_action( 'wp_ajax_nopriv_wvg_change_images', 'wvg_change_images' );
if (!function_exists('wvg_change_images')) {
	function wvg_change_images() {
		if ($_POST['variation']) {
			global $woocommerce;
			$variation_id = (int) $_POST['variation'];
			$variation = wc_get_product($variation_id);
			$parent = $variation->get_parent_id();
			$parent = wc_get_product($parent);
			if (get_post_meta($variation_id, '_wvg_gallery', true)) {
				$gallery_ids = get_post_meta($variation_id, '_wvg_gallery', true);
				$gallery_ids = explode(';', $gallery_ids);
			} else {
				$gallery_ids = $parent->get_gallery_image_ids();
			}
			$result = '';
			//get variation main image
			$image = $variation->get_image_id();
			//NEW WAY

			$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
			$post_thumbnail_id = $variation->get_image_id();
			$wrapper_classes   = array(
				'woocommerce-product-gallery',
				'images'
			);
			$result .= '<div class="'.esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ).'" data-columns="'.esc_attr( $columns ).'" style="opacity: 0; transition: opacity .25s ease-in-out;">
				<figure class="woocommerce-product-gallery__wrapper">';
			$html  = wc_get_gallery_image_html( $post_thumbnail_id, true );
			$result .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id );
			error_log(print_r($gallery_ids, true));
			foreach ( $gallery_ids as $attachment_id ) {
				if (!empty($attachment_id)) {
					$result .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id  ), $attachment_id );
				}
			}
			$result .= '</figure></div>';
			echo $result;
			wp_die();
		} else {
			echo 'error';
			wp_die();
		}
	}
}


