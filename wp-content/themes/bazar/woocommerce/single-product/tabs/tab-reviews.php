<?php
/**
 * Reviews tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( comments_open() ) : ?>
	<li class="reviews_tab"><a href="#tab-reviews"><?php echo apply_filters('woocommerce_reviews_tab_title', __( 'Reviews', 'yit' )) ?><?php echo comments_number(' (0)', ' (1)', ' (%)'); ?></a></li>
<?php endif; ?>