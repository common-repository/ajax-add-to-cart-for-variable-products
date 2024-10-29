<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Aacvp_Loader{
	
	protected static $_instance = null; 
	
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct() {
		$this->set_constants(); 
		$this->includes(); 
	}
	
	public function set_constants() {
		$this->define( "AACVP_PATH", plugin_dir_path( AACVP_PLUGIN_FILE ) ); // Plugin path
		$this->define( "AACVP_PLUGIN_BASENAME", plugin_basename( AACVP_PLUGIN_FILE ) );
		$this->define( "AACVP_URL", untrailingslashit( plugins_url( '/', AACVP_PLUGIN_FILE ) ) ); // plugin url
		$this->define( "AACVP_VERSION", "0.1.0" ); //Plugin version
	}
	
	public function define( $constant_name, $constant_value ){
		if( !defined( $constant_name ) ){
			define( $constant_name, $constant_value );
		}
	}
	
	public function includes() {
		require_once AACVP_PATH.'/includes/aacvp-functions.php';
		require_once AACVP_PATH.'/includes/aacvp-button.php';
		require_once AACVP_PATH.'/includes/aacvp-price.php';
	}
}