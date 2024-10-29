<?php 
/**
 * Plugin Name: Ajax Add to Cart for Variable Products
 * Author:      Dinah Chen
 * Description: Increase your WooCommerce store's sales by enabling Ajax add to cart function for variable products on shop, category, and other non-single product pages in the form of a neatly designed, responsive pop-up window.   
 * Version:     1.0.0
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: ajax-add-to-cart-variable-products
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AACVP_PLUGIN_FILE' ) ) {
	define( 'AACVP_PLUGIN_FILE', __FILE__ );
}

function aacvp_init() {
	
	if( !class_exists( 'woocommerce' ) ) return;
	
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return; 

	if ( ! class_exists( 'Aacvp_Loader' ) ) {
		require_once 'includes/class-aacvp-loader.php';
	}

	aacvp_start();
}
add_action( 'plugins_loaded','aacvp_init', 15 );

function aacvp_start() {
	return Aacvp_Loader::get_instance(); 
}

