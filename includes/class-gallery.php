<?php
namespace WPGG;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery Class
 */
class Gallery {

	/**
	 * Instance
	 * @var Gallery
	 */
	private static $instance = null;

	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'wp', array( $this, 'init_hooks' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-product-gallery-grid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		if ( ! is_product() ) {
			return;
		}

		// Disable core WooCommerce gallery support to prevent interference
		remove_theme_support( 'wc-product-gallery-zoom' );
		remove_theme_support( 'wc-product-gallery-lightbox' );
		remove_theme_support( 'wc-product-gallery-slider' );

		// Remove default WooCommerce gallery at priority 20 and others just in case
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		
		// Some themes might use different priorities or direct calls, 
		// but standard WooCommerce uses 20.
		
		// Add custom gallery at same priority 20
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'render_gallery' ), 20 );
		
		// Force gallery to be visible if themes hide it via CSS
		add_action( 'wp_head', function() {
			echo '<style>.woocommerce-product-gallery { opacity: 1 !important; visibility: visible !important; }</style>';
		} );

		// PhotoSwipe 5 needs this class on body or container for default arrow/icon positioning in some themes
		add_filter( 'body_class', function( $classes ) {
			if ( is_product() ) {
				$classes[] = 'pswp-photoswipe-5-default';
			}
			return $classes;
		} );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_assets() {
		if ( ! is_product() ) {
			return;
		}

		// Splide (Mobile Slider)
		wp_enqueue_style( 'splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css', array(), '4.1.4' );
		wp_enqueue_script( 'splide', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', array(), '4.1.4', true );

		// PhotoSwipe (Modal Gallery)
		// Use unique handles to prevent conflicts with PhotoSwipe v4 from WP core/WooCommerce
		wp_enqueue_style( 'wpgg-photoswipe', 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.css', array(), '5.4.4' );
		wp_enqueue_script( 'wpgg-photoswipe-lightbox', 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/umd/photoswipe-lightbox.umd.min.js', array(), '5.4.4', true );
		wp_enqueue_script( 'wpgg-photoswipe-core', 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/umd/photoswipe.umd.min.js', array(), '5.4.4', true );
		
		// Custom Assets
		wp_enqueue_style( 'wpgg-gallery', WPGG_URL . 'assets/css/gallery.css', array(), WPGG_VERSION );
		wp_enqueue_script( 'wpgg-gallery', WPGG_URL . 'assets/js/gallery.js', array( 'splide', 'wpgg-photoswipe-lightbox', 'wpgg-photoswipe-core' ), WPGG_VERSION, true );

		// Localize data
		wp_localize_script( 'wpgg-gallery', 'wpggData', array(
			'breakpoint'     => apply_filters( 'wpgg_breakpoint', 1024 ),
			'maxGridImages'  => apply_filters( 'wpgg_max_grid_images', 5 ),
			'i18n'           => array(
				'showAll' => __( 'Show all photos', 'woocommerce-product-gallery-grid' ),
				'counter' => __( 'of', 'woocommerce-product-gallery-grid' ),
			),
		) );
	}

	/**
	 * Render the gallery
	 */
	public function render_gallery() {
		global $product;

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$images = $this->get_product_images( $product );
		
		if ( empty( $images ) ) {
			return;
		}

		include WPGG_PATH . 'templates/gallery.php';
	}

	/**
	 * Get product images (Featured + Gallery)
	 */
	private function get_product_images( $product ) {
		$images = array();

		// Featured Image
		$featured_id = $product->get_image_id();
		if ( $featured_id ) {
			$images[] = $this->get_image_data( $featured_id );
		}

		// Gallery Images
		$gallery_ids = $product->get_gallery_image_ids();
		if ( ! empty( $gallery_ids ) ) {
			foreach ( $gallery_ids as $id ) {
				$images[] = $this->get_image_data( $id );
			}
		}

		return $images;
	}

	/**
	 * Get formatted image data
	 */
	private function get_image_data( $attachment_id ) {
		$full_src = wp_get_attachment_image_src( $attachment_id, 'full' );
		$html = wp_get_attachment_image( $attachment_id, 'woocommerce_single', false, array(
			'class' => 'wpgg-image',
			'loading' => 'lazy',
		) );
		
		return array(
			'id'      => $attachment_id,
			'url'     => $full_src ? $full_src[0] : '',
			'width'   => $full_src ? $full_src[1] : 0,
			'height'  => $full_src ? $full_src[2] : 0,
			'alt'     => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
			'html'    => $html ? $html : '<!-- Image data found but no HTML generated -->',
			'thumb'   => wp_get_attachment_image_url( $attachment_id, 'thumbnail' ),
		);
	}
}
