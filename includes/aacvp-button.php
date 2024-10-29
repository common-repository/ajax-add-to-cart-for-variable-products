<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Replace default loop buttons with custom ones 
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_after_shop_loop_item', 'aacvp_template_loop_add_to_cart', 10);

function aacvp_template_loop_add_to_cart(){
	global $product; 
	
	if ( !$product->is_type( 'variable' ) ) {
		
		if ( $product->is_type( 'simple' ) ) {
			
			echo '<a href="?add-to-cart='.esc_attr( $product->get_id() ).'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_html( $product->get_sku() ).'" aria-label="Add to cart:'. esc_html( $product->get_name() ).'" aria-describedby rel="nofollow">'.esc_html( $product->add_to_cart_text() ).'</a>';
		} else {
			
			$aacvp_button_html = apply_filters(
				'aacvp_loop_add_to_cart_link', // WPCS: XSS ok.
				sprintf(
					'<a href="%s" aria-describedby="aacvp_loop_add_to_cart_link_describedby_%s" data-quantity="%s" class="%s" %s>%s</a>',
					esc_url( $product->add_to_cart_url() ),
					esc_attr( $product->get_id() ),
					esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
					esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					esc_html( $product->add_to_cart_text() )
				),
				$product,
				$args
			);
			
			$aacvp_allowed_html = array(
				'a' => array(
					'href' => array(),
					'aria-describedby' => array(),
					'data-quantity' => array(), 
					'class' => array()
				),
			);
			
			echo wp_kses( $aacvp_button_html, $aacvp_allowed_html );
	
		}
	
	} else {
	
		$variations = $product->get_available_variations();
		$counter = 0; 
		foreach( $variations as $variation ) {
			if ( ! aacvp_if_attributes_set( $variation ) ) {
				$counter++; 
			}
		}
		if ( $counter == 0 ) {
		
			echo '<a href="#" class="button woo-aacvp-add_to_cart_button">'.esc_html( $product->add_to_cart_text() ).'</a>';
	
			// Return <input type="hidden"> with all variation information 
			aacvp_variations_data( $product ); 
		
		} else {
		
			echo '<a href="#" class="button woo-aacvp-add_to_cart_form">'.esc_html( $product->add_to_cart_text() ).'</a>';
			
			// Return <input type="hidden"> with all variation information
			aacvp_variations_form( $product );
		
		}
	}?>
	<span id="aacvp_loop_add_to_cart_link_describedby_<?php echo esc_attr( $product->get_id() ); ?>" class="screen-reader-text">
		<?php echo esc_html( $args['aria-describedby_text'] ); ?>
	</span>
<?php 	
}

function aacvp_variations_data( $product ) {
	$product_name = $product->get_name(); 
	echo '<input type="hidden" name="product_name" value="'. esc_html( $product_name ).'">'; 

	$available_variations = $product->get_available_variations();
	$__variations_arr = array(); 
	foreach ( $available_variations as $variation_obj ) {
		$variation_id = $variation_obj['variation_id'];
		$variation_sku = $variation_obj['sku'];
		$variation_price_html = $variation_obj['price_html'];
		$variation_image_id = $variation_obj['image_id'];
		$variation_image_url = wp_get_attachment_image_url($variation_image_id, 'full');
		$variation_price = $variation_obj['display_price'];
		$variation_stock = ( $variation_obj['is_in_stock'] == 1 ) ? 'In stock' : 'Out of stock' ;
		$variation_attributes = aacvp_get_attributes( $variation_obj );
		
		echo '<input type="hidden" name="variation_data" data-variation_id="'.esc_attr( $variation_id ).'" data-variation_sku="'.esc_html($variation_sku).'" data-variation_price_html="'.esc_html($variation_price_html).'" data-variation_image="'.esc_url($variation_image_url).'" data-variation_price="'.esc_attr($variation_price).'" data-variation_attr="'.esc_attr($variation_attributes).'">'; 
		
	}
}

function aacvp_if_attributes_set( $variation ){
	$attributes = $variation['attributes'];
	$attr_count = count( $attributes );
	$attr_show = [];
	foreach( $attributes as $name => $value ) {
		if ( !empty( $value ) ) array_push( $attr_show, $value );
	}
	$attr_show_count = count( $attr_show );
	return ( ( $attr_count == $attr_show_count ) ) ? true : false; 
}

function aacvp_get_attributes( $variation ) {
	$attributes = $variation['attributes'];
	$if_attributes_all_set = aacvp_if_attributes_set( $variation );
	if ( $if_attributes_all_set ) {
		$_attr_array = [];
		foreach( $attributes as $name => $value ) {
			$_name = aacvp_get_clean_name( $name );
			$_value = $value; 
			$_attr_array[$_name] = $_value;
		}
		$_attr_str = wp_json_encode( $_attr_array );
		$_attr_str_parse = htmlspecialchars( $_attr_str, ENT_QUOTES, 'UTF-8' );
		return $_attr_str_parse;
	} 
}

function aacvp_get_clean_name( $name ) {
	if ( strpos( $name, 'attribute_pa_' ) === 0 ) $name = str_replace( 'attribute_pa_', '', $name );
	if ( strpos( $name, 'attribute_' ) === 0 ) $name = str_replace( 'attribute_', '', $name );
	if ( strpos( $name, 'pa_' ) === 0 ) $name = str_replace( 'pa_', '', $name );
	return $name; 
}

function aacvp_variations_form( $product ) {
	$product_id = $product->get_id(); 
	$product_url = get_permalink( $product_id ); 
	$product_name = $product->get_name(); 
	
	echo '<input type="hidden" name="product_name" value="'.esc_html($product_name).'">'; 
	
	$attributes = $product->get_attributes(); 

	$attr_keys_arr = array_keys( $attributes );
	$attr_keys_json = wp_json_encode( $attr_keys_arr );
	$attr_keys_str = function_exists( 'wc_esc_json' ) ? wc_esc_json( $attr_keys_json ) : _wp_specialchars( $attr_keys_json, ENT_QUOTES, 'UTF-8', true );
	
	echo '<input type="hidden" name="attribute_keys" data-attribute_keys="'.esc_attr($attr_keys_str).'">'; 
	
	$attr_clean_keys_arr = [];
	foreach( $attr_keys_arr as $attr_key ){
		$attr_clean_key = aacvp_get_clean_name( $attr_key );
		array_push( $attr_clean_keys_arr, $attr_clean_key );
	}
	$attr_clean_keys_json = wp_json_encode( $attr_clean_keys_arr );
	$attr_clean_keys_str = function_exists( 'wc_esc_json' ) ? wc_esc_json( $attr_clean_keys_json ) : _wp_specialchars( $attr_clean_keys_json, ENT_QUOTES, 'UTF-8', true );
	
	echo '<input type="hidden" name="attribute_clean_keys" data-attribute_keys="'.esc_attr($attr_clean_keys_str).'">';
	
	$options = array();
	foreach( $attributes as $attribute ) {
		$attribute_key = $attribute->get_name(); 
		$attribute_name = aacvp_get_clean_name( $attribute->get_name() );
		$attribute_options_ids = $attribute->get_options();
		$attribute_options_names = array();
		foreach ($attribute_options_ids as $attribute_option_id) {
			if ( ! is_int( $attribute_option_id ) ){
				$name = strval($attribute_option_id); 
			} else {
				$term = get_term( $attribute_option_id, $attribute_key );
				$name = $term->name; 
			}
			array_push( $attribute_options_names, $name );
		}
		array_push( $options, [
			strtolower($attribute_name) => $attribute_options_names
		] );
	}

	$available_variations = $product->get_available_variations();
	
	foreach($available_variations as $variation) {
		$variation_attributes = $variation['attributes'];
		$empty_attributes = array();
		foreach( $variation_attributes as $variation_attribute_key => $variation_attribute_value ) {
			if ( empty( $variation_attribute_value )  ) {
				$attribute_name = aacvp_get_clean_name( $variation_attribute_key );
				foreach( $options as $option ) {
					if ( $option[$attribute_name] ) {
						array_push( $empty_attributes, [
							$attribute_name => $option[$attribute_name]
						] );
					}
				}
			}
		}
		
		$variation_id = $variation['variation_id'];
		$variation_sku = $variation['sku'];
		$variation_price_html = esc_html($variation['price_html']);
		$variation_image_id = $variation['image_id'];
		$variation_image_url = esc_html(wp_get_attachment_image_url($variation_image_id, 'full'));
		$variation_price = $variation['display_price'];
		$variation_stock = ( $variation['is_in_stock'] == 1 ) ? 'In stock' : 'Out of stock' ;
		
		$variation_attributes = $variation['attributes'];
		$variation_attr_array = [];
		foreach( $variation_attributes as $key => $value ) {
			if ( ! empty( $value ) ) {
				array_push( $variation_attr_array, [
					aacvp_get_clean_name( $key ) => $value
				] );
			}
		}
		$variation_attr_json = wp_json_encode( $variation_attr_array );
		$variation_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variation_attr_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
		
		$variation_empty_attr_json = wp_json_encode( $empty_attributes );
		$variation_empty_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variation_empty_attr_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );
		
		echo '<input type="hidden" name="variation_data" data-product_id="'.esc_attr($product_id).'" data-variation_id="'.esc_attr($variation_id).'" data-variation_sku="'.esc_attr($variation_sku).'" data-variation_price_html="'.esc_html($variation_price_html).'" data-variation_image="'.esc_url($variation_image_url).'" data-variation_price="'.esc_attr($variation_price).'" data-variation_attr="'.esc_attr($variation_attr).'" data-empty_attr="'.esc_attr($variation_empty_attr).'">';
	}
}

function aacvp_add_to_cart_variable() {	
	// Verify nonce for security
    check_ajax_referer( 'aacvp_add_to_cart_variable', 'nonce' );

    $product_id = empty( $_POST['product_id'] ) ? '' : apply_filters('aacvp_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( absint( $_POST['quantity']) );
    $variation_id = empty( $_POST['variation_id'] ) ? '' : absint($_POST['variation_id']);
	$attributes = isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['attributes'] ) ) : array();	
    
	$variation = new WC_Product_Variation($variation_id);
    $variation_data = $variation->get_data();

    foreach ($variation_data['attributes'] as $key => $value) {
        if (isset($attributes[$key]) && $attributes[$key] !== $value) {
            wp_send_json_error(array('error' => 'Invalid attributes selected.'));
        }
    }

    $passed_validation = apply_filters('aacvp_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $attributes);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $attributes) && 'publish' === $product_status) {
        do_action('aacvp_ajax_added_to_cart', $product_id);
        wc_clear_notices();

        // Return fragments and cart hash for mini cart update
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error(array('error' => 'Unable to add to cart.'));
    }

    wp_die();
}
add_action('wp_ajax_aacvp_add_to_cart_variable', 'aacvp_add_to_cart_variable');
add_action('wp_ajax_nopriv_aacvp_add_to_cart_variable', 'aacvp_add_to_cart_variable');

function aacvp_loop_add_to_cart_link( $product ){
	$html = sprintf( 
        '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
        esc_url( $product->add_to_cart_url() ),
        esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
        esc_attr( implode( ' ', array_filter( array(
            'button',
            'product_type_' . $product->get_type(),
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
        ) ) ) ),
        wc_implode_html_attributes( array(
            'data-product_id'  => $product->get_id(),
            'data-product_sku' => $product->get_sku(),
            'aria-label'       => $product->add_to_cart_description(),
            'rel'              => 'nofollow',
        ) ),
        esc_html( $product->add_to_cart_text() )
    );
	return $html;
}

function aacvp_add_to_cart_product_id(){
	// Verify nonce for security
    check_ajax_referer( 'add-to-cart', 'security' );
	
	return isset( $_REQUEST['add-to-cart']  ) ? absint( $_REQUEST['add-to-cart'] ) : 0;
}

function aacvp_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {
	return apply_filters( 'aacvp_add_to_cart_validation', $passed, $product_id, $quantity, $variation_id, $variations );
}

function aacvp_ajax_added_to_cart() {
    // Ensure WooCommerce is loaded
    if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
        return;
    }

    // Verify nonce for security
    check_ajax_referer( 'add-to-cart', 'security' );

    // Retrieve product data from the AJAX request
    $product_id = empty( $_POST['product_id'] ) ? '' : apply_filters( 'aacvp_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $quantity   = empty( $_POST['quantity'] ) ? 1 : absint( $_POST['quantity'] );
    $variation_id = empty( $_POST['variation_id'] ) ? 0 : absint( $_POST['variation_id'] );
	$variations = !empty( $_POST['variation'] ) && is_array( $_POST['variation'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['variation'] ) ) : array();	
	

    // Validate the add-to-cart action
    $passed_validation = apply_filters( 'aacvp_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

    // Attempt to add the product to the cart
    if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
        do_action( 'aacvp_ajax_added_to_cart', $product_id );

        if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
            wc_add_to_cart_message( array( $product_id => $quantity ), true );
        }

        // Send back refreshed fragments for the cart, if needed
        WC_AJAX::get_refreshed_fragments();
    } else {
        // Handle errors
        $data = array(
            'error' => true,
            'product_url' => apply_filters( 'custom_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
        );
        wp_send_json( $data );
    }

    wp_die();
}
