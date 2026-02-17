<?php
/**
 * Plugin Name: WooCommerce Product Gallery Grid
 * Description: Overrides the default WooCommerce single product gallery with a custom responsive grid/slider.
 * Version: 1.0.0
 * Author: Antigravity
 * Text Domain: woocommerce-product-gallery-grid
 * Prefix: WPGG_
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'WPGG_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPGG_URL', plugin_dir_url( __FILE__ ) );
define( 'WPGG_VERSION', '1.0.0' );

require_once WPGG_PATH . 'includes/class-gallery.php';

/**
 * Initialize the plugin
 */
function WPGG_init() {
	// Check if WooCommerce is active
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="error"><p>' . esc_html__( 'WooCommerce Product Gallery Grid requires WooCommerce to be installed and active.', 'woocommerce-product-gallery-grid' ) . '</p></div>';
		} );
		return;
	}

	\WPGG\Gallery::get_instance();
}
add_action( 'plugins_loaded', 'WPGG_init' );
