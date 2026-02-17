<?php
/**
 * Gallery Template
 *
 * @var array $images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$max_grid = apply_filters( 'wpgg_max_grid_images', 5 );
$total_images = count( $images );
?>

<div class="wpgg-gallery" id="wpgg-gallery-container">
    <div id="wpgg-slider" class="splide">
        <div class="splide__track">
            <div class="gallery-grid splide__list">
                <?php foreach ( $images as $index => $image ) : 
                    $classes = array( 'gallery-item', 'splide__slide' );
                    if ( $index === 0 ) {
                        $classes[] = 'grid-item--main';
                    }
                    if ( $index >= $max_grid ) {
                        $classes[] = 'grid-item--extra';
                    }
                    ?>
                    <a href="<?php echo esc_url( $image['url'] ); ?>" 
                       class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
                       data-index="<?php echo $index; ?>"
                       data-pswp-width="<?php echo esc_attr( $image['width'] ); ?>"
                       data-pswp-height="<?php echo esc_attr( $image['height'] ); ?>"
                       target="_blank">
                        
                        <div class="image-wrapper">
                            <?php echo $image['html']; ?>
                        </div>

                        <?php if ( $index === ( $max_grid - 1 ) && $total_images > $max_grid ) : ?>
                            <div class="wpgg-overlay">
                                <button class="wpgg-show-all wpgg-btn" aria-label="<?php esc_attr_e( 'Show all photos', 'woocommerce-product-gallery-grid' ); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span><?php esc_html_e( 'Show all photos', 'woocommerce-product-gallery-grid' ); ?></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
