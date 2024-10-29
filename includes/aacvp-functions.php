<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aacvp_custom_template( $template, $template_name, $template_path ) {
    global $woocommerce;

    $_template = $template;
    if ( ! $template_path ) $template_path = $woocommerce->template_url;

    // Check if the template exists in your plugin
    $plugin_path  = AACVP_PATH . 'templates/' . $template_name;

    if ( file_exists( $plugin_path ) ) {
        $template = $plugin_path;
    }

    if ( ! $template ) {
        $template = $_template;
    }

    return $template;
}
add_filter('woocommerce_locate_template', 'aacvp_custom_template', 10, 3);

function aacvp_include_scripts() {
    // Include jQuery library if it is not already loaded on the site 
    if ( ! wp_script_is( 'jquery', 'enqueued' ) ) wp_enqueue_script('jquery');

    // Enqueue custom JavaScript file
	$file_path = AACVP_PATH . '/js/aacvp-script.js';
	
	$version = file_exists($file_path) ? filemtime($file_path) : time(); 
	$file_url = AACVP_URL. '/js/aacvp-script.js';
	
	wp_enqueue_script('aacvp-script', $file_url, array('jquery'), $version, true);
	
	$file_path_2 = AACVP_PATH . '/js/aacvp-ajax-add-to-cart.js';
	$file_url_2 = AACVP_URL. '/js/aacvp-ajax-add-to-cart.js';
	$version_2 = file_exists($file_path_2) ? filemtime($file_path_2) : time(); 
	
	wp_enqueue_script('aacvp-ajax-script', $file_url_2, array('jquery'), $version_2, true);
	
	wp_localize_script('aacvp-ajax-script', 'aacvp_ajax_obj', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
		'aacvp_nonce' => wp_create_nonce('aacvp_add_to_cart_variable')
	));
}
add_action('wp_enqueue_scripts', 'aacvp_include_scripts');

function aacvp_include_styles() {
	$file_path = AACVP_URL. '/css/aacvp-style.css';
	$version = file_exists($file_path) ? filemtime($file_path) : time(); 
	
	wp_enqueue_style('aacvp-style', $file_path, array(), $version, 'all');
}
add_action('wp_enqueue_scripts', 'aacvp_include_styles');

// Include modal template file 
function aacvp_include_modal() {
	$template_path = AACVP_PATH . 'includes/templates/aacvp-modal.php'; 
	
	if ( file_exists( $template_path ) ) {
		include $template_path; 
	}  else {
		echo "template path not found"; 
	}
}

function aacvp_modal_shortcode() {
	ob_start(); 
	aacvp_include_modal(); 
	return ob_get_clean(); 
}
add_shortcode('aacvp-modal', 'aacvp_modal_shortcode'); 

function aacvp_display_modal() {
	echo do_shortcode('[aacvp-modal]'); 
}
add_action('wp_footer', 'aacvp_display_modal'); 