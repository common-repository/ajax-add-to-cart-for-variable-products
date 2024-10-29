<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Replace default price HTML with custom HTML
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action('woocommerce_after_shop_loop_item_title', 'aacvp_template_loop_price', 10);

function aacvp_template_loop_price() {
	global $product; 
	if ( ! $product->is_type('variable')  ){
		if ( $price_html = $product->get_price_html() ){
			?>
			<span class="price"><?php echo wp_kses_post($price_html); ?></span>
		<?php 	
		}
	} else {
		$product_id = $product->get_id(); 
		if ( $default_count = aacvp_is_default_set( $product_id ) ){
			// If default variation is set 
			$default_variation_id = aacvp_get_default_variation_id( $product_id ); 
			$variation = new WC_Product_Variation( $default_variation_id );
			if ( $variation_price_html = $variation->get_price_html() ){
				?>
				<span class="price"><?php echo wp_kses_post($variation_price_html); ?></span>
			<?php 	
			}
		} else {
			// If default variation is NOT set
			$cheapest_variation_id = aacvp_get_cheapest_variation_price( $product_id ) ; 
			$variation = new WC_Product_Variation( $cheapest_variation_id );
			if ( $variation_price_html = $variation->get_price_html() ){
				?>
				<span class="price"><?php echo wp_kses_post($variation_price_html); ?></span>
			<?php 	
			}
		}
	}
}

function aacvp_is_default_set( $product_id ) {
	$product = wc_get_product( $product_id );
	$default_attributes = $product->get_default_attributes(); 
	$count = count( $default_attributes ); 
	return ( $count > 0 ) ? true : false ;
}

function aacvp_get_default_variation_id( $product_id ){
	$product = wc_get_product( $product_id );
	$default_attributes = $product->get_default_attributes(); 
	$available_variations = $product->get_available_variations(); 
	foreach ( $available_variations as $variation ){
		$variation_id = $variation['variation_id'];
		$variation_obj = wc_get_product( $variation_id );
		$variation_attributes = $variation_obj->get_attributes(); 
		$is_default = true; 
		foreach ( $default_attributes as $attribute => $value ) {
            if ( ! isset( $variation_attributes[ $attribute ] ) || $variation_attributes[ $attribute ] != $value ) {
                $is_default = false;
                break;
            }
        }
		if ( $is_default ){
			return $variation_id; 
		}
	}
}

function aacvp_get_variation_price( $variation_id ){
	$variation = new WC_Product_Variation( $variation_id );
	if ( $variation ){
		$regular_price = $variation->get_regular_price(); 
		$sale_price = $variation->get_sale_price(); 
		return array(
			'regular_price' => $regular_price,
			'sale_price' => $sale_price
		);
	} else {
		return null; // Variation not found 
	}
}

function aacvp_get_cheapest_variation_price( $product_id ) {
	$product = wc_get_product( $product_id );
	$available_variations = $product->get_available_variations();
    $lowest_price = null;
    $cheapest_variation_id = null;
	
	foreach ( $available_variations as $variation_data ) {
		$variation_id = $variation_data['variation_id'];
		$variation = new WC_Product_Variation( $variation_id );
		$price = $variation->get_price();
		if ( is_null( $lowest_price ) || $price < $lowest_price ) {
            $lowest_price = $price;
            $cheapest_variation_id = $variation_id;
        }
	}
	
	return $cheapest_variation_id; 
}