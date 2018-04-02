<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

global $woocommerce, $post;

if ( $post->post_content ) : ?>

		<?php $heading = esc_html( apply_filters('woocommerce_product_description_heading', __( 'Product Description', 'yit' ) ) ); ?>

		<?php if( ! empty( $heading ) ): ?>
			<h2><?php echo $heading; ?></h2>
		<?php endif; ?>

		<?php the_content(); ?>

<?php endif; ?>